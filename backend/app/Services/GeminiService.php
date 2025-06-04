<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Document;
use Carbon\Carbon;

class GeminiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    }

    protected function getApiKey(): string
    {
        return env('GEMINI_API_KEY');
    }

    protected function getNextFileName(string $title, int $version): string
    {
        $ext = pathinfo($title, PATHINFO_EXTENSION);
        $base = pathinfo($title, PATHINFO_FILENAME);
        return "{$base}_{$version}.{$ext}";
    }

    /**
     * Upload the file to Gemini API and return the file ID.
     *
     * @param Document $document
     * @return string Uploaded file's Gemini ID
     *
     * @throws \Exception If file not found or upload failed.
     */
    protected function uploadFile(Document $document): string
    {
        $version = $document->upload_version ?? 1;
        $fileName = $this->getNextFileName($document->title, $version);

        $filePath = storage_path("app/public/{$document->file_path}");
        if (!file_exists($filePath)) {
            throw new \Exception('File does not exist on server.');
        }

        $response = Http::attach(
            'file',
            file_get_contents($filePath),
            $fileName
        )->post("https://generativelanguage.googleapis.com/upload/v1beta/files?key=" . $this->getApiKey());

        if ($response->failed()) {
            throw new \Exception('Failed to upload file to Gemini: ' . $response->body());
        }

        $responseData = $response->json();

        // Updated to read the new response structure
        $fileData = $responseData['file'] ?? null;
        if (!$fileData || empty($fileData['name'])) {
            throw new \Exception('Invalid upload response from Gemini.');
        }

        $fileId = $fileData['name'];
        $uri = $fileData['uri'];

        // Use expirationTime from response if available, else fallback to 23 hours
        $expirationTime = isset($fileData['expirationTime'])
            ? Carbon::parse($fileData['expirationTime'])
            : now()->addHours(23);

        $document->update([
            'gemini_id' => $fileId,
            'upload_version' => $version + 1,
            'expires_at' => $expirationTime,
            'uri' => $uri,
        ]);

        return $uri;
    }

    /**
     * Ensure file is fresh (not expired), else re-upload and return current gemini_id.
     *
     * @param Document $document
     * @return string Gemini file ID
     */
    public function ensureFileIsFresh(Document $document): string
    {
        $shouldReupload = !$document->gemini_id ||
            !$document->expires_at ||
            now()->greaterThanOrEqualTo($document->expires_at);

        return $shouldReupload
            ? $this->uploadFile($document)
            : $document->uri;
    }

    /**
     * Ask a question referencing the uploaded file.
     *
     * @param Document $document
     * @param string $question
     * @return string Answer text
     * 
     * @throws \Exception If Gemini request fails.
     */

    public function ask(Document $document, string $question): string
    {
        // Get the correct file URI and MIME type (you must implement this)
        $fileId = $this->ensureFileIsFresh($document); // e.g., "gs://your-bucket/file.pdf"

        $prompt = "Answer the following question using the uploaded file content:\n\n{$question}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $this->getApiKey(), [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'file_data' => [
                                        'mime_type' => 'application/pdf',
                                        'file_uri' => $fileId,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

        if ($response->failed()) {
            throw new \Exception('Gemini failed to answer: ' . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text', 'No response.');
    }

    /**
     * Ask a question by embedding a base64-encoded PDF into the Gemini request.
     *
     * @param Document $document
     * @param string $question
     * @return string Answer text
     *
     * @throws \Exception If the file is missing or the Gemini API fails.
     */
    public function ask_upload(Document $document, string $question): string
    {
        // Locate the file
        $filePath = storage_path("app/public/{$document->file_path}");
        if (!file_exists($filePath)) {
            throw new \Exception('File does not exist on server.');
        }

        // Read and base64-encode the PDF file
        $pdfContents = file_get_contents($filePath);
        $base64EncodedPdf = base64_encode($pdfContents);

        // Prepare the prompt and payload
        $prompt = "Use the attached PDF content to answer the following question. \
        Return your response as plain text only — do not use Markdown, headings, or bullet points. \
        You may use \n for new lines and \t for indentation to improve formatting and clarity.:\n\n{$question}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => 'application/pdf',
                                'data' => $base64EncodedPdf,
                            ]
                        ],
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]
        ];

        // Make the POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $this->getApiKey(), $payload);

        if ($response->failed()) {
            throw new \Exception('Gemini failed to answer: ' . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text', 'No response.');
    }



}

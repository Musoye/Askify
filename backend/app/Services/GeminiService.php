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

        // Use expirationTime from response if available, else fallback to 23 hours
        $expirationTime = isset($fileData['expirationTime']) 
            ? Carbon::parse($fileData['expirationTime']) 
            : now()->addHours(23);

        $document->update([
            'gemini_id' => $fileId,
            'upload_version' => $version + 1,
            'expires_at' => $expirationTime,
        ]);

        return $fileId;
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
            : $document->gemini_id;
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
        $fileId = $this->ensureFileIsFresh($document);

        $prompt = "Answer the following question using the uploaded file content:\n\n{$question}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/models/gemini-1.5-pro:generateContent", [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'tools' => [
                ['fileData' => ['fileId' => $fileId]],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Gemini failed to answer: ' . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text', 'No response.');
    }
}

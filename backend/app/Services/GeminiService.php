<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use App\Models\Document;

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

    protected function getNextFileName(string $originalName, int $version): string
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        return "{$base}_{$version}.{$ext}";
    }

    protected function uploadFile(Document $document): string
    {
        $version = $document->upload_version ?? 1;
        $fileName = $this->getNextFileName($document->original_name, $version);

        $response = Http::attach(
            'file', file_get_contents(storage_path("app/{$document->file_path}")), $fileName
        )->post("{$this->baseUrl}/files:upload?key=" . $this->getApiKey());

        if ($response->failed()) {
            throw new \Exception('Failed to upload file to Gemini.');
        }

        $fileId = $response->json()['name'] ?? null;

        $document->update([
            'gemini_file_id' => $fileId,
            'upload_version' => $version + 1,
        ]);

        return $fileId;
    }

    public function ensureFileIsFresh(Document $document): string
    {
        $shouldReupload = !$document->gemini_file_id ||
            $document->updated_at->diffInHours(now()) >= 23;

        return $shouldReupload
            ? $this->uploadFile($document)
            : $document->gemini_file_id;
    }

    public function ask(Document $document, string $question): string
    {
        $fileId = $this->ensureFileIsFresh($document);

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/models/gemini-1.5-pro:generateContent", [
            'contents' => [
                ['parts' => [['text' => $question]]],
            ],
            'tools' => [
                ['fileData' => ['fileId' => $fileId]],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Gemini failed to answer.');
        }

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';
    }
}

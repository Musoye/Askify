<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::orderBy('created_at', 'desc')->get();

        // This is used for migration and tagging documents
        // $gemini = new GeminiService();

        // foreach ($documents as $doc) {
        //     try {
        //         $tags = $gemini->generateAndStoreTags($doc);   // ← stores to DB
        //         Log::info("Tagged #{$doc->id}: {$tags}");
        //     } catch (\Throwable $e) {
        //         Log::error("Tagging failed for #{$doc->id}", [
        //             'error' => $e->getMessage(),
        //         ]);
        //     }
        // }

        if ($documents->count() > 0) {
            return response()->json([
                'message' => 'Documents information Retrived',
                'status' => 200,
                'data' => DocumentResource::collection($documents)
            ], 200);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'file' => 'required|file|mimes:txt,pdf,md',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error in uploading file',
                'errors' => $validator->messages()
            ], 422);
        }

        $user_id = auth()->id();

        // Save file locally
        $path = $request->file('file')->store('documents', 'public');
        $doc = Document::create([
            'user_id' => $user_id,
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description,
        ]);

        $gemini = new GeminiService();

        try {
            $tags = $gemini->generateAndStoreTags($doc);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get answer from Gemini',
                'error' => $e->getMessage()
            ], 500);
        }

        // Upload to Gemini
        // try {
        //     $geminiService = new GeminiService();
        //     $geminiService->ensureFileIsFresh($doc);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'Document saved locally, but failed to upload to Gemini.',
        //         'error' => $e->getMessage(),
        //         'data' => new DocumentResource($doc)
        //     ], 500);
        // }

        return response()->json([
            'message' => 'Document has been uploaded and Succesfully',
            'status' => 201,
            'data' => new DocumentResource($doc)
        ], 201);
    }

    public function show(Document $document)
    {
        return response()->json([
            'message' => 'Document retrieved succesfuuly',
            'status' => 200,
            'data' => new DocumentResource($document)
        ], 200);
        //
    }

    public function update(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'file' => 'nullable|file|mimes:txt,pdf,md',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error updating document',
                'errors' => $validator->messages()
            ], 422);
        }

        // Update title if provided
        if ($request->filled('title')) {
            $document->title = $request->title;
        }

        // If file is uploaded, replace and reset gemini_id
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('documents', 'public');
            $document->file_path = $path;
            $document->gemini_id = null;
        }

        $document->save();

        return response()->json([
            'message' => 'Document updated successfully',
            'status' => 200,
            'data' => new DocumentResource($document)
        ], 200);
    }

    public function view(Document $document)
    {
        return Storage::disk('public')->response($document->file_path);
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return response()->json([
            'message' => 'Document has been Deleted Successfully'
        ]);
    }

    public function recommendDocument($document_id)
    {
        $document = Document::find($document_id);

        if (!$document) {
            return Response::json(['message' => 'Document not found', 'status' => 404], 404);
        }

        $tags = collect(explode(',', $document->tags))
            ->map(fn($t) => trim(strtolower($t)))
            ->filter();

        if ($tags->isEmpty()) {
            return Response::json(['message' => 'No tags on this document', 'status' => 200], 200);
        }

        $candidates = Document::where('id', '!=', $document->id)
            ->whereNotNull('tags')
            ->get()
            ->map(function ($doc) use ($tags) {
                $docTags = collect(explode(',', $doc->tags))
                    ->map(fn($t) => trim(strtolower($t)));

                $doc->match_score = $tags->intersect($docTags)->count();

                return $doc;
            })
            ->filter(fn($doc) => $doc->match_score > 0)        // keep only those with at least one overlap
            ->sortByDesc('match_score')                         // highest overlap first
            ->take(3)                                           // top 3
            ->values();                                         // reset keys for clean JSON


        if ($candidates->isEmpty()) {
            return Response::json(['message' => 'No recommended documents', 'status' => 200], 200);
        }

        return Response::json([
            'message' => 'Recommended documents',
            'status' => 200,
            'data' => DocumentResource::collection($candidates)
        ], 200);
    }
}

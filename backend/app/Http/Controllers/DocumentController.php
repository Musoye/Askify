<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\GeminiService;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::orderBy('created_at', 'desc')->get();

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


    /**
     * Store a newly created resource in storage.
     */
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

        // Create document record
        $doc = Document::create([
            'user_id' => $user_id,
            'title' => $request->title,
            'file_path' => $path,
            'description' => $request->description,
        ]);

        // Upload to Gemini
        try {
            $geminiService = new GeminiService();
            $geminiService->ensureFileIsFresh($doc);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Document saved locally, but failed to upload to Gemini.',
                'error' => $e->getMessage(),
                'data' => new DocumentResource($doc)
            ], 500);
        }

        return response()->json([
            'message' => 'Document has been uploaded and linked to Gemini',
            'status' => 201,
            'data' => new DocumentResource($doc)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return response()->json([
            'message' => 'Document retrieved succesfuuly',
            'status' => 200,
            'data' => new DocumentResource($document)
        ], 200);
        //
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return response()->json([
            'message' => 'Document has been Deleted Successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Resources\QuestionResource;
use Illuminate\Support\Facades\Validator;
use App\Services\GeminiService;
class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::orderBy('created_at', 'desc')->get();

        if ($questions->count() > 0) {
            return response()->json([
                'message' => 'Questions information Retrived',
                'status' => 200,
                'data' => QuestionResource::collection($questions)
            ], 200);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'document_id' => 'required|exists:documents,id',
            'question' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the question first
        $question = Question::create([
            'user_id' => auth()->id(),   // Use authenticated user
            'document_id' => $request->document_id,
            'question' => $request->question,
        ]);

        // Load the document model
        $document = Document::findOrFail($request->document_id);

        // Initialize GeminiService
        $gemini = new GeminiService();

        try {
            // Ask Gemini the question with the document
            $answer = $gemini->ask($document, $request->question);

            // Save the answer in the question model
            $question->answer = $answer;
            $question->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get answer from Gemini',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Question submitted and answered',
            'data' => $question,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        return response()->json([
            'message' => 'Document retrieved succesfuuly',
            'status' => 200,
            'data' => new QuestionResource($question)
        ], 200);
    }


    public function getByDocument($document_id)
    {
        $questions = Question::where('document_id', $document_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($questions->count() > 0) {
            return response()->json([
                'message' => 'Questions retrieved by document',
                'status' => 200,
                'data' => QuestionResource::collection($questions)
            ], 200);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    public function getByUser($document_id)
    {
        $user_id = auth()->id();

        $questions = Question::where('user_id', $user_id)
            ->where('document_id', $document_id)
            ->orderBy('created_at', 'desc')
            ->get();


        if ($questions->count() > 0) {
            return response()->json([
                'message' => 'User questions retrieved',
                'status' => 200,
                'data' => QuestionResource::collection($questions)
            ], 200);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'nullable|string|max:1000',
            'answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($question->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only update if fields are present
        $updates = array_filter($validator->validated());

        $question->update($updates);

        return response()->json([
            'message' => 'Question updated',
            'data' => $question,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json([
            'message' => 'Question has been Deleted Successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Services\NoteService;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    protected $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    public function index(Request $request)
    {
        $notes = $this->noteService->getAllNotesForUser($request->user());

        return response()->json([
            'success' => true,
            'data'    => $notes,
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $note = $this->noteService->getNoteById($request->user(), $id);

        if (!$note) {
            return response()->json([
                'success' => true,
                'message' => 'Note not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $note,
        ], 200);
    }

    public function store(NoteRequest $request)
    {
        $note = $this->noteService->createNote($request);

        return response()->json([
            'success' => true,
            'message' => 'Note created succesfully',
            'data'    => $note,
        ], 201);
    }

    public function update(NoteRequest $request, $id)
    {
        $note = $this->noteService->updateNote($request->user(), $request, $id);

        if (!$note) {
            return response()->json([
                'success' => true,
                'message' => 'Note not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Note updated succesfully',
            'data'    => $note,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $deleted = $this->noteService->deleteNote($request->user(), $id);

        if (!$deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Note not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully',
        ], 200);
    }
}

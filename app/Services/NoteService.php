<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class NoteService
{
    public function getAllNotesForUser($user)
    {
        return $user->notes()->with('tags')->get();
    }

    public function getNoteById($user, $id)
    {
        return $user->notes()->with('tags')->find($id);
    }

    public function createNote($request)
    {
        $note = new Note($request->all());
        $note->user_id = Auth::id();

        if ($request->image) {
            $image = $request->file('image')->store('images', 'public');
            $note->image = $image;
        }

        $note->save();

        if ($request->tags) {
            $note->tags()->attach($request->input('tags'));
        }

        return $note;
    }

    public function updateNote($user, $request, $id)
    {
        $note = $user->notes()->find($id);

        if (!$note) {
            return null;
        }

        if ($request->hasFile('image')) {
            $existingImage = $note->getAttributes()['image'];
            $filePath = public_path('storage/' . $existingImage);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $image = $request->file('image')->store('images', 'public');
            $note->image = $image;
        }

        $note->update($request->all());

        if ($request->has('tags')) {
            $note->tags()->sync($request->input('tags'));
        }

        return $note;
    }

    public function deleteNote($user, $id)
    {
        $note = $user->notes()->find($id);

        if (!$note) {
            return false;
        }

        $image = $note->getAttributes()['image'];
        if (File::exists(public_path('storage/' . $image))) {
            File::delete(public_path('storage/' . $image));
        }

        $note->delete();
        return true;
    }
}

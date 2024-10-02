<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use App\Services\NoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NoteTest extends TestCase
{
    protected $noteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->noteService = new NoteService();
        Storage::fake('public');
    }
    
    public function test_getAllNotesForUser()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $user = User::create([
            'name' => 'test user',
            'email' => 'test@gmail.com',
            'password' => Hash::make('123qwe'),
        ]);
        
        $note1 = new Note(['title' => 'Note 1', 'description' => 'Content for note 1', 'user_id' => $user->id]);
        $note1->save();

        $note2 = new Note(['title' => 'Note 2', 'description' => 'Content for note 2', 'user_id' => $user->id]);
        $note2->save();

        $notes = $this->noteService->getAllNotesForUser($user);

        $this->assertCount(2, $notes);
        $this->assertTrue($notes->contains($note1));
        $this->assertTrue($notes->contains($note2));
    }

    public function test_getNoteById()
    {
        Artisan::call('migrate');

        $user = User::create([
            'name' => 'test user',
            'email' => 'test@gmail.com',
            'password' => Hash::make('123qwe'),
        ]);

        $note = new Note(['title' => 'Note 1', 'description' => 'descripción', 'user_id' => $user->id]);
        $note->save();

        $foundNote = $this->noteService->getNoteById($user, $note->id);

        $this->assertEquals($note->id, $foundNote->id);
    }

    public function test_createNote()
    {
        Artisan::call('migrate');
        Artisan::call('passport:client', [
            '--personal' => true,
        ]);
        Artisan::call('db:seed');

        $user = User::create([
            'name' => 'test user',
            'email' => 'test@gmail.com',
            'password' => Hash::make('123qwe'),
        ]);

        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('notes.store'), [
            'title' => 'Test Note',
            'description' => 'This is a test note.',
            'tags' => [1, 2]
        ]);

        $response->assertStatus(201);
    }
}

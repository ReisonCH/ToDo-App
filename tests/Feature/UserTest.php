<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;


class UserTest extends TestCase
{
    public function test_register() {

        Artisan::call('migrate');

        $response = $this->postJson(route('auth.register'), [
            "name" => "test user",
            "email" => "test@gmail.com",
            "password" => "123qwe",
            "password_confirmation" => "123qwe"
        ]);
        $response->assertStatus(201);


        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
            'name'  => 'test user',
        ]);

        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function test_register_fails_with_missing_name()
    {
        Artisan::call('migrate');
        $response = $this->postJson(route('auth.register'), [
            "email" => "test@gmail.com",
            "password" => "123qwe",
            "password_confirmation" => "123qwe"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_register_fails_when_passwords_do_not_match()
    {
        Artisan::call('migrate');
        $response = $this->postJson(route('auth.register'), [
            "name" => "test user",
            "email" => "test@gmail.com",
            "password" => "123qwe",
            "password_confirmation" => "wrongpassword"
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_login_successful()
    {
        Artisan::call('migrate');

        Artisan::call('passport:client', [
            '--personal' => true,
        ]);

        // Crea un usuario
        $user = User::create([
            'name' => 'test user',
            'email' => 'test@gmail.com',
            'password' => Hash::make('123qwe'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@gmail.com',
            'password' => '123qwe',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'token',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    
}

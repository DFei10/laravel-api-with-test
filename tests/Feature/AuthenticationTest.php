<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_existing_users_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertCreated();
        $response->assertJsonStructure(['access_token']);
    }

    public function test_users_cannot_login_with_bad_credentials(): void
    {
        $this->postJson(
            '/api/auth/login',
            ['email' => $this->faker->email(), 'password' => 'password']
        )->assertJsonValidationErrors(['email' => 'The provided credentials are incorrect.']);

        $this->postJson(
            '/api/auth/login',
            ['email' => $this->faker->email()]
        )->assertJsonValidationErrors(['password' => 'The password field is required.']);

        $this->postJson(
            '/api/auth/login',
            ['password' => 'password']
        )->assertJsonValidationErrors(['email' => 'The email field is required.']);
    }

    public function test_authenticated_users_can_logout(): void
    {
        $user = User::factory()->create();

        $loginResponse = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password']);

        $loginResponse->assertCreated();

        $this->assertCount(1, $user->tokens);
        $this->postJson('/api/auth/logout', [], ['Authorization' => "Bearer {$loginResponse->json('access_token')}"])->assertNoContent();
        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_unauthenticated_users_cannot_logout(): void
    {
        $this->postJson('/api/auth/logout')->assertUnauthorized();
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RegisterationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @return array<string,string>|array
     */
    protected function prepareBody(?string $except = null): array
    {
        $data = [
            'email' => $this->faker->email(),
            'name' => $this->faker->name(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        if ($except) {
            $data = array_filter($data, function (string $key) use ($except) {
                return $key !== $except;
            }, ARRAY_FILTER_USE_KEY);
        }

        return $data;
    }

    public function test_users_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', $this->prepareBody());

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['access_token']);
    }

    public function test_email_is_required_for_registration(): void
    {
        $response = $this->postJson('/api/auth/register', $this->prepareBody('email'));

        $response->assertJsonValidationErrors(['email' => 'The email field is required.']);
    }

    public function test_name_is_required_for_registration(): void
    {
        $response = $this->postJson('/api/auth/register', $this->prepareBody('name'));

        $response->assertJsonValidationErrors(['name' => 'The name field is required.']);
    }

    public function test_password_is_required_for_registration(): void
    {
        $response = $this->postJson('/api/auth/register', $this->prepareBody('password'));

        $response->assertJsonValidationErrors(['password' => 'The password field is required.']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->postJson('/api/auth/register', $this->prepareBody('password_confirmation'));

        $response->assertJsonValidationErrors(['password' => 'The password field confirmation does not match.']);
    }
}

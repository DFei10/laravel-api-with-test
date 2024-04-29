<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected string $accessToken;

    public function setUp(): void
    {
        parent::setUp();

        $user = Profile::factory()->create()->user;

        $this->accessToken = $user->createToken('testing')->plainTextToken;
    }

    public function test_user_can_view_his_profile(): void
    {
        $this->withToken($this->accessToken)
            ->getJson('/api/profile')
            ->assertJsonStructure([
                'email',
                'name',
                'profile' => [
                    'birth_date',
                    'avatar_path',
                    'state',
                    'bio',
                ],
            ]);
    }

    public function test_user_can_update_his_name(): void
    {
        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['name' => $name = $this->faker->name()])
            ->assertJsonPath('name', $name);

        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['name' => ''])
            ->assertJsonValidationErrorFor('name')
            ->assertJsonValidationErrors(['name' => 'The name field is required.']);
    }

    public function test_user_can_update_his_email(): void
    {
        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['email' => $email = $this->faker->email()])
            ->assertJsonPath('email', $email);

        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['email' => ''])
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The email field is required.']);
    }

    public function test_user_can_update_his_birth_date(): void
    {
        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['birth_date' => $birthDate = $this->faker->date()])
            ->assertJsonPath('profile.birth_date', $birthDate);

        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['birth_date' => ''])
            ->assertJsonValidationErrorFor('birth_date')
            ->assertJsonValidationErrors(['birth_date' => 'The birth date field is required.']);
    }

    public function test_user_can_update_his_state(): void
    {
        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['state' => $state = $this->faker->state()])
            ->assertJsonPath('profile.state', $state);

        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['state' => ''])
            ->assertJsonValidationErrorFor('state')
            ->assertJsonValidationErrors(['state' => 'The state field is required.']);
    }

    public function test_user_can_update_his_bio(): void
    {
        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['bio' => $bio = $this->faker->text()])
            ->assertJsonPath('profile.bio', $bio);

        $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['bio' => ''])
            ->assertJsonPath('profile.bio', null);
    }

    public function test_user_can_update_his_avatar(): void
    {
        Storage::fake('public');

        $response = $this->withToken($this->accessToken)
            ->patchJson('/api/profile', ['avatar' => UploadedFile::fake()->image('avatar.jpg')]);

        $response->assertOk();

        Storage::disk('public')->assertExists($response->json('profile.avatar_path'));
    }
}

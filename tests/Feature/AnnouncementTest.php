<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_students_cannot_post(): void
    {
        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $response = $this->withToken($accessToken)->postJson('/api/announcements');

        $response->assertStatus(403);
    }

    public function test_teachers_can_post(): void
    {
        $announcement = Announcement::factory()->make();

        $accessToken = $announcement->user->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->postJson('/api/announcements', $announcement->toArray())
            ->assertJsonPath('title', $announcement->title)
            ->assertJsonPath('description', $announcement->description)
            ->assertJsonPath('location', $announcement->type === 'online' ? null : $announcement->location)
            ->assertJsonPath('price', $announcement->price)
            ->assertJsonPath('student_count', $announcement->student_count)
            ->assertJsonPath('type', $announcement->type)
            ->assertJsonPath('status', $announcement->status);
    }

    public function test_type_and_status_are_valid(): void
    {
        $accessToken = User::factory()->create(['category' => 1])->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->postJson(
            '/api/announcements',
            ['type' => 'offline', 'status' => 'opened']
        )
            ->assertStatus(422)
            ->assertValid(['type', 'status']);

        $this->withToken($accessToken)->postJson(
            '/api/announcements',
            ['type' => 'online', 'status' => 'closed']
        )
            ->assertStatus(422)
            ->assertValid(['type', 'status']);

        $this->withToken($accessToken)->postJson(
            '/api/announcements',
            ['type' => 'test', 'status' => 'test']
        )
            ->assertStatus(422)
            ->assertInvalid(['type', 'status'])
            ->assertJsonValidationErrors(
                [
                    'type' => 'the type field must be either online or offline.',
                    'status' => 'the status field must be either closed or opened.',
                ]
            );
    }

    public function test_location_is_required_when_type_is_offline(): void
    {
        $accessToken = User::factory()->create(['category' => 1])->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->postJson(
            '/api/announcements',
            Announcement::factory()->raw(['type' => 'online', 'location' => null])
        )
            ->assertOk()
            ->assertJsonPath('location', null);

        $this->withToken($accessToken)->postJson(
            '/api/announcements',
            Announcement::factory()->raw(['type' => 'offline', 'location' => 'the sun'])
        )
            ->assertOk()
            ->assertJsonPath('location', 'the sun');

        $this->withToken($accessToken)->postJson(
            '/api/announcements',
            Announcement::factory()->raw(['type' => 'offline', 'location' => null])
        )
            ->assertStatus(422)
            ->assertInvalid(['location']);
    }

    public function test_teachers_can_update_only_their_posts(): void
    {
        $announcement = Announcement::factory()->create();

        $accessToken = User::factory()->create(['category' => 1])->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->patchJson('/api/announcements/'.$announcement->id)
            ->assertForbidden();
    }

    public function test_teachers_can_update_their_posts(): void
    {
        $announcement = Announcement::factory()->create();

        $accessToken = $announcement->user->createToken('test')->plainTextToken;

        $updates = Announcement::factory()->raw();

        $response = $this->withToken($accessToken)->patchJson(
            '/api/announcements/'.$announcement->id,
            [
                'title' => $updates['title'],
                'description' => $updates['description'],
                'price' => $updates['price'],
                'student_count' => $updates['student_count'],
            ]
        );

        $response->assertJsonPath('title', $updates['title'])
            ->assertJsonPath('description', $updates['description'])
            ->assertJsonPath('price', $updates['price'])
            ->assertJsonPath('student_count', $updates['student_count']);
    }

    public function test_teachers_can_update_post_location(): void
    {
        $announcement = Announcement::factory()->create();

        $accessToken = $announcement->user->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->patchJson(
            '/api/announcements/'.$announcement->id,
            [
                'type' => 'online',
            ]
        )->assertJsonPath('type', 'online')
            ->assertJsonPath('location', null);

        $this->withToken($accessToken)->patchJson(
            '/api/announcements/'.$announcement->id,
            [
                'type' => 'offline',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('location');

        $this->withToken($accessToken)->patchJson(
            '/api/announcements/'.$announcement->id,
            [
                'type' => 'offline',
                'location' => 'the sun',
            ]
        )
            ->assertJsonPath('type', 'offline')
            ->assertJsonPath('location', 'the sun');

        $this->withToken($accessToken)->patchJson(
            '/api/announcements/'.$announcement->id,
            [
                'type' => 'offline',
            ]
        )
            ->assertJsonPath('type', 'offline')
            ->assertJsonPath('location', 'the sun');

    }

    public function test_teachers_can_delete_only_their_posts(): void
    {
        $announcement = Announcement::factory()->create();

        $accessToken = User::factory()->create(['category' => 1])->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->deleteJson('/api/announcements/'.$announcement->id)
            ->assertForbidden();
    }

    public function test_techers_can_delete_their_posts(): void
    {
        $announcement = Announcement::factory()->create();

        $accessToken = $announcement->user->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->delete('/api/announcements/'.$announcement->id)
            ->assertNoContent();
    }
}

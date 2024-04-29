<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_students_can_enroll(): void
    {
        $announcement = Announcement::factory()->create(['student_count' => 1, 'status' => 'opened']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->assertCount(0, $announcement->enrolls);

        $this->withToken($accessToken)
            ->postJson('/api/announcements/'.$announcement->id.'/enrolls')
            ->assertNoContent();

        $this->assertCount(1, $announcement->fresh()->enrolls);
    }

    public function test_students_cannot_enroll_to_a_closed_post(): void
    {
        $announcement = Announcement::factory()->create(['student_count' => 1, 'status' => 'closed']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->postJson('/api/announcements/'.$announcement->id.'/enrolls')
            ->assertForbidden();

        $this->assertCount(0, $announcement->enrolls);
    }

    public function test_students_can_enroll_only_when_there_available_place(): void
    {
        $announcement = Announcement::factory()->create(['student_count' => 0, 'status' => 'opened']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->postJson('/api/announcements/'.$announcement->id.'/enrolls')
            ->assertForbidden();

        $this->assertCount(0, $announcement->enrolls);
    }
}

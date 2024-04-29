<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementLikeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_users_can_like_announcements()
    {
        $announcement = Announcement::factory()->create();

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->assertCount(0, $announcement->likes);

        $this->withToken($accessToken)
            ->postJson("/api/announcements/{$announcement->id}/likes")
            ->assertNoContent();

        $this->assertCount(1, $announcement->fresh()->likes);
    }

    public function test_users_can_unlike_announcements()
    {
        $announcement = Announcement::factory()->create();

        $user = User::factory()->create();

        $like = Like::factory()->create(['user_id' => $user, 'announcement_id' => $announcement->id]);

        $accessToken = $user->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->deleteJson("/api/announcements/{$announcement->id}/likes")
            ->assertNoContent();

        $this->assertDatabaseMissing('likes', $like->toArray());
    }
}

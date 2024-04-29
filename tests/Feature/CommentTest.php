<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_users_can_comment_on_posts(): void
    {
        $announcement = Announcement::factory()->create();

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)
            ->postJson("/api/announcements/{$announcement->id}/comments", ['comment' => $comment = $this->faker->text()])
            ->assertOk()
            ->assertJsonPath('comment', $comment)
            ->assertJsonPath('announcement_id', $announcement->id);
    }

    public function test_users_cannot_edit_other_users_comments(): void
    {
        $comment = Comment::factory()->create();

        $accessToken = User::factory()->create()->createToken('text')->plainTextToken;

        $this->withToken($accessToken)
            ->patchJson("/api/announcements/{$comment->announcement->id}/comments/{$comment->id}")
            ->assertStatus(403);

        $anotherAnnouncement = Announcement::factory()->create();

        $this->withToken($accessToken)
            ->patchJson("/api/announcements/{$anotherAnnouncement->id}/comments/{$comment->id}")
            ->assertStatus(404);
    }

    public function test_users_can_edit_their_comments(): void
    {
        $comment = Comment::factory()->create();

        $accessToken = $comment->user->createToken('text')->plainTextToken;

        $this->withToken($accessToken)
            ->patchJson(
                "/api/announcements/{$comment->announcement->id}/comments/{$comment->id}",
                ['comment' => $comment = $this->faker->text()]
            )
            ->assertOk()
            ->assertJsonPath('comment', $comment);
    }

    public function test_users_cannot_delete_other_users_comments(): void
    {
        $comment = Comment::factory()->create();

        $accessToken = User::factory()->create()->createToken('text')->plainTextToken;

        $this->withToken($accessToken)
            ->deleteJson(
                "/api/announcements/{$comment->announcement->id}/comments/{$comment->id}",
            )
            ->assertStatus(403);
    }

    public function test_users_can_delete_their_comments(): void
    {
        $comment = Comment::factory()->create();

        $accessToken = $comment->user->createToken('text')->plainTextToken;

        $this->withToken($accessToken)
            ->deleteJson(
                "/api/announcements/{$comment->announcement->id}/comments/{$comment->id}",
            )
            ->assertNoContent();

        $this->assertDatabaseMissing('comments', $comment->toArray());
    }
}

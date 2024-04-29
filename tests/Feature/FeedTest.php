<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_users_can_see_teachers_post(): void
    {
        $firstPost = Announcement::factory()->create(['status' => 'opened']);
        $this->travel(1)->minutes();
        $secondPost = Announcement::factory()->create(['status' => 'opened']);
        $this->travel(1)->minutes();
        $thirdPost = Announcement::factory()->create(['status' => 'opened']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;      

        $this->withToken($accessToken)->getJson('/api/feed')
            ->assertOk()
            ->assertJsonPath('data.0.id', $thirdPost->id)
            ->assertJsonPath('data.1.id', $secondPost->id)
            ->assertJsonPath('data.2.id', $firstPost->id);
    }

    public function test_feed_shows_likes_count_for_each_post(): void
    {
        $post = Announcement::factory()->create(['status' => 'opened']);

        Like::factory(5)->create(['announcement_id' => $post]);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->getJson('/api/feed')
            ->assertOk()
            ->assertJsonPath('data.0.likes_count', 5);
    }

    public function test_feed_shows_comments_count_for_each_post(): void
    {
        $post = Announcement::factory()->create(['status' => 'opened']);

        Comment::factory(5)->create(['announcement_id' => $post]);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->getJson('/api/feed')
            ->assertOk()
            ->assertJsonPath('data.0.comments_count', 5);
    }

    public function test_feed_does_not_show_closed_posts(): void
    {
        $post = Announcement::factory()->create(['status' => 'closed']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->getJson('/api/feed')
            ->assertOk()
            ->assertJsonMissing($post->toArray());
    }

    public function test_users_can_filter_posts_by_type(): void
    {
        $onlinePost = Announcement::factory()->create(['status' => 'opened', 'type' => 'online']);
        $offlinePost = Announcement::factory()->create(['status' => 'opened', 'type' => 'offline']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->getJson('/api/feed?type=online')
            ->assertOk()
            ->assertJsonMissing(['id' => $offlinePost->id])
            ->assertJsonPath('data.0.id', $onlinePost->id);

        $this->withToken($accessToken)->getJson('/api/feed?type=offline')
            ->assertOk()
            ->assertJsonMissing(['id' => $onlinePost->id])
            ->assertJsonPath('data.0.id', $offlinePost->id);

        $this->withToken($accessToken)->getJson('/api/feed?type=unknown')
            ->assertOk()
            ->assertJsonMissing(['id' => $onlinePost->id])
            ->assertJsonMissing(['id' => $offlinePost->id]);
    }

    public function test_users_can_search_for_posts(): void
    {
        $relevantPost = Announcement::factory()->create(['status' => 'opened', 'title' => 'post title']);
        $anotherRelevantPost = Announcement::factory()->create(['status' => 'opened', 'description' => 'post description']);
        $irrelevantPost = Announcement::factory()->create(['status' => 'opened']);

        $accessToken = User::factory()->create()->createToken('test')->plainTextToken;

        $this->withToken($accessToken)->getJson('/api/feed?search=post title')
            ->assertOk()
            ->assertJsonMissing(['id' => $anotherRelevantPost->id])
            ->assertJsonMissing(['id' => $irrelevantPost->id])
            ->assertJsonPath('data.0.id', $relevantPost->id);

        $this->withToken($accessToken)->getJson('/api/feed?search=post description')
            ->assertOk()
            ->assertJsonMissing(['id' => $relevantPost->id])
            ->assertJsonMissing(['id' => $irrelevantPost->id])
            ->assertJsonPath('data.0.id', $anotherRelevantPost->id);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementLikeController extends Controller
{
    public function store(Announcement $announcement)
    {
        if ($announcement->likes()->where('user_id', auth()->id())->count()) {
            return response()->json(['message' => 'You already liked the announcement'], 403);
        }

        $announcement->likes()->create(['user_id' => auth()->id()]);

        return response()->noContent();
    }

    public function destroy(Announcement $announcement)
    {
        $likes = $announcement->likes()->where('user_id', auth()->id())->get();

        if (! $likes->count()) {
            return response()->noContent(403);
        }

        $likes->each->delete();

        return response()->noContent();
    }
}

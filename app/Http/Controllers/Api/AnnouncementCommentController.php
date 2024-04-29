<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;

class AnnouncementCommentController extends Controller
{
    public function store(Announcement $announcement)
    {
        request()->validate([
            'comment' => ['required', 'string'],
        ]);

        return response()->json($announcement->comments()->create([
            'user_id' => auth()->id(),
            'comment' => request('comment'),
        ]));
    }

    public function update(Announcement $announcement, Comment $comment)
    {
        Gate::authorize('manage-comment', $comment);

        request()->validate([
            'comment' => ['required', 'string'],
        ]);

        $comment->update(['comment' => request('comment')]);

        return response()->json($comment->fresh());
    }

    public function destory(Announcement $announcement, Comment $comment)
    {
        Gate::authorize('manage-comment', $comment);

        $comment->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Comment;
use App\Models\Reply;
use Illuminate\Http\Request;

class AnnouncementReplyController extends Controller
{
    public function store(Announcement $announcement, Comment $comment, Request $request)
    {

        $request->validate([
            'reply' => ['required', 'string'],
        ]);

        Reply::create([
            'user_id' => auth()->id(),
            'announcement_id' => $announcement->id,
            'comment_id' => $comment->id,
            'reply' => $request->reply,
        ]);

        return response()->noContent();
    }
}

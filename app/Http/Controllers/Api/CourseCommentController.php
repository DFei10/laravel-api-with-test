<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Comment;
use Illuminate\Http\Request;

class CourseCommentController extends Controller
{
    public function store(Announcement $announcement , Request $request){
        
        $request->validate([
            'comment' =>  ['required', 'string'],
        ]);
        
        Comment::create([
            'user_id' => auth()->id(),
            'announcement_id' => $announcement->id,
            'comment' => $request->comment
        ]);

        return response()->noContent();
    }
}

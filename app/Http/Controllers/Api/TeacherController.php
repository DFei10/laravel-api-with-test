<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class TeacherController extends Controller
{
    public function show(User $teacher)
    {
        if (! $teacher->category) {
            return abort(404);
        }

        return response()->json($teacher->load('profile', 'certificates')->toArray());
    }
}

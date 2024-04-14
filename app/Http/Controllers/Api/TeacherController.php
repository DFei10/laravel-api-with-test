<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    public function show(User $teacher){

        return response()->json([
            'response' => $teacher
        ]);
    }
    
}

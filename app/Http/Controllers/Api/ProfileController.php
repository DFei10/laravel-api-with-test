<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'birth_date' =>  ['required', 'date_format:Y-m-d'],
            'avatar' => ['required', 'image'],
            'state' => ['string', 'required'],
            'bio' => ['string'],
        ]);


        Profile::create([
            'user_id' => auth()->id(),
            'birth_date' =>  $request->birth_date,
            'avatar_url' => $request->file('avatar')->store('avatars'),
            'state' => $request->state,
            'bio' => $request->bio,
        ]);

        //php artisan storage:link

        return response()->noContent();
    }

    public function show(Request $request)
    {
        return response()->json([
            'response' => $request->user()->load('profile'),
        ]);
    }
}

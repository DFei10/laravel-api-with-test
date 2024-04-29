<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return response()->json(
            request()->user()->load('profile')
        );
    }

    public function update()
    {
        if (request()->hasAny(['birth_date', 'state', 'bio', 'avatar'])) {
            $attributes = request()->validate([
                'birth_date' => 'sometimes|required|date',
                'state' => 'sometimes|required|string',
                'bio' => 'string',
                'avatar' => 'sometimes|required|image'
            ]);

            if (request()->has('avatar')) {
                $path = Storage::putFile('avatars', $attributes['avatar']);
                unset($attributes['avatar']);

                if ($path) {
                    $attributes['avatar_path'] = $path;
                }
            }
            
            auth()->user()->profile()->update($attributes);
        } else {
            $attributes = request()->validate([
                'name' => 'sometimes|required',
                'email' => 'sometimes|required|email',
            ]);
            auth()->user()->update($attributes);
        }

        return auth()->user()->load('profile');
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke()
    {
        // @phpstan-ignore-next-line
        auth()->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}

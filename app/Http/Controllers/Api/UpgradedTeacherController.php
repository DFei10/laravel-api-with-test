<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpgradedTeacherController extends Controller
{
    /**
     * @return never|JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        if (auth()->user()->category === 1) {
            return abort(409);
        }

        $attributes = request()->validate([
            'certificates.*.title' => 'required|string',
            'certificates.*.university' => 'required|string',
            'certificates.*.graduation_date' => 'required|date',
        ]);

        $request->user()->update([
            'category' => 1,
        ]);

        $certificates = auth()->user()->certificates()->createMany($attributes['certificates']);

        return response()->json(auth()->user()->setRelation('certificates', $certificates)->toArray());
    }
}

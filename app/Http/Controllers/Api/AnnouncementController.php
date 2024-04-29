<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function store(StoreAnnouncementRequest $request)
    {
        return response()->json(auth()->user()->announcements()->create($request->validated()));
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        $validated = $request->safe()->all();

        if ($request->type === 'online') {
            $validated['location'] = null;
        }

        $announcement->update($validated);

        return response()->json($announcement->fresh());
    }

    public function destroy(Announcement $announcement)
    {
        if (! ($announcement->user_id == auth()->id())) {
            return abort(403);
        }

        $announcement->delete();

        return response()->noContent();
    }
}

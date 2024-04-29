<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementEnrollmentController extends Controller
{
    public function __invoke(Announcement $announcement)
    {
        if ($announcement->isClosed()) {
            return response()->json(['message' => 'Enrollment is closed for this announcement.'], 403);
        }

        if (! $announcement->hasAvailableEnrollment()) {
            return response()->json(['message' => 'Enrollment for this announcement is currently full'], 403);
        }

        $announcement->enrolls()->create(['user_id' => auth()->id()]);

        return response()->noContent();
    }
}

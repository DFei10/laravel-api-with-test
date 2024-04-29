<?php

namespace App\Http\Controllers\Api;

use App\AnnouncementFilter;
use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementFeedContoller extends Controller
{
    public function index(AnnouncementFilter $filter)
    {
        $filter->apply($query = Announcement::query());

        return response()->json(
            $query->withCount(['likes', 'comments'])
                ->where('status', Announcement::STATUS_OPENED)
                ->latest()
                ->paginate(20)
        );
    }
}

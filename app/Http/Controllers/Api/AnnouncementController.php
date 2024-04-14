<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function PHPSTORM_META\type;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {



        $request->validate([
            'title' =>  ['required', 'string'],
            'description' => ['required', 'string'],
            'type' =>  ['required', 'numeric'],
            'location' => ['required', 'string'],
            'price' => ['required', 'string'],
            'status' => ['required', 'numeric'],
            'student_count' => ['required', 'numeric'],
        ]);


        if (!in_array($request->type, [0, 1]) || !in_array($request->status, [0, 1])) {
            return response()->json(['error' => 'Resource not found'], 404);
        }


        Announcement::create([
            'user_id' => auth()->id(),
            'title' =>  $request->title,
            'description' =>  $request->description,
            'type' =>  $request->type,
            'location' =>  $request->location,
            'price' =>  $request->price,
            'status' =>  $request->status,
            'student_count' =>  $request->student_count,
        ]);

        //php artisan storage:link

        return response()->noContent();
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' =>  ['string'],
            'description' => ['string'],
            'type' =>  ['numeric'],
            'location' => ['string'],
            'price' => ['string'],
            'status' => ['numeric'],
            'student_count' => ['numeric'],
        ]);

        if ($request->filled('title')) {
            $requestData['title'] = $request->title;
        }

        $announcement->update([
            'title' => $request->filled('title') ? $request->title : DB::raw('title'),
            'description' => $request->filled('description') ? $request->description : DB::raw('description'),
            'type' => $request->filled('type') ? $request->type : DB::raw('type'),
            'location' => $request->filled('location') ? $request->location : DB::raw('location'),
            'price' => $request->filled('price') ? $request->price : DB::raw('price'),
            'status' => $request->filled('status') ? $request->status : DB::raw('status'),
            'student_count' => $request->filled('student_count') ? $request->student_count : DB::raw('student_count'),
        ]);

        return response()->noContent();
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->noContent();
    }
}

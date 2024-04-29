<?php

use App\Http\Controllers\Api\AnnouncementCommentController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AnnouncementEnrollmentController;
use App\Http\Controllers\Api\AnnouncementFeedContoller;
use App\Http\Controllers\Api\AnnouncementLikeController;
use App\Http\Controllers\Api\AnnouncementReplyController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UpgradedTeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/register', RegisterController::class);

Route::post('auth/login', LoginController::class);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', LogoutController::class);

    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::get('/profile', [ProfileController::class, 'show']);

    Route::get('/teacher/{teacher}', [TeacherController::class, 'show']);

    Route::post('/become-teacher', [UpgradedTeacherController::class, 'store']);

    Route::post('/announcements', [AnnouncementController::class, 'store'])->middleware('teacher');
    Route::patch('/announcements/{announcement}', [AnnouncementController::class, 'update'])->middleware('teacher');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->middleware('teacher');

    Route::get('/feed', [AnnouncementFeedContoller::class, 'index']);

    Route::post('/announcements/{announcement}/enrolls', AnnouncementEnrollmentController::class)->middleware('student');

    Route::post('/announcements/{announcement}/likes', [AnnouncementLikeController::class, 'store']);
    Route::post('/announcements/{announcement}/likes', [AnnouncementLikeController::class, 'store']);
    Route::delete('/announcements/{announcement}/likes', [AnnouncementLikeController::class, 'destroy']);

    Route::post('/announcements/{announcement}/comments', [AnnouncementCommentController::class, 'store']);
    Route::patch('/announcements/{announcement}/comments/{comment}', [AnnouncementCommentController::class, 'update'])->scopeBindings();
    Route::delete('/announcements/{announcement}/comments/{comment}', [AnnouncementCommentController::class, 'destory'])->scopeBindings();

    Route::post('/announcements/{announcement}/comments/{comment}/replies', [AnnouncementReplyController::class, 'store']);
});

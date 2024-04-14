<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AnnouncementsFeedContoller;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CourseCommentController;
use App\Http\Controllers\Api\CourseEnrollmentController;
use App\Http\Controllers\Api\CourseLikeController;
use App\Http\Controllers\Api\CourseReplyController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\TeacherUpgradeController;
use App\Http\Controllers\Api\UpdateAvatarController;
use App\Http\Controllers\Api\UpdateBioController;
use App\Http\Controllers\Api\UpdateBirth_dateController;
use App\Http\Controllers\Api\UpdateStateController;
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
    // Profile Routes
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::patch('/profile/birth_date', UpdateBirth_dateController::class);
    Route::patch('/profile/state', UpdateStateController::class);
    Route::patch('/profile/avatar', UpdateAvatarController::class);
    Route::patch('/profile/bio', UpdateBioController::class);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/teacher/{teacher}', [TeacherController::class, 'show']);

    // get all teachers

    // become teacher
    // certificates here
    Route::post('/become-teacher', [TeacherUpgradeController::class, 'store'])->middleware('student');

    Route::post('/announcements', [AnnouncementController::class, 'store'])->middleware('teacher');
    Route::patch('/announcements/{announcement}', [AnnouncementController::class, 'update'])->middleware('teacher');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->middleware('teacher');

    Route::get('/feed', [AnnouncementsFeedContoller::class, 'index']);

    Route::post('/announcements/{announcement}/enrolls', CourseEnrollmentController::class)->middleware('student');

    // //likes + comments + replies
    Route::post('/announcements/{announcement}/likes' , [CourseLikeController::class, 'store']);
    Route::delete('/announcements/{announcement}/likes' , [CourseLikeController::class, 'destroy'] );
    Route::post('/announcements/{announcement}/comments' , [CourseCommentController::class, 'store']);
    Route::post('/announcements/{announcement}/comments/{comment}/replies' , [CourseReplyController::class, 'store']);
});

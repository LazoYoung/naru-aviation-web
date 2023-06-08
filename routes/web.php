<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThreadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/forum', [ForumController::class, 'getView'])
    ->middleware(['auth', 'verified'])
    ->name('forum.browse');

Route::controller(ThreadController::class)->group(function () {
    Route::get('/thread', 'show')
        ->middleware(['auth', 'verified'])
        ->name('forum.thread.show');

    Route::get('/thread/fetch', 'fetch')
        ->name('forum.thread.fetch');

    Route::post('/thread/new', 'store')
        ->middleware(['auth', 'verified'])
        ->name('forum.thread.store');

    Route::get('/thread/content-peek', 'getContentPeek')
        ->name('forum.thread.content-peek');

    Route::get('/thread/view-count', 'getViewCount')
        ->name('forum.thread.view-count');

    Route::get('/thread/author-name', 'getAuthorName')
        ->name('forum.thread.author-name');

    Route::get('/thread/created-time', 'getCreatedTime')
        ->name('forum.thread.created-time');
});

Route::controller(PostController::class)->group(function () {
    Route::post('/post/new', 'store')
        ->middleware(['auth', 'verified'])
        ->name('forum.post.store');

    Route::get('/post/content', 'getContent')
        ->name('forum.post.content');

    Route::get('/post/like-count', 'getLikeCount')
        ->name('forum.post.like-count');

    Route::get('/post/has-liked', 'hasLiked')
        ->middleware(['auth', 'verified'])
        ->name('forum.post.has-liked');

    Route::post('/post/like', 'like')
        ->middleware(['auth', 'verified'])
        ->name('forum.post.like');

    Route::post('/post/dislike', 'dislike')
        ->middleware(['auth', 'verified'])
        ->name('forum.post.dislike');
});

Route::controller(CalendarController::class)->group(function () {
    Route::get('/calendar', 'show')
        ->name('calendar.show');

    Route::post('/calendar/new', 'submitNewEvent')
        ->middleware(['auth', 'verified'])
        ->name('calendar.new');

    Route::post('/calendar/update', 'updateEvent')
        ->middleware(['auth', 'verified'])
        ->name('calendar.update');
});

Route::controller(FileController::class)->group(function () {
    Route::post('/upload', 'upload')
        ->middleware(['auth', 'verified'])
        ->name('file.upload');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

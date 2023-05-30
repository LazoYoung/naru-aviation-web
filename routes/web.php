<?php

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/forum', [ForumController::class, 'browseView'])
    ->middleware(['auth', 'verified'])
    ->name('forum.browse');

Route::get('/thread', [ThreadController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('forum.thread.show');

Route::post('/thread/new', [ThreadController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('forum.thread.store');

Route::get('/thread/content-peek', [ThreadController::class, 'getContentPeek'])
    ->name('forum.thread.content-peek');

Route::get('/thread/view-count', [ThreadController::class, 'getViewCount'])
    ->name('forum.thread.view-count');

Route::get('/thread/author-name', [ThreadController::class, 'getAuthorName'])
    ->name('forum.thread.author-name');

Route::get('/thread/created-time', [ThreadController::class, 'getCreatedTime'])
    ->name('forum.thread.created-time');

Route::get('/post/like-count', [PostController::class, 'getLikeCount'])
    ->name('forum.post.like-count');

Route::get('/post/has-liked', [PostController::class, 'hasLiked'])
    ->middleware(['auth', 'verified'])
    ->name('forum.post.has-liked');

Route::post('/post/like', [PostController::class, 'like'])
    ->middleware(['auth', 'verified'])
    ->name('forum.post.like');

Route::post('/post/dislike', [PostController::class, 'dislike'])
    ->middleware(['auth', 'verified'])
    ->name('forum.post.dislike');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

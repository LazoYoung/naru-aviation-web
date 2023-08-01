<?php

use App\Http\Controllers\AirportController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThreadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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

Route::get('/', function (Request $request) {
    return Inertia::render('Home', [
        'mapApiKey' => config('app.map_api_key'),
        'isAdmin' => $request->user()?->is_admin ?? false,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::controller(PilotController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/dispatch', 'getDispatchView')
            ->name('pilot.dispatch.view');
        Route::post('/dispatch', 'dispatchFlight')
            ->name('pilot.dispatch.submit');
    });

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

    Route::get('/thread/author-gravatar', 'getAuthorGravatarHash')
        ->name('forum.thread.author-gravatar');

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

    Route::get('/post/gravatar', 'getGravatarHash')
        ->name('forum.post.gravatar');

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

Route::controller(GalleryController::class)->group(function () {
    Route::get('/gallery', 'showGallery')
        ->name('image.show.gallery');

    Route::get('/image/fetch/all', 'getImages')
        ->name('image.fetch.all');

    Route::post('/image/new', 'submitImage')
        ->name('image.submit.new');
});

Route::controller(EventController::class)->group(function () {
    Route::get('/calendar', 'showCalendar')
        ->name('event.show.calendar');

    Route::get('/event/thread', 'visitEventThread')
        ->name('event.thread');

    Route::get('/event/fetch/all', 'getEvents')
        ->name('event.fetch.all');

    Route::post('/event/new', 'submitNewEvent')
        ->middleware(['auth', 'auth.admin'])
        ->name('event.submit.new');

    Route::post('/event/update', 'updateEvent')
        ->middleware(['auth', 'auth.admin'])
        ->name('event.submit.update');
});

Route::controller(FileController::class)->group(function () {
    Route::post('/upload', 'upload')
        ->middleware(['auth', 'verified'])
        ->name('file.upload');
});

Route::controller(AirportController::class)->group(function () {
    Route::post('/airport/new', 'addAirport')
        ->middleware(['auth', 'auth.admin'])
        ->name('airport.new');
    Route::get('/airport/fetch/all', 'fetchAirports')
        ->name('airport.fetch.all');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

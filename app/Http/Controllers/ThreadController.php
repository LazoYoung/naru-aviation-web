<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Post;
use App\Models\Thread;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ThreadController extends Controller {
    private const hour = 60;
    private const day = 24 * self::hour;
    private const month = 30 * self::day;
    private const year = 12 * self::month + 5 * self::day;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $max = sizeof(Category::cases()) - 1;
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:32',
            'category' => 'required|int|min:0|max:'.$max,
            'content' => 'required',
        ]);
        $thread = $this->createThread($request, $validated);
        $post = new Post([
            'content' => $validated['content'],
        ]);
        $post->thread()->associate($thread);
        $post->user()->associate($request->user());

        if (!$post->save()) {
            abort(500);
        }

        return redirect(route('forum.thread.show', [
            'id' => $thread->getQueueableId(),
        ]));
    }

    /**
     * Display the specified resource.
     *
     * Method: POST
     * Expected input: id
     */
    public function show(Request $request) {
        $this->validateThread($request);

        $thread = Thread::find($request->query('id'));
        return Inertia::render('Forum/Thread', [
            'thread' => $thread,
            'posts' => $thread->posts()->oldest()->get(),
            'user' => $request->user(),
        ]);
    }

    public function getContentPeek(Request $request): Response {
        $this->validateThread($request);

        $thread = Thread::find($request->query('id'));
        $content = $thread->posts->first()->content;
        return response(Str::limit($content, 20));
    }

    /**
     * Get number of views for the given thread.
     *
     * Method: GET
     * Expected query: id
     */
    public function getViewCount(Request $request): Response {
        $this->validateThread($request);

        // todo restructure database to implement
        $thread = Thread::find($request->query('id'));
        return response(0);
    }

    /**
     * Get name of the original thread creator.
     *
     * Method: GET
     * Expected query: id
     */
    public function getAuthorName(Request $request): Response {
        $this->validateThread($request);

        $thread = Thread::find($request->query('id'));
        $name = $thread->user->name;
        return response($name);
    }

    /**
     * Get how much time has passed after creating the thread.
     *
     * Method: GET
     * Expected query: id
     */
    public function getCreatedTime(Request $request): Response {
        $this->validateThread($request);

        $thread = Thread::find($request->query('id'));
        $ms = $thread->created_at
            ->diffAsCarbonInterval(new Carbon())
            ->milliseconds;
        $format = $this->getTimeDiffFormat($ms);
        return response($format);
    }

    private function getTimeDiffFormat($ms) {
        $rem = round($ms / 60000);
        $y = floor($rem / self::year);
        $rem -= $y * self::year;
        $m = floor($rem / self::month);
        $rem -= $m * self::month;
        $d = floor($rem / self::day);
        $rem -= $d * self::day;
        $h = floor($rem / self::hour);
        $min = $rem - $h * self::hour;

        // todo l18n
        if ($y > 1) {
            return $y . " years ago";
        }
        if ($m > 1) {
            return $m . " months ago";
        }
        if ($d > 1) {
            return $d . " days ago";
        }
        if ($h > 1) {
            return $h . " hours ago";
        }
        if ($min > 1) {
            return $min . " minutes ago";
        }
        return "Just now";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thread $thread) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread) {
        //
    }

    private function createThread(Request $request, array $validated): Thread {
        return $request->user()->threads()->create([
            'title' => $validated['title'],
            'category' => $validated['category'],
        ]);
    }

    private function validateThread(Request $request) {
        return $request->validate([
            'id' => 'exists:App\Models\Thread,id'
        ]);
    }
}

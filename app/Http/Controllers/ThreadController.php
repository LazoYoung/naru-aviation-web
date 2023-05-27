<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThreadController extends Controller {
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
     */
    public function show(Request $request) {
        $thread = Thread::find($request->query('id'));

        if ($thread == null) {
            abort(404);
        }

        return Inertia::render('Forum/Thread', [
            'thread' => $thread,
            'posts' => $thread->posts()->oldest()->get(),
            'user' => $request->user(),
        ]);
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
}

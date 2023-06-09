<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Event;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class EventController extends Controller {
    public function showCalendar(Request $request): Response {
        return Inertia::render('Event/Calendar', [
            'admin' => $request->user()?->is_admin ?? false
        ]);
    }

    public function getEvents(): JsonResponse {
        return response()->json(Event::all()->toJson());
    }

    public function visitEventThread(Request $request) {
        $validated = $request->validate([
            'id' => 'exists:App\Models\Event,id',
        ]);

        try {
            $event = Event::find($validated['id']);
            $thread = Thread::whereBelongsTo($event)->firstOrFail();

            return redirect(route('forum.thread.show', [
                'id' => $thread->getQueueableId()
            ]));
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    public function submitNewEvent(Request $request) {
        try {
            $user = $request->user();
            $validated = $request->validate([
                'title' => ['string', 'min:5'],
                'start' => ['required', 'date'],
                'end' => ['required', 'date'],
                'description' => ['required', 'string'],
            ]);

            $event = new Event([
                'title' => $validated['title'],
                'start' => $validated['start'],
                'end' => $validated['end'],
            ]);
            $thread = new Thread([
                'title' => $validated['title'],
                'category' => Category::getId(Category::EVENT),
            ]);
            $post = new Post([
                'content' => $validated['description'],
            ]);

            $event->user()->associate($user);
            $event->saveOrFail();

            $thread->user()->associate($user);
            $thread->event()->associate($event);
            $thread->saveOrFail();

            $post->user()->associate($user);
            $post->thread()->associate($thread);
            $post->saveOrFail();
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }

        return response(null, 200);
    }

    public function updateEvent(Request $request) {
        try {
            $validated = $request->validate([
                'id' => 'exists:App\Models\Event,id',
                'title' => ['string', 'min:5'],
                'start' => ['required', 'date'],
                'end' => ['required', 'date'],
                'description' => ['required', 'string'],
            ]);

            $event = Event::find($validated['id']);
            $event->title = $validated['title'];
            $event->start = $validated['start'];
            $event->end = $validated['end'];
            $event->saveOrFail();

            $postId = Thread::whereBelongsTo($event)->posts()->firstOrFail()->getQueueableId();
            $post = Post::find($postId);
            $post->content = $validated['description'];
            $post->saveOrFail();
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }

        return response(null, 500);
    }
}

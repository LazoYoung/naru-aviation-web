<?php

namespace Tests\Feature\Event;

use App\Models\Event;
use App\Models\Thread;
use App\Models\User;
use Tests\TestCase;

class CalendarTest extends TestCase {
    public function test_calendar_page_is_displayed(): void {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/calendar');

        $response->assertOk();
    }

    public function test_event_is_redirected(): void {
        $user = User::factory()->create();
        $event = $this->createEvent();
        $thread = $this->createThread();

        $event->user()->associate($user);
        $thread->event()->associate($event);
        $event->save();
        $thread->save();

        $this
            ->actingAs($user)
            ->get(route('event.thread', ['id' => $event->id]))
            ->assertRedirectToRoute('forum.thread.show', ['id' => $thread->id]);
    }

    private function createEvent(): Event {
        return Event::factory()->create();
    }

    private function createThread(): Thread {
        return Thread::factory()->create();
    }
}

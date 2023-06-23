<?php

namespace Event;

use App\Models\Event;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class EventCrudTest extends TestCase {
    public function test_event_can_be_added(): void {
        $user = User::factory()->create();
        $data = [
            'title' => 'Another event',
            'start' => Carbon::now(),
            'end' => Carbon::create(day: Carbon::now()->day + 1),
            'description' => 'Description of this event',
        ];

        $user->is_admin = true;
        $response = $this->actingAs($user)->post(route('event.submit.new'), $data);

        $response->assertOk();
    }

    public function test_invalid_add_is_rejected(): void {
        $user = User::factory()->create();
        $data = [];

        $user->is_admin = true;
        $response = $this->actingAs($user)->post(route('event.submit.new'), $data);

        $response->assertBadRequest();
    }

    public function test_unauthorized_add_is_rejected(): void {
        $user = User::factory()->create();
        $data = [];

        $user->is_admin = false;
        $response = $this->actingAs($user)->post(route('event.submit.new'), $data);

        $response->assertUnauthorized();
    }

    public function test_event_can_be_updated(): void {
        $user = User::factory()->create();
        $data = [
            'title' => 'Another event',
            'start' => Carbon::now(),
            'end' => Carbon::create(day: Carbon::now()->day + 1),
            'description' => 'Description of this event',
        ];
        $user->is_admin = true;
        $submitResp = $this->actingAs($user)->post(route('event.submit.new'), $data);
        $eventId = intval($submitResp->getContent());
        $newTitle = 'New title';
        $newDate = Carbon::create(month: Carbon::now()->month + 1);
        $newDesc = 'New description';

        $data = [
            'id' => $eventId,
            'title' => $newTitle,
            'start' => $newDate,
            'end' => $newDate,
            'description' => $newDesc,
        ];

        $response = $this->actingAs($user)->post(route('event.submit.update'), $data);
        $newEvent = Event::find($eventId);
        $postId = Thread::whereBelongsTo($newEvent)->first()->posts()->first()->getQueueableId();
        $newPost = Post::find($postId);

        $response->assertOk();
        self::assertEquals($newEvent->title, $newTitle);
        self::assertEquals($newEvent->start, $newDate);
        self::assertEquals($newEvent->end, $newDate);
        self::assertEquals($newPost->content, $newDesc);
    }

    public function test_invalid_update_is_rejected(): void {
        $user = User::factory()->create();
        $user->is_admin = true;
        $data = [];
        $response = $this->actingAs($user)->post(route('event.submit.update'), $data);

        $response->assertBadRequest();
    }

    public function test_unauthorized_update_is_rejected(): void {
        $user = User::factory()->create();
        $user->is_admin = false;
        $data = [];
        $response = $this->actingAs($user)->post(route('event.submit.update'), $data);

        $response->assertUnauthorized();
    }
}

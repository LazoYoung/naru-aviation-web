<?php

namespace Forum;

use App\Enums\Category;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadTest extends TestCase {
    use RefreshDatabase;

    public function test_fetch_all(): void {
        $user = User::factory()->create();
        $thread = Thread::factory()->create([
            'user_id' => $user->id
        ]);
        $count = Thread::count();

        $thread->save();
        $response = $this->actingAs($user)->get(route('forum.thread.fetch'));

        $this->assertCount($count, $response->json());
    }

    public function test_fetch_by_category(): void {
        $user = User::factory()->create();
        $category = Category::getId(Category::GENERAL);
        $data = ['category' => $category];
        $thread = $this->createThread($user, $data);
        $response = $this->actingAs($user)->get(route('forum.thread.fetch', [
            'category' => $category
        ]));
        $match = false;

        $response->assertOk();

        foreach ($response->json() as $item) {
            if ($item['id'] == $thread->id) {
                $match = true;
                break;
            }
        }

        $this->assertTrue($match);
    }

    public function test_fetch_by_title(): void {
        $user = User::factory()->create();
        $title = 'Title placeholder';
        $data = ['title' => $title];
        $thread = $this->createThread($user, $data);
        $response = $this->actingAs($user)->get(route('forum.thread.fetch', [
            'title' => $title
        ]));
        $match = false;

        $response->assertOk();

        foreach ($response->json() as $item) {
            if ($item['id'] == $thread->id) {
                $match = true;
                break;
            }
        }

        $this->assertTrue($match);
    }

    public function test_store_new_thread(): void {
        $user = User::factory()->create();
        $title = 'Title placeholder';
        $category = Category::getId(Category::GENERAL);
        $content = 'Content placeholder';
        $response = $this->actingAs($user)->post(route('forum.thread.store'), [
            'title' => $title,
            'category' => $category,
            'content' => $content,
        ]);

        $response->assertRedirectContains(route('forum.thread.show'));
    }

    public function test_thread_can_be_displayed(): void {
        $user = User::factory()->create();
        $thread = $this->createThread($user);
        $thread->save();

        $response = $this->actingAs($user)->get(route('forum.thread.show', [
            'id' => $thread->id
        ]));

        $response->assertOk();
    }

    private function createThread($user, $data = null): Thread {
        $data['user_id'] = $user->id;
        return Thread::factory()->create($data);
    }
}

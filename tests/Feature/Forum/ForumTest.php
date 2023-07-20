<?php

namespace Forum;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase {
    use RefreshDatabase;

    public function test_forum_can_be_rendered(): void {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('forum.browse'));

        $response->assertOk();
    }
}

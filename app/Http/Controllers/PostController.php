<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller {

    public function getLikeCount(Request $request): Response {
        $this->validatePost($request);

        $count = $this->getPost($request)->likes->count();
        return response($count);
    }

    /**
     * Method: POST
     * Expected input: post-id
     * Result: true/false
     * Possible status: 200
     */
    public function hasLiked(Request $request): Response {
        $this->validatePost($request);

        $post = $this->getPost($request);
        $user = $request->user();
        $like = $this->getLike($post, $user);
        return response(($like != null) ? 'true' : 'false');
    }

    /**
     * Method: POST
     * Expected input: post-id
     * Result: none
     * Possible status: 200, 500
     */
    public function like(Request $request): Response {
        $this->validatePost($request);

        $post = $this->getPost($request);
        $user = $request->user();
        $this->validateLike($post, $user, true);

        $like = new Like();
        $like->user()->associate($user);
        $like->post()->associate($post);

        if ($like->save()) {
            return response(null);
        } else {
            return response(null, 500);
        }
    }

    /**
     * Method: POST
     * Expected input: post-id
     * Result: none
     * Possible status: 200, 500
     */
    public function dislike(Request $request): Response {
        $this->validatePost($request);

        $post = $this->getPost($request);
        $user = $request->user();
        $this->validateLike($post, $user, false);

        if ($this->getLike($post, $user)->delete()) {
            return response(null);
        } else {
            return response(null, 500);
        }
    }

    private function getPost(Request $request): Post {
        return Post::find($request->input("post-id"));
    }

    private function getLike(Post $post, User $user): ?Like {
        $like = $post->likes()
            ->whereBelongsTo($user)
            ->first(); // QueryException
        return (fn($e):?Like=>$e)($like);
    }

    private function validatePost(Request $request) {
        $request->validate([
            'post-id' => ['exists:App\Models\Post,id']
        ]);
    }

    private function validateLike(Post $post, User $user, bool $like) {
        $prev = $this->getLike($post, $user);

        if (($like && $prev != null) || (!$like && $prev == null)) {
            abort(400);
        }
    }
}

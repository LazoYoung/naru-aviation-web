<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class SimbriefController {

    public function updateId(Request $request): Response {
        try {
            $user = $this->getUser($request);
            $user->simbrief_id = $request['id'];
            $user->saveOrFail();
            return response(null, 200);
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    public function getId(Request $request): Response {
        try {
            $user = $this->getUser($request);
            $id = $user->simbrief_id;
            return response($id, 200);
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    private function getUser(Request $request): User {
        return $request->user();
    }

}
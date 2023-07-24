<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class APIKeyController {

    private int $acars_length;

    public function __construct() {
        $this->acars_length = 32;
    }

    public function getACARSKey(Request $request) {
        $user = $this->getUser($request);
        $key = $user->acars_key;

        if ($key != null) {
            return response($key, 200);
        } else {
            return response(null, 404);
        }
    }

    public function resetACARSKey(Request $request): Response {
        $user = $this->getUser($request);
        try {
            $newKey = $this->generateKey($this->acars_length);
            $user->acars_key = $newKey;
            $user->saveOrFail();
        } catch (Throwable $e) {
            return response($e->getMessage(), 500);
        }
        return response($newKey, 200);
    }

    private function getUser(Request $request): User {
        return $request->user();
    }

    /**
     * @noinspection SpellCheckingInspection
     * @throws Exception thrown if random int cannot be generated
     */
    private function generateKey(int $length): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $clen = strlen($characters);
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $characters[random_int(0, $clen - 1)];
        }
        return $key;
    }
}

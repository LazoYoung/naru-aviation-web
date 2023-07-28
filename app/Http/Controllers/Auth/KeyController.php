<?php

namespace App\Http\Controllers\Auth;

use App\Models\Key;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class KeyController {

    private int $acars_length;

    public function __construct() {
        $this->acars_length = 32;
    }

    public function getACARSKey(Request $request) {
        $user = $this->getUser($request);
        $key = $user->keys->firstWhere('type', '=', 'acars');

        if ($key != null) {
            return response($key->value, 200);
        } else {
            return response(null, 404);
        }
    }

    public function resetACARSKey(Request $request): Response {
        try {
            $user = $this->getUser($request);
            $key = $user->keys->firstWhere('type', '=', 'acars');

            if ($key === null) {
                $key = new Key();
                $key->type = 'acars';
                $key->user()->associate($user);
            }

            $key->value = $this->generateKey();
            $key->saveOrFail();
            return response($key->value, 200);
        } catch (Throwable $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * @noinspection SpellCheckingInspection
     * @throws Exception thrown if random int cannot be generated
     */
    public function generateKey(): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz!@#$^*_';
        $clen = strlen($characters);
        $key = '';
        for ($i = 0; $i < $this->acars_length; $i++) {
            $key .= $characters[random_int(0, $clen - 1)];
        }
        return $key;
    }

    private function getUser(Request $request): User {
        return $request->user();
    }
}

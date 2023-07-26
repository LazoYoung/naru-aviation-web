<?php

namespace Database\Factories;

use App\Http\Controllers\Auth\KeyController;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
use Throwable;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Key>
 */
class KeyFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception thrown if key generation fails
     */
    public function definition(): array {
        $ctr = $this->getKeyController();

        return [
            'type' => 'acars',
            'value' => $ctr->generateKey(),
        ];
    }

    private function getKeyController(): KeyController {
        return App::make('App\Http\Controllers\Auth\KeyController');
    }
}

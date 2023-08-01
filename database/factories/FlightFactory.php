<?php

namespace Database\Factories;

use App\Models\Flight;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Flight>
 */
class FlightFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array {
        return [
            "refreshed_at" => Carbon::now(),
            "user_id" => User::factory(),
            "offline" => false,
            "status" => random_int(0, 4),
            "off_block" => Carbon::now()->subHour(),
            "on_block" => null,
            "latitude" => random_int(-90, 90),
            "longitude" => random_int(-180, 180),
            "altitude" => random_int(0, 30000),
            "airspeed" => random_int(0, 300),
            "heading" => random_int(0, 359),
        ];
    }

    public function offline(int $period = 1): FlightFactory {
        return $this->state(function () use ($period) {
            return [
                "offline" => true,
                "refreshed_at" => Carbon::now()->subMinutes($period)
            ];
        });
    }

    public function complete(bool $complete = true): FlightFactory {
        return $this->state(function () use ($complete) {
            return [
                "status" => 4,
                "off_block" => $complete ? Carbon::now()->subHour() : null,
                "on_block" => $complete ? Carbon::now()->subMinutes(10) : null
            ];
        });
    }

}

<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array {
        return [
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
            "user_id" => User::factory(),
            "preflight_at" => Carbon::now()->addMinutes(90)
        ];
    }

    public function overdue(int $minutes): BookingFactory {
        return $this->state(function () use ($minutes) {
            return [
                "preflight_at" => Carbon::now()->subMinutes($minutes)
            ];
        });
    }

}

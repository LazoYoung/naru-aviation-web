<?php

namespace Database\Factories;

use App\Models\Logbook;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Logbook>
 */
class LogbookFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array {
        return [
            "user_id" => User::factory(),
            "origin" => "RKSS",
            "destination" => "RKPC",
            "date" => Carbon::now(),
            "flight_time" => random_int(40, 80)
        ];
    }

}

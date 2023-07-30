<?php

namespace Database\Factories;

use App\Models\Beacon;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Beacon>
 */
class BeaconFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array {
        return [
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
            "flight_id" => Flight::factory(),
            "latitude" => random_int(-90, 90),
            "longitude" => random_int(-180, 180),
            "altitude" => random_int(0, 30000),
            "airspeed" => random_int(0, 280),
            "heading" => random_int(0, 359)
        ];
    }
}

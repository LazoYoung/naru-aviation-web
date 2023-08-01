<?php

namespace Database\Factories;

use App\Models\Flight;
use App\Models\Flightplan;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Flightplan>
 */
class FlightplanFactory extends Factory {

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
            "booking_id" => null,
            "flight_id" => null,
            "callsign" => "NAR" . random_int(1, 999),
            "aircraft" => "A320",
            "origin" => "RKSS",
            "destination" => "RKPC",
            "altitude" => "27000",
            "off_block" => Carbon::now()->addHours(),
            "on_block" => Carbon::now()->addHours(2),
            "route" => "KAMI1W KAMIT Y722 OLMEN OLME2T"
        ];
    }

}

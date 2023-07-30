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
        $sob = Carbon::now()->addMinutes(random_int(60, 300));

        return [
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
            "user_id" => User::factory(),
            "aircraft" => "A320",
            "flight_number" => "NA" . random_int(1, 999),
            "sched_off_block" => $sob,
            "sched_on_block" => $sob->copy()->addMinutes(random_int(40, 80)),
            "origin" => "RKPC",
            "destination" => "RKSS",
            "altitude" => "27000",
            "route" => "KAMI1W KAMIT Y722 OLMEN OLME2T",
            "remarks" => "Callsign NARU",
        ];
    }

}

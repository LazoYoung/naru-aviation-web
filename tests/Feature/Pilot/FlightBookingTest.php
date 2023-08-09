<?php

namespace Pilot;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlightBookingTest extends TestCase {
    use RefreshDatabase;

    public function test_correct_form_is_accepted(): void {
        $user = User::factory()->create();
        $offBlock = Carbon::parse("2023-07-30T04:50Z");
        $data = [
            "callsign" => "NAR310",
            "aircraft" => "A320",
            "origin" => "RKSS",
            "alternate" => null,
            "destination" => "RKPC",
            "off_block" => $offBlock,
            "flight_time" => "1:05",
            "route" => "KAMI1W KAMIT Y722 OLMEN OLME2T",
            "remarks" => null,
        ];

        $response = $this->actingAs($user)->post(route("pilot.dispatch.submit"), $data);
        $response->assertOk();
        $this->assertDatabaseCount(Booking::class, 1);
    }

    public function test_invalid_form_is_rejected(): void {
        $user = User::factory()->create();
        $data = [
            "callsign" => null,
            "aircraft" => null,
            "origin" => null,
            "alternate" => null,
            "destination" => null,
            "off_block" => null,
            "flight_time" => null,
            "route" => null,
            "remarks" => null,
        ];

        $response = $this->actingAs($user)->post(route("pilot.dispatch.submit"), $data);
        $response->assertStatus(302);
        $this->assertDatabaseEmpty(Booking::class);
    }

}

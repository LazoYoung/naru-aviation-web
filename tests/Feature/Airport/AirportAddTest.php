<?php

namespace Airport;

use App\Models\Airport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AirportAddTest extends TestCase {
    use RefreshDatabase;

    public function test_valid_airport_is_added(): void {
        $user = User::factory()->create();
        $user->is_admin = true;
        $airport = Airport::factory()->create();
        $response = $this->actingAs($user)->post(route('airport.new'), [
            'name' => $airport->name,
            'icao' => $airport->icao,
            'latitude' => $airport->latitude,
            'longitude' => $airport->longitude,
        ]);

        $response->assertOk();
    }

    public function test_invalid_airport_is_rejected(): void {
        $user = User::factory()->create();
        $user->is_admin = true;
        $response = $this->actingAs($user)->post(route('airport.new'), [
            'name' => null,
            'icao' => null,
            'latitude' => null,
            'longitude' => null,
        ]);

        $response->assertBadRequest();
    }

    public function test_unauthorized_user_is_rejected(): void {
        $user = User::factory()->create();
        $user->is_admin = false;
        $airport = Airport::factory()->create();
        $response = $this->actingAs($user)->post(route('airport.new'), [
            'name' => $airport->name,
            'icao' => $airport->icao,
            'latitude' => $airport->latitude,
            'longitude' => $airport->longitude,
        ]);

        $response->assertUnauthorized();
    }
}

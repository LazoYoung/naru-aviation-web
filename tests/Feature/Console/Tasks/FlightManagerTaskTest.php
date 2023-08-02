<?php

namespace Console\Tasks;

use App\Console\Tasks\FlightManagerTask;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Flightplan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SplObjectStorage;
use Tests\TestCase;

class FlightManagerTaskTest extends TestCase {
    use RefreshDatabase;

    public function test_booked_flights_are_inserted(): void {
        $task = new FlightManagerTask();
        $overdue = new SplObjectStorage();
        Booking::factory(3)->create();
        $b1 = Booking::factory()->overdue(1)->create();
        $b2 = Booking::factory()->overdue(20)->create();
        $b3 = Booking::factory()->overdue(60)->create();

        Booking::all()->each(function (Booking $booking) {
            $flightplan = Flightplan::factory()->create();
            $flightplan->booking()->associate($booking);
            $flightplan->saveOrFail();
        });

        $overdue->attach($b1);
        $overdue->attach($b2);
        $overdue->attach($b3);

        $this->assertDatabaseEmpty(Flight::class);
        $task->insertFlights();
        $this->assertDatabaseCount(Flight::class, $overdue->count());
    }

    public function test_offline_flights_are_deleted(): void {
        $task = new FlightManagerTask();
        $online = Flight::factory(3)->create();
        $offline = new SplObjectStorage();

        $f1 = Flight::factory()->offline()->complete()->create();
        $f2 = Flight::factory()->offline(30)->create();
        $offline->attach($f1);
        $offline->attach($f2);

        Flight::all()->each(function (Flight $flight) {
            $flightplan = Flightplan::factory()->create();
            $flightplan->flight()->associate($flight);
            $flightplan->saveOrFail();
        });

        $this->assertDatabaseCount(Flight::class, $online->count() + $offline->count());
        $task->deleteFlights();
        $this->assertDatabaseCount(Flight::class, $online->count());
    }
}

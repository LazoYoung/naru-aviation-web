<?php

namespace Console\Tasks;

use App\Console\Tasks\FlightManagerTask;
use App\Models\Booking;
use App\Services\Flight\Flight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SplObjectStorage;
use Tests\TestCase;

class FlightManagerTaskTest extends TestCase {
    use RefreshDatabase;

    public function test_booked_flights_are_inserted(): void {
        Flight::clearAll();
        $task = new FlightManagerTask();
        $overdue = new SplObjectStorage();
        Booking::factory(3)->create();
        $b1 = Booking::factory()->overdue(1)->create();
        $b2 = Booking::factory()->overdue(20)->create();
        $b3 = Booking::factory()->overdue(60)->create();

        $overdue->attach($b1);
        $overdue->attach($b2);
        $overdue->attach($b3);

        $this->assertCount(0, Flight::getAllFlights());
        $task->insertFlights();
        $this->assertCount($overdue->count(), Flight::getAllFlights());
    }

    public function test_offline_flights_are_deleted(): void {
        Flight::clearAll();
        $task = new FlightManagerTask();
        $online = Flight::createMockings(2);
        $offline = Flight::createMockings(2, true, true);

        $this->assertCount(count($online) + count($offline), Flight::getAllFlights());
        $task->deleteFlights();
        $this->assertCount(count($online), Flight::getAllFlights());
    }
}

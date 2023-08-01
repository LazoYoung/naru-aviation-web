<?php

namespace App\Console\Tasks;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Logbook;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class FlightManagerTask {

    public function __invoke(): void {
        try {
            $this->insertFlights();
            $this->deleteFlights();
        } catch (Throwable $t) {
            Log::error("FlightManagerTask operation failed!");
            Log::error(print_r($t->getTraceAsString(), true));
        }
    }

    /**
     * @throws Throwable
     */
    protected function insertFlights(): void {
        $bookings = $this->getOverdueBookings();

        foreach ($bookings as $booking) {
            $flight = $this->createScheduledFlight($booking);
            $this->updateFlightplan($booking, $flight);
            $booking->deleteOrFail();
        }
    }

    /**
     * @throws Throwable
     */
    protected function deleteFlights(): void {
        $flights = $this->getOfflineFlights();

        foreach ($flights as $flight) {
            if ($flight->status == 4) { // Aircraft has landed
                $this->createLogbook($flight);
                $this->deleteFlight($flight);
            } else if ($this->getOfflineTime($flight)->minutes >= 30) {
                $this->deleteFlight($flight);
            }
        }
    }

    /**
     * @throws Throwable
     */
    private function createLogbook(Flight $flight): void {
        $plan = $flight->flightplan;
        $logbook = new Logbook([
            "origin" => $plan->origin,
            "destination" => $plan->destination,
            "date" => Carbon::parse($plan->off_block),
            "flight_time" => $this->getFlightTime($flight)->minutes
        ]);
        $logbook->user()->associate($flight->user);
        $logbook->saveOrFail();
    }

    private function getFlightTime(Flight $flight): CarbonInterval {
        $offBlock = Carbon::parse($flight->off_block);
        $onBlock = Carbon::parse($flight->on_block);
        return $onBlock->diffAsCarbonInterval($offBlock);
    }

    private function getOfflineTime(Flight $flight): CarbonInterval {
        return $flight->updated_at->diffAsCarbonInterval(Carbon::now());
    }

    /**
     * @throws Throwable
     */
    private function deleteFlight(Flight $flight): void {
        $flight->deleteOrFail();
    }

    /**
     * @throws Throwable
     */
    private function createScheduledFlight(Booking $booking): Flight {
        $user = $booking->user;
        $flight = new Flight();
        $flight->user()->associate($user);
        $flight->saveOrFail();
        return $flight;
    }

    /**
     * @throws Throwable
     */
    private function updateFlightplan(Booking $oldParent, Flight $newParent): void {
        $flightplan = $oldParent->flightplan;
        $flightplan->booking()->disassociate();
        $flightplan->flight()->associate($newParent);
        $flightplan->saveOrFail();
    }

    /**
     * @return Collection<Booking> collection of flights where preflight is overdue
     */
    private function getOverdueBookings(): Collection {
        return Booking::whereDate("preflight_at", ">=", Carbon::now())->get();
    }

    /**
     * @return Collection<Flight> collection of flights with ACARS offline
     */
    private function getOfflineFlights(): Collection {
        return Flight::where("offline", "=", true)->get();
    }

}

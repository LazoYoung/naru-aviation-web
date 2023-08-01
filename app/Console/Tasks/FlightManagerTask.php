<?php

namespace App\Console\Tasks;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Logbook;
use Carbon\Carbon;
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
    public function insertFlights(): void {
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
    public function deleteFlights(): void {
        $flights = $this->getOfflineFlights();

        foreach ($flights as $flight) {
            if ($flight->status == 4) { // Aircraft has landed

                $logbook = Logbook::create($flight);
                $logbook->saveOrFail();
                $flight->deleteOrFail();
            } else if ($flight->getOfflineTime()->minutes >= 30) {
                $flight->deleteOrFail();
            }
        }
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

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
     * @link https://github.com/LazoYoung/naru-aviation-web/wiki/Flight-service#service-flow
     * @throws Throwable
     */
    public function insertFlights(): void {
        $bookings = $this->getOverdueBookings();

        foreach ($bookings as $booking) {
            $flight = $this->createScheduledFlight($booking);
            $this->updateFlightplan($booking, $flight);
            $flight->saveOrFail();
            $booking->deleteOrFail();
        }
    }

    /**
     * @link https://github.com/LazoYoung/naru-aviation-web/wiki/Flight-service#service-flow
     * @throws Throwable
     */
    public function deleteFlights(): void {
        $flights = $this->getOfflineFlights();

        foreach ($flights as $flight) {
            if ($flight->isComplete()) {
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
        $flight = Flight::create();
        $flight->user()->associate($user);
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
        return Booking::where("preflight_at", ">=", Carbon::now())->get();
    }

    /**
     * @return Collection<Flight> collection of flights with ACARS offline
     */
    private function getOfflineFlights(): Collection {
        return Flight::where("offline", "=", true)->get();
    }

}

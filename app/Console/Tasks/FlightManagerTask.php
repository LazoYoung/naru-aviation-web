<?php

namespace App\Console\Tasks;

use App\Models\Booking;
use App\Models\Logbook;
use App\Services\Flight\Flight;
use App\Services\Flight\FlightPlan;
use App\Services\Flight\FlightStatus;
use Carbon\Carbon;
use DateTimeZone;
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
            Flight::createFromBooking($booking);
        }
    }

    /**
     * @link https://github.com/LazoYoung/naru-aviation-web/wiki/Flight-service#service-flow
     * @throws Throwable
     */
    public function deleteFlights(): void {
        $flights = Flight::getOfflineFlights();

        foreach ($flights as $flight) {
            if ($flight->getStatus() == FlightStatus::Arrived) {
                $logbook = Logbook::create($flight);
                $logbook->saveOrFail();
                $flight->delete();
            } else if ($flight->getBeacon()->getIdleTime()->i >= 30) {
                $flight->delete();
            }
        }
    }

    /**
     * @return Collection<Booking> collection of flights where preflight is overdue
     */
    private function getOverdueBookings(): Collection {
        return Booking::where("preflight_at", ">=", Carbon::now(DateTimeZone::UTC))->get();
    }

}

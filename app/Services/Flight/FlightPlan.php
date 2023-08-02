<?php

namespace App\Services\Flight;

use App\Models\Booking;

class FlightPlan {
    private string $callsign;
    private string $aircraft;
    private string $origin;
    private ?string $alternate;
    private string $destination;
    private int $altitude;
    private int $off_block;
    private int $on_block;
    private string $route;
    private string $remarks;

    private function __construct(
        string  $callsign,
        string  $aircraft,
        string  $origin,
        ?string $alternate,
        string  $destination,
        int     $altitude,
        int     $off_block,
        int     $on_block,
        string  $route,
        string  $remarks
    ) {
        $this->callsign = $callsign;
        $this->aircraft = $aircraft;
        $this->origin = $origin;
        $this->alternate = $alternate;
        $this->destination = $destination;
        $this->altitude = $altitude;
        $this->off_block = $off_block;
        $this->on_block = $on_block;
        $this->route = $route;
        $this->remarks = $remarks;
    }

    public static function create(
        string  $callsign,
        string  $aircraft,
        string  $origin,
        ?string $alternate,
        string  $destination,
        int     $altitude,
        int     $off_block,
        int     $on_block,
        string  $route,
        string  $remarks
    ): FlightPlan {
        return new FlightPlan(
            $callsign, $aircraft, $origin,
            $alternate, $destination, $altitude,
            $off_block, $on_block, $route, $remarks
        );
    }

    public static function createFromBooking(Booking $booking): FlightPlan {
        return new FlightPlan(
            $booking->callsign,
            $booking->aircraft,
            $booking->origin,
            $booking->alternate,
            $booking->destination,
            $booking->altitude,
            $booking->off_block,
            $booking->on_block,
            $booking->route,
            $booking->remarks
        );
    }

    /**
     * @return string
     */
    public function getCallsign(): string {
        return $this->callsign;
    }

    /**
     * @return string
     */
    public function getAircraft(): string {
        return $this->aircraft;
    }

    /**
     * @return string
     */
    public function getOrigin(): string {
        return $this->origin;
    }

    /**
     * @return string|null
     */
    public function getAlternate(): ?string {
        return $this->alternate;
    }

    /**
     * @return string
     */
    public function getDestination(): string {
        return $this->destination;
    }

    /**
     * @return int
     */
    public function getAltitude(): int {
        return $this->altitude;
    }

    /**
     * @return int
     */
    public function getOffBlock(): int {
        return $this->off_block;
    }

    /**
     * @return int
     */
    public function getOnBlock(): int {
        return $this->on_block;
    }

    /**
     * @return string
     */
    public function getRoute(): string {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getRemarks(): string {
        return $this->remarks;
    }
}

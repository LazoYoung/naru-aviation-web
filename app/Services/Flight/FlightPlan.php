<?php

namespace App\Services\Flight;

use App\Models\Booking;
use Carbon\Carbon;
use Exception;

class FlightPlan {
    private string $callsign;
    private string $aircraft;
    private string $origin;
    private ?string $alternate;
    private string $destination;
    private int $altitude;
    private int $off_block;
    private int $on_block;
    private ?string $route;
    private ?string $remarks;

    private function __construct(
        string  $callsign,
        string  $aircraft,
        string  $origin,
        ?string $alternate,
        string  $destination,
        int     $altitude,
        int     $off_block,
        int     $on_block,
        ?string  $route,
        ?string  $remarks
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
        ?string  $route,
        ?string  $remarks
    ): FlightPlan {
        return new FlightPlan(
            $callsign, $aircraft, $origin,
            $alternate, $destination, $altitude,
            $off_block, $on_block,
            $route, $remarks
        );
    }

    public static function createFromArray(array $arr, ?FlightPlan $base = null): FlightPlan {
        $off_block = $arr["off_block"] ? Carbon::parse($arr["off_block"])->timestamp : $base?->off_block;
        $on_block = $arr["on_block"] ? Carbon::parse($arr["on_block"])->timestamp : $base?->on_block;

        return self::create(
            $arr["callsign"] ?: $base?->callsign,
            $arr["aircraft"] ?: $base?->aircraft,
            $arr["origin"] ?: $base?->origin,
            $arr["alternate"] ?: $base?->alternate,
            $arr["destination"] ?: $base?->destination,
            $arr["altitude"] ?: $base?->altitude,
            $off_block,
            $on_block,
            $arr["route"] ?: $base?->route,
            $arr["remarks"] ?: $base?->remarks
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
            intval($booking->off_block),
            intval($booking->on_block),
            $booking->route,
            $booking->remarks
        );
    }

    /**
     * @throws Exception
     */
    public static function createMockings(int $count): array {
        $arr = array();

        for ($i = 0; $i < $count; $i++) {
            $arr[] = self::createMocking();
        }
        return $arr;
    }

    /**
     * @throws Exception
     */
    public static function createMocking(): FlightPlan {
        return self::create(
            callsign: "NA" . random_int(1, 999),
            aircraft: "A320",
            origin: "RKSS",
            alternate: "RKPK",
            destination: "RKPC",
            altitude: "27000",
            off_block: Carbon::now()->timestamp,
            on_block: Carbon::now()->addHour()->timestamp,
            route: "KAMI1W KAMIT Y722 OLMEN OLME2T",
            remarks: null
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

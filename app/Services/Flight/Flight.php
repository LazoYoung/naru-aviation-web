<?php

namespace App\Services\Flight;

use DateInterval;
use DateTime;

class Flight {

    /**
     * @var array<Flight> flight storage
     */
    private static array $storage = [];

    private int $user_id;
    private FlightStatus $status;
    private FlightPlan $flightplan;
    private FlightBeacon $beacon;
    private ?int $off_block;
    private ?int $on_block;

    public static function get(int $user_id): ?Flight {
        return Flight::$storage[$user_id];
    }

    public static function create(int $user_id, FlightPlan $flightplan): Flight {
        $flight = new Flight($user_id, $flightplan);
        Flight::$storage[$user_id] = $flight;
        return $flight;
    }

    private function __construct(int $user_id, FlightPlan $flightplan) {
        $this->user_id = $user_id;
        $this->status = FlightStatus::Preflight;
        $this->flightplan = $flightplan;
        $this->beacon = new FlightBeacon();
        $this->off_block = null;
        $this->on_block = null;
    }

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->user_id;
    }

    /**
     * @return FlightStatus
     */
    public function getStatus(): FlightStatus {
        return $this->status;
    }

    /**
     * @return FlightPlan
     */
    public function getFlightplan(): FlightPlan {
        return $this->flightplan;
    }

    /**
     * @return FlightBeacon
     */
    public function getBeacon(): FlightBeacon {
        return $this->beacon;
    }

    /**
     * @return int|null
     */
    public function getOffBlock(): ?int {
        return $this->off_block;
    }

    /**
     * @return int|null
     */
    public function getOnBlock(): ?int {
        return $this->on_block;
    }

}

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
    // todo fill these later on
    private ?string $origin;
    private ?string $destination;
    private ?int $off_block;
    private ?int $on_block;

    /**
     * @param int $user_id id of user who operates the flight
     * @return Flight|null
     */
    public static function get(int $user_id): ?Flight {
        return self::$storage[$user_id];
    }

    /**
     * @return Flight[] indexed array of all flights
     */
    public static function getAllFlights(): array {
        return array_values(self::$storage);
    }

    /**
     * @return Flight[] indexed array of offline flights
     */
    public static function getOfflineFlights(): array {
        $arr = array_filter(self::$storage, fn($flight) => $flight->getBeacon()->isOffline());
        return array_values($arr);
    }

    public static function create(int $user_id, FlightPlan $flightPlan): Flight {
        $flight = new Flight($user_id, $flightPlan);
        self::$storage[$user_id] = $flight;
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
     * @return DateTime|null
     */
    public function getOffBlock(): ?DateTime {
        return new DateTime("@$this->off_block");
    }

    /**
     * @return DateTime|null
     */
    public function getOnBlock(): ?DateTime {
        return new DateTime("@$this->on_block");
    }

    /**
     * @return DateInterval duration of flight until now
     */
    public function getFlightTime(): DateInterval {
        if (!isset($this->off_block)) {
            return new DateInterval("PT0S");
        }
        if (!isset($this->on_block)) {
            return $this->getOffBlock()->diff(new DateTime(), true);
        }
        return $this->getOffBlock()->diff($this->getOnBlock(), true);
    }

    /**
     * @return string|null actual departure airport
     */
    public function getOrigin(): ?string {
        return $this->origin;
    }

    /**
     * @return string|null actual arrival airport
     */
    public function getDestination(): ?string {
        return $this->destination;
    }

    public function delete(): void {
        unset(self::$storage[$this->user_id]);
    }

}

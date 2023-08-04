<?php

namespace App\Services\Flight;

use App\Models\Booking;
use App\Models\User;
use DateInterval;
use DateTime;
use Exception;
use Throwable;

class Flight {

    /**
     * @var array<Flight> flight storage
     */
    private static array $storage = [];

    private int $user_id;
    private FlightStatus $status;
    private FlightPlan $flightPlan;
    private FlightBeacon $beacon;
    private string $origin;
    private string $destination;
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

    /**
     * Create a Flight out of $booking and delete the booking.
     * @throws Throwable thrown if unable to delete the booking
     */
    public static function createFromBooking(Booking $booking): Flight {
        $flight = self::create($booking->user_id, FlightPlan::createFromBooking($booking));
        $booking->deleteOrFail();
        return $flight;
    }

    public static function clearAll(): void {
        self::$storage = array();
    }

    /**
     * @throws Exception
     */
    public static function createMocking(bool $offline = false, bool $complete = false): Flight {
        $user_id = User::factory()->create()->id;
        $mock = self::create($user_id, FlightPlan::createMocking());

        if ($offline) {
            $mock->beacon->setOffline();
        }
        if ($complete) {
            $mock->setStatus(FlightStatus::Arrived);
            $mock->origin = $mock->flightPlan->getOrigin();
            $mock->destination = $mock->flightPlan->getDestination();
        }
        return $mock;
    }

    /**
     * @throws Exception
     */
    public static function createMockings(int $count, bool $offline = false, bool $complete = false): array {
        $arr = array();

        for ($i = 0; $i < $count; $i++) {
            $arr[] = self::createMocking($offline, $complete);
        }
        return $arr;
    }

    private function __construct(int $user_id, FlightPlan $flightplan) {
        $this->user_id = $user_id;
        $this->flightPlan = $flightplan;
        $this->off_block = null;
        $this->on_block = null;
        $this->beacon = new FlightBeacon();
        $this->status = FlightStatus::Preflight;
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
    public function getFlightPlan(): FlightPlan {
        return $this->flightPlan;
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
     * @return string actual departure airport
     */
    public function getOrigin(): string {
        return $this->origin;
    }

    /**
     * @return string actual arrival airport
     */
    public function getDestination(): string {
        return $this->destination;
    }

    /**
     * @param FlightStatus $status new status of this flight
     */
    public function setStatus(FlightStatus $status): void {
        $this->status = $status;

        switch ($status) {
            case FlightStatus::Departing:
                $this->off_block = time();
                break;
            case FlightStatus::Arrived:
                $this->on_block = time();
                break;
            default:
                // do nothing
        }
    }

    /**
     * @param FlightPlan $flightPlan new flightplan of this flight
     */
    public function setFlightPlan(FlightPlan $flightPlan): void {
        $this->flightPlan = $flightPlan;
    }

    /**
     * @param string $destination new destination
     */
    public function setDestination(string $destination): void {
        $this->destination = $destination;
    }

    /**
     * Delete this flight out of storage
     */
    public function delete(): void {
        unset(self::$storage[$this->user_id]);
    }

}

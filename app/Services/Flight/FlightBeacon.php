<?php

namespace App\Services\Flight;

use DateInterval;
use DateTime;

class FlightBeacon {
    private int $updated_at;
    private bool $offline = true;
    private float $latitude = 0;
    private float $longitude = 0;
    private int $altitude = 0;
    private int $airspeed = 0;
    private int $heading = 0;

    public function __construct() {
        $this->updated_at = time();
    }

    public function getLatitude(): float {
        return $this->latitude;
    }

    public function getLongitude(): float {
        return $this->longitude;
    }

    public function getAltitude(): int {
        return $this->altitude;
    }

    public function getAirspeed(): int {
        return $this->airspeed;
    }

    public function getHeading(): int {
        return $this->heading;
    }

    public function getUpdatedAt(): DateTime {
        return new DateTime("@$this->updated_at");
    }

    public function getIdleTime(): DateInterval {
        return $this->getUpdatedAt()->diff(new DateTime(), true);
    }

    public function isOffline(): bool {
        return $this->offline || $this->getIdleTime()->s > 60;
    }

    public function setOffline(): void {
        $this->offline = true;
    }

    public function update(float $lat, float $lon, int $alt, int $ias, int $hdg): void {
        $this->latitude = $lat;
        $this->longitude = $lon;
        $this->altitude = $alt;
        $this->airspeed = $ias;
        $this->heading = $hdg;
        $this->updated_at = time();
        $this->offline = false;
    }
}

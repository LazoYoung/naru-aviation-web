<?php

namespace App\Services\Flight;

enum FlightStatus: int {
    case Preflight = 0;
    case Boarding = 1;
    case Departing = 2;
    case Cruising = 3;
    case Landed = 4;
    case Arrived = 5;
}

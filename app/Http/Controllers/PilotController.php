<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flightplan;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\Exceptions\ParseErrorException;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use InvalidArgumentException;
use Throwable;

class PilotController extends Controller {

    public function getDispatchView(): \Inertia\Response {
        return Inertia::render("Pilot/Dispatch");
    }

    public function dispatchFlight(Request $request): Response {
        $val = $request->validate([
            "callsign" => ["required", "string"],
            "aircraft" => ["required", "string", "size:4"],
            "origin" => ["required", "string", "size:4"],
            "alternate" => ["required", "string", "size:4"],
            "destination" => ["required", "string", "size:4"],
            "altitude" => ["required", "numeric"],
            "off_block" => ["required", "date"],
            "flight_time" => ["required", "string"],
            "route" => ["required", "string"],
            "remarks" => ["string"]
        ]);

        try {
            $offBlock = Carbon::parse($val["off_block"], DateTimeZone::UTC);
            $onBlock = $this->getOnBlockTime($offBlock, $val["flight_time"]);
            $flightplan = new Flightplan([
                "callsign" => $val["callsign"],
                "aircraft" => $val["aircraft"],
                "origin" => $val["origin"],
                "alternate" => $val["alternate"],
                "destination" => $val["destination"],
                "altitude" => $val["altitude"],
                "off_block" => $offBlock,
                "on_block" => $onBlock,
                "route" => $val["route"],
                "remarks" => $val["remarks"]
            ]);
            $booking = new Booking([
                "preflight_at" => $offBlock->subMinutes(30)
            ]);
            $flightplan->booking()->associate($booking);
            $booking->user()->associate($request->user());
            $flightplan->saveOrFail();
            $booking->saveOrFail();
            return response($booking->toJson(), 200);
        } catch (Throwable $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function cancelBooking(Request $request): Response {
        // todo method stub
        return response(null, 404);
    }

    /**
     * @throws InvalidArgumentException string format is neither HH:MM nor MM
     * @throws Exception failed to parse $flightTime to CarbonInterval
     */
    private function getOnBlockTime(Carbon $offBlock, string $flightTime): Carbon {
        $min = $this->parseMinutes($flightTime);
        return $offBlock->copy()->add($min);
    }

    /**
     * @throws InvalidArgumentException $str format is neither HH:MM nor MM
     * @throws Exception failed to parse $str to CarbonInterval
     */
    private function parseMinutes(string $str): CarbonInterval {
        try {
            return CarbonInterval::createFromFormat("H:i", $str);
        } catch (ParseErrorException) {
            if (!is_numeric($str)) {
                throw new InvalidArgumentException("Wrong format: $str");
            }
            return CarbonInterval::create(minutes: $str);
        }
    }

}

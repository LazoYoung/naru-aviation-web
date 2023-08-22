<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\Exceptions\ParseErrorException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use InvalidArgumentException;
use Throwable;

class PilotController extends Controller {

    public function getDispatchView(): \Inertia\Response {
        return Inertia::render("Pilot/Dispatch");
    }

    /**
     * @throws ValidationException
     */
    public function dispatchFlight(Request $request): RedirectResponse|Response {
        $val = $request->validate([
            "callsign" => ["required", "string"],
            "aircraft" => ["required", "string", "size:4"],
            "origin" => ["required", "string", "size:4"],
            "alternate" => ["nullable", "string", "size:4"],
            "destination" => ["required", "string", "size:4"],
            "off_block" => ["required", "date"],
            "flight_time" => ["required", "string"],
            "route" => ["required", "string"],
            "remarks" => ["nullable", "string"]
        ]);

        $offBlock = Carbon::parse($val["off_block"]);

        if ($offBlock->isBefore(Carbon::now()->addMinutes(30))) {
            throw ValidationException::withMessages([
                "off_block" => "Departure time is invalid.",
            ]);
        }

        try {
            $onBlock = $this->getOnBlockTime($offBlock, $val["flight_time"]);
            $booking = new Booking([
                "preflight_at" => $offBlock->subMinutes(30),
                "callsign" => $val["callsign"],
                "aircraft" => $val["aircraft"],
                "origin" => $val["origin"],
                "alternate" => $val["alternate"],
                "destination" => $val["destination"],
                "off_block" => $offBlock,
                "on_block" => $onBlock,
                "route" => $val["route"],
                "remarks" => $val["remarks"]
            ]);
            $booking->user()->associate($request->user());
            $booking->saveOrFail();
            return to_route('home');
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

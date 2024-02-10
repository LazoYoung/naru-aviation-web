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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use InvalidArgumentException;
use Throwable;

class PilotController extends Controller {

    public function getDashboardView(): \Inertia\Response {
        return Inertia::render("Pilot/Dashboard");
    }

    public function getDispatchView(): \Inertia\Response {
        return Inertia::render("Pilot/Dispatch");
    }

    /**
     * @throws ValidationException
     */
    public function dispatchFlight(Request $request): RedirectResponse|Response {
        $validator = Validator::make($request->all(), [
            "callsign" => ["required", "string"],
            "aircraft" => ["required", "string", "size:4"],
            "origin" => ["required", "string", "size:4"],
            "alternate" => ["nullable", "string", "size:4"],
            "destination" => ["required", "string", "size:4"],
            "off_block" => ["required", "date"],
            "block_time" => ["required", "string"],
            "route" => ["required", "string"],
            "remarks" => ["nullable", "string"]
        ]);
        $input = $validator->validated();
        $offBlock = $input["off_block"] ? Carbon::parse($input["off_block"]) : null;
        $onBlock = null;

        $validator->after(function (\Illuminate\Validation\Validator $v) use ($input, &$offBlock, &$onBlock) {
            if (preg_match("/^(NAR\d+)$/", $input["callsign"]) != 1) {
                $v->errors()->add("callsign", "The callsign must start with NAR followed by digits.");
            }

            if (!isset($offBlock) || $offBlock->isBefore(Carbon::now()->addMinutes(30))) {
                $v->errors()->add("off_block", "You cannot depart this early.");
            }

            try {
                $blockTime = $this->parseMinutes($input["block_time"]);
                $onBlock = $this->getOnBlockTime($offBlock, $blockTime);
            } catch (Throwable) {
                $v->errors()->add("block_time", "The format must be either HOUR:MIN or MIN");
            }
        });

        if ($validator->fails()) {
            return to_route('pilot.dispatch')
                ->withErrors($validator)
                ->withInput($request->all());
        }

        try {
            $booking = new Booking([
                "preflight_at" => $offBlock->subMinutes(30),
                "callsign" => $input["callsign"],
                "aircraft" => $input["aircraft"],
                "origin" => $input["origin"],
                "alternate" => $input["alternate"],
                "destination" => $input["destination"],
                "off_block" => $offBlock,
                "on_block" => $onBlock,
                "route" => $input["route"],
                "remarks" => $input["remarks"]
            ]);
            $booking->user()->associate($request->user());
            $booking->saveOrFail();
            return to_route('pilot.dashboard');
        } catch (Throwable $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function cancelBooking(Request $request): Response {
        // todo method stub
        return response(null, 404);
    }

    private function getOnBlockTime(Carbon $offBlock, CarbonInterval $blockTime): Carbon {
        return $offBlock->copy()->add($blockTime);
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

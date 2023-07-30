<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class PilotController extends Controller {

    public function getDispatchView(): \Inertia\Response {
        return Inertia::render("Pilot/Dispatch");
    }

    public function dispatchFlight(Request $request): \Illuminate\Http\Response {
        $validated = $request->validate([
            "aircraft" => ["required", "string", "size:4"],
            "flight_number" => ["required", "string"],
            "off_block" => ["required", "date"],
            "origin" => ["required", "string", "size:4"],
            "destination" => ["required", "string", "size:4"],
            "altitude" => ["required", "numeric"],
            "route" => ["required", "string"],
            "remarks" => ["string"]
        ]);

        try {
            $flight = new Flight($validated);
            $flight->saveOrFail();
            return response($flight->toJson(), 200);
        } catch (Throwable $e) {
            return response($e->getMessage(), 500);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Throwable;

class AirportController extends Controller {

    public function addAirport(Request $request) {
        try {
            $validated = $request->validate([
                'name' => 'string',
                'icao' => ['string', 'min:4', 'max:4'],
                'latitude' => 'decimal:0,6',
                'longitude' => 'decimal:0,6',
            ]);
            $airport = new Airport([
                'name' => $validated['name'],
                'icao' => $validated['icao'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            $airport->saveOrFail();
            return response(null, 200);
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    public function fetchAirports() {
        return response()->json(Airport::all()->toJson());
    }

}

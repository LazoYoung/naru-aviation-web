<?php

namespace App\Services\Datalink;

use App\Exceptions\MalformedBulkException;
use App\Exceptions\UnknownIntentException;
use App\Models\Booking;
use App\Services\Flight\Flight;
use App\Services\Flight\FlightPlan;
use App\Services\Flight\FlightStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use JsonException;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Throwable;

class DataLinkHandler {

    private DataLink $dataLink;

    public function __construct(DataLink $dataLink) {
        $this->dataLink = $dataLink;
    }

    /**
     * Compute against the inbound message to produce a response
     *
     * @param MessageInterface $msg
     * @return string|null JSON-serialized response
     *
     * @throws InvalidArgumentException thrown if the message is not coalesced.
     * @throws JsonException thrown if JSON decode fails.
     */
    public function computeIntent(MessageInterface $msg): string|null {
        if (!$msg->isCoalesced()) {
            throw new InvalidArgumentException("Message is not coalesced.");
        }

        $json = json_decode($msg->getPayload(), true, 512, JSON_THROW_ON_ERROR);
        $intent = $json["intent"];
        $ident = $json["ident"] ?? null;
        $bulk = $json["bulk"];

        try {
            return match ($intent) {
                "fetch" => $this->onFetch($ident, $bulk),
                "start" => $this->onStart($ident, $bulk),
                "cancel" => $this->onCancel($ident),
                "report" => $this->onReport($bulk),
                "event" => $this->onEvent($bulk),
                default => throw new UnknownIntentException(),
            };
        } catch (MalformedBulkException) {
            return JsonBuilder::response(400, $ident, "Request form is invalid.");
        } catch (UnknownIntentException) {
            return JsonBuilder::response(400, $ident, "Unknown intent: $intent");
        }
    }

    private function onFetch(string $ident, array $bulk): string {
        $type = $bulk["type"];

        if ($type === "booking") {
            return $this->onFetchBooking($ident);
        } else {
            throw new MalformedBulkException("Unknown type: $type");
        }
    }

    private function onStart(string $ident, array $bulk): string {
        $flightplan = $bulk["flightplan"] ?? null;

        if ($bulk["scheduled"]) {
            return $this->onFlightStart($ident, $flightplan);
        } else {
            return $this->onCharterFlightStart($ident, $flightplan);
        }
    }

    private function onCancel(string $ident): string {
        try {
            $user_id = $this->dataLink->getUser()->id;
            $flight = Flight::get($user_id);

            if (!isset($flight)) {
                return JsonBuilder::response(450, $ident, "There is no flight.");
            }

            $flight->delete();
            return JsonBuilder::response(200, $ident, "Flight cancelled.");
        } catch (Throwable $t) {
            Log::warning($t->getMessage());
            return JsonBuilder::response(500, $ident, "Server error.");
        }
    }

    private function onReport(array $bulk): null {
        $user_id = $this->dataLink->getUser()->id;
        $flight = Flight::get($user_id);

        if (isset($flight)) {
            $lat = $bulk["latitude"];
            $lon = $bulk["longitude"];
            $alt = $bulk["altitude"];
            $ias = $bulk["ias"];
            $hdg = $bulk["heading"];
            $flight->getBeacon()->update($lat, $lon, $alt, $ias, $hdg);
        }
        return null;
    }

    private function onEvent(array $bulk): null {
        $user_id = $this->dataLink->getUser()->id;
        $flight = Flight::get($user_id);

        if (isset($flight)) {
            switch ($bulk["event"]) {
                case "status":
                    $this->onStatusChangeEvent($flight, $bulk);
                    break;
                case "divert":
                    $this->onDivertEvent($flight, $bulk);
                    break;
            }
        }
        return null;
    }

    private function onFetchBooking(string $ident): string {
        $user = $this->dataLink->getUser();
        $booking = Booking::whereUserId($user->id)
            ->orderBy("preflight_at")
            ->first();

        if (!isset($booking)) {
            return JsonBuilder::response(404, $ident, "You haven't booked a flight.");
        }

        $offBlock = Carbon::parse($booking->off_block)->toAtomString();
        $onBlock = Carbon::parse($booking->on_block)->toAtomString();
        $response = [
            "flightplan" => [
                "callsign" => $booking->callsign,
                "aircraft" => $booking->aircraft,
                "origin" => $booking->origin,
                "alternate" => $booking->alternate,
                "destination" => $booking->destination,
                "off_block" => $offBlock,
                "on_block" => $onBlock,
                "route" => $booking->route,
                "remarks" => $booking->remarks
            ]
        ];

        return JsonBuilder::response(200, $ident, $response);
    }

    private function onCharterFlightStart(string $ident, array $flightplan): string {
        $user_id = $this->dataLink->getUser()->id;

        if (Flight::get($user_id) !== null) {
            return JsonBuilder::response(450, $ident, "The flight has already started.");
        }

        try {
            $fpn = FlightPlan::createFromArray($flightplan);
            $callsign = $fpn->getCallsign();

            foreach (Flight::getAllFlights() as $flight) {
                if (strcmp($callsign, $flight->getFlightPlan()->getCallsign()) == 0) {
                    return JsonBuilder::response(401, $ident, "Callsign in use.");
                }
            }

            if (Booking::whereCallsign($callsign)->exists()) {
                return JsonBuilder::response(401, $ident, "Callsign in use.");
            }

            Flight::create($user_id, $fpn);
            return JsonBuilder::response(200, $ident, "Flight is submitted.");
        } catch (Throwable) {
            return JsonBuilder::response(400, $ident, "Flightplan form is invalid.");
        }
    }


    private function onFlightStart(string $ident, array $flight_plan): string {
        $user_id = $this->dataLink->getUser()->id;
        $flight = Flight::get($user_id);

        if (isset($flight)) {
//            $basePlan = $flight->getFlightPlan();
//            $fpn = FlightPlan::createFromArray($flightplan);
//
//            if (strcmp($fpn->getCallsign(), $basePlan->getCallsign()) != 0) {
//                return JsonBuilder::response(400, $ident, "Request form is invalid.");
//            }
//
//            if ($flight->getBeacon()->isOffline()) {
//                $fpn = FlightPlan::createFromArray($flightplan, $basePlan);
//                $flight->setFlightPlan($fpn);
//                return JsonBuilder::response(200, $ident, "Flight is submitted with revised flightplan.");
//            }
            return JsonBuilder::response(450, $ident, "The flight has already started.");
        }

        $booking = Booking::whereUserId($user_id)
            ->orderBy("preflight_at")
            ->first();

        if (!isset($booking)) {
            return JsonBuilder::response(404, $ident, "You haven't booked a flight.");
        }

        if (Carbon::parse($booking->preflight_at)->isAfter(Carbon::now())) {
            return JsonBuilder::response(440, $ident, Carbon::parse($booking->preflight_at)->timestamp);
        }

        if ($flight_plan) {
            $fpn = FlightPlan::createFromArray($flight_plan);

            if (strcmp($fpn->getCallsign(), $booking->callsign) == 0) {
                $booking->aircraft = $fpn->getAircraft();
                $booking->route = $fpn->getRoute();
                $booking->origin = $fpn->getOrigin();
                $booking->destination = $fpn->getDestination();
                $booking->alternate = $fpn->getAlternate();
                $booking->off_block = $fpn->getOffBlock();
                $booking->on_block = $fpn->getOnBlock();
                $booking->remarks = $fpn->getRemarks();
            } else {
                return JsonBuilder::response(400, $ident, "Callsign does not match.");
            }
        }

        try {
            Flight::createFromBooking($booking);
            return JsonBuilder::response(200, $ident, "Flight started.");
        } catch (Throwable) {
            return JsonBuilder::response(500, $ident, "Failed to delete a booking record.");
        }
    }

    private function onStatusChangeEvent(Flight $flight, array $bulk): void {
        try {
            $id = $bulk["status"];
            $status = FlightStatus::from($id);
            $flight->setStatus($status);
        } catch (Throwable) {
            Log::warning("Invalid FlightStatus received from ACARS: $id");
        }
    }

    private function onDivertEvent(Flight $flight, array $bulk): void {
        $flight->setDestination($bulk["airport"]);
    }

}

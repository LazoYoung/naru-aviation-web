<?php

namespace App\Services\Datalink;

use App\Exceptions\MalformedBulkException;
use App\Exceptions\UnknownIntentException;
use App\Models\Booking;
use App\Services\Flight\Flight;
use App\Services\Flight\FlightPlan;
use Illuminate\Support\Carbon;
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
        $ident = $json["ident"];
        $bulk = $json["bulk"];

        try {
            return match ($intent) {
                "fetch" => $this->onFetch($ident, $bulk),
                "start" => $this->onStart($ident, $bulk),
                "report" => $this->onReport($bulk),
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

    private function onFetchBooking(string $ident): string {
        $user = $this->dataLink->getUser();
        $booking = Booking::whereUserId($user->id)
            ->orderBy("preflight_at")
            ->first();

        if (isset($booking)) {
            return JsonBuilder::response(200, $ident, $booking->toJson());
        } else {
            return JsonBuilder::response(404, $ident, null);
        }
    }

    private function onStart(string $ident, array $bulk): string {
        $scheduled = strcasecmp("true", $bulk["scheduled"]);
        $flightplan = $bulk["flightplan"];

        if ($scheduled) {
            return $this->onStartScheduled($ident, $flightplan);
        } else {
            return $this->onStartUnscheduled($ident, $flightplan);
        }
    }

    private function onStartUnscheduled(string $ident, array $flightplan): string {
        $user_id = $this->dataLink->getUser()->id;

        if (Flight::get($user_id) !== null) {
            return JsonBuilder::response(450, $ident, "The flight has already started.");
        }

        try {
            $fpn = FlightPlan::createFromArray($flightplan);
        } catch (Throwable) {
            return JsonBuilder::response(400, $ident, "Flightplan form is invalid.");
        }

        Flight::create($user_id, $fpn);
        return JsonBuilder::response(200, $ident, "Flight is submitted.");
    }


    private function onStartScheduled(string $ident, array $flightplan): string {
        $user_id = $this->dataLink->getUser()->id;
        $flight = Flight::get($user_id);

        if (isset($flight)) {
            if ($flight->getBeacon()->isOffline()) {
                $base = $flight->getFlightPlan();
                $fpn = FlightPlan::createFromArray($flightplan, $base);
                $flight->setFlightPlan($fpn);
                return JsonBuilder::response(200, $ident, "Flight is submitted with revised flightplan.");
            }

            return JsonBuilder::response(450, $ident, "The flight has already started.");
        }

        $booking = Booking::whereUserId($user_id)
            ->orderBy("preflight_at")
            ->first();

        if (!isset($booking)) {
            return JsonBuilder::response(404, $ident, "You haven't booked a flight.");
        }

        if (Carbon::parse($booking->preflight_at)->isAfter(Carbon::now())) {
            return JsonBuilder::response(440, $ident, time());
        }

        try {
            Flight::createFromBooking($booking);
        } catch (Throwable) {
            return JsonBuilder::response(500, $ident, "Failed to delete a booking record.");
        }
        return JsonBuilder::response(200, $ident, "Flight is submitted.");
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
            $flight->setStatus($bulk["status"]);
            $flight->getBeacon()->update($lat, $lon, $alt, $ias, $hdg);
        }
        return null;
    }

}

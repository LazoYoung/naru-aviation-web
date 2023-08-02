<?php

namespace App\Services\Datalink;

use App\Exceptions\MalformedBulkException;
use App\Exceptions\UnknownIntentException;
use App\Models\Booking;
use InvalidArgumentException;
use JsonException;
use Ratchet\RFC6455\Messaging\MessageInterface;

class DataLinkHandler {

    private DataLink $dataLink;

    public function __construct(DataLink $dataLink) {
        $this->dataLink = $dataLink;
    }

    /**
     * Compute against the inbound message to produce a response
     *
     * @param MessageInterface $msg
     * @return string JSON-serialized response
     *
     * @throws InvalidArgumentException thrown if the message is not coalesced.
     * @throws JsonException thrown if JSON decode fails.
     */
    public function computeIntent(MessageInterface $msg): string {
        if (!$msg->isCoalesced()) {
            throw new InvalidArgumentException("Message is not coalesced.");
        }

        $json = json_decode($msg->getPayload(), true, 512, JSON_THROW_ON_ERROR);
        $intent = $json["intent"];
        $bulk = $json["bulk"];

        try {
            return match ($intent) {
                "fetch" => $this->onFetch($bulk),
                "start" => $this->onStart($bulk),
                "report" => $this->onReport($bulk),
                "event" => $this->onEvent($bulk),
                default => throw new UnknownIntentException(),
            };
        } catch (MalformedBulkException) {
            return JsonBuilder::response(400, "Request form is invalid.");
        } catch (UnknownIntentException) {
            return JsonBuilder::response(400, "Unknown intent: $intent");
        }
    }

    private function onFetch(array $bulk): string {
        $type = $bulk["type"];

        if ($type === "booking") {
            return $this->onFetchBooking();
        } else {
            throw new MalformedBulkException("Unknown type: $type");
        }
    }

    private function onFetchBooking(): string {
        $user = $this->dataLink->getUser();
        $booking = Booking::whereUserId($user->id)
            ->orderBy("preflight_at")
            ->first();

        if (isset($booking)) {
            return JsonBuilder::response(response: $booking->toJson());
        } else {
            return JsonBuilder::response(404);
        }
    }

    private function onStart(array $bulk): string {
        $scheduled = strcasecmp("true", $bulk["scheduled"]);
        $flightplan = $bulk["flightplan"];

        if ($scheduled) {
            return $this->onStartScheduled($flightplan);
        } else {
            return $this->onStartUnscheduled($flightplan);
        }
    }

    private function onStartUnscheduled(array $flightplan): string {
        // todo method stub
        return JsonBuilder::response(500);
    }

    private function onStartScheduled(array $flightplan): string {
        // todo method stub
        return JsonBuilder::response(500);
    }

    private function onReport(array $bulk): string {
        $latitude = $bulk["latitude"];
        $longitude = $bulk["longitude"];
        $altitude = $bulk["altitude"];
        $ias = $bulk["ias"];
        $heading = $bulk["heading"];

        // todo method stub
        return JsonBuilder::response(500);
    }

    private function onEvent(array $bulk): string {
        $phase = $bulk["phase"];

        // todo method stub
        return JsonBuilder::response(500);
    }

}

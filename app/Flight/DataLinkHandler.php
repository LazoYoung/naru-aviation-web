<?php

namespace App\Flight;

use App\Exceptions\MalformedBulkException;
use App\Exceptions\UnknownIntentException;
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
            return $this->getFailResponse("Bulk format is incorrect.");
        } catch (UnknownIntentException) {
            return $this->getFailResponse("Unknown intent: $intent");
        }
    }

    /**
     * @throws JsonException
     */
    private function onFetch(array $bulk): string {
        $type = $bulk["type"];

        if ($type === "booking") {
            return $this->onFetchBooking($bulk);
        } else {
            throw new MalformedBulkException("Unknown type: $type");
        }
    }

    /**
     * @throws JsonException
     */
    private function onFetchBooking(array $bulk): string {
        // todo method stub

        $value = [
            "flightplan" => [
                "origin" => "RKPC",
                "destination" => "RKSS",
                "alternate" => "RKPC",
                "flightno" => "NA501",
                "cruise" => "27000",
                "route" => "DCT OLMEN OLME2T",
            ],
            "schedule" => [
                "departure" => "0450",
                "arrival" => "0600",
            ]
        ];
        return $this->getSuccessResponse($value);
    }

    /**
     * @throws JsonException
     */
    private function onStart(array $bulk): string {
        $scheduled = strcasecmp("true", $bulk["scheduled"]);
        $flightplan = $bulk["flightplan"];

        // todo method stub

        return $this->getSuccessResponse();
    }

    /**
     * @throws JsonException
     */
    private function onReport(array $bulk): string {
        $latitude = $bulk["latitude"];
        $longitude = $bulk["longitude"];
        $altitude = $bulk["altitude"];
        $ias = $bulk["ias"];
        $heading = $bulk["heading"];

        // todo method stub

        return $this->getSuccessResponse();
    }

    /**
     * @throws JsonException
     */
    private function onEvent(array $bulk): string {
        $phase = $bulk["phase"];

        // todo method stub

        return $this->getSuccessResponse();
    }

    /**
     * @throws JsonException
     */
    private function getSuccessResponse(mixed $response = ""): string {
        $value = [
            "status" => "success",
            "response" => $response
        ];
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    private function getFailResponse(mixed $response = ""): string {
        $value = [
            "status" => "fail",
            "response" => $response
        ];
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

}
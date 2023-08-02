<?php

namespace App\Services\Datalink;

use Illuminate\Support\Facades\Log;
use Throwable;

class JsonBuilder {

    private static array $response = [
        200 => "Success",
        400 => "Bad Request",
        404 => "Not Found",
        500 => "Server Error",
    ];

    public static function response(int $status = 200, mixed $response = ""): string {
        $value = [
            "intent" => "response",
            "status" => $status,
            "message" => JsonBuilder::$response[$status],
            "response" => $response
        ];
        try {
            return json_encode($value, JSON_THROW_ON_ERROR);
        } catch (Throwable $t) {
            Log::error("Failed to parse JSON to string.");
            Log::error(print_r($t->getTraceAsString(), true));
            return "Fatal server error!";
        }
    }

}

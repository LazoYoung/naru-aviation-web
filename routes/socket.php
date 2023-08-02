<?php

use App\Services\Datalink\WebSocketHandler;
use BeyondCode\LaravelWebSockets\Exceptions\InvalidWebSocketController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;

try {
    return WebSocketsRouter::getFacadeRoot()->webSocket('/socket', WebSocketHandler::class);
} catch (InvalidWebSocketController $e) {
    echo $e->getTraceAsString();
}

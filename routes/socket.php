<?php

use App\Flight\WebSocketHandler;
use BeyondCode\LaravelWebSockets\Exceptions\InvalidWebSocketController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use BeyondCode\LaravelWebSockets\Server\Router;

try {
    return WebSocketsRouter::getFacadeRoot()->webSocket('/socket', WebSocketHandler::class);
} catch (InvalidWebSocketController $e) {
    echo $e->getTraceAsString();
}

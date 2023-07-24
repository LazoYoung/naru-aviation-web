<?php

use App\WebSocketHandler;
use BeyondCode\LaravelWebSockets\Exceptions\InvalidWebSocketController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use BeyondCode\LaravelWebSockets\Server\Router;

try {
    getRouter()->webSocket('/socket', WebSocketHandler::class);
} catch (InvalidWebSocketController $e) {
    echo $e->getTraceAsString();
}

function getRouter(): Router {
    return WebSocketsRouter::getFacadeRoot();
}

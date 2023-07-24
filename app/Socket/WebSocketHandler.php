<?php

namespace App\Socket;

use stdClass;
use Exception;
use SplObjectStorage;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class WebSocketHandler implements MessageComponentInterface {

    private SplObjectStorage $clients;
    private int $nextId = 1;

    public function __construct() {
        $this->clients = new SplObjectStorage();
    }

    /** @noinspection PhpDynamicFieldDeclarationInspection */
    function onOpen(ConnectionInterface $conn): void {
        // Prevent Ratchet from raising exception: undefined property
        $conn->socketId = $this->nextId++;
        $conn->app = new stdClass();
        $conn->app->id = 'naru-socket';
        $this->clients->attach($conn);
    }

    function onClose(ConnectionInterface $conn): void {
        $this->clients->detach($conn);
    }

    function onError(ConnectionInterface $conn, Exception $e): void {
        $conn->close();
    }

    function onMessage(ConnectionInterface $conn, $msg): void {
        $conn->send('Message sent.');
    }
}

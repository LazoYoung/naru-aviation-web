<?php /** @noinspection PhpDynamicFieldDeclarationInspection */

namespace App\ACARS;

use App\Models\Key;
use Exception;
use Illuminate\Support\Facades\Log;
use JsonException;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use RuntimeException;
use stdClass;
use Throwable;

class WebSocketHandler implements MessageComponentInterface {

    // Indexing with key id
    private array $activeKeys;

    // Indexing with socket id
    private array $dataLinks;

    private int $nextId;

    public function __construct() {
        $this->activeKeys = array();
        $this->dataLinks = array();
        $this->nextId = 0;
    }

    public function onOpen(ConnectionInterface $conn): void {
        try {
            $id = $this->_onOpen($conn);
            Log::info("Socket connection $id opened.");
        } catch (Throwable $t) {
            Log::error("Exception while opening a socket connection.");
            Log::error(print_r($t->getTraceAsString(), true));
        }
    }

    public function onClose(ConnectionInterface $conn): void {
        try {
            $dataLink = $this->getDataLink($conn);
            $this->_onClose($dataLink);
        } catch (Throwable $t) {
            Log::error("Exception while closing a socket connection.");
            Log::error(print_r($t->getTraceAsString(), true));
        }
    }

    public function onError(ConnectionInterface $conn, Exception $e): void {
        $conn->close();
        Log::error($e->getMessage());
        Log::error(print_r($e->getTraceAsString(), true));
    }

    public function onMessage(ConnectionInterface $conn, MessageInterface $msg): void {
        try {
            $dataLink = $this->getDataLink($conn);
            $this->_onMessage($dataLink, $conn, $msg);
        } catch (Throwable $t) {
            Log::error("Exception while receiving a socket message.");
            Log::error(print_r($t->getTraceAsString(), true));
        }
    }

    private function _onOpen(ConnectionInterface $conn): int {
        // Prevent Ratchet from raising exception: undefined property
        $id = $this->nextId++;
        $conn->socketId = $id;
        $conn->app = new stdClass();
        $conn->app->id = 'naru-socket';
        $this->dataLinks[$id] = new DataLink($conn);
        return $id;
    }

    private function _onClose(DataLink $dataLink): void {
        $id = $dataLink->getSocketId();
        $key = $dataLink->getAPIKey();

        if ($key != null) {
            unset($this->activeKeys[$key->id]);
        }
        unset($this->dataLinks[$id]);
        Log::info("Socket connection $id closed.");
    }

    private function _onMessage(DataLink $dataLink, ConnectionInterface $conn, MessageInterface $msg): void {
        $id = $dataLink->getSocketId();
        $payload = $msg->getPayload();

        if (!$dataLink->isAuthorized()) {
            $key = Key::getDatalinkKey($payload);

            if ($key == null) {
                $conn->send("This key is invalid");
                $conn->close();
                Log::debug("Connection $id presented an invalid key: $payload");
                return;
            }

            if (isset($this->activeKeys[$key->id])) {
                $conn->send("This key is in use.");
                Log::debug("Connection $id presented a duplicate key: $payload");
                return;
            }

            $dataLink->authorize($key);
            $this->activeKeys[$key->id] = $key;
            $conn->send("Authentication success.");
            Log::info("Connection $id authorized with key: $payload");
            return;
        }

        if ($msg->isCoalesced()) {
            try {
                $json = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
                Log::info("Inbound payload from socket $id:", $json);
            } catch (JsonException $e) {
                Log::error("Failed to decode JSON payload.");
                Log::error(print_r($e->getTraceAsString(), true));
            }
        }
    }

    private function getDataLink(ConnectionInterface $conn): DataLink {
        if (!isset($conn->socketId)) {
            throw new RuntimeException("Connection does not have 'socketId' property.");
        }
        if (!isset($conn->app)) {
            throw new RuntimeException("Connection does not have 'app' property.");
        }
        return $this->dataLinks[$conn->socketId];
    }
}

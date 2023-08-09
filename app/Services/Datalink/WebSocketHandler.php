<?php /** @noinspection PhpDynamicFieldDeclarationInspection */

namespace App\Services\Datalink;

use App\Models\Key;
use Exception;
use Illuminate\Support\Facades\Log;
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

        if ($dataLink->isAuthorized()) {
            unset($this->activeKeys[$dataLink->getAPIKey()->id]);
        }
        unset($this->dataLinks[$id]);
        Log::info("Socket connection $id closed.");
    }

    private function _onMessage(DataLink $dataLink, ConnectionInterface $conn, MessageInterface $msg): void {
        if (!$dataLink->isAuthorized() && !$this->identify($dataLink, $conn, $msg)) {
            return;
        }

        try {
            $id = $dataLink->getSocketId();
            Log::info("Inbound payload from socket $id: {$msg->getPayload()}");

            $response = $dataLink->getHandler()->computeIntent($msg);

            if (isset($response)) {
                $conn->send($response);
                Log::debug("Response sent to socket $id: $response");
            }
        } catch (Throwable $t) {
            Log::error($t->getMessage());
            Log::error(print_r($t->getTraceAsString(), true));
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

    private function identify(DataLink $dataLink, ConnectionInterface $conn, MessageInterface $msg): bool {
        try {
            $id = $dataLink->getSocketId();
            $request = json_decode($msg->getPayload(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            $conn->send(JsonBuilder::response(400, null, "Request form is invalid."));
            return false;
        }

        $ident = $request["ident"];

        if (!strcmp($request["intent"], "auth")) {
            $conn->send(JsonBuilder::response(400, $ident, "You must be authorized before any request!"));
            return false;
        }

        try {
            $input = $request["bulk"]["key"];
        } catch (Throwable) {
            $conn->send(JsonBuilder::response(400, $ident, "Request form is invalid."));
            return false;
        }

        $key = Key::getDatalinkKey($input);

        if ($key === null) {
            $conn->send(JsonBuilder::response(404, $ident, "Key does not match."));
            $conn->close();
            Log::debug("Connection $id presented an invalid key: $input");
            return false;
        }

        if (isset($this->activeKeys[$key->id])) {
            $conn->send(JsonBuilder::response(403, $ident, "Key already in use."));
            Log::debug("Connection $id presented a duplicate key: $input");
            return false;
        }

        $dataLink->authorize($key);
        $this->activeKeys[$key->id] = $key;
        $conn->send(JsonBuilder::response(200, $ident, "You are logged in."));
        Log::info("Connection $id authorized with key: $input");
        return true;
    }
}

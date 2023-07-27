<?php

namespace App\ACARS;

use App\Models\Key;
use Ratchet\ConnectionInterface;

class DataLink {
    private int $socket_id;
    private ?Key $key;

    /** @noinspection PhpUndefinedFieldInspection */
    public function __construct(ConnectionInterface $conn) {
        $this->socket_id = $conn->socketId;
        $this->key = null;
    }

    /**
     * @return int socket id of the backing connection
     */
    public function getSocketId(): int {
        return $this->socket_id;
    }

    /**
     * @return bool true only if this connection has been authorized with valid API key
     */
    public function isAuthorized(): bool {
        return isset($this->key);
    }

    /**
     * @return Key|null the API key if this session is authorized, null otherwise
     */
    public function getAPIKey(): ?Key {
        return $this->key;
    }

    /**
     * Authorize this connection
     * @param Key $key The validated key
     */
    public function authorize(Key $key): void {
        $this->key = $key;
    }

}

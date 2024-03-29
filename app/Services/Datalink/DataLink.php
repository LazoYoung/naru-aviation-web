<?php

namespace App\Services\Datalink;

use App\Models\Key;
use App\Models\User;
use Ratchet\ConnectionInterface;
use RuntimeException;

class DataLink {

    private ConnectionInterface $connection;
    private DataLinkHandler $handler;
    private ?Key $key;
    private ?User $user;

    public function __construct(ConnectionInterface $conn) {
        $this->connection = $conn;
        $this->key = null;
        $this->handler = new DataLinkHandler($this);
    }

    /**
     * @noinspection PhpUndefinedFieldInspection
     * @return int socket id of the backing connection
     */
    public function getSocketId(): int {
        return $this->connection->socketId;
    }

    /**
     * @return ConnectionInterface the backing connection
     */
    public function getConnection(): ConnectionInterface {
        return $this->connection;
    }

    /**
     * @return bool true only if this connection has been authorized with valid API key
     */
    public function isAuthorized(): bool {
        return isset($this->key);
    }

    /**
     * @return Key the API key
     * @throws RuntimeException this datalink is unauthorized
     */
    public function getAPIKey(): Key {
        if (!$this->isAuthorized()) {
            throw new RuntimeException("This datalink is unauthorized.");
        }

        return $this->key;
    }

    /**
     * @return User the user
     * @throws RuntimeException this datalink is unauthorized
     */
    public function getUser(): User {
        if (!$this->isAuthorized()) {
            throw new RuntimeException("This datalink is unauthorized.");
        }

        return $this->user;
    }

    /**
     * Authorize this connection
     * @param Key $key The validated key
     */
    public function authorize(Key $key): void {
        $this->key = $key;
        $this->user = $key->user;
    }

    /**
     * @return DataLinkHandler the message handler for this instance
     */
    public function getHandler(): DataLinkHandler {
        return $this->handler;
    }

}

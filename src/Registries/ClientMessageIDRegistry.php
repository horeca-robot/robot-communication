<?php
namespace App\Registries;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ratchet\ConnectionInterface;

class ClientMessageIDRegistry
{
    private array $clients = [];
    private array $messageIds = [];
    private string $clientPrefix = 'client_';

    public function registerClient(ConnectionInterface $client, UuidInterface $uuid)
    {
        $uuidAsString = $uuid->toString();

        $this->clients[$uuidAsString] = $client;
        $this->messageIds[$this->clientPrefix.$client->resourceId] = $uuid;
    }

    public function unregisterClientByClient(ConnectionInterface $client) : void
    {
        if(isset($this->messageIds[$this->clientPrefix.$client->resourceId]))
        {
            $messageId = $this->messageIds[$this->clientPrefix.$client->resourceId];
            if($messageId instanceof UuidInterface && isset($this->clients[$messageId->toString()]))
            {
                unset($this->clients[$messageId->toString()]);
            }

            unset($this->messageIds[$this->clientPrefix.$client->resourceId]);
        }
    }

    public function unregisterClientByMessageID(string $messageId) : void
    {
        if(isset($this->clients[$messageId]))
        {
            $client = $this->clients[$messageId];
            if($client instanceof ConnectionInterface && $this->messageIds[$this->clientPrefix.$client->resourceId])
            {
                unset($this->messageIds[$this->clientPrefix.$client->resourceId]);
            }

            unset($this->clients[$messageId]);
        }
    }

    public function getClientByMessageID(string $messageId) : ?ConnectionInterface
    {
        if(Uuid::isValid($messageId))
        {
            if(isset($this->clients[$messageId]) && $this->clients[$messageId] instanceof ConnectionInterface)
            {
                return $this->clients[$messageId];
            }
        }

        return null;
    }

    public function getMessageIDByClientID(ConnectionInterface $client) : ?UuidInterface
    {
        if(isset($this->clients[$client->resourceId]) && $this->clients[$client->resourceId] instanceof UuidInterface)
        {
            return $this->clients[$client->resourceId];
        }

        return null;
    }
}

<?php
namespace App;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

use Exception;
use SplObjectStorage;

/**
 * This class is a simple websocket application that broadcasts every send message to all participants within the socket.
 */
class MassBroadcastSocket implements MessageComponentInterface
{
    protected SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf("Connection %d sending message \"%s\" to other connection %s\n", $from->resourceId, $msg, $numRecv, $numRecv === 1);

        foreach($this->clients as $client)
        {
            if($from !== $client)
                $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $conn->close();

        echo "An error has occurred: {$e->getMessage()}\n";
    }
}

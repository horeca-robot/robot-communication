<?php
namespace App;

use App\Registries\ClientMessageIDRegistry;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

use Exception;
use Ramsey\Uuid\Uuid;
use SplObjectStorage;

class ClientToClientSocket implements MessageComponentInterface
{
    private SplObjectStorage $clients;
    private ClientMessageIDRegistry $clientMessageIDRegistry;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
        $this->clientMessageIDRegistry = new ClientMessageIDRegistry;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) : void
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf("Connection %d sending message \"%s\" to other connection %s\n", $from->resourceId, $msg, $numRecv, $numRecv === 1);
        
        $decodedMessage = $this->decodeMessage($msg);
        if($decodedMessage == null)
        {
            $from->send(json_encode([
                'type' => "ws-error",
                'payload' => [
                    'message' => "Messages should be sent as JSON strings."
                ]
            ]));

            return;
        }

        if(!isset($decodedMessage['clientID']) || (isset($decodedMessage['clientID']) && !Uuid::isValid($decodedMessage['clientID'])))
        {
            $clientID = Uuid::uuid4();
            $this->clientMessageIDRegistry->registerClient($from, $clientID);
            
            $decodedMessage['clientID'] = $clientID->toString();

            $this->broadcastMessageToOtherClients($from, $decodedMessage);
            $this->sendMessageReceivedMessage($from);
            return;
        }

        if(isset($decodedMessage['clientID']) && Uuid::isValid($decodedMessage['clientID']))
        {
            if($this->broadcastMessageToSpecificClient($decodedMessage['clientID'], $decodedMessage))
            {
                $this->sendMessageReceivedMessage($from);
                $this->clientMessageIDRegistry->unregisterClientByMessageID($decodedMessage['clientID']);
                return;
            }

            $this->sendCouldNotSendMessageToTargetedClientMessage($from);
            return;
        }

        $this->sendClientNotFoundMessage($from);
    }

    public function onClose(ConnectionInterface $conn) : void
    {
        $this->clients->detach($conn);
        $this->clientMessageIDRegistry->unregisterClientByClient($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e) : void
    {
        $conn->close();
        $this->clientMessageIDRegistry->unregisterClientByClient($conn);

        echo "An error has occurred: {$e->getMessage()}\n";
    }

    private function decodeMessage(string $message) : ?array
    {
        $decoded = json_decode($message, true);

        if(is_bool($decoded) || $decoded == null)
            return null;

        return $decoded;
    }

    private function broadcastMessageToOtherClients(ConnectionInterface $from, array $message) : void
    {
        foreach($this->clients as $client)
        {
            if($from !== $client)
                $client->send(json_encode($message));
        }
    }

    private function broadcastMessageToSpecificClient(string $messageId, array $message) : bool
    {
        $client = $this->clientMessageIDRegistry->getClientByMessageID($messageId);

        if($client == null)
            return false;

        if($client instanceof ConnectionInterface)
        {
            $client->send(json_encode($message));
            return true;
        }

        return false;
    }

    private function sendMessageReceivedMessage(ConnectionInterface $from) : void
    {
        $from->send(json_encode([
            'type' => "ws-success",
            'payload' => [
                'message' => "Message has been received and is currently being broadcasted to the other client."
            ]
        ]));
    }

    private function sendClientNotFoundMessage(ConnectionInterface $from) : void
    {
        $from->send(json_encode([
            'type' => "ws-error",
            'payload' => [
                'message' => "Could not find any client to send the message to."
            ]
        ]));
    }

    private function sendCouldNotSendMessageToTargetedClientMessage(ConnectionInterface $from) : void
    {
        $from->send(json_encode([
            'type' => "ws-error",
            'payload' => [
                'message' => "Could not send the message to the targeted client."
            ]
        ]));
    }
}

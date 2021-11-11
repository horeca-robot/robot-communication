# robot-communication
Websocket server that enables communication between two clients (bartender app and the robot itself)

- [robot-communication](#robot-communication)
  - [Implementation explanation](#implementation-explanation)
  - [Communication between clients](#communication-between-clients)

## Implementation explanation
The main idea behind the communication is that the websocket acts as a vehicle that transfers information from one client to another client. The only thing that a client must do is connect to the correct endpoint and topic. When a client sends information, the other client must parse this information and return the correct response.

The socket itself uses communication over the STOMP protocol. This means that one simply can't directly connect with the socket endpoint. The socket endpoint acts as a gateway of some sorts. As long as the main application endpoint (for example: `https://example.com/websocket-endpoint`) is being requested via a GET method, the socket application will run. Once the socket application is running the client must connect to the right topic within the socket. A topic is nothing more than a channel. When the client is connected to a channel they may send and/or receive messages.

## Communication between clients
Both clients will need to connect to the actual web-socket endpoint before they can subscribe to any topics. The standard endpoint that is being used in this application is: `http://127.0.0.1:8080/robot-communication`. Once a client has connected to this endpoint they may start subscribing to topics.

There are two topics within the socket, `/topic/robot/request` and `/topic/robot/response`. `/topic/robot/request` is being used when a client wants to send a request to the other client. When a client subscribes to this topic they will receive **all** incomming requests. `/topic/robot/response` is being used when a client wants to respond to a request. When a client subscribes to this topic they will receive **all** incomming responses.

There are two STOMP endpoints that can be used: `/robot/{robotId}/request` and `/robot/{robotId}/response`. When a client wants to send a request to another client, they can use `/robot/{robotId}/request`. when a client wants to send a response to another client, they can use `/robot/{robotId}/response`. The following format must be used when giving a payload:
```
{
    type: 'YOUR_COMMAND_TYPE_HERE',
    payload: {}
}
```
When a request has been issued, it will be formatted to the following string: `{:ROBOT_ID} {:PAYLOAD_AS_JSON_STRING}`. It is up to the authors of the clients to check which request is directed at them, same goes for the responses.
<br/>
<br/>
*Example project(s) can be found in the `examples` folder.*

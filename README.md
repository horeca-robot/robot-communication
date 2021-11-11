# robot-communication
Websocket server that enables communication between two clients (bartender app and the robot itself)

## Implementation explanation
The main idea behind the communication is that the websocket acts as a vehicle that transfers information from one client to another client. The only thing that a client must do is connect to the correct endpoint and topic. When a client sends information, the other client must parse this information and return the correct response.

The socket itself uses communication over the STOMP protocol. This means that one simply can't directly connect with the socket endpoint. The socket endpoint acts as a gateway of some sorts. As long as the main application endpoint (for example: `https://example.com/websocket-endpoint`) is being requested via a GET method, the socket application will run. Once the socket application is running the client must connect to the right topic within the socket. A topic is nothing more than a channel. When the client is connected to a channel they may send and/or receive messages.

## Communication standards
Communication through the socket is pretty simple. The first thing that a client has to do is connect with the correct endpoint. The endpoint for this specific application is: `http://127.0.0.1:8080/robot-communication`. Once the client has connected to this endpoint it must subscribe to the correct topic. The following topics are available:
- /robot/{:robotId}

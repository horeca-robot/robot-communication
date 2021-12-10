# Robot Communication
Websocket server that enables communication between two clients (app -> robot or app -> app)

## Implementation explanation
The main idea behind the communication is that the websocket acts as a vehicle that transfers information from one client to another client. The only thing that a client must do is connect to the correct endpoint. When a client sends information, the other client must parse this information and, optionally, return the correct response.

## Endpoints
| Endpoint | Client       |
|----------|--------------|
| /robot   | app -> robot |
| /app     | app -> app   |

## Sending data
The websocket itself has absolutely 0 code to enforce any message structure. You can just send strings of text between the clients.

class Socket
{
    _socket = null;
    _stompClient = null;
    _connected = false;

    constructor(url) {
        this._socket = new SockJS(url);
        this._stompClient = new Stomp.over(this._socket);
    }

    connect(options = {}) {
        return new Promise((resolve, reject) => {
            try {
                this._stompClient.connect(options, (frame) => {
                    this._connected = true;
                    resolve(frame);
                });
            } catch(e) {
                this._connected = false;
                reject(e);
            }
        });
    }

    getIncommingRobotRequests(callback) {
        if(!(callback instanceof Function))
            throw new Error("Function expects 'callback' to be a function.");

        if(!this._connected)
            throw new Error("No active socket connection found.");

        this._stompClient.subscribe('/topic/robot/request', (response) => callback(response));
    }

    getIncommingRobotResponses(callback) {
        if(!(callback instanceof Function))
            throw new Error("Function expects 'callback' to be a function.");

        if(!this._connected)
            throw new Error("No active socket connection found.");
        
        this._stompClient.subscribe('/topic/robot/response', (response) => callback(response));
    }

    sendData(endpoint, payload, options = {}) {
        if(!this._connected)
            throw new Error("No active socket connection found.");

        if(payload instanceof Object)
            payload = JSON.stringify(payload);

        this._stompClient.send(endpoint, options, payload);
    }

    disconnect() {
        this._stompClient.disconnect();
        this._connected = false;
    }
}

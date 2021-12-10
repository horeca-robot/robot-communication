export default class Socket
{
    _url = null;
    _socket = null;
    _callbacks = {
        onOpen: [],
        onClose: [],
        onMessage: [],
        onError: [],
    };

    constructor(url)
    {
        this._url = url;
    }

    connect()
    {
        return new Promise((resolve, reject) =>
        {
            this._socket = new WebSocket(this._url);
    
            try
            {
                if(!(this._socket instanceof WebSocket))
                    throw new Error("Socket could not be instantiated.");
            }
            catch(e)
            {
                reject(e);
            }

            const fireCallbacksOfArray = (array, event) =>
            {
                for(const callback of array)
                {
                    callback(event);
                }
            }
    
            this._socket.addEventListener('open', (event) => fireCallbacksOfArray(this._callbacks.onOpen, event));
            this._socket.addEventListener('close', (event) => fireCallbacksOfArray(this._callbacks.onClose, event));
            this._socket.addEventListener('message', (event) => fireCallbacksOfArray(this._callbacks.onMessage, event));
            this._socket.addEventListener('error', (event) => fireCallbacksOfArray(this._callbacks.onError, event));

            const socket = this._socket;
            let intervalWaitUntilWebSocketHasConnected = null;

            intervalWaitUntilWebSocketHasConnected = setInterval(() => {
                if(socket.readyState === 1)
                {
                    clearInterval(intervalWaitUntilWebSocketHasConnected);
                    resolve(true);
                }
                else if (socket.readyState !== 0)
                {
                    clearInterval(intervalWaitUntilWebSocketHasConnected);
                    
                    try
                    {
                        throw new Error("Socket could not be instantiated.");
                    }
                    catch(e)
                    {
                        reject(e);
                    }
                }
            }, 5);
        });
    }

    send(data)
    {
        if(!(this._socket instanceof WebSocket))
            throw new Error("You are not connected to any socket.");

        this._socket.send(data);
    }

    registerOnOpenCallback(callback)
    {
        this._registerCallback(this._callbacks.onOpen, callback);
    }

    registerOnCloseCallback(callback)
    {
        this._registerCallback(this._callbacks.onClose, callback);
    }

    registerOnMessageCallback(callback)
    {
        this._registerCallback(this._callbacks.onMessage, callback);
    }

    registerOnErrorCallback(callback)
    {
        this._registerCallback(this._callbacks.onError, callback);
    }

    disconnect()
    {
        if(!(this._socket instanceof WebSocket))
            throw new Error("You are not connected to any socket.");

        this._socket.close();
    }

    _registerCallback(property, callback)
    {        
        if(!(callback instanceof Function))
            throw new Error("Invalid callback given, it must be a valid function.");

        if(!Array.isArray(property))
            throw new Error("Callback can't be assigned to non-existent event");

        property.push(callback);
    }
}

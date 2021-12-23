import Socket from "./Socket.js";

document.addEventListener('DOMContentLoaded', async () =>
{
    const socket = new Socket(`ws://localhost:8082/customer`);

    socket.registerOnOpenCallback((event) => console.log(event));
    socket.registerOnCloseCallback((event) => console.log(event));
    socket.registerOnMessageCallback((event) => console.log(event.data));
    socket.registerOnErrorCallback((event) => console.log(event));

    if(await socket.connect())
    {
        socket.send('banaan :D')
    }
    
});

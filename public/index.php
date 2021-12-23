<?php

use App\ClientToClientSocket;
use Ratchet\App;

use App\MassBroadcastSocket;

require_once __DIR__.'/../vendor/autoload.php';

$app = new App('localhost', 8082, '127.0.0.1');

$app->route('/robot', new MassBroadcastSocket, ['*']);
$app->route('/customer', new ClientToClientSocket, ['*']);

$app->run();

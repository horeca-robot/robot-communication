<?php

use Ratchet\App;

use App\MassBroadcastSocket;

require_once __DIR__.'/../vendor/autoload.php';

$app = new App('localhost', 8080, '127.0.0.1');

$app->route('/robot', new MassBroadcastSocket, ['*']);
$app->route('/app', new MassBroadcastSocket, ['*']);

$app->run();

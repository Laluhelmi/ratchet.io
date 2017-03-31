<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;
     require __DIR__ . '/vendor/autoload.php';
     require './Chat.php';

   $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        8081,'172.10.69.150'
    );

    $server->run();


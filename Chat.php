<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/vendor/autoload.php';

class Chat implements MessageComponentInterface {

    protected $clients;
    private $user = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        if ($this->isJson($msg)) {
            $datajson = json_decode($msg);
            $this->user[$from->resourceId] = $datajson->user;
            $dataUserOnline = array();
            foreach ($this->user as $key => $value) {
                $dataUserOnline[] = array($key , $value);
            }
            $data['user'] = $datajson->user;
            $data['pesan'] = $datajson->pesan;
            $data['type'] = 'new';
            $data['useronline'] = $dataUserOnline;
            $d = json_encode($data);
            $this->sendMessageToNew($d, $from);
        } else {
            $data['user'] = $this->user[$from->resourceId];
            $data['type'] = 'chat';
            $data['pesan'] = $msg;
            $this->sendMessage(json_encode($data), $from);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $userremove = $this->user[$conn->resourceId];
        unset($this->user[$conn->resourceId]);
        $this->tell($userremove);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function tell($param) {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $dataUserOnline = array();
                foreach ($this->user as $key => $value) {
                    $dataUserOnline[] = array($key ,$value);
                }
                $pesan = json_encode(array('user' => $param,
                    'pesan' => 'telahh meninggalkan chat',
                    'type'=>'new','useronline'=>$dataUserOnline));
                $client->send($pesan);
            }
        }
    }

    public function sendMessage($pesan, $from) {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($pesan);
            }
        }
    }
    public function sendMessageToNew($pesan){
         foreach ($this->clients as $client) {
                $client->send($pesan);
        }
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}

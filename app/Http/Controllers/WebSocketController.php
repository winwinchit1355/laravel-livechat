<?php

namespace App\Http\Controllers;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
/**
 * @author Mihawk | codedoct.com
 */
class WebSocketController implements MessageComponentInterface
{
    protected $clients;
    private $users;
    private $userresources;
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
        $this->userresources = [];
    }
    /**
     * [onOpen description]
     * @method onOpen
     * @param  ConnectionInterface $conn [description]
     * @return [JSON]                    [description]
     * @example connection               var conn = new WebSocket('ws://localhost:8090');
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
    }
    /**
     * [onMessage description]
     * @method onMessage
     * @param  ConnectionInterface $conn [description]
     * @param  [JSON.stringify]          $msg  [description]
     * @return [JSON]                    [description]
     * @example sendAll                  conn.send(JSON.stringify({command: "sendAll", data: {message:"halo global"}, from: "3"}));
     * @example sendConnection           conn.send(JSON.stringify({command: "sendConnection", data: "halo kampret - kampret", to: [1,2]}));
     * @example message                  conn.send(JSON.stringify({command: "message", to: "1", from: "9", message: "it needs xss protection"}));
     * @example register                 conn.send(JSON.stringify({command: "register", userId: 9}));
     */
    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $data = json_decode($msg);
        if (isset($data->command)) {
            switch ($data->command) {
                case "sendAll":
                    if ($data->from) {
                        foreach ($this->userresources as $key => $resources) {
                            if ($key != $data->from) {
                                foreach ($resources as $resourceId) { //setiap user ID bisa buka di banyak browser
                                    if (isset($this->users[$resourceId])) {
                                        $this->users[$resourceId]->send(json_encode($data->data));
                                    }
                                }
                            }
                        }
                    }
                break;
                case "sendConnection":
                    if (count($data->to)>0) {
                        foreach ($this->userresources as $key => $resources) {
                            if (in_array($key, $data->to)) {
                                foreach ($resources as $resourceId) { //setiap user ID bisa buka di banyak browser
                                    if (isset($this->users[$resourceId])) {
                                        $this->users[$resourceId]->send(json_encode($data->data));
                                    }
                                }
                            }
                        }
                    }
                break;
                case "message":
                    if ($data->from && $data->to) {
                        if ( isset($this->userresources[$data->to]) ) {
                            foreach ($this->userresources[$data->to] as $key => $resourceId) {
                                if ( isset($this->users[$resourceId]) ) {
                                    $this->users[$resourceId]->send(json_encode($data->data));
                                }
                            }
                        }
                        if (isset($this->userresources[$data->from])) {
                            foreach ($this->userresources[$data->from] as $key => $resourceId) {
                                if ( isset($this->users[$resourceId])  && $conn->resourceId != $resourceId ) { //jika buka di browser yg berbeda
                                    $this->users[$resourceId]->send(json_encode($data->data));
                                }
                            }
                        }
                    }
                break;
                case "register":
                    if (isset($data->userId)) {
                        if (isset($this->userresources[$data->userId])) {
                            if (!in_array($conn->resourceId, $this->userresources[$data->userId]))
                            {
                                $this->userresources[$data->userId][] = $conn->resourceId;
                            }
                        }else{
                            $this->userresources[$data->userId] = [];
                            $this->userresources[$data->userId][] = $conn->resourceId;
                        }
                    }
                    // $conn->send(json_encode($this->users));
                    $conn->send(json_encode($this->userresources));
                break;
                default:
                    $example = array(
                        'methods' => [
                                    "send to all" => '{command: "sendAll", data: {message:"halo global"}, from: "3"}',
                                    "send to group" => '{command: "sendConnection", data: "halo kampret - kampret", to: [1,2]}',
                                    "message" => '{command: "message", to: "1", from: "2", data: {message:"halo gan"}',
                                    "register" => '{command: "register", userId: 9}',
                                ],
                    );
                    $conn->send(json_encode($example));
                break;
            }
        }
    }
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        unset($this->users[$conn->resourceId]);
        foreach ($this->userresources as &$userId) {
            foreach ($userId as $key => $resourceId) {
                if ($resourceId==$conn->resourceId) {
                    unset( $userId[ $key ] );
                }
            }
        }
    }
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

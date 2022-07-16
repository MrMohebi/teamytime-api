<?php
require_once "../configs/index.php";

if (isset($client)) {
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');
    $users = $usersCollection->find()->toArray();

    for ($i = 0; $i < count($users); $i++){
        $c = json_decode(json_encode($users[$i]),true);
        $users[$i]->_id = $c["_id"]['$oid'];
    }

    echo json_encode($users);
}


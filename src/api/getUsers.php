<?php
require_once "../configs/index.php";

if (isset($client)) {
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');
    $users = $usersCollection->find();

    $result = [];
    foreach ($users as $eUser){
        $result[] = ["_id"=>(string)$eUser['_id'], "name"=>$eUser["name"]];
    }

    echo json_encode($result);
}


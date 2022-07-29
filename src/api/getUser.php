<?php
require_once "../configs/index.php";

if (isset($client) && isset($_GET["userID"])) {
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');
    $user = $usersCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($_GET["userID"])]);

    if(!$user){
        exit(404);
    }

    exit(json_encode($user));
}
exit(400);

<?php
require_once "../configs/index.php";


if (isset($client) && isset($_GET["adminID"]) ) {
    $adminsCollection = $client->selectCollection($_ENV['DB_NAME'], 'admins');

    $admin = $adminsCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($_GET["adminID"])]);

    if(!$admin){
        exit(404);
    }

    exit(json_encode($admin));
}

<?php
require_once "../configs/index.php";

$headers = apache_request_headers();
$TOKEN = $headers['Token'];

if (isset($client) && $_POST["name"] && $_POST["phone"] && $_POST["role"]) {
    $adminsCollection = $client->selectCollection($_ENV['DB_NAME'], 'admins');
    $userCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');

    if(!($admin = $adminsCollection->findOne(["token"=>$TOKEN]))){
        exit("401");
    }

    $companyID = $admin->companyID;

    $userCollection->insertOne([
        "name"=>$_POST["name"],
        "role"=>$_POST["role"],
        "phone"=>$_POST["phone"],
        "companyID"=>$companyID,
        "profile"=>"",
        "isActive"=>true,
        "systemRole"=>"user",
        "telegramId"=>"",
        "team"=>"",
    ]);

    exit("200");
}else{
    exit("400");
};
<?php
require_once "../configs/index.php";

header('Content-Type: application/json; charset=utf-8');

$headers = apache_request_headers();
$TOKEN = $headers['Token'];

if (isset($client)) {

    $adminsCollection = $client->selectCollection($_ENV['DB_NAME'], 'admins');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');

    if(!($admin = $adminsCollection->findOne(["token"=>$TOKEN]))){
        exit("401");
    }

    $company = $companiesCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($admin->companyID)]);
    $users = $usersCollection->find(["companyID"=>$company->_id->__toString()]);

    $BASE_URL = "https://" . $company->eName . ".unimun.me/user/";
    $result = [];
    foreach ($users as $user){
        $result[$user->name] = $BASE_URL . $user->_id->__toString();
    }

    echo json_encode($result);
    exit();
}
<?php
require_once "../configs/index.php";

if (isset($client) && isset($_GET['companyEName'])) {

    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');

    $company = $companiesCollection->findOne(["eName"=>new MongoDB\BSON\Regex( $_GET['companyEName'], 'i' ) ]);

    $users = $usersCollection->find(["companyID"=>$company->_id->__toString()])->toArray();

    for ($i = 0; $i < count($users); $i++){
        $c = json_decode(json_encode($users[$i]),true);
         unset($users[$i]->_id);
        $users[$i]->id = $c["_id"]['$oid'];
    }

    echo json_encode($users);
}else{
    echo 400;
}


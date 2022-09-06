<?php
require_once "../configs/index.php";

$headers = apache_request_headers();
$TOKEN = $headers['Token'];

if (isset($client)  && isset($_POST["userID"])) {
    $adminsCollection = $client->selectCollection($_ENV['DB_NAME'], 'admins');
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');


    if(!($admin = $adminsCollection->findOne(["token"=>$TOKEN]))){
        exit("401");
    }

    $company = $companiesCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($admin->companyID)]);


    $reportsCollection->updateOne(
        [
            "userID"=>$_POST["userID"],
            "companyID"=>$company->_id->__toString(),
            "jalaliDate"=>$_POST["jalaliDate"]
        ]
        ,
        [ '$set' =>
            [
                "adminReview"=>$_POST["adminReview"] ? json_decode($_POST["adminReview"]) : [],
            ]
        ]

    );
    exit('200');
}else{
    exit("400");
}

function isAdminAuth($token){
    return $token === "093845b5f724e4a047c9f2221cd903b4";
}
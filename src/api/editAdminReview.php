<?php
require_once "../configs/index.php";

$headers = apache_request_headers();

if (isset($client)  && isAdminAuth($headers['Token']) && isset($_POST["userID"]) && isset($_POST["companyID"]) && strlen($_POST["userID"]) == 24 && strlen($_POST["companyID"]) == 24) {
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $reportsCollection->updateOne(
        [
            "userID"=>$_POST["userID"],
            "companyID"=>$_POST["companyID"],
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
<?php
require_once "../configs/index.php";

if (isset($client) && isset($_POST["userID"]) && isset($_POST["companyID"])) {
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $reportsCollection->insertOne([
        "userID"=>$_POST["userID"],
        "companyID"=>$_POST["companyID"],
        "timeFields"=>$_POST["timeFields"] ? json_decode($_POST["timeFields"]) : [],
        "textFields"=>$_POST["textFields"] ? json_decode($_POST["textFields"]) : [],
        "jalaliDate"=>$_POST["jalaliDate"],
        "createdAt"=>time()
    ]);
    exit("200");
}
exit("400");



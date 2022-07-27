<?php
require_once "../configs/index.php";

use Morilog\Jalali\Jalalian;

if (isset($client) && isset($_GET["userID"]) && isset($_GET["companyID"])) {
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $report = $reportsCollection->findOne([
        "userID"=>$_GET["userID"],
        "companyID"=>$_GET["companyID"],
        "jalaliDate"=>$_GET["jalaliDate"],
    ]);

    if(isset($report)){
        $reportsCollection->updateOne(
            [
                "userID"=>$_GET["userID"],
                "companyID"=>$_GET["companyID"],
                "jalaliDate"=>$_GET["jalaliDate"]
            ]
            ,
            [ '$set' =>
                [
                    "timeFields"=>$_GET["timeFields"] ? json_decode($_GET["timeFields"]) : [],
                    "textFields"=>$_GET["textFields"] ? json_decode($_GET["textFields"]) : [],
                    "updatedAt"=>time()
                ]
            ]

        );
    }else{
        $reportsCollection->insertOne([
            "userID"=>$_GET["userID"],
            "companyID"=>$_GET["companyID"],
            "timeFields"=>$_GET["timeFields"] ? json_decode($_GET["timeFields"]) : [],
            "textFields"=>$_GET["textFields"] ? json_decode($_GET["textFields"]) : [],
            "jalaliDate"=>$_GET["jalaliDate"],
            "dayTimestamp"=>(new Jalalian(explode("/",$_GET["jalaliDate"])[0], explode("/",$_GET["jalaliDate"])[1], explode("/",$_GET["jalaliDate"])[2]))->getTimestamp(),
            "createdAt"=>time()
        ]);
    }


    exit("200");
}
exit("400");



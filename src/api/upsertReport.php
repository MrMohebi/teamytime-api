<?php
require_once "../configs/index.php";

use Morilog\Jalali\Jalalian;

if (isset($client) && isset($_POST["userID"]) && isset($_POST["companyID"])) {
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $report = $reportsCollection->findOne([
        "userID"=>$_POST["userID"],
        "companyID"=>$_POST["companyID"],
        "jalaliDate"=>$_POST["jalaliDate"],
    ]);

    if(isset($report)){
        $reportsCollection->updateOne(
            [
                "userID"=>$_POST["userID"],
                "companyID"=>$_POST["companyID"],
                "jalaliDate"=>$_POST["jalaliDate"]
            ]
            ,
            [ '$set' =>
                [
                    "timeFields"=>$_POST["timeFields"] ? json_decode($_POST["timeFields"]) : [],
                    "textFields"=>$_POST["textFields"] ? json_decode($_POST["textFields"]) : [],
                    "updatedAt"=>time()
                ]
            ]

        );
    }else{
        $reportsCollection->insertOne([
            "userID"=>$_POST["userID"],
            "companyID"=>$_POST["companyID"],
            "timeFields"=>$_POST["timeFields"] ? json_decode($_POST["timeFields"]) : [],
            "textFields"=>$_POST["textFields"] ? json_decode($_POST["textFields"]) : [],
            "jalaliDate"=>$_POST["jalaliDate"],
            "dayTimestamp"=>(new Jalalian(explode("/",$_POST["jalaliDate"])[0], explode("/",$_POST["jalaliDate"])[1], explode("/",$_POST["jalaliDate"])[2]))->getTimestamp(),
            "createdAt"=>time()
        ]);
    }


    exit("200");
}
exit("400");



<?php
require_once "../configs/index.php";

use Morilog\Jalali\Jalalian;

if (isset($client) && isset($_GET["userID"]) && isset($_GET["companyID"]) && strlen($_GET["userID"]) == 24 && strlen($_GET["companyID"]) == 24) {
    $URL = "https://teladminu.devmrm.ir/hook.php";
    $unimunUserIDs = [
        "62d29adb4546ab0f754ee4ce",
        "62d29adb4546ab0f754ee4cf",
        "62d29adb4546ab0f754ee4d0"
    ];

    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');
    $user = $usersCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($_GET["userID"])]);
    if(!$user){
        exit("404");
    }


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


    if(in_array($_GET["userID"], $unimunUserIDs)){
        $report = $reportsCollection->findOne([
            "userID"=>$_GET["userID"],
            "companyID"=>$_GET["companyID"],
            "jalaliDate"=>$_GET["jalaliDate"],
        ]);
        $data = [
            'isNewDailyReport'=>true,
            'report'=>json_encode($report),
            'user'=>json_encode($user),
            'isTest'=>false,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL."?".http_build_query($data));
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $x = curl_exec($ch);
        curl_close($ch);
    }

    exit("200");
}
exit("400");



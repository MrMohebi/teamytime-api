<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Morilog\Jalali\Jalalian;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../../");
$dotenv->load();

$client = new MongoDB\Client(
    "mongodb://".$_ENV['DB_USER'].":".$_ENV['DB_PASS']."@vpn.m3m.dev:27011/?retryWrites=true&w=majority"
);

$autoReportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'autoReports');
$reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');



$autoUserIDs = [
    "62d29adb4546ab0f754ee4ce",
//    "62d29adb4546ab0f754ee4cf",
//    "62d29adb4546ab0f754ee4d0"
];
$companyID = "62d39f952710907a46033c3c";
$URL = 'https://time.m3m.dev/api/upsertReport.php';


$today = Jalalian::now()->format('Y/m/d');



foreach ($autoUserIDs as $userID){

    if(!$reportsCollection->findOne(["userID"=>$userID, 'jalaliDate'=>$today])){
        $sampleReport = $autoReportsCollection->findOne(['userID'=>$userID, "companyID"=>$companyID, "isSent"=>0]);
        if($sampleReport){
            $data = [
               'userID'=>$userID,
               'companyID'=>$companyID,
               'jalaliDate'=>$today ,
               'timeFields'=>json_encode($sampleReport->timeFields),
               'textFields'=>json_encode($sampleReport->textFields),
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $URL."?".http_build_query($data));
            curl_exec($ch);
            curl_close($ch);

            $autoReportsCollection->updateOne(["_id"=>new MongoDB\BSON\ObjectId($sampleReport->_id->__toString())],['$set'=>['isSent'=>1]]);
        }else{
            echo "no sample report";
        }
    }

}

$autoReportsCollection->updateOne(['name'=>'test'], ['$inc'=>['count'=>1]], ['upsert'=>true]);


<?php
require_once "../configs/index.php";

use Morilog\Jalali\Jalalian;
use MongoDB\BSON\Regex;

$headers = apache_request_headers();

if (isset($client) && isAdminAuth($headers['Token'])) {
    $DAYS_TO_GET_DEFAULT = 7;

    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');


    $company = $companiesCollection->findOne(["eName"=>new Regex(preg_quote('Arnoya'), 'i')]);

    $startDate = $endDate = $tempDay = null;
    $daysArray = [];

    if(isset($_GET["startDate"])){
        $tDateE = explode("/",$_GET["startDate"]);
        $startDate = (new Jalalian($tDateE[0], $tDateE[1], $tDateE[2]));
        $tempDay = (new Jalalian($tDateE[0], $tDateE[1], $tDateE[2]));
    }else{
        $startDate = Jalalian::now()->subDays($DAYS_TO_GET_DEFAULT);
        $tempDay = Jalalian::now()->subDays($DAYS_TO_GET_DEFAULT);
    }
    if(isset($_GET["endDate"])){
        $tDateE = explode("/",$_GET["endDate"]);
        $endDate = (new Jalalian($tDateE[0], $tDateE[1], $tDateE[2]));
    }else{
        $endDate = Jalalian::now();
    }

    $reports = $reportsCollection->find(["companyID"=>(string)$company->_id, "dayTimestamp"=>['$gte'=> $startDate->getTimestamp(),'$lte'=> $endDate->getTimestamp()]], ["sort"=>array('jalaliDate' => -1)])->toArray();

    while ($tempDay->lessThanOrEqualsTo($endDate)){
        $daysArray[] = $tempDay->format("Y/m/d");
        $tempDay = $tempDay->addDays(1);
    }

    $result = [];

    for ($i = 0; $i < count($reports); $i++){
        $c = json_decode(json_encode($reports[$i]),true);
        $reports[$i]->id = $c["_id"]['$oid'];
        unset($reports[$i]->_id);

        // join user
        $user = $usersCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($reports[$i]->userID)]);
        $reports[$i]->user = $user;

        $result[$reports[$i]->jalaliDate][] = $reports[$i];
    }


    foreach ($daysArray as $eDay) {
        if (!isset($result[$eDay])) {
            $result[$eDay] = null;
        }
    }

    ksort($result);
    exit(json_encode($result));
}else{
    exit(401);
}


function isAdminAuth($token){
    return $token === "093845b5f724e4a047c9f2221cd903b4";
}
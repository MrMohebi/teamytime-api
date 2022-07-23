<?php
require_once "../configs/index.php";

use Morilog\Jalali\Jalalian;

if (isset($client) && isset($_GET["userID"])) {
    $DAYS_TO_GET = 7;

    $startDate = $endDate = $tempDay = null;
    $daysArray = [];

    if(isset($_GET["startDate"])){
        $tDateE = explode("/",$_GET["startDate"]);
        $startDate = (new Jalalian($tDateE[0], $tDateE[1], $tDateE[2]));
        $tempDay = (new Jalalian($tDateE[0], $tDateE[1], $tDateE[2]));
    }else{
        $startDate = Jalalian::now()->subDays($DAYS_TO_GET);
        $tempDay = Jalalian::now()->subDays($DAYS_TO_GET);
    }

    if(isset($_GET["endDate"])){
        $tDateE = explode("/",$_GET["endDate"]);
        $endDate = (new Jalalian($tDateE[0], $tDateE[1], $tDateE[2]));
    }else{
        $endDate = Jalalian::now();
    }


    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $reports = $reportsCollection->find(["userID"=>$_GET["userID"], "dayTimestamp"=>['$gte'=> $startDate->getTimestamp(),'$lte'=> $endDate->getTimestamp()]], ["sort"=>array('jalaliDate' => -1)])->toArray();


    while ($tempDay->lessThanOrEqualsTo($endDate)){
        $daysArray[] = $tempDay->format("Y/m/d");
        $tempDay = $tempDay->addDays(1);
    }


    $result = [];

    for ($i = 0; $i < count($reports); $i++){
        $c = json_decode(json_encode($reports[$i]),true);
        unset($reports[$i]->_id);
        $reports[$i]->id = $c["_id"]['$oid'];

        $result[$reports[$i]->jalaliDate] = $reports[$i];
    }

    foreach ($daysArray as $eDay){
        if(!isset($result[$eDay])){
            $result[$eDay] = null;
        }
    }

    ksort($result);
    exit(json_encode($result));
}
exit("400");

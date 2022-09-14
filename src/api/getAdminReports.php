<?php
require_once "../configs/index.php";

use Morilog\Jalali\Jalalian;

$headers = apache_request_headers();
$TOKEN = $headers['Token'];

if (isset($client)) {
    $DAYS_TO_GET_DEFAULT = 7;

    $toleranceTime = 1 * 60 * 60;

    $adminsCollection = $client->selectCollection($_ENV['DB_NAME'], 'admins');
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');

    if(!($admin = $adminsCollection->findOne(["token"=>$TOKEN]))){
        exit("401");
    }



    $company = $companiesCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($admin->companyID)]);

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

    $reports = $reportsCollection->find(["companyID"=>$company->_id->__toString(), "dayTimestamp"=>['$gte'=> $startDate->getTimestamp(),'$lte'=> $endDate->getTimestamp()]], ["sort"=>array('jalaliDate' => -1, "createdAt"=>1)])->toArray();

    while ($tempDay->lessThanOrEqualsTo($endDate)){
        $daysArray[] = $tempDay->format("Y/m/d");
        $tempDay = $tempDay->addDays(1);
    }

    $result = [];
    $sentUsersID = [];

    for ($i = 0; $i < count($reports); $i++){
        $reports[$i]->id = $reports[$i]->_id->__toString();
        unset($reports[$i]->_id);

        if(strlen($reports[$i]->userID) !== 24)
            continue;

        // join user
        $user = $usersCollection->findOne(["_id"=>new MongoDB\BSON\ObjectId($reports[$i]->userID)]);

        if($user){
            $reports[$i]->user = $user;

            $result[$reports[$i]->jalaliDate]['reports'][] = $reports[$i];
            $result[$reports[$i]->jalaliDate]['sentUsers'][] = $user;
            $sentUsersID[$reports[$i]->jalaliDate][] = $user->_id;
        }
    }


    foreach ($daysArray as $eDay) {
        $result[$eDay]['unsentUsers'] = $usersCollection->find(["companyID"=>$company->_id->__toString(),"_id"=>['$nin'=>$sentUsersID[$eDay]??[]]])->toArray();

        $dayArr = explode("/",$eDay);
        $date = new Jalalian($dayArr[0],$dayArr[1],$dayArr[2]);

        $relativeTolerance = $date->addDays(1)->addSeconds($toleranceTime);
        $canEdit = $relativeTolerance->greaterThan(Jalalian::now());

        $isGraterThanToday = $date->toCarbon()->greaterThan(Carbon\Carbon::now());

        $remainTime = $relativeTolerance->getTimestamp() - Jalalian::now()->getTimestamp();

        $remainTime = $canEdit  ? $remainTime : -1;
        $remainTime = !$isGraterThanToday ? $remainTime : $relativeTolerance->getTimestamp() - time();

        $result[$eDay]['canEdit'] = $canEdit;
        $result[$eDay]['remainTime'] = $remainTime;
    }

    ksort($result);
    exit(json_encode($result));
}else{
    exit(400);
}
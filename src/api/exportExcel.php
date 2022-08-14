<?php
require_once "../configs/index.php";

use MongoDB\BSON\Regex;
use Morilog\Jalali\Jalalian;

$data = array(
    array("NAME" => "John Doe", "EMAIL" => "john.doe@gmail.com", "GENDER" => "Male", "COUNTRY" => "United States"),
    array("NAME" => "Gary Riley", "EMAIL" => "gary@hotmail.com", "GENDER" => "Male", "COUNTRY" => "United Kingdom"),
    array("NAME" => "Edward Siu", "EMAIL" => "siu.edward@gmail.com", "GENDER" => "Male", "COUNTRY" => "Switzerland"),
    array("NAME" => "Betty Simons", "EMAIL" => "simons@example.com", "GENDER" => "Female", "COUNTRY" => "Australia"),
    array("NAME" => "Frances Lieberman", "EMAIL" => "lieberman@gmail.com", "GENDER" => "Female", "COUNTRY" => "United Kingdom")
);



if(isset($client)){
    $MONTH = 5;
    $YEAR = 1401;
    $PREVIOUS_MONTHS = 2;

    $TIME_FIELD = 'مدت زمان کار';

    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');

    $company = $companiesCollection->findOne(["eName"=>new Regex(preg_quote('Arnoya'), 'i')]);


    $startDate = (new Jalalian($YEAR, $MONTH,1))->getFirstDayOfMonth();
    $endDate = $startDate->addMonths()->subDays();

    $HEADERS = [$YEAR."میانگین تمامی روز های","اعضا","تیم تخصصی","ردیف"];
    // append months sum header
    for ($i = $PREVIOUS_MONTHS; $i >=0; $i--){
        $text = "مجموع ساعت کار ";
        if($i>0){
            $text .= $startDate->subMonths($i)->format("%B") . " " . $YEAR;
        }else{
            $text .= $startDate->format("%B") . " " . $YEAR;
        }
        array_unshift($HEADERS, $text);
    }


    $daysArray = [];
    $tempDay = $startDate;
    while ($tempDay->lessThanOrEqualsTo($endDate)){
        $daysArray[] = $tempDay->format("Y/m/d");
        array_unshift($HEADERS, "گزارش " . $tempDay->format('%B d'));
        $tempDay = $tempDay->addDays(1);
    }

//    print_r($HEADERS);

    $users = $usersCollection->find(["companyID"=>$company->_id->__toString()], ["sort"=>array('jalaliDate' => 1, "team"=>-1)]);


    $result = [];

    foreach ($users as $index=>$user){
        $reports = $reportsCollection->find([
            "userID"=>$user->_id->__toString(),
            "companyID"=>$company->_id->__toString(),
            "dayTimestamp"=>[
                '$gte'=> $startDate->getTimestamp(),
                '$lte'=> $endDate->getTimestamp()
            ]
        ], ["sort"=>array('jalaliDate' => 1)]);

        // add Row number
        $result[$index][] = $index+1;
        // add team
        $result[$index][] = $user->team;
        // add name
        $result[$index][] = $user->name;
        // add average on year
        $result[$index][] = "averageYearDay";

        $dayWalk = $startDate;

        foreach ($reports as $userReport){

        }

        print_r($result);

        break;
    }
}











//// Excel file name for download
//$fileName = "codexworld_export_data-" . date('Ymd') . ".xlsx";
//
//// Headers for download
////header("Content-Disposition: attachment; filename=\"$fileName\"");
////header("Content-Type: application/vnd.ms-excel");
//
//$flag = false;
//foreach($data as $row) {
//    if(!$flag) {
//        // display column names as first row
//        echo implode("\t", array_keys($row)) . "\n";
//        $flag = true;
//    }
//    // filter data
//    array_walk($row, 'filterData');
//    echo implode("\t", array_values($row)) . "\n";
//}
//
//exit;





function filterData(&$str): void{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(str_contains($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

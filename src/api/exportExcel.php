<?php
require_once "../configs/index.php";

use MongoDB\BSON\Regex;
use Morilog\Jalali\Jalalian;

// Excel file name for download
$fileName = "export_data-" . date('Ymd') . time() . ".xlsx";

// Headers for download
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Content-Type: application/vnd.ms-excel");

if(isset($client)){
    $MONTH = 5;
    $YEAR = 1401;
    $PREVIOUS_MONTHS = 2;

    $REQUEST_FIELD = 'مدت زمان کار';

    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');

    $company = $companiesCollection->findOne(["eName"=>new Regex(preg_quote('Arnoya'), 'i')]);


    $startDate = (new Jalalian($YEAR, $MONTH,1))->getFirstDayOfMonth();
    $endDate = $startDate->addMonths()->subDays();

    $monthArray= [];
    $HEADERS = [$YEAR."میانگین تمامی روز های","اعضا","تیم تخصصی","ردیف"];
    // append months sum header
    for ($i = $PREVIOUS_MONTHS; $i >=0; $i--){
        $text = "مجموع ساعت کار ";
        $monthDate = $i>0 ? $startDate->subMonths($i) : $startDate;

        $text .= $monthDate->format("%B") . " " . $YEAR;
        array_unshift($HEADERS, $text);
        $monthArray[] =  $monthDate->getFirstDayOfMonth()->format("Y/m/d");
    }


    $daysArray = [];
    $tempDay = $startDate;
    while ($tempDay->lessThanOrEqualsTo($endDate)){
        $daysArray[] = $tempDay->format("Y/m/d");
        array_unshift($HEADERS, "گزارش " . $tempDay->format('%B d'));
        $tempDay = $tempDay->addDays(1);
    }


    $users = $usersCollection->find(["companyID"=>$company->_id->__toString()], ["sort"=>array('jalaliDate' => 1, "team"=>-1)]);


    $result = [];

    foreach ($users as $index=>$user){
        $yearReports = $reportsCollection->find([
            "userID"=>$user->_id->__toString(),
            "companyID"=>$company->_id->__toString(),
            "dayTimestamp"=>[
                '$gte'=> $startDate->getFirstDayOfYear()->getTimestamp(),
                '$lte'=> $startDate->addYears()->getFirstDayOfYear()->subDays()->getTimestamp()
            ]
        ], ["sort"=>array('jalaliDate' => 1)]);

        $yearSum = 0;
        $monthsSum = [];
        $daysHours = [];

        foreach ($yearReports as $userReport){
            $reportDate= Jalalian::forge($userReport->dayTimestamp);
            $fieldHour = "";
            foreach ($userReport->timeFields as $timeField){
                if($timeField['title'] == $REQUEST_FIELD)
                    $fieldHour = $timeField['value'];
            }
            [$hour, $minute] = explode(":", $fieldHour);
            $filedTime = round((int)$hour + ($minute/60), 1) ;
            if($reportDate->getMonth() == $startDate->getMonth()){
                $daysHours[$userReport->jalaliDate] = $filedTime;
            }

            if(!isset($monthsSum[$reportDate->getFirstDayOfMonth()->format("Y/m/d")]))
                $monthsSum[$reportDate->getFirstDayOfMonth()->format("Y/m/d")] = 0;

            $monthsSum[$reportDate->getFirstDayOfMonth()->format("Y/m/d")] +=  $filedTime;
            $yearSum += $filedTime;
        }

        // add Row number
        $result[$index][0] = $index+1;
        // add team
        $result[$index][] = $user->team ?? "";
        // add name
        $result[$index][] = $user->name;
        // add average on year
        $result[$index][] = round($yearSum/288, 1);
        // add month sums
        foreach ($monthArray as $Month){
            $result[$index][] = $monthsSum[$Month] ?? 0;
        }
        // add days
        foreach ($daysArray as $eDay){
            $result[$index][] = $daysHours[$eDay] ?? -1;
        }

        $result[$index] = array_reverse($result[$index]);
    }

    // headers
    echo implode("\t", $HEADERS) . "\n";
    // data
    foreach ($result as $dataRow){
        array_walk($dataRow, 'filterData');
        echo implode("\t", array_values($dataRow)) . "\n";
    }

}



function filterData(&$str): void{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(str_contains($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

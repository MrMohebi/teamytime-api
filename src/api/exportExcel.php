<?php
require_once "../configs/index.php";

use MongoDB\BSON\Regex;
use Morilog\Jalali\Jalalian;

$headers = apache_request_headers();

$COMPANY_NAME= 'Arnoya';

$MONTH = $_GET['month'];
$YEAR = $_GET['year'];
$PREVIOUS_MONTHS = $_GET['previousMonth'];

//$REQUEST_FIELD = 'مدت زمان کار';
$REQUEST_FIELD = $_GET['fieldTitle'];


// Excel file name for download
$fileName = "$REQUEST_FIELD/$YEAR-$MONTH.xlsx";

// Headers for download
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Content-Type: application/vnd.ms-excel");

if(isset($client) && isAdminAuth($headers['Token'])){

    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $usersCollection = $client->selectCollection($_ENV['DB_NAME'], 'users');

    $company = $companiesCollection->findOne(["eName"=>new Regex(preg_quote($COMPANY_NAME), 'i')]);


    $startDate = (new Jalalian($YEAR, $MONTH,1))->getFirstDayOfMonth();
    $endDate = $startDate->addMonths()->subDays();

    $monthArray= [];
    $HEADERS = [$YEAR."میانگین $REQUEST_FIELD تمامی روز های","اعضا","تیم تخصصی","ردیف"];
    // append months sum header
    for ($i = $PREVIOUS_MONTHS; $i >=0; $i--){
        $text = "مجموع " . $REQUEST_FIELD . " ";
        $monthDate = $i>0 ? $startDate->subMonths($i) : $startDate;

        $text .= $monthDate->format("%B") . " " . $YEAR;
        array_unshift($HEADERS, $text);
        $monthArray[] =  $monthDate->getFirstDayOfMonth()->format("Y/m/d");
    }


    $daysArray = [];
    $tempDay = $startDate;
    while ($tempDay->lessThanOrEqualsTo($endDate)){
        $daysArray[] = $tempDay->format("Y/m/d");
        array_unshift($HEADERS, "گزارش " . $tempDay->format('d %B'));
        $tempDay = $tempDay->addDays(1);
    }


    $users = $usersCollection->find(["companyID"=>$company->_id->__toString()], ["sort"=>array('jalaliDate' => 1, "team"=>-1)]);


    $resultTeamGroup = [];

    foreach ($users as $index=>$user){
        $row = [];

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


        // add team
        $row[] = $user->team ?? "";
        // add name
        $row[] = $user->name;
        // add average on year
        $row[] = round($yearSum/288, 1);
        // add month sums
        foreach ($monthArray as $Month){
            $row[] = $monthsSum[$Month] ?? 0;
        }
        // add days
        foreach ($daysArray as $eDay){
            $row[] = $daysHours[$eDay] ?? -1;
        }

        $resultTeamGroup[$user->team][$index] = $row;

    }


    $result = [];
    $walker = 1;
    foreach ($resultTeamGroup as $teamName=>$team){
        $totalRow = [];
        foreach ($team as $member){
            if(strlen($teamName) > 1){
                for ($i = 0; $i < count($member); $i++){
                    if(!isset($totalRow[$i]))
                        $totalRow[$i] = null;

                    if(is_numeric($member[$i])){
                        $totalRow[$i] += $member[$i];
                        if($totalRow[$i] < 0)
                            $totalRow[$i] = -1;
                    }else{
                        $totalRow[$i] = $teamName;
                    }
                }
            }
            array_unshift($member, $walker);$walker++;

            $result[] = array_reverse($member);
        }
        if(count($totalRow) > 0){
            array_unshift($totalRow, $walker);$walker++;
            $totalRow[3] = round(((float)$totalRow[3])/count($team),1);
            $totalRow[2] = "Total";
            $result[] = array_reverse($totalRow);
        }
    }
//
//    $spreadsheet = new Spreadsheet();
//    $sheet = $spreadsheet->getSheet(0);
//    $sheet->setCellValue($HEADERS);
//    echo $sheet;
//    exit();

    // headers
    echo implode("\t", $HEADERS) . "\n";
    // data
    foreach ($result as $dataRow){
        array_walk($dataRow, 'filterData');
        echo implode("\t", array_values($dataRow)) . "\n";
    }

}else{
    exit("401");
}



function filterData(&$str): void{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(str_contains($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

function isAdminAuth($token){
    return $token === "093845b5f724e4a047c9f2221cd903b4";
}
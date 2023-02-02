<?php

use MongoDB\BSON\Regex;


require_once "../configs/index.php";

if (isset($client) && isset($_GET["company"]) && isset($_GET["month"]) && isset($_GET["year"])) {
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $company = $companiesCollection->findOne(["eName"=>new Regex(preg_quote($_GET["company"]), 'i')]);
    $timeFieldNames = [];
    foreach ($company->timeFields as $timeField){
        $timeFieldNames[] = $timeField['title'];
    }

    $month = $_GET["month"];
    $year = $_GET["year"];

    foreach ($timeFieldNames as $fieldName){
        $url =  "https://timeservice.unimun.me/api/exportExcel.php?month=$month&year=$year&previousMonth=12&fieldTitle=$fieldName";
        echo $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        $fp = fopen('../tmp/rss.xlsx', 'w+');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec ($ch);
        curl_close ($ch);
        fclose($fp);
//        grab_image($url, "../tmp/aaa20.xmls");
//        $file = file_get_contents($url);
//        echo basename($url);
//        file_put_contents('./myDir/myFile.gif', $image);
    }


}else{
    exit("400");
}

function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}
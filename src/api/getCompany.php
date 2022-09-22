<?php

use MongoDB\BSON\Regex;

require_once "../configs/index.php";

if (isset($client) && isset($_GET["company"])) {
    $companiesCollection = $client->selectCollection($_ENV['DB_NAME'], 'companies');
    $company = $companiesCollection->findOne(["eName"=>new Regex(preg_quote($_GET["company"]), 'i')]);
    if($company){
        $c = json_decode(json_encode($company),true);
        unset($company->_id);
        $company->id = $c["_id"]['$oid'];
        $company->allowedDays = 3;
        echo json_encode($company);
    }else{
        echo 404;
    }
}


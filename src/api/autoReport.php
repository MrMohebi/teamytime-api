<?php
require_once "../configs/index.php";

if(isset($client)){
    $autoReportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'autoReports');

    $autoReportsCollection->updateOne(['name'=>'test'], ['$inc'=>['count'=>1]], ['upsert'=>true]);
    echo 'done';

}

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../../");
$dotenv->load();

$client = new MongoDB\Client(
    "mongodb://".$_ENV['DB_USER'].":".$_ENV['DB_PASS']."@".$_ENV['DB_URL']."/?retryWrites=true&w=majority"
);

$autoReportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'autoReports');

$autoReportsCollection->updateOne(['name'=>'test'], ['$inc'=>['count'=>1]], ['upsert'=>true]);
$output = shell_exec('ls');

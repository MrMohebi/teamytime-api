<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new MongoDB\Client(
    'mongodb+srv://root:dbtimePass@vpn.devmrm.ir/test?retryWrites=true&w=majority'
);

$db = $client->test;
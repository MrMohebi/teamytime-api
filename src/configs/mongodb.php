<?php
include_once "env.php";
$client = new MongoDB\Client(
    "mongodb://".$_ENV['DB_USER'].":".$_ENV['DB_PASS']."@".$_ENV['DB_URL']."/?retryWrites=true&w=majority"
);


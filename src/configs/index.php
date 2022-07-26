<?php
require_once __DIR__ . '/../../vendor/autoload.php';

date_default_timezone_set("Asia/Tehran");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");


require_once "mongodb.php";
require_once "env.php";

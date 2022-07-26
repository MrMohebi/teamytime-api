<?php
require_once __DIR__ . '/../../vendor/autoload.php';

date_default_timezone_set("Asia/Tehran");

header("Access-Control-Allow-Headers: Authorization, Content-Type, Token");
header("Access-Control-Allow-Origin: *");
header('content-type: application/json; charset=utf-8');


require_once "mongodb.php";
require_once "env.php";

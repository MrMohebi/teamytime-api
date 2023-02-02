<?php
require_once "../../../configs/index.php";
require_once "Styles.php";
require_once "ExelV1.php";

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. "data.xlsx".'"');

// https://timeservice.unimun.me/api/exel/v1/export.php


$exel = new \exel\v1\ExelV1();
$spreadsheet = $exel->export();







$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit();
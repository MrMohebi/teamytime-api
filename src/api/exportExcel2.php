<?php
require_once "../configs/index.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$HEADERS = ["میانگین  تمامی روز های","اعضا","تیم تخصصی","ردیف"];


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getSheet(0);
$sheet->setCellValue($HEADERS);
echo $sheet;
exit();
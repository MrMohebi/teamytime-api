<?php

namespace exel\v1;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ExelV1
{
    public Spreadsheet $spreadsheet;
    public Worksheet $worksheet;

    private function setStyle($cell, $styleArr): void{
        if(is_array($cell)){
            foreach ($cell as $eCell){
                $this->worksheet->getStyle($eCell)->applyFromArray($styleArr);
            }
        }else{
            $this->worksheet->getStyle($cell)->applyFromArray($styleArr);
        }
    }

    /**
     * @throws Exception
     */
    public function __construct(){
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeSheetByIndex($this->spreadsheet->getIndex($this->spreadsheet->getSheetByName('Worksheet')));
    }

    /**
     * @throws Exception
     */
    private function createAndSetWorkSheet($name):void{
        $this->worksheet = new Worksheet($this->spreadsheet, $name);
        $this->worksheet->setRightToLeft(true);
        $this->spreadsheet->addSheet($this->worksheet, 0);
    }

    private function setName($name):void{
        $this->worksheet->setCellValue('L1', "نام کارمند : ");
        $this->worksheet->mergeCells('M1:O1');
        $this->worksheet->setCellValue('M1', $name);
        $this->setStyle(['L1', 'M1'], Styles::userNameCell());
    }

    /**
     * @throws Exception
     */
    private function setHeaders():void{
        // --------------- header 1 ----------------
        $this->worksheet->mergeCells('A1:E1');
        $this->worksheet->setCellValue('A1', 'گزارش کار');

        $this->worksheet->setCellValue('A2', 'تاریخ');

        $this->worksheet->mergeCells('B2:E2');
        $this->worksheet->setCellValue('B2', 'نام فیلد توضیحات');

        $this->setStyle(['A1', 'A2', 'B2'], Styles::headerOneCell());

        // --------------- header 2 ----------------
        $this->worksheet->mergeCells('F1:H1');
        $this->worksheet->setCellValue('F1', 'کار حضوری');

        $this->worksheet->setCellValue('F2', 'ساعت شروع');
        $this->worksheet->setCellValue('G2', 'ساعت پایان');
        $this->worksheet->setCellValue('H2', 'مجموع کار');

        $this->setStyle(['F1', 'F2', 'G2', 'H2'], Styles::headerTwoCell());

        // --------------- header 3 ----------------
        $this->worksheet->setCellValue('I1', 'کار غیر حضوری');
        $this->worksheet->setCellValue('I2', 'مدت زمان');

        $this->setStyle(['I1', 'I2'], Styles::headerThreeCell());

        // --------------- header 4 ----------------
        $this->worksheet->setCellValue('J1', 'مرخصی');
        $this->worksheet->setCellValue('J2', 'مدت زمان');

        $this->setStyle(['J1', 'J2'], Styles::headerFourCell());

        // --------------- header 3 ----------------
        $this->worksheet->setCellValue('K1', 'کل کار روز');
        $this->worksheet->setCellValue('K2', 'مدت زمان');

        $this->setStyle(['K1', 'K2'], Styles::headerOneCell());


        // --------------- sum headers ----------------
        $this->worksheet->mergeCells('L2:M2');
        $this->worksheet->mergeCells('L3:M3');
        $this->worksheet->mergeCells('L4:M4');
        $this->worksheet->mergeCells('L5:M5');
        $this->worksheet->setCellValue('L2', ' کار حضوری');
        $this->worksheet->setCellValue('L3', 'کار غیر حضوری');
        $this->worksheet->setCellValue('L4', 'مرخصی');
        $this->worksheet->setCellValue('L5', 'کل کار');

        $this->worksheet->setCellValue('O2', 'ساعت');
        $this->worksheet->setCellValue('O3', 'ساعت');
        $this->worksheet->setCellValue('O4', 'ساعت');
        $this->worksheet->setCellValue('O5', 'ساعت');


        $this->setStyle(['O2', 'O3', 'O4', 'O5'], Styles::userNameCell());
        $this->setStyle(['L5', 'N5'], Styles::headerOneCell());
        $this->setStyle(['L2', 'N2'], Styles::generate([Styles::$bold, Styles::$purple]));
        $this->setStyle(['L3', 'N3'], Styles::generate([Styles::$bold, Styles::$blue]));
        $this->setStyle(['L4', 'N4'], Styles::generate([Styles::$bold, Styles::$orange]));


    }

    /**
     * @throws Exception
     */
    public function createUserSheet($name):void{
        $this->createAndSetWorkSheet($name);
        $this->setName($name);
        $this->setHeaders();

        // set Column size as contents
        foreach(range('A','Z') as $columnID) {
            $this->worksheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    /**
     * @throws Exception
     */
    public function export(): Spreadsheet{
        $this->createUserSheet('محمد مهدی محبی');
        return $this->spreadsheet;
    }
}
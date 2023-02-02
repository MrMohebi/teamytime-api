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
        $this->worksheet->setCellValue('A1', $name);
        $this->setStyle('A1', Styles::$userNameCell);
    }

    /**
     * @throws Exception
     */
    private function setHeaders():void{
        // --------------- header 1 ----------------
        $this->worksheet->mergeCells('A2:E2');
        $this->worksheet->setCellValue('A2', 'گزارش کار');

        $this->worksheet->setCellValue('A3', 'تاریخ');

        $this->worksheet->mergeCells('B3:E3');
        $this->worksheet->setCellValue('B3', 'نام فیلد توضیحات');

        $this->setStyle(['A2', 'A3', 'B3'], Styles::$headerOneCell);

        // --------------- header 2 ----------------
        $this->worksheet->mergeCells('F2:H2');
        $this->worksheet->setCellValue('F2', 'کار حضوری');

        $this->worksheet->setCellValue('F3', 'ساعت شروع');
        $this->worksheet->setCellValue('G3', 'ساعت پایان');
        $this->worksheet->setCellValue('H3', 'مجموع کار');

        $this->setStyle(['F2', 'F3', 'G3', 'H3'], Styles::$headerTwoCell);

        // --------------- header 3 ----------------
        $this->worksheet->setCellValue('I2', 'کار غیر حضوری');
        $this->worksheet->setCellValue('I3', 'مدت زمان');

        $this->setStyle(['I2', 'I3'], Styles::$headerThreeCell);

        // --------------- header 4 ----------------
        $this->worksheet->setCellValue('J2', 'مرخصی');
        $this->worksheet->setCellValue('J3', 'مدت زمان');

        $this->setStyle(['J2', 'J3'], Styles::$headerFourCell);

        // --------------- header 3 ----------------
        $this->worksheet->setCellValue('K2', 'کل کار روز');
        $this->worksheet->setCellValue('K3', 'مدت زمان');

        $this->setStyle(['K2', 'K3'], Styles::$headerOneCell);

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
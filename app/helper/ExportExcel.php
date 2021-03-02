<?php

require_once 'phplugin/PHPExcel/PHPExcel.php';

class ExportExcel
{
    private $Creator = 'LP3I Depok Cilodong';
    private $Title = 'Psikotest';
    private $Subject = 'Data Peserta Psikotest';
    private $Description = 'Data Peserta Psikotest';
    private $Keyword = 'Psikotest';
    private $FileName = 'Data Peserta Psikotest';
    private $Excel, $Alphabet;

    public function __construct()
    {
        $this->Excel = new PHPExcel();
        $this->Alphabet = explode('|', 'A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z');
    }

    private function _extract($ColNames = [], $DataList = [])
    {
        $this->Excel->getProperties()->setCreator($this->Creator)->setLastModifiedBy($this->Creator)->setTitle($this->Title)->setSubject($this->Subject)->setDescription($this->Description)->setKeywords($this->Keyword);
        $StyleCol = array(
            'font' => array('bold' => true),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            )
        );
        $StyleRow = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            )
        );
        $this->Excel->setActiveSheetIndex(0)->setCellValue('A1', strtoupper($this->Subject));
        $this->Excel->getActiveSheet()->mergeCells('A1:' . $this->Alphabet[(sizeof($ColNames) - 1)] . '1');
        $this->Excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->Excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
        $this->Excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for ($cn = 0; $cn < sizeof($ColNames); $cn++) {
            $this->Excel->setActiveSheetIndex(0)->setCellValue($this->Alphabet[$cn] . '3', $ColNames[$cn]);
            $this->Excel->getActiveSheet()->getStyle($this->Alphabet[$cn] . '3')->applyFromArray($StyleCol);
        }
        $this->Excel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
        $this->Excel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $this->Excel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
        for ($dl = 0; $dl < sizeof($DataList); $dl++) {
            $this->Excel->setActiveSheetIndex(0)->setCellValue('A' . ($dl + 4), ($dl + 1));
            $this->Excel->getActiveSheet()->getStyle('A' . ($dl + 4))->applyFromArray($StyleRow);
            for ($data = 0; $data < sizeof($DataList[$dl]); $data++) {
                $this->Excel->setActiveSheetIndex(0)->setCellValue($this->Alphabet[($data + 1)] . ($dl + 4), $DataList[$dl][$data]);
                $this->Excel->getActiveSheet()->getStyle($this->Alphabet[($data + 1)] . ($dl + 4))->applyFromArray($StyleRow);
            }
            $this->Excel->getActiveSheet()->getRowDimension($dl + 4)->setRowHeight(20);
        }
        $this->Excel->getActiveSheet(0)->setTitle($this->Title);
        $this->Excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->FileName . '.xlsx"');
        header('Cache-Control: max-age=0');
        $ExcelWrite = PHPExcel_IOFactory::createWriter($this->Excel, 'Excel2007');
        $ExcelWrite->save('php://output');
    }

    public function export($label = [], $data = [], $config = [])
    {
        $labels = array('NO');
        $values = array();
        foreach ($data as $dat) {
            array_push($values, array_values($dat));
        }
        if (isset($config['filename'])) $this->FileName = $config['filename'];
        if (isset($config['subject'])) $this->Subject = $config['subject'];
        $this->_extract(array_merge($labels, $label), $values);
    }
}

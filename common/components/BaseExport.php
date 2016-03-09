<?php
namespace common\components;

use common\extensions\PHPExcel;

/**
 * author HDY.TT
 * 导出excel基类
 */
abstract class BaseExport extends BaseComponent
{

    public $excel_sort_order = [];//导出excel第一行内容对应信息
    //example [
    //  'hotel_name' => '酒店名称', hotel_name 表示数据库字段 酒店名称 为excel第一行显示
    //];
    private $_excel ;
    public function init()
    {
        parent::init();
    }

    public function export($title, $filename)
    {
        $excel = $this->setTemplate($this->excel_sort_order);
        $excel = $this->setTitle($this->_excel, $title);
        $data = $this->getData();
        $excel = $this->setExcelData($this->_excel, $data);
        $this->saveExcel($this->_excel, $filename);
    }
    protected function setTemplate(array $params)
    {
        $objPHPExcel = new PHPExcel();
        $m = 0;
        foreach($params as $k1=>$v1)
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr(65+$m).'1',$v1 );
            $m++;
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $this->_excel = $objPHPExcel;
        return $objPHPExcel;
    }

    protected function setExcelData($excel,$data = [])
    {
        if(empty($data)||!is_array($data)||count($data)<=0){
            return false;
        }
        foreach($data as $k => $v){
            $num=$k+2;
            $m1 = 0;
            foreach($v as $k3=>$v3){
                $excel->setActiveSheetIndex(0)->setCellValue(chr(65+$m1).$num, $v3, \PHPExcel_Cell_DataType::TYPE_STRING);
                $m1++;
            }
        }
        $this->_excel = $excel;
        return $excel;
    }

    public function setTitle($excel, $title)
    {
        $excel->getActiveSheet()->setTitle($title);
        $this->_excel = $excel;
        return $excel;
    }

    protected function saveExcel($excel, $filename = '1234')
    {
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        //header("Content-Type: application/force-download");
        //header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        header("Pragma: no-cache");
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    abstract function getData();

}

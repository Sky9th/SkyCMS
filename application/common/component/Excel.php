<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/21
 * Time: 11:25
 */
namespace app\common\component;

class Excel {

    protected $xlsCell  = array(
        array('orderid','单号',400),
        array('total','成交价格',70),
        array('tip','备注',105),
        array('toolid','主运单号',100),
        array('seller','销售平台',200),
    );

    protected $xlsData = [
        [
            'orderid'=>'test',
            'total'=>'test',
            'tip'=>'test',
            'toolid'=>'test',
            'seller'=>'test',
        ]
    ];

    /**
     * 导出Excel
     * @param $expCellName array 表头
     * @param $expTableData array 数据
     * @param $expTitle string 文件名
     * @param $path string 保存路径
     */
    public function export($expCellName, $expTableData, $expTitle, $path = "php://output"){
        //$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle;//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellName[$cellNum-1].'1');
        $objPHPExcel->setActiveSheetIndex()->setCellValue('A1', $expTitle);
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        $styleArray = array();
        $styleArray['alignment']['vertical'] = \PHPExcel_Style_Alignment::VERTICAL_CENTER;
        $styleArray['alignment']['horizontal'] = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
        $styleArray['font']['size'] = 16;
        $styleArray['font']['bold'] = true;
        $styleArray['font']['name'] = '楷体_GB2312';
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$cellName[$cellNum-1].'1')->applyFromArray($styleArray);

        $thArray = array();
        $thArray['alignment']['vertical'] = \PHPExcel_Style_Alignment::VERTICAL_CENTER;
        $thArray['alignment']['horizontal'] = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
        $thArray['font']['size'] = 9;
        $thArray['font']['name'] = '楷体_GB2312';
        $thArray['font']['bold'] = true;
        $thArray['borders'] = array(
            'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
        );

        for( $i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($cellName[$i])->setWidth($expCellName[$i][2]/8);
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(23);
            $objPHPExcel->getActiveSheet()->getStyle($cellName[$i].'2')->applyFromArray($thArray);
        }

        $cellArray = array();
        $cellArray['alignment']['vertical'] = \PHPExcel_Style_Alignment::VERTICAL_CENTER;
        $cellArray['alignment']['horizontal'] = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
        $cellArray['font']['size'] = 9  ;
        $cellArray['font']['name'] = '楷体_GB2312';
        $cellArray['borders'] = array(
            'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
            'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
        );
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet()->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
                $objPHPExcel->getActiveSheet()->getStyle($cellName[$j].($i+3))->applyFromArray($cellArray);
            }
            $objPHPExcel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(23);
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$expTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($path);

    }

    public function import($file_id){
        $file = '.'.get_file($file_id);
        if(!file_exists($file)){
            return false;
        }
        $data = [];
        $objPHPExcel = \PHPExcel_IOFactory::load($file);
        $sheets = $objPHPExcel->getAllSheets();
        foreach ($sheets as $sheet) {
            $data[] = $sheet->toArray();
        }
        if( count($data) == 1 ){
            return $data[0];
        }
        return $data;
    }

}
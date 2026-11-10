<?php
	require('../media/PHPExcel.php');
	require_once("otksr-oop.php");
	session_start();
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			// $username = 'arlbena';
		}
	// $ControlNumber = $_GET['ControlNumber'];
	$dateToday = date("M. d, Y");
	$dateTodayName = date("Ymd");
	
	/* Items from GET */
	$dateFrom 	= $_GET['startDate'];
	$dateTo 	= $_GET['endDate'];
	$EN			= $_GET['EN'];
	
	
	//BORDERS STYLE
	//set all borders corner to thin 
	$styleArray = array(
	  'borders' => array(
		'allborders' => array(
		  'style' => PHPExcel_Style_Border::BORDER_THIN //set borders
		)
	  ),
	  'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID, //set back color
        'color' => array('rgb'=>'FFFFFF'),
		)
	); 
	
	// create new phpexcel page
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	
	// set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	
	$objSheet = $objPHPExcel->getActiveSheet();
	$title = "EOM"; //set title cont.
	$objSheet->setTitle($title); //set title cont.
	
	$objSheet->getCell('A1')->setValue('EN'); 
	$objSheet->getCell('B1')->setValue('DATE IN'); 
	$objSheet->getCell('C1')->setValue('TIME IN'); 
	$objSheet->getCell('D1')->setValue('MODE IN'); 
	$objSheet->getCell('E1')->setValue('DATE OUT'); 
	$objSheet->getCell('F1')->setValue('TIME OUT');
	$objSheet->getCell('G1')->setValue('MODE OUT');
	$objSheet->getCell('H1')->setValue('PKID'); 
	
	$objSheet->getColumnDimension('A')->setWidth(15);
	$objSheet->getColumnDimension('B')->setWidth(15);
	$objSheet->getColumnDimension('C')->setWidth(15);
	$objSheet->getColumnDimension('D')->setWidth(15);
	$objSheet->getColumnDimension('E')->setWidth(15);
	$objSheet->getColumnDimension('F')->setWidth(15);
	$objSheet->getColumnDimension('G')->setWidth(15);
	$objSheet->getColumnDimension('H')->setWidth(15);
	
	$array_employee = OTKSR::getInstance()->excel_attendance_export($dateFrom,$dateTo,$EN);
	
	$countEom = 2;
	
	for($x=0;$x<count($array_employee['empno']);$x++){
		$pkid = $array_employee['pkid'][$x];
		$empno = $array_employee['empno'][$x];
		
		if($array_employee['date_in'][$x] != ''){
			$date_in =  date('m/d/Y', strtotime($array_employee['date_in'][$x]));
		}else{
			$date_in = '';
		}
		if($array_employee['date_out'][$x] != ''){
			$date_out =  date('m/d/Y', strtotime($array_employee['date_out'][$x]));
		}else{
			$date_out = '';
		}
		if($array_employee['time_in'][$x] != ''){
			$time_in =  date('H:i:s', strtotime($array_employee['time_in'][$x]));
		}else{
			$time_in = '';
		}
		if($array_employee['time_out'][$x] != ''){
			$time_out =  date('H:i:s', strtotime($array_employee['time_out'][$x]));
		}else{
			$time_out = '';
		}
		
		$mode_in = $array_employee['mode_in'][$x];
		$mode_out = $array_employee['mode_out'][$x];
		
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$countEom, $empno,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('B'.$countEom,  $date_in,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('C'.$countEom,  $time_in,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('D'.$countEom,  $mode_in,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('E'.$countEom, $date_out,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('F'.$countEom, $time_out,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('G'.$countEom,  $mode_out,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('H'.$countEom, $pkid,PHPExcel_Cell_DataType::TYPE_STRING);
		$countEom++;
	}
	
	 // $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setVisible(FALSE);
	
	$objSheet->getStyle('A1:'.'H'.($countEom - 1))->applyFromArray($styleArray);
	
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// header('Content-Disposition: attachment;filename=Raw Data '.$dateTodayName.'.xlsx');
	header('Content-Disposition: attachment;filename=Raw Data '.$dateTodayName.'.xls'); // used for excel 2003 format
	header('Cache-Control: max-age=0');
	
	// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // used for excel 2003 format
	$objWriter->save('php://output');
?>
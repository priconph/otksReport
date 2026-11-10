<?php
	require('../media/PHPExcel.php');
	require_once("otksr-oop.php");
	session_start();
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			//$username = 'arlbena';
		}
	//$ControlNumber = $_GET['ControlNumber'];
	$dateToday = date("M. d, Y");
	$dateTodayName = date("Ymd");

	//create new phpexcel page
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	//set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	
	$objSheet = $objPHPExcel->getActiveSheet();
	$title = "EOM"; //set title cont.
	$objSheet->setTitle($title); //set title cont.
	
	$objSheet->getCell('A1')->setValue('IDNUM'); 
	$objSheet->getCell('B1')->setValue('EMP_CODE'); 
	$objSheet->getCell('C1')->setValue('ABSENCES'); 
	$objSheet->getCell('D1')->setValue('UND_TIME'); 
	$objSheet->getCell('E1')->setValue('NIG_DIFF'); 
	$objSheet->getCell('F1')->setValue('SL'); 
	$objSheet->getCell('G1')->setValue('VL'); 
	//$objSheet->getCell('H1')->setValue('SD'); 
	$objSheet->getCell('H1')->setValue('OT_1'); 
	$objSheet->getCell('I1')->setValue('OT_2'); 
	$objSheet->getCell('J1')->setValue('OT_2A'); 
	$objSheet->getCell('K1')->setValue('OT_3'); 
	$objSheet->getCell('L1')->setValue('OT_3A'); 
	$objSheet->getCell('M1')->setValue('OT_4'); 
	$objSheet->getCell('N1')->setValue('OT_4A'); 
	$objSheet->getCell('O1')->setValue('OT_5'); 
	$objSheet->getCell('P1')->setValue('OT_5A'); 
	$objSheet->getCell('Q1')->setValue('OT_6'); 
	$objSheet->getCell('R1')->setValue('OT_6A'); 
	$objSheet->getCell('S1')->setValue('ADJ_1'); 
	$objSheet->getCell('T1')->setValue('ADJ_2'); 
	
	$countEom = 2;
	$eom_record = $_POST['eom_record'];
	$ctr_end = count($eom_record['idnum']);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$countEom, $ctr_end,PHPExcel_Cell_DataType::TYPE_STRING);
		// $objPHPExcel->getActiveSheet()
		// ->setCellValueExplicit('A'.$countEom, '0'.$ctr_end,PHPExcel_Cell_DataType::TYPE_STRING);
		// $objPHPExcel->getActiveSheet()
		// ->setCellValueExplicit('B'.$countEom,$rowEN['SalaryType'],PHPExcel_Cell_DataType::TYPE_STRING);
		// $objSheet->getCell('C'.$countEom)->setValue($absent); 
		// $objSheet->getCell('D'.$countEom)->setValue($undertime); 
		// $objSheet->getCell('E'.$countEom)->setValue($nightDiff); 
		// $objSheet->getCell('F'.$countEom)->setValue($sl); 
		// $objSheet->getCell('G'.$countEom)->setValue($vl); 
		// $objSheet->getCell('H'.$countEom)->setValue($ot1); 
		// $objSheet->getCell('I'.$countEom)->setValue($ot2); 
		// $objSheet->getCell('J'.$countEom)->setValue($ot2a); 
		// $objSheet->getCell('K'.$countEom)->setValue($ot3); 
		// $objSheet->getCell('L'.$countEom)->setValue($ot3a); 
		// $objSheet->getCell('M'.$countEom)->setValue($ot4); 
		// $objSheet->getCell('N'.$countEom)->setValue($ot4a); 
		// $objSheet->getCell('O'.$countEom)->setValue($ot5); 
		// $objSheet->getCell('P'.$countEom)->setValue($ot5a); 
		// $objSheet->getCell('Q'.$countEom)->setValue($ot6); 
		// $objSheet->getCell('R'.$countEom)->setValue($ot6a); 
		// $objSheet->getCell('S'.$countEom)->setValue("0"); 
		// $objSheet->getCell('T'.$countEom)->setValue("0"); 
		// $countEom++;

	
	
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=EOM'.$dateTodayName.'.xlsx');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	
?>
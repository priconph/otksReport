<?php
	require('PHPExcel.php');
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
	$dateFrom = $_GET['startDate'];
	$dateTo = $_GET['endDate'];
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
		
	$selectEOMEmpNo = OTKSR::getInstance()->getEOMEmpNo($dateFrom, $dateTo);							
	while($rowEN = mysqli_fetch_array($selectEOMEmpNo)) {
		$selectEOMAbsent = OTKSR::getInstance() -> getEOMAbsent($rowEN['empno'], $dateFrom, $dateTo);
		$absent = 0;
		while($rowAbsent = mysqli_fetch_array($selectEOMAbsent)) {
			$absent += $rowAbsent['absent'];
		}
		$selectEOMUndertimeLate = OTKSR::getInstance() -> getEOMUnderTimeLate($rowEN['empno'], $dateFrom, $dateTo);
		$undertime = 0;
		while($rowUndertimeLate = mysqli_fetch_array($selectEOMUndertimeLate)) {
			$undertime += $rowUndertimeLate['under_mins'] + $rowUndertimeLate['late_mins'];
		}
		$selectEOMNightDiff = OTKSR::getInstance() -> getEOMNightDiff($rowEN['empno'], $dateFrom, $dateTo);
		$nightDiff = 0;
		while($rowNightDiff = mysqli_fetch_array($selectEOMNightDiff)) {
			$nightDiff += $rowNightDiff['nightdiff'];
		}
		$selectEOMSL = OTKSR::getInstance() -> getEOMSL($rowEN['empno'], $dateFrom, $dateTo);
		$sl = 0;
		while($rowSL = mysqli_fetch_array($selectEOMSL)) {
			if ($rowSL['leavenum'] != 0){
				if($rowSL['leavecode'] == 1){
					$sl += $rowSL['leavenum'];
				}
			}
		}
		$selectEOMVL = OTKSR::getInstance() -> getEOMVL($rowEN['empno'], $dateFrom, $dateTo);
		$vl = 0;
		while($rowVL = mysqli_fetch_array($selectEOMVL)) {
			if ($rowVL['leavenum'] != 0){
				if($rowVL['leavecode'] == 2 or $row['leavecode'] == 3 or $row['leavecode'] == 4 or $row['leavecode'] == 5 or $row['leavecode'] == 6 ){
					$vl += $rowVL['leavenum'];
				}
			}
		}
		$selectEOMOT1 = OTKSR::getInstance() -> getEOMOT1($rowEN['empno'], $dateFrom, $dateTo);
		$ot1 = 0;
		while($rowOT1 = mysqli_fetch_array($selectEOMOT1)) {
			$ot1 += $rowOT1['ot1'];
		}
		$selectEOMOT2 = OTKSR::getInstance() -> getEOMOT2($rowEN['empno'], $dateFrom, $dateTo);
		$ot2 = 0;
		while($rowOT2 = mysqli_fetch_array($selectEOMOT2)) {
			$ot2 += $rowOT2['ot2'];
		}
		$selectEOMOT2a = OTKSR::getInstance() -> getEOMOT2a($rowEN['empno'], $dateFrom, $dateTo);
		$ot2a = 0;
		while($rowOT2a = mysqli_fetch_array($selectEOMOT2a)) {
			$ot2a += $rowOT2a['ot2a'];
		}
		$selectEOMOT3 = OTKSR::getInstance() -> getEOMOT3($rowEN['empno'], $dateFrom, $dateTo);
		$ot3 = 0;
		while($rowOT3 = mysqli_fetch_array($selectEOMOT3)) {
			$ot3 += $rowOT3['ot3'];
		}
		$selectEOMOT3a = OTKSR::getInstance() -> getEOMOT3a($rowEN['empno'], $dateFrom, $dateTo);
		$ot3a = 0;
		while($rowOT3a = mysqli_fetch_array($selectEOMOT3a)) {
			$ot3a += $rowOT3a['ot3a'];
		}
		$selectEOMOT4 = OTKSR::getInstance() -> getEOMOT4($rowEN['empno'], $dateFrom, $dateTo);
		$ot4 = 0;
		while($rowOT4 = mysqli_fetch_array($selectEOMOT4)) {
			$ot4 += $rowOT4['ot4'];
		}
		$selectEOMOT4a = OTKSR::getInstance() -> getEOMOT4a($rowEN['empno'], $dateFrom, $dateTo);
		$ot4a = 0;
		while($rowOT4a = mysqli_fetch_array($selectEOMOT4a)) {
			$ot4a += $rowOT['ot4a'];
		}
		$selectEOMOT5 = OTKSR::getInstance() -> getEOMOT5($rowEN['empno'], $dateFrom, $dateTo);
		$ot5 = 0;
		while($rowOT5 = mysqli_fetch_array($selectEOMOT5)) {
			$ot5 += $rowOT5['ot5'];
		}
		$selectEOMOT5a = OTKSR::getInstance() -> getEOMOT5a($rowEN['empno'], $dateFrom, $dateTo);
		$ot5a = 0;
		while($rowOT5a = mysqli_fetch_array($selectEOMOT5a)) {
			$ot5a += $rowOT5a['ot5a'];
		}
		$selectEOMOT6 = OTKSR::getInstance() -> getEOMOT6($rowEN['empno'], $dateFrom, $dateTo);
		$ot6 = 0;
		while($rowOT6 = mysqli_fetch_array($selectEOMOT6)) {
			$ot6 += $rowOT6['ot6'];
		}
		$selectEOMOT6a = OTKSR::getInstance() -> getEOMOT6a($rowEN['empno'], $dateFrom, $dateTo);
		$ot6a = 0;
		while($rowOT6a = mysqli_fetch_array($selectEOMOT6a)) {
			$ot6a += $rowOT6a['ot6a'];
		}
		
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$countEom, '0'.$rowEN['empno'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('B'.$countEom,$rowEN['SalaryType'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('C'.$countEom,$absent,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('D'.$countEom,$undertime,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('E'.$countEom,$nightDiff,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('F'.$countEom,$sl,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('G'.$countEom,$vl,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('H'.$countEom,$ot1,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('I'.$countEom,$ot2,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('J'.$countEom,$ot2a,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('K'.$countEom,$ot3,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('L'.$countEom,$ot3a,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('M'.$countEom,$ot4,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('N'.$countEom,$ot4a,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('O'.$countEom,$ot5,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('P'.$countEom,$ot5a,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('Q'.$countEom,$ot6,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('R'.$countEom,$ot6a,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('S'.$countEom,'0',PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('T'.$countEom,'0',PHPExcel_Cell_DataType::TYPE_STRING);
		
		/*$objSheet->getCell('C'.$countEom)->setValue($absent); 
		$objSheet->getCell('D'.$countEom)->setValue($undertime); 
		$objSheet->getCell('E'.$countEom)->setValue($nightDiff); 
		$objSheet->getCell('F'.$countEom)->setValue($sl); 
		$objSheet->getCell('G'.$countEom)->setValue($vl); 
		//$objSheet->getCell('H'.$countEom)->setValue("0"); 
		$objSheet->getCell('H'.$countEom)->setValue($ot1); 
		$objSheet->getCell('I'.$countEom)->setValue($ot2); 
		$objSheet->getCell('J'.$countEom)->setValue($ot2a); 
		$objSheet->getCell('K'.$countEom)->setValue($ot3); 
		$objSheet->getCell('L'.$countEom)->setValue($ot3a); 
		$objSheet->getCell('M'.$countEom)->setValue($ot4); 
		$objSheet->getCell('N'.$countEom)->setValue($ot4a); 
		$objSheet->getCell('O'.$countEom)->setValue($ot5); 
		$objSheet->getCell('P'.$countEom)->setValue($ot5a); 
		$objSheet->getCell('Q'.$countEom)->setValue($ot6); 
		$objSheet->getCell('R'.$countEom)->setValue($ot6a); 
		$objSheet->getCell('S'.$countEom)->setValue("0"); 
		$objSheet->getCell('T'.$countEom)->setValue("0"); */
		
		$countEom++;
	}
	
	
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=EOM'.$dateTodayName.'.xls');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
?>
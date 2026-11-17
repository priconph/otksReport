<?php
ob_start(); // keep stray output/warnings out of the binary download
ini_set('display_errors', '0');
ini_set('log_errors', '1');
	require_once __DIR__ . '/../media/vendor/autoload.php'; // phpoffice/phpspreadsheet (replaces legacy PHPExcel)
	use PhpOffice\PhpSpreadsheet\Cell\DataType;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xls;
	require_once __DIR__ . '/otksr-oop.php';
	if (session_status() === PHP_SESSION_NONE) { session_start(); }
	if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			//$username = 'arlbena';
		}
	//$ControlNumber = $_GET['ControlNumber'];
	$dateToday = date("M. d, Y");
	$dateTodayName = date("Ymd");
	$dateFrom = $_GET['startDate'] ?? '';
	$dateTo = $_GET['endDate'] ?? '';
	//create new phpexcel page
	$objPHPExcel = new Spreadsheet();
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	//set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
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
		$sl=0;
		$vl=0;
		/* late file */
		$late_file_eom_all = OTKSR::getInstance() -> getEom_late_file($rowEN['empno'], $dateFrom, $dateTo);
		if($late_file_eom_all instanceof mysqli_result){
			while ( $late_file_row_all = mysqli_fetch_array($late_file_eom_all) ){
				if ($late_file_row_all['leavenum'] != 0){
					if($late_file_row_all['leavecode'] == 1){
						$sl += (float)$late_file_row_all['leavenum'];
					}
				}
				if ($late_file_row_all['leavenum'] != 0){
					if($late_file_row_all['leavecode'] == 2 || $late_file_row_all['leavecode'] == 3 || $late_file_row_all['leavecode'] == 4 || $late_file_row_all['leavecode'] == 5 || $late_file_row_all['leavecode'] == 6 ){
						$vl += (float)$late_file_row_all['leavenum'];
					}
				}
			}
		}

		$selectEOMAbsent = OTKSR::getInstance() -> getEOMAbsent($rowEN['empno'], $dateFrom, $dateTo);
		$absent = 0;
		while($rowAbsent = mysqli_fetch_array($selectEOMAbsent)) {
			$absent += (float)$rowAbsent['absent'];
		}
		$selectEOMUndertimeLate = OTKSR::getInstance() -> getEOMUnderTimeLate($rowEN['empno'], $dateFrom, $dateTo);
		$undertime = 0;
		while($rowUndertimeLate = mysqli_fetch_array($selectEOMUndertimeLate)) {
			// $undertime += $rowUndertimeLate['under_mins'] + $rowUndertimeLate['late_mins'];
			$undertime += (float)$rowUndertimeLate['under_conv'] + (float)$rowUndertimeLate['late_conv'];
		}
		$selectEOMNightDiff = OTKSR::getInstance() -> getEOMNightDiff($rowEN['empno'], $dateFrom, $dateTo);
		$nightDiff = 0;
		while($rowNightDiff = mysqli_fetch_array($selectEOMNightDiff)) {
			$nightDiff += (float)$rowNightDiff['nightdiff'];
		}
		$selectEOMSL = OTKSR::getInstance() -> getEOMSL($rowEN['empno'], $dateFrom, $dateTo);
		// $sl = 0;
		while($rowSL = mysqli_fetch_array($selectEOMSL)) {
			if ($rowSL['leavenum'] != 0){
				if($rowSL['leavecode'] == 1){
					$sl += number_format($rowSL['leavenum'], 2, '.', '');
				}
			}
		}
		$selectEOMVL = OTKSR::getInstance() -> getEOMVL($rowEN['empno'], $dateFrom, $dateTo);
		// $vl = 0;
		while($rowVL = mysqli_fetch_array($selectEOMVL)) {
			if ($rowVL['leavenum'] != 0){
				if($rowVL['leavecode'] == 2 or $rowVL['leavecode'] == 3 or $rowVL['leavecode'] == 4 or $rowVL['leavecode'] == 5 or $rowVL['leavecode'] == 6 ){
					$vl += number_format($rowVL['leavenum'], 2, '.', '');
				}
			}
		}
		$selectEOMOT1 = OTKSR::getInstance() -> getEOMOT1($rowEN['empno'], $dateFrom, $dateTo);
		$ot1 = 0;
		while($rowOT1 = mysqli_fetch_array($selectEOMOT1)) {
			$ot1 += (float)$rowOT1['ot1'];
		}
		$selectEOMOT2 = OTKSR::getInstance() -> getEOMOT2($rowEN['empno'], $dateFrom, $dateTo);
		$ot2 = 0;
		while($rowOT2 = mysqli_fetch_array($selectEOMOT2)) {
			$ot2 += (float)$rowOT2['ot2'];
		}
		$selectEOMOT2a = OTKSR::getInstance() -> getEOMOT2a($rowEN['empno'], $dateFrom, $dateTo);
		$ot2a = 0;
		while($rowOT2a = mysqli_fetch_array($selectEOMOT2a)) {
			$ot2a += (float)$rowOT2a['ot2a'];
		}
		$selectEOMOT3 = OTKSR::getInstance() -> getEOMOT3($rowEN['empno'], $dateFrom, $dateTo);
		$ot3 = 0;
		while($rowOT3 = mysqli_fetch_array($selectEOMOT3)) {
			$ot3 += (float)$rowOT3['ot3'];
		}
		$selectEOMOT3a = OTKSR::getInstance() -> getEOMOT3a($rowEN['empno'], $dateFrom, $dateTo);
		$ot3a = 0;
		while($rowOT3a = mysqli_fetch_array($selectEOMOT3a)) {
			$ot3a += (float)$rowOT3a['ot3a'];
		}
		$selectEOMOT4 = OTKSR::getInstance() -> getEOMOT4($rowEN['empno'], $dateFrom, $dateTo);
		$ot4 = 0;
		while($rowOT4 = mysqli_fetch_array($selectEOMOT4)) {
			$ot4 += (float)$rowOT4['ot4'];
		}
		$selectEOMOT4a = OTKSR::getInstance() -> getEOMOT4a($rowEN['empno'], $dateFrom, $dateTo);
		$ot4a = 0;
		while($rowOT4a = mysqli_fetch_array($selectEOMOT4a)) {
			$ot4a += (float)$rowOT['ot4a'];
		}
		$selectEOMOT5 = OTKSR::getInstance() -> getEOMOT5($rowEN['empno'], $dateFrom, $dateTo);
		$ot5 = 0;
		while($rowOT5 = mysqli_fetch_array($selectEOMOT5)) {
			$ot5 += (float)$rowOT5['ot5'];
		}
		$selectEOMOT5a = OTKSR::getInstance() -> getEOMOT5a($rowEN['empno'], $dateFrom, $dateTo);
		$ot5a = 0;
		while($rowOT5a = mysqli_fetch_array($selectEOMOT5a)) {
			$ot5a += (float)$rowOT5a['ot5a'];
		}
		$selectEOMOT6 = OTKSR::getInstance() -> getEOMOT6($rowEN['empno'], $dateFrom, $dateTo);
		$ot6 = 0;
		while($rowOT6 = mysqli_fetch_array($selectEOMOT6)) {
			$ot6 += (float)$rowOT6['ot6'];
		}
		$selectEOMOT6a = OTKSR::getInstance() -> getEOMOT6a($rowEN['empno'], $dateFrom, $dateTo);
		$ot6a = 0;
		while($rowOT6a = mysqli_fetch_array($selectEOMOT6a)) {
			$ot6a += (float)$rowOT6a['ot6a'];
		}
		
		/* late file ot */
		$late_file_ot_all = OTKSR::getInstance() -> getEom_late_file_ot($rowEN['empno'], $dateFrom, $dateTo);
		if(is_array($late_file_ot_all)){
			for( $x=0; $x<count($late_file_ot_all['empno']); $x++ ){
				if( $late_file_ot_all['ot_type'][$x] == 1){
					$ot1 = $ot1 + $late_file_ot_all['ot_count'][$x];
				}
				if($late_file_ot_all['ot_type'][$x] == 2){
					$ot2 = $ot2 + $late_file_ot_all['ot_count'][$x];
					if($late_file_ot_all['ot_count'][$x] > 8){
						$excess = $late_file_ot_all['ot_count'][$x] - 8;
						$ot2 = $ot2 - $excess;
						$ot2a += $excess;
					}
				}
				if($late_file_ot_all['ot_type'][$x] == 3){
					$ot3 = $ot3 + $late_file_ot_all['ot_count'][$x];
					if($late_file_ot_all['ot_count'][$x] > 8){
						$excess = $late_file_ot_all['ot_count'][$x] - 8;
						$ot3 = $ot3 - $excess;
						$ot3a += $excess;
					}
				}
				if($late_file_ot_all['ot_type'][$x] == 4){
					$ot4 = $ot4 + $late_file_ot_all['ot_count'][$x];
					if($late_file_ot_all['ot_count'][$x] > 8){
						$excess = $late_file_ot_all['ot_count'][$x] - 8;
						$ot4 = $ot4 - $excess;
						$ot4a += $excess;
					}
				}
				if($late_file_ot_all['ot_type'][$x] == 5){
					$ot5 = $ot5 + $late_file_ot_all['ot_count'][$x];
					if($late_file_ot_all['ot_count'][$x] > 8){
						$excess = $late_file_ot_all['ot_count'][$x] - 8;
						$ot5 = $ot5 - $excess;
						$ot5a += $excess;
					}
				}
				if($late_file_ot_all['ot_type'][$x] == 6){
					$ot6 = $ot6 + $late_file_ot_all['ot_count'][$x];
					if($late_file_ot_all['ot_count'][$x] > 8){
						$excess = $late_file_ot_all['ot_count'][$x] - 8;
						$ot6 = $ot6 - $excess;
						$ot6a += $excess;
					}
				}
			}
		}
					
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$countEom, '0'.$rowEN['empno'],DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('B'.$countEom,$rowEN['SalaryType'],DataType::TYPE_STRING);
		$objSheet->getCell('C'.$countEom)->setValue($absent); 
		$objSheet->getCell('D'.$countEom)->setValue($undertime); 
		$objSheet->getCell('E'.$countEom)->setValue($nightDiff); 
		$objSheet->getCell('F'.$countEom)->setValue($sl); 
		$objSheet->getCell('G'.$countEom)->setValue($vl);
		// $objSheet->getCell('H'.$countEom)->setValue("0");
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
		$objSheet->getCell('T'.$countEom)->setValue("0"); 
		
		$countEom++;
	}
	$objSheet->getStyle('D2:T'.($countEom-1) )->getNumberFormat()->setFormatCode('#,##0.00');
	
	unset($styleArray);
	while (ob_get_level() > 0) { ob_end_clean(); }
	header('Content-Type: application/vnd.ms-excel');
	// header('Content-Disposition: attachment;filename=EOM'.$dateTodayName.'.xlsx');
	header('Content-Disposition: attachment;filename=EOM'.$dateTodayName.'.xls'); // used for excel 2003 format
	header('Cache-Control: max-age=0');
	
	$objWriter = new Xls($objPHPExcel); // used for excel 2003 format
	$objWriter->save('php://output');
?>
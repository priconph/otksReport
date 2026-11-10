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
	$dateFrom = $_GET['startDate'];
	$dateTo = $_GET['endDate'];
	//create new phpexcel page
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Lucida Fax');
	//set font
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
	$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
	$objSheet = $objPHPExcel->getActiveSheet();
	$title = "TFM"; //set title cont.
	$objSheet->setTitle($title); //set title cont.
	
	$objSheet->getCell('A1')->setValue('C_DATE'); 
	$objSheet->getCell('B1')->setValue('IDNUM'); 
	$objSheet->getCell('C1')->setValue('EMP_CODE'); 
	$objSheet->getCell('D1')->setValue('T_IN'); 
	$objSheet->getCell('E1')->setValue('T_OUT'); 
	$objSheet->getCell('F1')->setValue('T_IN_OT'); 
	$objSheet->getCell('G1')->setValue('T_OUT_OT'); 
	$objSheet->getCell('H1')->setValue('REG_HOUR'); 
	$objSheet->getCell('I1')->setValue('OT_HOUR'); 
	$objSheet->getCell('J1')->setValue('ABSENT');
	$objSheet->getCell('K1')->setValue('LATE_UNT'); 
	$objSheet->getCell('L1')->setValue('SL_VL_CODE'); 
	$objSheet->getCell('M1')->setValue('SL_VL_NUM'); 
	$objSheet->getCell('N1')->setValue('OT_1'); 
	$objSheet->getCell('O1')->setValue('OT_2'); 
	$objSheet->getCell('P1')->setValue('OT_2A'); 
	$objSheet->getCell('Q1')->setValue('OT_3'); 
	$objSheet->getCell('R1')->setValue('OT_3A'); 
	$objSheet->getCell('S1')->setValue('OT_4'); 
	$objSheet->getCell('T1')->setValue('OT_4A');
	$objSheet->getCell('U1')->setValue('OT_5');
	$objSheet->getCell('V1')->setValue('OT_5A');
	$objSheet->getCell('W1')->setValue('OT_6');
	$objSheet->getCell('X1')->setValue('OT_6A');
	$objSheet->getCell('Y1')->setValue('N_DIFF');
	$objSheet->getCell('Z1')->setValue('AUT');
	
	
	$count_tfm = 2;
	$late_file_leave = OTKSR::getInstance() -> getEOMEmpNo($dateFrom, $dateTo);


	while($row_1 = mysqli_fetch_array($late_file_leave)){
		$empno 		= $row_1['empno'];
		$SalaryType = $row_1['SalaryType'];

		/* late leave */
		$late_file_eom_all = OTKSR::getInstance() -> getEom_late_file($empno, $dateFrom, $dateTo);
		// $late_file_eom_all = OTKSR::getInstance()->getEom_late_file('4326', $dateFrom, $dateTo);

		if($late_file_eom_all === 0){
			//do nothing
	
		} else {
			// echo json_encode(mysqli_fetch_assoc($late_file_eom_all));
			// exit();
			while( $row = mysqli_fetch_array($late_file_eom_all) ){
				
				$C_DATE = date('m/d/Y', strtotime($row['date']));
				$objPHPExcel->getActiveSheet()
				->setCellValueExplicit('A'.$count_tfm,$C_DATE,PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()
				->setCellValueExplicit('B'.$count_tfm, '0'.$empno,PHPExcel_Cell_DataType::TYPE_STRING);
				$objSheet->getCell('C'.$count_tfm)->setValue($SalaryType); 
				$leavecode = OTKSR::getInstance()->leavecode_convertion($row['leavecode']);
				$objSheet->getCell('D'.$count_tfm)->setValue($leavecode); 
				$objSheet->getCell('E'.$count_tfm)->setValue(''); 
				$objSheet->getCell('F'.$count_tfm)->setValue(''); 
				$objSheet->getCell('G'.$count_tfm)->setValue('');
				$objSheet->getCell('H'.$count_tfm)->setValue(''); 
				$objSheet->getCell('I'.$count_tfm)->setValue(''); 
				$objSheet->getCell('J'.$count_tfm)->setValue(''); 
				$objSheet->getCell('K'.$count_tfm)->setValue(''); 
				$objSheet->getCell('L'.$count_tfm)->setValue($leavecode);
				$objSheet->getCell('M'.$count_tfm)->setValue($row['leavenum']); 
				$objSheet->getCell('N'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('O'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('P'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('Q'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('R'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('S'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('T'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('U'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('V'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('W'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('X'.$count_tfm)->setValue('0.00'); 
				$objSheet->getCell('Y'.$count_tfm)->setValue('0.00');
				$objSheet->getCell('Z'.$count_tfm)->setValue('FALSE');
				$count_tfm++;
			}


		}
		$late_file_ot_all = OTKSR::getInstance() -> getEom_late_file_ot($empno, $dateFrom, $dateTo);
		if($late_file_ot_all != 0 || $late_file_ot_all != "0"){			
			for( $x=0; $x<count($late_file_ot_all['empno']); $x++ ){
				$ot1 	= 0;
				$ot2 	= 0;
				$ot2a 	= 0;
				$ot3	= 0;
				$ot3a 	= 0;
				$ot4 	= 0;
				$ot4a 	= 0;
				$ot5 	= 0;
				$ot5a 	= 0;
				$ot6 	= 0;
				$ot6a 	= 0;
			
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
				
				$C_DATE = date('m/d/Y', strtotime($late_file_ot_all['ot_date'][$x]));
				$objPHPExcel->getActiveSheet()
				->setCellValueExplicit('A'.$count_tfm,$C_DATE,PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()
				->setCellValueExplicit('B'.$count_tfm, '0'.$empno,PHPExcel_Cell_DataType::TYPE_STRING);
				$objSheet->getCell('C'.$count_tfm)->setValue($SalaryType); 
				$objSheet->getCell('D'.$count_tfm)->setValue(''); 
				$objSheet->getCell('E'.$count_tfm)->setValue(''); 
				$objSheet->getCell('F'.$count_tfm)->setValue(''); 
				$objSheet->getCell('G'.$count_tfm)->setValue('');
				$objSheet->getCell('H'.$count_tfm)->setValue(''); 
				$objSheet->getCell('I'.$count_tfm)->setValue(''); 
				$objSheet->getCell('J'.$count_tfm)->setValue(''); 
				$objSheet->getCell('K'.$count_tfm)->setValue(''); 
				$objSheet->getCell('L'.$count_tfm)->setValue('');
				$objSheet->getCell('M'.$count_tfm)->setValue('0.00');
				$objSheet->getCell('N'.$count_tfm)->setValue($ot1); 
				$objSheet->getCell('O'.$count_tfm)->setValue($ot2); 
				$objSheet->getCell('P'.$count_tfm)->setValue($ot2a); 
				$objSheet->getCell('Q'.$count_tfm)->setValue($ot3); 
				$objSheet->getCell('R'.$count_tfm)->setValue($ot3a); 
				$objSheet->getCell('S'.$count_tfm)->setValue($ot4); 
				$objSheet->getCell('T'.$count_tfm)->setValue($ot4a); 
				$objSheet->getCell('U'.$count_tfm)->setValue($ot5); 
				$objSheet->getCell('V'.$count_tfm)->setValue($ot5a); 
				$objSheet->getCell('W'.$count_tfm)->setValue($ot6); 
				$objSheet->getCell('X'.$count_tfm)->setValue($ot6a); 
				$objSheet->getCell('Y'.$count_tfm)->setValue('0.00');
				$objSheet->getCell('Z'.$count_tfm)->setValue('FALSE');
				$count_tfm++;
			}
		}
	}
	/* normal records */
	$result = OTKSR::getInstance()->export_tfm($dateFrom,$dateTo);	
	while( $row = mysqli_fetch_array($result) ){
		$C_DATE = date('m/d/Y', strtotime($row['C_DATE']));
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('A'.$count_tfm,$C_DATE,PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()
		->setCellValueExplicit('B'.$count_tfm, '0'.$row['IDNUM'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objSheet->getCell('C'.$count_tfm)->setValue($row['EMP_CODE']); 
		$objSheet->getCell('D'.$count_tfm)->setValue($row['T_IN']); 
		$objSheet->getCell('E'.$count_tfm)->setValue($row['T_OUT']); 
		$T_IN_OUT = '';
		$STR_OUT = '';
		if($row['T_IN'] == '' || $row['T_OUT'] == '' || $row['DATE_OUT'] == ''){ 
			$T_IN_OUT = 'inc';
		}else{
			$date_in 	= strtotime($row['C_DATE'].' '.$row['T_IN']);
			$date_out 	= strtotime($row['DATE_OUT'].' '.$row['T_OUT']);
			$diff 		= $date_out - $date_in;
			$one_hour   = 60*60;
			$diff = $diff / $one_hour;
			if($diff > 16){
				$T_IN_OUT = 'str';
			}
		}	
		if($row['T_IN'] == 'do' || $row['T_IN'] == 'yec' || 
				$row['T_IN'] == 'SL' ||	$row['T_IN'] == 'VL' || 
				$row['T_IN'] == 'BDL' || $row['T_IN'] == 'ML' || 
				$row['T_IN'] == 'FL' || $row['T_IN'] == 'BL'){
			$T_IN_OUT = '';
		}
		if($row['MODE_IN'] == 'OB'){
		  $T_IN_OUT = 'ob';
		}
		if($row['MODE_IN'] == 'BUS LATE'){
		  $T_IN_OUT = 'bl';
		}
		if($row['LATE_UNT'] > '0'){
		  $T_IN_OUT = 'unt';
		}
		if($row['T_IN'] == 'do'){
		  $T_IN_OUT = 'off';
		}
		
		$objSheet->getCell('F'.$count_tfm)->setValue($T_IN_OUT); 
		$objSheet->getCell('G'.$count_tfm)->setValue($row['T_OUT_OT']);
		$objSheet->getCell('H'.$count_tfm)->setValue($row['REG_HOUR']); 
		$objSheet->getCell('I'.$count_tfm)->setValue($row['OT_HOUR']); 
		$objSheet->getCell('J'.$count_tfm)->setValue($row['ABSENT']); 
		$objSheet->getCell('K'.$count_tfm)->setValue($row['LATE_UNT']); 
		$leavecode = OTKSR::getInstance()->leavecode_convertion($row['SL_VL_CODE']);
		$objSheet->getCell('L'.$count_tfm)->setValue($leavecode);
		$objSheet->getCell('M'.$count_tfm)->setValue($row['SL_VL_NUM']); 
		$objSheet->getCell('N'.$count_tfm)->setValue($row['OT_1']); 
		$objSheet->getCell('O'.$count_tfm)->setValue($row['OT_2']); 
		$objSheet->getCell('P'.$count_tfm)->setValue($row['OT_2A']); 
		$objSheet->getCell('Q'.$count_tfm)->setValue($row['OT_3']); 
		$objSheet->getCell('R'.$count_tfm)->setValue($row['OT_3A']); 
		$objSheet->getCell('S'.$count_tfm)->setValue($row['OT_4']); 
		$objSheet->getCell('T'.$count_tfm)->setValue($row['OT_4A']); 
		$objSheet->getCell('U'.$count_tfm)->setValue($row['OT_5']); 
		$objSheet->getCell('V'.$count_tfm)->setValue($row['OT_5A']); 
		$objSheet->getCell('W'.$count_tfm)->setValue($row['OT_6']); 
		$objSheet->getCell('X'.$count_tfm)->setValue($row['OT_6A']); 
		$objSheet->getCell('Y'.$count_tfm)->setValue($row['N_DIFF']); 
		$objSheet->getCell('Z'.$count_tfm)->setValue($row['AUT']);
		$count_tfm++;
	}
	// for($x=2;$x<=$count_tfm;$x++){
		$objSheet->getStyle('M2:Y'.($count_tfm-1) )->getNumberFormat()->setFormatCode('#,##0.00');
	// }
	unset($styleArray);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// header('Content-Disposition: attachment;filename=EOM'.$dateTodayName.'.xlsx');
	header('Content-Disposition: attachment;filename=TFM'.$dateTodayName.'.xls'); // used for excel 2003 format
	header('Cache-Control: max-age=0');
	
	// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // used for excel 2003 format
	$objWriter->save('php://output');
?>
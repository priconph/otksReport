<?php
$excel_class = 'class/excel.php';
require_once('otksr-oop.php');

if(file_exists($excel_class)){
	include($excel_class);
}else{
	echo 'File '.$excel_class.' does not exist';
	exit;
}

$month_year = $_GET['ms'];
$month_name = date('F', strtotime($month_year));
$year 		= date('Y', strtotime($month_year));
$year_mos   = date('Y-m-', strtotime($month_year));

$excel = new EXCEL;
/* set title */
$excel->title = 'Sheet1';
/* add a new page */
$excel->add_page();
/* use the default font style */
$excel->set_default_font_style();
/* select an active page */
$excel->set_active_sheet(0);

$width_allowance = .81;

/* Format settings */
$header_format = array(
	'bold' 		  => true,
	'h_alignment' => 'center'	
);
$content_format_center = array(
	'h_alignment' => 'center'
);
$content_format_left = array(
	'h_alignment' => 'left'	
);
$division_format = array(
	'bold' 		  => true,
	'h_alignment' => 'left'	
);

$sheet_index = 0;
$result_div = OTKSR::getInstance()->load_division_list();
	while($row_div = mysqli_fetch_array($result_div)) {
		/* Set sheet and title */
		$div_pkid = $row_div['pkid'];
		$division = $row_div['Division'];
		$excel->title = $division;
		$excel->add_sheet($sheet_index);
		$sheet_index++;
		
		/* Set excel width */
		$array_cell = array('A','B','C','D','E','F','G','H');
		$array_width = array('10','4','18','4','13','13','13','13');
		$excel->set_width_array($array_cell,$array_width);
		
		/* Merged cells */
		$excel->merge_cells('A1:H1');
		$excel->merge_cells('A2:H2');
		$excel->merge_cells('A3:H3');
		$excel->merge_cells('A5:B5');
		$excel->merge_cells('C5:D5');

		/* Add header values */
		// $excel->place_value('A1','PRICON MICROELECTRONIC, INC.','string','',true);
		// $excel->place_value('A2','LIST OF TARDINESS/UNDERTIME REPORT','string','',true);
		// $excel->place_value('A3','For the Month of '.$month_name.' '.$year,'string','',true);
		// $excel->place_value('A5','EMP NO.','string','',true);
		// $excel->place_value('C5','EMPLOYEE NAME','string','',true);
		// $excel->place_value('E5','DATE','string','',true);
		// $excel->place_value('F5','TIME IN','string','',true);
		// $excel->place_value('G5','TIME OUT','string','',true);
		// $excel->place_value('H5','LATE/UND.','string','',true);		
		
		// $excel->place_value('A7',$row_div['Division'],'string','',true);
		// $excel->set_format('A7',$division_format);
		
		/* Set header format */
		// $excel->set_format('A1',$header_format);
		// $excel->set_format('A2',$header_format);
		// $excel->set_format('A3',$header_format);
		// $excel->set_format('A5:L5',$header_format);
		
		$row_ctr = 8;
		$result = OTKSR::getInstance()->load_record_with_tardi($year_mos,$div_pkid);
		$gtotal_late_tardi = 0;
		$empno_latest = '';
		$max_row = $result->num_rows;
		$record_ctr = 0;
		while($row = mysqli_fetch_array($result)) {
			$late_mins 			= OTKSR::getInstance()->getMinutesEquivalent($row['late_mins']);
			$under_mins 		= OTKSR::getInstance()->getMinutesEquivalent($row['under_mins']);
			$total_late_tardi 	= $late_mins + $under_mins;
			
			if($empno_latest == '') {
				$gtotal_late_tardi += $total_late_tardi;
			} else {
				if($empno_latest == $row['empno']) {
					$gtotal_late_tardi += $total_late_tardi;
				} else {
					$excel->place_value('G'.($row_ctr),'TOTAL','string','',true);
					$excel->place_value('H'.($row_ctr),$gtotal_late_tardi,'number','2',true);
					
					$gtotal_late_tardi = $total_late_tardi;
					$empno_latest = '';
					$row_ctr++;
					if( ($record_ctr+1) != ($max_row) ){
						$row_ctr++;
					}
				}
				
			}
			
			$excel->place_value('A'.$row_ctr,$row['empno'],'string','',true);
			$excel->place_value('C'.$row_ctr,$row['empname'],'string','',true);
			$excel->place_value('E'.$row_ctr,date('d-M-Y',strtotime($row['date_in'])),'string','',true);
			$excel->place_value('F'.$row_ctr,date('H:i',strtotime($row['time_in'])),'string','',true);
			$excel->place_value('G'.$row_ctr,date('H:i',strtotime($row['time_out'])),'string','',true);
			$excel->place_value('H'.$row_ctr,$total_late_tardi,'number','2',true);
				
			$excel->set_format('A'.$row_ctr,$content_format_center);
			$excel->set_format('C'.$row_ctr,$content_format_left);
			$excel->set_format('E'.$row_ctr,$content_format_center);
			$excel->set_format('F'.$row_ctr,$content_format_center);
			$excel->set_format('G'.$row_ctr,$content_format_center);
			$excel->set_format('H'.$row_ctr,$content_format_center);
			$empno_latest = $row['empno'];
			
			$row_ctr++;
			$record_ctr++;
			
			if( ($record_ctr+1) == ($max_row) ){
				$excel->place_value('G'.($row_ctr+1),'TOTAL','string','',true);
				$excel->place_value('H'.($row_ctr+1),$gtotal_late_tardi,'string','',true);
			}
			/* Add design to total */
			$excel->set_format('G'.$row_ctr,$header_format);
			$excel->set_format('H'.$row_ctr,$header_format);
			
		}	
		$excel->setheaderfooter($month_name,$year,$division,$division_format,$header_format);	
	}
// $i=0;
// while($i<3) {
	// $excel->createSheet($i);
	// $excel->title = $i;
    // $i++;
// }

/* set width - Array Cells, Array Width */
// $array_cell = array('A','B','C','E');
// $array_width = array('20','20','20','20');
// $excel->set_width($array_cell,$array_width);

/* set height - Array Rows, Array Height */
// $array_row = array('2','3');
// $array_height = array(25,50);
// $excel->set_height($array_row,$array_height);

/* borders - Cell, Left, Right, Top, Bottom */
// $excel->set_borders('A2',1,1,1,1);
// $excel->set_borders('B2:B3',1,1,1,1);

/* place value - cell, value, type(string or blanl), alignment, wordwrap(true or false) */
// $excel->place_value('A1','450121212121212121212121','string','',true);

/* set font - cell, bold, color, size, style */
// $excel->set_font('A1',true,'',14,'Verdana');

/* set file name */
$filename = "Tardiness ".$month_name.' '.$year;
/* output excel - filename, excel version (2003,2007) */
$excel->output($filename,'2003');


/* SELECT `tkdataraw`.`pkid` AS `pkid`,
       `tkdataraw`.`empno` AS `empno`,
       `db_hris`.`tbl_EmployeeInfo`.`SalaryType` AS `SalaryType`,
       CONCAT(`db_hris`.`tbl_EmployeeInfo`.`LastName`,', ',`db_hris`.`tbl_EmployeeInfo`.`FirstName`) AS `empname`,
       (SELECT `Division` FROM `db_hris`.`tbl_Division` `div` WHERE `div`.`pkid` = `db_hris`.`tbl_EmployeeInfo`.`fkDivision` LIMIT 0,1) AS `Division`,
       `tkdataraw`.`date` AS `date`,
       `tkdataraw`.`time_in` AS `time_in`,
       `tkdataraw`.`mode_in` AS `mode_in`,
       `tkdataraw`.`date_out` AS `date_out`,
       `tkdataraw`.`time_out` AS `time_out`,
       `tkdataraw`.`mode_out` AS `mode_out`,
       `tkdataraw`.`late_mins` AS `late_mins`,
       `tkdataraw`.`under_mins` AS `under_mins`,
       `tkdataraw`.`status` AS `status`
FROM (`tkdataraw`
      JOIN `db_hris`.`tbl_EmployeeInfo` on((`tkdataraw`.`empno` = `db_hris`.`tbl_EmployeeInfo`.`EmpNo`)))
WHERE `db_hris`.`tbl_EmployeeInfo`.`SalaryType` != 3 AND (`tkdataraw`.`late_mins` != 0 OR `tkdataraw`.`under_mins` != 0)
ORDER BY  `tkdataraw`.`empno`,`tkdataraw`.`date`
 */
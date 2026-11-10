<?php
/* date today */
$date_today = strtotime(date('Y-m-d H:i:s'));

/* assign date range */
$date_start = strtotime('2016-03-01');
$date_end 	= strtotime('2016-03-15');

if(check_date_between($date_today,$date_start,$date_end)){
	echo 'date is in between';
}else{
	echo 'date not in between';
}echo "<br>";

$array_date = array();
/* place all date in an array */
while ($date_start <= $date_end){
	$array_date[] = date('Y-m-d',$date_start);
	$date_start = strtotime('+1 day',$date_start);
}

$empno = 'J007';
$dayoff_status = check_day_off($empno);
$number_of_work_days = count_number_of_work_days($date_start,$date_end,$dayoff_status,$array_date);
echo "<br> Total Number of working days for $empno: ".$number_of_work_days;

/* Count the number of days on date range given */
function count_number_of_work_days($date_start,$date_end,$dayoff_status,$array_date){
	$number_of_work_days = 0;
	if($dayoff_status == 0){
		for($x=0;$x<count($array_date);$x++){
			if(date('D',strtotime($array_date[$x])) != 'Sun'){
				echo $array_date[$x]."<br>";
				$number_of_work_days ++;
			}
		}
	}else{
		/* Loop through all date and count date */
		for($x=0;$x<count($array_date);$x++){
			if(date('D',strtotime($array_date[$x])) != 'Sun'){
				if(date('D',strtotime($array_date[$x])) == 'Sat'){
					$dayoff = check_dayoff_saturday($array_date[$x]);
					if($dayoff == 1){
						continue;
					}
				}
				echo $array_date[$x]."<br>";
				$number_of_work_days ++;
			}
		}
	}
	return $number_of_work_days;
}

/* Deduct set holidays to the number of work days */
function deduct_holidays($number_of_work_days){
	/* Get all assigned holidays using a query */
}

/* Check if employee has a dayoff */
function check_day_off($empno){
	$dayoff_type = 1;
	return $dayoff_type;
}

/* Check if saturday is a dayoff */
function check_dayoff_saturday($date){
	$date = $date;
	$first_sat = date("Y-m-d", strtotime('first saturday of this month'));
	$third_sat = date("Y-m-d", strtotime('third saturday of this month'));
	$fifth_sat = date("Y-m-d", strtotime('fifth saturday of this month'));

	if($date == $first_sat || $date == $third_sat || $date == $fifth_sat){
		return 0;
	}else{
		return 1;
	}
}

/* formats should be in strtotime */
function check_date_between($date,$date_start,$date_end){
	if($date >= $date_start && $date <= $date_end){
		return true;
	}else{
		return false;
	}
}

// $query = "SELECT DISTINCT(`empno`) AS `EmpNo` FROM `db_hris`.`tbl_EmployeeInfo` WHERE `SalaryType` != '3' AND (`EmpStatus` = '1' OR `EmpStatus` = '3') ORDER BY `empno`";
// $result = $this->query($query);
// $emloyee_work_days = array();
// while($row = mysqli_fetch_array($result)){
	// $dayoff_status = $this->check_day_off($row['EmpNo']);
	// $array_date = $this->array_date($start_date,$end_date);
	// $number_of_work_days = $this->count_number_of_work_days($start_date,$end_date,$dayoff_status,$array_date);
	// /* get tklogs data count */
	// $tklog_rec_cnt = $this->count_tklogs_data($row['EmpNo'],$start_date,$end_date);
	// $emloyee_work_days[] = $row['EmpNo'] . ' nowd: ' . ($number_of_work_days * 2) .' tklogs: '. $tklog_rec_cnt;
// }

<?php
	if(is_ajax()) {
		if(isset($_POST["action"]) && !empty($_POST["action"])) {
			$action = $_POST["action"];
			switch($action) {
				//case "" : functionName(); break;
				case "updateRawData" 				: updateRawData(); break;
				case "loadEmpInfo" 					: loadEmpInfo(); break;
				case "addRawDataGuard" 				: addRawDataGuard(); break;
				case "addRawData" 					: addRawData(); break;
				case "deleteRawData" 				: deleteRawData(); break;
				case "delete_raw_data" 				: delete_raw_data(); break;
				case "updateDailyTFM" 				: updateDailyTFM(); break;
				case "deleteDailyTFM" 				: deleteDailyTFM(); break;
				case "updateSLVL" 					: updateSLVL(); break;
				case "deleteHolidayAbsent" 			: deleteHolidayAbsent(); break;
				case "approveAttendance" 			: approveAttendance(); break;
				case "recomputeTFM" 				: recomputeTFM(); break;
				case "compute_employee_tfm_data"	: compute_employee_tfm_data(); break;
				case "load_employee_information"	: load_employee_information(); break;
				case "count_unfiled_straight_hours" : count_unfiled_straight_hours(); break;
				case "fix_attendance_time_in" 		: fix_attendance_time_in(); break;
				case "fix_attendance_time_out" 		: fix_attendance_time_out(); break;
				case "fix_attendance_time_in_prev" 	: fix_attendance_time_in_prev(); break;
				case "fix_attendance_time_out_prev"	: fix_attendance_time_out_prev(); break;
				case "get_first_cut_off" 			: get_first_cut_off(); break;
				case "get_second_cut_off" 			: get_second_cut_off(); break;
				case "save_adjusted_time_in_out" 	: save_adjusted_time_in_out(); break;
				case "save_offset" 					: save_offset(); break;
				case "delete_offset" 				: delete_offset(); break;
				case "save_holiday" 				: save_holiday(); break;
				case "delete_holiday" 				: delete_holiday(); break;
				case "upload_attendance_csv" 		: upload_attendance_csv(); break;
				case "update_raw_data_from_csv" 	: update_raw_data_from_csv(); break;
				case "delete_leave" 				: delete_leave(); break;
				case "edit_leave_information" 		: edit_leave_information(); break;
				case "delete_employee_dayoff" 		: delete_employee_dayoff(); break;
				case "add_employee_dayoff" 			: add_employee_dayoff(); break;
				case "add_yec_training" 			: add_yec_training(); break;
				case "delete_yec_training" 			: delete_yec_training(); break;
				case "get_undertime_late" 			: get_undertime_late(); break;
				case "add_late_ot" 					: add_late_ot(); break;
				case "delete_late_ot" 				: delete_late_ot(); break;
				
				/* HRIS */
				case "load_all_empno"				: load_all_empno(); break;
				case "validate_existing_empno"		: validate_existing_empno(); break;
			}
		}
	}

	function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	function sample(){
		$return = array();
		$return['msg'] = 'test';
		echo json_encode($return);
	}
	
	function updateRawData() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> updateRawData($return['pkid'], $return['array_update'], $return['username']);
		$return['msg'] = $result['check'];
		$return['msg2'] = $result['tklogs_result'];
		$return["result"] = json_encode($result);		
		echo json_encode($return);
	}
	
	function loadEmpInfo() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> getEmpInfo(strtoupper($return['empno']));
		if($row = mysqli_fetch_array($result)) {
			$return['ECode'] = $row['SalaryType'];
			$return['name'] = $row['LastName'].", ". $row['FirstName'];
			$return['department'] = $row['Department'];
			$return['position'] = $row['Position'];
			$return['section'] = $row['Section'];
		}
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function addRawDataGuard() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> addRawDataGuard($return['empno'], $return['date'], $return['time_in'], $return['mode_in'], $return['date_out'], $return['time_out'], $return['mode_out'], $return['username'], $return['approver'], $return['status']);

		$existing=mysqli_num_rows($result);
		$return['msg']=$existing;
		
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function addRawData() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> addRawData($return['empno'], $return['date'], $return['time_in'], $return['mode_in'], $return['date_out'], $return['time_out'], $return['mode_out'], $return['username'], $return['approver'], $return['status']);

		$return['msg'] = $result['check'];
		$return['msg2'] = $result['tklogs_result'];
		// $return['test'] = $return['empno'] . ' ' . $return['date'] . ' ' . $return['time_in'] . ' ' . $return['mode_in'] . ' ' . $return['date_out'] . ' ' . $return['time_out'] . ' ' . $return['mode_out'] . ' ' . $return['username'] . ' ' . $return['approver'] . ' ' . $return['status'];
		// $return['test'] = $result;
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
		
	function deleteRawData() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> deleteRawData($return['pkid'], $return['empno'], $return['date'], $return['date_out'], $return['time_in'], $return['time_out']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function delete_raw_data() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> delete_raw_data($return['pkid']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function updateDailyTFM() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";		
		$result = OTKSR::getInstance() -> updateRecordDailyTFM($return['pkid'], $return['empno'], $return['date'], $return['time_in'], $return['mode_in'], $return['date_out'], $return['time_out'], $return['mode_out'], $return['reg_hours'],  $return['ot_hours'], $return['absent'], $return['late_conv'], $return['under_conv'], $return['slvl_code'], $return['slvl_num'], $return['ot1'], $return['ot2'], $return['ot2a'], $return['ot3'], $return['ot3a'], $return['ot4'], $return['ot4a'], $return['ot5'], $return['ot5a'], $return['ot6'], $return['ot6a'], $return['nightdiff'], $return['aut'], $return['username']);
		// $return["result"] = json_encode($result);
		$return['msg'] = $result['msg'];
		$return['tklogs_result'] = $result['tklogs_result'];
		echo json_encode($return);
	}
	
	function deleteDailyTFM() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> deleteDailyTFM($return['pkid'], $return['empno'], $return['date'], $return['date_out'], $return['time_in'], $return['time_out']);
	}
	
	function updateSLVL() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> uploadSLVL($return['empno'], $return['code'], $return['date'], $return['num_days'], $return['paid_leave'], $return['username'],$return['cutoff_date']);
		$return["msg"] = $result['msg'];
		$return["empno_return"] = $result['empno'];
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function deleteHolidayAbsent() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> deleteRecordHoliday($return['date']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function approveAttendance() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> approveAttendance($return['status'], $return['pkid'], $return['username'], $return['empno'], $return['dateIn']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function getApproverName() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> getApproverName($return['empno']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function recomputeTFM() {
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance() -> getEmpNoTkDataHRIS($return['startDate'], $return['endDate'], $return['username']);
		$return["result"] = json_encode($result);
		echo json_encode($return);
		//$result = OTKSR::getInstance() -> checkTardiness('H005','2014-07-07', $return['username']);		//happenned because of late
		//$return["test"] = $result;
		//echo "test";
		
		//$result = OTKSR::getInstance() -> checkOT('I002','2014-07-07', $return['username']);		//happenned because of OT computation grrrr
		//$return["test"] = $result;
		//$return["result"] = json_encode($result);
		//echo json_encode($return);
	}
	
	function compute_employee_tfm_data(){
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		
		$start_date = strtotime($return['startDate']); // or your date as well
		$end_date = strtotime($return['endDate']);
		$datediff = $end_date - $start_date;
		$datediff = floor($datediff/(60*60*24));
		$return['msg'] = '';
		
		if($datediff > 31){
			$return['msg'] = 'Cannot compute TFM! Maximum date range should not be greater than 31 days. <br> Dataset for computation is too large!';
			echo json_encode($return);
			exit;
		}
		
		$result = OTKSR::getInstance() -> compute_employee_tfm_data($return['startDate'], $return['endDate'], $return['username']);
				
		$html  = '<table class="table" border="1" id="tbl_recompute">';
		$html .= '	<thead>';
		$html .= ' 		<tr>';
		$html .= ' 			<th>EmpNo</th>';
		$html .= ' 			<th>Date</th>';
		$html .= ' 			<th>Absent</th>';
		$html .= ' 			<th>Late</th>';
		$html .= ' 			<th>Undertime</th>';
		$html .= ' 			<th>Nightdiff</th>';
		$html .= ' 		</tr>';
		$html .= '	</thead>';
		for($x=0;$x<count($result['empno']);$x++){
			$html .= '<tr align="center">';
			$html .= 	'<td>'.$result['empno'][$x].'</td>';
			$html .= 	'<td>'.$result['date'][$x].'</td>';
			$html .= 	'<td>'.$result['absent'][$x].'</td>';
			$html .= 	'<td>'.$result['late'][$x].'</td>';
			$html .= 	'<td>'.$result['undertime'][$x].'</td>';
			$html .= 	'<td>'.$result['nightdiff'][$x].'</td>';
			$html .= '</tr>';
		}
		$html .= '	</tbody>';
		$html .= '</table>';
		
		$return["html"] = $html;
		
		// $return["html"] = $return['startDate'] . ' ' . $return['endDate'] . ' ' . $return['username'];
		
		echo json_encode($return);
	}
	
	function load_employee_information(){
		require_once("otksr-oop.php");
		$return = $_POST;
		$result = "";
		$result = OTKSR::getInstance()->load_employee_information($return['empno']);
		$row = mysqli_fetch_array($result);
		$return['EmpNo']  		= $row['EmpNo'];
		$return['Name']  		= $row['firstname'] . " " . $row['lastname'];
		$return['Position']  	= $row['Position'];
		$return['Department']  	= $row['Department'];
		$return['Section']  	= $row['Section'];
		
		$return["result"] = json_encode($result);
		echo json_encode($return);
	}
	
	function count_unfiled_straight_hours(){
		require_once("otksr-oop.php");
		$return = array();
		$p = $_POST;
		$result = '';
		$result = OTKSR::getInstance()->count_unfiled_straight_hours($p['start_date'],$p['end_date']);
		$return['rec_list'] = $result;
		echo json_encode($return);
	}
	
	function fix_attendance_time_in(){
		require_once("otksr-oop.php");
		$return = array();
		$p = $_POST;
		$result = '';
		$result = OTKSR::getInstance()->fix_attendance_time_in($p['pkid']);
		$return['rec_list'] = $result;
		echo json_encode($return);
	}
	
	function fix_attendance_time_out(){
		require_once("otksr-oop.php");
		$return = array();
		$p = $_POST;
		$result = '';
		$result = OTKSR::getInstance()->fix_attendance_time_out($p['pkid']);
		$return['rec_list'] = $result;
		echo json_encode($return);
	}
	
	function fix_attendance_time_in_prev(){
		require_once("otksr-oop.php");
		$return = array();
		$p = $_POST;
		$result = '';
		$result = OTKSR::getInstance()->fix_attendance_time_in_prev($p['pkid']);
		$return['rec_list'] = $result;
		echo json_encode($return);
	}
	
	function fix_attendance_time_out_prev(){
		require_once("otksr-oop.php");
		$return = array();
		$p = $_POST;
		$result = '';
		$result = OTKSR::getInstance()->fix_attendance_time_out_prev($p['pkid']);
		$return['rec_list'] = $result;
		echo json_encode($return);
	}
	
	function get_first_cut_off(){
		$return 				= array();
		$p 						= $_POST;
		$year_month 			= $p['selected_month'];
		$year_month_day 		= $year_month.'-'.'01';
		$array_year_month 		= explode('-',$year_month_day);
		$year 					= $array_year_month[0];
		$month 					= $array_year_month[1];
				
		/* check if month is 1 */
		if($month == 1){
			$prev_year		= $year - 1;
			$prev_month 	= str_pad('12', 2, '0', STR_PAD_LEFT);

		}else {
			$prev_year	= $year;
			$prev_month 	= str_pad($month - 1, 2, '0', STR_PAD_LEFT);
		}
		
		$last_day_of_last_month = date("Y-m-t", strtotime($prev_year.'-'.$prev_month.'-'.'01'));
		$array_last_day_of_last_month = explode('-',$last_day_of_last_month);
		$last_day = $array_last_day_of_last_month[2];
		
		if($last_day >= 30){
			$return['start_date'] 	= $return['start_date'] = $prev_year.'-'.$prev_month.'-30';
			$return['end_date']		= $year.'-'.$month.'-14';
		}else{
			$return['start_date'] = $prev_year.'-'.$prev_month.'-01';
			$return['end_date']		= $year.'-'.$month.'-14';
		}
		$return['sample'] = $year_month_day;
		echo json_encode($return);
	}
	
	function get_second_cut_off(){
		$return 				= array();
		$p 						= $_POST;
		$year_month 			= $p['selected_month'];
		$year_month_day 		= $year_month.'-'.'15';
		$array_year_month 		= explode('-',$year_month_day);
		$year 					= $array_year_month[0];
		$month 					= $array_year_month[1];
			
		$last_day_of_this_month = date("Y-m-t", strtotime($year.'-'.$month.'-'.'01'));
		$array_last_day_of_this_month = explode('-',$last_day_of_this_month);
		$last_day = $array_last_day_of_this_month[2];
		
		if($last_day < 29){
			$return['start_date']		= $year.'-'.$month.'-15';
			$return['end_date'] 		= $return['start_date'] = $year_month.'-'.$last_day;
		}else{
			$return['start_date']		= $year.'-'.$month.'-15';
			$return['end_date'] 		= $year.'-'.$month.'-29';
		}
		$return['sample'] = $year_month_day;
		echo json_encode($return);
	}
	
	function save_adjusted_time_in_out(){
		require_once("otksr-oop.php");
		$return = array();
		$p = $_POST;
		$result = '';
		$result = OTKSR::getInstance()->save_adjusted_time_in_out( $p['array_in_out'] );
		$return['msg'] = '';
		// for($x=0;$x<count($p['array_in_out']['empno']);$x++){
			// if( $p['array_in_out']['adj_row'][$x] == 'true' ){
				// $return['msg']  .= $p['array_in_out']['date'][$x] .' // '.$p['array_in_out']['adj_row'][$x].' \n';
			// }
		// }
		
		$return['update_query'] = $result;
		
		echo json_encode($return);
	}
	
	function load_all_empno(){
		require_once("otksr-oop.php");
		$result = OTKSR::getInstance()->load_all_empno();
		$empno = array();
		while($row = mysqli_fetch_array($result)){
			$empno[] = $row['EmpNo'];
 		}
		echo json_encode($empno);
	}
	
	function validate_existing_empno(){
		require_once("otksr-oop.php");
		$p = $_POST;
		$empno = $p['empno'];
		$result = OTKSR::getInstance()->validate_existing_empno($empno);
		if($result->num_rows != 0){
			$return['valid'] = true;
		}else{
			$return['valid'] = false;
		}
		echo json_encode($return);
	}
	
	function save_offset(){
		require_once("otksr-oop.php");	
		$p 				= $_POST;
		$empno 			= $p['empno'];
		$date			= $p['date'];
		$username		= $p['username'];
		$result 		= OTKSR::getInstance()->save_offset($empno,$date,$username);
		$return['msg'] 	= $result;
		echo json_encode($return);
	}
	
	function delete_offset(){
		require_once("otksr-oop.php");	
		$p 				= $_POST;
		$pkid 			= $p['pkid'];
		$username		= $p['username'];
		$result 		= OTKSR::getInstance()->delete_offset($pkid,$username);
		$return['msg'] 	= $result;
		echo json_encode($return);
	}
	
	function save_holiday(){
		require_once("otksr-oop.php");	
		$p 				= $_POST;
		$date 			= $p['date'];
		$username		= $p['username'];
		$remarks		= $p['remarks'];
		$type			= $p['type'];
		$result 		= OTKSR::getInstance()->save_holiday($date,$username,$remarks,$type);
		$return['msg'] 	= $result;
		echo json_encode($return);
	}
	
	function delete_holiday(){
		require_once("otksr-oop.php");	
		$p 				= $_POST;
		$pkid 			= $p['pkid'];
		$username		= $p['username'];
		$result 		= OTKSR::getInstance()->delete_holiday($pkid,$username);
		$return['msg'] 	= $result;
		echo json_encode($return);
	}
	
	function upload_attendance_csv(){
		$return = array();
		if(file_exists($_FILES["file_1"]["tmp_name"])){
			$file = $_FILES["file_1"]["tmp_name"];
			$file_name = $_FILES["file_1"]["name"];
			$extension = explode('.',$file_name);
			$extension = end($extension);
			$array_attendance = array();
			if($extension != 'CSV' && $extension != 'csv'){
				$return['msg'] = '<div class="alert alert-danger">Please upload CSV file only!</danger>';
			}else{
				$file = fopen($file,"r");
				$header = true;
				echo '<table id="tbl_attendance_update" class="table">';
				while (($line = fgetcsv($file)) !== FALSE) {
				  if($header){
					 echo '	<thead>';
					 echo '		<tr>';
					 echo '			<th>'.$line[0].'</th>';
					 echo '			<th>'.$line[1].'</th>';
					 echo '			<th>'.$line[2].'</th>';
					 echo '			<th>'.$line[3].'</th>';
					 echo '			<th>'.$line[4].'</th>';
					 echo '			<th>'.$line[5].'</th>';
					 echo '			<th>'.$line[6].'</th>';
					 echo '			<th>'.$line[7].'</th>';
					 echo '		</tr>';
					 echo '	</thead>';
					 echo '	<tbody>';
					 $header = false;
				  }else{
					 echo '		<tr>';
					 echo '			<td>'.$line[0].'</td>';
					 echo '			<td>'.$line[1].'</td>';
					 echo '			<td>'.$line[2].'</td>';
					 echo '			<td>'.$line[3].'</td>';
					 echo '			<td>'.$line[4].'</td>';
					 echo '			<td>'.$line[5].'</td>';
					 echo '			<td>'.$line[6].'</td>';
					 echo '			<td>'.$line[7].'</td>';
					 echo '		</tr>';
				  }
				}
				echo '	</tbody>';
				echo '</table>';
				fclose($file);
				$return['msg'] = '';
			}
		}else{
			$return['msg'] = 'File does not exist!';
		}

		// echo json_encode($return);
		echo $return['msg'];
	}
	
	function update_raw_data_from_csv(){
		require_once('otksr-oop.php');
		$p = $_POST;
		$record = $p['record'];
		$result = OTKSR::getInstance()->update_raw_data_from_csv($record);
		$return = array();
		
		$return['msg'] = $result;
		echo json_encode($return);
	}
	
	function delete_leave(){
		require_once('otksr-oop.php');
		$p = $_POST;
		$pkid = $p['pkid'];
		$result = OTKSR::getInstance()->delete_leave($pkid);
		$return = array();
		$return['msg'] = $result;
		echo json_encode($return);
	}
	
	function edit_leave_information(){
		require_once('otksr-oop.php');
		$return = array();
		$return['msg'] = OTKSR::getInstance()->edit_leave_information( $_POST['pkid'], $_POST['dol'], $_POST['tol'], $_POST['lc'], $_POST['pl'], $_POST['cod'] );
		echo json_encode($return);
	}
	
	function delete_employee_dayoff(){
		require_once('otksr-oop.php');
		$return = array();
		$return['msg'] = OTKSR::getInstance()->delete_employee_dayoff( $_POST['empno'] );
		echo json_encode($return);
	}
	
	function add_employee_dayoff(){
		require_once('otksr-oop.php');
		$p = $_POST;
		$result = OTKSR::getInstance()->add_employee_dayoff($p['txt_empno'],$p['txt_type'],$p['txt_saturday'],$p['txt_username']);
		$return = array();
		$return['msg'] = $result;
		echo json_encode($return);
	}
	
	function add_yec_training(){
		require_once('otksr-oop.php');
		$result = OTKSR::getInstance()->add_yec_training($_POST);
		$return = array();
		$return['msg'] = $result;
		echo json_encode($return);
	}
	
	function delete_yec_training(){
		require_once('otksr-oop.php');
		$result = OTKSR::getInstance()->delete_yec_training($_POST);
		$return = array();
		$return['msg'] = $result;
		echo json_encode($return);
	}
	
	function get_undertime_late(){
		require_once('otksr-oop.php');
		$result = OTKSR::getInstance()->get_undertime_late($_POST);
		$return = array();
		while($row = mysqli_fetch_array($result)){
			$return['late'] 		= $row['late_conv'];
			$return['undertime'] 	= $row['under_conv'];
		}
		echo json_encode($return);
	}
	
	function add_late_ot(){
		require_once('otksr-oop.php');
		$result = OTKSR::getInstance()->add_late_ot($_POST);
		$return = array();
		$return['msg'] = $result;
		echo json_encode($return);
	}
	
	function delete_late_ot(){
		require_once('otksr-oop.php');
		$result = OTKSR::getInstance()->delete_late_ot($_POST);
		$return = array();
		$return['msg'] = $result;
		echo json_encode($return);
	}
?>
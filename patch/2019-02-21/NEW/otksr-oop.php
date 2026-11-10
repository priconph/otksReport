<?php
class OTKSR extends mysqli
	{
		private static $instance = null;
		
		/* db connection config variables */

		// public $user = "root";
		// public $pass = "newd3v";
		// public $dbName = "db_otks";
		// public $dbHost = "localhost";
		
		// require_once('./db_config/connection.php');
		public $user = "root";
		public $pass = "newd3v";
		public $dbName = "db_otks";
		public $dbHost = "localhost";
		
		public static function getInstance() {
			if(!self::$instance instanceof self) 
			{
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function __clone()
		{
			trigger_error('Clone is not allowed.',E_USER_ERROR);
		}
		
		public function __wakeup()
		{
			trigger_error('Deserializing is not allowed.',E_USER_ERROR);
		}
		
		private function __construct() {
			parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
			if(mysqli_connect_error())
			{
				exit('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
			}
			parent::set_charset('utf-8');
		}
		//Raw Data Module
		public function getRawData($dateFrom, $dateTo) {
			$query = "SELECT tkdataraw.pkid,tbl_EmployeeInfo.FirstName,tbl_EmployeeInfo.LastName, tbl_Position.Position, tbl_Department.Department, tbl_Section.Section,tkdataraw.empno, tkdataraw.date, 
			tkdataraw.time_in, tkdataraw.mode_in, tkdataraw.date_out,tkdataraw.time_out, tkdataraw.mode_out FROM db_hris.tbl_EmployeeInfo
			INNER JOIN db_hris.tbl_Department ON db_hris.tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment
			INNER JOIN db_hris.tbl_Section ON db_hris.tbl_Section.pkid = tbl_EmployeeInfo.fkSection 
			INNER JOIN db_hris.tbl_Position ON tbl_Position.pkid = tbl_EmployeeInfo.fkPosition 
			INNER JOIN db_otks.tkdataraw ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.lastupdate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
			ORDER BY tkdataraw.lastupdate;";
			return $this->query($query);
		}
		
		//Get Empno of Requestor or the Approver
		public function getEmpNo($username) {
			$query = "SELECT tbl_EmployeeInfo.EmpNo FROM db_acl.tbl_useraccnt INNER JOIN db_hris.tbl_EmployeeInfo ON tbl_EmployeeInfo.pkid = tbl_useraccnt.fkemployee 
					WHERE tbl_useraccnt.username='".$username."';";
			return $this->query($query);
		}
		
		//Get requestor information
		public function getRequestorInfo($empNo) {
			$query = "SELECT tbl_EmployeeInfo.fkDepartment, tbl_EmployeeInfo.fkSection, tbl_EmployeeInfo.LastName, tbl_EmployeeInfo.FirstName, tbl_EmployeeInfo.MiddleName, tbl_Department.Department, tbl_Division.Division FROM db_hris.tbl_EmployeeInfo 
				INNER JOIN db_hris.tbl_Department ON tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment INNER JOIN db_hris.tbl_Division ON tbl_Division.pkid = tbl_EmployeeInfo.fkDivision 
				WHERE tbl_EmployeeInfo.EmpNo='".$empNo."';";
			return $this->query($query);
		} 
		
		public function getEmpInfo($empno) {
			$query = "SELECT tbl_EmployeeInfo.SalaryType,tbl_EmployeeInfo.LastName, tbl_EmployeeInfo.FirstName, tbl_Department.Department, tbl_Section.Section, tbl_Position.Position FROM db_hris.tbl_EmployeeInfo INNER JOIN db_hris.tbl_Department
			ON tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment INNER JOIN db_hris.tbl_Section ON tbl_Section.pkid = tbl_EmployeeInfo.fkSection INNER JOIN db_hris.tbl_Position ON tbl_Position.pkid = tbl_EmployeeInfo.fkPosition WHERE tbl_EmployeeInfo.EmpNo='".$empno."';";
			return $this->query($query);
		}
		
		public function addRawData($empNo, $date, $timein, $modein, $dateout, $timeout, $modeout, $username) {
			$today = date ('Y-m-d');
			$query = "SELECT * FROM db_otks.tkdataraw WHERE empno='".$empNo."' AND date='".$date."';";
			$check = $this->query($query);
			if($check->num_rows == 0) {
				$new_pkid  = $this->new_pkid();
				$new_pkid += 1;
				$date_today = date('Y-m-d');
				// $attendanceDataRaw = $this->query("INSERT INTO db_otks.tkdataraw VALUES('$new_pkid','" . $today . "','" . $empNo . "','" . $date . "','" . $timein . ":00','". $modein ."','".$dateout."','".$timeout.":00','".$modeout."','','',0,0,0,0,0,0,0,0,0,0,'',0, 0, 0, 0, 0,0,0,0,0,0,0,0,0,0,0, '', 1, 'otks', '".$username."');");	//new data
				$query = "INSERT INTO tkdataraw (
							`pkid`,
							`empno`,
							`lastupdate`,
							`date`,
							`time_in`,
							`mode_in`,
							`date_out`,
							`time_out`,
							`mode_out`,
							`status`,
							`approver`,
							`username`) 
						VALUES(
							'$new_pkid',
							'".$empNo."',
							'$date_today',
							'$date',
							'$timein',
							'$modein',
							'$dateout',
							'$timeout',
							'$modeout',
							'1',
							'otks',
							'$username');";
				$this->query($query);
				
				// $tklogs_result = $this->update_tklogs($new_pkid,$empNo,$date,$timein,$modein,$dateout,$timeout,$modeout,$date_today);
				$tklogs_result = $this->add_update_tklogs($new_pkid,$empNo,$date,$timein,$modein,$dateout,$timeout,$modeout,$date_today);
				
				$array_return = array();
				$array_return['check'] 		 = '0';
				$array_return['tklogs_result'] = $tklogs_result;
				
				return $array_return;
			} else {
				$array_return = array();
				$array_return['check'] 		 = $query;
				$array_return['tklogs_result'] = $query;
				return $array_return;
			}
		}
		
		public function updateRawData($pkid, $array_update, $username) {
			$today = date ('Y-m-d'); //today;
			$pkid 		= $array_update['pkid'];
			$dateIn 	= $array_update['date'];
			$timeIn 	= $array_update['time_in'];
			$modeIn 	= $array_update['mode_in'];
			$dateOut	= $array_update['date_out'];
			$timeOut	= $array_update['time_out'];
			$modeOut	= $array_update['mode_out'];
			$empno		= $array_update['empno'];
			
			$query = "UPDATE db_otks.tkdataraw SET 
						tkdataraw.lastupdate='".$today."',
						tkdataraw.date = '".$dateIn."',
						tkdataraw.time_in='".$timeIn."',
						tkdataraw.mode_in='".$modeIn."',
						tkdataraw.mode_out='".$modeOut."',
						tkdataraw.date_out='".$dateOut."',
						tkdataraw.time_out='".$timeOut."',
						absent = '0', 
						tkdataraw.username='".$username."' 
					WHERE 
						tkdataraw.pkid='".$pkid."' 
						AND 
						tkdataraw.empno ='".$empno."';";
			
			$this->query($query);
			
			$test_query = $query;
			
			// $tklogs_result = $this->update_tklogs('',$empno,$dateIn,$timeIn,$modeIn,$dateOut,$timeOut,$modeOut,$today);
			$tklogs_result = $this->add_update_tklogs($pkid,$empno,$dateIn,$timeIn,$modeIn,$dateOut,$timeOut,$modeOut,$today);
			
			$array_return = array();
			$array_return['check'] 		 = 'Record has updated!';
			$array_return['tklogs_result'] = $tklogs_result;
			return $array_return;
		}
		
		public function add_update_tklogs($raw_data_pkid,$empNo,$date,$timein,$modein,$dateout,$timeout,$modeout,$date_today){
			$message = "";
			/* tklogs for IN */
			$query = "SELECT `pkid`,`empno`,`date`,`time`,`mode`,`modetype` FROM tklogs WHERE `fkid_tkdata` = '$raw_data_pkid' AND (`mode` = 'IN' OR `mode` = 'OB' OR `mode` = 'BUS LATE' OR `mode` = '') AND `logdel` = '0';"; 
			$message .= $query;
			
			$result = $this->query($query);
			/* check the mode type */
			if($modein == 'IN'){
				$modetype = 'NORMAL';
			}else if($modein == 'OB'){
				$modetype = 'OB-IN';
			}else if($modein == 'BUS LATE'){
				$modetype = 'BUS LATE';
			}else if($mode_in == ''){
				$modetype = '';
			}
			/* validate if date_in, time_in, and mode_in is not blank */
			/* if all has a value, logdel would be 0 */
			if($date != '' && $timein != '' && $modein != ''){
				$logdel = 0;
			}else{
				$logdel = 1;
			}
			
			/* validate existing logs */
			if($result->num_rows == 0){
				$query = "INSERT INTO `tklogs` (
							`empno`,
							`lastupdate`,
							`fkid_tkdata`,
							`date`,
							`time`,
							`mode`,
							`modetype`,
							`logdel`) 
						   VALUES(
							'$empNo',
							'$date_today',
							'$raw_data_pkid',
							'$date',
							'$timein',
							'$modein',
							'$modetype',
							'$logdel');";
				$this->query($query);
				$message .= $query."<br>";
			}else{
				/* if a tklogs already exist */
				while( $row = mysqli_fetch_array($result) ){
					$pkid = $row['pkid'];
					
					$query = "UPDATE 
								`tklogs` 
							  SET 
								`logdel` 	  = '0',
					            `lastupdate`  = '$date_today',
								`date`		  = '$date',
								`time`		  = '$timein',
								`mode`		  = '$modein',
								`modetype`	  = '$modetype',
								`logdel`	  = '$logdel'
							  WHERE 
								pkid = '$pkid';";
					$this->query($query);
					$message .= $query."<br>";
				}
			}
			
			/* tklogs for OUT */
			$query = "SELECT `pkid`,`empno`,`date`,`time`,`mode`,`modetype` FROM tklogs WHERE `fkid_tkdata` = '$raw_data_pkid' AND (`mode` = 'OUT' OR `mode` = 'OB' OR `mode` = 'UNDERTIME' OR `mode` = '') AND `logdel` = '0';"; 
			$result = $this->query($query);
			$message .= $query;
			/* check the mode type */
			if($modeout == 'OUT'){
				$modetype = 'NORMAL';
			}else if($modeout == 'OB'){
				$modetype = 'OB-OUT';
			}else if($modeout == 'UNDERTIME'){
				$modetype = 'NORMAL';
			}else if($modeout == ''){
				$modetype == '';
			}
			
			/* validate if date_in, time_in, and mode_in is not blank */
			/* if all has a value, logdel would be 0 */
			if($dateout != '' && $timeout != '' && $modeout != ''){
				$logdel = 0;
			}else{
				$logdel = 1;
			}
			/* validate existing logs */
			if($result->num_rows == 0){
				$query = "INSERT INTO `tklogs` (
							`empno`,
							`lastupdate`,
							`fkid_tkdata`,
							`date`,
							`time`,
							`mode`,
							`modetype`,
							`logdel`) 
						   VALUES(
							'$empNo',
							'$date_today',
							'$raw_data_pkid',
							'$dateout',
							'$timeout',
							'$modeout',
							'$modetype',
							'$logdel');";
				$this->query($query);
				$message .= $query."<br>";
			}else{
				/* if a tklogs already exist */
				while( $row = mysqli_fetch_array($result) ){
					$pkid = $row['pkid'];
					$query = "UPDATE 
								`tklogs` 
							  SET 
								`logdel` 	  = '0',
					            `lastupdate`  = '$date_today',
								`date`	  = '$dateout',
								`time`	  = '$timeout',
								`mode`	  = '$modeout',
								`modetype`	  = '$modetype',
								`logdel`	  = '$logdel'
							  WHERE 
								pkid = '$pkid';";
					$this->query($query);
					$message .= $query."<br>";
				}
			}
			return $message;
		}
		
		public function update_tklogs($new_pkid,$empNo,$date,$timein,$modein,$dateout,$timeout,$modeout,$date_today){
			
			$date_today = date('Y-m-d H:i:s');
			
			if($date != '' && $timein != '' && $modein != ''){
				/* check if date in time in exist in logs */
				$query = "SELECT `pkid`,`empno`,`date`,`time`,`mode`,`modetype` FROM tklogs WHERE `empno` = '$empNo' AND `date` = '$date' AND (`mode` = 'IN' OR `mode` = 'OB' OR `mode` = 'BUS LATE');"; 
				$result = $this->query($query);
				$message = 'no output';
				if($modein == 'IN'){
					$modetype = 'NORMAL';
				}else if($modein == 'OB'){
					$modetype = 'OB-IN';
				}else if($modein == 'BUS LATE'){
					$modetype = 'BUS LATE';
				}else if($mode_in == ''){
					$modetype = '';
				}
				
				if( $result->num_rows == 0 ) {
					$query = "INSERT INTO `tklogs` (
								`empno`,
								`lastupdate`,
								`fkid_tkdata`,
								`date`,
								`time`,
								`mode`,
								`modetype`) 
							   VALUES(
								'$empNo',
								'$date_today',
								'$new_pkid',
								'$date',
								'$timein',
								'$modein',
								'$modetype');";
					$this->query($query);
					$message = $query."<br>";
					
				}else{
					while($row = mysqli_fetch_array($result)){
						if(!isset($pkid)){
							$pkid = $row['pkid'];
						}
					}
					$query = "UPDATE 
								`tklogs` 
							  SET 
								`logdel` 	  = '0',
					            `lastupdate`  = '$date_today',
								`date`		  = '$date',
								`time`		  = '$timein',
								`mode`		  = '$modein',
								`modetype`	  = '$modetype' 
							  WHERE 
								pkid = '$pkid';";
					$this->query($query);
					$message = $query."<br>";
				}
			}else{
				/* check if date in time in exist in logs */
				$query = "SELECT `pkid`,`empno`,`date`,`time`,`mode`,`modetype` FROM tklogs WHERE `empno` = '$empNo' AND `date` = '$date' AND (`mode` = 'IN' OR `mode` = 'OB' OR `mode` = 'BUS LATE');"; 
				$result = $this->query($query);
				if( $result->num_rows == 0 ) {
					
				}else{
					while($row = mysqli_fetch_array($result)){
						if(!isset($pkid)){
							$pkid = $row['pkid'];
						}
					}
					$query = "UPDATE 
								`tklogs` 
							  SET 
					            `logdel`  	  = '1'
							  WHERE 
								pkid = '$pkid';";
					$this->query($query);
					$message = $query."<br>";
				}
			}
			$message .= $empNo . '//' .  $dateout . ' // ' . $timeout . ' // ' . $modeout;
			if($dateout != '' && $timeout != '' && $modeout != ''){
				$query = "SELECT `pkid`,`empno`,`date`,`time`,`mode`,`modetype` FROM tklogs WHERE `empno` = '$empNo' AND `date` = '$date' AND (`mode` = 'OUT' OR `mode` = 'OB' OR `mode` = 'UNDERTIME')"; 
				$result = $this->query($query);
				
				if($modeout == 'OUT'){
					$modetype = 'NORMAL';
				}else if($modeout == 'OB'){
					$modetype = 'OB-OUT';
				}else if($modeout == 'UNDERTIME'){
					$modetype = 'NORMAL';
				}
				
				if( $result->num_rows == 0 ) {
					// $new_pkid = $this->max_pkid_tklogs();
					$query = "INSERT INTO `tklogs` (
								`empno`,
								`lastupdate`,
								`fkid_tkdata`,
								`date`,
								`time`,
								`mode`,
								`modetype`) 
							   VALUES(
								'$empNo',
								'$date_today',
								'$new_pkid',
								'$date',
								'$timeout',
								'$modeout',
								'$modetype');";
					$this->query($query);
					$message .= $query."<br>";
				}else{
					while($row = mysqli_fetch_array($result)){
						$pkid = $row['pkid'];
					}
					$query = "UPDATE 
								`tklogs` 
							  SET 
							   `logdel` 	  = '0',	
							   `lastupdate`   = '$date_today',
								`date`		  = '$dateout',
								`time`		  = '$timeout',
								`mode`		  = '$modeout',
								`modetype`	  = '$modetype'
							  WHERE 
								pkid = '$pkid';";
					$this->query($query);
					$message .= $query."<br>";
				}
				
			}else{
				/* check if date in time in exist in logs */
				$query = "SELECT `pkid`,`empno`,`date`,`time`,`mode`,`modetype` FROM tklogs WHERE `empno` = '$empNo' AND `date` = '$date' AND (`mode` = 'IN' OR `mode` = 'OB' OR `mode` = 'BUS LATE');"; 
				$result = $this->query($query);
				if( $result->num_rows == 0 ) {
					
				}else{
					while($row = mysqli_fetch_array($result)){
						if(!isset($pkid)){
							$pkid = $row['pkid'];
						}
					}
					$query = "UPDATE 
								`tklogs` 
							  SET 
					            `logdel` = '1',
							  WHERE 
								pkid = '$pkid';";
					$this->query($query);
					$message = $query."<br>";
				}
			}			
			return $message;
		}
		
		public function max_pkid_tklogs(){
			$query = "SELECT MAX(`pkid`) AS `max_pkid` FROM `tklogs`;";
			$row = $this->query($query);
			return $row['max_pkid'] + 1;
		}
		
		public function deleteRawData($pkid, $empno, $date, $dateOut, $timeIn, $timeOut) {
			// $query = $this->query("DELETE FROM db_otks.tkdataraw WHERE tkdataraw.pkid='".$pkid."' AND tkdataraw.empno='".$empno."' AND tkdataraw.date='".$date."' AND tkdataraw.date_out='".$dateOut."' AND tkdataraw.time_in='".$timeIn."' AND tkdataraw.time_out='".$timeOut."';");
			$query = $this->query("DELETE FROM db_otks.tkdataraw WHERE tkdataraw.pkid='".$pkid."';");
		}
		
		public function delete_raw_data($pkid) {
			// $query = $this->query("DELETE FROM db_otks.tkdataraw WHERE tkdataraw.pkid='".$pkid."' AND tkdataraw.empno='".$empno."' AND tkdataraw.date='".$date."' AND tkdataraw.date_out='".$dateOut."' AND tkdataraw.time_in='".$timeIn."' AND tkdataraw.time_out='".$timeOut."';");
			/* update tklogs information */
			$query = "UPDATE `tklogs` SET `logdel` = '1' WHERE `fkid_tkdata` = '$pkid';";
			$result = $this->query($query);
			/* remove tkdataraw information */
			$query = "DELETE FROM db_otks.tkdataraw WHERE tkdataraw.pkid='$pkid';";
			$this->query($query);
			return 0;
		}
		
		//end
		
		// DailyTFM Module
		public function getDailyTFM($dateFrom, $dateTo) {
			$query = $this->query("SELECT DISTINCT(tkdataraw.empno), tkdataraw.pkid, tbl_EmployeeInfo.SalaryType, tkdataraw.date, tkdataraw.time_in, tkdataraw.mode_in,
			tkdataraw.date_out, tkdataraw.time_out, tkdataraw.mode_out,tkdataraw.reg_hours, tkdataraw.late_mins, tkdataraw.ot_credit, tkdataraw.absent,tkdataraw.under_mins, tkdataraw.leavecode, tkdataraw.leavenum, 
			tkdataraw.ot1, tkdataraw.ot2, tkdataraw.ot2a, tkdataraw.ot3, tkdataraw.ot3a, tkdataraw.ot4, tkdataraw.ot4a, tkdataraw.ot5, tkdataraw.ot5a, tkdataraw.ot6, tkdataraw.ot6a,
			tkdataraw.nightdiff, tkdataraw.aut FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.lastupdate BETWEEN '" . $dateFrom . 
			"' AND '" . $dateTo . "' ORDER BY tkdataraw.empno, tkdataraw.date ASC");
			return $query;
		}
		
		public function checkOT($empno, $date, $username) {
			$selectOT = $this->query("SELECT tkovertime_lom.lom_ot_hours, tkovertime_req.req_type FROM db_otks.tkovertime_lom INNER JOIN db_otks.tkovertime_req ON tkovertime_req.req_no = tkovertime_lom.req_id 
			WHERE tkovertime_req.req_status='1' AND tkovertime_lom.empno='".$empno."' AND tkovertime_lom.lom_date='".$date."';");
			//echo $selectOT->num_rows;
			if($selectOT->num_rows == 0) {
			} else {
				while($row = mysqli_fetch_array($selectOT)) {
					$otHours = $row['lom_ot_hours'];
					$otType = $row['req_type'];
				}
				
				if($otType == "Regular") {
					$updateOT1 = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot1='".$otHours."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
				} else if($otType == "Sunday") {
					if($otHours > 8) { //update ot3 and ot3a
						$excessOT = $otHours - 8;
						$updateOT3a = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot3='8', tkdataraw.ot3a='".$excessOT."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} else { //update ot3
						$updateOT3 = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot3='".$otHours."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					}
				} else if($otType == "Sunday/Legal Holiday") {
					if($otHours > 8) { //update ot4 and ot4a
						$excessOT = $otHours - 8;
						$updateOT4a = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot4='8', tkdataraw.ot4a='".$excessOT."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} else { //update ot4
						$updateOT4 = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot4='".$otHours."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} 
				} else if($otType == "Legal Holiday") {
					if($otHours > 8) { //update ot6 and ot6a
						$excessOT = $otHours - 8;
						$updateOT6a = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot6='8', tkdataraw.ot6a='".$excessOT."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} else { //update ot6
						$updateOT6 = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot6='".$otHours."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} 
				} else if($otType == "Special Non Working Holiday") {
					if($otHours > 8) { //update ot5 and ot5a
						$excessOT = $otHours - 8;
						$updateOT5a = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot5='8', tkdataraw.ot5a='".$excessOT."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} else { //update ot5
						$updateOT5 = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot5='".$otHours."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} 
				} else if($otType == "Sunday/Special Holiday") {
					if($otHours > 8) { //update ot2 and ot2a
						$excessOT = $otHours - 8;
						$updateOT2a = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot2='8', tkdataraw.ot2a='".$excessOT."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} else { //update ot2
						$updateOT2 = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.ot2='".$otHours."', tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."';");
					} 
				} 
			}
			//return $otHours . $otType;	dahil to nung di gumana. un pla dhil s datatype hmp
		}
		
		public function checkUnderTime($empno, $dateOut, $username) {
			$checkSaturday = $this->query("SELECT DAYNAME('".$dateOut."') AS ROW");
			while($rowSaturday = mysqli_fetch_array($checkSaturday)) {
				$saturday = $rowSaturday['ROW'];
			}
			if($saturday != 'Saturday') {
				$checkDayOff = $this->query("SELECT dayoff_type FROM db_otks.tkday_off WHERE empno='".$empno."';");
				if($checkDayOff->num_rows == 1) { //with day off meaning out is 4:30pm
					$getTimeOut = $this->query("SELECT tkdataraw.time_out FROM db_otks.tkdataraw WHERE empno='".$empno."' AND date_out='".$dateOut."';");
					while($row = mysqli_fetch_array($getTimeOut)) {
						$timeOut = $row['time_out'];
					}
					$str = explode(":", $timeOut);
					$hour = $str[0];
					$minutes = $str[1];
					if($hour <= 16) {
						//undertime
						if($hour == 16) {
							$requiredMins = 30; //Since out is 4:30
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes);
						} else if($hour == 15) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 1;
						} else if($hour == 14) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 2;
						} else if($hour == 13) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 3;
						} else if($hour == 12) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 4;
						} else if($hour == 11) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 5;
						}
						$this->updateUndertime($empno, $dateOut, $underMinsEquivalent, $username);
					} 
				} else {
					$getTimeOut = $this->query("SELECT tkdataraw.time_out FROM db_otks.tkdataraw WHERE empno='".$empno."' AND date_out='".$dateOut."';");
					while($row = mysqli_fetch_array($getTimeOut)) {
						$timeOut = $row['time_out'];
					}
					$str = explode(":", $timeOut);
					$hour = $str[0];
					$minutes = $str[1];
					if($hour <= 15) {
						//undertime
						if($hour == 15) {
							$requiredMins = 30; //Since out is 4:30
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes);
						} else if($hour == 14) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 1;
						} else if($hour == 13) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 2;
						} else if($hour == 12) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 3;
						} else if($hour == 11) {
							$requiredMins = 30;
							$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 4;
						}
						$this->updateUndertime($empno, $dateOut, $underMinsEquivalent, $username);
					}
				}
			} else {
				$getTimeOut = $this->query("SELECT tkdataraw.time_out FROM db_otks.tkdataraw WHERE empno='".$empno."' AND date_out='".$dateOut."';");
				while($row = mysqli_fetch_array($getTimeOut)) {
					$timeOut = $row['time_out'];
				}
				$str = explode(":", $timeOut);
				$hour = $str[0];
				$minutes = $str[1];
				if($hour <= 15) {
					//undertime
					if($hour == 15) {
						$requiredMins = 30; //Since out is 4:30
						$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes);
					} else if($hour == 14) {
						$requiredMins = 30;
						$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 1;
					} else if($hour == 13) {
						$requiredMins = 30;
						$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 2;
					} else if($hour == 12) {
						$requiredMins = 30;
						$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 3;
					} else if($hour == 11) {
						$requiredMins = 30;
						$underMinsEquivalent = $this->getUndertime($requiredMins,$minutes) + 4;
					}
					$this->updateUndertime($empno, $dateOut, $underMinsEquivalent, $username);
				}
			}
			//echo "[".$underMinsEquivalent."]";
		}
		
		public function getUndertime($requiredMins,$minutes) {
			$underMins = ($requiredMins-$minutes);
			if($underMins == 1) {
				$underMinsEquivalent = 0.02;
			} else if($underMins == 2) {
				$underMinsEquivalent = 0.03;
			} else if($underMins == 3) {
				$underMinsEquivalent = 0.05;
			} else if($underMins == 4) {
				$underMinsEquivalent = 0.06;
			} else if($underMins == 5) {
				$underMinsEquivalent = 0.08;
			} else if($underMins == 6) {
				$underMinsEquivalent = 0.1;
			} else if($underMins == 7) {
				$underMinsEquivalent = 0.11;
			} else if($underMins == 8) {
				$underMinsEquivalent = 0.13;
			} else if($underMins == 9) {
				$underMinsEquivalent = 0.15;
			} else if($underMins == 10) {
				$underMinsEquivalent = 0.16;
			} else if($underMins == 11) {
				$underMinsEquivalent = 0.18;
			} else if($underMins == 12) {
				$underMinsEquivalent = 0.2;
			} else if($underMins == 13) {
				$underMinsEquivalent = 0.21;
			} else if($underMins == 14) {
				$underMinsEquivalent = 0.23;
			} else if($underMins == 15) {
				$underMinsEquivalent = 0.25;
			} else if($underMins == 16) {
				$underMinsEquivalent = 0.26;
			} else if($underMins == 17) {
				$underMinsEquivalent = 0.28;
			} else if($underMins == 18) {
				$underMinsEquivalent = 0.3;
			} else if($underMins == 19) {
				$underMinsEquivalent = 0.31;
			} else if($underMins == 20) {
				$underMinsEquivalent = 0.33;
			} else if($underMins == 21) {
				$underMinsEquivalent = 0.35;
			} else if($underMins == 22) {
				$underMinsEquivalent = 0.36;
			} else if($underMins == 23) {
				$underMinsEquivalent = 0.38;
			} else if($underMins == 24) {
				$underMinsEquivalent = 0.4;
			} else if($underMins == 25) {
				$underMinsEquivalent = 0.41;
			} else if($underMins == 26) {
				$underMinsEquivalent = 0.43;
			} else if($underMins == 27) {
				$underMinsEquivalent = 0.45;
			} else if($underMins == 28) {
				$underMinsEquivalent = 0.46;
			} else if($underMins == 29) {
				$underMinsEquivalent = 0.48;
			} else if($underMins == 30) {
				$underMinsEquivalent = 0.5;
			} else if($underMins == 31) {
				$underMinsEquivalent = 0.51;
			} else if($underMins == 32) {
				$underMinsEquivalent = 0.53;
			} else if($underMins == 33) {
				$underMinsEquivalent = 0.55;
			} else if($underMins == 34) {
				$underMinsEquivalent = 0.56;
			} else if($underMins == 35) {
				$underMinsEquivalent = 0.58;
			} else if($underMins == 36) {
				$underMinsEquivalent = 0.6;
			} else if($underMins == 37) {
				$underMinsEquivalent = 0.61;
			} else if($underMins == 38) {
				$underMinsEquivalent = 0.63;
			} else if($underMins == 39) {
				$underMinsEquivalent = 0.65;
			} else if($underMins == 40) {
				$underMinsEquivalent = 0.66;
			} else if($underMins == 41) {
				$underMinsEquivalent = 0.68;
			} else if($underMins == 42) {
				$underMinsEquivalent = 0.7;
			} else if($underMins == 43) {
				$underMinsEquivalent = 0.71;
			} else if($underMins == 44) {
				$underMinsEquivalent = 0.73;
			} else if($underMins == 45) {
				$underMinsEquivalent = 0.75;
			} else if($underMins == 46) {
				$underMinsEquivalent = 0.76;
			} else if($underMins == 47) {
				$underMinsEquivalent = 0.78;
			} else if($underMins == 48) {
				$underMinsEquivalent = 0.8;
			} else if($underMins == 49) {
				$underMinsEquivalent = 0.81;
			} else if($underMins == 50) {
				$underMinsEquivalent = 0.83;
			} else if($underMins == 51) {
				$underMinsEquivalent = 0.85;
			} else if($underMins == 52) {
				$underMinsEquivalent = 0.86;
			} else if($underMins == 53) {
				$underMinsEquivalent = 0.88;
			} else if($underMins == 54) {
				$underMinsEquivalent = 0.9;
			} else if($underMins == 55) {
				$underMinsEquivalent = 0.91;
			} else if($underMins == 56) {
				$underMinsEquivalent = 0.93;
			} else if($underMins == 57) {
				$underMinsEquivalent = 0.95;
			} else if($underMins == 58) {
				$underMinsEquivalent = 0.96;
			} else if($underMins == 59) {
				$underMinsEquivalent = 0.98;
			} else if($underMins == 60) {
				$underMinsEquivalent = 1;
			}
			return $underMinsEquivalent;
		}
		
		public function updateUndertime($empno, $date, $undertime, $username) {
			$query = $this->query("UPDATE db_otks.tkdataraw SET under_mins='".$undertime."', tkdataraw.username='".$username."' WHERE date='".$date."' AND date_out='".$date."' AND empno='".$empno."';");
		}
		
		public function checkNightDiff($empno, $date, $username) {
			$checkTimeInOut = $this->query("SELECT date, time_in, date_out, time_out FROM db_otks.tkdataraw WHERE empno='".$empno."' AND date='".$date."' AND tkdataraw.status=1 ;");
			//echo $checkTimeInOut->num_rows;
			if($checkTimeInOut->num_rows == 0) {
				//no time in/out
			} else {
				$getTimeInOut = $this->query("SELECT date, time_in, date_out, time_out FROM db_otks.tkdataraw WHERE tkdataraw.status=1 AND empno='".$empno."' AND date='".$date."';");
				while($row = mysqli_fetch_array($getTimeInOut)) {
					$dateIn = $row['date'];
					$timeIn = $row['time_in'];
					$dateOut = $row['date_out'];
					$timeOut = $row['time_out'];
				}
				if($dateIn != $dateOut) {
					if($timeIn != "" && $timeOut != "") {
						$strTimeIn = explode(":", $timeIn);
						$strTimeOut = explode(":", $timeOut);
						$hourTimeIn = $strTimeIn[0];
						$hourTimeOut = $strTimeOut[0];
						$minutesTimeIn = $strTimeIn[1];
						$minutesTimeOut = $strTimeOut[1];
						
						if($hourTimeIn <= 23 && $hourTimeOut >= 7) {
							if($hourTimeIn == 23) {
								if($minutesTimeIn >= 30) {
									//late
									$updateNighDiff = $this->query("UPDATE db_otks.tkdataraw SET nightdiff=8, tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."' AND date_out='".$dateOut."';");
								} else {
									//ndi late
									$updateNighDiff = $this->query("UPDATE db_otks.tkdataraw SET nightdiff=8, tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."' AND date_out='".$dateOut."';");
									//echo "UPDATE db_otks.tkdataraw SET nightdiff=8 WHERE empno='".$empno."' AND date='".$date."' AND date_out='".$dateOut."';";
								}
							} else {
								//ndi late. tapos add night diff na 8
								$updateNighDiff = $this->query("UPDATE db_otks.tkdataraw SET nightdiff=8, tkdataraw.username='".$username."' WHERE empno='".$empno."' AND date='".$date."' AND date_out='".$dateOut."';");
								//echo "UPDATE db_otks.tkdataraw SET nightdiff=8 WHERE empno='".$empno."' AND date='".$date."' AND date_out='".$dateOut."';";
							}
						} 
					}
				}
			}
		}
		
		public function getEmpNoTkDataHRIS($dateFrom, $dateTo, $username) {
			$format = 'Y-m-d'; 
			$dateToday = date ($format ); //today;
			foreach($this->dateRange($dateFrom, $dateTo) as $i=>$i_value) {
				//echo "SELECT tkdata.empno FROM db_otks.tkdata WHERE tkdata.date = '" .$i_value. "'"."<br>";
				$checkSunday = $this->query("SELECT DAYNAME('".$i_value."') AS ROW");
				while($rowSunday = mysqli_fetch_array($checkSunday)) {
					$sunday = $rowSunday['ROW'];
				}
				if($sunday != 'Sunday') {
					$selectEmpNoOTKS = $this->query("SELECT tkdataraw.empno FROM db_otks.tkdataraw WHERE tkdataraw.status=1 AND tkdataraw.date = '" .$i_value. "'");
					while($rowOTKS = mysqli_fetch_array($selectEmpNoOTKS)) {
						$selectEmpNoHRIS = $this->query("SELECT tbl_EmployeeInfo.EmpNo FROM db_hris.tbl_EmployeeInfo WHERE EmpStatus = '1' AND NOT SalaryType = 3;");
						while($rowHRIS = mysqli_fetch_array($selectEmpNoHRIS)) {
							//echo $rowHRIS['EmpNo']."----".$rowOTKS['empno']."<br>";
							if($rowHRIS['EmpNo'] == $rowOTKS['empno']) {
								//already have record for the day
								$queryOT = $this->checkOT($rowHRIS['EmpNo'], $i_value, $username);
								$queryUndertime = $this->checkUnderTime($rowHRIS['EmpNo'], $i_value, $username);
								$queryNightDiff = $this->checkNightDiff($rowHRIS['EmpNo'], $i_value, $username);
								$queryLate = $this->checkTardiness($rowHRIS['EmpNo'], $i_value, $username);
								
							} if($rowHRIS['EmpNo'] != $rowOTKS['empno']) {
								//create new record for being absent
								$checkIfExist = $this->query("SELECT * FROM tkdataraw WHERE tkdataraw.status=1 AND lastUpdate='".date('Y-m-d')."' AND empno='".$rowHRIS['EmpNo']."' AND date='".$i_value."';");
								
								if($checkIfExist->num_rows == 0) {
									$queryOT = $this->checkOT($rowHRIS['EmpNo'], $i_value, $username);
									$attendanceDataRawAbsent = $this->query("INSERT INTO db_otks.tkdataraw VALUES('0','" . date('Y-m-d') . "','" . $rowHRIS['EmpNo'] . "','" . $i_value . "','','','','','','','',0,0,0,0,0,0,0,1,0,0,'',0, 0, 0, 0, 0,0,0,0,0,0,0,0,0,0,0, '', 1, 'otks', '".$username."');");
									//echo "INSERT --> ";
								}
							}
							//echo $rowHRIS['EmpNo']."----".$rowOTKS['empno']."<br>";
						}
					}	
					
				}
			}
		}
		
		/******************************** NEW ************************************/
		
		public function compute_employee_tfm_data($dateFrom,$dateTo,$username){
			$output = array();
			$dateToday = date('Y-m-d');
			
			foreach($this->dateRange($dateFrom, $dateTo) as $i=>$i_value) {
				/* break on future dates and date today */
				if( strtotime($i_value) >= strtotime( date('Y-m-d') ) ){
					break;
				}
				$employee_id_num = $this->select_all_pmi_employee();
				/* i_value is the moving date */
				for($x=0;$x<count($employee_id_num);$x++){
					$empno = $employee_id_num[$x];
					$absent_output 			= $this->update_absent($empno,$i_value);
					$late_output 			= $this->update_late($empno,$i_value);
					$undertime_output 		= $this->update_undertime($empno,$i_value);
					$nightdiff_output 		= $this->update_nightdiff($empno,$i_value);
					$output['empno'][] 		= $employee_id_num[$x];
					$output['date'][] 		= $i_value;
					$output['absent'][] 	= $absent_output;
					$output['late'][] 		= $late_output;
					$output['undertime'][] 	= $undertime_output;
					$output['nightdiff'][] 	= $nightdiff_output;
				}
			}
			// $output['nightdiff'][0] = $this->add_absent_to_daily_without_leave($dateFrom,$dateTo);
			$this->add_absent_to_daily_without_leave($dateFrom,$dateTo);
			return $output;
		}
		
		public function dateRange($dateFrom, $dateTo) {
			$step = '+1 day';
			$format = 'Y-m-d' ;
			$dates = array();
			$current = strtotime( $dateFrom );
			$last = strtotime( $dateTo );

			while( $current <= $last ) {
				$dates[] = date( $format, $current );
				$current = strtotime( $step, $current );
			}
			return $dates;
		}
		
		public function select_all_pmi_employee(){
			$array_empno = array();
			$query = "SELECT DISTINCT (`EmpNo`) FROM `db_hris`.`tbl_EmployeeInfo`  WHERE (`EmpStatus` = '1' OR `EmpStatus` = '3') AND `SalaryType` != '3' ORDER BY `tbl_EmployeeInfo`.`EmpNo`;";
			$result = $this->query($query);
			while($row = mysqli_fetch_array($result)){
				$array_empno[] = $row['EmpNo'];
			}
			return $array_empno;
		}
		
		public function update_absent($empno,$date_in){
			$row = 0;
			$query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in');";
			$result = $this->query($query);
			
			$row = mysqli_fetch_array($result);
			
			$pkid = $this->check_null($row['pkid']);
			$date = $this->check_null($row['date']);
			$date_out = $this->check_null($row['date_out']);
			$time_in = $this->check_null($row['time_in']);
			$time_out = $this->check_null($row['time_out']);
			$mode_in = $this->check_null($row['mode_in']);
			$mode_out = $this->check_null($row['mode_out']);
			$dayoff_type = 0;
			$is_it_saturday_dayoff = 0;
			
			if(date('D',strtotime($date_in)) == 'Sat'){
				/* check if employee has dayoff */
				$dayoff_type = $this->check_day_off($empno);
				if($dayoff_type == 1){
					/* check what saturday is dayoff */
					$saturday_dayoff = $this->check_saturday_day_off($empno);
					/* check if this saturday is a dayoff return 0 if 1st and 3rd Saturday Dayoff */
					$saturday_type = $this->check_dayoff_saturday($date_in);
					/* check if saturday is 1st and 3rd, check if employee dayoff is 1st and 3rd dayoff */
					if($saturday_dayoff == 0 && $saturday_type == 0){
						$is_it_saturday_dayoff = 1;
					}else if($saturday_dayoff == 1 && $saturday_type == 1){
						$is_it_saturday_dayoff = 1;
					}else{
						$is_it_saturday_dayoff = 0;
					}
				}
			}
			
			if( (count($row) == 0) ){
				if(date('D',strtotime($date_in)) == 'Sun'){
					return '-';
				}
				
				if($is_it_saturday_dayoff == 1 && $dayoff_type == 1){
					/* insert record but with no absent */
					$date_today = date('Y-m-d H:i:s');
					$query = "INSERT INTO tkdataraw (
								 `lastupdate`, 
								 `empno`,
								 `date`, 
								 `time_in`, 
								 `mode_in`,
								 `date_out`, 
								 `time_out`, 
								 `mode_out`,
								 `sched_in`, 
								 `sched_out`, 
								 `leavecode`,
								 `absent`,
								 `status`, 
								 `approver`, 
								 `username`) 
							VALUES(
								'$date_today', 
								'$empno', 
								'$date_in', 
								'do',
								'',
								'', 
								'', 
								'',
								'', 
								'', 
								'',
								'0',
								'1',
								'otks', 
								'otks'
								);";
					$this->query($query);
					return "dayoff";
				}				
				
				/* check other option for absent exemption */
				$absent = 0;
				$new_time_in = '';
				/* validate if day is a holiday */
				$is_holiday 	= $this->check_holiday($date_in);
				if($is_holiday){
					$absent = 0;
					$new_time_in = 'holiday';
					$leave_code = '';
				}else{
					/* validate if user has offset */		
					$is_offset 	= $this->check_offset($empno,$date_in);	
					$absent = 0;
					if($is_offset){
						$absent = 0;
						$new_time_in = 'do';
						$leave_code = '7';
					}else{
						/* validate if user is in YEC Training */
						$is_yec_training = $this->check_yec_training($empno,$date_in);	
						if($is_yec_training){
							$absent = 0;
							$new_time_in = 'yec';
							$leave_code = '';
						}else{
							$absent = 1;
							$new_time_in = '';
							$leave_code = '';
						}
					}
				}
				
				$date_today = date('Y-m-d H:i:s');
				$query = "INSERT INTO tkdataraw (
							`lastupdate`, 
							`empno`,
							 `date`, 
							 `time_in`, 
							 `mode_in`,
							 `date_out`, 
							 `time_out`, 
							 `mode_out`,
							 `sched_in`, 
							 `sched_out`, 
							 `leavecode`,
							 `absent`,
							 `status`, 
							 `approver`, 
							 `username`) 
						VALUES(
							'$date_today', 
							'$empno', 
							'$date_in', 
							'$new_time_in', 
							'',
							'', 
							'', 
							'',
							'', 
							'', 
							'$leave_code',
							'$absent',
							'1', 
							'otks', 
							'otks'
							);";
				$this->query($query);
				// return $query."<br>";
				return "<span style='color:red;'>Absent</span>";
			}else{
				/* checking order 
					1. Check Holiday
					2. Check Offset
					3. Check Dayoff
				*/
				
				/* check other way of absent exemption */
				$absent = 0;
				$new_time_in = '';
				/* validate if day is a holiday */
				$is_holiday 	= $this->check_holiday($date_in);
				
				if($is_holiday && $time_in == ''){
					$query  = "UPDATE ";
					$query .= "	`tkdataraw` `raw` ";
					$query .= "SET ";
					if($row['time_in'] != ''){
						$query .= "	`raw`.`absent` = '0' ";
					}else{
						$query .= "	`raw`.`absent` = '0', `raw`.`time_in` = 'holiday' ";
					}					
					$query .= "WHERE ";
					$query .= " `raw`.`pkid` = '".$row['pkid']."';";
					$result = $this->query($query);
					return "holiday";
				}
				
				$absent = 0;
				$new_time_in = '';
				/* cancel holiday */
				$is_holiday 	= $this->check_holiday($date_in);
				
				if(!$is_holiday && $time_in == 'holiday'){
					$query  = "UPDATE ";
					$query .= "	`tkdataraw` `raw` ";
					$query .= "SET ";
					$query .= "	`raw`.`absent` = '1', `raw`.`time_in` = '' ";
					$query .= "WHERE ";
					$query .= " `raw`.`pkid` = '".$row['pkid']."';";
					$result = $this->query($query);
					return "holiday";
				}
				
				/* validate if user has offset */		
				$is_offset 	= $this->check_offset($empno,$date_in);				
				if($is_offset && $time_in == ''){
					$query  = "UPDATE ";
					$query .= "	`tkdataraw` `raw` ";
					$query .= "SET ";
					$query .= "	`raw`.`absent` = '0' ";
					$query .= "WHERE ";
					$query .= " `raw`.`pkid` = '".$row['pkid']."';";
					$result = $this->query($query);
					return "sdo";
				}
				
				/* cancel switch dayoff */
				if(!$is_offset && $time_in == 'sdo'){
					$query  = "UPDATE ";
					$query .= "	`tkdataraw` `raw` ";
					$query .= "SET ";
					$query .= "	`raw`.`absent` = '0', `raw`.`time_in` = '' ";
					$query .= "WHERE ";
					$query .= " `raw`.`pkid` = '".$row['pkid']."';";
					$result = $this->query($query);
					return "sdo";
				}
				
				/* validate if user is in YEC Training */
				$is_yec_training = $this->check_yec_training($empno,$date_in);	
				if($is_yec_training && $time_in == ''){
					$absent = 0;
					$new_time_in = 'yec';
					$query  = "UPDATE ";
					$query .= "	`tkdataraw` `raw` ";
					$query .= "SET ";
					$query .= "	`raw`.`absent` = '0', `raw`.`time_in` = '$new_time_in' ";
					$query .= "WHERE ";
					$query .= " `raw`.`pkid` = '".$row['pkid']."';";
					$result = $this->query($query);
					return "yec training";
				}
				
				if($is_it_saturday_dayoff == 1 && $dayoff_type == 1){
					/* insert record but with no absent */
					$date_today = date('Y-m-d H:i:s');
					/* check if he has a time in before updating */
					if($row['time_in'] == ''){
						$query = "UPDATE 
									tkdataraw 
								  SET
									 `lastupdate` = '$date_today', 
									 `empno` = '$empno',
									 `date` = '$date_in', 
									 `time_in` = 'do', 
									 `mode_in` = '',
									 `date_out` = '', 
									 `time_out` = '', 
									 `mode_out` = '',
									 `sched_in` = '', 
									 `sched_out` = '', 
									 `leavecode` = '',
									 `absent` = '0',
									 `status` = '1', 
									 `approver` = 'otks', 
									 `username` = 'otks' 
									WHERE `pkid` = '$pkid';";
						$this->query($query);
						return "dayoff";
					}
				}
				
				/* cancel dayoff */
				if($is_it_saturday_dayoff == 0 && $time_in == 'do'){
					$query = "UPDATE 
									tkdataraw 
								  SET
									 `lastupdate` = '$date_today', 
									 `empno` 	= '$empno',
									 `date` 	= '$date_in', 
									 `time_in` 	= '', 
									 `mode_in` 	= '',
									 `date_out` = '', 
									 `time_out` = '', 
									 `mode_out` = '',
									 `sched_in` = '', 
									 `sched_out` = '', 
									 `leavecode` = '',
									 `absent` = '1',
									 `status` = '1', 
									 `approver` = 'otks', 
									 `username` = 'otks' 
									WHERE `pkid` = '".$row['pkid']."';";
						$this->query($query);
						return "cancel dayoff";
				}
				
				if($row['time_in'] == 'SL' || $row['time_in'] == 'VL' || $row['time_in'] == 'BDL' 
						|| $row['time_in'] == 'ML' || $row['time_in'] == 'FL' || $row['time_in'] == 'BL'){
					$query  = "UPDATE ";
					$query .= "	`tkdataraw` `raw` ";
					$query .= "SET ";
					$query .= "	`raw`.`absent` = '1' ";
					$query .= "WHERE ";
					$query .= " `raw`.`pkid` = '".$row['pkid']."';";
					$result = $this->query($query);
					return "<span style='color:red;'>FILLED LEAVE</span>";
				}
				
				/* validate if a row exist and there is no time in and time out add an absent */
				if( ( $row['time_in'] == '' || is_null($row['time_in'] ) ) && ( $row['time_out'] == '' || is_null($row['time_out'] ) ) ){
					// if($row['time_in'] == 'SL' || $row['time_in'] == 'VL' || $row['time_in'] == 'BDL' 
						// || $row['time_in'] == 'ML' || $row['time_in'] == 'FL' || $row['time_in'] == 'BL'  ){
						$query  = "UPDATE ";
						$query .= "	`tkdataraw` `raw` ";
						$query .= "SET ";
						$query .= "	`raw`.`absent` = '1',`raw`.`time_in` = '' ";
						$query .= "WHERE ";
						$query .= " `raw`.`pkid` = '".$row['pkid']."';";
						$result = $this->query($query);
						return "<span style='color:red;'>Absent</span>";
					// }
				}
				
				/* if there is a record update the absent to 0 */
				$query  = "UPDATE ";
				$query .= "	`tkdataraw` `raw` ";
				$query .= "SET ";
				$query .= "	`raw`.`absent` = '0' ";
				$query .= "WHERE ";
				$query .= " `raw`.`pkid` = '".$row['pkid']."';";
				$result = $this->query($query);
			}
			return "-";
			// return 'row count'.count($row);
		}
		
		public function check_holiday($date_in){
			$query = "SELECT `date` FROM `vw_holiday` WHERE `date` = '$date_in' AND `logdel` = '0';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return true;
			}else{
				return false;
			}
		}
		
		public function check_special_holiday($date_in){
			$query = "SELECT `type` FROM `vw_holiday` WHERE `date` = '$date_in' AND `type`='2' `logdel` = '0';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return true;
			}else{
				return false;
			}
		}
		
		public function check_offset($empno,$date_in){
			$query = "SELECT `date` FROM `vw_offset` WHERE `empno` = '$empno' AND `date` = '$date_in' AND `logdel` = '0';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return true;
			}else{
				return false;
			}
		}
		
		public function check_daily($empno){
			$query = "SELECT `SalaryType` FROM db_hris.`tbl_EmployeeInfo` WHERE `EmpNo` = '$empno' AND `SalaryType` = '1';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return true;
			}else{
				return false;
			}
		}
		
		public function add_absent_to_daily_without_leave($dateFrom,$dateTo){
			$query = "SELECT `date` from `tbl_holiday` WHERE `type` = '2' AND `date` BETWEEN '$dateFrom' AND '$dateTo' AND logdel ='0';";
			$result = $this->query($query);
			$array_date = array();
			while($row = mysqli_fetch_array($result)){
				$array_date[] = $row['date'];
			}
			for($x=0;$x<count($array_date);$x++){
				$array_date[$x] = "`raw`.`date` = '".$array_date[$x]."'";
			}
			$where = "WHERE (".implode(' OR ',$array_date).") AND `emp_info`.`SalaryType` = '1' AND (`raw`.`time_in` = '' OR `raw`.`time_in` = 'holiday');";
			$query = "UPDATE `tkdataraw` `raw` LEFT JOIN `db_hris`.`tbl_EmployeeInfo` `emp_info` ON `raw`.`empno` = `emp_info`.`EmpNo` SET `raw`.`absent` = '1', `raw`.`time_in` = '' ".$where;
			$result = $this->query($query);
			return $query;
		}
		
		public function check_yec_training($empno,$date_in){
			$query = "SELECT `empno` FROM `vw_yec_training` WHERE `empno` = '$empno' AND `training_date_end` >= '$date_in';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return true;
			}else{
				return false;
			}
		}
		
		public function update_late($empno,$date_in){
			// $query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in' OR `raw`.`date_out` = '$date_in');";
			$query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in');";
			$result = $this->query($query);
					
			$row = mysqli_fetch_array($result);
			
			$pkid 	= '';
			$query 	= '';
			
			$pkid = $this->check_null($row['pkid']);
			$date = $this->check_null($row['date']);
			$date_out = $this->check_null($row['date_out']);
			$time_in = $this->check_null($row['time_in']);
			$time_out = $this->check_null($row['time_out']);
			$mode_in = $this->check_null($row['mode_in']);
			$mode_out = $this->check_null($row['mode_out']);
			
			$employee_time_info = 'Empno: '.$empno.' Date: '.$date.' Time-In: '.$time_in.' <br>';
			
			if($date != '' && $date_out != '' && $time_in != '' && $time_out != '' && $mode_in != '' && $mode_out != ''){
				$holiday = $this->check_holiday($date);
				/* check if holiday */
				if($holiday){
					$query = "UPDATE `tkdataraw` SET `late_mins` = '0', `late_conv` = '0' WHERE `pkid` = '$pkid';";
					$this->query($query);
					return '-';
				}
				/* check if user has dayoff */
				$dayoff_type = $this->check_day_off($empno);
				/* Employee Actual Time-In */
				$actual_time_in = strtotime($date.' '.$time_in);
				$time_hour = explode( ' ' , date('Y-m-d H:i:s', $actual_time_in ) );
				$time_hour = explode(':', $time_hour[1] );
				$time_hour = $time_hour[0];
				/* check if morning shift or midnight shift */
				if($time_hour < 12 ){
					/* employee time in AM */
					$regular_time_in = strtotime($date_in.' 07:30:00');
				}else{
					/* employee time in PM */
					$regular_time_in = strtotime($date_in.' 19:30:00');
				}
				
				if( ($actual_time_in >  $regular_time_in) && ($mode_in == "IN") && !$this->is_sunday($date) ){
					$time_diff = floor( ($actual_time_in - $regular_time_in) / 60);
					/* check if timein is within 4 hours */
					if($dayoff_type == 1){
						/* with day off late is within 240 minutes or 4 hours */
						if($time_diff < 340){
							$minute_equivalent = $this->getMinutesEquivalent($time_diff);
							if($minute_equivalent >= 4){
								$minute_equivalent = 4;
							}
							/* query for updating late */
							$query = "UPDATE `tkdataraw` SET `late_mins` = '$time_diff', `late_conv` = '$minute_equivalent' WHERE `pkid` = '$pkid';";
							$this->query($query);
							// return $employee_time_info.'you are '.($time_diff).' minute late '.$minute_equivalent;
							return '<span style="color:red;"><b>'.$time_diff.' min</b></span>';
							// return $query;
						}
					}else{
						/* with day off late is within 240 minutes or 4 hours */
						if($time_diff < 340){
							$minute_equivalent = $this->getMinutesEquivalent($time_diff);
							if($minute_equivalent >= 4){
								$minute_equivalent = 4;
							}
							/* query for updating late */
							$query = "UPDATE `tkdataraw` SET `late_mins` = '$time_diff', `late_conv` = '$minute_equivalent' WHERE `pkid` = '$pkid';";
							$this->query($query);
							// return $employee_time_info.'you are '.($time_diff).' minute late '.$minute_equivalent;
							return '<span style="color:red;"><b>'.$time_diff.' min</b></span>';
							// return $query;
						}
					}
				}else{
					/* early login, update employee late to 0 */
					$query = "UPDATE `tkdataraw` SET `late_mins` = '0', `late_conv` = '0' WHERE `pkid` = '$pkid';";
					$this->query($query);
					return '-';
					// return $query;
				}
			} else {
			$query = "UPDATE `tkdataraw` SET `late_mins` = '0', `late_conv` = '0' WHERE `pkid` = '$pkid';";
			$this->query($query);
			return 'Inconsistency';
			// return 'Inconsistency'.$query;
			}
		}
		
		public function update_undertime($empno,$date_in){
			// $query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in');";
			$query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in' OR `raw`.`date_out` = '$date_in');";
			$result = $this->query($query);
					
			$row = mysqli_fetch_array($result);
			
			$pkid = '';
			$pkid = $this->check_null($row['pkid']);
			$date = $this->check_null($row['date']);
			$date_out = $this->check_null($row['date_out']);
			$time_in = $this->check_null($row['time_in']);
			$time_out = $this->check_null($row['time_out']);
			$mode_in = $this->check_null($row['mode_in']);
			$mode_out = $this->check_null($row['mode_out']);
			
			if($date != '' && $date_out != '' && $time_in != '' && $time_out != '' && $mode_in != '' && $mode_out != ''){
				$holiday = $this->check_holiday($date);
				if($holiday){
					$query = "UPDATE `tkdataraw` SET `late_mins` = '0', `late_conv` = '0' WHERE `pkid` = '$pkid';";
					$this->query($query);
					return '-';
				}
				/* check if user has dayoff */
				$dayoff_type = $this->check_day_off($empno);
				
				/* Employee Actual Time-Out */
				$actual_time_out = strtotime($date_out.' '.$time_out);
				$time_hour = explode( ' ' , date('Y-m-d H:i:s', $actual_time_out ) );
				$time_hour = explode(':', $time_hour[1] );
				$time_hour = $time_hour[0];
				
				/* check if morning shift or midnight shift */
				if($time_hour < 12 ){
					/* night shift, morning out */
					/* check if employee has dayoff */
					if($dayoff_type == 1){
						if(date('D',strtotime($date_out)) == 'Sat'){
							/* employee time out AM */
							$regular_time_out = strtotime($date_out.' 03:30:00');
						}else{
							$regular_time_out = strtotime($date_out.' 04:30:00');
						}
					}else{
						$regular_time_out = strtotime($date_out.' 03:30:00');
					}
				}else{
					/* day shift, afternoon out */
					/* employee time out PM */
					if($dayoff_type == 1){
						if(date('D',strtotime($date_out)) == 'Sat'){
						/* employee time out AM */
							$regular_time_out = strtotime($date_out.' 15:30:00');
						}else{
							$regular_time_out = strtotime($date_out.' 16:30:00');
						}
					}else{
						$regular_time_out = strtotime($date_out.' 15:30:00');
					}
					
				}
				
				if( ($actual_time_out < $regular_time_out) && ($mode_out == 'OUT' || $mode_out == 'UNDERTIME') && !$this->is_sunday($date)){
					$time_diff = floor( ($regular_time_out - $actual_time_out) / 60 );
					$minute_equivalent = $this->getMinutesEquivalent($time_diff);
					
					$query = "UPDATE tkdataraw SET `under_mins` = '$time_diff', `under_conv` = '$minute_equivalent' WHERE `pkid` = '$pkid'";
					$this->query($query);
					return '<span style="color:red;"><b>'.$time_diff.' min</b></span>';
					// return $query;
				}else{
					// $minute_equivalent = $this->getMinutesEquivalent($time_diff);
					$query = "UPDATE tkdataraw SET `under_mins` = '0' , `under_conv` = '0' WHERE `pkid` = '$pkid'";
					$this->query($query);
					return "-";
				}
			}
			// $minute_equivalent = $this->getMinutesEquivalent($time_diff);
			$query = "UPDATE tkdataraw SET `under_mins` = '0', `under_conv` = '0' WHERE `pkid` = '$pkid'";
			$this->query($query);
			return "Inconsistency";
		}
		
		public function update_nightdiff($empno,$date_in){
			// $query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in' OR `raw`.`date_out` = '$date_in');";
			$query = "SELECT `raw`.`pkid`, `raw`.`date`, `raw`.`date_out`, `raw`.`time_in`, `raw`.`time_out` , `raw`.`mode_in`, `raw`.`mode_out` FROM `tkdataraw` `raw` WHERE `raw`.`empno` = '$empno' AND (`raw`.`date` = '$date_in');";
			$result = $this->query($query);
					
			$row = mysqli_fetch_array($result);
			
			$pkid = '';
			$pkid = $this->check_null($row['pkid']);
			$date = $this->check_null($row['date']);
			$date_out = $this->check_null($row['date_out']);
			$time_in = $this->check_null($row['time_in']);
			$time_out = $this->check_null($row['time_out']);
			$mode_in = $this->check_null($row['mode_in']);
			$mode_out = $this->check_null($row['mode_out']);
			
			if($date != '' && $date_out != '' && $time_in != '' && $time_out != '' && $mode_in != '' && $mode_out != ''){
				$nightdiff_start 	= $date_in.' 22:00:00';
				$tomorrow = date( 'Y-m-d', date(strtotime($nightdiff_start) + 86400) );
				$nightdiff_end 		= $tomorrow.' 06:00:00';
				
				$actual_time_in  = $date.' '.$time_in;
				$actual_time_out = $date_out.' '.$time_out;
				
				/* get 11:00PM by adding 3600 seconds equivalent to 60 minutes from the nightdiff start */
				$eleven_pm = date('Y-m-d H:i:s' , strtotime($nightdiff_start) + 3600);
				
				$time_hour = $this->get_hour($actual_time_in);
				
				
				/* check if time in is a PM check if timeout is greater than 11:00 PM the minum night diff an employee can get */
				if(  $time_hour >= 12 && (strtotime($actual_time_out) >= strtotime($eleven_pm)) ){
					/* convert to seconds */
					$actual_time_in = strtotime($actual_time_in);
					$actual_time_out = strtotime($actual_time_out);
					$eleven_pm = strtotime($eleven_pm);
					$nightdiff_end = strtotime($nightdiff_end);
					
					$nightdiff = 1;
					/* loop until actual time in is less than end of night diff or time in is less than time out */
					while ($actual_time_in < $nightdiff_end || $actual_time_in < $actual_time_out){
						/* add 1 hour to actual time in */
						$actual_time_in += 3600;
						/* check if incremented time in is greater than equal to 11:00 PM */
						if( ($actual_time_in > $eleven_pm) && ($actual_time_in <= $actual_time_out) && ($actual_time_in <= $nightdiff_end) ){
							$nightdiff++;
						}
					}
					if($nightdiff > 8){
						$nightdiff = 8;
					}
					$query = "UPDATE `tkdataraw` SET `nightdiff` = '$nightdiff' WHERE `pkid` = '$pkid';";
					$this->query($query);
					return '<span style = "color:green;"><b>'.$nightdiff.'</b></span>';
					// return $query;
				}
				
				/* drivers nightdiff computation */
				/* check if time in is a PM check if timeout is greater than 11:00 PM the minum night diff an employee can get */
				if(  ( strtotime($actual_time_out) >= strtotime($eleven_pm) ) ){
					/* convert to seconds */
					$actual_time_in = strtotime($actual_time_in);
					$actual_time_out = strtotime($actual_time_out);
					$eleven_pm = strtotime($eleven_pm);
					$nightdiff_end = strtotime($nightdiff_end);
					
					$nightdiff = 1;
					/* loop until actual time in is less than end of night diff or time in is less than time out */
					while ($actual_time_in < $nightdiff_end || $actual_time_in < $actual_time_out){
						/* add 1 hour to actual time in */
						$actual_time_in += 3600;
						/* check if incremented time in is greater than equal to 11:00 PM */
						if( ($actual_time_in > $eleven_pm) && ($actual_time_in <= $actual_time_out) && ($actual_time_in <= $nightdiff_end) ){
							$nightdiff++;
						}
					}
					// $nightdiff = $nightdiff - 1;
					if($nightdiff > 8){
						$nightdiff = 8;
					}
					$query = "UPDATE `tkdataraw` SET `nightdiff` = '$nightdiff' WHERE `pkid` = '$pkid';";
					$this->query($query);
					return '<span style = "color:green;"><b>'.$nightdiff.'</b></span>';
					// return $query;
				}
				
			}
			$query = "UPDATE `tkdataraw` SET `nightdiff` = '0' WHERE `pkid` = '$pkid';";
			$this->query($query);
			return '-';
		}
		
		public function update_no_out($empno,$date_in){
			
		}
		
		public function check_approved_straight_hours($empno,$date){
			$query = "SELECT * FROM vw_straight_hours WHERE `empno` = '$empno' AND `date_from` = '$date';";
			$result = $this->query($query);
			$valid = false;
			while( $row = mysqli_fetch_array($result) ){
				if( $row['status'] == 2 ){
					$valid = true;
				}
			}
			return $valid;
		} 
		
		public function check_null($data){
			if(is_null($data) || $data == ''){
				return '';
			}else{
				return $data;
			}
		}
		
		public function get_last_pkid(){
			$result = $this->query("SELECT MAX(pkid) as max_id FROM tkdataraw;");
			$row = mysqli_fetch_array($result);
			$last_id = $row['max_id'];
			return $last_id;
		}
				
		public function check_dayoff_saturday($date){
			$validate_date = date("F-d-Y",strtotime($date));
			$validate_date = explode("-",$validate_date);
			
			$first_sat = date( "Y-m-d" , strtotime('first saturday of '.$validate_date[0]));
			$third_sat = date( "Y-m-d", strtotime('third saturday of '.$validate_date[0]));
			$fifth_sat = date( "Y-m-d", strtotime('fifth saturday of '.$validate_date[0]));
			
			if($date == $first_sat || $date == $third_sat){
				return 0;
			}else if( $date == $fifth_sat ){
				return 2;
			}else{
				return 1;
			}
		}
		
		public function check_dayoff_saturday_alternate($date){
			$validate_date = date("F-d-Y",strtotime($date));
			$validate_date = explode("-",$validate_date);
			
			$first_sat = date( "Y-m-d" , strtotime('first saturday of '.$validate_date[0]));
			$third_sat = date( "Y-m-d", strtotime('third saturday of '.$validate_date[0]));
			$fifth_sat = date( "Y-m-d", strtotime('fifth saturday of '.$validate_date[0]));
			
			if($date == $first_sat || $date == $third_sat || $date == $fifth_sat){
				return 1;
			}else{
				return 0;
			}
		}
		
		public function check_day_off($empno){
			$query = "SELECT `dayoff_type` FROM `tkday_off` WHERE `empno` = '$empno' AND `dayoff_type` = '1' AND `logdel` ='0';";
			$result = $this->query($query);
			
			$dayoff_type = 0;
			$row_day_off = mysqli_fetch_array($result);
			if( count($row_day_off) != 0 ){
				/* set dayoff to 1 if employee exist in tkday_off */
				$dayoff_type = 1;
			}
			return $dayoff_type;
		}
		
		public function check_saturday_day_off($empno){
			$query = "SELECT `saturday` FROM `tkday_off` WHERE `empno` = '$empno' AND `dayoff_type` = '1' AND `logdel` ='0';";
			$result = $this->query($query);
			
			while( $row = mysqli_fetch_array($result) ){
				/* 0 = 1st and 3rd saturday, 1 = 2nd and 4th saturday */
				$saturday_dayoff = $row['saturday'];
			}				
			return $saturday_dayoff;
		}
		
		public function is_sunday($date){
			$sunday = date('D', strtotime($date));
			if($sunday == 'Sun'){
				return true;
			}
			return false;
		}
		
		public function get_hour($date_time){
			$time_hour = explode(' ', $date_time );
			$time_hour = explode(':', $time_hour[1] );
			$time_hour = $time_hour[0];
			return $time_hour;
		}
		
		/******************************** NEW ************************************/
		
		public function deleteDailyTFM($pkid, $empno, $date, $dateOut, $timeIn, $timeOut) {
			$query = $this->query("DELETE FROM db_otks.tkdataraw WHERE tkdataraw.pkid='".$pkid."' AND tkdataraw.empno='".$empno."' AND tkdataraw.date='".$date."' AND tkdataraw.date_out='".$dateOut."' AND tkdataraw.time_in='".$timeIn."' AND tkdataraw.time_out='".$timeOut."';");
		}
		
		
		public function updateRecordDailyTFM($pkid, $empno, $dateIn, $timeIn, $modeIn, $dateOut, $timeOut, $modeOut, $reg_hours, $ot_credit, $absent, $late_conv, $under_conv, $slVlCode, $slVlHr, $ot1, $ot2, $ot2a, $ot3, $ot3a, $ot4, $ot4a, $ot5, $ot5a, $ot6, $ot6a, $nightdiff, $aut, $username) {
			$today = date ('Y-m-d H:i:s'); //today;
			$query = "UPDATE db_otks.tkdataraw SET
									tkdataraw.date = '".$dateIn."',
									tkdataraw.time_in='".$timeIn."',
									tkdataraw.mode_in='".$modeIn."',
									tkdataraw.lastupdate='".$today."',
									tkdataraw.date_out='".$dateOut."',
									tkdataraw.time_out='".$timeOut."', 
									tkdataraw.mode_out='".$modeOut."', 
									tkdataraw.reg_hours='".$reg_hours."', 
									tkdataraw.reg_hours ='".$ot_credit."', 
									tkdataraw.absent='".$absent."', 
									tkdataraw.late_conv='".$late_conv."', 
									tkdataraw.under_conv='".$under_conv."', 
									tkdataraw.leavecode='".$slVlCode."', 
									tkdataraw.leavenum='".$slVlHr."', 
									tkdataraw.nightdiff='".$nightdiff."', 
									tkdataraw.ot1='".$ot1."', 
									tkdataraw.ot2='".$ot2."', 
									tkdataraw.ot2a='".$ot2a."', 
									tkdataraw.ot3='".$ot3."', 
									tkdataraw.ot3a='".$ot3a."', 
									tkdataraw.ot4='".$ot4."', 
									tkdataraw.ot4a='".$ot4a."', 
									tkdataraw.ot5='".$ot5."', 
									tkdataraw.ot5a='".$ot5a."', 
									tkdataraw.ot6='".$ot6."', 
									tkdataraw.ot6a='".$ot6a."', 
									tkdataraw.aut='".$aut."', 
									tkdataraw.username='".$username."' 
								WHERE 
									tkdataraw.pkid='".$pkid."' 
								AND 
									tkdataraw.empno ='".$empno."' 
								AND 
									tkdataraw.date = '".$dateIn."';";
			$this->query($query);
			
			// return $query;
			$tklogs_result = $this->update_tklogs('',$empno,$dateIn,$timeIn,$modeIn,$dateOut,$timeOut,$modeOut,$today);
			$return_array = array();
			$return_array['msg'] = 'Data has been updated!';
			// $return_array['msg'] = $query;
			$return_array['tklogs_result'] = $query;
			return $return_array;
			
		}
		
		public function getInconsistencyRecord($dateFrom, $dateTo) {
			$query = $this->query("SELECT 
									tkdataraw.pkid, 
									tbl_Department.Department, 
									tbl_Section.Section, 
									tkdataraw.date, 
									tkdataraw.date_out, 
									tkdataraw.empno, 
									tbl_EmployeeInfo.LastName, 
									tbl_EmployeeInfo.FirstName, 
									tbl_EmployeeInfo.MiddleName, 
									tbl_Position.Position, 
									tkdataraw.time_in, 
									tkdataraw.time_out 
								FROM 
									db_hris.tbl_EmployeeInfo 
								INNER JOIN 
									db_hris.tbl_Department 
								ON 
									tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment 
								INNER JOIN 
									db_hris.tbl_Section 
								ON 
									tbl_Section.pkid = tbl_EmployeeInfo.fkSection 
								INNER JOIN 
									db_otks.tkdataraw 
								ON 
									tkdataraw.empno = tbl_EmployeeInfo.EmpNo 
								INNER JOIN 
									db_hris.tbl_Position 
								ON 
									tbl_Position.pkid = tbl_EmployeeInfo.fkPosition 
								WHERE 
									tkdataraw.status=1 
								AND 
									((tkdataraw.date 
								BETWEEN 
									'".$dateFrom."' 
								AND 
									'".$dateTo."')
								OR
									(tkdataraw.date_out 
								BETWEEN 
									'".$dateFrom."' 
								AND 
									'".$dateTo."'))
								AND 
									absent = '0' 
								ORDER BY 
									tbl_Department.Department ASC, 
									tkdataraw.empno ASC, 
									tkdataraw.date ASC, 
									tbl_Section.Section ASC");
				//ORDER BY tbl_Department.Department ASC");
			return $query;
		}
		
		public function getReceivingSheet($dateFrom, $dateTo) {
			$query = $this->query("SELECT DISTINCT(tbl_Department.Department) FROM db_hris.tbl_EmployeeInfo INNER JOIN db_hris.tbl_Department 
				ON tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment INNER JOIN db_otks.tkdataraw
				ON tkdataraw.empno = tbl_EmployeeInfo.EmpNo WHERE tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' AND absent = '0' 
				AND (tkdataraw.time_in = '' OR tkdataraw.time_out = '')
				ORDER BY tbl_Department.Department ASC");
				
			return $query;
		}
		
		public function uploadSLVL($empno, $code, $date, $noDaysLeave, $daysPaid, $username, $cutoff_date) {
			if($empno == ''){
				return "OK";
			}
			switch($code){
				case "1" : $code_equiv = "SL"; break;
				case "2" : $code_equiv = "VL"; break;
				case "3" : $code_equiv = "FL"; break;
				case "4" : $code_equiv = "ML"; break;
				case "5" : $code_equiv = "BDL"; break;
				case "6" : $code_equiv = "BL"; break;
				case "7" : $code_equiv = "do"; break;
				default	: "Leave";
			}
			$format = 'Y-m-d'; 
			$today = date ($format ); //today;
			
			$checkAttendance = $this->query("SELECT pkid, date, time_in, date_out, time_out FROM db_otks.tkdataraw WHERE empno='".$empno."' AND date='".$date."';");
			if($checkAttendance->num_rows == 0) {
				$return = array();
				$query = "INSERT INTO `tkdataraw` (`lastupdate`,`empno`,`date`,`time_in`,`absent`,`leavecode`,`leavenum`,`username`,`status`) 
											VALUES('".date('Y-m-d')."','$empno','$date','$code_equiv','1','$code','$daysPaid','otks','1');";
				$this->query($query);
			}
			$checkAttendance = $this->query("SELECT pkid, date, time_in, date_out, time_out FROM db_otks.tkdataraw WHERE empno='".$empno."' AND date='".$date."';");	
			while($row = mysqli_fetch_array($checkAttendance)){
				$pkid 		= $row['pkid'];
				$date_in 	= $row['date'];
				$time_in 	= $row['time_in'];
				$date_out	= $row['date_out']; 
				$time_out	= $row['time_out'];
			}
			if($time_in == ''){
				$time_in = $code_equiv;
			}
			$update_query  = "UPDATE db_otks.tkdataraw SET 
								`absent`	='1',
								`time_in`	='".$time_in."', 
								`leavecode`	='".$code."', 
								`leavenum`	='".$daysPaid."', 
								`username`	='".$username."' 
							WHERE 
								`empno`		='".$empno."' 
							AND 
								`pkid`		='".$pkid."';";
			$updateDatRaw = $this->query($update_query);
			$tklogs_result = $this->add_update_tklogs($pkid,$empno,$date_in,$date_out,'',$date_out,$time_out,'',date('Y-m-d H:i:s'));
			
			$query = "SELECT `fk_tkdataraw` FROM `tbl_leave_cutoff_date` WHERE `fk_tkdataraw` = '$pkid';";
			$result = $this->query($query);
			if($result->num_rows == 0) {
				$query = "INSERT INTO `tbl_leave_cutoff_date` (`fk_tkdataraw`,`date_of_leave`,`days`,`days_paid`,`cutoff_date`) VALUES('$pkid','$date','$noDaysLeave','$daysPaid','$cutoff_date');";
				$this->query($query);
				$return['msg'] = "OK New Data!";
			}else{
				while($row = mysqli_fetch_array($result)){
					$pkid = $row['pkid'];
				}
				$query = "UPDATE `tbl_leave_cutoff_date` SET `date_of_leave` = '$date', `days` = '$noDaysLeave',`days_paid` = '$daysPaid',`cutoff_date` = '$cutoff_date') WHERE `pkid` = '$pkid';";
				$this->query($query);
				$return['msg'] = "OK Update Data!";
			}
			$return = array();
			
			$return['empno'] = $empno;
			return $return;
		}
		
		public function checkTardiness($empno, $date, $username) {
			$getTimeIn = $this->query("SELECT tkdataraw.time_in FROM db_otks.tkdataraw WHERE tkdataraw.empno='".$empno."' AND tkdataraw.date='".$date."';");
			if($getTimeIn->num_rows == 0) {
				//walang time in/out. he/she can be absent or sl/vl
			} else {
				while($rowIn = mysqli_fetch_array($getTimeIn)) {
					$time = $rowIn['time_in'];
				}
				$timeToday = new DateTime($time);
				$hourToday = ($timeToday->format('H'));
				$minToday = ($timeToday->format('i'));
				if($hourToday >= 7) {
					if($hourToday >= 15) {
						if(($hourToday >=20) || ($hourToday >= 19 && $minToday >= 31)) {
							$lateTime = new DateTime('19:31:00'); //7:30pm late
							$this->updateTardiness($lateTime, $time, $empno, $date, $username);
						}
					} else if ($hourToday >= 8 || ($hourToday >= 7 && $minToday >= 31)) {
						$lateTime = new DateTime('07:31:00'); //7:30 am late
						$this->updateTardiness($lateTime, $time, $empno, $date, $username);
					}
				}
			}
		}
		
		public function updateTardiness($lateTime, $time, $empno, $date, $username) {
				//echo $empno."<br>";
			$timeCame = new DateTime($time);
			$interval = $lateTime->diff($timeCame);
			$hour = ($interval->format('%H'))*60;
			$minutes = ($interval->format('%i'));
			$minutesLate = (int)((int) $minutes + (int) $hour);
			$selectTardiness = "SELECT nooflate, noofmins FROM tktardiness WHERE empno = '" . $empno . "' AND month = '" . substr($date, 5,2) . "';";
			$selectNoOfTardiness = $this->query($selectTardiness);
			if($selectNoOfTardiness->num_rows == 0) {
				$leftMins = 100 - (int) $minutesLate;
				$minutesEquivalent= $this->getMinuteEquivalent($minutesLate);
				$queryInsert = $this->query("INSERT INTO db_otks.tktardiness VALUES('" . $empno . "','" . substr($date, 5,2) . "','1', '" . $leftMins . "');");
				$updateTkDataRawLateMins = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.late_mins = '" . $minutesEquivalent . "', tkdataraw.username='".$username."' WHERE empno ='" . $empno . "' AND date = '" . $date . "' AND time_in = '" . $time . "';");
				//$test = "UPDATE db_otks.tkdataraw SET tkdataraw.late_mins = '" . $minutesEquivalent . "', tkdataraw.username='".$username."' WHERE empno ='" . $empno . "' AND date = '" . $date . "' AND time_in = '" . $time . "';";
			} else {
				while($row = mysqli_fetch_array($selectNoOfTardiness)) {
					$noofmins = $row['noofmins'];
					$nooflate = $row['nooflate'];
				}
				$nomins = (int)((int)$noofmins - (int)$minutesLate);
				$nooflate = (int)((int)$nooflate + 1);
				$minutesEquivalent= $this->getMinuteEquivalent($minutesLate);
				//$test = "UPDATE db_otks.tkdataraw SET tkdataraw.late_mins = '" . $minutesEquivalent . "', tkdataraw.username='".$username."' WHERE empno ='" . $empno . "' AND date = '" . $date . "' AND time_in = '" . $time . "';";
				$updateTkDataRawLateMins = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.late_mins = '" . $minutesEquivalent . "', tkdataraw.username='".$username."' WHERE empno ='" . $empno . "' AND date = '" . $date . "' AND time_in = '" . $time . "';");
				$queryUpdate = $this->query("UPDATE db_otks.tktardiness SET nooflate ='" . $nooflate . "', noofmins ='" . $nomins . "' WHERE empno = '" .$empno . "' AND month = '" . substr($date, 5,2) . "';");					//echo "UPDATE db_otks.tktardiness SET nooflate ='" . $nooflate . "', noofmins ='" . $noofmins . "' WHERE empno = '" .$empno . "' AND month = '" . substr($date, 5,2) . "';" . "<br>";
				
			}
			//return $test;
		}
		
		public function getMinuteEquivalent($minutes) {
			if($minutes == 1) {
				$minutesEquivalent = 0.02;
			} else if($minutes == 2) {
				$minutesEquivalent = 0.03;
			} else if($minutes == 3) {
				$minutesEquivalent = 0.05;
			} else if($minutes == 4) {
				$minutesEquivalent = 0.06;
			} else if($minutes == 5) {
				$minutesEquivalent = 0.08;
			} else if($minutes == 6) {
				$minutesEquivalent = 0.1;
			} else if($minutes == 7) {
				$minutesEquivalent = 0.11;
			} else if($minutes == 8) {
				$minutesEquivalent = 0.13;
			} else if($minutes == 9) {
				$minutesEquivalent = 0.15;
			} else if($minutes == 10) {
				$minutesEquivalent = 0.16;
			} else if($minutes == 11) {
				$minutesEquivalent = 0.18;
			} else if($minutes == 12) {
				$minutesEquivalent = 0.2;
			} else if($minutes == 13) {
				$minutesEquivalent = 0.21;
			} else if($minutes == 14) {
				$minutesEquivalent = 0.23;
			} else if($minutes == 15) {
				$minutesEquivalent = 0.25;
			} else if($minutes == 16) {
				$minutesEquivalent = 0.26;
			} else if($minutes == 17) {
				$minutesEquivalent = 0.28;
			} else if($minutes == 18) {
				$minutesEquivalent = 0.3;
			} else if($minutes == 19) {
				$minutesEquivalent = 0.31;
			} else if($minutes == 20) {
				$minutesEquivalent = 0.33;
			} else if($minutes == 21) {
				$minutesEquivalent = 0.35;
			} else if($minutes == 22) {
				$minutesEquivalent = 0.36;
			} else if($minutes == 23) {
				$minutesEquivalent = 0.38;
			} else if($minutes == 24) {
				$minutesEquivalent = 0.4;
			} else if($minutes == 25) {
				$minutesEquivalent = 0.41;
			} else if($minutes == 26) {
				$minutesEquivalent = 0.43;
			} else if($minutes == 27) {
				$minutesEquivalent = 0.45;
			} else if($minutes == 28) {
				$minutesEquivalent = 0.46;
			} else if($minutes == 29) {
				$minutesEquivalent = 0.48;
			} else if($minutes == 30) {
				$minutesEquivalent = 0.5;
			} else if($minutes == 31) {
				$minutesEquivalent = 0.51;
			} else if($minutes == 32) {
				$minutesEquivalent = 0.53;
			} else if($minutes == 33) {
				$minutesEquivalent = 0.55;
			} else if($minutes == 34) {
				$minutesEquivalent = 0.56;
			} else if($minutes == 35) {
				$minutesEquivalent = 0.58;
			} else if($minutes == 36) {
				$minutesEquivalent = 0.6;
			} else if($minutes == 37) {
				$minutesEquivalent = 0.61;
			} else if($minutes == 38) {
				$minutesEquivalent = 0.63;
			} else if($minutes == 39) {
				$minutesEquivalent = 0.65;
			} else if($minutes == 40) {
				$minutesEquivalent = 0.66;
			} else if($minutes == 41) {
				$minutesEquivalent = 0.68;
			} else if($minutes == 42) {
				$minutesEquivalent = 0.7;
			} else if($minutes == 43) {
				$minutesEquivalent = 0.71;
			} else if($minutes == 44) {
				$minutesEquivalent = 0.73;
			} else if($minutes == 45) {
				$minutesEquivalent = 0.75;
			} else if($minutes == 46) {
				$minutesEquivalent = 0.76;
			} else if($minutes == 47) {
				$minutesEquivalent = 0.78;
			} else if($minutes == 48) {
				$minutesEquivalent = 0.8;
			} else if($minutes == 49) {
				$minutesEquivalent = 0.81;
			} else if($minutes == 50) {
				$minutesEquivalent = 0.83;
			} else if($minutes == 51) {
				$minutesEquivalent = 0.85;
			} else if($minutes == 52) {
				$minutesEquivalent = 0.86;
			} else if($minutes == 53) {
				$minutesEquivalent = 0.88;
			} else if($minutes == 54) {
				$minutesEquivalent = 0.9;
			} else if($minutes == 55) {
				$minutesEquivalent = 0.91;
			} else if($minutes == 56) {
				$minutesEquivalent = 0.93;
			} else if($minutes == 57) {
				$minutesEquivalent = 0.95;
			} else if($minutes == 58) {
				$minutesEquivalent = 0.96;
			} else if($minutes == 59) {
				$minutesEquivalent = 0.98;
			} else if($minutes == 60) {
				$minutesEquivalent = 1;
			}
			return $minutesEquivalent;
		}
		//end
		public function getMinutesEquivalent($minutes) {
			$minutesEquivalent = round( ( ($minutes/60) - 0.002 ), 2 );
			if($minutesEquivalent == '0.01' || $minutesEquivalent == '.01'){
				$minutesEquivalent = '0.02';
			}
			return $minutesEquivalent;
		}
		
		// Manual Updating Module
		public function getRecordforManualUpdating($dateFrom, $dateTo) {
			$query = "SELECT tkdataraw.pkid, tkdataraw.empno, tbl_EmployeeInfo.LastName, tbl_EmployeeInfo.FirstName, tbl_Department.Department, tbl_Section.Section,
			tkdataraw.date, tkdataraw.time_in, tkdataraw.date_out,tkdataraw.time_out, tkdataraw.absent, tkdataraw.undertime, tkdataraw.sl, tkdataraw.vl
			tkdataraw.nightdiff FROM db_hris.tbl_EmployeeInfo INNER JOIN db_hris.tbl_Department ON db_hris.tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment
			INNER JOIN db_hris.tbl_Section ON db_hris.tbl_Section.pkid = tbl_EmployeeInfo.fkSection INNER JOIN db_otks.tkdataraw ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno
			WHERE tkdataraw.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' ORDER BY tkdataraw.date;";
			return $this->query($query);
		}
		
		public function updateRecordManualUpdating($pkid, $empno, $dateIn, $timeIn, $modeIn, $dateOut, $timeOut, $modeOut, $regHrs, $otHrs, $absent, $undertime, $slVlCode, $slVlHr, $ot1, $ot2, $ot2a, $ot3, $ot3a, $ot4, $ot4a, $ot5, $ot5a, $ot6, $ot6a, $nightdiff, $aut) {
			$format = 'Y-m-d'; 
			$today = date ($format ); //today;
			if($slVlCode == '1') {
				$query = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.date = '".$dateIn."', tkdataraw.time_in='".$timeIn."', tkdataraw.mode_in='".$modeIn."', tkdataraw.lastupdate='".$today."',
				tkdataraw.date_out='".$dateOut."',tkdataraw.time_out='".$timeOut."', mode_out='".$modeOut."', tkdataraw.reg_hours='".$regHrs."', tkdataraw.ot_credit='".$otHrs."',tkdataraw.absent='".$absent."',tkdataraw.under_mins='".$undertime
				."',tkdataraw.sl='".$slVlHr."',tkdataraw.vl='0',tkdataraw.nightdiff='".$nightdiff."', tkdataraw.ot1='".$ot1."', tkdataraw.ot2='".$ot2."', tkdataraw.ot2a='".$ot2a."', tkdataraw.ot3='".$ot3."', tkdataraw.ot3a='".$ot3a.
				"', tkdataraw.ot4='".$ot4."', tkdataraw.ot4a='".$ot4a."', tkdataraw.ot5='".$ot5."', tkdataraw.ot5a='".$ot5a."', tkdataraw.ot6='".$ot6."', tkdataraw.ot6a='".$ot6a."', tkdataraw.aut='".$aut."' WHERE tkdataraw.pkid='".$pkid."' AND tkdataraw.empno ='".$empno."';");
			} else {
				$query = $this->query("UPDATE db_otks.tkdataraw SET tkdataraw.date = '".$dateIn."', tkdataraw.time_in='".$timeIn."', tkdataraw.mode_in='".$modeIn."', tkdataraw.lastupdate='".$today."',
				tkdataraw.date_out='".$dateOut."',tkdataraw.time_out='".$timeOut."', mode_out='".$modeOut."', tkdataraw.reg_hours='".$regHrs."', tkdataraw.ot_credit='".$otHrs."',tkdataraw.absent='".$absent."',tkdataraw.under_mins='".$undertime
				."',tkdataraw.vl='".$slVlHr."',tkdataraw.sl='0',tkdataraw.nightdiff='".$nightdiff."', tkdataraw.ot1='".$ot1."', tkdataraw.ot2='".$ot2."', tkdataraw.ot2a='".$ot2a."', tkdataraw.ot3='".$ot3."', tkdataraw.ot3a='".$ot3a.
				"', tkdataraw.ot4='".$ot4."', tkdataraw.ot4a='".$ot4a."', tkdataraw.ot5='".$ot5."', tkdataraw.ot5a='".$ot5a."', tkdataraw.ot6='".$ot6."', tkdataraw.ot6a='".$ot6a."', tkdataraw.aut='".$aut."' WHERE tkdataraw.pkid='".$pkid."' AND tkdataraw.empno ='".$empno."';");
			}
			return $query;
		}
		
		//end
		
		//EOM Module
		public function getEOMEmpNo($dateFrom, $dateTo) {
			$query = $this->query("SELECT DISTINCT(tkdataraw.empno), tbl_EmployeeInfo.SalaryType FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo 
			ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' ORDER BY tkdataraw.empno;");
			return $query;
		}
		
		public function getEOMAbsent($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.absent FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		}
		
		public function getEOMUnderTimeLate($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.under_mins, tkdataraw.under_conv, tkdataraw.late_mins, tkdataraw.late_conv FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		}
		
		public function getEOMNightDiff($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.nightdiff FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		}
		
		public function getEOMSL($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.leavenum, tkdataraw.leavecode FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			/*
			if($query->num_rows==0) {
				return "0";
			} else {
				return $query;
			}*/
			return $query;
		}
		
		public function getEOMVL($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.leavenum, tkdataraw.leavecode FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			/*if($query->num_rows==0) {
				return "0";
			} else {
				return $query;
			}*/
			return $query;
		}
		
		public function getEOMOT1($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot1 FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' AND `tkdataraw`.`mode_in` != 'OB';");
			return $query;
		}
		
		public function getEOMOT2($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot2 FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT2a($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot2a FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT3($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot3 FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT3a($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot3a FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT4($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot4 FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT4a($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot4a FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT5($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot5 FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT5a($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot5a FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT6($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot6 FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOMOT6a($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.ot6a FROM db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON 
			tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."';");
			return $query;
		} 
		
		public function getEOM($empno, $dateFrom, $dateTo) {
			$query = $this->query("SELECT tkdataraw.empno, tbl_EmployeeInfo.SalaryType, tkdataraw.absent, tkdataraw.under_mins, tkdataraw.nightdiff, tkdataraw.leavecode, tkdataraw.leavenum, 
			tkdataraw.ot1, tkdataraw.ot2, tkdataraw.ot2a, tkdataraw.ot3, tkdataraw.ot3a, tkdataraw.ot4, tkdataraw.ot4a, tkdataraw.ot5, tkdataraw.ot5a, tkdataraw.ot6, tkdataraw.ot6a FROM  
			db_otks.tkdataraw INNER JOIN db_hris.tbl_EmployeeInfo ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=1 AND tkdataraw.empno='".$empno."' AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' ORDER BY tkdataraw.empno;");
			return $query;
		}
		
		public function total_all_eom($empno,$date_in,$date_out){
			$query = "SELECT 
						`t1`.`empno` AS `empno`, 
						SUM(`t1`.`absent`) AS `absent`, 
						SUM(`t1`.`under_conv`) + SUM(`t1`.`late_conv`) AS `undertime`,
						SUM(`t1`.`nightdiff`) AS `nightdiff`,
						SUM(`t1`.`ot1`) AS `ot1`,
						SUM(`t1`.`ot2`) AS `ot2`,
						SUM(`t1`.`ot2a`) AS `ot2a`,
						SUM(`t1`.`ot3`) AS `ot3`,
						SUM(`t1`.`ot3a`) AS `ot3a`,
						SUM(`t1`.`ot4`) AS `ot4`,
						SUM(`t1`.`ot4a`) AS `ot4a`,
						SUM(`t1`.`ot5`) AS `ot5`,
						SUM(`t1`.`ot5a`) AS `ot5a`,
						SUM(`t1`.`ot6`) AS `ot6`
					FROM 
						`tkdataraw` `t1` 
					WHERE
						`t1`.`empno` = '$empno' AND (`t1`.`date` BETWEEN '$date_in' AND '$date_out') 
					AND `t1`.`mode_in` != 'OB';";
			return $this->query($query);
		}
		
		public function getIndividualTimeCardEmpNo($dateFrom, $dateTo) {
			$query = "SELECT 
							DISTINCT(tkdataraw.empno), 
							tbl_EmployeeInfo.LastName, 
							tbl_EmployeeInfo.FirstName, 
							tbl_EmployeeInfo.MiddleName, 
							tbl_Department.Department 
						FROM 
							db_hris.tbl_EmployeeInfo 
						INNER JOIN 
							db_otks.tkdataraw 
						ON 
							tbl_EmployeeInfo.EmpNo = tkdataraw.empno
						INNER JOIN 
							db_hris.tbl_Department 
						ON 
							tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment 
						WHERE 
							tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' 
						AND 
							tkdataraw.status=1 ORDER BY tbl_Department.Department, tkdataraw.date;";
			return $this->query($query);
		}
		public function getIndividualTimeCardEmpNoe($dateFrom, $dateTo) {
			$query = "SELECT 
							DISTINCT(tkdataraw.empno), 
							tbl_EmployeeInfo.LastName, 
							tbl_EmployeeInfo.FirstName, 
							tbl_EmployeeInfo.MiddleName, 
							tbl_Department.Department 
						FROM 
							db_hris.tbl_EmployeeInfo 
						INNER JOIN 
							db_otks.tkdataraw 
						ON 
							tbl_EmployeeInfo.EmpNo = tkdataraw.empno
						INNER JOIN 
							db_hris.tbl_Department 
						ON 
							tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment 
						WHERE 
							tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' AND tkdataraw.empno = 'F036'
						AND 
							tkdataraw.status=1 ORDER BY tbl_Department.Department AND tkdataraw.date;";
			return $this->query($query);
		}
		public function getIndividualTimeCard($empno, $dateFrom, $dateTo) {
			$query = "SELECT tkdataraw.date, tkdataraw.time_in, tkdataraw.time_out, tkdataraw.ot1, tkdataraw.ot2, tkdataraw.ot2a, tkdataraw.ot3, tkdataraw.ot3a, tkdataraw.ot4, tkdataraw.ot4a, tkdataraw.ot5, tkdataraw.ot5a, tkdataraw.ot6, tkdataraw.ot6a,
			tkdataraw.under_mins, tkdataraw.late_conv, tkdataraw.under_conv, tkdataraw.nightdiff, tkdataraw.absent, tkdataraw.leavecode, tkdataraw.leavenum 
			FROM 
				db_hris.tbl_EmployeeInfo 
			INNER JOIN 
				db_otks.tkdataraw 
			ON 
				tbl_EmployeeInfo.EmpNo = tkdataraw.empno
			INNER JOIN 
				db_hris.tbl_Department 
			ON 
				tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment 
			WHERE tkdataraw.empno =  '".$empno."'
			AND tkdataraw.date BETWEEN '".$dateFrom."' AND '".$dateTo."' AND tkdataraw.status=1
			AND tkdataraw.mode_in != 'OB' 
			ORDER BY tkdataraw.date ASC;";
			// return $query;
			return $this->query($query);
		}
		//end
		
		public function getIndividualTimeCard_late_file($empno, $dateFrom, $dateTo) {
			$query = "SELECT `raw`.`empno`, `tco`.* FROM `tbl_leave_cutoff_date` `tco` INNER JOIN `tkdataraw` `raw` ON `tco`.`fk_tkdataraw` = `raw`.`pkid` WHERE `tco`.`date_of_leave` < '$dateFrom' AND `raw`.`empno` = '$empno' AND `tco`.`cutoff_date` BETWEEN '$dateFrom' AND '$dateTo';";
			$result = $this->query($query);
			$pkid = array();
			while( $row = mysqli_fetch_array($result) ){
				$pkid[] = $row['fk_tkdataraw'];
			}
			if(count($pkid) != 0 ){
				$query  = "SELECT tkdataraw.date, tkdataraw.time_in, tkdataraw.time_out, tkdataraw.ot1, tkdataraw.ot2, tkdataraw.ot2a, tkdataraw.ot3, tkdataraw.ot3a, tkdataraw.ot4, tkdataraw.ot4a, tkdataraw.ot5, tkdataraw.ot5a, tkdataraw.ot6, tkdataraw.ot6a,
				tkdataraw.under_mins, tkdataraw.late_conv, tkdataraw.under_conv, tkdataraw.nightdiff, tkdataraw.absent, tkdataraw.leavecode, tkdataraw.leavenum 
				FROM 
					db_hris.tbl_EmployeeInfo 
				INNER JOIN 
					db_otks.tkdataraw 
				ON 
					tbl_EmployeeInfo.EmpNo = tkdataraw.empno
				INNER JOIN 
					db_hris.tbl_Department 
				ON 
					tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment 
				WHERE "; 
				$array_pkid_query = array();
				for($x=0;$x<count($pkid);$x++){
					$array_pkid_query[] = "tkdataraw.pkid =  '".$pkid[$x]."' ";
				}
				$query .= " ";
				$query .= implode(' OR ',$array_pkid_query);
				$query .= " ";
				$query .= "AND 
					tkdataraw.status=1
				ORDER BY 
					tkdataraw.date ASC;";
				return $this->query($query);
			}
			return 0;
		}
		
		public function getEom_late_file_ot( $empno, $dateFrom, $dateTo ){
			$query = "SELECT * FROM `vw_late_ot` WHERE `empno` = '$empno' AND `ot_date_paid` BETWEEN '$dateFrom' AND '$dateTo' ORDER BY ot_date ASC;";
			$result = $this->query($query);			
			if( $result->num_rows == 0 ){
				return 0;
			} else {
				$record = array();
				while( $row = mysqli_fetch_array($result) ){
					$record['ot_date'][] 		= $row['ot_date'];
					$record['ot_date_paid'][] 	= $row['ot_date_paid'];
					$record['empno'][] 			= $row['empno'];
					$record['ot_count'][] 		= $row['ot_count'];
					$record['ot_type'][] 		= $row['ot_type'];
				}
				return $record;
			}			
		}
				
		public function getEom_late_file($empno, $dateFrom, $dateTo) {
			// $query = "SELECT `raw`.`empno`, `raw`.`leavecode`, `tco`.* FROM `tbl_leave_cutoff_date` `tco` INNER JOIN `tkdataraw` `raw` ON `tco`.`fk_tkdataraw` = `raw`.`pkid` WHERE `tco`.`date_of_leave` < '$dateFrom' AND `raw`.`empno` = '$empno' AND `tco`.`cutoff_date` BETWEEN '$dateFrom' AND '$dateTo';";
			$query = "SELECT `raw`.`empno`, `tco`.* FROM `tbl_leave_cutoff_date` `tco` INNER JOIN `tkdataraw` `raw` ON `tco`.`fk_tkdataraw` = `raw`.`pkid` WHERE `tco`.`date_of_leave` < '$dateFrom' AND `raw`.`empno` = '$empno' AND `tco`.`cutoff_date` BETWEEN '$dateFrom' AND '$dateTo' ORDER BY date_of_leave;";
			$result = $this->query($query);
			if($result->num_rows == 0) {
				return 0;
			} else {
				$pkid      = array();
				$leavecode = array();
				while( $row = mysqli_fetch_array($result) ){
					$pkid[] = $row['fk_tkdataraw'];
				}
				$query  = "SELECT tkdataraw.date, tkdataraw.time_in, tkdataraw.time_out, tkdataraw.ot1, tkdataraw.ot2, tkdataraw.ot2a, tkdataraw.ot3, tkdataraw.ot3a, tkdataraw.ot4, tkdataraw.ot4a, tkdataraw.ot5, tkdataraw.ot5a, tkdataraw.ot6, tkdataraw.ot6a,
						tkdataraw.under_mins, tkdataraw.late_conv, tkdataraw.under_conv, tkdataraw.nightdiff, tkdataraw.absent, tkdataraw.leavecode, tkdataraw.leavenum, tkdataraw.status 
						FROM 
							db_hris.tbl_EmployeeInfo 
						INNER JOIN 
							db_otks.tkdataraw 
						ON 
							tbl_EmployeeInfo.EmpNo = tkdataraw.empno
						INNER JOIN 
							db_hris.tbl_Department 
						ON 
							tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment 
						WHERE "; 
				$array_pkid_query = array();
				for($x=0;$x<count($pkid);$x++){
					$array_pkid_query[] = "tkdataraw.pkid =  '".$pkid[$x]."' ";
				}
				$query .= " ";
				$query .= implode(' OR ',$array_pkid_query);
				$query .= " ";
				$query .= "AND 
					tkdataraw.status=1
				ORDER BY 
					tkdataraw.date ASC;";
				return $this->query($query);				
			}
			
		}
		
		//function for holiday. It is use to delete the record who marked absent during holiday season :p
		public function deleteRecordHoliday($date) {
			$query = $this->query("DELETE FROM tkdataraw WHERE pkid=0 AND date='".$date."';");
		}
		
		//Approver Module
		public function approveAttendance($stat, $pkid, $username, $empno, $dateIn) {
			$query = "UPDATE db_otks.tkdataraw SET tkdataraw.status='".$stat."', username='".$username."', lastupdate='".date('Y-m-d')."' WHERE tkdataraw.pkid='".$pkid."' and tkdataraw.empno='".$empno."' AND tkdataraw.date='".$dateIn."';";
			$this->query($query);
		}
		
		//Guard Module
		public function getRawDataGuard($username, $dateFrom, $dateTo) {
			$query = "SELECT tkdataraw.pkid,tbl_EmployeeInfo.FirstName,tbl_EmployeeInfo.LastName, tbl_Position.Position, tbl_Department.Department, tbl_Section.Section,tkdataraw.empno, tkdataraw.date, 
			tkdataraw.time_in, tkdataraw.mode_in, tkdataraw.date_out,tkdataraw.time_out, tkdataraw.mode_out, tkdataraw.status, tkdataraw.approver FROM db_hris.tbl_EmployeeInfo
			INNER JOIN db_hris.tbl_Department ON db_hris.tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment
			INNER JOIN db_hris.tbl_Section ON db_hris.tbl_Section.pkid = tbl_EmployeeInfo.fkSection 
			INNER JOIN db_hris.tbl_Position ON tbl_Position.pkid = tbl_EmployeeInfo.fkPosition 
			INNER JOIN db_otks.tkdataraw ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.username='".$username."' AND tkdataraw.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
			ORDER BY tkdataraw.lastupdate;";
			return $this->query($query);
		}
		
		public function addRawDataGuard($empNo, $date, $timein, $modein, $dateout, $timeout, $modeout, $username, $approverName, $status) {
			$check = $this->query("SELECT * FROM db_otks.tkdataraw WHERE empno='".$empNo."' AND date='".$date."';");
			if($check->num_rows == 0) {
				$new_pkid  = $this->new_pkid();
				$new_pkid += 1;
				$attendanceDataRaw = $this->query("INSERT INTO db_otks.tkdataraw VALUES('$new_pkid','" . date('Y-m-d') . "','" . $empNo . "','" . $date . "','" . $timein . ":00','". $modein ."','".$dateout."','".$timeout.":00','".$modeout."','','',0,0,0,0,0,0,0,0,0,0,'',0, 0, 0, 0, 0,0,0,0,0,0,0,0,0,0,0, '', '".$status."', '".$approverName."', '".$username."');");	//new data
				return $check;
			} else {
				return $check;
			}
		}
		
		public function new_pkid(){
			$new_pkid = $this->query('SELECT MAX(pkid) AS max_pkid FROM db_otks.tkdataraw;');
			$row = mysqli_fetch_array($new_pkid);
			return $row['max_pkid'];
		}
		
		public function getApproverName($empNo) {
			$selectSection = $this->query("SELECT tbl_EmployeeInfo.fkSection FROM db_hris.tbl_EmployeeInfo WHERE tbl_EmployeeInfo.EmpNo='".$empNo."';");
			while($rowSection = mysqli_fetch_array($selectSection)) {
				$section = $rowSection['fkSection'];
			}
			$query = "SELECT DISTINCT(tbl_useraccnt.email_add), tbl_EmployeeInfo.EmpNo, tbl_EmployeeInfo.MiddleName, tbl_EmployeeInfo.LastName,tbl_EmployeeInfo.FirstName, tbl_approver.fkemployee FROM db_hris.tbl_EmployeeInfo,db_acl.tbl_approver, db_acl.tbl_useraccnt 
				WHERE tbl_approver.fkemployee=tbl_useraccnt.pkid and tbl_useraccnt.fkEmployee=tbl_EmployeeInfo.pkid and  tbl_approver.fksection='".$section."'";
			return $this->query($query);
		}
		
		//sendEmail
		public function getEmailAdd($key, $cont1, $cont2) {
			if($key == "EN") {
				$query = "SELECT tbl_useraccnt.email_add FROM db_hris.tbl_EmployeeInfo INNER JOIN db_acl.tbl_useraccnt ON tbl_useraccnt.fkEmployee = tbl_EmployeeInfo.pkid WHERE tbl_EmployeeInfo.EmpNo='".$content."';";
				return $this->query($query);
			} else if($key == "NAME") {
				$query = "SELECT tbl_useraccnt.email_add FROM db_hris.tbl_EmployeeInfo INNER JOIN db_acl.tbl_useraccnt ON tbl_useraccnt.fkEmployee = tbl_EmployeeInfo.pkid WHERE tbl_EmployeeInfo.FirstName LIKE '%".$cont1."%' AND tbl_EmployeeInfo.LastName LIKE '%".$cont2."%'";
				return $this->query($query);
				//return $query;
			}
			
		}
		
		public function getRawDataApprover($approverName, $dateFrom, $dateTo) {
			$query = "SELECT tkdataraw.pkid,tbl_EmployeeInfo.FirstName,tbl_EmployeeInfo.LastName, tbl_Position.Position, tbl_Department.Department, tbl_Section.Section,tkdataraw.empno, tkdataraw.date, 
			tkdataraw.time_in, tkdataraw.mode_in, tkdataraw.date_out,tkdataraw.time_out, tkdataraw.mode_out, tkdataraw.status, tkdataraw.approver FROM db_hris.tbl_EmployeeInfo
			INNER JOIN db_hris.tbl_Department ON db_hris.tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment
			INNER JOIN db_hris.tbl_Section ON db_hris.tbl_Section.pkid = tbl_EmployeeInfo.fkSection 
			INNER JOIN db_hris.tbl_Position ON tbl_Position.pkid = tbl_EmployeeInfo.fkPosition 
			INNER JOIN db_otks.tkdataraw ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.approver='".$approverName."' AND tkdataraw.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
			ORDER BY tkdataraw.lastupdate;";
			return $this->query($query);
		}
		
		public function getPendingAttendance($dateFrom, $dateTo) {
			$query = "SELECT tkdataraw.pkid,tbl_EmployeeInfo.FirstName,tbl_EmployeeInfo.LastName, tbl_Position.Position, tbl_Department.Department, tbl_Section.Section,tkdataraw.empno, tkdataraw.date, 
			tkdataraw.time_in, tkdataraw.mode_in, tkdataraw.date_out,tkdataraw.time_out, tkdataraw.mode_out, tkdataraw.status, tkdataraw.approver FROM db_hris.tbl_EmployeeInfo
			INNER JOIN db_hris.tbl_Department ON db_hris.tbl_Department.pkid = tbl_EmployeeInfo.fkDepartment
			INNER JOIN db_hris.tbl_Section ON db_hris.tbl_Section.pkid = tbl_EmployeeInfo.fkSection 
			INNER JOIN db_hris.tbl_Position ON tbl_Position.pkid = tbl_EmployeeInfo.fkPosition 
			INNER JOIN db_otks.tkdataraw ON tbl_EmployeeInfo.EmpNo = tkdataraw.empno WHERE tkdataraw.status=0 AND tkdataraw.date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
			ORDER BY tkdataraw.lastupdate;";
			return $this->query($query);
		}
		
		public function getUserInfo($username) {
			$query = "SELECT tbl_useraccnt.LN, tbl_useraccnt.FN FROM db_acl.tbl_useraccnt WHERE tbl_useraccnt.username='".$username."';";
			return $this->query($query);
		}
		
		public function load_employee_information($empno){
			$query = "SELECT * FROM db_hris.vwEmpInfo WHERE EmpNo = '$empno';";
			return $this->query($query);
		}
		
		public function test_raw_data($empno, $date_in){
			$query = "SELECT * FROM `tkdataraw` WHERE `empno` = '$empno' AND `date` = '$date_in';";
			$result = $this->query($query);
			return $result;
			// return $query;
		}
		
		public function count_unfiled_straight_hours( $start_date, $end_date ){
			$query = "SELECT DISTINCT(`empno`) AS `EmpNo` FROM `db_hris`.`tbl_EmployeeInfo` WHERE `SalaryType` != '3' AND (`EmpStatus` = '1' OR `EmpStatus` = '3') ORDER BY `empno`";
			$result = $this->query($query);
			
			$array_date = $this->array_date($start_date,$end_date);
			$array_emp_info = array();
			while($row = mysqli_fetch_array($result)){
				/* fetch employee time difference */
				for($x=0;$x<count($array_date);$x++){
					$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `date_out`, `time_out` FROM `tkdataraw` WHERE `empno` = '".$row['EmpNo']."' AND `date` = '".$array_date[$x]."';";
					$result2 	= $this->query($query);
					while($row_emp_info = mysqli_fetch_array($result2)){
						if($row_emp_info['date'] == '' || $row_emp_info['date_out'] == ''){
							continue;
						}
						$date_time_in	= $row_emp_info['date'] . ' ' . $row_emp_info['time_in'];
						$date_time_out	= $row_emp_info['date_out'] . ' ' . $row_emp_info['time_out'];
						$time_diff 	= strtotime($date_time_out) - strtotime($date_time_in);
						$hour		= floor(($time_diff / 60) / 60);
						$minute		= ($time_diff / 60) % 60;
						$time_diff  = $hour . ' hour and ' . $minute . ' minute';
						if($hour < 15){
							continue;
						}else{
							$validate_straight = $this->validate_filled_straight_hours($row_emp_info['empno'],$row_emp_info['date']);
							if($validate_straight){
								continue;
							}
						}
						$array_emp_info['empno'][] 			= $row_emp_info['empno'];
						$array_emp_info['pkid'][] 			= $row_emp_info['pkid'];
						$array_emp_info['date'][] 			= $row_emp_info['date'];
						$array_emp_info['time_in'][] 		= $row_emp_info['time_in'];
						$array_emp_info['date_out'][] 		= $row_emp_info['date_out'];
						$array_emp_info['time_out'][] 		= $row_emp_info['time_out'];
						$array_emp_info['date_time_in'][] 	= $date_time_in;
						$array_emp_info['date_time_out'][] 	= $date_time_out;
						$array_emp_info['time_diff'][] 		= $time_diff;
					}
				}
			}
			
			return $array_emp_info;

		}
		
		public function array_date($start_date,$end_date){
			$start_date = strtotime($start_date);
			$end_date 	= strtotime($end_date);
			$array_date = array();
			/* place all date in an array */
			while ($start_date <= $end_date){
				$array_date[] = date('Y-m-d',$start_date);
				$start_date = strtotime('+1 day',$start_date);
			}
			return $array_date;
		}
		
		public function validate_filled_straight_hours($empno,$date_in){
			$query = "SELECT 
						 `td`.`pkid` 
						FROM 
						 `tkstraight_details` `td`
						INNER JOIN
						 `tkstraight_main` `tm`
						ON
						 `td`.`fkstraight_main` = `tm`.`pkid`
						WHERE 
						 `td`.`empno`='$empno' 
						AND 
						 `td`.`date_from` = '$date_in' 
						AND 
						 `tm`.`status` = '2';";
			$result = $this->query($query);
			$validate = false;
			while($row = mysqli_fetch_array($result)){
				$validate = true;
			}
			return $validate;
		}
		
		public function fix_attendance_time_in($pkid){
			/* Select the first record */
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
			$result 	= $this->query($query);
			
			while($row = mysqli_fetch_array($result)){
				$empno 		= $row['empno'];
				$date  		= $row['date'];
				$time_in 	= $row['time_in'];
				$mode_in 	= $row['mode_in'];
				$date_out 	= $row['date_out'];
				$time_out 	= $row['time_out'];
				$mode_out 	= $row['mode_out'];
			}
			
			/* check if date in is blank */
			if( $date == '' ){
				$date = $date_out;
			}
			
			require_once('./functions/php_functions.php');
			$TimeAndDate = new TimeAndDate();
			$TimeAndDate->date_request = $date;
			$array_date = $TimeAndDate->separate_month_day_year();
			
			/* get the date of first pkid */
			if($array_date['day'] >= 30 || $array_date['day'] <= 14){
				if($array_date['day'] == 30 || $array_date['day'] == 31){
					$end_loop_date = $array_date['year'].'-'.($array_date['month']+1).'-14';
				}else{
					$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-14';
				}
			}else{
				$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-29';
			}
						
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `empno` = '$empno' AND `pkid` >= '$pkid' AND (`date` >= '$date') ORDER BY `date`;";
			$result 	= $this->query($query);
			$array_emp_info = array();

			while($row = mysqli_fetch_array($result)){
				if( strtotime($row['date']) > strtotime($end_loop_date) ){
					break;
				}
				/* add to array */
				if($row['pkid'] == $pkid){
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= '';
					$array_emp_info['time_in'][] 		= '';
					$array_emp_info['mode_in'][] 		= '';
					$array_emp_info['date_out'][] 		= $row['date'];
					$array_emp_info['time_out'][] 		= $row['time_in'];
					$array_emp_info['mode_out'][] 		= $row['mode_in'];
					/* save the value to be moved */
					$new_date_in 	= $row['date_out'];
					$new_time_in 	= $row['time_out'];
					$new_mode_in 	= $row['mode_out'];
					
				}else{
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $new_date_in;
					$array_emp_info['time_in'][] 		= $new_time_in;
					$array_emp_info['mode_in'][] 		= $new_mode_in;
					$array_emp_info['date_out'][] 		= $row['date'];
					$array_emp_info['time_out'][] 		= $row['time_in'];
					$array_emp_info['mode_out'][] 		= $row['mode_in'];
					/* save the value to be moved */
					$new_date_in 	= $row['date_out'];
					$new_time_in 	= $row['time_out'];
					$new_mode_in 	= $row['mode_out'];
				}
				
			}
			return $array_emp_info;
		}
		
		public function fix_attendance_time_out($pkid){
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
			$result 	= $this->query($query);
			
			while($row = mysqli_fetch_array($result)){
				$empno 		= $row['empno'];
				$date  		= $row['date'];
				$time_in 	= $row['time_in'];
				$mode_in 	= $row['mode_in'];
				$date_out 	= $row['date_out'];
				$time_out 	= $row['time_out'];
				$mode_out 	= $row['mode_out'];
			}
			
			/* check if date in is blank */
			if( $date == '' ){
				$date = $date_out;
			}
			
			require_once('./functions/php_functions.php');
			$TimeAndDate = new TimeAndDate();
			$TimeAndDate->date_request = $date;
			$array_date = $TimeAndDate->separate_month_day_year();
			
			/* get the date of first pkid */
			if($array_date['day'] >= 30 || $array_date['day'] <= 14){
				if($array_date['day'] == 30 || $array_date['day'] == 31){
					$end_loop_date = $array_date['year'].'-'.($array_date['month']+1).'-14';
				}else{
					$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-14';
				}
			}else{
				$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-29';
			}
			
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `empno` = '$empno' AND `pkid` >= '$pkid' AND (`date` >= '$date') ORDER BY `date`;";
			$result 	= $this->query($query);
			$array_emp_info = array();
			
			while($row = mysqli_fetch_array($result)){
				// $array_emp_info['end_loop_date'] = $row['date']. " vs " .$end_loop_date;
				if( strtotime($row['date']) > strtotime($end_loop_date) ){
					break;
				}
				/* add to array */
				if($row['pkid'] == $pkid){
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $row['date'];
					$array_emp_info['time_in'][] 		= $row['time_in'];
					$array_emp_info['mode_in'][] 		= $row['mode_in'];
					$array_emp_info['date_out'][] 		= '';
					$array_emp_info['time_out'][] 		= '';
					$array_emp_info['mode_out'][] 		= '';
					
					$new_date_in 	= $row['date_out'];
					$new_time_in 	= $row['time_out'];
					$new_mode_in 	= $row['mode_out'];
					
				}else{
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $new_date_in;
					$array_emp_info['time_in'][] 		= $new_time_in;
					$array_emp_info['mode_in'][] 		= $new_mode_in;
					$array_emp_info['date_out'][] 		= $row['date'];
					$array_emp_info['time_out'][] 		= $row['time_in'];
					$array_emp_info['mode_out'][] 		= $row['mode_in'];
					
					$new_date_in 	= $row['date_out'];
					$new_time_in 	= $row['time_out'];
					$new_mode_in 	= $row['mode_out'];
				}
			}
			return $array_emp_info;
		}
		
		public function fix_attendance_time_in_prev($pkid){
			/* Select the first record */
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
			$result 	= $this->query($query);
			
			while($row = mysqli_fetch_array($result)){
				$empno 		= $row['empno'];
				$date  		= $row['date'];
				$time_in 	= $row['time_in'];
				$mode_in 	= $row['mode_in'];
				$date_out 	= $row['date_out'];
				$time_out 	= $row['time_out'];
				$mode_out 	= $row['mode_out'];
			}
			
			require_once('./functions/php_functions.php');
			$TimeAndDate = new TimeAndDate();
			$TimeAndDate->date_request = $date;
			$array_date = $TimeAndDate->separate_month_day_year();
			
			/* get the date of first pkid */
			if($array_date['day'] >= 30 || $array_date['day'] <= 14){
				if($array_date['day'] == 30 || $array_date['day'] == 31){
					$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-30';
				}else{
					$end_loop_date = $array_date['year'].'-'.($array_date['month']+1).'-01';
				}
			}else{
				$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-15';
			}
						
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `empno` = '$empno' AND (`date` BETWEEN '$end_loop_date' AND '$date' ) ORDER BY `date` DESC;";
			$result 	= $this->query($query);
			$array_emp_info = array();

			while($row = mysqli_fetch_array($result)){
				/* if( strtotime($row['date']) > strtotime($date) ){
					break;
				} */
				/* add to array */
				if($row['pkid'] == $pkid){
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $row['date_out'];
					$array_emp_info['time_in'][] 		= $row['time_out'];
					$array_emp_info['mode_in'][] 		= $row['mode_out'];
					$array_emp_info['date_out'][] 		= '';
					$array_emp_info['time_out'][] 		= '';
					$array_emp_info['mode_out'][] 		= '';
					/* save the value to be moved */
					$new_date_out 	= $row['date'];
					$new_time_out 	= $row['time_in'];
					$new_mode_out 	= $row['mode_in'];
				}else{
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $row['date_out'];
					$array_emp_info['time_in'][] 		= $row['time_out'];
					$array_emp_info['mode_in'][] 		= $row['mode_out'];
					$array_emp_info['date_out'][] 		= $new_date_out;
					$array_emp_info['time_out'][] 		= $new_time_out;
					$array_emp_info['mode_out'][] 		= $new_mode_out ;
					/* save the value to be moved */
					$new_date_out 	= $row['date'];
					$new_time_out 	= $row['time_in'];
					$new_mode_out 	= $row['mode_in'];
				}
				
			}
			return $array_emp_info;
		}
		
		public function fix_attendance_time_out_prev($pkid){
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
			$result 	= $this->query($query);
			
			while($row = mysqli_fetch_array($result)){
				$empno 		= $row['empno'];
				$date  		= $row['date'];
				$time_in 	= $row['time_in'];
				$mode_in 	= $row['mode_in'];
				$date_out 	= $row['date_out'];
				$time_out 	= $row['time_out'];
				$mode_out 	= $row['mode_out'];
			}
			
			require_once('./functions/php_functions.php');
			$TimeAndDate = new TimeAndDate();
			$TimeAndDate->date_request = $date;
			$array_date = $TimeAndDate->separate_month_day_year();
			
			/* get the date of first pkid */
			if($array_date['day'] >= 30 || $array_date['day'] <= 14){
				if($array_date['day'] == 30 || $array_date['day'] == 31){
					$end_loop_date = $array_date['year'].'-'.($array_date['month']+1).'-14';
				}else{
					$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-14';
				}
			}else{
				$end_loop_date = $array_date['year'].'-'.$array_date['month'].'-29';
			}
			
			$query 		= "SELECT `pkid`,`empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `empno` = '$empno' AND `pkid` >= '$pkid' ORDER BY `date`;";
			$result 	= $this->query($query);
			$array_emp_info = array();
			
			while($row = mysqli_fetch_array($result)){
				// $array_emp_info['end_loop_date'] = $row['date']. " vs " .$end_loop_date;
				if( strtotime($row['date']) > strtotime($end_loop_date) ){
					break;
				}
				/* add to array */
				if($row['pkid'] == $pkid){
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $row['date'];
					$array_emp_info['time_in'][] 		= $row['time_in'];
					$array_emp_info['mode_in'][] 		= $row['mode_in'];
					$array_emp_info['date_out'][] 		= '';
					$array_emp_info['time_out'][] 		= '';
					$array_emp_info['mode_out'][] 		= '';
					
					$new_date_in 	= $row['date_out'];
					$new_time_in 	= $row['time_out'];
					$new_mode_in 	= $row['mode_out'];
					
				}else{
					$array_emp_info['empno'][] 			= $row['empno'];
					$array_emp_info['pkid'][] 			= $row['pkid'];
					$array_emp_info['date'][] 			= $new_date_in;
					$array_emp_info['time_in'][] 		= $new_time_in;
					$array_emp_info['mode_in'][] 		= $new_mode_in;
					$array_emp_info['date_out'][] 		= $row['date'];
					$array_emp_info['time_out'][] 		= $row['time_in'];
					$array_emp_info['mode_out'][] 		= $row['mode_in'];
					
					$new_date_in 	= $row['date_out'];
					$new_time_in 	= $row['time_out'];
					$new_mode_in 	= $row['mode_out'];
				}
			}
			return $array_emp_info;
		}
		
		/* //////////////////////////////////////////////////////////////////// */
		
		
		/* Count the number of days on date range given */
		public function count_number_of_work_days($date_start,$date_end,$dayoff_status,$array_date){
			$number_of_work_days = 0;
			if($dayoff_status == 0){
				for($x=0;$x<count($array_date);$x++){
					if(date('D',strtotime($array_date[$x])) != 'Sun'){
						$number_of_work_days ++;
					}
				}
			}else{
				/* Loop through all date and count date */
				for($x=0;$x<count($array_date);$x++){
					if(date('D',strtotime($array_date[$x])) != 'Sun'){
						if(date('D',strtotime($array_date[$x])) == 'Sat'){
							$dayoff = $this->check_dayoff_saturday($array_date[$x]);
							if($dayoff == 1){
								continue;
							}
						}
						$number_of_work_days ++;
					}
				}
			}
			return $number_of_work_days;
		}
		
		public function count_tklogs_data($empno,$start_date,$end_date){
			$query = "SELECT COUNT(pkid) AS `rec_count` FROM `tklogs` WHERE `empno` = '$empno' AND (`date` BETWEEN  '$start_date' AND '$end_date');";
			$result = $this->query($query);
			while($row=mysqli_fetch_array($result)){
				return $row['rec_count'];
			}
		}
		
		public function additional_tklogs($empno,$end_date){
			/* check if  */
			$query = "SELECT COUNT(pkid) AS `rec_count` FROM `tklogs` WHERE `empno` = '$empno' AND `date` = '$end_date' AND `mode` = 'OUT';";
		}
		
		public function save_adjusted_time_in_out($array_in_out){
			$message = "";
			for($x=0;$x<count($array_in_out['empno']);$x++){
				$query = "SELECT `mode_in`,`mode_out` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
				$result = $this->query($query);
				$mode_in = "IN";
				$mode_out = "OUT";
				while($row = mysqli_fetch_array($result)){
					if ($row['mode_in'] != ''){
						$mode_in = $row['mode_in'];
					}
					if ($row['mode_out'] != ''){
						$mode_in = $row['mode_out'];
					}
				}
				if( $array_in_out['adj_row'][$x] == 'true'){
					$query  = "UPDATE   ";
					$query .= "	`tkdataraw`";
					$query .= "SET";
					$query .= "	`date` 		= '".$array_in_out['date'][$x]."', ";
					$query .= "	`time_in` 	= '".$array_in_out['time_in'][$x]."', ";
					$query .= "	`mode_in` 	= '".$mode_in."', ";
					$query .= "	`date_out` 	= '".$array_in_out['date_out'][$x]."', ";
					$query .= "	`time_out` 	= '".$array_in_out['time_out'][$x]."', ";
					$query .= "	`mode_out` 	= '".$mode_out."' ";
					$query .= "WHERE ";
					$query .= "	`pkid` = '".$array_in_out['pkid'][$x]."';";
					$this->query($query);
					$message .= $query." || Query:";
					
					$query = "SELECT `pkid`, `empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
					$result = $this->query($query);
					$row = mysqli_fetch_array( $result );
					
					$tklogs_result = $this->add_update_tklogs($row['pkid'],$row['empno'],$row['date'],$row['time_in'],$row['mode_in'],$row['date_out'],$row['time_out'],$row['mode_out'],date('Y-m-d H:i:s'));
				}
			}
			return $query.' logs: '.$tklogs_result;
		}
		
		public function load_all_empno(){
			$query = "SELECT `hris`.`EmpNo` FROM `db_hris`.`tbl_EmployeeInfo` `hris`  WHERE `hris`.`SalaryType` != '3' AND `hris`.`EmpStatus` = '1';";
			return $this->query($query);
		}
		
		public function validate_existing_empno($empno){
			$query = "SELECT `hris`.`EmpNo` FROM `db_hris`.`tbl_EmployeeInfo` `hris`  WHERE `hris`.`EmpNo` = '$empno' AND `hris`.`SalaryType` != '3' AND `hris`.`EmpStatus` = '1';";
			return $this->query($query);
		}
		
		public function save_offset($empno,$date,$username){
			$query = "SELECT `pkid` FROM `tbl_offset` WHERE `empno`='$empno' AND `date` = '$date' AND `logdel` = '0';";
			$result = $this->query($query);
			if( $result->num_rows !=0 ){
				return "Record already exist \n EmpNo  : $empno \n Date      : $date";
			}
			$query = "INSERT INTO `tbl_offset` (`empno`,`date`,`username`) VALUES ('$empno','$date','$username');";
			$this->query($query);
			return "Data has been saved";
		}
		
		public function delete_offset($pkid,$username){
			$date_today = date("Y-m-d H:i:s");
			$query = "UPDATE `tbl_offset` SET `logdel` = '1', `lastupdate` = '$date_today' WHERE `pkid` = '$pkid';";
			$this->query($query);
			return 1;
		}
		
		public function save_holiday($date,$username,$remarks,$type){
			$date_today = "Y-m-d H:i:s";
			$query = "SELECT `pkid` FROM `vw_holiday` WHERE `date` = '$date' AND `logdel` = '0';";
			$result = $this->query($query);
			if( $result->num_rows !=0 ){
				return "Record already exist \n Date  : $date";
			}
			$query = "INSERT INTO `tbl_holiday` (`date`,`username`,`remarks`,`type`) VALUES ('$date','$username','$remarks','$type');";
			$this->query($query);
			return "Holiday has been saved!";
		}
		
		public function delete_holiday($pkid,$username){
			$date_today = "Y-m-d H:i:s";
			$query = "UPDATE `tbl_holiday` SET `logdel` = '1', `lastupdate` = '$date_today', `username` = '$username' WHERE `pkid` = '$pkid';";
			$this->query($query);
			return 1;
		}
		
		public function excel_attendance_export($date_start,$date_end,$empno){
			if($empno != ''){
				$query  = "SELECT DISTINCT(`EmpNo`) FROM `db_hris`.`tbl_EmployeeInfo` WHERE `EmpStatus` != '2' AND `SalaryType` != '3' AND";
				$empno = explode(',',$empno);
				$array_empno_condition = array();
				for($x=0;$x<count($empno);$x++){
					$array_empno_condition[] = "`EmpNo` = '".$empno[$x]."'";
				}
				$query .= " (";
				$query .= implode(' OR ',$array_empno_condition);
				$query .= " ) ";
				$query .= " ORDER BY `EmpNo`";
			}else{
				$query = "SELECT DISTINCT(`EmpNo`) FROM `db_hris`.`tbl_EmployeeInfo` WHERE `EmpStatus` != '2' AND `SalaryType` != '3' ORDER BY `EmpNo`;";
			}			
			$result = $this->query($query);
			$employee_record = array();
			while($row = mysqli_fetch_array($result)){
				
				$next_date 	= $date_start;
				$end_date 	= $date_end;
				
				// $array_otks = $this->select_otks_info($date_start, $date_end, $empno);
				$array_otks = $this->select_otks_info($date_start, $date_end, $row['EmpNo']);
				
				while( $next_date <= $end_date ){
					if( date('D',strtotime($next_date)) == 'Sun' ){
						$date1 = str_replace('-', '/', $next_date);
						$next_date = date('Y-m-d',strtotime($date1 . "+1 days"));
						continue;
					}
					$exist = false;
					for( $x=0;$x<count($array_otks['empno']);$x++){
						$date_in_otks = $array_otks['date'][$x];
						if($date_in_otks == $next_date){
							$exist 	  	= true;
							$pkid	  	= $array_otks['pkid'][$x];
							$empno    	= $array_otks['empno'][$x];
							$date_in  	= $array_otks['date'][$x];
							$time_in  	= $array_otks['time_in'][$x];
							$mode_in  	= $array_otks['mode_in'][$x];
							$date_out  	= $array_otks['date_out'][$x];
							$time_out  	= $array_otks['time_out'][$x];
							$mode_out  	= $array_otks['mode_out'][$x];
						}
					}
					if($exist){
						$employee_record['pkid'][] 		= $pkid;
						$employee_record['empno'][] 	= $empno;
						$employee_record['date_in'][] 	= $date_in;
						$employee_record['time_in'][] 	= $time_in;
						$employee_record['mode_in'][] 	= $mode_in;
						$employee_record['date_out'][] 	= $date_out;
						$employee_record['time_out'][] 	= $time_out;
						$employee_record['mode_out'][] 	= $mode_out;
					}else{
						$employee_record['pkid'][] 		= '';
						$employee_record['empno'][] 	= $row['EmpNo'];
						$employee_record['date_in'][]	= $next_date;
						$employee_record['time_in'][] 	= '';
						$employee_record['mode_in'][] 	= '';
						$employee_record['date_out'][] 	= '';
						$employee_record['time_out'][] 	= '';
						$employee_record['mode_out'][] 	= '';
					}
					$date1 = str_replace('-', '/', $next_date);
					$next_date = date('Y-m-d',strtotime($date1 . "+1 days"));
				}
				
			}
			return $employee_record;
		}
		
		public function select_otks_info($date_in,$date_out,$empno){
			$query = "SELECT `pkid`, `empno`, `date`, `time_in`, `mode_in`, `date_out`, `time_out`, `mode_out` FROM `tkdataraw` WHERE `empno` = '$empno' AND ((`date` BETWEEN '$date_in' AND '$date_out') OR (`date_out` BETWEEN '$date_in' AND '$date_out') );";
			$result = $this->query($query);
			$return = array();
			while( $row = mysqli_fetch_array($result) ){
				if( strtotime($date_in) <= strtotime($row['date']) || $row['date'] != ""){
					$return['pkid'][] 		= $row['pkid'];
					$return['empno'][] 		= $row['empno'];
					$return['date'][]  		= $row['date'];
					$return['time_in'][]  	= $row['time_in'];
					$return['mode_in'][]  	= $row['mode_in'];
					$return['date_out'][]  	= $row['date_out'];
					$return['time_out'][]  	= $row['time_out'];
					$return['mode_out'][]  	= $row['mode_out'];
				}
			}
			// $return['query'][]  	= $query;
			return $return;
		}
		
		public function select_all_distinct_employee(){
			// $query = "SELECT DISTINCT(`empno`) as empno FROM tkdataraw ORDER BY `empno` LIMIT 0,1;";
			$query = "SELECT DISTINCT(`empno`) as empno FROM tkdataraw ORDER BY `empno`;";
			$result = $this->query($query);
			$empno = array();
			while($row = mysqli_fetch_array($result)){
				$empno[] = $row['empno'];
			}
			return $empno;
		}
		
		public function update_raw_data_from_csv($record){
			$record_length = count($record['empno']);
			$msg = '';	
			for($x=0;$x<$record_length;$x++){
				$empno 		= $record['empno'][$x];
				if($record['date_in'][$x] != ''){
					$date_in 	= date('Y-m-d', strtotime($record['date_in'][$x]));
				}else{
					$date_in 	= '';
				}
				if($record['time_in'][$x] != ''){
					$time_in 	= date('H:i:s', strtotime($record['time_in'][$x]));
				}else{
					$time_in = '';
				}
				if($record['date_out'][$x] != ''){
					$date_out 	= date('Y-m-d', strtotime($record['date_out'][$x]));
				}else{
					$date_out 	= '';
				}
				if($record['time_out'][$x] != ''){
					$time_out 	= date('H:i:s', strtotime($record['time_out'][$x]));
				}else{
					$time_out = '';
				}
				$mode_in    = $record['mode_in'][$x];
				$mode_out    = $record['mode_out'][$x];
				$pkid 		= $record['pkid'][$x];
				$date_today	= date('Y-m-d H:i:s');
				if($record['pkid'][$x] == ''){
					continue;
				}
				if($time_in == '' && $date_out == '' && $time_out == ''){
					$query = "UPDATE `tkdataraw` SET `absent` = '1' WHERE `pkid` = '$pkid';";
					$this->query($query);
					$tklogs_result = $this->add_update_tklogs($pkid,$empno,$date_in,$time_in,'',$date_out,$time_out,'',$date_today);
					$msg .= $query.' <br>';
					continue;
				}
				$query = "UPDATE `tkdataraw` SET `date` = '$date_in', `time_in` = '$time_in', `mode_in` = '$mode_in', `date_out` = '$date_out', `time_out` = '$time_out', `mode_out` = '$mode_out', `absent` = '0' WHERE `pkid` = '$pkid';";
				$this->query($query);
				$tklogs_result = $this->add_update_tklogs($pkid,$empno,$date_in,$time_in,$mode_in,$date_out,$time_out,$mode_out,$date_today);
				$msg .= $query. ' <br>';
			}
			return $msg;
		}
		
		public function delete_leave($pkid){
			$query = "DELETE FROM `tbl_leave_cutoff_date` WHERE `pkid` = '$pkid';";
			$this->query($query);
			// return $query;
			return "Deleted";
		}
		
		public function edit_leave_information($pkid,$dol,$tol,$lc,$pl,$cod){
			$query = "UPDATE `tbl_leave_cutoff_date` SET `date_of_leave` = '$dol', `days` = '$lc', `days_paid` = '$pl', cutoff_date = '$cod' WHERE `pkid` = '$pkid';";
			$this->query($query);
			
			$query = "SELECT `fk_tkdataraw` FROM `tbl_leave_cutoff_date` WHERE `pkid` = '$pkid';";
			$result = $this->query($query);
			while( $row = mysqli_fetch_array($result) ){
				$pkid_tkadataraw = $row['fk_tkdataraw'];
			}
			
			/* update tkdataraw */
			$update_query  = "UPDATE db_otks.tkdataraw SET 
								`absent`	='1',
								`leavecode`	='".$tol."',
								`leavenum`	='".$pl."',
								`username`	='".$username."'
							WHERE 
								`pkid`		='".$pkid_tkadataraw."';";
			$updateDatRaw = $this->query($update_query);
			// return $update_query;
			return "Updated";
		}
		
		public function delete_employee_dayoff($empno){
			$query = "DELETE FROM `tkday_off` WHERE `empno` = '$empno';";
			$this->query($query);
			return "Deleted";
		}
		
		public function add_employee_dayoff($empno,$dayoff_type,$dayoff_saturday,$username){
			$empno = $this->real_escape_string($empno);
			$username = $this->real_escape_string($username);
			$date_today = date('Y-m-d H:i:s');
			$query = "SELECT `pkid` FROM `tkday_off` WHERE `empno` = '$empno';";
			$result = $this->query($query);
			if( $result->num_rows != 0){
				return "Record already exist!";
			}
			$query = "INSERT INTO `tkday_off` (`date_time_created`,`created_by`,`empno`,`dayoff_type`,`saturday`,`lastupdate`,`username`) VALUES ('$date_today','$username','$empno','$dayoff_type','$dayoff_saturday','$date_today','$username');";
			$this->query($query);
			return "New record has been saved!";
		}
		
		public function add_yec_training($post){
			$empno = $post['empno'];
			$query = "SELECT `empno` FROM `vw_yec_training` where `empno` = '$empno';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return "Employee already exist!";
			}
			$training_date_end = $post['training_date_end'];
			$date_today = date('Y-m-d H:i:s');
			$username = $post['username'];
			$query = "INSERT INTO `tbl_yec_training` (`empno`,`training_date_end`,`lastupdate`,`username`) VALUES ('$empno','$training_date_end','$date_today','$username');";
			$result = $this->query($query);
			return "Employee has been added!";
		}
		
		public function delete_yec_training($post){
			$pkid 	= $post['pkid'];
			$query 	= "UPDATE `tbl_yec_training` SET `logdel` = '1' WHERE `pkid` = '$pkid';";
			$this->query($query);
			return "record has been deleted";
		}
		
		public function export_tfm($start_date,$end_date){
			$query = "SELECT * FROM `vw_tfm_export` WHERE `C_DATE` BETWEEN '$start_date' AND '$end_date' ORDER BY  `IDNUM` , `C_DATE`;";
			return $this->query($query);
		}
		
		public function leavecode_convertion($str){
			if($str == 1) {
				$timeIn = "SL";
				$timeOut = "";
			} else if($str == 2) {
				$timeIn = "VL";
				$timeOut = "";
			} else if($str == 3) {
				$timeIn = "FL";
				$timeOut = "";
			} else if($str == 4) {
				$timeIn = "ML";
				$timeOut = "";
			} else if($str == 5) {
				$timeIn = "BDL";
				$timeOut = "";
			} else if($str == 6) {
				$timeIn = "BL";
				$timeOut = "";
			} else if($str == 7) {
				$timeIn = "Dayoff";
				$timeOut = "";
			} else {
				// $timeIn = substr($rowTimeCard['time_in'], 0, 5);
				// $timeOut = substr($rowTimeCard['time_out'], 0, 5);
			}
			return $timeIn;
		}
		
		public function get_undertime_late($data){
			$pkid = $data['pkid'];
			$query = "SELECT `late_conv`, `under_conv` FROM `tkdataraw` WHERE `pkid` = '$pkid';";
			return $this->query($query);
		}
		
		public function add_late_ot($data){
			$empno = $this->real_escape_string($data['txtLateOTEmpno']);
			$ot_date = $this->real_escape_string($data['txtLateOTDate']);
			$ot_count = $this->real_escape_string($data['txtLateOTCount']);
			$ot_type = $this->real_escape_string($data['txt_LateOTType']);
			$ot_month_paid = $this->real_escape_string($data['txt_LateOTMonth']);
			$ot_cutoff = $this->real_escape_string($data['txt_LateOTCutoff']);
			$cutoff_date = $ot_month_paid . '-' . $ot_cutoff;
			$lastupdate = date('Y-m-d H:i:s');
			$username = $data['username'];
			/* check if a record already exist */
			$query = "SELECT * FROM `vw_late_ot` WHERE `empno` = '$empno' AND `ot_date` = '$ot_date';";
			$result = $this->query($query);
			if($result->num_rows == 0){
				$query = "INSERT INTO 
							`tbl_late_ot` 
								(`empno`,
								`ot_date`,
								`ot_count`,
								`ot_type`,
								`ot_date_paid`,
								`lastupdate`,
								`username`) 
							VALUES(
								'$empno',
								'$ot_date',
								'$ot_count',
								'$ot_type',
								'$cutoff_date',
								'$lastupdate',
								'$username'
							);";
				$this->query($query);
				return "Record has been saved!";
				// return $query;
			}
			return "Record already exist!";
		}
		
		public function delete_late_ot($data){
			$pkid = $data['pkid'];
			$lastupdate = date('Y-m-d H:i:s');
			$username = $data['username'];
			$query = "UPDATE `tbl_late_ot` SET `logdel` = '1', `lastupdate` = '$lastupdate', `username` = '$username' WHERE `pkid` = '$pkid';";
			$this->query($query);
			return "Record has been deleted!";
			// return $query;
		}
		
		/* administrative */
		public function get_valid_ip($ip_address){
			$query = "SELECT `ip_address` FROM `tbl_valid_ip` WHERE `ip_address` = '$ip_address';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				return true;
			}
			return false;
		}

		public function get_empname($empno){
			$query = "SELECT `hris`.`EmpNo`,`hris`.`LastName`,`hris`.`FirstName`,`hris`.`MiddleName` FROM `db_hris`.`tbl_EmployeeInfo` `hris`  WHERE `hris`.`SalaryType` != '3' AND `hris`.`EmpStatus` = '1' AND `EmpNo` = '$empno';";
			$result = $this->query($query);
			if($result->num_rows != 0){
				$row = mysqli_fetch_array($result);
				$empname = $row['LastName'].', '.$row['FirstName'].' '.$row['MiddleName'][0].'.';
			}else{
				$empname = "";
			}
			return $empname;
		}

		public function get_elfs_data($status){
			if($status == 0){
				/* all pending */
				$status_where = "WHERE status = '0' ";
			}else{
				/* all */
				$status_where = "";
			}
			// $query = "SELECT '' AS `fk_tkdataraw`,
			// 		       `elfs`.`EmpNo` AS `empno`,
			// 		       `elfs`.`new_leave_applied` AS `leave_type`,
			// 		       `elfs`.`date_absent_from` as `date_of_leave`,
			// 		       `elfs`.`no_of_days` AS `days`,
			// 		       `elfs`.`no_of_paid` AS `days_paid`,
			// 		       `elfs`.`cutoff_date` AS `cutoff_date`,
			// 		       IFNULL((SELECT `otks`.`pkid` 
			// 		       FROM `db_otks`.`tbl_leave_cutoff_date` `otks` 
			// 		       WHERE `otks`.`empno` = `elfs`.`EmpNo` 
			// 		       		AND `otks`.`leave_type`	 = `elfs`.`new_leave_applied` 
			// 		       		AND `otks`.`date_of_leave` = `elfs`.`date_absent_from`  
			// 		       		AND `otks`.`cutoff_date` = `elfs`.`cutoff_date`
			// 		       	LIMIT 0,1),0) as `status` 
			// 		FROM `db_hris`.`vw_leaves` `elfs` 
			// 		WHERE `cutoff_date` > '2017-02-20'";
			// $query 	= "SELECT * FROM `db_hris`.`vw_leaves_otks` ".$status_where.";";
			// $query 	= "SELECT '' AS `fk_tkdataraw`,
                       // `elfs`.`EmpNo` AS `empno`,
                       // `elfs`.`new_leave_applied` AS `leave_type`,
                       // `elfs`.`date_absent_from` AS `date_of_leave`,
                       // `elfs`.`no_of_days` AS `days`,
                       // `elfs`.`no_of_paid` AS `days_paid`,
                       // `elfs`.`cutoff_date` AS `cutoff_date`,
                       // ifnull(
                                // (SELECT `otks`.`pkid`
                                 // FROM `db_otks`.`tbl_leave_cutoff_date` `otks`
                                 // WHERE ((`otks`.`empno` = `elfs`.`EmpNo`)
                                        // AND (`otks`.`leave_type` = `elfs`.`new_leave_applied`)
                                        // AND (`otks`.`date_of_leave` = `elfs`.`date_absent_from`)
                                        // AND (`otks`.`cutoff_date` = `elfs`.`cutoff_date`))
                                 // LIMIT 0,1),0) AS `status`
                        // FROM db_hris.`vw_leaves` `elfs`
                        // WHERE (`elfs`.`cutoff_date` > '2019-01-05');";
			$query 	= "SELECT '' AS `fk_tkdataraw`,
                       `elfs`.`EmpNo` AS `empno`,
                       `elfs`.`new_leave_applied` AS `leave_type`,
                       `elfs`.`date_absent_from` AS `date_of_leave`,
                       `elfs`.`no_of_days` AS `days`,
                       `elfs`.`no_of_paid` AS `days_paid`,
                       `elfs`.`cutoff_date` AS `cutoff_date`
                        FROM db_hris.`vw_leaves` `elfs`
                        WHERE (`elfs`.`cutoff_date` > '2019-01-05');";
			$result = $this->query($query);
			return $result;
		}

		public function upload_slvl_elfs($empno, $code, $date, $noDaysLeave, $daysPaid, $username, $cutoff_date) {
			$database = "db_otks";
			/* code equivalent */
			$return = array();
			switch($code){
				case "1" : $code_equiv = "SL"; break;
				case "2" : $code_equiv = "VL"; break;
				case "3" : $code_equiv = "FL"; break;
				case "4" : $code_equiv = "ML"; break;
				case "5" : $code_equiv = "BDL"; break;
				case "6" : $code_equiv = "BL"; break;
				case "7" : $code_equiv = "do"; break;
				default	: "Leave";
			}
			
			$format = 'Y-m-d';
			$today = date($format ); //today;
			
			/* check if there is an attendance for the leave date */
			$query = "SELECT pkid, date, time_in, date_out, time_out FROM $database.tkdataraw WHERE empno='".$empno."' AND date='".$date."';";
			$checkAttendance = $this->query($query);
			/* if no attendance insert a new row */
			if($checkAttendance->num_rows == 0) {
				$return = array();
				$query = "INSERT INTO `tkdataraw` (`lastupdate`,`empno`,`date`,`time_in`,`absent`,`leavecode`,`leavenum`,`username`,`status`) 
											VALUES('".date('Y-m-d')."','$empno','$date','$code_equiv','1','$code','$daysPaid','elfs','1');";
				$this->query($query);
				$return['query'][] = $query;
			}
			
			/* Update row data */
			$query = "SELECT pkid, date, time_in, date_out, time_out FROM $database.tkdataraw WHERE empno='".$empno."' AND date='".$date."';";
			$return['query'][] = $query;
			$checkAttendance = $this->query($query);	
			while($row = mysqli_fetch_array($checkAttendance)){
				$pkid 		= $row['pkid'];
				$date_in 	= $row['date'];
				$time_in 	= $row['time_in'];
				$date_out	= $row['date_out']; 
				$time_out	= $row['time_out'];
			}
			/* check if time is empty */
			if($time_in == ''){
				$time_in = $code_equiv;
			}
			$update_query  = "UPDATE $database.tkdataraw SET 
								`absent`	='1',
								`time_in`	='".$time_in."', 
								`leavecode`	='".$code."', 
								`leavenum`	='".$daysPaid."', 
								`username`	='elfs' 
							WHERE 
								`empno`		='".$empno."' 
							AND 
								`pkid`		='".$pkid."';";
			$updateDatRaw = $this->query($update_query);
			$tklogs_result = $this->add_update_tklogs($pkid,$empno,$date_in,$date_out,'',$date_out,$time_out,'',date('Y-m-d H:i:s'));
			
			$return['query'][] = $update_query;

			/* validate if record already exist in tbl_leave_cutoff_date table */
			$query = "SELECT `fk_tkdataraw` FROM `tbl_leave_cutoff_date` WHERE `fk_tkdataraw` = '$pkid';";
			$return['query'][] = $query;
			$result = $this->query($query);
			if($result->num_rows == 0) {
				/* if record does not exist, insert new row */
				/* NOTE: Add other fields */
				$query = "INSERT INTO `tbl_leave_cutoff_date` (`fk_tkdataraw`,
																`empno`,
																`leave_type`,
																`date_of_leave`,
																`days`,
																`days_paid`,
																`cutoff_date`) 
														VALUES('$pkid',
																'$empno',
																'$code',
																'$date',
																'$noDaysLeave',
																'$daysPaid',
																'$cutoff_date');";
				$this->query($query);
				$return['query'][] = $query;
				$return['msg'] = "OK New Data!";
			}else{
				/* if record already exist, update the record */
				while($row = mysqli_fetch_array($result)){
					$pkid = $row['pkid'];
				}
				$query = "UPDATE `tbl_leave_cutoff_date` 
							SET `empno`			= '$empno',
								`leave_type` 	= '$code', 
								`date_of_leave` = '$date', 
								`days` 			= '$noDaysLeave',
								`days_paid` 	= '$daysPaid',
								`cutoff_date` 	= '$cutoff_date') 
							WHERE `pkid` = '$pkid';";
				$this->query($query);
				$return['msg'] = "OK Update Data!";
				$return['query'][] = $query;
			}
			
			$return['empno'] = $empno;
			return $return;
		}
		
		/* Generate tardiness and undertime report - start*/
		public function load_division_list() {
			$query = "
				SELECT `pkid`,
					   `Division`
				FROM `db_hris`.`tbl_Division`
				WHERE `logdel`=0
			";
			return $this->query($query);
		}
		
		public function load_record_with_tardi($month_selected,$fkDivision){
			$query = "
				SELECT `tkdataraw`.`pkid` AS `pkid`,
					   `tkdataraw`.`empno` AS `empno`,
					   `db_hris`.`tbl_EmployeeInfo`.`SalaryType` AS `SalaryType`,
					    CONCAT(`db_hris`.`tbl_EmployeeInfo`.`LastName`,', ',`db_hris`.`tbl_EmployeeInfo`.`FirstName`) AS `empname`,
						(SELECT `Division` FROM `db_hris`.`tbl_Division` `div` WHERE `div`.`pkid` = `db_hris`.`tbl_EmployeeInfo`.`fkDivision` LIMIT 0, 1) AS `Division`,
					   `tkdataraw`.`date` AS `date_in`,
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
				WHERE (`tkdataraw`.`late_mins` != 0
					   OR `tkdataraw`.`under_mins` != 0)
				  AND `tkdataraw`.`leavecode`=''
				  AND `tkdataraw`.`date` LIKE '%".$month_selected."%'
				  AND `db_hris`.`tbl_EmployeeInfo`.`fkDivision`=".$fkDivision."
				ORDER BY `tkdataraw`.`empno`,
						 `tkdataraw`.`date`";
			return $this->query($query);
		}
		
		public function get_status($EmpNo, $new_leave_applied, $date_absent_from, $cutoff_date) {
			$result = $this->query("SELECT pkid FROM tbl_leave_cutoff_date WHERE empno='$EmpNo' AND leave_type='$new_leave_applied' AND date_of_leave ='$date_absent_from' AND cutoff_date='$cutoff_date' LIMIT 0,1");
			return $result->num_rows;
			// return "SELECT pkid FROM tbl_leave_cutoff_date WHERE empno='$EmpNo' AND leave_type='$new_leave_applied' AND date_of_leave ='$date_absent_from' AND cutoff_date='$cutoff_date' LIMIT 0,1";
		}
	
	}

?>

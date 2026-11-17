<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<head>
<title>OTKS REPORT</title>

	<!--SCRIPT and CSS-->
	<?php
	require_once(__DIR__ . '/includes.php');
	if(session_status() === PHP_SESSION_NONE){ session_start(); }
	$username = '';
	if (array_key_exists("username", $_SESSION)) //username validation
	{
		$username = $_SESSION['username'];
		//$username = 'arlbena';
	}
	require_once(__DIR__ . '/check_login.php');
	?>
	
	<style> 
	<!--OVERFLOW ON PANEL-->
	.table {
		table-layout:fixed;
	}

	.table td {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.panel-body{
		
	}

	th {
		font-size: 50%;
		font-family: "Arial", Times, serif;
	}
	td {
		font-size: 50%;
		white-space: nowrap;
		font-family: "Arial Narrow", Times, serif;
	}
	
	.printbreak {
		page-break-before: always;
	}
	
	.container{
		position:absolute;
		height:350;
		width:360;
		left:15;
    }
	.container1{
		position:relative;
		left:360;
		height:350;
		width:360;
    }
	.container2{
		left:28;
		position:absolute;
		height:350;
		width:360;
   	}
	.container3{
		position:relative;
		left:360;
		height:350;
		width:360;
   	}
	.container4{
		left:28;
		position:absolute;
		height:350;
		width:360;
   	}
	.container5{
		position:relative;
		left:360;
		height:350;
		width:360;
   	}
	#tbl_timecard tr td{
		padding:1px;
	}
	</style>
<script>
function unhideButton(){
	$('#printTimeCard4pages').show();
	$('#printTimeCard6pages').show();
	$('#printTimeCardLegal').show();
}
$(document).ready(function(){
	var selected_month = '';
	var start_date = '';
	var end_date = '';
	//START DATE VALIDATION
	$('#txtStartDate').change(function(){
		start_date = $('#txtStartDate').val();
		end_date = $('#txtEndDate').val();
			if (start_date>end_date){
				$('#txtStartDate').val(end_date);  
			}
	});
	//END DATE VALIDATION
	$('#txtEndDate').change(function(){
		start_date = $('#txtStartDate').val();
		end_date = $('#txtEndDate').val();
			if (end_date<start_date){
				$('#txtEndDate').val(start_date);  
			}
	});
	//PRINT TIMECARD
	
	$('#printTimeCard4pages').click(function(){
		// window.location.href = "individualTimeCard_4pages.php?txtStartDate="+$('#txtStartDate').val()+"&txtEndDate="+$('#txtEndDate').val()+'&month='+selected_month;
		window.open( "individualTimeCard_4pages.php?txtStartDate="+$('#txtStartDate').val()+"&txtEndDate="+$('#txtEndDate').val()+'&month='+selected_month , "_blank");
	});
	$('#printTimeCard6pages').click(function(){
		// $('#mainContainer').hide();
		// $('.top-container').hide();
		// $('#printTimeCard4pages').hide();
		// $('#printTimeCard6pages').hide();
		// window.print();
		// setTimeout(function () { 
			// $('.top-container').show();  
			// $('#printTimeCard4pages').hide();
			// $('#printTimeCard6pages').show();
		// } , 100);
		window.open( "individualTimeCard_6pages.php?txtStartDate="+$('#txtStartDate').val()+"&txtEndDate="+$('#txtEndDate').val()+'&month='+selected_month , "_blank");
	});
	$('#printTimeCardLegal').click(function(){
		// window.location.href = "individualTimeCardLegal.php?txtStartDate="+$('#txtStartDate').val()+"&txtEndDate="+$('#txtEndDate').val()+'&month='+selected_month;
		window.open(  "individualTimeCardLegal.php?txtStartDate="+$('#txtStartDate').val()+"&txtEndDate="+$('#txtEndDate').val()+'&month='+selected_month, "_blank");
	});
	
	/* cut off shortcuts */
	$('#set_date_1').click(function(){
		$('#set_date_type').val('1');
	});

	$('#set_first_cutoff').click(function(){
		$('#set_date_type').val('2');
	});

	$('#set_second_cutoff').click(function(){
		$('#set_date_type').val('3');
	});

	$('#frm_date_range').submit(function(e){
		e.preventDefault();
		start_date = $('#txtStartDate').val();
		end_date = $('#txtEndDate').val();
		
		if($('#set_date_type').val() == 1){
			window.location.href="individualTimeCard.php?txtStartDate="+start_date+"&txtEndDate="+end_date
		}
		
		if($('#set_date_type').val() == 2){
			selected_month = $('#txt_month_range').val();
			if(selected_month == ""){
				alert("Please select a month!");
				return false;
			}
			var data = {
				"action"			: "get_first_cut_off",
				"selected_month"	: selected_month
			}
			data = $.param(data);
			$.ajax({
				type		: "post",
				dataType	: "json",
				data		: data,
				url		: "reportHandler.php",
				success	: function(result){
					start_date = result['start_date'];
					end_date = result['end_date'];
					window.location.href="individualTimeCard.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
				}
			});
		}
		
		if($('#set_date_type').val() == 3){
			var selected_month = $('#txt_month_range').val();
			if(selected_month == ""){
				alert("Please select a month!");
				return false;
			}
			var data = {
				"action"			: "get_second_cut_off",
				"selected_month"	: selected_month
			}
			data = $.param(data);
			$.ajax({
				type		: "post",
				dataType	: "json",
				data		: data,
				url		: "reportHandler.php",
				success	: function(result){
					start_date = result['start_date'];
					end_date = result['end_date'];
					window.location.href="individualTimeCard.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
				}
			});
		}
	});
});
</script>
	
</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
	<nav class="navbar navbar-default" role="navigation" style="">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a class="navbar-brand" href="#" >OTKS REPORT</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			  <ul class="nav navbar-nav menu">
				<li class=""><a href="index.php" id="loadRawData"class="fa fa-tasks fa-lg"> RAW DATA</a></li>
				<li class=""><a href="dailyTFM.php" id="loadDailyTFM" class="fa fa-tasks fa-lg"> TFM</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL" class="fa fa-upload fa-lg"> SL/VL</a></li>
				<li class=""><a href="eom.php" id="loadEom" class="fa fa-tasks fa-lg"> EOM</a></li>
				<li class="active"><a href="individualTimeCard.php" id="loadIndividualTimecard" class="fa fa-user fa-lg"> Timecard</a></li>
				<li class=""><a href="passRequests.php" id="" class="fa fa-car fa-lg"> Pass Requests</a></li>
				<li class=""><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>	
	
	<?php
		if( !isset($_GET['txtStartDate']) || !isset($_GET['txtEndDate']) ){
			$date_start = date('Y-m-d');
			$date_end = date('Y-m-d');
		}else{
			$date_start = $_GET['txtStartDate'];
			$date_end = $_GET['txtEndDate'];
		}
		
		if(isset($_GET['month'])){
			$month = $_GET['month'];
		}else{
			$month = '';
		}
	?>
	<div class="row panel panel-primary top-container">
		<div class="panel-heading" style="margin-bottom:10px;"><b>INDIVIDUAL TIMECARD</b></div>
		<div class="col-sm-10">
			<form class="form-inline" action="#" id="frm_date_range">
			  <input type="hidden" id="set_date_type">
			  <div class="form-group">
				<label class="sr-only" for="txtStartDate">Start Date</label>
				<?php 
					echo '<input type="date" class="form-control" id="txtStartDate" name="txtStartDate" placeholder="Start Date" value="'.htmlspecialchars((string)$date_start, ENT_QUOTES, 'UTF-8').'">';
				?>
			  </div>
			  <div class="form-group">
				<label class="sr-only" for="txtEndDate">End Date</label>
				<?php 
					echo '<input type="date" class="form-control" id="txtEndDate" name = "txtEndDate" placeholder="End Date" value="'.htmlspecialchars((string)$date_end, ENT_QUOTES, 'UTF-8').'">';
				?>
			  </div>
			  <button type="submit" class="btn btn-primary" id="set_date_1"><span class="fa fa-calendar"></span> SET DATE</button>
			  <button type="button" class="btn btn-success" id="printTimeCard4pages" style="display:none;"><span class="fa fa-print"></span> TIMECARD [4 - A4]</button>
			  <button type="button" class="btn btn-success" id="printTimeCard6pages" style="display:none;"><span class="fa fa-print"></span> TIMECARD [6 - A4]</button>
			  <button type="button" class="btn btn-success" id="printTimeCardLegal" style="display:none;"><span class="fa fa-print"></span> TIMECARD [6 - Legal]</button>
			  <br><br>
			  <input type="month" pattern="[0-9]*" class="form-control" id="txt_month_range" value="<?php echo htmlspecialchars((string)$month, ENT_QUOTES, 'UTF-8'); ?>" >
			  <button type="submit" class="btn btn-primary" id="set_first_cutoff"><span class="fa fa-calendar-check-o"></span> 1ST CUT OFF</button>
			  <button type="submit" class="btn btn-primary" id="set_second_cutoff"><span class="fa fa-calendar-check-o"></span> 2ND CUT OFF</button>
			</form>
		</div>
		<div class="col-sm-2">
			Cut-off Information <br>
			30 - 14 and 15 - 29
		</div>
	</div>
	<div class="panel panel-default mid-container" style="border:hidden;">
		<div class="panel panel-body">
				<?php
				if(isset($_GET['txtStartDate'])){
					echo "<script>unhideButton()</script>";
					require_once(__DIR__ . '/otksr-oop.php');
					$dateFrom = $_GET['txtStartDate'];
					$dateTo = $_GET['txtEndDate'] ?? '';
					
					//get user name
					$selectUserInfo = OTKSR::getInstance()->getUserInfo($username);
					$loggedName = '';
					while($selectUserInfo instanceof mysqli_result && ($rowUInfo = mysqli_fetch_array($selectUserInfo))) {
						$loggedName = $rowUInfo['LN'].", ". $rowUInfo['FN'];
					}
					
					$selectEmpInfo = OTKSR::getInstance()->getIndividualTimeCardEmpNo($dateFrom, $dateTo);
					if(!($selectEmpInfo instanceof mysqli_result)){ error_log('individualTimeCard: employee query failed: ' . OTKSR::getInstance()->error); }
					$ctr = 0;
			
					while($selectEmpInfo instanceof mysqli_result && ($rowInfo = mysqli_fetch_array($selectEmpInfo))) {
					if($ctr==0){
						echo '<div class="container">';
					}
					echo '<table border="1" width="320" style="font-size:19px;" id="tbl_timecard">
							<thead>
								<tr>
									<th style="text-align:center" colspan="13">PRICON MICROELECTRONICS INC. <br> OTKS 2.0 EMPLOYEE ATTENDANCE REPORT</th>
								</tr>
								<tr>
									<th style="text-align:center" colspan="13" style="font-size:100">For the Pay Period: '.htmlspecialchars((string)$dateFrom, ENT_QUOTES, 'UTF-8').'~'.htmlspecialchars((string)$dateTo, ENT_QUOTES, 'UTF-8').'</th>
								</tr>
								<tr>';
									$totalRegOT = 0;
									$totalSunOT = 0;
									$totalSpeOT = 0;
									$totalHolOT = 0;
									$totalUndertime = 0;
									$totalNightDiff = 0;
									$totalAbsent = 0;
									$totalSLVL = 0;
									$empno = $rowInfo['empno'];
									$ln = $rowInfo['LastName'];
									$fn = $rowInfo['FirstName'];
									$mn = $rowInfo['MiddleName'];
									$dept = $rowInfo['Department'];
								echo '
									<th colspan="7">Employee Name: '.htmlspecialchars((string)$ln, ENT_QUOTES, 'UTF-8').', '.htmlspecialchars((string)$fn, ENT_QUOTES, 'UTF-8').' '.htmlspecialchars(substr((string)$mn, 0, 1), ENT_QUOTES, 'UTF-8').'.</th>										
									<th colspan="8">Dept/Sect Name: '.htmlspecialchars((string)$dept, ENT_QUOTES, 'UTF-8').'</th>										
								</tr>
								<tr>
									<th colspan="13">Employee Number: '.htmlspecialchars((string)$empno, ENT_QUOTES, 'UTF-8').'</th>
								</tr>
								<tr>
									<th>Date</th>
									<th>Time In</th>
									<th>Time Out</th>
									<th>Regular OT</th>
									<th>Sunday OT</th>
									<th>Special Holiday</th>
									<th>Holiday OT</th>
									<th>Undertime</th>
									<th>Night Diff</th>
									<th>Absent</th>									
									<th>SL/VL</th>									
								</tr>
											
							</thead>
							<tbody>';
							/* late file ot */
							$late_file_ot_all = OTKSR::getInstance()->getEom_late_file_ot($empno, $dateFrom, $dateTo);
							if(is_array($late_file_ot_all)){
								for( $x=0; $x<count($late_file_ot_all['empno']); $x++ ){
									$late_ot_reg 		= 0;
									$late_ot_special 	= 0;
									$late_ot_sunday 	= 0;
									$late_ot_holiday 	= 0;
									$date_late_ot = '';
									if( $late_file_ot_all['ot_type'][$x] == 1){
										$date_late_ot = date('d-M-y',strtotime( $late_file_ot_all['ot_date'][$x] ));
										$late_ot_reg = (float)$late_file_ot_all['ot_count'][$x];
										$totalRegOT += $late_ot_reg;
									}
									if($late_file_ot_all['ot_type'][$x] == 2){
										$date_late_ot = date('d-M-y',strtotime( $late_file_ot_all['ot_date'][$x] ));
										$late_ot_special = (float)$late_file_ot_all['ot_count'][$x];
										$totalSpeOT += $late_ot_special;
									}
									if($late_file_ot_all['ot_type'][$x] == 3){
										$date_late_ot = date('d-M-y',strtotime( $late_file_ot_all['ot_date'][$x] ));
										$late_ot_sunday = (float)$late_file_ot_all['ot_count'][$x];
										$$totalSunOT += $late_ot_sunday;
									}
									if($late_file_ot_all['ot_type'][$x] == 4){
										$date_late_ot = date('d-M-y',strtotime( $late_file_ot_all['ot_date'][$x] ));
										$late_ot_holiday = (float)$late_file_ot_all['ot_count'][$x];
										$totalHolOT += $late_ot_holiday;
									}
									if($late_file_ot_all['ot_type'][$x] == 5){
										$date_late_ot = date('d-M-y',strtotime( $late_file_ot_all['ot_date'][$x] ));
										$late_ot_special = (float)$late_file_ot_all['ot_count'][$x];
										$totalSpeOT += $late_ot_special;
									}
									if($late_file_ot_all['ot_type'][$x] == 6){
										$date_late_ot = date('d-M-y',strtotime( $late_file_ot_all['ot_date'][$x] ));
										$late_ot_holiday = (float)$late_file_ot_all['ot_count'][$x];
										$totalHolOT += $late_ot_holiday;
									}
									
								echo '<tr>
										<td>'.$date_late_ot.'</td>
										<td>ot late</td>
										<td></td>
										<td>'.number_format($late_ot_reg, 2, '.', '').'</td>
										<td>'.number_format($late_ot_sunday, 2, '.', '').'</td>
										<td>'.number_format($late_ot_special, 2, '.', '').'</td>
										<td>'.number_format($late_ot_holiday, 2, '.', '').'</td>';
								  echo '<td>0.00</td>';
      							  echo '<td>0.00</td>';
								  echo '<td>0.00</td>
										<td>0.00</td>

									</tr>';
								}
							}
							
							/* Late File */							
							$late_filled_leaves = OTKSR::getInstance()->getIndividualTimeCard_late_file($empno, $dateFrom, $dateTo);
							if($late_filled_leaves instanceof mysqli_result){
								while($rowTimeCard = mysqli_fetch_array($late_filled_leaves)) {
									$date = $rowTimeCard['date'];
									if($rowTimeCard['leavecode'] == 1) {
										$timeIn = "SL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 2) {
										$timeIn = "VL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 3) {
										$timeIn = "FL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 4) {
										$timeIn = "ML";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 5) {
										$timeIn = "BDL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 6) {
										$timeIn = "BL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 7) {
										$timeIn = "Dayoff";
										$timeOut = "";
									} else {
										$timeIn = substr($rowTimeCard['time_in'], 0, 5);
										$timeOut = substr($rowTimeCard['time_out'], 0, 5);
									}
									$regOT = (float)$rowTimeCard['ot1'];
									$sundayOT = (float)$rowTimeCard['ot3'] + (float)$rowTimeCard['ot3a'];
									$specialOT = (float)$rowTimeCard['ot2'] + (float)$rowTimeCard['ot2a'] + (float)$rowTimeCard['ot5'] + (float)$rowTimeCard['ot5a'];
									$holidayOT = (float)$rowTimeCard['ot4'] + (float)$rowTimeCard['ot4a'] + (float)$rowTimeCard['ot6'] + (float)$rowTimeCard['ot6a'];
									$undertime = (float)$rowTimeCard['under_conv'] + (float)$rowTimeCard['late_conv'];
									$nightDiff = (float)$rowTimeCard['nightdiff'];
									$absent = (float)$rowTimeCard['absent'];
									$slvl = (float)$rowTimeCard['leavenum'];
									// $totalRegOT += $regOT;
									// $totalSunOT += $sundayOT;
									// $totalSpeOT += $specialOT;
									// $totalHolOT += $holidayOT;
									// $totalUndertime += $undertime;
									// $totalNightDiff += $nightDiff;
									// $totalAbsent += $absent;
									$totalSLVL += $slvl;
									echo '<tr>
										<td>'; 
										$source = $date;
										$dateFormat = new DateTime($source);
										echo $dateFormat->format('d-M-y').'</td>
										<td>'.$timeIn.'</td>
										<td>'.$timeOut.'</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>';
										if($undertime == '') {
											echo '<td></td>';
										} else {
											echo '<td></td>';
										}
										echo '<td></td>
										<td></td>
										<td>'.number_format($slvl,2,'.','').'</td>
									</tr>';
								}
							}
							/* Regular In and Out */
							echo '<tr>';
									$individualTimeCard = OTKSR::getInstance()->getIndividualTimeCard($empno, $dateFrom, $dateTo);
									while($individualTimeCard instanceof mysqli_result && ($rowTimeCard = mysqli_fetch_array($individualTimeCard))) {
										$date = $rowTimeCard['date'];
										if($rowTimeCard['leavecode'] == 1) {
											$timeIn = "SL";
											$timeOut = "";
										} else if($rowTimeCard['leavecode'] == 2) {
											$timeIn = "VL";
											$timeOut = "";
										} else if($rowTimeCard['leavecode'] == 3) {
											$timeIn = "FL";
											$timeOut = "";
										} else if($rowTimeCard['leavecode'] == 4) {
											$timeIn = "ML";
											$timeOut = "";
										} else if($rowTimeCard['leavecode'] == 5) {
											$timeIn = "BDL";
											$timeOut = "";
										} else if($rowTimeCard['leavecode'] == 6) {
											$timeIn = "BL";
											$timeOut = "";
										} else if($rowTimeCard['leavecode'] == 7) {
											$timeIn = "Dayoff";
											$timeOut = "";
										} else {
											$timeIn = substr($rowTimeCard['time_in'], 0, 5);
											$timeOut = substr($rowTimeCard['time_out'], 0, 5);
										}
										$regOT = (float)$rowTimeCard['ot1'];
										$sundayOT = (float)$rowTimeCard['ot3'] + (float)$rowTimeCard['ot3a'];
										$specialOT = (float)$rowTimeCard['ot2'] + (float)$rowTimeCard['ot2a'] + (float)$rowTimeCard['ot5'] + (float)$rowTimeCard['ot5a'];
										$holidayOT = (float)$rowTimeCard['ot4'] + (float)$rowTimeCard['ot4a'] + (float)$rowTimeCard['ot6'] + (float)$rowTimeCard['ot6a'];
										$undertime = (float)$rowTimeCard['under_conv'] + (float)$rowTimeCard['late_conv'];
										$nightDiff = (float)$rowTimeCard['nightdiff'];
										$absent = (float)$rowTimeCard['absent'];
										$slvl = (float)$rowTimeCard['leavenum'];
										$totalRegOT += $regOT;
										$totalSunOT += $sundayOT;
										$totalSpeOT += $specialOT;
										$totalHolOT += $holidayOT;
										$totalUndertime += $undertime;
										$totalNightDiff += $nightDiff;
										$totalAbsent += $absent;
										$totalSLVL += $slvl;
									echo '
									<td>'; 
									$source = $date;
									$dateFormat = new DateTime($source);
									echo $dateFormat->format('d-M-y').'</td>
									<td>'.$timeIn.'</td>
									<td>'.$timeOut.'</td>
									<td>'.number_format($regOT,2,'.','').'</td>
									<td>'.number_format($sundayOT,2,'.','').'</td>
									<td>'.number_format($specialOT,2,'.','').'</td>
									<td>'.number_format($holidayOT,2,'.','').'</td>';
									if($undertime == '') {
										echo '<td>0.00</td>';
									} else {
										echo '<td>'.number_format($undertime,2,'.','').'</td>';
									}
									echo '<td>'.number_format($nightDiff,2,'.','').'</td>
									<td>'.number_format($absent,2,'.','').'</td>
									<td>'.number_format($slvl,2,'.','').'</td>
								</tr>';
							}
							
						echo '<tr>
							<td colspan=3></td>
							<td>'.number_format($totalRegOT,2,'.','').'</td>
							<td>'.number_format($totalSunOT,2,'.','').'</td>
							<td>'.number_format($totalSpeOT,2,'.','').'</td>
							<td>'.number_format($totalHolOT,2,'.','').'</td>
							<td>'.number_format($totalUndertime,2,'.','').'</td>
							<td>'.number_format($totalNightDiff,2,'.','').'</td>
							<td>'.number_format($totalAbsent,2,'.','').'</td>
							<td>'.number_format($totalSLVL,2,'.','').'</td>
						</tr>
						<tr>
							<td colspan=5>'.htmlspecialchars((string)$loggedName, ENT_QUOTES, 'UTF-8').'</td>
							<td colspan=6 align="right">'.date('Y-m-d H:i:s').'</td>
						</tr>
					</tbody>';
									

					echo '</table>';
					if($ctr<5){
							if($ctr==0){
								echo '</div>';//closing of container
								$ctr++;
								echo '<div class="container'.$ctr.'">';
							}
							else{
								echo '</div>'; //closing of moving container
								$ctr++;
								echo '<div class="container'.$ctr.'">';
							}
					}
					else{
						echo '</div>';
						echo '<div class="printbreak"></div>';
						$ctr=$ctr-$ctr;
					}
					
				}
						
			}
			?>
		</div>
	</div>
		
<!--MODALS-->

	<!-- Modal Edit-->
	<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:800px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">MANUAL UPDATING</h4>
		  </div>
		  <div class="modal-body">
				<form class="form-inline" role="form">
					<div class="form-group">
						<label style="margin-right:20px;width:160px">ID: </label><input type="text" id="" placeholder="ID" readonly>
						<label style="margin-right:20px;width:160px">ECode: </label><input type="text" id="" placeholder="ECode" readonly>
						<label style="margin-right:20px;width:160px">Date IN: </label><input type="text" id="" placeholder="Date IN">
						<label style="margin-right:20px;width:160px">In: </label><input type="text" id="" placeholder="In">
						<label style="margin-right:20px;width:160px">IN Remarks: </label><input type="text" id="" placeholder="IN Remarks"><br>
						<label style="margin-right:20px;width:160px">Date OUT: </label><input type="text" id="" placeholder="Date OUT">
						<label style="margin-right:20px;width:160px">OUT Remarks: </label><input type="text" id="" placeholder="OUT Remarks">
						<label style="margin-right:20px;width:160px">REG_HOURS: </label><input type="text" id="" placeholder="REG_HOURS">
						<label style="margin-right:20px;width:160px">OT HOURS: </label><input type="text" id="" placeholder="OT HOURS">
						<label style="margin-right:20px;width:160px">ABSENT: </label><input type="text" id="" placeholder="ABSENT">
						<label style="margin-right:20px;width:160px">Undertime: </label><input type="text" id="" placeholder="Undertime">
						<label style="margin-right:20px;width:160px">SL VL CODE: </label><input type="text" id="" placeholder="SL VL CODE">
						<label style="margin-right:20px;width:160px">SL VL NUM: </label><input type="text" id="" placeholder="SL VL NUM">
						<label style="margin-right:20px;width:160px">OT 1: </label><input type="text" id="" placeholder="OT 1">
						<label style="margin-right:20px;width:160px">OT 2: </label><input type="text" id="" placeholder="OT 2">
						<label style="margin-right:20px;width:160px">OT 2A: </label><input type="text" id="" placeholder="OT 2A">
						<label style="margin-right:20px;width:160px">OT 3: </label><input type="text" id="" placeholder="OT 3">
						<label style="margin-right:20px;width:160px">OT 3A: </label><input type="text" id="" placeholder="OT 3A">
						<label style="margin-right:20px;width:160px">OT 4: </label><input type="text" id="" placeholder="OT 4">
						<label style="margin-right:20px;width:160px">OT 4A: </label><input type="text" id="" placeholder="OT 4A">
						<label style="margin-right:20px;width:160px">OT 5: </label><input type="text" id="" placeholder="OT 5">
						<label style="margin-right:20px;width:160px">OT 5A: </label><input type="text" id="" placeholder="OT 5A">
						<label style="margin-right:20px;width:160px">OT 6: </label><input type="text" id="" placeholder="OT 6">
						<label style="margin-right:20px;width:160px">OT 6A: </label><input type="text" id="" placeholder="OT 6A">
						<label style="margin-right:20px;width:160px">Night Diff: </label><input type="text" id="" placeholder="Night Diff">
						<label style="margin-right:20px;width:160px">AUT: </label><input type="text" id="" placeholder="AUT">
					</div>
				</form>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary">Save changes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	

</body>
</html>
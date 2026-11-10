<?php
session_start();
	require_once("includes.php");
	require_once('otksr-oop.php');
	session_start();
	if (array_key_exists("username", $_SESSION))
	{
		session_start();
		$username = $_SESSION['username'];
	}
	require_once('check_login.php');
	
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

<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<head>
<title>OTKS REPORT</title>

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
	overflow:scroll;
}
.styleUpdate{
	width:180px;
}
.update_padding{
	padding:2px;
}
.update_padding_4{
	padding:4px;
}
.padding_top{
	padding-top:8px;
}
</style>
</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
	<nav class="navbar navbar-default" role="navigation">
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
				<li class=""><a href="index.php?txtStartDate=<?php echo $date_start; ?>&txtEndDate=<?php echo $date_end; ?>&month=<?php echo $month; ?>" class="fa fa-tasks fa-lg" id="loadRawData"> RAW DATA</a></li>
				<li class="active"><a href="dailyTFM.php" id="loadDailyTFM" class="fa fa-tasks fa-lg"> TFM</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL" class="fa fa-upload fa-lg"> SL/VL</a></li>
				<li class=""><a href="eom.php" id="loadEom" class="fa fa-tasks fa-lg"> EOM</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard" class="fa fa-user fa-lg"> Timecard</a></li>
				<li class=""><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

		<div class="row panel panel-primary top-container">
			<div class="panel-heading" style="margin-bottom:10px;"><b>DAILY TFM</b></div>
			<div class="col-sm-8">
				<form class="form-inline" action="#" id="frm_set_date">
				  <input type="hidden" id="set_date_type">
				  <div class="form-group">
					<label class="sr-only" for="txtStartDate">Start Date</label>
					<?php 
						echo '<input type="date" class="form-control" id="txtStartDate" name="txtStartDate" placeholder="Start Date" value="'.$date_start.'">';
					?>
				  </div>
				  <div class="form-group">
					<label class="sr-only" for="txtEndDate">End Date</label>
					<?php 
						echo '<input type="date" class="form-control" id="txtEndDate" name = "txtEndDate" placeholder="End Date" value="'.$date_end.'">';
					?>
				  </div>
				  <button type="submit" class="btn btn-primary" id="set_date_1"><span class="fa fa-calendar"></span> SET DATE</button><br><br>
				  <input type="month" pattern="[0-9]*" class="form-control" id="txt_month_range" value="<?php echo $month; ?>" >
				  <button type="submit" class="btn btn-primary" id="set_first_cutoff"><span class="fa fa-calendar-check-o"></span> 1ST CUT OFF</button>
				  <button type="submit" class="btn btn-primary" id="set_second_cutoff"><span class="fa fa-calendar-check-o"></span> 2ND CUT OFF</button>
				  <button type="button" class="btn btn-success" id="btn_export_excel"><span class="fa fa-file-excel-o"></span> EXPORT TFM</button>
				</form>
			</div>
			
			<div class="col-sm-3 col-sm-offset-1">
				Cut-off Information <br>
				30 - 14 and 15 - 29
			</div>
		</div>

		<div class="row panel panel-default mid-container">
			<div class="col-sm-12" style="overflow-y:hidden;">
				<p class="align-r">
					<button class="btn btn-default fa fa-calendar-o" id="btnRecompute" onclick=""> RECOMPUTE DATA</button>
					<button class="btn btn-default fa fa-calendar-plus-o" id="btnYECTraining"> YEC TRAINING</button>
					<button class="btn btn-default fa fa-calendar-plus-o" id="btnHoliday"> HOLIDAY</button>
					<button class="btn btn-default fa fa-calendar-minus-o" id="btnOffset"> OFFSET</button>
					<button class="btn btn-default fa fa-calendar-minus-o" id="btnLateOT"> LATE OT</button>
				</p>
				<table border="1" class="table table-striped" id="tblMain">
					<thead>
						<tr>
							<!--<th style="display:none;">PKID</th>-->
							<th>Inconsistency</th>
							<th>EN</th>
							<th>Salary Type</th>
							<th>Date IN</th>
							<th>In</th>
							<th>IN Remarks</th>
							<th>Date OUT</th>
							<th>Out</th>
							<th>OUT Remarks</th>
							<th>REG_HOURS</th>
							<th>OT HOURS</th>
							<th>Absent</th>
							<th>Undertime</th>
							<th>SL VL CODE</th>
							<th>SL VL NUM</th>
							<th>OT 1</th>
							<th>OT 2</th>
							<th>OT 2A</th>
							<th>OT 3</th>
							<th>OT 3A</th>
							<th>OT 4</th>
							<th>OT 4A</th>
							<th>OT 5</th>
							<th>OT 5A</th>
							<th>OT 6</th>
							<th>OT 6A</th>
							<th>Night Diff</th>
							<th>AUT</th>
							
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<!--</div>-->

	
<!--MODALS-->

	<!-- Modal Edit-->
	<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:700px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">MANUAL UPDATING</h4>
		  </div>
		  <div class="modal-body">
				<div>

				  <!-- Nav tabs -->
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#emp_info" aria-controls="emp_info" role="tab" data-toggle="tab">Employee Information</a></li>
					<li role="presentation"><a href="#login" aria-controls="login" role="tab" data-toggle="tab">Log-In</a></li>
					<li role="presentation"><a href="#leave_and_deductions" aria-controls="leave_and_deductions" role="tab" data-toggle="tab">Leave and Deductions</a></li>
					<li role="presentation"><a href="#overtime" aria-controls="overtime" role="tab" data-toggle="tab">Overtime</a></li>
				  </ul>

				  <!-- Tab panes -->
				  <div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="emp_info">
						<div class="row">
							<!-- Employee Information -->
							<h4 class="update_padding_4">Employee Information</h4>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top padding_top">EmpNo: </label><input class="form-control" type="text" id="empno" placeholder="EMPNO" readonly>
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top padding_top">ECode: </label><input class="form-control" type="text" id="SalaryType" placeholder="Salary Type" readonly>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top padding_top padding_top">PKID: </label><input class="form-control" type="text" id="pkid" placeholder="PKID" readonly>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="login">
						<!-- Time In Information -->
						<div class="row">
							<h4 class="update_padding_4">Time-In Information</h4>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">Date: </label><input class="form-control" type="date" id="date" placeholder="Date">
								</div>
								<div class="form-inline update_padding ">
									<label class="control-label col-sm-4 padding_top">Mode IN: </label>
									<select id="mode_in" class="form-control">
										<option value="IN">IN</option>
										<option value="BUS LATE">BUS LATE</option>
										<option value="OB">OB</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top padding_top">Time IN: </label><input class="form-control" type="time" id="time_in" placeholder="Time IN">
								</div>
							</div>
						</div>
												
						<!-- Time Out Information -->
						<div class="row">
							<h4 class="update_padding_4">Time-Out Information</h4>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">Date OUT: </label><input class="form-control" type="date" id="date_out" placeholder="Date OUT">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">Mode OUT: </label>
									<select id="mode_out" class="form-control">
										<option value="OUT">OUT</option>
										<option value="OB">OB</option>
										<option value="UNDERTIME">UNDERTIME</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">Time OUT: </label><input class="form-control" type="time" id="time_out" placeholder="Time OUT">
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="leave_and_deductions">
						<!-- Other Information -->
						<div class="row">
							<h4 class="update_padding_4">Leave and Deductions</h4>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">REG_HOURS: </label><input class="form-control" type="text" id="reg_hours" placeholder="REG_HOURS">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">ABSENT: </label><input class="form-control" type="text" id="absent" placeholder="Absent">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">LATE: </label><input class="form-control" type="text" id="late_update" placeholder="LATE">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">SL VL CODE: </label><!--<input class=" type="text" id="slvl_code" placeholder="SL VL CODE">-->
									<select id="slvl_code" class="form-control">
										<option value="1">SL</option>
										<option value="2">VL</option>
										<option value="3">Faternity Leave</option>
										<option value="4">Maternity Leave</option>
										<option value="5">Birthday Leave</option>
										<option value="6">Bereavement Leave</option>
										<option value="7">Day Off</option>
									</select><br><br>
								</div>
								
							</div>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT HOURS: </label><input type="text" class="form-control" id="ot_hours" placeholder="OT HOURS">
								</div>
								<!--<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">Undertime: </label><input class="form-control" type="text" id="undertime" placeholder="Undertime">
								</div>-->
								<br><br>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">UNDERTIME: </label><input class="form-control" type="text" id="undertime_update" placeholder="UNDERTIME">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">SL VL NUM: </label><input class="form-control" type="text" id="slvl_num" placeholder="SL VL NUM">
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="overtime">
						<div class="row">
							<h4 class="update_padding_4">Overtime</h4>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 1: </label><input class="form-control" type="text" id="ot1" placeholder="OT 1">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 2: </label><input class="form-control" type="text" id="ot2" placeholder="OT 2">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 3: </label><input class="form-control" type="text" id="ot3" placeholder="OT 3">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 4: </label><input class="form-control" type="text" id="ot4" placeholder="OT 4">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 5: </label><input class="form-control" type="text" id="ot5" placeholder="OT 5">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 6: </label><input class="form-control" type="text" id="ot6" placeholder="OT 6">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">Night Diff: </label><input class="form-control" type="text" id="nightdiff" placeholder="Night Diff">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-inline update_padding">
									<br><br>
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 2A: </label><input class="form-control" type="text" id="ot2a" placeholder="OT 2A">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 3A: </label><input class="form-control" type="text" id="ot3a" placeholder="OT 3A">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 4A: </label><input class="form-control" type="text" id="ot4a" placeholder="OT 4A">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 5A: </label><input class="form-control" type="text" id="ot5a" placeholder="OT 5A">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">OT 6A: </label><input class="form-control" type="text" id="ot6a" placeholder="OT 6A">
								</div>
								<div class="form-inline update_padding">
									<label class="control-label col-sm-4 padding_top">AUT: </label>
									<select class="form-control" id="aut">
										<option>TRUE</option>
										<option>FALSE</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				  </div>

				</div>
				
				
				<!--<form class="form-inline" role="form">
					<div class="form-group">-->

						
				<!--</div>
				</form>-->
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn-Update">Save changes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	
	<!-- Modal Holiday-->
	<div class="modal fade" id="modalHoliday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:750px;">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Enter a Date of a Holiday</h4>
		  </div>
		  <div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="col-sm-12" style="padding:2px;">
							<p style="text-align:right;">
								<button type="button" class="btn btn-primary fa fa-user" id="btn_add_holiday"> Add Holiday</button>
							</p>
						</div>
						<div id="container_add_holiday">
							<div class="col-sm-6">
								<label class="control-label">Date:</label>
								<input class="form-control" type="date" id="txtHoliday">
							</div>
							<div class="col-sm-6">
								<label class="control-label">Remarks:</label>
								<input class="form-control" type="text" id="txtHoliday_Remarks">
							</div>
							<div class="col-sm-6">
								<label class="control-label">Type of Holiday:</label>
								<select class="form-control" id="txtHoliday_Type">
									<option value="1">Regular</option>
									<option value="2">Special</option>
								</select>
								<!--<input class="form-control" type="text" id="txtHoliday_Type">-->
							</div>
							<div class="col-sm-12" style="padding:10px;">
								<p style="text-align:right;">
									<button type="button" class="btn btn-success fa fa-save" id="btn_save_holiday">Save Holiday</button>
								</p>
								<hr>
							</div>
						</div>
					</div>
					<div id="container_tbl_offset">
						<div class="col-sm-12" style="padding:5px;">
							<table id="tbl_holiday" class="table">
								<thead>
									<tr>
										<th style="width:10%;">DATE</th>
										<th>REMARKS</th>
										<th style="width:5%;"></th>
									</tr>
								</thead>
								<tbody>
								</tbody>							
							</table>
						</div>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<!--<button type="button" class="btn btn-primary" id="btnSetHoliday">Set Date as Holiday</button>-->
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<!-- Modal Offset-->
	<div class="modal fade" id="modalOffset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:750px;">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Offset</h4>
		  </div>
		  <div class="modal-body">
				<div class="row">
					<div class="col-sm-2 col-sm-offset-10" style="padding:2px;">
						<button type="button" class="btn btn-primary fa fa-user" id="btn_add_offset"> Add Offset</button>
					</div>
					<div id="container_add_offset">
						<datalist id="list_empno"></datalist>
						<div class="col-sm-6">
							<label>Empno</label><input class="form-control" type="text" id="txtOffsetEmpno" list="list_empno">
						</div>
						<div class="col-sm-6">
							<label>Date</label><input class="form-control" type="date" id="txtOffsetDate">
						</div>
						<div class="col-sm-2 col-sm-offset-10" style="padding:5px;">
							<button type="button" class="btn btn-success fa fa-save" id="btn_save_offset">Save Offset</button>
						</div>
					</div>
					<div id="container_tbl_offset">
						<div class="col-sm-12" style="padding:5px;">
							<table id="tbl_offset" class="table">
								<thead>
									<tr>
										<th>EMPNO</th>
										<th>DATE</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								</tbody>							
							</table>
						</div>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<!---->
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<!-- Modal YEC Training-->
	<div class="modal fade" id="modalYECTraining" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:750px;">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">YEC Training</h4>
		  </div>
		  <div class="modal-body">
				<div class="row">
					<div id="container_add_yec_training">
						<datalist id="list_empno"></datalist>
						<div class="col-sm-6">
							<label>Empno</label><input class="form-control" type="text" id="txtYECTrainingEmpno" list="list_empno">
						</div>
						<div class="col-sm-6">
							<label>End Date of Training</label><input class="form-control" type="date" id="txtDateEndTraining">
						</div>
						<div class="col-sm-12" style="padding:5px;">
							<p style="text-align:right;">
							 <button type="button" class="btn btn-success fa fa-save" id="btn_save_yec_training">Save Training</button>
							</p>
						</div>
					</div>
					<div id="container_tbl_offset">
						<div class="col-sm-12" style="padding:5px;">
							<table id="tbl_yec_training" class="table">
								<thead>
									<tr>
										<th>EMPNO</th>
										<th>Training End Date</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								</tbody>							
							</table>
						</div>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<!---->
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<!-- Modal Late OT-->
	<div class="modal fade" id="modalLateOT" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:750px;">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Late File OT</h4>
		  </div>
		  <div class="modal-body">
				<div class="row">
					<div class="col-sm-2 col-sm-offset-10" style="padding:2px;">
						<button type="button" class="btn btn-primary fa fa-user" id="btn_add_late_ot"> File Late OT</button>
						<button type="button" class="btn btn-primary fa fa-remove hide_1_late_ot" id="btn_close_late_ot"> Close</button>
					</div>
					<div id="container_add_late_ot" class="hide_1_late_ot">
						<form id="frm-add_late_ot">
							<datalist id="list_empno"></datalist>
							<div class="row">
								<div class="col-sm-6">
									<style>
										.label_late_ot_width{
											width	: 100px;
										}
										#{
											width	:	70%;
										}
									</style>
									<div class="form-inline">
										<label class="label_late_ot_width">Empno: </label><input class="form-control" type="text" id="txtLateOTEmpno" name="txtLateOTEmpno" list="list_empno" required>
									</div>
									
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<h4>OT Information</h4>
								</div>
								<div class="col-sm-6">
									<div class="form-inline">
										<label class="label_late_ot_width">Date of OT: </label><input class="form-control" type="date" id="txtLateOTDate" name="txtLateOTDate" required>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-inline">
										<label class="label_late_ot_width">OT Count: </label><input class="form-control" type="number" step="0.01" id="txtLateOTCount" name="txtLateOTCount" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-inline">
										<label class="label_late_ot_width">OT Type: </label><select class="form-control" id="txt_LateOTType" name="txt_LateOTType">
											<option value="1">Regular</option>
											<option value="2">Sunday / Special Holiday</option>
											<option value="3">Sunday</option>
											<option value="4">Sunday / Legal Holiday</option>
											<option value="5">Special Non-Working Holiday</option>
											<option value="6">Legal Sunday</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-inline">
										<label class="label_late_ot_width">Month Paid: </label><input type="month" class="form-control" id="txt_LateOTMonth" name="txt_LateOTMonth" required>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-inline">
										<div class="form-inline">
											<label class="label_late_ot_width">Cutoff Paid: </label><select class="form-control" id="txt_LateOTCutoff" name="txt_LateOTCutoff">
												<option value="10">1st Cutoff</option>
												<option value="20">2nd Cutoff</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-2 col-sm-offset-10" style="padding:5px;">
								<button type="submit" class="btn btn-success fa fa-save" id="btn_save_late_ot">Save</button>
							</div>
						</form>
					</div>
					<div id="container_tbl_late_ot">
						<div class="col-sm-12" style="padding:5px;">
							<table id="tbl_late_ot" class="table">
								<thead>
									<tr>
										<th>Empno</th>
										<th>Date of OT</th>
										<th>OT Count</th>
										<th>OT Type</th>
										<th>Date Paid</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								</tbody>				
							</table>
						</div>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<!---->
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<div class="modal fade" tabindex="-1" role="dialog" id="system_message">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">System Message</h4>
		  </div>
		  <div class="modal-body">
			
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	

</body>
</html>

<script>
// $('#myTabs a').click(function (e) {
  // e.preventDefault()
  // $(this).tab('show')
// });
// $('#myTabs a[href="#profile"]').tab('show') // Select tab by name
// $('#myTabs a:first').tab('show') // Select first tab
// $('#myTabs a:last').tab('show') // Select last tab
// $('#myTabs li:eq(2) a').tab('show') // Select third tab (0-indexed)
</script>

<script>
var username = " <?php echo $username; ?> ";
var x;
var iPos;
var page;
var pkiddel;
var empnodel;
var datedel;
//datatable

var selected_row = '';

$(document).ready(function() {
$('#loader').hide();

var date_start = $('#txtStartDate').val();
var date_end = $('#txtEndDate').val();
	

var oTable = $('#tblMain').DataTable({
	"scrollX"	 : true,
	"bProcessing": true,
	"bServerSide": true,
	"aaSorting"	 : [ [ 3 ,"asc"] ],
	"sAjaxSource": "./server_side_scripts/tfm.php?date_start="+date_start+"&date_end="+date_end
}); 

var oTable_offset = $('#tbl_offset').DataTable({
	"bProcessing": true,
	"bServerSide": true,
	"sAjaxSource": "./server_side_scripts/offset.php"
});

var oTable_holiday = $('#tbl_holiday').DataTable({
	"bProcessing": true,
	"bServerSide": true,
	"sAjaxSource": "./server_side_scripts/holiday.php"
});

var oTable_yec_training = $('#tbl_yec_training').DataTable({
	"bProcessing": true,
	"bServerSide": true,
	"sAjaxSource": "./server_side_scripts/yec_training.php"
});

var oTable_late_ot = $('#tbl_late_ot').DataTable({
	"bProcessing": true,
	"bServerSide": true,
	"aaSorting"	 : [],
	"sAjaxSource": "./server_side_scripts/late_ot.php"
});

$('#btn_export_excel').click(function(){
	window.location.href = "tfm-ExportToExcel.php?startDate="+$('#txtStartDate').val()+"&endDate="+$('#txtEndDate').val()
});

//function for selecting a row and place a value on pkid, empno, date, for deletion purposes
	$('#tblMain').on("click","tbody tr", function (event) { 
		// iPos = oTable.fnGetPosition( this );
		selected_row = $(this);
		$('#tblMain tbody tr').attr('style' , '');
		$(this).attr('style' , 'background-color:teal;color:black;');
		
		//Set variable data for delete
		pkiddel = $(this).find(".pkid").val(); //get value from a class
		empnodel = $(this).find(".empno").val(); //get value from a class
		datedel = $(this).find(".date").val(); //get value from a class
		dateOutdel = $(this).find(".date_out").val(); //get value from a class
		timeIndel = $(this).find(".time_in").val(); //get value from a class
		timeOutdel = $(this).find(".time_out").val(); //get value from a class
		
		});
	
	//Function for Deleting Selected Row
	$('#btn-DeleteRow').click( function () {
		//alert ("row "+iPos+" has been deleted!")
		alert("PKID: "+pkiddel+" EMPNO: "+empnodel+" date: "+datedel);
		var data={
			"action" : "deleteDailyTFM",
			"pkid" : pkiddel,
			"empno" : empnodel,
			"date" : datedel,
			"date_out" : dateOutdel,
			"time_in" : timeIndel,
			"time_out" : timeOutdel
		};
		data = $.param(data);
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "reportHandler.php", //Relative or absolute path to response.php file
			data: data,
			success: function(data) {
					oTable.row(selected_row).remove().draw( false );
				}
		});
		return false;
	});

	//Get value of each cell the table
	$('#tblMain').on("dblclick","tbody tr", function (event) { 
		var pkid 		= $(this).find(".pkid").val(); //get value from a class
		var empno 		= $(this).find("td:eq(1)").text(); //get value from a class
		var SalaryType 	= $(this).find("td:eq(2)").text(); //get value from a class
		var date 		= $(this).find("td:eq(3)").text(); //get value from a class
		var time_in 	= $(this).find("td:eq(4)").text(); //get value from a class
		var mode_in 	= $(this).find("td:eq(5)").text(); //get value from a class
		var date_out 	= $(this).find("td:eq(6)").text(); //get value from a class
		var time_out 	= $(this).find("td:eq(7)").text(); //get value from a class
		var mode_out 	= $(this).find("td:eq(8)").text(); //get value from a class
		var reg_hours 	= $(this).find("td:eq(9)").text(); //get value from a class
		var ot_hours 	= $(this).find("td:eq(10)").text(); //get value from a class
		var absent 		= $(this).find("td:eq(11)").text(); //get value from a class
		// var undertime 	= $(this).find("td:eq(12)").text(); //get value from a class
		var slvl_code 	= $(this).find("td:eq(13)").text(); //get value from a class
		var slvl_num 	= $(this).find("td:eq(14)").text(); //get value from a class
		var ot1 		= $(this).find("td:eq(15)").text(); //get value from a class
		var ot2 		= $(this).find("td:eq(16)").text(); //get value from a class
		var ot2a 		= $(this).find("td:eq(17)").text(); //get value from a class
		var ot3 		= $(this).find("td:eq(18)").text(); //get value from a class
		var ot3a 		= $(this).find("td:eq(19)").text(); //get value from a class
		var ot4 		= $(this).find("td:eq(20)").text(); //get value from a class
		var ot4a 		= $(this).find("td:eq(21)").text(); //get value from a class
		var ot5 		= $(this).find("td:eq(22)").text(); //get value from a class
		var ot5a 		= $(this).find("td:eq(23)").text(); //get value from a class
		var ot6 		= $(this).find("td:eq(24)").text(); //get value from a class
		var ot6a 		= $(this).find("td:eq(25)").text(); //get value from a class
		var nightdiff 	= $(this).find("td:eq(26)").text(); //get value from a class
		var aut 		= $(this).find("td:eq(27)").text(); //get value from a class
		
		$('#pkid').val(pkid);
		$('#empno').val(empno);
		$('#SalaryType').val(SalaryType);
		$('#date').val(date);
		$('#time_in').val(time_in);
		$('#mode_in').val(mode_in);
		$('#date_out').val(date_out);
		$('#time_out').val(time_out);
		$('#mode_out').val(mode_out);
		$('#reg_hours').val(reg_hours);
		$('#ot_hours').val(ot_hours);
		$('#absent').val(absent);
		$('#slvl_code').val(slvl_code);
		$('#slvl_num').val(slvl_num);
		get_late_and_undertime(pkid);
		$('#ot1').val(ot1);
		$('#ot2').val(ot2);
		$('#ot2a').val(ot2a);
		$('#ot3').val(ot3);
		$('#ot3a').val(ot3a);
		$('#ot4').val(ot4);
		$('#ot4a').val(ot4a);
		$('#ot5').val(ot5);
		$('#ot5a').val(ot5a);
		$('#ot6').val(ot6);
		$('#ot6a').val(ot6a);
		$('#nightdiff').val(nightdiff);
		$('#aut').val(aut);
		$('#modalUpdate').data('pkid',pkid).modal('show');
	});
	
	$('#btn-Update').click(function(){
		var pkid 		= $('#modalUpdate').data('pkid');
		var empno 		= $('#empno').val();
		var SalaryType 	= $('#SalaryType').val();
		var date 		= $('#date').val();
		var time_in 	= $('#time_in').val();
		var mode_in 	= $('#mode_in').val();
		var date_out 	= $('#date_out').val();
		var time_out 	= $('#time_out').val();
		var mode_out 	= $('#mode_out').val();
		var reg_hours 	= $('#reg_hours').val();
		var ot_hours 	= $('#ot_hours').val();
		var absent 		= $('#absent').val();
		// var undertime 	= $('#undertime').val();
		var late_conv	= $('#late_update').val();
		var under_conv	= $('#undertime_update').val();
		var slvl_code 	= $('#slvl_code').val();
		var slvl_num 	= $('#slvl_num').val();
		var ot1 		= $('#ot1').val();
		var ot2 		= $('#ot2').val();
		var ot2a 		= $('#ot2a').val();
		var ot3 		= $('#ot3').val();
		var ot3a 		= $('#ot3a').val();
		var ot4 		= $('#ot4').val();
		var ot4a 		= $('#ot4a').val();
		var ot5 		= $('#ot5').val();
		var ot5a 		= $('#ot5a').val();
		var ot6 		= $('#ot6').val();
		var ot6a 		= $('#ot6a').val();
		var nightdiff 	= $('#nightdiff').val();
		var aut 		= $('#aut').val();
		
		// if(time_in != "" || time_out != "" || date != "" || date_out != ""){
			// absent = "0";
		// }
		// if(slvl_code=="7"){
			// absent = "0";
		// }

		var data = {
				"action" 	: "updateDailyTFM",
				"pkid" 		: pkid, 
				"empno" 	: empno, 
				"date" 		: date, 
				"time_in" 	: time_in, 
				"mode_in" 	: mode_in, 
				"date_out" 	: date_out, 
				"time_out" 	: time_out,
				"mode_out" 	: mode_out,
				"reg_hours" : reg_hours,
				"ot_hours" 	: ot_hours,
				"absent" 	: absent,
				// "undertime" : undertime,
				"late_conv" : late_conv,
				"under_conv": under_conv,
				"slvl_code" : slvl_code,
				"slvl_num" 	: slvl_num,
				"ot1" 		: ot1,
				"ot2" 		: ot2,
				"ot2a" 		: ot2a,
				"ot3" 		: ot3,
				"ot3a" 		: ot3a,
				"ot4" 		: ot4,
				"ot4a" 		: ot4a,
				"ot5" 		: ot5,
				"ot5a" 		: ot5a,
				"ot6" 		: ot6,
				"ot6a" 		: ot6a,
				"nightdiff" : nightdiff,
				"aut" 		: aut,
				"username" 	: username
			};
			
				
		data = $.param(data);
		$.ajax({
			  type: "POST",
			  dataType: "json",
			  url: "reportHandler.php", //Relative or absolute path to response.php file
			  data: data,
			  success: function(data) {
				alert(data['msg']);
				// $('body').append(data['msg']);
				// $('body').append(data['tklogs_result']);
				oTable.cell( selected_row.find('td:eq(1)') ).data($('#empno').val());
				oTable.cell( selected_row.find('td:eq(2)') ).data($('#SalaryType').val());
				oTable.cell( selected_row.find('td:eq(3)') ).data($('#date').val());
				oTable.cell( selected_row.find('td:eq(4)') ).data($('#time_in').val());
				oTable.cell( selected_row.find('td:eq(5)') ).data($('#mode_in').val());
				oTable.cell( selected_row.find('td:eq(6)') ).data($('#date_out').val());
				oTable.cell( selected_row.find('td:eq(7)') ).data($('#time_out').val());
				oTable.cell( selected_row.find('td:eq(8)') ).data($('#mode_out').val());
				oTable.cell( selected_row.find('td:eq(9)') ).data($('#reg_hours').val());
				oTable.cell( selected_row.find('td:eq(10)') ).data($('#ot_hours').val());
				oTable.cell( selected_row.find('td:eq(11)') ).data($('#absent').val());
				oTable.cell( selected_row.find('td:eq(12)') ).data( parseFloat($('#late_update').val()) + parseFloat($('#undertime_update').val()));
				oTable.cell( selected_row.find('td:eq(13)') ).data($('#slvl_code').val());
				oTable.cell( selected_row.find('td:eq(14)') ).data($('#slvl_num').val());
				oTable.cell( selected_row.find('td:eq(15)') ).data($('#ot1').val());
				oTable.cell( selected_row.find('td:eq(16)') ).data($('#ot2').val());
				oTable.cell( selected_row.find('td:eq(17)') ).data($('#ot2a').val());
				oTable.cell( selected_row.find('td:eq(18)') ).data($('#ot3').val());
				oTable.cell( selected_row.find('td:eq(19)') ).data($('#ot3a').val());
				oTable.cell( selected_row.find('td:eq(20)') ).data($('#ot4').val());
				oTable.cell( selected_row.find('td:eq(21)') ).data($('#ot4a').val());
				oTable.cell( selected_row.find('td:eq(22)') ).data($('#ot5').val());
				oTable.cell( selected_row.find('td:eq(23)') ).data($('#ot5a').val());
				oTable.cell( selected_row.find('td:eq(24)') ).data($('#ot6').val());
				oTable.cell( selected_row.find('td:eq(25)') ).data($('#ot6a').val());
				oTable.cell( selected_row.find('td:eq(26)') ).data($('#nightdiff').val());
				oTable.cell( selected_row.find('td:eq(27)') ).data($('#aut').val());
									
				$('#modalUpdate').modal('hide');
				
			}
		});
		return false;
	});
	
	function get_late_and_undertime(pkid){
		$.ajax({
			type	: "post",
			dataType: "json",
			url		: "reportHandler.php",
			data	: "action=get_undertime_late&pkid="+pkid,
			success	:function(result){
				$('#late_update').val(result['late']);
				$('#undertime_update').val(result['undertime']);
			},error	: function(){
				alert('error handler');
			}
		});
	}

	//START DATE VALIDATION
	$('#txtStartDate').change(function(){
		var startDate = $('#txtStartDate').val();
		var endDate = $('#txtEndDate').val();
			if (startDate>endDate){
				$('#txtStartDate').val(endDate);  
			}
	});
	//END DATE VALIDATION
	$('#txtEndDate').change(function(){
		var startDate = $('#txtStartDate').val();
		var endDate = $('#txtEndDate').val();
			if (endDate<startDate){
				$('#txtEndDate').val(startDate);  
			}
	});
	
	$('#btnRecompute').click(function(){
		if (!confirm("Compute TFM Data?") ) {
			return false;
		}
		$('#system_message .modal-body').empty();
		$('#system_message .modal-body').append('<center><h4 class="fa fa-calculator fa-lg"> Recomputing data.. Please be patient..</h4></center>');
		$('#system_message').modal('show');
		
		$('#system_message').modal({
			backdrop: 'static',
			keyboard: false
		});
		
		$('#system_message button').hide();
			
		var username = "<?php echo $username; ?>";
		var startDate = $('#txtStartDate').val();
		var endDate = $('#txtEndDate').val();
		data = {
			// "action" : "recomputeTFM",
			"action" : "compute_employee_tfm_data",
			"username" : username,
			"startDate" : startDate,
			"endDate" : endDate
		}
		
		data = $.param(data);
		
		$.ajax({
		  type: "POST",
		  dataType: "json",
		  url: "reportHandler.php", //Relative or absolute path to response.php file
		  data: data,
		  success: function(result) {
			if(result['msg'] != ''){
				$('#system_message .modal-body').empty();
				$('#system_message .modal-body').append(result['msg']);
				return false;
			}
			// alert(result['html']);
			$('#system_message .modal-body').empty();
			$('#system_message .modal-body').append(result['html']);
			$('#tbl_recompute').DataTable();
			$('#system_message button').show();
			oTable.ajax.reload();
		  }
	   }); //return false;
	});
	
	$('#btnHoliday').click(function(){
		$('#container_add_holiday').hide();
		$('#modalHoliday').modal('show');
	});
	
	$('#btn_add_holiday').click(function(){
		$('#container_add_holiday').toggle(400);
	});
	
	$('#btn_save_holiday').click(function(){
		var date = $('#txtHoliday').val();
		var remarks = $('#txtHoliday_Remarks').val();
		var type = $('#txtHoliday_Type').val();
		var data = {
			"action"	: "save_holiday",
			"date"		: date,
			"remarks"  	: remarks,
			"type"  	: type,
			"username"	: username
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				alert(result['msg']);
				oTable_holiday.ajax.reload();
			}
		});
	});
	
	$('#tbl_holiday tbody').on('click', 'tr .btn-danger', function(){
		var pkid = $(this).val();
		// alert(pkid);
		var data = {
			"action"	: "delete_holiday",
			"pkid"      : pkid,
			"username"	: username
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				// alert(result['msg']);
				oTable_holiday.ajax.reload();
			},error : function(){
				alert('error handler');
			}
		});
	});
	
	$('#btnOffset').click(function(){
		$('#container_add_offset').hide();
		load_all_empno();
		$('#modalOffset').modal('show');
	});
	
	$('#btn_add_offset').click(function(){
		$('#container_add_offset').toggle(400);
	});
	
	$('#btn_save_offset').click(function(){
		/* validate if empno exist */
		var empno = $('#txtOffsetEmpno').val();
		var date  = $('#txtOffsetDate').val();
		validate_existing_empno(empno,function(result){
			if(!result){
				alert("Employee does not exist!");
				return false;
			}
		});
		var data = {
			"action"	: "save_offset",
			"empno"     : empno,
			"date"		: date,
			"username"	: username
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				alert(result['msg']);
				oTable_offset.ajax.reload();
			}
		});
	});
	
	$('#tbl_offset tbody').on('click', 'tr .btn-danger', function(){
		var pkid = $(this).val();
		var data = {
			"action"	: "delete_offset",
			"pkid"      : pkid,
			"username"	: username
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				// alert(result['msg']);
				oTable_offset.ajax.reload();
			},error : function(){
				alert('error handler');
			}
		});
	});
	
	/* Late filled OT */
	$('#btnLateOT').click(function(){
		load_all_empno();
		$('.hide_1_late_ot').hide();
		$('#modalLateOT').modal('show');
	});
	
	$('#btn_add_late_ot').click(function(){
		$('#container_add_late_ot').toggle(400);
	});
	
	$('#frm-add_late_ot').submit(function(e){
		e.preventDefault();
		add_late_ot( $(this).serialize() );
		
	});
	
	function add_late_ot(data){
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: "action=add_late_ot&"+data+"&username="+username,
			url		: "reportHandler.php",
			success	: function(result){
				alert(result['msg']);
				oTable_late_ot.ajax.reload();
			}			
		});
	}
	
	$('#tbl_late_ot tbody').on('click', 'tr .btn-danger', function(){
		if(confirm("Delete this record?")){
			var pkid = $(this).data('id');
			delete_late_ot(pkid);
		}		
	});
	
	function delete_late_ot(pkid){
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: "action=delete_late_ot&pkid="+pkid+"&username="+username,
			url		: "reportHandler.php",
			success	: function(result){
				alert(result['msg']);
				oTable_late_ot.ajax.reload();
			}			
		});
	}
	
	$('#btnYECTraining').click(function(){
		load_all_empno();
		$('#modalYECTraining').modal('show');
	});
	
	$('#btn_save_yec_training').click(function(){
		if( $('#txtYECTrainingEmpno').val() == '' || $('#txtDateEndTraining').val() == ''){
			alert('Please fill in all blanks');
			return false;
		}
		var data = {
			"action"			: "add_yec_training",
			"empno"      		: $('#txtYECTrainingEmpno').val(),
			"training_date_end"	: $('#txtDateEndTraining').val(),
			"username"			: username
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				alert(result['msg']);
				$('#txtYECTrainingEmpno').val('');
				$('#txtDateEndTraining').val('');
				oTable_yec_training.ajax.reload();
			},error : function(){
				alert('error handler');
			}
		});
	});
	
	$('#tbl_yec_training tbody').on('click', 'tr .btn-danger', function(){
		var pkid = $(this).val();
		var data = {
			"action"	: "delete_yec_training",
			"pkid"      : pkid,
			"username"	: username
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				// alert(result['msg']);
				oTable_yec_training.ajax.reload();
			},error : function(){
				alert('error handler');
			}
		});
	});
			
	// $('#btnSetHoliday').click(function(){
		// var Holiday = $('#txtHoliday').val();
		
		// var data = {
		// "action" : "deleteHolidayAbsent",
		// "date" : Holiday
		// };
		
		// $.param(data);
		// $.ajax({
			  // type: "POST",
			  // dataType: "json",
			  // url: "reportHandler.php", //Relative or absolute path to response.php file
			  // data: data,
			  // success: function(data) {
					// alert(Holiday + " has been set to holiday!");
					// $('#modalHoliday').modal('hide');
			// }
		// });
		// return false;
	// });
	
	$('#set_date_1').click(function(){
		$('#set_date_type').val('1');
	});
	
	$('#set_first_cutoff').click(function(){
		$('#set_date_type').val('2');
	});
	
	$('#set_second_cutoff').click(function(){
		$('#set_date_type').val('3');
	});
	
	$('#frm_set_date').submit(function(e){
		e.preventDefault();
		var start_date = $('#txtStartDate').val();
		var end_date = $('#txtEndDate').val();
		
		if($('#set_date_type').val() == 1){
			window.location.href="dailyTFM.php?txtStartDate="+start_date+"&txtEndDate="+end_date
		}
		
		if($('#set_date_type').val() == 2){
			var selected_month = $('#txt_month_range').val();
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
					window.location.href="dailyTFM.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
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
					window.location.href="dailyTFM.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
				}
			});
		}
	});

});

function load_all_empno(){
	var data = {
		"action" : "load_all_empno"
	}
	data = $.param(data);
	
	$.ajax({
		type		: "post",
		dataType	: "json",
		data		: data,
		url		: "reportHandler.php",
		success	: function(result){
			$('#list_empno').empty();
			for(x=0;x<result.length;x++){
				$('#list_empno').append('<option>'+result[x]+'</option>');
			}
		},error : function(){
			alert('error handler');
		}
	});
}



function validate_existing_empno(empno,callback){
	var data = {
		"action" : "validate_existing_empno",
		"empno"  : empno
	}
	data = $.param(data);
	
	$.ajax({
		type		: "post",
		dataType	: "json",
		data		: data,
		url		: "reportHandler.php",
		success	: function(result){
			callback(result['valid']);
		},error : function(){
			alert('error handler');
		}
	});
}



</script>

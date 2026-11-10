<?php
	require_once("includes.php"); 
	require_once("otksr-oop.php");
	session_start();
	$username = $_SESSION['username'];
	$dateToday = date('Y-m-d');
	echo '<input type="hidden" id="hd_uname" value="'.$username.'">';
	require_once('check_login.php');
	
	if(isset($_GET)){
		$date_start = $_GET['txtStartDate'];
		$date_end = $_GET['txtEndDate'];
	}

	if($date_start=="" || $date_start==""){
		$date_start = $dateToday;
		$date_end = $dateToday;
	}

	if(isset($_GET['month'])){
		$month = $_GET['month'];
	}else{
		$month = '';
	}
?>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>OTKS REPORT</title>
</head>
<style>
.hide_column{
    display : none;
}
</style>
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
				<li class="active"><a href="index.php" id="loadRawData" class="fa fa-tasks fa-lg"> RAW DATA</a></li>
				<li class=""><a href="dailyTFM.php?txtStartDate=<?php echo $date_start; ?>&txtEndDate=<?php echo $date_end; ?>&month=<?php echo $month; ?>" class="fa fa-tasks fa-lg" id="loadDailyTFM"> TFM</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL" class="fa fa-upload fa-lg"> SL/VL</a></li>
				<li class=""><a href="eom.php" id="loadEom" class="fa fa-tasks fa-lg"> EOM</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard" class="fa fa-user fa-lg"> Timecard</a></li>
				<li class=""><a href="passRequests.php" id="" class="fa fa-car fa-lg"> Pass Requests</a></li>
				<li class=""><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>
			 </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	<div class="row panel panel-primary top-container">
		<div class="panel-heading" style="margin-bottom:10px;"><b>RAW DATA</b></div>
		
		<div class="col-sm-8">	
			<form class="form-inline" action="#" id="frm_date_range">
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
			</form>
		</div>	
		
		<div class="col-sm-3 col-sm-offset-1">
			Cut-off Information <br>
			30 - 14 and 15 - 29 
			<div class="form-inline"> 
				<?php
				if(isset($_GET['empno'])){
					$empno_search = $_GET['empno'];
				}else{
					$empno_search = '';
				}
				?>
				<label class="">Employee Number</label>
				<input type="text" class="form-control" id="txt_employee_number_search" value="<?php echo $empno_search; ?>">
				<button class="btn btn-primary" id="btn_employee_search">
					<span class="fa fa-search"></span> Search
				</button>
			</div>
		</div>
		
			
	
</div>
<div class="row panel panel-default mid-container">
	<div class="col-sm-12">
		<p class="align-r">
			<button id="btn-AddRec" class="btn btn-default fa fa-user fa-lg"> Add Record</button>
			<button id="btn-DeleteRow" class="btn btn-default fa fa-trash fa-lg"> Delete Selected</button>
			<!--<button id="btn-StraightHours" class="btn btn-default fa fa-edit fa-lg"> Straight Hours</button>-->
			<button id="btn-GenerateIR" class="btn btn-default fa fa-print fa-lg"> Inconsistency Report</button>
		
			  <button class="fa btn btn-default">
				  <div class="dropdown ">
					  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color:black;"><span class="fa fa-gear"> Batch Update</span> <span class="caret"></span></a>
					  <ul class="dropdown-menu">
						<li><a href="#" id="btn_move_time_in" class="fa fa-arrow-right"> Adjust Time In</a></li>
						<li role="separator" class="divider" style="padding:0px;margin:0px;"></li>
						<li><a href="#" id="btn_move_time_out" class="fa fa-arrow-right"> Adjust Time Out</a></li>
						<li role="separator" class="divider" style="padding:0px;margin:0px;"></li>
						<li><a href="#" id="btn_export_attendance_information" class="fa fa-file-excel-o"> Export Attendance (XLSX)</a></li>
						<li role="separator" class="divider" style="padding:0px;margin:0px;"></li>
						<li><a href="#" id="btn_import_attendance_information" class="fa fa-file-excel-o"> Import Attendance (CSV)</a></li>
						<!--<li role="separator" class="divider" style="padding:0px;margin:0px;"></li>
						<li><a href="#" id="btn_move_time_in_prev" class="fa fa-arrow-left"> Adjust Time In</a></li>
						<li role="separator" class="divider" style="padding:0px;margin:0px;"></li>
						<li><a href="#" id="btn_move_time_out_prev" class="fa fa-arrow-left"> Adjust Time Out</a></li>-->
					  </ul>
				 </div>
			 </button>
		</p>
	</div>
	<div class="col-sm-12">	
		<table class="display" id="tblMain" border="1">
			<thead>
				<tr>
					<th>Inconsistency</th>
					<th>PKID</th>
					<th>Date IN</th>
					<th>Time In</th>
					<th>Mode IN</th>
					<th>Date OUT</th>
					<th>Time Out</th>
					<th>Mode OUT</th>
					<th>EN</th>
					<th>Name</th>
					<th>Department</th>
					<th>Position</th>
					<th>Section</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
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
						<label style="margin-right:20px;width:160px">EN: </label><input  class="form-control"  type="text" id="txtEmpNo" placeholder="ID"readonly style = "width:25%;"><br>
						<hr>
						<h4>Time In Information</h4>
						<div class = "form-inline">
							<label style="margin-right:20px;width:160px">Date In: </label><input class="form-control" type="date" id="txtDateIn" placeholder="ECode" style = "width:25%;">
							<label style="margin-right:20px;width:160px">Time IN: </label><input class="form-control" type="time" id="txtTimeIn" placeholder="Time IN" step="1" style = "width:25%;">
						</div>
						<label style="margin-right:15px;width:160px">Mode In: </label>
						<select class="form-control" id="txtModeIn" style = "width:25%;">
							<option value=""></option>
							<option value="IN">IN</option>
							<option value="BUS LATE">BUS LATE</option>
							<option value="OB">OB</option>
						</select>
						<!--<p class="align-r"><button type="button" class="btn btn-danger fa fa-trash" id="btn_clear_time_in">Clear Time-In</button></p>-->
						<hr>
						<h4>Time Out Information</h4>
						<label style="margin-right:20px;width:160px">Date Out: </label><input class="form-control" type="date" id="txtDateOut" placeholder="In" style = "width:25%;">
						<label style="margin-right:20px;width:160px">Time OUT: </label><input class="form-control" type="time" id="txtTimeOut" placeholder="IN Remarks" step="1" style = "width:25%;">
						<label style="margin-right:20px;width:160px">Mode Out: </label><select class="form-control" id="txtModeOut" style = "width:25%;">
						<option value=""></option>
						<option value="OUT">OUT</option>
						<option value="OB">OB</option>
						<option value="UNDERTIME">UNDERTIME</option>
						</select><br>
					</div>
				</form>
		  </div>	
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn-Update">Save changes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<!-- Modal Add Row-->
	<div class="modal fade" id="modalAddRec" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:800px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">ADD NEW RECORD <center><label id="exist" style="color:red;">RECORD ALREADY EXIST!</label></center></h4>
		  </div>
		  <div class="modal-body">
			<div class="form-group">
				<h4>Employee Information</h4>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-inline">
							<label class="col-sm-4 control-label">EN </label><input class="form-control" type="text" id="txtEmpNoA" name="txtEmpNoA" name="txtEmpNoA" placeholder="Employee Number">
						</div>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Name </label><input class="form-control" type="text" id="txtNameA" name="txtNameA" placeholder="Name" readonly>
						</div>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Position </label><input class="form-control" type="text" id="txtPositionA" name="txtPositionA" placeholder="Postion" readonly>
						</div>
						
					</div>
					<div class="col-sm-6">
						<div class="form-inline">
							<!--<label class="col-sm-4 control-label">ECode: </label><input  class="form-control" type="text" id="txtECodeA" name="txtECodeA" placeholder="ECode" readonly>-->
						</div>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Department </label><input class="form-control" type="text" id="txtDepartmentA" name="txtDepartmentA" placeholder="Department" readonly>
						</div>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Section </label><input class="form-control" type="text" id="txtSectionA" name="txtSectionA" placeholder="Section" readonly>
						</div>
						
					</div>
				</div>
				<h4>Login Information</h4>
				<div class="row">
					<div class="col-sm-6">
						<h5>Time In:</h5>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Date In </label><input class="form-control" type="date" id="txtDateInA" name="txtDateInA" placeholder="">
						</div>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Mode In </label>
							<select class="form-control" id="txtModeInA">
								<option value=""></option>
								<option value="IN">IN</option>
								<option value="BUS LATE">BUS LATE</option>
								<option value="OB">OB</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<br><br>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Time IN </label><input class="form-control" type="time" id="txtTimeInA" name="txtTimeInA" placeholder="">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<h5>Time Out:</h5>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Date Out </label><input class="form-control" type="date" id="txtDateOutA" name="txtDateOutA" placeholder="">
						</div>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Mode Out </label>
							<select class="form-control" id="txtModeOutA">
								<option value=""></option>
								<option value="OUT">OUT</option>
								<option value="OB">OB</option>
								<option value="UNDERTIME">UNDERTIME</option>
							</select>
						</div>						
					</div>
					<div class="col-sm-6">
						<br><br>
						<div class="form-inline">
							<label class="col-sm-4 control-label">Time OUT </label><input class="form-control" type="time" id="txtTimeOutA" name="txtTimeOutA" placeholder="" >
						</div>
					</div>
				</div>
			</div>
		  </div>
		  <div class="modal-footer">
					<button class="btn btn-primary" id="btn-AddRecModal">ADD RECORD</button>
				</form>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
  <div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_confirmation">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Delete Record</h4>
		  </div>
		  <div class="modal-body">
			
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-danger" id="btn_delete">Delete Record</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
   </div><!-- /.modal -->
	
</body>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_irregular_straight">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">No Filed Straight Hours</h4>
      </div>
      <div class="modal-body">
        <p>One fine body&hellip;</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close Modal</button>
        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal_move_in_out">
  <div class="modal-dialog" style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Move Time-In / Time-Out</h4>
      </div>
      <div class="modal-body">
        <p>One fine body&hellip;</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary fa fa-save">Save</button>
		<button type="button" class="btn btn-default fa fa-close" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal_system_message_straight_confirmation">
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
        <button type="button" class="btn btn-primary">Proceed</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal_move_in_out_confirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">System Message</h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">
			<p><h4>Warning! You will not be able to revert changes once saved.</h4></p>
			<p><h4>Continue?</h4></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary fa fa-save">Continue</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal_upload_file_for_import">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Import Updated (CSV)</h4>
      </div>
      <div class="modal-body">
		<div id="container_upload_file">
			<form id="frm-upload" action="#" method="post" enctype="multipart/form-data">
				<input type="file" name="file_1" id="file_1" required>
				<!--<input type="submit">-->
			
		</div>
		<div id="container_result">
			<input type="hidden" id="hd_pkid">
			
		</div>
	  </div>
      <div class="modal-footer">
				<button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
				<button type="submit" class="btn btn-success fa fa-save" id="btn_save_updated_raw_data" value="save"> Update Data</button>
				<button type="submit" class="btn btn-primary fa fa-upload" id="btn_upload_file_for_import" value="upload"> Upload</button>
			</form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal_export_attendance">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Export Attendance Excel File</h4>
      </div>
      <div class="modal-body">
		<div class="row">
			<div class="col-md-3">
				<label style="font-size:14px;">Employee Number: </label>
			</div>
			<div class="col-md-4">
				<input type="text" class="form-control" id="txt_export_empno" placeholder="Example: J005,J007">
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-3">
				<label style="font-size:14px;">Section: </label>
			</div>
			<div class="col-md-5">
				<input type="text" class="form-control" id="txt_export_section">
			</div>
		</div>
	  </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
		<button type="submit" class="btn btn-success fa fa-file-excel-o" id="btn_export_to_excel" value="save"> Export Data</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</html>
<div id="sample_div"></div>
<script type="text/javascript" src="./js/common_functions.js"></script>

<script type="text/javascript">
var handler = 'reportHandler.php';
var username = $('#hd_uname').val();
var oTable = '';
var selected_row = '';

var start_date = $('#txtStartDate').val();
var end_date   = $('#txtEndDate').val();
var empno 	   = $('#txt_employee_number_search').val();
var table_id   = 'tblMain';

$(document).ready(function() {
	oTable = $('#'+table_id).DataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting"	 : [ [1,"asc"] ],
		"pageLength" : 10,
		"sAjaxSource": "./server_side_scripts/raw_data.php?date_start="+start_date+"&date_end="+end_date+"&empno="+empno,
		"columnDefs": [
			{ className: "hide_column", "targets": [ 1 ] }
		  ]
	} );
	
} );

var array_update = [];

$('#' + table_id + ' tbody').on('dblclick','tr',function(){
	array_update = [];
	selected_row = $(this);
	
	array_update = {
		"pkid"	 	 : selected_row.find('td:eq(1)').text(),
		"date"	 	 : selected_row.find('td:eq(2)').text(),
		"time_in"	 : selected_row.find('td:eq(3)').text(),
		"mode_in"	 : selected_row.find('td:eq(4)').text(),
		"date_out"	 : selected_row.find('td:eq(5)').text(),
		"time_out"	 : selected_row.find('td:eq(6)').text(),
		"mode_out"	 : selected_row.find('td:eq(7)').text(),
		"empno"	 	 : selected_row.find('td:eq(8)').text(),
		"name"	 	 : selected_row.find('td:eq(9)').text(),
		"Position"	 : selected_row.find('td:eq(10)').text(),
		"Department" : selected_row.find('td:eq(11)').text(),
		"Section"	 : selected_row.find('td:eq(12)').text()
	}
	// alert(array_update['pkid']);
	// return false;
	
	$('#txtEmpNo').val(array_update['empno']);
	$('#txtDateIn').val(array_update['date']);
	$('#txtTimeIn').val(array_update['time_in']);
	$('#txtModeIn').val(array_update['mode_in']);
	$('#txtDateOut').val(array_update['date_out']);
	$('#txtTimeOut').val(array_update['time_out']);
	$('#txtModeOut').val(array_update['mode_out']);
	
	$('#modalUpdate').modal('show');
});

$('#' + table_id + ' tbody').on('click','tr',function(){
	selected_row = $(this);
	$('#' + table_id + ' tbody tr').attr('style','');
	selected_row.attr('style','background-color:teal;color:white;');
});

//Function to Open Add Record Modal
$('#btn-AddRec').click( function () {
	$('#exist').hide();
	$('#modalAddRec').modal('show'); 
});	
		
$('#btn-Update').click(function(){
	update_row();
});

$('#btn-DeleteRow').click(function(){
	delete_row_confirmation();
});

$('#btn-GenerateIR').click(function(){
	window.open("inconsistencyrecord.php?date="+start_date+"&date_out="+end_date);
});

$('#btn_move_time_in').click(function(){
	move_time_in();
});

$('#btn_move_time_out').click(function(){
	move_time_out();
});

$('#btn_move_time_in_prev').click(function(){
	move_time_in_prev();
});

$('#btn_move_time_out_prev').click(function(){
	move_time_out_prev();
});

$('#txtEmpNoA').change(function(){
	load_employee_information($(this).val());
});

$('#btn_export_attendance_information').click(function(){
	$('#txt_export_empno').val(empno);
	$('#modal_export_attendance').modal('show');
});

$('#btn_export_to_excel').click(function(){
	var empno_export = $('#txt_export_empno').val();
	window.location.href = "excel_attendance_export.php?startDate=" + start_date + "&endDate=" + end_date + '&EN=' + empno_export;
	$('#modal_export_attendance').modal('hide');
});

$('#btn_import_attendance_information').click(function(){
	$('#container_result').empty();
	$('#container_upload_file').show();
	$('#btn_save_updated_raw_data').hide();
	$('#btn_upload_file_for_import').show();
	$('#modal_upload_file_for_import').modal('show');
});

var tbl_attendance_update = '';
$('button[type="submit"]').on('click', function(){
      $('#frm-upload').data('button', this.value);
});

$('#frm-upload').on('submit',function(e) {
	 e.preventDefault();
	 var submitButton = $(this).data('button');

	 /* save function */
	 if(submitButton == 'save'){
		
		var table_data = tbl_attendance_update.rows().data();	
		var x_end = table_data.length;
		/* initialize array value */
		var record 			= {};
			record.empno 	= [];
			record.date_in 	= [];
			record.time_in 	= [];
			record.mode_in 	= [];
			record.date_out = [];
			record.time_out = [];
			record.mode_out = [];
			record.pkid 	= [];
		
		for(x=0;x<x_end;x++){
			record.empno.push( table_data[x][0] );
			record.date_in.push( table_data[x][1] );
			record.time_in.push( table_data[x][2] );
			record.mode_in.push( table_data[x][3] );
			record.date_out.push( table_data[x][4] );
			record.time_out.push( table_data[x][5] );
			record.mode_out.push( table_data[x][6] );
			record.pkid.push( table_data[x][7] );
		}		
		// alert(record.empno[50]);
		
		var data = {
			"action"	: "update_raw_data_from_csv",
			"record"	: record
		}
		
		data = $.param(data);
		
		$('#container_result').empty();	
		$('#container_result').prepend('<p><h3>Updating Data, Please Wait..</h3></p>');
		$('#btn_save_updated_raw_data').hide();
		$.ajax({
			type	: "POST",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(data){
				$('#container_result').empty();
				$('#container_result').append('<div class="alert alert-success"><h3><span class="fa fa-save"></span>  Attendance update success!</h3></div>');
				$('#btn_save_updated_raw_data').hide();
				oTable.ajax.reload();
			},error	: function(){
				alert('error handler');
			}
		});
	 }
	 
	 /* upload function */
	 if(submitButton == 'upload'){
		  var form_data = new FormData(this);
		  form_data.append("action","upload_attendance_csv");
		  $('#container_upload_file').hide();
		  $('#btn_upload_file_for_import').hide();
		  $('#container_result').empty();
		  $('#container_result').append('<p>Importing Data, Please wait..</p>');	
		  
		  $.ajax({
			   url: "reportHandler.php",    // Url to which the request is send
			   type: "POST",             	// Type of request to be send, called as method
			   data: form_data, 		 	// Data sent to server, a set of key/value pairs (i.e. form fields and values)
			   contentType: false,       	// The content type used when sending data to the server.
			   cache: false,             	// To unable request pages to be cached
			   processData:false,        	// To send DOMDocument or non processed data file it is set to false
			   success: function(data)   	// A function to be called if request succeeds
			   {
				
				$('#btn_save_updated_raw_data').show();
				$('#container_result').empty();
				$('#container_result').append(data);
				tbl_attendance_update = $('#tbl_attendance_update').DataTable();
			   }, error : function (data) {
					
			   }
		  });
	  }
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
	var start_date = $('#txtStartDate').val();
	var end_date = $('#txtEndDate').val();
	
	if($('#set_date_type').val() == 1){
		window.location.href="index.php?txtStartDate="+start_date+"&txtEndDate="+end_date
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
				window.location.href="index.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
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
				window.location.href="index.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
			}
		});
	}
});

/* search specific employee */
$('#btn_employee_search').click(function(){
	var start_date 	= "<?php echo $date_start; ?>";
	var end_date 	= "<?php echo $date_end; ?>";
	var month 		= "<?php echo $month; ?>";
	if(start_date != '' && end_date != ''){
		var search_1 	=  "txtStartDate="+start_date+"&txtEndDate="+end_date;
	}else{
		var search_1    = '';
		alert("Please select a date range first!");
	}
	if(month != ''){
		var complete_search = search_1 +'&month='+month;
	}else{
		var complete_search = search_1;
	}
	var empno = $('#txt_employee_number_search').val();
	if(empno == ''){
		return false;
	}
	complete_search += '&empno='+empno;
	// alert("index.php?" + complete_search);
	window.location.href= "index.php?" + complete_search;
});

$('#txt_employee_number_search').change(function(){
	var start_date 	= "<?php echo $date_start; ?>";
	var end_date 	= "<?php echo $date_end; ?>";
	var month 		= "<?php echo $month; ?>";
	if(start_date != '' && end_date != ''){
		var search_1 	=  "txtStartDate="+start_date+"&txtEndDate="+end_date;
	}else{
		var search_1    = '';
		alert("Please select a date range first!");
	}
	if(month != ''){
		var complete_search = search_1 +'&month='+month;
	}else{
		var complete_search = search_1;
	}
	var empno = $('#txt_employee_number_search').val();
	if(empno == ''){
		// return false;
	}
	complete_search += '&empno='+empno;
	// alert("index.php?" + complete_search);
	window.location.href= "index.php?" + complete_search;
});

function load_employee_information(empno){
	var data = {
		"action"	: "load_employee_information",
		"empno"		: empno
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "reportHandler.php",
		success	: function(result){
			$('#txtEmpNoA').val(result['EmpNo']);
			$('#txtNameA').val(result['Name']);
			$('#txtPositionA').val(result['Position']);
			$('#txtDepartmentA').val(result['Department']);
			$('#txtSectionA').val(result['Section']);
		},error	: function(){
			alert('error');
		}
	});
}

//Function for adding row on table

$("#btn-AddRecModal").click(function(){  
	add_row();
});

function add_row(){
	var empno 		= $('#txtEmpNoA').val(); //get value from textbox id
	var ECode 		= $('#txtECodeA').val();
	var name 		= $('#txtNameA').val();
	var department 	= $('#txtDepartmentA').val();
	var position 	= $('#txtPositionA').val();
	var section 	= $('#txtSectionA').val();
	var date 		= $('#txtDateInA').val(); //get value from textbox id
	var time_in 	= $('#txtTimeInA').val();
	var mode_in 	= $('#txtModeInA').val();
	var date_out 	= $('#txtDateOutA').val();
	var time_out 	= $('#txtTimeOutA').val();
	var mode_out 	= $('#txtModeOutA').val();
						
	//pass data into an array					
	var data = {
		  "action"	 : "addRawData", //action passed on handler
		  "empno" 	 : empno,
		  "date" 	 : date,
		  "time_in"  : time_in,
		  "mode_in"  : mode_in,
		  "date_out" : date_out,
		  "time_out" : time_out,
		  "mode_out" : mode_out,
		  "username" : username
		  
	  };
	
	data = $.param(data); //turn data into serialize form
		
	$.ajax({
	  type: "POST",
	  dataType: "json",
	  url: "reportHandler.php", //Relative or absolute path 
	  data: data,
	  success: function(data) {
		// $('body').append(data['msg2']);
		if(data['msg'] == 0){
			$('#tblMain').dataTable().fnAddData([
					date,
					time_in,
					date_out,
					time_out,
					empno,
					name,
					position,
					department,
					section,
					"",// for the hidden column pkid
					"",// for the hidden column pkid
					"",// for the hidden column pkid
					"" // for the hidden column pkid
				]);
				$('#modalAddRec').modal('hide'); //closes modal on adding success
		  }else{
				$('#exist').show(); //shows a record exist in the modal if duplicate record occurs
		  }
	  }
	});
	return false;
}

function update_row(){
	var array_new_values = {
		"pkid"	 	 : selected_row.find('td:eq(1)').text(),
		"date"	 	 : $('#txtDateIn').val(),
		"time_in"	 : $('#txtTimeIn').val(),
		"mode_in"	 : $('#txtModeIn').val(),
		"date_out"	 : $('#txtDateOut').val(),
		"time_out"	 : $('#txtTimeOut').val(),
		"mode_out"	 : $('#txtModeOut').val(),
		"empno"	 	 : selected_row.find('td:eq(8)').text(),
		"name"	 	 : selected_row.find('td:eq(9)').text(),
		"Position"	 : selected_row.find('td:eq(10)').text(),
		"Department" : selected_row.find('td:eq(11)').text(),
		"Section"	 : selected_row.find('td:eq(12)').text()
	}
	
	if($('#txtTimeOut').val() == "" || $('#txtDateOut').val() == ""){
		array_new_values['mode_out'] 	= "";
	}
	
	if($('#txtDateIn').val() == "" || $('#txtTimeIn').val() == ""){
		array_new_values['mode_in'] 	= "";
	}
	
	var data = {
		"action"       : "updateRawData",
		"array_update" : array_new_values,
		"username"     : username
	}
	
	call_ajax(data, handler, function(result){
		alert(result['msg']);
		oTable.ajax.reload();
		$('#modalUpdate').modal('hide');
	});
}

function delete_row_confirmation(){
	var pkid = selected_row.find('td:eq(1)').text();
	var empno = selected_row.find('td:eq(8)').text();
	var datein = selected_row.find('td:eq(2)').text();
	var timein = selected_row.find('td:eq(3)').text();
	var dateout = selected_row.find('td:eq(5)').text();
	var timeout = selected_row.find('td:eq(6)').text();
	var message  = '<div class="row"> ';
		message += '	<div class="col-sm-3" style="padding:40px;">';
		message += '		<p><span class="fa fa-warning fa-5x"></span></p>';
		
		message += '	</div>';
		message += '	<div class="col-sm-9">';
		message += '		<p><b> Are you sure you want to delete record of '+empno+'?</b></p>';
		message += '		<p>Login Details:</p>';
		message += '		<p>Date In: '+datein+'</p>';
		message += '		<p>Time In: '+timein+'</p>';
		message += '		<p>Date Out: '+dateout+'</p>';
		message += '		<p>Time Out: '+timeout+'</p>';
		message += '	</div>';
		message += '</div>';
	
	var modal_id = 'modal_delete_confirmation';
	$('#'+modal_id+' .modal-body').empty();
	$('#'+modal_id+' .modal-body').append(message);
	$('#'+modal_id).modal('show');
	
	$('#btn_delete').click(function(){
		delete_row(pkid);
	});
}

function delete_row(pkid){
	var data = {
		"action"	: "delete_raw_data",
		"pkid"		: pkid
	}
	call_ajax(data, handler, function(){
		oTable.row(selected_row).remove().draw( false );
		var modal_id = 'modal_delete_confirmation';
		$('#'+modal_id).modal('hide');
	});
}

$('#btn-StraightHours').click(function(){ 
	$('#modal_irregular_straight').modal('show'); 
});

// count_unfiled_straight_hours();

function count_unfiled_straight_hours(){
	$('#modal_irregular_straight .modal-body').empty();
	var data = {
		"action"	: "count_unfiled_straight_hours",
		"start_date": $('#txtStartDate').val(),
		"end_date" 	: $('#txtEndDate').val()
	}
	$('#modal_irregular_straight .modal-body').append('Loding none filled straight hours.');
	$('#btn-StraightHours').attr('class','btn btn-default fa-lg fa fa-spinner');
	$('#btn-StraightHours').prop('disabled',true);
	call_ajax(data, handler, function(result){
		$('#modal_irregular_straight .modal-body').empty();
		$('#btn-StraightHours').attr('class','btn btn-default fa-lg fa fa-edit');
		$('#btn-StraightHours').prop('disabled', false);
		if(typeof result['rec_list']['empno'] === 'undefined'){
			return false;
		}
		$('#btn-StraightHours').prepend(' ' + result['rec_list']['empno'].length);
		
		
		var html  = '<div id="container_table_irreg_straight">';
			html += '<table border=1 id="tbl_irreg_straight">';
			html += '	<thead>';
			html += '		<tr>';
			html += '			<th>Empno</th>';
			html += '			<th>Date-In</th>';
			html += '			<th>Time-In</th>';
			html += '			<th>Date-Out</th>';
			html += '			<th>Time-Out</th>';
			// html += '			<th>Date Time In</th>';
			// html += '			<th>Date Time Out</th>';
			html += '			<th>Time Diff</th>';
			html += '			<th>Option</th>';
			html += '		</tr>';
			html += '	</thead>';
			html += '	<tbody>';
		for(x=0;x<result['rec_list']['empno'].length;x++){
			html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
			html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
			html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
			html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
			html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
			html += '			<td>' + result['rec_list']['time_out'][x] + '</td>'; 
			html += '			<td>' + result['rec_list']['time_diff'][x] + '</td>'; 
			html += '			<td><center><button id="btn_no_in" class="btn btn-default fa" style="margin:5px;width:100px;">No Time-In </button> <button id="btn_no_out" class="btn btn-default fa" style="margin:5px;width:100px;">No Time-Out </button></center></td>'; 
			html += '		</tr>';
		}
		html += '	</tbody>';
		html += '</table>';
		html += '</div>';
		html += '<div id="container_time_in_out_information">';
		html += '</div>';
		$('#modal_irregular_straight .modal-body').append(html);
		oTable2 = $('#tbl_irreg_straight').DataTable();
		count_unfiled_straight_hours_events();
	});
}

function move_time_in(){
	// var pkid = $(this).closest('tr').attr('id');
	
	var pkid = selected_row.find('td:eq(1)').text();
	var date_in = selected_row.find('td:eq(2)').text();
	var data = {
		"action"	: "fix_attendance_time_in",
		"pkid"		: pkid
	}
	
	call_ajax(data, handler, function(result){
		$('#modal_move_in_out .modal-body').empty();
		var html = '<h4 id="h_move_time_in">Adjusted Time-In Information</h4>';
		$('#modal_move_in_out .modal-body').append(html);
		var html  = '<table id="tbl_time_in_out">';
			html += '	<thead>';
			html += '		<tr>';
			html += '			<th>Empno</th>';
			html += '			<th>Date-In</th>';
			html += '			<th>Time-In</th>';
			html += '			<th>Date-Out</th>';
			html += '			<th>Time-Out</th>';
			html += '			<th style="width:1%;">Adjust Row</th>';
			html += '		</tr>';
			html += '	</thead>';
			html += '	<tbody>';
			for(x=0;x<result['rec_list']['empno'].length;x++){
				html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
				html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
				html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
				html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
				html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
				html += '			<td>' + result['rec_list']['time_out'][x] + '</td>';
				html += '			<td><input type="checkbox" value="' + result['rec_list']['pkid'][x] + '" id="chk_adj_row" name="chk_adj_row" checked></td>';
				html += '		</tr>';
			}
			html += '	</tbody>';
			html += '</table>';
		
		$('#modal_move_in_out .modal-body').append(html);
		$('#modal_move_in_out').modal('show'); 
		
		oTable3 = $('#tbl_time_in_out').DataTable({
			"paginate"	: false
		});
	
	});
}

function move_time_out(){
	// var pkid = $(this).closest('tr').attr('id');
	
	var pkid = selected_row.find('td:eq(1)').text();
	var data = {
		"action"	: "fix_attendance_time_out",
		"pkid"		: pkid
	}
	
	call_ajax(data, handler, function(result){
		// alert(result['rec_list']['end_loop_date']);
		$('#modal_move_in_out .modal-body').empty();
		var html = '<h4 id="h_move_time_out">Adjusted Time-Out Information</h4>';
		$('#modal_move_in_out .modal-body').append(html);
		var html  = '<table id="tbl_time_in_out">';
			html += '	<thead>';
			html += '		<tr>';
			html += '			<th>Empno</th>';
			html += '			<th>Date-In</th>';
			html += '			<th>Time-In</th>';
			html += '			<th>Date-Out</th>';
			html += '			<th>Time-Out</th>';
			html += '			<th style="width:1%;">Adjust Row</th>';
			html += '		</tr>';
			html += '	</thead>';
			html += '	<tbody>';
			for(x=0;x<result['rec_list']['empno'].length;x++){
				html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
				html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
				html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
				html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
				html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
				html += '			<td>' + result['rec_list']['time_out'][x] + '</td>';
				html += '			<td><input type="checkbox" value="' + result['rec_list']['pkid'][x] + '" id="chk_adj_row" name="chk_adj_row" checked></td>';
				html += '		</tr>';
			}
			html += '	</tbody>';
			html += '</table>';
		
		$('#modal_move_in_out .modal-body').append(html);
		$('#modal_move_in_out').modal('show'); 
		
		oTable3 = $('#tbl_time_in_out').DataTable({
			"paginate"	: false
		});
	
	});
}

// function move_time_in_prev(){
	// var pkid = selected_row.find('td:eq(1)').text();
	// var data = {
		// "action"	: "fix_attendance_time_in_prev",
		// "pkid"		: pkid
	// }
	
	// call_ajax(data, handler, function(result){
		// $('#modal_move_in_out .modal-body').empty();
		// var html = '<h4 id="h_move_time_in">Adjusted Time-In Information</h4>';
		// $('#modal_move_in_out .modal-body').append(html);
		// var html  = '<table id="tbl_time_in_out">';
			// html += '	<thead>';
			// html += '		<tr>';
			// html += '			<th>Empno</th>';
			// html += '			<th>Date-In</th>';
			// html += '			<th>Time-In</th>';
			// html += '			<th>Date-Out</th>';
			// html += '			<th>Time-Out</th>';
			// html += '		</tr>';
			// html += '	</thead>';
			// html += '	<tbody>';
			// for(x=0;x<result['rec_list']['empno'].length;x++){
				// html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
				// html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
				// html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
				// html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
				// html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
				// html += '			<td>' + result['rec_list']['time_out'][x] + '</td>';
				// html += '		</tr>';
			// }
			// html += '	</tbody>';
			// html += '</table>';
		
		// $('#modal_move_in_out .modal-body').append(html);
		// $('#modal_move_in_out').modal('show'); 
		
		// oTable3 = $('#tbl_time_in_out').DataTable({
			// "paginate"	: false
		// });	
	// });
// }

// function move_time_out_prev(){
	/* var pkid = $(this).closest('tr').attr('id'); */
	
	// var pkid = selected_row.find('td:eq(1)').text();
	// var data = {
		// "action"	: "fix_attendance_time_out",
		// "pkid"		: pkid
	// }
	
	// call_ajax(data, handler, function(result){
		/* alert(result['rec_list']['end_loop_date']); */
		// $('#modal_move_in_out .modal-body').empty();
		// var html = '<h4 id="h_move_time_out">Adjusted Time-Out Information</h4>';
		// $('#modal_move_in_out .modal-body').append(html);
		// var html  = '<table id="tbl_time_in_out">';
			// html += '	<thead>';
			// html += '		<tr>';
			// html += '			<th>Empno</th>';
			// html += '			<th>Date-In</th>';
			// html += '			<th>Time-In</th>';
			// html += '			<th>Date-Out</th>';
			// html += '			<th>Time-Out</th>';
			// html += '		</tr>';
			// html += '	</thead>';
			// html += '	<tbody>';
			// for(x=0;x<result['rec_list']['empno'].length;x++){
				// html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
				// html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
				// html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
				// html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
				// html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
				// html += '			<td>' + result['rec_list']['time_out'][x] + '</td>'; 
				// html += '		</tr>';
			// }
			// html += '	</tbody>';
			// html += '</table>';
		
		// $('#modal_move_in_out .modal-body').append(html);
		// $('#modal_move_in_out').modal('show'); 
		
		// oTable3 = $('#tbl_time_in_out').DataTable({
			// "paginate"	: false
		// });
	
	// });
// }

$('#modal_move_in_out .fa-save').click(function(){
	$('#modal_move_in_out_confirmation').modal('show');
});

$('#modal_move_in_out_confirmation .fa-save').click(function(){
	var array_in_out 				= {};
		array_in_out.pkid 			= [];
		array_in_out.empno 			= [];
		array_in_out.date 			= [];
		array_in_out.time_in 		= [];
		array_in_out.date_out 		= [];
		array_in_out.time_out 		= [];
		array_in_out.adj_row 		= [];
		
	$('#tbl_time_in_out tbody tr').each(function(){
		array_in_out.pkid.push( this.id );
		array_in_out.empno.push( $(this).find('td:eq(0)').text() );
		array_in_out.date.push( $(this).find('td:eq(1)').text() );
		array_in_out.time_in.push( $(this).find('td:eq(2)').text() );
		array_in_out.date_out.push( $(this).find('td:eq(3)').text() );
		array_in_out.time_out.push( $(this).find('td:eq(4)').text() );
		var adj_row = false;
		if( $(this).find('td:eq(5) #chk_adj_row').prop('checked') == true ){
			adj_row = true;
		}
		array_in_out.adj_row.push( adj_row );
	});

	console.log(array_in_out.pkid);
	console.log(array_in_out.empno);
	console.log(array_in_out.date);
	console.log(array_in_out.time_in);
	console.log(array_in_out.date_out);
	console.log(array_in_out.time_out);
	console.log(array_in_out.adj_row);
	
	// return false;
	
	var data = {
		"action"		: "save_adjusted_time_in_out",
		"array_in_out"	: array_in_out
	}
	
	call_ajax(data, handler, function(result){
		// alert(result['msg']);
		oTable.ajax.reload();
		$('#modal_move_in_out_confirmation').modal('hide');
		$('#modal_move_in_out').modal('hide');
	});
});

function count_unfiled_straight_hours_events(){
	/* event */
	/* NO IN */
	$('#tbl_irreg_straight tbody').on('click', 'tr td #btn_no_in', function(){
		var pkid = $(this).closest('tr').attr('id');
		var data = {
			"action"	: "fix_attendance_time_in",
			"pkid"		: pkid
		}
		$('#container_table_irreg_straight').toggle();
		call_ajax(data, handler, function(result){
			$('#container_time_in_out_information').empty();
			// var html = '<h4>Adjusted Time-In Information</h4>';
			$('#container_time_in_out_information').append(html);
			var html  = '<div style="float:right;padding:10px;">';
				html += ' 	<button class="btn btn-success fa fa-save"> Save</button> ';
				html += ' 	<button class="btn btn-default fa fa-close"> Close</button>';
				html += '</div>';
			$('#container_time_in_out_information').append(html);
			var html  = '<table id="tbl_time_in_out">';
				html += '	<thead>';
				html += '		<tr>';
				html += '			<th>Empno</th>';
				html += '			<th>Date-In</th>';
				html += '			<th>Time-In</th>';
				html += '			<th>Date-Out</th>';
				html += '			<th>Time-Out</th>';
				html += '		</tr>';
				html += '	</thead>';
				html += '	<tbody>';
				for(x=0;x<result['rec_list']['empno'].length;x++){
					html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
					html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
					html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
					html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
					html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
					html += '			<td>' + result['rec_list']['time_out'][x] + '</td>'; 
					html += '		</tr>';
				}
				html += '	</tbody>';
				html += '</table>';
			$('#container_time_in_out_information').append(html);
			oTable3 = $('#tbl_time_in_out').DataTable();
			$('#container_time_in_out_information .btn-default').click(function(){
				$('#container_table_irreg_straight').toggle();
				$('#container_time_in_out_information').empty();
			});
		});
		
	});
	/* NO OUT */
	$('#tbl_irreg_straight tbody').on('click', 'tr td #btn_no_out', function(){
		var pkid = $(this).closest('tr').attr('id');
		var data = {
			"action"	: "fix_attendance_time_out", //dito ka na
			"pkid"		: pkid
		}
		$('#container_table_irreg_straight').toggle();
		call_ajax(data, handler, function(result){
			$('#container_time_in_out_information').empty();
			// var html = '<h4>Adjusted Time-In Information</h4>';
			$('#container_time_in_out_information').append(html);
			var html  = '<div style="float:right;padding:10px;">';
				html += ' 	<button class="btn btn-success fa fa-save"> Save</button> ';
				html += ' 	<button class="btn btn-default fa fa-close"> Close</button>';
				html += '</div>';
			$('#container_time_in_out_information').append(html);
			var html  = '<table id="tbl_time_in_out">';
				html += '	<thead>';
				html += '		<tr>';
				html += '			<th>Empno</th>';
				html += '			<th>Date-In</th>';
				html += '			<th>Time-In</th>';
				html += '			<th>Date-Out</th>';
				html += '			<th>Time-Out</th>';
				html += '		</tr>';
				html += '	</thead>';
				html += '	<tbody>';
				for(x=0;x<result['rec_list']['empno'].length;x++){
					html += '		<tr id="' + result['rec_list']['pkid'][x] + '">'; 
					html += '			<td>' + result['rec_list']['empno'][x] + '</td>'; 
					html += '			<td>' + result['rec_list']['date'][x] + '</td>'; 
					html += '			<td>' + result['rec_list']['time_in'][x] + '</td>';
					html += '			<td>' + result['rec_list']['date_out'][x] + '</td>'; 
					html += '			<td>' + result['rec_list']['time_out'][x] + '</td>'; 
					html += '		</tr>';
				}
				html += '	</tbody>';
				html += '</table>';
			$('#container_time_in_out_information').append(html);
			oTable3 = $('#tbl_time_in_out').DataTable();
			$('#container_time_in_out_information .btn-default').click(function(){
				$('#container_table_irreg_straight').toggle();
				$('#container_time_in_out_information').empty();
			});
		});
	});
}
</script>
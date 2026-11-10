<!--SCRIPT and CSS-->
<?php
	require_once("includes.php"); 
	require_once("otksr-oop.php");
	session_start();
	$username = $_SESSION['username'];
	// $username = "marlope"; 
	$dateToday = date('Y-m-d');
	echo '<input type="hidden" id="hd_uname" value="'.$username.'">';
?>
<html>
<head>
	<title>OTKS REPORT</title>
</head>
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
				<li class="active"><a href="index.php" id="loadRawData">OTKS Raw Data</a></li>
				<li class=""><a href="dailyTFM.php" id="loadDailyTFM">Daily TFM</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<li class=""><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
				<li class=""><a href="pendingAttendance.php" id="loadPendingAttendance">Pending Attendance</a></li>
			  </ul>
			 </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	<div class="row panel panel-default panel_top">
		<?php
			if(isset($_GET)){
				$date_start = $_GET['txtStartDate'];
				$date_end = $_GET['txtEndDate'];
			}
			
			if($date_start=="" || $date_start==""){
				$date_start = $dateToday;
				$date_end = $dateToday;
			}
		?>
		<div class="col-sm-12">	
			<form class="form-inline" role="form" method="GET" action="index.php" id="frm-date_range">
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
				  <button type="submit" class="btn btn-default">SET DATE</button>
				</form>
			
			<button id="btn-DeleteRow" class="btn btn-default">DELETE SELECTED</button>
			<button id="btn-GenerateIR" class="btn btn-default">INCONSISTENCY REPORT</button><br><br>
			</div>
		</div>
		
		<div class="row panel panel-default panel_body">
			<div class="col-sm-12">	
			<table class="display" id="tblMain" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th style="">PKID</th>
						<th>Date IN</th>
						<th>Time In</th>
						<th style="">Mode IN</th>
						<th>Date OUT</th>
						<th>Time Out</th>
						<th style="">Mode OUT</th>
						<th>EN</th>
						<th>Name</th>
						<th>Department</th>
						<th>Position</th>
						<th>Section</th>
						<th>Inconsistency</th>

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
						<label style="margin-right:20px;width:160px">Mode In: </label><select class="form-control" id="txtModeIn" style = "width:25%;">
						<option value="IN">IN</option>
						<option value="BUS LATE">BUS LATE</option>
						</select><br>
						<hr>
						<h4>Time Out Information</h4>
						<label style="margin-right:20px;width:160px">Date Out: </label><input class="form-control" type="date" id="txtDateOut" placeholder="In" style = "width:25%;">
						<label style="margin-right:20px;width:160px">Time OUT: </label><input class="form-control" type="time" id="txtTimeOut" placeholder="IN Remarks" step="1" style = "width:25%;">
						<label style="margin-right:20px;width:160px">Mode Out: </label><select class="form-control" id="txtModeOut" style = "width:25%;">
						<option value="OUT">OUT</option>
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
				<label style="margin-right:20px;width:160px">EN: </label><input style="width:168px;" type="text" id="txtEmpNoA" name="txtEmpNoA" placeholder="Employee Number" oninput="loadEmpInfo(this.value)">
				<label style="margin-right:20px;width:160px">ECode: </label><input style="width:168px;" type="text" id="txtECodeA" name="txtECodeA" placeholder="ECode" readonly>
				<label style="margin-right:20px;width:160px">Name: </label><input style="width:168px;" type="text" id="txtNameA" name="txtNameA" placeholder="Name" readonly>
				<label style="margin-right:20px;width:160px">Department: </label><input style="width:168px;" type="text" id="txtDepartmentA" name="txtDepartmentA" placeholder="Department" readonly>
				<label style="margin-right:20px;width:160px">Position: </label><input style="width:168px;" type="text" id="txtPositionA" name="txtPositionA" placeholder="Postion" readonly>
				<label style="margin-right:20px;width:160px">Section: </label><input style="width:168px;" type="text" id="txtSectionA" name="txtSectionA" placeholder="Section" readonly>
				<label style="margin-right:20px;width:160px">Date In: </label><input style="width:168px;" type="date" id="txtDateInA" name="txtDateInA" placeholder="">
				<label style="margin-right:20px;width:160px">Time IN: </label><input style="width:168px;" type="time" id="txtTimeInA" name="txtTimeInA" placeholder="">
				<label style="margin-right:20px;width:160px">Mode In: </label><select style="width:168px;" id="txtModeInA">
					<option value="IN">IN</option>
					<option value="BUS LATE">BUS LATE</option>
					</select><br>
				<label style="margin-right:20px;width:160px">Date Out: </label><input style="width:168px;" type="date" id="txtDateOutA" name="txtDateOutA" placeholder="">
				<label style="margin-right:20px;width:160px">Time OUT: </label><input style="width:168px;" type="time" id="txtTimeOutA" name="txtTimeOutA" placeholder="" >
				<label style="margin-right:20px;width:160px">Mode Out: </label><select style="width:168px;" id="txtModeOutA">
					<option value="OUT">OUT</option>
					</select><br>
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
</html>

<script type="text/javascript" src="./js/common_functions.js"></script>

<script type="text/javascript">
var handler = 'reportHandler.php';
var username = $('#hd_uname').val();
var oTable = '';
var selected_row = '';

var start_date = $('#txtStartDate').val();
var end_date = $('#txtEndDate').val();
var table_id = 'tblMain';

$(document).ready(function() {
	oTable = $('#'+table_id).DataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "./server_side_scripts/server_processing_raw_data.php?date_start="+start_date+"&date_end="+end_date
	} );
} );

var array_update = [];

$('#' + table_id + ' tbody').on('dblclick','tr',function(){
	array_update = [];
	selected_row = $(this);
	
	array_update = {
		"pkid"	 	 : selected_row.find('td:eq(0)').text(),
		"date"	 	 : selected_row.find('td:eq(1)').text(),
		"time_in"	 : selected_row.find('td:eq(2)').text(),
		"mode_in"	 : selected_row.find('td:eq(3)').text(),
		"date_out"	 : selected_row.find('td:eq(4)').text(),
		"time_out"	 : selected_row.find('td:eq(5)').text(),
		"mode_out"	 : selected_row.find('td:eq(6)').text(),
		"empno"	 	 : selected_row.find('td:eq(7)').text(),
		"name"	 	 : selected_row.find('td:eq(8)').text(),
		"Position"	 : selected_row.find('td:eq(9)').text(),
		"Department" : selected_row.find('td:eq(10)').text(),
		"Section"	 : selected_row.find('td:eq(11)').text()
	}
	
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


$('#btn-Update').click(function(){
	update_row();
});

$('#btn-DeleteRow').click(function(){
	delete_row_confirmation();
});

$('#btn-GenerateIR').click(function(){
	window.open("inconsistencyrecord.php?date="+start_date+"&date_out="+end_date);
});

function update_row(){
	var array_new_values = {
		"pkid"	 	 : selected_row.find('td:eq(0)').text(),
		"date"	 	 : $('#txtDateIn').val(),
		"time_in"	 : $('#txtTimeIn').val(),
		"mode_in"	 : $('#txtModeIn').val(),
		"date_out"	 : $('#txtDateOut').val(),
		"time_out"	 : $('#txtTimeOut').val(),
		"mode_out"	 : $('#txtModeOut').val(),
		"empno"	 	 : selected_row.find('td:eq(7)').text(),
		"name"	 	 : selected_row.find('td:eq(8)').text(),
		"Position"	 : selected_row.find('td:eq(9)').text(),
		"Department" : selected_row.find('td:eq(10)').text(),
		"Section"	 : selected_row.find('td:eq(11)').text()
	}
	
	var data = {
		"action"       : "updateRawData",
		"array_update" : array_new_values,
		"username"     : username
	}
	
	call_ajax(data, handler, function(result){
		alert(result['msg']);
		oTable.cell( selected_row.find('td:eq(1)') ).data(array_new_values['date']);
		oTable.cell( selected_row.find('td:eq(2)') ).data(array_new_values['time_in']);
		oTable.cell( selected_row.find('td:eq(3)') ).data(array_new_values['mode_in']);
		oTable.cell( selected_row.find('td:eq(4)') ).data(array_new_values['date_out']);
		oTable.cell( selected_row.find('td:eq(5)') ).data(array_new_values['time_out']);
		oTable.cell( selected_row.find('td:eq(6)') ).data(array_new_values['mode_out']);
		if(array_new_values['time_in'] != "" && array_new_values['time_out'] != ""){
			oTable.cell( selected_row.find('td:eq(12)') ).data('');
		}
		$('#modalUpdate').modal('hide');
	});
}

function delete_row_confirmation(){
	var pkid = selected_row.find('td:eq(0)').text();
	var empno = selected_row.find('td:eq(7)').text();
	var datein = selected_row.find('td:eq(1)').text();
	var timein = selected_row.find('td:eq(2)').text();
	var dateout = selected_row.find('td:eq(4)').text();
	var timeout = selected_row.find('td:eq(5)').text();
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
		alert('data has been deleted');
		oTable.row(selected_row).remove().draw( false );
	});
}

</script>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<title>OTKS REPORT</title>
<head>
<?php
require_once("includes.php");
session_start();
if (array_key_exists("username", $_SESSION)) //username validation
{
	$username = $_SESSION['username'];
}
echo '<input type="hidden" id="hd_username" value="'.$username.'">';
require_once('check_login.php');
?>
	
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
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard" class="fa fa-user fa-lg"> Timecard</a></li>
				<li class="active"><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>	
	
	<div class="row panel panel-primary top-container">
		<div class="panel-heading" style="margin-bottom:10px;"><b>SETTINGS</b></div>
		<div class="panel-body" style="height:80%;padding:30px;padding-top:10px;">
			<div class="row">
				<div class="col-sm-6" style="padding:5px;">
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default" style="padding:20px;">
								<span class="fa fa-calendar-minus-o fa-lg"> OT Checker</span>
								<hr>
								<table id="tbl_ot_checker" class="table table-bordered">
									<thead>
										<tr>	
											<th>Empno</th>
											<th>OT Date</th>
											<th>OT Filed</th>			
											<th>Valid OT</th>
											<th>Requestor</th>
											<th>Approver</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6" style="padding:5px;">
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default" style="padding:20px;">
								<span class="fa fa-users fa-lg"> Dayoff</span>
								<hr>
								<p style="text-align:right;">
								<button class="btn btn-success fa fa-plus" style="margin-bottom:10px;" id="btn_add_dayoff"> Add Dayoff</button>
								</p>
								<table id="tbl_dayoff" class="table table-bordered">
									<thead>
										<tr>
											<th>EmpNo</th>
											<th>Saturday Dayoff</th>
											<th style="width:20%;"></th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" id="modal_add_dayoff">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"><span class="fa fa-plus"></span> Add Dayoff</h4>
		  </div>
		  <div class="modal-body">
			<form id="frm-add-dayoff">
				<div class="row" style="padding-bottom:10px;">
					<div class="col-sm-2">
						<label class="control-label fa-1x" style="padding-top:8px;">Emp #: </label>
					</div>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="txt_empno" name="txt_empno" autocomplete="off" required>
					</div>
					<div class="col-sm-2">
						<label class="control-label fa-1x" style="padding-top:8px;">Type : </label>
					</div>
					<div class="col-sm-4">
						<select class="form-control" id="txt_type" name="txt_type">
							<option value="0">Without Day-off</option>
							<option value="1" selected>With Day-off</option>
						</select>
					</div>
					<div class="col-sm-2">
						<label class="control-label fa-1x" style="padding-top:8px;">Saturday Day-off : </label>
					</div>
					<div class="col-sm-4">
						<select class="form-control" id="txt_saturday" name="txt_saturday">
							<option value="0">1st & 3rd Sat</option>
							<option value="1" selected>2nd & 4th Sat</option>
						</select>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-success"><span class="fa fa-save"></span> Save</button>
			</form>
		 </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_dayoff">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"><span class="fa fa-plus"></span> Add Dayoff</h4>
		  </div>
		  <div class="modal-body">
			<form id="frm-delete-dayoff">
			<div class="alert alert-danger">
				
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-danger"><span class="fa fa-trash"></span> Delete</button>
			</form>
		 </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
</body>
</html>

<script>
var username = $('hd_username').val();
var oTable_dayoff = '';
var oTable_offset = '';
var oTable_holiday = '';
$(document).ready(function(){
	
	oTable_dayoff = $('#tbl_dayoff').DataTable({
		"scrollY"	 : 300,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "./server_side_scripts/dayoff.php"
	});
	
	var oTable_ot_checker = $('#tbl_ot_checker').DataTable({
		"scrollY"	 : 337,
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting"  : [],
		"sAjaxSource": "./server_side_scripts/ot_checker.php"
	});
	
	$('#btn_add_dayoff').click(function(){ $('#modal_add_dayoff').modal('show'); });
	
	$('#frm-add-dayoff').submit(function(e){
		e.preventDefault();
		save_dayoff( $(this).serialize() );
	});
	
	$('#frm-delete-dayoff').submit(function(e){
				
	});
	
	$('#tbl_dayoff tbody').on('click', 'tr .btn-danger', function(){
		$('#frm-delete-dayoff').data( 'id',$(this).data('id') );
		var selected_tr = $(this).closest('tr');
		var empno = selected_tr.find('td:eq(0)').text();
		var dayoff_type = selected_tr.find('td:eq(1)').text();
		delete_dayoff(empno);
	});
	
});

function save_dayoff( formdata ){
	$.ajax({
		type	: "post",
		dataType: "json",
		url		: "reportHandler.php",
		data	: "action=add_employee_dayoff" + '&' + 'username=' + username + '&' + formdata,
		success	: function(result){
			alert(result['msg']);
			oTable_dayoff.draw(false);
		},error	: function(){
			alert('error');
		}
	});
}

function delete_dayoff(empno){
	$.ajax({
		type	: "post",
		dataType: "json",
		url		: "reportHandler.php",
		data	: "action=delete_employee_dayoff" + '&' + 'empno=' + empno,
		success	: function(result){
			oTable_dayoff.draw(false);
			$('.modal').modal('hide');
			alert(result['msg']);
		},error	: function(){
			alert('error');
		}
	});
}
</script>
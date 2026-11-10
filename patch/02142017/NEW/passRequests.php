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
				<li class="" id="li_pass_requests"><a href="passRequests.php" id="" class="fa fa-car fa-lg"> Pass Requests</a></li>
				<li class=""><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

		<div class="row panel panel-primary top-container">
			<div class="panel-heading" style="margin-bottom:10px;"><b>APPROVED PASS REQUESTS</b></div>
				<div class="panel-body">
			 	 	<form id="frm_search_record">
				 	 	<div class="row">
				 	 		<div class="col-sm-2" style="padding:2px;padding-left:20px;">
				 	 			<label class="fa-sm"> Month:</label>
				 	 		</div>
				 	 		<div class="col-sm-2" style="padding:2px;">
				 	 			<label class="fa-sm"> Request Type:</label>
				 	 		</div>
				 	 		<div class="col-sm-4" style="padding:2px;">
				 	 			
				 	 		</div>
				 	 	</div>
				 	 	<div class="row">
				 	 		<div class="col-sm-2" style="padding:2px;padding-left:20px;">
				 	 			<input type="month" class="form-control" id="txt_month" value="<?php echo date('Y-m'); ?>">
				 	 		</div>
				 	 		<div class="col-sm-2" style="padding:2px;">
				 	 			<select class="form-control" id="cmb_request_type">
									<option value=""> All</option>
									<option value="0"> Undertime</option>
									<option value="1"> OB</option>
								</select>
				 	 		</div>
				 	 		<div class="col-sm-4" style="padding:2px;">
				 	 			<button type="button" class="btn btn-primary" id="btn_set_first_cutoff"><span class="fa fa-calendar"></span> 1ST CUTOFF</button>
				 	 			<button type="button" class="btn btn-primary" id="btn_set_second_cutoff"><span class="fa fa-calendar"></span> 2ND CUTOFF</button>
				 	 		</div>
				 	 	</div>
				 	 </form>
				 </div>
			</div>
		</div>

		<div class="row panel panel-default mid-container">
			<div class="col-sm-12" style="overflow-y:hidden;">
				<p class="align-r">
				</p>
				<table border="1" class="table table-striped" id="tblMain">
					<thead>
						<tr>
							<th style="width:10%;">Request Type</th> <!-- Request type 0-OB, 1-undertime -->
							<th>Date Filed</th>
							<th>EN</th>
							<th>Name</th>
							<th>Time In</th>
							<th>Time Out</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<!--</div>-->
</body>
</html>

<script>
</script>

<script type="text/javascript" src="js/common_functions.js"></script>
<script>
var username = " <?php echo $username; ?> ";
var selected_row = '';
var handler = 'reportHandler.php';
var date_start = $('#txtStartDate').val();
var date_end = $('#txtEndDate').val();

$('.navbar .nav li').attr('class','');
$('.navbar .nav li#li_pass_requests').attr('class','active');

var tblMain_dt = $('#tblMain').DataTable({ ajax : "server_side_scripts/pass_request.php" });

$('#frm_search_record #btn_set_first_cutoff').click(function(){
	var selected_month = $('#frm_search_record #txt_month').val();
	get_first_cut_off(selected_month);
});

$('#frm_search_record #btn_set_second_cutoff').click(function(){
	var selected_month = $('#frm_search_record #txt_month').val();
	get_second_cut_off(selected_month);
});

function get_first_cut_off(selected_month){
	var data = {
		"action"			: "get_first_cut_off",
		"selected_month"	: selected_month
	}
	call_ajax(data, handler, function(result){
		tblMain_dt.ajax.url("server_side_scripts/pass_request.php?start_date="+result['start_date']+"&end_date="+result['end_date']+'&request_type='+$('#frm_search_record #cmb_request_type').val()).load();
	});
}

function get_second_cut_off(selected_month){
	var data = {
		"action"			: "get_second_cut_off",
		"selected_month"	: selected_month
	}
	
	call_ajax(data, handler, function(result){
		tblMain_dt.ajax.url("server_side_scripts/pass_request.php?start_date="+result['start_date']+"&end_date="+result['end_date']+'&request_type='+$('#frm_search_record #cmb_request_type').val()).load();
	});
}

</script>

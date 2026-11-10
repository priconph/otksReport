<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<head>
<title>OTKS REPORT</title>

<!--SCRIPT and CSS-->
<?php
require_once("includes.php");
require_once('otksr-oop.php');
session_start();
require_once('check_login.php');
?>
	
<style> 
<!--OVERFLOW ON PANEL-->
.table {
.table {
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
</style>

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
				<li class="active"><a href="eom.php" id="loadEom" class="fa fa-tasks fa-lg"> EOM</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard" class="fa fa-user fa-lg"> Timecard</a></li>
				<li class=""><a href="passRequests.php" id="" class="fa fa-car fa-lg"> Pass Requests</a></li>
				<li class=""><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>

			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>	
	<div class="row panel panel-primary top-container" id="mainContainer">
		<div class="panel-heading" style="margin-bottom:10px;"><b>EOM</b></div>
		<?php
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
		<div class="col-sm-8">	
			<form class="form-inline" id="frm_date_range">
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
		</div>
		<!--<button id="btn_exporteomtoexcel2" class="btn btn-success">EXPORT TO EXCEL2</button>-->
	</div>
	<div class="row panel panel-default mid-container" id="mainContainer">
		<div class="col-sm-12" style="overflow-y:hidden;">
		<p class="align-r">
			<button type="button" id="btn_exporteomtoexcel" class="btn btn-success fa fa-file-excel-o"> EXCEL EXPORT</button>
		</p>
		<?php 
		
		if (isset($_GET['txtStartDate'])){
			$dateFrom = $_GET['txtStartDate'];
			$dateTo = $_GET['txtEndDate'];
			echo '<table border="1" class="table" id="tblMain">
					<thead>
						<tr>
							<th>IDNUM</th>
							<th>EMP_CODE</th>
							<th>ABSENCES</th>
							<th>UND_TIME</th>
							<th>NIG_DIFF</th>
							<th>SL</th>
							<th>VL</th>
							<th>SD</th>
							<th>OT_1</th>
							<th>OT_2</th>
							<th>OT_2A</th>
							<th>OT_3</th>
							<th>OT_3A</th>
							<th>OT_4</th>
							<th>OT_4A</th>
							<th>OT_5</th>
							<th>OT_5A</th>
							<th>OT_6</th>
							<th>OT_6A</th>
							<th>ADJ1</th>
							<th>ADJ2</th>
						</tr>
					</thead>
				';
			?>
			<?php				
				echo '<tbody>';
				$selectEOMEmpNo = OTKSR::getInstance()->getEOMEmpNo($dateFrom, $dateTo);							
				while($rowEN = mysqli_fetch_array($selectEOMEmpNo)) {
					$absent 	= 0;
					$undertime 	= 0;
					$nightDiff 	= 0;
					$absent 	= 0;
					$ot1    	= 0;
					$ot2		= 0;
					$ot2a 		= 0;
					$ot3 		= 0;
					$ot3a 		= 0;
					$ot4 		= 0;
					$ot4a 		= 0;
					$ot5 		= 0;
					$ot5a 		= 0;
					$ot6 		= 0;
					$ot6a 		= 0;
					$sl 		= 0;
					$vl 		= 0;

					/* late file ot */
					$late_file_ot_all = OTKSR::getInstance() -> getEom_late_file_ot($rowEN['empno'], $dateFrom, $dateTo);
					if($late_file_ot_all != 0 || $late_file_ot_all != "0"){
						for( $x=0; $x<count($late_file_ot_all['empno']); $x++ ){
							if( $late_file_ot_all['ot_type'][$x] == 1){
								$ot1 = $ot1 + $late_file_ot_all['ot_count'][$x];
							}
							if($late_file_ot_all['ot_type'][$x] == 2){
								$ot2 = $ot2 + $late_file_ot_all['ot_count'][$x];
								if($late_file_ot_all['ot_count'][$x] > 8){
									$excess = $late_file_ot_all['ot_count'][$x] - 8;
									$ot2 = $ot2 - $excess;
									$ot2a += $excess;
								}
							}
							if($late_file_ot_all['ot_type'][$x] == 3){
								$ot3 = $ot3 + $late_file_ot_all['ot_count'][$x];
								if($late_file_ot_all['ot_count'][$x] > 8){
									$excess = $late_file_ot_all['ot_count'][$x] - 8;
									$ot3 = $ot3 - $excess;
									$ot3a += $excess;
								}
							}
							if($late_file_ot_all['ot_type'][$x] == 4){
								$ot4 = $ot4 + $late_file_ot_all['ot_count'][$x];
								if($late_file_ot_all['ot_count'][$x] > 8){
									$excess = $late_file_ot_all['ot_count'][$x] - 8;
									$ot4 = $ot4 - $excess;
									$ot4a += $excess;
								}
							}
							if($late_file_ot_all['ot_type'][$x] == 5){
								$ot5 = $ot5 + $late_file_ot_all['ot_count'][$x];
								if($late_file_ot_all['ot_count'][$x] > 8){
									$excess = $late_file_ot_all['ot_count'][$x] - 8;
									$ot5 = $ot5 - $excess;
									$ot5a += $excess;
								}
							}
							if($late_file_ot_all['ot_type'][$x] == 6){
								$ot6 = $ot6 + $late_file_ot_all['ot_count'][$x];
								if($late_file_ot_all['ot_count'][$x] > 8){
									$excess = $late_file_ot_all['ot_count'][$x] - 8;
									$ot6 = $ot6 - $excess;
									$ot6a += $excess;
								}
							}
						}
					}
					

					// /* late file leave */
					$late_file_eom_all = OTKSR::getInstance() -> getEom_late_file($rowEN['empno'], $dateFrom, $dateTo);
					if($late_file_eom_all != 0 || $late_file_eom_all != "0"){
						while ( $late_file_row_all = mysqli_fetch_array($late_file_eom_all) ){
							if ($late_file_row_all['leavenum'] != 0){
								if($late_file_row_all['leavecode'] == 1){
									$sl += $late_file_row_all['leavenum'];
								}
							}
							if ($late_file_row_all['leavenum'] != 0){
								if($late_file_row_all['leavecode'] == 2 || $late_file_row_all['leavecode'] == 3 || $late_file_row_all['leavecode'] == 4 || $late_file_row_all['leavecode'] == 5 || $late_file_row_all['leavecode'] == 6 ){
									$vl += $late_file_row_all['leavenum'];
								}
							}
						}
					}
					
					/* normal_records */
					echo "<tr>";
					$eom_all = OTKSR::getInstance() -> total_all_eom($rowEN['empno'], $dateFrom, $dateTo);
					while ( $row_all = mysqli_fetch_array($eom_all) ){
						$absent = $row_all['absent'];
						$undertime = $row_all['undertime'];
						$nightDiff = $row_all['nightdiff'];
						$absent = $row_all['absent'];
						$ot1 += $row_all['ot1'];
						$ot2 += $row_all['ot2'];
						$ot2a += $row_all['ot2a'];
						$ot3 += $row_all['ot3'];
						$ot3a += $row_all['ot3a'];
						$ot4 += $row_all['ot4'];
						$ot4a += $row_all['ot4a'];
						$ot5 += $row_all['ot5'];
						$ot5a += $row_all['ot5a'];
						$ot6 += $row_all['ot6'];
						$ot6a += $row_all['ot6a'];
					}
					
					$selectEOMSL = OTKSR::getInstance() -> getEOMSL($rowEN['empno'], $dateFrom, $dateTo);
					while($rowSL = mysqli_fetch_array($selectEOMSL)) {
						if ($rowSL['leavenum'] != 0){
							if($rowSL['leavecode'] == 1){
								$sl += $rowSL['leavenum'];
							}
						}
					}
					
					$selectEOMVL = OTKSR::getInstance() -> getEOMVL($rowEN['empno'], $dateFrom, $dateTo);

					while($rowVL = mysqli_fetch_array($selectEOMVL)) {
						if ($rowVL['leavenum'] != 0){
							if($rowVL['leavecode'] == 2 or $rowVL['leavecode'] == 3 or $rowVL['leavecode'] == 4 or $rowVL['leavecode'] == 5 or $rowVL['leavecode'] == 6 ){
								$vl += $rowVL['leavenum'];
							}
						}
					}
					
					
					//echo "<td style='display:none;'>".$rowEN['pkid']."</td>";
					echo "<td>0".$rowEN['empno']."</td>";
					echo "<td>".$rowEN['SalaryType']."</td>";
					echo "<td>".$absent."</td>";
					echo "<td>".$undertime."</td>";
					echo "<td>".$nightDiff."</td>";
					echo "<td>".$sl."</td>";
					echo "<td>".$vl."</td>";
					echo "<td>0</td>";
					echo "<td>".$ot1."</td>";
					echo "<td>".$ot2."</td>";
					echo "<td>".$ot2a."</td>";
					echo "<td>".$ot3."</td>";
					echo "<td>".$ot3a."</td>";
					echo "<td>".$ot4."</td>";
					echo "<td>".$ot4a."</td>";
					echo "<td>".$ot5."</td>";
					echo "<td>".$ot5a."</td>";
					echo "<td>".$ot6."</td>";
					echo "<td>".$ot6a."</td>";
					echo "<td>0</td>";
					echo "<td>0</td>";
					echo "</tr>";
					$absent = 0;
					$undertime = 0;
					$nightDiff = 0;
				}
				
					
			}	
					?>
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
						<label style="margin-right:20px;width:160px">IDNUM: </label><input type="text" id="idnum" placeholder="ID" readonly>
						<label style="margin-right:20px;width:160px">EMP_CODE: </label><input type="text" id="empcode" placeholder="ECode" readonly>
						<label style="margin-right:20px;width:160px">ABSENCES: </label><input type="text" id="absences" placeholder="ABSENCES">
						<label style="margin-right:20px;width:160px">UND_TIME: </label><input type="text" id="und_time" placeholder="UND_TIME">
						<label style="margin-right:20px;width:160px">NIG_DIFF: </label><input type="text" id="nig_diff" placeholder="NIG_DIFF">
						<label style="margin-right:20px;width:160px">SL: </label><input type="text" id="sl" placeholder="SL OUT">
						<label style="margin-right:20px;width:160px">VL: </label><input type="text" id="vl" placeholder="VL">
						<label style="margin-right:20px;width:160px">SD: </label><input type="text" id="sd" placeholder="SD">
						<label style="margin-right:20px;width:160px">OT_1: </label><input type="text" id="ot_1" placeholder="OT_1"><br>
						<label style="margin-right:20px;width:160px">OT_2: </label><input type="text" id="ot_2" placeholder="OT_2">
						<label style="margin-right:20px;width:160px">OT_2A: </label><input type="text" id="ot_2a" placeholder="OT_2A">
						<label style="margin-right:20px;width:160px">OT_3: </label><input type="text" id="ot_3" placeholder="OT_3">
						<label style="margin-right:20px;width:160px">OT_3A: </label><input type="text" id="ot_3a" placeholder="OT_3A">
						<label style="margin-right:20px;width:160px">OT_4: </label><input type="text" id="ot_4" placeholder="OT_4">
						<label style="margin-right:20px;width:160px">OT_4A: </label><input type="text" id="ot_4a" placeholder="OT_4A">
						<label style="margin-right:20px;width:160px">OT_5: </label><input type="text" id="ot_5" placeholder="OT_5">
						<label style="margin-right:20px;width:160px">OT_5A: </label><input type="text" id="ot_5a" placeholder="OT_5A">
						<label style="margin-right:20px;width:160px">OT_6: </label><input type="text" id="ot_6" placeholder="OT_6">
						<label style="margin-right:20px;width:160px">OT_6a: </label><input type="text" id="ot_6a" placeholder="OT_6a">
						<label style="margin-right:20px;width:160px">ADJ1: </label><input type="text" id="adj1" placeholder="ADJ1">
						<label style="margin-right:20px;width:160px">ADJ2: </label><input type="text" id="adj2" placeholder="ADJ2">
					</div>
				</form>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn-update">Save changes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<!-- Modal Loading EOM -->
	<div class="modal fade" tabindex="-1" role="dialog" id="modal_loading">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">SYSTEM MESSAGE</h4>
		  </div>
		  <div class="modal-body">
			<p><h4>Generating EOM.. Please be patient..</h4></p>
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
var id,idnum,empcode,absences,und_time,nig_diff,sl,vl,sd,ot_1,ot_2,ot_2a,
	ot_3,ot_3a,ot_4,ot_4a,ot_5,ot_5a,ot_6,ot_6a,adj1,adj2;

//function for edit
$(document).ready(function(){
	var x;
	var oTable = $('#tblMain').DataTable({
		"scrollX"	 : true,
		"scrollY"	 : 240
	});
		
	$('#btn_exporteomtoexcel').click(function(){
		window.location.href = "eom-ExportToExcel.php?startDate="+ $('#txtStartDate').val() + "&endDate="+ $('#txtEndDate').val();
	});
	
	$('#btn_exporteomtoexcel2').click(function(){
		// alert("eom-ExportToExcel2.php?startDate="+ $('#txtStartDate').val() + "& endDate="+ $('#txtEndDate').val());
		// window.location.href = "eom-ExportToExcel2.php?startDate="+ $('#txtStartDate').val() + "& endDate="+ $('#txtEndDate').val();
	});
	
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
	
	$(function() {
	$(document).ajaxStart(function() {
			$('#mainContainer').hide();
			$('#loader').show();
	});
	$(document).ajaxStop(function() {
			$('#mainContainer').show();
			$('#loader').hide();
		});
	});
	
});

$('document').ready(function(){
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
			$('#modal_loading').modal('show');
			window.location.href="eom.php?txtStartDate="+start_date+"&txtEndDate="+end_date;
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
					$('#modal_loading').modal('show');
					start_date = $('#txtStartDate').val(result['start_date']);
					start_date = $('#txtEndDate').val(result['end_date']);
					window.location.href="eom.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
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
					$('#modal_loading').modal('show');
					start_date = $('#txtStartDate').val(result['start_date']);
					start_date = $('#txtEndDate').val(result['end_date']);
					window.location.href="eom.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
				}
			});
		}
	});
});

</script>
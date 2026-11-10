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
				<li class=""><a href="index.php" id="loadRawData">OTKS Raw Data</a></li>
				<li class=""><a href="dailyTFM.php" id="loadDailyTFM">Daily TFM</a></li>
				<!--<li class=""><a href="manualUpdating.php" id="loadManualUpdating">Manual Updating</a></li>-->
				<!--<li class=""><a href="uploadOT.php" id="loadUploadOT">Upload OT</a></li>-->
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<!--<li class=""><a href="lateUpload.php" id="loadLateUpload">Late Upload for OT and Leave</a></li>-->
				<li class="active"><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
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
			<button id="btn_exporteomtoexcel" class="btn btn-success fa fa-file-excel-o"> EXCEL EXPORT</button>
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
									//echo $rowEN['empno']."<br>";
									//$selectEOMData = OTKSR::getInstance() -> getEOMAbsent($rowEN['empno'], '2014-06-09', '2014-06-17');
									echo "<tr>";
									$selectEOMAbsent = OTKSR::getInstance() -> getEOMAbsent($rowEN['empno'], $dateFrom, $dateTo);
									$absent = 0;
									while($rowAbsent = mysqli_fetch_array($selectEOMAbsent)) {
										$absent += $rowAbsent['absent'];
									}
									$selectEOMUndertimeLate = OTKSR::getInstance() -> getEOMUnderTimeLate($rowEN['empno'], $dateFrom, $dateTo);
									$undertime = 0;
									while($rowUndertimeLate = mysqli_fetch_array($selectEOMUndertimeLate)) {
										$undertime += $rowUndertimeLate['under_mins'] + $rowUndertimeLate['late_mins'];
									}
									$selectEOMNightDiff = OTKSR::getInstance() -> getEOMNightDiff($rowEN['empno'], $dateFrom, $dateTo);
									$nightDiff = 0;
									while($rowNightDiff = mysqli_fetch_array($selectEOMNightDiff)) {
										$nightDiff += $rowNightDiff['nightdiff'];
									}
									
									$selectEOMSL = OTKSR::getInstance() -> getEOMSL($rowEN['empno'], $dateFrom, $dateTo);
									$sl = 0;
									while($rowSL = mysqli_fetch_array($selectEOMSL)) {
										if ($rowSL['leavenum'] != 0){
											if($rowSL['leavecode'] == 1){
												$sl += $rowSL['leavenum'];
											}
										}
									}
									$selectEOMVL = OTKSR::getInstance() -> getEOMVL($rowEN['empno'], $dateFrom, $dateTo);
									$vl = 0;
									while($rowVL = mysqli_fetch_array($selectEOMVL)) {
										if ($rowVL['leavenum'] != 0){
											if($rowVL['leavecode'] == 2 or $row['leavecode'] == 3 or $row['leavecode'] == 4 or $row['leavecode'] == 5 or $row['leavecode'] == 6 ){
												$vl += $rowVL['leavenum'];
											}
										}
									}
									$selectEOMOT1 = OTKSR::getInstance() -> getEOMOT1($rowEN['empno'], $dateFrom, $dateTo);
									$ot1 = 0;
									while($rowOT1 = mysqli_fetch_array($selectEOMOT1)) {
										$ot1 += $rowOT1['ot1'];
									}
									$selectEOMOT2 = OTKSR::getInstance() -> getEOMOT2($rowEN['empno'], $dateFrom, $dateTo);
									$ot2 = 0;
									while($rowOT2 = mysqli_fetch_array($selectEOMOT2)) {
										$ot2 += $rowOT2['ot2'];
									}
									$selectEOMOT2a = OTKSR::getInstance() -> getEOMOT2a($rowEN['empno'], $dateFrom, $dateTo);
									$ot2a = 0;
									while($rowOT2a = mysqli_fetch_array($selectEOMOT2a)) {
										$ot2a += $rowOT2a['ot2a'];
									}
									$selectEOMOT3 = OTKSR::getInstance() -> getEOMOT3($rowEN['empno'], $dateFrom, $dateTo);
									$ot3 = 0;
									while($rowOT3 = mysqli_fetch_array($selectEOMOT3)) {
										$ot3 += $rowOT3['ot3'];
									}
									$selectEOMOT3a = OTKSR::getInstance() -> getEOMOT3a($rowEN['empno'], $dateFrom, $dateTo);
									$ot3a = 0;
									while($rowOT3a = mysqli_fetch_array($selectEOMOT3a)) {
										$ot3a += $rowOT3a['ot3a'];
									}
									$selectEOMOT4 = OTKSR::getInstance() -> getEOMOT4($rowEN['empno'], $dateFrom, $dateTo);
									$ot4 = 0;
									while($rowOT4 = mysqli_fetch_array($selectEOMOT4)) {
										$ot4 += $rowOT4['ot4'];
									}
									$selectEOMOT4a = OTKSR::getInstance() -> getEOMOT4a($rowEN['empno'], $dateFrom, $dateTo);
									$ot4a = 0;
									while($rowOT4a = mysqli_fetch_array($selectEOMOT4a)) {
										$ot4a += $rowOT['ot4a'];
									}
									$selectEOMOT5 = OTKSR::getInstance() -> getEOMOT5($rowEN['empno'], $dateFrom, $dateTo);
									$ot5 = 0;
									while($rowOT5 = mysqli_fetch_array($selectEOMOT5)) {
										$ot5 += $rowOT5['ot5'];
									}
									$selectEOMOT5a = OTKSR::getInstance() -> getEOMOT5a($rowEN['empno'], $dateFrom, $dateTo);
									$ot5a = 0;
									while($rowOT5a = mysqli_fetch_array($selectEOMOT5a)) {
										$ot5a += $rowOT5a['ot5a'];
									}
									$selectEOMOT6 = OTKSR::getInstance() -> getEOMOT6($rowEN['empno'], $dateFrom, $dateTo);
									$ot6 = 0;
									while($rowOT6 = mysqli_fetch_array($selectEOMOT6)) {
										$ot6 += $rowOT6['ot6'];
									}
									$selectEOMOT6a = OTKSR::getInstance() -> getEOMOT6a($rowEN['empno'], $dateFrom, $dateTo);
									$ot6a = 0;
									while($rowOT6a = mysqli_fetch_array($selectEOMOT6a)) {
										$ot6a += $rowOT6a['ot6a'];
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
			<p><h4>Computing EOM.. Please be patient..</h4></p>
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
	var oTable = $('#tblMain').dataTable({
	});
		
	$('#btn_exporteomtoexcel').click(function(){
		window.location.href = "eom-ExportToExcel.php?startDate="+ $('#txtStartDate').val() + "& endDate="+ $('#txtDate').val();
	});
	
	$('#btn_exporteomtoexcel2').click(function(){
		window.location.href = "eom-ExportToExcel2.php?startDate="+ $('#txtStartDate').val() + "& endDate="+ $('#txtDate').val();
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
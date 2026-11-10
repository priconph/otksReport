<html>
<head>
<title>FINAL</title>

	<!--SCRIPT and CSS-->
	<?php
	require_once("includes.php");
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
	th {
		font-size: 50%
	}
	td {
		font-size: 50%
	}
	</style>
	<script>
	$(document).ready(function(){
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
		//PRINT TIMECARD
		$('#printTimeCard').click(function(){
			$('#mainContainer').hide();
			$('#printTimeCard').hide();
			window.print();
		});
		
	});
	</script>
	
</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
		<nav class="navbar navbar-default" role="navigation" style="background-color:lightblue;">
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
				<li class=""><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class="active"><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>	
	<div class="panel panel-success" id="mainContainer">
		<div class="panel-heading">
			<h3 class="panel-title">INDIVIDUAL TIMECARD</h3>
		</div>
		<div class="panel-body">
				
					<form class="form-inline" role="form" method="POST" action="individualTimeCard.php">
					  <div class="form-group">
						<label class="sr-only" for="selSection">SECTION: </label>
						<select class="form-control" id="selSection">
							<option>SAMPLE SECTION</option>
						</select>
					  </div>
					  <div class="form-group">
						<label class="sr-only" for="txtStartDate">Start Date</label>
						<?php 
							if(isset($_POST['txtStartDate'])){
							echo '<input type="date" class="form-control" id="txtStartDate" name="txtStartDate" placeholder="Start Date" value="'.$_POST['txtStartDate'].'">';
							}
							else{
							echo '<input type="date" class="form-control" id="txtStartDate" name="txtStartDate" placeholder="Start Date" value="'.$dateToday.'">';
							}
						?>
					  </div>
					  <div class="form-group">
						<label class="sr-only" for="txtEndDate">End Date</label>
						<?php 
							if(isset($_POST['txtEndDate'])){
							echo '<input type="date" class="form-control" id="txtEndDate" name = "txtEndDate" placeholder="End Date" value="'.$_POST['txtEndDate'].'">';
							}
							else{
							echo '<input type="date" class="form-control" id="txtEndDate" name = "txtEndDate" placeholder="End Date" value="'.$dateToday.'">';
							}
						?>
					  </div>
					  <button type="submit" class="btn btn-default" id="SetDateRange">SET DATE</button>
					</form>
		</div>
	</div>
	
		<input type="button" id="printTimeCard" value="Print Table" />
		<div>
			<?php
				require_once('otksr-oop.php');
				$dateFrom = '2014-06-02';
				$dateTo = '2014-06-17';
				$selectEmpInfo = OTKSR::getInstance()->getIndividualTimeCardEmpNo($dateFrom, $dateTo);
				while($rowInfo = mysqli_fetch_array($selectEmpInfo)) {
				echo '<table border="1" width="300">
					<thead>
							<tr>
								<th style="text-align:center" colspan="13">PRICON MICROELECTRONICS INC. <br> OTKS 2.0 EMPLOYEE ATTENDANDANCE REPORT</th>
							</tr>
							<tr>
								<th style="text-align:center" colspan="13" style="font-size:100">For the Pay Period: '.$dateFrom.'~'.$dateTo.'</th>
							</tr>
							<tr>';
								$empno = $rowInfo['empno'];
								$ln = $rowInfo['LastName'];
								$fn = $rowInfo['FirstName'];
								$mn = $rowInfo['MiddleName'];
								$dept = $rowInfo['Department'];
							echo '
								<th colspan="7">Employee Name: '.$ln.', '.$fn.' '.substr($mn, 0, 1).'.</th>										
								<th colspan="8">Dept/Sect Name: '.$dept.'</th>										
							</tr>
							<tr>
								<th colspan="13">Employee Number: '.$empno.'</th>
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
						<tbody>
							<tr>';
								$individualTimeCard = OTKSR::getInstance()->getIndividualTimeCard($empno, $dateFrom, $dateTo);
								while($rowTimeCard = mysqli_fetch_array($individualTimeCard)) {
									$date = $rowTimeCard['date'];
									if($rowTimeCard['leavecode'] == 1) {
										$timeIn = "SL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 2) {
										$timeIn = "VL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 3) {
										$timeIn = "PL";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 4) {
										$timeIn = "ML";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 5) {
										$timeIn = "Bday";
										$timeOut = "";
									} else if($rowTimeCard['leavecode'] == 6) {
										$timeIn = "BL";
										$timeOut = "";
									} else {
										$timeIn = substr($rowTimeCard['time_in'], 0, 5);
										$timeOut = substr($rowTimeCard['time_out'], 0, 5);
									}
									$regOT = $rowTimeCard['ot1'];
									$sundayOT = $rowTimeCard['ot3'] + $rowTimeCard['ot3a'];
									$specialOT = $rowTimeCard['2'] + $rowTimeCard['ot2a'] + $rowTimeCard['5'] + $rowTimeCard['ot5a'];
									$holidayOT = $rowTimeCard['4'] + $rowTimeCard['ot4a'] + $rowTimeCard['6'] + $rowTimeCard['ot6a'];
									$undertime = $rowTimeCard['under_mins'];
									$nightDiff = $rowTimeCard['nightdiff'];
									$absent = $rowTimeCard['absent'];
									$slvl = $rowTimeCard['leavenum'];
								
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
							</tr>
						</tbody>';
							}
						}
				echo '</table>';
			?>
				
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
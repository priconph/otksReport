<html>
<head>
<title>FINAL</title>

	<!--CSS-->
    <link href="./css/bootstrap.css" rel="stylesheet">
	<link href="./css/jquery-ui.css" rel="stylesheet">
	<link rel="stylesheet" href="./css/jquery-ui.css">
	<link rel="stylesheet" href="./media/css/demo_table.css">

	<!--SCRIPT-->
	<script src="./js/jquery.js"></script>
	<script src="./js/modal.js"></script>
	<script src="./js/jquery-ui.js"></script> 
	<script type="text/javascript" language="javascript" src="./js/jquery.dataTables.js"></script>
	
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
				<li class=""><a href="uploadOT.php" id="loadUploadOT">Upload OT</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<!--<li class="active"><a href="lateUpload.php" id="loadLateUpload">Late Upload for OT and Leave</a></li>-->
				<li class=""><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>	
		
			<div class="panel panel-success" id="mainContainer">
		<div class="panel-heading">
			<h3 class="panel-title">LATE UPLOAD</h3>
		</div>
		<div class="panel-body">
				<div class="panel panel-info" id="panel1"> <!--TARGET CONTAINER FOR TABLES-->
					<div class="panel-heading">
						<h4 class="panel-title">LATE UPLOAD FOR OT</h4>
					</div>
					<div class="panel-body">
						<label style="margin-right:20px;width:140px">Employee No.: </label><input type="text">
						<label style="margin-right:20px;width:140px">Date: </label><input type="date">
						<label style="margin-right:20px;width:140px">OT Type: </label>
						<select>
						<option>Regular</option>
						<option>Sunday/Special</option>
						<option>Sunday</option>
						<option>Sunday/Legal</option>
						<option>Special</option>
						<option>Legal</option>
						</select>
						<label style="margin-right:20px;width:50px">Hours: </label><input type="text"><br>
						<center><button class="btn-primary">Submit</button></center>
					</div>
				</div>
				<div class="panel panel-info" id="panel2"> <!--TARGET CONTAINER FOR TABLES-->
					<div class="panel-heading">
						<h4 class="panel-title">LATE UPLOAD FOR LEAVE</h4>
					</div>
					<div class="panel-body">
						<label style="margin-right:20px;width:140px">Employee No.: </label><input type="text">
						<label style="margin-right:20px;width:140px">Date: </label><input type="date">
						<label style="margin-right:20px;width:140px">Leave Type: </label>
						<select>
						<option>SL</option>
						<option>VL</option>
						</select>
						<label style="margin-right:20px;width:140px">Days: </label><input type="text"><br>
						<center><button class="btn-primary">Submit</button></center>
					</div>
				</div>
			
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
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
	</style>
	
	<script>
	//function for edit
	$(document).ready(function() {
    $('#tblMain').dataTable()
		.makeEditable();
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
				<li class="active"><a href="uploadOT.php" id="loadUploadOT">Upload OT</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<!--<li class=""><a href="lateUpload.php" id="loadLateUpload">Late Upload for OT and Leave</a></li>-->
				<li class=""><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
			  </ul>
			  <!--
			  <form class="navbar-form navbar-left" role="search">
				<div class="form-group">
				  <input type="text" class="form-control" placeholder="Search">
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			  </form> -->
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>
	
	<div class="panel panel-success" id="mainContainer">
		<div class="panel-heading">
			<h3 class="panel-title">UPLOAD OT</h3>
		</div>
			<div class="panel-body">
				<form action="uploadOT.php" method="post" enctype="multipart/form-data">
					<label for="file">CHOOSE A TEXT FILE TO UPLOAD:</label><br>
					<input type="file" name="file" id="file"><br>
					<input type="submit" name="submit" value="Submit">
			</form>
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
						pending..
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
	</div>

</body>
</html>
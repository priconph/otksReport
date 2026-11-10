<html>
<head>
<title>OTKS REPORT</title>

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
	.panel-primary{
		overflow:scroll;
	}
	
	 .divPage
        {
            width:300px;
            height:200px;
        }
	
	
	</style>
	
	<script>
	//function for edit
	function editData(){
	$('#modalUpdate').modal('show')                // initializes and invokes show immediately
	}

	
	$(document).ready(function() {
		//Change active class on navbar
		$('.nav li a').on('click', function() {
			$(this).parent().parent().find('.active').removeClass('active');
			$(this).parent().addClass('active');
		});
		
		//Load Daily TFM in tblContainer as default
		if ($("#mainContainer").text().length > 0) {
			$("#mainContainer").load("./rawData.php"); 
		}    
		
		//Load rawData.php in mainContainer on click
		$("#loadRawData").on("click", function() {
			$("#mainContainer").load("./rawData.php"); 
		});	
		
		//Load dailyTFM.php in mainContainer on click
		$("#loadDailyTFM").on("click", function() {
			$("#mainContainer").load("./dailyTFM.php"); 
		});	
		
		//Load manualUpdating.php in mainContainer on click
		$("#loadManualUpdating").on("click", function() { 
			$("#mainContainer").load("./manualUpdating.php"); 
		});
		
		//Load inconsistencyLogs.php in mainContainer on click
		$("#loadInconsistencyLogs").on("click", function() { 
			$("#mainContainer").load("./inconsistencyLogs.php"); 
		});
		
		//Load lateUpload.php in mainContainer on click
		$("#loadLateUpload").on("click", function() { 
			$("#mainContainer").load("./lateUpload.php"); 
		});
		
		//Load eom.php in mainContainer on click
		$("#loadEom").on("click", function() { 
			$("#mainContainer").load("./eom.php"); 
		});
		
		//Load uploadOT.php in mainContainer on click
		$("#loadUploadOT").on("click", function() { 
			$("#mainContainer").load("./uploadOT.php"); 
		});
		
		//Load uploadSLVL.php in mainContainer on click
		$("#loadUploadSLVL").on("click", function() { 
			$("#mainContainer").load("./uploadSLVL.php"); 
		});
		
		//Load individualTimeCard.php in mainContainer on click
		$("#loadIndividualTimecard").on("click", function() { 
			$("#mainContainer").load("./individualTimeCard.php"); 
		});
	});

	
</script>
	
	
</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
	<!--NAVBAR-->
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
				<li class="active"><a href="#" id="loadRawData">OTKS Raw Data</a></li>
				<li class=""><a href="#" id="loadDailyTFM">Daily TFM</a></li>
				<li class=""><a href="#" id="loadManualUpdating">Manual Updating</a></li>
				<li class=""><a href="#" id="loadUploadOT">Upload OT</a></li>
				<li class=""><a href="#" id="loadUploadSLVL">Upload SL/VL</a></li>
				<li class=""><a href="#" id="loadLateUpload">Late Upload for OT and Leave</a></li>
				<li class=""><a href="#" id="loadEom">EOM Data</a></li>
				<li class=""><a href="#" id="loadIndividualTimecard">Individual Timecard</a></li>
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

	
	<div class="panel panel-success" id="mainContainer"> <!--TARGET CONTAINER FOR TABLES-->
		 <!--<img alt="Progress" src="./images/loader.gif" id="imgProg  visible="false" />-->
	</div>

	

</body>
</html>
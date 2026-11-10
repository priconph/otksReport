<!--SCRIPT and CSS-->
<?php
session_start();
	require_once("includes.php");
	require_once('otksr-oop.php');
	//require_once('./scripts/serverSide.php');
	//require_once('./scripts/ssp.class.php');

	$username = "arlbena";

?>

<html>
<head>
<title>FINAL</title>
	
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
	</style>
	
	<script>
	var username = " <?php echo $username; ?> ";
	var x;
	var iPos;
	var page;
	var pkiddel;
	var empnodel;
	var datedel;
	//datatable
	
	$(document).ready(function() {
    
	var oTable = $('#tblMain').dataTable({
        "processing": true,
        "serverSide": true,
		//"aoColumnDefs": [{ "sClass": "center", "aTargets": [ 0 ] }],
		"sAjaxSource" : "./scripts/serverSide.php",
		/*"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			$('td:eq(0)', nRow).addClass( "pkid" );
			$('td:eq(1)', nRow).addClass( "empno" );
			$('td:eq(2)', nRow).addClass( "SalaryType" );
			$('td:eq(3)', nRow).addClass( "date" );
			$('td:eq(4)', nRow).addClass( "time_in" );
			$('td:eq(5)', nRow).addClass( "mode_in" );
			$('td:eq(6)', nRow).addClass( "date_out" );
			$('td:eq(7)', nRow).addClass( "time_out" );
			$('td:eq(8)', nRow).addClass( "mode_out" );
			$('td:eq(9)', nRow).addClass( "reg_hours" );
			$('td:eq(10)', nRow).addClass( "ot_hours" );
			$('td:eq(11)', nRow).addClass( "absent" );
			$('td:eq(12)', nRow).addClass( "undertime" );
			$('td:eq(13)', nRow).addClass( "slvl_code" );
			$('td:eq(14)', nRow).addClass( "slvl_num" );
			$('td:eq(15)', nRow).addClass( "ot1" );
			$('td:eq(16)', nRow).addClass( "ot2" );
			$('td:eq(17)', nRow).addClass( "ot2a" );
			$('td:eq(18)', nRow).addClass( "ot3" );
			$('td:eq(19)', nRow).addClass( "ot3a" );
			$('td:eq(20)', nRow).addClass( "ot4" );
			$('td:eq(21)', nRow).addClass( "ot4a" );
			$('td:eq(22)', nRow).addClass( "ot5" );
			$('td:eq(23)', nRow).addClass( "ot5a" );
			$('td:eq(24)', nRow).addClass( "ot6" );
			$('td:eq(25)', nRow).addClass( "ot6a" );
			$('td:eq(26)', nRow).addClass( "nightdiff" );
			$('td:eq(27)', nRow).addClass( "aut" );
		}*/
    } );
	
	//function for selecting a row and place a value on pkid, empno, date, for deletion purposes
		$('#tblMain').on("click","tbody tr", function (event) { 
			iPos = oTable.fnGetPosition( this );
			if ( $(this).hasClass('selected') ) {
					$(this).removeClass('selected');
				}
			else {
					oTable.$('tr.selected').removeClass('selected');
					$(this).addClass('selected');
				}
			//Set variable data for delete
			pkiddel = $(this).find(".pkid").html(); //get value from a class
			empnodel = $(this).find(".empno").html(); //get value from a class
			datedel = $(this).find(".date").html(); //get value from a class
			dateOutdel = $(this).find(".date_out").html(); //get value from a class
			timeIndel = $(this).find(".time_in").html(); //get value from a class
			timeOutdel = $(this).find(".time_out").html(); //get value from a class
			
			});
		
		//Function for Deleting Selected Row
		$('#btn-DeleteRow').click( function () {
			//alert ("row "+iPos+" has been deleted!")
			alert("PKID: "+pkiddel+" EMPNO: "+empnodel+" date: "+datedel);
			var data={
				"action" : "deleteDailyTFM",
				"pkid" : pkiddel,
				"empno" : empnodel,
				"date" : datedel,
				"date_out" : dateOutdel,
				"time_in" : timeIndel,
				"time_out" : timeOutdel
			};
			data = $.param(data);
			
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "reportHandler.php", //Relative or absolute path to response.php file
				data: data,
				success: function(data) {
						oTable.fnDeleteRow(iPos);//delete row
					}
			});
			return false;
		});
		
		//Get value of each cell the table
		$('#tblMain').on("dblclick","tbody tr", function (event) { 
			iPos = oTable.fnGetPosition( this );
			x = iPos;
					
			if ( $(this).hasClass('selected') ) {
				$(this).removeClass('selected');
				}
			else {
				oTable.$('tr.selected').removeClass('selected');
				$(this).addClass('selected');
			}
		
			var pkid = $(this).find(".pkid").html(); //get value from a class
			var empno = $(this).find(".empno").html(); //get value from a class
			var SalaryType = $(this).find(".SalaryType").html(); //get value from a class
			var date = $(this).find(".date").html(); //get value from a class
			var time_in = $(this).find(".time_in").html(); //get value from a class
			var mode_in = $(this).find(".mode_in").html(); //get value from a class
			var date_out = $(this).find(".date_out").html(); //get value from a class
			var time_out = $(this).find(".time_out").html(); //get value from a class
			var mode_out = $(this).find(".mode_out").html(); //get value from a class
			var reg_hours = $(this).find(".reg_hours").html(); //get value from a class
			var ot_hours = $(this).find(".ot_hours").html(); //get value from a class
			var absent = $(this).find(".absent").html(); //get value from a class
			var undertime = $(this).find(".undertime").html(); //get value from a class
			var slvl_code = $(this).find(".slvl_code").html(); //get value from a class
			var slvl_num = $(this).find(".slvl_num").html(); //get value from a class
			var ot1 = $(this).find(".ot1").html(); //get value from a class
			var ot2 = $(this).find(".ot2").html(); //get value from a class
			var ot2a = $(this).find(".ot2a").html(); //get value from a class
			var ot3 = $(this).find(".ot3").html(); //get value from a class
			var ot3a = $(this).find(".ot3a").html(); //get value from a class
			var ot4 = $(this).find(".ot4").html(); //get value from a class
			var ot4a = $(this).find(".ot4a").html(); //get value from a class
			var ot5 = $(this).find(".ot5").html(); //get value from a class
			var ot5a = $(this).find(".ot5a").html(); //get value from a class
			var ot6 = $(this).find(".ot6").html(); //get value from a class
			var ot6a = $(this).find(".ot6a").html(); //get value from a class
			var nightdiff = $(this).find(".nightdiff").html(); //get value from a class
			var aut = $(this).find(".aut").html(); //get value from a class
			
			$('#pkid').val(pkid);
			$('#empno').val(empno);
			$('#SalaryType').val(SalaryType);
			$('#date').val(date);
			$('#time_in').val(time_in);
			$('#mode_in').val(mode_in);
			$('#date_out').val(date_out);
			$('#time_out').val(time_out);
			$('#mode_out').val(mode_out);
			$('#reg_hours').val(reg_hours);
			$('#ot_hours').val(ot_hours);
			$('#absent').val(absent);
			$('#undertime').val(undertime);
			$('#slvl_code').val(slvl_code);
			$('#slvl_num').val(slvl_num);
			$('#ot1').val(ot1);
			$('#ot2').val(ot2);
			$('#ot2a').val(ot2a);
			$('#ot3').val(ot3);
			$('#ot3a').val(ot3a);
			$('#ot4').val(ot4);
			$('#ot4a').val(ot4a);
			$('#ot5').val(ot5);
			$('#ot5a').val(ot5a);
			$('#ot6').val(ot6);
			$('#ot6a').val(ot6a);
			$('#nightdiff').val(nightdiff);
			$('#aut').val(aut);
			$('#modalUpdate').data('pkid',pkid).modal('show');
					
		});
		
		$('#btn-Update').click(function(){
			var pkid = $('#modalUpdate').data('pkid');
			var empno = $('#empno').val();
			var SalaryType = $('#SalaryType').val();
			var date = $('#date').val();
			var time_in = $('#time_in').val();
			var mode_in = $('#mode_in').val();
			var date_out = $('#date_out').val();
			var time_out = $('#time_out').val();
			var mode_out = $('#mode_out').val();
			var reg_hours = $('#reg_hours').val();
			var ot_hours = $('#ot_hours').val();
			var absent = $('#absent').val();
			var undertime = $('#undertime').val();
			var slvl_code = $('#slvl_code').val();
			var slvl_num = $('#slvl_num').val();
			var ot1 = $('#ot1').val();
			var ot2 = $('#ot2').val();
			var ot2a = $('#ot2a').val();
			var ot3 = $('#ot3').val();
			var ot3a = $('#ot3a').val();
			var ot4 = $('#ot4').val();
			var ot4a = $('#ot4a').val();
			var ot5 = $('#ot5').val();
			var ot5a = $('#ot5a').val();
			var ot6 = $('#ot6').val();
			var ot6a = $('#ot6a').val();
			var nightdiff = $('#nightdiff').val();
			var aut = $('#aut').val();
			
			if(time_in != "" || time_out != "" || date != "" || date_out != ""){
				absent = "0";
			}
					
			var data = {
					"action" : "updateDailyTFM",
					"pkid" : pkid, 
					"empno" : empno, 
					"date" : date, 
					"time_in" : time_in, 
					"mode_in" : mode_in, 
					"date_out" : date_out, 
					"time_out" : time_out,
					"mode_out" : mode_out,
					"reg_hours" : reg_hours,
					"ot_hours" : ot_hours,
					"absent" : absent,
					"undertime" : undertime,
					"slvl_code" : slvl_code,
					"slvl_num" : slvl_num,
					"ot1" : ot1,
					"ot2" : ot2,
					"ot2a" : ot2a,
					"ot3" : ot3,
					"ot3a" : ot3a,
					"ot4" : ot4,
					"ot4a" : ot4a,
					"ot5" : ot5,
					"ot5a" : ot5a,
					"ot6" : ot6,
					"ot6a" : ot6a,
					"nightdiff" : nightdiff,
					"aut" : aut,
					"username" : username
				};
				
			data = $.param(data);
			$.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "reportHandler.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(data) {
					var oSettings = oTable.fnSettings();
					var page = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength);
					oTable.fnUpdate( date, x, "3");
					oTable.fnUpdate( time_in, x, "4");
					oTable.fnUpdate( mode_in, x, "5");
					oTable.fnUpdate( date_out, x, "6");
					oTable.fnUpdate( time_out, x, "7");
					oTable.fnUpdate( mode_out, x, "8");
					oTable.fnUpdate( reg_hours, x, "9");
					oTable.fnUpdate( ot_hours, x, "10");
					oTable.fnUpdate( absent, x, "11");
					oTable.fnUpdate( undertime, x, "12");
					oTable.fnUpdate( slvl_code, x, "13");
					oTable.fnUpdate( slvl_num, x, "14");
					oTable.fnUpdate( ot1, x, "15");
					oTable.fnUpdate( ot2, x, "16");
					oTable.fnUpdate( ot2a, x, "17");
					oTable.fnUpdate( ot3, x, "18");
					oTable.fnUpdate( ot3a, x, "19");
					oTable.fnUpdate( ot4, x, "20");
					oTable.fnUpdate( ot4a, x, "21");
					oTable.fnUpdate( ot5, x, "22");
					oTable.fnUpdate( ot5a, x, "23");
					oTable.fnUpdate( ot6, x, "24");
					oTable.fnUpdate( ot6a, x, "25");
					oTable.fnUpdate( nightdiff, x, "26");
					oTable.fnUpdate( aut, x, "27");
					oTable.fnPageChange( page ); //go to the current pagination
					$('#modalUpdate').modal('hide');
					}
			});
			return false;
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
		
		
		$('#btnHoliday').click(function(){
			$('#modalHoliday').modal('show');
		});
		
		$('#btnSetHoliday').click(function(){
			var Holiday = $('#txtHoliday').val();
			
			var data = {
			"action" : "deleteHolidayAbsent",
			"date" : Holiday
			};
			
			$.param(data);
			$.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "reportHandler.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(data) {
						alert(Holiday + " has been set to holiday!");
						$('#modalHoliday').modal('hide');
				}
			});
			return false;
		});
		
	


	} );

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
				<li class="active"><a href="dailyTFM.php" id="loadDailyTFM">Daily TFM</a></li>
				<!--<li class=""><a href="manualUpdating.php" id="loadManualUpdating">Manual Updating</a></li>-->
				<!--<li class=""><a href="uploadOT.php" id="loadUploadOT">Upload OT</a></li>-->
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<!--<li class=""><a href="lateUpload.php" id="loadLateUpload">Late Upload for OT and Leave</a></li>-->
				<li class=""><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
			  </ul>
			 </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>
	
	
		<div class="panel panel-success" id="mainContainer">
		<div class="panel-heading">
			<h3 class="panel-title">DAILY TFM</h3>
		</div>
		<div class="panel-body">
			
			<form class="form-inline" role="form" method="POST" action="testserverside.php">
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
			  <button type="submit" class="btn btn-default">SET DATE</button>
			 <?php
				if(isset($_POST['txtStartDate'])){
					$_SESSION['startDateTFM'] = $_POST['txtStartDate'];
					$_SESSION['endDateTFM'] = $_POST['txtEndDate'];
				
				//$check = OTKSR::getInstance()->getEmpNoTkDataHRIS($_POST['txtStartDate'],$_POST['txtEndDate'],$username);
				 //oTable.ajax.reload();
				}
				else
				{
					$_SESSION['startDateTFM'] = date("Y-m-d");
					$_SESSION['endDateTFM'] = date("Y-m-d");
				}
			 ?>
			</form>
				
			<button class="btn btn-default" id="btnHoliday">HOLIDAY</button>
			<table id="tblMain" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>pkid</th>
						<th>empno</th>
						<th>SalaryType</th>
						<th>date</th>
						<th>time_in.</th>
						<th>mode_in</th>
						<th>date_out</th>
						<th>time_out</th>
						<th>mode_out</th>
						<th>reg_hours</th>
						<th>ot_credit</th>
						<th>absent</th>
						<th>under_mins</th>
						<th>leavecode</th>
						<th>leavenum</th>
						<th>ot1</th>
						<th>ot2</th>
						<th>ot2a</th>
						<th>ot3</th>
						<th>ot3a</th>
						<th>ot4</th>
						<th>ot4a</th>
						<th>ot5</th>
						<th>ot5a</th>
						<th>ot6</th>
						<th>ot6a</th>
						<th>nightdiff</th>
						<th>aut</th>
					</tr>
				</thead>
				
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
						<label style="margin-right:20px;width:160px">PKID: </label><input class="styleUpdate" type="text" id="pkid" placeholder="PKID" readonly>
						<label style="margin-right:20px;width:160px">EmpNo: </label><input class="styleUpdate" type="text" id="empno" placeholder="EMPNO" readonly>
						<label style="margin-right:20px;width:160px">ECode: </label><input class="styleUpdate" type="text" id="SalaryType" placeholder="Salary Type"><br>
						<label style="margin-right:20px;width:160px">Date: </label><input class="styleUpdate" type="date" id="date" placeholder="Date">
						<label style="margin-right:20px;width:160px">Time IN: </label><input class="styleUpdate" type="time" id="time_in" placeholder="Time IN"><br>
						<label style="margin-right:20px;width:160px">Mode IN: </label><input class="styleUpdate" type="text" id="mode_in" placeholder="Mode IN"><br>
						<label style="margin-right:20px;width:160px">Date OUT: </label><input class="styleUpdate" type="date" id="date_out" placeholder="Date OUT">
						<label style="margin-right:20px;width:160px">Time OUT: </label><input class="styleUpdate" type="time" id="time_out" placeholder="Time OUT"><br>
						<label style="margin-right:20px;width:160px">Mode OUT: </label><input class="styleUpdate" type="text" id="mode_out" placeholder="Mode OUT"><br>
						<label style="margin-right:20px;width:160px">REG_HOURS: </label><input class="styleUpdate" type="text" id="reg_hours" placeholder="REG_HOURS">
						<label style="margin-right:20px;width:160px">OT HOURS: </label><input class="styleUpdate" type="text" id="ot_hours" placeholder="OT HOURS">
						<label style="margin-right:20px;width:160px">Absent: </label><input class="styleUpdate" type="text" id="absent" placeholder="Absent">
						<label style="margin-right:20px;width:160px">Undertime: </label><input class="styleUpdate" type="text" id="undertime" placeholder="Undertime">
						<label style="margin-right:20px;width:160px">SL VL CODE: </label><input class="styleUpdate" type="text" id="slvl_code" placeholder="SL VL CODE">
						<label style="margin-right:20px;width:160px">SL VL NUM: </label><input class="styleUpdate" type="text" id="slvl_num" placeholder="SL VL NUM">
						<label style="margin-right:20px;width:160px">OT 1: </label><input class="styleUpdate" type="text" id="ot1" placeholder="OT 1">
						<label style="margin-right:20px;width:160px">OT 2: </label><input class="styleUpdate" type="text" id="ot2" placeholder="OT 2">
						<label style="margin-right:20px;width:160px">OT 2A: </label><input class="styleUpdate" type="text" id="ot2a" placeholder="OT 2A">
						<label style="margin-right:20px;width:160px">OT 3: </label><input class="styleUpdate" type="text" id="ot3" placeholder="OT 3">
						<label style="margin-right:20px;width:160px">OT 3A: </label><input class="styleUpdate" type="text" id="ot3a" placeholder="OT 3A">
						<label style="margin-right:20px;width:160px">OT 4: </label><input class="styleUpdate" type="text" id="ot4" placeholder="OT 4">
						<label style="margin-right:20px;width:160px">OT 4A: </label><input class="styleUpdate" type="text" id="ot4a" placeholder="OT 4A">
						<label style="margin-right:20px;width:160px">OT 5: </label><input class="styleUpdate" type="text" id="ot5" placeholder="OT 5">
						<label style="margin-right:20px;width:160px">OT 5A: </label><input class="styleUpdate" type="text" id="ot5a" placeholder="OT 5A">
						<label style="margin-right:20px;width:160px">OT 6: </label><input class="styleUpdate" type="text" id="ot6" placeholder="OT 6">
						<label style="margin-right:20px;width:160px">OT 6A: </label><input class="styleUpdate" type="text" id="ot6a" placeholder="OT 6A">
						<label style="margin-right:20px;width:160px">Night Diff: </label><input class="styleUpdate" type="text" id="nightdiff" placeholder="Night Diff">
						<label style="margin-right:20px;width:160px">AUT: </label><select class="styleUpdate" id="aut">
						<option>TRUE</option>
						<option>FALSE</option>
						</select>
				
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
	
	
	<!-- Modal Holiday-->
	<div class="modal fade" id="modalHoliday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:300px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Enter a Date of a Holiday</h4>
		  </div>
		  <div class="modal-body">
				<form class="form-inline" role="form">
					<div class="form-group" style="margin-left:15px;margin-top:15px">
						<label>Date</label><input type="date" id="txtHoliday">
					</div>
				</form>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btnSetHoliday">Set Date as Holiday</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	

</body>
</html>
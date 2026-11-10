<html>
<head>
<title>OTKS REPORT</title>

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
	.panel-body{
		overflow:scroll;
	}
	</style>
	
	<script>
	
	//Script Variables 
	var x;
	var iPos;
	var page;
	var pkiddel;
	var empnodel;
	var datedel;
	
	//datatable
	$(document).ready(function() {
		//DataTables Initialization
		var oTable = $('#tblMain').dataTable({
			stateSave: true,
			"bPaginate": true,
			"sDom": 'T<"clear">lfrtip',
			"oTableTools": {
				"aButtons": [
					"copy",
					"xls"
				]
			}
		});
		
		//function for selecting a row and place a value on pkid, empno, date, for deletion purposes
		$('#tblMain').on("click","tbody tr", function (event) { 
			iPos = oTable.fnGetPosition( this );
			if ( $(this).hasClass('selected') ) {
				$(this).removeClass('selected');
				}
			else {
				oTable.$('tr.selected').removeClass('selected');
				$(this).addClass('selected');
			
			//Set variable data for delete
			pkiddel = $(this).find(".pkid").html(); //get value from a class
			empnodel = $(this).find(".empno").html(); //get value from a class
			datedel = $(this).find(".date").html(); //get value from a class
			}
			});
		
		//Function for Deleting Selected Row
		$('#btn-DeleteRow').click( function () {
			//alert ("row "+iPos+" has been deleted!")
			alert("PKID: "+pkiddel+" EMPNO: "+empnodel+" date: "+datedel);
			var data={
			"action" : "deleteDailyTFM",
			"pkid" : pkiddel,
			"empno" : empnodel,
			"date" : datedel
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
		
		
		$('#tblMain').on("dblclick","tbody tr", function (event) { 
			iPos = oTable.fnGetPosition( this );
			x = iPos;
			
			var empno = $(this).find(".empno").html(); //get value from a class
			var name = $(this).find(".name").html(); //get value from a class
			var department = $(this).find(".department").html(); //get value from a class
			var section = $(this).find(".section").html(); //get value from a class
			var date = $(this).find(".date").html(); //get value from a class
			var time_in = $(this).find(".time_in").html(); //get value from a class
			var mode_in = $(this).find(".mode_in").html(); //get value from a class
			var date_out = $(this).find(".date_out").html(); //get value from a class
			var time_out = $(this).find(".time_out").html(); //get value from a class
			var mode_out = $(this).find(".mode_out").html(); //get value from a class
			var absent = $(this).find(".absent").html(); //get value from a class
			var undertime = $(this).find(".undertime").html(); //get value from a class
			var sl = $(this).find(".sl").html(); //get value from a class
			var vl = $(this).find(".vl").html(); //get value from a class
			var nightdiff = $(this).find(".nightdiff").html(); //get value from a class
			
			$('#txtEmpNo').val(empno);
			$('#txtName').val(name);
			$('#txtDepartment').val(department);
			$('#txtSection').val(section);
			$('#txtDate').val(date);
			$('#txtTime_In').val(time_in);
			$('#txtMode_In').val(mode_in);
			$('#txtDate_Out').val(date_out);
			$('#txtTime_Out').val(time_out);
			$('#txtMode_Out').val(mode_out);
			$('#txtAbsent').val(absent);
			$('#txtUndertime').val(undertime);
			$('#txtSL').val(sl);
			$('#txtVL').val(vl);
			$('#txtNightDiff').val(nightdiff);
						
			$('#modalUpdate').modal('show');
			
		});
		
		//handles update here	
		$('#btn-Update').click(function(){
			//var pkid = $('#modalUpdate').data('pkid');
			var empno = $('#txtEmpNo').val();
			var name = $('#txtName').val();
			var department = $('#txtDepartment').val();
			var date = 	$('#txtDate').val();
			var time_in = $('#txtTime_In').val();
			var mode_in = $('#txtMode_In').val();
			var date_out = $('#txtDate_Out').val();
			var time_out = $('#txtTime_Out').val();
			var mode_out = $('#txtMode_Out').val();
			var absent = $('#txtAbsent').val();
			var undertime = $('#txtUndertime').val();
			var sl = $('#txtSL').val();
			var vl = $('#txtVL').val();
			var nightdiff = $('#txtNightDiff').val();
			
			var data={
					"action" : "updateManualUpdating", 
					"empno" : empno, 
					"name" : name, 
					"department" : department, 
					"section" : section, 
					"date" : date, 
					"time_in" : time_in, 
					"mode_in" : mode_in, 
					"date_out" : date_out, 
					"time_out" : time_out,
					"mode_out" : mode_out,
					"absent" : absent,
					"undertime" : undertime,
					"sl" : sl,
					"vl" : vl,
					"nightdiff": nightdiff
				};
			data = $.param(data);
			$.ajax({
				  type: "POST",
				  dataType: "json",
				  url: "reportHandler.php", //Relative or absolute path to response.php file
				  data: data,
				  success: function(data) {
					//window.location.href = 'rawData.php'; 
					oTable.fnReloadAjax();
					var oSettings = oTable.fnSettings();
					var page = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength);
					oTable.fnUpdate( date, x, "4");
					oTable.fnUpdate( time_in, x, "5");
					oTable.fnUpdate( mode_in, x, "6");
					oTable.fnUpdate( date_out, x, "7");
					oTable.fnUpdate( time_out, x, "8");
					oTable.fnUpdate( mode_out, x, "9");
					oTable.fnUpdate( absent, x, "10");
					oTable.fnUpdate( undertime, x, "11");
					oTable.fnUpdate( sl, x, "12");
					oTable.fnUpdate( vl, x, "13");
					oTable.fnUpdate( nightdiff, x, "14");
					oTable.fnPageChange( page );
					$('#modalUpdate').modal('hide');
					}
			});
			return false;
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
				<li class=""><a href="rawData.php" id="loadRawData">OTKS Raw Data</a></li>
				<li class=""><a href="dailyTFM.php" id="loadDailyTFM">Daily TFM</a></li>
				<li class="active"><a href="manualUpdating.php" id="loadManualUpdating">Manual Updating</a></li>
				<li class=""><a href="uploadOT.php" id="loadUploadOT">Upload OT</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<li class=""><a href="lateUpload.php" id="loadLateUpload">Late Upload for OT and Leave</a></li>
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
	
	<div class="panel panel-success" id="mainContainer"> <!--TARGET CONTAINER FOR TABLES-->		
		<div class="panel-heading">
			<h3 class="panel-title">DAILY TFM</h3>
		</div>
		<div class="panel-body">
			
			<form class="form-inline" role="form" method="POST" action="manualUpdating.php">
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
			</form>
			<button id="btn-DeleteRow">DELETE SELECTED</button>

			<table border="1" class="table" id="tblMain">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Dept</th>
						<th>Sect</th>
						<th>Date IN</th>
						<th>In</th>
						<th>IN Remarks</th>
						<th>Date OUT</th>
						<th>Out</th>
						<th>OUT Remarks</th>
						<th>Absent</th>
						<th>Undertime</th>
						<th>SL</th>
						<th>VL</th>
						<th>NIGHT DIFF</th>
					</tr>
				</thead>
				<tbody>
					<!--<tr ondblclick="editData()"> FOR MODAL UPDATING-->
					<?php				
						$y=1;
						while ($y<=20){ 
							echo '<tr>'; //ON DBL CLICK MODAL RESERVE FUNCTION
							//echo '<tr>';
							echo '<td>a</td>
							<td class="cell1">'.$y.'</td>
							<td class="cell2">'.$y.'</td>
							<td class="cell3">'.$y.'</td>
							<td class="cell4">'.$y.'</td>
							<td class="cell5">'.$y.'</td>
							<td class="cell6">'.$y.'</td>
							<td class="cell7">'.$y.'</td>
							<td class="cell8">'.$y.'</td>
							<td class="cell9">'.$y.'</td>
							<td class="cell10">'.$y.'</td>
							<td class="cell11">'.$y.'</td>
							<td class="cell12">'.$y.'</td>
							<td class="cell13">'.$y.'</td>
							<td class="cell14">'.$y.'</td>';
							echo '</tr>';
							$y++;
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
						<label style="margin-right:20px;width:160px">EN: </label><input type="text" id="txtEmpNo" placeholder="ID" readonly>
						<label style="margin-right:20px;width:160px">Name: </label><input type="text" id="txtName" placeholder="ECode" readonly>
						<label style="margin-right:20px;width:160px">Dept: </label><input type="text" id="txtDepartment" placeholder="Department">
						<label style="margin-right:20px;width:160px">Sect: </label><input type="text" id="txtSection" placeholder="Section">
						<label style="margin-right:20px;width:160px">Date IN: </label><input type="date" id="txtDate" placeholder="IN Remarks">
						<label style="margin-right:20px;width:160px">In: </label><input type="time" id="txtTime_In" placeholder="Date OUT"><br>
						<label style="margin-right:20px;width:160px">IN Remarks: </label><input type="text" id="txtMode_In" placeholder="REG_HOURS"><br>
						<label style="margin-right:20px;width:160px">Date OUT: </label><input type="date" id="txtDate_Out" placeholder="Out">
						<label style="margin-right:20px;width:160px">Out: </label><input type="time" id="txtTime_Out" placeholder="OUT Remarks"><br>
						<label style="margin-right:20px;width:160px">OUT Remarks: </label><input type="text" id="txtMode_Out" placeholder="OT HOURS"><br>
						<label style="margin-right:20px;width:160px">Absent: </label><input type="text" id="txtAbsent" placeholder="ABSENT">
						<label style="margin-right:20px;width:160px">Undertime: </label><input type="text" id="txtUndertime" placeholder="Undertime">
						<label style="margin-right:20px;width:160px">SL: </label><input type="text" id="txtSL" placeholder="SL">
						<label style="margin-right:20px;width:160px">VL: </label><input type="text" id="txtVL" placeholder="VL">
						<label style="margin-right:20px;width:160px">NIGHT DIFF: </label><input type="txtNightDiff" id="" placeholder="OT 1">
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
	

</body>
</html>
</html>
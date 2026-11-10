<html>
<head>
<title>FINAL</title>

	<!--SCRIPT and CSS-->
	<?php
		require_once("includes.php"); 
		require_once("otksr-oop.php");
		session_start();
		$username = "marlope";
	?>

	<style> 
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
		.pkid,.mode_in,.mode_out{
			display : none;
		}
	</style>
	
	<script>
	
	function loadDiv(){
	var auto_refresh = setInterval(function () {
    $('.panel-body').fadeOut('slow', function() {
        $(this).load('/echo/json/', function() {
            $(this).fadeIn('slow');
        });
    });
	}, 15000); 
	}
	
	//function that loads additional employee info in adding a Record
	function loadEmpInfo(empno){
		var empno = $('#txtEmpNoA').val();
		$('#exist').hide();
		var data = {
			"action" : "loadEmpInfo",
			"empno" : empno
		};
		data = $.param(data);
		
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "reportHandler.php",
			data: data,
			success: function(data){
					$('#txtECodeA').val(data['ECode']);
					$('#txtNameA').val(data['name']);
					$('#txtDepartmentA').val(data['department']);
					$('#txtPositionA').val(data['position']);
					$('#txtSectionA').val(data['section']);
					loadApprover(empno);
				}
		});
	}
	
	function loadApprover(empno) {
		$('#approver').empty();
		$.getJSON('test.php', {empno : empno}, function(data) {
			$.each(data, function(index, item) {
				var myOptions = { val1 : item.NAME};

				$.each(myOptions, function(val, text) {
					$('#approver').append(new Option(text,val));
				});
			});
		}); 
	}
	//Script Variables 
	var username = "<?php echo $username; ?>";
	var x,xx;
	var iPos;
	var page;
	var pkiddel;
	var empnodel;
	var datedel;
	var date_out;
	var time_in;
	var time_out;
	
	//function for edit
	$(document).ready(function() {

		$('#exist').hide();
		//Data Tables Initialization
		var oTable = $('#tblMain').dataTable({
			"columnDefs": [ 
					{ 
						"sClass": "pkid", "aTargets": [ 0 ] //hidden column in datatables
					},
					{ 
						"sClass": "mode_in", "aTargets": [ 3 ] //hidden column in datatables
					},
					{ 
						"sClass": "mode_out", "aTargets": [ 6 ] //hidden column datatables
					}
					]
		});
			
		//Function to Open Add Record Modal
		$('#btn-AddRec').click( function () {
			$('#exist').hide();
			$('#modalAddRec').modal('show'); 
		});	
		
		//Function for adding row on table
		$("#btn-AddRecModal").click(function(){  
			var empno = $('#txtEmpNoA').val(); //get value from textbox id
			var ECode = $('#txtECodeA').val();
			var name= $('#txtNameA').val();
			var department = $('#txtDepartmentA').val();
			var position = $('#txtPositionA').val();
			var section = $('#txtSectionA').val();
			var date = 	$('#txtDateInA').val(); //get value from textbox id
			var time_in = $('#txtTimeInA').val();
			var mode_in = $('#txtModeInA').val();
			var date_out = $('#txtDateOutA').val();
			var time_out = $('#txtTimeOutA').val();
			var mode_out = $('#txtModeOutA').val();
								
			//pass data into an array					
			var data = {
				  "action": "addRawDataGuard", //action passed on handler
				  "empno" : empno,
				  "date" : date,
				  "time_in" : time_in,
				  "mode_in" : mode_in,
				  "date_out" : date_out,
				  "time_out" : time_out,
				  "mode_out" : mode_out,
				  "username" : username,
				  "status" : "0"
				  
			  };
			data = $.param(data); //turn data into serialize form
			
			$.ajax({
			  type: "POST",
			  dataType: "json",
			  url: "reportHandler.php", //Relative or absolute path 
			  data: data,
			  success: function(data) {
				if(data['msg'] == 0){
					$('#tblMain').dataTable().fnAddData([
							date,
							time_in,
							date_out,
							time_out,
							empno,
							name,
							position,
							department,
							section,
							"",// for the hidden column pkid
							"",// for the hidden column pkid
							"",// for the hidden column pkid
							"" // for the hidden column pkid
						] );
						$('#modalAddRec').modal('hide'); //closes modal on adding success
				  }else{
						$('#exist').show(); //shows a record exist in the modal if duplicate record occurs
				  }
			  }
			});
			return false;
				
		});
		
		/*
		//Function for Deleting Selected Row
		$('#btn-DeleteRow').click( function () {
			if(datedel!=0 || date_outdel!=0 || time_indel!=0 || time_outdel!=0){ //validation for data deletion
				alert("Date In, Date Out, Time In, and Time Out must be deleted before deleting this record!");
			}
			else{ 
			
				alert("PKID: "+pkiddel+" EMPNO: "+empnodel+" date: "+datedel); //messagebox containing data of the deleted record
				//pass data into an array 
				var data={
					"action" : "deleteRawData", //action passed on handler
					"pkid" : pkiddel,
					"empno" : empnodel,
					"date" : datedel,	
					"date_out" : date_outdel,		
					"time_in" : time_indel,		
					"time_out" : time_outdel	
				};
				data = $.param(data); //convert data into serialize form
				
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
			}
		});
		*/
		
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
			date_outdel = $(this).find(".date_out").html(); //get value from a class
			time_indel = $(this).find(".time_in").html(); //get value from a class
			time_outdel = $(this).find(".time_out").html(); //get value from a class
		});
		
		
		//Get cell individual values and place it in a variable, display value on modal
		$('#tblMain').on("dblclick","tbody tr", function (event) { 
			if ( $(this).hasClass('selected') ) {
					$(this).removeClass('selected');
				}
			else {
					oTable.$('tr.selected').removeClass('selected');
					$(this).addClass('selected');
				}	
		
					x = $(this).index();						
					xx = $(this);	//get row for changing font color after the update procedure
					
			var pkid = $(this).find(".pkid").html(); //get value from a class
			var empno = $(this).find(".empno").html(); //get value from a class
			var date = $(this).find(".date").html(); //get value from a class
			var time_in = $(this).find(".time_in").html(); //get value from a class
			var mode_in = $(this).find(".mode_in").html(); //get value from a class
			var date_out = $(this).find(".date_out").html(); //get value from a class
			var time_out = $(this).find(".time_out").html(); //get value from a class
			var mode_out = $(this).find(".mode_out").html(); //get value from a class
						
			iPos = oTable.fnGetPosition( this ); //select row in datatables
			x = iPos;
					
			$('#txtEmpNo').val(empno); //pass value of empno to the textbox txtEmpNo
			$('#txtDateIn').val(date);
			$('#txtTimeIn').val(time_in);
			$('#txtModeIn').val(mode_in);
			$('#txtDateOut').val(date_out);
			$('#txtTimeOut').val(time_out);
			$('#txtModeOut').val(mode_out);
			
			$('#modalUpdate').data('pkid',pkid).modal('show');
					
		});
							
	/*
	//handles update here	
	$('#btn-Update').click(function(){
		var pkid = $('#modalUpdate').data('pkid'); 
		var empno = $('#txtEmpNo').val(); //get textbox txtEmpNo value and assign it in empno
		var date = 	$('#txtDateIn').val();
		var time_in = $('#txtTimeIn').val();
		var mode_in = $('#txtModeIn').val();
		var date_out = $('#txtDateOut').val();
		var time_out = $('#txtTimeOut').val();
		var mode_out = $('#txtModeOut').val(); //get textbox txtModeOut value and assign it in mode_out
		
		var data={
				"action" : "updateRawData", 
				"pkid" : pkid, 
				"empno" : empno, 
				"date" : date, 
				"time_in" : time_in, 
				"mode_in" : mode_in, 
				"date_out" : date_out, 
				"time_out" : time_out,
				"mode_out" : mode_out,
				"username" : username
			};
		data = $.param(data);
		$.ajax({
			  type: "POST",
			  dataType: "json",
			  url: "reportHandler.php", //Relative or absolute path to response.php file
			  data: data,
			  success: function(data) {
					//window.location.href = "./index.php";
					var oSettings = oTable.fnSettings();
					var page = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength);
					
					//changes the datatables data
					oTable.fnUpdate( pkid, x, "0");
					oTable.fnUpdate( date, x, "1");
					oTable.fnUpdate( time_in, x, "2");
					oTable.fnUpdate( mode_in, x, "3");
					oTable.fnUpdate( date_out, x, "4");
					oTable.fnUpdate( time_out, x, "5");
					oTable.fnUpdate( mode_out, x, "6");
					
					if( date != "" && time_in != "" && date_out != "" && time_out != "" ){
						$(xx).find('.date').css({"color":"#000000"}); //change color to black
						$(xx).find('.time_in').css({"color":"#000000"}); //change color to black
						$(xx).find('.date_out').css({"color":"#000000"}); //change color to black
						$(xx).find('.time_out').css({"color":"#000000"}); //change color to black
						$(xx).find('.empno').css({"color":"#000000"}); //change color to black
						$(xx).find('.SalaryType').css({"color":"#000000"}); //change color to black
						$(xx).find('.Department').css({"color":"#000000"}); //change color to black
						$(xx).find('.Position').css({"color":"#000000"}); //change color to black
						$(xx).find('.Section').css({"color":"#000000"}); //change color to black
					}										
					oTable.fnPageChange( page );
					$('#modalUpdate').modal('hide');
				}
		});
		return false;
	});
	
	$('#btn-GenerateIR').click(function(){
		window.open("inconsistencyrecord.php");
	}); */
	
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
	//On change pass approver to module
	   
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
				<li class="active"><a href="index.php" id="loadRawData">OTKS Raw Data</a></li>
				<li class=""><a href="dailyTFM.php" id="loadDailyTFM">Daily TFM</a></li>
				<li class=""><a href="uploadSLVL.php" id="loadUploadSLVL">Upload SL/VL</a></li>
				<li class=""><a href="eom.php" id="loadEom">EOM Data</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard">Individual Timecard</a></li>
			  </ul>
			 </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>
		
	<div class="panel panel-success" id="mainContainer"> <!--TARGET CONTAINER FOR TABLES-->	
		<div class="panel-heading">
			<h3 class="panel-title">RAW DATA FROM OTKS</h3>
		</div>
		<div class="panel-body">
			<form class="form-inline" role="form" method="POST" action="guardModule.php">
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
			<?php
			
			if(isset($_POST['txtStartDate'])){
				$_SESSION['date'] = $_POST['txtStartDate'];
				$_SESSION['date_out'] = $_POST['txtEndDate'];
			}else{
				$_SESSION['date'] = "";
				$_SESSION['date_out'] = "";
			}
				
			?>
			
			<button id="btn-AddRec" class="btn btn-default">ADD RECORD</button>
			<!--<button id="btn-DeleteRow" class="btn btn-default">DELETE SELECTED</button>
			<button id="btn-GenerateIR" class="btn btn-default">INCONSISTENCY REPORT</button><br><br>-->
			<table class="display" id="tblMain" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th style="">PKID</th>
						<th>Date IN</th>
						<th>Time In</th>
						<th style="">Mode IN</th>
						<th>Date OUT</th>
						<th>Time Out</th>
						<th style="">Mode OUT</th>
						<th>EN</th>
						<th>Name</th>
						<th>Department</th>
						<th>Position</th>
						<th>Section</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if (isset($_POST['txtStartDate'])){
							$empNoQuery = OTKSR::getInstance() -> getRawDataGuard($username,$_POST['txtStartDate'], $_POST['txtEndDate']);
							while($row = mysqli_fetch_array($empNoQuery)) {
								echo '<tr>';						
								if($row['time_in']=="" || $row['time_out']=="" || $row['date_out']=="" || $row['date']==""){
									echo '
									<td style="" class="pkid">'.$row['pkid'].'</td>
									<td class="date" style="color:red;">' . $row['date'] . '</td>
									<td class="time_in" style="color:red;">' . $row['time_in'] . '</td>
									<td class="mode_in" style="">' . $row['mode_in'] . '</td>
									<td class="date_out" style="color:red;">' . $row['date_out'] . '</td>
									<td class="time_out" style="color:red;">' . $row['time_out'] . '</td>
									<td class="mode_out" style="">' . $row['mode_out'] . '</td>
									<td class="empno" style="color:red;">' . $row['empno'] . '</td>
									<td class="SalaryType" style="color:red;">' . $row['LastName'].', '.$row['FirstName']. '</td>
									<td class="Department" style="color:red;">' . $row['Department'] . '</td>
									<td class="Position" style="color:red;">' . $row['Position'] . '</td>
									<td class="Section" style="color:red;">' . $row['Section'] . '</td>
									<td class="status" style="color:red;">' . $row['status'] . '</td>
									
									</font>';
								}
								else{
									echo '
									<td style="">'.$row['pkid'].'</td>
									<td class="date">' . $row['date'] . '</td>
									<td class="time_in">' . $row['time_in'] . '</td>
									<td class="mode_in" style="">' . $row['mode_in'] . '</td>
									<td class="date_out">' . $row['date_out'] . '</td>
									<td class="time_out">' . $row['time_out'] . '</td>
									<td class="mode_out" style="">' . $row['mode_out'] . '</td>
									<td class="empno">' . $row['empno'] . '</td>
									<td class="SalaryType">' . $row['LastName'].', '.$row['FirstName']. '</td>
									<td class="Department">' . $row['Department'] . '</td>
									<td class="Position">' . $row['Position'] . '</td>
									<td class="Section">' . $row['Section'] . '</td>
									<td class="Status">' . $row['status'] . '</td>';
								}
								echo '</tr>';
							}	
						}					
					?>
				</tbody>
			</table>
		</div>
	</div>
<!--MODALS-->

	<!-- Modal Edit
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
						<label style="margin-right:20px;width:160px">EN: </label><input style="width:158px;" type="text" id="txtEmpNo" placeholder="ID"readonly><br>
						<label style="margin-right:20px;width:160px">Date In: </label><input type="date" id="txtDateIn" placeholder="ECode">
						<label style="margin-right:20px;width:160px">Time IN: </label><input style="width:158px;" type="time" id="txtTimeIn" placeholder="Date IN">
						<label style="margin-right:20px;width:160px">Mode In: </label><select style="width:158px;" id="txtModeIn">
						<option value="IN">IN</option>
						<option value="BUS LATE">BUS LATE</option>
						</select><br>
						<label style="margin-right:20px;width:160px">Date Out: </label><input type="date" id="txtDateOut" placeholder="In">
						<label style="margin-right:20px;width:160px">Time OUT: </label><input style="width:158px;" type="time" id="txtTimeOut" placeholder="IN Remarks">
						<label style="margin-right:20px;width:160px">Mode Out: </label><select style="width:158px;" id="txtModeOut">
						<option value="OUT">OUT</option>
						</select><br>
					</div>
				</form>
		  </div>	
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn-Update">Save changes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>-->
	
	<!-- Modal Add Row-->
	<div class="modal fade" id="modalAddRec" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:800px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">ADD NEW RECORD<center><label id="exist" style="color:red;">RECORD ALREADY EXIST!</label></center></h4>
		  </div>
		  <div class="modal-body">
			<div class="form-group">
				<label style="margin-right:20px;width:160px">EN: </label><input style="width:168px;" type="text" id="txtEmpNoA" name="txtEmpNoA" placeholder="Employee Number" onchange="loadEmpInfo(this.value)">
				<label style="margin-right:20px;width:160px">ECode: </label><input style="width:168px;" type="text" id="txtECodeA" name="txtECodeA" placeholder="ECode" readonly>
				<label style="margin-right:20px;width:160px">Name: </label><input style="width:168px;" type="text" id="txtNameA" name="txtNameA" placeholder="Name" readonly>
				<label style="margin-right:20px;width:160px">Department: </label><input style="width:168px;" type="text" id="txtDepartmentA" name="txtDepartmentA" placeholder="Department" readonly>
				<label style="margin-right:20px;width:160px">Position: </label><input style="width:168px;" type="text" id="txtPositionA" name="txtPositionA" placeholder="Postion" readonly>
				<label style="margin-right:20px;width:160px">Section: </label><input style="width:168px;" type="text" id="txtSectionA" name="txtSectionA" placeholder="Section" readonly>
				<label style="margin-right:20px;width:160px">Date In: </label><input style="width:168px;" type="date" id="txtDateInA" name="txtDateInA" placeholder="">
				<label style="margin-right:20px;width:160px">Time IN: </label><input style="width:168px;" type="time" id="txtTimeInA" name="txtTimeInA" placeholder="">
				<label style="margin-right:20px;width:160px">Mode In: </label><select style="width:168px;" id="txtModeInA">
					<option value="IN">IN</option>
					<option value="BUS LATE">BUS LATE</option>
					</select><br>
				<label style="margin-right:20px;width:160px">Date Out: </label><input style="width:168px;" type="date" id="txtDateOutA" name="txtDateOutA" placeholder="">
				<label style="margin-right:20px;width:160px">Time OUT: </label><input style="width:168px;" type="time" id="txtTimeOutA" name="txtTimeOutA" placeholder="">
				<label style="margin-right:20px;width:160px">Mode Out: </label><select style="width:168px;" id="txtModeOutA">
					<option value="OUT">OUT</option>
					</select><br><br>
				<center><label style="margin-right:20px;width:250px" >SELECT AN APPROVER: </label><br>
				<?php
				
						echo "<div id='loadApprover'>";
						echo '<select id="approver">';
						echo '</select>';
						echo '<div>'
				?></center>
			</div>
				
		  </div>
		  <div class="modal-footer">
					<button class="btn btn-primary" id="btn-AddRecModal">ADD RECORD</button>
				</form>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	

</body>
</html>
<?php
/*
if(strpos("my string 'd", "'") != false) {
	//echo "contains";
	$str = "my string 'd";
	echo str_replace("'", "", $str);
} else {
	echo "none";
}
/*require_once('otksr-oop.php');
$dateFrom = '2014-06-02';
$dateTo = '2014-06-17';
echo "";
$selectEmpInfo = OTKSR::getInstance()->getIndividualTimeCardEmpNo($dateFrom, $dateTo);
while($rowInfo = mysqli_fetch_array($selectEmpInfo)) {
	$empno = $rowInfo['empno'];
	$ln = $rowInfo['LastName'];
	$fn = $rowInfo['FirstName'];
	$mn = $rowInfo['MiddleName'];
	$dept = $rowInfo['Department'];
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
			$timeIn = $rowTimeCard['time_in'];
			$timeOut = $rowTimeCard['time_out'];
		}
		$regOT = $rowTimeCard['ot1'];
		$sundayOT = $rowTimeCard['ot3'] + $rowTimeCard['ot3a'];
		$specialOT = $rowTimeCard['2'] + $rowTimeCard['ot2a'] + $rowTimeCard['5'] + $rowTimeCard['ot5a'];
		$holidayOT = $rowTimeCard['4'] + $rowTimeCard['ot4a'] + $rowTimeCard['6'] + $rowTimeCard['ot6a'];
		$undertime = $rowTimeCard['under_mins'];
		$nightDiff = $rowTimeCard['nightdiff'];
		$absent = $rowTimeCard['absent'];
		$slvl = $rowTimeCard['leavenum'];
	}
}
/*$dateFrom = '2014-06-02';
$dateTo = '2014-06-17';
echo "<table border>";
echo "<th>Emp No</th>";
echo "<th>Salary Type</th>";
echo "<th>Absent</th>";
echo "<th>Undertime</th>";
echo "<th>NightDiff</th>";
echo "<th>SL</th>";
echo "<th>VL</th>";
echo "<th>SD</th>";
echo "<th>OT1</th>";
echo "<th>OT2</th>";
echo "<th>OT2a</th>";
echo "<th>OT3</th>";
echo "<th>OT3a</th>";
echo "<th>OT4</th>";
echo "<th>OT4a</th>";
echo "<th>OT5</th>";
echo "<th>OT5a</th>";
echo "<th>OT6</th>";
echo "<th>OT6a</th>";
$selectEOMEmpNo = OTKSR::getInstance()->getEOMEmpNo($dateFrom, $dateTo);
while($rowEN = mysqli_fetch_array($selectEOMEmpNo)) {
	//echo $rowEN['empno']."<br>";
	//$selectEOMData = OTKSR::getInstance() -> getEOMAbsent($rowEN['empno'], '2014-06-09', '2014-06-17');
	echo "<tr>";
	$selectEOMAbsent = OTKSR::getInstance() -> getEOMAbsent($rowEN['empno'], $dateFrom, $dateTo);
	while($rowAbsent = mysqli_fetch_array($selectEOMAbsent)) {
		$absent += $rowAbsent['absent'];
	}
	$selectEOMUndertimeLate = OTKSR::getInstance() -> getEOMUnderTimeLate($rowEN['empno'], $dateFrom, $dateTo);
	while($rowUndertimeLate = mysqli_fetch_array($selectEOMUndertimeLate)) {
		$undertime += $rowUndertimeLate['under_mins'] + $rowUndertimeLate['late_mins'];
	}
	$selectEOMNightDiff = OTKSR::getInstance() -> getEOMNightDiff($rowEN['empno'], $dateFrom, $dateTo);
	while($rowNightDiff = mysqli_fetch_array($selectEOMNightDiff)) {
		$nightDiff += $rowNightDiff['nightdiff'];
	}
	$selectEOMSL = OTKSR::getInstance() -> getEOMSL($rowEN['empno'], $dateFrom, $dateTo);
	if($selectEOMSL != "0") {
		while($rowSL = mysqli_fetch_array($selectEOMSL)) {
			$sl += $rowSL['leavenum'];
		}
	} else {
		$sl = 0;
	}
	$selectEOMVL = OTKSR::getInstance() -> getEOMVL($rowEN['empno'], $dateFrom, $dateTo);
	if($selectEOMVL != "0") {
		while($rowVL = mysqli_fetch_array($selectEOMVL)) {
			$vl += $rowVL['leavenum'];
		}
	} else {
		$vl = 0;
	}
	$selectEOMOT1 = OTKSR::getInstance() -> getEOMOT1($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT1 = mysqli_fetch_array($selectEOMOT1)) {
		$ot1 += $rowOT1['ot1'];
	}
	$selectEOMOT2 = OTKSR::getInstance() -> getEOMOT2($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT2 = mysqli_fetch_array($selectEOMOT2)) {
		$ot2 += $rowOT2['ot2'];
	}
	$selectEOMOT2a = OTKSR::getInstance() -> getEOMOT2a($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT2a = mysqli_fetch_array($selectEOMOT2a)) {
		$ot2a += $rowOT2a['ot2a'];
	}
	$selectEOMOT3 = OTKSR::getInstance() -> getEOMOT3($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT3 = mysqli_fetch_array($selectEOMOT3)) {
		$ot3 += $rowOT3['ot3'];
	}
	$selectEOMOT3a = OTKSR::getInstance() -> getEOMOT3a($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT3a = mysqli_fetch_array($selectEOMOT3a)) {
		$ot3a += $rowOT3a['ot3a'];
	}
	$selectEOMOT4 = OTKSR::getInstance() -> getEOMOT4($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT4 = mysqli_fetch_array($selectEOMOT4)) {
		$ot4 += $rowOT4['ot4'];
	}
	$selectEOMOT4a = OTKSR::getInstance() -> getEOMOT4a($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT4a = mysqli_fetch_array($selectEOMOT4a)) {
		$ot4a += $rowOT['ot4a'];
	}
	$selectEOMOT5 = OTKSR::getInstance() -> getEOMOT5($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT5 = mysqli_fetch_array($selectEOMOT5)) {
		$ot5 += $rowOT5['ot5'];
	}
	$selectEOMOT5a = OTKSR::getInstance() -> getEOMOT5a($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT5a = mysqli_fetch_array($selectEOMOT5a)) {
		$ot5a += $rowOT5a['ot5a'];
	}
	$selectEOMOT6 = OTKSR::getInstance() -> getEOMOT6($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT6 = mysqli_fetch_array($selectEOMOT6)) {
		$ot6 += $rowOT6['ot6'];
	}
	$selectEOMOT6a = OTKSR::getInstance() -> getEOMOT6a($rowEN['empno'], $dateFrom, $dateTo);
	while($rowOT6a = mysqli_fetch_array($selectEOMOT6a)) {
		$ot6a += $rowOT6a['ot6a'];
	}
	echo "<td>".$rowEN['empno']."</td>";
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
	echo "</tr>";
	$absent = 0;
	$undertime = 0;
	$nightDiff = 0;
}
echo "</table>";



//$query = OTKSR::getInstance()->checkNightDiff('7168', '2014-06-09');

$query = OTKSR::getInstance()->checkUnderTime('J005', '2014-06-02') ;

$requiredMins=60;
$minutes=10;
$underMins = $requiredMins - $minutes;
			if($underMins == 1) {
				$underMinsEquivalent = 0.02;
			} else if($underMins == 2) {
				$underMinsEquivalent = 0.03;
			} else if($underMins == 3) {
				$underMinsEquivalent = 0.05;
			} else if($underMins == 4) {
				$underMinsEquivalent = 0.06;
			} else if($underMins == 5) {
				$underMinsEquivalent = 0.08;
			} else if($underMins == 6) {
				$underMinsEquivalent = 0.1;
			} else if($underMins == 7) {
				$underMinsEquivalent = 0.11;
			} else if($underMins == 8) {
				$underMinsEquivalent = 0.13;
			} else if($underMins == 9) {
				$underMinsEquivalent = 0.15;
			} else if($underMins == 10) {
				$underMinsEquivalent = 0.16;
			} else if($underMins == 11) {
				$underMinsEquivalent = 0.18;
			} else if($underMins == 12) {
				$underMinsEquivalent = 0.2;
			} else if($underMins == 13) {
				$underMinsEquivalent = 0.21;
			} else if($underMins == 14) {
				$underMinsEquivalent = 0.23;
			} else if($underMins == 15) {
				$underMinsEquivalent = 0.25;
			} else if($underMins == 16) {
				$underMinsEquivalent = 0.26;
			} else if($underMins == 17) {
				$underMinsEquivalent = 0.28;
			} else if($underMins == 18) {
				$underMinsEquivalent = 0.3;
			} else if($underMins == 19) {
				$underMinsEquivalent = 0.31;
			} else if($underMins == 20) {
				$underMinsEquivalent = 0.33;
			} else if($underMins == 21) {
				$underMinsEquivalent = 0.35;
			} else if($underMins == 22) {
				$underMinsEquivalent = 0.36;
			} else if($underMins == 23) {
				$underMinsEquivalent = 0.38;
			} else if($underMins == 24) {
				$underMinsEquivalent = 0.4;
			} else if($underMins == 25) {
				$underMinsEquivalent = 0.41;
			} else if($underMins == 26) {
				$underMinsEquivalent = 0.43;
			} else if($underMins == 27) {
				$underMinsEquivalent = 0.45;
			} else if($underMins == 28) {
				$underMinsEquivalent = 0.46;
			} else if($underMins == 29) {
				$underMinsEquivalent = 0.48;
			} else if($underMins == 30) {
				$underMinsEquivalent = 0.5;
			} else if($underMins == 31) {
				$underMinsEquivalent = 0.51;
			} else if($underMins == 32) {
				$underMinsEquivalent = 0.53;
			} else if($underMins == 33) {
				$underMinsEquivalent = 0.55;
			} else if($underMins == 34) {
				$underMinsEquivalent = 0.56;
			} else if($underMins == 35) {
				$underMinsEquivalent = 0.58;
			} else if($underMins == 36) {
				$underMinsEquivalent = 0.6;
			} else if($underMins == 37) {
				$underMinsEquivalent = 0.61;
			} else if($underMins == 38) {
				$underMinsEquivalent = 0.63;
			} else if($underMins == 39) {
				$underMinsEquivalent = 0.65;
			} else if($underMins == 40) {
				$underMinsEquivalent = 0.66;
			} else if($underMins == 41) {
				$underMinsEquivalent = 0.68;
			} else if($underMins == 42) {
				$underMinsEquivalent = 0.7;
			} else if($underMins == 43) {
				$underMinsEquivalent = 0.71;
			} else if($underMins == 44) {
				$underMinsEquivalent = 0.73;
			} else if($underMins == 45) {
				$underMinsEquivalent = 0.75;
			} else if($underMins == 46) {
				$underMinsEquivalent = 0.76;
			} else if($underMins == 47) {
				$underMinsEquivalent = 0.78;
			} else if($underMins == 48) {
				$underMinsEquivalent = 0.8;
			} else if($underMins == 49) {
				$underMinsEquivalent = 0.81;
			} else if($underMins == 50) {
				$underMinsEquivalent = 0.83;
			} else if($underMins == 51) {
				$underMinsEquivalent = 0.85;
			} else if($underMins == 52) {
				$underMinsEquivalent = 0.86;
			} else if($underMins == 53) {
				$underMinsEquivalent = 0.88;
			} else if($underMins == 54) {
				$underMinsEquivalent = 0.9;
			} else if($underMins == 55) {
				$underMinsEquivalent = 0.91;
			} else if($underMins == 56) {
				$underMinsEquivalent = 0.93;
			} else if($underMins == 57) {
				$underMinsEquivalent = 0.95;
			} else if($underMins == 58) {
				$underMinsEquivalent = 0.96;
			} else if($underMins == 59) {
				$underMinsEquivalent = 0.98;
			} else if($underMins == 60) {
				$underMinsEquivalent = 1;
			}
			echo $underMinsEquivalent;

$timeOut = "15:40";
$str = explode(":", $timeOut);
echo $hour = $str[0]."<br>";
echo $minutes = $str[1]."<br>";
echo $second = $str[2]."<br>";

if($hour < 16) {
	echo "undertime";
} else {
	echo "no";
}


OTKSR::getInstance()->getEmpNoTkDataHRIS('2014-06-01','2014-06-09');
//$dates = array();
//array_push($dates,OTKSR::getInstance()->getEmpNoTkDataHRIS('2014-06-01','2014-06-09'));
//print_r(array_values($dates))."<br>";
//for($i=0; $i< sizeof($dates);$i++) {
	//			print_r( $dates[$i]);
	//		}
//dateRange();

function dateRange() {
	$first = '2014-06-01';
	$last = '2014-06-09';
	$step = '+1 day';
	$format = 'Y/m/d' ;
	$dates = array();
	$current = strtotime( $first );
	$last = strtotime( $last );

	while( $current <= $last ) {

		echo $dates[] = date( $format, $current ) . "<br>";
		$current = strtotime( $step, $current );
	}
	
	return $dates;
} 

	$jsonarray = array();
	$counter = 0;
	require_once("otksr-oop.php");
	$check = OTKSR::getInstance()->getEmpNoTkDataHRIS('2014-06-01', '2014-06-03');
	$query = OTKSR::getInstance()->getDailyTFM('2014-06-01', '2014-06-03');
	echo "<table border>
	<th>PKID</th>
	<th>EN</th>
	<th>ECode</th>
	<th>Date IN</th>
	<th>In</th>
	<th>IN Remarks</th>
	<th>Date OUT</th>
	<th>Out</th>
	<th>OUT Remarks</th>
	<th>REG_HOURS</th>
	<th>OT HOURS</th>
	<th>Absent</th>
	<th>Undertime</th>
	<th>SL VL CODE</th>
	<th>SL VL NUM</th>
	<th>OT 1</th>
	<th>OT 2</th>
	<th>OT 2A</th>
	<th>OT 3</th>
	<th>OT 3A</th>
	<th>OT 4</th>
	<th>OT 4A</th>
	<th>OT 5</th>
	<th>OT 5A</th>
	<th>OT 6</th>
	<th>OT 6A</th>
	<th>Night Diff</th>
	<th>AUT</th>";
	while($row = mysqli_fetch_array($query)) {
		echo "<tr>";
		echo "<td>".$row['pkid']."</td>";
		echo "<td>".$row['empno']."</td>";
		echo "<td>".$row['SalaryType']."</td>";
		echo "<td>".$row['date']."</td>";
		echo "<td>".$row['time_in']."</td>";
		echo "<td>".$row['mode_in']."</td>";
		echo "<td>".$row['date_out']."</td>";
		echo "<td>".$row['time_out']."</td>";
		echo "<td>".$row['mode_out']."</td>";
		echo "<td>reg hours</td>";
		echo "<td>ot hours</td>";
		echo "<td>".$row['absent']."</td>";
		echo "<td>undertime</td>";
		echo "<td>sl/vl code</td>";
		echo "<td>sl/vl num</td>";
		echo "<td>1</td>";
		echo "<td>2</td>";
		echo "<td>3</td>";
		echo "<td>4</td>";
		echo "<td>5</td>";
		echo "<td>6</td>";
		echo "<td>7</td>";
		echo "<td>8</td>";
		echo "<td>9</td>";
		echo "<td>10</td>";
		echo "<td>11</td>";
		echo "<td>night diff</td>";
		echo "<td>aut</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo json_encode($jsonarray);
*/
?>
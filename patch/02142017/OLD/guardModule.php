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
		var empno = $('#txtEmpNoA').val().toUpperCase();
		$('#txtEmpNoA').val(empno);
		
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
		$.getJSON('jsonloadApproverNames.php', {empno : empno}, function(data) {
			$.each(data, function(index, item) {
				var myOptions = { val1 : item.NAME};

				$.each(myOptions, function(val, text) {
					$('#approver').append(new Option(text, text));
				});
			});
		}); 
	}
	
	//FUNCTION FOR SENDING EMAIL
	function sendEMailApprover(approverName, empName, empNo, dateIn, timeIn, modeIn, dateOut, timeOut, modeOut){ //function for sending email
		var arrayOfStrings = approverName.split(" ");
		var arrLenght = arrayOfStrings.length-1;
		var approverLN = arrayOfStrings[0];
		var approverFN = arrayOfStrings[arrLenght];
		$.post('sendEmail.php', {action : "sendEmailApprover", empName : empName, empNo : empNo, approverLN:approverLN, approverFN: approverFN, dateIn : dateIn, timeIn : timeIn, modeIn: modeIn, dateOut : dateOut, timeOut : timeOut, modeOut : modeOut}, function(result) {});
		
		return false;
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
			var empno = $('#txtEmpNoA').val().toUpperCase(); //get value from textbox id
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
			var approver = $('#approver').val();
			var status = "Pending";			
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
				  "approver" : approver,
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
					window.location.href = 'guardModule.php';
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
			var empname = $(this).find(".empname").html(); //get value from a class
			var date = $(this).find(".date").html(); //get value from a class
			var time_in = $(this).find(".time_in").html(); //get value from a class
			var mode_in = $(this).find(".mode_in").html(); //get value from a class
			var date_out = $(this).find(".date_out").html(); //get value from a class
			var time_out = $(this).find(".time_out").html(); //get value from a class
			var mode_out = $(this).find(".mode_out").html(); //get value from a class
			var approver = $(this).find(".approver").html(); //get value from a class
						
			iPos = oTable.fnGetPosition( this ); //select row in datatables
			x = iPos;
					
			$('#txtEmpName').val(empname); //pass value of empno to the textbox txtEmpNo
			$('#txtEmpNo').val(empno); //pass value of empno to the textbox txtEmpNo
			$('#txtDateIn').val(date);
			$('#txtTimeIn').val(time_in);
			$('#txtModeIn').val(mode_in);
			$('#txtDateOut').val(date_out);
			$('#txtTimeOut').val(time_out);
			$('#txtModeOut').val(mode_out);
			$('#txtApprover').val(approver);
			$('#modalUpdate').data('pkid',pkid).modal('show');
					
		});
							
	
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
		var empname = $('#txtEmpName').val(); //get textbox txtEmpName value and assign it in empname
		var approver = $('#txtApprover').val(); 
		
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
						$(xx).find('.empname').css({"color":"#000000"}); //change color to black
						$(xx).find('.Department').css({"color":"#000000"}); //change color to black
						$(xx).find('.Position').css({"color":"#000000"}); //change color to black
						$(xx).find('.Section').css({"color":"#000000"}); //change color to black
					}										
					oTable.fnPageChange( page );
					sendEMailApprover(approver, empname, empno, date, time_in, mode_in, date_out, time_out, mode_out);
					alert("Attendance successfully save and send to the approver. Thank you!"); 
					window.setTimeout(function() {
						$('#modalUpdate').modal('hide');
					}, 500);
				}
		});
		return false;
	});
	
	$('#btn-GenerateIR').click(function(){
		window.open("inconsistencyrecord.php");
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

			<!-- Collect the nav links, forms, and other content for toggling
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
			<h3 class="panel-title">ADD ATTENDANCE MANUALLY</h3>
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
			
			<button id="btn-AddRec" class="btn btn-default">ADD RECORD</button><br><br>
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
						<th>Approver</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if (isset($_POST['txtStartDate'])){
							$empNoQuery = OTKSR::getInstance() -> getRawDataGuard($username,$_POST['txtStartDate'], $_POST['txtEndDate']);
							while($row = mysqli_fetch_array($empNoQuery)) {
								if($row['status'] == '0') {
									$stat = "Pending";
								} else if($row['status'] == 1) {
									$stat = "Approved";
								} else if($row['status'] == 2) {
									$stat = "Disapproved";
								}
								echo '<tr>';						
								if($row['time_in']=="" || $row['time_out']=="" || $row['date_out']=="" || $row['date']==""){
									echo '
									<td style="" class="pkid">'.$row['pkid'].'</td>
									<td class="date" style="color:red;">' . $row['date'] . '</td>
									<td class="time_in" style="color:red;">' . $row['time_in'] . '</td>
									<td class="mode_in" style="">' . $row['mode_in'] . '</td>
									<td class="date_out" style="color:red;">' . $row['date_out'] . '</td>';
									if($row['time_out'] == ':00') {
										echo '<td class="time_out" style="color:red;"></td>';
									} else {
										echo '<td class="time_out" style="color:red;">' . $row['time_out'] . '</td>';
									}
									echo '<td class="mode_out" style="">' . $row['mode_out'] . '</td>
									<td class="empno" style="color:red;">' . $row['empno'] . '</td>
									<td class="empname" style="color:red;">' . $row['LastName'].', '.$row['FirstName']. '</td>
									<td class="Department" style="color:red;">' . $row['Department'] . '</td>
									<td class="Position" style="color:red;">' . $row['Position'] . '</td>
									<td class="Section" style="color:red;">' . $row['Section'] . '</td>
									<td class="status" style="color:red;">' . $stat . '</td>
									<td class="approver" style="color:red;">' . $row['approver'] . '</td>
									</font>';
								}
								else{
									echo '
									<td style="" class="pkid">'.$row['pkid'].'</td>
									<td class="date">' . $row['date'] . '</td>
									<td class="time_in">' . $row['time_in'] . '</td>
									<td class="mode_in" style="">' . $row['mode_in'] . '</td>
									<td class="date_out">' . $row['date_out'] . '</td>
									<td class="time_out">' . $row['time_out'] . '</td>
									<td class="mode_out" style="">' . $row['mode_out'] . '</td>
									<td class="empno">' . $row['empno'] . '</td>
									<td class="empname">' . $row['LastName'].', '.$row['FirstName']. '</td>
									<td class="Department">' . $row['Department'] . '</td>
									<td class="Position">' . $row['Position'] . '</td>
									<td class="Section">' . $row['Section'] . '</td>
									<td class="Status">' . $stat . '</td>
									<td class="approver" >' . $row['approver'] . '</td>';
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

	<!-- Modal Edit-->
	<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:800px">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">UPDATE LOG OUT</h4>
		  </div>
		  <div class="modal-body">
				<form class="form-inline" role="form">
					<div class="form-group">
						<label style="margin-right:20px;width:160px">EN: </label><input style="width:158px;" type="text" id="txtEmpNo" placeholder="ID" readonly><input style="display:none;width:158px;" type="text" id="txtEmpName" readonly><input style="display:none;width:158px;" type="text" id="txtApprover" readonly><br>
						<label style="margin-right:20px;width:160px">Date In: </label><input type="date" id="txtDateIn" placeholder="ECode" readonly>
						<label style="margin-right:20px;width:160px">Time IN: </label><input type="time" style="width:158px;" id="txtTimeIn" placeholder="Date IN" readonly>
						<label style="margin-right:20px;width:160px">Mode In: </label><input type="text" style="width:158px;" id="txtModeIn"  readonly><br>
						<label style="margin-right:20px;width:160px">Date Out: </label><input type="date" id="txtDateOut" placeholder="In">
						<label style="margin-right:20px;width:160px">Time OUT: </label><input style="width:158px;" type="time" id="txtTimeOut" placeholder="IN Remarks">
						<label style="margin-right:20px;width:160px">Mode Out: </label><select style="width:158px;" id="txtModeOut">
						<option value="OUT">OUT</option>
						</select><br>
					</div>
				</form>
		  </div>	
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn-Update">Save and Send</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
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
				<label style="margin-right:20px;width:160px">EN: </label><input value="" style="width:168px;" type="text" id="txtEmpNoA" name="txtEmpNoA" placeholder="Employee Number" onchange="loadEmpInfo(this.value)">
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
				<div id="loadApprover">
					<select id="approver">
					</select>
				</div>
				</center>
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
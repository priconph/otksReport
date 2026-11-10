<html>
<head>
<title>DEPARTMENT APPROVER</title>

	<!--SCRIPT and CSS-->
	<?php
		require_once("includes.php");
		require_once("otksr-oop.php");
		session_start();
		//$username = "arlbena";
		if (array_key_exists("username", $_SESSION))
		{
			$username = $_SESSION['username'];
			$getEmpNo = OTKSR::getInstance()->getEmpNo($username); 
			$rowEmpNo = mysqli_fetch_array($getEmpNo);
			$empNo = $rowEmpNo['EmpNo'];
			$getEmpInfo = OTKSR::getInstance()->getRequestorInfo($empNo);
			$rowEmpInfo = mysqli_fetch_array($getEmpInfo); //assign value from a query
			$approverName = $rowEmpInfo['FirstName'] . " " . substr($rowEmpInfo['MiddleName'], 0, 1) . ". " .$rowEmpInfo['LastName'];
		}
		else
		{
			echo "<script>alert('Your session has expired. Please log in again!');</script>";
			echo "<script>setTimeout(function(){window.parent.location.reload()}, 100);</script>";
		}
		
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
				}
		});
		
	}
	
	//Script Variables 
	var username = "<?php echo $username; ?>";
	var x;
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
						"sClass": "pkid", "aTargets": [ 0 ]
					},
					{ 
						"sClass": "mode_in", "aTargets": [ 3 ]
					},
					{ 
						"sClass": "mode_out", "aTargets": [ 6 ]
					}
					]
		});
			
		
		//Function for adding row on table
		$("#btn-AddRecModal").click(function(){  
			var empno = $('#txtEmpNoA').val();
			var ECode = $('#txtECodeA').val();
			var name= $('#txtNameA').val();
			var department = $('#txtDepartmentA').val();
			var position = $('#txtPositionA').val();
			var section = $('#txtSectionA').val();
			var date = 	$('#txtDateInA').val();
			var time_in = $('#txtTimeInA').val();
			var mode_in = $('#txtModeInA').val();
			var date_out = $('#txtDateOutA').val();
			var time_out = $('#txtTimeOutA').val();
			var mode_out = $('#txtModeOutA').val();
								
								
			var data = {
				  "action": "addRawData",
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
						$('#modalAddRec').modal('hide');
				  }else{
						$('#exist').show();
				  }
			  }
			});
			return false;
				
		});
		

		//Function for Deleting Selected Row
		$('#btn-DeleteRow').click( function () {
			if(datedel!=0 || date_outdel!=0 || time_indel!=0 || time_outdel!=0){
				alert("Date In, Date Out, Time In, and Time Out must be deleted before deleting this record!");
			}
			else{
			
				alert("PKID: "+pkiddel+" EMPNO: "+empnodel+" date: "+datedel);
			
				var data={
					"action" : "deleteRawData",
					"pkid" : pkiddel,
					"empno" : empnodel,
					"date" : datedel,	
					"date_out" : date_outdel,		
					"time_in" : time_indel,		
					"time_out" : time_outdel	
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
			}
			//Set variable data for delete
			pkiddel = $(this).find(".pkid").html(); //get value from a class
			empnodel = $(this).find(".empno").html(); //get value from a class
			datedel = $(this).find(".date").html(); //get value from a class
			date_outdel = $(this).find(".date_out").html(); //get value from a class
			time_indel = $(this).find(".time_in").html(); //get value from a class
			time_outdel = $(this).find(".time_out").html(); //get value from a class
			
		});
		
		
		//Get cell individual values and place it in a variable
		$('#tblMain').on("dblclick","tbody tr", function (event) { 
			if ( $(this).hasClass('selected') ) {
					$(this).removeClass('selected');
				}
			else {
					oTable.$('tr.selected').removeClass('selected');
					$(this).addClass('selected');
				}	
		
					x = $(this).index();						
							
			var pkid = $(this).find(".pkid").html(); //get value from a class
			var empno = $(this).find(".empno").html(); //get value from a class
			var date = $(this).find(".date").html(); //get value from a class
			var time_in = $(this).find(".time_in").html(); //get value from a class
			var mode_in = $(this).find(".mode_in").html(); //get value from a class
			var date_out = $(this).find(".date_out").html(); //get value from a class
			var time_out = $(this).find(".time_out").html(); //get value from a class
			var mode_out = $(this).find(".mode_out").html(); //get value from a class
			var status = $(this).find(".status").html(); //get value from a class
			if(status == 'Pending') {
				status = '0';
			} else if(status == 'Approved') {
				status = '1';
			} else if(status == 'Disapproved') {
				status = '2';
			} else if(status == 'Cancelled') {
				status = '3';
			}
			iPos = oTable.fnGetPosition( this );
			x = iPos;
					
			$('#txtEmpNo').val(empno);
			$('#txtDateIn').val(date);
			$('#txtTimeIn').val(time_in);
			$('#txtModeIn').val(mode_in);
			$('#txtDateOut').val(date_out);
			$('#txtTimeOut').val(time_out);
			$('#txtModeOut').val(mode_out);
			$('#statusUpdate').val(status);
			$('#modalUpdate').data('pkid',pkid).modal('show');
					
		});
							
	//handles update here	
	$('#btn-Update').click(function(){
		var pkid = $('#modalUpdate').data('pkid');
		var empno = $('#txtEmpNo').val();
		var date = 	$('#txtDateIn').val();
		var time_in = $('#txtTimeIn').val();
		var mode_in = $('#txtModeIn').val();
		var date_out = $('#txtDateOut').val();
		var time_out = $('#txtTimeOut').val();
		var mode_out = $('#txtModeOut').val();
		var status = $('#statusUpdate').val();
		
		var data={
				"action" : "approveAttendance", 
				"pkid" : pkid,
				"status" : status,
				"username" : '<?php echo $username; ?>',
				"empno" : empno,
				"dateIn" : date
			};
		data = $.param(data);
		$.ajax({
			  type: "POST",
			  dataType: "json",
			  url: "reportHandler.php", //Relative or absolute path to response.php file
			  data: data,
			  success: function(data) {
					window.location.href = "./managerApprover.php";
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
					oTable.fnPageChange( page );
					$('#modalUpdate').modal('hide');
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
});
	</script>

	
</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
	
	<div class="panel panel-primary" id="mainContainer"> <!--TARGET CONTAINER FOR TABLES-->	
		<div class="panel-heading">
			<h3 class="panel-title" style="margin-left:1000px"><b>Department Head</b></h3>
		</div>
		<div class="panel-body">
			<form class="form-inline" role="form" method="POST" action="managerApprover.php">
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
							$empNoQuery = OTKSR::getInstance() -> getRawDataApprover($approverName, $_POST['txtStartDate'], $_POST['txtEndDate']);
							while($row = mysqli_fetch_array($empNoQuery)) {
								if($row['status'] == '0') {
									$stat = "Pending";
								} else if($row['status'] == 1) {
									$stat = "Approved";
								} else if($row['status'] == 2) {
									$stat = "Disapproved";
								} else if($row['status'] == 3) {
									$stat = "Cancelled";
								}
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
									<td class="status" style="color:red;">' . $stat . '</td>
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
									<td class="status">' . $stat . '</td>';
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
			<h4 class="modal-title" id="myModalLabel">Employee Information</h4>
		  </div>
		  <div class="modal-body">
				<form class="form-inline" role="form">
					<div class="form-group">
						<label style="margin-right:20px;width:160px">EN: </label><input type="text" id="txtEmpNo" placeholder="ID" readonly><br>
						<label style="margin-right:20px;width:160px">Date In: </label><input type="text" id="txtDateIn" placeholder="ECode" readonly>
						<label style="margin-right:20px;width:160px">Time IN: </label><input type="text" id="txtTimeIn" placeholder="Date IN" readonly>
						<label style="margin-right:20px;width:160px">Mode In: </label><input type="text" id="txtModeIn" readonly><br>
						<label style="margin-right:20px;width:160px">Date Out: </label><input type="text" id="txtDateOut" placeholder="In" readonly>
						<label style="margin-right:20px;width:160px">Time OUT: </label><input type="text" id="txtTimeOut" placeholder="IN Remarks" readonly>
						<label style="margin-right:20px;width:160px">Mode Out: </label><input type="text" id="txtModeOut" readonly><br>
						<label style="margin-right:15px;width:160px">Status:</label>
						<select id="statusUpdate" style="width:140px;height:25px;">
							<option value="0">Pending</option>
							<option value="1">Approved</option>
							<option value="2">Disapproved</option>
							<option value="3">Cancelled</option>
						</select>
						
					</div>
				</form>
		  </div>	
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btn-Update">Save</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	
	
</body>
</html>
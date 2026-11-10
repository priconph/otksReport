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
	.panel-body{
		overflow:scroll;
	}
	</style>
	
	<script>
	//function for edit
	$(document).ready(function() {
    $('#tblMain').dataTable();
	});
	</script>
	
	
</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
	
	
		<div class="panel-heading">
			<h3 class="panel-title">INCONCISTENCY LOGS</h3>
		</div>
		<div class="panel-body">
			
			<form class="form-inline" role="form">
			  <div class="form-group">
				<label class="sr-only" for="txtStartDate">Start Date</label>
				<?php echo '<input type="date" class="form-control" id="txtStartDate" placeholder="Start Date" value="'.$dateToday.'">'; ?>
			  </div>
			  <div class="form-group">
				<label class="sr-only" for="txtEndDate">End Date</label>
				<?php echo '<input type="date" class="form-control" id="txtEndDate" placeholder="End Date" value="'.$dateToday.'">';?>
			  </div>
			  <button type="submit" class="btn btn-default">SET DATE</button>
			</form>

			<table border="1" class="table" id="tblMain">
				<thead>
					<tr>
						<th>ID</th>
						<th>ECode</th>
						<th>Date IN</th>
						<th>In</th>
						<th>IN Remarks</th>
						<th>Date OUT</th>
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
						<th>AUT</th>
					</tr>
				</thead>
				<tbody>
					<!--<tr ondblclick="editData()"> FOR MODAL UPDATING-->
					<tr ondblclick="editData()">
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
						<td>a</td>
					</tr>
				</tbody>
			</table>
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
<?php
require_once('includes.php');
?>
<head>
<style>
body{
	background-image	:url('');
	background-repeat	:no-repeat;
	background-size		:cover;
}
</style>
</head>
<!--<div class="row">
	<div class="col-sm-4 col-sm-offset-8">
		<label>Employee Number: </label><input type="text" class="form-control" id="txt_empno">
	</div>
</div>-->
<br><br>

<table id="tbl_ot_checker">
	<thead>
		<tr>	
			<th>Empno</th>
			<th>OT Filed</th>
			<th>OT Date</th>
			<th>Filed OT</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>

<script>
	var oTable = $('#tbl_ot_checker').DataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "./server_side_scripts/ot_checker.php?empno="+$('#txt_empno').val()
	});
</script>
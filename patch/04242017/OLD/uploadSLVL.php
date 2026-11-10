<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<head>
<title>OTKS REPORT</title>
<link rel="stylesheet" type="text/css" href="css/otks.css">
<?php
session_start();
$username = $_SESSION['username'];
require_once("includes.php");
require_once('check_login.php');
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

	}
	</style>
	
<script>
function updateSLVL(empno,temp1,temp2,temp3,temp4,cutoff_date){
	//alert(empno + " " + temp1 + " " + temp2 + " " + temp3 + " " + temp4 );
	var username = " <?php echo $username; ?> ";
	var data = {
		"action" : "updateSLVL",
		"empno" : empno,
		"code" : temp1,
		"num_days" : temp2,
		"date" : temp3,
		"paid_leave" : temp4,
		"cutoff_date" : cutoff_date,
		"username" : username
	};
	data = $.param(data);
	$.ajax({
		type: "POST",
		dataType: "json",
		url: "reportHandler.php", //Relative or absolute path to response.php file
		data: data,
		success: function(result) {
		   add_status(result['empno_return'],result['msg']);
		}
	});
	return false;
}

function add_status(empno,status){
	$('#tblMain tbody tr').each(function(){
		var empno_current = $(this).find('td:eq(0)').text();
		empno_current = empno.replace(/ /g,'');
		if(empno == empno_current){
			$(this).find('td:eq(5)').text(status);
		}
	});
}
//function for edit
$(document).ready(function() {
	$('#tblMain tbody tr').each(function(){
		var empno = $(this).find('td:eq(0)').text();
		empno = empno.replace(/ /g,'');
		if(empno == ""){
			$(this).remove();
		}
	});
	$('#tblMain').dataTable();
});


$(document).ready(function(){
	var month = '<?php echo $_GET['month']; ?>';
	if(month == ''){
		$('#frm_upload input').prop('disabled','true');
	}

	$('#set_first_cutoff').click(function(){
		$('#txt_cutoff_date').val(1);
	});

	$('#set_second_cutoff').click(function(){
		$('#txt_cutoff_date').val(2);
	});

	$('#frm_select_cutoff_period').submit(function(e){
		e.preventDefault();
		if($('#txt_cutoff_date').val() == 1){
			var selected_month = $('#txt_month_range').val();
			if(selected_month == ""){
			alert("Please select a month!");
				return false;
			}
			// alert('first cutoff');
			var data = {
				"action"			: "get_first_cut_off",
				"selected_month"	: selected_month
			}
			data = $.param(data);
			$.ajax({
				type		: "post",
				dataType	: "json",
				data		: data,
				url		: "reportHandler.php",
				success	: function(result){
					window.location.href="uploadSLVL.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
				}
			});
		}else{
			var selected_month = $('#txt_month_range').val();
			if(selected_month == ""){
				alert("Please select a month!");
				return false;
			}
			// alert('second cutoff');
			var data = {
				"action"			: "get_second_cut_off",
				"selected_month"	: selected_month
			}
			data = $.param(data);
			$.ajax({
				type		: "post",
				dataType	: "json",
				data		: data,
				url		: "reportHandler.php",
				success	: function(result){
					window.location.href="uploadSLVL.php?txtStartDate="+result['start_date']+"&txtEndDate="+result['end_date']+'&month='+selected_month;
				}
			});
		}
	});
});

$(document).ready(function(){
	var oTable = $('#tbl_leaves').DataTable({
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting"	 : [ [ 1 ,"desc"] ],
		"sAjaxSource": "./server_side_scripts/vw_leaves.php"
	}); 
	$('#btn_uploaded_leaves').click(function(){
		oTable.ajax.reload();
		$('#modal_filed_leaves').modal('show');
	});
	var pkid = 0;
	
	$('#tbl_leaves tbody').on('click','tr .fa-trash', function(){
		pkid = $(this).closest('tr').find('#hd_pkid').val();
		$('#txt_empno_delete').text( $(this).closest('tr').find('td:eq(0)').text() );
		$('#txt_dol_delete').text( $(this).closest('tr').find('td:eq(1)').text() );
		$('#txt_lc_delete').text( $(this).closest('tr').find('td:eq(2)').text() );
		$('#txt_pl_delete').text( $(this).closest('tr').find('td:eq(3)').text() );
		$('#txt_doc_delete').text( $(this).closest('tr').find('td:eq(4)').text() );
		$('#modal_confirm_delete').modal('show');
	});
	
	$('#btn_delete_leave').click(function(){ delete_leave(); });
	
	function delete_leave(){
		var data = {
			"action"		: "delete_leave",
			"pkid"			: pkid
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				oTable.ajax.reload();
				$('#modal_confirm_delete').modal('hide');
			}
		});
	}
	
	$('#btn_delete_leave').click(function(){ delete_leave(); });
	
	$('#tbl_leaves tbody').on('click','tr .fa-edit', function(){
		pkid = $(this).closest('tr').find('#hd_pkid').val();
		$('#txt_empno_edit').val( $(this).closest('tr').find('td:eq(0)').text() );
		$('#txt_dol_edit').val( $(this).closest('tr').find('td:eq(1)').text() );
		$('#txt_lc_edit').val( $(this).closest('tr').find('td:eq(3)').text() );
		$('#txt_pl_edit').val( $(this).closest('tr').find('td:eq(4)').text() );
		$('#txt_doc_edit').val( $(this).closest('tr').find('td:eq(5)').text() );
		$('#modal_edit_leave').modal('show');
		
		switch ( $(this).closest('tr').find('td:eq(2)').text() ){
			case "SL"	: var tol = "1"; break;
			case "VL"	: var tol = "2"; break;
			case "FL"	: var tol = "3"; break;
			case "ML"	: var tol = "4"; break;
			case "BDL"	: var tol = "5"; break;
			case "BL"	: var tol = "6"; break;
		}
		$('#txt_tol_edit').val( tol );
		
	});
	
	$('#btn_edit_leave').click(function(){ edit_leave(); });
	
	function edit_leave(){
		var data = {
			"action"		: "edit_leave_information",
			"pkid"			: pkid,
			"dol"			: $('#txt_dol_edit').val(),
			"tol"			: $('#txt_tol_edit').val(),
			"lc"			: $('#txt_lc_edit').val(),
			"pl"			: $('#txt_pl_edit').val(),
			"cod"			: $('#txt_doc_edit').val()
		}
		data = $.param(data);
		$.ajax({
			type	: "post",
			dataType: "json",
			data	: data,
			url		: "reportHandler.php",
			success	: function(result){
				// alert(result['msg']);
				// $('body').append(result['msg']);
				oTable.ajax.reload();
				$('#modal_edit_leave').modal('hide');
			}
		});
	}

	/* New Script for ELFS upload */
	var handler = 'reportHandler.php';
	var dt_tbl_elfs = '';

	get_elfs_data();


	function get_elfs_data(){
		$.ajax({
			type 	: "POST",
			dataType: "JSON",
			data   	: "action=get_elfs_data",
			url 	: handler,
			success : function(result){
				console.log(result);
				if(dt_tbl_elfs != ""){
					dt_tbl_elfs.destroy();
					dt_tbl_elfs = '';
					$('#tbl_leaves_elfs tbody').empty();
				}
				$('#tbl_leaves_elfs tbody').append(result['html']);
				dt_tbl_elfs = $('#tbl_leaves_elfs').DataTable({ aaSorting : [] });
			},error : function(result){
				console.log(result);
			}
		});
	}

	$('#btn_save_elfs_data_otks').click(function(){
		$('.btn').prop('disabled',true);
		save_elfs_data_otks();
	});

	function save_elfs_data_otks(){
		var username = " <?php echo $username; ?> ";
		$.ajax({
			type 	: "POST",
			dataType: "JSON",
			data   	: "action=save_elfs_data_otks&username="+username,
			url 	: handler,
			success : function(result){
				console.log(result);
				get_elfs_data();
				$('.btn').prop('disabled',false);
			},error : function(result){
				console.log(result);
			}
		});
	}
	
});

</script>


</head>
<?php
$dateToday = date("Y-m-d"); //date Today Formatting
?>
<body>
	<div class="modal fade" tabindex="-1" role="dialog" id="modal_filed_leaves">
	  <div class="modal-dialog" style="width:800px;">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Uploaded Leaves</h4>
		  </div>
		  <div class="modal-body">
			<table id="tbl_leaves">
				<thead>
					<tr>
						<th>Empno</th>
						<th>Date of Leave</th>
						<th>Leave Type</th>
						<th>Leave Count</th>
						<th>Paid Leave</th>
						<th>Date of Cutoff</th>
						<th style="width:10%;">Edit / Delete</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<!--<button type="button" class="btn btn-primary">Save changes</button>-->
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="modal fade" tabindex="-1" role="dialog" id="modal_confirm_delete">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Delete</h4>
		  </div>
		  <div class="modal-body">
			<p><b> Do you wish to delete this record?</p>
			Record Details: </b></p>
			<table class="table">
				<tr><td>Empno: </td><td><label id="txt_empno_delete"></label></td></tr>
				<tr><td>Date of Leave: </td><td><label id="txt_dol_delete"></label></td></tr>
				<tr><td>Leave Count: </td><td><label id="txt_lc_delete"></label></td></tr>
				<tr><td>Paid Leave: </td><td><label id="txt_pl_delete"></label></td></tr>
				<tr><td>Date of cutoff: </td><td><label id="txt_doc_delete"></label></td></tr>
			</table>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
			<button type="button" class="btn btn-danger" id="btn_delete_leave"><span class="fa fa-trash"></span> Delete</button>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div class="modal fade" tabindex="-1" role="dialog" id="modal_edit_leave">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Edit</h4>
		  </div>
		  <div class="modal-body">
			<table class="table">
				<tr><td>Empno: </td><td><input type="text" class="form-control" id="txt_empno_edit" disabled></td></tr>
				<tr><td>Date of Leave: </td><td><input type="date" class="form-control" id="txt_dol_edit"></td></tr>
				<tr>
					<td>Type of Leave: </td>
					<td>
						<select class="form-control" id="txt_tol_edit">
							<option value="1">SL</option>
							<option value="2">VL</option>
							<option value="3">FL</option>
							<option value="4">ML</option>
							<option value="5">BDL</option>
							<option value="6">BL</option>
						</select>
					</td>
				</tr>
				<tr><td>Leave Count: </td><td><input type="text" class="form-control" id="txt_lc_edit"></td></tr>
				<tr><td>Paid Leave: </td><td><input type="text" class="form-control" id="txt_pl_edit"></td></tr>
				<tr><td>Date of cutoff: </td><td><input type="date" class="form-control" id="txt_doc_edit"></td></tr>
			</table>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-success" id="btn_edit_leave"><span class="fa fa-trash"></span> Edit</button>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
		<nav class="navbar navbar-default" role="navigation" style="">
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
				<li class=""><a href="index.php" id="loadRawData"class="fa fa-tasks fa-lg"> RAW DATA</a></li>
				<li class=""><a href="dailyTFM.php" id="loadDailyTFM" class="fa fa-tasks fa-lg"> TFM</a></li>
				<li class="active"><a href="uploadSLVL.php" id="loadUploadSLVL" class="fa fa-upload fa-lg"> SL/VL</a></li>
				<li class=""><a href="eom.php" id="loadEom" class="fa fa-tasks fa-lg"> EOM</a></li>
				<li class=""><a href="individualTimeCard.php" id="loadIndividualTimecard" class="fa fa-user fa-lg"> Timecard</a></li>
				<li class=""><a href="passRequests.php" id="" class="fa fa-car fa-lg"> Pass Requests</a></li>
				<li class=""><a href="settings.php" id="loadIndividualTimecard" class="fa fa-gears fa-lg"> Settings</a></li>
			  </ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
		</nav>
	<div class="row top-container">
		<div class="col-sm-12">
			<div class="panel panel-primary" id="mainContainer">
				<div class="panel-heading">
					<h3 class="panel-title"><b>UPLOAD SL / VL</b></h3>
				</div>
				<div class="panel-body">
					<form id="frm_select_cutoff_period" method="post">
						<div class="row">
							<?php
								if(isset($_GET['month'])){
									$month = $_GET['month'];
									$start_date = $_GET['txtStartDate'];
									$end_date = $_GET['txtStartDate'];
								}else{
									$month = '';
									$start_date = '';
									$end_date = '';
								}
							 ?>
							<div class="col-sm-2" style="padding-top:3px;">
								<label style="font-size:20px;">Cut-off Period: </label>
							</div>
							<div class="col-sm-3">
								 <input type="month" pattern="[0-9]*" class="form-control" id="txt_month_range" value="<?php echo $month; ?>" required>
							</div>
							<div class="col-sm-7">
								 <input type="hidden" id="txt_cutoff_date"> 
								 <button type="submit" class="btn btn-primary" id="set_first_cutoff"><span class="fa fa-calendar-check-o"></span> 1ST CUT OFF</button>
								 <button type="submit" class="btn btn-primary" id="set_second_cutoff"><span class="fa fa-calendar-check-o"></span> 2ND CUT OFF</button>
								 <button type="button" class="btn btn-success" id="btn_uploaded_leaves"><span class="fa fa-file-o"></span> FILED LEAVES</button>
							</div>
						</div>
					</form>
					
					<!-- Tab Menu -->
					<div>
					  <!-- Nav tabs -->
					  <ul class="nav nav-tabs" role="tablist">
					    <li role="presentation" class="active"><a href="#elfs" aria-controls="elfs" role="tab" data-toggle="tab">Employee Leave File System (ELFS)</a></li>
					    <li role="presentation"><a href="#nautilus" aria-controls="nautilus" role="tab" data-toggle="tab">Nautilus</a></li>
					  </ul>

					  	<!-- Tab panes -->
					  	<div class="tab-content">
						    <div role="tabpanel" class="tab-pane active" id="elfs">
						    	<div class="row">
						    		<div class="col-sm-12">
						    			<div class="panel panel-primary">
								    		<div class="panel-heading"><span class="fa fa-upload"></span> Upload Leaves from ELMS</div>
								    		<div class="panel-body">
												<form id="frm_upload_slvl_elfs">
										    		<div class="row">
										    			<div class="col-sm-6 col-sm-offset-6">
										    				<button class="btn btn-primary fa pull-right" type="button" id="btn_save_elfs_data_otks"> Save Leave to OTKS</button><br /><br />
										    			</div>
										    		</div>
										    		<div class="row">
										    			<div class="col-sm-12">
									    					<table class='table table-condensed table-bordered' id='tbl_leaves_elfs'>
																<thead>
																	<tr>
																		<th>EmpNo</th>
																		<th>Code</th>
																		<th>No. of Days Leave</th>
																		<th>Date</th>
																		<th>Paid Leave</th>
																		<th>Cutoff Date</th>
																		<th>Status</th>
																	</tr>
																</thead>
																<tbody>
																</tbody>
															</table>
										    			</div>
										    		</div>
										    	</form>
								    		</div>
								    	</div>
						    		</div>
						    	</div>
						    	
							   </div><!-- div end elfs -->
						    <div role="tabpanel" class="tab-pane" id="nautilus">
							    <br />
							    <?php
								echo '<form action="uploadSLVL.php?txtStartDate='.$start_date.'&txtEndDate='.$end_date.'&month='.$month.'" method="post" enctype="multipart/form-data" id="frm_upload">';
								?>					
								
								<div class="row">
									<div class="col-sm-3">
										<label for="file">CHOOSE A TEXT FILE TO UPLOAD:</label>
										<input class="btn btn-default" type="file" name="file" id="file">
									</div>
									<div class="col-sm-4">
										<br><input class="btn btn-success" type="submit" name="submit" value="Submit">
									</div>
								</div>
								</form>

								<?php
									if(!isset($_GET['txtStartDate'])){
										echo "Please select a cutoff period.";
										exit;
									}
									$day_of_month = explode('-',$_GET['txtStartDate']);
									$day_of_month = $day_of_month[2];
									if(isset($_FILES['file']['name'])){
										
										$file = $_FILES['file']['name'];
										$fileCheck = explode(".", $file);
										if($fileCheck[1]!='txt'){
											echo 'ERROR: TEXT FILES ONLY!';
										}
										else{
											if ($_FILES["file"]["error"] > 0){ echo "Return Code: " . $_FILES["file"]["error"] . "<br>"; }
											else
											{
												if (file_exists($_FILES["file"]["name"])){}
												else{}
											}

											if ($_FILES["file"]["error"] > 0){}
											else
											{
												if (file_exists($_FILES["file"]["name"])){}
												else{}
											}
												
											echo "File uploaded: ".$_FILES["file"]["name"];
											$fileName = $_FILES["file"]["tmp_name"];
											$fd = fopen($fileName, "r");
											$contents = fread($fd, filesize($fileName));
											fclose($fd);
											$delimiter1 = ";";
											$delimiter2 = "\n";
											$splitContents1 = explode($delimiter2, $contents);
											$splitContents2 = explode($delimiter1, $contents);
											$counter = "";
											
											$empno = "";
											$ctr=0;
											echo "<table class='table' border='1' id='tblMain'>";
											echo "	<thead>";
											echo "		<tr>";
											echo "			<th>EmpNo</th>";
											echo "			<th> Code </th>";
											echo "			<th> No. of Days Leave </th>";
											echo "			<th> Date </th>";
											echo "			<th> Paid Leave </th>";
											echo "			<th> Status </th>";
											echo "		</tr>";
											echo "	</thead>";
											echo "	<tbody>";
											foreach($splitContents1 as $perEmployee) {
											   echo "<tr>";
												foreach(explode($delimiter1, $perEmployee) as $details) {
													if($counter == 0) {
														$empno = $details;
														$slvl[$ctr] = $details;
														$ctr++;
														echo "<td>$details </td>";
														$counter++;
													} else if($counter == 1) {
														$temp1 = $details;
														$slvl[$ctr] = $details;
														$ctr++;
														echo "<td>$details </td>";
														$counter++;
													} else if($counter == 2) {
														$temp2 = $details;
														$slvl[$ctr] = $details;
														$ctr++;
														echo "<td>$details </td>";
														$counter++;
													} else if($counter == 3) {
														$temp3 = $details;
														$temp3 = date('Y-m-d',strtotime($temp3));
														$slvl[$ctr] = $details;
														$ctr++;
														echo "<td>$details </td>";
														$counter++;
													} else if($counter == 4) {
														$temp4 = $details;
														$slvl[$ctr] = $details;
														$ctr++;
														$counter = 0;
														echo "<td>$details </td>";
													}
													
												}
												
												$year_of_cutoff = date( 'Y', strtotime($temp3) );
												
												if($day_of_month == "15"){
													$new_day = '20';
												}else{
													$new_day = '10';
												}
												
												$cutoff_date = $month.'-'.$new_day;
												echo 	 '<script>
															updateSLVL("'.$empno.'","'.$temp1.'","'.$temp2.'","'.$temp3.'",'.$temp4.',"'.$cutoff_date.'");
														 </script>';
												echo "	<td></td>";
												echo "</tr>";
											}
											echo " </tbody>";
											echo "</table>";
										}		
									}
								?>
					    	</div><!--end div nautilus tab-->
					  	</div>
					</div>

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
	

</body>
</html>


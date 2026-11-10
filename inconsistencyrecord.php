<html>
<?php
require_once('otksr-oop.php');
require_once('includes.php');
session_start();

function header_OTKS(){
	$dateFrom = $_GET['date'];
	$dateTo = $_GET['date_out'];
	echo "<div>";
	echo "OTKS ONLINE REPORT SYSTEM";
	echo "<br>Inconsistency Logs Report - Receiving Sheet";
	echo "<br>";
	echo "</div>";
}

function header_OTKS2(){
	$dateFrom = $_GET['date'];
	$dateTo = $_GET['date_out'];
	echo "<div>";
	echo "OTKS ONLINE REPORT SYSTEM";
	echo "<br>Inconsistency Logs Report";
	echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
	&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
	&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
	&nbsp&nbsp&nbsp".$dateFrom." TO ".$dateTo."</label>";
	echo "<br>";
	echo "</div>";
}

function footer_OTKS(){
	echo "<br><br><div>";
	echo "Prepared By: __________________________________ \t Checked By: __________________________________<br>";
	echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			MIS PERSONNEL &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
			MIS PERSONNEL<br>";
	echo "NOTICE TO RECIPIENTS:";
	echo "<font size='1'>";
	echo "<br>1) PLEASE SUBMIT TO CONCERNED EMPLOYEE FOR CORRECTION. THE EMPLOYEE SHOULD WRITE ON THE MISSING TIME LOG(TIME-IN OR TIME-OUT)";
	echo "<br>2) THE IMMEDIATE SUPERIOR SHOULD SIGN AND PRINT HIS/HER NAME ON THE APPROVAL COLUMN TO VALIDATE THE CORRECTION. MIS WILL NOT CONSIDER UNAPPROVED ITEMS";
	echo "<br>3) THIS REPORT SHOULD BE SENT BACK TO MIS IMMIDIATELY UPON COMPLETION BY THE CONCERNED DEPARTMENT</font>";
	echo "<br>";
	echo "</div>";
}
?>
<title></title>
<head>

</head>
<style>
.printbreak, .printbreak2{
page-break-before: always;
}
@media print{
  body{ background-color:#FFFFFF; background-image:none; color:#000000 }
  #ad{ display:none;}
  #leftbar{ display:none;}
  #contentarea{ width:100%;}
}
</style>

<script type="text/javascript">
            $(document).ready(function (){  

				 $('.printpreview').hide()
                    window.print();
                $('.printpreview').click(function()
                {
                    $('.printpreview').hide()
                    window.print();
                });


            });
    </script>
	
	
<body>
<?php
	
	header_OTKS();
	
	$dateFrom = $_GET['date'];
	$dateTo = $_GET['date_out'];
		
	echo "<table><thead>";
	echo "<th>DEPARTMENT</th>";
	echo "<th>RECEIVED BY</th>";
	echo "<th>TIME AND DATE</th>";
	echo "<th>SIGNATURE</th></thead>";
	
	$query = OTKSR::getInstance() -> getReceivingSheet($dateFrom, $dateTo);
	while($row = mysqli_fetch_array($query)) {
		if($row['time_in'] == "" || $row['time_out'] == "") {
			echo "<tbody><tr>";
			echo "<td>".$row['Department']."</td>";
			echo "<td>___________</td>";
			echo "<td>___________</td>";
			echo "<td>___________</td>";
			echo "</tr></tbody>";
		} 
	}
	
	/*
	foreach(OTKSR::getInstance()->dateRange($dateFrom, $dateTo) as $i=>$i_value) {
		$query = OTKSR::getInstance() -> getReceivingSheet($i_value);
		while($row = mysqli_fetch_array($query)) {
			if($row['time_in'] == "" || $row['time_out'] == "") {
				echo "<tbody><tr>";
				echo "<td>".$row['Department']."</td>";
				echo "<td>___________</td>";
				echo "<td>___________</td>";
				echo "<td>___________</td>";
				echo "</tr></tbody>";
			} 
		}
	}
	*/
	echo "</table>";
	
	

echo '<div class="printbreak"></div>';
	echo '';
	header_OTKS2();
	echo "<table border=1 style='font-size:1em'>";
	echo "<tr><th>Department</th>";
	echo "<th>Section</th>";
	echo "<th>Date</th>";
	echo "<th>EmpNo</th>";
	echo "<th>EmpName</th>";
	echo "<th>Position</th>";
	echo "<th>Time-In</th>";
	echo "<th>Time-Out</th>";
	echo "<th>Supervisor</th></tr>";
	$i=0;
	$query = OTKSR::getInstance() -> getInconsistencyRecord($dateFrom, $dateTo);
	while($row = mysqli_fetch_array($query)) {
		if($row['time_in'] == "" || $row['time_out'] == "") {
			if($row['time_in'] == "do" || $row['time_in'] == "SL" || $row['time_in'] == "VL" || $row['time_in'] == "FL" || $row['time_in'] == "ML" || $row['time_in'] == "BDL" || $row['time_in'] == "BRL" || $row['time_in'] == "holiday" || $row['time_in'] == "yec"){
				continue;
			}
			
			if( $row['time_in'] == '' && $row['time_out'] == '' ){
				continue;
			}
			
			if($row['date'] =='') {
				$date = $row['date_out'];
			} else {
				$date = $row['date'];
			}
						
			if(!isset($department) || $department==$row['Department']){

			echo "<tr>";
			echo "<td>".$row['Department']."</td>";
			$department = $row['Department'];
			echo "<td>".$row['Section']."</td>";
			echo "<td>".$date."</td>";
			echo "<td>".$row['empno']."</td>";
			echo "<td>".$row['LastName'].", ".$row['FirstName']." " .$row['MiddleName']."</td>";
			echo "<td>".$row['Position']."</td>";
			echo "<td>".$row['time_in']."</td>";
			echo "<td>".$row['time_out']."</td>";
			echo "<td></td>";
			echo "</tr>";
			$department = $row['Department'];
					
			}else{
			echo '</table>';
			footer_OTKS();
			echo '<div class="printbreak"></div>';
			header_OTKS2();

			echo '<table border=1 style="font-size:1em">';
			echo "<tr><th>Department</th>";
			echo "<th>Section</th>";
			echo "<th>Date</th>";
			echo "<th>EmpNo</th>";
			echo "<th>EmpName</th>";
			echo "<th>Position</th>";
			echo "<th>Time-In</th>";
			echo "<th>Time-Out</th>";
			echo "<th>Supervisor</th></tr>";
			echo "<tr>";
			echo "<td>".$row['Department']."</td>";
			echo "<td>".$row['Section']."</td>";
			echo "<td>".$date."</td>";
			echo "<td>".$row['empno']."</td>";
			echo "<td>".$row['LastName'].", ".$row['FirstName']." " .$row['MiddleName']."</td>";
			echo "<td>".$row['Position']."</td>";
			echo "<td>".$row['time_in']."</td>";
			echo "<td>".$row['time_out']."</td>";
			echo "<td></td>";
			echo "</tr>";
			$department = $row['Department'];
			}
		} 
	}
	echo "</table>";

	footer_OTKS();

	echo '<center><button class="printpreview">PRINT THIS REPORT</button></center>';
	
?>
</body>
</html>
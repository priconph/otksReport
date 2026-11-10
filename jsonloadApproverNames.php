<?php
	$empNo = $_GET['empno']; 
	$jsonarray = array();
	$counter = 0;
	#for oop
	require_once("otksr-oop.php");
	$query = OTKSR::getInstance()->getApproverName($empNo);
	while($row = mysqli_fetch_array($query)) {
		$approverName = $row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName'];
		$json = array("NAME" => $approverName);
		$jsonarray[$counter] = $json;
		$counter++;
	} 
	
	echo json_encode($jsonarray);
?>
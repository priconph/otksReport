<?php
require_once("includes.php");
require_once("otksr-oop.php");
$dateToday = date("Y-m-d");
$username= "arlenli";
?>
<center>
<div class="panel panel-success" id="mainContainer" style="width:80%">
	<div class="panel-heading">
		<h3 class="panel-title">UPLOAD CSV</h3>
	</div>
	<div class="panel-body">
		<form action="importTFM.php" method="post" enctype="multipart/form-data">
			<table align="center">
				<tr><label for="file">UPLOAD CSV FILE TO UPDATE AND ADD ITEMS:</label></tr>
				<tr><td><input type="file" name="file" id="file"></td></tr>
				<tr><td><input type="submit" name="submit" value="Submit"></td></tr>
			</table>
		</form>
		<a href="./itemList.php"><button class="btn btn-primary">Go Back</button></a><br>
<?php

if(isset($_FILES['file']['name'])){
	$file = $_FILES['file']['name'];
	$fileCheck = explode(".", $file);
	if($fileCheck[1]!='csv'){
		echo 'ERROR: PLEASE MAKE SURE YOU CONVERTED THE EXCEL FILE TO CSV FORMAT.<br>
				How to convert excel file to csv:<br>
				1. Open File <br>
				2. Save File as csv (example: itemlist.csv) then press save.
				<br><img src="./images/csv.png" style="width:1000px">';
	}
	else{
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Error: " . $_FILES["file"]["error"] . "<br>";
		}
		else
		{
			echo "Upload: " . $_FILES["file"]["name"] . "<br>";
			echo "Type: " . $_FILES["file"]["type"] . "<br>";
			echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br>";
			   
			$a=$_FILES["file"]["tmp_name"];
			
			//OSWS::getInstance()->deleteAllRecord();
			 
			$csv_file = $a; 
			 
			if (($getfile = fopen($csv_file, "r")) !== FALSE) {
				$data = fgetcsv($getfile, 1000, ",");
				while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
					$result = $data;
					$str = implode(",", $data);
					$slice = explode(",", $str);
					$col1 = $slice[0];
					$col2 = $slice[1];
					$col3 = $slice[2];
					$col4 = $slice[3];
					$col5 = $slice[4];
					$col6 = $slice[5];
					$col7 = $slice[6];
					$col8 = $slice[7];
					$col9 = $slice[8];
					$col10 = $slice[9];
					$col11 = $slice[10];
					$col12 = $slice[11];
					$col13 = $slice[12];
					$col14 = $slice[13];
					$col15 = $slice[14];
					$col16 = $slice[15];
					$col17 = $slice[16];
					$col18 = $slice[17];
					$col19 = $slice[18];
					$col20 = $slice[19];
					$col21 = $slice[20];
					$col22 = $slice[21];
					$col23 = $slice[22];
					$col24 = $slice[23];
					$col25 = $slice[24];
					$col26 = $slice[25];
					$col27 = $slice[26];
					$col28 = $slice[27];
					echo "insert: ".$col1."/".$col2."/".$col3."/".$col4."/".$col5."/".$col6."/".$col7."/".$col8."/".$col9."/".$col10."/".$col11."/".$col12."/".$col13."/".$col14."/".$col15."/".$col16."/".$col17."/".$col18."/".$col19."/".$col20."/".$col21."/".$col22."/".$col23."/".$col24."/".$col25."/".$col26."/".$col27."/".$col28."<br>";
					//OSWS::getInstance()->addNewItem($col1,$col2,$col3,$col4,$col5,$dateToday,$username);
				}
			}
			echo "<script>alert('Supplies Updated!'); window.location.href='#'; </script>";
			

		}
	}echo "			</div>
				</div>
			</center>";
}
?>
<?php
$conn = mysql_connect('localhost','root','newd3v') or die("cant connect server");
mysql_select_db('db_otks') or die("cant connect db");

$query = 'SELECT * FROM vw_daily_employees;';
$result = mysql_query($query);

while($row = mysql_fetch_array($result)){
	$query = "UPDATE `tkdataraw` SET `time_in` = '', `absent` = '1' WHERE `empno` = '".$row['empno']."' AND `time_in` = 'holiday' AND `date` = '2016-08-05'; "."<br>";
	echo $query;
}
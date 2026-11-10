<?php
$conn = mysql_connect('localhost','root','newd3v') or die("cant connect server");
mysql_select_db('db_otks') or die("cant connect db");

$query = "SELECT `pkid`,`empno` FROM `tkdataraw` WHERE `time_in` > '11:00:00' AND `time_in` < '16:30' AND (`date` BETWEEN '2016-07-30' AND '2016-08-14') AND `mode_in` = 'IN';";
$result = mysql_query($query);

while($row = mysql_fetch_array($result)){
	$query = "UPDATE `tkdataraw` SET `late_mins` = '240', `late_conv` = '4' WHERE `pkid` = '".$row['pkid']."' AND `empno` = '".$row['empno']."';"."<br>";
	echo $query;
}
/* late */
/* SELECT * FROM `tkdataraw` WHERE time_in > '07:31:00' AND `time_in` < '11:00:00' AND (`date`  BETWEEN '2016-07-30' AND '2016-08-14') AND `mode_in` = 'IN' AND `late_mins` = 0; */
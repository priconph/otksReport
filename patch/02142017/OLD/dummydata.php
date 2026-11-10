<?php
$server = "localhost";
$user = "root";
$pass = "newd3v";
$database = "db_otks";
$con = "";
$con = mysql_connect($server,$user,$pass) or die ("error connecting to sever!");
mysql_select_db($database,$con) or die("error connecting to database!");

?>
<center>
OTKS DUMMY RECORD GENERATOR <br>(JUNE 01, 2014 TO JUNE 09, 2014) <br><br>
<form method= "post" action="dummydata.php">
ENTER EMPNUM:<input type="text" name="empnum">
<input type="submit">
</form>

DELETE ALL DUMMY RECORDS
<form method= "post" action="dummydata.php">
ENTER EMPNUM:<input type="text" name="empnumdel">
<input type="submit">
</form>

<?php
if(isset($_POST['empnumdel']))
{
$query = 'delete from tkdataraw where pkid="0" and empno="'.$_POST['empnumdel'].'"';
echo "Dummy Data with the employee number of ".$_POST['empnumdel']." has been deleted!";
 mysql_query($query);
}

if(isset($_POST['empnum'])){
$ctr=0;
	for($x=9;$x>0;$x--){
		if($ctr==0){
			$query = "insert into tkdataraw (pkid,lastupdate,empno,date,time_in,mode_in,date_out,time_out,absent,under_mins)
			 values ('0','2014-06-".$x."','".$_POST['empnum']."','2014-06-0".$x."','7:30','IN','2014-06-".$x."','16:30','0','1'))";
			 echo $query."<br>";
			 mysql_query($query);
			 $ctr=1;
		 }
		 else{
			 $query = "insert into tkdataraw (pkid,lastupdate,empno,date,time_in,mode_in,date_out,time_out,absent,under_mins)
			 values ('0','2014-06-".$x."','".$_POST['empnum']."','2014-06-0".$x."','7:30','IN','2014-06-".$x."','16:30','0','0')";
			 echo $query."<br>";
			 mysql_query($query);
			 $ctr=0;
		 }
	}

}


?>
</center>
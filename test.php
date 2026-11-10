<?php
$date = '2016-04-16';
$validate_date = date("F-d-Y",strtotime($date));
$validate_date = explode("-",$validate_date);

$first_sat = date( "Y-m-d" , strtotime('first saturday of '.$validate_date[0]));
$third_sat = date( "Y-m-d", strtotime('third saturday of '.$validate_date[0]));
$fifth_sat = date( "Y-m-d", strtotime('fifth saturday of '.$validate_date[0]));

echo $third_sat."<br>"; 

if($date == $first_sat || $date == $third_sat || $date == $fifth_sat){
	echo '0 this is a working sat';
}else{
	echo '1 non working saturday';
}


 $now = strtotime('2016-01-20'); // or your date as well
 $your_date = strtotime('2016-01-28');
 $datediff = $now - $your_date;
 echo floor($datediff/(60*60*24));
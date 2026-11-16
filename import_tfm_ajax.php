<?php

$array_tfm = $_POST['array_tbl_tfm'];
$return = array();

$conn = mysql_connect('localhost','root','sys!w@nDb123') or die( mysql_error() );
mysql_select_db('db_otks_mj',$conn) or die( mysql_error() );

$date_today = date('Y-m-d H:i:s');
for($x=0;$x<count($array_tfm['idnum']);$x++){
	$sl = 0;
	$vl = 0;
	if( $array_tfm['idnum'][$x] == 'SL' ){
		$leavecode = '1';
		$sl = '1';
	}else ( $array_tfm['idnum'][$x] == 'VL' ){
		$leavecode = '1';
		$vl = '1';
	}
	$query = "INSERT INTO 
				`tkdataraw` (
					`empno`,
					`lastupdate`,
					`date`,
					`time_in`,
					`mode_in`,
					`date_out`,
					`time_out`,
					`mode_out`,
					`sched_in`,
					`sched_out`,
					`reg_hours`,
					`late_mins`,
					`under_mins`,
					`ot_credit`,
					`ot_filed`,
					`ot_night`,
					`day_count`,
					`absent`,
					`linked`,
					`link_id`,
					`leavecode`,
					`leavenum`,
					`nightdiff`,
					`sl`,
					`vl`,
					`ot1`,
					`ot2`,
					`ot2a`,
					`ot3`,
					`ot3a`,
					`ot4`,
					`ot4a`,
					`ot5`,
					`ot5a`,
					`ot6`,
					`ot6a`,
					`aut`,
					`status`,
					`approver`,
					`username`
				) 
			  VALUES (
					'".$array_tfm['idnum'][$x]."', 
					'".$date_today."',
					'".$array_tfm['c_date'][$x]."'
					'".$array_tfm['t_in'][$x]."',
					'IN',
					'".$array_tfm['t_in'][$x]."',
					'".$array_tfm['c_date'][$x]."',
					'".$array_tfm['t_out'][$x]."',
					'OUT',
					'".$array_tfm['t_out'][$x]."',
					'',
					'',
					'',
					'".$array_tfm['late_unt'][$x]."',					
					'0',
					'0',
					'0',
					'0',
					'0',
					'".$array_tfm['absent'][$x]."',
					'',
					'',
					'".$leavecode."',
					'".$array_tfm['slvl_num'][$x]."',
					'".$array_tfm['n_diff'][$x]."',
					'".$sl."',
					'".$vl."',
					'".$array_tfm['ot_1'][$x]."',
					'".$array_tfm['ot_2'][$x]."',
					'".$array_tfm['ot_2_a'][$x]."',
					'".$array_tfm['ot_3'][$x]."',
					'".$array_tfm['ot_3_a'][$x]."',
					'".$array_tfm['ot_4'][$x]."',
					'".$array_tfm['ot_4_a'][$x]."',
					'".$array_tfm['ot_5'][$x]."',
					'".$array_tfm['ot_5_a'][$x]."',
					'".$array_tfm['ot_6'][$x]."',
					'".$array_tfm['ot_6_a'][$x]."',
					`FALSE`,
					`1`,
					`otks`,
					`otks`
				);";
	mysql_query($query);
}

$return['msg'] = 'done';
// $return['msg'] = count($array_tfm['idnum']);
echo json_encode($return);
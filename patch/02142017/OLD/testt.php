
<?php

if(isset($_POST)){
	$html = '';
	$chk_sample = $_POST['chk_sample'];
	for($x=0;$x<count($chk_sample);$x++){
		$html .= "Index [". $x ."] ".$chk_sample[$x]."\n";
	}
	$return = array();
	$return['html'] = $html;
	echo json_encode($return);
}


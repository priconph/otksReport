	<?php
		/* check for active username and IP Address */
		$user_ip = $_SERVER['REMOTE_ADDR'];
		require_once('otksr-oop.php');
		$valid_ip = OTKSR::getInstance()->get_valid_ip($user_ip);
		if(!$valid_ip){
			echo "WARNING YOU ARE NOT ALLOWED TO VIEW THIS PAGE!";
			header("Location: email.php?ip_address=".$user_ip); /* Redirect browser */
			exit;
		}
		session_start();
		if(!isset($_SESSION['username'])){
			
			// echo "WARNING YOU ARE NOT ALLOWED TO VIEW THIS PAGE!";
			// header("Location: email.php?ip_address=".$user_ip); /* Redirect browser */
			exit;
		}
	?>	
	<!--JQUERY SCRIPT-->
	<script src="../media/js/jquery.js"></script>
	<script src="../media/js/jquery-ui.js"></script> 
	<!--<script type="text/javascript" language="javascript" src="./media/js/jquery-1.10.2.min.js"></script>-->
	<!--CSS-->
    <!--<link href="./media/css/bootstrap.css" rel="stylesheet">-->
	<link href="../media/css/bootstrap-yeti.min.css" rel="stylesheet">
	<script type="text/javascript" language="javascript" src="../media/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" href="../media/css/jquery.dataTables.css">
	<!--<link rel="stylesheet" href="./media/css/normalization.css">-->
	<link rel="stylesheet" href="./css/otks_report.css">
	<!--SOME SCRIPT-->
	<script type="text/javascript" language="javascript" src="../media/js/modal.js"></script>
	<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.min.js"></script>

	
	<!--font awesome -->
	<link href="../media/font_awesome/css/font-awesome.min.css" rel="stylesheet">
	
	<!--OTKS STYLESHEET-->
	<link rel="stylesheet" type="text/css" href="css/otks.css">

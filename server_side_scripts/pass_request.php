<?php
require_once(__DIR__ . '/ssp_pdo.php');

$extraConds = ["`ob_status` = '1'"];
$extraParams = [];

if (isset($_GET['start_date'])) {
	$extraConds[] = '(`ob_date` BETWEEN ? AND ?)';
	$extraParams[] = (string)$_GET['start_date'];
	$extraParams[] = (string)($_GET['end_date'] ?? '');
}

if (isset($_GET['request_type']) && $_GET['request_type'] != '') {
	$extraConds[] = '`ob_purpose` = ?';
	$extraParams[] = (string)$_GET['request_type'];
}

$result = ssp_run('tkob', [
	'ob_purpose', 'ob_date', 'empno', 'emp_name', 'ob_time_out', 'ob_time_return',
], 'pkid', null, $extraConds, $extraParams);

require_once('../reportHandler.php');

function ob_purpose($str)
{
	$purpose = 'not defined[' . $str . ']';
	if ($str == 0) {
		$purpose = 'Undertime';
	}
	if ($str == 1) {
		$purpose = 'OB';
	}
	return $purpose;
}

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$aaData[] = [
		'<center>' . ob_purpose($aRow['ob_purpose']) . '</center>',
		'<center>' . $aRow['ob_date'] . '</center>',
		'<center>' . $aRow['empno'] . '</center>',
		get_empname($aRow['empno']),
		'<center>' . $aRow['ob_time_out'] . '</center>',
		'<center>' . $aRow['ob_time_return'] . '</center>',
	];
}

ssp_output($result, $aaData);

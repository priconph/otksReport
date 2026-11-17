<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('tkday_off', ['empno', 'saturday', 'pkid'], 'pkid');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	if ($aRow['saturday'] == 0) {
		$day_off = '1st and 3rd';
	} else if ($aRow['saturday'] == 1) {
		$day_off = '2nd and 4th';
	} else if ($aRow['saturday'] == 2) {
		$day_off = '2nd and 3rd';
	} else {
		$day_off = 'ERROR';
	}
	$aaData[] = [
		$aRow['empno'],
		$day_off,
		'<button class="fa fa-remove btn btn-danger" data-id="' . $aRow['pkid'] . '"></button>',
	];
}

ssp_output($result, $aaData);

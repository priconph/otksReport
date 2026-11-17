<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('vw_holiday', ['date', 'remarks', 'pkid'], 'pkid');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$aaData[] = [
		$aRow['date'],
		$aRow['remarks'],
		'<button class="fa fa-remove btn btn-danger" value="' . $aRow['pkid'] . '"></button>',
	];
}

ssp_output($result, $aaData);

<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('vw_yec_training', ['empno', 'training_date_end', 'pkid'], 'pkid');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$aaData[] = [
		$aRow['empno'],
		$aRow['training_date_end'],
		'<button class="fa fa-remove btn btn-danger" value="' . $aRow['pkid'] . '"></button>',
	];
}

ssp_output($result, $aaData);

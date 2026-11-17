<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('vw_offset', ['empno', 'date', 'username', 'pkid', 'lastupdate'], 'pkid');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$aaData[] = [
		$aRow['empno'],
		$aRow['date'],
		'<button class="fa fa-remove btn btn-danger" value="' . $aRow['pkid'] . '"></button>',
	];
}

ssp_output($result, $aaData);

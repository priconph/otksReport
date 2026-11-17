<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('vw_leaves', [
	'empno', 'date_of_leave', 'leavecode', 'days', 'days_paid', 'cutoff_date', 'pkid',
], 'pkid');

require_once('../otksr-oop.php');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$aaData[] = [
		"<input type='hidden' id='hd_pkid' value='" . $aRow['pkid'] . "'>" . $aRow['empno'],
		$aRow['date_of_leave'],
		OTKSR::getInstance()->leavecode_convertion($aRow['leavecode']),
		$aRow['days'],
		$aRow['days_paid'],
		$aRow['cutoff_date'],
		"<button class='btn btn-default fa fa-edit' title='edit'></button>
					  <button class='btn btn-danger fa fa-trash' title='delete'></button>",
	];
}

ssp_output($result, $aaData);

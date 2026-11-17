<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('vw_late_ot', [
	'empno', 'ot_date', 'ot_count', 'ot_type', 'ot_date_paid',
	'lastupdate', 'username', 'pkid', 'logdel',
], 'pkid');

$ot_types = [
	'1' => 'Regular',
	'2' => 'Sunday / Special Holiday',
	'3' => 'Sunday',
	'4' => 'Sunday / Legal Holiday',
	'5' => 'Special Non-Working Holiday',
	'6' => 'Legal Sunday',
];

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$ot_type = $ot_types[(string)$aRow['ot_type']] ?? '';

	$ot_date_paid = (string)$aRow['ot_date_paid'];
	$parts = explode('-', $ot_date_paid);
	$days_paid = $parts[2] ?? '';
	switch ($days_paid) {
		case '10': $ot_date_paid = date('M', strtotime($aRow['ot_date_paid'])) . ' 1st cutoff'; break;
		case '20': $ot_date_paid = date('M', strtotime($aRow['ot_date_paid'])) . ' 2nd cutoff'; break;
	}

	$aaData[] = [
		"<input type='hidden' id='hd_lateot_pkid' value='" . $aRow['pkid'] . "'> " . $aRow['empno'],
		$aRow['ot_date'],
		$aRow['ot_count'],
		$ot_type,
		$ot_date_paid,
		'<button class="fa fa-remove btn btn-danger" data-id="' . $aRow['pkid'] . '"></button>',
	];
}

ssp_output($result, $aaData);

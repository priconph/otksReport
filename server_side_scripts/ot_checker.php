<?php
require_once(__DIR__ . '/ssp_pdo.php');

// $sTable = 'vw_ot_checker'; // 03182026 by Nessa
$result = ssp_run('vw_ot_checker_rev3', [
	'empno', 'ot_date', 'filed_ot', 'valid_ot', 'req_by', 'req_approver',
	'req_status', 'date_created', 'lastupdate', 'pkid',
], 'pkid');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$date_created = date('F d, Y h:i:s A', strtotime((string)$aRow['date_created']));
	$lastupdate   = date('F d, Y h:i:s A', strtotime((string)$aRow['lastupdate']));

	$status = '';
	switch ((string)$aRow['req_status']) {
		case '0': $status = "<span class='badge' style='background:gray;'>Draft</span>"; break;
		case '1': $status = "<span class='badge' style='background:yellow;color:black;'>Pending</span>"; break;
		case '2': $status = "<span class='badge' style='background:green;'>Approved (" . $lastupdate . ")</span>"; break;
		case '3': $status = "<span class='badge' style='background:red;'>Disapproved</span>"; break;
		case '4': $status = "<span class='badge' style='background:orange;'>Cancelled</span>"; break;
	}

	$aaData[] = [
		$aRow['empno'],
		$aRow['ot_date'],
		$aRow['filed_ot'],
		$aRow['valid_ot'],
		$aRow['req_by'],
		$date_created,
		$aRow['req_approver'],
		$status,
	];
}

ssp_output($result, $aaData);

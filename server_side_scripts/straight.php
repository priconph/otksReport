<?php
require_once(__DIR__ . '/ssp_pdo.php');

$result = ssp_run('vw_straight', [
	'empno', 'LastName', 'FirstName', 'date_from', 'time_from',
	'date_to', 'time_to', 'hours_consumed', 'remarks', 'status', 'pkid',
], 'pkid');

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$status = '';
	switch ((string)$aRow['status']) {
		case '0': $status = "<span class='badge' style='background:gray;'>Draft</span>"; break;
		case '1': $status = "<span class='badge' style='background:yellow;color:black;'>Pending</span>"; break;
		case '2': $status = "<span class='badge' style='background:green;'>Approved</span>"; break;
		case '3': $status = "<span class='badge' style='background:red;'>Disapproved</span>"; break;
		case '4': $status = "<span class='badge' style='background:orange;'>Cancelled</span>"; break;
	}

	/* Column order kept from the original script (date_from, date_to, time_from, time_to) */
	$aaData[] = [
		$aRow['empno'],
		$aRow['LastName'] . ', ' . $aRow['FirstName'],
		$aRow['date_from'],
		$aRow['date_to'],
		$aRow['time_from'],
		$aRow['time_to'],
		$aRow['hours_consumed'],
		$aRow['remarks'],
		$status,
	];
}

ssp_output($result, $aaData);

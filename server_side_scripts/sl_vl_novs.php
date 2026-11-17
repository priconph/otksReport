<?php
require_once(__DIR__ . '/ssp_pdo.php');

/* Trusted, fixed select expressions; searchable/sortable expressions are derived by stripping the alias. */
$result = ssp_run(
	'db_hris.`vw_leaves` `elfs`',
	[
		"'' AS `fk_tkdataraw`",
		"`elfs`.`EmpNo` AS `empno`",
		"`elfs`.`new_leave_applied` AS `leave_type`",
		"`elfs`.`date_absent_from` AS `date_of_leave`",
		"`elfs`.`no_of_days` AS `days`",
		"`elfs`.`no_of_paid` AS `days_paid`",
		"`elfs`.`cutoff_date` AS `cutoff_date`",
	],
	'`elfs`.`empid`',
	null,
	['(`elfs`.`cutoff_date` > ? AND `elfs`.`date_absent_from` > ?)'],
	['2019-01-05', '2020-07-31']
);

require_once('../reportHandler.php');
$status_all = get_elfs_status_all('', '', '', '');
if (!is_array($status_all)) {
	$status_all = [];
}

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$status = in_array($aRow['empno'], $status_all) ? 1 : 0;
	$badge = $status == 0 ? '<span style="color:red;">Not in OTKS</span>' : '<span style="color:green;">Added in OTKS</span>';
	$aaData[] = [
		$aRow['empno'],
		$aRow['leave_type'],
		$aRow['days'],
		$aRow['date_of_leave'],
		$aRow['days_paid'],
		$aRow['cutoff_date'],
		$badge,
	];
}

ssp_output($result, $aaData);

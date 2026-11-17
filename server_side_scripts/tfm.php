<?php
require_once(__DIR__ . '/ssp_pdo.php');

$aColumns = [
	'inconsistent', 'empno', 'SalaryType',
	'date', 'time_in', 'mode_in',
	'date_out', 'time_out', 'mode_out',
	'reg_hours', 'ot_credit', 'absent',
	'total_undertime', 'leavecode', 'leavenum',
	/* OT's */
	'ot1',
	'ot2', 'ot2a',
	'ot3', 'ot3a',
	'ot4', 'ot4a',
	'ot5', 'ot5a',
	'ot6', 'ot6a',
	'nightdiff', 'aut',
	/* not used in the table */
	'pkid', 'late_mins',
	'under_mins',
];

/* used this field for searching data typed in the search box */
$array_search = [
	'empno',
	'date', 'time_in', 'mode_in',
	'date_out', 'time_out', 'mode_out',
];

$date_start = (string)($_GET['date_start'] ?? '');
$date_end   = (string)($_GET['date_end'] ?? '');

$result = ssp_run(
	'vw_daily_tfm',
	$aColumns,
	'pkid',
	$array_search,
	['(`date` BETWEEN ? AND ?)'],
	[$date_start, $date_end]
);

$aaData = [];
foreach ($result['rows'] as $aRow) {
	$inconsistencies = '';
	if ((string)$aRow['time_in'] === '' || (string)$aRow['time_out'] === '') {
		$inconsistencies .= '<center><span class="badge fa fa-warning fa-lg" style="background-color:red;"> </span></center>';
	}

	$aaData[] = [
		$inconsistencies,
		$aRow['empno'] . '<input type="hidden" class="pkid" value="' . $aRow['pkid'] . '">' . '<input type="hidden" class="empno" value="' . $aRow['empno'] . '">', //1
		$aRow['SalaryType'], //2
		$aRow['date'] . '<input type="hidden" class="date" value="' . $aRow['date'] . '">', //3
		$aRow['time_in'] . '<input type="hidden" class="time_in" value="' . $aRow['time_in'] . '">', //4
		$aRow['mode_in'], //5
		$aRow['date_out'] . '<input type="hidden" class="date_out" value="' . $aRow['date_out'] . '">', //6
		$aRow['time_out'] . '<input type="hidden" class="time_out" value="' . $aRow['time_out'] . '">', //7
		$aRow['mode_out'], //8
		$aRow['reg_hours'], //9
		$aRow['ot_credit'], //10
		$aRow['absent'], //11
		$aRow['total_undertime'], //12
		$aRow['leavecode'], //13
		$aRow['leavenum'], //14
		$aRow['ot1'], //15
		$aRow['ot2'], //16
		$aRow['ot2a'], //17
		$aRow['ot3'], //18
		$aRow['ot3a'], //19
		$aRow['ot4'], //20
		$aRow['ot4a'], //21
		$aRow['ot5'], //22
		$aRow['ot5a'], //23
		$aRow['ot6'], //24
		$aRow['ot6a'], //25
		$aRow['nightdiff'], //26
		$aRow['aut'], //27
	];
}

ssp_output($result, $aaData);

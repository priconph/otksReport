<?php
ob_start(); // first statement, before any include
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine
 * License:   GPL v2 or BSD (3-point)
 */

function respond(array $payload, int $status = 200): never
{
	while (ob_get_level() > 0) {
		ob_end_clean(); // discard stray output, warnings, BOM
	}
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-store');
	echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
	exit;
}

/* Array of database columns which should be read and sent back to DataTables. */
$aColumns = [
	'inconsistent',
	'pkid',
	'date',
	'time_in',
	'mode_in',
	'date_out',
	'time_out',
	'mode_out',
	'empno',
	'LastName',
	'Department',
	'Position',
	'Section',
	'FirstName',
	'shift',
];

/* used this field for searching data typed in the search box */
$array_search = ['empno', 'LastName'];

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = 'pkid';

/* DB table to use (fixed allowlist, never from input) */
$sTable = 'vw_raw_data_rev';
// $sTable = 'vw_raw_data'; // ORIG
// $sTable = 'vw_raw_data_temp'; // TEMP 03262024

require_once('config.php'); // defines $username, $password, $db_name, $server

try {
	/* Charset intentionally not set: old mysql_* connection used the server default (open item). */
	$pdo = new PDO('mysql:host=' . $server . ';dbname=' . $db_name, $username, $password, [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	]);

	$quote = static fn(string $col): string => '`' . str_replace('`', '', $col) . '`';

	/*
	 * Paging
	 */
	$sLimit = '';
	$limitParams = [];
	if (isset($_GET['iDisplayStart']) && ($_GET['iDisplayLength'] ?? '-1') != '-1') {
		$start  = max(0, (int)$_GET['iDisplayStart']);
		$length = min(1000, max(1, (int)($_GET['iDisplayLength'] ?? 10)));
		$sLimit = 'LIMIT ?, ?';
		$limitParams = [$start, $length];
	}

	/*
	 * Ordering (column names come from the fixed $aColumns list only)
	 */
	$sOrder = '';
	if (isset($_GET['iSortCol_0'])) {
		$orderParts = [];
		$sortingCols = (int)($_GET['iSortingCols'] ?? 0);
		for ($i = 0; $i < $sortingCols; $i++) {
			$colIdx = (int)($_GET['iSortCol_' . $i] ?? 0);
			if (($_GET['bSortable_' . $colIdx] ?? '') == 'true' && isset($aColumns[$colIdx]) && $aColumns[$colIdx] !== 'inconsistent') {
				$dir = strtolower((string)($_GET['sSortDir_' . $i] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
				$orderParts[] = $quote($aColumns[$colIdx]) . ' ' . $dir;
			}
		}
		if ($orderParts) {
			$sOrder = 'ORDER BY ' . implode(', ', $orderParts);
		}
	}

	/*
	 * Filtering
	 */
	$conditions = [];
	$params = [];

	$sSearch = (string)($_GET['sSearch'] ?? '');
	if ($sSearch !== '') {
		$searchParts = [];
		foreach ($aColumns as $col) {
			if (in_array($col, $array_search, true)) {
				$searchParts[] = $quote($col) . ' LIKE ?';
				$params[] = '%' . $sSearch . '%';
			}
		}
		$conditions[] = '(' . implode(' OR ', $searchParts) . ')';
	}

	/* Individual column filtering */
	for ($i = 0; $i < count($aColumns); $i++) {
		if (($_GET['bSearchable_' . $i] ?? '') == 'true' && (string)($_GET['sSearch_' . $i] ?? '') !== '') {
			$conditions[] = $quote($aColumns[$i]) . ' LIKE ?';
			$params[] = '%' . $_GET['sSearch_' . $i] . '%';
		}
	}

	/* Date range and optional employee list */
	$date_start = (string)($_GET['date_start'] ?? '');
	$date_end   = (string)($_GET['date_end'] ?? '');
	$empno      = (string)($_GET['empno'] ?? '');

	$conditions[] = '((`date` BETWEEN ? AND ?) OR (`date_out` BETWEEN ? AND ?))';
	array_push($params, $date_start, $date_end, $date_start, $date_end);

	if ($empno !== '') {
		$empnoList = explode(',', $empno);
		$conditions[] = '`empno` IN (' . implode(', ', array_fill(0, count($empnoList), '?')) . ')';
		foreach ($empnoList as $e) {
			$params[] = $e;
		}
	}

	$sWhere = 'WHERE ' . implode(' AND ', $conditions);

	/*
	 * SQL queries
	 * Get data to display
	 */
	$selectColumns = implode(', ', array_map($quote, $aColumns));
	$stmt = $pdo->prepare("SELECT $selectColumns FROM `$sTable` $sWhere $sOrder $sLimit");
	$pos = 1;
	foreach ($params as $p) {
		$stmt->bindValue($pos++, $p, PDO::PARAM_STR);
	}
	foreach ($limitParams as $p) {
		$stmt->bindValue($pos++, $p, PDO::PARAM_INT);
	}
	$stmt->execute();
	$rResult = $stmt->fetchAll();

	/* Data set length after filtering (SQL_CALC_FOUND_ROWS is deprecated in MySQL 8) */
	$stmt = $pdo->prepare("SELECT COUNT(`$sIndexColumn`) FROM `$sTable` $sWhere");
	$pos = 1;
	foreach ($params as $p) {
		$stmt->bindValue($pos++, $p, PDO::PARAM_STR);
	}
	$stmt->execute();
	$iFilteredTotal = (int)$stmt->fetchColumn();

	/* Total data set length */
	$iTotal = (int)$pdo->query("SELECT COUNT(`$sIndexColumn`) FROM `$sTable`")->fetchColumn();
} catch (Throwable $e) {
	error_log('server_side_scripts/raw_data.php: ' . $e->getMessage());
	respond(['error' => 'Server error'], 500);
}

/*
 * Output
 */
$output = [
	'sEcho' => (int)($_GET['sEcho'] ?? 0),
	'iTotalRecords' => $iTotal,
	'iTotalDisplayRecords' => $iFilteredTotal,
	'aaData' => [],
];

foreach ($rResult as $aRow) {
	$inconsistencies = '';
	if ((string)$aRow['time_in'] === '' || (string)$aRow['time_out'] === '') {
		$inconsistencies .= '<center><span class="badge fa fa-warning fa-lg" style="background-color:red;"> </span></center>';
	}

	switch ((string)$aRow['shift']) {
		case '1':
			$shift = '<center><span class="badge" style="background-color: lightblue; color: black;">Morning Shift (6-2AM) </span></center>';
			break;
		case '2':
			$shift = '<center><span class="badge" style="background-color: orange;">Afternoon Shift (2-10PM) </span></center>';
			break;
		case '3':
			$shift = '<center><span class="badge" style="background-color:black;">Night Shift (10-6AM) </span></center>';
			break;
		default:
			$shift = '<center><span class="badge" style="background-color: green;">Normal Shift<br>(7:30-4:30AM or 7:30PM-7:30AM) </span></center>';
			break;
	}

	$output['aaData'][] = [
		$inconsistencies,
		$aRow['pkid'],
		$aRow['date'],
		$aRow['time_in'],
		$aRow['mode_in'],
		$aRow['date_out'],
		$aRow['time_out'],
		$aRow['mode_out'],
		$aRow['empno'],
		$aRow['LastName'] . ', ' . $aRow['FirstName'],
		$aRow['Department'],
		$aRow['Position'],
		$aRow['Section'],
		$shift,
		$aRow['shift'],
	];
}

respond($output);

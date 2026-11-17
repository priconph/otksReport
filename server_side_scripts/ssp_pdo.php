<?php
/*
 * Shared PDO helper for the DataTables (legacy 1.9 server-side protocol) endpoints in this folder.
 * Replaces the copy-pasted mysql_* boilerplate. Table, column and index names are trusted values
 * written in each endpoint file; they never come from request input. All request values are bound.
 */
ob_start(); // before any include, so stray output/warnings never corrupt the JSON
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . '/config.php'); // defines $username, $password, $db_name, $server

function ssp_respond(array $payload, int $status = 200): never
{
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-store');
	echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
	exit;
}

/* Plain names get backticks; entries that already contain quoting/expressions are used as written. */
function ssp_ident(string $name): string
{
	return preg_match('/^\w+$/', $name) ? '`' . $name . '`' : $name;
}

/* Expression usable in WHERE/ORDER BY: strips a trailing "AS `alias`"; null for literals. */
function ssp_filter_expr(string $column): ?string
{
	$expr = trim(preg_replace('/\s+AS\s+`[^`]+`\s*$/i', '', $column));
	return ($expr === '' || $expr === "''") ? null : ssp_ident($expr);
}

function ssp_pdo(): PDO
{
	static $pdo = null;
	global $server, $db_name, $username, $password;
	if ($pdo === null) {
		$pdo = new PDO('mysql:host=' . $server . ';dbname=' . $db_name . ';charset=utf8mb4', $username, $password, [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		]);
	}
	return $pdo;
}

/**
 * @param string     $table         trusted table/view (plain name or full trusted SQL fragment)
 * @param string[]   $columns       trusted select expressions, in DataTables column order
 * @param string     $indexColumn   trusted column used for COUNT()
 * @param string[]|null $searchColumns columns searched by the search box (null = all)
 * @param string[]   $extraConds    trusted SQL conditions using ? placeholders
 * @param array      $extraParams   values for the placeholders in $extraConds
 * @return array{rows: array, total: int, filtered: int, echo: int}
 */
function ssp_run(string $table, array $columns, string $indexColumn, ?array $searchColumns = null, array $extraConds = [], array $extraParams = []): array
{
	try {
		$pdo = ssp_pdo();
		$tableSql = ssp_ident($table);

		/* Paging */
		$sLimit = '';
		$limitParams = [];
		if (isset($_GET['iDisplayStart']) && ($_GET['iDisplayLength'] ?? '-1') != '-1') {
			$sLimit = 'LIMIT ?, ?';
			$limitParams = [
				max(0, (int)$_GET['iDisplayStart']),
				min(1000, max(1, (int)($_GET['iDisplayLength'] ?? 10))),
			];
		}

		/* Ordering */
		$sOrder = '';
		if (isset($_GET['iSortCol_0'])) {
			$orderParts = [];
			$sortingCols = (int)($_GET['iSortingCols'] ?? 0);
			for ($i = 0; $i < $sortingCols; $i++) {
				$idx = (int)($_GET['iSortCol_' . $i] ?? 0);
				if (($_GET['bSortable_' . $idx] ?? '') == 'true' && isset($columns[$idx])) {
					$expr = ssp_filter_expr($columns[$idx]);
					if ($expr !== null) {
						$dir = strtolower((string)($_GET['sSortDir_' . $i] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';
						$orderParts[] = $expr . ' ' . $dir;
					}
				}
			}
			if ($orderParts) {
				$sOrder = 'ORDER BY ' . implode(', ', $orderParts);
			}
		}

		/* Filtering */
		$conditions = [];
		$params = [];

		$sSearch = (string)($_GET['sSearch'] ?? '');
		if ($sSearch !== '') {
			$searchParts = [];
			foreach ($columns as $col) {
				$name = ssp_filter_expr($col);
				if ($name === null) {
					continue;
				}
				if ($searchColumns !== null && !in_array($col, $searchColumns, true)) {
					continue;
				}
				$searchParts[] = $name . ' LIKE ?';
				$params[] = '%' . $sSearch . '%';
			}
			if ($searchParts) {
				$conditions[] = '(' . implode(' OR ', $searchParts) . ')';
			}
		}

		foreach ($columns as $i => $col) {
			$name = ssp_filter_expr($col);
			if ($name !== null && ($_GET['bSearchable_' . $i] ?? '') == 'true' && (string)($_GET['sSearch_' . $i] ?? '') !== '') {
				$conditions[] = $name . ' LIKE ?';
				$params[] = '%' . $_GET['sSearch_' . $i] . '%';
			}
		}

		foreach ($extraConds as $c) {
			$conditions[] = $c;
		}
		foreach ($extraParams as $p) {
			$params[] = $p;
		}

		$sWhere = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

		$selectList = implode(', ', array_map('ssp_ident', $columns));
		$stmt = $pdo->prepare("SELECT $selectList FROM $tableSql $sWhere $sOrder $sLimit");
		$pos = 1;
		foreach ($params as $p) {
			$stmt->bindValue($pos++, $p, PDO::PARAM_STR);
		}
		foreach ($limitParams as $p) {
			$stmt->bindValue($pos++, $p, PDO::PARAM_INT);
		}
		$stmt->execute();
		$rows = $stmt->fetchAll();

		/* Filtered count (SQL_CALC_FOUND_ROWS is deprecated in MySQL 8) */
		$idx = ssp_ident($indexColumn);
		$stmt = $pdo->prepare("SELECT COUNT($idx) FROM $tableSql $sWhere");
		$pos = 1;
		foreach ($params as $p) {
			$stmt->bindValue($pos++, $p, PDO::PARAM_STR);
		}
		$stmt->execute();
		$filtered = (int)$stmt->fetchColumn();

		/* Total count */
		$total = (int)$pdo->query("SELECT COUNT($idx) FROM $tableSql")->fetchColumn();
	} catch (Throwable $e) {
		error_log(basename($_SERVER['SCRIPT_NAME'] ?? 'ssp') . ': ' . $e->getMessage());
		ssp_respond(['error' => 'Server error'], 500);
	}

	return [
		'rows'     => $rows,
		'total'    => $total,
		'filtered' => $filtered,
		'echo'     => (int)($_GET['sEcho'] ?? 0),
	];
}

/* Send the legacy DataTables response; $aaData is the list of display rows. */
function ssp_output(array $result, array $aaData): never
{
	ssp_respond([
		'sEcho'                => $result['echo'],
		'iTotalRecords'        => $result['total'],
		'iTotalDisplayRecords' => $result['filtered'],
		'aaData'               => $aaData,
	]);
}

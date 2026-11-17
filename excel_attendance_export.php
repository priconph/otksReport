<?php
ob_start(); // keep stray output/warnings out of the binary download
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once __DIR__ . '/../media/vendor/autoload.php'; // phpoffice/phpspreadsheet (replaces legacy PHPExcel)
require_once __DIR__ . '/otksr-oop.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
$username = $_SESSION['username'] ?? null;

$dateTodayName = date('Ymd');

/* Items from GET */
$dateFrom = (string)($_GET['startDate'] ?? '');
$dateTo   = (string)($_GET['endDate'] ?? '');
$EN       = (string)($_GET['EN'] ?? '');

try {
	$spreadsheet = new Spreadsheet();
	$spreadsheet->getDefaultStyle()->getFont()->setName('Lucida Fax');
	$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setTitle('EOM');

	$headers = ['EN', 'DATE IN', 'TIME IN', 'MODE IN', 'DATE OUT', 'TIME OUT', 'MODE OUT', 'PKID'];
	foreach ($headers as $i => $header) {
		$column = chr(ord('A') + $i);
		$sheet->setCellValue($column . '1', $header);
		$sheet->getColumnDimension($column)->setWidth(15);
	}

	$array_employee = OTKSR::getInstance()->excel_attendance_export($dateFrom, $dateTo, $EN);

	$formatDate = static fn($v): string => ($v ?? '') !== '' ? date('m/d/Y', strtotime((string)$v)) : '';
	$formatTime = static fn($v): string => ($v ?? '') !== '' ? date('H:i:s', strtotime((string)$v)) : '';

	$row = 2;
	$total = count($array_employee['empno'] ?? []);
	for ($x = 0; $x < $total; $x++) {
		$values = [
			$array_employee['empno'][$x],
			$formatDate($array_employee['date_in'][$x] ?? ''),
			$formatTime($array_employee['time_in'][$x] ?? ''),
			$array_employee['mode_in'][$x] ?? '',
			$formatDate($array_employee['date_out'][$x] ?? ''),
			$formatTime($array_employee['time_out'][$x] ?? ''),
			$array_employee['mode_out'][$x] ?? '',
			$array_employee['pkid'][$x] ?? '',
		];
		foreach ($values as $i => $value) {
			$sheet->setCellValueExplicit(chr(ord('A') + $i) . $row, (string)$value, DataType::TYPE_STRING);
		}
		$row++;
	}

	/* thin borders on every cell, solid white fill */
	$sheet->getStyle('A1:H' . ($row - 1))->applyFromArray([
		'borders' => [
			'allBorders' => ['borderStyle' => Border::BORDER_THIN],
		],
		'fill' => [
			'fillType' => Fill::FILL_SOLID,
			'color'    => ['rgb' => 'FFFFFF'],
		],
	]);

	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	header('Content-Type: application/vnd.ms-excel'); // Excel 2003 (.xls) format, as before
	header('Content-Disposition: attachment;filename="Raw Data ' . $dateTodayName . '.xls"');
	header('Cache-Control: max-age=0');

	$writer = new Xls($spreadsheet);
	$writer->save('php://output');
	$spreadsheet->disconnectWorksheets();
	unset($spreadsheet);
} catch (Throwable $e) {
	error_log('excel_attendance_export.php: ' . $e->getMessage());
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	http_response_code(500);
	header('Content-Type: text/plain; charset=utf-8');
	echo 'Export failed.';
}

<?php
// PhpSpreadsheet-based wrapper (replaces the legacy PHPExcel wrapper).
// Do not enable display_errors here: any warning would be written into the downloaded file.

$autoload = __DIR__ . '/../../media/vendor/autoload.php';
if(!file_exists($autoload)){
	error_log('PhpSpreadsheet autoload not found: ' . $autoload);
	http_response_code(500);
	echo 'Export library is not available.';
	exit;
}
require_once $autoload;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EXCEL extends Spreadsheet{
	public $title = '';
	public $objSheet = '';
	/*
		create new spreadsheet page
	*/
	public function add_page(){
		$this->objSheet = $this->getActiveSheet();
		$this->objSheet->setTitle($this->title); //set title cont.
	}
	/*
		Set default font style
	*/
	public function set_default_font_style(){
		$this->getDefaultStyle()->getFont()->setName('Times New Roman');
	}
	/*
		Set active sheet
	*/
	public function set_active_sheet($sheet_number){
		$this->setActiveSheetIndex($sheet_number);
	}
	/*
		Create new sheet
	*/
	public function add_sheet($index){
		$this->createSheet($index);
		$this->set_active_sheet($index);
		$this->objSheet = $this->getActiveSheet();
		$this->objSheet->setTitle($this->title); //set title cont.
	}
	/*
		Set cell width
	*/
	public function set_width($cell,$width){
		$this->objSheet->getColumnDimension($cell)->setWidth($width);
	}
	/*
		Set cell width using array
	*/
	public function set_width_array($cell,$width){
		for($i=0;$i<count($width);$i++) {
			$this->objSheet->getColumnDimension($cell[$i])->setWidth((float)$width[$i]);
		}
	}
	/*
		Set height
	*/
	public function set_height($array_row,$array_height){
		for($x=0;$x<count($array_row);$x++){
			$this->objSheet->getRowDimension($array_row[$x])->setRowHeight($array_height[$x]);
		}
	}
	public function setheaderfooter($month_name,$year,$division,$division_format,$header_format){
		/* Add header values */
		$this->place_value('A1','PRICON MICROELECTRONIC, INC.','string','',true);
		$this->place_value('A2','LIST OF TARDINESS/UNDERTIME REPORT','string','',true);
		$this->place_value('A3','For the Month of '.$month_name.' '.$year,'string','',true);
		$this->place_value('A5','EMP NO.','string','',true);
		$this->place_value('C5','EMPLOYEE NAME','string','',true);
		$this->place_value('E5','DATE','string','',true);
		$this->place_value('F5','TIME IN','string','',true);
		$this->place_value('G5','TIME OUT','string','',true);
		$this->place_value('H5','LATE/UND.','string','',true);
		$this->place_value('A7',$division,'string','',true);

		/* Set header format */
		$this->set_format('A1',$header_format);
		$this->set_format('A2',$header_format);
		$this->set_format('A3',$header_format);
		$this->set_format('A5:L5',$header_format);
		$this->set_format('A7',$division_format);

		/* Set page margin */
		$this->getActiveSheet()->getPageMargins()->setTop(1);
		$this->getActiveSheet()->getPageMargins()->setRight(0.75);
		$this->getActiveSheet()->getPageMargins()->setLeft(0.75);
		$this->getActiveSheet()->getPageMargins()->setBottom(1);

		$this->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);
	}
	/*
		Set borders
	*/
	public function set_borders($cell,$left,$right,$top,$bottom){
		$default_border = Border::BORDER_THIN;
		if($left == '1' && $right == '1' && $top == '1' && $bottom == '1'){
			$this->objSheet->getStyle($cell)->applyFromArray(array(
				'borders' => array(
					'allBorders' => array('borderStyle' => $default_border)
				)
			));
		}else{
			$sides = array('left' => $left, 'right' => $right, 'top' => $top, 'bottom' => $bottom);
			foreach($sides as $side => $enabled){
				if($enabled == '1'){
					$this->objSheet->getStyle($cell)->applyFromArray(array(
						'borders' => array(
							$side => array('borderStyle' => $default_border)
						)
					));
				}
			}
		}
	}
	/*
		merge cells
	*/
	public function merge_cells($cell){
		$this->objSheet->mergeCells($cell);
	}
	/*
		set font
	*/
	public function set_font($cell,$bold,$color,$background_color,$size,$font){
		if(!$bold){
			$bold = false;
		}
		if($color == ''){
			$color = '000000';
		}
		if($size == ''){
			$size = '10';
		}
		if($font == ''){
			$font = 'Times New Roman';
		}
		$styleArray = array(
			'font'  => array(
				'bold'  => $bold,
				'color' => array('rgb' => $color),
				'size'  => (float)$size,
				'name'  => $font
			)
		);
		if($background_color != ''){
			$styleArray['fill'] = array(
				'fillType' => Fill::FILL_SOLID,
				'startColor' => array('rgb' => $background_color)
			);
		}
		$this->objSheet->getStyle($cell)->applyFromArray($styleArray);
	}
	/*
		place values
	*/
	public function place_value($cell,$value,$data_type = ''){
		if($data_type == 'string'){
			$this->objSheet->setCellValueExplicit($cell, (string)$value, DataType::TYPE_STRING);
		}else if($data_type == 'number_2_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0.00');
		}else if($data_type == 'number_0_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode('0');
		}else if($data_type == 'percentage_0_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
		}else if($data_type == 'percentage_2_decimal'){
			$this->objSheet->getCell($cell)->setValue($value);
			$this->objSheet->getStyle($cell)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
		}
		else{
			$this->objSheet->getCell($cell)->setValue($value);
		}
	}

	public function set_format($cell,$array_format){
		/********************
		* 		FONT 		*
		********************/
		$array_font = array();
		$array_font['name'] = (isset($array_format['name']) && $array_format['name'] != '') ? $array_format['name'] : "Times New Roman";
		$array_font['size'] = (isset($array_format['size']) && $array_format['size'] != '') ? (float)$array_format['size'] : 10;
		$array_font['bold'] = (!empty($array_format['bold']));
		$array_font['color'] = array('rgb' => (isset($array_format['color']) && $array_format['color'] != '') ? $array_format['color'] : '000000');
		/********************
		* 	  ALIGNMENT	   	*
		********************/
		$array_alignment = array();
		if(isset($array_format['h_alignment']) && $array_format['h_alignment'] != ''){
			switch($array_format['h_alignment']){
				case "left" 	: $array_alignment['horizontal'] = Alignment::HORIZONTAL_LEFT; break;
				case "right"	: $array_alignment['horizontal'] = Alignment::HORIZONTAL_RIGHT; break;
				case "center"	: $array_alignment['horizontal'] = Alignment::HORIZONTAL_CENTER; break;
			}
		}
		$array_alignment['vertical'] = Alignment::VERTICAL_CENTER;
		$array_alignment['wrapText'] = (!empty($array_format['wordwrap']));

		/********************
		* 	ARRAY STYLE	*
		********************/
		$styleArray = array(
			 'font'  => $array_font
			,'alignment' => $array_alignment
		);
		/* FILL - only when a fill colour is requested */
		if(isset($array_format['fill_color']) && $array_format['fill_color'] != ''){
			$styleArray['fill'] = array(
				'fillType' => Fill::FILL_SOLID,
				'startColor' => array('rgb' => $array_format['fill_color'])
			);
		}
		$this->objSheet->getStyle($cell)->applyFromArray($styleArray);
	}
	/*
		Generate Excel Output
	*/
	public function output($filename, $file_type){
		$filename = preg_replace('/[^A-Za-z0-9 _.\-]/', '', (string)$filename);
		if($file_type == "2003"){
			$content_type = 'application/vnd.ms-excel';
			$extension = '.xls';
			$objWriter = new Xls($this);
		}else{
			$content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
			$extension = '.xlsx';
			$objWriter = new Xlsx($this);
		}
		// discard any stray output so it does not end up inside the file
		while(ob_get_level() > 0){
			ob_end_clean();
		}
		header('Content-Type: '.$content_type);
		header('Content-Disposition: attachment;filename="'.$filename.$extension.'"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
}
?>

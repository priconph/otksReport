<script src="../media/js/jquery.js"></script>

<?php
require_once('../media/excel_reader/excel_reader.php');

$excel = new PhpExcelReader;      // creates object instance of the class
$excel->read('../common/TFM_TIME.xls');   // reads and stores the excel file data

/* attach buttons */
echo '<input type="text" disabled id="txt_message" value="Loading.. Please be PATIENT!">';

echo '<button class="btn btn-primary" id="btn_import_tfm" disabled> Import TFM to database</button>';

function sheetData($sheet) {
  $re = '<table border="1" id="tbl_tfm">';     // starts html table

  $x = 1;
  while($x <= $sheet['numRows']) {
    $re .= "<tr>\n";
    $y = 1;
    while($y <= $sheet['numCols']) {
      $cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
      $re .= " <td>$cell</td>\n";  
      $y++;
    }  
    $re .= "</tr>\n";
    $x++;
  }

  return $re .'</table>';     // ends and returns the html table
}

$nr_sheets = count($excel->sheets);       // gets the number of worksheets
$excel_data = '';              // to store the the html tables with data of each sheet

// traverses the number of sheets and sets html table with each sheet data in $excel_data
for($i=0; $i<$nr_sheets; $i++) {
  $excel_data .= '<h4>Sheet '. ($i + 1) .' (<em>'. $excel->boundsheets[$i]['name'] .'</em>)</h4>'. sheetData($excel->sheets[$i]) .'<br/>';  
}

echo $excel_data;      // outputs HTML tables with excel file data
?>

<script>
var array_tbl_tfm 			= {};
	array_tbl_tfm.c_date	= [];
	array_tbl_tfm.idnum		= [];
	array_tbl_tfm.t_in		= [];
	array_tbl_tfm.t_out		= [];
	array_tbl_tfm.t_in_ot	= [];
	array_tbl_tfm.t_out_ot	= [];
	array_tbl_tfm.reg_hour	= [];
	array_tbl_tfm.ot_hour	= [];
	array_tbl_tfm.absent	= [];
	array_tbl_tfm.late_unt	= [];
	array_tbl_tfm.slvl_code	= [];
	array_tbl_tfm.slvl_num	= [];
	array_tbl_tfm.ot_1		= [];
	array_tbl_tfm.ot_2		= [];
	array_tbl_tfm.ot_2_a	= [];
	array_tbl_tfm.ot_3		= [];
	array_tbl_tfm.ot_3_a	= [];
	array_tbl_tfm.ot_4		= [];
	array_tbl_tfm.ot_4_a	= [];
	array_tbl_tfm.ot_5		= [];
	array_tbl_tfm.ot_5_a	= [];
	array_tbl_tfm.ot_6		= [];
	array_tbl_tfm.ot_6_a	= [];
	array_tbl_tfm.n_diff	= [];
	array_tbl_tfm.aut		= [];
	
var ctr = 0;
$('#tbl_tfm tr').each(function(){
	if(ctr!=0){
		array_tbl_tfm.c_date.push( $(this).find('td:eq(0)').text() );
		array_tbl_tfm.idnum.push( $(this).find('td:eq(1)').text() );
		array_tbl_tfm.t_in.push( $(this).find('td:eq(3)').text() );
		array_tbl_tfm.t_out.push( $(this).find('td:eq(4)').text() );
		array_tbl_tfm.t_in_ot.push( $(this).find('td:eq(5)').text() );
		array_tbl_tfm.t_out_ot.push( $(this).find('td:eq(6)').text() );
		array_tbl_tfm.reg_hour.push( $(this).find('td:eq(7)').text() );
		array_tbl_tfm.ot_hour.push( $(this).find('td:eq(8)').text() );
		array_tbl_tfm.absent.push( $(this).find('td:eq(9)').text() );
		array_tbl_tfm.late_unt.push( $(this).find('td:eq(10)').text() );
		array_tbl_tfm.slvl_code.push( $(this).find('td:eq(11)').text() );
		array_tbl_tfm.slvl_num.push( $(this).find('td:eq(12)').text() );
		array_tbl_tfm.ot_1.push( $(this).find('td:eq(13)').text() );
		array_tbl_tfm.ot_2.push( $(this).find('td:eq(14)').text() );
		array_tbl_tfm.ot_2_a.push( $(this).find('td:eq(15)').text() );
		array_tbl_tfm.ot_3.push( $(this).find('td:eq(16)').text() );
		array_tbl_tfm.ot_3_a.push( $(this).find('td:eq(17)').text() );
		array_tbl_tfm.ot_4.push( $(this).find('td:eq(18)').text() );
		array_tbl_tfm.ot_4_a.push( $(this).find('td:eq(19)').text() );
		array_tbl_tfm.ot_5.push( $(this).find('td:eq(20)').text() );
		array_tbl_tfm.ot_5_a.push( $(this).find('td:eq(21)').text() );
		array_tbl_tfm.ot_6.push( $(this).find('td:eq(22)').text() );
		array_tbl_tfm.ot_6_a.push( $(this).find('td:eq(23)').text() );
		array_tbl_tfm.n_diff.push( $(this).find('td:eq(24)').text() );
		array_tbl_tfm.aut.push( $(this).find('td:eq(25)').text() );
	}
	ctr++;
});
$('#txt_message').val('complete');
$('#btn_import_tfm').prop('disabled',false);

console.log(array_tbl_tfm);

$('#btn_import_tfm').click(function(){
	if(!confirm("Import these data to database?")){
		return false;
	}
	var data = {
		"action"			: "",
		"array_tbl_tfm"		: array_tbl_tfm
	}
	data = $.param(data);
	$.ajax({
		type	: "post",
		dataType: "json",
		data	: data,
		url		: "import_tfm_ajax.php",
		success	: function(result){
			alert(result['msg']);
		}
	});
});

</script>

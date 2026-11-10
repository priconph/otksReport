<?php
class TimeAndDate{
	public $date_request;
	
	public function get_last_month_day(){
		$year_month_day = $this->date_request;
		
		$array_year_month 		= explode('-',$year_month_day);
		$year 					= $array_year_month[0];
		$month 					= $array_year_month[1];
				
		/* check if month is 1 */
		if($month == 1){
			$prev_year		= $year - 1;
			$prev_month 	= str_pad('12', 2, '0', STR_PAD_LEFT);

		}else {
			$prev_year	= $year;
			$prev_month 	= str_pad($month - 1, 2, '0', STR_PAD_LEFT);
		}
		
		$last_day_of_last_month = date("Y-m-t", strtotime($prev_year.'-'.$prev_month.'-'.'01'));
		$array_last_day_of_last_month = explode('-',$last_day_of_last_month);
		$last_day = $array_last_day_of_last_month[2];
		return $last_day;
	}
	
	public function separate_month_day_year(){
		$array_month_day_year = explode('-', $this->date_request);
		$array_date 	= array();
		$array_date['year']		 	= $array_month_day_year[0];
		$array_date['month'] 		= $array_month_day_year[1];
		$array_date['day'] 			= $array_month_day_year[2];
		return $array_date;
	}
}

class sample extends TimeAndDate{
	public function hello_world(){
		echo 'extended';
	}
}

?>
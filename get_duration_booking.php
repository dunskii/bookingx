<?php
session_start();
require_once('../../../wp-load.php');
$booking_customer_note = 'zero';
global $wpdb;

$seat_alias = crud_option_multisite("bkx_alias_seat");
$base_alias = crud_option_multisite("bkx_alias_base");
$addition_alias = crud_option_multisite('bkx_alias_addition');

$baseid 	= $_POST['baseid'];
$additionid = $_POST['additionid'];

if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && $_SESSION['free_seat_id']!=''  && crud_option_multisite('enable_any_seat') == 1 && crud_option_multisite('select_default_seat') != ''):
	$mobonlyobj = $_SESSION['free_seat_id'];
else:
	$mobonlyobj = $_POST['seatid'];
endif;

$booking_summary = '<ul>';
 
if(is_numeric($mobonlyobj) && $mobonlyobj!=0){
 	$SeatObj = get_post($mobonlyobj);
    $seatmeta = get_post_custom( $SeatObj->ID );
    $seat_is_pre_payment = isset( $seatmeta['seatIsPrePayment'] ) ? esc_attr( $seatmeta['seatIsPrePayment'][0] ) : "";
    if($seat_is_pre_payment == 'N'){

    	$booking_customer_note = sprintf( __( 'Payment will be made when the customer comes for the booking.', 'bookingx' ), '');
    }
    //$booking_summary .= '<li><b>'.$seat_alias.' name : </b>'.$SeatObj->post_title.'';
    $booking_summary .= sprintf( __( '<li><b> %1$s name : </b> %2$s', 'bookingx' ), $seat_alias,$SeatObj->post_title);
}

if(isset($baseid) && $baseid!='')
{
    $GetBaseObj = get_post($baseid);   
    $objBase = get_post_custom( $GetBaseObj->ID ); 
    //$base_data = '<li><b>'.$base_alias.' name : </b>'.$GetBaseObj->post_title.'';
    $base_data = sprintf( __( '<li><b> %1$s name : </b> %2$s', 'bookingx' ), $base_alias,$GetBaseObj->post_title);
}

$res_arr = array();

function get_no_of_days($month)
{
	$no_of_days = 0;
		
	return $no_of_days;
}

function group_nums($array) { 
   $ret  = array(); 
   $temp = array(); 
   foreach($array as $val) { 
      if(next($array) == ($val + 1)) 
         $temp[] = $val; 
      else 
         if(count($temp) > 0) { 
            $temp[] = $val; 
            $ret[]  = $temp[0].':'.end($temp); 
            $temp   = array(); 
         } 
         else 
            $ret[] = $val; 
   } 
   return $ret; 
}

/**
*getDaysNumber method 
*This method returns array of numbers equivalent to days array
*
*@params array $arr
*@return array 
*/
function getDaysNumber($arr)
{
	$arrNumber = array();
	foreach($arr as $day)
	{
		$day = strtolower($day);
		if($day == "sunday")
		{
			array_push($arrNumber, 7);
		}
		elseif($day == "monday")
		{
			array_push($arrNumber, 1);
		}
		elseif($day == "tuesday")
		{
			array_push($arrNumber, 2);
		}
		elseif($day == "wednesday")
		{
			array_push($arrNumber, 3);
		}
		elseif($day == "thursday")
		{
			array_push($arrNumber, 4);
		}
		elseif($day == "friday")
		{
			array_push($arrNumber, 5);
		}
		elseif($day == "saturday")
		{
			array_push($arrNumber, 6);
		}
	}
	return ($arrNumber);

}

/**
*checkIfSeatCanBeBooked method 
*This method checks wether the selected seat can be booked based on the total duration of base and addition selected
*
*@params string $seatid, integer $totalduration
*@return boolean 
*/
function checkIfSeatCanBeBooked($seatid, $totalduration)
{
	global $wpdb;
	//$res_seat = $wpdb->get_results("SELECT * FROM bkx_seat WHERE seat_id = ".trim($seatid)."");
 
        $GetSeatObj = get_post($seatid);
        $res_seat = get_post_custom( $GetSeatObj->ID );
        $seat_is_certain_day = $values['seat_is_certain_day'][0];
	if(isset($seat_is_certain_day) && $seat_is_certain_day == "Y")
	{
		//$res_seat_time = $wpdb->get_results("SELECT * FROM bkx_seat_time WHERE seat_id = ".trim($seatid)."");
		$res_seat_time_arr = array();
                
                $seat_days_time = maybe_unserialize($values['seat_days_time'][0]);
                $res_seat_time= maybe_unserialize($seat_days_time);
		//print_r($res_seat_time);
		foreach($res_seat_time as $temp)
		{
			$res_seat_time_arr[strtolower($temp->day)]['time_from'] = $temp->time_from; 				
			$res_seat_time_arr[strtolower($temp->day)]['time_till'] = $temp->time_till;
		}
		$days = 0 ;
		if($totalduration > (24*60*60))
		{
			$days = $totalduration/(24*60*60);
		}
		$days_mod = $totalduration%(24*60*60);
		if($days_mod > 0)
		{
			$days = $days + 1;
		}
		//print_r($res_seat_time_arr);
		$arrDays = array();
		foreach($res_seat_time_arr as $key=>$val)
		{
			array_push($arrDays, $key);
		}
		
		$arrDays = getDaysNumber($arrDays);
		//print_r($arrDays);
		//sort days array 
		sort($arrDays,SORT_NUMERIC);
		$nums = $arrDays;
		$arrSorted = group_nums($nums);
		$arrNew = array();
		//print_r($arrSorted); 
		$newArryGrouped = array();
		$arrGrouped = array();
		$newArryGroupedLength = array();
		$temp_max = 0;
		$temp_max_arr = array();
		foreach($arrSorted as $temp)
		{
			if(substr_count($temp,':')>0)
			{
				//array_push($newArryGrouped,$temp);
				$tempArryGrouped['value'] = $temp; 
				$arrExploded = explode(':',$temp);
				$arrNumbers = range($arrExploded[0],$arrExploded[1]);
				$tempArryGrouped['length'] = sizeof($arrNumbers);
				//$temp_max = sizeof($arrNumbers);
				if(sizeof($arrNumbers)>$temp_max)
				{
					$temp_max = sizeof($arrNumbers);
					$temp_max_arr['value'] = $temp;
					$temp_max_arr['length'] = sizeof($arrNumbers);
				}
				array_push($newArryGrouped, $tempArryGrouped);

				$arrGrouped[sizeof($arrNumbers)][] = $temp;
				//array_push($newArryGroupedLength, sizeof($arrNumbers));
			}
		}

		if(sizeof($arrGrouped)>0)
		{
			$counter_temp==0;
			$counter_temp_arr = array();
			foreach($arrGrouped as $key=>$temp)
			{
				if($key>$counter_temp)
				{
					$counter_temp = $key;
					$counter_temp_arr = $temp;
				}
			}
			//print_r($counter_temp_arr);
			$finalValues = array();
			$counter = 0;
			foreach($counter_temp_arr as $temp)
			{
				$tempExplode = explode(':',$temp);
				$tempValues = range($tempExplode[0],$tempExplode[1]);
				if($counter==0)
				{
					$finalValues = $tempValues;
				}
				if(in_array(1,$tempValues) || in_array(7,$tempValues))
				{
					$finalValues = $tempValues;
				}
				$counter = $counter + 1;
			}
			$arrNew = $finalValues;
		}
		else
		{
			if(in_array(1,$arrSorted) && in_array(7,$arrSorted))
			{
				$arrNew = array(1,7);			
			}
			else
			{
				$arrNew = array(end($arrSorted));
			}
		}
		//now checks the size of $arrNew 
		$lengthDays = sizeof($arrNew);
	}
	else
	{
		$lengthDays = 7;
		return 1;
	}
	//if($days > 7 && $lengthDays)
	if($lengthDays < $days)
	{
		return 0;
	}
	else
	{
		return 1;
	}
}

//mobile only data
if(isset($mobonlyobj)){
	$mob_only_res = $objBase['base_is_mobile_only'][0];;
}
 
//calculation of base duration
$str_addition_name = '';
if(isset($baseid))
{
	//$base_query = "Select * from bkx_base where base_id=".trim($baseid);
	//$objBase = $wpdb->get_results($base_query);
        $base_time_option = $objBase['base_time_option'][0];
	 

	$res_arr['base']['base_time_option'] = $base_time_option;
	$base_time=0;
	if($base_time_option=="H"){
		//calculate base minutes
		$base_minutes = $objBase['base_minutes'][0];
		$minute = 0;
		if($base_minutes == 0){
			$minute = 0;
		}else if($base_minutes == 15){
			$minute = 0.25;
		}else if($base_minutes == 30){
			$minute = 0.5;
		}else if($base_minutes == 45){
			$minute = 0.75;
		}

		$base_hours_display = '';
		if( $objBase['base_hours'][0] ){
                //$base_hours_display .= $objBase['base_hours'][0]. " Hours ";
                $base_hours_display .= sprintf( __( '%1$s Hours', 'bookingx' ), $objBase['base_hours'][0]);
        }
        if( $base_minutes!=0 ) {
               //$base_hours_display .= $base_minutes." Minutes";
               $base_hours_display .= sprintf( __( '%1$s Minutes', 'bookingx' ), $base_minutes);
        }

		$booking_summary .= $base_data . " - " .$base_hours_display;

		//$booking_summary .= sprintf( __( '%1$s - %1$s', 'bookingx' ), $base_data, $base_hours_display);

		$base_time = $objBase['base_hours'][0] + $minute; //add base minutes to base hours
	}
	if( $base_time_option=="D" ){
		$base_time = $objBase['base_day'][0]*24;
		
	}
	if( $base_time_option=="M" ){
		$base_time = $objBase['base_month'][0]*30*24;
	}
	//total base time in seconds
	$total_base_time_insec = $base_time * 60 *60;

	//calculation of addition duration
	$objAddition = array();
	if( isset($_POST['additionid']) && sizeof($_POST['additionid']) > 0 )
	{                
       //Get Seat post Array
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'bkx_addition',
            'post_status'      => 'publish',
            'include'          => $_POST['additionid']
        );
        $objListAddition = get_posts( $args );

		$str_addition_name = "<ul>";
		foreach( $objListAddition as $extra ){
           // $booking_summary .= '<li>'.$SeatObj->post_title.'</li>';  
            $objextra = get_post_custom( $extra->ID );
           //print_r($extra);
			$objAddition[$extra->ID]['addition_time_option'] = $objextra['addition_time_option'][0];
			if($objextra['addition_time_option'][0]=="H"){
				//calculate addition minutes in hours
				$addition_minutes = $objextra['addition_minutes'][0];
				$minute = 0;
				if($addition_minutes == 0){
					$minute = 0;
				}else if($addition_minutes == 15){
					$minute = 0.25;
				}else if($addition_minutes == 30){
					$minute = 0.5;
				}else if($addition_minutes == 45){
					$minute = 0.75;
				}
				 
				$objAddition[$extra->ID]['addition_time'] = $objextra['addition_hours'][0] + $minute;

				$extra_hours_display = '';			 
				if($objextra['addition_hours'][0]){
		                //$extra_hours_display .= $objextra['addition_hours'][0]. " Hours ";
		                $extra_hours_display .= sprintf( __( '%1$s Hours', 'bookingx' ), $objextra['addition_hours'][0]);
		        }
		        if($addition_minutes!=0){
		               //$extra_hours_display .= $addition_minutes." Minutes";
		               $extra_hours_display .= sprintf( __( '%1$s Minutes', 'bookingx' ), $addition_minutes);
		        }

		        $objAddition[$extra->ID]['addition_time_display']  = $extra_hours_display;

				//add addition minutes to addition hours
			}else if($temp->addition_time_option=="D"){
				$objAddition[$extra->ID]['addition_time'] = $objextra['addition_days'][0]*24;
				//$objAddition[$extra->ID]['addition_time_display'] = $objextra['addition_days'][0]." Days";
				$objAddition[$extra->ID]['addition_time_display'] = sprintf( __( '%1$s Days', 'bookingx' ), $objextra['addition_days'][0]);

			}else if($temp->addition_time_option=="M"){
				$objAddition[$extra->ID]['addition_time'] = $objextra['addition_months'][0]*24*30;
				//$objAddition[$extra->ID]['addition_time_display'] = $objextra['addition_months'][0]." Months";
				$objAddition[$extra->ID]['addition_time_display'] = sprintf( __( '%1$s Months', 'bookingx' ), $objextra['addition_months'][0]);
			}
			//$str_addition_name .= "<li>".$extra->post_title." - $".$objextra['addition_price'][0]." - ".$extra_hours_display."</li>";
			$str_addition_name .= sprintf( __( '<li> %1$s - %2$s%3$s - %4$s </li>', 'bookingx' ),$extra->post_title,get_current_currency(),$objextra['addition_price'][0],$extra_hours_display);
		}
		//$booking_summary .= '</ul>';
		$str_addition_name.="</ul>";

		$booking_summary .= '<li><b>'.sprintf( __( '%1$s', 'bookingx' ), $addition_alias).' : </b></li><li>'.$str_addition_name.'</li>';
		
	}
	$counter = 0;
	//print_r($objAddition);
	if(isset($objAddition) && (sizeof($objAddition)>0))
	{
		foreach($objAddition as $key=>$val)
		{
			if(isset($val['addition_time']))
			{
				$counter = $counter+ $val['addition_time']; 
			}
		}
	}
	//total addition time in seconds
	$total_addition_time_insec = $counter * 60 * 60; //convert addition time in seconds
	//total duration booked in seconds
	$total_duration_seconds = $total_addition_time_insec + $total_base_time_insec;
    //check if can be booked 
	$seatid = $_POST['seatid'];
	$result = checkIfSeatCanBeBooked($seatid, $total_duration_seconds); //if allowed to select time option

	$counter = $counter + $base_time;
	
	if($counter < 24)
	{
		$output_time = sprintf( __( '%1$s Hours', 'bookingx' ), $counter);
	}
	else if($counter >= 24)
	{
		$days = floor($counter/24);
		$hours_remaining = $counter%24;
		if($days<30)
		{
			if($hours_remaining!=0)
			{
				//$output_time = $days." Days ".$hours_remaining." Hours";
				$output_time = sprintf( __( '%1$s Days %2$s Hours', 'bookingx' ), $days,$hours_remaining);
			}
			else
			{
				//$output_time = $days." Days";
				$output_time = sprintf( __( '%1$s Days', 'bookingx' ), $days);
			}
		}
		else if($days>30)
		{
			$months = $days/30;
			$days_remaining = $days%30;
			if($hours_remaining==0)
			{
				if($days_remaining==0)
				{
					//$output_time = $months." Months ";
					$output_time = sprintf( __( '%1$s Months', 'bookingx' ), $months);
				}
				else
				{
					//$output_time = $months." Months ".$days_remaining." Days";
					$output_time = sprintf( __( '%1$s Months %2$s Days', 'bookingx' ), $months,$days_remaining);
					 
				}
			}
			else
			{
				if($days_remaining==0)
				{
					//$output_time = $months." Months ".$hours_remaining." Hours";
					$output_time = sprintf( __( '%1$s Months %2$s Hours', 'bookingx' ), $months, $hours_remaining);
				}
				else
				{
					//$output_time = $months." Months ".$days_remaining." Days ".$hours_remaining." Hours";
					$output_time = sprintf( __( '%1$s Months %2$s Days %3$s Hours', 'bookingx' ), $months, $days_remaining, $hours_remaining);
				}
			}
		}
	}
}
$total_tax = 0;
$total_price = 0;
$total_price 	= bkx_cal_total_price( $baseid, $_POST['extended'] , $additionid );

if(!empty($total_price)){
	$bkx_cal_total_tax 		= bkx_cal_total_tax( $total_price );
	if(!empty($bkx_cal_total_tax)){
		$total_tax 		= $bkx_cal_total_tax['total_tax'];
		$tax_rate 		= $bkx_cal_total_tax['tax_rate'];
		$grand_total 	= $bkx_cal_total_tax['grand_total'];
		$tax_name 		= $bkx_cal_total_tax['tax_name'];
		$total_price 	= $bkx_cal_total_tax['total_price'];

		$total_tax 		= number_format((float)$total_tax, 2, '.', '');
		$total_price 	= number_format((float)$total_price, 2, '.', '');
		$grand_total 	= number_format((float)$grand_total, 2, '.', '');
	}
}


//$booking_summary .= '<li><b>Total Time  : </b>'.$output_time.'</li></ul>';
$booking_summary .= sprintf( __( '<li><b> Total Time : </b> %1$s </li></ul>', 'bookingx' ),$output_time);

$currency = get_current_currency();
$output_time 							= str_replace('"', "", $output_time);
$arr_output['addition_list'] 			= $str_addition_name;
$arr_output['booking_customer_note'] 	= $booking_customer_note;
$arr_output['booked_summary'] 			= $booking_summary;
$arr_output['time_output'] 				= $output_time;
$arr_output['totalduration_in_seconds'] = $total_duration_seconds;
$arr_output['result'] 					= $result;
$arr_output['mob_only'] 				= $mobonlyobj;
$arr_output['total_price'] 				= $total_price;
$arr_output['total_tax'] 				= $total_tax;
$arr_output['tax_rate'] 				= $tax_rate;
$arr_output['grand_total'] 				= $grand_total;
$arr_output['tax_name'] 				= $tax_name;
$arr_output['currency']					= $currency;

$output_time							= json_encode($arr_output);

echo $output_time;
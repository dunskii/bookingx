<?php
require_once('../../../wp-load.php');
session_start();

global $wpdb;
$start = $_POST['start'];
$bookingduration = $_POST['bookingduration'];
$bookingdate = $_POST['bookingdate'];
$order_statuses = array('bkx-processing','bkx-on-hold','bkx-completed');
if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && crud_option_multisite('enable_any_seat') == 1 && crud_option_multisite('select_default_seat') != ''):
	$base_id = $_SESSION['_session_base_id'];
//get current booking start time slot
	$bookinghour = explode(':',$start);
	$hours_temp = $bookinghour[0];
	$minutes_temp = $bookinghour[1];
	if($minutes_temp=='00')
	{
		$counter = 0;
	}
	else if($minutes_temp=='15')
	{
		$counter = 1;
	}
	else if($minutes_temp=='30')
	{
		$counter = 2;
	}
	else if($minutes_temp=='45')
	{
		$counter = 3;
	}
	$startingSlotNumber = intval($hours_temp)*4+$counter;

	$_enable_any_seat_selected_id = (int)crud_option_multisite('select_default_seat'); // Default Seat id
 	$search = array('bookigndate' => $bookingdate,'service_id'=>$base_id , 'seat_id' => 'any' , 'status' => $order_statuses,'display'=> 1);
	$BkxBooking =  new BkxBooking();
	$objBookigntime = $BkxBooking->GetBookedRecords($search);


	if(!empty($objBookigntime)):

		foreach ($objBookigntime as $objBooknig):
			$slot_id= $objBooknig['full_day'];
			$duration = ($objBooknig['booking_time'])/60;
			$seatslots = intval($duration/15);
			$_SESSION['_seatslots']=$seatslots;

			$booking_slot_range=array();
			if($seatslots>1)
			{
				for($i=0; $i<$seatslots; $i++) {
					$slot_id = $objBooknig['full_day'];
					$booking_slot_range[] = $slot_id + $i;
				}
			}else{
				$booking_slot_range[] =	$slot_id;
			}
			$booking_seat_id= $objBooknig['seat_id'];
			$get_booked_range[$slot_id][$booking_seat_id] =$booking_slot_range;
		endforeach;

		if($_SESSION['_seatslots']>1){
			for($i=0; $i<$_SESSION['_seatslots']; $i++) {
				$find_slot_value_in_booked_array[] = ($startingSlotNumber+1)+ $i;
			}
		}else{
			$find_slot_value_in_booked_array[] =$startingSlotNumber+1;;
		}
		//echo '<pre> $find_slot_value_in_booked_array : ' . print_r($find_slot_value_in_booked_array, true) . '</pre>';

		//print_r($get_booked_range);

		if(!empty($get_booked_range)):
			$slot_id=$startingSlotNumber+1;
			foreach($get_booked_range as $slot_id=>$get_booked_slot)
			{
				$free_seat_all_arr= $get_free_seats;
				$send_seat_block_data= array();
				$slots_blocked=array();
				$slot_id=$startingSlotNumber+1;
				//$fullArray=array();
				if(!empty($get_booked_slot)) :
					//echo '<pre>' . print_r($get_booked_slot, true) . '</pre>';
					foreach ($get_booked_slot as $seat_id=>$booked_slot)
					{
					//echo '<pre> Need to blocked is : ' . $slot_id . '' . print_r($booked_slot, true) . '</pre><br>';
						foreach($find_slot_value_in_booked_array as $_find_slot)
						{
							if(in_array($_find_slot,$booked_slot)) {
								//echo 'Now find slot id is : ' . $_find_slot ."<br>";
								$total_booked_slot_count = sizeof($booked_slot);
								$now_booked_slots_is[] = $booked_slot;
								$now_booked_slots_with_seats[$_find_slot][] = $seat_id;
							}
						}
					}
				endif;
			}
			/*echo $slot_id.' <pre> $now_booked_slots_with_seats : ' . print_r($now_booked_slots_with_seats, true) . '</pre>';*/
			if(!empty($now_booked_slots_with_seats))
			{
				 foreach($now_booked_slots_with_seats as $booked_slot_id=>$booked_seats)
				 {
					 foreach($booked_seats as $now_seat_booked)
					 {
						 $booked_all_seat_id_arr[] = $now_seat_booked;
						 $booked_all_seat_id_arr= array_unique($booked_all_seat_id_arr);
					 }
				 }
			}
			
			$BkxBase = new BkxBase('',$base_id);
			$seat_by_base = $BkxBase->get_seat_by_base();

			if(!empty($seat_by_base)){
				$total_free_seat_all  = sizeof($seat_by_base);
				$free_seat_all_arr = $seat_by_base;
			}

			if(!empty($free_seat_all_arr)) :
				foreach ($free_seat_all_arr as $get_seat_id) :
					if(!in_array($get_seat_id,$booked_all_seat_id_arr)):
						$total_frees_seats[] = $get_seat_id;
					endif;
				endforeach;
			endif;
			//echo '<pre>' . print_r($total_frees_seats, true) . '</pre>';
			if(!empty($total_frees_seats) && sizeof($total_frees_seats) > 1)
			{
				if(in_array($_enable_any_seat_selected_id, $total_frees_seats)):
					$free_seat_id = $_enable_any_seat_selected_id;
				else :
					$seat_id_key = array_rand($total_frees_seats);
					$free_seat_id= $total_frees_seats[$seat_id_key];
				endif;
			}
			else
			{
				$free_seat_id = $total_frees_seats[0];
			}

		/**
		 * Find Seat where other slot are available
		 */

		else:
			
			$BkxBase = new BkxBase('',$base_id);
			$seat_by_base = $BkxBase->get_seat_by_base();

			if(!empty($seat_by_base)){
				$total_free_seat_all  = sizeof($seat_by_base);
				$free_seat_all_arr = $seat_by_base;
			}
			if(!empty($free_seat_all_arr) && sizeof($free_seat_all_arr) > 1)
			{
				if(in_array($_enable_any_seat_selected_id, $free_seat_all_arr)):
					$free_seat_id = $_enable_any_seat_selected_id;
				else :
					$seat_id_key = array_rand($free_seat_all_arr);
					$free_seat_id= $free_seat_all_arr[$seat_id_key];
				endif;
			}
			else
			{
				$free_seat_id = $free_seat_all_arr[0];
			}
		endif;
	else:

		$BkxBase = new BkxBase('',$base_id);
		$seat_by_base = $BkxBase->get_seat_by_base();

		if(!empty($seat_by_base)){
			$total_free_seat_all  = sizeof($seat_by_base);
			$free_seat_all_arr = $seat_by_base;
		}

		if(!empty($free_seat_all_arr) && sizeof($free_seat_all_arr) > 1){

			if(in_array($_enable_any_seat_selected_id, $free_seat_all_arr)):
				$free_seat_id = $_enable_any_seat_selected_id;
			else :
				$seat_id_key = array_rand($free_seat_all_arr);
				$free_seat_id= $free_seat_all_arr[$seat_id_key];
			endif;
		}
		else
		{
			$free_seat_id = $free_seat_all_arr[0];
		}
	endif;

	/*$bookingtimeQuery = "SELECT * FROM bkx_booking_record br inner join bkx_booking_time bt on bt.booking_record_id = br.booking_record_id WHERE br.payment_status = 'Completed' AND br.seat_id = ".trim($free_seat_id)." ".$add_base_clause." AND bt.booking_date = '".trim($bookingdate)."'";*/

	$_SESSION['free_seat_id'] = $free_seat_id;
	$search = array('bookigndate' => $bookingdate , 'seat_id' => $free_seat_id 
		,'status' => $order_statuses,'free_seat_id'=> $free_seat_id ,'display'=>1);
	$BkxBooking =  new BkxBooking();
	$resBookingTime = $BkxBooking->GetBookedRecords($search);

else:
	$seatid = $_POST['seatid'];
	$search = array('bookigndate' => $bookingdate ,'status' => $order_statuses, 'seat_id' => $_POST['seatid'] ,'display'=>1);
	$BkxBooking =  new BkxBooking();
	$resBookingTime = $BkxBooking->GetBookedRecords($search);	
endif;

$bookingSlot = "";
$arrTempExplode = array();
$resBookingTime = apply_filters('customise_check_booking_data',$resBookingTime);

if(!empty($resBookingTime))
{
	foreach($resBookingTime as $temp)
	{
		$bookingSlot .= $temp['full_day'];
	}
	$arrTempExplode = explode(',',$bookingSlot);
	$arrTempExplode = array_unique($arrTempExplode);
}
	//get current booking start time slot
	$bookinghour = explode(':',$start);
	$hours_temp = $bookinghour[0];
	$minutes_temp = $bookinghour[1];
	if($minutes_temp=='00')
	{
		$counter = 0;
	}
	else if($minutes_temp=='15')
	{
		$counter = 1;
	}
	else if($minutes_temp=='30')
	{
		$counter = 2;
	}
	else if($minutes_temp=='45')
	{
		$counter = 3;
	}

$startingSlotNumber = intval($hours_temp)*4+$counter;

$oneDayBooking ;
$res = 1;
$numberOfSlotsToBeBooked = 0;
if(in_array($startingSlotNumber,$arrTempExplode)) //return false if the start time exist in the booked slots
{
	$res = 0;

}
else
{

	$bookingDurationMinutes = ($bookingduration/60);
	$numberOfSlotsToBeBooked = ($bookingDurationMinutes/15);
	$lastSlotNumber = $startingSlotNumber+$numberOfSlotsToBeBooked;
	if($lastSlotNumber<=96)
	{
		$oneDayBooking = 1;
	}
	else
	{
		$oneDayBooking = 0;
	}
	if($oneDayBooking == 1)	
	{
		$arrBookingSlot = range($startingSlotNumber, $lastSlotNumber);
		foreach($arrBookingSlot as $temp)
		{
			if(in_array($temp,$arrTempExplode))
			{
				$res = 0;
				continue;
			}
		}
	}
	else //for multi day booking
	{
		//first check for current day only
		$lastSlotNumberTemp = 96;
		$bookedInCurrentDay = (96 - $startingSlotNumber);
		$numberOfSlotsRemaining = ($numberOfSlotsToBeBooked - $bookedInCurrentDay);
		//for current day slots
		$arrBookingSlot = range($startingSlotNumber, $lastSlotNumberTemp);
		foreach($arrBookingSlot as $temp)
		{
			if(in_array($temp,$arrTempExplode))
			{
				$res = 0;
				continue;
			}
		}
		//for remaining slots
		if($res == 0)
		{

		}
		else if($res == 1)
		{
			$totalDays = floor($numberOfSlotsRemaining /96); //to find number of days 
			$totalDaysMod = $numberOfSlotsRemaining %96; //to find if extend to next day
			if($totalDaysMod > 0) //if goes to next day add 1 to the days value
			{
				$totalDays = $totalDays +1;
			}

			for($i=1;$i<=$totalDays;$i++)
			{
				if($numberOfSlotsRemaining > 0) //to prevent slot to be negative
				{
					$nextDate = date('m/d/Y', strtotime("+$i day", strtotime($bookingdate)));

					/*$bookingtimeQueryNext = "SELECT * FROM `bkx_booking_record` inner join bkx_booking_time on bkx_booking_time.booking_record_id = bkx_booking_record.booking_record_id WHERE  payment_status = 'Completed' AND seat_id = ".trim($seatid)." AND bkx_booking_time.booking_date = '".trim($nextDate)."'";
					$resBookingTimeNext = $wpdb->get_results($bookingtimeQueryNext);*/

					$search = array('bookigndate' => $nextDate , 'seat_id' => $_POST['seatid'] ,'display'=>1);
					$BkxBooking =  new BkxBooking();
					$resBookingTimeNext = $BkxBooking->get_order_time_data('',$search);	


					$bookingSlotNext = "";
					foreach($resBookingTimeNext as $temp)
					{
						$bookingSlotNext .= $temp['full_day'];
					}
					$arrTempExplodeNext = explode(',',$bookingSlotNext);
					$arrTempExplodeNext = array_unique($arrTempExplodeNext);
					$availableSlots = range(1,96);
					$lastSlotRequired;
					if($numberOfSlotsRemaining > 96)
					{
						$lastSlotRequired = 96;
					}
					else
					{
						$lastSlotRequired = $numberOfSlotsRemaining;
					}
					$requiredSlots = range(1,$lastSlotRequired);
					
					foreach($requiredSlots as $temp)
					{
						if(in_array($temp,$arrTempExplodeNext))
						{
							$res = 0;
							continue;
						}
					}
					$numberOfSlotsRemaining = $numberOfSlotsRemaining - 96;//get remaining slots after current loop
				}
			}
		}
	}
}

//echo '<pre>' . print_r($_SESSION['free_seat_id'], true) . '</pre>';
$arr_output['result'] = $res;
$arr_output['no_of_slots'] = $numberOfSlotsToBeBooked;
$arr_output['starting_slot'] = $startingSlotNumber;
$arr_output['booking_date'] = $bookingdate;
$output = json_encode($arr_output);
echo $output;
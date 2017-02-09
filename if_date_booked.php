<?php
	session_start();
	require_once('../../../wp-load.php');
	global $wpdb;

	$bookigndate = '';
	$full_day = '';
	if(isset($_POST['bookigndate']))
	{
		$bookigndate = $_POST['bookigndate'];
		$query_bookigtime = "SELECT * FROM bkx_booking_time WHERE booking_date = '".trim($bookigndate)."'"; 
		$objBookigntime= $wpdb->get_results($query_bookigtime);
		$full_day = $objBookigntime->full_day;
	}
	$booking_slot_temp = '';
	$booking_slot = array();
	if(sizeof($objBookigntime)>0)
	{
		foreach($objBookigntime as $temp)
		{
			$booking_slot_temp .= $temp->full_day;
		}
		
		$booking_slot_arr = explode(',',$booking_slot_temp);
		foreach($booking_slot_arr as $slot)
		{
			if($slot!="")
			{
				array_push($booking_slot,$slot);
			}
		}
		$booking_slot = array_unique($booking_slot); //makes an array unique
		$booking_slot = sort($booking_slot, SORT_NUMERIC);  //sort the array numerically
	}
	
	//print_r($booking_slot);
	$number = range(1,96);
	if(sizeof($booking_slot)>0)
	{
		if(sizeof($booking_slot)==96)
		{
			echo "full";
		}
		else
		{
			echo "partiallyfull";
		}
	}
	else
	{
		echo "empty";
	}
?>
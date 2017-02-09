<?php
session_start();
require_once('../../../wp-load.php');
$id = $_POST['booking_record_id'];
$BkxBooking = new BkxBooking();
$get_order = $BkxBooking->get_order_meta_data($id);

 
$BkxSeatObj    = $get_order['seat_arr']['main_obj'];
$BkxBaseObj    = $get_order['base_arr']['main_obj'];
$BkxBaseMetaData = $BkxBaseObj->meta_data;
$BkxExtraObj   = $get_order['extra_arr'];

$booking_data = array();
$booking_data['seat_name'] = $BkxSeatObj->get_title();
$booking_data['booking_start_date'] = $get_order['booking_start_date'];
$booking_data['booking_end_date'] 	= $get_order['booking_end_date'];
$booking_data['booking_end_date'] 	= $get_order['booking_end_date'];
$booking_data['total_duration'] 	= $get_order['total_duration'];

$booking_data['base_name'] 			= $BkxBaseObj->get_title();
$booking_data['base_time_option'] 	= $BkxBaseMetaData['base_time_option'][0];
$booking_data['extended_base_time'] = $BkxBaseMetaData['extended_base_time'][0];
$booking_data['base_hours'] 		= $BkxBaseMetaData['base_hours'][0];
$booking_data['base_minutes'] 		= $BkxBaseMetaData['base_minutes'][0];
$booking_data['base_day'] 			= $BkxBaseMetaData['base_day'][0];
$booking_data['base_month'] 		= $BkxBaseMetaData['base_month'][0];
$booking_data['seat_color'] 		= "#FFF";

$addition_data = array();

if(!empty($BkxExtraObj))
{
	foreach ($BkxExtraObj as $key => $ExtraObj) {
		$ExtraMetaData = $ExtraObj['main_obj']->meta_data;
		
		$addition_data[$key]['addition_name'] 			= $ExtraObj['title'];
		$addition_data[$key]['addition_time_option'] 	= $ExtraMetaData['addition_time_option'][0];
		$addition_data[$key]['addition_hours'] 			= $ExtraMetaData['addition_hours'][0];
		$addition_data[$key]['addition_minutes'] 		= $ExtraMetaData['addition_minutes'][0];
		$addition_data[$key]['addition_days'] 			= $ExtraMetaData['addition_days'][0];
		$addition_data[$key]['addition_months'] 		= $ExtraMetaData['addition_months'][0];
		$addition_data[$key]['addition_overlap'] 		= $ExtraMetaData['addition_overlap'][0];
	}
}
$arr_booking['booking'] = $booking_data;
$arr_booking['addition'] = $addition_data;
 
$output = json_encode($arr_booking);
echo $output;
die;
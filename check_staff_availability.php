<?php
require_once('../../../wp-load.php');
/**
 * Created by Indianic.
 * User: Divyang Parekh
 * Date: 16/11/15
 * Time: 2:55 PM
 * For : Reassign of Staff Booking process
 * Status 1 = Successfully Reassign
 * Status 4 = something went Wrong
 */
global $wpdb;

require_once('booking_general_functions.php');
$seat_id = $_POST['seat_id'];
$base_id = $_POST['base_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$booking_record_id = $_POST['booking_record_id'];
$status['booking_record_id'] =$booking_record_id;
 
if($booking_record_id != '') :
    update_post_meta($booking_record_id,'seat_id',$seat_id);
    $order_meta_data = get_post_meta($booking_record_id,'order_meta_data',true);

    $base_id = get_post_meta($booking_record_id,'base_id',true);
    $order_meta_data['seat_id'] = $seat_id;
    update_post_meta($booking_record_id,'order_meta_data',$order_meta_data);

    $status['name'] = $order_meta_data['first_name'].' '.$order_meta_data['last_name'];
    
    $status['data'] = 1;
else:
    $status['data'] = 4;
endif;

if(function_exists('reassign_available_emp_list')):
        $get_available_staff	=  reassign_available_emp_list($seat_id,$start_date,$end_date,$base_id);
        if(!empty($get_available_staff)):
                $counter = 0 ;
                foreach($get_available_staff as $get_employee):
                        $status['staff'][$counter]['seat_id'] = $get_employee['id'];
                        $status['staff'][$counter]['name'] = $get_employee['name'];
                        $counter++;
                endforeach;
        endif;
endif;
echo json_encode($status);


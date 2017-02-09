<?php
require_once('../../../wp-load.php');
/**
 * Created By : Divyang Parekh
 * Date : 2-11-2015
 * For : Cancel Booking Functionality
 */
global $wpdb;
$current_user = wp_get_current_user();
if ( !($current_user instanceof WP_User) )
    return;

$roles = $current_user->roles;
$data = $current_user->data;
$booking_record_id = $_POST['booking_record_id'];
$mode = $_POST['mode'];
$current_user_id = $current_user->ID;
$date = date("Y-m-d H:i:s");
$admin_email = get_option( 'admin_email' );

$BkxBookingObj = new BkxBooking('',$booking_record_id);

$BkxBookingData = $BkxBookingObj->get_order_meta_data($booking_record_id);

if(isset($booking_record_id) && $booking_record_id!='' && $mode == 'cancelled'):

    $new_status_obj = get_post_status_object('bkx-cancelled');
    $new_status = $new_status_obj->label;
    $old_status = $BkxBookingObj->get_order_status($booking_record_id);
 
    $update_order = $BkxBookingObj->update_status('cancelled');
    //Add note in comment for cancelled booking
    $BkxBookingObj->add_order_note( sprintf( __( 'Successfully update order from %1$s to %2$s.', 'bookingx' ), $old_status, $new_status ), 0, $manual );

    if ($update_order) :
       
       $objBooking = $BkxBookingObj->get_order_meta_data($booking_record_id);

        /**
         * Send mail to Customer
         */
        $subject ="Booking Id#".$booking_record_id." : Booking Cancelled";
        $to_customer = $objBooking['email'];
        $message_body ='';
        $message_body .= 'Dear '.$objBooking['first_name'].' '.$objBooking['last_name'].",";
        $message_body .= '<p>Your booking has been successfully cancelled. Booking Id : #'.$booking_record_id.'</p>';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        // Additional headers
         $headers .= 'To: '.ucfirst($objBooking['first_name']).' <'.$objBooking['email'].'>' . "\r\n";
         $headers .= 'From: Booking Admin <'.$admin_email.'>' . "\r\n";
        // Mail it
        if($to_customer!=''):  wp_mail($to_customer, $subject, $message_body, $headers);endif;

        /**
         * Send mail to Admin
         */
        $admin_headers  = 'MIME-Version: 1.0' . "\r\n";
        $admin_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        // Additional headers
        $admin_headers .= 'From: Booking Admin <'.$admin_email.'>' . "\r\n";

        $admin_body ='';
        $admin_body .= 'Dear Admin,';
        $admin_body .= '<p>Booking has been cancelled of '.$objBooking['first_name'].' '.$objBooking['last_name'].' and his booking id is #'.$booking_record_id.'</p>';

        if($admin_email!=''):  wp_mail($admin_email, $subject, $admin_body, $admin_headers); endif;

        echo 1;
    endif;
elseif(isset($booking_record_id) && $booking_record_id!='' && $mode == 'completed' && isset( $roles['0'] ) && $roles['0']=='administrator'):
    
    $query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_seat ON bkx_booking_record.seat_id = bkx_seat.seat_id INNER JOIN bkx_base ON bkx_base.base_id = bkx_booking_record.base_id where booking_record_id = '.trim($booking_record_id);
    $objBookingobj = $wpdb->get_results($query);
    $objBooking = $objBookingobj[0];

    $employee_have_booking = $wpdb->get_results("SELECT booking_record_id FROM bkx_booking_record WHERE seat_id = {$objBooking['seat_id']} AND booking_record_id <> {$objBooking['booking_record_id']} AND (order_status = 'completed')  AND ('{$objBooking['booking_start_date']}' BETWEEN booking_start_date AND booking_end_date AND '{$objBooking['booking_end_date']}' BETWEEN booking_start_date AND booking_end_date AND ('{$objBooking['booking_start_date']}' <= booking_start_date AND '{$objBooking['booking_end_date']}' >= booking_end_date))");


    $already_booking_record_id = $employee_have_booking[0]->booking_record_id;
    if(empty($employee_have_booking) && $already_booking_record_id==''):
    $cancel_booking	= array('order_status' => 'completed','payment_status'=>'Completed', 'updated_date' => $date, 'updated_by' => $current_user_id);
    $where = array( 'booking_record_id' => $booking_record_id);
    $wpdb->update( $table_name, $cancel_booking, $where);

    $query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_seat ON bkx_booking_record.seat_id = bkx_seat.seat_id INNER JOIN bkx_base ON bkx_base.base_id = bkx_booking_record.base_id where booking_record_id = '.trim($booking_record_id);
    $objBookingobj = $wpdb->get_results($query);
    $objBooking = $objBookingobj[0];

    /**
     * Send mail to Customer
     */
    $subject ="Booking Id#".$booking_record_id." : Booking Completed";
    $to_customer = $objBooking['email'];
    $message_body ='';
    $message_body .= 'Dear '.$objBooking['first_name'].' '.$objBooking['last_name'].",";
    $message_body .= '<p>Your booking has been successfully Completed. Booking Id : #'.$booking_record_id.'</p>';

    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    // Additional headers
    $headers .= 'To: '.ucfirst($objBooking['first_name']).' <'.$objBooking['email'].'>' . "\r\n";
    $headers .= 'From: Booking Admin <'.$admin_email.'>' . "\r\n";
    // Mail it
    if($to_customer!=''):  wp_mail($to_customer, $subject, $message_body, $headers);endif;

    /**
     * Send mail to Admin
     */

    $admin_headers  = 'MIME-Version: 1.0' . "\r\n";
    $admin_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    // Additional headers
    $admin_headers .= 'From: Booking Admin <'.$admin_email.'>' . "\r\n";


    $admin_body ='';
    $admin_body .= 'Dear Admin,';
    $admin_body .= '<p>Booking has been completed of '.$objBooking['first_name'].' '.$objBooking['last_name'].' and his booking id is #'.$booking_record_id.'</p>';

    if($admin_email!=''):  wp_mail($admin_email, $subject, $admin_body, $admin_headers); endif;
    if ($wpdb->last_error) : echo 0; else : echo 1;endif;
    else:
        echo 2;
    endif;
endif;
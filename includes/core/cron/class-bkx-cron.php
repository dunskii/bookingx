<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class BkxCron
 * Core Class for Bookings Payment and Status check for Unnecessary Hold Slots Time.
 */
class BkxCron
{
    public function __construct() {
        add_action('bkx_check_booking_process', array($this, 'bkx_check_booking_process_call_back'));
    }

    public function bkx_check_booking_process_call_back(){
        $status = array('bkx-pending', 'bkx-missed',);
        $args = array(
            'post_type' => 'bkx_booking',
            'post_status' => $status,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'initiated_for_payment',
                    'value' => 1,
                    'compare' => '='
                )
            ),
        );

        $get_bookings_obj = new WP_Query($args);
        $cancelled_booking = array();
        $success_booking = array();
        if ($get_bookings_obj->have_posts()) {
            while ($get_bookings_obj->have_posts()) : $get_bookings_obj->the_post();
                $booking_id = get_the_ID();
                $payment_data = get_post_meta( $booking_id, 'payment_meta', true);
                if(isset($payment_data['transactionID']) && $payment_data['transactionID'] > 0 ){
                    $success_booking[] = $booking_id;
                }else{
                    $order = new BkxBooking('', $booking_id);
                    $order->update_status('cancelled');
                    $cancelled_booking[] = $booking_id;
                }
            endwhile;
        }

        return array(   'cancelled_bookings' => implode(" , ", $cancelled_booking ),
                        'success_booking' => implode(" , ", $success_booking )
        );
    }

}

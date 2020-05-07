<?php
/**
 * Bookingx Bkx_Ajax_Loader. AJAX Event Handlers.
 *
 * @class   Bkx_Ajax_Loader
 */

defined( 'ABSPATH' ) || exit;

class Bkx_Ajax_Loader
{
    /**
     * Hook in ajax handlers.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
        add_action( 'template_redirect', array( __CLASS__, 'do_bkx_ajax' ), 0 );
        self::add_ajax_events();
    }

    /**
     * Set BKX AJAX constant and headers.
     */
    public static function define_ajax() {
        if ( ! empty( $_GET['bkx-ajax'] ) ) {
            bkx_maybe_define_constant( 'DOING_AJAX', true );
            bkx_maybe_define_constant( 'BKX_DOING_AJAX', true );
            if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
                @ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
            }
            $GLOBALS['wpdb']->hide_errors();
        }
    }

    /**
     * Send headers for BKX Ajax Requests.
     *
     * @since 2.5.0
     */
    private static function bkx_ajax_headers() {
        send_origin_headers();
        @header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
        @header( 'X-Robots-Tag: noindex' );
        send_nosniff_header();
        status_header( 200 );
    }

    /**
     * Check for BKX Ajax request and fire action.
     */
    public static function do_bkx_ajax() {
        global $wp_query;

        if ( ! empty( $_GET['bkx-ajax'] ) ) {
            $wp_query->set( 'bkx-ajax', sanitize_text_field( wp_unslash( $_GET['bkx-ajax'] ) ) );
        }

        $action = $wp_query->get( 'bkx-ajax' );

        if ( $action ) {
            self::bkx_ajax_headers();
            $action = sanitize_text_field( $action );
            do_action( 'bkx_ajax_' . $action );
            wp_die();
        }
    }

    /**get_step_2_details
     * Hook in methods - uses WordPress ajax handlers (admin-ajax).
     */
    public static function add_ajax_events() {
        $ajax_events = array(
            'on_seat_change'                          => true,
            'on_base_change'                          => true,
            'update_booking_total'                    => true,
            'get_step_2_details'                      => true,
            'get_step_3_details'                      => true,
            'get_calendar_availability'               => true,
            'display_availability_slots'              => true,
            'availability_slots_by_dates'             => true,
            'get_verify_slot'                         => true,
            'payment_method_on_change'                => true,
            'get_payment_method'                      => true,
            'book_now'                                => true,
            'back_to_booking_page'                    => true,
            'send_email_receipt'                      => true,
            'check_staff_availability'                => true,
        );

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_bookingx_' . $ajax_event, array( __CLASS__, $ajax_event ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_bookingx_' . $ajax_event, array( __CLASS__, $ajax_event ) );
                add_action( 'bkx_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
            }
        }
    }

    public static function get_endpoint( $request = '' ) {
        return esc_url_raw( apply_filters( 'bookingx_ajax_get_endpoint', add_query_arg( 'bkx-ajax', $request, remove_query_arg( array( '_wpnonce' ), home_url( '/', 'relative' ) ) ), $request ) );
    }

    public static function on_seat_change() {
        check_ajax_referer( 'on-seat-change', 'security' );
        if ( ! empty( $_POST['seat_id'] ) ) {
            $seat_id = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
            $base_obj = new BkxBase();
            $base_html = $base_obj->get_base_options_for_booking_form( $seat_id  );
            echo $base_html;
        }
        wp_die();
    }

    public static function on_base_change() {
        check_ajax_referer( 'on-base-change', 'security' );
        if ( ! empty( $_POST['base_id'] ) ) {
            $base_id = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
            $extra_obj = new BkxExtra();
            $extra_html = $extra_obj->get_extra_options_for_booking_form( $base_id  );
            $BkxBase = new BkxBase( "", $base_id );
            $extended_limit = get_post_meta( $base_id, 'base_extended_limit', true );
            $service_extended_html = "";
            if( $BkxBase->service_extended == "Y" && $extended_limit > 0 ){
                for($e = 0; $e <= $extended_limit; $e++ ){
                    $service_extended_html .= sprintf( __( '<option value="%d"> %d </option>', 'bookingx' ), $e, $e );
                }
            }
            $bkx_booking_style = bkx_crud_option_multisite('bkx_booking_style');
            $booking_style = ( ( !isset($bkx_booking_style) || $bkx_booking_style == "" ) ? "default" : $bkx_booking_style);
            $base_time_option   = get_post_meta( $base_id, 'base_time_option', true );
            $base_day           = get_post_meta( $base_id, 'base_day', true );
            if(isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0 ){
                $booking_style = "default";
            }
            $result['extra_html']       = $extra_html;
            $result['extended']         = $service_extended_html;
            $result['booking_style']    = $booking_style;
            $result['time_option']      = $base_time_option;
            echo json_encode($result);
        }
        wp_die();
    }

    public static function get_step_2_details() {
        check_ajax_referer( 'get-step-2-details', 'security' );
        if ( ! empty( $_POST['base_id'] ) ) {
            $args['seat_id'] = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
            $args['base_id'] = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
            if(isset($_POST['extra_ids']) && !empty($_POST['extra_ids']) && $_POST['extra_ids']!="None"){
                $args['extra_ids'] = array_map( 'absint', (array) isset( $_POST['extra_ids'] ) ? wp_unslash( $_POST['extra_ids'] ) : array() );
            }
            $BkxBooking = new BkxBooking();
            echo $BkxBooking->get_step_2_booking_form_details( $args );
        }
        wp_die();
    }

    public static function get_step_3_details() {
        check_ajax_referer( 'get-step-3-details', 'security' );
        if ( ! empty( $_POST['base_id'] ) ) {
            $args['seat_id'] = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
            $args['base_id'] = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
            $args['service_extend'] = (isset($_POST['service_extend']) && $_POST['service_extend'] > 0 ) ? sanitize_text_field( wp_unslash( $_POST['service_extend'] ) ) : 0;
            if(isset($_POST['extra_id']) && !empty($_POST['extra_id']) && $_POST['extra_id']!="None"){
                $args['extra_ids'] = array_map( 'absint', (array) isset( $_POST['extra_id'] ) ? wp_unslash( $_POST['extra_id'] ) : array() );
            }
            $args['is_admin']     = isset($_POST['is_admin']) ? sanitize_text_field( wp_unslash( $_POST['is_admin'] ) ) : false;
            $args['booking_date'] = sanitize_text_field( wp_unslash( $_POST['booking_date'] ) );
            $args['booking_time'] = sanitize_text_field( wp_unslash( $_POST['booking_time'] ) );
            $args['time_option'] = sanitize_text_field( wp_unslash( $_POST['time_option'] ) );
            $args['days_selected'] = wp_unslash( $_POST['booking_multi_days'] );
            $BkxBooking = new BkxBooking();
            echo $BkxBooking->get_step_3_booking_form_details( $args );
        }
        wp_die();
    }

    public static function get_calendar_availability(){
        check_ajax_referer( 'get-calendar-availability', 'security' );
        $args['seat_id'] = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $args['base_id'] = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
        if(isset($_POST['extra_id']) && !empty($_POST['extra_id']) && $_POST['extra_id']!="None"){
            $args['extra_ids'] = array_map( 'absint', (array) isset( $_POST['extra_id'] ) ? wp_unslash( $_POST['extra_id'] ) : array() );
        }
        $BkxBooking = new BkxBooking();
        $availability = $BkxBooking->get_booking_form_calendar_availability( $args );
        echo json_encode($availability);
        wp_die();
    }

    public static function update_booking_total() {
        check_ajax_referer( 'update-booking-total', 'security' );
        $base_id    = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
        $seat_id    = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $service_extend    = sanitize_text_field( wp_unslash( $_POST['service_extend'] ) );
        $selected_ids['base_id'] = $base_id;
        $selected_ids['seat_id'] = $seat_id;
        $selected_ids['service_extend'] = ( isset($service_extend) && $service_extend > 0 ? $service_extend : 0);
        $extra_ids = "";
        if($_POST['extra_ids'] != "None"){
            $extra_ids = array_map( 'absint', (array) isset( $_POST['extra_ids'] ) ? wp_unslash( $_POST['extra_ids'] ) : array() );
        }
        $selected_ids['extra_ids'] = $extra_ids;
        $BkxBooking = new BkxBooking();
        $generate_total = $BkxBooking->booking_form_generate_total_formatted( $selected_ids );
        echo json_encode($generate_total);
        wp_die();
    }

    public static function availability_slots_by_dates(){
        check_ajax_referer( 'display-availability-slots-by-dates', 'security' );

        $BkxBooking = new BkxBooking();
        $args['seat_id'] = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $args['base_id'] = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
        $args['type']   = sanitize_text_field( wp_unslash( $_POST['type'] ) );
        if(isset($_POST['extra_id']) && !empty($_POST['extra_id']) && $_POST['extra_id']!="None"){
            $args['extra_ids'] = array_map( 'absint', (array) isset( $_POST['extra_id'] ) ? wp_unslash( $_POST['extra_id'] ) : array() );
        }
        $operation_type = "+7 day";
        $end_date =  date('Y-m-d', strtotime( $operation_type ));
        $args['booking_date'] = sanitize_text_field( wp_unslash( $_POST['booking_date'] ) );
        $get_date_range = bkx_getDatesFromRange( $args['booking_date'],$end_date, 'Y-m-d' ); //2019-6-3
       // echo '<pre>',print_r($get_date_range,1),'</pre>';die;
        if(isset($args['type']) && $args['type'] != "" ){
            if( $args['type'] == 'next'  ){
                $operation_type =  "+4 day" ;
                $end_date =  date('Y-m-d', strtotime($args['booking_date'].$operation_type));
                $get_date_range = bkx_getDatesFromRange( $args['booking_date'],$end_date, 'Y-m-d' ); //2019-6-3
            }

            if( $args['type'] == 'prev'  ){
                $operation_type =  "-3 day" ;
                $end_date =  date('Y-m-d', strtotime($args['booking_date'].$operation_type));
                if ($end_date >= date('Y-m-d') ) {
                    $get_date_range = bkx_getDatesFromRange( $end_date, $args['booking_date'], 'Y-m-d' ); //2019-6-3
                }else{
                    $get_date_range = bkx_getDatesFromRange( date('Y-m-d'), date('Y-m-d', strtotime($args['booking_date']."+ 3 day")), 'Y-m-d' ); //2019-6-3
                }
            }
        }
        $availability_slots_body = "";
        if(!empty($get_date_range)){
            foreach ($get_date_range as $range ){
                ob_start();
                $args['booking_date'] = $range;
                $BkxBooking->display_availability_slots_html( $args );
                $availability_slots = ob_get_contents();
                ob_end_clean();
                $availability_slots_body .= '<div class="item"><div class="select-time"><table class="table table-bordered booking-slots '.$range.'" data-total_slots="0" data-starting_slot="0" data-date="'.$range.'">'.$availability_slots.'</table></div></div>';
            }
        }
        echo $availability_slots_body;
        wp_die();
    }
    public static function display_availability_slots(){
        check_ajax_referer( 'display-availability-slots', 'security' );
        $BkxBooking = new BkxBooking();
        $args['seat_id'] = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $args['base_id'] = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
        if(isset($_POST['extra_id']) && !empty($_POST['extra_id']) && $_POST['extra_id']!="None"){
            $args['extra_ids'] = array_map( 'absint', (array) isset( $_POST['extra_id'] ) ? wp_unslash( $_POST['extra_id'] ) : array() );
        }
        $args['booking_date'] = sanitize_text_field( wp_unslash( $_POST['booking_date'] ) );
        //$bkx_booking_style = bkx_crud_option_multisite('bkx_booking_style');
        //$booking_style = ( ( !isset($bkx_booking_style) || $bkx_booking_style == "" ) ? "default" : $bkx_booking_style);
        $base_time_option   = get_post_meta( $args['base_id'], 'base_time_option', true );
        $base_day           = get_post_meta( $args['base_id'], 'base_day', true );
        $extra_day = 0;
        if(!empty($args['extra_ids'])){
            $extra_ids  = $args['extra_ids'];
            if(!empty($extra_ids) && $extra_ids != "None"){
                foreach ($extra_ids as $extra_id ){
                    $BkxExtra = new BkxExtra("", $extra_id );
                    $extra_time = $BkxExtra->get_time();
                    if($extra_time['type'] == 'D'){
                        $seconds = $extra_time['in_sec'] / 60 * 60 ;
                        $dt1 = new DateTime("@0");
                        $dt2 = new DateTime("@$seconds");
                        $extra_day +=  $dt1->diff($dt2)->d;
                    }
                }
            }
        }

        $allowed[] = 0;
        $availability_slot_flag = true;
        if( isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0 ){
            $start_date = $args['booking_date'];
            $base_day = ($base_day > 1) ? ($base_day - 1) : 0;
            if($extra_day > 0 ){
                $base_day += $extra_day;
            }
            $end_date =  date('Y-m-d', strtotime($start_date. " + {$base_day} days"));
            $get_date_range = bkx_getDatesFromRange( $start_date, $end_date, 'Y-m-d' ); //2019-6-3
            $availability   = $BkxBooking->get_booking_form_calendar_availability($args);
            if(!empty($get_date_range) && $availability_slot_flag == true){
                $allowed = array();
                foreach ($get_date_range as $date ){
                    $args['booking_date'] = $date;
                    if(!empty($availability['unavailable_days']) && in_array( date("m/d/Y", strtotime( $date ) ), $availability['unavailable_days'])){
                        $already_booked = array(0);
                    }else{
                        $already_booked = $BkxBooking->GetBookedRecords( $args );
                    }
                    $allowed[] = empty($already_booked) ? 1 : 0 ;
                }
            }
            $args['days_range'] = bkx_getDatesFromRange( $start_date, $end_date, 'Y-m-j' ); //2019-6-3;
        }
        $args['allowed_day_book'] = $allowed;
        echo $availability_slots =  $BkxBooking->display_availability_slots_html( $args );
        //echo '<pre>', print_r($availability_slots, 1), '</pre>';
        wp_die();
    }

    public static function get_verify_slot(){
        check_ajax_referer( 'get-verify-slot', 'security' );
        $BkxBooking = new BkxBooking();
        $args['seat_id'] = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $args['base_id'] = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
        if(isset($_POST['extra_id']) && !empty($_POST['extra_id']) && $_POST['extra_id']!="None"){
            $args['extra_ids'] = array_map( 'absint', (array) isset( $_POST['extra_id'] ) ? wp_unslash( $_POST['extra_id'] ) : array() );
        }
        $args['date'] = sanitize_text_field( wp_unslash( $_POST['date'] ) );
        $args['slot'] = sanitize_text_field( wp_unslash( $_POST['slot'] ) );
        $args['time'] = sanitize_text_field( wp_unslash( $_POST['time'] ) );
        $BkxBooking->get_verify_slot( $args );
    }

    public static function get_payment_method(){
        check_ajax_referer( 'get-payment-method', 'security' );
        $BkxPaymentCore = new BkxPaymentCore();
        $bkx_get_available_gateways = $BkxPaymentCore->bkx_get_available_gateways();
        $prepayment = false;
        if(isset($_POST['seat_id']) && $_POST['seat_id'] != 0 ){
            $seat_id = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
            $seatIsPrePayment = get_post_meta( $seat_id, 'seatIsPrePayment', true );
            $prepayment = isset( $seatIsPrePayment) && esc_attr( $seatIsPrePayment ) == 'Y' ?  true : false;
        }
        if(!empty($bkx_get_available_gateways)){
            echo $BkxPaymentCore->bkx_get_available_gateways_html( $prepayment );
        }
        wp_die();
    }

    public static function payment_method_on_change(){
        check_ajax_referer( 'payment-method-on-change', 'security' );
        $result = array();
        $BkxPaymentCore = new BkxPaymentCore();
        $bkx_get_available_gateways = $BkxPaymentCore->bkx_get_available_gateways();
        $payment_id = sanitize_text_field( wp_unslash( $_POST['payment_id'] ) );
        if(!empty($payment_id)){
            $gateway = $bkx_get_available_gateways[$payment_id];
            $gateway_obj = $gateway['class'];
            $result['image_url']    =  isset($gateway_obj->image_url) ? $gateway_obj->image_url : "";
            $result['title']        =  $gateway['title'];
        }
        echo  json_encode($result);
        wp_die();
    }

    public static function book_now(){

        check_ajax_referer( 'book-now', 'security' );
        $Bkxbooking = new BkxBooking();
        $booking = array();

        $booking['meta_data']['redirect_to'] = "";
        $args['seat_id']        = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $args['base_id']        = sanitize_text_field( wp_unslash( $_POST['base_id'] ) );
        $args['extra_ids']      = sanitize_text_field( wp_unslash( $_POST['extra_id'] ) );
        $args['service_extend'] = sanitize_text_field( wp_unslash( $_POST['service_extend'] ) );
        $args['date']           = sanitize_text_field( wp_unslash( $_POST['date'] ) );
        $args['slot']           = sanitize_text_field( wp_unslash( $_POST['starting_slot'] ) );
        $args['time']           = sanitize_text_field( wp_unslash( $_POST['booking_time'] ) );
        $args['time_option']    = sanitize_text_field( wp_unslash( $_POST['time_option'] ) );
        $args['booking_multi_days']  = wp_unslash( $_POST['booking_multi_days'] );
        //echo '<pre>',print_r($args,1),'</pre>';die;
        $get_verify_slot = json_decode( $Bkxbooking->get_verify_slot( $args, false ) );
        if(!empty($get_verify_slot) && $get_verify_slot->result == 1 ||  !empty($args['booking_multi_days'])){
            $booking = $Bkxbooking->generate_order($_POST, null, true);
            //send email that edit booking confirmed
            if(isset($_POST['edit_booking']) && $_POST['edit_booking'] == true ){
                $Bkxbooking->booking_email($booking['meta_data']['order_id'], 'edit');
                $Bkxbooking->booking_email($booking['meta_data']['order_id'], 'customer_edit');
            }else{
                //send email that new booking confirmed
                $Bkxbooking->booking_email($booking['meta_data']['order_id'], 'pending');
                $Bkxbooking->booking_email($booking['meta_data']['order_id'], 'customer_pending');
            }

            $pos = strpos($booking['meta_data']['last_page_url'], "post-new.php");
            if ($pos !== false) {
                $booking['meta_data']['redirect_to'] = get_edit_post_link( $booking['meta_data']['order_id'],'&' );
            }
        }
        echo ( !empty($booking['meta_data']['order_id']) ? json_encode($booking) : "NORF" );
        wp_die();
    }

    public static function back_to_booking_page(){
        check_ajax_referer( 'back-to-booking-page', 'security' );
        $bkx_set_booking_page  = bkx_crud_option_multisite("bkx_set_booking_page");
        echo ( ( isset( $bkx_set_booking_page ) && $bkx_set_booking_page!="" ) ? get_permalink( $bkx_set_booking_page ) :  "NORF" );
        wp_die();
    }

    public static function send_email_receipt(){
        check_ajax_referer( 'send-email-receipt', 'security' );
        $order_id = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );
        $BkxBooking = new BkxBooking();
        echo ( isset($order_id) && $order_id != "" ? $BkxBooking->bkx_order_edit_status( $order_id, "customer_pending") : "NORF" );
        wp_die();
    }

    public static function check_staff_availability(){
        check_ajax_referer( 'check-staff-availability', 'security' );
        if(is_multisite()):
            $blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
            switch_to_blog($blog_id);
        endif;
        $seat_id    = sanitize_text_field( wp_unslash( $_POST['seat_id'] ) );
        $booking_record_id = sanitize_text_field( wp_unslash( $_POST['booking_record_id'] ) );
        $status['booking_record_id'] = $booking_record_id;
        $BkxBookingObj = new BkxBooking('', $booking_record_id);
        $order_meta_data = $BkxBookingObj->get_order_meta_data($booking_record_id);
        $base_id = sanitize_text_field($order_meta_data['base_id']);
        $start_date = sanitize_text_field($order_meta_data['booking_start_date']);
        $end_date = sanitize_text_field($order_meta_data['booking_end_date']);
        $old_seat_id = $order_meta_data['seat_id'];
        if ($booking_record_id != '') :
            update_post_meta($booking_record_id, 'seat_id', $seat_id);
            $order_meta_data = get_post_meta($booking_record_id, 'order_meta_data', true);
            $base_id = get_post_meta($booking_record_id, 'base_id', true);
            $order_meta_data['seat_id'] = $seat_id;
            update_post_meta($booking_record_id, 'order_meta_data', $order_meta_data);
            $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
            $old_seat = get_the_title($old_seat_id);
            $new_seat = get_the_title($seat_id);
            $BkxBookingObj->add_order_note(sprintf(__('Successfully updated %1$s from %2$s to %3$s.', 'bookingx'), $alias_seat, $old_seat, $new_seat), 0);
            $status['name'] = $order_meta_data['first_name'] . ' ' . $order_meta_data['last_name'];
            $status['data'] = 1;
        else:
            $status['data'] = 4;
        endif;
        if (function_exists('bkx_reassign_available_emp_list')):
            $get_available_staff = bkx_reassign_available_emp_list($seat_id, $start_date, $end_date, $base_id);
            if (!empty($get_available_staff)):
                $counter = 0;
                foreach ($get_available_staff as $get_employee):
                    $status['staff'][$counter]['seat_id'] = $get_employee['id'];
                    $status['staff'][$counter]['name'] = $get_employee['name'];
                    $counter++;
                endforeach;
            endif;
        endif;
        echo json_encode($status);
        wp_die();
    }
}
Bkx_Ajax_Loader::init();
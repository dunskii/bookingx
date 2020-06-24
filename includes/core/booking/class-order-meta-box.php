<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class Bkx_Meta_Boxes
 */
class Bkx_Meta_Boxes
{
    /**
     * @var string
     */
    public $post_type = 'bkx_booking';
    /**
     * @var array
     */
    protected static $billing_fields = array();

    /**
     * Bkx_Meta_Boxes constructor.
     */
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'remove_meta_boxes'), 10);
        add_action('add_meta_boxes', array($this, 'rename_meta_boxes'), 20);
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        //add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
    }

    /**
     *Add Meta Box For bkx_booking Post Type
     */
    public function add_meta_boxes()
    {
        global $post;
        $order_type_object = get_post_type_object($this->post_type);
        $order_id = get_post_meta($post->ID, 'order_id', true);
        add_meta_box('bkx-general-data', sprintf(__('%s Data', 'bookingx'), $order_type_object->labels->singular_name),
            array($this, 'bookingx_output'), $this->post_type, 'normal', 'high');
        if (isset($order_id) && $order_id != '') {
            add_meta_box('bkx-order_note', sprintf(__('%s #%s Notes', 'bookingx'), $order_type_object->labels->singular_name, $post->ID), array($this, 'bookingx_note_output'), $this->post_type, 'side', 'high');
        }
        add_meta_box('bkx-order_reassign', sprintf(__('%s Reassign', 'bookingx'), $order_type_object->labels->singular_name), array($this, 'bookingx_reassign_output'), $this->post_type, 'side');
    }

    /**
     * Rename Meta Boxes
     */
    public function rename_meta_boxes()
    {
        global $post;
        // Comments/Reviews
        if (isset($post) && ('publish' == $post->post_status || 'private' == $post->post_status)) {
            remove_meta_box('commentsdiv', 'product', 'normal');
            add_meta_box('commentsdiv', __('Reviews', 'Bookingx'), 'post_comment_meta_box', 'product', 'normal');
        }
    }

    /**
     * No Require below Meta Boxes Remove
     */
    public function remove_meta_boxes()
    {
        remove_meta_box('commentsdiv', $this->post_type, 'normal');
        remove_meta_box('commentstatusdiv', $this->post_type, 'normal');
        remove_meta_box('slugdiv', $this->post_type, 'normal');
        remove_meta_box('submitdiv', $this->post_type, 'side');
    }

    /**
     * @param $post
     * @param null $return_type
     * @return string|void
     * @throws Exception
     */
    public function bookingx_output($post, $return_type = null)
    {
        $order_id = $post->ID;
        if (empty($post->ID))
            return;
        $seat_id = get_post_meta($order_id, 'seat_id', true);
        if (isset($seat_id) && $seat_id != "") {
            $order_summary = "";
            $orderObj = new BkxBooking();
            $order_meta = $orderObj->get_order_meta_data($post->ID);
            wp_nonce_field('bookingx_save_data', 'bookingx_meta_nonce');
            $extra_id = get_post_meta($post->ID, 'addition_ids', true);
            $extra_id = rtrim($extra_id, ",");
            $booking_start_date = date('m/d/Y', strtotime($order_meta['booking_start_date']));

            $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');
            $base_alias = bkx_crud_option_multisite('bkx_alias_base');
            $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');

            $payment_status = '';
            $check_total_payment = 0;
            $payment_meta = get_post_meta($order_id, 'payment_meta', true);
            if (!empty($payment_meta) && is_array($payment_meta)) {
                $payment_status = $payment_meta['payment_status'];
                $check_total_payment = $payment_meta['pay_amt'];
            }
            $payment_status = ($payment_status) ? $payment_status : 'Pending';
            $seat_base_edit_mode = 0;
            if ($payment_status == 'Completed' || $payment_status == 'completed') {
                $seat_base_edit_mode = 1;
            }

            if (isset($check_total_payment) && $check_total_payment != '' && $check_total_payment != 0) {
                $check_remaining_payment = $order_meta['total_price'] - $check_total_payment;
            }

            $search['search_by'] = 'future';
            $search['search_date'] = date('Y-m-d');
            $booked_days = '';
            $BookedRecords = $orderObj->GetBookedRecordsByUser($search);
            if (!empty($BookedRecords)) {
                foreach ($BookedRecords as $bookings) :
                    if (isset($bookings['booking_multi_days']) && !empty($bookings['booking_multi_days'])) :
                        $booking_multi_days = $bookings['booking_multi_days'];
                        $booking_multi_days_arr = explode(",", $booking_multi_days);
                        if (!empty($booking_multi_days_arr)) {
                            $booking_multi_days_data = !empty($booking_multi_days_arr[0]) ? $booking_multi_days_arr[0] : "";
                            $booking_multi_days_data_1 = !empty($booking_multi_days_arr[1]) ? $booking_multi_days_arr[1] : "";
                            $booking_multi_days_range = bkx_getDatesFromRange($booking_multi_days_data, $booking_multi_days_data_1, "m/d/Y");
                            if (!empty($booking_multi_days_range)) {
                                foreach ($booking_multi_days_range as $multi_days) {
                                    $booked_days .= $multi_days . ",";
                                }
                            }
                        }
                    endif;
                    $booked_start_dates = date('m/d/Y', strtotime($bookings['booking_start_date']));
                    $booked_days .= $booked_start_dates . ",";
                endforeach;
                $booked_days = rtrim($booked_days, ',');
            } else {
                $booked_days = 0;
            }
            $booked_days_arr = explode(",", $booked_days);
            $booked_days_filtered = implode(",", array_unique($booked_days_arr));
            //print_r($order_meta['extra_arr']);
            $extra_data = sprintf(__('<p>%s service\'s :', 'Bookingx'), $addition_alias);
            if (!empty($order_meta['extra_arr'])) {
                foreach ($order_meta['extra_arr'] as $key => $extra_arr) {
                    $extra_data .= sprintf(__('&nbsp;<a href="%s" target="_blank">%s</a>&nbsp;', 'Bookingx'), $extra_arr['permalink'], $extra_arr['title'], $extra_arr['title']);
                }
            }
            $BkxBaseObj = new BkxBase('', $order_meta['base_id']);
            $base_time = $BkxBaseObj->get_time($order_meta['base_id']);
            $base_time_option = get_post_meta($post->ID, 'base_time_option', true);
            $base_time_option = (isset($base_time_option) && $base_time_option != "") ? $base_time_option : "H";
            $total_time = "-";
            $end_time = date('H:i', strtotime($order_meta['booking_end_date']));
            $date_format = bkx_crud_option_multisite('date_format');
            if (isset($base_time_option) && $base_time_option == "H") {
                $total_time = sprintf(__('%s', 'bookingx'), date('h:i A', strtotime($order_meta['booking_start_date'])), date('h:i A ', strtotime($order_meta['booking_end_date'])));
                //$duration   = sprintf( __( '%s', 'bookingx' ), $order_meta['total_duration'] );
                $booking_duration = str_replace('(', '', $order_meta['total_duration']);
                $booking_duration = str_replace(')', '', $booking_duration);
                $duration = sprintf(__('%s', 'bookingx'), $booking_duration);
                $date_data = sprintf(__('%s', 'bookingx'), date($date_format, strtotime($order_meta['booking_date'])));
            } else {
                $days_selected = get_post_meta($post->ID, 'booking_multi_days', true);
                if (!empty($days_selected)) {
                    $last_key = sizeof($days_selected) - 1;
                    $start_date = date('F d, Y', strtotime($days_selected[0]));
                    $end_date = date('F d, Y', strtotime($days_selected[$last_key]));
                    $date_data = "{$start_date} To {$end_date}";
                    $duration = sprintf(__('%s', 'bookingx'), $base_time['formatted']);
                }
            }

            if (isset($order_meta['seat_id']) && $order_meta['seat_id'] != '') {
                $order_summary = sprintf('<div class="bkx-order_summary_full">', 'Bookingx');
                $order_summary .= sprintf(__('<h3>Booking #%d details</h3>', 'Bookingx'), $post->ID);
                $order_summary .= sprintf('<div class="bkx-general_full">', 'Bookingx');
                //$order_summary .= sprintf( __('<h4>Customer Information</h4>','Bookingx'));
                $order_summary .= sprintf(__('<p>Full Name : <span id="bkx_fname"> %s </span><span id="bkx_lname"> %s </span></p>', 'Bookingx'), $order_meta['first_name'], $order_meta['last_name']);
                $order_summary .= sprintf(__('<p>Phone : <a href="callto:%s" id="bkx_phone"><span id="bkx_phone">%s</span></a></p>', 'Bookingx'), $order_meta['phone'], $order_meta['phone']);
                $order_summary .= sprintf(__('<p>Email : <a href="mailto:%s" id="bkx_email"><span id="bkx_email">%s</span></a></p>', 'Bookingx'), $order_meta['email'], $order_meta['email']);
                $order_summary .= sprintf(__('<p><span id="bkx_address"> %s %s </span></p>', 'Bookingx'), $order_meta['city'], $order_meta['state']);
                $order_summary .= sprintf(__('<p><span id="bkx_postcode"> %s </span> </p>', 'Bookingx'), $order_meta['postcode']);
                $order_summary .= sprintf('</div>', 'Bookingx');

                $order_summary .= sprintf('<div class="bkx-general_full">', 'Bookingx');
                //$order_summary .= sprintf( __('<h4>Booking Details</h4>','Bookingx'));
                $order_summary .= sprintf(__('<p>Booking Date : <span id="bkx_booking_date">%s </span> </p>', 'Bookingx'), $date_data);
                if (isset($base_time_option) && $base_time_option == "H") {
                    $order_summary .= sprintf(__('<p> Time : <span id="bkx_booking_time">%s </span> </p>', 'Bookingx'), "{$order_meta['booking_time_from']} - {$end_time}");
                }
                $order_summary .= sprintf(__('<p> Duration : <span id="bkx_booking_duration">%s </span> </p>', 'Bookingx'), $duration);
                $order_summary .= sprintf(__('<p>Status : <span id="bkx_booking_status">%s </span> </p>', 'Bookingx'), $orderObj->get_order_status($post->ID));
                $order_summary .= sprintf(__('<p>Total : %s<span id="bkx_booking_total">%s </span> </p> </p>', 'Bookingx'), bkx_get_current_currency(), $order_meta['total_price']);
                $payment_source = $order_meta['bkx_payment_gateway_method'];
                if (!empty($payment_source) && $payment_source != "cash" && $payment_source != "N") {
                    $BkxPaymentCore = new BkxPaymentCore();
                    $bkx_get_available_gateways = $BkxPaymentCore->bkx_get_available_gateways();
                    $gateways_name = $bkx_get_available_gateways[$payment_source]['title'];
                    $payment_source = sprintf(__("%s", 'Bookingx'), $gateways_name);
                } else {
                    $payment_source = sprintf(__("%s", 'Bookingx'), "Offline");
                }
                $order_summary .= sprintf(__('<p id="bkx_payment_status">Payment : %s </p>', 'Bookingx'), $payment_status);
                if ($payment_status == 'Completed' || $payment_status == 'completed') {
                    $order_summary .= sprintf(__('<p>Payment By : %s </p>', 'Bookingx'), $payment_source);
                    $order_summary .= sprintf(__('<p>Transaction Id : %s </p>', 'Bookingx'), $payment_meta['transactionID']);
                    if (isset($check_remaining_payment) && $check_remaining_payment != 0 && $check_remaining_payment != '') {
                        $order_summary .= sprintf(__('<p> <b> Note : %s%s %s  </b> </p>', 'Bookingx'), bkx_get_current_currency(), $check_remaining_payment, 'payment will be made when the customer comes for the booking');
                    }
                } else {
                    $order_summary .= sprintf(__('<p> Note  : %s </p>', 'Bookingx'), "payment will be made when the customer comes for the booking");
                }
                $order_summary .= sprintf('</div>', 'Bookingx');
                $extra_data = sprintf(__('<p id="bkx_extra_name">%s service\'s :', 'Bookingx'), $addition_alias);
                $extra_data .= '<span id="bkx_extra_data">';
                if (!empty($order_meta['extra_arr'])) {
                    foreach ($order_meta['extra_arr'] as $key => $extra_arr) {
                        $extra_data .= sprintf(__('&nbsp;<a href="%s" target="_blank">%s</a>&nbsp;', 'Bookingx'), $extra_arr['permalink'], $extra_arr['title'], $extra_arr['title']);
                    }
                } else {
                    $extra_data .= sprintf('&nbsp;None', 'Bookingx');
                }
                $extra_data .= '</span>';
                $order_summary .= sprintf('<div class="bkx-general_full">', 'Bookingx');

                $order_summary .= sprintf(__('<p id="bkx_seat_name">%s Name : <a href="%s" title="%s">%s</a></p>', 'Bookingx'), $seat_alias, $order_meta['seat_arr']['permalink'], $order_meta['seat_arr']['title'], $order_meta['seat_arr']['title']);
                $order_summary .= sprintf(__('<p id="bkx_base_name">%s Name : <a href="%s">%s</a> </p>', 'Bookingx'), $base_alias, $order_meta['base_arr']['permalink'], $order_meta['base_arr']['title'], $order_meta['base_arr']['title']);
                $order_summary .= $extra_data;
                $order_summary .= sprintf('</div>', 'Bookingx');
                $order_summary .= sprintf('<div style="clear:left;">&nbsp;</div>', 'Bookingx');
                $order_summary .= sprintf('</div>', 'Bookingx');
            }
            if ($return_type == 'ajax') {
                return $order_summary;
            }
            echo $order_summary;
            echo do_shortcode('[bkx_booking_form order_post_id=' . $post->ID . ']');
        } else {
            echo do_shortcode('[bkx_booking_form]');
        }
    }

    /**
     * @param $post
     * @param null $return_type
     * @return string|void
     * @throws Exception
     */
    public function bookingx_note_output($post, $return_type = null)
    {
        if (empty($post->ID))
            return;

        $orderObj = new BkxBooking();
        $order_meta = $orderObj->get_order_meta_data($post->ID);

        if (empty($order_meta['seat_id'])) {
            echo '<style>#bkx-order_summary { display : none; };</style>';
            return;
        }
        $script = '<script type="text/javascript"> jQuery(document).ready(function(){ jQuery( "#bkx_id_add_custom_note" ).on( "click", function() {
		jQuery("#bkx_add_custom_note_loader").show();
		jQuery("#bkx_id_add_custom_note").hide();
		var booking_id = jQuery( "#bkx_booking_id" ).val();
		var bkx_custom_note = jQuery( "#bkx_custom_note" ).val();
		if(bkx_custom_note == ""){
			jQuery("#bkx_add_custom_note_err").html("Please add note.");
			jQuery("#bkx_add_custom_note_loader").hide();
			jQuery("#bkx_id_add_custom_note").show();
			return false;
		}
		if(confirm("Are you sure want to add note?")){
			jQuery("#bkx_add_custom_note_err").hide();
			var data = {
	            "action": "bkx_action_add_custom_note",
	            "bkx_custom_note": bkx_custom_note,
	            "booking_id" : booking_id
	        };
	        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	        jQuery.post(ajaxurl, data, function(response) {
	        	jQuery("#bkx_add_custom_note_loader").hide();
	        	jQuery("#bkx_id_add_custom_note").hide();
	             location.reload();
	        });
		}		
	});});</script>';
        $order_summary = sprintf('<div class="bkx-order_summary">', 'Bookingx');
        $order_summary .= sprintf(__('<h3>Booking #%d Notes</h3>', 'Bookingx'), $post->ID);
        $order_summary .= sprintf(__('<span id="bkx_add_custom_note_err"></span><textarea rows="4" id="bkx_custom_note"></textarea>', 'Bookingx'), '');
        $order_summary .= sprintf(__('<input type="hidden" id="bkx_booking_id" value="%d">', 'Bookingx'), $post->ID);

        $order_summary .= sprintf(__('<a style="cursor:pointer;" id="bkx_id_add_custom_note" name="bkx_add_custom_note"> Add note </a> <span id="bkx_add_custom_note_loader" style="display:none;"> Please wait.. </span>', 'Bookingx'), '');
        $order_summary .= $orderObj->get_booking_notes($post->ID);
        $order_summary .= sprintf('</div>', 'Bookingx');
        $order_summary .= $script;
        if ($return_type == 'ajax') {
            return $order_summary;
        }
        echo $order_summary;
    }

    /**
     * @param $post
     * @param null $return_type
     * @return string|void
     * @throws Exception
     */
    public function bookingx_reassign_output($post, $return_type = null)
    {
        if (empty($post->ID))
            return;
        $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');
        $orderObj = new BkxBooking();
        $order_meta = $orderObj->get_order_meta_data($post->ID);

        if (empty($order_meta['seat_id'])) {
            echo '<style>#bkx-order_reassign { display : none; };</style>';
            return;
        }
        $reassign_booking = '';
        $reassign_booking .= sprintf(__('<div class="bkx-order_summary"><p>Current %s assign : <a href="%s" title="%s">%s</a></p>', 'Bookingx'),
            $seat_alias, $order_meta['seat_arr']['permalink'],
            $order_meta['seat_arr']['title'],
            $order_meta['seat_arr']['title']);
        $source = $order_meta['booking_start_date'];
        $date = new DateTime($source);
        $booking_start_date = addslashes($date->format('m/d/Y'));
        $start_time = addslashes($date->format('H:i:s'));
        $order_start_date = isset($order_meta['start_date']) ? $order_meta['start_date'] : "";
        $order_end_date = isset($order_meta['end_date']) ? $order_meta['end_date'] : "";
        $order_base_id = isset($order_meta['base_id']) ? $order_meta['base_id'] : "";
        $get_available_staff = bkx_reassign_available_emp_list($order_meta['seat_id'], $order_start_date, $order_end_date, $order_base_id);
        $reassign_booking .= '<b>Reassign ' . $seat_alias . ' to booking: </b>
			 <select id="id_reassign_booking_' . $order_meta['order_id'] . '" name="reassign_booking" data-order-id="' . $order_meta['order_id'] . '">
					  <option value=""> --Reassign ' . $seat_alias . ' --</option>';
        if (!empty($get_available_staff)):
            foreach ($get_available_staff as $staff):
                if (isset($staff['name']) && $staff['name'] != "") :
                    $reassign_booking .= ' <option value="' . $staff['id'] . '">' . ucwords($staff['name']) . ' </option>';
                endif;
            endforeach;
        endif;
        $reassign_booking .= '</select></div>';
        if ($return_type == 'ajax') {
            return $reassign_booking;
        } else {
            echo $reassign_booking;
        }
    }
}

new Bkx_Meta_Boxes();
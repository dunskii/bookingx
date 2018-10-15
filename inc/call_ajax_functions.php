<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
add_action('wp_ajax_bkx_action_status', 'bkx_action_status');
/**
 * Update Order Status
 */
function bkx_action_status()
{
    global $wpdb; // this is how you get access to the database
    $bkx_action_status = explode("_", sanitize_text_field($_POST['status']));

    $order_id = $bkx_action_status[0];
    $order_status = $bkx_action_status[1];

    $BkxBooking = new BkxBooking('', $order_id);
    $BkxBooking->update_status($order_status);
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_check_seat_exixts', 'bkx_prefix_ajax_check_seat_exixts');
add_action('wp_ajax_nopriv_check_seat_exixts', 'bkx_prefix_ajax_check_seat_exixts');
/**
 * Check seat user exixts in Database
 */
function bkx_prefix_ajax_check_seat_exixts()
{
    if (is_multisite()) {
        $current_blog_id = get_current_blog_id();
        switch_to_blog($current_blog_id);
    }
    $seatEmail = sanitize_text_field($_POST['email']);
    $user_id = username_exists($seatEmail);
    if (!$user_id && email_exists($seatEmail) == false) {
        $data = 'success';
    } else {
        $data = 'error';
    }
    echo $data;

    wp_die();
}


add_action('wp_ajax_nopriv_get_time_format', 'bkx_ajax_get_time_format');
add_action('wp_ajax_get_time_format', 'bkx_ajax_get_time_format');
/**
 *
 */
function bkx_ajax_get_time_format()
{

    $day_input = sanitize_text_field($_REQUEST['name']);
    if (isset($day_input) && $day_input <= 12) {
        $day_input = sanitize_text_field($_REQUEST['name']);
    } else {
        $day_input = sanitize_text_field($_REQUEST['name'][0]);
    }
    $am_array = array_map(function ($n) {
        return sprintf('%d:00 AM', $n);
    }, range($day_input, $day_input));
    $pm_array = array_map(function ($n) {
        return sprintf('%d:00 PM', $n);
    }, range($day_input, $day_input));
    $days_array_merge = array_merge($am_array, $pm_array);
    $search_result1 = $days_array_merge;

    echo json_encode($search_result1);

    wp_die();
}

add_action('wp_ajax_bookingx_set_as_any_seat', 'bkx_bookingx_set_as_any_seat_callback');
add_action('wp_ajax_nopriv_bookingx_set_as_any_seat', 'bkx_bookingx_set_as_any_seat_callback');

/**
 *
 */
function bkx_bookingx_set_as_any_seat_callback()
{

    $seat_id = absint(sanitize_text_field($_GET['seat_id']));

    if ('bkx_seat' === get_post_type($seat_id)) {
        bkx_crud_option_multisite('select_default_seat', $seat_id, 'update');
    }
    wp_safe_redirect(wp_get_referer() ? remove_query_arg(array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer()) : admin_url('edit.php?post_type=bkx_seat'));
    wp_die();
}

add_action('wp_ajax_get_user', 'bkx_get_user');
/**
 *
 */
function bkx_get_user()
{
    global $wpdb; // this is how you get access to the database
    $role = sanitize_text_field($_POST['data']);
    $user_query = new WP_User_Query(array(
        'role' => $role
    ));
    $free_user_obj = '';
    $all_users = $user_query->results;

    if (!empty($all_users) && !is_wp_error($all_users)) {
        foreach ($all_users as $key => $user) {
            $user_id = $user->data->ID;

            $args = array(
                'post_type' => 'bkx_seat',
                'suppress_filters' => false,
                'meta_query' => array(
                    array(
                        'key' => 'seat_wp_user_id',
                        'value' => $user_id,
                        'compare' => '=',
                    )
                ),
            );

            $seat_obj = new WP_Query($args);

            if (!empty($seat_obj) && !is_wp_error($seat_obj)) {
                if (!$seat_obj->have_posts()) :
                    $free_user_obj[] = get_user_by('id', $user_id);
                endif;
            }
        }
    }
    echo json_encode($free_user_obj);

    wp_die(); // this is required to return a proper result
}

add_action('wp_ajax_bkx_action_view_summary', 'bkx_action_view_summary_callback');
add_action('wp_ajax_nopriv_bkx_action_view_summary', 'bkx_action_view_summary_callback');

/**
 *
 */
function bkx_action_view_summary_callback()
{
    $post_id = sanitize_text_field($_POST['post_id']);

    if (empty($post_id))
        require '0';

    $get_post = get_post($post_id);
    $Bkx_Meta_Boxes = new Bkx_Meta_Boxes();

    $order_full_output = $Bkx_Meta_Boxes->bookingx_output($get_post, 'ajax');
    $bookingx_note_output = $Bkx_Meta_Boxes->bookingx_note_output($get_post, 'ajax');
    $bookingx_reassign_output = $Bkx_Meta_Boxes->bookingx_reassign_output($get_post, 'ajax');
    // $order_summary_output = '<div class="bkx-order_summary"> <h3>Booking #'.$post_id.' Notes </h3></div>';
    echo '<td class="section-1" colspan="10">' . $order_full_output . '</td>
       <td colspan="3" class="section-2">' . $bookingx_note_output . '' . $bookingx_reassign_output . '</td>';
    wp_die();
}

add_action('wp_ajax_bkx_action_listing_view', 'bkx_action_listing_view_callback');
add_action('wp_ajax_nopriv_bkx_action_listing_view', 'bkx_action_listing_view_callback');

/**
 *
 */
function bkx_get_page_content_callback()
{
    $page_id = sanitize_text_field($_POST['page_id']);

    if (empty($page_id))
        require '0';

    $get_content = get_post($page_id);
    $get_data['post_title'] = $get_content->post_title;
    $get_data['post_content'] = apply_filters('the_content', $get_content->post_content);
    echo json_encode($get_data);
    wp_die();
}

add_action('wp_ajax_bkx_get_page_content', 'bkx_get_page_content_callback');
add_action('wp_ajax_nopriv_bkx_get_page_content', 'bkx_get_page_content_callback');


/**
 *
 */
function bkx_action_add_custom_note_callback()
{
    $booking_id = sanitize_text_field($_POST['booking_id']);
    $bkx_custom_note = $_POST['bkx_custom_note'];

    if (empty($booking_id) || empty($bkx_custom_note))
        require '0';

    $bookingObj = new BkxBooking('', $booking_id);
    $bookingObj->add_order_note(sprintf(__($bkx_custom_note, 'bookingx'), ''), 0, $manual);

    wp_die();
}

add_action('wp_ajax_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback');
add_action('wp_ajax_nopriv_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback');


/**
 *
 */
function bkx_cust_setting_callback()
{
    parse_str($_POST['cust_data'], $cust_data);

    $user_id = get_current_user_id();

    $data = array();
    $password_is_ok = 0;


    if (empty($cust_data['bkx_cust_name'])) {
        $error[] = sprintf(__('%s', 'Bookingx'), 'Please fill your name.');
    }
    if (empty($cust_data['bkx_cust_email'])) {
        $error[] = 'Please fill your email.';
    }
    if (filter_var($cust_data['bkx_cust_email'], FILTER_VALIDATE_EMAIL) === false) {
        $error[] = $cust_data['bkx_cust_email'] . " is not a valid email address";
    }
    if (empty($cust_data['bkx_cust_phone'])) {
        $error[] = 'Please fill your phone number.';
    }
    if (empty($cust_data['bkx_cust_gc'])) {
        $error[] = 'Please fill your google calendar id.';
    }
    if ((empty($cust_data['bkx_cust_pwd']) && !empty($cust_data['bkx_cust_cnf_pwd'])) || (!empty($cust_data['bkx_cust_pwd']) && empty($cust_data['bkx_cust_cnf_pwd'])) || $cust_data['bkx_cust_pwd'] != $cust_data['bkx_cust_cnf_pwd']) {
        $error[] = "Password not match ";
    }
    if ($cust_data['bkx_cust_pwd'] == $cust_data['bkx_cust_cnf_pwd']) {
        $password_is_ok = 1;
    }

    if (empty($cust_data['bkx_cust_add_street'])) {
        $error[] = 'Please fill your street address.';
    }
    if (empty($cust_data['bkx_cust_add_city'])) {
        $error[] = 'Please fill your city.';
    }
    if (empty($cust_data['bkx_cust_add_state'])) {
        $error[] = 'Please fill your state.';
    }
    if (empty($cust_data['bkx_cust_add_zip'])) {
        $error[] = 'Please fill your zip.';
    }

    if (empty($error) && !empty($user_id)) {

        $userdata = array(
            'ID' => $user_id,
            'first_name' => $cust_data['bkx_cust_name'],
            'user_email' => $cust_data['bkx_cust_email']
        );
        wp_update_user($userdata);
        update_user_meta($user_id, 'bkx_cust_name', $cust_data['bkx_cust_name']);
        update_user_meta($user_id, 'bkx_cust_email', $cust_data['bkx_cust_email']);
        update_user_meta($user_id, 'bkx_cust_phone', $cust_data['bkx_cust_phone']);
        update_user_meta($user_id, 'bkx_cust_gc', $cust_data['bkx_cust_gc']);
        update_user_meta($user_id, 'bkx_cust_add_street', $cust_data['bkx_cust_add_street']);
        update_user_meta($user_id, 'bkx_cust_add_city', $cust_data['bkx_cust_add_city']);
        update_user_meta($user_id, 'bkx_cust_add_state', $cust_data['bkx_cust_add_state']);
        update_user_meta($user_id, 'bkx_cust_add_zip', $cust_data['bkx_cust_add_zip']);

        if ($password_is_ok == 1 && !is_admin()) {
            wp_set_password($cust_data['bkx_cust_pwd'], $user_id);
        }

        $data['success'] = 'Your settings saved successfully.';

    } else {
        $data['error'] = $error;
    }

    echo json_encode($data);
    wp_die();

}

add_action('wp_ajax_bkx_cust_setting', 'bkx_cust_setting_callback');
add_action('wp_ajax_nopriv_bkx_cust_setting', 'bkx_cust_setting_callback');

function bkx_get_base_obj_by_id(){
    if( isset($_POST['base_id']) && $_POST['base_id']!="" && $_POST['base_id']!= 0 ){
        //$BaseObj    = get_post( $_POST['base_id'] );
        $BaseMeta   = get_post_meta( $_POST['base_id'] );
        echo json_encode($BaseMeta);
    }
    wp_die();
}
add_action('wp_ajax_bkx_get_base_obj_by_id', 'bkx_get_base_obj_by_id');
add_action('wp_ajax_nopriv_bkx_get_base_obj_by_id', 'bkx_get_base_obj_by_id');

//bkx_get_base_on_seat
/**
 *
 */
function bkx_get_base_on_seat_callback()
{
    $term = '';


    if (isset($_POST['seatid'])) {
        $term = sanitize_text_field($_POST['seatid']);
    }
    if (isset($_POST['loc'])) {
        $_SESSION["baseid"] = $baseid = sanitize_text_field($_POST['baseid']);
        $BaseObj = get_post($baseid);
        if (!empty($BaseObj) && !is_wp_error($BaseObj)) {
            $BaseMetaObj = get_post_custom($BaseObj->ID);
            $base_is_location_fixed = isset($BaseMetaObj['base_is_location_fixed']) ? esc_attr($BaseMetaObj['base_is_location_fixed'][0]) : "";

        }


    } elseif (isset($_POST['mob'])) {

        $baseid1 = sanitize_text_field($_POST['baseid1']);
        $BaseObj = get_post($baseid1);
        if (!empty($BaseObj) && !is_wp_error($BaseObj)) {
            $BaseMetaObj = get_post_custom($BaseObj->ID);
            $base_is_mobile_only = isset($BaseMetaObj['base_is_mobile_only']) ? esc_attr($BaseMetaObj['base_is_mobile_only'][0]) : "";
            $base_location_type = isset($BaseMetaObj['base_location_type']) ? esc_attr($BaseMetaObj['base_location_type'][0]) : "";
            $arr_base['base_location_type'] = $base_location_type;


            //print_r($base_is_mobile_only);
        }

    } else {
        /**
         *  Added By : Divyang Parekh
         *  Reason For : Any Seat Functionality
         */
        $arr_base = array();
        if ($term == 'any' && get_option('enable_any_seat') == 1):
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'bkx_base',
                'post_status' => 'publish',
                'suppress_filters' => false
            );

        elseif(isset($_POST['order']) && $_POST['order'] =='edit') :
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'bkx_base',
                'post_status' => 'publish',
                'suppress_filters' => false
                 
            );

        else:

            //$where_clause ='WHERE seat_id = '.trim($term);
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'bkx_base',
                'post_status' => 'publish',
                'suppress_filters' => false,
                'meta_query' => array(
                    array(
                        'key' => 'base_selected_seats',
                        'value' => serialize($term),
                        'compare' => 'like'
                    )
                )
            );

        endif;

        if (isset($term) && $term != '') {
            $objSeat = get_post($term);
            if (!empty($objSeat) && !is_wp_error($objSeat)) {
                $values = get_post_custom($objSeat->ID);

                if (!empty($values) && !is_wp_error($values)) {
                    $seat_street = isset($values['seat_street']) ? esc_attr($values['seat_street'][0]) : "";
                    $seat_city = isset($values['seat_city']) ? esc_attr($values['seat_city'][0]) : "";
                    $seat_state = isset($values['seat_state']) ? esc_attr($values['seat_state'][0]) : "";
                    $seat_zip = isset($values['seat_zip']) ? esc_attr($values['seat_zip'][0]) : "";
                    $seat_country = isset($values['seat_country']) ? esc_attr($values['seat_country'][0]) : "";
                    $seat_is_certain_month = $values['seat_is_certain_month'][0];
                    $seat_months = isset($values['seat_months'][0]) && $values['seat_months'][0] != '' ? $temp_seat_months = explode(',', $values['seat_months'][0]) : '';
                    $seat_days = isset($values['seat_days'][0]) && $values['seat_days'][0] != '' ? 
                                 implode(",",maybe_unserialize($values['seat_days'][0])) : '';
                     
                    $seat_is_certain_day = $values['seat_is_certain_day'][0];
                    $seat_days_time = maybe_unserialize($values['seat_days_time'][0]);
                    $res_seat_time = maybe_unserialize($seat_days_time);

                    if (!empty($res_seat_time)) {
                        $res_seat_time_arr = array();
                        foreach ($res_seat_time as $temp) {
                            $res_seat_time_arr[strtolower($temp['day'])]['time_from'] = $temp['time_from'];
                            $res_seat_time_arr[strtolower($temp['day'])]['time_till'] = $temp['time_till'];
                        }
                    }
                    $seat_is_pre_payment = isset($values['seatIsPrePayment']) ? esc_attr($values['seatIsPrePayment'][0]) : "";
                    $seat_payment_type = isset($values['seat_payment_type']) ? esc_attr($values['seat_payment_type'][0]) : ""; //Payment Type

                    $seat_deposite_type = isset($values['seat_payment_option']) ? esc_attr($values['seat_payment_option'][0]) : ""; // Deposite Type
                    $seat_fixed_amount = isset($values['seat_amount']) ? esc_attr($values['seat_amount'][0]) : "";
                    $seat_percentage = isset($values['seat_percentage']) ? esc_attr($values['seat_percentage'][0]) : "";
                    $seat_phone = isset($values['seatPhone']) ? esc_attr($values['seatPhone'][0]) : "";
                    $seat_notification_email = isset($values['seatEmail']) ? esc_attr($values['seatEmail'][0]) : "";
                    $seat_notification_alternate_email = isset($values['seatIsAlternateEmail']) ? esc_attr($values['seatIsAlternateEmail'][0]) : "";
                    $seatAlternateEmail = isset($values['seatAlternateEmail']) ? esc_attr($values['seatAlternateEmail'][0]) : "";
                    $seat_ical_address = isset($values['seatIsIcal']) ? esc_attr($values['seatIsIcal'][0]) : "";
                    $seatIcalAddress = isset($values['seatIcalAddress']) ? esc_attr($values['seatIcalAddress'][0]) : "";
                }
            }

            if (empty($seat_days) || $seat_is_certain_day == "N") {
                $seat_days = bkx_crud_option_multisite("bkx_business_days");
                if(!empty($seat_days)){
                    $generate_string = "";
                    foreach ($seat_days as $days_week) {
                        $days_week_obj = reset($days_week);
                        $generate_string .= implode(",", $days_week_obj).",";
                    }
                    $seat_days = rtrim($generate_string,",");
                }
            }
            //            $arr_base['seat_is_certain_time'] = $objSeat[0]->seat_is_certain_time;
            //            $arr_base['seat_time_from'] = $objSeat[0]->seat_time_from;
            //            $arr_base['seat_time_till'] = $objSeat[0]->seat_time_till;
            $arr_base['seat_is_certain_days'] = $seat_is_certain_day;
            $arr_base['seat_days'] = $seat_days;
            $arr_base['seat_is_certain_month'] = $seat_is_certain_month;
            $arr_base['seat_months'] = $seat_months;
            $arr_base['seat_is_booking_prepayment'] = $seat_is_pre_payment;
        }
        ///end seat data fetch

        $get_base_array = get_posts($args);
        if (!empty($get_base_array)) {
            $counter = 0;
            foreach ($get_base_array as $key => $value) {
                $base_name = $value->post_title;
                $base_id = $value->ID;

                $values = get_post_custom($value->ID);

                if (!empty($values) && !is_wp_error($values)) {
                    $base_price = isset($values['base_price']) ? esc_attr($values['base_price'][0]) : 0;
                    $base_time_option = isset($values['base_time_option']) ? esc_attr($values['base_time_option'][0]) : "";
                    $base_month = isset($values['base_month']) ? esc_attr($values['base_month'][0]) : "";
                    $base_day = isset($values['base_day']) ? esc_attr($values['base_day'][0]) : "";
                    $base_hours = isset($values['base_hours']) ? esc_attr($values['base_hours'][0]) : "";

                    $base_minutes = isset($values['base_minutes']) ? esc_attr($values['base_minutes'][0]) : "";
                    $base_is_extended = isset($values['base_is_extended']) ? esc_attr($values['base_is_extended'][0]) : "";
                    $base_is_location_fixed = isset($values['base_is_location_fixed']) ? esc_attr($values['base_is_location_fixed'][0]) : "";
                    $base_is_mobile_only = isset($values['base_is_mobile_only']) ? esc_attr($values['base_is_mobile_only'][0]) : "";
                    $base_is_location_differ_seat = isset($values['base_is_location_differ_seat']) ? esc_attr($values['base_is_location_differ_seat'][0]) : "";
                    $base_street = isset($values['base_street']) ? esc_attr($values['base_street'][0]) : "";
                    $base_city = isset($values['base_city']) ? esc_attr($values['base_city'][0]) : "";
                    $base_state = isset($values['base_state']) ? esc_attr($values['base_state'][0]) : "";
                    $base_postcode = isset($values['base_postcode']) ? esc_attr($values['base_postcode'][0]) : "";
                    $base_is_allow_addition = isset($values['base_is_allow_addition']) ? esc_attr($values['base_is_allow_addition'][0]) : "";
                    $base_is_unavailable = isset($values['base_is_unavailable']) ? esc_attr($values['base_is_unavailable'][0]) : "";
                    $base_unavailable_from = isset($values['base_unavailable_from']) ? esc_attr($values['base_unavailable_from'][0]) : "";
                    $base_unavailable_till = isset($values['base_unavailable_till']) ? esc_attr($values['base_unavailable_till'][0]) : "";
                    $base_location_type = isset($values['base_location_type']) ? esc_attr($values['base_location_type'][0]) : "Fixed Location";
                    $res_seat_final = maybe_unserialize($values['base_selected_seats'][0]);
                    $res_seat_final = maybe_unserialize($res_seat_final);
                }
                $base_time = '';
                if ($base_time_option == "H") {
                    if ($base_hours != 0) {
                        $base_time .= $base_hours . " Hours ";
                    }
                    if ($base_minutes != 0) {
                        $base_time .= $base_minutes . " Minutes ";
                    }
                } else if ($base_time_option == "D") {
                    $base_time .= $base_day . "Days ";
                }
                $arr_base['base_list'][$counter]['base_id'] = $base_id;
                $arr_base['base_list'][$counter]['base_name'] = $base_name;
                $arr_base['base_list'][$counter]['base_price'] = $base_price;
                $arr_base['base_list'][$counter]['base_time'] = $base_time;
                $arr_base['base_list'][$counter]['base_location_type'] = $base_location_type;
                $counter++;
            }
        }

    }
    $output = json_encode($arr_base);
    echo $output;

    wp_die();
}

add_action('wp_ajax_bkx_get_base_on_seat', 'bkx_get_base_on_seat_callback');
add_action('wp_ajax_nopriv_bkx_get_base_on_seat', 'bkx_get_base_on_seat_callback');

//bkx_get_booked_days_callback
/**
 *
 */
function bkx_get_booked_days_callback()
{
    $baseid = sanitize_text_field($_POST['baseid']);
    $BaseMetaObj = get_post_custom($baseid);
    $base_time_option = $BaseMetaObj['base_time_option'][0];
    $base_extended = isset($BaseMetaObj['base_is_extended']) ? esc_attr($BaseMetaObj['base_is_extended'][0]) : "";
    if ($base_time_option == "D" || $base_time_option == "M") {
        $base_data['disable'] = 'yes';
        $base_data['time'] = $BaseMetaObj['base_day'][0];
        echo json_encode($base_data);
    } else {
        echo json_encode('');
    }

    wp_die();
}

add_action('wp_ajax_bkx_get_booked_days', 'bkx_get_booked_days_callback');
add_action('wp_ajax_nopriv_bkx_get_booked_days', 'bkx_get_booked_days_callback');

//bkx_get_if_base_extended
/**
 *
 */
function bkx_get_if_base_extended_callback()
{
    $term = '';
    $base_extended = 'N';
    if (isset($_POST['baseid']) && $_POST['baseid'] != '') {
        $term = sanitize_text_field($_POST['baseid']);
        $BaseObj = get_post($term);
        if (!empty($BaseObj) && !is_wp_error($BaseObj)) {
            $BaseMetaObj = get_post_custom($BaseObj->ID);
            $base_time_option = $BaseMetaObj['base_time_option'][0];
            $base_extended = isset($BaseMetaObj['base_is_extended']) ? esc_attr($BaseMetaObj['base_is_extended'][0]) : "N";
            if ($base_time_option == "H") {
                echo $base_extended;
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_bkx_get_if_base_extended', 'bkx_get_if_base_extended_callback');
add_action('wp_ajax_nopriv_bkx_get_if_base_extended', 'bkx_get_if_base_extended_callback');


//bkx_get_addition_on_base
/**
 *
 */
function bkx_get_addition_on_base_callback()
{
    $term = '';
    $seat_id = sanitize_text_field($_POST['seat_id']);
    if (isset($_POST['baseid'])) {
        $term = sanitize_text_field($_POST['baseid']);

         if(isset($_POST['order']) && $_POST['order'] =='edit') {
                $args = array('posts_per_page' => -1,
                    'post_type' => 'bkx_addition',
                    'post_status' => 'publish',
                    'suppress_filters' => false

                );
        }else{
                $args = array('posts_per_page' => -1,
                    'post_type' => 'bkx_addition',
                    'post_status' => 'publish',
                    'suppress_filters' => false,
                    'meta_query' => array(array(
                        'key' => 'extra_selected_base',
                        'value' => serialize($term),
                        'compare' => 'like'
                    )
                    )
                );
        }

        $get_extra_array = get_posts($args);
    }

    if (!empty($get_extra_array) && !is_wp_error($get_extra_array)) {
        $arr_seat = '';
        $counter = 0;
        foreach ($get_extra_array as $key => $value) {

            $extra_name = $value->post_title;
            $extra_id = $value->ID;

            $values = get_post_custom($extra_id);

            if (!empty($values) && !is_wp_error($values)) {
                $addition_price = isset($values['addition_price']) ? esc_attr($values['addition_price'][0]) : "";
                $addition_time_option = isset($values['addition_time_option']) ? esc_attr($values['addition_time_option'][0]) : "";
                $addition_overlap = isset($values['addition_overlap']) ? esc_attr($values['addition_overlap'][0]) : "";
                $addition_months = isset($values['addition_months']) ? esc_attr($values['addition_months'][0]) : "";

                $addition_days = isset($values['addition_days']) ? esc_attr($values['addition_days'][0]) : "";
                $addition_hours = isset($values['addition_hours']) ? esc_attr($values['addition_hours'][0]) : "";
                $addition_minutes = isset($values['addition_minutes']) ? esc_attr($values['addition_minutes'][0]) : "";
                $addition_is_unavailable = isset($values['addition_is_unavailable']) ? esc_attr($values['addition_is_unavailable'][0]) : "";
                $addition_available_from = isset($values['addition_unavailable_from']) ? esc_attr($values['addition_unavailable_from'][0]) : "";
                $addition_available_to = isset($values['addition_unavailable_to']) ? esc_attr($values['addition_unavailable_to'][0]) : "";
                $res_base_final = maybe_unserialize($values['extra_selected_base'][0]);
                $res_base_final = maybe_unserialize($res_base_final);

                $extra_selected_seats = maybe_unserialize($values['extra_selected_seats'][0]);
                $extra_selected_seats = maybe_unserialize($extra_selected_seats);
            }

            if ( (!empty($extra_selected_seats) && in_array($seat_id, $extra_selected_seats)) ||  isset($_POST['order']) && $_POST['order'] =='edit' ) {

                $extra_time = '';
                if ($addition_time_option == "H") {
                    if ($addition_hours != 0) {
                        $extra_time .= $addition_hours . " Hours ";
                    }
                    if ($addition_minutes != 0) {
                        $extra_time .= $addition_minutes . " Minutes ";
                    }
                } else if ($seat_time_option == "D") {
                    $extra_time .= $addition_days . "Days ";
                }

                $arr_seat[$counter]['addition_alies'] = get_option('bkx_alias_addition', 'Addition');
                $arr_seat[$counter]['addition_id'] = $extra_id;
                $arr_seat[$counter]['addition_name'] = $extra_name;
                $arr_seat[$counter]['addition_price'] = $addition_price;
                $arr_seat[$counter]['addition_time'] = $extra_time;
                $counter++;
            }
        }
    }

    $output = json_encode($arr_seat);
    echo $output;
    wp_die();
}

add_action('wp_ajax_bkx_get_addition_on_base', 'bkx_get_addition_on_base_callback');
add_action('wp_ajax_nopriv_bkx_get_addition_on_base', 'bkx_get_addition_on_base_callback');

//bkx_get_total_price_callback
/**
 *
 */
function bkx_get_total_price_callback()
{
    $total_price = 0;
    $base_price = 0;
    $addition_price = 0;
    $get_service_hours = 0;
    $get_service_minute = 0;
    $get_extra_hours = 0;
    $get_extra_minute = 0;
    $get_total_time_of_services = 0;

    if (isset($_POST['baseid']) && $_POST['baseid'] != "") {
        $get_service_hours = get_post_meta($_POST['baseid'], 'base_hours', true);
        $get_total_time_of_services += $get_service_hours * 60;
        $get_service_minute = get_post_meta($_POST['baseid'], 'base_minutes', true);
        $get_total_time_of_services += (isset($get_service_minute) && $get_service_minute != "") ? $get_service_minute : 0;
    }


    if (isset($_POST['seatid'])) {
        $seat = sanitize_text_field($_POST['seatid']);
        $objSeat = get_post($seat);
        if (!empty($objSeat) && !is_wp_error($objSeat)) {
            $SeatMetaObj = get_post_custom($objSeat->ID);
            if (!empty($SeatMetaObj) && !is_wp_error($SeatMetaObj)) {
                $booking_require_prepayment = isset($SeatMetaObj['seatIsPrePayment']) ? esc_attr($SeatMetaObj['seatIsPrePayment'][0]) : "";
                $deposit_type = isset($SeatMetaObj['seat_payment_option']) ? esc_attr($SeatMetaObj['seat_payment_option'][0]) : ""; // Deposite Type
                $payment_type = isset($SeatMetaObj['seat_payment_type']) ? esc_attr($SeatMetaObj['seat_payment_type'][0]) : ""; //Payment Type
                $fixed_amount = isset($SeatMetaObj['seat_amount']) ? esc_attr($SeatMetaObj['seat_amount'][0]) : "";
                $percentage = isset($SeatMetaObj['seat_percentage']) ? esc_attr($SeatMetaObj['seat_percentage'][0]) : "";
            }
        }
    }
    $additionid = array_map('sanitize_text_field', wp_unslash($_POST['additionid']));
    if (isset($additionid) && !empty($additionid)) {
        foreach ($additionid as $extra_id) {
            $get_extra_hours = get_post_meta($extra_id, 'addition_hours', true);
            $get_total_time_of_services += $get_extra_hours * 60;
            $get_extra_minute = get_post_meta($extra_id, 'addition_minutes', true);
            $get_total_time_of_services += (isset($get_extra_minute) && $get_extra_minute != "") ? $get_extra_minute : 0;
        }
    }
    $total_price = bkx_cal_total_price(sanitize_text_field($_POST['baseid']), sanitize_text_field($_POST['extended']), $additionid);

    if ($booking_require_prepayment == "Y") {

        if ($payment_type == "FP") {
            $deposit_price = $total_price;
        }

        if ($payment_type == "D") {
            if ($deposit_type == "FA") {
                $deposit_price = $fixed_amount;
            } else if ($deposit_type == "P") {
                $deposit_price = ($percentage / 100) * $total_price;
            }
        }
    } else {
        $deposit_price = 0;
    }

    if (!empty($get_total_time_of_services) && $get_total_time_of_services != 0) {
        $total_time_of_services_formatted = bkx_total_time_of_services_formatted($get_total_time_of_services);
    }

    $result['total_tax'] = bkx_cal_total_tax($total_price);
    $result['total_price'] = number_format((float)$total_price, 2, '.', '');
    $result['seat_is_booking_prepayment'] = $booking_require_prepayment;
    $result['deposit_price'] = number_format((float)$deposit_price, 2, '.', '');
    $result['total_time_of_services'] = $get_total_time_of_services;
    $result['total_time_of_services_formatted'] = $total_time_of_services_formatted;


    $output = json_encode($result);
    echo $output;
    wp_die();
}

add_action('wp_ajax_bkx_get_total_price', 'bkx_get_total_price_callback');
add_action('wp_ajax_nopriv_bkx_get_total_price', 'bkx_get_total_price_callback');

/**
 *
 */
function bkx_get_duration_booking_callback()
{
    $booking_customer_note = 'zero';
    $seat_alias = bkx_crud_option_multisite("bkx_alias_seat");
    $base_alias = bkx_crud_option_multisite("bkx_alias_base");
    $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
    $get_service_hours = 0;
    $get_service_minute = 0;
    $get_extra_hours = 0;
    $get_extra_minute = 0;
    $total_base_duration = 0;
    $get_total_time_of_services = 0;
    $total_extra_duration = 0;

    $baseid = sanitize_text_field($_POST['baseid']);
    $additionid = array_map('sanitize_text_field', wp_unslash($_POST['additionid']));

    if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && $_SESSION['free_seat_id'] != '' && bkx_crud_option_multisite('enable_any_seat') == 1 && bkx_crud_option_multisite('select_default_seat') != ''):
        $mobonlyobj = $_SESSION['free_seat_id'];
    else:
        $mobonlyobj = sanitize_text_field($_POST['seatid']);
    endif;

    $booking_summary = '<ul>';

    if (is_numeric($mobonlyobj) && $mobonlyobj != 0) {
        $SeatObj = get_post($mobonlyobj);
        $seatmeta = get_post_custom($SeatObj->ID);
        $seat_is_pre_payment = isset($seatmeta['seatIsPrePayment']) ? esc_attr($seatmeta['seatIsPrePayment'][0]) : "";
        if ($seat_is_pre_payment == 'N') {

            $booking_customer_note = sprintf(__('Payment will be made when the customer comes for the booking.', 'bookingx'), '');
        }
        //$booking_summary .= '<li><b>'.$seat_alias.' name : </b>'.$SeatObj->post_title.'';
        $booking_summary .= sprintf(__('<li><b> %1$s name : </b> %2$s', 'bookingx'), $seat_alias, $SeatObj->post_title);
    }

    if (isset($baseid) && $baseid != '') {
        $GetBaseObj = get_post($baseid);
        $objBase = get_post_custom($GetBaseObj->ID);
        $base_data = sprintf(__('<li><b> %1$s name : </b> %2$s', 'bookingx'), $base_alias, $GetBaseObj->post_title);
    }

    $res_arr = array();
    //mobile only data
    if (isset($mobonlyobj)) {
        $mob_only_res = $objBase['base_is_mobile_only'][0];;
    }


    //calculation of base duration
    $str_addition_name = '';
    if (isset($baseid)) {
        $get_service_hours = get_post_meta($_POST['baseid'], 'base_hours', true);
        $get_total_time_of_services += $get_service_hours * 60;
        $get_service_minute = get_post_meta($_POST['baseid'], 'base_minutes', true);
        $get_total_time_of_services += (isset($get_service_minute) && $get_service_minute != "") ? $get_service_minute : 0;
        $base_time_option = $objBase['base_time_option'][0];

        $res_arr['base']['base_time_option'] = $base_time_option;
        $base_time = 0;
        if ($base_time_option == "H") {
            //calculate base minutes
            $base_minutes = $objBase['base_minutes'][0];

            $total_base_duration = ($get_service_hours * 60) + $get_service_minute;

            if (!empty($total_base_duration) && $total_base_duration != 0) {
                $total_time_of_base_formatted = bkx_total_time_of_services_formatted($total_base_duration);
            }

            $booking_summary .= $base_data . " - " . $total_time_of_base_formatted;

            $base_time = $objBase['base_hours'][0] + $minute; //add base minutes to base hours
        }
        if ($base_time_option == "D") {
            $base_time = $objBase['base_day'][0] * 24;

        }
        if ($base_time_option == "M") {
            $base_time = $objBase['base_month'][0] * 30 * 24;
        }
        //total base time in seconds
        $total_base_time_insec = $base_time * 60 * 60;
        //calculation of addition duration
        $objAddition = array();
        if (isset($additionid) && sizeof($additionid) > 0) {
            //Get Seat post Array
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'bkx_addition',
                'post_status' => 'publish',
                'include' => $additionid,
                'suppress_filters' => false
            );
            $objListAddition = get_posts($args);

            $str_addition_name = "";
            $total_time_of_extra_formatted = "";
            foreach ($objListAddition as $extra) {

                $extra_id = $extra->ID;
                $get_extra_hours = get_post_meta($extra_id, 'addition_hours', true);
                $get_total_time_of_services += (isset($get_extra_hours) && $get_extra_hours != "") ? ($get_extra_hours * 60) : 0;;
                $get_extra_minute = get_post_meta($extra_id, 'addition_minutes', true);
                $get_total_time_of_services += (isset($get_extra_minute) && $get_extra_minute != "") ? $get_extra_minute : 0;

                $total_extra_duration = ($get_extra_hours * 60) + $get_extra_minute;

                if (!empty($total_extra_duration) && $total_extra_duration != 0) {
                    $total_time_of_extra_formatted = bkx_total_time_of_services_formatted($total_extra_duration);
                }
                $extra_hours_display = sprintf(__('%1$s', 'bookingx'), $total_time_of_extra_formatted);
                $objAddition[$extra->ID]['addition_time_display'] = $extra_hours_display;

                $objextra = get_post_custom($extra->ID);
                $objAddition[$extra->ID]['addition_time_option'] = $objextra['addition_time_option'][0];
                if ($objextra['addition_time_option'][0] == "H") {
                    //calculate addition minutes in hours
                    $addition_minutes = $objextra['addition_minutes'][0];
                    $objAddition[$extra->ID]['addition_time'] = $objextra['addition_hours'][0] + $minute;
                    //add addition minutes to addition hours
                } else if ($temp->addition_time_option == "D") {
                    $objAddition[$extra->ID]['addition_time'] = $objextra['addition_days'][0] * 24;
                    $objAddition[$extra->ID]['addition_time_display'] = sprintf(__('%1$s Days', 'bookingx'), $objextra['addition_days'][0]);

                } else if ($temp->addition_time_option == "M") {
                    $objAddition[$extra->ID]['addition_time'] = $objextra['addition_months'][0] * 24 * 30;
                    $objAddition[$extra->ID]['addition_time_display'] = sprintf(__('%1$s Months', 'bookingx'), $objextra['addition_months'][0]);
                }
                $str_addition_name .= sprintf(__('%1$s - %2$s%3$s - %4$s , ', 'bookingx'), $extra->post_title, bkx_get_current_currency(), $objextra['addition_price'][0], $extra_hours_display);
            }
            $str_addition_name = rtrim($str_addition_name, ", ");

            $booking_summary .= '<li><b>' . sprintf(__('%1$s', 'bookingx'), $addition_alias) . ' : </b> ' . $str_addition_name . '</li>';

        }
        $counter = 0;
        if (isset($objAddition) && (sizeof($objAddition) > 0)) {
            foreach ($objAddition as $key => $val) {
                if (isset($val['addition_time'])) {
                    $counter = $counter + $val['addition_time'];
                }
            }
        }
        //total addition time in seconds
        $total_addition_time_insec = $counter * 60 * 60; //convert addition time in seconds
        //total duration booked in seconds
        $total_duration_seconds = $total_addition_time_insec + $total_base_time_insec;
        //check if can be booked 
        $seatid = sanitize_text_field($_POST['seatid']);
        $result = bkx_checkIfSeatCanBeBooked($seatid, $total_duration_seconds); //if allowed to select time option
    }
    $total_tax = 0;
    $total_price = 0;
    $total_price = bkx_cal_total_price($baseid, sanitize_text_field($_POST['extended']), $additionid);

    if (!empty($total_price)) {
        $bkx_cal_total_tax = bkx_cal_total_tax($total_price);
        if (!empty($bkx_cal_total_tax)) {
            $total_tax = $bkx_cal_total_tax['total_tax'];
            $tax_rate = $bkx_cal_total_tax['tax_rate'];
            $grand_total = $bkx_cal_total_tax['grand_total'];
            $tax_name = $bkx_cal_total_tax['tax_name'];
            $total_price = $bkx_cal_total_tax['total_price'];

            $total_tax = number_format((float)$total_tax, 2, '.', '');
            $total_price = number_format((float)$total_price, 2, '.', '');
            $grand_total = number_format((float)$grand_total, 2, '.', '');
        } else {
            $total_tax = 0;
            $tax_rate = '';
            $grand_total = $total_price;
            $tax_name = " ";
            $total_price = $total_price;
        }
    }

    if (!empty($get_total_time_of_services) && $get_total_time_of_services != 0) {
        $bkx_total_time_of_services_formatted = bkx_total_time_of_services_formatted($get_total_time_of_services);
    }
    
    $booking_summary .= sprintf(__('<li><b> Total Time : </b> %1$s </li></ul>', 'bookingx'), $bkx_total_time_of_services_formatted);

    $currency = bkx_get_current_currency();
    $output_time = str_replace('(', "", $bkx_total_time_of_services_formatted);
    $output_time = str_replace(')', "", $output_time);
    $arr_output['addition_list'] = $str_addition_name;
    $arr_output['booking_customer_note'] = $booking_customer_note;
    $arr_output['booked_summary'] = $booking_summary;
    $arr_output['time_output'] = $output_time;
    $arr_output['totalduration_in_seconds'] = $get_total_time_of_services * 60;
    $arr_output['result'] = $result;
    $arr_output['mob_only'] = $mobonlyobj;
    $arr_output['total_price'] = $total_price;
    $arr_output['total_tax'] = $total_tax;
    $arr_output['tax_rate'] = $tax_rate;
    $arr_output['grand_total'] = $grand_total;
    $arr_output['tax_name'] = $tax_name;
    $arr_output['currency'] = $currency;

    $output_time = json_encode($arr_output);

    echo $output_time;
    wp_die();
}

add_action('wp_ajax_bkx_get_duration_booking', 'bkx_get_duration_booking_callback');
add_action('wp_ajax_nopriv_bkx_get_duration_booking', 'bkx_get_duration_booking_callback');


//bkx_check_ifit_canbe_booked
/**
 *
 */
function bkx_check_ifit_canbe_booked_callback()
{
    $start = sanitize_text_field($_POST['start']);
    $bookingduration = sanitize_text_field($_POST['bookingduration']);
    $bookingdate = sanitize_text_field($_POST['bookingdate']);
    $order_statuses = array('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed');
    $update_order_slot  = sanitize_text_field($_POST['update_order_slot']);
    $selected_order_id  = sanitize_text_field($_POST['selected_order_id']);
    $current_order_id   = sanitize_text_field($_POST['order_id']);
    if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && bkx_crud_option_multisite('enable_any_seat') == 1 && bkx_crud_option_multisite('select_default_seat') != ''):
        $base_id = $_SESSION['_session_base_id'];
        //get current booking start time slot
        $bookinghour = explode(':', $start);
        $hours_temp = $bookinghour[0];
        $minutes_temp = $bookinghour[1];
        if ($minutes_temp == '00') {
            $counter = 0;
        } else if ($minutes_temp == '15') {
            $counter = 1;
        } else if ($minutes_temp == '30') {
            $counter = 2;
        } else if ($minutes_temp == '45') {
            $counter = 3;
        }
        $startingSlotNumber = intval($hours_temp) * 4 + $counter;

        $_enable_any_seat_selected_id = (int)bkx_crud_option_multisite('select_default_seat'); // Default Seat id
        $search = array('bookigndate' => $bookingdate, 'service_id' => $base_id, 'seat_id' => 'any', 'status' => $order_statuses, 'display' => 1);
        $BkxBooking = new BkxBooking();
        $objBookigntime = $BkxBooking->GetBookedRecords($search);


        if (!empty($objBookigntime)):

            foreach ($objBookigntime as $objBooknig):
                $slot_id = $objBooknig['full_day'];
                $duration = ($objBooknig['booking_time']) / 60;
                $seatslots = intval($duration / 15);
                $_SESSION['_seatslots'] = $seatslots;
                $booking_slot_range = array();
                if ($seatslots > 1) {
                    for ($i = 0; $i < $seatslots; $i++) {
                        $slot_id = $objBooknig['full_day'];
                        $booking_slot_range[] = $slot_id + $i;
                        $checked_booked_slots[] = array('slot_id' => $objBooknig['full_day'] + $i, 'order_id' => $objBooknig['booking_record_id']);
                    }
                } else {
                    $booking_slot_range[] = $slot_id;
                    $checked_booked_slots[] = array('slot_id' => $objBooknig['full_day'], 'order_id' => $objBooknig['booking_record_id']);
                }
                $booking_seat_id = $objBooknig['seat_id'];
                $get_booked_range[$slot_id][$booking_seat_id] = $booking_slot_range;
            endforeach;

            if ($_SESSION['_seatslots'] > 1) {
                for ($i = 0; $i < $_SESSION['_seatslots']; $i++) {
                    $find_slot_value_in_booked_array[] = ($startingSlotNumber + 1) + $i;
                }
            } else {
                $find_slot_value_in_booked_array[] = $startingSlotNumber + 1;;
            }

            if (!empty($get_booked_range)):
                $slot_id = $startingSlotNumber + 1;
                foreach ($get_booked_range as $slot_id => $get_booked_slot) {
                    $free_seat_all_arr = $get_free_seats;
                    $send_seat_block_data = array();
                    $slots_blocked = array();
                    $slot_id = $startingSlotNumber + 1;
                    if (!empty($get_booked_slot)) :
                        foreach ($get_booked_slot as $seat_id => $booked_slot) {
                            foreach ($find_slot_value_in_booked_array as $_find_slot) {
                                if (in_array($_find_slot, $booked_slot)) {
                                    $total_booked_slot_count = sizeof($booked_slot);
                                    $now_booked_slots_is[] = $booked_slot;
                                    $now_booked_slots_with_seats[$_find_slot][] = $seat_id;
                                }
                            }
                        }
                    endif;
                }
                if (!empty($now_booked_slots_with_seats)) {
                    foreach ($now_booked_slots_with_seats as $booked_slot_id => $booked_seats) {
                        foreach ($booked_seats as $now_seat_booked) {
                            $booked_all_seat_id_arr[] = $now_seat_booked;
                            $booked_all_seat_id_arr = array_unique($booked_all_seat_id_arr);
                        }
                    }
                }

                $BkxBase = new BkxBase('', $base_id);
                $seat_by_base = $BkxBase->get_seat_by_base();

                if (!empty($seat_by_base)) {
                    $total_free_seat_all = sizeof($seat_by_base);
                    $free_seat_all_arr = $seat_by_base;
                }

                if (!empty($free_seat_all_arr)) :
                    foreach ($free_seat_all_arr as $get_seat_id) :
                        if (!in_array($get_seat_id, $booked_all_seat_id_arr)):
                            $total_frees_seats[] = $get_seat_id;
                        endif;
                    endforeach;
                endif;
                if (!empty($total_frees_seats) && sizeof($total_frees_seats) > 1) {
                    if (in_array($_enable_any_seat_selected_id, $total_frees_seats)):
                        $free_seat_id = $_enable_any_seat_selected_id;
                    else :
                        $seat_id_key = array_rand($total_frees_seats);
                        $free_seat_id = $total_frees_seats[$seat_id_key];
                    endif;
                } else {
                    $free_seat_id = $total_frees_seats[0];
                }

            /**
             * Find Seat where other slot are available
             */

            else:

                $BkxBase = new BkxBase('', $base_id);
                $seat_by_base = $BkxBase->get_seat_by_base();

                if (!empty($seat_by_base)) {
                    $total_free_seat_all = sizeof($seat_by_base);
                    $free_seat_all_arr = $seat_by_base;
                }
                if (!empty($free_seat_all_arr) && sizeof($free_seat_all_arr) > 1) {
                    if (in_array($_enable_any_seat_selected_id, $free_seat_all_arr)):
                        $free_seat_id = $_enable_any_seat_selected_id;
                    else :
                        $seat_id_key = array_rand($free_seat_all_arr);
                        $free_seat_id = $free_seat_all_arr[$seat_id_key];
                    endif;
                } else {
                    $free_seat_id = $free_seat_all_arr[0];
                }
            endif;
        else:

            $BkxBase = new BkxBase('', $base_id);
            $seat_by_base = $BkxBase->get_seat_by_base();

            if (!empty($seat_by_base)) {
                $total_free_seat_all = sizeof($seat_by_base);
                $free_seat_all_arr = $seat_by_base;
            }

            if (!empty($free_seat_all_arr) && sizeof($free_seat_all_arr) > 1) {

                if (in_array($_enable_any_seat_selected_id, $free_seat_all_arr)):
                    $free_seat_id = $_enable_any_seat_selected_id;
                else :
                    $seat_id_key = array_rand($free_seat_all_arr);
                    $free_seat_id = $free_seat_all_arr[$seat_id_key];
                endif;
            } else {
                $free_seat_id = $free_seat_all_arr[0];
            }
        endif;

        $_SESSION['free_seat_id'] = $free_seat_id;
        $search = array('bookigndate' => $bookingdate, 'seat_id' => $free_seat_id
        , 'status' => $order_statuses, 'free_seat_id' => $free_seat_id, 'display' => 1);
        $BkxBooking = new BkxBooking();
        $resBookingTime = $BkxBooking->GetBookedRecords($search);

    else:
        $seatid = sanitize_text_field($_POST['seatid']);
        $search = array('bookigndate' => $bookingdate, 'status' => $order_statuses, 'seat_id' => sanitize_text_field($_POST['seatid']), 'display' => 1);
        $BkxBooking = new BkxBooking();
        $resBookingTime = $BkxBooking->GetBookedRecords($search);
    endif;


    $bookingSlot = "";
    $arrTempExplode = array();
    $resBookingTime = apply_filters('bkx_customise_check_booking_data', $resBookingTime);

    $order_id_by_date = array();
    if (!empty($resBookingTime)) {
        foreach ($resBookingTime as $temp) {
            $order_id_by_date[] = $temp['booking_record_id'];
            if(isset($current_order_id) && $current_order_id!="" && $current_order_id != $temp['booking_record_id']){
                $bookingSlot .= $temp['full_day'];
            }
            
        }
        $arrTempExplode = explode(',', $bookingSlot);
        $arrTempExplode = array_unique($arrTempExplode);
    }

    // print_r($arrTempExplode);
    // print_r($resBookingTime);
    //get current booking start time slot
    $bookinghour = explode(':', $start);
    $hours_temp = $bookinghour[0];
    $minutes_temp = $bookinghour[1];
    if ($minutes_temp == '00') {
        $counter = 0;
    } else if ($minutes_temp == '15') {
        $counter = 1;
    } else if ($minutes_temp == '30') {
        $counter = 2;
    } else if ($minutes_temp == '45') {
        $counter = 3;
    }

    $startingSlotNumber = intval($hours_temp) * 4 + $counter;

    $oneDayBooking;
    $res = 1;
    $numberOfSlotsToBeBooked = 0;
    if (in_array($startingSlotNumber, $arrTempExplode) && ($update_order_slot == 0 || $update_order_slot == '')) //return false if the start time exist in the booked slots
    {
        $res = 0;
    } else {

        $bookingDurationMinutes = ($bookingduration / 60);
        $numberOfSlotsToBeBooked = ($bookingDurationMinutes / 15);
        $lastSlotNumber = $startingSlotNumber + $numberOfSlotsToBeBooked;
        if ($lastSlotNumber <= 96) {
            $oneDayBooking = 1;
        } else {
            $oneDayBooking = 0;
        }
        if ($oneDayBooking == 1) {
            $arrBookingSlot = range($startingSlotNumber, $lastSlotNumber);
            foreach ($arrBookingSlot as $temp) {
                if (in_array($temp, $arrTempExplode) && ($update_order_slot == 0 || $update_order_slot == '')) {

                    $res = 0;
                    continue;
                }
            }
        } else //for multi day booking
        {
            //first check for current day only
            $lastSlotNumberTemp = 96;
            $bookedInCurrentDay = (96 - $startingSlotNumber);
            $numberOfSlotsRemaining = ($numberOfSlotsToBeBooked - $bookedInCurrentDay);
            //for current day slots
            $arrBookingSlot = range($startingSlotNumber, $lastSlotNumberTemp);
            foreach ($arrBookingSlot as $temp) {
                if (in_array($temp, $arrTempExplode)) {
                    $res = 0;
                    continue;
                }
            }
            //for remaining slots
            if ($res == 0) {

            } else if ($res == 1) {
                $totalDays = floor($numberOfSlotsRemaining / 96); //to find number of days
                $totalDaysMod = $numberOfSlotsRemaining % 96; //to find if extend to next day
                if ($totalDaysMod > 0) //if goes to next day add 1 to the days value
                {
                    $totalDays = $totalDays + 1;
                }

                for ($i = 1; $i <= $totalDays; $i++) {
                    if ($numberOfSlotsRemaining > 0) //to prevent slot to be negative
                    {
                        $nextDate = date('m/d/Y', strtotime("+$i day", strtotime($bookingdate)));

                        $search = array('bookigndate' => $nextDate, 'seat_id' => sanitize_text_field($_POST['seatid']), 'display' => 1);
                        $BkxBooking = new BkxBooking();
                        $resBookingTimeNext = $BkxBooking->get_order_time_data('', $search);


                        $bookingSlotNext = "";
                        $arrTempExplodeNext = array();
                        if (!empty($resBookingTimeNext)) {
                            foreach ($resBookingTimeNext as $temp) {
                                $bookingSlotNext .= $temp['full_day'];
                            }
                            $arrTempExplodeNext = explode(',', $bookingSlotNext);
                            $arrTempExplodeNext = array_unique($arrTempExplodeNext);

                        }

                        $availableSlots = range(1, 96);
                        $lastSlotRequired = 0;
                        if ($numberOfSlotsRemaining > 96) {
                            $lastSlotRequired = 96;
                        } else {
                            $lastSlotRequired = $numberOfSlotsRemaining;
                        }
                        $requiredSlots = range(1, $lastSlotRequired);
                        if (!empty($requiredSlots)) {
                            foreach ($requiredSlots as $temp) {
                                if (in_array($temp, $arrTempExplodeNext)) {
                                    $res = 0;
                                    continue;
                                }
                            }
                        }
                        $numberOfSlotsRemaining = $numberOfSlotsRemaining - 96;//get remaining slots after current loop
                    }
                }
            }
        }
    }
    $arr_output['result'] = $res;
    $arr_output['no_of_slots'] = $numberOfSlotsToBeBooked;
    $arr_output['starting_slot'] = $startingSlotNumber;
    $arr_output['booking_date'] = $bookingdate;
    $arr_output['order_id_by_date'] = $order_id_by_date;
    $arr_output['selected_order_id'] = $selected_order_id;
    $arr_output['current_order_id'] = $current_order_id;
    $output = json_encode($arr_output);
    echo $output;
    wp_die();

}

add_action('wp_ajax_bkx_check_ifit_canbe_booked', 'bkx_check_ifit_canbe_booked_callback');
add_action('wp_ajax_nopriv_bkx_check_ifit_canbe_booked', 'bkx_check_ifit_canbe_booked_callback');

//bkx_check_staff_availability
/**
 *
 */
function bkx_check_staff_availability_callback()
{
    $seat_id = sanitize_text_field($_POST['seat_id']);
    $base_id = sanitize_text_field($_POST['base_id']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $booking_record_id = sanitize_text_field($_POST['booking_record_id']);
    $status['booking_record_id'] = $booking_record_id;
    $BkxBookingObj = new BkxBooking('', $booking_record_id);
    $order_meta_data = $BkxBookingObj->get_order_meta_data($booking_record_id);
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
        $BkxBookingObj->add_order_note(sprintf(__('Successfully update %1$s from %2$s to %3$s.', 'bookingx'), $alias_seat, $old_seat, $new_seat), 0, $manual);
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

add_action('wp_ajax_bkx_check_staff_availability', 'bkx_check_staff_availability_callback');
add_action('wp_ajax_nopriv_bkx_check_staff_availability', 'bkx_check_staff_availability_callback');

//bkx_displaytime_options
/**
 *
 */
function bkx_displaytime_options_callback()
{
    require_once(BKX_PLUGIN_DIR_PATH . 'inc/booking_general_functions.php');

    $base_alias = bkx_crud_option_multisite("bkx_alias_base");
    $bookigndate = '';
    $full_day = '';
    $order_statuses = array('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed', 'bkx-ack');
    $current_order_id = isset($_POST['order_id']) && $_POST['order_id']!="" ? $_POST['order_id'] : 0;

    if (isset($_POST['bookigndate'])) {
        /**
         *    Added By : Divyang Parekh
         *    Reason For : Any Seat Functionality
         */
        session_start();
        $service_id = sanitize_text_field($_POST['service_id']);
        $bookigndate = sanitize_text_field($_POST['bookigndate']);
        $_SESSION['_session_base_id'] = $service_id;

        if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && $service_id != '' && bkx_crud_option_multisite('enable_any_seat') == 1 && bkx_crud_option_multisite('select_default_seat') != ''):
            $_enable_any_seat_selected_id = (int)bkx_crud_option_multisite('select_default_seat'); // Default Seat id
            $search = array('bookigndate' => $bookigndate,
                'service_id' => $service_id,
                'status' => $order_statuses,
                'seat_id' => 'any', 'display' => 1);
            $BkxBooking = new BkxBooking();
            $objBookigntime = $BkxBooking->GetBookedRecords($search);
            $BkxBase = new BkxBase('', $service_id);
            $seat_by_base = $BkxBase->get_seat_by_base();
            if (!empty($seat_by_base)) {
                $total_free_seat_all = sizeof($seat_by_base);
                $free_seat_all_arr = $seat_by_base;
            }
            if (!empty($free_seat_all_arr)):
                foreach ($free_seat_all_arr as $seat_id) {
                    $get_range_by_seat[$seat_id] = bkx_get_range($bookigndate, $seat_id);
                }
            endif;
            if (!empty($objBookigntime)):
                foreach ($objBookigntime as $objBooknig):
                    $slot_id = $objBooknig['full_day'];
                    $duration = ($objBooknig['booking_time']) / 60;
                    $seatslots = intval($duration / 15);
                    $_SESSION['_display_seat_slots'] = $seatslots;
                    $booking_slot_range = array();
                    if ($seatslots > 1) {
                        for ($i = 0; $i < $seatslots; $i++) {
                            $slot_id = $objBooknig['full_day'];
                            $booking_slot_range[] = $slot_id + $i;
                        }
                    } else {
                        $booking_slot_range[] = $slot_id;
                    }
                    $booking_seat_id = $objBooknig['seat_id'];
                    $get_booked_range[$slot_id][$booking_seat_id] = $booking_slot_range;
                endforeach;
            endif;
            if (!empty($get_booked_range)):
                foreach ($get_booked_range as $slot_id => $get_booked_slot) {
                    if (function_exists('bkx_get_booked_slot')):
                        $get_data[] = bkx_get_booked_slot($slot_id, $get_booked_range, $free_seat_all_arr, $total_free_seat_all);
                    endif;
                }
                if (!empty($get_data)):
                    foreach ($get_data as $slots_booked_data) {
                        if (!empty($slots_booked_data['slots_blocked'])) {
                            foreach ($slots_booked_data as $slot_ids) {
                                foreach ($slot_ids as $_slot_id) {
                                    $slots_are_booked[] = $_slot_id;
                                    $slots_are_booked = array_unique($slots_are_booked);
                                }
                            }
                        }
                    }
                endif;
            endif;
        else:

            $search = array('bookigndate' => $bookigndate,
                'service_id' => $service_id,
                'status' => $order_statuses,
                'seat_id' => sanitize_text_field($_POST['seatid']), 'display' => 1);
            $BkxBooking = new BkxBooking();
            
            $objBookigntime = $BkxBooking->GetBookedRecords($search);
            if (!empty($objBookigntime)) {
                $full_day = $objBookigntime[0]->full_day;
            }
        endif;
    }

    $BkxBase = new BkxBase('', sanitize_text_field($_POST['service_id']));
    $base_meta_data = $BkxBase->meta_data;

    $base_time_option = $base_meta_data['base_time_option'][0];
    $base_day = $base_meta_data['base_day'][0];
    $base_extended = isset($base_meta_data['base_is_extended']) ? esc_attr($base_meta_data['base_is_extended'][0]) : "";

    $booking_slot_temp = '';
    $booking_slot_arr = array();
    $i = 0;
    $booking_slot = array();

    $objBookigntime = apply_filters('bkx_customise_display_booking_data', $objBookigntime);

    if (!empty($objBookigntime)) {
        $cnt = count($objBookigntime);
        foreach ($objBookigntime as $temp) {

            $wp_postobj = get_post($temp['booking_record_id']);

            /**
             * Updated By  : Divyang Parekh
             * For  : Add Any Seat functionality.*/
            if (bkx_crud_option_multisite('enable_any_seat') == 1 && bkx_crud_option_multisite('select_default_seat') != '' && sanitize_text_field($_POST['seatid']) == 'any'):

                if (!empty($slots_are_booked)):
                    $booking_slot_arr = $slots_are_booked;
                endif;

            else:
                /* Updated By : Madhuri Rokade*/
                /* Reason : To get the multiple booking slots. */
                $duration = ($temp['booking_time']) / 60;
                $seatslots = intval($duration / 15);
                /**
                 * Updated By  : Divyang Parekh
                 * For  : Add Any Seat functionality.
                 */
                if ($seatslots > 1) {
                    for ($i = 0; $i < $seatslots + 1; $i++) {
                        $booking_slot_arr[] = $temp['full_day'] + $i;
                        $checked_booked_slots[] = array('created_by' => $wp_postobj->post_author, 'slot_id' => $temp['full_day'] + $i, 'order_id' => $temp['booking_record_id']);
                    }
                } else {
                    $booking_slot_arr[] = $temp['full_day'];
                    $checked_booked_slots[] = array('created_by' => $wp_postobj->post_author, 'slot_id' => $temp['full_day'], 'order_id' => $temp['booking_record_id']);
                }
            endif;
        }
        foreach ($booking_slot_arr as $slot) {
            if ($slot != "") {
                array_push($booking_slot, $slot);
            }
        }
    }

    $bookingdate = sanitize_text_field($_POST['bookigndate']);
    $range = bkx_get_range($bookingdate, sanitize_text_field($_POST['seatid']));
    //start and end of hours of a day
    $start = 0;
    $end = 24;

    $first = $start * 3600; // Timestamp of the first cell
    $last = $end * 3600; // Timestamp of the last cell

    $step = 15 * 60; // Timestamp increase interval to one cell ahead
    $counter = 1;

    $time_available_color = bkx_crud_option_multisite('bkx_time_available_color');
    $time_available_color = ($time_available_color) ? $time_available_color : '#368200';

    $time_selected_color = bkx_crud_option_multisite('bkx_time_selected_color');
    $time_selected_color = ($time_selected_color) ? $time_selected_color : '#e64754';

    $time_unavailable_color = bkx_crud_option_multisite('bkx_time_unavailable_color');
    $time_unavailable_color = ($time_unavailable_color) ? $time_unavailable_color : 'gray';
    $bkx_time_new_selected = bkx_crud_option_multisite('bkx_time_new_selected');

    if ($base_time_option == 'H') { ?>
        <style type="text/css">
            <?php if(isset($current_order_id) && $$current_order_id!= 0){ ?>
            .booking-status-booked {
                background-color: <?php echo $time_unavailable_color;?>;
            }
            <?php }else{?>
            .booking-status-booked {
                background-color: <?php echo $time_unavailable_color;?>;
            }
            <?php } ?>
            .booking-status-open {
                background-color: <?php echo $time_available_color;?>;
            }

            .booking-status-current {
                background-color: <?php echo $time_selected_color;?>;
            }
            .booking-status-new {
                background-color: <?php echo $time_selected_color;?>;
            }
        </style>
        <p><label><?php echo sprintf(esc_html__('Booking of the day', 'bookingx'), ''); ?></label></p>
        <div class="booking-status-div">
            <div class="booking-status"><?php echo sprintf(esc_html__('Booked', 'bookingx'), ''); ?>
                <div class="booking-status-booked"></div>
            </div>
            <div class="booking-status"><?php echo sprintf(esc_html__('Open', 'bookingx'), ''); ?>
                <div class="booking-status-open"></div>
            </div>
            <div class="booking-status"><?php echo sprintf(esc_html__('Current', 'bookingx'), ''); ?>
                <div class="booking-status-current"></div>
            </div>
            <?php if(isset($current_order_id) && $current_order_id!= 0){ ?>
            <div class="booking-status"><?php echo sprintf(esc_html__('New', 'bookingx'), ''); ?>
                <div class="booking-status-new"></div>
            </div>
            <?php } ?>
  
        </div>
        <br/>
        <div id="date_time_display">
            <?php
            /**
             * Updated By :  Divyang Parekh
             * Please do not edit below code Or carefully
             * Please don't change below div class name, its very sensitive and its depend on some functionality.
             */
            for ($t = $first; $t < $last; $t = $t + $step) {
                $ccs = $t;              // Current cell starts
                $cce = $ccs + $step;    // Current cell ends
                $empty = 'free';

                if (!in_array($counter, $range)) {
                    $empty = 'notavailable';
                } else {
                    if (in_array($counter, $booking_slot))//if that slot booked or not
                    {
                        $empty = 'full';
                    }
                }
                $order_data = bkx_check_slot_order_id($counter, $checked_booked_slots);
                $order_id = isset($order_data['order_id']) && $order_data['order_id'] != '' ? $order_data['order_id'] : 0;
                $created_by = isset($order_data['created_by']) && $order_data['created_by'] != '' ? $order_data['created_by'] : 0;

                ?>
                <div data-booking-id="<?php echo $order_id; ?>" data-user-id="<?php echo $created_by; ?>"
                     class="<?php echo $counter; ?>-is-<?php echo $empty; ?> app_timetable_cell <?php echo $counter; ?> <?php echo $empty; ?> <?php echo sanitize_text_field($_POST['bookigndate']) . '-' . $counter; ?>"
                     id="<?php echo bkx_secs2hours($ccs); ?>"
                     data-slotnumber="<?php echo sanitize_text_field($_POST['bookigndate']) . '-' . $counter; ?>">
                    <input type="hidden" value="<?php echo bkx_secs2hours($ccs); ?>"/>
                    <?php
                    echo bkx_secs2hours($ccs);
                    ?>
                </div>
                <?php
                $counter = $counter + 1;
            }
            ?>
        </div>
        <br/>
    <?php } ?>
    <!-- || $base_time_option == 'M' -->
    <?php if ($base_time_option == 'D' && $base_day > 1 && $base_extended == 'Y') : ?>
    <div class="gfield_description"> If <?php echo $base_alias; ?> can be extended time is set in days/months display
        second calender for end date.
    </div>
    <div class="ginput_container" id="base_extended_calender">
        <input name="datepicker_extended" id="id_datepicker_extended" type="text" value="" class="" tabindex="8"></div>
<?php endif; ?>
    <script type="text/javascript">
        var loader = jQuery('.bookingx-loader');
        loader.hide();
    </script>
    <?php
    wp_die();
}

add_action('wp_ajax_bkx_displaytime_options', 'bkx_displaytime_options_callback');
add_action('wp_ajax_nopriv_bkx_displaytime_options', 'bkx_displaytime_options_callback');

//bkx_get_extra_data_callback
/**
 *
 */
function bkx_get_extra_data_callback()
{
    $extra_id = sanitize_text_field($_POST['extra_id']);
    $BkxExtra = new BkxExtra('', $extra_id);

    $post_title = $BkxExtra->post->post_title;
    if (isset($post_title) && $post_title != '') {
        echo $post_title . ' - ' . bkx_get_current_currency() . $BkxExtra->meta_data['addition_price'][0];
    } else {
        echo 0;
    }

    wp_die();
}

add_action('wp_ajax_bkx_get_extra_data', 'bkx_get_extra_data_callback');
add_action('wp_ajax_nopriv_bkx_get_extra_data', 'bkx_get_extra_data_callback');

//bkx_cancel_booking_callback
/**
 *
 */
function bkx_cancel_booking_callback()
{
    $current_user = wp_get_current_user();
    if (!($current_user instanceof WP_User))
        return;

    $roles = $current_user->roles;
    $data = $current_user->data;
    $booking_record_id = sanitize_text_field($_POST['booking_record_id']);
    $mode = sanitize_text_field($_POST['mode']);
    $is_cust = 0;
    $comment = sanitize_text_field($_POST['comment']);
    if (!empty($comment)) {
        $is_cust = 1;
    }
    $current_user_id = $current_user->ID;
    $date = date("Y-m-d H:i:s");
    $admin_email = get_option('admin_email');

    $BkxBookingObj = new BkxBooking('', $booking_record_id);

    $BkxBookingData = $BkxBookingObj->get_order_meta_data($booking_record_id);

    if (isset($booking_record_id) && $booking_record_id != '' && $mode == 'cancelled'):

        $new_status_obj = get_post_status_object('bkx-cancelled');
        $new_status = $new_status_obj->label;
        $old_status = $BkxBookingObj->get_order_status($booking_record_id);

        $update_order = $BkxBookingObj->update_status('cancelled');
        //Add note in comment for cancelled booking
        $BkxBookingObj->add_order_note(sprintf(__('Successfully update order from %1$s to %2$s.', 'bookingx'), $old_status, $new_status), 0, $manual);

        $BkxBookingObj->add_order_note(sprintf(__(' %1$s ', 'bookingx'), $comment), $is_cust, $manual);

        if ($update_order) :

            $objBooking = $BkxBookingObj->get_order_meta_data($booking_record_id);

            /**
             * Send mail to Customer
             */
            $subject = "Booking Id#{$booking_record_id} : Booking Cancelled";
            $to_customer = $objBooking['email'];
            $message_body = '';
            $message_body .= 'Dear ' . $objBooking['first_name'] . ' ' . $objBooking['last_name'] . ",";
            $message_body .= '<p>Your booking has been successfully cancelled. Booking Id : #' . $booking_record_id . '</p>';

            // To send HTML mail, the Content-type header must be set
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            // Additional headers
            $headers .= 'To: ' . ucfirst($objBooking['first_name']) . ' <' . $objBooking['email'] . '>' . "\r\n";
            $headers .= 'From: Booking Admin <' . $admin_email . '>' . "\r\n";
            // Mail it
            if ($to_customer != ''): wp_mail($to_customer, $subject, $message_body, $headers);endif;

            /**
             * Send mail to Admin
             */
            $admin_headers = 'MIME-Version: 1.0' . "\r\n";
            $admin_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            // Additional headers
            $admin_headers .= 'From: Booking Admin <' . $admin_email . '>' . "\r\n";

            $admin_body = '';
            $admin_body .= 'Dear Admin,';
            $admin_body .= '<p>Booking has been cancelled of ' . $objBooking['first_name'] . ' ' . $objBooking['last_name'] . ' and his booking id is #' . $booking_record_id . '</p>';

            if ($admin_email != ''): wp_mail($admin_email, $subject, $admin_body, $admin_headers); endif;
            echo 1;
        endif;
    endif;
    wp_die();
}

add_action('wp_ajax_bkx_cancel_booking', 'bkx_cancel_booking_callback');
add_action('wp_ajax_nopriv_bkx_cancel_booking', 'bkx_cancel_booking_callback');

/**
 * @param int $get_total_time_of_services
 * @return string
 */
function bkx_total_time_of_services_formatted($get_total_time_of_services = 0)
{

    if (!empty($get_total_time_of_services) && $get_total_time_of_services != 0) {
        $minutes = $get_total_time_of_services;
        $zero = new DateTime('@0');
        $offset = new DateTime('@' . $minutes * 60);
        $diff = $zero->diff($offset);
        if ($minutes > 60) {
            $total_time_of_services_formatted = " ({$diff->format('%h Hours %i Minutes')})";
        } elseif ($minutes == 60) {
            $total_time_of_services_formatted = " ({$diff->format('%h Hour')})";
        } else {
            $total_time_of_services_formatted = " ({$diff->format('%i Minutes')})";
        }
    }
    return $total_time_of_services_formatted;
}
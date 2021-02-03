<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Booking Action Update in Admin side
 */
function bkx_action_status()
{
    $bkx_action_status = explode("_", sanitize_text_field($_POST['status']));

    $order_id = $bkx_action_status[0];
    $order_status = $bkx_action_status[1];
    if (is_multisite()):
        $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
        switch_to_blog($blog_id);
    endif;
    $BkxBooking = new BkxBooking('', $order_id);
    $BkxBooking->update_status($order_status);
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_nopriv_bkx_action_status', 'bkx_action_status');
add_action('wp_ajax_bkx_action_status', 'bkx_action_status');

/**
 * Get Time Format Dropdown Auto Suggest
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

add_action('wp_ajax_nopriv_get_time_format', 'bkx_ajax_get_time_format');
add_action('wp_ajax_get_time_format', 'bkx_ajax_get_time_format');

/**
 * Set Any Seat ID On Resource Listing Area
 */
function bkx_bookingx_set_as_any_seat_callback()
{
    $seat_id = absint(sanitize_text_field($_GET['seat_id']));
    $select_default_seat = bkx_crud_option_multisite('select_default_seat');

    if ('bkx_seat' === get_post_type($seat_id) && $select_default_seat == "") {
        bkx_crud_option_multisite('select_default_seat', $seat_id, 'update');
    } else {
        bkx_crud_option_multisite('select_default_seat', "", 'update');
    }
    wp_safe_redirect(wp_get_referer() ? remove_query_arg(array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer()) : admin_url('edit.php?post_type=bkx_seat'));
    wp_die();
}

add_action('wp_ajax_bookingx_set_as_any_seat', 'bkx_bookingx_set_as_any_seat_callback');
add_action('wp_ajax_nopriv_bookingx_set_as_any_seat', 'bkx_bookingx_set_as_any_seat_callback');

/**
 * Validate User Resource
 */
function bkx_validate_seat_get_user()
{
    if (is_multisite()):
        $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
        switch_to_blog($blog_id);
    endif;
    $role = sanitize_text_field($_POST['data']);
    $user_query = new WP_User_Query(array(
        'role' => $role
    ));
    $free_user_obj = array();
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
                //if (!$seat_obj->have_posts()) :
                $free_user_obj[] = get_user_by('id', $user_id);
                //endif;
            }
        }
    }
    echo json_encode($free_user_obj);
    wp_die(); // this is required to return a proper result
}

add_action('wp_ajax_bkx_validate_seat_get_user', 'bkx_validate_seat_get_user');
add_action('wp_ajax_nopriv_bkx_validate_seat_get_user', 'bkx_validate_seat_get_user');

/**
 * Get User Data when on change event on Resource edit page Admin side
 * @param $userobj
 */
function bkx_get_user_data($userobj)
{
    if (is_multisite()):
        $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
        switch_to_blog($blog_id);
    endif;

    $user_id = sanitize_text_field($_POST['data']);

    if (empty($user_id))
        return;

    $userobj = get_user_by('id', $user_id);
    $user_prefill = array();
    if (!empty($userobj) && !is_wp_error($userobj)) {
        $user_email = $userobj->user_email;
        $seat_post_id = get_user_meta($user_id, 'seat_post_id', true);
        if (isset($seat_post_id) && $seat_post_id != "") {
            $user_phone = get_post_meta($seat_post_id, 'seatPhone', true);
        }
        $user_prefill['email'] = $user_email;
        $user_prefill['phone'] = $user_phone;
    }
    echo json_encode($user_prefill);
    wp_die(); // this is required to return a proper result
}

add_action('wp_ajax_bkx_get_user_data', 'bkx_get_user_data');
add_action('wp_ajax_nopriv_bkx_get_user_data', 'bkx_get_user_data');
/**
 * Bookings Listing Admin side When Click View Button call this function
 * @return int
 * @throws Exception
 */
function bkx_action_view_summary_callback()
{
    if (is_multisite()):
        $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
        switch_to_blog($blog_id);
    endif;
    $post_id = sanitize_text_field($_POST['post_id']);
    if (empty($post_id))
        return 0;

    $get_post = get_post($post_id);
    $Bkx_Meta_Boxes = new Bkx_Meta_Boxes();
    $order_full_output = $Bkx_Meta_Boxes->bookingx_output($get_post, 'ajax');
    $bookingx_note_output = $Bkx_Meta_Boxes->bookingx_note_output($get_post, 'ajax');
    $bookingx_reassign_output = $Bkx_Meta_Boxes->bookingx_reassign_output($get_post, 'ajax');
    // $order_summary_output = '<div class="bkx-order_summary"> <h3>Booking #'.$post_id.' Notes </h3></div>';
	$bkx_dashboard_column_selected = bkx_crud_option_multisite("bkx_dashboard_column");
	$bkx_booking_columns_data = !empty($bkx_dashboard_column_selected) ? $bkx_dashboard_column_selected : Bookingx_Admin::bkx_booking_columns_data();
	$calculate_cols = sizeof($bkx_booking_columns_data) ;
	$total_cols  = !empty($bkx_booking_columns_data) ? $calculate_cols  - 3 : 10;

    $output = '<td class="section-1 '.$calculate_cols.'" colspan="'.$total_cols.'">' . $order_full_output . '</td>
       <td colspan="3" class="section-2">' . $bookingx_note_output . '' . $bookingx_reassign_output . '</td>';
    echo apply_filters('bkx_order_summary_output', $output,$post_id);
    wp_die();
}

add_action('wp_ajax_bkx_action_view_summary', 'bkx_action_view_summary_callback');
/**
 * Admin Booking view add custom note.
 * @return int
 */
function bkx_action_add_custom_note_callback()
{
    if (is_multisite()):
        $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
        switch_to_blog($blog_id);
    endif;
    $booking_id = sanitize_text_field($_POST['booking_id']);
    $bkx_custom_note = sanitize_text_field($_POST['bkx_custom_note']);

    if (empty($booking_id) || empty($bkx_custom_note))
        return 0;

    $bookingObj = new BkxBooking('', $booking_id);
    $bookingObj->add_order_note(sprintf(__($bkx_custom_note, 'bookingx'), ''), 0);
    wp_die();
}

add_action('wp_ajax_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback');
add_action('wp_ajax_nopriv_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback');
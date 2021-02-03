<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$plugin = plugin_basename(__FILE__);

function bkx_process_mail_by_status($booking_id, $subject, $content, $email = null, $is_customer = false)
{
    if (empty($booking_id))
        return;

    if (!empty($content)) {
        $message_body = $content;
    }

    $amount_pending = 0;
    $event_address = "";
    $amount_paid = 0;
    $bkx_business_name = bkx_crud_option_multisite("bkx_business_name");
    $bkx_business_phone = bkx_crud_option_multisite("bkx_business_phone");
    $bkx_business_email = bkx_crud_option_multisite("bkx_business_email");
    $business_address_1 = bkx_crud_option_multisite("bkx_business_address_1");
    $business_address_2 = bkx_crud_option_multisite("bkx_business_address_2");
    $bkx_business_city = bkx_crud_option_multisite("bkx_business_city");
    $bkx_business_state = bkx_crud_option_multisite("bkx_business_state");
    $bkx_business_zip = bkx_crud_option_multisite("bkx_business_zip");
    $bkx_business_country = bkx_crud_option_multisite("bkx_business_country");

    $bookingObj = new BkxBooking();
    $order_meta = $bookingObj->get_order_meta_data($booking_id);
    $order_status = $bookingObj->get_order_status($booking_id);
    $currency = isset($order_meta['currency']) && $order_meta['currency'] != '' ? $order_meta['currency'] : bkx_get_current_currency();

    $BkxBaseObj = $order_meta['base_arr']['main_obj'];
    $BkxSeatObj = $order_meta['seat_arr']['main_obj'];
    $BkxSeatInfo = $BkxSeatObj->seat_personal_info;

    $admin_email = $email->recipient;
    $to = isset($is_customer) && $is_customer == true ? $order_meta['email'] : $admin_email;
    $mail_type = isset($email->email_type) && !empty($email->email_type) ? $email->get_content_type($email->email_type) : 'text/html';
    // multiple recipients

    //change request for template tags calculate booking duration 7/4/2013
    $booking_time = $order_meta['booking_time']['in_sec'];
    //conver second into minutes
    $booking_time_minutes = $booking_time / 60;
    $flag_hours = false;
    if ($booking_time_minutes > 60) {
        $booking_time_hours = $booking_time / (60 * 60);
        $flag_hours = true;
    }
    $booking_duration = "";
    if ($flag_hours == true) {
        $booking_duration = $booking_time_hours . "Hours ";
    } else {
        $booking_duration = $booking_time_minutes . "Minutes ";
    }

    $base_time_option = get_post_meta($booking_id, 'base_time_option', true);
    $base_time_option = (isset($base_time_option) && $base_time_option != "") ? $base_time_option : "H";

    if (isset($base_time_option) && $base_time_option == "H") {
        $booking_duration = getDuration($order_meta);
    } else {
        list($date_data, $booking_duration, $start_date) = getDayDateDuration($booking_id);
    }

    $booking_duration = apply_filters('bkx_booking_duration', $booking_duration, $order_meta );
    $booking_total_duration = apply_filters('bkx_booking_total_duration', $booking_duration, $order_meta );

    // end booking duration calculation 7/4/2013
    $addition_list = '-';
    if (isset($order_meta['addition_ids']) && $order_meta['addition_ids'] != '') {
        $addition_list = "";
        $BkxExtra = new BkxExtra();
        $BkxExtraObj = $BkxExtra->get_extra_by_ids(rtrim($order_meta['addition_ids'], ","));
        foreach ($BkxExtraObj as $addition) {
            $addition_list .= $addition->get_title() . ",";
        }
    }
    $payment_data = get_post_meta($order_meta['order_id'], 'payment_meta', true);
    if (!empty($payment_data)) {
        $transactionID = $payment_data['transactionID'];
        $amount_paid = $payment_data['pay_amt'];
        $amount_pending = ($order_meta['total_price'] - $payment_data['pay_amt']);
    } else {
        $transactionID = '-';
        $amount_pending = $order_meta['total_price'];
    }
    if (isset($BkxSeatInfo['seat_street']) && $BkxSeatInfo['seat_street']) {
        $event_address .= $BkxSeatInfo['seat_street'] . ", ";
    } else {
        $event_address .= "{$business_address_1}, {$business_address_2}";
    }

    if (isset($BkxSeatInfo['seat_city']) && $BkxSeatInfo['seat_city']) {
        $event_address .= $BkxSeatInfo['seat_city'] . ", ";
    } else {
        $event_address .= "{$bkx_business_city} ,";
    }

    if (isset($BkxSeatInfo['seat_state']) && $BkxSeatInfo['seat_state']) {
        $event_address .= $BkxSeatInfo['seat_state'] . ", ";
    } else {
        $event_address .= "{$bkx_business_state} ,";
    }

    if (isset($BkxSeatInfo['seat_postcode']) && $BkxSeatInfo['seat_postcode']) {
        $event_address .= $BkxSeatInfo['seat_postcode'];
    } else {
        $event_address .= $bkx_business_zip;
    }

    if (isset($BkxSeatInfo['seat_country']) && $BkxSeatInfo['seat_country']) {
        $event_address .= $BkxSeatInfo['seat_country'];
    } else {
        $event_address .= $bkx_business_country;
    }
    $total_price = ($order_meta['total_price'] != '') ? $order_meta['total_price'] : 0;
    $amount_paid = bkx_get_formatted_price($amount_paid);
    $total_price = bkx_get_formatted_price($total_price);
    $amount_pending = bkx_get_formatted_price($amount_pending);

    $subject = str_replace("{booking_number}", $order_meta['order_id'], $subject);
    $subject = str_replace("{site_title}", get_bloginfo(), $subject);
    $subject = str_replace("{site_address}", get_site_url(), $subject);
    $subject = str_replace("{fname}", $order_meta['first_name'], $subject);
    $subject = str_replace("{lname}", $order_meta['last_name'], $subject);
    $subject = str_replace("{total_price}", $currency . $order_meta['total_price'] . ' ' . bkx_crud_option_multisite('currency_option'), $subject);
    $subject = str_replace("{txn_id}", $transactionID, $subject);
    $subject = str_replace("{order_id}", $order_meta['order_id'], $subject);
    $subject = str_replace("{total_duration}", $booking_total_duration, $subject);
    $subject = str_replace("{total_price}", $total_price , $subject);
    $subject = str_replace("{siteurl}", site_url(), $subject);
    $subject = str_replace("{seat_name}", $order_meta['seat_arr']['title'], $subject);
    $subject = str_replace("{base_name}", $order_meta['base_arr']['title'], $subject);
    $subject = str_replace("{additions_list}", $addition_list, $subject);
    $subject = str_replace("{time_of_booking}", $booking_duration, $subject);
    $subject = str_replace("{date_of_booking}", $order_meta['booking_start_date'], $subject);
    $subject = str_replace("{location_of_booking}", $event_address, $subject);
    $subject = str_replace("{amount_paid}", $amount_paid, $subject);
    $subject = str_replace("{amount_pending}", $amount_pending, $subject);
    $subject = str_replace("{business_name}", $bkx_business_name, $subject);
    $subject = str_replace("{business_email}", $bkx_business_email, $subject);
    $subject = str_replace("{business_phone}", $bkx_business_phone, $subject);
    $subject = str_replace("{booking_status}", $order_status, $subject);
    $subject = str_replace("{booking_edit_url}", edit_booking_url($booking_id), $subject);


    $message_body = str_replace("{fname}", $order_meta['first_name'], $message_body);
    $message_body = str_replace("{lname}", $order_meta['last_name'], $message_body);
    $message_body = str_replace("{total_price}", $currency . $order_meta['total_price'] . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace("{txn_id}", $transactionID, $message_body);
    $message_body = str_replace("{order_id}", $order_meta['order_id'], $message_body);
    $message_body = str_replace("{phone}", $order_meta['phone'], $message_body);
    $message_body = str_replace("{email}", $order_meta['phone'], $message_body);
    $message_body = str_replace("{total_duration}", $booking_total_duration, $message_body);
    $message_body = str_replace("{total_price}", $total_price . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace("{siteurl}", site_url(), $message_body);
    $message_body = str_replace("{seat_name}", $order_meta['seat_arr']['title'], $message_body);
    $message_body = str_replace("{base_name}", $order_meta['base_arr']['title'], $message_body);
    $message_body = str_replace("{additions_list}", $addition_list, $message_body);
    $message_body = str_replace("{time_of_booking}", $booking_duration, $message_body);
    $message_body = str_replace("{date_of_booking}", $order_meta['booking_start_date'], $message_body);
    $message_body = str_replace("{location_of_booking}", $event_address, $message_body);
    $message_body = str_replace("{total_price}", $total_price , $message_body);
    $message_body = str_replace("{amount_paid}", $amount_paid, $message_body);
    $message_body = str_replace("{amount_pending}", $amount_pending , $message_body);
    $message_body = str_replace("{business_name}", $bkx_business_name, $message_body);
    $message_body = str_replace("{business_email}", $bkx_business_email, $message_body);
    $message_body = str_replace("{business_phone}", $bkx_business_phone, $message_body);
    $message_body = str_replace("{booking_status}", $order_status, $message_body);
    $message_body = str_replace("{booking_edit_url}", edit_booking_url($booking_id), $message_body);
	$message_body = apply_filters('bkx_booking_created_email_content', $message_body, $booking_id );

    // Mail it
    bkx_mail_format_and_send_process($subject, $message_body, $to, $mail_type);
}

function bkx_generate_template_page($option_key, $content, $page_title, $page_id, $parent_id = null)
{
    $page_data = array();
    if (isset($page_id) && $page_id != "") {
        $page_data = get_post($page_id);
    }
    if (!empty($page_data)) {
        bkx_crud_option_multisite($option_key, $page_id, 'update');
    } else {
        $page_array = array();
        $page_array['post_title'] = $page_title;
        $page_array['post_content'] = $content;
        $page_array['post_status'] = 'publish';
        $page_array['post_type'] = 'page';
        $page_array['comment_status'] = 'closed';
        $page_array['ping_status'] = 'closed';
        $page_array['post_category'] = array(1);
        if (isset($parent_id) && $parent_id > 0) {
            $page_array['post_parent'] = $parent_id;
        }
        $page_id = wp_insert_post($page_array);
        bkx_crud_option_multisite($option_key, $page_id, 'update');
    }
}

//Create default Template Pages Function
add_action('init', 'bkx_create_default_template');
function bkx_create_default_template()
{
    $bkx_set_booking_page = bkx_crud_option_multisite("bkx_set_booking_page");
    $bkx_dashboard_page_id = bkx_crud_option_multisite('bkx_dashboard_page_id');
    $bkx_edit_booking_page_id = bkx_crud_option_multisite('bkx_edit_booking_page_id');
    $my_account_id = bkx_crud_option_multisite('bkx_my_account_page_id');
    bkx_generate_template_page("bkx_dashboard_page_id", "[bkx_dashboard]", __("Dashboard", 'bookingx'), $bkx_dashboard_page_id);
    bkx_generate_template_page("bkx_set_booking_page", "[bkx_booking_form]", __("Booking Form", 'bookingx'), $bkx_set_booking_page);
    bkx_generate_template_page("bkx_edit_booking_page_id", "[bkx_booking_form]", __("Edit Booking", 'bookingx'), $bkx_edit_booking_page_id, $bkx_dashboard_page_id);
    bkx_generate_template_page("bkx_my_account_page_id", "[bkx_my_account]", __("My Account", 'bookingx'), $my_account_id, $bkx_dashboard_page_id);
}

// Add settings link on plugin page
add_filter("plugin_action_links_{$plugin}", 'bkx_plugin_settings_link');
function bkx_plugin_settings_link($links)
{
    $settings_link = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=bkx_booking&page=bkx-setting&bkx_tab=bkx_general">Settings</a>';
    //array_unshift($links, $settings_link);
    return $links;
}

add_filter('query_vars', 'bkx_query_vars');

add_filter('get_search_query', 'bkx_search_label');
function bkx_search_label($query)
{
    global $pagenow, $typenow;

    if ('edit.php' !== $pagenow || 'bkx_booking' !== $typenow || !get_query_var('bkx_booking_search') || !isset($_GET['s'])) { // WPCS: input var ok.
        return $query;
    }

    return bkx_clean(wp_unslash($_GET['s'])); // WPCS: input var ok, sanitization ok.
}

function bkx_query_vars($qv)
{
    $qv[] = 'search_by_dates';
    $qv[] = 'search_by_selected_date';
    $qv[] = 'seat_view';
    $qv[] = 'bkx_booking_search';
    return $qv;
}

add_filter('post_row_actions', 'bkx_modify_list_row_actions', 2, 100);
function bkx_modify_list_row_actions($actions, $post)
{
    if (in_array($post->post_type, array('bkx_booking'))) {
        if (isset($actions['inline hide-if-no-js'])) {
            unset($actions['inline hide-if-no-js']);
        }
        if(!empty($actions) && isset($actions['edit'])){
	        $view_link = "";
	        $orderObj = new BkxBooking();
	        $order_meta = $orderObj->get_order_meta_data($post->ID);
	        $get_order_status = $orderObj->get_order_status($post->ID);
	        $booking_start_date = $order_meta['booking_start_date'];
	        $edit_link = get_edit_post_link($post->ID);
	        if(isset($get_order_status,$booking_start_date) && ( $get_order_status == 'Cancelled' || $get_order_status == 'Completed' || time() > strtotime($booking_start_date) )){
	        	unset($actions['edit']);
	        	unset($actions['trash']);
	        }else{
		        $actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>',
			        esc_url( $edit_link ),
			        esc_html( __( 'Reschedule', 'bookingx' ) ) );
	        }

	        if (!empty($order_meta) && isset($order_meta['first_name'])) {
		        $view_link = 'javascript:bkx_view_summary(\'' . $post->ID . '\',\'' . $order_meta['first_name'] . ' ' . $order_meta['last_name'] . '\')';
	        }
	        $actions['view'] = sprintf( '<a href="%1$s">%2$s</a>',
		                $view_link,
			        esc_html( __( 'View', 'bookingx' ) ) );

        }
    }
    return $actions;
}

//add_filter('comments_clauses', 'bkx_exclude_order_comments', 10, 1);
function bkx_exclude_order_comments($clauses)
{
    global $wpdb;
    $type_array = array(
        'bkx_seat',
        'bkx_base',
        'bkx_addition',
        'bkx_booking'
    );
    if (!$clauses['join']) {
        $clauses['join'] = '';
    }
    if (!stristr($clauses['join'], "JOIN $wpdb->posts ON")) {
        $clauses['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
    }
    if ($clauses['where']) {
        $clauses['where'] .= ' AND ';
    }
    $clauses['where'] .= " $wpdb->posts.post_type NOT IN ('" . implode("','", $type_array) . "') ";
    return $clauses;
}

// Count comments
add_filter('wp_count_comments', 'bkx_count_comments', 10, 2);
function bkx_count_comments($stats, $post_id)
{
    global $wpdb;

    if (0 === $post_id) {
        $stats = get_transient('bkx_count_comments');
        if (!$stats) {
            $stats = array();
	        $count = $wpdb->get_results($wpdb->prepare("SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} WHERE comment_type != 'booking_note' GROUP BY comment_approved"), ARRAY_A);
            $total = 0;
            $approved = array(
                '0' => 'moderated',
                '1' => 'approved',
                'spam' => 'spam',
                'trash' => 'trash',
                'post-trashed' => 'post-trashed'
            );
            foreach ((array)$count as $row) {
                // Don't count post-trashed toward totals
                if ('post-trashed' != $row['comment_approved'] && 'trash' != $row['comment_approved']) {
                    $total += $row['num_comments'];
                }
                if (isset($approved[$row['comment_approved']])) {
                    $stats[$approved[$row['comment_approved']]] = $row['num_comments'];
                }
            }
            $stats['total_comments'] = $total;
            $stats['all'] = $total;
            foreach ($approved as $key) {
                if (empty($stats[$key])) {
                    $stats[$key] = 0;
                }
            }
            $stats = (object)$stats;
            set_transient('bkx_count_comments', $stats);
        }
    }
    return $stats;
}

// Disable post type view mode options
add_filter('view_mode_post_types', 'bkx_disable_view_mode_options');
function bkx_disable_view_mode_options($post_types)
{
    unset($post_types['bkx_seat'], $post_types['bkx_addition'], $post_types['bkx_booking'], $post_types['bkx_base']);
    return $post_types;
}

add_action('load-edit.php', 'bkx_bulk_action');
function bkx_bulk_action()
{
    if (empty($_REQUEST['action']))
        return;
    $action = sanitize_text_field($_REQUEST['action']);
    $order_statuses = bkx_get_order_statuses();
    $new_status = substr($action, 5); // get the status name from action
    $report_action = 'marked_' . $new_status;
    if (!isset($order_statuses['bkx-' . $new_status])) {
        return;
    }
    $changed = 0;

    $post_ids = array_map('absint', (array)$_REQUEST['post']);
	$post_ids = array_map('sanitize_text_field', wp_unslash($post_ids));
    foreach ($post_ids as $post_id) {
        $order = new BkxBooking('', $post_id);
        $order->update_status($new_status);
        do_action('bkx_order_edit_status', $post_id, $new_status);
        $changed++;
    }
    $sendback = add_query_arg(array(
        'post_type' => 'bkx_booking',
        $report_action => true,
        'changed' => $changed,
        'ids' => join(',', $post_ids)
    ), '');

    if (isset($_GET['post_status'])) {
        $sendback = add_query_arg('post_status', sanitize_text_field($_GET['post_status']), $sendback);
    }
    wp_redirect(esc_url_raw($sendback));
    exit();
}

/**
 * Generate Rewrite Rule for Booking Page
 */
function bookingx_url_rewrite_tag()
{
    add_rewrite_tag('%bookingx_type%', '([^&]+)');
    add_rewrite_tag('%bookingx_type_id%', '([^&]+)');
}

add_action('init', 'bookingx_url_rewrite_tag', 10, 0);

function bookingx_url_rewrite_rule()
{
    $bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
    if (isset($bkx_set_booking_page) && $bkx_set_booking_page != '') {
        $page_obj = get_post($bkx_set_booking_page);
        $page_slug = $page_obj->post_name;
    }
    add_rewrite_rule('^' . $page_slug . '/([^/]+)/([^/]+)/?', 'index.php?page_id=' . $bkx_set_booking_page . '&bookingx_type=$matches[1]&bookingx_type_id=$matches[2]', 'top');
}

add_action('init', 'bookingx_url_rewrite_rule', 10, 0);

add_action('current_screen', function ($current_screen) {
    if ($current_screen->id === 'edit-bkx_booking')
        add_filter("views_{$current_screen->id}", 'bkx_list_table_views_filter');
}, 20);

function bkx_list_table_views_filter($view)
{
    global $wp_query, $wpdb;
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
        $current_role = $current_user->roles[0];
        $current_seat_id = $current_user->ID;
        if ($bkx_seat_role == $current_role && $current_seat_id != '') {
            unset($view);
            $seat_post_id = get_user_meta($current_seat_id, 'seat_post_id', true);
            if (isset($seat_post_id) && $seat_post_id != '') {
	            $querystr = $wpdb->prepare(
		            "SELECT $wpdb->posts.post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts}, {$wpdb->postmeta}
						  WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
						  AND $wpdb->posts.post_type = 'bkx_booking'
						  AND $wpdb->postmeta.meta_key = 'seat_id' 
						  AND  $wpdb->postmeta.meta_value = %s
						  AND $wpdb->posts.post_status IN ('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed', 'bkx-cancelled')
						  GROUP BY $wpdb->posts.post_status", $seat_post_id );
                $bkx_post_count = $wpdb->get_results($querystr, OBJECT);
                $query_request = $wp_query->query['post_status'];
                $status_counts = array();
                if (!empty($bkx_post_count)) {
                    $total_post = 0;
                    foreach ($bkx_post_count as $value) {
                        $post_status = get_post_status_object($value->post_status);
                        if ($value->post_status == 'bkx-pending') {
                            $post_status->label = 'Pending';
                        }
                        $status_counts[$value->post_status][] = array('label' => $post_status->label, 'count' => $value->num_posts);
                        $total_post += $value->num_posts;
                    }
                }
                if ($query_request == '') {
                    $all_class = 'class="current"';
                } else {
                    $all_class = "";
                }
                $view['all'] = '<a ' . $all_class . ' href="edit.php?post_type=bkx_booking"> All <span class="count">(' . $total_post . ')</span></a>';
                if (!empty($status_counts)) {
                    foreach ($status_counts as $key => $status) {
                        if ($query_request == $key) {
                            $add_class = 'class="current"';
                        } else {
                            $add_class = "";
                        }
                        $view[$key] = '<a ' . $add_class . ' href="edit.php?post_status=' . $key . '&post_type=bkx_booking">' . $status[0]['label'] . ' <span class="count">(' . $status[0]['count'] . ')</span></a>';
                    }
                }
            }
        }
    }
    return $view;
}

function bkx_cal_total_tax($total_price)
{
    $bkx_tax_rate = bkx_crud_option_multisite("bkx_tax_rate");
    $bkx_tax_name = bkx_crud_option_multisite("bkx_tax_name");
    $bkx_prices_include_tax = bkx_crud_option_multisite("bkx_prices_include_tax");
    $total_tax_is = 0;
    if (empty($bkx_tax_rate) || $bkx_tax_rate < 0 || empty($total_price) || !is_numeric($total_price) || !is_numeric($bkx_tax_rate))
        return $total_tax_is;

    $total_tax = array();
    $include_tax = '';
    $total_tax_is = ($total_price * $bkx_tax_rate) / 100;
    $total_tax_is = number_format((float)$total_tax_is, 2, '.', '');

    if ($bkx_prices_include_tax == 1) { //Yes, I will enter prices inclusive of tax
        $total_price = $total_price - $total_tax_is;
        $include_tax = 'minus';
    }
    if ($bkx_prices_include_tax == 0) { //No, I will enter prices exclusive of tax
        $total_price = $total_price;
        $include_tax = 'plus';
    }
    $grand_total = $total_price + $total_tax_is;
    $total_tax_is = number_format((float)$total_tax_is, 2, '.', '');
    $total_price = number_format((float)$total_price, 2, '.', '');

    if (!empty($total_price) && is_numeric($total_price)) {
        $total_tax['total_price'] = $total_price;
        $total_tax['total_tax'] = $total_tax_is;
        $total_tax['tax_name'] = $bkx_tax_name;
        $total_tax['calculation'] = $include_tax;
        $total_tax['tax_rate'] = $bkx_tax_rate;
        $total_tax['grand_total'] = number_format((float)$grand_total, 2, '.', '');
    }
    return $total_tax;
}

function bkx_secs2hours($secs)
{
    $min = (int)($secs / 60);
    $hours = "00";
    if ($min < 60)
        $hours_min = $hours . ":" . $min;
    else {
        $hours = (int)($min / 60);
        if ($hours < 10)
            $hours = "0" . $hours;
        $mins = $min - $hours * 60;
        if ($mins < 10)
            $mins = "0" . $mins;
        $hours_min = $hours . ":" . $mins;
    }
    //if ( $this->time_format )
    $hours_min = date('H:i', strtotime($hours_min . ":00"));
    return $hours_min;
}

function bkx_getDayName($day)
{
    if ($day == 0) {
        $day_name = sprintf(__('Sunday', 'bookingx'), '');
    } else if ($day == 1) {
        $day_name = sprintf(__('Monday', 'bookingx'), '');
    } else if ($day == 2) {
        $day_name = sprintf(__('Tuesday', 'bookingx'), '');
    } else if ($day == 3) {
        $day_name = sprintf(__('Wednesday', 'bookingx'), '');
    } else if ($day == 4) {
        $day_name = sprintf(__('Thursday', 'bookingx'), '');
    } else if ($day == 5) {
        $day_name = sprintf(__('Friday', 'bookingx'), '');
    } else if ($day == 6) {
        $day_name = sprintf(__('Saturday', 'bookingx'), '');
    }
    return $day_name;
}

function bkx_getMinsSlot($mins)
{
    if (intval($mins) == 0) {
        $slot = 1;
    } else if (intval($mins) == 15) {
        $slot = 2;
    } else if (intval($mins) == 30) {
        $slot = 3;
    } else if (intval($mins) == 45) {
        $slot = 4;
    }

    return $slot;
}

function bkx_admin_actions()
{
    $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
    $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
    $bkx_alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
    $bkx_seat_taxonomy_status = bkx_crud_option_multisite('bkx_seat_taxonomy_status');
    $bkx_base_taxonomy_status = bkx_crud_option_multisite('bkx_base_taxonomy_status');
    $bkx_addition_taxonomy_status = bkx_crud_option_multisite('bkx_addition_taxonomy_status');

    add_submenu_page('edit.php?post_type=bkx_booking', __('Settings', 'bookingx'), __('Settings', 'bookingx'), 'manage_options', 'bkx-setting', 'bkx_setting_page_callback');

    if (isset($bkx_seat_taxonomy_status) && $bkx_seat_taxonomy_status == 1) {
        add_submenu_page('edit.php?post_type=bkx_booking', __("$alias_seat Category", 'bookingx'), __("$alias_seat Category", 'bookingx'), 'manage_options', 'edit-tags.php?taxonomy=bkx_seat_cat&post_type=bkx_seat');
    }

    if (isset($bkx_base_taxonomy_status) && $bkx_base_taxonomy_status == 1) {
        add_submenu_page('edit.php?post_type=bkx_booking', __("$bkx_alias_base Category", 'bookingx'), __("$bkx_alias_base Category", 'bookingx'), 'manage_options', 'edit-tags.php?taxonomy=bkx_base_cat&post_type=bkx_base');
    }

    if (isset($bkx_addition_taxonomy_status) && $bkx_addition_taxonomy_status == 1) {
        add_submenu_page('edit.php?post_type=bkx_booking', __("$bkx_alias_addition Category", 'bookingx'), __("$bkx_alias_addition Category", 'bookingx'), 'manage_options', 'edit-tags.php?taxonomy=bkx_addition_cat&post_type=bkx_addition');
    }
}

add_action('admin_menu', 'bkx_admin_actions');

//add_filter('custom_menu_order', 'bkx_reorder_admin_menu_pages');
function bkx_reorder_admin_menu_pages($menu_ord)
{
    global $submenu;
    // Enable the next line to see all menu orders
	//echo "<pre>".print_r($submenu, true)."</pre>";
    $arr = array();
    $find_submenu = array();
    if (!empty($submenu['edit.php?post_type=bkx_booking'])) {
        $submenu_obj = $submenu['edit.php?post_type=bkx_booking'];
        //echo "<pre>".print_r($submenu_obj, true)."</pre>";
        foreach ($submenu_obj as $key => $submenu_data) {
            //find Seat Post type Key
            if ($submenu_data[2] == 'edit.php?post_type=bkx_seat') {
                $find_submenu['bkx_seat_post_type'] = $key;
            }
            if ($submenu_data[2] == 'edit-tags.php?taxonomy=bkx_seat_cat&post_type=bkx_seat') {
                $find_submenu['bkx_seat_category'] = $key;
            }
            if ($submenu_data[2] == 'edit.php?post_type=bkx_base') {
                $find_submenu['bkx_base_post_type'] = $key;
            }
            if ($submenu_data[2] == 'edit-tags.php?taxonomy=bkx_base_cat&post_type=bkx_base') {
                $find_submenu['bkx_base_category'] = $key;
            }
            if ($submenu_data[2] == 'edit.php?post_type=bkx_addition') {
                $find_submenu['bkx_addition_post_type'] = $key;
            }
            if ($submenu_data[2] == 'edit-tags.php?taxonomy=bkx_addition_cat&post_type=bkx_addition') {
                $find_submenu['bkx_addition_category'] = $key;
            }
	        if ($submenu_data[2] == 'edit.php?post_type=bkx_packages') {
		        $find_submenu['bkx_packages_post_type'] = $key;
	        }
        }
    }
    //echo "<pre>".print_r($find_submenu, true)."</pre>";
    if (isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])) {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][5];     //my original order was 5,10,15,16,17,18
	    $arr[] = $submenu['edit.php?post_type=bkx_booking'][12];
    }
    if (isset($find_submenu['bkx_seat_category']) && $find_submenu['bkx_seat_category'] != " ") {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_seat_category']];
    }
    if (isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])) {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][13];
    }
    if (isset($find_submenu['bkx_base_category']) && $find_submenu['bkx_base_category'] != " ") {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_base_category']];
    }
    if (isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])) {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][14];
    }
    if (isset($find_submenu['bkx_addition_category']) && $find_submenu['bkx_addition_category'] != " ") {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_addition_category']];
    }
	$arr[] = apply_filters('bkx_menu_before_settings_sorting', $submenu);
    if (isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])) {
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][15];
    }
	$arr[] = apply_filters('bkx_menu_after_settings_sorting', $submenu);
    $submenu['edit.php?post_type=bkx_booking'] = $arr;
    //echo "<pre>".print_r($arr, true)."</pre>";
    return $menu_ord;
}

function bkx_plugin_add_settings_link($links)
{
    $settings_link = '<a href="edit.php?post_type=bkx_booking&page=bkx-setting">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}

add_filter("plugin_action_links_$plugin", 'bkx_plugin_add_settings_link');

add_action('bkx_make_booking_hook', 'bkx_make_booking_hook_call_back');
/**
 * @param $post
 * @throws Exception
 */
function bkx_make_booking_hook_call_back($post){
	if (empty($_POST['seat_id']) && empty($_POST['base_id']) || empty($_POST['starting_slot']) || empty($_POST['time_option']) )
        return;

    $bkx_return_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
    $Bkxbooking = new BkxBooking();
    $booking_data = $Bkxbooking->MakeBookingProcess($post , false);
    $booking = json_decode($booking_data, true);
    $order_id = $booking['meta_data']['order_id'];
    $bkx_return_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_return_url);
    if(empty($order_id)){
        return;
    }
    $booking_meta = array('order_id' => $order_id);
    $process_response = array('success' => true, 'data' => $booking_meta);
    update_post_meta($order_id, 'bkx_capture_payment', $process_response);
    echo '<meta http-equiv="refresh" content="0;url=' . $bkx_return_url . '">';
}

add_action( 'admin_notices', 'bkx_admin_notice_error' );
function bkx_admin_notice_error() {
    $message_data = array();
    $admin_screen = get_current_screen();
    if(isset($_REQUEST['bkx_tab']) && sanitize_text_field($_REQUEST['bkx_tab']) == 'bkx_payment' || $admin_screen->id == 'bkx_seat' || $admin_screen->post_type == 'bkx_seat'){
        $BkxPaymentCore = new BkxPaymentCore();
        $bkx_get_available_gateways = $BkxPaymentCore->is_activate_payment_gateway();
        $gateway_status = apply_filters('bkx_is_payment_gateway_activate', $bkx_get_available_gateways );

        if(empty($gateway_status) || $bkx_get_available_gateways == 1){
            $class = 'notice notice-info is-dismissible';
            $message = __( 'No payment merchant has been select. If you require pre-payment for your booking please enable a payment gateway.', 'bookingx' );
            $message_data['class'] = $class;
            $message_data['message'] = $message;
        }
    }
    if(!empty($message_data)){
        $message_data = apply_filters('bkx_admin_admin_notices', $message_data );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $message_data['class'] ), esc_html( $message_data['message'] ) );
    }
}
function bkx_excerpt_more( $more ) {

    if ( ! is_single() ) {
        $more = sprintf( '<a class="read-more" href="%1$s"> %2$s </a>',
            get_permalink( get_the_ID() ),
            __( 'Read More', 'bookingx' )
        );
    }
    return $more;
}
add_filter( 'excerpt_more', 'bkx_excerpt_more',999);

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function bkx_custom_excerpt_length( $length ) {
    return 5;
}
add_filter( 'excerpt_length', 'bkx_custom_excerpt_length',999);

add_filter('bkx_booking_detail_load_before', 'bkx_booking_detail_load_before_action');
function bkx_booking_detail_load_before_action( $booking_id ){
    $user = get_userdata(get_current_user_id());
    $user_id = get_current_user_id();
    $is_mobile = 0;
    if (wp_is_mobile()) {
        $is_mobile = 1;
    }
    $is_able_cancelled = false;

    $orderObj = new BkxBooking(array(),$booking_id);
    try {
        $order_meta = $orderObj->get_order_meta_data($booking_id);
    } catch (Exception $e) {
    }
    $booking_created_by = $order_meta['created_by'];

    $order_status = $orderObj->get_order_status($booking_id);


    $payment_meta = get_post_meta($booking_id, 'payment_meta', true);
    $currency = $order_meta['currency'];
    $payment_status = isset($payment_meta['payment_status']) && $payment_meta['payment_status'] != "" ? $payment_meta['payment_status'] : "";
    $check_total_payment = isset($payment_meta['pay_amt']) && $payment_meta['pay_amt'] != "" ? $payment_meta['pay_amt'] : 0;
    $transaction_id = isset($payment_meta['transactionID']) && $payment_meta['transactionID'] != "" ? $payment_meta['transactionID'] : "";
    $payment_status = (isset($payment_status)) ? $payment_status : 'Pending';
    if (isset($check_total_payment) && $check_total_payment != '' && $check_total_payment != 0) {
        $check_remaining_payment = $order_meta['total_price'] - $check_total_payment;
    }

    $payment_source_method = $order_meta['bkx_payment_gateway_method'];
    list($pending_paypal_message, $payment_source) = getPaymentInfo($payment_source_method, $payment_status);

    $extra_html = getExtraHtml($order_meta);
    list($bkx_business_name, $bkx_business_email, $bkx_business_phone, $bkx_business_address) = getBusinessInfo();

    $first_header = esc_html("Booking Information", 'bookingx');
    $second_header = sprintf(__('Your Booking with %s', 'bookingx'), $bkx_business_name);
    if ($is_mobile == 1) {
        $first_header = sprintf(__('Your Booking with %s', 'bookingx'), $bkx_business_name);
        $second_header = esc_html("Booking Information", 'bookingx');
    }

    $base_id = $order_meta['base_id'];
    $BkxBaseObj = new BkxBase('', $base_id);
    $base_time = $BkxBaseObj->get_time($base_id);
    $base_time_option = get_post_meta($booking_id, 'base_time_option', true);
    $base_time_option = (isset($base_time_option) && $base_time_option != "") ? $base_time_option : "H";
    $date_format = bkx_crud_option_multisite('date_format');

    if (isset($base_time_option) && $base_time_option == "H") {
        $total_time = getDateDuration($order_meta);
        $duration = getDuration($order_meta);
        $date_data = sprintf(__('%s %s', 'bookingx'), date($date_format, strtotime($order_meta['booking_date'])),$order_meta['booking_time_from'] );
        $start_date = $order_meta['booking_date'];
    } else {
        list($date_data, $duration, $start_date) = getDayDateDuration($booking_id);
    }
	$order_timezone = apply_filters('bkx_booking_time_zone_admin_content', '', $order_meta );
    $cancel_booking_page = bkx_crud_option_multisite('cancellation_policy_page_id');
    $check_cancel_booking = bkx_crud_option_multisite('enable_cancel_booking');
    $cancel_policy_url = "";
    $booking_date_converted = strtotime(date('Y-m-d', strtotime($start_date))) . ' ';
    $current_date = strtotime(date('Y-m-d'));
    $status = array('Pending', 'Acknowledged', 'Missed', 'Failed' );
    if ($booking_date_converted >= $current_date && in_array($order_status, $status) &&  $check_cancel_booking == 1 ) {
        $is_able_cancelled = true;
        if(!empty($cancel_booking_page)){
            $cancel_policy_url = get_permalink($cancel_booking_page);
        }
    }

    $booking_detail['first_header'] = $first_header;
    $booking_detail['second_header'] = $second_header;
    $booking_detail['currency'] = $currency;
    $booking_detail['is_mobile'] = $is_mobile;
    $booking_detail['is_able_cancelled'] = $is_able_cancelled;
    $booking_detail['cancel_policy_url'] = $cancel_policy_url;

    $booking_detail['booking_info'] = array(
        'ID' => $booking_id,
        'date' => $date_data,
        'extra' => $extra_html,
        'duration' => $duration,
        'total' => $order_meta['total_price'],
        'status' => $order_status,
        'service' => $order_meta['base_arr']['main_obj']->post->post_title,
        'staff' => $order_meta['seat_arr']['main_obj']->post->post_title,
	    'timezone_data' => $order_timezone
        );
    $booking_detail['booking_payment'] = array();
    if(!empty($payment_meta) && is_array($payment_meta)){
        $booking_detail['booking_payment'] = array(
            'gateway' => $payment_source,
            'payment_status' => $payment_status,
            'extra' => $extra_html,
            'message' => $pending_paypal_message,
        );
        if (isset($payment_source) && $payment_source != "Offline Payment") {
            $booking_detail['booking_payment']['transaction_id'] = $transaction_id;
        }
    }

    $booking_detail['booking_business'] = array(
        'name' => $bkx_business_name,
        'phone' => $bkx_business_phone,
        'email' => $bkx_business_email,
        'address' => $bkx_business_address,
        'staff' => $order_meta['seat_arr']['main_obj']->post->post_title,
    );

    return $booking_detail;
}
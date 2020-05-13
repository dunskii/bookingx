<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
$plugin = plugin_basename(__FILE__);

function bkx_process_mail_by_status($booking_id, $subject, $content, $email = null, $is_customer = false){
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
    if ( isset($BkxSeatInfo['seat_street']) && $BkxSeatInfo['seat_street']){
        $event_address .= $BkxSeatInfo['seat_street'] . ", ";
    }else{
        $event_address .=  "{$business_address_1}, {$business_address_2}";
    }

    if ( isset($BkxSeatInfo['seat_city']) && $BkxSeatInfo['seat_city']){
        $event_address .= $BkxSeatInfo['seat_city'] . ", ";
    }else{
        $event_address .= "{$bkx_business_city} ,";
    }

    if ( isset($BkxSeatInfo['seat_state']) && $BkxSeatInfo['seat_state']){
        $event_address .= $BkxSeatInfo['seat_state'] . ", ";
    }else{
        $event_address .= "{$bkx_business_state} ,";
    }

    if ( isset($BkxSeatInfo['seat_postcode']) && $BkxSeatInfo['seat_postcode']){
        $event_address .= $BkxSeatInfo['seat_postcode'];
    }else{
        $event_address .= $bkx_business_zip;
    }

    if ( isset($BkxSeatInfo['seat_country']) && $BkxSeatInfo['seat_country']){
        $event_address .= $BkxSeatInfo['seat_country'];
    }else{
        $event_address .= $bkx_business_country;
    }
    $total_price = ($order_meta['total_price'] != '') ? $order_meta['total_price'] : 0;
    $subject = str_replace("{booking_number}", $order_meta['order_id'], $subject);
    $subject = str_replace("{site_title}", get_bloginfo(), $subject);
    $subject = str_replace("{site_address}", get_site_url(), $subject);
    $subject = str_replace("{fname}", $order_meta['first_name'], $subject );
    $subject = str_replace("{lname}", $order_meta['last_name'], $subject );
    $subject = str_replace("{total_price}", $currency . $order_meta['total_price'] . ' ' . bkx_crud_option_multisite('currency_option'), $subject );
    $subject = str_replace("{txn_id}", $transactionID, $subject );
    $subject = str_replace("{order_id}", $order_meta['order_id'], $subject );
    $subject = str_replace("{total_duration}", $order_meta['total_duration'], $subject );
    $subject = str_replace("{total_price}", $total_price . ' ' . bkx_crud_option_multisite('currency_option'), $subject );
    $subject = str_replace("{siteurl}", site_url(), $subject );
    $subject = str_replace("{seat_name}", $order_meta['seat_arr']['title'], $subject );
    $subject = str_replace("{base_name}", $order_meta['base_arr']['title'], $subject );
    $subject = str_replace("{additions_list}", $addition_list, $subject );
    $subject = str_replace("{time_of_booking}", $booking_duration, $subject );
    $subject = str_replace("{date_of_booking}", $order_meta['booking_start_date'], $subject );
    $subject = str_replace("{location_of_booking}", $event_address, $subject );
    $subject = str_replace("{amount_paid}", $currency . $amount_paid . ' ' . bkx_crud_option_multisite('currency_option'), $subject );
    $subject = str_replace("{amount_pending}", $currency . $amount_pending . ' ' . bkx_crud_option_multisite('currency_option'), $subject );
    $subject = str_replace("{business_name}", $bkx_business_name, $subject );
    $subject = str_replace("{business_email}", $bkx_business_email, $subject );
    $subject = str_replace("{business_phone}", $bkx_business_phone, $subject );
    $subject = str_replace("{booking_status}", $order_status, $subject );
    $subject = str_replace("{booking_edit_url}", edit_booking_url($booking_id), $subject );



    $message_body = str_replace("{fname}", $order_meta['first_name'], $message_body);
    $message_body = str_replace("{lname}", $order_meta['last_name'], $message_body);
    $message_body = str_replace("{total_price}", $currency . $order_meta['total_price'] . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace("{txn_id}", $transactionID, $message_body);
    $message_body = str_replace("{order_id}", $order_meta['order_id'], $message_body);
    $message_body = str_replace("{total_duration}", $order_meta['total_duration'], $message_body);
    $message_body = str_replace("{total_price}", $total_price . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace("{siteurl}", site_url(), $message_body);
    $message_body = str_replace("{seat_name}", $order_meta['seat_arr']['title'], $message_body);
    $message_body = str_replace("{base_name}", $order_meta['base_arr']['title'], $message_body);
    $message_body = str_replace("{additions_list}", $addition_list, $message_body);
    $message_body = str_replace("{time_of_booking}", $booking_duration, $message_body);
    $message_body = str_replace("{date_of_booking}", $order_meta['booking_start_date'], $message_body);
    $message_body = str_replace("{location_of_booking}", $event_address, $message_body);
    $message_body = str_replace("{amount_paid}", $currency . $amount_paid . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace("{amount_pending}", $currency . $amount_pending . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace("{business_name}", $bkx_business_name, $message_body);
    $message_body = str_replace("{business_email}", $bkx_business_email, $message_body);
    $message_body = str_replace("{business_phone}", $bkx_business_phone, $message_body);
    $message_body = str_replace("{booking_status}", $order_status, $message_body);
    $message_body = str_replace("{booking_edit_url}", edit_booking_url($booking_id), $message_body);

    // Mail it
    bkx_mail_format_and_send_process($subject, $message_body, $to, $mail_type);
}



function bkx_generate_template_page( $option_key , $content, $page_title, $page_id ){
    $page_data = array();
    if(isset($page_id) && $page_id != "" ){ $page_data = get_post( $page_id ); }
    if( !empty($page_data) ){
        bkx_crud_option_multisite($option_key, $page_id, 'update');
    }else{
        $page_array = array();
        $page_array['post_title']       = $page_title;
        $page_array['post_content']     = $content;
        $page_array['post_status']      = 'publish';
        $page_array['post_type']        = 'page';
        $page_array['comment_status']   = 'closed';
        $page_array['ping_status']      = 'closed';
        $page_array['post_category']    = array(1);
        $page_id  = wp_insert_post($page_array);
        bkx_crud_option_multisite($option_key, $page_id, 'update');
    }
}

//Create default Template Pages Function
add_action('init', 'bkx_create_default_template');
function bkx_create_default_template(){
    $bkx_set_booking_page           = bkx_crud_option_multisite("bkx_set_booking_page");
    $bkx_set_thank_you_page         = bkx_crud_option_multisite('bkx_set_thank_you_page');
    $booking_edit_process_page_id   = bkx_crud_option_multisite('booking_edit_process_page_id');
    bkx_generate_template_page( "bkx_set_thank_you_page" , "", "Thank You", $bkx_set_thank_you_page );
    bkx_generate_template_page( "booking_edit_process_page_id" , "[bkx_booking_form]", "Booking Edit", $booking_edit_process_page_id );
    bkx_generate_template_page( "bkx_set_booking_page" , "[bkx_booking_form]", "Booking Form", $bkx_set_booking_page );
}
// Add settings link on plugin page
add_filter("plugin_action_links_{$plugin}", 'bkx_plugin_settings_link');
function bkx_plugin_settings_link($links){
    $settings_link = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=bkx_booking&page=bkx-setting&bkx_tab=bkx_general">Settings</a>';
    //array_unshift($links, $settings_link);
    return $links;
}
add_filter('query_vars', 'bkx_query_vars');

add_filter( 'get_search_query',  'bkx_search_label' ) ;
function bkx_search_label( $query ){
    global $pagenow, $typenow;

    if ( 'edit.php' !== $pagenow || 'bkx_booking' !== $typenow || ! get_query_var( 'bkx_booking_search' ) || ! isset( $_GET['s'] ) ) { // WPCS: input var ok.
        return $query;
    }

    return wc_clean( wp_unslash( $_GET['s'] ) ); // WPCS: input var ok, sanitization ok.
}

function bkx_query_vars( $qv ){
    $qv[] = 'search_by_dates';
    $qv[] = 'search_by_selected_date';
    $qv[] = 'seat_view';
    $qv[] = 'bkx_booking_search';
    return $qv;
}

add_filter('post_row_actions', 'bkx_row_actions', 2, 100);
function bkx_row_actions($actions, $post){
    if ( in_array( $post->post_type, array('bkx_booking') ) ) {
        if (isset($actions['inline hide-if-no-js'])) {
            unset($actions['inline hide-if-no-js']);
        }
    }
    return $actions;
}

add_filter('comments_clauses', 'bkx_exclude_order_comments', 10, 1);
function bkx_exclude_order_comments($clauses){
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
function bkx_count_comments($stats, $post_id){
    global $wpdb;
    //delete_transient( 'wc_count_comments' );
    if (0 === $post_id) {
        $stats = get_transient('wc_count_comments');
        if (!$stats) {
            $stats = array();
            $count = $wpdb->get_results("SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} WHERE comment_type != 'booking_note' GROUP BY comment_approved", ARRAY_A);
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
            set_transient('wc_count_comments', $stats);
        }
    }
    return $stats;
}

// Disable post type view mode options
add_filter('view_mode_post_types', 'bkx_disable_view_mode_options');
function bkx_disable_view_mode_options($post_types){
    unset($post_types['bkx_seat'], $post_types['bkx_addition'], $post_types['bkx_booking'], $post_types['bkx_base']);
    return $post_types;
}

add_action('load-edit.php', 'bkx_bulk_action');
function bkx_bulk_action(){
    if (empty($_REQUEST['action']))
        return;
    $action = $_REQUEST['action'];
    $order_statuses = bkx_get_order_statuses();
    $new_status = substr($action, 5); // get the status name from action
    $report_action = 'marked_' . $new_status;
    if (!isset($order_statuses['bkx-' . $new_status])) {
        return;
    }
    $changed = 0;
    $post_ids = array_map('absint', (array)$_REQUEST['post']);
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
function bookingx_url_rewrite_tag(){
    add_rewrite_tag('%bookingx_type%', '([^&]+)');
    add_rewrite_tag('%bookingx_type_id%', '([^&]+)');
}
add_action('init', 'bookingx_url_rewrite_tag', 10, 0);

function bookingx_url_rewrite_rule(){
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

function bkx_list_table_views_filter( $view ) {
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
                $querystr = " 
                SELECT {$wpdb}->posts.post_status, COUNT( * ) AS num_posts FROM {$wpdb}->posts, {$wpdb}->postmeta
                WHERE {$wpdb}->posts.ID = $wpdb->postmeta.post_id 
                AND {$wpdb}->posts.post_type = 'bkx_booking'
                AND {$wpdb}->postmeta.meta_key = 'seat_id' 
                AND {$wpdb}->postmeta.meta_value = $seat_post_id 
                AND {$wpdb}->posts.post_status IN ('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed', 'bkx-cancelled')
                GROUP BY {$wpdb}->posts.post_status";
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

function bkx_cal_total_tax($total_price) {
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
        $total_tax['grand_total'] = number_format((float)$grand_total, 2, '.', '');;
    }
    return $total_tax;
}

function bkx_secs2hours($secs) {
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

function bkx_getDayName( $day ) {
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

function bkx_getMinsSlot($mins) {
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

function bkx_admin_actions() {
    $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
    $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
    $bkx_alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
    $bkx_seat_taxonomy_status = bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' ); 
    $bkx_base_taxonomy_status = bkx_crud_option_multisite( 'bkx_base_taxonomy_status' );
    $bkx_addition_taxonomy_status = bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' );

    add_submenu_page('edit.php?post_type=bkx_booking', __('Settings', 'bookingx'), __('Settings', 'bookingx'), 'manage_options', 'bkx-setting', 'bkx_setting_page_callback');

    if( isset($bkx_seat_taxonomy_status) && $bkx_seat_taxonomy_status == 1 ){
        add_submenu_page('edit.php?post_type=bkx_booking', __("$alias_seat Category", 'bookingx'), __("$alias_seat Category", 'bookingx'), 'manage_options', 'edit-tags.php?taxonomy=bkx_seat_cat&post_type=bkx_seat');
    }

    if( isset($bkx_base_taxonomy_status) && $bkx_base_taxonomy_status == 1 ){
        add_submenu_page('edit.php?post_type=bkx_booking', __("$bkx_alias_base Category", 'bookingx'), __("$bkx_alias_base Category", 'bookingx'), 'manage_options', 'edit-tags.php?taxonomy=bkx_base_cat&post_type=bkx_base');
    }

    if( isset($bkx_addition_taxonomy_status) && $bkx_addition_taxonomy_status == 1 ){
        add_submenu_page('edit.php?post_type=bkx_booking', __("$bkx_alias_addition Category", 'bookingx'), __("$bkx_alias_addition Category", 'bookingx'), 'manage_options', 'edit-tags.php?taxonomy=bkx_addition_cat&post_type=bkx_addition');
    }
}
add_action('admin_menu', 'bkx_admin_actions');

add_filter( 'custom_menu_order', 'bkx_reorder_admin_menu_pages' );
function bkx_reorder_admin_menu_pages( $menu_ord ){
    global $submenu;
    // Enable the next line to see all menu orders
    $arr = array();
    $find_submenu = array();
    if(!empty($submenu['edit.php?post_type=bkx_booking'])){
        $submenu_obj = $submenu['edit.php?post_type=bkx_booking'];
        foreach ($submenu_obj as $key => $submenu_data) {
            //find Seat Post type Key
            if($submenu_data[2] =='edit.php?post_type=bkx_seat'){
                $find_submenu['bkx_seat_post_type'] = $key;
            }
            if($submenu_data[2] ==  'edit-tags.php?taxonomy=bkx_seat_cat&post_type=bkx_seat') {
                $find_submenu['bkx_seat_category'] = $key;
            }
            if($submenu_data[2] =='edit.php?post_type=bkx_base') {
                $find_submenu['bkx_base_post_type'] = $key;
            }
            if($submenu_data[2] ==  'edit-tags.php?taxonomy=bkx_base_cat&post_type=bkx_base') {
                $find_submenu['bkx_base_category'] = $key;
            }
            if($submenu_data[2] =='edit.php?post_type=bkx_addition') {
                $find_submenu['bkx_addition_post_type'] = $key;
            }
            if($submenu_data[2] ==  'edit-tags.php?taxonomy=bkx_addition_cat&post_type=bkx_addition') {
                $find_submenu['bkx_addition_category'] = $key;
            }
        }
    }
    if(isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][5];     //my original order was 5,10,15,16,17,18
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][11];
    }
    if(isset($find_submenu['bkx_seat_category']) && $find_submenu['bkx_seat_category'] != " "){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_seat_category']];
    }
    if(isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][12];
    }
    if(isset($find_submenu['bkx_base_category']) && $find_submenu['bkx_base_category'] != " "){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_base_category']];
    }
    if(isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][13];
    }
    if(isset($find_submenu['bkx_addition_category']) && $find_submenu['bkx_addition_category'] != " "){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_addition_category']];
    }
    if(isset($submenu['edit.php?post_type=bkx_booking']) && !empty($submenu['edit.php?post_type=bkx_booking'])){
        $arr[] = $submenu['edit.php?post_type=bkx_booking'][14];
    }
    $submenu['edit.php?post_type=bkx_booking'] = $arr;
    return $menu_ord;
}

function bkx_plugin_add_settings_link($links){
    $settings_link = '<a href="edit.php?post_type=bkx_booking&page=bkx-setting">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
add_filter("plugin_action_links_$plugin", 'bkx_plugin_add_settings_link');
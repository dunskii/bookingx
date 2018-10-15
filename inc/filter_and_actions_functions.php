<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Create Seat Post Type
 */
$plugin = plugin_basename(__FILE__);

function bkx_create_base_builtup(){

    bkx_crud_option_multisite( 'bkx_alias_seat', 'Resource','update');
    bkx_crud_option_multisite( 'bkx_alias_base', 'Service','update');
    bkx_crud_option_multisite( 'bkx_alias_addition', 'Extra','update');
    bkx_crud_option_multisite( 'bkx_alias_notification', 'Notification','update');
    bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias', 'How many times you want to extend this service?','update');
    bkx_crud_option_multisite( 'bkx_label_of_step1', 'Please select what you would like to book','update');
}

add_action('init', 'bkx_create_seat_post_type');
function bkx_create_seat_post_type()
{
    bkx_crud_option_multisite('bkx_alias_seat') == "" ? bkx_crud_option_multisite( 'bkx_alias_seat', 'Resource','update') : '';
    bkx_crud_option_multisite('bkx_alias_base') == "" ? bkx_crud_option_multisite( 'bkx_alias_base', 'Service','update') : '';
    bkx_crud_option_multisite('bkx_alias_addition') == "" ? bkx_crud_option_multisite( 'bkx_alias_addition', 'Extra','update') : '';
    bkx_crud_option_multisite('bkx_alias_notification') == "" ? bkx_crud_option_multisite( 'bkx_alias_notification', 'Notification','update') : '';
    bkx_crud_option_multisite('bkx_notice_time_extended_text_alias') == "" ? bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias', 'How many times you want to extend this service?','update') : '';
    bkx_crud_option_multisite('bkx_label_of_step1') == "" ? bkx_crud_option_multisite( 'bkx_label_of_step1', 'Please select what you would like to book','update') : '';


    $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
    $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
    $bkx_alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
    $alias_seat = isset($alias_seat) && $alias_seat != '' ? $alias_seat : "Resource";
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x($alias_seat, 'Post Type General Name', 'bookingx'),
        'singular_name' => _x($alias_seat, 'Post Type Singular Name', 'bookingx'),
        'menu_name' => __($alias_seat, 'bookingx'),
        'all_items' => __($alias_seat, 'bookingx'),
        'view_item' => __("View $alias_seat", 'bookingx'),
        'add_new_item' => __("Add New Resource", 'bookingx'),
        'add_new' => __("Add New Resource", 'bookingx'),
        'edit_item' => __("Edit Resource", 'bookingx'),
        'update_item' => __("Update $alias_seat", 'bookingx'),
        'search_items' => __("Search $alias_seat", 'bookingx'),
        'not_found' => __('Not Found', 'bookingx'),
        'not_found_in_trash' => __('Not found in Trash', 'bookingx')
    );

    register_post_type('bkx_seat', array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',

        ),
        'rewrite' => array(
            'slug' => sanitize_title($alias_seat)
        ),
        'show_in_menu' => 'edit.php?post_type=bkx_booking'
    )); 
    $bkx_seat_taxonomy_status = bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' );  


    if( isset($bkx_seat_taxonomy_status) && $bkx_seat_taxonomy_status == 1 ){
        // Category 
        $labels = array(
            'name'              => __( $alias_seat.' Category' , 'bookingx' ),
            'singular_name'     => __( $alias_seat .' Category', 'bookingx' ),
            'search_items'      => __( 'Search '.ucwords($alias_seat) .' Category'  , 'bookingx' ),
            'all_items'         => __( 'All '.ucwords($alias_seat) .' Category'  , 'bookingx' ),
            'parent_item'       => __( 'Parent '.ucwords($alias_seat) .' Category' , 'bookingx' ),
            'parent_item_colon' => __( 'Parent '.ucwords($alias_seat) .' Category', 'bookingx' ),
            'edit_item'         => __( 'Edit '.ucwords($alias_seat) .' Category' , 'bookingx' ),
            'update_item'       => __( 'Update '.ucwords($alias_seat) .' Category'  , 'bookingx' ),
            'add_new_item'      => __( 'Add New '.ucwords($alias_seat) .' Category'  , 'bookingx' ),
            'new_item_name'     => __( 'New Category Name', 'bookingx' ),
            'menu_name'         => __( 'Categories', 'bookingx' ),
        );

        register_taxonomy( 'bkx_seat_cat', array( 'bkx_seat' ), array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_menu'      => true,
            'rewrite'       => array(
                'slug' => 'bkx-seat-category'
            )
        ) );
    } 
}

/**
 * Create Base Post Type
 */

add_action('init', 'bkx_create_base_post_type');
function bkx_create_base_post_type()
{
    $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
    $bkx_alias_base = isset($bkx_alias_base) && $bkx_alias_base != '' ? $bkx_alias_base : "Service";
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x($bkx_alias_base, 'Post Type General Name', 'bookingx'),
        'singular_name' => _x($bkx_alias_base, 'Post Type Singular Name', 'bookingx'),
        'menu_name' => __($bkx_alias_base, 'bookingx'),
        'all_items' => __($bkx_alias_base, 'bookingx'),
        'view_item' => __("View $alias_base", 'bookingx'),
        'add_new_item' => __("Add New $alias_base", 'bookingx'),
        'add_new' => __("Add New $alias_base", 'bookingx'),
        'edit_item' => __("Edit $alias_base", 'bookingx'),
        'update_item' => __("Update $alias_base", 'bookingx'),
        'search_items' => __("Search $alias_base", 'bookingx'),
        'not_found' => __('Not Found', 'bookingx'),
        'not_found_in_trash' => __('Not found in Trash', 'bookingx')
    );
    register_post_type('bkx_base', array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ),
        'rewrite' => array(
            'slug' => sanitize_title($bkx_alias_base)
        ),
        'show_in_menu' => 'edit.php?post_type=bkx_booking'
    ));
    $bkx_base_taxonomy_status = bkx_crud_option_multisite( 'bkx_base_taxonomy_status' );  

    if( isset($bkx_base_taxonomy_status) && $bkx_base_taxonomy_status == 1 ){
        // Category 
        $labels = array(
            'name'              => __( $bkx_alias_base .' Category' , 'bookingx' ),
            'singular_name'     => __( $bkx_alias_base .' Category', 'bookingx' ),
            'search_items'      => __( 'Search '.ucwords($bkx_alias_base) .' Category'  , 'bookingx' ),
            'all_items'         => __( 'All '.ucwords($bkx_alias_base) .' Category' , 'bookingx' ),
            'parent_item'       => __( 'Parent '.ucwords($bkx_alias_base).' Category'  , 'bookingx' ),
            'parent_item_colon' => __( 'Parent '.ucwords($bkx_alias_base).' Category'  , 'bookingx' ),
            'edit_item'         => __( 'Edit '.ucwords($bkx_alias_base).' Category'  , 'bookingx' ),
            'update_item'       => __( 'Update '.ucwords($bkx_alias_base) .' Category' , 'bookingx' ),
            'add_new_item'      => __( 'Add New '.ucwords($bkx_alias_base).' Category'  , 'bookingx' ),
            'new_item_name'     => __( 'New '.ucwords($bkx_alias_base).' Category Name', 'bookingx' ),
            'menu_name'         => __( 'Categories', 'bookingx' ),
        );
        register_taxonomy( 'bkx_base_cat', array( 'bkx_base' ), array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
        ) );
    }
}

/**
 * Create Base Post Type
 */

add_action('init', 'bkx_create_addition_post_type');
function bkx_create_addition_post_type()
{
    $bkx_alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
    $bkx_alias_addition = isset($bkx_alias_addition) && $bkx_alias_addition != '' ? $bkx_alias_addition : "Extra";
    $bkx_addition_taxonomy_status = bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' );


    $labels = array(
        'name' => _x($bkx_alias_addition, 'Post Type General Name', 'bookingx'),
        'singular_name' => _x($bkx_alias_addition, 'Post Type Singular Name', 'bookingx'),
        'menu_name' => __($bkx_alias_addition, 'bookingx'),
        'all_items' => __($bkx_alias_addition, 'bookingx'),
        'view_item' => __("View $alias_addition", 'bookingx'),
        'add_new_item' => __("Add New Extra", 'bookingx'),
        'add_new' => __("Add New Extra", 'bookingx'),
        'edit_item' => __("Edit Extra", 'bookingx'),
        'update_item' => __("Update Extra", 'bookingx'),
        'search_items' => __("Search $alias_addition", 'bookingx'),
        'not_found' => __('Not Found', 'bookingx'),
        'not_found_in_trash' => __('Not found in Trash', 'bookingx')
    );
    register_post_type('bkx_addition', array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ),
        'rewrite' => array(
            'slug' => sanitize_title($bkx_alias_addition)
        ),
        'show_in_menu' => 'edit.php?post_type=bkx_booking'
    ));

     if( isset($bkx_addition_taxonomy_status) && $bkx_addition_taxonomy_status == 1 ){
        // Category 
        $labels = array(
            'name'              => __( $bkx_alias_addition .' Category', 'bookingx' ),
            'singular_name'     => __( $bkx_alias_addition .' Category', 'bookingx' ),
            'search_items'      => __( 'Search '.ucwords($bkx_alias_addition).' Category'  , 'bookingx' ),
            'all_items'         => __( 'All '.ucwords($bkx_alias_addition).' Category'  , 'bookingx' ),
            'parent_item'       => __( 'Parent '.ucwords($bkx_alias_addition).' Category'  , 'bookingx' ),
            'parent_item_colon' => __( 'Parent '.ucwords($bkx_alias_addition).' Category' , 'bookingx' ),
            'edit_item'         => __( 'Edit '.ucwords($bkx_alias_addition).' Category'  , 'bookingx' ),
            'update_item'       => __( 'Update '.ucwords($bkx_alias_addition).' Category'  , 'bookingx' ),
            'add_new_item'      => __( 'Add New '.ucwords($bkx_alias_addition).' Category'  , 'bookingx' ),
            'new_item_name'     => __( 'New '.ucwords($bkx_alias_addition).' Category Name', 'bookingx' ),
            'menu_name'         => __( 'Categories', 'bookingx' ),
        );

        register_taxonomy( 'bkx_addition_cat', array( 'bkx_addition' ), array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
        ) );
    }
}

/**
 * Create Booking Post Type
 */

add_action('init', 'bkx_create_booking_post_type');
function bkx_create_booking_post_type()
{

    $labels = array(
        'name' => _x('Booking', 'Post Type General Name', 'bookingx'),
        'singular_name' => _x('Booking', 'Post Type Singular Name', 'bookingx'),
        'menu_name' => __('BookingX', 'bookingx'),
        'all_items' => __("Bookings", 'bookingx'),
        'view_item' => __("View Booking", 'bookingx'),
        'add_new_item' => __("Add Booking", 'bookingx'),
        'add_new' => __("Add Booking", 'bookingx'),
        'edit_item' => __("Edit Booking", 'bookingx'),
        'update_item' => __("Update Booking", 'bookingx'),
        'search_items' => __("Search Booking", 'bookingx'),
        'not_found' => __('Booking not Found', 'bookingx'),
        'not_found_in_trash' => __('Booking not found in Trash', 'bookingx')
    );


    register_post_type('bkx_booking', array(
        'labels' => $labels,
        'public' => true,
        'menu_position' => 25,
        'supports' => array('title', 'comments'),
        'capabilities' => array()
    ));
}

add_action('admin_menu', 'bkx_notification_bubble_menu');
function bkx_notification_bubble_menu()
{
    global $menu;
    $count_posts = (array)wp_count_posts('bkx_booking');
    $pending_booking_count = $count_posts['bkx-pending'];
    if ($pending_booking_count) {
        foreach ($menu as $key => $value) {
            if ($menu[$key][2] == 'edit.php?post_type=bkx_booking') {
                $menu[$key][0] .= ' &nbsp;<span class="update-plugins bkx-count-' . $pending_booking_count . '"><span class="plugin-count" aria-hidden="true">' . $pending_booking_count . '</span><span class="screen-reader-text">' . $pending_booking_count . ' Booking\'s Pending</span></span>';
                return;
            }
        }
    }
}

add_action('init', 'register_bookingx_post_status');
/**
 * Register our custom post statuses, used for order status.
 */
function register_bookingx_post_status()
{
    $order_statuses = apply_filters('bookingx_register_bkx_booking_post_statuses', array(
        'bkx-pending' => array(
            'label' => _x('Pending', 'Order status', 'bookingx'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'bookingx')
        ),
        'bkx-ack' => array(
            'label' => _x('Acknowledged', 'Order status', 'bookingx'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Acknowledged <span class="count">(%s)</span>', 'Acknowledged <span class="count">(%s)</span>', 'bookingx')
        ),
        'bkx-completed' => array(
            'label' => _x('Completed', 'Order status', 'bookingx'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'bookingx')
        ),
        'bkx-cancelled' => array(
            'label' => _x('Cancelled', 'Order status', 'bookingx'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'bookingx')
        ),
        'bkx-missed' => array(
            'label' => _x('Missed', 'Order status', 'bookingx'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Missed <span class="count">(%s)</span>', 'Missed <span class="count">(%s)</span>', 'bookingx')
        ),
        'bkx-failed' => array(
            'label' => _x('Failed', 'Order status', 'bookingx'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'bookingx')
        )
    ));

    foreach ($order_statuses as $order_status => $values) {
        register_post_status($order_status, $values);
    }
}

/**
 *  Manage Remove Admin menu organize
 */
function remove_add_new_menu_bookingx()
{
    remove_submenu_page('edit.php?post_type=bkx_booking', 'post-new.php?post_type=bkx_booking');
}

add_action('admin_menu', 'remove_add_new_menu_bookingx');


function bkx_on_all_status_transitions($new_status, $old_status, $post)
{
    $post_id = $post->ID;
    if ($new_status != $old_status && get_post_type($post) == 'bkx_booking') {
        if ($new_status == 'trash') {
            $order = new BkxBooking('', $post_id);
            $order->update_status('cancelled');
            wp_safe_redirect(wp_get_referer());
            die;
        }
    }
}

add_action('transition_post_status', 'bkx_on_all_status_transitions', 10, 3);


function bkx_order_edit_status($booking_id, $status)
{
    $bookingObj = new BkxBooking();
    $order_meta = $bookingObj->get_order_meta_data($booking_id);
    $content = "";

    switch ($status) {
        case 'ack':
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_ack');
            $page_obj = get_post($template_id);
            $subject = $page_obj->post_title;
            if (empty($subject)) {
                $subject = get_bloginfo() . ' has confirmed your booking for ' . $order_meta['base_arr']['title'];
            }
            $subject = apply_filters('bkx_order_subject_for_ack', $subject);
            bkx_process_mail_by_status($booking_id, $subject, $content, $template_id);
            break;
        case 'completed':
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_complete');
            $page_obj = get_post($template_id);
            $subject = $page_obj->post_title;
            if (empty($subject)) {
                $subject = 'Thanks you for your booking hope you had a great expeince';
            }
            $subject = apply_filters('bkx_order_subject_for_completed', $subject);
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_complete');
            bkx_process_mail_by_status($booking_id, $subject, $content, $template_id);
            break;
        case 'missed':
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_missed');
            $page_obj = get_post($template_id);
            $subject = $page_obj->post_title;
            if (empty($subject)) {
                $subject = 'Sorry you missed you booking, you can change your booking here';
            }

            $subject = apply_filters('bkx_order_subject_for_missed', $subject);
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_missed');
            bkx_process_mail_by_status($booking_id, $subject, $content, $template_id);
            break;
        case 'cancelled':
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_cancelled');
            $page_obj = get_post($template_id);
            $subject = $page_obj->post_title;
            if (empty($subject)) {
                $subject = 'Your booking has been cancelled';
            }

            $subject = apply_filters('bkx_order_subject_for_cancelled', $subject);
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_cancelled');
            bkx_process_mail_by_status($booking_id, $subject, $content, $template_id);
            break;
        default:
            $template_id = bkx_crud_option_multisite('bkx_tempalate_status_pending');
            $page_obj = get_post($template_id);
            $subject = $page_obj->post_title;
            if (empty($subject)) {
                $subject = get_bloginfo() . ' has confirmed your booking for ' . $order_meta['base_arr']['title'];
            }
            $subject = apply_filters('bkx_order_subject_for_pending', $subject);
            bkx_process_mail_by_status($booking_id, $subject, $content, $template_id);
            break;
    }
}

add_action('bkx_order_edit_status', 'bkx_order_edit_status', 10, 2);


function bkx_process_mail_by_status($booking_id, $subject, $content, $template_id = null)
{
    global $wpdb;

    if (empty($booking_id))
        return;

    if (!empty($template_id)) {
        $page_obj = get_post($template_id);
        $template_content = apply_filters('the_content', $page_obj->post_content);

        if (!empty($template_content)) {
            $message_body = $template_content;
        }
    }

    $amount_pending = 0;
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

    // multiple recipients
    $to = $order_meta['email'];
    $admin_email = get_settings('admin_email');

    //change request for template tags calculate booking duration 7/4/2013
    $booking_time = $order_meta['booking_time'];
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

    $message_body = str_replace("[/fname]", "", $message_body);
    $message_body = str_replace('[/lname]', "", $message_body);
    $message_body = str_replace('[/total_price]', "", $message_body);
    $message_body = str_replace('[/txn_id]', "", $message_body);
    $message_body = str_replace('[/order_id]', "", $message_body);
    $message_body = str_replace('[/total_duration]', "", $message_body);
    $message_body = str_replace('[/paid_price]', "", $message_body);
    $message_body = str_replace('[/bal_price]', "", $message_body);
    $message_body = str_replace('[/siteurl]', "", $message_body);
    $message_body = str_replace('[/seat_name]', "", $message_body);
    $message_body = str_replace('[/base_name]', "", $message_body);
    $message_body = str_replace('[/additions_list]', "", $message_body);
    $message_body = str_replace('[/time_of_booking]', "", $message_body);
    $message_body = str_replace('[/date_of_booking]', "", $message_body);
    $message_body = str_replace('[/location_of_booking]', "", $message_body);
    $message_body = str_replace('[/amount_paid]', "", $message_body);
    $message_body = str_replace('[/amount_pending]', "", $message_body);
    $message_body = str_replace('[/business_name]', "", $message_body);
    $message_body = str_replace('[/business_phone]', "", $message_body);
    $message_body = str_replace('[/business_email]', "", $message_body);
    $message_body = str_replace('[/booking_status]', "", $message_body);

    $payment_data = get_post_meta($order_meta['order_id'], 'payment_meta', true);
    if (!empty($payment_data)) {
        $transactionID = $payment_data['transactionID'];
        $amount_paid = $payment_data['pay_amt'];
        $amount_pending = ($order_meta['total_price'] - $payment_data['pay_amt']);
    } else {
        $transactionID = '-';
        $amount_pending = $order_meta['total_price'];
    }
    if ( isset($BkxSeatInfo['seat_street']) && $BkxSeatInfo['seat_street'])
        $event_address = $BkxSeatInfo['seat_street'] . ", ";
    if ( isset($BkxSeatInfo['seat_city']) && $BkxSeatInfo['seat_city'])
        $event_address .= $BkxSeatInfo['seat_city'] . ", ";
    if ( isset($BkxSeatInfo['seat_state']) && $BkxSeatInfo['seat_state'])
        $event_address .= $BkxSeatInfo['seat_state'] . ", ";
    if ( isset($BkxSeatInfo['seat_postcode']) && $BkxSeatInfo['seat_postcode'])
        $event_address .= $BkxSeatInfo['seat_postcode'];
    if ( isset($BkxSeatInfo['seat_country']) && $BkxSeatInfo['seat_country'])
        $event_address .= $BkxSeatInfo['seat_country'];

    $total_price = ($order_meta['total_price'] != '') ? $order_meta['total_price'] : 0;
    $message_body = str_replace("[fname]", $order_meta['first_name'], $message_body);
    $message_body = str_replace('[lname]', $order_meta['last_name'], $message_body);
    $message_body = str_replace('[total_price]', $currency . $order_meta['total_price'] . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace('[txn_id]', $transactionID, $message_body);
    $message_body = str_replace('[order_id]', $order_meta['order_id'], $message_body);
    $message_body = str_replace('[total_duration]', $order_meta['total_duration'], $message_body);
    $message_body = str_replace('[total_price]', $total_price . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace('[siteurl]', site_url(), $message_body);
    $message_body = str_replace('[seat_name]', $order_meta['seat_arr']['title'], $message_body);
    $message_body = str_replace('[base_name]', $order_meta['base_arr']['title'], $message_body);
    $message_body = str_replace('[additions_list]', $addition_list, $message_body);
    $message_body = str_replace('[time_of_booking]', $booking_duration, $message_body);
    $message_body = str_replace('[date_of_booking]', $order_meta['booking_start_date'], $message_body);
    $message_body = str_replace('[location_of_booking]', $event_address, $message_body);
    $message_body = str_replace('[amount_paid]', $currency . $amount_paid . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace('[amount_pending]', $currency . $amount_pending . ' ' . bkx_crud_option_multisite('currency_option'), $message_body);
    $message_body = str_replace('[business_name]', $bkx_business_name, $message_body);
    $message_body = str_replace('[business_email]', $bkx_business_email, $message_body);
    $message_body = str_replace('[business_phone]', $bkx_business_phone, $message_body);
    $message_body = str_replace('[booking_status]', $order_status, $message_body);
    // Mail it
    bkx_mail_format_and_send_process($subject, $message_body, $to);
    //}
}

//Create default Template Pages Function
function bkx_create_default_template()
{
    $thank_you_page_id = bkx_crud_option_multisite('bkx_tempalate_thankyou');
    $payment_pending_page_id = bkx_crud_option_multisite('bkx_tempalate_pending');
    $payment_success_page_id = bkx_crud_option_multisite('bkx_tempalate_sucess');
    $bkx_tempalate_status_pending = bkx_crud_option_multisite("bkx_tempalate_status_pending");
    $bkx_tempalate_status_ack = bkx_crud_option_multisite("bkx_tempalate_status_ack");
    $bkx_tempalate_status_complete = bkx_crud_option_multisite("bkx_tempalate_status_complete");
    $bkx_tempalate_status_missed = bkx_crud_option_multisite("bkx_tempalate_status_missed");
    $bkx_tempalate_status_cancelled = bkx_crud_option_multisite("bkx_tempalate_status_cancelled");


    $default_content = 'These tags can be placed in the email form and they will pull data from the database to include in the email.
        [fname], [lname], [total_price], [order_id] , [txn_id], [seat_name], [base_name], [additions_list], [time_of_booking], [date_of_booking], [location_of_booking].';


    $thanks_pending_content = 'Thanks [fname][/fname] for booking [base_name][/base_name],

Here are your booking details.
<ul>
    <li style="list-style-type: none;">
        <ul>
            <li>Booking ID: [order_id][/order_id]</li>
            <li>Resource: [seat_name][/seat_name]</li>
            <li>Service: [base_name][/base_name]</li>
            <li>Extras: [additions_list][/additions_list]</li>
        </ul>
        <ul>
            <li>Time: [time_of_booking][/time_of_booking]</li>
            <li>Date: [date_of_booking][/date_of_booking]</li>
            <li>Location: [location_of_booking][/location_of_booking]</li>
            <li>Price: [total_price][/total_price]</li>
        </ul>
    </li>
    <li>
        <ul>
            <li>Amount Paid : [amount_paid][/amount_paid]</li>
            <li>Amount Pending : [amount_pending][/amount_pending]</li>
            <li>Business Name : [business_name][/business_name]</li>
            <li>Business Phone : [business_phone][/business_phone]</li>
            <li>Business Email : [business_email][/business_email]</li>
            <li>Booking Status : [booking_status][/booking_status]</li>
        </ul>
    </li>
</ul>';


    $cancelled_content = 'Hi [fname][/fname],

Your [base_name][/base_name] with [seat_name][/seat_name] has been cancelled.

You can rebook <a href="http://booking-x.com">here</a>.';


    $thank_you_page = get_page_by_title('Thank You | Payment Confirmed');
    $payment_pending = get_page_by_title('Email Confirmation | Payment Pending');
    $payment_success = get_page_by_title('Transaction Success');
    $booking_edit_process = get_page_by_title('Booking Edit');
    $booking_page_title = get_page_by_title('Booking Form');

    $bkx_tempalate_status_pending_page = get_page_by_title('Email Template | Status Pending');
    $bkx_tempalate_status_ack_page = get_page_by_title('Email Template | Status Acknowledge');
    $bkx_tempalate_status_complete_page = bkx_crud_option_multisite("Email Template | Status Complete");
    $bkx_tempalate_status_missed_page = bkx_crud_option_multisite("Email Template | Status Missed");
    $bkx_tempalate_status_cancelled_page = bkx_crud_option_multisite("Email Template | Status Cancelled");

    if (!$thank_you_page) {

        $tpl_t['post_title'] = "Thank You | Payment Confirmed";
        $tpl_t['post_content'] = $default_content;
        $tpl_t['post_status'] = 'publish';
        $tpl_t['post_type'] = 'page';
        $tpl_t['comment_status'] = 'closed';
        $tpl_t['ping_status'] = 'closed';
        $tpl_t['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $thank_you_page_id = wp_insert_post($tpl_t);

        bkx_crud_option_multisite("bkx_tempalate_thankyou", $thank_you_page_id, 'update');
    }

    if (empty($bkx_tempalate_status_pending) && !$bkx_tempalate_status_pending_page) {

        $tpl_p['post_title'] = "Email Template | Status Pending";
        $tpl_p['post_content'] = $thanks_pending_content;
        $tpl_p['post_status'] = 'publish';
        $tpl_p['post_type'] = 'page';
        $tpl_p['comment_status'] = 'closed';
        $tpl_p['ping_status'] = 'closed';
        $tpl_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $payment_pending_page_id = wp_insert_post($tpl_p);

        bkx_crud_option_multisite("bkx_tempalate_status_pending", $payment_pending_page_id, 'update');
    }

    if (empty($bkx_tempalate_status_ack) && !$bkx_tempalate_status_ack_page) {

        $tpl_p['post_title'] = "Email Template | Status Acknowledge";
        $tpl_p['post_content'] = $thanks_pending_content;
        $tpl_p['post_status'] = 'publish';
        $tpl_p['post_type'] = 'page';
        $tpl_p['comment_status'] = 'closed';
        $tpl_p['ping_status'] = 'closed';
        $tpl_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $payment_pending_page_id = wp_insert_post($tpl_p);

        bkx_crud_option_multisite("bkx_tempalate_status_ack", $payment_pending_page_id, 'update');
    }

    if (empty($bkx_tempalate_status_complete) && !$bkx_tempalate_status_complete_page) {

        $tpl_p['post_title'] = "Email Template | Status Complete";
        $tpl_p['post_content'] = $thanks_pending_content;
        $tpl_p['post_status'] = 'publish';
        $tpl_p['post_type'] = 'page';
        $tpl_p['comment_status'] = 'closed';
        $tpl_p['ping_status'] = 'closed';
        $tpl_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $payment_pending_page_id = wp_insert_post($tpl_p);

        bkx_crud_option_multisite("bkx_tempalate_status_complete", $payment_pending_page_id, 'update');
    }

    if (empty($bkx_tempalate_status_missed) && !$bkx_tempalate_status_missed_page) {

        $tpl_p['post_title'] = "Email Template | Status Missed";
        $tpl_p['post_content'] = $default_content;
        $tpl_p['post_status'] = 'publish';
        $tpl_p['post_type'] = 'page';
        $tpl_p['comment_status'] = 'closed';
        $tpl_p['ping_status'] = 'closed';
        $tpl_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $payment_pending_page_id = wp_insert_post($tpl_p);

        bkx_crud_option_multisite("bkx_tempalate_status_missed", $payment_pending_page_id, 'update');
    }

    if (empty($bkx_tempalate_status_cancelled) && !$bkx_tempalate_status_cancelled_page) {

        $tpl_p['post_title'] = "Email Template | Status Cancelled";
        $tpl_p['post_content'] = $cancelled_content;
        $tpl_p['post_status'] = 'publish';
        $tpl_p['post_type'] = 'page';
        $tpl_p['comment_status'] = 'closed';
        $tpl_p['ping_status'] = 'closed';
        $tpl_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $payment_pending_page_id = wp_insert_post($tpl_p);

        bkx_crud_option_multisite("bkx_tempalate_status_cancelled", $payment_pending_page_id, 'update');
    }

    if (!$payment_success) {

        $tpl_s['post_title'] = "Transaction Success";
        $tpl_s['post_content'] = $default_content;
        $tpl_s['post_status'] = 'publish';
        $tpl_s['post_type'] = 'page';
        $tpl_s['comment_status'] = 'closed';
        $tpl_s['ping_status'] = 'closed';
        $tpl_s['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $payment_success_page_id = wp_insert_post($tpl_s);

        bkx_crud_option_multisite("bkx_tempalate_sucess", $payment_success_page_id, 'update');
    }

    if (!$booking_edit_process) {

        $tpl_s['post_title'] = "Booking Edit";
        $tpl_s['post_content'] = "[bookingform]";
        $tpl_s['post_status'] = 'publish';
        $tpl_s['post_type'] = 'page';
        $tpl_s['comment_status'] = 'closed';
        $tpl_s['ping_status'] = 'closed';
        $tpl_s['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $booking_edit_process_page_id = wp_insert_post($tpl_s);

        bkx_crud_option_multisite("booking_edit_process_page_id", $booking_edit_process_page_id, 'update');
    }


    if (!$booking_page_title):
        // Create post object
        $_p = array();
        $_p['post_title'] = "Booking Form";
        $_p['post_content'] = "[bookingform]";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $booking_page_id = wp_insert_post($_p);
        bkx_crud_option_multisite("bkx_set_booking_page", $booking_page_id, 'update');
    endif;

}

add_action('init', 'bkx_create_default_template');

// Add settings link on plugin page
add_filter("plugin_action_links_{$plugin}", 'bkx_plugin_settings_link');
function bkx_plugin_settings_link($links)
{
    $settings_link = '<a href="' . site_url() . '/wp-admin/edit.php?post_type=bkx_booking&page=bkx-setting&bkx_tab=bkx_general">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

add_filter('query_vars', 'bkx_query_vars');

function bkx_query_vars($qv)
{
    $qv[] = 'search_by_dates';
    $qv[] = 'search_by_selected_date';
    $qv[] = 'seat_view';
    return $qv;
}

add_filter('parse_query', 'bkx_add_meta_query');

function bkx_add_meta_query($query)
{

    global $pagenow, $wpdb;

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
        $current_role = $current_user->roles[0];

        if ($bkx_seat_role == $current_role) {
            $current_seat_id = $current_user->ID;
            $seat_post_id = get_user_meta($current_seat_id, 'seat_post_id', true);
            $search_by_seat_post_meta = array(
                array(
                    'key' => 'seat_id',
                    'value' => $seat_post_id,
                    'compare' => '=',
                ),
            );
            $query->set('meta_query', $search_by_seat_post_meta);
        }
    }


    $seat_view = isset($query->query_vars['seat_view']) ? $query->query_vars['seat_view'] : '';

    if (!empty($seat_view) && is_int($seat_view)) {

        $seat_view_query = array(
            array(
                'key' => 'seat_id',
                'value' => $seat_view,
                'compare' => '=',
            ),
        );
        $query->set('meta_query', $seat_view_query);
    }


    $search_by_dates = isset($query->query_vars['search_by_dates']) ? $query->query_vars['search_by_dates'] : '';
    $search_by_selected_date = isset($query->query_vars['search_by_selected_date']) ? $query->query_vars['search_by_selected_date'] : '';

    switch ($search_by_dates) {

        case 'today':
            $search_by_dates_meta = array(
                array(
                    'key' => 'booking_date',
                    'value' => date('m/d/Y'),
                    'compare' => '=',
                ),
            );
            break;

        case 'tomorrow':
            $tomorrow = date("m/d/Y", strtotime("+1 day"));
            $search_by_dates_meta = array(
                array(
                    'key' => 'booking_date',
                    'value' => $tomorrow,
                    'compare' => '=',
                ),
            );
            break;

        case 'this_week':

            $monday = date('m/d/Y', strtotime('monday this week'));
            $sunday = date('m/d/Y', strtotime('sunday this week'));

            $search_by_dates_meta = array(
                array(
                    'key' => 'booking_date',
                    'value' => array($monday, $sunday),
                    'compare' => 'BETWEEN',
                ),
            );

            break;

        case 'next_week':
            $monday = date('m/d/Y', strtotime('monday next week'));
            $sunday = date('m/d/Y', strtotime('sunday next week'));

            $search_by_dates_meta = array(
                array(
                    'key' => 'booking_date',
                    'value' => array($monday, $sunday),
                    'compare' => 'BETWEEN',
                ),
            );
            break;

        case 'this_month':

            $monday = date('m/01/Y');
            $sunday = date('m/t/Y');

            $search_by_dates_meta = array(
                array(
                    'key' => 'booking_date',
                    'value' => array($monday, $sunday),
                    'compare' => 'BETWEEN',
                ),
            );
            break;

        case 'choose_date':
            $search_by_dates_meta = array(
                array(
                    'key' => 'booking_date',
                    'value' => $search_by_selected_date,
                    'compare' => '=',
                ),
            );
            break;

        default:
            # code...
            break;
    }

    if (isset($query->query_vars['search_by_dates'])) {
        if (!empty($query->query_vars['search_by_dates'])) {
            $meta_query = $search_by_dates_meta;
            $query->set('meta_query', $meta_query);
        }
    }
}


add_filter('posts_request', 'bkx_posts_request', 2, 10);

function bkx_posts_request($order, $WP_Query)
{
    global $wpdb;

    $listing_view = isset($_GET['listing_view']) ? sanitize_text_field($_GET['listing_view']) : '';

    if (isset($WP_Query->query['post_type']) && $WP_Query->query['post_type'] == 'bkx_booking' && $listing_view == 'weekly' || $listing_view == 'monthly') {
        $get_posts = $wpdb->get_results($order, ARRAY_A);
        $BkxBooking = new BkxBooking();
        $timezone_string = bkx_crud_option_multisite('timezone_string');
        if (isset($timezone_string) && $timezone_string != "") {
            date_default_timezone_set($timezone_string);
        }


        if (!empty($get_posts)) {
            $generate_json = '';
            $generate_info = array();

            foreach ($get_posts as $key => $posts) {
                $time_block_bg_color = bkx_crud_option_multisite("time_block_bg_color");
                $order_meta_data = $BkxBooking->get_order_meta_data($posts['ID']);
                $first_name = $order_meta_data['first_name'];
                $last_name = $order_meta_data['last_name'];
                $booking_date = $order_meta_data['booking_date'];
                $booking_start_date = $order_meta_data['booking_start_date'];
                $booking_end_date = $order_meta_data['booking_end_date'];
                $total_duration = $order_meta_data['total_duration'];
                $base_arr = $order_meta_data['base_arr']['main_obj']->post;
                $service_name = $base_arr->post_title;

                $seat_id = get_post_meta($posts['ID'], 'seat_id', true);
                $seat_colour = get_post_meta($seat_id, 'seat_colour', true);
                $seat_colour = empty($seat_colour) ? $time_block_bg_color : $seat_colour;


                $start_date_obj = new DateTime($booking_start_date);
                $end_date_obj = new DateTime($booking_end_date);

                $formatted_start_date = date_format($start_date_obj, 'Y-m-d') . "T" . date_format($start_date_obj, 'H:i:s');
                $formatted_end_date = date_format($end_date_obj, 'Y-m-d') . "T" . date_format($end_date_obj, 'H:i:s');
                if ($listing_view == 'monthly') {
                    $generate_info['title'] = $first_name . ' ' . $last_name;
                    $generate_info['start'] = $formatted_start_date;
                    $generate_info['backgroundColor'] = $seat_colour;
                } else {
                    $generate_info['title'] = $first_name . ' ' . $last_name . ' - ( ' . $service_name . ')';
                    $generate_info['start'] = $formatted_start_date;
                    $generate_info['end'] = $formatted_end_date;
                    $generate_info['backgroundColor'] = $seat_colour;
                }
                $generate_info['url'] = admin_url('edit.php?post_type=bkx_booking&view=' . $posts['ID'] . '&bkx_name=' . $first_name . ' ' . $last_name . '#post-' . $posts['ID']);
                $generate_json .= json_encode($generate_info) . ',';
            }
            $generate_json = rtrim($generate_json, ",");
            bkx_crud_option_multisite('bkx_calendar_json_data', $generate_json, 'update');
        }
    }

    return $order;
}

add_action('parse_query', 'bkx_booking_search_custom_fields');

function bkx_booking_search_custom_fields($wp)
{
    global $pagenow, $wpdb;

    if ('edit.php' != $pagenow || empty($wp->query_vars['s']) || $wp->query_vars['post_type'] != 'bkx_booking') {
        return;
    }
    $order = new BkxBooking();
    $post_ids = $order->order_search($_GET['s']);

    if (!empty($post_ids)) {
        // Remove "s" - we don't want to search order name.
        unset($wp->query_vars['s']);

        // so we know we're doing this.
        $wp->query_vars['bkx_booking_search'] = true;

        // Search by found posts.
        $wp->query_vars['post__in'] = array_merge($post_ids, array(
            0
        ));
    }
}

add_filter('bulk_actions-edit-bkx_booking', 'bkx_booking_bulk_actions');
function bkx_booking_bulk_actions($actions)
{

    if (isset($actions['edit'])) {
        unset($actions['edit']);
    }
    return $actions;
}

add_filter('post_row_actions', 'bkx_row_actions', 2, 100);
function bkx_row_actions($actions, $post)
{
    if (in_array($post->post_type, array(
        'bkx_booking'
    ))) {
        if (isset($actions['inline hide-if-no-js'])) {
            unset($actions['inline hide-if-no-js']);
        }
    }
    return $actions;
}

add_filter('comments_clauses', 'bkx_exclude_order_comments', 10, 1);
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

    //delete_transient( 'wc_count_comments' );
    if (0 === $post_id) {

        $stats = get_transient('wc_count_comments');

        if (!$stats) {
            $stats = array();

            $count = $wpdb->get_results("SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} WHERE comment_type != 'booking_note' GROUP BY comment_approved", ARRAY_A);
            //print_r($wpdb->last_query);
            //die;
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
function bookingx_url_rewrite_tag()
{

    add_rewrite_tag('%bookingx_type%', '([^&]+)');
    add_rewrite_tag('%bookingx_type_id%', '([^&]+)');
}

add_action('init', 'bookingx_url_rewrite_tag', 10, 0);

function bookingx_url_rewrite_rule()
{
    flush_rewrite_rules();
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

function bkx_list_table_views_filter(array $view)
{
    global $wp_query, $wpdb;

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
        $current_role = $current_user->roles[0];
        $current_seat_id = $current_user->ID;


        if ($bkx_seat_role == $current_role && $current_seat_id != '') {

            unset($view);
            $add_class = '';

            $seat_post_id = get_user_meta($current_seat_id, 'seat_post_id', true);
            if (isset($seat_post_id) && $seat_post_id != '') {

                $querystr = "
                SELECT $wpdb->posts.post_status, COUNT( * ) AS num_posts
                FROM $wpdb->posts, $wpdb->postmeta
                WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
                AND $wpdb->posts.post_type = 'bkx_booking'
                AND $wpdb->postmeta.meta_key = 'seat_id' 
                AND $wpdb->postmeta.meta_value = $seat_post_id 
                AND $wpdb->posts.post_status IN ('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed', 'bkx-cancelled')
                GROUP BY $wpdb->posts.post_status
             ";

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


function bkx_cal_total_price($base_id, $base_extended = null, $extra_id = null)
{
    $total_price = 0;
    $base_price = 0;
    $addition_price = 0;

    // echo "Base";
    // print_r($base_id);
    // echo "<br>Extended";
    // print_r($base_extended);
    // echo "<br>Extra";
    // print_r($extra_id);
    // die;

    if (!empty($base_id)) {
        $BaseObj = get_post($base_id);
        $BkxBase = new BkxBase($BaseObj);
        if (!empty($BkxBase) && !is_wp_error($BkxBase)) {
            $base_price = $BkxBase->get_price();
        }
    }

    if (isset($base_extended) && ($base_extended != 0)) {
        $base_price = $base_extended * $base_price;
    }

    if (isset($extra_id) && sizeof($extra_id) > 0) {
        $BkxExtraObj = new BkxExtra();
        $addition_price = $BkxExtraObj->get_total_price_by_ids($extra_id);
    }

    $total_price = $base_price + $addition_price;


    return $total_price;
}


function bkx_cal_total_tax($total_price)
{
    $bkx_tax_rate = bkx_crud_option_multisite("bkx_tax_rate");
    $bkx_tax_name = bkx_crud_option_multisite("bkx_tax_name");
    $bkx_prices_include_tax = bkx_crud_option_multisite("bkx_prices_include_tax");


    if (empty($bkx_tax_rate) || $bkx_tax_rate < 0 || empty($total_price) || !is_numeric($total_price) || !is_numeric($bkx_tax_rate))
        return;
    $total_tax = array();
    $total_tax_is = 0;
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
        $total_tax['grand_total'] = $grand_total;
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

function bkx_check_slot_order_id($slot_id, $checked_booked_slots)
{

    if (!empty($checked_booked_slots)) {
        foreach ($checked_booked_slots as $key => $slot_data) {
            if ($slot_data['slot_id'] == $slot_id) {
                return $slot_data;
            }
        }
    }
}


function bkx_group_nums($array)
{
    $ret = array();
    $temp = array();
    foreach ($array as $val) {
        if (next($array) == ($val + 1))
            $temp[] = $val;
        else
            if (count($temp) > 0) {
                $temp[] = $val;
                $ret[] = $temp[0] . ':' . end($temp);
                $temp = array();
            } else
                $ret[] = $val;
    }
    return $ret;
}

/**
 *bkx_getDaysNumber method
 *This method returns array of numbers equivalent to days array
 *
 * @params array $arr
 * @return array
 */
function bkx_getDaysNumber($arr)
{
    $arrNumber = array();
    foreach ($arr as $day) {
        $day = strtolower($day);
        if ($day == "sunday") {
            array_push($arrNumber, 7);
        } elseif ($day == "monday") {
            array_push($arrNumber, 1);
        } elseif ($day == "tuesday") {
            array_push($arrNumber, 2);
        } elseif ($day == "wednesday") {
            array_push($arrNumber, 3);
        } elseif ($day == "thursday") {
            array_push($arrNumber, 4);
        } elseif ($day == "friday") {
            array_push($arrNumber, 5);
        } elseif ($day == "saturday") {
            array_push($arrNumber, 6);
        }
    }
    return ($arrNumber);

}

/**
 *bkx_checkIfSeatCanBeBooked method
 *This method checks wether the selected seat can be booked based on the total duration of base and addition selected
 *
 * @params string $seatid, integer $totalduration
 * @return boolean
 */
function bkx_checkIfSeatCanBeBooked($seatid, $totalduration)
{
    global $wpdb;
    //$res_seat = $wpdb->get_results("SELECT * FROM bkx_seat WHERE seat_id = ".trim($seatid)."");

    $GetSeatObj = get_post($seatid);
    $res_seat = get_post_custom($GetSeatObj->ID);
    $seat_is_certain_day = $values['seat_is_certain_day'][0];
    if (isset($seat_is_certain_day) && $seat_is_certain_day == "Y") {
        //$res_seat_time = $wpdb->get_results("SELECT * FROM bkx_seat_time WHERE seat_id = ".trim($seatid)."");
        $res_seat_time_arr = array();

        $seat_days_time = maybe_unserialize($values['seat_days_time'][0]);
        $res_seat_time = maybe_unserialize($seat_days_time);
        //print_r($res_seat_time);
        foreach ($res_seat_time as $temp) {
            $res_seat_time_arr[strtolower($temp->day)]['time_from'] = $temp->time_from;
            $res_seat_time_arr[strtolower($temp->day)]['time_till'] = $temp->time_till;
        }
        $days = 0;
        if ($totalduration > (24 * 60 * 60)) {
            $days = $totalduration / (24 * 60 * 60);
        }
        $days_mod = $totalduration % (24 * 60 * 60);
        if ($days_mod > 0) {
            $days = $days + 1;
        }
        //print_r($res_seat_time_arr);
        $arrDays = array();
        foreach ($res_seat_time_arr as $key => $val) {
            array_push($arrDays, $key);
        }

        $arrDays = bkx_getDaysNumber($arrDays);
        //print_r($arrDays);
        //sort days array 
        sort($arrDays, SORT_NUMERIC);
        $nums = $arrDays;
        $arrSorted = bkx_group_nums($nums);
        $arrNew = array();
        //print_r($arrSorted); 
        $newArryGrouped = array();
        $arrGrouped = array();
        $newArryGroupedLength = array();
        $temp_max = 0;
        $temp_max_arr = array();
        foreach ($arrSorted as $temp) {
            if (substr_count($temp, ':') > 0) {
                //array_push($newArryGrouped,$temp);
                $tempArryGrouped['value'] = $temp;
                $arrExploded = explode(':', $temp);
                $arrNumbers = range($arrExploded[0], $arrExploded[1]);
                $tempArryGrouped['length'] = sizeof($arrNumbers);
                //$temp_max = sizeof($arrNumbers);
                if (sizeof($arrNumbers) > $temp_max) {
                    $temp_max = sizeof($arrNumbers);
                    $temp_max_arr['value'] = $temp;
                    $temp_max_arr['length'] = sizeof($arrNumbers);
                }
                array_push($newArryGrouped, $tempArryGrouped);

                $arrGrouped[sizeof($arrNumbers)][] = $temp;
                //array_push($newArryGroupedLength, sizeof($arrNumbers));
            }
        }

        if (sizeof($arrGrouped) > 0) {
            $counter_temp == 0;
            $counter_temp_arr = array();
            foreach ($arrGrouped as $key => $temp) {
                if ($key > $counter_temp) {
                    $counter_temp = $key;
                    $counter_temp_arr = $temp;
                }
            }
            //print_r($counter_temp_arr);
            $finalValues = array();
            $counter = 0;
            foreach ($counter_temp_arr as $temp) {
                $tempExplode = explode(':', $temp);
                $tempValues = range($tempExplode[0], $tempExplode[1]);
                if ($counter == 0) {
                    $finalValues = $tempValues;
                }
                if (in_array(1, $tempValues) || in_array(7, $tempValues)) {
                    $finalValues = $tempValues;
                }
                $counter = $counter + 1;
            }
            $arrNew = $finalValues;
        } else {
            if (in_array(1, $arrSorted) && in_array(7, $arrSorted)) {
                $arrNew = array(1, 7);
            } else {
                $arrNew = array(end($arrSorted));
            }
        }
        //now checks the size of $arrNew 
        $lengthDays = sizeof($arrNew);
    } else {
        $lengthDays = 7;
        return 1;
    }
    //if($days > 7 && $lengthDays)
    if ($lengthDays < $days) {
        return 0;
    } else {
        return 1;
    }
}

function bkx_admin_actions()
{
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

function bkx_reorder_admin_menu_pages( $menu_ord ) 
{
    global $submenu;

    // Enable the next line to see all menu orders
    //echo '<pre>'.print_r($submenu ,true).'</pre>';
    $arr = array();
    $find_submenu = array();
    if(!empty($submenu['edit.php?post_type=bkx_booking'])){
        $submenu_obj = $submenu['edit.php?post_type=bkx_booking'];
        foreach ($submenu_obj as $key => $submenu_data) {
            //find Seat Post type Key
            if($submenu_data[2] =='edit.php?post_type=bkx_seat')
            {
                $find_submenu['bkx_seat_post_type'] = $key;
            }
            if($submenu_data[2] ==  'edit-tags.php?taxonomy=bkx_seat_cat&post_type=bkx_seat')
            {
                $find_submenu['bkx_seat_category'] = $key;
            }

            if($submenu_data[2] =='edit.php?post_type=bkx_base')
            {
                $find_submenu['bkx_base_post_type'] = $key;
            }
            if($submenu_data[2] ==  'edit-tags.php?taxonomy=bkx_base_cat&post_type=bkx_base')
            {
                $find_submenu['bkx_base_category'] = $key;
            }

            if($submenu_data[2] =='edit.php?post_type=bkx_addition')
            {
                $find_submenu['bkx_addition_post_type'] = $key;
            }
            if($submenu_data[2] ==  'edit-tags.php?taxonomy=bkx_addition_cat&post_type=bkx_addition')
            {
                $find_submenu['bkx_addition_category'] = $key;
            }


        }
    }

    
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][5];     //my original order was 5,10,15,16,17,18
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][11];
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_seat_category']];
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][12];
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_base_category']];
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][13];
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][$find_submenu['bkx_addition_category']];
    $arr[] = $submenu['edit.php?post_type=bkx_booking'][14];
    $submenu['edit.php?post_type=bkx_booking'] = $arr;

    return $menu_ord;
}


function bkx_plugin_add_settings_link($links)
{
    $settings_link = '<a href="edit.php?post_type=bkx_booking&page=bkx-setting">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}

add_filter("plugin_action_links_$plugin", 'bkx_plugin_add_settings_link');

function bkx_wpdocs_dequeue_script()
{
    wp_deregister_script('et-shortcodes-js');
}

add_action('wp_print_scripts', 'bkx_wpdocs_dequeue_script', 100);
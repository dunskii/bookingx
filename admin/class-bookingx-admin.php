<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dunskii.com
 * @since      0.7.6.4.4
 *
 * @package    Bookingx
 * @subpackage Bookingx/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bookingx
 * @subpackage Bookingx/admin
 * @author     Dunskii Web Services <divyang@dunskii.com>
 */
class Bookingx_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since      0.7.6.4.4
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since      0.7.6.4.4
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since      0.7.6.4.4
     */
    public function __construct($plugin_name = null, $version = null)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_filter('bkx_calender_unavailable_days', array($this, 'bkx_calender_unavailable_days'));
        add_filter('register_post_type_args', array($this, 'bkx_seat_post_type_args'), 10, 2);
    }

    public function bkx_seat_post_type_args($args, $post_type)
    {
        if ('bkx_seat' === $post_type || 'bkx_base' === $post_type || 'bkx_addition' === $post_type) {
            $args['show_in_rest'] = true;
            // Optionally customize the rest_base or rest_controller_class
            $args['rest_base'] = $post_type;
            $args['rest_controller_class'] = 'WP_REST_Posts_Controller';
        }
        return $args;
    }

    public function bkx_create_seat_post_type()
    {
        bkx_crud_option_multisite('bkx_alias_seat') == "" ? bkx_crud_option_multisite('bkx_alias_seat', 'Resource', 'update') : '';
        bkx_crud_option_multisite('bkx_alias_base') == "" ? bkx_crud_option_multisite('bkx_alias_base', 'Service', 'update') : '';
        bkx_crud_option_multisite('bkx_alias_addition') == "" ? bkx_crud_option_multisite('bkx_alias_addition', 'Extra', 'update') : '';
        bkx_crud_option_multisite('bkx_alias_notification') == "" ? bkx_crud_option_multisite('bkx_alias_notification', 'Notification', 'update') : '';
        bkx_crud_option_multisite('bkx_notice_time_extended_text_alias') == "" ? bkx_crud_option_multisite('bkx_notice_time_extended_text_alias', 'How many times you want to extend this service?', 'update') : '';
        bkx_crud_option_multisite('bkx_label_of_step1') == "" ? bkx_crud_option_multisite('bkx_label_of_step1', 'Please select what you would like to book', 'update') : '';
        $bkx_booking_style = bkx_crud_option_multisite('bkx_booking_style');
        if ((!isset($bkx_booking_style) || $bkx_booking_style == "")) {
            bkx_crud_option_multisite("bkx_booking_style", "default", 'update');
        }
        $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
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
            'has_archive' => sanitize_title($alias_seat),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => sanitize_title($alias_seat)
            ),
            'show_in_menu' => 'edit.php?post_type=bkx_booking'
        ));
        $bkx_seat_taxonomy_status = bkx_crud_option_multisite('bkx_seat_taxonomy_status');
        if (isset($bkx_seat_taxonomy_status) && $bkx_seat_taxonomy_status == 1) {
            // Category
            $labels = array(
                'name' => __($alias_seat . ' Category', 'bookingx'),
                'singular_name' => __($alias_seat . ' Category', 'bookingx'),
                'search_items' => __('Search ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'all_items' => __('All ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'parent_item' => __('Parent ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'parent_item_colon' => __('Parent ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'edit_item' => __('Edit ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'update_item' => __('Update ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'add_new_item' => __('Add New ' . ucwords($alias_seat) . ' Category', 'bookingx'),
                'new_item_name' => __('New Category Name', 'bookingx'),
                'menu_name' => __('Categories', 'bookingx'),
            );
            register_taxonomy('bkx_seat_cat', array('bkx_seat'), array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'query_var' => true,
                'show_in_menu' => true,
                'rewrite' => array(
                    'slug' => 'bkx-seat-category'
                )
            ));
        }
    }

    public function bkx_create_base_post_type()
    {
        $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
        $bkx_alias_base = isset($bkx_alias_base) && $bkx_alias_base != '' ? $bkx_alias_base : "Service";
        // Set UI labels for Custom Post Type
        $labels = array(
            'name' => _x($bkx_alias_base, 'Post Type General Name', 'bookingx'),
            'singular_name' => _x($bkx_alias_base, 'Post Type Singular Name', 'bookingx'),
            'menu_name' => __($bkx_alias_base, 'bookingx'),
            'all_items' => __($bkx_alias_base, 'bookingx'),
            'view_item' => __("View $bkx_alias_base", 'bookingx'),
            'add_new_item' => __("Add New $bkx_alias_base", 'bookingx'),
            'add_new' => __("Add New $bkx_alias_base", 'bookingx'),
            'edit_item' => __("Edit $bkx_alias_base", 'bookingx'),
            'update_item' => __("Update $bkx_alias_base", 'bookingx'),
            'search_items' => __("Search $bkx_alias_base", 'bookingx'),
            'not_found' => __('Not Found', 'bookingx'),
            'not_found_in_trash' => __('Not found in Trash', 'bookingx')
        );
        register_post_type('bkx_base', array(
            'labels' => $labels,
            'public' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_menu' => 'edit.php?post_type=bkx_booking',
            'has_archive' => sanitize_title($bkx_alias_base),
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => sanitize_title($bkx_alias_base)
            ),
        ));
        $bkx_base_taxonomy_status = bkx_crud_option_multisite('bkx_base_taxonomy_status');
        if (isset($bkx_base_taxonomy_status) && $bkx_base_taxonomy_status == 1) {
            // Category
            $labels = array(
                'name' => __($bkx_alias_base . ' Category', 'bookingx'),
                'singular_name' => __($bkx_alias_base . ' Category', 'bookingx'),
                'search_items' => __('Search ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'all_items' => __('All ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'parent_item' => __('Parent ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'parent_item_colon' => __('Parent ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'edit_item' => __('Edit ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'update_item' => __('Update ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'add_new_item' => __('Add New ' . ucwords($bkx_alias_base) . ' Category', 'bookingx'),
                'new_item_name' => __('New ' . ucwords($bkx_alias_base) . ' Category Name', 'bookingx'),
                'menu_name' => __('Categories', 'bookingx'),
            );
            register_taxonomy('bkx_base_cat', array('bkx_base'), array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'query_var' => true,
            ));
        }
    }

    public function bkx_create_addition_post_type()
    {
        $bkx_alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
        $bkx_alias_addition = isset($bkx_alias_addition) && $bkx_alias_addition != '' ? $bkx_alias_addition : "Extra";
        $bkx_addition_taxonomy_status = bkx_crud_option_multisite('bkx_addition_taxonomy_status');
        $labels = array(
            'name' => _x($bkx_alias_addition, 'Post Type General Name', 'bookingx'),
            'singular_name' => _x($bkx_alias_addition, 'Post Type Singular Name', 'bookingx'),
            'menu_name' => __($bkx_alias_addition, 'bookingx'),
            'all_items' => __($bkx_alias_addition, 'bookingx'),
            'view_item' => __("View $bkx_alias_addition", 'bookingx'),
            'add_new_item' => __("Add New Extra", 'bookingx'),
            'add_new' => __("Add New Extra", 'bookingx'),
            'edit_item' => __("Edit Extra", 'bookingx'),
            'update_item' => __("Update Extra", 'bookingx'),
            'search_items' => __("Search $bkx_alias_addition", 'bookingx'),
            'not_found' => __('Not Found', 'bookingx'),
            'not_found_in_trash' => __('Not found in Trash', 'bookingx')
        );
        register_post_type('bkx_addition', array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => sanitize_title($bkx_alias_addition),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => sanitize_title($bkx_alias_addition)
            ),
            'show_in_menu' => 'edit.php?post_type=bkx_booking'
        ));
        if (isset($bkx_addition_taxonomy_status) && $bkx_addition_taxonomy_status == 1) {
            // Category
            $labels = array(
                'name' => __($bkx_alias_addition . ' Category', 'bookingx'),
                'singular_name' => __($bkx_alias_addition . ' Category', 'bookingx'),
                'search_items' => __('Search ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'all_items' => __('All ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'parent_item' => __('Parent ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'parent_item_colon' => __('Parent ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'edit_item' => __('Edit ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'update_item' => __('Update ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'add_new_item' => __('Add New ' . ucwords($bkx_alias_addition) . ' Category', 'bookingx'),
                'new_item_name' => __('New ' . ucwords($bkx_alias_addition) . ' Category Name', 'bookingx'),
                'menu_name' => __('Categories', 'bookingx'),
            );
            register_taxonomy('bkx_addition_cat', array('bkx_addition'), array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'query_var' => true,
            ));
        }
    }

    public function bkx_create_booking_post_type()
    {
        $labels = array(
            'name' => _x('Bookings', 'Bookings', 'bookingx'),
            'singular_name' => _x('Booking', 'Bookings', 'bookingx'),
            'menu_name' => __('Booking X', 'bookingx'),
            'all_items' => __("Bookings", 'bookingx'),
            'view_item' => __("View Booking", 'bookingx'),
            'add_new_item' => __("Add Booking", 'bookingx'),
            'add_new' => __("Add Booking", 'bookingx'),
            'edit_item' => __("Update Booking", 'bookingx'),
            'update_item' => __("Update booking", 'bookingx'),
            'search_items' => __("Search bookings", 'bookingx'),
            'not_found' => __(' Booking\'s not Found', 'bookingx'),
            'not_found_in_trash' => __('Booking\'s not found in Trash', 'bookingx')
        );
        register_post_type('bkx_booking', array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => null,
            'delete_with_user' => false,
            'hierarchical' => false,
            'supports' => array('title'),
        ));
    }

    function bkx_notification_bubble_menu()
    {
        remove_submenu_page('edit.php?post_type=bkx_booking', 'post-new.php?post_type=bkx_booking');
        global $menu;
        $count_posts = (array)wp_count_posts('bkx_booking');
        $pending_booking_count = !empty($count_posts) && $count_posts['bkx-pending'] > 0 ? $count_posts['bkx-pending'] : 0;
        foreach ($menu as $key => $value) {
            if ($menu[$key][2] == 'edit.php?post_type=bkx_booking') {
                $menu[$key][6] = 'dashicons-calendar-alt';
                if ($pending_booking_count > 0) {
                    $menu[$key][0] .= ' &nbsp;<span class="update-plugins bkx-count-' . $pending_booking_count . '"><span class="plugin-count" aria-hidden="true">' . $pending_booking_count . '</span><span class="screen-reader-text">' . $pending_booking_count . ' Booking\'s Pending</span></span>';
                }
                return;
            }
        }
    }

    function bkx_booking_parse_comment_query( $wp ){
	     if(is_admin() ){
		     $current_screen = get_current_screen();
		     if(!is_wp_error($current_screen) && $current_screen->base == 'edit-comments'){
			     $disable_custom_type = apply_filters('bkx_disable_custom_type_in_comment_lists',array('booking_note'));
			     $wp->query_vars['type__not_in'] = $disable_custom_type;
		     }
         }
	    return $wp;
    }

    function bkx_booking_search_custom_fields($wp) {
        global $pagenow;
        if ('edit.php' != $pagenow || empty($wp->query_vars['s']) || $wp->query_vars['post_type'] != 'bkx_booking') {
            return;
        }
        $order = new BkxBooking();
        $post_ids = $order->order_search($wp->query_vars['s']);
        if (!empty($wp->query_vars['s'])) {
            // Remove "s" - we don't want to search order name.
            unset($wp->query_vars['s']);
            // so we know we're doing this.
            $wp->query_vars['bkx_booking_search'] = true;
            // Search by found posts.
            $wp->query_vars['post__in'] = $post_ids;
        }
    }

    function bkx_add_meta_query($query)
    {
        global $pagenow;
        if (isset($pagenow) && $pagenow != 'edit.php' || $query->query_vars['post_type'] != 'bkx_booking') {
            return;
        }
        $seat_view = isset($query->query_vars['seat_view']) ? $query->query_vars['seat_view'] : '';
        $search_by_dates = isset($query->query_vars['search_by_dates']) ? $query->query_vars['search_by_dates'] : '';
        $search_by_selected_date = isset($query->query_vars['search_by_selected_date']) ? $query->query_vars['search_by_selected_date'] : '';
        $search_by_selected_date = date('Y-m-d', strtotime($search_by_selected_date));

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
        if (isset($seat_view) && $seat_view > 0) {
            $seat_view_query = array(
                array(
                    'key' => 'seat_id',
                    'value' => $seat_view,
                    'compare' => '=',
                ),
            );
            $query->set('meta_query', $seat_view_query);
        }
        switch ($search_by_dates) {
            case 'today':
                $search_by_dates_meta = array(
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-d'),
                        'compare' => '=',
                    ),
                );
                break;
            case 'tomorrow':
                $tomorrow = date("Y-m-d", strtotime("+1 day"));
                $search_by_dates_meta = array(
                    array(
                        'key' => 'booking_date',
                        'value' => $tomorrow,
                        'compare' => '=',
                    ),
                );
                break;
            case 'this_week':
                $monday = date('Y-m-d', strtotime('monday this week'));
                $sunday = date('Y-m-d', strtotime('sunday this week'));
                $search_by_dates_meta = array(
                    array(
                        'key' => 'booking_date',
                        'value' => array($monday, $sunday),
                        'compare' => 'BETWEEN',
                    ),
                );
                break;
            case 'next_week':
                $monday = date('Y-m-d', strtotime('monday next week'));
                $sunday = date('Y-m-d', strtotime('sunday next week'));
                $search_by_dates_meta = array(
                    array(
                        'key' => 'booking_date',
                        'value' => array($monday, $sunday),
                        'compare' => 'BETWEEN',
                    ),
                );
                break;
            case 'this_month':
                $monday = date('Y-m-01');
                $sunday = date('Y-m-t');
                $search_by_dates_meta = array(
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'booking_date',
                            'value' => array($monday, $sunday),
                            'compare' => 'BETWEEN',
                        ),
                        array(
                            'key' => 'booking_date',
                            'value' => array(date('Y-n-1'), $sunday),
                            'compare' => 'BETWEEN',
                        ),
                    )
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
                $meta_query[] = $search_by_dates_meta;
                $meta_query[] = $seat_view_query;
                $query->set('meta_query', $meta_query);
            }
        }
    }

    public function bkx_booking_handle_bulk_actions( $redirect_to, $do_action, $post_ids ) {
        $allow_status = array( 'mark_pending', 'mark_ack', 'mark_completed', 'mark_missed', 'mark_cancelled' );
        if ( !in_array($do_action, $allow_status) ) {
            return $redirect_to;
        }
        $order_status = "";
        foreach ( $post_ids as $post_id ) {
            if(isset($post_id) && $post_id > 0 ){
                $bkx_action_status = explode("_", sanitize_text_field($do_action));
                $order_status = $bkx_action_status[1];
                if (is_multisite()):
                    $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
                    switch_to_blog($blog_id);
                endif;
                $BkxBooking = new BkxBooking('', $post_id);
                $BkxBooking->update_status($order_status);
            }
        }
        $redirect_to = add_query_arg( array( 'bulk_status_changed' => $order_status, 'bulk_status_count' => count( $post_ids )  ), $redirect_to );
        return $redirect_to;
    }

    function bkx_bulk_action_admin_notice(){
        if ( ! empty( $_REQUEST['bulk_status_changed'] ) ) {
            $count     = intval($_REQUEST['bulk_status_count']);
            $status = ucwords($_REQUEST['bulk_status_changed']);
            printf( '<div id="message" class="updated fade">' .
                _n( "Booking have changed status to {$status}.",
                    '%d of bookings have changed status to %s.',
                    $count ,
                    'bookingx'
                ) . '</div>', $count, $status );
        }
    }


    function bkx_booking_bulk_actions($actions){
        if (isset($actions['edit'])) {
            unset($actions['edit']);
        }
        return $actions;
    }

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
     * Register the stylesheets for the admin area.
     *
     * @since      0.7.6.4.4
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bookingx_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bookingx_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
            if(is_bookingx_admin() === true ){
                $wp_scripts = wp_scripts();
                wp_enqueue_style('bkx-admin-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css',
                    false, BKX_PLUGIN_VER, false);
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/bookingx-admin.css', array(), $this->version, 'all');
            }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since      0.7.6.4.4
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bookingx_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bookingx_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if(is_bookingx_admin() === true ){
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script("bkx-seat-sol", BKX_PLUGIN_DIR_URL . "public/js/admin/sol.js", false, BKX_PLUGIN_VER, true);
            wp_enqueue_style('bkx-seat-sol-style', BKX_PLUGIN_DIR_URL . "public/css/sol.css");

            $enable_cancel_booking = bkx_crud_option_multisite('enable_cancel_booking');
            $enable_cancel_booking = isset($enable_cancel_booking) ? $enable_cancel_booking : 0;

            $wp_localize_array = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'is_cancel' => $enable_cancel_booking,
                'default_style' => BKX_PLUGIN_PUBLIC_URL . "/images/preview/2.jpg",
                'day_style' => BKX_PLUGIN_PUBLIC_URL . "/images/preview/2b.jpg");
            wp_register_script("bkx-admin-js", plugin_dir_url(__FILE__) . 'js/bookingx-admin.js', array('jquery', 'iris'), BKX_PLUGIN_VER, true);
            wp_localize_script('bkx-admin-js', 'bkx_admin', $wp_localize_array);
            wp_enqueue_script('bkx-admin-js');
        }

    }

    public function createDateRange($first, $last, $step = '+1 day', $output_format = 'm/d/Y')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while ($current <= $last) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public function bkx_calender_unavailable_days($days)
    {

        $bkx_biz_vac_sd = bkx_crud_option_multisite('bkx_biz_vac_sd');
        $bkx_biz_vac_ed = bkx_crud_option_multisite('bkx_biz_vac_ed');
        $biz_vacations = array();
        if (isset($bkx_biz_vac_sd) && !empty($bkx_biz_vac_sd) && isset($bkx_biz_vac_ed) && !empty($bkx_biz_vac_ed)) {
            $biz_vacations = $this->createDateRange($bkx_biz_vac_sd, $bkx_biz_vac_ed);
        }
        $bkx_biz_pub_holiday = bkx_crud_option_multisite('bkx_biz_pub_holiday');
        $holidays = array();
        if (!empty($bkx_biz_pub_holiday)) {
            foreach ($bkx_biz_pub_holiday as $date) {
                if (isset($date) && !empty($date)) {
                    $holidays[] = $date;
                }
            }
        }
        $biz_vacations = array_merge($biz_vacations, $holidays);
        $days['unavailable_days'] = array_merge($biz_vacations, array_unique($days['unavailable_days']));
        return $days;
    }

    public function date_format_correct($date, $format = 'm/d/Y')
    {
        $date = date('m/d/Y', strtotime($date));
        $date_arr = explode("/", $date);
        $date_formated = strtotime("{$date_arr[2]}/{$date_arr[1]}/{$date_arr[0]}");// "Y-m-d"
        $date_formated = date($format, $date_formated);
        return $date_formated;
    }

    public function export_now()
    {
        if (isset($_POST['export_xml']) && sanitize_text_field($_POST['export_xml']) == "Export xml") :
            $BkxExportObj = new BkxExport();
            $BkxExportObj->export_now();
            die;
        endif;
    }

    public function import_now()
    {
        if (isset($_POST['import_xml']) && sanitize_text_field($_POST['import_xml']) == "Import Xml") :
            $BkxImport = new BkxImport();
            $BkxImport->import_now($_FILES, $_POST);
        endif;
    }

    function bkx_booking_columns($existing_columns)
    {
        unset($existing_columns);
	    $columns = $this->bkx_booking_columns_data();
        // Dashboard Column manage to show data ( Fetch Data from Other Setting Tab)
        $bkx_dashboard_column_selected = bkx_crud_option_multisite("bkx_dashboard_column");
	    $bkx_booking_columns_data = apply_filters('bkx_dashboard_booking_columns_before', $columns);
        if (!empty($bkx_booking_columns_data)) {
            foreach ($bkx_booking_columns_data as $key => $booking_columns) {
                if ($key != "cb" && !empty($bkx_dashboard_column_selected) && !in_array($key, $bkx_dashboard_column_selected)) {
                    unset($columns[$key]);
                }
            }
        }
        $columns = apply_filters('bkx_dashboard_booking_columns_after', $columns);
        return $columns;
    }

    public function bkx_booking_columns_data()
    {
        $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');
        $base_alias = bkx_crud_option_multisite('bkx_alias_base');
        $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');

        $columns = array();
        $columns['cb'] = '<input type="checkbox" />';
        $columns['order_status'] = '<span class="status_head tips" data-tip="' . esc_attr__('Status', 'bookingx') . '">' . esc_attr__('Current Status', 'bookingx') . '</span>';
        $columns['booking_date'] = __('Date', 'bookingx');
        $columns['booking_time'] = __('Time', 'bookingx');
        $columns['duration'] = __('Duration', 'bookingx');
        $columns['client_name'] = __('Name', 'bookingx');
        $columns['client_phone'] = __('Phone', 'bookingx');
        $columns['seat'] = __($seat_alias, 'bookingx');
        $columns['service'] = __($base_alias, 'bookingx');
        $columns['extra'] = __('# of ' . $addition_alias, 'bookingx');
        $columns['order_total'] = __('Total', 'bookingx');
        $columns['order_actions'] = __('Actions', 'bookingx');
        $columns['order_view'] = __('View', 'bookingx');

        $columns = apply_filters('bkx_dashboard_booking_columns', $columns);

        return $columns;
    }

    public function render_bkx_booking_columns($column)
    {
        global $post;

        if (empty($post->ID))
            return;

        $orderObj = new BkxBooking();
        $order_meta = $orderObj->get_order_meta_data($post->ID);
        $base_id = $order_meta['base_id'];
        $BkxBaseObj = new BkxBase('', $base_id);
        $base_time = $BkxBaseObj->get_time($base_id);
        $base_time_option = get_post_meta($post->ID, 'base_time_option', true);
        $base_time_option = (isset($base_time_option) && $base_time_option != "") ? $base_time_option : "H";
        $total_time = "-";
        $date_format = bkx_crud_option_multisite('date_format');
        if (isset($base_time_option) && $base_time_option == "H") {
            $total_time = getDateDuration($order_meta);
            $duration = getDuration($order_meta);
            $date_data = sprintf(__('%s', 'bookingx'), date($date_format, strtotime($order_meta['booking_date'])));
        } else {
            list($date_data, $duration) = getDayDateDuration($post->ID);
        }
        switch ($column) {
            case 'order_status':
                $order_status = $orderObj->get_order_status($post->ID);
                echo sprintf(__('%s', 'bookingx'), $order_status);
                break;
            case 'booking_date':
                echo sprintf(__('%s', 'bookingx'), $date_data);
                break;
            case 'booking_time':
                echo "{$total_time}";
                break;
            case 'duration':
                echo $duration;
                break;
            case 'client_name':
                echo sprintf(__('%s %s', 'bookingx'), $order_meta['first_name'], $order_meta['last_name']);
                break;
            case 'client_phone':
                $client_phone = $order_meta['phone'];
                echo sprintf(__('%s', 'bookingx'), $client_phone);
                break;
            case 'seat':
                $seat_arr = $order_meta['seat_arr'];
                printf(__('%s', 'bookingx'), $seat_arr['title']);
                break;
            case 'service':
                $base_arr = $order_meta['base_arr'];
                printf(__('%s', 'bookingx'), $base_arr['main_obj']->post->post_title);
                break;
            case 'extra':
                if (isset($order_meta['extra_arr']) && $order_meta['extra_arr'] != "") {
                    $extra_arr = $order_meta['extra_arr'];
                    echo sizeof($extra_arr);
                }
                break;
            case 'order_total':
                $currency = isset($order_meta['currency']) && $order_meta['currency'] != '' ? $order_meta['currency'] : bkx_get_current_currency();
                $total_price = $order_meta['total_price'];
                echo sprintf(__('%s%d %s', 'bookingx'), $currency, $total_price, bkx_crud_option_multisite('currency_option'));
                break;
            case 'order_actions':
                $order_status = strtolower($orderObj->get_order_status($post->ID));
                $exclude_status = array('completed', 'missed', 'cancelled');
                $pending = ($order_status == 'pending') ? "selected" : '';
                $ack = ($order_status == 'acknowledged') ? "selected" : '';
                $completed = ($order_status == 'completed') ? "selected" : '';
                $missed = ($order_status == 'missed') ? "selected" : '';
                $cancelled = ($order_status == 'cancelled') ? "selected" : '';

                $business_address_1 = bkx_crud_option_multisite("bkx_business_address_1");
                $business_address_2 = bkx_crud_option_multisite("bkx_business_address_2");
                $bkx_business_city = bkx_crud_option_multisite("bkx_business_city");
                $bkx_business_state = bkx_crud_option_multisite("bkx_business_state");
                $bkx_business_zip = bkx_crud_option_multisite("bkx_business_zip");
                $bkx_business_country = bkx_crud_option_multisite("bkx_business_country");

                $full_address = "{$business_address_1},{$business_address_2},{$bkx_business_city},{$bkx_business_state},{$bkx_business_zip},{$bkx_business_country}";
                if (isset($post_json)) {
                    echo '<input type="hidden" id="post_ids" value="' . $post_json . '">';
                }
                echo '<input type="hidden" id="address_val' . $post->ID . '" value="' . $full_address . '">';
                $status_dropdown = '';
                $status_dropdown .= '<select name="bkx-action" class="bkx_action_status" style="width:91px;" id="bulk-action-selector-top">';
                $status_dropdown .= '<option value="' . $post->ID . '_pending" ' . $pending . '>Pending</option>';
                $status_dropdown .= '<option value="' . $post->ID . '_ack" ' . $ack . '>Acknowledged</option>';
                $status_dropdown .= '<option value="' . $post->ID . '_completed" ' . $completed . '>Completed</option>';
                $status_dropdown .= '<option value="' . $post->ID . '_missed" ' . $missed . '>Missed</option>';
                $status_dropdown .= '<option value="' . $post->ID . '_cancelled" ' . $cancelled . '>Cancelled</option></select>';
                if (!in_array($order_status, $exclude_status)) {
                    echo $status_dropdown;
                } else {
                    ?>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery("#cb-select-<?php echo $post->ID;?>").attr('disabled', 'disabled');
                        });
                    </script>
                    <?php echo ucwords($order_status);
                }
                break;
            case 'order_view' :
                echo sprintf(__('<span class="view_href_booking-' . $post->ID . '"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx'), 'javascript:bkx_view_summary(\'' . $post->ID . '\',\'' . $order_meta['first_name'] . ' ' . $order_meta['last_name'] . '\')', 'View', 'View');
                echo sprintf(__('<span class="close_booking-' . $post->ID . '" style="display:none;"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx'), 'javascript:close_booking(\'' . $post->ID . '\')', 'Close', 'Close');
                echo '<div class="loader-' . $post->ID . '" style="display:none;"> Please wait..</div>';
                break;
        }
    }

    public function bkx_change_view_link($permalink, $post)
    {
        if ($post->post_type == 'bkx_booking') {
            $orderObj = new BkxBooking();
            $order_meta = $orderObj->get_order_meta_data($post->ID);
            $permalink = "";
            if (!empty($order_meta) && isset($order_meta['first_name'])) {
                $permalink = 'javascript:bkx_view_summary(\'' . $post->ID . '\',\'' . $order_meta['first_name'] . ' ' . $order_meta['last_name'] . '\')';
            }
        }
        return $permalink;
    }

    public function bkx_booking_bulk_admin_footer()
    {
        global $post_type;

        ob_start();
        if ('bkx_booking' == $post_type) {
            $bkx_view_id = "";
            $bkx_view_name = "";
            if (isset($_REQUEST["view"])) {
                $bkx_view_id = sanitize_text_field($_REQUEST["view"]);
            }
            if (isset($_REQUEST["bkx_name"])) {
                $bkx_view_name = sanitize_text_field($_GET["bkx_view_name"]);
            }
            ?>
            <script type="text/javascript">
                function bkx_view_summary(post_id, name, colour_json) {
                    jQuery('.loader-' + post_id).show();
                    var data = {
                        'action': 'bkx_action_view_summary',
                        'post_id': post_id
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        var bkx_row_action_view = jQuery('#post-' + post_id + ' .row-actions .view');
                        jQuery('.loader-' + post_id).hide();
                        jQuery('.close_booking-' + post_id).show();
                        jQuery(bkx_row_action_view).hide();
                        jQuery("<span class=\"close close_booking-" + post_id + "\" style=\"\"><a href=\"javascript:close_booking(" + post_id + ")\" title=\"Close\" style=\"padding: 0 5px;\">Close</a></span>").insertAfter(bkx_row_action_view);
                        var postobj = jQuery('#post-' + post_id);
                        jQuery('.view_href_booking-' + post_id).hide();
                        jQuery("<tr class='view_summary view_summary-" + post_id + "'>" + response).insertAfter(postobj).slideDown('slow');
                    });
                }

                function close_booking(post_id) {
                    var bkx_row_action_close = jQuery('#post-' + post_id + ' .row-actions .close');
                    var bkx_row_action_view = jQuery('#post-' + post_id + ' .row-actions .view');
                    jQuery('.close_booking-' + post_id).hide();
                    jQuery('.view_href_booking-' + post_id).show();
                    jQuery('.view_summary-' + post_id).hide();
                    jQuery(bkx_row_action_view).show();
                    jQuery(bkx_row_action_close).remove();
                }

                jQuery(function () {
                    var bkx_view_id = '<?php echo $bkx_view_id; ?>';
                    var bkx_view_name = '<?php echo $bkx_view_name; ?>';
                    if (bkx_view_id) {
                        jQuery('#post-' + bkx_view_id).focus();
                        bkx_view_summary(bkx_view_id, bkx_view_name);
                    }
                    jQuery('.search_by_dates').on('change', function () {
                        var search_by_dates = jQuery(this).val();
                        if (search_by_dates == 'choose_date') {
                            jQuery('.search_by_dates_section').html('');
                            jQuery('.search_by_dates_section').append('<input type="hidden" name="search_by_dates" value="choose_date" >');
                            jQuery('.search_by_dates_section').append('<input type="text" name="search_by_selected_date" placeholder="Select a date" class="search_by_selected_date">');
                            jQuery(".search_by_selected_date").datepicker();
                        }
                    });
                    var span_trash = jQuery('.row-actions').find('span.trash').find('a');
                    jQuery(".listing_view").nextAll('#post-query-submit').remove();
                    jQuery(span_trash).click(function () {
                        if (confirm('Are you sure want to Cancel this booking?')) {
                        } else {
                            return false;
                        }
                    });

                    jQuery('.row-actions').find('span.trash').find('a').text('Cancel');
                    jQuery('<option>').val('mark_pending').text('<?php _e('Pending', 'bookingx');?>').appendTo('select[name="action"]');

                    jQuery('<option>').val('mark_ack').text('<?php _e('Acknowledged', 'bookingx');?>').appendTo('select[name="action"]');

                    jQuery('<option>').val('mark_completed').text('<?php _e('Completed', 'bookingx');?>').appendTo('select[name="action"]');

                    jQuery('<option>').val('mark_missed').text('<?php _e('Missed', 'bookingx');?>').appendTo('select[name="action"]');

                    jQuery('<option>').val('mark_cancelled').text('<?php _e('Cancelled', 'bookingx');?>').appendTo('select[name="action"]');

                    jQuery('<option>').val('mark_pending').text('<?php _e('Pending', 'bookingx');?>').appendTo('select[name="action2"]');

                    jQuery('<option>').val('mark_ack').text('<?php _e('Acknowledged', 'bookingx');?>').appendTo('select[name="action2"]');

                    jQuery('<option>').val('mark_completed').text('<?php _e('Completed', 'bookingx');?>').appendTo('select[name="action2"]');

                    jQuery('<option>').val('mark_missed').text('<?php _e('Missed', 'bookingx');?>').appendTo('select[name="action2"]');

                    jQuery('<option>').val('mark_cancelled').text('<?php _e('Cancelled', 'bookingx');?>').appendTo('select[name="action2"]');

                });

                jQuery('.bkx_action_status').on('change', function () {
                    if (confirm('Are you sure want to change status?')) {
                        var bkx_action_status = jQuery(this).val();
                        var data = {
                            'action': 'bkx_action_status',
                            'status': bkx_action_status
                        };

                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        jQuery.post(ajaxurl, data, function (response) {
                            location.reload();
                        });
                    } else {
                        return false;
                    }
                });
                <?php
                $listing_view = isset($_REQUEST['listing_view']) ? sanitize_text_field($_REQUEST['listing_view']) : '';
                if($listing_view == 'weekly' || $listing_view == 'monthly'){

                $this->bkx_generate_listing_view($listing_view);
                ?>
                jQuery("<div class='weekly-data' id='calendar'></div>").insertBefore(".wp-list-table").slideDown('slow');
                jQuery(".wp-list-table").hide();
                <?php } ?>
            </script>
            <?php
            $content = ob_get_contents();

            return $content;
        }
    }

    public function bkx_booking_search_by_dates($post_type, $which = null)
    {
        //only add filter to post type you want
        if ('bkx_booking' == $post_type) {
            //change this to the list of values you want to show
            //in 'label' => 'value' format
            $values = array(
                'All dates' => '',
                'Today' => 'today',
                'Tomorrow' => 'tomorrow',
                'This Week' => 'this_week',
                'Next Week' => 'next_week',
                'This Month' => 'this_month',
                'Select a Date' => 'choose_date'
            );
            $search_by_dates = isset($_GET['search_by_dates']) ? sanitize_text_field($_GET['search_by_dates']) : '';

            if ($search_by_dates == 'choose_date') {
                $search_by_dates = '';
            }
            ?>
            <div class="search_by_dates_section" style="float: left;">
                <select name="search_by_dates" class="search_by_dates">
                    <?php
                    foreach ($values as $label => $value) {
                        printf
                        ('<option value="%s"%s>%s</option>',
                            $value,
                            $value == $search_by_dates ? ' selected="selected"' : '',
                            $label
                        );
                    }
                    ?>
                </select>
            </div>
            <input type="submit" name="filter_action" style="float: left;" id="post-query-submit" class="button"
                   value="Filter">
            <?php
        }
    }

    public function bkx_booking_seat_view($post_type, $which = null)
    {
        if ('bkx_booking' == $post_type) {

            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'bkx_seat',
                'post_status' => 'publish',
                'suppress_filters' => false
            );
            $get_seat_array = get_posts($args);
            $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
            if (!empty($get_seat_array)) {

                foreach ($get_seat_array as $key => $seat_data) {
                    $seat_obj[$seat_data->ID] = $seat_data->post_title;
                }
            }

            if (!empty($seat_obj)) : ?>
                <select name="seat_view" class="seat_view">
                    <option> Select <?php echo $alias_seat; ?> </option>
                    <?php
                    $seat_view = isset($_GET['seat_view']) ? sanitize_text_field($_GET['seat_view']) : '';
                    foreach ($seat_obj as $seat_id => $seat_name) {
                        printf
                        ('<option value="%s"%s>%s</option>',
                            $seat_id,
                            $seat_id == $seat_view ? ' selected="selected"' : '',
                            $seat_name
                        );
                    }
                    ?>
                </select>
                <input type="submit" name="filter_action" style="float: left;" class="button seat_view" value="view">
            <?php endif;
        }
    }

    public function bkx_booking_listing_view($post_type, $which = null)
    {
        //only add filter to post type you want
        if ('bkx_booking' == $post_type) {

            $values = array(
                'Agenda' => 'agenda',
                'Weekly' => 'weekly',
                'Monthly' => 'monthly'
            );
            ?>
            <select name="listing_view" class="listing_view">
                <?php
                $listing_view = isset($_GET['listing_view']) ? sanitize_text_field($_GET['listing_view']) : '';
                foreach ($values as $label => $value) {
                    printf
                    ('<option value="%s"%s>%s</option>',
                        $value,
                        $value == $listing_view ? ' selected="selected"' : '',
                        $label
                    );
                }
                ?>
            </select>
            <input type="submit" name="filter_action" style="float: left;" class="button listing_view"
                   value="Change view">
            <?php
        }
    }

    public function bkx_generate_listing_view($type)
    {
        if ($type == 'weekly' || $type == 'monthly') {
            $defaultView = ($type) && $type == 'weekly' ? 'agendaWeek' : 'month';
            $bkx_calendar_json_data = bkx_crud_option_multisite('bkx_calendar_json_data');
            $BkxBooking = new BkxBooking();
            $search['booking_date'] = date('Y-m-d');
            $search['by'] = "this_month";
            $search['type'] = $type;
            $bkx_calendar_json_data = $BkxBooking->CalendarJsonData($search);
            if (isset($bkx_calendar_json_data) && !empty($bkx_calendar_json_data)) {
                ?>
                jQuery(document).ready(function() {
                jQuery('#calendar').fullCalendar({
                plugins: [ 'interaction', 'dayGrid', 'timeGrid' ],
                header: {
                left: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                defaultView: '<?php echo $defaultView; ?>',
                defaultDate: '<?php echo date('Y-m-d'); ?>',
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                eventLimit: true,
                viewRender: function(currentView){
                var minDate = moment(),
                maxDate = moment().add(2,'weeks');
                // Past
                if (minDate >= currentView.start && minDate <= currentView.end) {
                jQuery(".fc-prev-button").prop('disabled', true);
                jQuery(".fc-prev-button").addClass('fc-state-disabled');
                }
                else {
                jQuery(".fc-prev-button").removeClass('fc-state-disabled');
                jQuery(".fc-prev-button").prop('disabled', false);
                }

                },
                events: [<?php echo $bkx_calendar_json_data; ?>]
                });
                });
            <?php }
        }
    }
}

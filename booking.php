<?php
/* *
 *  Plugin Name: BookingX
 *  Plugin URI: 
 *  Description: BookingX is a booking and appointments plugin for WordPress
 *  Author: Dunskii Web Services 
 *  Version: 1.1 
 *  Author URI: http://dunskii.com
 * 
 *  Text Domain: bookingx
 *  Domain Path: /i18n/languages/
 */

session_start();
define( 'WP_MEMORY_LIMIT', '512M' );
//session_start();
//include shord code file
require_once( 'inc/class-meta-box-functions.php' );
require_once( 'paypal.class.php' ); //when needed
require_once( 'httprequest.php' ); //when needed
require_once( 'create_shortcode.php' ); //when needed
require_once( 'shortcode/shortcode.php' );
require_once( 'paypal.class.php' );
require_once( 'inc/class-bkx-seat.php' );
require_once( 'inc/class-bkx-base.php' );
require_once( 'inc/class-bkx-extra.php' );
require_once( 'inc/class-bkx-booking.php' );
require_once( 'inc/template-hooks.php' );
require_once( 'inc/template-functions.php' );
require_once( 'inc/multisite-functions.php' );
require_once( 'inc/class-order-meta-box.php' );
require_once( 'git-updater/BFIGitHubPluginUploader.php' );

require_once( 'my_account_bx.php' );
require_once( 'my_login_bx.php' );
require_once( 'booking_widget.php' );

define( 'PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
wp_cache_flush();

add_action( 'init', 'do_output_buffer' );
function do_output_buffer()
{
    ob_start();
}
if ( is_admin() ) {
    new BFIGitHubPluginUpdater( __FILE__, 'dunskii', "BookingX" );
}
/**
 * Generate Rewrite Rule for Booking Page
 */
function bookingx_url_rewrite_tag()
{
    
    add_rewrite_tag( '%bookingx_type%', '([^&]+)' );
    add_rewrite_tag( '%bookingx_type_id%', '([^&]+)' );
}
add_action( 'init', 'bookingx_url_rewrite_tag', 10, 0 );

function bookingx_url_rewrite_rule()
{
    flush_rewrite_rules();
    $bkx_set_booking_page = get_option( 'bkx_set_booking_page' );
    if ( isset( $bkx_set_booking_page ) && $bkx_set_booking_page != '' ) {
        $page_obj  = get_post( $bkx_set_booking_page );
        $page_slug = $page_obj->post_name;
    }
    add_rewrite_rule( '^' . $page_slug . '/([^/]+)/([^/]+)/?', 'index.php?page_id=' . $bkx_set_booking_page . '&bookingx_type=$matches[1]&bookingx_type_id=$matches[2]', 'top' );
}
add_action( 'init', 'bookingx_url_rewrite_rule', 10, 0 );

add_action( 'init', 'register_bookingx_post_status' );
/**
 * Register our custom post statuses, used for order status.
 */
function register_bookingx_post_status()
{
    
    $order_statuses = apply_filters( 'bookingx_register_bkx_booking_post_statuses', array(
         'bkx-pending' => array(
             'label' => _x( 'Pending Booking', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Pending Booking <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'bookingx' ) 
        ),
        'bkx-processing' => array(
             'label' => _x( 'Processing', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'bookingx' ) 
        ),
        'bkx-on-hold' => array(
             'label' => _x( 'On Hold', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'bookingx' ) 
        ),
        'bkx-completed' => array(
             'label' => _x( 'Completed', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'bookingx' ) 
        ),
        'bkx-cancelled' => array(
             'label' => _x( 'Cancelled', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'bookingx' ) 
        ),
        'bkx-refunded' => array(
             'label' => _x( 'Refunded', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'bookingx' ) 
        ),
        'bkx-failed' => array(
             'label' => _x( 'Failed', 'Order status', 'bookingx' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'bookingx' ) 
        ) 
    ) );
    
    foreach ( $order_statuses as $order_status => $values ) {
        register_post_status( $order_status, $values );
    }
}

add_filter( 'manage_bkx_booking_posts_columns', 'bkx_booking_columns' );

function bkx_booking_columns( $existing_columns )
{
    
    $seat_alias     = crud_option_multisite( 'bkx_alias_seat' );
    $base_alias     = crud_option_multisite( 'bkx_alias_base' );
    $addition_alias = crud_option_multisite( 'bkx_alias_addition' );
    
    $columns                    = array();
    $columns[ 'cb' ]            = $existing_columns[ 'cb' ];
    $columns[ 'order_status' ]  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'bookingx' ) . '">' . esc_attr__( 'Status', 'bookingx' ) . '</span>';
    $columns[ 'booking_date' ]  = __( 'Booking Date', 'bookingx' );
    $columns[ 'booking_time' ]  = __( '  Booking Time', 'bookingx' );
    $columns[ 'client_name' ]   = __( 'Client Name', 'bookingx' );
    $columns[ 'client_phone' ]  = __( 'Client Phone', 'bookingx' );
    $columns[ 'seat' ]          = __( $seat_alias, 'bookingx' );
    $columns[ 'service' ]       = __( $base_alias, 'bookingx' );
    $columns[ 'extra' ]         = __( '# of ' . $addition_alias, 'bookingx' );
    $columns[ 'order_total' ]   = __( 'Total', 'bookingx' );
    $columns[ 'order_actions' ] = __( 'Actions', 'bookingx' );
    
    return $columns;
}

add_action( 'manage_bkx_booking_posts_custom_column', 'render_bkx_booking_columns' );
function render_bkx_booking_columns( $column )
{
    global $post;
    $orderObj   = new BkxBooking();
    $order_meta = $orderObj->get_order_meta_data( $post->ID );
    //print_r($order_meta);
    switch ( $column ) {
        case 'order_status':
            $order_status = $orderObj->get_order_status( $post->ID );
            echo sprintf( __( '%s', 'bookingx' ), $order_status );
            break;
        case 'booking_date':
            $date_format = get_option( 'date_format' );
            echo sprintf( __( '%s', 'bookingx' ), date( $date_format, strtotime( $order_meta[ 'booking_date' ] ) ) );
            break;
        case 'booking_time':
            echo sprintf( __( '%s - %s', 'bookingx' ), date( 'h:i A', strtotime( $order_meta[ 'booking_start_date' ] ) ), date( 'h:i A ', strtotime( $order_meta[ 'booking_end_date' ] ) ) );
            break;
        case 'client_name':
            echo sprintf( __( '%s %s', 'bookingx' ), $order_meta[ 'first_name' ], $order_meta[ 'last_name' ] );
            break;
        case 'client_phone':
            $client_phone = $order_meta[ 'phone' ];
            echo sprintf( __( '%d', 'bookingx' ), $client_phone );
            break;
        case 'seat':
            $seat_arr = $order_meta[ 'seat_arr' ];
            printf( __( '<ul><li><a href="%s" title="%s">%s</a></li><ul>', 'bookingx' ), esc_url( $seat_arr[ 'permalink' ] ), $seat_arr[ 'title' ], $seat_arr[ 'title' ] );
            break;
        case 'service':
            $base_arr = $order_meta[ 'base_arr' ];
            printf( __( '<ul><li><a href="%s" title="%s">%s</a></li><ul>', 'bookingx' ), esc_url( $base_arr[ 'permalink' ] ), $base_arr[ 'title' ], $base_arr[ 'title' ] );
            break;
        case 'extra':
            $extra_arr = $order_meta[ 'extra_arr' ];
            if ( !empty( $extra_arr ) ) {
                $extra_data = '<ul>';
                foreach ( $extra_arr as $key => $value ) {
                    $extra_data .= sprintf( __( '<li><a href="%s" title="%s">%s</a></li>', 'bookingx' ), esc_url( $value[ 'permalink' ] ), $value[ 'title' ], $value[ 'title' ] );
                }
                $extra_data .= '</ul>';
                echo $extra_data;
            }
            break;
        case 'order_total':
            $currency    = isset( $order_meta[ 'currency' ] ) && $order_meta[ 'currency' ] != '' ? $order_meta[ 'currency' ] : get_current_currency();
            $total_price = $order_meta[ 'total_price' ];
            echo sprintf( __( '<a href="#" title="%s(%s)">%s%d</a>', 'bookingx' ), get_option( 'currency_option' ), $currency, $currency, $total_price );
            break;
        case 'order_actions':
            break;
    }
}

add_action( 'admin_footer', 'bkx_booking_bulk_admin_footer', 10 );
function bkx_booking_bulk_admin_footer()
{
    global $post_type;
    
    if ( 'bkx_booking' == $post_type ) {
?>
     <script type="text/javascript">
      jQuery(function() {
        jQuery('<option>').val('mark_processing').text('<?php
        _e( 'Mark processing', 'woocommerce' );
?>').appendTo('select[name="action"]');
        jQuery('<option>').val('mark_processing').text('<?php
        _e( 'Mark processing', 'woocommerce' );
?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_on-hold').text('<?php
        _e( 'Mark on-hold', 'woocommerce' );
?>').appendTo('select[name="action"]');
        jQuery('<option>').val('mark_on-hold').text('<?php
        _e( 'Mark on-hold', 'woocommerce' );
?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_completed').text('<?php
        _e( 'Mark complete', 'woocommerce' );
?>').appendTo('select[name="action"]');
        jQuery('<option>').val('mark_completed').text('<?php
        _e( 'Mark complete', 'woocommerce' );
?>').appendTo('select[name="action2"]');
      });
      </script>
      <?php
    }
}

// Disable post type view mode options
add_filter( 'view_mode_post_types', 'disable_view_mode_options' );
function disable_view_mode_options( $post_types )
{
    unset( $post_types[ 'bkx_seat' ], $post_types[ 'bkx_addition' ], $post_types[ 'bkx_booking' ], $post_types[ 'bkx_base' ] );
    return $post_types;
    
}
add_action( 'load-edit.php', 'bkx_bulk_action' );
function bkx_bulk_action()
{
    $action         = $_REQUEST[ 'action' ];
    $order_statuses = bkx_get_order_statuses();
    $new_status     = substr( $action, 5 ); // get the status name from action
    $report_action  = 'marked_' . $new_status;
    
    if ( !isset( $order_statuses[ 'bkx-' . $new_status ] ) ) {
        return;
    }
    
    $changed = 0;
    
    $post_ids = array_map( 'absint', (array) $_REQUEST[ 'post' ] );
    
    foreach ( $post_ids as $post_id ) {
        $order = new BkxBooking( '', $post_id );
        $order->update_status( $new_status );
        do_action( 'bkx_order_edit_status', $post_id, $new_status );
        $changed++;
    }
    
    $sendback = add_query_arg( array(
         'post_type' => 'bkx_booking',
        $report_action => true,
        'changed' => $changed,
        'ids' => join( ',', $post_ids ) 
    ), '' );
    
    if ( isset( $_GET[ 'post_status' ] ) ) {
        $sendback = add_query_arg( 'post_status', sanitize_text_field( $_GET[ 'post_status' ] ), $sendback );
    }
    wp_redirect( esc_url_raw( $sendback ) );
    exit();
}


function bkx_clean( $var )
{
    if ( is_array( $var ) ) {
        return array_map( 'bkx_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

add_filter( 'comments_clauses', 'bkx_exclude_order_comments', 10, 1 );
function bkx_exclude_order_comments( $clauses )
{
    global $wpdb;
    $type_array = array(
         'bkx_seat',
        'bkx_base',
        'bkx_addition',
        'bkx_booking' 
    );
    
    if ( !$clauses[ 'join' ] ) {
        $clauses[ 'join' ] = '';
    }
    
    if ( !stristr( $clauses[ 'join' ], "JOIN $wpdb->posts ON" ) ) {
        $clauses[ 'join' ] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
    }
    
    if ( $clauses[ 'where' ] ) {
        $clauses[ 'where' ] .= ' AND ';
    }
    
    $clauses[ 'where' ] .= " $wpdb->posts.post_type NOT IN ('" . implode( "','", $type_array ) . "') ";
    
    return $clauses;
}

// Count comments
add_filter( 'wp_count_comments', 'bkx_count_comments', 10, 2 );
function bkx_count_comments( $stats, $post_id )
{
    global $wpdb;
    
    //delete_transient( 'wc_count_comments' );
    if ( 0 === $post_id ) {
        
        $stats = get_transient( 'wc_count_comments' );
        
        if ( !$stats ) {
            $stats = array();
            
            $count    = $wpdb->get_results( "SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} WHERE comment_type != 'booking_note' GROUP BY comment_approved", ARRAY_A );
            //print_r($wpdb->last_query);
            //die;
            $total    = 0;
            $approved = array(
                 '0' => 'moderated',
                '1' => 'approved',
                'spam' => 'spam',
                'trash' => 'trash',
                'post-trashed' => 'post-trashed' 
            );
            
            foreach ( (array) $count as $row ) {
                // Don't count post-trashed toward totals
                if ( 'post-trashed' != $row[ 'comment_approved' ] && 'trash' != $row[ 'comment_approved' ] ) {
                    $total += $row[ 'num_comments' ];
                }
                if ( isset( $approved[ $row[ 'comment_approved' ] ] ) ) {
                    $stats[ $approved[ $row[ 'comment_approved' ] ] ] = $row[ 'num_comments' ];
                }
            }
            
            $stats[ 'total_comments' ] = $total;
            $stats[ 'all' ]            = $total;
            foreach ( $approved as $key ) {
                if ( empty( $stats[ $key ] ) ) {
                    $stats[ $key ] = 0;
                }
            }
            
            $stats = (object) $stats;
            set_transient( 'wc_count_comments', $stats );
        }
    }
    
    return $stats;
    
}




add_action( 'parse_query', 'bkx_booking_search_custom_fields' );

function bkx_booking_search_custom_fields( $wp )
{
    global $pagenow;
    
    if ( 'edit.php' != $pagenow || empty( $wp->query_vars[ 's' ] ) || $wp->query_vars[ 'post_type' ] != 'bkx_booking' ) {
        return;
    }
    $order    = new BkxBooking();
    $post_ids = $order->order_search( $_GET[ 's' ] );
    
    if ( !empty( $post_ids ) ) {
        // Remove "s" - we don't want to search order name.
        unset( $wp->query_vars[ 's' ] );
        
        // so we know we're doing this.
        $wp->query_vars[ 'bkx_booking_search' ] = true;
        
        // Search by found posts.
        $wp->query_vars[ 'post__in' ] = array_merge( $post_ids, array(
             0 
        ) );
    }
    
}

add_filter( 'bulk_actions-edit-bkx_booking', 'bkx_booking_bulk_actions' );
function bkx_booking_bulk_actions( $actions )
{
    
    if ( isset( $actions[ 'edit' ] ) ) {
        unset( $actions[ 'edit' ] );
    }
    return $actions;
}

add_filter( 'post_row_actions', 'bkx_row_actions', 2, 100 );
function bkx_row_actions( $actions, $post )
{
    if ( in_array( $post->post_type, array(
         'bkx_booking' 
    ) ) ) {
        if ( isset( $actions[ 'inline hide-if-no-js' ] ) ) {
            unset( $actions[ 'inline hide-if-no-js' ] );
        }
    }
    return $actions;
}

function bkx_get_order_statuses()
{
    $order_statuses = array(
         'bkx-pending' => _x( 'Pending Booking', 'Order status', 'bookingx' ),
        'bkx-processing' => _x( 'Processing', 'Order status', 'bookingx' ),
        'bkx-on-hold' => _x( 'On Hold', 'Order status', 'bookingx' ),
        'bkx-completed' => _x( 'Completed', 'Order status', 'bookingx' ),
        'bkx-cancelled' => _x( 'Cancelled', 'Order status', 'bookingx' ),
        'bkx-refunded' => _x( 'Refunded', 'Order status', 'bookingx' ),
        'bkx-failed' => _x( 'Failed', 'Order status', 'bookingx' ) 
    );
    return apply_filters( 'bkx_order_statuses', $order_statuses );
}

add_filter( 'request', 'custom_request_query' );
function custom_request_query( $vars )
{
    // Status
    if ( !isset( $vars[ 'post_status' ] ) && $vars[ 'post_type' ] == 'bkx_booking' ) {
        $post_statuses         = bkx_get_order_statuses();
        $vars[ 'post_status' ] = array_keys( $post_statuses );
    }
    
    return $vars;
}
/**
 *function to check the users role and permission 
 *@access public
 *@param 
 *@return String $capability of the current user
 */
function get_capability()
{
    $siteuser_edit_option = get_option( 'bkx_siteuser_canedit_seat', "default" );
    // $siteuser_edit_option = "Y";
    if ( $siteuser_edit_option == "Y" ) {
        $capability = "read";
    } else {
        $capability = "manage_options";
    }
    //echo $capability;
    return $capability;
}
/**
 *function to list seat pages
 *@access public
 *@param 
 *@return void
 */
function list_seats()
{
    $capability = get_capability();
    
    // Check if current user is an admin
    if ( !current_user_can( $capability ) ) {
        //wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    include( 'list_seats.php' );
}
/**
 *function add seat form
 *@access public
 *@param 
 *@return void
 */

function add_seat()
{
    $capability = get_capability();
    // Check if current user is an admin
    if ( !current_user_can( $capability ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include( 'add_seat.php' );
}

add_action( 'wp_ajax_get_user', 'get_user' );

function get_user()
{
    global $wpdb; // this is how you get access to the database
    $role       = $_POST[ 'data' ];
    $user_query = new WP_User_Query( array(
         'role' => $role 
    ) );
    
    echo json_encode( $user_query->results );
    
    die(); // this is required to return a proper result
}


/**
 *function to display notifications
 *@access public
 *@param 
 *@return void
 */
function notifications()
{
    // Check if current user is an admin
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include( 'notifications.php' );
}
/**
 *function for settings page
 *@access public
 *@param 
 *@return void
 */
function bkx_setting_page_callback()
{
    // Check if current user is an admin
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include( 'settings.php' );
}

/**
 *function to generate seats list
 *@access public
 *@param 
 *@return void
 */
function generate_seats_list()
{
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    $value  = array(
         'one' => 'one',
        'two' => 'two' 
    );
    $output = json_encode( $value );
    return $output;
}

############## REGISTER THE SHORTCODE #########################

function register_pp_shortcodes()
{
    set_time_limit( 0 );
}
/**
 *function to register admin actions
 *@access public
 *@param 
 *@return void
 */
function booking_admin_actions()
{
    //add_menu_page('Booking', 'Booking', 'manage_options', 'yfbiz-booking', 'list_seats');    
    $seat_alias         = get_option( 'bkx_alias_seat', 'Resource' );
    $base_alias         = get_option( 'bkx_alias_base', 'Service,' );
    $addition_alias     = get_option( 'bkx_alias_addition', 'Extra' );
    $notification_alias = get_option( 'bkx_alias_notification', 'Notification' );
    $capability         = get_capability();
    
    add_submenu_page( 'edit.php?post_type=bkx_booking', __( 'Settings', 'bookingx' ), __( 'Settings', 'bookingx' ), 'manage_options', 'bkx-setting', 'bkx_setting_page_callback' );
    add_menu_page( 'Booking', 'Booking', 'read', 'yfbiz-booking', 'dashboard' );
    add_submenu_page( 'yfbiz-booking', 'List ' . $seat_alias . 's', 'List ' . $seat_alias . 's', 'manage_options', 'list_seat', 'list_seats' );
    add_submenu_page( 'yfbiz-booking', 'List ' . $base_alias . 's', 'List ' . $base_alias . 's', 'manage_options', 'list-base', 'list_bases' );
    add_submenu_page( 'yfbiz-booking', 'List ' . $addition_alias . 's', 'List ' . $addition_alias . 's', 'manage_options', 'list-addition', 'list_additions' );
    add_submenu_page( 'yfbiz-booking', 'Add ' . $seat_alias, 'Add ' . $seat_alias, 'manage_options', 'add-seat', 'add_seat' );
    add_submenu_page( 'yfbiz-booking', 'Add ' . $base_alias, 'Add ' . $base_alias, 'manage_options', 'add-base', 'add_base' );
    add_submenu_page( 'yfbiz-booking', 'Add ' . $addition_alias, 'Add ' . $addition_alias, 'manage_options', 'add-addition', 'add_addition' );
    //add_submenu_page( 'yfbiz-booking', 'Settings', 'Settings', 'manage_options', 'settings', 'settings');
}


/**
 *function to create all tables in database while installation of plugin
 *@access public
 *@param 
 *@return void
 */
function create_booking_tables()
{
    /*@ruchira 
    While implementation of image upload to  directory.
    make the plugin directory writable by system before installation of plugin    
    */
    $plugin_dir = plugin_dir_path( __FILE__ );
    if ( !is_writable( $plugin_dir ) ) {
        echo $plugin_dir . " is not writable, make it writable";
        die();
    }
    
    add_option( 'bkx_alias_seat', 'Seat', '', 'yes' );
    add_option( 'bkx_alias_base', 'Base', '', 'yes' );
    add_option( 'bkx_alias_addition', 'Addition', '', 'yes' );
    add_option( 'bkx_alias_notification', 'Notification', '', 'yes' );
    
    add_option( "bkx_siteclient_css_text_color", '40120a', '', 'yes' );
    add_option( "bkx_siteclient_css_background_color", 'f0e8e7', '', 'yes' );
    add_option( "bkx_siteclient_css_border_color", '1f191f', '', 'yes' );
    add_option( "bkx_siteclient_css_progressbar_color", '875428', '', 'yes' );
    
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    /* do nothing */
    
    flush_rewrite_rules();
    
    //add_action('init', 'register_pp_shortcodes');
    //start create  a new page while activating this plugin
    $the_page_title = 'Your Booking Form Here';
    $the_page_name  = 'your-booking-form-here';
    
    // the menu entry...
    delete_option( "my_plugin_page_title" );
    add_option( "my_plugin_page_title", $the_page_title, '', 'yes' );
    // the slug...
    delete_option( "my_plugin_page_name" );
    add_option( "my_plugin_page_name", $the_page_name, '', 'yes' );
    // the id...
    delete_option( "my_plugin_page_id" );
    add_option( "my_plugin_page_id", '0', '', 'yes' );
    
    $the_page         = get_page_by_title( $the_page_title );
    $the_login_page   = get_page_by_title( "Login Here" );
    $the_account_page = get_page_by_title( "My Account" );
    
    if ( !$the_page ):
    // Create post object
        $_p                     = array();
        $_p[ 'post_title' ]     = $the_page_title;
        $_p[ 'post_content' ]   = "[bookingform]";
        $_p[ 'post_status' ]    = 'private';
        $_p[ 'post_type' ]      = 'page';
        $_p[ 'comment_status' ] = 'closed';
        $_p[ 'ping_status' ]    = 'closed';
        $_p[ 'post_category' ]  = array(
             1 
        ); // the default 'Uncatrgorised'
        
    // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
    else:
    // the plugin may have been previously active and the page may just be trashed...
        $the_page_id           = $the_page->ID;
        $bkx_set_booking_page  = update_option( 'bkx_set_booking_page', $the_page_id );
    //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id           = wp_update_post( $the_page );
    endif;
    
    
    /**Added By : Divyang Parekh
     * Date : 3-11-2015
     * For : Create Page For Login and Customer Booking List
     */
    if ( !$the_login_page ):
        $_login                     = array();
        $_login[ 'post_title' ]     = 'Login Here';
        $_login[ 'post_content' ]   = "[login_customer_bx]";
        $_login[ 'post_status' ]    = 'Publish';
        $_login[ 'public' ]         = true;
        $_login[ 'post_type' ]      = 'page';
        $_login[ 'comment_status' ] = 'closed';
        $_login[ 'ping_status' ]    = 'closed';
        $_login[ 'post_category' ]  = array(
             1 
        ); // the default 'Uncatrgorised'
        
    // Insert the post into the database
        $_login_page_id = wp_insert_post( $_login );
    endif;
    if ( !$the_account_page ):
        $_my_account                     = array();
        $_my_account[ 'post_title' ]     = 'My Account';
        $_my_account[ 'post_content' ]   = "[my_account_bx]";
        $_my_account[ 'post_status' ]    = 'Publish';
        $_my_account[ 'public' ]         = true;
        $_my_account[ 'post_type' ]      = 'page';
        $_my_account[ 'comment_status' ] = 'closed';
        $_my_account[ 'ping_status' ]    = 'closed';
        $_my_account[ 'post_category' ]  = array(
             1 
        ); // the default 'Uncatrgorised'
        
    // Insert the post into the database
        $_my_account_page_id = wp_insert_post( $_my_account );
    endif;
    
    delete_option( 'my_plugin_page_id' );
    add_option( 'my_plugin_page_id', $the_page_id );
    $permalink = get_permalink( $the_page_id );
}

//to display display notices to admin users to drag booking widget to the area of yours choice
add_action( 'admin_notices', 'my_admin_notice' );
/**
 *function to display notification in admin area after installation of plugin
 *@access public
 *@param 
 *@return void
 */
function my_admin_notice()
{
    global $pagenow;
    $flag_widget = is_active_widget( false, false, 'booking_widget' );
    if ( !$flag_widget ) {
        echo '<div class="updated">
             <p>Drag Booking Widget to activate booking form for your site users.</p>
         </div>';
    }
}
/**
 *function to drop tables while uninstallation of plugins
 *@access public
 *@param 
 *@return void
 */
function remove_booking_tables()
{
    flush_rewrite_rules();
}

add_action( 'admin_menu', 'booking_admin_actions' );
add_action( 'widgets_init', create_function( '', 'register_widget( "booking_widget" );' ) );
register_activation_hook( __FILE__, 'create_booking_tables' );
//register_deactivation_hook( __FILE__, 'remove_booking_tables' );

add_action( 'wp_ajax_check_seat_exixts', 'prefix_ajax_check_seat_exixts' );
add_action( 'wp_ajax_nopriv_check_seat_exixts', 'prefix_ajax_check_seat_exixts' );
function prefix_ajax_check_seat_exixts()
{
    $current_blog_id = get_current_blog_id();
    switch_to_blog( $current_blog_id );
    
    $seatEmail = $_POST[ 'email' ];
    $user_id   = username_exists( $seatEmail );
    if ( !$user_id && email_exists( $seatEmail ) == false ) {
        $data = 'success';
    } else {
        $data = 'error';
    }
    echo $data;
    die();
}

/**
 * Create Seat Post Type
 */
add_action( 'init', 'bkx_create_seat_post_type' );
function bkx_create_seat_post_type()
{
    $alias_seat = get_option( 'bkx_alias_seat', "Resources" );
    $alias_seat = isset( $alias_seat ) && $alias_seat != '' ? $alias_seat : "Resources";
    // Set UI labels for Custom Post Type
    $labels     = array(
         'name' => _x( $alias_seat, 'Post Type General Name', 'bookingx' ),
        'singular_name' => _x( $alias_seat, 'Post Type Singular Name', 'bookingx' ),
        'menu_name' => __( $alias_seat, 'bookingx' ),
        'all_items' => __( "All $alias_seat", 'bookingx' ),
        'view_item' => __( "View $alias_seat", 'bookingx' ),
        'add_new_item' => __( "Add New Resource", 'bookingx' ),
        'add_new' => __( "Add New Resource", 'bookingx' ),
        'edit_item' => __( "Edit Resource", 'bookingx' ),
        'update_item' => __( "Update $alias_seat", 'bookingx' ),
        'search_items' => __( "Search $alias_seat", 'bookingx' ),
        'not_found' => __( 'Not Found', 'bookingx' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'bookingx' ) 
    );
    
    register_post_type( 'bkx_seat', array(
         'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array(
             'title',
            'editor',
            'excerpt',
            'thumbnail' 
        ),
        'show_in_menu' => 'edit.php?post_type=bkx_booking' 
    ) );
}

/**
 * Create Base Post Type
 */

add_action( 'init', 'bkx_create_base_post_type' );
function bkx_create_base_post_type()
{
    $alias_base = get_option( 'bkx_alias_base', "Services" );
    $alias_base = isset( $alias_base ) && $alias_base != '' ? $alias_base : "Services";
    // Set UI labels for Custom Post Type
    $labels     = array(
         'name' => _x( $alias_base, 'Post Type General Name', 'bookingx' ),
        'singular_name' => _x( $alias_base, 'Post Type Singular Name', 'bookingx' ),
        'menu_name' => __( $alias_base, 'bookingx' ),
        'all_items' => __( "All $alias_base", 'bookingx' ),
        'view_item' => __( "View $alias_base", 'bookingx' ),
        'add_new_item' => __( "Add New Service", 'bookingx' ),
        'add_new' => __( "Add New Service", 'bookingx' ),
        'edit_item' => __( "Edit Service", 'bookingx' ),
        'update_item' => __( "Update Service", 'bookingx' ),
        'search_items' => __( "Search $alias_base", 'bookingx' ),
        'not_found' => __( 'Not Found', 'bookingx' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'bookingx' ) 
    );
    register_post_type( 'bkx_base', array(
         'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array(
             'title',
            'editor',
            'excerpt',
            'thumbnail' 
        ),
        'show_in_menu' => 'edit.php?post_type=bkx_booking' 
    ) );
}

/**
 * Create Base Post Type
 */

add_action( 'init', 'bkx_create_addition_post_type' );
function bkx_create_addition_post_type()
{
    $alias_addition = get_option( 'bkx_alias_addition', "Extras" );
    $alias_addition = isset( $alias_addition ) && $alias_addition != '' ? $alias_addition : "Extras";
    $labels         = array(
         'name' => _x( $alias_addition, 'Post Type General Name', 'bookingx' ),
        'singular_name' => _x( $alias_addition, 'Post Type Singular Name', 'bookingx' ),
        'menu_name' => __( $alias_addition, 'bookingx' ),
        'all_items' => __( "All $alias_addition", 'bookingx' ),
        'view_item' => __( "View $alias_addition", 'bookingx' ),
        'add_new_item' => __( "Add New Extra", 'bookingx' ),
        'add_new' => __( "Add New Extra", 'bookingx' ),
        'edit_item' => __( "Edit Extra", 'bookingx' ),
        'update_item' => __( "Update Extra", 'bookingx' ),
        'search_items' => __( "Search $alias_addition", 'bookingx' ),
        'not_found' => __( 'Not Found', 'bookingx' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'bookingx' ) 
    );
    register_post_type( 'bkx_addition', array(
         'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array(
             'title',
            'editor',
            'excerpt',
            'thumbnail' 
        ),
        'show_in_menu' => 'edit.php?post_type=bkx_booking' 
    ) );
}

/**
 * Create Booking Post Type
 */

add_action( 'init', 'bkx_create_booking_post_type' );
function bkx_create_booking_post_type()
{
    
    $labels = array(
         'name' => _x( 'Booking', 'Post Type General Name', 'bookingx' ),
        'singular_name' => _x( 'Booking', 'Post Type Singular Name', 'bookingx' ),
        'menu_name' => __( 'BookingX', 'bookingx' ),
        'all_items' => __( "All Booking", 'bookingx' ),
        'view_item' => __( "View Booking", 'bookingx' ),
        'add_new_item' => __( "Add New Booking", 'bookingx' ),
        'add_new' => __( "Add New Booking", 'bookingx' ),
        'edit_item' => __( "Edit Booking", 'bookingx' ),
        'update_item' => __( "Update Booking", 'bookingx' ),
        'search_items' => __( "Search Booking", 'bookingx' ),
        'not_found' => __( 'Not Found Booking', 'bookingx' ),
        'not_found_in_trash' => __( 'Not found Booking in Trash', 'bookingx' ) 
    );
    
    register_post_type( 'bkx_booking', array(
         'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_position' => 25,
        'capabilities' => array(
        // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
            ) 
    ) );
}
//Set default role for  $alias_seat and give permission.
add_role( 'resource', __( 'Resource' ), array(
     'read' => true,
    'edit_posts' => true,
    'delete_posts' => false 
) );
require_once 'inc/metabox.php';

function my_scripts_method()
{
    $base_alias = get_option( "bkx_alias_base", "Base" );
    
    wp_enqueue_script( "jquery-1-9-1-min", plugins_url( "js/jquery-1.9.1.min.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-ui", plugins_url( "js/jquery-ui.js", __FILE__ ), false, null, true );
    
    wp_enqueue_script( "jquery-fullcalendar-min", plugins_url( "js/fullcalendar/fullcalendar.min.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-once", plugins_url( "js/jquery.once.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-timepicker", plugins_url( "js/jquery.timePicker.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "common_script", plugins_url( "js/common.js", __FILE__ ), false, null, true );
    $translation_array = array(
         'plugin_url' => plugins_url( '', __FILE__ ),
        'base' => $base_alias 
    );
    
    wp_localize_script( 'common_script', 'url_obj', $translation_array );
    
    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'fullcalendar-css', plugins_url( 'css/fullcalendar.css', __FILE__ ), false, false );
    wp_enqueue_style( 'fullcalendar-print-css', plugins_url( 'css/fullcalendar.print.css', __FILE__ ), false, false );
    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'jquery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ), false, false );
}
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );


function load_custom_wp_admin_style()
{
    $base_alias = get_option( "bkx_alias_base", "Base" );
    
    wp_enqueue_script( "jquery-1-9-1-min", plugins_url( "js/jquery-1.9.1.min.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-ui", plugins_url( "js/jquery-ui.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-fullcalendar-min", plugins_url( "js/fullcalendar/fullcalendar.min.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-once", plugins_url( "js/jquery.once.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-timepicker", plugins_url( "js/jquery.timePicker.js", __FILE__ ), false, null, true );
    
    wp_enqueue_script( "common_script", plugins_url( "admin/js/common.js", __FILE__ ), false, null, true );
    $edit_order_data_array = array(
         'order_id' => '',
        'seat_id' => '',
        'base_id' => '',
        'extra_id' => 0,
        'action' => 'add' 
    );
    $translation_array     = array(
         'plugin_url' => plugins_url( '', __FILE__ ),
        'base' => $base_alias 
    );
    wp_localize_script( 'common_script', 'url_obj', $translation_array );
    wp_localize_script( 'common_script', 'edit_order_data', $edit_order_data_array );
    
    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'fullcalendar-css', plugins_url( 'css/fullcalendar.css', __FILE__ ), false, false );
    wp_enqueue_style( 'fullcalendar-print-css', plugins_url( 'css/fullcalendar.print.css', __FILE__ ), false, false );
    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'jquery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ), false, false );
    
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );


// Add settings link on plugin page
function plugin_settings_link( $links )
{
    $settings_link = '<a href="' . site_url() . '/wp-admin/admin.php?page=settings">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

$plugin = plugin_basename( __FILE__ );
//echo plugin_dir_url( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_settings_link' );


function manage_google_calendar()
{
    require_once plugin_dir_path( __FILE__ ) . 'google-api-php-client/src/Google_Client.php';
    require_once plugin_dir_path( __FILE__ ) . 'google-api-php-client/src/contrib/Google_CalendarService.php';
    // session_start();
    
    $client = new Google_Client();
    $client->setApplicationName( "Google Calendar PHP Starter Application" );
    
    $client->setClientId( '935351891856-0ru57ev8q13ugp3v73co8kf6inun34kp.apps.googleusercontent.com' );
    $client->setClientSecret( '8ZbDiWPCAlrDFGGC4TSIU_SX' );
    $client->setRedirectUri( 'http://booking.php-dev.in/wp-content/plugins/yfbizbooking/google-api-php-client/examples/calendar/simple.php' );
    //$client->setDeveloperKey('AIzaSyAbZ5lr4XKSYPGGPGHvjByfLg8u5JnDRxE');
    
    $cal = new Google_CalendarService( $client );
    if ( isset( $_GET[ 'logout' ] ) ) {
        unset( $_SESSION[ 'token' ] );
    }
    
    if ( isset( $_GET[ 'code' ] ) ) {
        $client->authenticate( $_GET[ 'code' ] );
        $_SESSION[ 'token' ] = $client->getAccessToken();
        header( 'Location: http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] );
    }
    
    if ( isset( $_SESSION[ 'token' ] ) ) {
        $client->setAccessToken( $_SESSION[ 'token' ] );
    }
    
    if ( $client->getAccessToken() ) {
        $calList = $cal->calendarList->listCalendarList();
        print "<h1>Calendar List</h1><pre>" . print_r( $calList, true ) . "</pre>";
        
        $event = new Google_Event();
        $event->setSummary( 'Appointment' );
        $event->setLocation( 'Somewhere' );
        $start = new Google_EventDateTime();
        $start->setDateTime( '2013-06-25T16:44:37+05:30' );
        $event->setStart( $start );
        $end = new Google_EventDateTime();
        $end->setDateTime( '2013-06-26T16:44:37+05:30' );
        $event->setEnd( $end );
        
        $event->attendees = $attendees;
        $createdEvent     = $cal->events->insert( 'primary', $event );
        echo "Event Created";
        print_r( $createdEvent );
        echo $createdEvent->getId();
        
        
        $_SESSION[ 'token' ] = $client->getAccessToken();
    } else {
        $authUrl = $client->createAuthUrl();
        print "<a class='login' href='$authUrl'>Connect Me!</a>";
    }
}
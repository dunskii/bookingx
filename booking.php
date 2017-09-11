<?php
/* *
 *  Plugin Name: BookingX
 *  Plugin URI: 
 *  Description: BookingX is a booking and appointments plugin for WordPress
 *  Author: Dunskii Web Services 
 *  Version: 0.5  
 *  Author URI: http://dunskii.com
 * 
 *  Text Domain: bookingx
 *  Domain Path: /i18n/languages/
 */

session_start();
//define( 'WP_MEMORY_LIMIT', '512M' );
//session_start();
//include shord code file

require_once( 'inc/call_ajax_functions.php' );
require_once( 'admin/settings/setting-functions.php' );
require_once( 'inc/filter_and_actions_functions.php' );
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
require_once( 'inc/class-shortcode-editor.php' );
require_once( 'inc/template-hooks.php' );
require_once( 'inc/template-functions.php' );
require_once( 'inc/multisite-functions.php' );
require_once( 'inc/class-order-meta-box.php' );
require_once( 'inc/class-bkx-export.php' );
require_once( 'inc/class-bkx-import.php' );
require_once( 'admin/booking-listing.php' );
require_once( 'git-updater/BFIGitHubPluginUploader.php' );

require_once( 'my_account_bx.php' );
require_once( 'my_login_bx.php' );
require_once( 'booking_widget.php' );

define( 'PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
wp_cache_flush();

add_action( 'init', 'do_output_buffer' );
function do_output_buffer(){
    ob_start(); 
}
if ( is_admin() ) {
    new BFIGitHubPluginUpdater( __FILE__, 'dunskii', "BookingX" );
}

function bkx_clean( $var )
{
    if ( is_array( $var ) ) {
        return array_map( 'bkx_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

function bkx_get_order_statuses()
{
    $order_statuses = array(
        'bkx-pending' => _x( 'Pending', 'Order status', 'bookingx' ),
        'bkx-ack' => _x( 'Acknowledged', 'Order status', 'bookingx' ),
        'bkx-completed' => _x( 'Completed', 'Order status', 'bookingx' ),
        'bkx-missed' => _x( 'Missed', 'Order status', 'bookingx' ),
        'bkx-cancelled' => _x( 'Cancelled', 'Order status', 'bookingx' ),
        'bkx-failed' => _x( 'Failed', 'Order status', 'bookingx' ) 
    );
    return apply_filters( 'bkx_order_statuses', $order_statuses );
}

function bkx_setting_page_callback()
{
    include( 'settings.php' );
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
    add_submenu_page( 'edit.php?post_type=bkx_booking', __( 'Settings', 'bookingx' ), __( 'Settings', 'bookingx' ), 'manage_options', 'bkx-setting', 'bkx_setting_page_callback' );
}
add_action( 'admin_menu', 'booking_admin_actions' );

add_filter('plugin_action_links', 'bkx_plugin_action_links', 10, 2);
function bkx_plugin_action_links( $links, $file ) {
        $links[ ] = '<a href="edit.php?post_type=bkx_booking&page=bkx-setting">' . __( 'Settings', 'bookingx' ) . '</a>';  
    return $links;
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
    // if ( !is_writable( $plugin_dir ) ) {
    //     echo $plugin_dir . " is not writable, make it writable";
    //     die();
    // }

    crud_option_multisite("bkx_alias_seat", 'Resource', 'update');
    crud_option_multisite("bkx_alias_base", 'Service', 'update');
    crud_option_multisite("bkx_alias_addition", 'Extra', 'update');
    crud_option_multisite("bkx_alias_notification", 'Notification', 'update');
    crud_option_multisite("bkx_siteclient_css_text_color", '#40120a', 'update');
    crud_option_multisite("bkx_siteclient_css_background_color", '#f0e8e7', 'update');
    crud_option_multisite("bkx_siteclient_css_border_color", '#1f191f', 'update');
    crud_option_multisite("bkx_siteclient_css_progressbar_color", '#875428', 'update');
    
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    /* do nothing */
    
    flush_rewrite_rules();
    
    // the menu entry...
   // crud_option_multisite("my_plugin_page_title", $the_page_title, 'update');
    // the slug...
  //  crud_option_multisite("my_plugin_page_name", $the_page_name, 'update');

    $the_login_page   = get_page_by_title( "Login Here" );
    $the_account_page = get_page_by_title( "My Account" );

      
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
    
    crud_option_multisite("my_plugin_page_id", $the_page_id, 'update');
}

function wpdocs_dequeue_script() {
  wp_deregister_script('et-shortcodes-js');
}
add_action( 'wp_print_scripts', 'wpdocs_dequeue_script', 100 );


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
add_action( 'widgets_init', create_function( '', 'register_widget( "booking_widget" );' ) );
register_activation_hook( __FILE__, 'create_booking_tables' );
//register_deactivation_hook( __FILE__, 'remove_booking_tables' );


//Set default role for  $alias_seat and give permission.
add_role( 'resource', __( 'Resource' ), 
    array( 'read' => true, 'edit_posts' => true, 'delete_posts' =>false ) );

require_once 'inc/metabox.php';

function my_scripts_method()
{
    $base_alias = get_option( "bkx_alias_base", "Base" );
    
    wp_enqueue_script( "jquery-1-9-1-min", plugins_url( "js/jquery-1.9.1.min.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-ui", plugins_url( "js/jquery-ui.js", __FILE__ ), false, null, true );
   // wp_enqueue_script( "jquery-fullcalendar-min", plugins_url( "js/fullcalendar/fullcalendar.min.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-once", plugins_url( "js/jquery.once.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-timepicker", plugins_url( "js/jquery.timePicker.js", __FILE__ ), false, null, true );

    $booking_edit_process_page_id = crud_option_multisite("booking_edit_process_page_id");
    if(get_the_ID() == $booking_edit_process_page_id){

        $nonce = $_REQUEST['_wpnonce'];
        if(isset($_REQUEST['i'])){ $encrypted_booking_id = $_REQUEST['i'];}

        //Booking Variables
        $decoded_booking_data= base64_decode($encrypted_booking_id);
        $decoded_booking_data_arr=  explode("|",$decoded_booking_data);
        $booking_id = $decoded_booking_data_arr[0];
        $seat_id = $decoded_booking_data_arr[1];
        $base_id = $decoded_booking_data_arr[2];
        //$extra_id = $decoded_booking_data_arr[3];
        $return_page_id= $decoded_booking_data_arr[4];

        $BkxBooking = new BkxBooking();
        $order_meta_data = $BkxBooking->get_order_meta_data($booking_id);
        $base_extended = $order_meta_data['extended_base_time'];

        $extra_id = get_post_meta( $booking_id, 'addition_ids', true );
        $extra_id = rtrim( $extra_id, ",");
        $base_extended = ($base_extended) ? $base_extended : 0 ;

        wp_deregister_script('et-shortcodes-js');

        wp_enqueue_script( "edit_booking_common_script", plugins_url( "js/edit_booking_common_script.js", __FILE__ ), false, rand(1,99999), true );
        $translation_array = array(
                                'plugin_url' => plugins_url( '', __FILE__ ),
                                'base' => $base_alias,
                                'order_id' => $booking_id,
                                'seat_id' => $seat_id,
                                'base_id' => $base_id,
                                'extra_id' =>!empty($extra_id) ? $extra_id : '0',
                                'extended' => $base_extended,
                                'return_page_id' => $return_page_id,
                                'action' => 'edit');
        wp_localize_script( 'edit_booking_common_script', 'edit_booking_obj', $translation_array );

    }else{

        wp_enqueue_script( "common_script", plugins_url( "js/common.js?ver=".rand(1,999999), __FILE__ ), false, rand(1,99999), true );
         $BkxBooking = new BkxBooking();

        $search['search_by'] = 'future';
        $search['search_date'] = date('Y-m-d');

        $booked_days = '';
        $BookedRecords = $BkxBooking->GetBookedRecordsByUser($search);
         
        if(!empty($BookedRecords)){
            foreach ($BookedRecords as  $bookings) : 
                $booking_multi_days = $bookings['booking_multi_days'];
                $booking_multi_days_arr = explode(",", $booking_multi_days );
                if(!empty($booking_multi_days_arr)){
                    $booking_multi_days_range = getDatesFromRange( $booking_multi_days_arr[0], $booking_multi_days_arr[1], "m/d/Y");
                    if(!empty($booking_multi_days_range)){
                        foreach ($booking_multi_days_range as $multi_days) {
                             $booked_days .=  $multi_days.",";
                        }
                    }
                }
                $booking_start_date = date('m/d/Y',strtotime($bookings['booking_start_date']));
                $booked_days .=  $booking_start_date.",";
            endforeach;
            $booked_days = rtrim( $booked_days, ',');
        }else { $booked_days = 0;}
        $booked_days_arr = explode(",", $booked_days);
        $booked_days_filtered = implode(",", array_unique($booked_days_arr) );

        $bkx_biz_vac_sd = crud_option_multisite('bkx_biz_vac_sd');
        $bkx_biz_vac_ed = crud_option_multisite('bkx_biz_vac_ed');

        $bkx_biz_pub_holiday = crud_option_multisite('bkx_biz_pub_holiday');
        if(!empty($bkx_biz_pub_holiday)){
            $bkx_biz_pub_holiday = array_values($bkx_biz_pub_holiday);
            $biz_pub_days = implode(",", $bkx_biz_pub_holiday); 
        }
        $biz_vac_days = getDatesFromRange( $bkx_biz_vac_sd, $bkx_biz_vac_ed, "m/d/y");
        if(!empty($biz_vac_days)){ $biz_vac_days = implode(",", $biz_vac_days); }


        $translation_array = array(
             'plugin_url' => plugins_url( '', __FILE__ ),
             'admin_ajax' => admin_url('admin-ajax.php'),
             'base' => $base_alias,
             'booked_days' => $booked_days_filtered,
             'biz_vac_days' => $biz_vac_days,
             'biz_pub_days' => $biz_pub_days
        );
        wp_localize_script( 'common_script', 'url_obj', $translation_array );

    }

    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    // wp_enqueue_style( 'fullcalendar-css', plugins_url( 'css/fullcalendar.css', __FILE__ ), false, false );
    // wp_enqueue_style( 'fullcalendar-print-css', plugins_url( 'css/fullcalendar.print.css', __FILE__ ), false, false );
    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'jquery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ), false, false );
    wp_enqueue_style( 'bodikea-upgrade', PLUGIN_DIR_URL.'css/bodikea-upgrade.css', '', rand(1,999999) );
}
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );


function load_custom_wp_admin_style()
{
    $screen = get_current_screen();

    $post_type = $screen->post_type;
    $get_allowed = get_allowed();
        $base_alias = get_option( "bkx_alias_base", "Base" );
    if(!empty($get_allowed) && !empty($post_type) && in_array($post_type, $get_allowed))
    {
        wp_enqueue_script( "jquery-ui","https://code.jquery.com/ui/1.12.1/jquery-ui.js", false, null, true );

   // wp_enqueue_script( "jquery-1-9-1-min", plugins_url( "js/jquery-1.9.1.min.js", __FILE__ ), false, null, true );
    //wp_enqueue_script( "jquery-ui", plugins_url( "js/jquery-ui.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-fullcalendar-moment", plugins_url( "js/fullcalendar/lib/moment.min.js", __FILE__ ), false, rand(1,9999999), true );
    wp_enqueue_script( "jquery-fullcalendar-min", plugins_url( "js/fullcalendar/fullcalendar.min.js", __FILE__ ), false, rand(1,9999999), true );

    wp_enqueue_script( "jquery-once", plugins_url( "js/jquery.once.js", __FILE__ ), false, null, true );
    wp_enqueue_script( "jquery-timepicker", '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js', false, null, true );
    
    wp_enqueue_script( "common_script", plugins_url( "admin/js/common.js", __FILE__ ), false, rand(1,9999999), true );

    $edit_order_data_array = array(
         'order_id' => '',
        'seat_id' => '',
        'base_id' => '',
        'extra_id' => 0,
        'action' => 'add',
        'admin_ajax' => admin_url('admin-ajax.php'),
    );
    $translation_array     = array(
         'plugin_url' => plugins_url( '', __FILE__ ),
        'base' => $base_alias,
        'admin_ajax' => admin_url('admin-ajax.php'),
    );
    wp_localize_script( 'common_script', 'url_obj', $translation_array );
    wp_localize_script( 'common_script', 'edit_order_data', $edit_order_data_array );  
    }

    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'fullcalendar-css', plugins_url( 'css/fullcalendar.min.css', __FILE__ ), false, false );
    // wp_enqueue_style( 'fullcalendar-print-css', plugins_url( 'css/fullcalendar.print.min.css', __FILE__ ), false, rand(1,9999999) );
    wp_enqueue_style( 'bookingform-css', plugins_url( 'css/bookingform-style.css', __FILE__ ), false, false );
    wp_enqueue_style( 'jquery-ui-css', plugins_url( 'css/jquery-ui.css', __FILE__ ), false, false );
    
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

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



<?php
/**
 * Booking Listing Page
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} ?>

<?php
    do_action('bkx_dashboard_before');
    if( isset($_REQUEST['view_booking_nonce']) &&
        wp_verify_nonce( $_REQUEST['view_booking_nonce'], 'view_booking_'.$_REQUEST['view-booking'] )) {
        do_action('bkx_dashboard_booking_view');
    }else{
        /**
         * Dashboard Booking Nav Tabs
         */
        do_action('bkx_dashboard_nav');
        /**
         * Dashboard Booking Nav Tabs Content
         */
        do_action( 'bkx_dashboard_content' );
    }
    do_action('bkx_dashboard_after');
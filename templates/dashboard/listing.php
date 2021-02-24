<?php
/**
 * BookingX Booking Listing Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bkx_dashboard_bookings_before' );

if ( isset( $_REQUEST['view_booking_nonce'], $_REQUEST['view-booking'] )
	&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['view_booking_nonce'] ) ), 'view_booking_' . sanitize_text_field( wp_unslash( $_REQUEST['view-booking'] ) ) )
) {
	do_action( 'bkx_dashboard_booking_view' );
} else {
	/**
	 * Dashboard Booking Nav Tabs
	 */
	do_action( 'bkx_dashboard_bookings_nav' );
	/**
	 * Dashboard Booking Nav Tabs Content
	 */
	do_action( 'bkx_dashboard_booking_content' );
}
do_action( 'bkx_dashboard_bookings_after' );

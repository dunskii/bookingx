<?php
defined( 'ABSPATH' ) || exit;
/**
 * Example of wpmu_new_blog usage
 *
 * @param int $blog_id Blog ID.
 * @param int $user_id User ID.
 * @param string $domain Site domain.
 * @param string $path Site path.
 * @param int $site_id Site ID. Only relevant on multi-network installs.
 * @param array $meta Meta data. Used to set initial site options.
 */

add_action( 'wpmu_new_blog', 'generate_while_multisite_creating', 10, 6 );
function generate_while_multisite_creating( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	 switch_to_blog( $blog_id );
	add_blog_option( $blog_id, 'bkx_alias_seat', esc_html__( 'Resource', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_alias_base', esc_html__( 'Service', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_alias_addition', esc_html__( 'Extra', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_alias_notification', esc_html__( 'Notification', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_notice_time_extended_text_alias', esc_html__( 'How many times you want to extend this service?', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_label_of_step1', esc_html__( 'Please select what you would like to book', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_form_text_color', esc_html__( '40120a', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_form_background_color', esc_html__( 'f0e8e7', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_siteclient_css_border_color', esc_html__( '1f191f', 'bookingx' ) );
	add_blog_option( $blog_id, 'bkx_siteclient_css_progressbar_color', esc_html__( '875428', 'bookingx' ) );
	$bkx_set_booking_page     = bkx_crud_option_multisite( 'bkx_set_booking_page' );
	$bkx_dashboard_page_id    = bkx_crud_option_multisite( 'bkx_dashboard_page_id' );
	$bkx_edit_booking_page_id = bkx_crud_option_multisite( 'bkx_edit_booking_page_id' );
	$my_account_id            = bkx_crud_option_multisite( 'bkx_my_account_page_id' );
	bkx_generate_template_page( 'bkx_dashboard_page_id', '[bkx_dashboard]', __( 'Dashboard', 'bookingx' ), $bkx_dashboard_page_id );
	bkx_generate_template_page( 'bkx_set_booking_page', '[bkx_booking_form]', __( 'Booking Form', 'bookingx' ), $bkx_set_booking_page );
	bkx_generate_template_page( 'bkx_edit_booking_page_id', '[bkx_booking_form]', __( 'Edit Booking', 'bookingx' ), $bkx_edit_booking_page_id, $bkx_dashboard_page_id );
	bkx_generate_template_page( 'bkx_my_account_page_id', '[bkx_my_account]', __( 'My Account', 'bookingx' ), $my_account_id, $bkx_dashboard_page_id );

	restore_current_blog();
}

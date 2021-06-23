<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.6
 * @package    Bookingx
 * @subpackage Bookingx/includes
 * @author     Dunskii Web Services <divyang@dunskii.com>
 */
class Bookingx_Activator {


	/**
	 * Short Description. (use period)
	 * Long Description.
	 *
	 * @since 1.0
	 */
	public function activate() {
		add_action( 'init', array( $this, 'bkx_basic_set_up' ) );
		if ( ! wp_next_scheduled( 'bkx_check_booking_process' ) ) {
			wp_schedule_event( time(), 'hourly', 'bkx_check_booking_process' );
		}
	}

	/**
	 * Quick Settings Related to Post Type name and others
	 */
	public function bkx_basic_set_up() {
		flush_rewrite_rules();
		add_role(
			'resource',
			__( 'Resource' ),
			array(
				'read'         => true,
				'edit_posts'   => true,
				'delete_posts' => false,
			)
		);
		bkx_crud_option_multisite( 'bkx_alias_seat', esc_html__( 'Resource', 'bookingx' ), 'update' );
		bkx_crud_option_multisite( 'bkx_alias_base', esc_html__( 'Service', 'bookingx' ), 'update' );
		bkx_crud_option_multisite( 'bkx_alias_addition', esc_html__( 'Extra', 'bookingx' ), 'update' );
		bkx_crud_option_multisite( 'bkx_alias_notification', esc_html__( 'Extra', 'Notification' ), 'update' );
		bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias', esc_html__( 'How many times you want to extend this service?', 'bookingx' ), 'update' );
		bkx_crud_option_multisite( 'bkx_label_of_step1', esc_html__( 'Please select what you would like to book', 'bookingx' ), 'update' );
	}
}

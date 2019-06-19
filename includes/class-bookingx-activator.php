<?php

/**
 * Fired during plugin activation
 *
 * @link       https://dunskii.com
 * @since      0.6.0-beta
 *
 * @package    Bookingx
 * @subpackage Bookingx/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.6.0-beta
 * @package    Bookingx
 * @subpackage Bookingx/includes
 * @author     Dunskii Web Services <divyang@dunskii.com>
 */
class Bookingx_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.6.0-beta
	 */
	public function activate() {
        add_action( 'init', array( $this,'bkx_basic_set_up' ) );
	}

	public function bkx_basic_set_up(){
        flush_rewrite_rules();
        bkx_crud_option_multisite( 'bkx_alias_seat', 'Resource','update');
        bkx_crud_option_multisite( 'bkx_alias_base', 'Service','update');
        bkx_crud_option_multisite( 'bkx_alias_addition', 'Extra','update');
        bkx_crud_option_multisite( 'bkx_alias_notification', 'Notification','update');
        bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias', 'How many times you want to extend this service?','update');
        bkx_crud_option_multisite( 'bkx_label_of_step1', 'Please select what you would like to book','update');
    }
}

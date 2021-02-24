<?php
/**
 * BookingX Core BookingX Booking Form Short code Class.
 * Used on Booking Page to make a booking
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'BkxBookingFormShortCode' ) ) {
	/**
	 * Class BkxBookingFormShortCode
	 */
	class BkxBookingFormShortCode {


		/**
		 * BkxBookingFormShortCode constructor.
		 */
		function __construct() {
			// Script Loader.
			include_once BKX_PLUGIN_DIR_PATH . 'includes/core/script/class-bkx-script-loader.php';
			// Ajax Loader.
			include_once BKX_PLUGIN_DIR_PATH . 'includes/core/ajax/class-bkx-ajax-loader.php';
			add_shortcode( 'bkx_booking_form', array( $this, 'BkxBookingFormShortCodeView' ) );
			do_action( 'before_bookingx_init' );
		}

		/**
		 * @return object
		 */
		function load_global_variables() {
			$seat_alias        = bkx_crud_option_multisite( 'bkx_alias_seat' );
			$base_alias        = bkx_crud_option_multisite( 'bkx_alias_base' );
			$addition_alias    = bkx_crud_option_multisite( 'bkx_alias_addition' );
			$bkx_booking_style = bkx_crud_option_multisite( 'bkx_booking_style' );
			$currency_sym      = bkx_get_current_currency();
			$currency_name     = get_option( 'currency_option' );
			$global_variables  = array(
				'seat'          => ucfirst( $seat_alias ),
				'base'          => ucfirst( $base_alias ),
				'extra'         => ucfirst( $addition_alias ),
				'currency_sym'  => $currency_sym,
				'currency_name' => $currency_name,
				'booking_style' => ( ( ! isset( $bkx_booking_style ) || $bkx_booking_style == '' ) ? 'default' : $bkx_booking_style ),
			);
			$global_variables  = array_merge( bkx_localize_string_text(), $global_variables );

			return (object) $global_variables;
		}

		/**
		 * @param $args
		 *
		 * @return false|string
		 */
		function BkxBookingFormShortCodeView( $args ) {
			ob_start();
			if ( is_admin() ) {
				$screen    = get_current_screen();
				$base      = $screen->base;
				$post_type = $screen->post_type;
				if ( $base == 'post' && $post_type == 'bkx_booking' ) {
					bkx_get_template( 'booking-form/update-booking-form.php', $args );
				}
			} else {
				bkx_get_template( 'booking-form/booking-form.php', $args );
			}
			return ob_get_clean();
		}
	}

	new BkxBookingFormShortCode();
}

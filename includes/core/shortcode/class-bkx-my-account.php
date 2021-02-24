<?php
/**
 * BookingX BookingX My Account Short code
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BkxMyAccount' ) ) {
	/**
	 * Class BkxMyAccount
	 */
	class BkxMyAccount {


		/**
		 * BkxMyAccount constructor.
		 */
		function __construct() {
			add_action( 'template_redirect', array( $this, 'Bkx_init' ) );
			add_shortcode( 'bkx_my_account', array( $this, 'BkxMyAccount' ) );
			do_action( 'before_bkx_my_account_init' );
		}

		/**
		 *
		 */
		public function Bkx_init() {
			if ( is_my_account() && ! is_user_logged_in() ) {
				$redirect_to = apply_filters( 'bkx_my_account_login_url', wp_login_url() );
				wp_redirect( $redirect_to );
			}
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
		public function BkxMyAccount( $args ) {
			ob_start();
			bkx_get_template( 'dashboard/my-account/my-account.php', $args );
			return ob_get_clean();
		}

		/**
		 * @param string $value
		 */
		public static function set_change_password_cookie( $value = '' ) {
			$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
			$rp_path   = isset( $_SERVER['REQUEST_URI'] ) ? current( explode( '?', sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) : ''; //phpcs:ignore Standard.Category.SniffName.ErrorCode

			if ( $value ) {
				setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			} else {
				setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			}
		}
	}

	new BkxMyAccount();
}

<?php
/**
 * BookingX BookingX Dashboard Short code
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'BkxDashboard' ) ) {
	/**
	 * Class BkxDashboard
	 */
	class BkxDashboard {


		/**
		 * BkxDashboard constructor.
		 */
		function __construct() {
			add_action( 'template_redirect', array( $this, 'Bkx_init' ) );
			add_shortcode( 'bkx_dashboard', array( $this, 'BkxDashboard' ) );
			do_action( 'before_bookingx_init' );
		}

		/**
		 * Bkx_init
		 */
		public function Bkx_init() {
			if ( is_dashboard() && ! is_user_logged_in() ) {
				$redirect_to = apply_filters( 'bkx_dashboard_login_url', wp_login_url() );
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
		 * @param $bookings
		 * @param null     $page_id
		 */
		public function booking_html( $bookings, $page_id = null ) {
			if ( empty( $bookings ) ) {
				return;
			}

			$dashboard_columns = bkx_get_dashboard_orders_columns();
			if ( empty( $page_id ) ) {
				$page_id = get_the_ID();
			}
			if ( ! empty( $bookings ) ) {
				foreach ( $bookings as $order_meta ) {
					$BkxBooking       = new BkxBooking( null, $order_meta['order_id'] );
					$get_order_status = $BkxBooking->get_order_status( $order_meta['order_id'] );
					$booking_id       = $order_meta['order_id'];
					$service_name     = $order_meta['base_arr']['main_obj']->post->post_title;
					$current_currency = isset( $order_meta['currency'] ) ? $order_meta['currency'] : '';
					$datetime         = strtotime( $BkxBooking->get_booking_date() );
					$yesterday        = strtotime( '-1 days' );
					?>
					<tr class="booking-x-orders-table__row booking-x-orders-table__row--status-<?php echo esc_attr( $get_order_status ); ?> order">
					<?php foreach ( $dashboard_columns as $column_id => $column_name ) : ?>
							<td class="booking-x-orders-table__cell booking-x-orders-table__cell-<?php echo esc_attr( $column_id ); ?>"
								data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php if ( has_action( 'bkx_dashboard_booking_column_' . $column_id ) ) : ?>
							<?php do_action( 'bkx_dashboard_booking_column_' . $column_id, $order_meta ); ?>

								<?php elseif ( 'booking-number' === $column_id ) : ?>
									<a href="<?php echo esc_url( $BkxBooking->get_view_booking_url( $booking_id, $page_id ) ); ?>">
									<?php echo esc_html__( _x( '#', 'hash before order number', 'bookingx' ) . $booking_id ); ?>
									</a>

								<?php elseif ( 'booking-date' === $column_id ) : ?>
									<time datetime="<?php echo esc_attr( date( 'c', strtotime( $BkxBooking->get_booking_date() ) ) ); ?>"><?php echo esc_html( $BkxBooking->get_booking_date() ); ?></time>

								<?php elseif ( 'booking-service' === $column_id ) : ?>
									<?php echo esc_html( $service_name ); ?>

								<?php elseif ( 'booking-total' === $column_id ) : ?>
									<?php echo wp_kses_post( sprintf( '%1$s%2$s', $current_currency, $order_meta['total_price'] ) ); ?>
								<?php elseif ( 'booking-actions' === $column_id ) : ?>
									<a class="btn btn-dark" href="<?php echo esc_url( $BkxBooking->get_view_booking_url( $booking_id, $page_id ) ); ?>">
									<?php echo esc_html__( 'View', 'bookingx' ); ?>
									</a>
									<?php if ( $datetime >= $yesterday ) : ?>
									<a class="btn btn-dark" href="<?php echo esc_url( edit_booking_url( $booking_id, $page_id ) ); ?>">
										<?php echo esc_html__( 'Edit', 'bookingx' ); ?>
									</a>
									<?php endif; ?>
									<?php do_action( 'bkx_view_booking_action', $booking_id ); ?>
								<?php endif; ?>
							</td>
					<?php endforeach; ?>
					</tr>
					<?php
				}
			}
		}

		/**
		 * @param  $args
		 * @return false|string
		 */
		public function BkxDashboard( $args ) {
			ob_start();
			bkx_get_template( 'dashboard/dashboard.php', $args );
			return ob_get_clean();
		}
	}

	new BkxDashboard();
}

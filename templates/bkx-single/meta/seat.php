<?php
/**
 * BookingX Single Post Seat Meta Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
global $bkx_seat;
$personal_info          = $bkx_seat->seat_personal_info;
$seat_available_months  = $bkx_seat->seat_available_months;
$seat_available_days    = $bkx_seat->seat_available_days;
$seat_notifiaction_info = $bkx_seat->seat_notifiaction_info;
$seat_payment_info      = $bkx_seat->seat_payment_info;
$booking_url            = $bkx_seat->booking_url;
$currency_option        = ( bkx_crud_option_multisite( 'currency_option' ) ? bkx_crud_option_multisite( 'currency_option' ) : 'AUD' );

if ( ! empty( $seat_available_months ) || ! empty( $seat_available_days ) || ! empty( $seat_payment_info ) ) :
	echo sprintf( '<h3>%s</h3>', esc_html__( 'Additional Information', 'bookingx' ) ); ?>
	<ul class="additional-info">
	<?php
	if ( ! empty( $seat_available_days ) ) {
		$days = '<ul>';
		foreach ( $seat_available_days as $day => $timing ) {
			$from = $timing['time_from'];
			$to   = $timing['time_till'];
			/* translators: 1: <li><span>. */
			$days .= sprintf( __( '<li><span> %1$s : </span><span> %2$s  To %3$s </span></li>' ), esc_html( ucwords( $day ) ), esc_html( $from ), esc_html( $to ) );
		}
		$days .= '</ul>';
	}
	$payment_deposite = '';

	if ( isset( $seat_payment_info['D'] ) ) {
		$payment_deposite = $seat_payment_info['D'];
	}
	if ( isset( $seat_payment_info['FP'] ) ) {
		$payment_full_pay = $seat_payment_info['FP'];
	}
	if ( ! empty( $payment_deposite ) ) :
		$payment         = bkx_get_formatted_price( $payment_deposite['amount'] );
		$payment_details = isset( $payment_deposite['type'] ) && $payment_deposite['type'] == 'FA' ? $payment : "{$payment_deposite['percentage']}&#37;";
		$payment_info    = sprintf( ' %s', esc_html( $payment_details ) );
	endif;
	if ( ! empty( $payment_full_pay ) ) :
		/* translators: 1: <li><span>. */
		$payment_info = sprintf( '%s', '<span> ' . esc_html( $payment_full_pay['value'] ) . ' </span>' );
	endif;
	if ( ! empty( $seat_available_months ) ) :
		$seat_available_months_string = implode( ', ', $seat_available_months );
		/* translators: 1: <li><span>. */
     echo sprintf( __( '<li><div class="months"><label>%1$s : %2$s </label> </li>', 'bookingx' ), esc_html( 'Months' ), esc_html( $seat_available_months_string ) ); // phpcs:ignore
	endif;
	if ( ! empty( $seat_available_days ) ) :
     echo sprintf( '<li><div class="days"><label> Days of operations : %s </label></li>', $days ); // phpcs:ignore
	endif;
	if ( ! empty( $seat_payment_info ) ) :
     echo sprintf( __( '<li><div class="payment-info"><label> Booking Require Pre Payment : &nbsp; %s </label></li>', 'bookingx' ), $payment_info ); // phpcs:ignore
	endif;
	?>
	</ul>
	<?php
else :
	echo '';
endif; ?>

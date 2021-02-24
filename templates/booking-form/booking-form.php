<?php
/**
 * BookingX Booking Main Form Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args ) || ! isset( $args ) ) {
	$args = array();
}
$args['type'] = 'new';
$order_id     = ( isset( $_REQUEST['order_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order_id'] ) ) : 0 ); // phpcs:disable WordPress.Security.NonceVerification
if ( ( isset( $order_id ) && $order_id != '' ) ) {
	$status = ( true );
} else {
	$status = ( false );
}

$booking_edit = false;
if ( isset( $_POST['order_id'], $_REQUEST['id'] ) && isset( $_REQUEST['edit_booking_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['edit_booking_nonce'] ) ), 'edit_booking_' . sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) ) ) {
	$booking_edit = true;
	$args['type'] = 'edit';
}
if ( isset( $args['order_id'] ) && $args['order_id'] > 0 ) {
	$args['order_id'] = sanitize_text_field( $order_id );
}
$args['order_id'] = $order_id;
$args['status']   = $status;
?>
<div class="booking-x">
	<div class="main-container">
		<div class="booking-x container">
			<div class="bkx-booking-form">
				<!--  Start Progress Bar     -->
				<?php bkx_get_template( 'booking-form/progress-bar/progress-bar.php', $args ); ?>
				<!--  End Progress Bar     -->
				<?php if ( isset( $status ) && $status == false ) : ?>
					<div class="bookingx-error-group"></div>
					<form method="post" name="bkx_booking_generate" id="bkx-booking-generate" class="bkx-booking-generate bkx-book-now">
						<!-- Start Initialized Steps -->
					<?php bkx_get_template( 'booking-form/steps/step-1.php' ); ?>
					<?php bkx_get_template( 'booking-form/steps/step-2.php' ); ?>
					<?php bkx_get_template( 'booking-form/steps/step-2b.php' ); ?>
					<?php bkx_get_template( 'booking-form/steps/step-3.php' ); ?>
					<?php bkx_get_template( 'booking-form/steps/step-4.php' ); ?>
					<?php bkx_get_template( 'booking-form/additional/legal-popup.php' ); ?>
					<?php do_action( 'bookingx_form_additional_fields' ); ?>
					</form>
				<?php endif; ?>
				<?php
				if ( isset( $_POST['order_id'] ) && ( isset( $_POST['bkx_payment_gateway_method'] ) || $booking_edit === true ) && ! empty( $_POST['order_id'] ) ) {
					$args = array(
						'order_id'                   => sanitize_text_field( wp_unslash( $_POST['order_id'] ) ),
						'bkx_payment_gateway_method' => sanitize_text_field( wp_unslash( $_POST['bkx_payment_gateway_method'] ) ),
					);
					do_action( 'bkx_payment_gateway_process_hook', $args );
				} else {
					/**
					 * Make Order while form automatically submitted by Event
					 */
					if ( ! isset( $_POST['order_id'] ) || ! isset( $_REQUEST['order_id'] ) ) {
						do_action( 'bkx_make_booking_hook', $_POST );
					}
				}
				?>
				<?php
				if ( ! isset( $status ) || $status != true || ! isset( $_GET['order_id'] ) || sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) == '' ) {

				} else {

					$order_id       = base64_decode( wp_unslash( $_GET['order_id'] ) );
					$order_id       = sanitize_text_field( $order_id );
					$action_data    = array( 'order_id' => $order_id );
					$payment_method = get_post_meta( $order_id, 'payment_method', true );
					if ( ! isset( $payment_method ) || $payment_method != 'bkx_gateway_pay_later' ) {
						$check_payment_data = get_post_meta( $order_id, 'payment_meta', true );
						$transactionID      = 0;
						if ( ! empty( $check_payment_data ) ) {
							$transactionID = sanitize_text_field( $check_payment_data['transactionID'] );
						}
						if ( $transactionID == 0 && $transactionID == '' ) {
							do_action( 'bkx_payment_gateway_capture_process_hook', $action_data );
						}
					}
					$payment_error = get_post_meta( $order_id, 'bkx_payment_message', true );
					if ( ! empty( $payment_error ) ) {
						echo '<div class="row"><div class="col-md-12"><ul class="bookingx-error">';
						// Translators: Payment Error.
						echo sprintf( '<li>Payment Error : %s </li>', esc_html( $payment_error ) );
						echo '</ul></div></div>';

					}
					$capture_payment = get_post_meta( $order_id, 'bkx_capture_payment', true );
					if ( ! empty( $capture_payment ) && $capture_payment['success'] == true ) {
						$BkxPaymentCore = new BkxPaymentCore();
                     echo $BkxPaymentCore->bkx_success_payment( $capture_payment ); // phpcs:ignore
					}
				}
				?>
				<!-- End Initialized Steps -->
			</div>
		</div>
	</div>
</div>

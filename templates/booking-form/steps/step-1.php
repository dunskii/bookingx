<?php
/**
 * BookingX Booking Form Step 1 Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
$bkx_booking_form_shortcode = new BkxBookingFormShortCode();
$load_global                = $bkx_booking_form_shortcode->load_global_variables();
$skip_this_step             = SkipStep();
$skip_this_step_style       = ( $skip_this_step === true ) ? 'style = "display: none;"' : '';
/**
 * Any Seat Functionality
 */
$enable_any_seat = bkx_crud_option_multisite( 'enable_any_seat' );
$enable_any_seat = apply_filters( 'bkx_enable_any_seat', $enable_any_seat );
$default_seat    = bkx_crud_option_multisite( 'select_default_seat' );
$default_seat    = apply_filters( 'bkx_enable_any_seat_id', $default_seat );
?>
<div class="step-1 bkx-form-active" data-active="1" <?php echo esc_html( $skip_this_step_style ); ?>>
	<div class="form-wrapper">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group bkx-staff">
					<label for="staff">
					<?php
					// Translators: Seat Label.
					printf( esc_html__( 'Select %s', 'bookingx' ), esc_html( $load_global->seat ) );
					?>
						<abbr class="required" title="required">*</abbr></label>
					<select name="staff-lists" class="custom-select bkx-staff-lists">
						<option>
						<?php
						// Translators: Seat Label.
						printf( esc_html__( 'Select a %s', 'bookingx' ), esc_html( $load_global->seat ) );
						?>
							</option>
						<?php
						if ( isset( $enable_any_seat ) && $enable_any_seat == 1 ) {
							// Translators: Any Seat Label.
							echo '<option value="any">' . sprintf( esc_html__( 'Any %1$s', 'bookingx' ), esc_html( $load_global->seat ) ) . '</option>';
						}
						?>

						<?php
						$SeatObj    = new BkxSeat();
						$seat_lists = $SeatObj->get_seat_lists();
						if ( ! empty( $seat_lists ) ) {
							foreach ( $seat_lists as $seat_id => $seat_name ) {
								?>
								<option value="<?php echo esc_attr( $seat_id ); ?>"><?php echo esc_html( $seat_name ); ?></option>
								<?php
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group bkx-services">
					<?php
					$base_alias = $load_global->base;
					// Translators: Base Label.
					$base_text = sprintf( esc_html__( 'Select %s', 'bookingx' ), esc_html( $base_alias ) );
					?>
					<label for="email"><?php echo esc_html( $base_text ); ?> <abbr
								class="required" title="required">*</abbr></label>
					<select name="service-lists" class="custom-select bkx-services-lists" disabled>
					</select>
				</div>
				<span class="bkx-extra-lists"></span>
			</div>
			<div class="col-md-6">

			</div>
			<div class="col-md-6">
				<div class="form-group service-extended" style="display: none;">
					<?php $bkx_notice_time_extended_text_alias = bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias' ); ?>
					<label for="email">
					<?php
					// Translators: Extended Label.
					echo sprintf( '%s', esc_html( $bkx_notice_time_extended_text_alias ) );
					?>
						<abbr class="required" title="required">*</abbr></label>
					<select name="service-extend" class="custom-select bkx-services-extend" disabled>
					</select>
				</div>
			</div>
		</div>
		<div class="bkx-booking-total total-text">
			Total: <span></span>
		</div>
		<!--user-detail bg-white px-3 py-4-->
		<div class="bkx-disable-days-note"></div>
	</div>
	<div class="button-wrapper">
		<button type="submit" class="btn btn-default button bkx-form-submission-next"><?php esc_html_e( 'Next', 'bookingx' ); ?></button>
	</div>
</div>

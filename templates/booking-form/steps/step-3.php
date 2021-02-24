<?php
/**
 * BookingX Booking Form Step 3 Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit; ?>
<?php
$privacy_policy     = bkx_crud_option_multisite( 'bkx_privacy_policy_page' );
$cancellation       = bkx_crud_option_multisite( 'bkx_cancellation_policy_page' );
$term_cond          = bkx_crud_option_multisite( 'bkx_term_cond_page' );
$privacy_policy_url = ( isset( $privacy_policy ) ) && $privacy_policy != '' ? get_permalink( $privacy_policy ) : '';
$term_cond_url      = ( isset( $term_cond ) ) && $term_cond != '' ? get_permalink( $term_cond ) : '';
$cancellation_url   = ( isset( $cancellation ) ) && $cancellation != '' ? get_permalink( $cancellation ) : '';
$bkx_legal          = bkx_crud_option_multisite( 'bkx_legal_options' );
?>
<div class="step-3 bkx-form-deactivate" data-active="3">
	<div class="user-detail">
		<div class="row"></div>
		<div class="bkx-additional-detail"></div>
	</div>
	<div class="form-wrapper">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label><?php esc_html_e( 'Name', 'bookingx' ); ?></label>
					<input type="text" name="bkx_first_name" class="form-control" placeholder="First name">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="mbl-blank">&nbsp;</label>
					<input type="text" name="bkx_last_name" class="form-control" placeholder="Last name">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label><?php esc_html_e( 'Contact Details', 'bookingx' ); ?></label>
					<input type="text" name="bkx_phone_number" class="form-control" placeholder="Phone">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="mbl-blank">&nbsp;</label>
					<input type="email" name="bkx_email_address" class="form-control" placeholder="Email">
				</div>
			</div>
		</div>
		<?php if ( isset( $bkx_legal ) && $bkx_legal == 1 ) : ?>
			<div class="row mt-4">
			<?php if ( isset( $term_cond_url ) && $term_cond_url != '' ) : ?>
				<div class="col-md-4">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="bkx-terms-and-conditions" name="bkx_terms_and_conditions">
						<label class="custom-control-label" for="bkx-terms-and-conditions">
				<?php echo sprintf( 'Do you agree with our <a href="#" data-toggle="modal" data-target=".bkx-tc">terms and conditions ?</a>' ); ?>
							</label>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( isset( $privacy_policy_url ) && $privacy_policy_url != '' ) : ?>
				<div class="col-md-4">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="bkx-privacy-policy" name="bkx_privacy_policy">
						<label class="custom-control-label" for="bkx-privacy-policy">
				<?php echo sprintf( 'Do you agree with our <a href="#" data-toggle="modal" data-target=".bkx-pp">privacy policy ?</a>' ); ?>
						</label>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( isset( $cancellation_url ) && $cancellation_url != '' ) : ?>
				<div class="col-md-4">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="bkx-cancellation-policy" name="bkx_cancellation_policy">
						<label class="custom-control-label" for="bkx-cancellation-policy">
				<?php echo sprintf( 'Do you agree with our <a href="#" data-toggle="modal" data-target=".bkx-cp">cancellation policy ?</a>' ); ?>
						</label>
					</div>
				</div>
			<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="button-wrapper">
		<button type="submit" class="btn btn-default button bkx-form-submission-next"><?php esc_html_e( 'Next', 'bookingx' ); ?></button></button>
		<button type="submit" class="btn btn-default button bkx-form-submission-previous"><?php esc_html_e( 'Previous', 'bookingx' ); ?></button>
	</div>
</div>

<?php
/**
 * BookingX Customer Change Password Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
do_action( 'bkx_before_customer_change_password_form' );
?>

	<div class="col-md-12 bkx-change-password-main">
		<span class="anchor" id="formChangePassword"></span>
		<!-- form card change password -->
		<div class="card card-outline-secondary">
			<div class="card-header">
				<h2 class="mb-0"><?php esc_html_e( 'Change Password', 'bookingx' ); ?></h2>
			</div>
			<div class="card-body">
				<div class="alert alert-success bkx-change-password-success-message" style="display: none;" role="alert"></div>
				<form class="form bkx-change-password-form" method="post" id="bkx-change-password" role="form" autocomplete="off">
					<div class="form-group">
						<label for="inputPasswordOld"><?php esc_html_e( 'Current Password', 'bookingx' ); ?></label>
						<input type="password" name="current_password" class="form-control bkx-current_password" id="inputPasswordOld" required="">
						<div class="invalid-feedback current_password-feedback"><?php esc_html_e( 'Please enter current password.', 'bookingx' ); ?></div>
					</div>
                    <?php $recommended_text = "Recommended : The password must be 8-20 characters, and must <em>not</em> contain spaces.";
                    $allowed_tag = array('em');
                    $recommended = wp_kses( $recommended_text, $allowed_tag );?>
					<div class="form-group">
						<label for="inputPasswordNew"><?php esc_html_e( 'New Password', 'bookingx' ); ?></label>
						<input type="password" name="new_password" class="form-control bkx-new_password" id="inputPasswordNew" required="">
						<span class="form-text small text-muted"><?php echo $recommended; ?></span>
						<div class="invalid-feedback new_password-feedback"><?php esc_html_e( 'Please enter new password.', 'bookingx' ); ?></div>
					</div>
					<div class="form-group">
						<label for="inputPasswordNewVerify"><?php esc_html_e( 'Confirm Password', 'bookingx' ); ?></label>
						<input type="password" name="confirm_password" class="form-control bkx-confirm_password" id="inputPasswordNewVerify" required="">
						<span class="form-text small text-muted"> <?php esc_html_e( 'To confirm, type the new password again.', 'bookingx' ); ?> </span>
						<div class="invalid-feedback confirm_password-feedback"><?php esc_html_e( 'Please enter confirm password.', 'bookingx' ); ?></div>
					</div>
					<?php do_action( 'bkx_customer_change_password_form' ); ?>
					<?php wp_nonce_field( 'bkx-change-password', 'bkx-change-password-nonce' ); ?>
					<div class="form-group">
						<button type="submit" name="change_password"
								class="btn btn-success btn-lg float-right bkx-change-password-btn"><?php esc_html_e( 'Save', 'bookingx' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<!-- /form card change password -->
	</div>
<?php
do_action( 'bkx_after_customer_change_password_form' );

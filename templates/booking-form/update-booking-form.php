<?php
/**
 * BookingX Update Booking Main Form Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
$form_mode = 'bkx-admin-new-booking';
if(!empty($args)){
	$order_id = $args['order_post_id'];
	$args['admin_edit'] = 1;
	$form_mode = 'bkx-admin-edit-booking';
} ?>
<div class="booking-x update-form <?php echo $form_mode;?>">
	<div class="main-container">
		<div class="booking-x container">
			<div class="bkx-booking-form">
				<!--  Start Progress Bar     -->
				<?php bkx_get_template( 'booking-form/progress-bar/progress-bar.php' ); ?>
				<!--  End Progress Bar     -->
				<div class="bookingx-error-group"></div>
				<!-- Start Initialized Steps -->
				<?php bkx_get_template( 'booking-form/steps/step-1.php' ); ?>
				<?php bkx_get_template( 'booking-form/steps/step-2.php', $args ); ?>
				<?php bkx_get_template( 'booking-form/steps/step-2b.php' ); ?>
				<?php bkx_get_template( 'booking-form/steps/step-3.php' ); ?>
				<?php bkx_get_template( 'booking-form/steps/step-4.php' ); ?>
				<!-- End Initialized Steps -->
			</div>
		</div>
	</div>
</div>

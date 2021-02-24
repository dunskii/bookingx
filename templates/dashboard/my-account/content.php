<?php
/**
 * BookingX Booking My Account Content Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;

do_action( 'bkx_dashboard_my_account_before_content' );
?>
	<div class="row bkx-my-account">
		<div class="col-sm-12">
			<p>&nbsp;</p>
			<?php bkx_get_template( 'dashboard/my-account/customer-details.php', $args ); ?>
			<p>&nbsp;</p>
			<?php bkx_get_template( 'dashboard/my-account/form-change-password.php', $args ); ?>
		</div>
	</div>
<?php
do_action( 'bkx_dashboard_my_account_after_content' );

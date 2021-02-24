<?php
/**
 * BookingX Dashboard Landing Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
do_action( 'bkx_before_dashboard' );
?>
	<div class="container-fluid">
		<div class="row">
			<?php
			do_action( 'bkx_dashboard_content' );
			?>
		</div>
	</div>
<?php
do_action( 'bkx_after_dashboard' );

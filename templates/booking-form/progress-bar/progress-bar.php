<?php
/**
 * BookingX Booking Form Progress Bar Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;

$bkx_label_step_1 = ( ! empty( bkx_crud_option_multisite( 'bkx_label_of_step1' ) ) ) ? bkx_crud_option_multisite( 'bkx_label_of_step1' ) : 'Please select what you would like to book';
// translators: Step 1 Label.
$bkx_label_step_1 = sprintf( esc_html__( '%1$s', 'bookingx' ), $bkx_label_step_1 );
$form_title       = '';

$is_active_5    = '';
$default_active = 'is-active';

if ( ! empty( $args ) && isset( $args['order_id'] ) && $args['order_id'] != '' ) {
	$is_active_5    = 'is-active';
	$default_active = '';
}
$skip_this_step       = SkipStep();
$skip_this_step_style = ( $skip_this_step == true ) ? 'style = "display: none;"' : '';
$back_booking         = '';

if ( $skip_this_step == true ) {
	echo "<div class='bkx-admin-loading-edit'><div class=\"row\"><div class=\"col-md-12\"> <h2>" . esc_html__( 'Please Wait Booking Form is Loading..', 'bookingx' ) . '</h2></div></div></div>';
}
?>
<div class="progress-wrapper" <?php echo esc_attr( $skip_this_step_style ); ?>>
	<?php
	if ( isset( $args['title'] ) && $args['title'] == 'true' ) {
		echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
	}
	?>
	<ul class="progress-tracker progress-tracker--text">
		<li class="progress-step <?php echo esc_attr( $default_active ); ?> left-curve progress-step-1">
			<span class="progress-text">
				<h4 class="progress-title"><?php echo sprintf( 'Step 1 : %s', esc_html( $bkx_label_step_1 ) ); ?> </h4>
			</span>
		</li>
		<li class="progress-step progress-step-2">
			<span class="progress-text"><h4 class="progress-title"><?php esc_html_e( 'Step 2 : Date & time', 'bookingx' ); ?></h4>
			</span>
		</li>
		<li class="progress-step progress-step-3">
			<span class="progress-text"><h4 class="progress-title"><?php esc_html_e( 'Step 3 : Details', 'bookingx' ); ?></h4>
			</span>
		</li>
		<li class="progress-step right-curve progress-step-4">
			<span class="progress-text"><h4 class="progress-title"><?php esc_html_e( 'Step 4 : Check Booking', 'bookingx' ); ?></h4>
			</span>
		</li>
		<li class="progress-step right-curve <?php echo esc_attr( $is_active_5 ); ?> progress-step-5">
			<span class="progress-text"><h4 class="progress-title"><?php esc_html_e( 'Step 5 : Booking Confirmed', 'bookingx' ); ?></h4>
			</span>
		</li>
	</ul>
</div>

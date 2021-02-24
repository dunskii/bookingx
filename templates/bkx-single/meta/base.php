<?php
/**
 * BookingX Single Post Base Meta Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;

global $bkx_base;
$base_id        = $bkx_base->id;
$booking_url    = $bkx_base->booking_page_url;
$bkx_alias_base = bkx_crud_option_multisite( 'bkx_alias_base' );
?>
<div class="row">
	<div class="col-md-8">
		<?php echo sprintf( '<h3>%s</h3>', esc_html__( 'Additional Information', 'bookingx' ) ); ?>
	</div>
	<div class="col-md-4">
		<form method="post" enctype='multipart/form-data' action="<?php echo esc_url( $booking_url ); ?>">
			<input type="hidden" name="type" value="bkx_seat"/>
			<input type="hidden" name="id" value="<?php echo esc_attr( $base_id ); ?>"/>
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Book now', 'bookingx' ); ?></button>
		</form>
	</div>
	<div class="col-md-12">
		<hr class="mt-1"/>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<ul class="additional-info">
			<?php
			$meta_data          = $bkx_base->meta_data;
			$base_location_type = isset( $meta_data['base_location_type'][0] ) ? esc_attr( $meta_data['base_location_type'][0] ) : 'Fixed Location';
			$duration           = $bkx_base->get_service_time();
			$duration_text      = service_hours_formatted( $duration );
			$extended           = $bkx_base->service_extended();
			$extended_label     = isset( $extended ) && $extended == 'Y' ? 'Yes' : 'No';
			echo sprintf( '<li><div class="durations"><label>%s :</label><span> %s</span></li>', 'Duration ', esc_html( $duration_text ) );
			echo sprintf( '<li><div class="extended"><label>%2$s Time Extended :</label><span> %1$s</span></li>', esc_html( $extended_label ), esc_html( $bkx_alias_base ) );
			do_action( 'bkx_base_meta_data', $base_id, $bkx_base );
			?>
		</ul>
	</div>
</div>

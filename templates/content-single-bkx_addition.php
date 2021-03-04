<?php
/**
 * BookingX Content of Extra Single Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
global $bkx_addition;
$alias_base  = bkx_crud_option_multisite( 'bkx_alias_base' );
$alias_extra = bkx_crud_option_multisite( 'bkx_alias_addition' );
$alias_seat  = bkx_crud_option_multisite( 'bkx_alias_seat' );

$booking_url    = $bkx_addition->booking_page_url;
$extra_id       = $bkx_addition->id;
$price_duration = bkx_get_post_price_duration_plain( $bkx_addition, $alias_base );
$BaseObj        = new BkxBase();
if ( isset( $base_id ) && $base_id != '' ) {
	$get_seat_by_base = $BaseObj->get_seat_by_base( $base_id );
}
if ( isset( $extra_id ) && $extra_id != '' ) {
	$get_base_by_extra = $BaseObj->get_base_by_extra( $extra_id );
	$bkx_seatObj       = new BkxSeat();
	$get_seat_by_extra = $bkx_seatObj->get_seat_by_extra( $extra_id );
}
$available_seats = bkx_get_post_with_price_duration( $get_seat_by_extra, $alias_seat, 'seat' );
$available_base  = bkx_get_post_with_price_duration( $get_base_by_extra, $alias_base, 'base' );
$image           = 'yes';
$desc            = 'yes';
if ( ! empty( $args ) ) {
	$desc  = $args['description'];
	$image = $args['image'];
}
$args_data = apply_filters(
	'bkx_single_post_view_args',
	array(
		'post_type' => 'bkx_addition',
		'ID'        => $extra_id,
	)
);
?>
<div class="bkx-single-post-view clearfix">
	<div class="container">
		<?php do_action( 'bkx_before_row_single_post', $args_data ); ?>
		<div class="row">
			<?php if ( $image == 'yes' ) : ?>
				<div class="col-md-4">
				<?php do_action( 'bkx_before_single_post_title', $args_data ); ?>
						<?php echo $bkx_addition->get_thumb(); //phpcs:ignore ?>
				<?php do_action( 'bkx_after_single_post_title', $args_data ); ?>
				</div>
			<?php endif; ?>
			<?php do_action( 'bkx_before_col_single_post', $args_data ); ?>
				<div class="col-md-<?php echo esc_attr( $image == 'yes' ) ? 8 : 12; ?>">
				<div class="row">
					<div class="col-md-8"><h1><?php echo esc_html( get_the_title( $extra_id ) ); ?></h1>
						<?php
						echo esc_html( "{$price_duration['time']}" );
						echo "{$price_duration['price']}"; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
						?>
					<div class="col-md-4">
						<form method="post" enctype='multipart/form-data' action="<?php echo esc_attr( $booking_url ); ?>">
							<input type="hidden" name="type" value="bkx_base"/>
							<input type="hidden" name="id" value="<?php echo esc_attr( $extra_id ); ?>"/>
							<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Book now', 'bookingx' ); ?></button>
						</form>
					</div>
				</div>
				<hr/>
				<?php if ( $desc == 'yes' ) : ?>
					<div class="row">
						<div class="col-md-12"><?php echo apply_filters( 'the_content', get_the_content( '', false, $bkx_addition->post ) ); //phpcs:ignore ?></div>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $available_seats ) ) : ?>
					<div class="available-seats"><?php echo $available_seats; //phpcs:ignore ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $available_base ) ) : ?>
					<div class="available-services"><?php echo $available_base; //phpcs:ignore ?></div>
				<?php endif; ?>
			</div>
			<?php do_action( 'bkx_after_col_single_post', $args_data ); ?>
		</div>
		<?php do_action( 'bkx_after_row_single_post', $args_data ); ?>
	</div>
</div>

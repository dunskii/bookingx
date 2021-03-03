<?php
/**
 * BookingX Content of Base Single Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
global $bkx_base;
$booking_url    = $bkx_base->booking_page_url;
$alias_base     = bkx_crud_option_multisite( 'bkx_alias_base' );
$base_id        = $bkx_base->id;
$price_duration = bkx_get_post_price_duration_plain( $bkx_base, $alias_base );
$args           = ! empty( $args ) ? $args : array();
$settings       = apply_filters( 'bookingx_block_grid_setting', $args );
$card_width     = apply_filters( 'bookingx_card_width_setting', $args );
$image          = 'yes';
$desc           = 'yes';
if ( ! empty( $args ) ) {
	$desc  = isset( $args['description'] ) && $args['description'] != '' ? $args['description'] : 'yes';
	$image = isset( $args['image'] ) && $args['image'] != '' ? $args['image'] : 'yes';
}
$args_data    = apply_filters(
	'bkx_listing_post_view_args',
	array(
		'post_type' => 'bkx_base',
		'ID'        => $base_id,
	)
);
$center_class = '';
?>
<div class="<?php echo esc_attr( $settings['class'] ); ?> base-<?php echo esc_attr( $base_id ); ?>">
	<div class="card<?php echo esc_attr( $settings['block'] ); ?>">
		<?php if ( $image == 'yes' ) : ?>
			<?php echo $bkx_base->get_thumb(); //phpcs:ignore ?>
		<?php endif; ?>
		<div class="card-body">
			<?php do_action( 'bkx_before_listing_title', $args_data ); ?>
				<h5 class="card-title m-0 text-center"><a href="<?php echo esc_url( get_permalink( $base_id ) ); ?>"><?php echo esc_html( get_the_title( $base_id ) ); ?></a></h5>
			<?php do_action( 'bkx_after_listing_title', $args_data ); ?>
			<p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo esc_html( $price_duration['time'] ); ?></p>
			<?php if ( $desc == 'yes' ) : ?>
				<p class="card-text"><?php echo wp_trim_words( get_the_content( $base_id ), 15, '...' ); //phpcs:ignore ?></p>
			<?php endif; ?>
			<?php do_action( 'bkx_after_listing_content', $args_data ); ?>
			<p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo $price_duration['price']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?></p>
			<div class="text-center">
				<a href="<?php echo esc_url( $booking_url ); ?>" class="btn btn-primary"><?php echo esc_html__( 'Book now', 'bookingx' ); ?></a>
			</div>
			<?php do_action( 'bkx_after_listing_book_now', $args_data ); ?>
		</div>
	</div>
</div>

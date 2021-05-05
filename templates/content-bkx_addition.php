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
$alias_extra        = bkx_crud_option_multisite( 'bkx_alias_addition' );
$booking_url        = $bkx_addition->booking_page_url;
$addition_id        = $bkx_addition->id;
$sale_price_details = $bkx_addition->sale_price_details;
$price_duration     = bkx_get_post_price_duration_plain( $bkx_addition, $alias_extra );
$args               = ! empty( $args ) ? $args : array();
$settings           = apply_filters( 'bookingx_block_grid_setting', $args );
$card_width         = apply_filters( 'bookingx_card_width_setting', $args );
$image              = 'yes';
$desc               = 'yes';
if ( ! empty( $args ) ) {
	$desc  = isset( $args['description'] ) && $args['description'] != '' ? $args['description'] : 'yes';
	$image = isset( $args['image'] ) && $args['image'] != '' ? $args['image'] : 'yes';
}
$args_data = apply_filters(
	'bkx_listing_post_view_args',
	array(
		'post_type' => 'bkx_addition',
		'ID'        => $addition_id,
	)
);
?>
<div class="<?php echo esc_attr( $settings['class'] ); ?>  addition-<?php echo esc_attr( $addition_id ); ?>">
	<div class="card <?php echo esc_attr( $settings['block'] ); ?> text-center mt-2">
		<?php if ( $image == 'yes' ) : ?>
			<?php echo $bkx_addition->get_thumb(); // phpcs:ignore ?>
		<?php endif; ?>
		<div class="card-body">
			<?php do_action( 'bkx_before_listing_title', $args_data ); ?>
			<h5 class="card-title m-0 text-center"><a href="<?php echo esc_url( get_permalink( $addition_id ) ); ?>"><?php echo esc_html( get_the_title( $addition_id ) ); ?></a></h5>
			<?php do_action( 'bkx_after_listing_title', $args_data ); ?>
			<p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo esc_html( $price_duration['time'] ); ?></p>
			<?php if ( $desc == 'yes' ) : ?>
				<p class="card-text"><?php echo wp_trim_words( get_the_content(), 15, '...' ); //phpcs:ignore ?></p>
			<?php endif; ?>
			<?php do_action( 'bkx_after_listing_content', $args_data ); ?>
			<?php if ( ! empty( $sale_price_details ) ) : ?>
				<p class="card-text mb-2 mt-2 text-center font-weight-bold price-section"><del><?php echo $sale_price_details['currency'] . $sale_price_details['addition_price']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?></del> <span class="bkx-sale-price"><?php echo $sale_price_details['currency'] . $sale_price_details['extra_sale_price']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?></span> </p>
			<?php else : ?>
				<p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo $price_duration['price']; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			 <div class="text-center">
				<a href="<?php echo esc_url( $booking_url ); ?>" class="btn btn-primary"><?php echo esc_html__( 'Book now', 'bookingx' ); ?></a>
			</div>
			<?php do_action( 'bkx_after_listing_book_now', $args_data ); ?>
		</div>
	</div>
</div>

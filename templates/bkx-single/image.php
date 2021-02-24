<?php
/**
 * BookingX Single Post Image Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
global $bkx_seat, $bkx_base, $bkx_addition;
if ( ! empty( $bkx_seat ) ) {
	$bkx_current_post = $bkx_seat;
}
if ( ! empty( $bkx_base ) ) {
	$bkx_current_post = $bkx_base;
}
if ( ! empty( $bkx_addition ) ) {
	$bkx_current_post = $bkx_addition;
}
?>
<div class="images col-1 service-category-active">
	<?php
	if ( has_post_thumbnail() ) {
		$gallery = '';
		$image   = get_the_post_thumbnail(
			$bkx_current_post->id,
			apply_filters( 'bkx_single_post_large_thumbnail_size', 'shop_single' ),
			array(
				'title' => get_the_title(),
				'alt'   => get_the_title(),
				'class' => 'img-thumbnail',
			)
		);
		echo apply_filters( // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			'bookingx_single_post_image_html',
			sprintf(
				'<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="prettyPhoto%s">%s</a>',
				esc_url( get_permalink() ),
				esc_attr( get_the_title() ),
				$gallery,
				$image
			),
			$bkx_current_post->id
		);
	} else {
		echo apply_filters( 'bookingx_single_post_image_html', sprintf( '<img src="%s" alt="%s" class="img-thumbnail" />', bkx_placeholder_img_src(), esc_html__( 'Placeholder', 'bookingx' ) ), $bkx_current_post->id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
	do_action( 'bookingx_post_thumbnails' );
	?>
</div>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $bkx_seat;
?>
<div class="images">
	<?php
		if ( has_post_thumbnail() ) {
			//$attachment_count = count( $bkx_seat->get_gallery_attachment_ids() );
			$gallery          = $attachment_count > 0 ? '[post-gallery]' : '';
			//$props            = wc_get_post_attachment_props( get_post_thumbnail_id(), $bkx_seat );
			$image            = get_the_post_thumbnail( $bkx_seat->ID, apply_filters( 'single_post_large_thumbnail_size', 'shop_single' ), array(
				'title'	 => get_the_title(),
				'alt'    => get_the_title(),
			) );
			echo apply_filters(
				'bookingx_single_post_image_html',
				sprintf(
					'<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="prettyPhoto%s">%s</a>',
					esc_url(get_permalink() ),
					esc_attr(get_the_title() ),
					$gallery,
					$image
				),
				$bkx_seat->ID
			);
		} else {
			echo apply_filters( 'bookingx_single_post_image_html', sprintf( '<img src="%s" alt="%s" />', bkx_placeholder_img_src(), __( 'Placeholder', 'bookingx' ) ), $bkx_seat->ID );
		}

		do_action( 'bookingx_post_thumbnails' );
	?>
</div>

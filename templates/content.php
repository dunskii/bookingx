<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php
	/**
	 * bookingx_before_single_post hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'bookingx_before_single_post',$data );
?>
<div itemscope itemtype="<?php //echo bookingx_get_schema(); ?>" id="<?php echo $url_arg_name;?>-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		/**
		 * bookingx_before_single_post_summary hook.
		 *
		 * @hooked bookingx_show_post_sale_flash - 10
		 * @hooked bookingx_show_post_images - 20
		 */
		do_action( 'bookingx_before_single_post_summary',$data );
	?>
	<div class="summary entry-summary">

		<?php
			/**
			 * bookingx_single_post_summary hook.
			 *
			 * @hooked bookingx_template_single_title - 5
			 * @hooked bookingx_template_single_rating - 10
			 * @hooked bookingx_template_single_price - 10
			 * @hooked bookingx_template_single_excerpt - 20
			 * @hooked bookingx_template_single_add_to_cart - 30
			 * @hooked bookingx_template_single_meta - 40
			 * @hooked bookingx_template_single_sharing - 50
			 */
			do_action( 'bookingx_single_post_summary',$data );
		?>
	</div><!-- .summary -->
	<?php
		/**
		 * bookingx_after_single_post_summary hook.
		 *
		 * @hooked bookingx_output_post_data_tabs - 10
		 * @hooked bookingx_upsell_display - 15
		 * @hooked bookingx_output_related_posts - 20
		 */
		do_action( 'bookingx_after_single_post_summary',$data );
	?>
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
</div><!-- #post-<?php the_ID(); ?> -->

    <?php do_action( 'bookingx_after_single_post',$data ); ?>
<?php 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wp_query,$wpdb;
$query = new WP_Query( array( 'post_type' => $post_type , 'posts_per_page'=> $per_page, 'post__in'=> $in_array ) );

if(!empty($in_array) && sizeof($in_array) == 1)
{
	$redirect_to = get_permalink($in_array[0]);
	wp_safe_redirect($redirect_to);
	die();
}
?>
<div class="bookingx-service-listing">

		<?php if ( $query->have_posts() ) : ?>
			<?php
			// Start the Loop.
			while ( $query->have_posts() ) : $query->the_post();
				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				require 'content.php';

			// End the loop.
			endwhile;

			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'bookingx'),
				'next_text'          => __( 'Next page', 'bookingx'),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'bookingx') . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'content', 'none' );

		endif;
		?>

 
	</div><!-- .Main service listing-area -->



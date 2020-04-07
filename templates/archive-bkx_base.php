<?php 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wp_query,$wpdb;
$query = new WP_Query( array( 'post_type' => 'bkx_base') );
get_header('bkx_seat');
?>
<div class="bookingx-lists container">
		<?php if ( $query->have_posts() ) : ?>
            <div class="row">
			<?php
			// Start the Loop.
			while ( $query->have_posts() ) : $query->the_post();
				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				require 'content-bkx_base.php';
			// End the loop.
			endwhile;?>
            </div>
            <?php

		// If no content, include the "No posts found" template.
		else :
			do_action('bookingx_no_bases_found');
		endif;
		?>
</div><!-- .Main service listing-area -->
<?php
get_footer('bkx_seat');
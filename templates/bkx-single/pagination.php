<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bkx_seat;
the_post_navigation( array(
				'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'bookingx' ) . '</span> ' .
					'<span class="screen-reader-text">' . __( 'Next post:', 'bookingx' ) . '</span> ' .
					'<span class="post-title">%title</span>',
				'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'bookingx' ) . '</span> ' .
					'<span class="screen-reader-text">' . __( 'Previous post:', 'bookingx' ) . '</span> ' .
					'<span class="post-title">%title</span>',
			) );
<?php
/**
 * Single Post title
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

the_title( '<h1 itemprop="name" class="post_title entry-title">', '</h1>' );
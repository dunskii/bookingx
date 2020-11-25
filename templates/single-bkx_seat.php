<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * The template for displaying all single posts and attachments
 *
 * @package Bookingx
 * @subpackage bookingx
 * @since      0.7.6.4.3
 */
get_header('bkx_seat'); ?>
<?php
/**
 * bookingx_before_main_content hook.
 *
 * @hooked bookingx_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked bookingx_breadcrumb - 20
 */
do_action('bookingx_before_main_content');
?>

<?php while (have_posts()) : the_post(); ?>

    <?php require 'content-single-bkx_seat.php'; ?>

<?php endwhile; // end of the loop. ?>

<?php
/**
 * bookingx_after_main_content hook.
 *
 * @hooked bookingx_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('bookingx_after_main_content');
?>

<?php
/**
 * bookingx_sidebar hook.
 *
 * @hooked bookingx_get_sidebar - 10
 */
do_action('bookingx_sidebar');
?>

<?php get_footer('bkx_seat');
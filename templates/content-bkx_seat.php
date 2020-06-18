<?php
defined('ABSPATH') || exit;
global $bkx_seat;
$booking_url = $bkx_seat->booking_page_url;
$seat_id = $bkx_seat->id;
$args = !empty($args) ? $args : array();
$settings = apply_filters('bookingx_block_grid_setting', $args);
$card_width = apply_filters('bookingx_card_width_setting', $args);
$args_data = apply_filters('bkx_listing_post_view_args', array('post_type' => 'bkx_seat', 'ID' => $seat_id) );
?>
<div class="<?php echo $settings['class']; ?> ">
    <div class="card<?php echo $settings['block']; ?>" style="width: <?php echo $card_width; ?>">
        <?php echo $bkx_seat->get_thumb(); ?>
        <div class="card-body">
            <?php do_action('bkx_before_listing_title', $args_data); ?>
                <h5 class="card-title m-0 text-center"><a href="<?php echo get_permalink($seat_id); ?>"><?php echo get_the_title($seat_id); ?></a></h5>
            <?php do_action('bkx_after_listing_title', $args_data); ?>
            <p class="card-text"><?php echo wp_trim_words(get_the_content(), 15, '...'); ?></p>
            <?php do_action('bkx_after_listing_content', $args_data); ?>
            <div class="text-center"><a href="<?php echo $booking_url; ?>" class="btn btn-primary"><?php echo esc_html('Book now', 'bookingx'); ?></a></div>
            <?php do_action('bkx_after_listing_book_now', $args_data); ?>
        </div>
    </div>
</div>

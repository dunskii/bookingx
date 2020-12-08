<?php
defined('ABSPATH') || exit;
global $bkx_addition;
$alias_extra = bkx_crud_option_multisite('bkx_alias_addition');
$booking_url = $bkx_addition->booking_page_url;
$addition_id = $bkx_addition->id;
$price_duration = bkx_get_post_price_duration_plain($bkx_addition, $alias_extra);
$args = !empty($args) ? $args : array();
$settings = apply_filters('bookingx_block_grid_setting', $args);
$card_width = apply_filters('bookingx_card_width_setting', $args);
$image = $desc = "yes";
if (!empty($args)) {
	$desc = isset($args['description']) && $args['description'] != "" ? $args['description'] : 'yes';
	$image = isset($args['image']) && $args['image'] != "" ? $args['image'] : 'yes';
}
$args_data = apply_filters('bkx_listing_post_view_args', array('post_type' => 'bkx_addition', 'ID' => $addition_id) );
//style="width: <?php echo $card_width;
?>
<div class="<?php echo $settings['class']; ?>  addition-<?php echo $addition_id; ?>">
    <div class="card<?php echo $settings['block']; ?>" >
        <?php if ($image == "yes") : ?>
            <?php echo $bkx_addition->get_thumb(); ?>
        <?php endif; ?>
        <div class="card-body">
            <?php do_action('bkx_before_listing_title', $args_data); ?>
            <h5 class="card-title m-0 text-center"><a href="<?php echo get_permalink($addition_id); ?>"><?php echo get_the_title($addition_id); ?></a></h5>
            <?php do_action('bkx_after_listing_title', $args_data); ?>
            <p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo $price_duration['time']; ?></p>
            <?php if ($desc == "yes") : ?>
                <p class="card-text"><?php echo wp_trim_words(get_the_content(), 15, '...'); ?></p>
            <?php endif; ?>
            <?php do_action('bkx_after_listing_content', $args_data); ?>
            <p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo $price_duration['price']; ?></p>
            <div class="text-center">
                <a href="<?php echo $booking_url; ?>" class="btn btn-primary"><?php echo esc_html('Book now', 'bookingx');?></a>
            </div>
            <?php do_action('bkx_after_listing_book_now', $args_data); ?>
        </div>
    </div>
</div>
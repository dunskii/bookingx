<?php
defined( 'ABSPATH' ) || exit;
global $bkx_addition;
$alias_extra = bkx_crud_option_multisite('bkx_alias_addition');
$booking_url = $bkx_addition->booking_page_url;
$addition_id = $bkx_addition->id;
$price_duration = bkx_get_post_price_duration_plain( $bkx_addition, $alias_base );
$settings = apply_filters('bookingx_block_grid_setting', $args);
$card_width = apply_filters('bookingx_card_width_setting');
?>
<div class="<?php echo $settings['class'];?>  addition-<?php echo $addition_id;?>">
    <div class="card<?php echo $settings['block'];?>" style="width: <?php echo $card_width;?>">
        <?php echo $bkx_addition->get_thumb();?>
        <div class="card-body">
            <h5 class="card-title m-0 text-center"><?php echo get_the_title(); ?></h5>
            <p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo $price_duration['time']; ?></p>
            <p class="card-text"><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
            <p class="card-text mb-2 mt-2 text-center font-weight-bold"><?php echo $price_duration['price']; ?></p>
            <div class="text-center">
                <a href="<?php echo $booking_url;?>" class="btn btn-primary">Book now</a>
            </div>
        </div>
    </div>
</div>
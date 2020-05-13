<?php
defined( 'ABSPATH' ) || exit;
global $bkx_seat;
$booking_url = $bkx_seat->booking_page_url;
$seat_id = $bkx_seat->id;
$settings = apply_filters('bookingx_block_grid_setting', $args);
$card_width = apply_filters('bookingx_card_width_setting', $args);
?>
<div class="<?php echo $settings['class'];?> ">
    <div class="card<?php echo $settings['block'];?>" style="width: <?php echo $card_width;?>">
        <?php echo $bkx_seat->get_thumb();?>
        <div class="card-body">
            <h5 class="card-title m-0 text-center"><a href="<?php echo get_permalink($seat_id);?>"><?php echo get_the_title($seat_id); ?></a></h5>
            <p class="card-text"><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
            <div class="text-center"><a href="<?php echo $booking_url;?>" class="btn btn-primary">Book now</a></div>
        </div>
    </div>
</div>
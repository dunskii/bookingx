<?php
defined( 'ABSPATH' ) || exit;
global $bkx_seat;
$booking_url = $bkx_seat->booking_page_url;
$settings = apply_filters('bookingx_block_grid_setting', $args);
?>
<div class="<?php echo $settings['class'];?> ">
    <div class="card<?php echo $settings['block'];?>" style="width: 18rem;">
        <?php echo $bkx_seat->get_thumb();?>
        <div class="card-body">
            <h5 class="card-title"><?php echo get_the_title(); ?></h5>
            <p class="card-text"><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
            <a href="<?php echo $booking_url;?>" class="btn btn-primary">Book now</a>
        </div>
    </div>
</div>
<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bkx_addition;
$alias_base = bkx_crud_option_multisite('bkx_alias_base');
$alias_extra = bkx_crud_option_multisite('bkx_alias_addition');
$alias_seat = bkx_crud_option_multisite('bkx_alias_seat');

$booking_url = $bkx_addition->booking_page_url;
$extra_id = $bkx_addition->id;
$price_duration = bkx_get_post_price_duration_plain( $bkx_addition, $alias_base );
$BaseObj = new BkxBase();
if(isset($base_id) && $base_id !=""){
    $get_seat_by_base = $BaseObj->get_seat_by_base($base_id);
}
if(isset($extra_id) && $extra_id !=""){
    $get_base_by_extra = $BaseObj->get_base_by_extra($extra_id);
    $BkxSeatObj = new BkxSeat();
    $get_seat_by_extra = $BkxSeatObj->get_seat_by_extra( $extra_id );
}
$available_seats = bkx_get_post_with_price_duration( $get_seat_by_extra, $alias_seat, 'seat' );
$available_base = bkx_get_post_with_price_duration( $get_base_by_extra, $alias_base, 'base' );
$image = $desc = "yes";
if(!empty($args)){
    $desc  = $args['description'];
    $image  = $args['image'];
}
?>
<div class="bkx-single-post-view clearfix">
    <div class="container">
        <div class="row">
            <?php if($image == "yes") :?>
            <div class="col-md-4">
                <?php echo $bkx_addition->get_thumb();?>
            </div>
            <?php endif; ?>
            <div class="col-md-<?php echo ($image == "yes" ) ? 8 : 12;?>">
                <div class="row">
                    <div class="col-md-8"><h1><?php echo get_the_title($extra_id);  ?></h1><h4><?php echo "{$price_duration['time']} {$price_duration['price']}";?></h4></div>
                    <div class="col-md-4">
                        <form method="post" enctype='multipart/form-data' action="<?php echo $booking_url;?>">
                            <input type="hidden" name="type" value="bkx_base" />
                            <input type="hidden" name="id" value="<?php echo esc_attr( $extra_id ); ?>" />
                            <button type="submit" class="btn btn-primary"><?php esc_html_e( 'Book now', 'bookingx' );; ?></button>
                        </form>
                    </div>
                </div>
                <hr/>
                <?php if($desc == "yes" ) :?>
                <div class="row"><div class="col-md-12"><?php echo apply_filters( 'the_content', get_the_content($extra_id) );?></div></div>
                <?php endif;?>
                <?php if(!empty($available_seats)):?>
                    <div class="available-seats"><?php echo $available_seats; ?></div>
                <?php endif;?>
                <?php if(!empty($available_base)):?>
                    <div class="available-services"><?php echo $available_base; ?></div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
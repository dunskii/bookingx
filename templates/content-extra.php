<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $bkx_addition;
$alias_base = bkx_crud_option_multisite('bkx_alias_base');
$alias_extra = bkx_crud_option_multisite('bkx_alias_addition');
$alias_seat = bkx_crud_option_multisite('bkx_alias_seat');


$booking_url = $bkx_addition->booking_page_url;
$extra_id = $bkx_addition->id;
$price_duration = get_post_price_duration_plain( $bkx_addition, $alias_base );

$BaseObj = new BkxBase();
$get_seat_by_base = $BaseObj->get_seat_by_base($base_id);
$get_base_by_extra = $BaseObj->get_base_by_extra($extra_id);


$BkxSeatObj = new BkxSeat();
$get_seat_by_extra = $BkxSeatObj->get_seat_by_extra( $extra_id );


$available_seats = get_post_with_price_duration( $get_seat_by_extra, $alias_seat, 'seat' );
$available_base = get_post_with_price_duration( $get_base_by_extra, $alias_base, 'base' );
?>
<div class="bkx-single-post-view clearfix">

	<div class="section-1">

		<?php echo $bkx_addition->get_thumb();?>
	</div>

	<div class="section-2 ">
			<div class="header-left"><h2><?php echo get_the_title().' - '.$price_duration['time'].' - '.$price_duration['price'];  ?></h2></div>
			<!-- <div class="header-right"> 
				<form method="post" enctype='multipart/form-data' action="<?php //echo $booking_url;?>">
					 	<input type="hidden" name="type" value="bkx_base" />
				                <input type="hidden" name="id" value="<?php //echo esc_attr( $base_id ); ?>" />
					 	<button type="submit" class="small-button smallblue alt"><?php //esc_html_e( 'Book now', 'bookingx' );; ?></button>
				</form>
			</div> -->
			<div class="clearfix"></div>
			<div class="descriptions businessHours"><p><?php echo get_the_content(); ?></p></div>

			<?php if(!empty($available_seats)):?>
				<div class="available-services businessHours"><?php echo $available_seats; ?></div>
			<?php endif;?>

			<?php if(!empty($available_base)):?>
				<div class="available-extras businessHours"><?php echo $available_base; ?></div>
			<?php endif;?>
	</div>
	
	<div class="clearfix"></div>
	<div class="col-4"></div>
</div>
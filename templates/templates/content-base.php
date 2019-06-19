<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bkx_base;
$alias_base = bkx_crud_option_multisite('bkx_alias_base');
$alias_extra = bkx_crud_option_multisite('bkx_alias_addition');
$alias_seat = bkx_crud_option_multisite('bkx_alias_seat');


$booking_url = $bkx_base->booking_page_url;
$base_id = $bkx_base->id;
$BaseObj = new BkxBase();
$price_duration = bkx_get_post_price_duration_plain( $bkx_base, $alias_base );
$get_seat_by_base = $BaseObj->get_seat_obj_by_base($base_id);

$BkxExtraObj = new BkxExtra();
$get_extra_by_base = $BkxExtraObj->get_extra_by_base( $base_id );

$available_seats = bkx_get_post_with_price_duration( $get_seat_by_base, $alias_seat );
$available_extras = bkx_get_post_with_price_duration( $get_extra_by_base, $alias_extra );
?>
<div class="bkx-single-post-view clearfix">

	<div class="section-1">

		<?php echo $bkx_base->get_thumb();?>
	</div>

	<div class="section-2 ">
			<div class="header-left"><h2><?php echo get_the_title().' - '.$price_duration['time'].' - '.$price_duration['price'];  ?></h2></div>
			<div class="header-right"> 
				<form method="post" enctype='multipart/form-data' action="<?php echo $booking_url;?>">
					 	<input type="hidden" name="type" value="bkx_base" />
				                <input type="hidden" name="id" value="<?php echo esc_attr( $base_id ); ?>" />
					 	<button type="submit" class="small-button smallblue alt"><?php esc_html_e( 'Book now', 'bookingx' );; ?></button>
				</form>
			</div>
			<div class="clearfix"></div>
			<div class="descriptions businessHours"><p><?php echo get_the_content(); ?></p></div>

			<?php if(!empty($available_seats)):?>
				<div class="available-services businessHours"><?php echo $available_seats; ?></div>
			<?php endif;?>

			<?php if(!empty($available_extras)):?>
				<div class="available-extras businessHours"><?php echo $available_extras; ?></div>
			<?php endif;?>

			<div class="meta-data businessHours"><?php bkx_get_template( 'bkx-single/meta/base.php' );?></div>
			<div class="add-to-booking">
			<form method="post" enctype='multipart/form-data' action="<?php echo $booking_url;?>">
				 	<input type="hidden" name="type" value="bkx_seat" />
			                <input type="hidden" name="id" value="<?php echo esc_attr( $seat_id ); ?>" />
				 	<button type="submit" class="small-button smallblue alt"><?php esc_html_e( 'Book now', 'bookingx' );; ?></button>
			</form>
			</div>
	</div>
	
	<div class="clearfix"></div>
	<div class="col-4"></div>
</div>
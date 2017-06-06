<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $bkx_seat;
$booking_url = $bkx_seat->booking_page_url;
$seat_id = $bkx_seat->id;
$BaseObj = new BkxBase();
$get_base_by_seat = $BaseObj->get_base_by_seat( get_the_ID() );


$BkxExtraObj = new BkxExtra();
$get_extra_by_seat = $BkxExtraObj->get_extra_by_seat( $get_base_by_seat );

$alias_base = crud_option_multisite('bkx_alias_base');
$alias_extra = crud_option_multisite('bkx_alias_addition');
$available_services = get_post_with_price_duration( $get_base_by_seat, $alias_base );
$available_extras = get_post_with_price_duration( $get_extra_by_seat, $alias_extra );

$personal_info = $bkx_seat->seat_personal_info;
$seat_available_months = $bkx_seat->seat_available_months;
$seat_available_days = $bkx_seat->seat_available_days;
$seat_notifiaction_info = $bkx_seat->seat_notifiaction_info;
$seat_payment_info = $bkx_seat->seat_payment_info;

 
?>
<div class="bkx-single-post-view clearfix">

	<div class="section-1">

		<?php echo $bkx_seat->get_thumb();?>
	</div>

	<div class="section-2 ">
			<div class="header-left"><h2><?php echo get_the_title(); ?></h2></div>
			<div class="header-right"> 
				<form method="post" enctype='multipart/form-data' action="<?php echo $booking_url;?>">
					 	<input type="hidden" name="type" value="bkx_seat" />
				                <input type="hidden" name="id" value="<?php echo esc_attr( $seat_id ); ?>" />
					 	<button type="submit" class="small-button smallblue alt"><?php esc_html_e( 'Book now', 'bookingx' );; ?></button>
				</form>
			</div>
			<div class="clearfix"></div>
			<div class="descriptions businessHours"><p><?php echo get_the_content(); ?></p></div>
			<?php if(!empty($available_services)):?>
				<div class="available-services businessHours"><?php echo $available_services; ?></div>
			<?php endif;?>

			<?php if(!empty($available_extras)):?>
				<div class="available-extras businessHours"><?php echo $available_extras; ?></div>
			<?php endif;?>

			<?php if(!empty($seat_available_months) || !empty($seat_available_days) || !empty($seat_payment_info)):?>
				<div class="meta-data businessHours"><?php bkx_get_template( 'bkx-single/meta/seat.php' );?></div>
			<?php endif;?>
			
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
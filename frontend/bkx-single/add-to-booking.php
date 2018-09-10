<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $bkx_seat,$bkx_base,$bkx_addition;

if(!empty($bkx_seat)){
	$type= $bkx_seat->bkx_post_type;
	$booking_url = $bkx_seat->booking_page_url;
	$id = $bkx_seat->id;
}
if(!empty($bkx_base)){
	$type= $bkx_base->bkx_post_type;
	$booking_url = $bkx_base->booking_page_url;
	$id = $bkx_base->id;
	$get_price = $bkx_base->get_price();
}

if(!empty($bkx_addition)){
	$type= $bkx_addition->bkx_post_type;
	$booking_url = $bkx_addition->booking_page_url;
	$id = $bkx_addition->id;
}

?>
<div class="col-3">
<?php do_action( 'bookingx_before_add_to_booking_form' ); ?>
<form class="booking" method="post" enctype='multipart/form-data' action="<?php echo $booking_url;?>">
	 	<?php do_action( 'bookingx_before_add_to_booking_button' ); ?>
	 	<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
                <input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	 	<button type="submit" class="small-button smallblue alt"><?php esc_html_e( 'Book now', 'bookingx' );; ?></button>
		<?php do_action( 'bookingx_after_add_to_booking_button' ); ?>
</form>
<?php do_action( 'bookingx_after_add_to_booking_form' ); ?>
</div>
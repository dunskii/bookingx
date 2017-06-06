<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $bkx_seat,$bkx_base,$bkx_addition;
$duration_text = $currencyBlock = '';
$currency_option = (crud_option_multisite('currency_option') ? crud_option_multisite('currency_option') : 'AUD' );

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
	$Duration = $bkx_base->get_service_time();
	$DurationHMObj = $Duration['H'];
	$DurationDObj = $Duration['D'];

	if(!empty($DurationHMObj)):
    $hours  = isset($DurationHMObj['hours']) && $DurationHMObj['hours'] > 0 && $DurationHMObj['hours']!='' ? $DurationHMObj['hours'].' Hours ' : '';
    $mins   = isset($DurationHMObj['minutes'])  && $DurationHMObj['minutes'] > 0 && $DurationHMObj['minutes']!='' ? $DurationHMObj['minutes'].' Minutes' : '';
    $duration_text = " - ".$hours.' '.$mins;
	endif;

	if(!empty($DurationDObj)):
	    $day    = isset($DurationDObj['days']) && $DurationDObj['days']!='' ? $DurationDObj['days'].' Days ' : '';
	    $duration_text = $day;
	endif;

	if(!empty($bkx_base->get_price())) :
		$currencyBlock = get_formatted_price($bkx_base->get_price());
	endif;
}

if(!empty($bkx_addition)){
	$type= $bkx_addition->bkx_post_type;
	$booking_url = $bkx_addition->booking_page_url;
	$id = $bkx_addition->id;
	$Duration = $bkx_addition->get_extra_time();
	$DurationHMObj = $Duration['H'];
	$DurationDObj = $Duration['D'];

	if(!empty($DurationHMObj)):
    $hours  = isset($DurationHMObj['hours']) && $DurationHMObj['hours'] > 0 && $DurationHMObj['hours']!='' ? $DurationHMObj['hours'].' Hours ' : '';
    $mins   = isset($DurationHMObj['minutes'])  && $DurationHMObj['minutes'] > 0 && $DurationHMObj['minutes']!='' ? $DurationHMObj['minutes'].' Minutes' : '';
    $duration_text = " - ".$hours.' '.$mins;
	endif;

	if(!empty($DurationDObj)):
	    $day    = isset($DurationDObj['days']) && $DurationDObj['days']!='' ? $DurationDObj['days'].' Days ' : '';
	    $duration_text = $day;
	endif;

	if(!empty($bkx_addition->get_price())) :
		$currencyBlock = ' - '. get_formatted_price($bkx_addition->get_price());
	endif;
}
$num_words =  apply_filters( 'bkx_excerpt_num_words', 15);
$trimmed_content = wp_trim_words( get_the_excerpt(), $num_words, $more = null );

echo '<div class="col-2"><span itemprop="name" class="serviceName"><a href="'.get_permalink().'" ><em class="title">'.get_the_title().''.$duration_text.''.$currencyBlock.'</em></a></span><div class="clearfix"></div>'.$trimmed_content.'</div>';



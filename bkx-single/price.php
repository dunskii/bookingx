<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $bkx_base,$bkx_seat;

if(!empty($bkx_base)):
$format = '<div class="price"><label>%s</label><span class="currency">$</span><span class="price-val">%d</span></div>';
//echo sprintf($format,'Price : ',$bkx_base->get_price());
endif;
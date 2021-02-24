<?php
/**
 * BookingX Single Post Price Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
global $bkx_base, $bkx_seat;
if ( ! empty( $bkx_base ) ) :
	$format = '<div class="price"><label>%s</label><span class="currency">$</span><span class="price-val">%d</span></div>';
endif;

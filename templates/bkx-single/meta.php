<?php
/**
 * BookingX Single Post Meta Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
global $bkx_seat, $bkx_base;
if ( ! empty( $bkx_seat ) ) :
	bkx_get_template( 'bkx-single/meta/seat.php' );
endif;
if ( ! empty( $bkx_base ) ) :
	bkx_get_template( 'bkx-single/meta/base.php' );
endif;

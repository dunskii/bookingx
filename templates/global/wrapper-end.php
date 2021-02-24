<?php
/**
 * BookingX Content wrappersEend Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;

$template = get_option( 'template' );

switch ( $template ) {

	case 'twentyeleven':
		echo '</div>';
		get_sidebar( '' );
		echo '</div>';
		break;

	case 'twentytwelve':
		echo '</div></div>';
		break;

	case 'twentythirteen ':
		echo '</div></div>';
		break;

	case 'twentyfourteen':
		echo '</div></div></div>';
		get_sidebar( '' );
		break;
	case 'twentyfifteen':
		echo '</div></div>';
		break;
	case 'twentysixteen':
		echo '</main></div>';
		break;
	default:
		echo '</div></div>';
		break;
}

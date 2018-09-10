<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Create a Shortcode for Showing Front side Bookings
 */
function bkx_my_account_view()
{
   if(!is_user_logged_in()):
       $the_login_page = get_page_by_title("Login Here");
       $login_page_id = $the_login_page->ID;
       $login_url = get_permalink($login_page_id);
       echo sprintf( __( '<p>You need to login first , <a href="%1$s"> Click here to login </a> </p>', 'bookingx' ), $login_url);
   else:
       require(BKX_PLUGIN_DIR_PATH.'frontend/customer-bookings.php');
   endif;

}
add_shortcode('bkx_my_account_view', 'bkx_my_account_view');
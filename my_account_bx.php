<?php
/**
 * Created by IndiaNic.
 * User: Divyang Parekh
 * Date: 3/11/15
 * Time: 12:21 PM
 */
function my_account_bx()
{
   if(!is_user_logged_in()):
       $the_login_page = get_page_by_title("Login Here");
       $login_page_id = $the_login_page->ID;
       $login_url = get_permalink($login_page_id);
        echo 'You need to login first.<a href="'.$login_url.'">Login here</a>';
   else:
       require(PLUGIN_DIR_PATH.'customer-bookings.php');
   endif;

}
add_shortcode('my_account_bx', 'my_account_bx');
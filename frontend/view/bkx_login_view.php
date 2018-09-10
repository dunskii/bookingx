<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/***
 * Create Shorcode for user Login view
 * @return string|void
 */
function bkx_login_view()
{
    ob_start();
    $the_account_page = get_page_by_title("My Account");
    $the_account_page_id= $the_account_page->ID;
    if($the_account_page_id!=''):$the_account_page_link = get_permalink($the_account_page_id);endif;
        $args = array(
            'echo'           => false,
            'remember'       => true,
            'redirect'       => $the_account_page_link,
            'form_id'        => 'loginform',
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'wp-submit',
            'label_username' => __( 'Username' ),
            'label_password' => __( 'Password' ),
            'label_remember' => __( 'Remember Me' ),
            'label_log_in'   => __( 'Log In' ),
            'value_username' => '',
            'value_remember' => false
        );
      return  wp_login_form($args);
      
}
add_shortcode('bkx_login_view', 'bkx_login_view');

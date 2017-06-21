<?php
/**
 * Created by IndiaNic.
 * User: Divyang Parekh
 * Date: 3/11/15
 * Time: 12:21 PM
 */

function login_customer_bx()
{
    ob_start();

    $the_account_page = get_page_by_title("My Account");
    $the_account_page_id= $the_account_page->ID;
    if($the_account_page_id!=''):$the_account_page_link = get_permalink($the_account_page_id);endif;
        $args = array(
            'echo'           => true,
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
    wp_login_form($args);

    ob_clean();
}
add_shortcode('login_customer_bx', 'login_customer_bx');

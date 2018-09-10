<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Example of wpmu_new_blog usage
 *
 * @param int $blog_id Blog ID.
 * @param int $user_id User ID.
 * @param string $domain Site domain.
 * @param string $path Site path.
 * @param int $site_id Site ID. Only relevant on multi-network installs.
 * @param array $meta Meta data. Used to set initial site options.
 */

add_action('wpmu_new_blog', 'generate_while_multisite_creating');

function generate_while_multisite_creating($blog_id, $user_id, $domain, $path, $site_id, $meta)
{

    switch_to_blog($blog_id);

    $the_page_title = 'Booking Form';

    add_blog_option( $blog_id, 'bkx_alias_seat', 'Resource');
    add_blog_option( $blog_id, 'bkx_alias_base', 'Service');
    add_blog_option( $blog_id, 'bkx_alias_addition', 'Extra');
    add_blog_option( $blog_id, 'bkx_alias_notification', 'Notification');
    add_blog_option( $blog_id, 'bkx_notice_time_extended_text_alias', 'How many times you want to extend this service?');
    add_blog_option( $blog_id, 'bkx_label_of_step1', 'Please select what you would like to book');

    add_blog_option( $blog_id, "bkx_siteclient_css_text_color", '40120a');
    add_blog_option( $blog_id, "bkx_siteclient_css_background_color", 'f0e8e7');
    add_blog_option( $blog_id, "bkx_siteclient_css_border_color", '1f191f');
    add_blog_option( $blog_id, "bkx_siteclient_css_progressbar_color", '875428');

    // Create post object
    $_p = array();
    $_p['post_title'] = $the_page_title;
    $_p['post_content'] = "[bookingform]";
    $_p['post_status'] = 'Publish';
    $_p['post_type'] = 'page';
    $_p['comment_status'] = 'closed';
    $_p['ping_status'] = 'closed';
    $_p['post_category'] = array(1); // the default 'Uncatrgorised'

    // Insert the post into the database
    $the_page_id = wp_insert_post($_p);

    update_blog_option($blog_id, 'bkx_set_booking_page', $the_page_id);

    /**Added By : Divyang Parekh
     * For : Create Page For Login and Customer Booking List
     */
    $_login = array();
    $_login['post_title'] = 'Login Here';
    $_login['post_content'] = "[bkx_login_view]";
    $_login['post_status'] = 'Publish';
    $_login['public'] = true;
    $_login['post_type'] = 'page';
    $_login['comment_status'] = 'closed';
    $_login['ping_status'] = 'closed';
    $_login['post_category'] = array(1); // the default 'Uncatrgorised'

    // Insert the post into the database
    wp_insert_post($_login);

    $_my_account = array();
    $_my_account['post_title'] = 'My Account';
    $_my_account['post_content'] = "[bkx_my_account_view]";
    $_my_account['post_status'] = 'Publish';
    $_my_account['public'] = true;
    $_my_account['post_type'] = 'page';
    $_my_account['comment_status'] = 'closed';
    $_my_account['ping_status'] = 'closed';
    $_my_account['post_category'] = array(1); // the default 'Uncatrgorised'

    // Insert the post into the database
    wp_insert_post($_my_account);
    add_blog_option($blog_id, 'bkx_plugin_page_id', $the_page_id);
    restore_current_blog();
}
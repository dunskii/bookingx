<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Example of wpmu_new_blog usage
 * 
 * @param int    $blog_id Blog ID.
 * @param int    $user_id User ID.
 * @param string $domain  Site domain.
 * @param string $path    Site path.
 * @param int    $site_id Site ID. Only relevant on multi-network installs.
 * @param array  $meta    Meta data. Used to set initial site options.
 */

	add_action( 'wpmu_new_blog', 'generate_while_multisite_creating' );

	 function generate_while_multisite_creating( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	 	switch_to_blog( $blog_id );

	 	$the_page_title = 'Your Booking Form Here';


 		add_blog_option('bkx_alias_seat','Resource', '', 'yes');
		add_blog_option('bkx_alias_base','Service', '', 'yes');
		add_blog_option('bkx_alias_addition','Extra', '', 'yes');
		add_blog_option('bkx_alias_notification','Notification', '', 'yes');
			
		add_blog_option("bkx_siteclient_css_text_color", '40120a', '', 'yes');
		add_blog_option("bkx_siteclient_css_background_color", 'f0e8e7', '', 'yes');
		add_blog_option("bkx_siteclient_css_border_color", '1f191f', '', 'yes');
		add_blog_option("bkx_siteclient_css_progressbar_color", '875428', '', 'yes');

    	// the menu entry...
	    add_blog_option("my_plugin_page_title", $the_page_title, '', 'yes');
	    // the slug...
	    add_blog_option("my_plugin_page_name", $the_page_name, '', 'yes');
	    // the id...
	    add_blog_option("my_plugin_page_id", '0', '', 'yes');


        // Create post object
        $_p = array();
        $_p['post_title']     = $the_page_title;
        $_p['post_content']   = "[bookingform]";
        $_p['post_status']    = 'Publish';
        $_p['post_type']      = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status']    = 'closed';
        $_p['post_category']  = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

        update_blog_option($blog_id,'bkx_set_booking_page',$the_page_id);
      
	      /**Added By : Divyang Parekh
	       * Date : 24-11-2016
	       * For : Create Page For Login and Customer Booking List
	       */
 
	      $_login = array();
	      $_login['post_title']     = 'Login Here';
	      $_login['post_content']   = "[login_customer_bx]";
	      $_login['post_status']    = 'Publish';
	      $_login['public']         =  true;
	      $_login['post_type']      = 'page';
	      $_login['comment_status'] = 'closed';
	      $_login['ping_status']    = 'closed';
	      $_login['post_category']  = array(1); // the default 'Uncatrgorised'

      // Insert the post into the database
	     $_login_page_id = wp_insert_post( $_login );

	      $_my_account = array();
	      $_my_account['post_title']     = 'My Account';
	      $_my_account['post_content']   = "[my_account_bx]";
	      $_my_account['post_status']    = 'Publish';
	      $_my_account['public']         = true;
	      $_my_account['post_type']      = 'page';
	      $_my_account['comment_status'] = 'closed';
	      $_my_account['ping_status']    = 'closed';
	      $_my_account['post_category']  = array(1); // the default 'Uncatrgorised'

      // Insert the post into the database
      $_my_account_page_id = wp_insert_post( $_my_account );

	add_blog_option( $blog_id,'my_plugin_page_id', $the_page_id );

    restore_current_blog();
		 
	}
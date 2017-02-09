<?php

/** Single Page ********************************************************/

if ( ! function_exists( 'bookingx_show_post_images' ) ) {
	function bookingx_show_post_images($data) {
            if(is_single() || (!empty($data) && strtolower($data['image']) =='yes' && $data['image']!=''))
            {
                bkx_get_template( 'bkx-single/image.php' ); 
            }
            return;
	}
}

//if ( ! function_exists( 'bookingx_show_post_thumbnails' ) ) {
//
//	/**
//	 * Output the post thumbnails.
//	 *
//	 * @subpackage	Post
//	 */
//	function bookingx_show_post_thumbnails() {
//		bkx_get_template( 'bkx-single/post-thumbnails.php' );
//	}
//}
//if ( ! function_exists( 'bookingx_output_post_data_tabs' ) ) {
//
//	/**
//	 * Output the post tabs.
//	 *
//	 * @subpackage	Post/Tabs
//	 */
//	function bookingx_output_post_data_tabs() {
//		bkx_get_template( 'bkx-single/tabs/tabs.php' );
//	}
//}
if ( ! function_exists( 'bookingx_template_single_title' ) ) {

	/**
	 * Output the post title.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_title($data) {
		bkx_get_template( 'bkx-single/title.php' );
	}
}
//if ( ! function_exists( 'bookingx_template_single_rating' ) ) {
//
//	/**
//	 * Output the post rating.
//	 *
//	 * @subpackage	Post
//	 */
//	function bookingx_template_single_rating() {
//		bkx_get_template( 'bkx-single/rating.php' );
//	}
//}
if ( ! function_exists( 'bookingx_template_single_price' ) ) {

	/**
	 * Output the post price.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_price($data) {
		bkx_get_template( 'bkx-single/price.php' );
	}
}
if ( ! function_exists( 'bookingx_template_single_excerpt' ) ) {

	/**
	 * Output the post short description (excerpt).
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_excerpt($data) {
            if(is_single() || (!empty($data) && strtolower($data['extra-info']) =='yes' && $data['extra-info']!=''))
            {
                bkx_get_template( 'bkx-single/short-description.php' );
            }
		
	}
}
if ( ! function_exists( 'bookingx_template_single_meta' ) ) {

	/**
	 * Output the post meta.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_meta($data) {
            if(!is_single())
                return;
	    bkx_get_template( 'bkx-single/meta.php' );
	}
}
if ( ! function_exists( 'bookingx_template_single_descriptions' ) ) {

	/**
	 * Output the post sharing.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_descriptions($data) {
            
           if(is_single() || (!empty($data) && strtolower($data['description']) =='yes' && $data['description']!=''))
           {
               bkx_get_template( 'bkx-single/descriptions.php' );
           }	
	}
}

if(!function_exists('bookingx_template_single_booking_url')){
    /**
     * 
     */
    function bookingx_template_single_booking_url() {
		bkx_get_template( 'bkx-single/add-to-booking.php' );
	}
}

if(!function_exists('bookingx_template_single_pagination')){
    /**
     * 
     */
    function bookingx_template_single_pagination($data) {
        if(!is_single())
                return;
		bkx_get_template( 'bkx-single/pagination.php' );
	}
}

/** global ****************************************************************/
if ( ! function_exists( 'bookingx_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function bookingx_output_content_wrapper($data) {
		bkx_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'bookingx_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function bookingx_output_content_wrapper_end($data) {
		bkx_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'bookingx_get_sidebar' ) ) {

	/**
	 * Get the Service sidebar template.
	 *
	 */
	function bookingx_get_sidebar($data) {
		bkx_get_template( 'global/sidebar.php' );
	}
}


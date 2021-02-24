<?php defined( 'ABSPATH' ) || exit;
/**
 * Content Wrappers.
 *
 * @see bookingx_output_content_wrapper()
 * @see bookingx_output_content_wrapper_end()
 */
add_action( 'bookingx_before_main_content', 'bookingx_output_content_wrapper', 10 );
add_action( 'bookingx_after_main_content', 'bookingx_output_content_wrapper_end', 10 );

/**
 * Breadcrumbs.
 *
 * @see bookingx_breadcrumb()
 */
add_action( 'bookingx_before_main_content', 'bookingx_breadcrumb', 20, 0 );

/**
 * Before Single Posts Summary Div.
 *
 * @see bookingx_show_post_images()
 * @see bookingx_show_post_thumbnails()
 */
add_action( 'bookingx_before_single_post_summary', 'bookingx_show_post_images', 20, 2 );

add_action( 'bookingx_single_post_summary', 'bookingx_template_single_title', 5, 2 );
add_action( 'bookingx_single_post_summary', 'bookingx_template_single_price', 10, 2 );
add_action( 'bookingx_single_post_summary', 'bookingx_template_single_descriptions', 10, 2 );
add_action( 'bookingx_single_post_summary', 'bookingx_template_single_excerpt', 20, 2 );
add_action( 'bookingx_single_post_summary', 'bookingx_template_single_meta', 40, 2 );


/**
 * After Single Posts Summary Div.
 *
 * @see bookingx_output_post_data_tabs()
 * @see bookingx_upsell_display()
 * @see bookingx_output_related_posts()
 */
add_action( 'bookingx_after_single_post_summary', 'bookingx_template_single_booking_url', 20, 2 );
add_action( 'bookingx_after_single_post_summary', 'bookingx_template_single_pagination', 20, 2 );

/**
 * Sidebar.
 *
 * @see bookingx_get_sidebar()
 */
add_action( 'bookingx_sidebar', 'bookingx_get_sidebar', 10 );

/**
 * Block hooks for Column Settings
 */
add_filter( 'bookingx_block_grid_setting', 'bookingx_block_grid_setting', 10 );
add_filter( 'bookingx_card_width_setting', 'bookingx_card_width_setting', 10 );

add_action( 'bookingx_base_meta_data', 'bookingx_base_meta_data_html', 10 );

add_action( 'bookingx_form_additional_fields', 'bookingx_form_additional_fields', 10 );

add_action( 'bkx_dashboard_bookings_nav', 'bkx_dashboard_bookings_nav', 10 );
add_action( 'bkx_dashboard_booking_content', 'bkx_dashboard_booking_content', 10 );
add_action( 'bkx_dashboard_booking_view', 'bkx_dashboard_booking_view', 10 );
add_action( 'bkx_dashboard_navigation', 'bkx_dashboard_navigation', 10 );
add_action( 'bkx_dashboard_content', 'bkx_dashboard_content', 10 );

add_action( 'bkx_my_account_content', 'bkx_my_account_content', 10 );

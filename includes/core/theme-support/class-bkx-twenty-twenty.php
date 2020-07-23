<?php
/**
 * Twenty Twenty support.
 * A quick shout-out to those who worked on WooCommerce for the inspiration on a good way to handle Theme Support
 *
 */

defined('ABSPATH') || exit;

/**
 * BKX_Twenty_Twenty class.
 */
class BKX_Twenty_Twenty
{

    /**
     * Theme init.
     */
    public static function init()
    {

        // Change BookingX wrappers.
        remove_action('bookingx_before_main_content', 'bookingx_output_content_wrapper', 10);
        remove_action('bookingx_after_main_content', 'bookingx_output_content_wrapper_end', 10);

        add_action('bookingx_before_main_content', array(__CLASS__, 'output_content_wrapper'), 10);
        add_action('bookingx_after_main_content', array(__CLASS__, 'output_content_wrapper_end'), 10);

        // This theme doesn't have a traditional sidebar.
        remove_action('bookingx_sidebar', 'bookingx_get_sidebar', 10);

        // Enqueue theme compatibility styles.
        add_filter('bookingx_enqueue_styles', array(__CLASS__, 'enqueue_styles'));

        // Register theme features.
        add_theme_support(
            'bookingx',
            array(
                'thumbnail_image_width' => 300,
                'single_image_width' => 450,
            )
        );

        // Background color change.
        add_action('after_setup_theme', array(__CLASS__, 'set_white_background'), 10);
    }

    /**
     * Open the Twenty Twenty wrapper.
     */
    public static function output_content_wrapper()
    {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    /**
     * Close the Twenty Twenty wrapper.
     */
    public static function output_content_wrapper_end()
    {
        echo '</main>';
        echo '</section>';
    }

    /**
     * Enqueue CSS for this theme.
     *
     * @param array $styles Array of registered styles.
     * @return array
     */
    public static function enqueue_styles($styles)
    {

        $styles['bookingx-twenty-twenty-main'] = array(
            'src' => str_replace(array('http:', 'https:'), '', BKX_PLUGIN_PUBLIC_URL) . '/css/theme/twenty-twenty.css',
            'deps' => '',
            'version' => BKX_PLUGIN_VER,
            'media' => 'all',
            'has_rtl' => true,
        );

        return apply_filters('bookingx_twenty_twenty_styles', $styles);
    }

    /**
     * @return mixed|void
     */
    public function default_colors(){
        $colors = array('booked' => '#666', 'open' => '#fff', 'current' => '#333', 'selected_time_color' => '#fff');
        return apply_filters('bkx_booking_form_color_schema', $colors);
    }

    /**
     * Set background color to white if it's default, otherwise don't touch it.
     */
    public static function set_white_background()
    {
        $background = sanitize_hex_color_no_hash(get_theme_mod('background_color'));
        $background_default = 'f5efe0';

        // Don't change user's choice of background color.
        if (!empty($background) && $background !== $background_default) {
            return;
        }

        // In case default background is found, change it to white.
        set_theme_mod('background_color', 'fff');
    }
}

if(!is_admin()){
    BKX_Twenty_Twenty::init();
}
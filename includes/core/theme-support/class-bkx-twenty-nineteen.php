<?php
/**
 * Twenty Nineteen support.
 * A quick shout-out to those who worked on WooCommerce for the inspiration on a good way to handle Theme Support
 *
 */

defined('ABSPATH') || exit;

/**
 * BKX_Twenty_Nineteen class.
 */
class BKX_Twenty_Nineteen
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

        // Tweak Twenty Nineteen features.
        add_action('wp', array(__CLASS__, 'tweak_theme_features'));
    }

    /**
     * Open the Twenty Nineteen wrapper.
     */
    public static function output_content_wrapper()
    {
        echo '<section id="primary" class="content-area">';
        echo '<main id="main" class="site-main">';
    }

    /**
     * Close the Twenty Nineteen wrapper.
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

        $styles['bookingx-twenty-nineteen-main'] = array(
            'src' => str_replace(array('http:', 'https:'), '', BKX_PLUGIN_PUBLIC_URL) . '/css/theme/twenty-nineteen.css',
            'deps' => '',
            'version' => BKX_PLUGIN_VER,
            'media' => 'all',
            'has_rtl' => true,
        );

        return apply_filters('bookingx_twenty_nineteen_styles', $styles);
    }

    /**
     * Tweak Twenty Nineteen features.
     */
    public static function tweak_theme_features()
    {
        if (is_bookingx()) {
            add_filter('twentynineteen_can_show_post_thumbnail', '__return_false');
        }
    }
}

BKX_Twenty_Nineteen::init();
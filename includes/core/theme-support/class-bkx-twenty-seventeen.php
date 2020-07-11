<?php
/**
 * Twenty Seventeen support.
 * A quick shout-out to those who worked on WooCommerce for the inspiration on a good way to handle Theme Support
 *
 */

defined('ABSPATH') || exit;

/**
 * BKX_Twenty_Seventeen class.
 */
class BKX_Twenty_Seventeen
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
        // Enqueue theme compatibility styles.
        add_filter('bookingx_enqueue_styles', array(__CLASS__, 'enqueue_styles'));
        add_filter('twentyseventeen_custom_colors_css', array(__CLASS__, 'theme_custom_colors_css'), 10, 3);

        // Register theme features.
        add_theme_support(
            'bookingx',
            array(
                'thumbnail_image_width' => 300,
                'single_image_width' => 450,
            )
        );
    }

    /**
     * Open the Twenty Twenty wrapper.
     */
    public static function output_content_wrapper()
    {
        echo '<div class="wrap">';
        echo '<div id="primary" class="content-area twentyseventeen">';
        echo '<main id="main" class="site-main" role="main">';
    }

    /**
     * Close the Twenty Twenty wrapper.
     */
    public static function output_content_wrapper_end()
    {
        echo '</main>';
        echo '</div>';
        get_sidebar();
        echo '</div>';
    }

    /**
     * Enqueue CSS for this theme.
     *
     * @param array $styles Array of registered styles.
     * @return array
     */
    public static function enqueue_styles($styles)
    {
        $styles['bookingx-twenty-seventeen-main'] = array(
            'src' => str_replace(array('http:', 'https:'), '', BKX_PLUGIN_PUBLIC_URL) . '/css/theme/twenty-seventeen.css',
            'deps' => '',
            'version' => BKX_PLUGIN_VER,
            'media' => 'all',
            'has_rtl' => true,
        );

        return apply_filters('bookingx_twenty_seventeen_styles', $styles);
    }

    /**
     * @param $css
     * @param $hue
     * @param $saturation
     * @return string
     */
    public static function theme_custom_colors_css($css, $hue, $saturation)
    {
        $css .= '
			.colors-custom .select2-container--default .select2-selection--single {
				border-color: hsl( ' . $hue . ', ' . $saturation . ', 73% );
			}
			.colors-custom .select2-container--default .select2-selection__rendered {
				color: hsl( ' . $hue . ', ' . $saturation . ', 40% );
			}
			.colors-custom .select2-container--default .select2-selection--single .select2-selection__arrow b {
				border-color: hsl( ' . $hue . ', ' . $saturation . ', 40% ) transparent transparent transparent;
			}
			.colors-custom .select2-container--focus .select2-selection {
				border-color: #000;
			}
			.colors-custom .select2-container--focus .select2-selection--single .select2-selection__arrow b {
				border-color: #000 transparent transparent transparent;
			}
			.colors-custom .select2-container--focus .select2-selection .select2-selection__rendered {
				color: #000;
			}
		';
        return $css;
    }


}

BKX_Twenty_Seventeen::init();
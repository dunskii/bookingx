<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Blocks
{
    public $plugin_name = 'booking-x';

    public $block_name = 'bkx-booking-form';

    public function __construct() {
        add_filter( 'block_categories', array( $this ,'bkx_block_categories') );
        add_action( 'init', array( $this,'bkx_booking_form_register_block_action' ) );
    }

    public function bkx_booking_form_register_block_action() {
        $script_slug = $this->plugin_name . '-' . $this->block_name;
        $style_slug = $this->plugin_name . '-' . $this->block_name . '-style';
        wp_enqueue_script(
            $script_slug,
            BKX_BLOCKS_ASSETS.'booking-form/block.js',
            [  // Dependencies that will have to be imported on the block JS file
                'wp-blocks', // Required: contains registerBlockType function that creates a block
                'wp-element', // Required: contains element function that handles HTML markup
                'wp-i18n', // contains registerBlockType function that creates a block
                'wp-server-side-render'
            ],
            BKX_PLUGIN_VER
        );

        // The block style
        // It will be loaded on the editor and on the site
        wp_register_style(
            $style_slug,
            BKX_BLOCKS_ASSETS . 'booking-form/css/style.css',
            ['wp-blocks'], // General style
            BKX_PLUGIN_VER
        );

        // Registering the block
        register_block_type(
            "{$this->plugin_name}/{$this->block_name}", // Block name with namespace
            [
                'style' => $style_slug, // General block style slug
                'editor_script' => $script_slug,  // The block script slug
                'render_callback' => [$this, 'bkx_booking_form_render_callback'],
            ]
        );
    }

    function bkx_booking_form_render_callback(){
        ob_start();
        echo do_shortcode('[bookingform]');
        $booking_form = ob_get_clean();
        return "<div class=\"gutenberg-bookingx\">{$booking_form}</div>";
    }

    function bkx_block_categories( $categories ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'booking-x',
                    'title' => __( 'Booking X', 'bookingx' ),
                    'icon'  => 'calendar-alt',
                ),
            )
        );
    }
}
new Bkx_Register_Blocks();
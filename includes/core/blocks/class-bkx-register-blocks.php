<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Blocks
{
    private static $_instance = null;

    public $plugin_name = 'booking-x';

    public $attributes = array();

    public $type = '';

    public $script_handle = array();

    public $style_handle = array();

    public function __construct() {
        add_filter( 'block_categories', array( $this ,'bkx_block_categories') );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_styles' ) );
        $this->register_block_type();
    }

    public function get_type() {

        return $this->type;

    }

    public function register_block_type() {

        register_block_type( $this->get_type(), array(
            'category'  => 'booking-x',
            'style' => $this->style_handle,
            'render_callback' => array( $this, 'render_block' ),
            'editor_script'   => $this->script_handle,
            'attributes'      => $this->attributes,
        ) );

    }

    public function enqueue_scripts() {

        $scripts = $this->getScripts();

        // If no scripts are registered, return.
        if ( empty( $scripts ) ) {
            return;
        }

        // Loop through scripts.
        foreach ( $scripts as $script ) {

            // Prepare parameters.
            $src       = isset( $script['src'] ) ? $script['src'] : false;
            $deps      = isset( $script['deps'] ) ? $script['deps'] : array();
            $version   = isset( $script['version'] ) ? $script['version'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;

            // Enqueue script.
            wp_enqueue_script( $script['handle'], $src, $deps, $version, $in_footer );

        }

    }

    public function getScripts() {

        return array();

    }

    public function enqueue_styles() {

        // Get registered styles.
        $styles = $this->getStyles();


        if ( empty( $styles ) ) {
            return;
        }

        // Loop through styles.
        foreach ( $styles as $style ) {

            // Prepare parameters.
            $src     = isset( $style['src'] ) ? $style['src'] : false;
            $deps    = isset( $style['deps'] ) ? $style['deps'] : array();
            $version = isset( $style['version'] ) ? $style['version'] : false;
            $media   = isset( $style['media'] ) ? $style['media'] : 'all';

            // Enqueue style.
            wp_enqueue_style( $style['handle'], $src, $deps, $version, $media );

        }

    }

    public function getStyles() {

        return array();

    }

    public function render_block( $attributes = array() ) {

        return '';

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
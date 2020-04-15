<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Seat_Block
{
    public $plugin_name = 'booking-x';

    public $block_name = 'bkx-seat-block';

    public $attributes = array(
        'showDesc'      => array( 'type' => 'boolean' ),
        'showImage'     => array( 'type' => 'boolean' ),
        //'showExtra'     => array( 'type' => 'boolean' ),
        'seatPostId'    => array( 'type' => 'string' ),
        'seatPosts'     => array( 'type' => 'object' ),
        'columns'       => array( 'type' => 'integer' ),
        'rows'          => array( 'type' => 'integer' ),

    );

    public function __construct() {
        add_action( 'init', array( $this,'bkx_seat_register_block_action' ) );
    }

    public function bkx_seat_register_block_action() {

        $script_slug = $this->plugin_name . '-' . $this->block_name;
        $bootstrap_script = $this->plugin_name . '-' . $this->block_name . '-bootstrap-script';
        $style_slug = $this->plugin_name . '-' . $this->block_name . '-style-block';
        $bootstrap_style = $this->plugin_name . '-' . $this->block_name . '-bootstrap-style';
        $style_main = $this->plugin_name . '-' . $this->block_name . '-style-main';

        $Bkx_Script_Loader = new Bkx_Script_Loader();
        $Bkx_Script_Loader->init();

        wp_enqueue_script(
            $script_slug,
            BKX_BLOCKS_ASSETS.'seat/block.js',
            [  // Dependencies that will have to be imported on the block JS file
                'wp-blocks', // Required: contains registerBlockType function that creates a block
                'wp-element', // Required: contains element function that handles HTML markup
                'wp-i18n', // contains registerBlockType function that creates a block
                'wp-server-side-render',
                'wp-components', 'wp-editor'
            ],
            BKX_PLUGIN_VER
        );

        wp_enqueue_script(
            $bootstrap_script,
            BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/bootstrap.min.js',
            [  // Dependencies that will have to be imported on the block JS file
                'wp-blocks', // Required: contains registerBlockType function that creates a block
                'wp-element', // Required: contains element function that handles HTML markup
                'wp-i18n', // contains registerBlockType function that creates a block
                'wp-server-side-render',
                'wp-components', 'wp-editor'
            ], // General style
            BKX_PLUGIN_VER
        );

        // The block style
        // It will be loaded on the editor and on the site
        wp_register_style(
            $bootstrap_style,
            BKX_PLUGIN_PUBLIC_URL . '/css/bootstrap.min.css',
            [], // General style
            BKX_PLUGIN_VER
        );

        wp_register_style(
            $style_main,
            BKX_PLUGIN_PUBLIC_URL . '/css/style.css',
            [], // General style
            BKX_PLUGIN_VER
        );

        wp_register_style(
            $style_slug,
            BKX_BLOCKS_ASSETS . 'seat/css/style.css',
            [], // General style
            BKX_PLUGIN_VER
        );

        // Registering the block
        register_block_type(
            "{$this->plugin_name}/{$this->block_name}", // Block name with namespace
            [
                'style' => array( $style_main, $style_slug, $bootstrap_style), // General block style slug
                'editor_script' => array($script_slug, $bootstrap_script),  // The block script slug
                'render_callback' => [$this, 'bkx_seats_render_callback'],
                'attributes'      => $this->attributes,
            ]
        );
    }

    function bkx_seats_render_callback( $attributes){
        // Prepare variables.
        $desc       = isset( $attributes['showDesc'] ) ? $attributes['showDesc'] : true;
        $image      = isset( $attributes['showImage'] ) ? $attributes['showImage'] : true;
        ///$info       = isset( $attributes['showExtra'] ) ? $attributes['showExtra'] : true;
        $seat_id    = isset( $attributes['seatPostId'] ) && $attributes['seatPostId'] > 0 ? $attributes['seatPostId'] : 'all';
        $columns    = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
        $rows       = isset( $attributes['rows'] ) ? $attributes['rows'] : 1;

       // echo '<pre>',print_r($attributes,1),'</pre>';
        ob_start();
        echo do_shortcode('[bookingx seat-id="'.$seat_id.'" columns="'.$columns.'" rows="'.$rows.'"  description="'.$desc.'" image="'.$image.'" extra-info="'.$info.'"]');
        $seats_data = ob_get_clean();
        return "<div class=\"gutenberg-booking-x-seats-data\">{$seats_data}</div>";
    }
}
new Bkx_Register_Seat_Block();
<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Addition_Block
{
    public $plugin_name = 'booking-x';

    public $block_name = 'bkx-addition-block';

    /**
     * @var array
     */
    public $attributes = array(
        'showDesc'      => array( 'type' => 'boolean' ),
        'showImage'     => array( 'type' => 'boolean' ),
        'additionDescription'      => array( 'type' => 'string' ),
        'additionImageStatus'      => array( 'type' => 'string' ),
        //'showaddition'     => array( 'type' => 'boolean' ),
        'additionPostId'    => array( 'type' => 'string' ),
        'additionPosts'     => array( 'type' => 'object' ),
        'columns'       => array( 'type' => 'integer' ),
        'rows'          => array( 'type' => 'integer' ),

    );

    public function __construct() {
        add_action( 'init', array( $this,'bkx_addition_register_block_action' ) );
    }

    public function bkx_addition_register_block_action() {

        $script_slug = $this->plugin_name . '-' . $this->block_name;

        $bootstrap_script = $this->plugin_name . '-' . $this->block_name . '-bootstrap-script';
        $style_slug = $this->plugin_name . '-' . $this->block_name . '-style-block';
        $bootstrap_style = $this->plugin_name . '-' . $this->block_name . '-bootstrap-style';
        $style_main = $this->plugin_name . '-' . $this->block_name . '-style-main';

        wp_enqueue_script(
            $script_slug,
            BKX_BLOCKS_ASSETS.'addition/block.js',
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
            BKX_BLOCKS_ASSETS . 'addition/css/style.css',
            [], // General style
            BKX_PLUGIN_VER
        );

        // Registering the block
        register_block_type(
            "{$this->plugin_name}/{$this->block_name}", // Block name with namespace
            [
                'style' => array( $style_main, $style_slug, $bootstrap_style), // General block style slug
                'editor_script' => array($script_slug, $bootstrap_script),  // The block script slug
                'render_callback' => [$this, 'bkx_additions_render_callback'],
                'attributes'      => $this->attributes,
            ]
        );
    }

    /**
     * @param $attributes
     * @return string
     */
    function bkx_additions_render_callback( $attributes){
        ob_start();
        // Prepare variables.
        $desc       = $attributes['additionDescription'];
        $image      = $attributes['additionImageStatus'];
        ///$info       = isset( $attributes['showaddition'] ) ? $attributes['showaddition'] : true;
        $addition_id    = isset( $attributes['additionPostId'] ) && $attributes['additionPostId'] > 0 ? $attributes['additionPostId'] : 'all';
        $columns    = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
        $rows       = isset( $attributes['rows'] ) ? $attributes['rows'] : 1;
        //echo '<pre>',print_r($attributes,1),'</pre>';
        echo do_shortcode('[bookingx block="1" extra-id="'.$addition_id.'" columns="'.$columns.'" rows="'.$rows.'"  description="'.$desc.'" image="'.$image.'"]');
        $additions_data = ob_get_clean();
        return "<div class=\"gutenberg-booking-x-additions-data\">{$additions_data}</div>";
    }
}
new Bkx_Register_Addition_Block();
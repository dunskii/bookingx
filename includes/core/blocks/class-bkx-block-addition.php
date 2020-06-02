<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Addition_Block extends Bkx_Block
{
    private static $_instance = null;

    public $plugin_name = 'booking-x';

    public $block_name = 'bkx-addition-block';

    public $type = "booking-x/bkx-addition-block";

    public $script_handle = array();

    public $style_handle = array();

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

    public static function get_instance() {

        if ( null === self::$_instance ) {
            self::$_instance = new self;
        }

        return self::$_instance;

    }

    public function getScripts() {
        $script_slug = $this->plugin_name . '-' . $this->block_name;
        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $bootstrap_script = $this->plugin_name . '-' . $this->type . '-bootstrap-script';
        $this->script_handle = array( $script_slug, $bootstrap_script );

        return array(
            array(
                'handle'   => $script_slug,
                'src'      => BKX_BLOCKS_ASSETS.'addition/block.js',
                'deps'     => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
                'version'  => $min ? BKX_PLUGIN_VER : filemtime( BKX_BLOCKS_ASSETS_BASE_PATH.'/addition/block.js' ),
                'callback' => array(),
            ),
            array(
                'handle'   => $bootstrap_script,
                'src'      => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/bootstrap.min.js',
                'deps'     => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
                'version'  => $min ? BKX_PLUGIN_VER : filemtime( BKX_PLUGIN_PUBLIC_PATH.'\js/booking-form/bootstrap.min.js' ),
                'callback' => array(),
            )
        );
    }

    public function getStyles() {
        $deps = array( 'wp-edit-blocks' );
        $bootstrap_style = $this->plugin_name . '-' . $this->type . '-bootstrap-style';
        $style_main = $this->plugin_name . '-' . $this->type . '-style-main';
        $style_slug = $this->plugin_name . '-' . $this->type . '-style-block';

        return array(
            array(
                'handle'  => $style_slug,
                'src'     => BKX_BLOCKS_ASSETS . 'addition/css/style.css',
                'deps'    => $deps,
                'version' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( BKX_BLOCKS_ASSETS_BASE_PATH . '\addition/css/style.css' ) : BKX_PLUGIN_VER,
            ),
            array(
                'handle'  => $style_main,
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/style.css',
                'deps'    => $deps,
                'version' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( BKX_PLUGIN_DIR_PATH . '\public/css/style.css' ) : BKX_PLUGIN_VER,
            ),
            array(
                'handle'  => $bootstrap_style,
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/bootstrap.min.css',
                'deps'    => $deps,
                'version' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( BKX_PLUGIN_DIR_PATH . '\public/css/bootstrap.min.css' ) : BKX_PLUGIN_VER,
            )
        );
    }

    /**
     * @param $attributes
     * @return string
     */
    function render_block( $attributes = array() ){
        // Prepare variables.
        $desc       = isset($attributes['additionDescription']) && $attributes['additionDescription'] != "" ? $attributes['additionDescription'] : 'yes';
        $image      = isset($attributes['additionImageStatus']) && $attributes['additionImageStatus'] != "" ? $attributes['additionImageStatus'] : 'yes';;
        //$info       = isset( $attributes['showaddition'] ) ? $attributes['showaddition'] : true;
        $addition_id    = isset( $attributes['additionPostId'] ) && $attributes['additionPostId'] > 0 ? $attributes['additionPostId'] : 'all';
        $columns    = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
        $rows       = isset( $attributes['rows'] ) ? $attributes['rows'] : 1;
        ob_start();
        echo do_shortcode('[bookingx block="1" extra-id="'.$addition_id.'" columns="'.$columns.'" rows="'.$rows.'"  description="'.$desc.'" image="'.$image.'"]');
        $additions_data = ob_get_clean();
        return "<div class=\"gutenberg-booking-x-additions-data\">{$additions_data}</div>";
    }
}

// Register block.
if ( true !== ( $registered = Bkx_Blocks::register( Bkx_Register_Addition_Block::get_instance() ) ) && is_wp_error( $registered ) ) {
    // Log that block could not be registered.
     echo '<pre>',print_r($registered->get_error_message(),1),'</pre>';
}
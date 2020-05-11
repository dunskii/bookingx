<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Base_Block extends Bkx_Block
{
    private static $_instance = null;

    public $plugin_name = 'booking-x';

    public $type = "booking-x/bkx-base-block";

    public $block_name = 'bkx-base-block';

    public $script_handle = array();

    public $attributes = array(
        'showDesc'      => array( 'type' => 'boolean' ),
        'showImage'     => array( 'type' => 'boolean' ),
        'baseDescription'      => array( 'type' => 'string' ),
        'baseImageStatus'      => array( 'type' => 'string' ),
        //'showExtra'     => array( 'type' => 'boolean' ),
        'basePostId'    => array( 'type' => 'string' ),
        'basePosts'     => array( 'type' => 'object' ),
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
                'src'      => BKX_BLOCKS_ASSETS.'base/block.js',
                'deps'     => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
                'version'  => $min ? BKX_PLUGIN_VER : filemtime( BKX_BLOCKS_ASSETS_BASE_PATH.'/base/block.js' ),
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
                'src'     => BKX_BLOCKS_ASSETS . 'base/css/style.css',
                'deps'    => $deps,
                'version' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( BKX_BLOCKS_ASSETS_BASE_PATH . '\base/css/style.css' ) : BKX_PLUGIN_VER,
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
        $desc       = $attributes['baseDescription'];
        $image      = $attributes['baseImageStatus'];
        ///$info       = isset( $attributes['showExtra'] ) ? $attributes['showExtra'] : true;
        $base_id    = isset( $attributes['basePostId'] ) && $attributes['basePostId'] > 0 ? $attributes['basePostId'] : 'all';
        $columns    = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
        $rows       = isset( $attributes['rows'] ) ? $attributes['rows'] : 1;
        ob_start();
        echo $bases_data = do_shortcode('[bookingx block="1" base-id="'.$base_id.'" columns="'.$columns.'" rows="'.$rows.'"  description="'.$desc.'" image="'.$image.'"]');
        $bases_data = ob_get_clean();
        return "<div class=\"gutenberg-booking-x-bases-data\">{$bases_data}</div>";
    }
}

// Register block.
if ( true !== ( $registered = Bkx_Blocks::register( Bkx_Register_Base_Block::get_instance() ) ) && is_wp_error( $registered ) ) {
    // Log that block could not be registered.
    echo '<pre>',print_r($registered->get_error_message(),1),'</pre>';
}
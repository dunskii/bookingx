<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Seat_Block extends Bkx_Register_Blocks
{
    private static $_instance = null;

    public $plugin_name = 'booking-x';

    public $type = "booking-x/bkx-seat-block";

    public $block_name = 'bkx-seat-block';

    public $script_handle = array();

    public $attributes = array(
        'showDesc'      => array( 'type' => 'boolean' ),
        'showImage'     => array( 'type' => 'boolean' ),
        'seatDescription'      => array( 'type' => 'string' ),
        'seatImageStatus'      => array( 'type' => 'string' ),
        //'showExtra'     => array( 'type' => 'boolean' ),
        'seatPostId'    => array( 'type' => 'string' ),
        'seatPosts'     => array( 'type' => 'object' ),
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
                'src'      => BKX_BLOCKS_ASSETS.'seat/block.js',
                'deps'     => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
                'version'  => $min ? BKX_PLUGIN_VER : filemtime( BKX_BLOCKS_ASSETS_BASE_PATH.'/seat/block.js' ),
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
                'src'     => BKX_BLOCKS_ASSETS . 'seat/css/style.css',
                'deps'    => $deps,
                'version' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( BKX_BLOCKS_ASSETS_BASE_PATH . '/seat/css/style.css' ) : BKX_PLUGIN_VER,
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
        $desc       = $attributes['seatDescription'];
        $image      = $attributes['seatImageStatus'];
        ///$info       = isset( $attributes['showExtra'] ) ? $attributes['showExtra'] : true;
        $seat_id    = isset( $attributes['seatPostId'] ) && $attributes['seatPostId'] > 0 ? $attributes['seatPostId'] : 'all';
        $columns    = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
        $rows       = isset( $attributes['rows'] ) ? $attributes['rows'] : 1;
        ob_start();
        echo do_shortcode('[bookingx block="1" seat-id="'.$seat_id.'" columns="'.$columns.'" rows="'.$rows.'"  description="'.$desc.'" image="'.$image.'"]');
        $seats_data = ob_get_clean();
        return "<div class=\"gutenberg-booking-x-seats-data\">{$seats_data}</div>";
    }
}
new Bkx_Register_Seat_Block();
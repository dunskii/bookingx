<?php //phpcs:ignore
/**
 * BookingX Base Block Loader Class
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Bkx_Register_Base_Block
 */
class Bkx_Register_Base_Block extends Bkx_Block {


	/**
	 * $_instance
	 *
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * $plugin_name
	 *
	 * @var string
	 */
	public $plugin_name = 'booking-x';

	/**
	 * $type
	 *
	 * @var string
	 */
	public $type = 'booking-x/bkx-base-block';

	/**
	 * $block_name
	 *
	 * @var string
	 */
	public $block_name = 'bkx-base-block';

	/**
	 * $script_handle
	 *
	 * @var array
	 */
	public $script_handle = array();

	/**
	 * $attributes
	 *
	 * @var string[][]
	 */
	public $attributes = array(
		'showDesc'        => array( 'type' => 'boolean' ),
		'showImage'       => array( 'type' => 'boolean' ),
		'baseDescription' => array( 'type' => 'string' ),
		'baseImageStatus' => array( 'type' => 'string' ),
		'basePostId'      => array( 'type' => 'string' ),
		'basePosts'       => array( 'type' => 'object' ),
		'columns'         => array( 'type' => 'integer' ),
		'rows'            => array( 'type' => 'integer' ),

	);

	/**
	 * Base Block get_instance
	 *
	 * @return Bkx_Register_Base_Block|null
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Base Block getScripts()
	 *
	 * @return array|array[]
	 */
	public function getScripts() : array {
		$script_slug         = $this->plugin_name . '-' . $this->block_name;
		$min                 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$bootstrap_script    = $this->plugin_name . '-' . $this->type . '-bootstrap-script';
		$this->script_handle = array( $script_slug, $bootstrap_script );
		$seat_alias          = bkx_crud_option_multisite( 'bkx_alias_seat' );
		$base_alias          = bkx_crud_option_multisite( 'bkx_alias_base' );
		$extra_alias         = bkx_crud_option_multisite( 'bkx_alias_addition' );
		return array(
			array(
				'handle'    => $script_slug,
				'src'       => BKX_BLOCKS_ASSETS . 'base/block.js',
				'deps'      => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
				'version'   => $min ? BKX_PLUGIN_VER : filemtime( BKX_BLOCKS_ASSETS_BASE_PATH . '/base/block.js' ),
				'callback'  => array(),
				'localized' => array(
					'site_url' => get_site_url(),
					's_alias'  => $seat_alias,
					'b_alias'  => $base_alias,
					'e_alias'  => $extra_alias,
				),
			),
			array(
				'handle'   => $bootstrap_script,
				'src'      => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/bootstrap.min.js',
				'deps'     => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
				'version'  => $min ? BKX_PLUGIN_VER : filemtime( BKX_PLUGIN_PUBLIC_PATH . '\js/booking-form/bootstrap.min.js' ),
				'callback' => array(),
			),
		);
	}

	/**
	 * Base Block getStyles()
	 *
	 * @return array|array[]
	 */
	public function getStyles() : array {
		$deps            = array( 'wp-edit-blocks' );
		$bootstrap_style = $this->plugin_name . '-' . $this->type . '-bootstrap-style';
		$style_main      = $this->plugin_name . '-' . $this->type . '-style-main';
		$style_slug      = $this->plugin_name . '-' . $this->type . '-style-block';

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
			),
		);
	}

	/**
	 * Base Block render_block()
	 *
	 * @param  array $attributes
	 * @return string
	 */
	function render_block( $attributes = array() ): string {
		// Prepare variables.
		$desc  = isset( $attributes['baseDescription'] ) && '' !== $attributes['baseDescription'] ? $attributes['baseDescription'] : 'yes';
		$image = isset( $attributes['baseImageStatus'] ) && '' !== $attributes['baseImageStatus'] ? $attributes['baseImageStatus'] : 'yes';
		// $info       = isset( $attributes['showExtra'] ) ? $attributes['showExtra'] : true;
		$base_id = isset( $attributes['basePostId'] ) && $attributes['basePostId'] > 0 ? $attributes['basePostId'] : 'all';
		$columns = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
		$rows    = isset( $attributes['rows'] ) ? $attributes['rows'] : 1;
		ob_start();
     echo $bases_data = do_shortcode( '[bookingx block="1" base-id="' . esc_attr( $base_id ) . '" columns="' . esc_attr( $columns ) . '" rows="' . esc_attr( $rows ) . '"  description="' . esc_attr( $desc ) . '" image="' . esc_attr( $image ) . '"]' ); // phpcs:disable
     $bases_data      = ob_get_clean();
     return "<div class=\"gutenberg-booking-x-bases-data\">{$bases_data}</div>";
	}
}

// Register block.
// phpcs:disable
if ( true !== ( $registered = Bkx_Blocks::register( Bkx_Register_Base_Block::get_instance() ) ) && is_wp_error( $registered ) ) {
	// Log that block could not be registered.
	echo '<pre>', print_r( $registered->get_error_message(), 1 ), '</pre>';
}

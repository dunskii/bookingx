<?php //phpcs:ignore
/**
 * Booking Form Block Loader Class
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Booking Form Gutenberg Block
 *
 * @class   Bkx_Register_Booking_Form_Block
 * @extend  Bkx_Block
 * @version 1.0
 */
class Bkx_Register_Booking_Form_Block extends Bkx_Block {


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
	 * $block_name
	 *
	 * @var string
	 */
	public $block_name = 'bkx-booking-form-block';

	/**
	 * $attributes
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * $type
	 *
	 * @var string
	 */
	public $type = 'booking-x/bkx-booking-form-block';

	/**
	 * $script_handle
	 *
	 * @var array
	 */
	public $script_handle = array();

	/**
	 * Block get_instance()
	 *
	 * @return Bkx_Register_Booking_Form_Block|null
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Block getScripts()
	 *
	 * @return array
	 */
	public function getScripts() : array {
		$script_slug         = $this->plugin_name . '-' . $this->block_name;
		$min                 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->script_handle = array( $script_slug );
		return array(
			array(
				'handle'   => $script_slug,
				'src'      => BKX_BLOCKS_ASSETS . 'booking-form/block.js',
				'deps'     => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ),
				'version'  => $min ? BKX_PLUGIN_VER : filemtime( BKX_BLOCKS_ASSETS_BASE_PATH . '/booking-form/block.js' ),
				'callback' => array(),
			),
		);
	}

	/**
	 * Block getStyles()
	 *
	 * @return array|array[]
	 */
	public function getStyles() : array {
		$deps       = array( 'wp-edit-blocks' );
		$style_slug = $this->plugin_name . '-' . $this->block_name . '-style-block';

		return array(
			array(
				'handle'  => $style_slug,
				'src'     => BKX_PLUGIN_DIR_PATH . '\public/css/style.css',
				'deps'    => $deps,
				'version' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( BKX_PLUGIN_DIR_PATH . '\public/css/style.css' ) : BKX_PLUGIN_VER,
			),
		);
	}

	/**
	 * Block render_block
	 *
	 * @param  $attributes
	 * @return string
	 */
	function render_block( $attributes = array() ): string {
		// Prepare variables.
		ob_start();
		echo $booking_forms_data = do_shortcode( '[bkx_booking_form]' ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		$booking_forms_data      = ob_get_clean();
		return "<div class=\"gutenberg-booking-x-booking_forms-data\">{$booking_forms_data}</div>";
	}
}

// Register block.
if ( true !== ( $registered = Bkx_Blocks::register( Bkx_Register_Booking_Form_Block::get_instance() ) ) && is_wp_error( $registered ) ) {
	// Log that block could not be registered.
	echo '<pre>', print_r( $registered->get_error_message(), 1 ), '</pre>'; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
}

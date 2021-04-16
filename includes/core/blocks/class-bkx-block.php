<?php
/**
 * BookingX Main Block Loader Class
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Bkx_Block
 */
class Bkx_Block {


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
	public $type = '';

	/**
	 * $script_handle
	 *
	 * @var array
	 */
	public $script_handle = array();

	/**
	 * $style_handle
	 *
	 * @var array
	 */
	public $style_handle = array();

	/**
	 * Bkx_Block constructor.
	 */
	public function __construct() {
	}

	/**
	 * Block Init
	 */
	public function init() {
		$this->register_block_type();
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_styles' ) );
		add_filter( 'is_bookingx', array( $this, 'is_bookingx_call' ) );
	}

	/**
	 * Get_type()
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Is_bookingx_call()
	 *
	 * @return bool
	 */
	public function is_bookingx_call(): bool {
		return true;
	}

	/**
	 * Block register_block_type
	 */
	public function register_block_type() {
		register_block_type(
			$this->get_type(),
			array(
				'category'        => 'booking-x',
				'style'           => $this->style_handle,
				'render_callback' => array( $this, 'render_block' ),
				'editor_script'   => $this->script_handle,
				'attributes'      => $this->attributes,
			)
		);

	}

	/**
	 * Block enqueue_scripts()
	 */
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
			$localized = isset( $script['localized'] ) ? $script['localized'] : false;
			if ( ! empty( $localized ) ) {
				$js_object = isset( $script['js_object'] ) && ! empty( $script['js_object'] ) ? $script['js_object'] : 'bkx_block_obj';
				wp_register_script( $script['handle'], $src, $deps, $version, $in_footer );
				// Localize the script with new data.
				wp_localize_script( $script['handle'], $js_object, $localized );
				// Enqueued script with localized data.
				wp_enqueue_script( $script['handle'] );
			} else {
				// Enqueue script.
				wp_enqueue_script( $script['handle'], $src, $deps, $version, $in_footer );
			}
		}

	}

	/**
	 * Block getScripts()
	 *
	 * @return array
	 */
	public function getScripts(): array {  // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return array();

	}

	/**
	 * Block enqueue_styles()
	 */
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

	/**
	 * Block getStyles()
	 *
	 * @return array
	 */
	public function getStyles(): array {  // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return array();

	}

	/**
	 * Block render_block()
	 *
	 * @param  array $attributes Attributes Array.
	 * @return string
	 */
	public function render_block( $attributes = array() ): string {
		return '';

	}
}

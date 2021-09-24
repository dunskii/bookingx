<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bookingx
 * @subpackage Bookingx/public
 * @author     Dunskii Web Services <divyang@dunskii.com>
 */
class Bookingx_Public {



	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 * @since 1.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bookingx_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bookingx_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bookingx-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bookingx_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bookingx_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bookingx-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 *  Gutenberg Blocks for Load this Class
	 *  Create Block & Register Block Type
	 */
	public function register_bookingx_blocks_settings() {
		if ( function_exists( 'register_block_type' ) ) {
			$seat_block     = 'bkx-seat-booking-x';
			$base_block     = 'bkx-base-booking-x';
			$addition_block = 'bkx-addition-booking-x';
			$seat_alias     = bkx_crud_option_multisite( 'bkx_alias_seat' );
			$base_alias     = bkx_crud_option_multisite( 'bkx_alias_base' );
			$extra_alias    = bkx_crud_option_multisite( 'bkx_alias_addition' );

			$localized = array(
				'end_point_seat'     => get_rest_url( null, 'wp/v2/bkx_seat' ),
				'end_point_base'     => get_rest_url( null, 'wp/v2/bkx_base' ),
				'end_point_addition' => get_rest_url( null, 'wp/v2/bkx_addition' ),
				's_alias'            => $seat_alias,
				'b_alias'            => $base_alias,
				'e_alias'            => $extra_alias,
			);

			register_block_type(
				BKX_PACKAGES_BLOCKS . '/bkx-booking-form/',
				array(
					'render_callback' => 'render_booking_form_block',
				)
			);
			register_block_type(
				BKX_PACKAGES_BLOCKS . '/bkx-seat/',
				array(
					'render_callback' => 'render_bkx_resource_block',
					'attributes'      => array(
						'showDesc'        => array( 'type' => 'boolean' ),
						'showImage'       => array( 'type' => 'boolean' ),
						'seatDescription' => array( 'type' => 'string' ),
						'seatImageStatus' => array( 'type' => 'string' ),
						'orderBy'         => array( 'type' => 'string' ),
						'order'           => array( 'type' => 'string' ),
						'seatPostId'      => array( 'type' => 'string' ),
						'columns'         => array( 'type' => 'integer' ),
						'rows'            => array( 'type' => 'integer' ),
					),
				)
			);
			register_block_type(
				BKX_PACKAGES_BLOCKS . '/bkx-base/',
				array(
					'render_callback' => 'render_bkx_service_block',
					'attributes'      => array(
						'showDesc'        => array( 'type' => 'boolean' ),
						'showImage'       => array( 'type' => 'boolean' ),
						'baseDescription' => array( 'type' => 'string' ),
						'baseImageStatus' => array( 'type' => 'string' ),
						'orderBy'         => array( 'type' => 'string' ),
						'order'           => array( 'type' => 'string' ),
						'basePostId'      => array( 'type' => 'string' ),
						'columns'         => array( 'type' => 'integer' ),
						'rows'            => array( 'type' => 'integer' ),
					),
				)
			);
			register_block_type(
				BKX_PACKAGES_BLOCKS . '/bkx-addition/',
				array(
					'render_callback' => 'render_bkx_addition_block',
					'attributes'      => array(
						'showDesc'            => array( 'type' => 'boolean' ),
						'showImage'           => array( 'type' => 'boolean' ),
						'additionDescription' => array( 'type' => 'string' ),
						'additionImageStatus' => array( 'type' => 'string' ),
						'orderBy'             => array( 'type' => 'string' ),
						'order'               => array( 'type' => 'string' ),
						'additionPostId'      => array( 'type' => 'string' ),
						'columns'             => array( 'type' => 'integer' ),
						'rows'                => array( 'type' => 'integer' ),
					),
				)
			);

			wp_enqueue_script( $seat_block, BKX_PACKAGES_BLOCKS_URL . '/bkx-seat/build/index.js', array(), BKX_PLUGIN_VER, true );
			wp_localize_script( $seat_block, 'bkx_seat_block_obj', $localized );

			wp_enqueue_script( $base_block, BKX_PACKAGES_BLOCKS_URL . '/bkx-base/build/index.js', array(), BKX_PLUGIN_VER, true );
			wp_localize_script( $base_block, 'bkx_base_block_obj', $localized );

			wp_enqueue_script( $addition_block, BKX_PACKAGES_BLOCKS_URL . '/bkx-addition/build/index.js', array(), BKX_PLUGIN_VER, true );
			wp_localize_script( $addition_block, 'bkx_addition_block_obj', $localized );

		}
	}
}

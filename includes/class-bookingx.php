<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.10
 * @package    Bookingx
 * @subpackage Bookingx/includes
 * @author     Dunskii Web Services <divyang@dunskii.com>
 */
defined( 'ABSPATH' ) || exit;

class Bookingx {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    Bookingx_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	protected static $_instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The Directory Path of the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $dir_path The Directory Path of the plugin.
	 */

	protected $dir_path;

	/**
	 * The Directory Url of the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $dir_url The Directory Url of the plugin.
	 */

	protected $dir_url;

	/**
	 * The Public Url of the plugin.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $public_url The Public Url of the plugin.
	 */

	protected $public_url;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		if ( defined( 'BOOKINGX_VERSION' ) ) {
			$this->version = BOOKINGX_VERSION;
		} else {
			$this->version = '1.0';
		}

		$this->dir_path   = plugin_dir_path( __DIR__ );
		$this->dir_url    = plugin_dir_url( __DIR__ );
		$this->public_url = $this->dir_url . 'public';

		$this->plugin_name = 'bookingx';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		// Set up localisation.
		$this->load_plugin_textdomain();
	}



	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function template_path() {
		return apply_filters( 'bkx_template_path', BKX_PLUGIN_DIR_PATH );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bookingx_Loader. Orchestrates the hooks of the plugin.
	 * - Bookingx_i18n. Defines internationalization functionality.
	 * - Bookingx_Admin. Defines all hooks for the admin area.
	 * - Bookingx_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bookingx-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bookingx-public.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bookingx-loader.php';

		/**
		 *  Bkx Short code Load
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/shortcode/class-bkx-dashboard.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/shortcode/class-bkx-my-account.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/shortcode/class-bkx-booking-form-shortcode.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/shortcode/class-shortcode-editor.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/shortcode/class-bkx-listing-shortcode.php';

		/**
		 *  Load Seat Post Type
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/seat/class-bkx-seat.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/seat/class-bkx-seat-meta-box.php';

		/**
		 *  Load Base Post Type
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/base/class-bkx-base.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/base/class-bkx-base-meta-box.php';

		/**
		 *  Load Extra Post Type
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/extra/class-bkx-extra.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/extra/class-bkx-extra-meta-box.php';

		/**
		 *  Load Booking Post Type
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/booking/class-bkx-booking.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/booking/class-order-meta-box.php';

		/**
		 *  Load Payment Gateways
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/payment-gateways/class-bkx-payment-core.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/payment-gateways/bkx-class-paypal-gateway.php';

		/**
		 *  Load Export - Import
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/export-import/class-bkx-export.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/export-import/class-bkx-import.php';

		/**
		 * BKX Template Loader
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-bkx-template-loader.php';

		/**
		 *  BKX Core Functions
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/functions/bkx-core-functions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/ajax/call-ajax-functions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/functions/filter-actions-functions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/functions/template-hooks.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/functions/template-functions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/functions/multisite-functions.php';

		/**
		 *  BKX Admin Files
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/settings_save.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/setting-functions.php';

		/**
		 * Email Config
		 */

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/class-bkx-emailsSetup.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/core/emails/class-bookingx-email-config.php';
		$this->loader = new Bookingx_Loader();

		$this->theme_support_includes();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0
	 */
	public function run() {
		$this->loader->run();
	}

	public function load_plugin_textdomain() {

		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		$locale = apply_filters( 'plugin_locale', $locale, 'bookingx' );

		unload_textdomain( 'bookingx' );
		load_textdomain( 'bookingx', WP_LANG_DIR . '/bookingx/bookingx-' . $locale . '.mo' );
		load_plugin_textdomain( 'bookingx', false, plugin_basename( dirname( BKX_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string    The name of the plugin.
	 * @since  1.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Bookingx_Loader    Orchestrates the hooks of the plugin.
	 * @since  1.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Bookingx_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'bkx_create_seat_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'bkx_create_base_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'bkx_create_addition_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'bkx_create_booking_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_bookingx_post_status' );
		$this->loader->add_action( 'manage_bkx_booking_posts_custom_column', $plugin_admin, 'render_bkx_booking_columns' );
		$this->loader->add_action( 'manage_edit-bkx_booking_sortable_columns', $plugin_admin, 'sorting_bkx_booking_columns' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bkx_notification_bubble_menu' );

		$this->loader->add_action( 'admin_footer', $plugin_admin, 'bkx_booking_bulk_admin_footer', 10 );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'bkx_booking_search_by_dates', 2, 10 );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'bkx_booking_seat_view', 2, 10 );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'bkx_booking_listing_view', 2, 10 );
		$this->loader->add_action( 'parse_query', $plugin_admin, 'bkx_booking_search_custom_fields' );
		$this->loader->add_action( 'parse_comment_query', $plugin_admin, 'bkx_booking_parse_comment_query' );
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'bkx_add_meta_query' );
		$this->loader->add_action( 'init', $plugin_admin, 'export_now' );
		$this->loader->add_action( 'init', $plugin_admin, 'import_now' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'bkx_bulk_action_admin_notice' );

		$this->loader->add_filter( 'manage_bkx_booking_posts_columns', $plugin_admin, 'bkx_booking_columns', 99, 2 );
		// $this->loader->add_filter('post_type_link', $plugin_admin, 'bkx_change_view_link', 10, 2);
		$this->loader->add_filter( 'bulk_actions-edit-bkx_booking', $plugin_admin, 'bkx_booking_bulk_actions' );
		$this->loader->add_filter( 'handle_bulk_actions-edit-bkx_booking', $plugin_admin, 'bkx_booking_handle_bulk_actions', 99, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new Bookingx_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_public, 'register_bookingx_blocks_settings' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		add_filter(
			'body_class',
			function ( $classes ) {
				if ( is_booking_page() ) {
					$classes = array_merge( $classes, array( 'booking-x-form' ) );
				}
				if ( is_dashboard() ) {
					$classes = array_merge( $classes, array( 'booking-x-dashboard' ) );
				}
				if ( is_bookingx() ) {
					return array_merge( $classes, array( 'booking-x' ) );
				}

			}
		);

	}

	/**
	 * @return string[]
	 */
	private function bkx_is_wp_support_themes() {
		return array(
			'twentyseventeen',
			'twentytwenty',
			'twentynineteen',
		);
	}

	/**
	 * Include classes for theme support.
	 */
	private function theme_support_includes() {
		if ( $this->bkx_is_wp_support_themes() ) {
			switch ( get_template() ) {
				case 'twentyseventeen':
					include_once BKX_PLUGIN_DIR_PATH . 'includes/core/theme-support/class-bkx-twenty-seventeen.php';
					break;
				case 'twentynineteen':
					include_once BKX_PLUGIN_DIR_PATH . 'includes/core/theme-support/class-bkx-twenty-nineteen.php';
					break;
				case 'twentytwenty':
					include_once BKX_PLUGIN_DIR_PATH . 'includes/core/theme-support/class-bkx-twenty-twenty.php';
					break;
				case 'twentytwentyone':
					include_once BKX_PLUGIN_DIR_PATH . 'includes/core/theme-support/class-bkx-twenty-twenty-one.php';
					break;
			}
		}
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string    The version number of the plugin.
	 * @since  1.0
	 */
	public function get_version() {
		return $this->version;
	}
}

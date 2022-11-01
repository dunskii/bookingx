<?php //phpcs:ignore
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dunskii.com
 * @since      1.0.16
 *
 * @package    Bookingx
 * @subpackage Bookingx/admin
 */
class Bookingx_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since      1.0.16
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since      1.0.16
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @since      1.0.16
	 */
	public function __construct( $plugin_name = null, $version = null ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'bkx_calender_unavailable_days', array( $this, 'bkx_calender_unavailable_days' ) );
		add_filter( 'register_post_type_args', array( $this, 'bkx_seat_post_type_args' ), 10, 2 );
	}


	/**
	 * Bkx_seat_post_type_args
	 *
	 * @param array  $args Args Array.
	 * @param string $post_type Post Type Name.
	 *
	 * @return mixed
	 */
	public function bkx_seat_post_type_args( $args, $post_type ) {
		if ( 'bkx_seat' === $post_type || 'bkx_base' === $post_type || 'bkx_addition' === $post_type ) {
			$args['show_in_rest'] = true;
			// Optionally customize the rest_base or rest_controller_class.
			$args['rest_base']             = $post_type;
			$args['rest_controller_class'] = 'WP_REST_Posts_Controller';
		}
		return $args;
	}

	/**
	 * Create Seat Post Type
	 */
	public function bkx_create_seat_post_type() {
		$alias_seat     = bkx_crud_option_multisite( 'bkx_alias_seat' );
		$alias_base     = bkx_crud_option_multisite( 'bkx_alias_base' );
		$alias_addition = bkx_crud_option_multisite( 'bkx_alias_addition' );
		$extended_text  = bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias' );
		$bkx_step_1     = bkx_crud_option_multisite( 'bkx_label_of_step1' );
		$bkx_notice_create_an_account     = bkx_crud_option_multisite( 'bkx_notice_create_an_account' );

		( false === $alias_seat ) ? bkx_crud_option_multisite( 'bkx_alias_seat', 'Resource', 'update' ) : '';
		( false === $alias_base ) ? bkx_crud_option_multisite( 'bkx_alias_base', 'Service', 'update' ) : '';
		( false === $alias_addition ) ? bkx_crud_option_multisite( 'bkx_alias_addition', 'Extra', 'update' ) : '';
		( false === $extended_text ) ? bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias', 'How many times you want to extend this service?', 'update' ) : '';
		( false === $bkx_step_1 ) ? bkx_crud_option_multisite( 'bkx_label_of_step1', 'Please select what you would like to book', 'update' ) : '';
		( false === $bkx_notice_create_an_account ) ? bkx_crud_option_multisite( 'bkx_notice_create_an_account', 'You have selected to create an account with us. We will email your log in credentials.', 'update' ) : '';
		$bkx_booking_style = bkx_crud_option_multisite( 'bkx_booking_style' );
		if ( ( ! isset( $bkx_booking_style ) || '' === $bkx_booking_style ) ) {
			bkx_crud_option_multisite( 'bkx_booking_style', 'default', 'update' );
		}
		// Set UI labels for Custom Post Type.
		$labels = array(
			'name'               => __( $alias_seat, 'bookingx' ), //phpcs:ignore
			'singular_name'      => __( $alias_seat, 'bookingx' ), //phpcs:ignore
			'menu_name'          => _x( $alias_seat, $alias_seat, 'bookingx' ), //phpcs:ignore
			'all_items'          => __( $alias_seat, 'bookingx' ), //phpcs:ignore
			'view_item'          => __( "View {$alias_seat}", 'bookingx' ), //phpcs:ignore
			'add_new_item'       => __( "Add New {$alias_seat}", 'bookingx' ), //phpcs:ignore
			'add_new'            => __( "Add New {$alias_seat}", 'bookingx' ), //phpcs:ignore
			'edit_item'          => __( "Edit {$alias_seat}", 'bookingx' ), //phpcs:ignore
			'update_item'        => __( "Update {$alias_seat}", 'bookingx' ), //phpcs:ignore
			'search_items'       => __( "Search {$alias_seat}", 'bookingx' ), //phpcs:ignore
			'not_found'          => __( 'Not Found', 'bookingx' ), //phpcs:ignore
			'not_found_in_trash' => __( 'Not found in Trash', 'bookingx' ), //phpcs:ignore
		);
		register_post_type(
			'bkx_seat',
			array(
				'labels'       => $labels,
				'public'       => true,
				'has_archive'  => sanitize_title( $alias_seat ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'query_var'    => true,
				'show_in_rest' => true,
				'rewrite'      => array(
					'slug' => sanitize_title( $alias_seat ),
				),
				'show_in_menu' => 'edit.php?post_type=bkx_booking',
			)
		);
		$bkx_seat_taxonomy_status = bkx_crud_option_multisite( 'bkx_seat_taxonomy_status' );
		if ( isset( $bkx_seat_taxonomy_status ) && ( 1 === $bkx_seat_taxonomy_status || '1' === $bkx_seat_taxonomy_status ) ) {
			// Category.
			$labels = array(
				'name'              => __( $alias_seat . ' Category', 'bookingx' ), //phpcs:ignore
				'singular_name'     => __( $alias_seat . ' Category', 'bookingx' ), //phpcs:ignore
				'search_items'      => __( 'Search ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'all_items'         => __( 'All ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'parent_item'       => __( 'Parent ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'parent_item_colon' => __( 'Parent ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'edit_item'         => __( 'Edit ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'update_item'       => __( 'Update ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'add_new_item'      => __( 'Add New ' . ucwords( $alias_seat ) . ' Category', 'bookingx' ), //phpcs:ignore
				'new_item_name'     => __( 'New Category Name', 'bookingx' ), //phpcs:ignore
				'menu_name'         => __( 'Categories', 'bookingx' ),//phpcs:ignore
			);
			register_taxonomy(
				'bkx_seat_cat',
				array( 'bkx_seat' ),
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'query_var'         => true,
					'show_in_menu'      => true,
					'rewrite'           => array(
						'slug' => 'bkx-seat-category',
					),
				)
			);
		}
	}

	/**
	 * Bkx_create_base_post_type()
	 */
	public function bkx_create_base_post_type() {
		$bkx_alias_base = bkx_crud_option_multisite( 'bkx_alias_base' );
		$bkx_alias_base = isset( $bkx_alias_base ) && '' !== $bkx_alias_base ? $bkx_alias_base : 'Service';
		// Set UI labels for Custom Post Type.
		$labels = array(
			'name'               => _x( $bkx_alias_base, 'Post Type General Name', 'bookingx' ), //phpcs:ignore
			'singular_name'      => _x( $bkx_alias_base, 'Post Type Singular Name', 'bookingx' ), //phpcs:ignore
			'menu_name'          => __( $bkx_alias_base, 'bookingx' ), //phpcs:ignore
			'all_items'          => __( $bkx_alias_base, 'bookingx' ), //phpcs:ignore
			'view_item'          => __( "View {$bkx_alias_base}", 'bookingx' ), //phpcs:ignore
			'add_new_item'       => __( "Add New {$bkx_alias_base}", 'bookingx' ), //phpcs:ignore
			'add_new'            => __( "Add New {$bkx_alias_base}", 'bookingx' ), //phpcs:ignore
			'edit_item'          => __( "Edit {$bkx_alias_base}", 'bookingx' ), //phpcs:ignore
			'update_item'        => __( "Update {$bkx_alias_base}", 'bookingx' ), //phpcs:ignore
			'search_items'       => __( "Search {$bkx_alias_base}", 'bookingx' ), //phpcs:ignore
			'not_found'          => __( 'Not Found', 'bookingx' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'bookingx' ),
		);
		register_post_type(
			'bkx_base',
			array(
				'labels'       => $labels,
				'public'       => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'show_in_menu' => 'edit.php?post_type=bkx_booking',
				'has_archive'  => sanitize_title( $bkx_alias_base ),
				'show_in_rest' => true,
				'query_var'    => true,
				'rewrite'      => array(
					'slug' => sanitize_title( $bkx_alias_base ),
				),
			)
		);
		$bkx_base_taxonomy_status = bkx_crud_option_multisite( 'bkx_base_taxonomy_status' );
		if ( isset( $bkx_base_taxonomy_status ) && 1 === $bkx_base_taxonomy_status ) {
			// Category.
			$labels = $this->bkx_create_category_label( $bkx_alias_base );
			register_taxonomy(
				'bkx_base_cat',
				array( 'bkx_base' ),
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'query_var'         => true,
				)
			);
		}
	}

	/**
	 * Bkx_create_addition_post_type()
	 */
	public function bkx_create_addition_post_type() {
		$bkx_alias_addition           = bkx_crud_option_multisite( 'bkx_alias_addition' );
		$bkx_alias_addition           = isset( $bkx_alias_addition ) && '' !== $bkx_alias_addition ? $bkx_alias_addition : 'Extra';
		$bkx_addition_taxonomy_status = bkx_crud_option_multisite( 'bkx_addition_taxonomy_status' );
		$labels                       = array(
			'name'               => _x( $bkx_alias_addition, 'Post Type General Name', 'bookingx' ),  //phpcs:ignore
			'singular_name'      => _x( $bkx_alias_addition, 'Post Type Singular Name', 'bookingx' ), //phpcs:ignore
			'menu_name'          => __( $bkx_alias_addition, 'bookingx' ), //phpcs:ignore
			'all_items'          => __( $bkx_alias_addition, 'bookingx' ),//phpcs:ignore
			'view_item'          => __( "View {$bkx_alias_addition}", 'bookingx' ), //phpcs:ignore
			'add_new_item'       => __( "Add New {$bkx_alias_addition}", 'bookingx' ), //phpcs:ignore
			'add_new'            => __( "Add New {$bkx_alias_addition}", 'bookingx' ), //phpcs:ignore
			'edit_item'          => __( "Edit {$bkx_alias_addition}", 'bookingx' ),//phpcs:ignore
			'update_item'        => __( "Update {$bkx_alias_addition}", 'bookingx' ),//phpcs:ignore
			'search_items'       => __( "Search $bkx_alias_addition", 'bookingx' ),//phpcs:ignore
			'not_found'          => __( 'Not Found', 'bookingx' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'bookingx' ),
		);
		register_post_type(
			'bkx_addition',
			array(
				'labels'       => $labels,
				'public'       => true,
				'has_archive'  => sanitize_title( $bkx_alias_addition ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'show_in_rest' => true,
				'rewrite'      => array(
					'slug' => sanitize_title( $bkx_alias_addition ),
				),
				'show_in_menu' => 'edit.php?post_type=bkx_booking',
			)
		);
		if ( isset( $bkx_addition_taxonomy_status ) && 1 === $bkx_addition_taxonomy_status ) {
			// Category.
			$labels = $this->bkx_create_category_label( $bkx_alias_addition );
			register_taxonomy(
				'bkx_addition_cat',
				array( 'bkx_addition' ),
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'query_var'         => true,
				)
			);
		}
	}

	/**
	 * Bkx_create_booking_post_type()
	 */
	public function bkx_create_booking_post_type() {
		$labels = array(
			'name'               => _x( 'Bookings', 'Bookings', 'bookingx' ),
			'singular_name'      => _x( 'Booking', 'Bookings', 'bookingx' ),
			'menu_name'          => __( 'Booking X', 'bookingx' ),
			'all_items'          => __( 'Bookings', 'bookingx' ),
			'view_item'          => __( 'View Booking', 'bookingx' ),
			'add_new_item'       => __( 'Add Booking', 'bookingx' ),
			'add_new'            => __( 'Add Booking', 'bookingx' ),
			'edit_item'          => __( 'Update Booking', 'bookingx' ),
			'update_item'        => __( 'Update booking', 'bookingx' ),
			'search_items'       => __( 'Search bookings', 'bookingx' ),
			'not_found'          => __( ' Booking\'s not Found', 'bookingx' ),
			'not_found_in_trash' => __( 'Booking\'s not found in Trash', 'bookingx' ),
		);
		register_post_type(
			'bkx_booking',
			array(
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => null,
				'delete_with_user'    => false,
				'hierarchical'        => false,
				'supports'            => array( 'title' ),
			)
		);
	}

	/**
	 * Bkx_notification_bubble_menu()
	 */
	function bkx_notification_bubble_menu() {
		remove_submenu_page( 'edit.php?post_type=bkx_booking', 'post-new.php?post_type=bkx_booking' );
		global $menu;
		$count_posts           = (array) wp_count_posts( 'bkx_booking' );
		$pending_booking_count = ! empty( $count_posts ) && $count_posts['bkx-pending'] > 0 ? $count_posts['bkx-pending'] : 0;
		foreach ( $menu as $key => $value ) {
			if ( 'edit.php?post_type=bkx_booking' === $menu[ $key ][2] ) {
				$menu[ $key ][6] = 'dashicons-calendar-alt'; //phpcs:ignore
				if ( $pending_booking_count > 0 ) {
					$menu[ $key ][0] .= ' &nbsp;<span class="update-plugins bkx-count-' . $pending_booking_count . '"><span class="plugin-count" aria-hidden="true">' . $pending_booking_count . '</span><span class="screen-reader-text">' . $pending_booking_count . ' Booking\'s Pending</span></span>'; //phpcs:ignore
				}
				return;
			}
		}
	}

	/**
	 * Bkx_booking_parse_comment_query()
	 *
	 * @param object $wp WP Object.
	 *
	 * @return mixed
	 */
	function bkx_booking_parse_comment_query( $wp ) {
		if ( is_admin() ) {
			require_once ABSPATH . 'wp-admin/includes/screen.php';
			$current_screen = get_current_screen();
			if ( ! empty( $current_screen ) && ! is_wp_error( $current_screen ) && 'edit-comments' === $current_screen->base ) {
				$disable_custom_type            = apply_filters( 'bkx_disable_custom_type_in_comment_lists', array( 'booking_note' ) );
				$wp->query_vars['type__not_in'] = $disable_custom_type;
			}
		}
		return $wp;
	}

	/**
	 * Bkx_booking_search_custom_fields()
	 *
	 * @param object $wp WP Object.
	 */
	function bkx_booking_search_custom_fields( $wp ) {
		global $pagenow;
		if ( 'edit.php' !== $pagenow || empty( $wp->query_vars['s'] ) || 'bkx_booking' !== $wp->query_vars['post_type'] ) {
			return;
		}
		$order    = new BkxBooking();
		$post_ids = $order->order_search( $wp->query_vars['s'] );
		if ( ! empty( $wp->query_vars['s'] ) ) {
			// Remove "s" - we don't want to search order name.
			unset( $wp->query_vars['s'] );
			// so we know we're doing this.
			$wp->query_vars['bkx_booking_search'] = true;
			// Search by found posts.
			$wp->query_vars['post__in'] = $post_ids;
		}
	}

	/**
	 * Bkx_add_meta_query()
	 *
	 * @param object $query Query.
	 */
	function bkx_add_meta_query( $query ) {
		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date

		global $pagenow;
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( isset( $pagenow ) && 'edit.php' !== $pagenow || 'bkx_booking' !== $query->query_vars['post_type'] ) {
			return;
		}

		$seat_view               = isset( $query->query_vars['seat_view'] ) ? $query->query_vars['seat_view'] : '';
		$search_by_dates         = isset( $query->query_vars['search_by_dates'] ) ? $query->query_vars['search_by_dates'] : '';
		$search_by_selected_date = isset( $query->query_vars['search_by_selected_date'] ) ? $query->query_vars['search_by_selected_date'] : '';
		$search_by_selected_date = date( 'Y-m-d', strtotime( $search_by_selected_date ) );
		$search_by_dates_meta    = array();
		$seat_view_query         = array();

		if ( is_user_logged_in() ) {
			$current_user  = wp_get_current_user();
			$bkx_seat_role = bkx_crud_option_multisite( 'bkx_seat_role' );
			$current_role  = $current_user->roles[0];
			if ( $bkx_seat_role === $current_role ) {
				$current_seat_id          = $current_user->ID;
				$seat_post_id             = get_user_meta( $current_seat_id, 'seat_post_id', true );
				$search_by_seat_post_meta = array(
					array(
						'key'     => 'seat_id',
						'value'   => $seat_post_id,
						'compare' => '=',
					),
				);
				$query->set( 'meta_query', $search_by_seat_post_meta );
			}
		}
		if ( isset( $seat_view ) && $seat_view > 0 ) {
			$seat_view_query = array(
				array(
					'key'     => 'seat_id',
					'value'   => $seat_view,
					'compare' => '=',
				),
			);
			$query->set( 'meta_query', $seat_view_query );
		}

		if ( isset( $search_by_dates ) && '' !== $search_by_dates ) {
			switch ( $search_by_dates ) {
				case 'today':
                    $today             = date( 'Y-m-j' );
					$search_by_dates_meta = array(
						array(
							'key'     => 'booking_date',
							'value'   => $today,
							'compare' => '=',
						),
					);
					break;
				case 'tomorrow':
					$tomorrow             = date( 'Y-m-j', strtotime( '+1 day' ) );
					$search_by_dates_meta = array(
						array(
							'key'     => 'booking_date',
							'value'   => $tomorrow,
							'compare' => '=',
						),
					);
					break;
				case 'this_week':
					$monday               = date( 'Y-m-j', strtotime( 'monday this week' ) );
					$sunday               = date( 'Y-m-j', strtotime( 'sunday this week' ) );
					$search_by_dates_meta = array(
						array(
							'key'     => 'booking_date',
							'value'   => array( $monday, $sunday ),
							'compare' => 'BETWEEN',
						),
					);
					break;
				case 'next_week':
					$monday               = date( 'Y-m-j', strtotime( 'monday next week' ) );
					$sunday               = date( 'Y-m-j', strtotime( 'sunday next week' ) );
					$search_by_dates_meta = array(
						array(
							'key'     => 'booking_date',
							'value'   => array( $monday, $sunday ),
							'compare' => 'BETWEEN',
						),
					);
					break;
				case 'this_month':
					$monday               = date( 'Y-m-1' );
					$sunday               = date( 'Y-m-t' );
					$search_by_dates_meta = array(
						array(
							'relation' => 'OR',
							array(
								'key'     => 'booking_date',
								'value'   => array( $monday, $sunday ),
								'compare' => 'BETWEEN',
							),
							array(
								'key'     => 'booking_date',
								'value'   => array( date( 'Y-n-1' ), $sunday ),
								'compare' => 'BETWEEN',
							),
						),
					);
					break;
				case 'choose_date':
					$search_by_dates_meta = array(
						array(
							'key'     => 'booking_date',
							'value'   => $search_by_selected_date,
							'compare' => '=',
						),
					);
					break;
				default:
					// code...
					break;
			}
			if ( isset( $query->query_vars['search_by_dates'] ) ) {
				if ( ! empty( $query->query_vars['search_by_dates'] ) ) {
					$meta_query[] = $search_by_dates_meta;
					$meta_query[] = $seat_view_query;
					$query->set( 'meta_query', $meta_query );
				}
			}
		}
		// phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date
	}

	/**
	 * Bkx_booking_handle_bulk_actions
	 *
	 * @param string $redirect_to Redirect Url.
	 * @param string $do_action Do Action Hook.
	 * @param array  $post_ids Post ID Array.
	 *
	 * @return string
	 */
	public function bkx_booking_handle_bulk_actions( string $redirect_to, string $do_action, array $post_ids ) {
		$allow_status = array( 'mark_pending', 'mark_ack', 'mark_completed', 'mark_missed', 'mark_cancelled' );
		if ( ! in_array( $do_action, $allow_status, true ) ) {
			return $redirect_to;
		}
		$order_status = '';
		foreach ( $post_ids as $post_id ) {
			if ( isset( $post_id ) && $post_id > 0 ) {
				$bkx_action_status = explode( '_', sanitize_text_field( $do_action ) );
				$order_status      = $bkx_action_status[1];
				if ( is_multisite() ) :
					$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
					switch_to_blog( $blog_id );
				endif;
				$bkx_booking = new BkxBooking( '', $post_id );
				$bkx_booking->update_status( $order_status, false );
			}
		}

		$redirect_to = add_query_arg(
			array(
				'bulk_status_changed' => $order_status,
				'bulk_status_count'   => count( $post_ids ),
			),
			$redirect_to
		);

		return $redirect_to;
	}

	/**
	 * Bkx_bulk_action_admin_notice
	 */
	function bkx_bulk_action_admin_notice() {
		if ( ! empty( $_REQUEST['bulk_status_changed'] ) ) { //phpcs:ignore
			$count  = sanitize_text_field( intval( $_REQUEST['bulk_status_count'] ) ); //phpcs:ignore
			$status = sanitize_text_field( ucwords( $_REQUEST['bulk_status_changed'] ) );//phpcs:ignore
			printf(
				'<div id="message" class="updated fade">' .
				_n( //phpcs:ignore
					"Booking have changed status to {$status}.", //phpcs:ignore
					'%1$d of bookings have changed status to %2$s.',
					$count,
					'bookingx'
				) . '</div>',
				esc_html( $count ),
				esc_html( $status )
			);
		}
	}

	/**
	 * Bkx_booking_bulk_actions
	 *
	 * @param $actions
	 *
	 * @return mixed
	 */
	function bkx_booking_bulk_actions( $actions ) {
		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}



	/**
	 * Register_bookingx_post_status
	 */
	function register_bookingx_post_status() {
		$order_statuses = apply_filters(
			'bookingx_register_bkx_booking_post_statuses',
			array(
				'bkx-pending'   => array(
					'label'                     => _x( 'Pending', 'Order status', 'bookingx' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'bookingx' ), //phpcs:ignore
				),
				'bkx-ack'       => array(
					'label'                     => _x( 'Acknowledged', 'Order status', 'bookingx' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Acknowledged <span class="count">(%s)</span>', 'Acknowledged <span class="count">(%s)</span>', 'bookingx' ), //phpcs:ignore
				),
				'bkx-completed' => array(
					'label'                     => _x( 'Completed', 'Order status', 'bookingx' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'bookingx' ), //phpcs:ignore
				),
				'bkx-cancelled' => array(
					'label'                     => _x( 'Cancelled', 'Order status', 'bookingx' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'bookingx' ), //phpcs:ignore
				),
				'bkx-missed'    => array(
					'label'                     => _x( 'Missed', 'Order status', 'bookingx' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Missed <span class="count">(%s)</span>', 'Missed <span class="count">(%s)</span>', 'bookingx' ), //phpcs:ignore
				),
				'bkx-failed'    => array(
					'label'                     => _x( 'Failed', 'Order status', 'bookingx' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'bookingx' ), //phpcs:ignore
				),
			)
		);
		foreach ( $order_statuses as $order_status => $values ) {
			register_post_status( $order_status, $values );
		}
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since      1.0.16
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
		if ( is_bookingx_admin() === true ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bookingx-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since      1.0.16
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
		if ( is_bookingx_admin() === true ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_register_style( 'jquery-ui-style', BKX_PLUGIN_DIR_URL . 'admin/css/jquery-ui/jquery-ui.min.css', array(), BKX_PLUGIN_VER );
			wp_enqueue_script( 'bkx-seat-sol', BKX_PLUGIN_DIR_URL . 'public/js/admin/sol.js', false, BKX_PLUGIN_VER, true );
			wp_enqueue_style( 'bkx-seat-sol-style', BKX_PLUGIN_DIR_URL . 'public/css/sol.css' );

			$enable_cancel_booking = bkx_crud_option_multisite( 'enable_cancel_booking' );
			$enable_cancel_booking = isset( $enable_cancel_booking ) ? $enable_cancel_booking : 0;

			$bkx_legal_options = bkx_crud_option_multisite( 'bkx_legal_options' );
			$bkx_legal_options = isset( $bkx_legal_options ) ? $bkx_legal_options : 0;

			$wp_localize_array = array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'is_cancel'        => $enable_cancel_booking,
				'is_legal_options' => $bkx_legal_options,
				'wp_date_format'   => 'mm/dd/yy',
				'default_style'    => BKX_PLUGIN_PUBLIC_URL . '/images/preview/2.jpg',
				'day_style'        => BKX_PLUGIN_PUBLIC_URL . '/images/preview/2b.jpg',
			);
			wp_register_script( 'bkx-admin-js', plugin_dir_url( __FILE__ ) . 'js/bookingx-admin.js', array( 'jquery', 'iris' ), BKX_PLUGIN_VER, true );
			wp_localize_script( 'bkx-admin-js', 'bkx_admin', $wp_localize_array );
			wp_enqueue_script( 'bkx-admin-js' );
		}

	}

	/**
	 * CreateDateRange
	 *
	 * @param string $first First Name.
	 * @param string $last Last Name.
	 * @param string $step Step Number.
	 * @param string $output_format Date Output.
	 *
	 * @return array
	 */
	public function createDateRange( $first, $last, $step = '+1 day', $output_format = 'm/d/Y' ) {
		$dates   = array();
		$current = strtotime( $first );
		$last    = strtotime( $last );
		while ( $current <= $last ) {

			$dates[] = date( $output_format, $current ); //phpcs:ignore
			$current = strtotime( $step, $current );
		}

		return $dates;
	}

	/**
	 * Bkx_calender_unavailable_days
	 *
	 * @param $days
	 *
	 * @return mixed
	 */
	public function bkx_calender_unavailable_days( $days ) {
		$bkx_biz_vac_sd = bkx_crud_option_multisite( 'bkx_biz_vac_sd' );
		$bkx_biz_vac_ed = bkx_crud_option_multisite( 'bkx_biz_vac_ed' );
		$biz_vacations  = array();
		if ( isset( $bkx_biz_vac_sd ) && ! empty( $bkx_biz_vac_sd ) && isset( $bkx_biz_vac_ed ) && ! empty( $bkx_biz_vac_ed ) ) {
			$biz_vacations = $this->createDateRange( $bkx_biz_vac_sd, $bkx_biz_vac_ed );
		}
		$bkx_biz_pub_holiday = bkx_crud_option_multisite( 'bkx_biz_pub_holiday' );
		$holidays            = array();
		if ( ! empty( $bkx_biz_pub_holiday ) ) {
			foreach ( $bkx_biz_pub_holiday as $date ) {
				if ( isset( $date ) && ! empty( $date ) ) {
					$holidays[] = $date;
				}
			}
		}
        $biz_vacations              = array_merge( $biz_vacations, $holidays );
        $biz_vacations              = array_filter( $biz_vacations );
        $unavailable_days           = array_filter( $days['unavailable_days'] );
        $days['unavailable_days']   = array_merge( $biz_vacations, array_unique( $unavailable_days ) );
		return $days;
	}

	public function date_format_correct( $date, $format = 'm/d/Y' ) {
		$date          = date( 'm/d/Y', strtotime( $date ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		$date_arr      = explode( '/', $date );
		$date_formated = strtotime( "{$date_arr[2]}/{$date_arr[1]}/{$date_arr[0]}" );// "Y-m-d".
		$date_formated = date( $format, $date_formated ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		return $date_formated;
	}

	public function export_now() {
		if ( isset( $_POST['export_xml'] ) && 'Export xml' === sanitize_text_field( $_POST['export_xml'] ) ) : //phpcs:ignore
			$bkx_export_obj = new BkxExport();
			$bkx_export_obj->export_now();
			die;
		endif;
	}

	/**
	 * Import_now
	 */
	public function import_now() {
		if ( isset( $_POST['import_xml'] ) && 'Import Xml' === sanitize_text_field( $_POST['import_xml'] ) ) : //phpcs:ignore
			$bkx_import = new BkxImport();
			$bkx_import->import_now();
		endif;
	}

	/**
	 * Bkx_booking_columns
	 *
	 * @param $existing_columns
	 *
	 * @return mixed|void
	 */
	function bkx_booking_columns( $existing_columns ) {
		unset( $existing_columns );
		$columns = $this->bkx_booking_columns_data();
		// Dashboard Column manage to show data ( Fetch Data from Other Setting Tab).
		$bkx_dashboard_column_selected = bkx_crud_option_multisite( 'bkx_dashboard_column' );
		$bkx_booking_columns_data      = apply_filters( 'bkx_dashboard_booking_columns_before', $columns );
		if ( ! empty( $bkx_booking_columns_data ) ) {
			foreach ( $bkx_booking_columns_data as $key => $booking_columns ) {
				if ( 'cb' !== $key && ! empty( $bkx_dashboard_column_selected ) && ! in_array( $key, $bkx_dashboard_column_selected, true ) ) {
					unset( $columns[ $key ] );
				}
			}
		}
		$columns = apply_filters( 'bkx_dashboard_booking_columns_after', $columns );
		return $columns;
	}

	/**
	 * Bkx_booking_columns_data
	 *
	 * @return mixed|void
	 */
	public function bkx_booking_columns_data() {
		$seat_alias     = bkx_crud_option_multisite( 'bkx_alias_seat' );
		$base_alias     = bkx_crud_option_multisite( 'bkx_alias_base' );
		$addition_alias = bkx_crud_option_multisite( 'bkx_alias_addition' );

		$columns                  = array();
		$columns['cb']            = '<input type="checkbox" />';
		$columns['order_status']  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'bookingx' ) . '">' . esc_attr__( 'Current Status', 'bookingx' ) . '</span>';
		$columns['booking_date']  = __( 'Date', 'bookingx' );
		$columns['booking_time']  = __( 'Time', 'bookingx' );
		$columns['duration']      = __( 'Duration', 'bookingx' );
		$columns['client_name']   = __( 'Name', 'bookingx' );
		$columns['client_phone']  = __( 'Phone', 'bookingx' );
		$columns['seat']          = sprintf( __( '%s', 'bookingx' ), $seat_alias ); //phpcs:ignore
		$columns['service']       = sprintf( __( '%s', 'bookingx' ), $base_alias ); //phpcs:ignore
		$columns['extra']         = sprintf( __( '# of %s', 'bookingx' ), $addition_alias ); //phpcs:ignore
		$columns['order_total']   = __( 'Total', 'bookingx' );
		$columns['order_actions'] = __( 'Actions', 'bookingx' );
		$columns['order_view']    = __( 'View', 'bookingx' );

		$columns = apply_filters( 'bkx_dashboard_booking_columns', $columns );

		return $columns;
	}

	/**
	 * Render_bkx_booking_columns
	 *
	 * @param $column
	 *
	 * @throws Exception
	 */
	public function render_bkx_booking_columns( $column ) {
		global $post;
		if ( empty( $post->ID ) ) {
			return;
		}
		$order_obj        = new BkxBooking();
		$order_meta       = $order_obj->get_order_meta_data( $post->ID );
		$base_time_option = get_post_meta( $post->ID, 'base_time_option', true );
		$base_time_option = ( isset( $base_time_option ) && '' !== $base_time_option ) ? $base_time_option : 'H';
		$total_time       = '-';
		$date_format      = bkx_crud_option_multisite( 'date_format' );
		if ( isset( $base_time_option ) && 'H' === $base_time_option ) {
			$total_time = getDateDuration( $order_meta );
			$duration   = getDuration( $order_meta );
			$date_data  = sprintf( __( '%s', 'bookingx' ), date( $date_format, strtotime( $order_meta['booking_start_date'] ) ) ); //phpcs:ignore
		} else {
			list($date_data, $duration) = getDayDateDuration( $post->ID );
		}
		switch ( $column ) {
			case 'order_status':
				$order_status = $order_obj->get_order_status( $post->ID );
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $order_status ) ); //phpcs:ignore
				break;
			case 'booking_date':
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $date_data ) ); //phpcs:ignore
				break;
			case 'booking_time':
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $total_time ) ); //phpcs:ignore
				break;
			case 'duration':
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $duration ) ); //phpcs:ignore
				break;
			case 'client_name':
				echo sprintf( esc_html( __( '%1$s %2$s', 'bookingx' ) ), esc_html( $order_meta['first_name'] ), esc_html( $order_meta['last_name'] ) ); //phpcs:ignore
				break;
			case 'client_phone':
				$client_phone = $order_meta['phone'];
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $client_phone ) ); //phpcs:ignore
				break;
			case 'seat':
				$seat_arr = $order_meta['seat_arr'];
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $seat_arr['title'] ) ); //phpcs:ignore
				break;
			case 'service':
				$base_arr = $order_meta['base_arr'];
				echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( $base_arr['main_obj']->post->post_title ) ); //phpcs:ignore
				break;
			case 'extra':
				if ( isset( $order_meta['extra_arr'] ) && '' !== $order_meta['extra_arr'] ) {
					$extra_arr = $order_meta['extra_arr'];
					echo sprintf( esc_html( __( '%s', 'bookingx' ) ), esc_html( count( $extra_arr ) ) ); //phpcs:ignore
				}
				break;
			case 'order_total':
				$currency        = isset( $order_meta['currency'] ) && '' !== $order_meta['currency'] ? $order_meta['currency'] : bkx_get_current_currency();
				//$currency_option = bkx_crud_option_multisite( 'currency_option' );
				$total_price     = apply_filters( 'bkx_booking_total_price_update', $order_meta['total_price'], $post->ID );
				echo sprintf( esc_html( __( '%1$s%2$d %3$s', 'bookingx' ) ), esc_html( $currency ), esc_html( $total_price ), "" ); //phpcs:ignore
				break;
			case 'order_actions':
				$order_status   = strtolower( $order_obj->get_order_status( $post->ID ) );
				$exclude_status = array( 'completed', 'missed', 'cancelled' );
				$pending        = ( 'pending' === $order_status ) ? 'selected' : '';
				$ack            = ( 'acknowledged' === $order_status ) ? 'selected' : '';
				$completed      = ( 'completed' === $order_status ) ? 'selected' : '';
				$missed         = ( 'missed' === $order_status ) ? 'selected' : '';
				$cancelled      = ( 'cancelled' === $order_status ) ? 'selected' : '';

				$business_address_1   = bkx_crud_option_multisite( 'bkx_business_address_1' );
				$business_address_2   = bkx_crud_option_multisite( 'bkx_business_address_2' );
				$bkx_business_city    = bkx_crud_option_multisite( 'bkx_business_city' );
				$bkx_business_state   = bkx_crud_option_multisite( 'bkx_business_state' );
				$bkx_business_zip     = bkx_crud_option_multisite( 'bkx_business_zip' );
				$bkx_business_country = bkx_crud_option_multisite( 'bkx_business_country' );

				$full_address = "{$business_address_1},{$business_address_2},{$bkx_business_city},{$bkx_business_state},{$bkx_business_zip},{$bkx_business_country}";
				if ( isset( $post_json ) ) {
					echo '<input type="hidden" id="post_ids" value="' . esc_attr( $post_json ) . '">';
				}
				echo '<input type="hidden" id="address_val' . esc_attr( $post->ID ) . '" value="' . esc_attr( $full_address ) . '">';
				$status_dropdown  = '';
				$status_dropdown .= '<select name="bkx-action" class="bkx_action_status" style="width:91px;" id="bulk-action-selector-top">';
				$status_dropdown .= '<option value="' . esc_attr( $post->ID ) . '_pending" ' . esc_attr( $pending ) . '>Pending</option>';
				$status_dropdown .= '<option value="' . esc_attr( $post->ID ) . '_ack" ' . esc_attr( $ack ) . '>Acknowledged</option>';
				$status_dropdown .= '<option value="' . esc_attr( $post->ID ) . '_completed" ' . esc_attr( $completed ) . '>Completed</option>';
				$status_dropdown .= '<option value="' . esc_attr( $post->ID ) . '_missed" ' . esc_attr( $missed ) . '>Missed</option>';
				$status_dropdown .= '<option value="' . esc_attr( $post->ID ) . '_cancelled" ' . esc_attr( $cancelled ) . '>Cancelled</option></select>';
				if ( ! in_array( $order_status, $exclude_status, true ) ) {
					echo $status_dropdown; //phpcs:ignore
				} else {
					?>
					<script type="text/javascript">
						jQuery(function () {
							jQuery("#cb-select-<?php echo esc_attr( $post->ID ); ?>").attr('disabled', 'disabled');
						});
					</script>
					<?php
					echo esc_html( ucwords( $order_status ) );
				}
				break;
			case 'order_view':
				echo sprintf( __( '<span class="view_href_booking-' . esc_attr( $post->ID ) . '"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx' ), 'javascript:bkx_view_summary(\'' . esc_attr( $post->ID ) . '\',\'' . esc_attr( $order_meta['first_name'] ) . ' ' . esc_attr( $order_meta['last_name'] ) . '\')', 'View', 'View' ); //phpcs:ignore
				echo sprintf( __( '<span class="close_booking-' . esc_attr( $post->ID ) . '" style="display:none;"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx' ), 'javascript:close_booking(\'' . esc_attr( $post->ID ) . '\')', 'Close', 'Close' ); //phpcs:ignore
				echo '<div class="loader-' . esc_attr( $post->ID ) . '" style="display:none;"> Please wait..</div>';
				break;
		}
	}

	/**
	 * @return string
	 */
	public function sorting_bkx_booking_columns() {
		$columns['booking_date'] = 'booking_date';
		return $columns;
	}

	/**
	 * Bkx_change_view_link
	 *
	 * @param $permalink
	 * @param $post
	 *
	 * @return mixed|string
	 * @throws Exception
	 */
	public function bkx_change_view_link( $permalink, $post ) {
		if ( 'bkx_booking' === $post->post_type ) {
			$order_obj  = new BkxBooking();
			$order_meta = $order_obj->get_order_meta_data( $post->ID );
			$permalink  = '';
			if ( ! empty( $order_meta ) && isset( $order_meta['first_name'] ) ) {
				$permalink = 'javascript:bkx_view_summary(\'' . esc_attr( $post->ID ) . '\',\'' . esc_attr( $order_meta['first_name'] ) . ' ' . esc_attr( $order_meta['last_name'] ) . '\')';
			}
		}
		return $permalink;
	}

	/**
	 * Bkx_booking_bulk_admin_footer
	 *
	 * @return false|string
	 */
	public function bkx_booking_bulk_admin_footer() {
		global $post_type;

		ob_start();
		if ( 'bkx_booking' === $post_type ) {
			$bkx_view_id   = '';
			$bkx_view_name = '';
			if ( isset( $_REQUEST['view'] ) ) { //phpcs:ignore
				$bkx_view_id = sanitize_text_field( $_REQUEST['view'] ); //phpcs:ignore
			}
			if ( isset( $_REQUEST['bkx_name'] ) ) { //phpcs:ignore
				$bkx_view_name = sanitize_text_field( $_GET['bkx_view_name'] ); //phpcs:ignore
			}
			?>
			<script type="text/javascript">
				function bkx_view_summary(post_id, name, colour_json) {
					jQuery('.loader-' + post_id).show();
					var data = {
						'action': 'bkx_action_view_summary',
						'post_id': post_id
					};
					jQuery.post(ajaxurl, data, function (response) {
						var bkx_row_action_view = jQuery('#post-' + post_id + ' .row-actions .view');
						jQuery('.loader-' + post_id).hide();
						jQuery('.close_booking-' + post_id).show();
						jQuery(bkx_row_action_view).hide();
						jQuery("<span class=\"close close_booking-" + post_id + "\" style=\"\"><a href=\"javascript:close_booking(" + post_id + ")\" title=\"Close\" style=\"padding: 0 5px;\">Close</a></span>").insertAfter(bkx_row_action_view);
						var postobj = jQuery('#post-' + post_id);
						jQuery('.view_href_booking-' + post_id).hide();
						jQuery("<tr class='view_summary view_summary-" + post_id + "'>" + response).insertAfter(postobj).slideDown('slow');
					});
				}

				function close_booking(post_id) {
					var bkx_row_action_close = jQuery('#post-' + post_id + ' .row-actions .close');
					var bkx_row_action_view = jQuery('#post-' + post_id + ' .row-actions .view');
					jQuery('.close_booking-' + post_id).hide();
					jQuery('.view_href_booking-' + post_id).show();
					jQuery('.view_summary-' + post_id).hide();
					jQuery(bkx_row_action_view).show();
					jQuery(bkx_row_action_close).remove();
				}

				jQuery(function () {
					var bkx_view_id = '<?php echo esc_html( $bkx_view_id ); ?>';
					var bkx_view_name = '<?php echo esc_html( $bkx_view_name ); ?>';
					if (bkx_view_id) {
						jQuery('#post-' + bkx_view_id).focus();
						bkx_view_summary(bkx_view_id, bkx_view_name);
					}
					jQuery('.search_by_dates').on('change', function () {
						var search_by_dates = jQuery(this).val();
						if (search_by_dates === 'choose_date') {
							jQuery('.search_by_dates_section').html('');
							jQuery('.search_by_dates_section').append('<input type="hidden" name="search_by_dates" value="choose_date" >');
							jQuery('.search_by_dates_section').append('<input type="text" name="search_by_selected_date" placeholder="Select a date" class="search_by_selected_date">');
							jQuery(".search_by_selected_date").datepicker();
						}
					});
					var span_trash = jQuery('.row-actions').find('span.trash').find('a');
					jQuery(".listing_view").nextAll('#post-query-submit').remove();
					jQuery(span_trash).click(function () {
						if (confirm('Are you sure want to Cancel this booking?')) {
						} else {
							return false;
						}
					});

					jQuery('.row-actions').find('span.trash').find('a').text('Cancel');
					jQuery('<option>').val('mark_pending').text('<?php esc_html_e( 'Pending', 'bookingx' ); ?>').appendTo('select[name="action"]');

					jQuery('<option>').val('mark_ack').text('<?php esc_html_e( 'Acknowledged', 'bookingx' ); ?>').appendTo('select[name="action"]');

					jQuery('<option>').val('mark_completed').text('<?php esc_html_e( 'Completed', 'bookingx' ); ?>').appendTo('select[name="action"]');

					jQuery('<option>').val('mark_missed').text('<?php esc_html_e( 'Missed', 'bookingx' ); ?>').appendTo('select[name="action"]');

					jQuery('<option>').val('mark_cancelled').text('<?php esc_html_e( 'Cancelled', 'bookingx' ); ?>').appendTo('select[name="action"]');

					jQuery('<option>').val('mark_pending').text('<?php esc_html_e( 'Pending', 'bookingx' ); ?>').appendTo('select[name="action2"]');

					jQuery('<option>').val('mark_ack').text('<?php esc_html_e( 'Acknowledged', 'bookingx' ); ?>').appendTo('select[name="action2"]');

					jQuery('<option>').val('mark_completed').text('<?php esc_html_e( 'Completed', 'bookingx' ); ?>').appendTo('select[name="action2"]');

					jQuery('<option>').val('mark_missed').text('<?php esc_html_e( 'Missed', 'bookingx' ); ?>').appendTo('select[name="action2"]');

					jQuery('<option>').val('mark_cancelled').text('<?php esc_html_e( 'Cancelled', 'bookingx' ); ?>').appendTo('select[name="action2"]');

				});

				jQuery('.bkx_action_status').on('change', function () {
					if (confirm('Are you sure want to change status?')) {
						var bkx_action_status = jQuery(this).val();
						var data = {
							'action': 'bkx_action_status',
							'status': bkx_action_status
						};

						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						jQuery.post(ajaxurl, data, function (response) {
							location.reload();
						});
					} else {
						return false;
					}
				});
				<?php
				$listing_view = isset( $_REQUEST['listing_view'] ) ? sanitize_text_field( $_REQUEST['listing_view'] ) : ''; //phpcs:ignore
				if ( 'weekly' === $listing_view || 'monthly' === $listing_view ) {
					$this->bkx_generate_listing_view( $listing_view );
					?>
				jQuery("<div class='weekly-data' id='calendar'></div>").insertBefore(".wp-list-table").slideDown('slow');
				jQuery(".wp-list-table").hide();
				<?php } ?>
			</script>
			<?php
			return ob_get_contents();
		}
	}

	/**
	 * Bkx_booking_search_by_dates
	 *
	 * @param string $post_type Post Type.
	 * @param null   $which
	 */
	public function bkx_booking_search_by_dates( $post_type, $which = null ) {
		// only add filter to post type you want.
		if ( 'bkx_booking' === $post_type ) {
			// change this to the list of values you want to show.
			// in 'label' => 'value' format.
			$values          = array(
				'All dates'     => '',
				'Today'         => 'today',
				'Tomorrow'      => 'tomorrow',
				'This Week'     => 'this_week',
				'Next Week'     => 'next_week',
				'This Month'    => 'this_month',
				'Select a Date' => 'choose_date',
			);
			$search_by_dates = isset( $_GET['search_by_dates'] ) ? sanitize_text_field( $_GET['search_by_dates'] ) : ''; //phpcs:ignore

			if ( 'choose_date' === $search_by_dates ) {
				$search_by_dates = '';
			}
			?>
			<div class="search_by_dates_section" style="float: left;">
				<select name="search_by_dates" class="search_by_dates">
					<?php
					foreach ( $values as $label => $value ) {
						printf(
							'<option value="%s" %s> %s </option>',
							esc_html( $value ),
							$value === $search_by_dates ? ' selected="selected"' : '',
							esc_html( $label )
						);
					}
					?>
				</select>
			</div>
			<input type="submit" name="filter_action" style="float: left;" id="post-query-submit" class="button" value="Filter">
			<?php
		}
	}

	/**
	 * Bkx_booking_seat_view
	 *
	 * @param $post_type
	 * @param null      $which
	 */
	public function bkx_booking_seat_view( $post_type, $which = null ) {

		if ( 'bkx_booking' === $post_type ) {
            global $wp_query;
            $seat_view               = isset( $wp_query->query_vars['seat_view'] ) ? (int) $wp_query->query_vars['seat_view'] : '';
			$args           = array(
				'posts_per_page'   => -1,
				'post_type'        => 'bkx_seat',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			);
			$get_seat_array = get_posts( $args );
			$alias_seat     = bkx_crud_option_multisite( 'bkx_alias_seat' );
			if ( ! empty( $get_seat_array ) ) {

				foreach ( $get_seat_array as $key => $seat_data ) {
					$seat_obj[ $seat_data->ID ] = $seat_data->post_title;
				}
			}

			if ( ! empty( $seat_obj ) ) :
				?>
				<select name="seat_view" class="seat_view">
					<option> Select <?php echo esc_html( $alias_seat ); ?> </option>
					<?php

					//$seat_view = isset( $_GET['seat_view'] ) ? sanitize_text_field( $_GET['seat_view'] ) : ''; //phpcs:ignore
					foreach ( $seat_obj as $seat_id => $seat_name ) {
						printf(
							'<option value="%s" %s>%s</option>',
							esc_html( $seat_id ),
							$seat_id === $seat_view ? ' selected="selected"' : '',
							esc_html( $seat_name )
						);
					}
					?>
				</select>
				<input type="submit" name="filter_action" style="float: left;" class="button seat_view" value="view">
				<?php
			endif;
		}
	}

	/**
	 * Bkx_booking_listing_view
	 *
	 * @param $post_type
	 * @param null      $which
	 */
	public function bkx_booking_listing_view( $post_type, $which = null ) {
		// only add filter to post type you want.
		if ( 'bkx_booking' === $post_type ) {

			$values = array(
				'Agenda'  => 'agenda',
				'Weekly'  => 'weekly',
				'Monthly' => 'monthly',
			);
			?>
			<select name="listing_view" class="listing_view">
				<?php
				$listing_view = isset( $_GET['listing_view'] ) ? sanitize_text_field( $_GET['listing_view'] ) : ''; //phpcs:ignore
				foreach ( $values as $label => $value ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_html( $value ),
						$value === $listing_view ? ' selected="selected"' : '',
						esc_html( $label )
					);
				}
				?>
			</select>
			<input type="submit" name="filter_action" style="float: left;" class="button listing_view" value="Change view">
			<?php
		}
	}

	/**
	 * Bkx_generate_listing_view
	 *
	 * @param $type
	 */
	public function bkx_generate_listing_view( $type ) {
		if ( 'weekly' === $type || 'monthly' === $type ) {
			$default_view = 'weekly' === $type ? 'timeGridWeek' : 'dayGridMonth';
			// $bkx_calendar_json_data = bkx_crud_option_multisite( 'bkx_calendar_json_data' );
			$bkx_booking            = new BkxBooking();
			$search['booking_date'] = date( 'Y-m-d' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$search['by']           = 'future';
			$search['type']         = $type;
			$order_statuses         = array( 'bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed' );
			$search['status']       = $order_statuses;
			$bkx_calendar_json_data = $bkx_booking->CalendarJsonData( $search );
			if ( isset( $bkx_calendar_json_data ) && ! empty( $bkx_calendar_json_data ) ) {
				?>
				document.addEventListener('DOMContentLoaded', function() {
				var calendarEl = document.getElementById('calendar');

				var calendar = new FullCalendar.Calendar(calendarEl, {
				initialDate: '<?php echo date( 'Y-m-d' ); ?>',
				initialView: '<?php echo $default_view; ?>',
				nowIndicator: true,
				headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
				},
				navLinks: true, // can click day/week names to navigate views
				editable: true,
				selectable: true,
				selectMirror: true,
				dayMaxEvents: true, // allow "more" link when too many events
				events: [
				<?php echo $bkx_calendar_json_data; //phpcs:ignore ?>
				]
				});

				calendar.render();
				});
				<?php
			}
		}
	}

	/**
	 * Bkx_create_category_label
	 *
	 * @param string $bkx_alias_base Alias Name.
	 *
	 * @return array
	 */
	public function bkx_create_category_label( string $bkx_alias_base ): array {
		return array(
			'name'              => __( $bkx_alias_base . ' Category', 'bookingx' ), //phpcs:ignore
			'singular_name'     => __( $bkx_alias_base . ' Category', 'bookingx' ), //phpcs:ignore
			'search_items'      => __( 'Search ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'all_items'         => __( 'All ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'parent_item'       => __( 'Parent ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'parent_item_colon' => __( 'Parent ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'edit_item'         => __( 'Edit ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'update_item'       => __( 'Update ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'add_new_item'      => __( 'Add New ' . ucwords( $bkx_alias_base ) . ' Category', 'bookingx' ), //phpcs:ignore
			'new_item_name'     => __( 'New ' . ucwords( $bkx_alias_base ) . ' Category Name', 'bookingx' ), //phpcs:ignore
			'menu_name'         => __( 'Categories', 'bookingx' ),
		);
	}

}

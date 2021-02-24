<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * BookingX Core Base Meta Box Class Load.
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BkxBaseMetabox' ) ) {
	add_action( 'load-post.php', array( 'BkxBaseMetabox', 'init' ) );
	add_action( 'load-post-new.php', array( 'BkxBaseMetabox', 'init' ) );
	add_action( 'load-edit.php', array( 'BkxBaseMetabox', 'init' ) );

	/**
	 * Class BkxBaseMetabox
	 *
	 * @property BkxBaseMetabox instance
	 */
	class BkxBaseMetabox {

        //phpcs:disable
     protected static $instance;

     protected $post_type = 'bkx_base';

     public static function init() {
             null === self::$instance and self::$instance = new self();
            return self::$instance;
     }
     //phpcs:enable

		/**
		 * BkxBaseMetabox constructor.
		 */
		public function __construct() {
			if ( false === is_admin() ) {
				return;
			}
			add_action( 'add_meta_boxes', array( $this, 'add_bkx_base_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_bkx_base_metaboxes' ), 10, 3 );
			add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, $this->post_type . '_columns_head' ), 10, 1 );
			add_filter( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, $this->post_type . '_columns_content' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'bkx_base_wp_enqueue_scripts' ) );
		}

		/**
		 * Enqueue Scripts for Service Meta Box
		 */
		public function bkx_base_wp_enqueue_scripts() {
			global $post;

			if ( ! empty( $post ) && 'bkx_base' !== $post->post_type ) {
				return;
			}

			$seat_alias = bkx_crud_option_multisite( 'bkx_alias_seat' );
			$base_alias = bkx_crud_option_multisite( 'bkx_alias_base' );
			wp_enqueue_script( 'iris' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_register_script( 'bkx-base-validate', BKX_PLUGIN_DIR_URL . 'public/js/admin/bkx-base-validate.js', false, BKX_PLUGIN_VER, true );
			$translation_array = array(
				'plugin_url' => BKX_PLUGIN_DIR_URL,
				'seat_alias' => $seat_alias,
				'base_alias' => $base_alias,
			);
			wp_localize_script( 'bkx-base-validate', 'base_obj', $translation_array );
			wp_enqueue_script( 'bkx-base-validate' );
		}

		/**
		 * Adding Meta Box
		 */
		public function add_bkx_base_metaboxes() {
			$alias = bkx_crud_option_multisite( 'bkx_alias_base' );
			// translators: Base Meta Label.
			add_meta_box( 'bkx_base_boxes', sprintf( __( '%s Details', 'bookingx' ), esc_html( $alias ) ), array( $this, 'bkx_base_boxes_metabox_callback' ), 'bkx_base', 'normal', 'high' );
		}

		/**
		 * Adding Meta Box Call Back
		 */
		public function bkx_base_boxes_metabox_callback( $post ) {
			$base_alias     = bkx_crud_option_multisite( 'bkx_alias_base' );
			$addition_alias = bkx_crud_option_multisite( 'bkx_alias_addition' );
			wp_nonce_field( 'bkx_base_boxes_metabox', 'bkx_base_boxes_metabox_nonce' );
			// Get Seat post Array.
			$args           = array(
				'posts_per_page'   => -1,
				'post_type'        => 'bkx_seat',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			);
			$get_seat_array = get_posts( $args );
			$values         = get_post_custom( $post->ID );

			$base_price          = isset( $values['base_price'] ) ? esc_html( $values['base_price'][0] ) : '';
			$base_time_option    = isset( $values['base_time_option'] ) ? esc_html( $values['base_time_option'][0] ) : '';
			$base_month          = isset( $values['base_month'] ) ? esc_html( $values['base_month'][0] ) : '';
			$base_day            = isset( $values['base_day'] ) ? esc_html( $values['base_day'][0] ) : '';
			$base_hours          = isset( $values['base_hours'] ) ? esc_html( $values['base_hours'][0] ) : 0;
			$base_extended_limit = isset( $values['base_extended_limit'] ) ? esc_html( $values['base_extended_limit'][0] ) : '';

			$base_minutes           = isset( $values['base_minutes'] ) ? esc_html( $values['base_minutes'][0] ) : '';
			$base_is_extended       = isset( $values['base_is_extended'] ) ? esc_html( $values['base_is_extended'][0] ) : 'N';
			$base_is_allow_addition = isset( $values['base_is_allow_addition'] ) ? esc_html( $values['base_is_allow_addition'][0] ) : 'Y';
			$base_is_unavailable    = isset( $values['base_is_unavailable'] ) ? esc_html( $values['base_is_unavailable'][0] ) : '';
			$base_unavailable_from  = isset( $values['base_unavailable_from'] ) ? esc_html( $values['base_unavailable_from'][0] ) : '';
			$base_unavailable_till  = isset( $values['base_unavailable_till'] ) ? esc_html( $values['base_unavailable_till'][0] ) : '';
			if ( ! empty( $values['base_selected_seats'][0] ) ) {
				$res_seat_final = maybe_unserialize( $values['base_selected_seats'][0] );
				$res_seat_final = maybe_unserialize( $res_seat_final );
			}
			$base_minute   = '';
			$base_seat_all = isset( $values['base_seat_all'] ) ? esc_html( $values['base_seat_all'][0] ) : '';
			$alias_seat    = bkx_crud_option_multisite( 'bkx_alias_seat' );
			?>
			<div class="error" id="error_list" style="display:none;"></div>
			<div class="active" id="base_name">
			<?php
			// translators: Base Price Label.
			printf( __( '%1$s  Price <span class="bkx-require">(*) Required</span>', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped 
			$base_price_html = 0;
			if ( isset( $base_price ) && '' !== $base_price ) {
				$base_price_html = esc_html( $base_price );
			}
			?>
				<div class="plugin-description">
					<input name="base_price" type="text" value="<?php echo esc_attr( $base_price_html ); ?>" id="id_base_price">
				</div>
			</div>

			<div class="active" id="months_days_times">
			<?php
			// translators: days, hours or minutes Label.
			printf( __( 'Is %1$s  time in  days, hours or minutes <span class="bkx-require">(*) Required</span>', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped 
			?>
				<div class="plugin-description">
					<select name="base_months_days_times" id="id_base_months_days_times" onchange=""
							class="medium gfield_select" tabindex="4">
						<option value=""><?php esc_html_e( 'Select Time Option', 'bookingx' ); ?></option>
						<option value="HourMinutes" 
			<?php
			if ( 'H' === $base_time_option ) {
				echo "selected='selected'";
			}
			?>
						><?php esc_html_e( 'Hour and Minutes', 'bookingx' ); ?></option>
						<option value="Days" 
			<?php
			if ( 'D' === $base_time_option ) {
				echo "selected='selected'";
			}
			?>
						><?php esc_html_e( 'Days', 'bookingx' ); ?></option>
					</select>
				</div>
			</div>
			<div class="active" id="months" style="display: none">
			<?php
			// translators: Number of Months Label.
			printf( __( 'Number of Months for %1$s Time <span class="bkx-require">(*) Required</span>', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			$base_month = ( isset( $base_month ) && '' !== $base_month ) ? $base_month : '';
			?>
				<div class="plugin-description">
					<input name="base_months" type="text" value="<?php echo esc_attr( $base_month ); ?>" id="id_base_months">
				</div>

			</div>
			<div class="active" id="days" style="display: none">
			<?php
			// translators: Number of Days Label.
			printf( __( 'Number of Days for %1$s Time <span class="bkx-require">(*) Required</span>', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			$base_day = ( isset( $base_day ) && '' !== $base_day ) ? $base_day : '';
			?>
				<div class="plugin-description">
					<input name="base_days" type="text" value="<?php echo esc_attr( $base_day ); ?>" id="id_base_days">
				</div>

			</div>
			<div class="active" id="hours_minutes" style="display: none">
			<?php
			// translators: Number of Days Label.
			printf( __( '%1$s  Time In Hours and Minutes <span class="bkx-require">(*) Required</span>', 'bookingx' ), esc_html( $base_alias ) );  // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			// translators: Base Hour Label.
          $base_hours_html = sprintf( esc_html__( '%1$s', 'bookingx' ), esc_html( $base_hours ) ); // phpcs:ignore
			?>
				<div class="plugin-description">
					<input name="base_hours_minutes" size="1" type="text" value="<?php echo esc_attr( $base_hours_html ); ?>" id="id_base_hours_minutes">
			<?php
			if ( isset( $base_minutes ) ) {
				$base_minute = $base_minutes;
			}
			?>
					<select name="base_minutes" id="id_base_minutes">
						<option value=00 
			<?php
			if ( 15 === $base_minute ) {
				echo 'selected';
			}
			?>
						><?php esc_html_e( '00', 'bookingx' ); ?></option>
						<option value=15 
			<?php
			if ( 15 === $base_minute ) {
				echo 'selected';
			}
			?>
						><?php esc_html_e( '15', 'bookingx' ); ?></option>
						<option value=30 
			<?php
			if ( 30 === $base_minute ) {
				echo 'selected';
			}
			?>
						><?php esc_html_e( '30', 'bookingx' ); ?></option>
						<option value=45 
			<?php
			if ( 45 === $base_minute ) {
				echo 'selected';
			}
			?>
						><?php esc_html_e( '45', 'bookingx' ); ?></option>
					</select>
				</div>
			</div>
			<div class="active" id="extended">
			<?php
			// translators: Extended  Label.
                printf( esc_html__( 'Can %1$s time be extended', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:ignore ?>
				<div class="plugin-description">
					<ul class="bkx_radio" id="input_2_15">
						<li class="bkx_choice_15_0">
							<input name="base_extended" type="radio" value="Yes" id="id_base_extended_yes" tabindex="10"
								<?php
								if ( 'Y' === $base_is_extended ) {
									echo "checked='checked'";
								}
								?>
							>
							<label for="choice_15_0"><?php esc_html_e( 'Yes', 'bookingx' ); ?></label>
						</li>
						<li class="bkx_choice_15_1">
							<input name="base_extended" type="radio" value="No" id="id_base_extended_no" tabindex="11"
			<?php
			if ( 'N' === $base_is_extended ) {
				echo "checked='checked'";
			}
			?>
							>
							<label for="choice_15_1"><?php esc_html_e( 'No', 'bookingx' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<div class="active" id="base_extended_input" style="display: none;">
			<?php
			// translators: Max Limit Label.
			printf( __( 'Max limit of %1$s extends', 'bookingx' ), esc_html( $base_alias ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			$base_extended_limit = ( isset( $base_extended_limit ) && ( '' !== $base_extended_limit ) ) ? $base_extended_limit : '';
			?>
				<div class="plugin-description">
					<input name="base_extended_limit" type="number" value="<?php echo esc_attr( $base_extended_limit ); ?>" id="id_base_extended_limit">
				</div>
			</div>
			<div class="active" id="base_name">
			<?php
			// translators: Seats available Label.
			printf( __( 'This %1$s  is available to the following %2$s <span class="bkx-require">(*) Required</span>', 'bookingx' ), esc_html( $base_alias ), esc_html( $alias_seat ) );  // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			?>
				<div class="plugin-description">
					<ul class="gfield_checkbox" id="input_2_9">
			<?php
			$selected = '';
			if ( ! empty( $get_seat_array ) && ! is_wp_error( $get_seat_array ) ) :
				?>
							<li class="bkx_choice_9_1">
								<input name="base_seat_all" <?php echo ( $base_seat_all ) ? 'checked' : ''; ?> type="checkbox" value="All" id="id_base_seat_all" tabindex="12">
								<label for="choice_9_1"><?php esc_html_e( 'All', 'bookingx' ); ?></label>
							</li>
				<?php
				foreach ( $get_seat_array as $value ) {
					if ( isset( $res_seat_final ) && count( $res_seat_final ) > 0 ) {
						$selected = false;
						if ( in_array( $value->ID, $res_seat_final, true ) || isset( $base_seat_all ) && 'All' === $base_seat_all ) {
							$selected = true;
						}
					}
					?>
								<li class="bkx_choice_9_2">
									<input name="base_seats[]" type="checkbox" value="<?php echo esc_attr( $value->ID ); ?>" tabindex="13" class="base-seat-checkboxes"
					<?php
					if ( true === $selected ) {
						echo "checked='checked'";
					}
					?>
										>
									<label for="choice_9_2"><?php echo esc_html( $value->post_title ); ?></label>
								</li>
					<?php
				}
			else :
				?>
							<li class="bkx_choice_9_1">
								<label for="choice_9_1"><b><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=bkx_seat' ) ); ?>">
				<?php
				// translators: Any Seats available Label.
				printf( esc_html__( 'You haven\'t any %1$s, Please create %1$s now :', 'bookingx' ), esc_html( $alias_seat ) );
				?>
											</a></b></label>
							</li>
			<?php endif; ?>
					</ul>
				</div>
			</div>

			<div class="active" id="allow_addition">
			<?php
			// translators: Extra available Label.
			printf( esc_html__( 'Does this %1$s allow for %2$ss :', 'bookingx' ), esc_html( $base_alias ), esc_html( $addition_alias ) );
			?>
				<div class="plugin-description">
					<ul class="bkx_radio" id="input_2_13">
						<li class="bkx_choice_13_0">
							<input name="base_allow_addition" type="radio" value="Yes" id="choice_13_0" tabindex="18" onclick=""
			<?php
			if ( 'Y' === $base_is_allow_addition ) {
				echo "checked='checked'";
			}
			?>
							>
							<label for="choice_13_0"><?php esc_html_e( 'Yes', 'bookingx' ); ?></label>
						</li>
						<li class="bkx_choice_13_1">
							<input name="base_allow_addition" type="radio" value="No" id="choice_13_1" tabindex="19" onclick=""
			<?php
			if ( 'N' === $base_is_allow_addition ) {
				echo "checked='checked'";
			}
			?>
							>
							<label for="choice_13_1"><?php esc_html_e( 'No', 'bookingx' ); ?></label>
						</li>
					</ul>
				</div>
			</div>

			<div class="active" id="is_unavailable">
			<?php
			// translators: Base available Label.
			printf( esc_html__( 'Does the  %1$s  Unavailable? :', 'bookingx' ), esc_html( $base_alias ) );
			?>
				<div class="plugin-description">
					<input type="checkbox" name="base_is_unavailable" id="id_base_is_unavailable" value="Yes"
			<?php
			if ( 'Y' === $base_is_unavailable ) {
				echo "checked='checked'";
			}
			?>
					>
					<label><?php esc_html_e( 'Yes', 'bookingx' ); ?></label>
				</div>
			</div>
			<div class="active" id="unavailable_from">
			<?php
			$base_unavailable_from = isset( $base_unavailable_from ) ? $base_unavailable_from : '';
			?>
				<div class="plugin-description">
					<label for="id_base_unavailable_from">
			<?php
			// translators: Base available Label.
			printf( esc_html__( '%1$s Unavailable From :', 'bookingx' ), esc_html( $base_alias ) );
			?>
					</label>
					<input type="text" autocomplete="off" name="base_unavailable_from" id="id_base_unavailable_from" value="<?php echo esc_attr( $base_unavailable_from ); ?>">
				</div>
			</div>
			<div class="active" id="unavailable_till">
			<?php
			// translators: Base available Label.
			printf( esc_html__( '%1$s  Unavailable Till :', 'bookingx' ), esc_html( $base_alias ) );
			$base_unavailable_till = isset( $base_unavailable_till ) ? $base_unavailable_till : '';
			?>
				<div class="plugin-description">
					<input autocomplete="off" type="text" name="base_unavailable_till" id="id_base_unavailable_till" value="<?php echo esc_attr( $base_unavailable_till ); ?>">
				</div>
			</div>
			<?php
		}

		/**
		 * Save_bkx_base_metaboxes
		 *
		 * @param integer $post_id Post Id.
		 * @param object  $post    WP Object.
		 * @param null    $update  Update.
		 */
		public function save_bkx_base_metaboxes( $post_id, $post, $update = null ) {
			// Bail if we're doing an auto save.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return '';
			}
			// if our current user can't edit this post, bail.
			if ( 'bkx_base' !== $post->post_type ) {
				return '';
			}
			if ( 'publish' !== $post->post_status ) {
				return '';
			}
			do_action( 'bkx_base_meta_box_save', $post_id );
         // phpcs:disable WordPress.Security.NonceVerification.Missing
			$base_price                = isset( $_POST['base_price'] ) ? sanitize_text_field( wp_unslash( $_POST['base_price'] ) ) : '';
			$base_month_days_time      = isset( $_POST['base_months_days_times'] ) ? sanitize_text_field( wp_unslash( $_POST['base_months_days_times'] ) ) : '';
			$base_months               = isset( $_POST['base_months'] ) ? sanitize_text_field( wp_unslash( $_POST['base_months'] ) ) : '';
			$base_days                 = isset( $_POST['base_days'] ) ? sanitize_text_field( wp_unslash( $_POST['base_days'] ) ) : '';
			$base_hours_minutes        = isset( $_POST['base_hours_minutes'] ) ? sanitize_text_field( wp_unslash( $_POST['base_hours_minutes'] ) ) : '';
			$base_minutes              = isset( $_POST['base_minutes'] ) ? sanitize_text_field( wp_unslash( $_POST['base_minutes'] ) ) : '';
			$base_extended             = isset( $_POST['base_extended'] ) ? sanitize_text_field( wp_unslash( $_POST['base_extended'] ) ) : '';
			$base_location_type        = isset( $_POST['base_location_type'] ) ? sanitize_text_field( wp_unslash( $_POST['base_location_type'] ) ) : '';
			$base_location_mobile      = isset( $_POST['base_location_mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['base_location_mobile'] ) ) : '';
			$base_location_differ_seat = isset( $_POST['base_location_differ_seat'] ) ? sanitize_text_field( wp_unslash( $_POST['base_location_differ_seat'] ) ) : '';
			$base_street               = isset( $_POST['base_street'] ) ? sanitize_text_field( wp_unslash( $_POST['base_street'] ) ) : '';
			$base_city                 = isset( $_POST['base_city'] ) ? sanitize_text_field( wp_unslash( $_POST['base_city'] ) ) : '';
			$base_state                = isset( $_POST['base_state'] ) ? sanitize_text_field( wp_unslash( $_POST['base_state'] ) ) : '';
			$base_postcode             = isset( $_POST['base_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['base_postcode'] ) ) : '';
			$base_allow_addition       = isset( $_POST['base_allow_addition'] ) ? sanitize_text_field( wp_unslash( $_POST['base_allow_addition'] ) ) : '';
			$base_seat_all             = isset( $_POST['base_seat_all'] ) ? sanitize_text_field( wp_unslash( $_POST['base_seat_all'] ) ) : '';
			$base_is_unavailable       = isset( $_POST['base_is_unavailable'] ) ? sanitize_text_field( wp_unslash( $_POST['base_is_unavailable'] ) ) : '';
			$base_unavailable_from     = isset( $_POST['base_unavailable_from'] ) ? sanitize_text_field( wp_unslash( $_POST['base_unavailable_from'] ) ) : '';
			$base_unavailable_till     = isset( $_POST['base_unavailable_till'] ) ? sanitize_text_field( wp_unslash( $_POST['base_unavailable_till'] ) ) : '';
			$base_seats_value          = isset( $_POST['base_seats'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['base_seats'] ) ) : '';
			$base_extended_limit       = isset( $_POST['base_extended_limit'] ) ? sanitize_text_field( wp_unslash( $_POST['base_extended_limit'] ) ) : '';
         // phpcs:enable WordPress.Security.NonceVerification.Missing
			$seat_slug = array();
			if ( ! empty( $base_seats_value ) ) {
				foreach ( $base_seats_value as $key => $seat_id ) {
					$seat_data   = get_post( $seat_id );
					$seat_slug[] = $seat_data->post_name;
				}
				update_post_meta( $post_id, 'seat_slugs', $seat_slug );
			}

			if ( 'Months' === $base_month_days_time ) {
				$base_time_option = 'M';
			}
			if ( 'Days' === $base_month_days_time ) {
				$base_time_option = 'D';
			}
			if ( 'HourMinutes' === $base_month_days_time ) {
				$base_time_option = 'H';
			}
			$base_is_extended = ( 'Yes' === $base_extended ) ? 'Y' : 'N';
			if ( ( 'Fixed Location' || 'FM' ) === $base_location_type ) {
				$is_location_fixed = 'Y';
				$is_mobile_only    = 'N';
			}
			if ( 'Mobile' === $base_location_type ) {
				$is_location_fixed = 'N';
				$is_mobile_only    = ( 'Yes' === $base_location_mobile ) ? 'Y' : 'N';
			}

			$is_location_different = ( 'Yes' === $base_location_differ_seat ) ? 'Y' : 'N';
			$is_allow_addition     = ( 'Yes' === $base_allow_addition ) ? 'Y' : 'N';
			$base_is_unavailable   = ( 'Yes' === $base_is_unavailable ) ? 'Y' : 'N';
			// Make sure your data is set before trying to save .
			if ( isset( $base_price ) ) {
				update_post_meta( $post_id, 'base_price', $base_price );
			}

			if ( isset( $base_time_option ) ) {
				update_post_meta( $post_id, 'base_time_option', $base_time_option );
			}
			if ( isset( $base_months ) ) {
				update_post_meta( $post_id, 'base_month', $base_months );
			}
			if ( isset( $base_days ) ) {
				update_post_meta( $post_id, 'base_day', $base_days );
			}
			if ( isset( $base_hours_minutes ) ) {
				update_post_meta( $post_id, 'base_hours', $base_hours_minutes );
			}
			if ( isset( $base_minutes ) ) {
				update_post_meta( $post_id, 'base_minutes', $base_minutes );
			}
			if ( isset( $base_seat_all ) ) {
				update_post_meta( $post_id, 'base_seats', $base_seat_all );
			}
			if ( isset( $is_location_fixed ) ) {
				update_post_meta( $post_id, 'base_is_location_fixed', $is_location_fixed );
			}
			if ( isset( $is_mobile_only ) ) {
				update_post_meta( $post_id, 'base_is_mobile_only', $is_mobile_only );
			}
			if ( isset( $is_location_different ) ) {
				update_post_meta( $post_id, 'base_is_location_differ_seat', $is_location_different );
			}
			if ( isset( $base_city ) ) {
				update_post_meta( $post_id, 'base_city', $base_city );
			}
			if ( isset( $base_street ) ) {
				update_post_meta( $post_id, 'base_street', $base_street );
			}
			if ( isset( $base_state ) ) {
				update_post_meta( $post_id, 'base_state', $base_state );
			}
			if ( isset( $base_postcode ) ) {
				update_post_meta( $post_id, 'base_postcode', $base_postcode );
			}
			if ( isset( $is_allow_addition ) ) {
				update_post_meta( $post_id, 'base_is_allow_addition', $is_allow_addition );
			}
			if ( isset( $base_is_extended ) ) {
				update_post_meta( $post_id, 'base_is_extended', $base_is_extended );
			}
			if ( isset( $base_is_unavailable ) ) {
				update_post_meta( $post_id, 'base_is_unavailable', $base_is_unavailable );
			}
			if ( isset( $base_unavailable_from ) ) {
				update_post_meta( $post_id, 'base_unavailable_from', $base_unavailable_from );
			}
			if ( isset( $base_unavailable_till ) ) {
				update_post_meta( $post_id, 'base_unavailable_till', $base_unavailable_till );
			}
			if ( ! empty( $base_seats_value ) ) {
				update_post_meta( $post_id, 'base_selected_seats', $base_seats_value );
			}
			if ( ! empty( $base_extended_limit ) ) {
				update_post_meta( $post_id, 'base_extended_limit', $base_extended_limit );
			}
			if ( ! empty( $base_location_type ) ) {
				update_post_meta( $post_id, 'base_location_type', $base_location_type );
			}
			update_post_meta( $post_id, 'base_seat_all', $base_seat_all );
		}

		/**
		 * Bkx_base_columns_head
		 *
		 * @param  array $defaults defaults array.
		 * @return mixed
		 */
		public function bkx_base_columns_head( $defaults ): array {
			$defaults['display_shortcode_all'] = 'Shortcode [bookingx base-id="all"]';
			return $defaults;
		}

		/**
		 * Bkx_base_columns_content()
		 *
		 * @param string $column_name Column Name.
		 * @param string $post_id     Post Id.
		 */
		public function bkx_base_columns_content( $column_name, $post_id ) {
			if ( 'display_shortcode_all' === $column_name ) {
				echo '[bookingx base-id="' . esc_attr( $post_id ) . '" description="yes" image="yes" extra-info="no"]';
			}
		}
	}
}

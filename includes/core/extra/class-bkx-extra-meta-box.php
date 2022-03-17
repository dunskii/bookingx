<?php // phpcs:ignore
/**
 * BookingX Extra Meta Box Loader Class
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BkxExtraMetaBox' ) ) {
	add_action( 'load-post.php', array( 'BkxExtraMetaBox', 'init' ) );
	add_action( 'load-post-new.php', array( 'BkxExtraMetaBox', 'init' ) );
	add_action( 'load-edit.php', array( 'BkxExtraMetaBox', 'init' ) );

	/**
	 * Class BkxExtraMetaBox
	 */
	class BkxExtraMetaBox {


		/**
		 * @var
		 */
		protected static $instance;
		/**
		 * @var string
		 */
		protected $post_type = 'bkx_addition';

		/**
		 * @return BkxExtraMetaBox
		 */
		public static function init() {
			null === self::$instance and self::$instance = new self();
			return self::$instance;
		}

		/**
		 * BkxExtraMetaBox constructor.
		 */
		public function __construct() {
			if ( is_admin() == false ) {
				return;
			}
			add_action( 'add_meta_boxes', array( $this, 'add_bkx_extra_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_bkx_addition_metaboxes' ), 10, 3 );
			add_filter(
				'manage_' . $this->post_type . '_posts_columns',
				array(
					$this,
					'bkx_addition_columns_head',
				),
				10,
				1
			);
			add_filter(
				'manage_' . $this->post_type . '_posts_custom_column',
				array(
					$this,
					'bkx_addition_columns_content',
				),
				10,
				2
			);
			add_action( 'admin_enqueue_scripts', array( $this, 'bkx_extra_wp_enqueue_scripts' ) );
		}

		/**
		 *
		 */
		public function bkx_extra_wp_enqueue_scripts() {
			global $post;
			if ( ! empty( $post ) && $post->post_type != 'bkx_addition' ) {
				return;
			}
			$addition_alias    = bkx_crud_option_multisite( 'bkx_alias_addition' );
			$base_alias        = bkx_crud_option_multisite( 'bkx_alias_base' );
			$translation_array = array(
				'plugin_url'  => BKX_PLUGIN_DIR_URL,
				'extra_alias' => $addition_alias,
				'base_alias'  => $base_alias,
			);
			wp_enqueue_script( 'iris' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_register_script( 'bkx-extra-validate', BKX_PLUGIN_DIR_URL . 'public/js/admin/bkx-extra-validate.js', false, BKX_PLUGIN_VER, true );
			wp_localize_script( 'bkx-extra-validate', 'extra_obj', $translation_array );
			wp_enqueue_script( 'bkx-extra-validate' );
		}

		/**
		 *
		 */
		public function add_bkx_extra_metaboxes() {
			$alias = bkx_crud_option_multisite( 'bkx_alias_addition' );
			// translators: Extra Alias Name.
			add_meta_box(
				'bkx_base_boxes',
				sprintf( esc_html__( '%s Details', 'bookingx' ), esc_html( ucwords( $alias ) ) ),
				array(
					$this,
					'bkx_extra_boxes_metabox_callback',
				),
				'bkx_addition',
				'normal',
				'high'
			);
		}

		/**
		 * @param $post
		 */
		public function bkx_extra_boxes_metabox_callback( $post ) {
			wp_nonce_field( 'bkx_extra_boxes_metabox', 'bkx_extra_boxes_metabox_nonce' );
			$addition_alias = bkx_crud_option_multisite( 'bkx_alias_addition' );
			$base_alias     = bkx_crud_option_multisite( 'bkx_alias_base' );
			// Get Seat post Array.
			$args                    = array(
				'posts_per_page'   => - 1,
				'post_type'        => 'bkx_base',
				'post_status'      => 'publish',
				'meta_key'         => 'base_is_allow_addition',
				'meta_value'       => 'Y',
				'suppress_filters' => false,
			);
			$get_base_array          = get_posts( $args );
			$values                  = get_post_custom( $post->ID );
			$alias_seat              = bkx_crud_option_multisite( 'bkx_alias_seat' );
			$addition_price = 0;
			$addition_price          += isset( $values['addition_price'] ) ? esc_attr( $values['addition_price'][0] ) : '';
			//$extra_sale_price        = isset( $values['extra_sale_price'] ) ? esc_html( $values['extra_sale_price'][0] ) : 0;
			$addition_time_option    = isset( $values['addition_time_option'] ) ? esc_attr( $values['addition_time_option'][0] ) : '';
			$addition_overlap        = isset( $values['addition_overlap'] ) ? esc_attr( $values['addition_overlap'][0] ) : '';
			$addition_months         = isset( $values['addition_months'] ) ? esc_attr( $values['addition_months'][0] ) : '';
			$addition_days           = isset( $values['addition_days'] ) ? esc_attr( $values['addition_days'][0] ) : '';
			$addition_hours          = isset( $values['addition_hours'] ) ? intval( esc_attr( $values['addition_hours'][0] ) ) : '';
			$addition_minutes        = isset( $values['addition_minutes'] ) ? intval( esc_attr( $values['addition_minutes'][0] ) ) : '';
			$addition_is_unavailable = isset( $values['addition_is_unavailable'] ) ? esc_attr( $values['addition_is_unavailable'][0] ) : '';
			$addition_available_from = isset( $values['addition_unavailable_from'] ) ? esc_attr( $values['addition_unavailable_from'][0] ) : '';
			$addition_available_to   = isset( $values['addition_unavailable_to'] ) ? esc_attr( $values['addition_unavailable_to'][0] ) : '';
			if ( ! empty( $values['extra_selected_base'][0] ) ) {
				$res_base_final = maybe_unserialize( $values['extra_selected_base'][0] );
				$res_base_final = maybe_unserialize( $res_base_final );
			}
			if ( ! empty( $values['extra_selected_seats'][0] ) ) {
				$extra_selected_seats = maybe_unserialize( $values['extra_selected_seats'][0] );
				$extra_selected_seats = maybe_unserialize( $extra_selected_seats );
			}
			// $extra_colour = isset($values['extra_colour']) ? esc_attr($values['extra_colour'][0]) : "";
			$args           = array(
				'posts_per_page'   => - 1,
				'post_type'        => 'bkx_seat',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			);
			$get_seat_array = get_posts( $args );
			?>
			<div class="error" id="error_list" style="display:none;"></div>
			<div class="active" id="base_name">
			<?php echo esc_html( $addition_alias ); ?> Price:
				<div class="plugin-description">
					<input name="addition_price" type="text" id="id_addition_price" value="<?php echo isset( $addition_price ) ? esc_attr( $addition_price ) : ''; ?>">
				</div>
			</div>

<!--			<div class="active" id="extra_sale_price">-->
<!--				--><?php
//				// translators: Base Sale Price Label.
//				printf( __( '%1$s  Sale Price', 'bookingx' ), esc_html( $addition_alias ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
//				$extra_sale_price_html = 0;
//				if ( isset( $extra_sale_price ) && '' !== $extra_sale_price ) {
//					$extra_sale_price_html = esc_html( $extra_sale_price );
//				}
//				?>
<!--				<div class="plugin-description">-->
<!--					<input name="extra_sale_price" type="text" value="--><?php //echo esc_attr( $extra_sale_price_html ); ?><!--" id="id_extra_sale_price">-->
<!--				</div>-->
<!--			</div>-->
			<div class="active" id="months_days_times">
				Is <?php echo esc_html( $addition_alias ); ?> time in days, hours & minutes:
				<div class="plugin-description">
					<select name="addition_time_option" id="id_addition_time_option" class="medium gfield_select"
							tabindex="4">
						<!--  <option value="Months"
			<?php
			// if($addition_time_option == "M"){ echo "selected='selected'"; }
			?>
						 >Months</option> -->
						<option value="Hour and Minutes"
			 <?php
				if ( $addition_time_option == 'H' ) {
					echo "selected='selected'";
				}
				?>
						>Hour and Minutes
						</option>
						<option value="Days"
			 <?php
				if ( $addition_time_option == 'D' ) {
					echo "selected='selected'";
				}
				?>
						>Days
						</option>

					</select>
				</div>
			</div>
			<div class="active" id="hours_minutes">
			<?php
			$addition_hours = isset( $addition_hours ) && $addition_hours != '' ? $addition_hours : 00;
			?>
			<?php echo esc_html( $addition_alias ); ?> Time In Hours and Minutes :
				<div class="plugin-description">
					<input name="addition_hours_minutes" type="text" value="<?php echo esc_attr( $addition_hours ); ?>"
						   id="id_addition_hours_minutes">
			<?php
			if ( isset( $addition_minutes ) ) {
				$addition_minute = $addition_minutes;
			}
			?>
					<select name="addition_minutes" id="id_addition_minutes">
						<option value=00
			 <?php
				if ( $addition_minute == 15 ) {
					echo 'selected';
				}
				?>
						>00
						</option>
						<option value=15
			 <?php
				if ( $addition_minute == 15 ) {
					echo 'selected';
				}
				?>
						>15
						</option>
						<option value=30
			 <?php
				if ( $addition_minute == 30 ) {
					echo 'selected';
				}
				?>
						>30
						</option>
						<option value=45
			 <?php
				if ( $addition_minute == 45 ) {
					echo 'selected';
				}
				?>
						>45
						</option>
					</select>
				</div>
			</div>
			<div class="active" id="days">
				Number of Days for <?php echo esc_html( $addition_alias ); ?> Time :
			<?php $addition_days = isset( $addition_days ) ? $addition_days : ''; ?>
				<div class="plugin-description">
					<input name="addition_days" type="text" value="<?php echo esc_attr( $addition_days ); ?>"
						   id="id_addition_days">
				</div>
			</div>
			<div class="active" id="overlap" style="display: none;">
				Does this <?php echo esc_html( $addition_alias ); ?> require its own time bracket or can it overlap with
				other <?php echo esc_html( $addition_alias ); ?> :
				<div class="plugin-description">
					<ul class="bkx_radio" id="input_3_14">
						<li class="bkx_choice_14_0">
							<input name="addition_overlap" type="radio" value="Own Time Bracket" id="choice_14_0"
								   tabindex="5"
			<?php
			if ( $addition_overlap == 'N' ) {
				echo "checked='true'";
			}
			?>
							>
							<label for="choice_14_0">Own Time Bracket</label>
						</li>
						<li class="bkx_choice_14_1">
							<input name="addition_overlap" type="radio" value="Overlap" id="choice_14_1" tabindex="6"
			  <?php
				if ( $addition_overlap == 'Y' ) {
					echo "checked='true'";
				}
				?>
							>
							<label for="choice_14_1">Overlap</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="active" id="months">
				Number of Months for <?php echo esc_html( $addition_alias ); ?> Time :
				<div class="plugin-description">
					<input name="addition_months" type="text" value="<?php echo esc_attr( $addition_months ); ?>"
						   id="id_addition_months">
				</div>
			</div>

			<div class="active" id="base_name">
				This <?php echo esc_html( $addition_alias ); ?> is available to the
				following <?php echo esc_html( $base_alias ); ?>'s :
				<div class="plugin-description">
					<ul class="gfield_checkbox" id="input_2_9">
						<li class="bkx_choice_9_1">
							<input name="addition_base_all" type="checkbox" value="All" id="id_addition_base_all"
								   tabindex="12" <?php /*if($addition_bases=="All"){ echo "checked='checked'"; }*/ ?> >
							<label for="choice_9_1">All</label>
						</li>
			<?php
			if ( ! empty( $get_base_array ) && ! is_wp_error( $get_base_array ) ) :
				foreach ( $get_base_array as $value ) {
					?>
					<?php
					$selected = false;
					if ( isset( $res_base_final ) && count( $res_base_final ) > 0 ) {
						$selected = false;
						if ( in_array( $value->ID, $res_base_final ) ) {
							 $selected = true;
						}
					}
					?>
								<li class="bkx_choice_9_2">
									<input name="addition_base[]" type="checkbox"
										   value="<?php echo esc_attr( $value->ID ); ?>"
										   tabindex="13" class="extra-base-checkboxes"
					<?php
					if ( $selected == true ) {
						echo "checked='checked'";
					}
					?>
									>
									<label for="choice_9_2"><?php echo esc_html( $value->post_title ); ?></label>
								</li>
					<?php
				}
			endif;
			?>
					</ul>
				</div>
			</div>


			<div class="active" id="seat_name">
				This <?php echo esc_html( $addition_alias ); ?> is available to the
				following <?php echo esc_html( $alias_seat ); ?> :
				<div class="plugin-description">
					<ul class="gfield_checkbox" id="input_2_9">
						<li class="bkx_choice_9_1">
							<input name="seat_all" type="checkbox" value="All" id="id_seat_all" tabindex="12">
							<label for="choice_9_1">All</label>
						</li>
			<?php
			if ( ! empty( $get_seat_array ) && ! is_wp_error( $get_seat_array ) ) :
				$selected = '';
				foreach ( $get_seat_array as $value ) {
					if ( ! empty( $extra_selected_seats ) ) {
						if ( in_array( $value->ID, $extra_selected_seats ) ) {
							$selected = '1';
						} else {
							$selected = '';
						}
					}
					?>
								<li class="bkx_choice_9_2">
									<input name="seat_on_extra[]" type="checkbox"
										   value="<?php echo esc_attr( $value->ID ); ?>"
										   tabindex="13" class="extra-seat-checkboxes"
					<?php
					if ( $selected == '1' ) {
						echo "checked='checked'";
					}
					?>
									>
									<label for="choice_9_2"><?php echo esc_html( $value->post_title ); ?></label>
								</li>
					<?php
				}
			endif;
			?>
					</ul>
				</div>
			</div>
			<!--<p><strong><?php /*esc_html_e('Colour', 'bookingx'); */ ?></strong></p>
			<p><?php /*printf(esc_html__('%1$s Colour', 'bookingx'), $addition_alias); */ ?></p>
			<p><input type="text" name="extra_colour" id="id_extra_colour"
					  value="<?php /*printf(esc_html__('%1$s', 'bookingx'), $extra_colour); */ ?>"/></p>-->
			<!--only for edit form  -->
			<div class="active" id="is_unavailable">
				Does the <?php echo esc_html( $addition_alias ); ?> Unavailable ?
				<div class="plugin-description">
					<input type="checkbox" name="addition_is_unavailable" id="id_addition_is_unavailable"
						   value="Yes"
			<?php
			if ( $addition_is_unavailable == 'Y' ) {
				echo "checked='checked'";
			}
			?>
					>
					<label>Yes</label>
				</div>
			</div>
			<div class="active" id="unavailable_from">
			<?php echo esc_html( $addition_alias ); ?> Unavailable
			<?php
			$addition_available_from = ( isset( $addition_available_from ) ? $addition_available_from : '' );
			$addition_available_to   = ( isset( $addition_available_to ) ? $addition_available_to : '' );
			?>
				<div class="plugin-description">
					From <input type="text" name="addition_unavailable_from" id="id_addition_unavailable_from"
								value="<?php echo esc_attr( $addition_available_from ); ?>"> To <input type="text"
																									   name="addition_unavailable_to"
																									   id="id_addition_unavailable_to"
																									   value="<?php echo esc_attr( $addition_available_to ); ?>">
				</div>
			</div>
			<?php
		}

		/**
		 * @param $post_id
		 * @param $post
		 * @param null    $update
		 */
		public function save_bkx_addition_metaboxes( $post_id, $post, $update = null ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( $post->post_type != 'bkx_addition' ) {
				return;
			}
			if ( $post->post_status != 'publish' ) {
				return;
			}
         // phpcs:disable WordPress.Security.NonceVerification.Missing
			$addition_price = 0;
			$addition_price                += isset( $_POST['addition_price'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_price'] ) ) : '';
			$extra_sale_price              = isset( $_POST['extra_sale_price'] ) ? sanitize_text_field( wp_unslash( $_POST['extra_sale_price'] ) ) : '';
			$addition_month_days_time      = isset( $_POST['addition_time_option'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_time_option'] ) ) : '';
			$addition_months               = isset( $_POST['addition_months'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_months'] ) ) : '';
			$addition_days                 = isset( $_POST['addition_days'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_days'] ) ) : '';
			$addition_hours_minutes        = isset( $_POST['addition_hours_minutes'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_hours_minutes'] ) ) : '';
			$addition_minutes              = isset( $_POST['addition_minutes'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_minutes'] ) ) : '';
			$addition_overlap              = isset( $_POST['addition_overlap'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_overlap'] ) ) : '';
			$addition_base_all             = isset( $_POST['addition_base_all'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_base_all'] ) ) : '';
			$addition_location_differ_seat = 'N';
			$addition_location_differ_base = 'N';
			$addition_is_unavailable       = isset( $_POST['addition_is_unavailable'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_is_unavailable'] ) ) : '';
			$addition_unavailable_from     = isset( $_POST['addition_unavailable_from'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_unavailable_from'] ) ) : '';
			$addition_unavailable_to       = isset( $_POST['addition_unavailable_to'] ) ? sanitize_text_field( wp_unslash( $_POST['addition_unavailable_to'] ) ) : '';
			$extra_seats_value             = isset( $_POST['addition_base'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['addition_base'] ) ) : '';
			$checked_seats                 = isset( $_POST['seat_on_extra'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['seat_on_extra'] ) ) : '';
			$extra_colour                  = isset( $_POST['extra_colour'] ) ? sanitize_text_field( wp_unslash( $_POST['extra_colour'] ) ) : '';
			if ( isset( $_POST['addition_is_unavailable'] ) && ( $_POST['addition_is_unavailable'] == 'Yes' ) ) {
				$addition_is_unavailable = 'Y';
			}
			$addition_time_option = '';
			if ( $addition_month_days_time == 'Months' ) {
				$addition_time_option = 'M';
			}
			if ( $addition_month_days_time == 'Days' ) {
				$addition_time_option = 'D';
			}
			if ( $addition_month_days_time == 'Hour and Minutes' ) {
				$addition_time_option = 'H';
			}
			$addition_overlap_val = '';
			if ( $addition_overlap == 'Overlap' ) {
				$addition_overlap_val = 'Y';
			}
			if ( $addition_overlap == 'Own Time Bracket' ) {
				$addition_overlap_val = 'N';
			}
			$addition_differ_seat = '';
			if ( $addition_location_differ_seat == 'Yes' ) {
				$addition_differ_seat = 'Y';
			}
			if ( $addition_location_differ_seat == 'No' ) {
				$addition_differ_seat = 'N';
			}
			$addition_differ_base = '';
			if ( $addition_location_differ_base == 'Yes' ) {
				$addition_differ_base = 'Y';
			}
			if ( $addition_location_differ_base == 'No' ) {
				$addition_differ_base = 'N';
			}

			if ( isset( $addition_price ) ) {
				update_post_meta( $post_id, 'addition_price', $addition_price );
			}
			if ( isset( $extra_sale_price ) ) {
				update_post_meta( $post_id, 'extra_sale_price', $extra_sale_price );
			}
			if ( isset( $addition_time_option ) ) {
				update_post_meta( $post_id, 'addition_time_option', $addition_time_option );
			}
			if ( isset( $addition_months ) ) {
				update_post_meta( $post_id, 'addition_months', $addition_months );
			}
			if ( isset( $addition_days ) ) {
				update_post_meta( $post_id, 'addition_days', $addition_days );
			}
			if ( isset( $addition_hours_minutes ) ) {
				update_post_meta( $post_id, 'addition_hours', $addition_hours_minutes );
			}
			if ( isset( $addition_minutes ) ) {
				update_post_meta( $post_id, 'addition_minutes', $addition_minutes );
			}
			if ( isset( $addition_overlap_val ) ) {
				update_post_meta( $post_id, 'addition_overlap', $addition_overlap_val );
			}
			if ( isset( $addition_base_all ) ) {
				update_post_meta( $post_id, 'addition_bases', $addition_base_all );
			}
			if ( isset( $addition_differ_seat ) ) {
				update_post_meta( $post_id, 'addition_differ_seat', $addition_differ_seat );
			}
			if ( isset( $addition_differ_base ) ) {
				update_post_meta( $post_id, 'addition_differ_base', $addition_differ_base );
			}
			if ( isset( $addition_is_unavailable ) ) {
				update_post_meta( $post_id, 'addition_is_unavailable', $addition_is_unavailable );
			}
			if ( isset( $addition_unavailable_from ) ) {
				update_post_meta( $post_id, 'addition_unavailable_from', $addition_unavailable_from );
			}
			if ( isset( $addition_unavailable_to ) ) {
				update_post_meta( $post_id, 'addition_unavailable_to', $addition_unavailable_to );
			}

			$addition_base = array_map( 'sanitize_text_field', wp_unslash( $_POST['addition_base'] ) );
			if ( ! empty( $addition_base ) ) {
				foreach ( $addition_base as $key => $base_id ) {
					$base_data   = get_post( $base_id );
					$base_slug[] = $base_data->post_name;
				}
				update_post_meta( $post_id, 'extra_selected_base_slugs', $base_slug );
			}

			$seat_on_extra = array_map( 'sanitize_text_field', wp_unslash( $_POST['seat_on_extra'] ) );
			if ( ! empty( $seat_on_extra ) ) {
				$seat_slug = array();
				foreach ( $seat_on_extra as $key => $seat_id ) {
					$seat_data   = get_post( $seat_id );
					$seat_slug[] = $seat_data->post_name;
				}
				update_post_meta( $post_id, 'extra_selected_seats_slugs', $seat_slug );
			}
			if ( ! empty( $extra_seats_value ) ) {
				update_post_meta( $post_id, 'extra_selected_base', $addition_base );
			}
			if ( ! empty( $checked_seats ) ) {
				update_post_meta( $post_id, 'extra_selected_seats', $seat_on_extra );
			}
         // phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * @param $defaults
		 *
		 * @return mixed
		 */
		public function bkx_addition_columns_head( $defaults ) {
			$defaults['display_shortcode_all'] = 'Shortcode [bookingx extra-id="all"]';

			return $defaults;
		}

		/**
		 * @param $column_name
		 * @param $post_ID
		 */
		public function bkx_addition_columns_content( $column_name, $post_ID ) {
			if ( $column_name == 'display_shortcode_all' ) {
				echo '[bookingx extra-id="' . esc_attr( $post_ID ) . '" description="yes" image="yes" extra-info="no"]';
			}
		}
	}
}

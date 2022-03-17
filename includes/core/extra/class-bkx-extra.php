<?php
defined( 'ABSPATH' ) || exit;

/**
 * Description of Class Bkx_Seat
 * Get All seat information
 *
 * @author Divyang Parekh
 */
class BkxExtra {



	/**
	 * @var null
	 */
	public $id;

	/**
	 * @var array|null|WP_Post
	 */
	public $post;

	/**
	 * @var mixed|void
	 */
	public $alias;

	/**
	 * @var string
	 */
	public $bkx_post_type;

	/**
	 * @var array
	 */
	public $meta_data = array();

	/**
	 * @var type
	 */
	public $get_price;
	/**
	 * @var type
	 */
	public $sale_price_details;

	/**
	 * @var type
	 */
	public $get_extra_time;

	/**
	 * @var mixed|void
	 */
	public $get_extra_overlap;

	/**
	 * @var mixed|void
	 */
	public $get_service_by_extra;

	/**
	 * @var mixed|void
	 */
	public $get_addition_unavailable;

	/**
	 * @var
	 */
	public $booking_url; // Booking url with arguments

	/**
	 * @var mixed|void
	 */
	public $booking_page_url; // Just Booking Page Url

	public $load_global = array();


	/**
	 * BkxExtra constructor.
	 *
	 * @param null $bkx_post
	 * @param null $post_id
	 */
	public function __construct( $bkx_post = null, $post_id = null ) {
		$this->bkx_post_type = 'bkx_addition';

		if ( isset( $post_id ) && $post_id != '' ) {
			$post_id  = $post_id;
			$bkx_post = get_post( $post_id );
		}

		if ( ! empty( $bkx_post ) ) {
			$post_id = $bkx_post->ID;
		}
		// Load Global Variables
		$bkx_booking_form_shortcode = new BkxBookingFormShortCode();
		$this->load_global          = $bkx_booking_form_shortcode->load_global_variables();

		if ( is_multisite() ) {
			$current_blog_id = get_current_blog_id();
			$alias_addition  = get_blog_option( $current_blog_id, 'bkx_alias_addition' );
		} else {
			$alias_addition = bkx_crud_option_multisite( 'bkx_alias_addition' );
		}

		$this->alias = $alias_addition;
		$this->post  = $bkx_post;
		if ( isset( $post_id ) && $post_id != '' ) {
			$this->meta_data = get_post_custom( $post_id );
		}
		$this->get_price                = $this->get_price();
		$this->sale_price_details       = $this->sale_price_details();
		$this->get_extra_time           = $this->get_extra_time();
		$this->get_extra_overlap        = $this->get_extra_overlap();
		$this->get_service_by_extra     = $this->get_service_by_extra();
		$this->get_addition_unavailable = $this->get_addition_unavailable();
		$this->booking_page_url         = $this->get_booking_page_url();
		$this->id                       = $post_id;

	}

	/**
	 * @return mixed|void
	 */
	public function get_title( $plain = false ) {
		if ( empty( $this->post ) ) {
			return '';
		}
		$extra_price = 0;
		$extra_price += $this->get_price();
		$extra_time  = $this->get_time();
		$extra_title = "{$this->post->post_title} - {$this->load_global->currency_name}{$this->load_global->currency_sym}{$extra_price} - {$extra_time['formatted']}";
		if ( $plain == true ) {
			$extra_title = $this->post->post_title;
		}
		return apply_filters( 'bkx_addition_title', $extra_title, $this );
	}

	/**
	 *
	 * @return type
	 */
	public function get_description() {
		$post_content = $this->post->post_content;
		return apply_filters( 'bkx_addition_description', $post_content, $this );
	}

	/**
	 *
	 * @return type
	 */
	public function get_excerpts() {
		$post_excerpt = $this->post->post_excerpt;
		return apply_filters( 'bkx_addition_price', $post_excerpt, $this );
	}

	/**
	 *
	 * @return type
	 */
	public function get_thumb() {
		if ( has_post_thumbnail( $this->post ) ) {
			$image = get_the_post_thumbnail(
				$this->post->ID,
				apply_filters( 'bkx_single_post_large_thumbnail_size', 'bkx_single' ),
				array(
					'title' => $this->get_title(),
					'class' => 'img-thumbnail',
					'alt'   => $this->get_title(),
				)
			);
		} else {
			$image = '<img src="' . BKX_PLUGIN_PUBLIC_URL . '/images/placeholder.png" class="img-thumbnail wp-post-image" alt="' . $this->get_title() . '" title="' . $this->get_title() . '">';
		}

		return apply_filters(
			'bookingx_single_post_image_html',
			sprintf(
				'<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="%s">%s</a>',
				esc_url( get_permalink() ),
				esc_attr( $this->get_title() ),
				'',
				$image
			),
			$this->post->ID
		);
	}

	/**
	 *
	 * @return type
	 */
	public function get_thumb_url() {
		$get_thumb_url = the_post_thumbnail_url( $this->post );
		return apply_filters( 'bkx_addition_thumb_url', $get_thumb_url, $this );
	}

	/**
	 *
	 * @return type
	 */
	public function get_price() {
		$meta_data                       = $this->meta_data;
		$addition_price                  = isset( $meta_data['addition_price'] ) ? esc_html( $meta_data['addition_price'][0] ) : 0;
		$extra_sale_price                = isset( $meta_data['extra_sale_price'] ) ? esc_html( $meta_data['extra_sale_price'][0] ) : 0;
		$price_array['addition_price']   = number_format( (float) $addition_price, 2, '.', '' );
		$price_array['extra_sale_price'] = number_format( (float) $extra_sale_price, 2, '.', '' );
		$price_array['meta_data']        = $meta_data;
		if ( isset( $extra_sale_price ) && $extra_sale_price > 0 ) {
			$addition_price = $extra_sale_price;
		}
		return apply_filters( 'bkx_addition_price', $addition_price, $price_array );

	}

	/**
	 * @return mixed|void
	 */
	public function calculate_discount() {

		if ( $this->is_on_sale() == false ) {
			return;
		}
		$meta_data        = $this->meta_data;
		$addition_price   = isset( $meta_data['addition_price'] ) ? esc_html( $meta_data['addition_price'][0] ) : 0;
		$extra_sale_price = isset( $meta_data['extra_sale_price'] ) ? esc_html( $meta_data['extra_sale_price'][0] ) : 0;
		$discount         = array();
		if ( isset( $addition_price ) && isset( $extra_sale_price ) && $extra_sale_price > 0 ) {
			$discount['amount'] = ( $addition_price - $extra_sale_price );
			if ( isset( $discount['amount'] ) && $discount['amount'] > 0 ) {
				$discount['percentage'] = ( $discount['amount'] * 100 / $addition_price );
			}
		}
		return apply_filters( 'addition_discount', $discount, $meta_data );
	}

	/**
	 * @return array|void
	 */
	public function sale_price_details() {

		if ( $this->is_on_sale() == false ) {
			return;
		}
		$meta_data                       = $this->meta_data;
		$addition_price                  = isset( $meta_data['addition_price'] ) ? esc_html( $meta_data['addition_price'][0] ) : 0;
		$extra_sale_price                = isset( $meta_data['extra_sale_price'] ) ? esc_html( $meta_data['extra_sale_price'][0] ) : 0;
		$calculate_discount              = $this->calculate_discount();
		$price_array['addition_price']   = number_format( (float) $addition_price, 2, '.', '' );
		$price_array['extra_sale_price'] = number_format( (float) $extra_sale_price, 2, '.', '' );
		$price_array['discount']         = $calculate_discount['amount'];
		$price_array['percentage']       = round( $calculate_discount['percentage'] );
		$price_array['currency']         = $this->load_global->currency_sym;

		return $price_array;
	}

	public function is_on_sale() {
		$meta_data        = $this->meta_data;
		$extra_sale_price = isset( $meta_data['extra_sale_price'] ) ? esc_html( $meta_data['extra_sale_price'][0] ) : 0;
		$is_sale          = false;
		if ( isset( $extra_sale_price ) && $extra_sale_price > 0 ) {
			$is_sale = true;
		}
		return $is_sale;
	}

	public function get_time() {

		if ( empty( $this->post ) ) {
			return '';
		}

		$extra_id          = $this->post->ID;
		$extra_time_option = get_post_meta( $extra_id, 'addition_time_option', true );
		$extra_day         = get_post_meta( $extra_id, 'addition_days', true );
		$extra_months      = get_post_meta( $extra_id, 'addition_months', true );

		$extra_time = 0;
		if ( $extra_time_option == 'H' ) {
			$extra_hours   = get_post_meta( $extra_id, 'addition_hours', true );
			$extra_minutes = get_post_meta( $extra_id, 'addition_minutes', true );

			$extra_hours   = isset( $extra_time_option ) && $extra_time_option == 'H' && ( $extra_hours > 0 && $extra_hours <= 23 ) ? $extra_hours : 0;
			$extra_minutes = isset( $extra_minutes ) && $extra_minutes > 0 ? $extra_minutes : 0;

			$total_extra_duration = ( $extra_hours * 60 ) + $extra_minutes;
			$extra_time           = $total_extra_duration;

			$extra['formatted'] = bkx_total_time_of_services_formatted( $total_extra_duration );
			$extra['type']      = $extra_time_option;

		} elseif ( $extra_time_option == 'D' ) {
			$extra['formatted'] = sprintf( __( '%1$s Days', 'bookingx' ), $extra_day );
			$extra_time         = $extra_day * 24 * 60;
			$extra['type']      = $extra_time_option;

		} elseif ( $extra_time_option == 'M' ) {
			$extra['formatted'] = sprintf( __( '%1$s Months', 'bookingx' ), $extra_months );
			$extra_time         = $extra_months * 24 * 30;
			$extra['type']      = $extra_time_option;
		}

		$extra['in_sec'] = $extra_time * 60;
		return apply_filters( 'bkx_addition_time', $extra, $this );
	}


	/**
	 * @param  $ids
	 * @return int|type
	 */
	public function get_total_price_by_ids( $ids ) {
		$extra_price = 0;
		if ( is_array( $ids ) ) {
			$extra_ids = $ids;
		} else {
			$extra_ids = array_filter( array_unique( explode( ',', $ids ) ) );
		}
		if ( ! empty( $extra_ids ) ) {
			foreach ( $extra_ids as $key => $value ) {
				$extra_post   = get_post( $value );
				$ExtraObj     = new BkxExtra( $extra_post );
				$extra_price += $ExtraObj->get_price();
			}
		}
		return $extra_price;
	}

	/**
	 * @param  $ids
	 * @return array
	 */
	public function get_extra_by_ids( $ids, $type = 'obj' ) {
		if ( is_array( $ids ) ) {
			$extra_ids = $ids;
		} else {
			$extra_ids = array_filter( array_unique( explode( ',', $ids ) ) );
		}
		if ( ! empty( $extra_ids ) ) {
			foreach ( $extra_ids as $key => $value ) {
				$Extra = new BkxExtra( '', $value );
				if ( $type == 'obj' ) {
					$ExtraObj[] = $Extra;
				} else {
					$ExtraObj[] = $Extra->get_title();
				}
			}
		}
		return $ExtraObj;
	}

	/**
	 *
	 * @return type
	 */
	public function get_extra_time() {
		$meta_data            = $this->meta_data;
		$addition_time_option = isset( $meta_data['addition_time_option'] ) ? esc_attr( $meta_data['addition_time_option'][0] ) : '';
		$addition_months      = isset( $meta_data['addition_months'] ) ? esc_attr( $meta_data['addition_months'][0] ) : '';
		$addition_days        = isset( $meta_data['addition_days'] ) ? esc_attr( $meta_data['addition_days'][0] ) : '';
		$addition_hours       = isset( $meta_data['addition_hours'] ) ? intval( esc_attr( $meta_data['addition_hours'][0] ) ) : '';
		$addition_minutes     = isset( $meta_data['addition_minutes'] ) ? intval( esc_attr( $meta_data['addition_minutes'][0] ) ) : '';
		$time_data            = array();
		if ( $addition_time_option == 'H' ) :
			$time_data[ $addition_time_option ]['hours'] = $addition_hours;
			if ( $addition_minutes != '' ) :
				$time_data[ $addition_time_option ]['minutes'] = $addition_minutes;
			endif;
		endif;
		if ( $addition_time_option == 'D' ) :
			$time_data[ $addition_time_option ]['days'] = $addition_days;
		endif;
		return apply_filters( 'bkx_addition_extra_time', $time_data, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_extra_overlap() {
		$meta_data        = $this->meta_data;
		$addition_overlap = isset( $meta_data['addition_overlap'] ) ? esc_attr( $meta_data['addition_overlap'][0] ) : '';
		$overlap_array    = array();
		if ( $addition_overlap == 'N' ) :
			$overlap_array[ $addition_overlap ] = 'Own Time Bracket';
		endif;
		if ( $addition_overlap == 'Y' ) :
			$overlap_array[ $addition_overlap ] = 'Overlap';
		endif;
		return apply_filters( 'bkx_addition_extra_overlap', $overlap_array, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_service_by_extra() {
		$meta_data        = $this->meta_data;
		$service_by_extra = '';
		if ( ! empty( $meta_data ) && isset( $meta_data['extra_selected_base'][0] ) ) {
			$service_by_extra = maybe_unserialize( $meta_data['extra_selected_base'][0] );
		}
		return apply_filters( 'bkx_addition_service_by_extra', $service_by_extra, $this );
	}

	/**
	 * @param  $data
	 * @return array|void
	 */
	public function get_extra_by_seat( $data ) {
		// Process Flow : seat_id ---> Base Data ---> base_id ----> Extra data
		// Seat id to Base data and Base data to Extra
		if ( empty( $data ) ) {
			return;
		}
		$ExtraData = array();
		foreach ( $data as $key => $base ) {
			$base_id     = $base->id;
			$args        = array(
				'post_type'   => $this->bkx_post_type,
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key'     => 'extra_selected_base',
						'value'   => $base_id,
						'compare' => 'LIKE',
					),
				),
			);
			$extraresult = new WP_Query( $args );
			if ( $extraresult->have_posts() ) :
				while ( $extraresult->have_posts() ) :
					$extraresult->the_post();
					$extra_array[] = get_the_ID();
					$extra_unique  = array_unique( $extra_array );
				endwhile;
				wp_reset_query();
				wp_reset_postdata();
			endif;
		}
		if ( ! empty( $extra_unique ) ) {
			foreach ( $extra_unique as $key => $extra_id ) {
				$bkx_extra_obj = new BkxExtra( '', $extra_id );
				$ExtraData[]   = $bkx_extra_obj;
			}
		}
		return $ExtraData;
	}

	public function get_extra_options_for_booking_form( $base_id = null ) {
		if ( ! isset( $base_id ) && $base_id == '' ) {
			return;
		}
		$extra_html = '';
		if ( is_multisite() ) :
			$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
			switch_to_blog( $blog_id );
		endif;
		$extra_lists = $this->get_extra_by_base( $base_id );

		if ( ! empty( $extra_lists ) ) {
			foreach ( $extra_lists as $extra_data ) {
				$extra_html .= '<div class="custom-control custom-checkbox">';
				$extra       = $extra_data->post;
				$extra_name  = $extra->post_title;
				$extra_id    = $extra->ID;
				$BkxExtra    = new BkxExtra( '', $extra_id );
				$extra_time  = $BkxExtra->get_time();
				$total_time  = $extra_time['in_sec'] / 60;

				$extra_price       = $BkxExtra->get_price();
				$extra_time_option = get_post_meta( $extra_id, 'addition_time_option', true );
				$extra_day         = get_post_meta( $extra_id, 'addition_days', true );
				$extra_hours       = get_post_meta( $extra_id, 'addition_hours', true );
				$extra_minutes     = get_post_meta( $extra_id, 'addition_minutes', true );
				if ( isset( $extra_time_option ) && $extra_time_option == 'H' ) {
					$total_time_formatted = bkx_convert_for_hours( $total_time * 60 );
				}
				if ( isset( $extra_time_option ) && $extra_time_option == 'D' ) {
					$total_time_formatted = bkx_convert_seconds( $total_time * 60 );
				}
				// translators: Extra ID Label.
				$extra_html .= sprintf( __( '<input type="checkbox" class="custom-control-input" id="bkx-extra-service-%1$d" value="%2$d" name="bkx-extra-service">', 'bookingx' ), $extra_id, $extra_id );
				// translators: Extra Information Label.
				$extra_html .= sprintf( __( '<label class="custom-control-label" for="bkx-extra-service-%1$d"> %2$s - %3$s%4$s%5$d - %6$s </label>', 'bookingx' ), esc_html( $extra_id ), esc_html( $extra_name ), $this->load_global->currency_name, $this->load_global->currency_sym, esc_html( $extra_price ), $total_time_formatted );
				$extra_html .= sprintf( '</div>' );
			}
		}
		if ( is_multisite() ) :
			restore_current_blog();
		endif;

		return $extra_html;

	}


	/**
	 * @param  $base_id
	 * @return array|void
	 */
	public function get_extra_by_base( $base_id ) {
		if ( empty( $base_id ) ) {
			return;
		}

		$ExtraData   = array();
		$args        = array(
			'post_type'   => $this->bkx_post_type,
			'post_status' => 'publish',
			'meta_query'  => array(
				array(
					'key'     => 'extra_selected_base',
					'value'   => $base_id,
					'compare' => 'LIKE',
				),
			),
		);
		$extraresult = new WP_Query( $args );
		if ( $extraresult->have_posts() ) :
			while ( $extraresult->have_posts() ) :
				$extraresult->the_post();
				$extra_array[] = get_the_ID();
				$extra_unique  = array_unique( $extra_array );
			endwhile;
			wp_reset_postdata();
		endif;
		if ( ! empty( $extra_unique ) ) {
			foreach ( $extra_unique as $key => $extra_id ) {
				$bkx_extra_obj = new BkxExtra( '', $extra_id );
				$ExtraData[]   = $bkx_extra_obj;
			}
		}
		return $ExtraData;
	}

	/**
	 * @return mixed|void
	 */
	public function get_addition_unavailable() {
		$meta_data = $this->meta_data;

		$addition_unavailable      = array();
		$addition_is_unavailable   = isset( $meta_data['addition_is_unavailable'] ) ? esc_attr( $meta_data['addition_is_unavailable'][0] ) : '';
		$addition_unavailable_from = isset( $meta_data['addition_unavailable_from'] ) ? esc_attr( $meta_data['addition_unavailable_from'][0] ) : '';
		$addition_unavailable_till = isset( $meta_data['addition_unavailable_to'] ) ? esc_attr( $meta_data['addition_unavailable_to'][0] ) : '';

		if ( $addition_is_unavailable == 'Y' ) :
			$addition_unavailable['from'] = $addition_unavailable_from;
			$addition_unavailable['till'] = $addition_unavailable_till;
		endif;

		return apply_filters( 'bkx_addition_unavailable', $addition_unavailable, $this );
	}

	/**
	 * @return mixed|void
	 */
	public function get_booking_page_url() {
		if ( is_multisite() ) {
			$current_blog_id      = get_current_blog_id();
			$bkx_set_booking_page = get_blog_option( $current_blog_id, 'bkx_set_booking_page' );
		} else {
			$bkx_set_booking_page = bkx_crud_option_multisite( 'bkx_set_booking_page' );
		}
		return apply_filters( 'bkx_booking_page_url', esc_url( user_trailingslashit( get_permalink( $bkx_set_booking_page ) ) ), $this );
	}
}

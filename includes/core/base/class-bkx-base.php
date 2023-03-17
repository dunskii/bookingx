<?php //phpcs:ignore
/**
 * BookingX Core Base Class Load.
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * BookingX Core Base Class Load.
 */
class BkxBase {


	/**
	 * $alias.
	 *
	 * @var mixed|void Alias Name.
	 */
	public $alias;
	/**
	 * $post_title
	 *
	 * @var string
	 */
	public $post_title;

	/**
	 * $meta_data Array
	 *
	 * @var array
	 */
	public $meta_data = array();

	/**
	 * $bkx_post_type
	 *
	 * @var string
	 */
	public $bkx_post_type = 'bkx_base';

	/**
	 * $post WP_Post
	 *
	 * @var array|null|type|WP_Post
	 */
	public $post;

	/**
	 * $get_price
	 *
	 * @var float
	 */
	public $get_price;

	/**
	 * $get_service_time
	 *
	 * @var string
	 */
	public $get_service_time;

	/**
	 * $get_seat_by_base
	 *
	 * @var string
	 */
	public $get_seat_by_base;

	/**
	 * $service_extended
	 *
	 * @var string
	 */
	public $service_extended;

	/**
	 * $get_service_fixed_mobile
	 *
	 * @var string
	 */
	public $get_service_fixed_mobile;

	/**
	 * $get_service_unavailable
	 *
	 * @var mixed|void
	 */
	public $get_service_unavailable;

	/**
	 * Sale_price_details
	 *
	 * @var
	 */
	public $sale_price_details;

	/**
	 * $booking_url Booking url with arguments
	 *
	 * @var mixed|void
	 */
	public $booking_url;

	/**
	 * Just Booking Page Url
	 *
	 * @var mixed|void
	 */
	public $booking_page_url;

	/**
	 * $url_arg_name URL Args Name
	 *
	 * @var string
	 */
	public $url_arg_name;

	/**
	 * Base ID
	 *
	 * @var null
	 */
	public $id;

	/**
	 * $load_global Data
	 *
	 * @var array|object
	 */
	public $load_global = array();

	/**
	 * BkxBase constructor.
	 *
	 * @param null $bkx_post Base Object.
	 * @param null $post_id  Base Post ID.
	 *
	 * @throws Exception Throw an Error.
	 */
	public function __construct( $bkx_post = null, $post_id = null ) {
		if ( is_multisite() ) :
			$blog_id = get_current_blog_id();
			switch_to_blog( $blog_id );
		endif;

		// Load Global Variables.
		$bkx_booking_form_short_code = new BkxBookingFormShortCode();
		$this->load_global           = $bkx_booking_form_short_code->load_global_variables();

		if ( ! empty( $bkx_post->post_type ) ) {
			$this->bkx_post_type = $bkx_post->post_type;
		}

		if ( ! empty( $bkx_post ) ) {
			$this->id = $bkx_post->ID;
			$post_id  = $bkx_post->ID;
		}
		if ( isset( $post_id ) && '' !== $post_id ) {
			$this->id = $post_id;
			$bkx_post = get_post( $post_id );
		}
		$alias_base                    = bkx_crud_option_multisite( 'bkx_alias_base', 'default' );
		$this->alias                   = $alias_base;
		$this->post                    = $bkx_post;
		$this->meta_data               = get_post_custom( $post_id );
		$this->get_price               = $this->get_price();
		$this->sale_price_details      = $this->sale_price_details();
		$this->get_service_time        = $this->get_service_time();
		$this->get_seat_by_base        = $this->get_seat_by_base();
		$this->service_extended        = $this->service_extended();
		$this->get_service_unavailable = $this->get_service_unavailable();
		$this->booking_url             = $this->get_booking_url();
		$this->booking_page_url        = $this->get_booking_page_url();
		$this->post_title              = $this->get_title();
		$this->id                      = $post_id;

		if ( is_multisite() ) :
			restore_current_blog();
		endif;
	}

	/**
	 * Get_meta_details_html
	 *
	 * @param  integer $base_id Base ID.
	 * @return string
	 */
	public function get_meta_details_html( $base_id ) {
		$booking_url    = $this->booking_page_url;
		$bkx_alias_base = bkx_crud_option_multisite( 'bkx_alias_base' );
		$duration       = $this->get_service_time( $base_id );
		$duration_text  = service_hours_formatted( $duration );
		$extended       = $this->service_extended();
		$extended_label = isset( $extended ) && 'Y' === $extended ? 'Yes' : 'No';
		$html           = '';
		$html          .= '<div class="row">
                    <div class="col-md-8">' . sprintf( '<h3>%s</h3>', __( 'Additional Information', 'bookingx' ) ) . "</div>
                    <div class=\"col-md-4\">
                        <form method=\"post\" enctype='multipart/form-data' action=\"" . esc_url( $booking_url ) . '">
                            <input type="hidden" name="type" value="bkx_seat" />
                            <input type="hidden" name="id" value="' . esc_attr( $base_id ) . '" /><button type="submit" class="btn btn-primary">' . esc_html( 'Book now' ) . '</button>
                        </form>
                    </div>
                <div class="col-md-12"><hr class="mt-1"/></div>
                </div>';
		$html          .= '<div class="row">
                    <div class="col-md-12">
                            <ul class="additional-info">';
     $html          .= sprintf( '<li><div class="durations"><label>%s :</label><span> %s</span></li>', 'Duration ', esc_html( $duration_text ) ); //phpcs:ignore
     $html          .= sprintf( __( '<li><div class="extended"><label>%2$s Time Extended :</label><span> %1$s</span></li>', 'bookingx' ), esc_html( $extended_label ), esc_html( $bkx_alias_base ) ); //phpcs:ignore
		$html          .= '</ul>
                    </div>
                </div>';
		return $html;
	}

	/**
	 * Get_base_options_for_booking_form
	 *
	 * @param  null $seat_id Seat ID.
	 * @return string|void
	 */
	public function get_base_options_for_booking_form( $seat_id = null ): string {
		if ( ! isset( $seat_id ) && '' === $seat_id ) {
			return '';
		}

		if ( is_multisite() ) :
			$blog_id = get_current_blog_id();
			switch_to_blog( $blog_id );
		endif;
		$base_lists = $this->get_base_by_seat( $seat_id );

		if ( ! empty( $base_lists ) ) {
         $base_html = sprintf( __( '<option> Select %s </option>', 'bookingx' ), esc_html( $this->load_global->base ) ); //phpcs:ignore
			foreach ( $base_lists as $base_data ) {
				$base      = $base_data->post;
				$base_name = $base_data->post_title;
				$base_id   = $base->ID;
             $base_html .= sprintf( __( '<option value="%1$d"> %2$s </option>', 'bookingx' ), esc_html( $base_id ), esc_html( $base_name ) ); //phpcs:ignore
			}
		} else {
			$base_html = esc_html( 'NORF' );
		}
		if ( is_multisite() ) :
			restore_current_blog();
		endif;

		return $base_html;

	}

	/**
	 * Get the title of the post.
	 *
	 * @return mixed|void
	 * @throws Exception Throwable.
	 */
	public function get_title( $plain = false ) {
		$post_id = $this->id;
		if ( empty( $post_id ) || $post_id == 'NaN' ) {
			return;
		}

		$base_price = 0;
		$bkx_post   = get_post( $post_id );
        $base_price += $this->get_price();
		$base_time  = $this->get_time( $post_id );
		$formatted  = '';
		if ( isset( $base_time['formatted'] ) ) {
			$formatted = $base_time['formatted'];
		}

		$base_title = "{$bkx_post->post_title} - {$this->load_global->currency_sym}{$base_price} - {$formatted}";
		if ( $plain == true ) {
			$base_title = $bkx_post->post_title;
		}
		return apply_filters( 'bkx_addition_title', $base_title );
	}

	/**
	 * @param  null $post_id
	 * @return mixed|void
	 * @throws Exception
	 */
	public function get_time( $post_id = null ) {
		$post_id = isset( $post_id ) ? $post_id : $this->id;

		if ( empty( $post_id ) ) {
			return;
		}

		$base_time_option = get_post_meta( $post_id, 'base_time_option', true );
		$base_time        = 0;
		if ( 'H' === $base_time_option ) {
			$base_hours   = get_post_meta( $post_id, 'base_hours', true );
			$base_minutes = get_post_meta( $post_id, 'base_minutes', true );
			$base_hours   = ( $base_hours > 0 && $base_hours <= 23 ) ? $base_hours : 0;
			$base_minutes = isset( $base_minutes ) && $base_minutes > 0 ? $base_minutes : 0;

			$total_base_duration = ( $base_hours * 60 ) + $base_minutes;
			$base_time           = $total_base_duration;
			$base['formatted']   = bkx_total_time_of_services_formatted( $total_base_duration );
			$base['type']        = $base_time_option;
		}

		if ( 'D' === $base_time_option ) {
			$base_day = get_post_meta( $post_id, 'base_day', true );
			if ( isset( $base_day ) && $base_day > 0 && $base_day < 2 ) {
				// translators: Day Label.
				$base['formatted'] = sprintf( __( '%1$s Day', 'bookingx' ), $base_day );
			}
			if ( isset( $base_day ) && $base_day > 0 && $base_day > 1 ) {
				// translators: Day Label.
				$base['formatted'] = sprintf( __( '%1$s Days', 'bookingx' ), $base_day );
			}
			$base_time    = $base_day * 24 * 60;
			$base['type'] = $base_time_option;
		}

		if ( 'M' === $base_time_option ) {
			$base_months = get_post_meta( $post_id, 'months', true );
			// translators: Months Label.
			$base['formatted'] = sprintf( __( '%1$s Months', 'bookingx' ), $base_months );
			$base_time         = $base_months * 24 * 30;
			$base['type']      = $base_time_option;
		}
		$base['in_sec'] = $base_time * 60;
		return apply_filters( 'bkx_addition_time', $base );
	}

	/**
	 * Get_description()
	 *
	 * @return mixed|void
	 */
	public function get_description() {
		$post_content = apply_filters( 'the_content', get_the_content( '', false, $this->post ) );
		return apply_filters( 'bkx_base_description', $post_content );
	}

	/**
	 * Get_excerpts()
	 *
	 * @return mixed|void
	 */
	public function get_excerpts() {
		$post_excerpt = $this->post->post_excerpt;
		return apply_filters( 'bkx_base_post_excerpt', $post_excerpt );
	}

	/**
	 * Get_thumb()
	 *
	 * @return mixed|void
	 * @throws Exception
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
			$image = '<img src="' . esc_url( BKX_PLUGIN_PUBLIC_URL ) . '/images/placeholder.png" class="img-thumbnail wp-post-image" alt="' . esc_attr( $this->get_title() ) . '" title="' . esc_attr( $this->get_title() ) . '">';
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
	 * Get_price()
	 *
	 */
	public function get_price() {
		$meta_data                      = $this->meta_data;

		$base_price                     = isset( $meta_data['base_price'] ) ? esc_html( $meta_data['base_price'][0] ) : 0;
		$base_sale_price                = isset( $meta_data['base_sale_price'] ) ? esc_html( $meta_data['base_sale_price'][0] ) : 0;
		$price_array['base_price']      = bkx_clean_price_format($base_price );
		$price_array['base_sale_price'] = bkx_clean_price_format($base_sale_price );
		$price_array['meta_data']       = $meta_data;
		return apply_filters( 'bkx_base_price', $base_price, $price_array );
	}

	public function is_on_sale() {
		$meta_data       = $this->meta_data;
		$base_sale_price = isset( $meta_data['base_sale_price'] ) ? esc_html( $meta_data['base_sale_price'][0] ) : 0;
		$is_sale         = false;
		if ( isset( $base_sale_price ) && $base_sale_price > 0 ) {
			$is_sale = true;
		}
		return $is_sale;
	}

	/**
	 * @return array|void
	 */
	public function sale_price_details() {

		if ( $this->is_on_sale() == false ) {
			return;
		}
		$meta_data                      = $this->meta_data;
		$base_price                     = isset( $meta_data['base_price'] ) ? esc_html( $meta_data['base_price'][0] ) : 0;
		$base_sale_price                = isset( $meta_data['base_sale_price'] ) ? esc_html( $meta_data['base_sale_price'][0] ) : 0;
		$calculate_discount             = $this->calculate_discount();
		$price_array['base_price']      = number_format( (float) $base_price, 2, '.', '' );
		$price_array['base_sale_price'] = number_format( (float) $base_sale_price, 2, '.', '' );
		$price_array['discount']        = $calculate_discount['amount'];
		$price_array['percentage']      = round( $calculate_discount['percentage'] );
		$price_array['currency']        = $this->load_global->currency_sym;

		return $price_array;
	}

	/**
	 * @return mixed|void
	 */
	public function calculate_discount() {

		if ( $this->is_on_sale() == false ) {
			return;
		}
		$meta_data       = $this->meta_data;
		$base_price      = isset( $meta_data['base_price'] ) ? esc_html( $meta_data['base_price'][0] ) : 0;
		$base_sale_price = isset( $meta_data['base_sale_price'] ) ? esc_html( $meta_data['base_sale_price'][0] ) : 0;
		$discount        = array();
		if ( isset( $base_price ) && isset( $base_sale_price ) && $base_sale_price > 0 ) {
			$discount['amount'] = ( $base_price - $base_sale_price );
			if ( isset( $discount['amount'] ) && $discount['amount'] > 0 ) {
				$discount['percentage'] = ( $discount['amount'] * 100 / $base_price );
			}
		}
		return apply_filters( 'base_discount', $discount, $meta_data );
	}


	/**
	 * GetMetData()
	 *
	 * @param  string $base_id Base ID.
	 * @return mixed
	 */
	public function bkx_get_meta_data( $base_id ) {
		if ( empty( $base_id ) ) {
			return '';
		}
		return get_post_meta( $base_id );
	}

	/**
	 * Get_service_time()
	 *
	 * @param  string $base_id Base Id.
	 * @return array
	 */
	public function get_service_time( $base_id = null ): array {
		if ( empty( $base_id ) ) {
			$base_id = $this->id;
		}
		$meta_data = $this->bkx_get_meta_data( $base_id );

		$base_time_option = isset( $meta_data['base_time_option'] ) ? esc_html( $meta_data['base_time_option'][0] ) : '';
		$base_day         = isset( $meta_data['base_day'] ) ? esc_html( $meta_data['base_day'][0] ) : '';
		$base_hours       = isset( $meta_data['base_hours'] ) ? intval( esc_html( $meta_data['base_hours'][0] ) ) : '';
		$base_minutes     = isset( $meta_data['base_minutes'] ) ? intval( esc_html( $meta_data['base_minutes'][0] ) ) : '';
		$time_data        = array();

		if ( 'H' === $base_time_option ) :
			$time_data[ $base_time_option ]['hours'] = $base_hours;
			if ( '' !== $base_minutes ) :
				$time_data[ $base_time_option ]['minutes'] = $base_minutes;
			endif;
		endif;
		if ( 'D' === $base_time_option ) :
			$time_data[ $base_time_option ]['days'] = $base_day;
		endif;

		return apply_filters( 'bkx_base_service_time', $time_data );
	}

	/**
	 * Get_sevice_time_data()
	 *
	 * @return mixed|void
	 */
	public function get_sevice_time_data() {
		$service_time         = $this->get_service_time();
		$get_sevice_time_data = '';
		if ( isset( $service_time ) && ! empty( $service_time['H'] ) ) {
			$hours   = $service_time['H']['hours'];
			$minutes = $service_time['H']['minutes'];
			if ( isset( $hours ) && '' !== $hours ) {
				if ( $hours > 1 ) {
					$hs = 's';
				}
				// translators:  Hour Label.
				$hour_data = sprintf( __( '%1$s hour %2$s', 'Bookingx' ), $hours, $hs );
			}
			if ( isset( $minutes ) && '' !== $minutes && $minutes > 0 ) {
				if ( $minutes > 1 ) {
					$ms = 's';
				}
				// translators: Minute Label.
				$minute_data = sprintf( __( '%1$s minute %2$s', 'Bookingx' ), $minutes, $ms );
			}
			$get_sevice_time_data = $hour_data . ' ' . $minute_data;
		}
		return apply_filters( 'bkx_base_sevice_time_data', $get_sevice_time_data, $this );
	}

	/**
	 * Service_extended()
	 *
	 * @return type
	 */
	public function service_extended() {
		$meta_data        = $this->meta_data;
		$base_is_extended = isset( $meta_data['base_is_extended'] ) ? esc_attr( $meta_data['base_is_extended'][0] ) : '';
		return apply_filters( 'bkx_base_service_extended', $base_is_extended, $this );
	}

	/**
	 * Get_seat_by_base()
	 *
	 * @return type
	 */
	public function get_seat_by_base() {
		$meta_data = $this->meta_data;
		if ( isset( $meta_data['base_selected_seats'][0] ) && '' !== $meta_data['base_selected_seats'][0] ) {
			$seat_by_base = maybe_unserialize( $meta_data['base_selected_seats'][0] );
			return apply_filters( 'bkx_base_seat_by_base', $seat_by_base, $this );
		}
	}

	/**
	 * Get_service_unavailable()
	 *
	 * @return mixed|void
	 */
	public function get_service_unavailable() {
		$meta_data             = $this->meta_data;
		$service_unavailable   = array();
		$base_is_unavailable   = isset( $meta_data['base_is_unavailable'] ) ? esc_html( $meta_data['base_is_unavailable'][0] ) : '';
		$base_unavailable_from = isset( $meta_data['base_unavailable_from'] ) ? esc_html( $meta_data['base_unavailable_from'][0] ) : '';
		$base_unavailable_till = isset( $meta_data['base_unavailable_till'] ) ? esc_html( $meta_data['base_unavailable_till'][0] ) : '';
		if ( 'Y' === $base_is_unavailable ) :
			$service_unavailable['from'] = $base_unavailable_from;
			$service_unavailable['till'] = $base_unavailable_till;
		endif;
		return apply_filters( 'bkx_base_service_unavailable', $service_unavailable, $this );
	}

	/**
	 * Get_base_by_seat
	 *
	 * @param string $seat_id Seat ID.
	 *
	 * @return array|void
	 * @throws Exception
	 */
	public function get_base_by_seat( $seat_id ): array {
		if ( ! isset( $seat_id ) && '' === $seat_id ) {
			return array();
		}

		if ( is_multisite() ) :
			$blog_id = get_current_blog_id();
			switch_to_blog( $blog_id );
		endif;

		$enable_any_seat = bkx_crud_option_multisite( 'enable_any_seat' );
		$enable_any_seat = isset( $enable_any_seat ) && $enable_any_seat > 0 ? $enable_any_seat : 0;
		$args            = array(
			'posts_per_page'   => -1,
			'post_type'        => 'bkx_base',
			'post_status'      => 'publish',
			'suppress_filters' => false,
		);
		if ( 0 === $enable_any_seat || ( 'any' !== $seat_id && $seat_id > 0 ) ) {
			$args['meta_query'] =
			array(
				'relation' => 'OR',
				array(
					'key'     => 'base_selected_seats',
					'value'   => maybe_serialize( $seat_id ),
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'base_selected_seats',
					'value'   => serialize( $seat_id ),
					'compare' => 'IN',
				),
			);
		}
		$base_result = new WP_Query( $args );

		$base_data = array();
		if ( $base_result->have_posts() ) :
			while ( $base_result->have_posts() ) :
				$base_result->the_post();
				$base_id      = get_the_ID();
				$bkx_base_obj = new BkxBase( '', $base_id );
				$base_data[]  = $bkx_base_obj;
			endwhile;
			wp_reset_query();
			wp_reset_postdata();
		endif;
		return $base_data;
	}

	/**
	 * Get_seat_obj_by_base()
	 *
	 * @param string $base_id Base ID.
	 *
	 * @return array
	 */
	public function get_seat_obj_by_base( $base_id ): array {
		if ( empty( $base_id ) ) {
			return array();
		}
		$seat_data           = array();
		$base_selected_seats = get_post_meta( $base_id, 'base_selected_seats', true );
		$base_seat_all       = get_post_meta( $base_id, 'base_seat_all', true );
		if ( isset( $base_seat_all ) && 'All' === $base_seat_all ) {
			$args       = array(
				'posts_per_page'   => -1,
				'post_type'        => 'bkx_seat',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			);
			$seats_post = get_posts( $args );
			if ( ! empty( $seats_post ) ) {
				foreach ( $seats_post as $seat_post ) {
					$bkx_seat_obj = new BkxSeat( '', $seat_post->ID );
					$seat_data[]  = $bkx_seat_obj;
				}
			}
		} else {
			if ( ! empty( $base_selected_seats ) ) {
				foreach ( $base_selected_seats as $key => $seat ) {
					$bkx_seat_obj = new BkxSeat( '', $seat );
					$seat_data[]  = $bkx_seat_obj;
				}
			}
		}

		return $seat_data;
	}

	/**
	 * Get_base_by_extra
	 *
	 * @param integer $extra_id Extra ID.
	 *
	 * @return array|void
	 * @throws Exception
	 */
	public function get_base_by_extra( int $extra_id ): array {
		if ( empty( $extra_id ) ) {
			return array();
		}
		$base_data           = array();
		$extra_selected_base = get_post_meta( $extra_id, 'extra_selected_base', true );
		if ( ! empty( $extra_selected_base ) ) {
			foreach ( $extra_selected_base as $key => $base_id ) {
				$bkx_base_obj = new BkxBase( '', $base_id );
				$base_data[]  = $bkx_base_obj;
			}
		}
		return $base_data;
	}

	/**
	 * Get_booking_url()
	 *
	 * @return mixed|void
	 */
	public function get_booking_url() {
		$bkx_set_booking_page = bkx_crud_option_multisite( 'bkx_set_booking_page' );
		$url_arg_name         = '';
		if ( bkx_crud_option_multisite( 'permalink_structure' ) ) {
			$booking_url = esc_url( user_trailingslashit( get_permalink( $bkx_set_booking_page ) . '' . $this->url_arg_name . '/' . get_the_ID() ) );
		} else {
			$arg         = array(
				'bookingx_type'    => $url_arg_name,
				'bookingx_type_id' => get_the_ID(),
			);
			$booking_url = esc_url( add_query_arg( $arg, get_permalink( $bkx_set_booking_page ) ) );
		}
		return apply_filters( 'bkx_booking_url', $booking_url, $this );
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

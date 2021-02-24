<?php
/**
 * BookingX Listing ShortCodes for Seat, Base and Extra Services
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class BKX_Listing_ShortCodes
 */
class BKX_Listing_ShortCodes {


	/**
	 * @var array
	 */
	private static $post_type = array();

	/**
	 *
	 */
	public static function init() {
		add_shortcode( 'bookingx', array( __CLASS__, 'bookingx_shortcode_callback' ) );
		self::$post_type = array( 'bkx_seat', 'bkx_base', 'bkx_addition' );
	}

	/**
	 * @param $atts
	 *
	 * @return false|string
	 */
	static function bookingx_shortcode_callback( $atts ) {
		ob_start();
		if ( ! empty( $atts ) ) {
			$post_type = self::find_post_type( $atts );
			$id        = 0;
			if ( isset( $atts['seat-id'] ) && $atts['seat-id'] != '' ) {
				$id = isset( $atts['seat-id'] ) && $atts['seat-id'] > 0 ? $atts['seat-id'] : 0;
			} elseif ( isset( $atts['base-id'] ) && $atts['base-id'] != '' ) {
				$id = isset( $atts['base-id'] ) && $atts['base-id'] > 0 ? $atts['base-id'] : 0;
			} elseif ( isset( $atts['extra-id'] ) && $atts['extra-id'] != '' ) {
				$id = isset( $atts['extra-id'] ) && $atts['extra-id'] > 0 ? $atts['extra-id'] : 0;
			}
			$id    = apply_filters( 'bkx_set_custom_id_by_post_type', $id, $atts );
			$order = 'ASC';
			if ( isset( $atts['order'] ) && $atts['order'] != '' ) {
				$order = $atts['order'];
			}
			$order_by = 'ID';
			if ( isset( $atts['order-by'] ) && $atts['order-by'] != '' ) {
				$order_by = $atts['order-by'];
			}
			$class = '';
			if ( $id > 0 ) {
				$query = new WP_Query(
					array(
						'post_type' => $post_type,
						'post__in'  => array( $id ),
					)
				);
			} else {
				$query = new WP_Query(
					array(
						'post_type' => $post_type,
						'order_by'  => $order_by,
						'order'     => $order,
					)
				);
				$class = 'booking-x-lists';
			}
			$class = apply_filters( 'bkx_set_class_shortcode_by_post_type', $class );
			if ( $query->have_posts() ) :?>
				<div class="container <?php echo esc_attr( $class ); ?>">
					<div class="row">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					if ( $id > 0 ) {
						bkx_get_template( "content-single-{$post_type}.php", $atts );
						do_action( 'bkx_load_template_content_single_file', $post_type );
					} else {
						 bkx_get_template( "content-{$post_type}.php", $atts );
						 do_action( 'bkx_load_template_content_file', $post_type );
					}
				endwhile;
				?>
					</div>
				</div>
				<?php
		 else :
			 do_action( "bookingx_no_{$post_type}_found" );
		 endif;
		 return ob_get_clean();
		}
	}

	/**
	 * @param $atts
	 *
	 * @return mixed|void
	 */
	private static function find_post_type( $atts ) {
		if ( empty( $atts ) ) {
			return;
		}
		$find_archive = array(
			'seat-id'  => 'bkx_seat',
			'base-id'  => 'bkx_base',
			'extra-id' => 'bkx_addition',
		);
		$find_archive = apply_filters( 'bkx_shortcode_archive_list', $find_archive );

		foreach ( $find_archive as $key => $data ) {
			if ( array_key_exists( $key, $atts ) ) {
				return $find_archive[ $key ];
			}
		}
	}
}

add_action( 'init', array( 'BKX_Listing_ShortCodes', 'init' ) );

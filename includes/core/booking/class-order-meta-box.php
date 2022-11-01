<?php // phpcs:ignore
/**
 * BookingX Main Booking Loader Class
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */
/**
 * Class Bkx_Meta_Boxes
 */
class Bkx_Meta_Boxes {


	/**
	 * @var string
	 */
	public $post_type = 'bkx_booking';
	/**
	 * @var array
	 */
	protected static $billing_fields = array();

	/**
	 * Bkx_Meta_Boxes constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Add Meta Box For bkx_booking Post Type
	 */
	public function add_meta_boxes() {
		global $post;
		$order_type_object = get_post_type_object( $this->post_type );
		$order_id          = get_post_meta( $post->ID, 'order_id', true );
		add_meta_box(
			'bkx-general-data',
			sprintf( esc_html( '%s Data' ), $order_type_object->labels->singular_name ),
			array( $this, 'bookingx_output' ),
			$this->post_type,
			'normal',
			'high'
		);
		if ( isset( $order_id ) && $order_id != '' ) {
			add_meta_box( 'bkx-order_note', sprintf( esc_html__( '%1$s #%2$s Notes', 'bookingx' ), $order_type_object->labels->singular_name, $post->ID ), array( $this, 'bookingx_note_output' ), $this->post_type, 'side', 'high' );
		}
		add_meta_box( 'bkx-order_reassign', sprintf( esc_html__( '%s Reassign', 'bookingx' ), $order_type_object->labels->singular_name ), array( $this, 'bookingx_reassign_output' ), $this->post_type, 'side' );
	}

	/**
	 * Rename Meta Boxes
	 */
	public function rename_meta_boxes() {
		global $post;
		// Comments/Reviews.
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) ) {
			remove_meta_box( 'commentsdiv', 'product', 'normal' );
			add_meta_box( 'commentsdiv', __( 'Reviews', 'bookingx' ), 'post_comment_meta_box', 'product', 'normal' );
		}
	}

	/**
	 * No Require below Meta Boxes Remove
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'commentsdiv', $this->post_type, 'normal' );
		remove_meta_box( 'commentstatusdiv', $this->post_type, 'normal' );
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'submitdiv', $this->post_type, 'side' );
	}

	/**
	 * @param  $post
	 * @param  null $return_type
	 * @return string|void
	 * @throws Exception
	 */
	public function bookingx_output( $post, $return_type = null ) {
     // phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
		$order_id = $post->ID;
		if ( empty( $post->ID ) ) {
			return;
		}
		$seat_id = get_post_meta( $order_id, 'seat_id', true );
		if ( isset( $seat_id ) && $seat_id != '' ) {
			$order_summary      = '';
			$gateways_name      = '';
			$order_summary_note = '';
			$orderObj           = new BkxBooking();
			$order_meta         = $orderObj->get_order_meta_data( $post->ID );
			wp_nonce_field( 'bookingx_save_data', 'bookingx_meta_nonce' );
			$extra_id           = get_post_meta( $post->ID, 'addition_ids', true );
			$extra_id           = rtrim( $extra_id, ',' );
			$booking_start_date = date( 'm/d/Y', strtotime( $order_meta['booking_start_date'] ) );
			$seat_alias         = bkx_crud_option_multisite( 'bkx_alias_seat' );
			$base_alias         = bkx_crud_option_multisite( 'bkx_alias_base' );
			$addition_alias     = bkx_crud_option_multisite( 'bkx_alias_addition' );
			$payment_status      = '';
			$check_total_payment = 0;
			$payment_meta        = get_post_meta( $order_id, 'payment_meta', true );
			if ( ! empty( $payment_meta ) && is_array( $payment_meta ) ) {
				$payment_status      = $payment_meta['payment_status'];
				$check_total_payment = $payment_meta['pay_amt'];
			}
			$payment_status      = ( $payment_status ) ? $payment_status : 'Pending';
			$seat_base_edit_mode = 0;
			if ( $payment_status == 'Completed' || $payment_status == 'completed' ) {
				$seat_base_edit_mode = 1;
			}

			if ( isset( $check_total_payment ) && $check_total_payment != '' && $check_total_payment != 0 ) {
				$check_remaining_payment = $order_meta['total_price'] - $check_total_payment;
			}
			$search['search_by']   = 'future';
			$search['search_date'] = date( 'Y-m-d' );
			$booked_days           = '';
			$BookedRecords         = $orderObj->GetBookedRecordsByUser( $search );
			if ( ! empty( $BookedRecords ) ) {
				foreach ( $BookedRecords as $bookings ) :
					if ( isset( $bookings['booking_multi_days'] ) && ! empty( $bookings['booking_multi_days'] ) ) :
						$booking_multi_days = $bookings['booking_multi_days'];
						if ( ! is_array( $booking_multi_days ) ) {
							$booking_multi_days_arr = explode( ',', $booking_multi_days );
						} else {
							$booking_multi_days_arr = $booking_multi_days;
						}

						if ( ! empty( $booking_multi_days_arr ) ) {
							$booking_multi_days_data   = ! empty( $booking_multi_days_arr[0] ) ? $booking_multi_days_arr[0] : '';
							$booking_multi_days_data_1 = ! empty( $booking_multi_days_arr[1] ) ? $booking_multi_days_arr[1] : '';
							$booking_multi_days_range  = bkx_getDatesFromRange( $booking_multi_days_data, $booking_multi_days_data_1, 'm/d/Y' );
							if ( ! empty( $booking_multi_days_range ) ) {
								foreach ( $booking_multi_days_range as $multi_days ) {
									$booked_days .= $multi_days . ',';
								}
							}
						}
					endif;
					$booked_start_dates = date( 'm/d/Y', strtotime( $bookings['booking_start_date'] ) );
					$booked_days       .= $booked_start_dates . ',';
				endforeach;
				$booked_days = rtrim( $booked_days, ',' );
			} else {
				$booked_days = 0;
			}
			$booked_days_arr      = explode( ',', $booked_days );
			$booked_days_filtered = implode( ',', array_unique( $booked_days_arr ) );
			$extra_data           = sprintf( '<p> ' . esc_html( '%s service\'s :' ), esc_html( $addition_alias ) );
			if ( ! empty( $order_meta['extra_arr'] ) ) {
				foreach ( $order_meta['extra_arr'] as $key => $extra_arr ) {
					$extra_data .= sprintf( '&nbsp;<a href="' . esc_url( '%1$s' ) . '" target="_blank"> ' . esc_html( '%2$s' ) . ' </a>&nbsp;', esc_url( $extra_arr['permalink'] ), esc_html( $extra_arr['title'] ), esc_html( $extra_arr['title'] ) );
				}
			}
			$bkx_baseObj      = new BkxBase( '', $order_meta['base_id'] );
			$base_time        = $bkx_baseObj->get_time( $order_meta['base_id'] );
			$base_time_option = get_post_meta( $post->ID, 'base_time_option', true );
			$base_time_option = ( isset( $base_time_option ) && $base_time_option != '' ) ? $base_time_option : 'H';
			$total_time       = '-';
			$end_time         = date( 'H:i', strtotime( $order_meta['booking_end_date'] ) );
			$date_format      = bkx_crud_option_multisite( 'date_format' );

			if ( isset( $base_time_option ) && $base_time_option == 'H' ) {
				$total_time = sprintf( esc_html__( '%s', 'bookingx' ), date( 'h:i A', strtotime( $order_meta['booking_start_date'] ) ), date( 'h:i A ', strtotime( $order_meta['booking_end_date'] ) ) );
				$duration   = sprintf( esc_html__( '%s', 'bookingx' ), esc_html( $order_meta['total_duration'] ) );
				$date_data  = sprintf( esc_html__( '%s', 'bookingx' ), date( $date_format, strtotime( $order_meta['booking_date'] ) ) );
				$duration   = sprintf( esc_html__( '%s', 'bookingx' ), esc_html( $duration ) );
			} else {
				list($date_data, $duration) = getDayDateDuration( $order_id );
			}

			if ( isset( $order_meta['seat_id'] ) && $order_meta['seat_id'] != '' ) {
				$order_summary  = sprintf( '<div class="bkx-order_summary_full bkx-admin-order-detail">' );
				$duration       = str_replace( '(', '', esc_html( $duration ) );
				$duration       = str_replace( ')', '', esc_html( $duration ) );
				$order_summary .= sprintf( '<h3> ' . esc_html( 'Booking #%1$d - %2$s | %3$s %4$s | Duration : %5$s' ) . ' </h3>', $post->ID, $orderObj->get_order_status( $post->ID ), "{$order_meta['booking_time_from']} - {$end_time}", $date_data, $duration );
				$order_summary .= sprintf( '<div class="bkx-general_full">' );
				$order_summary .= sprintf( '<p>Full Name : <span id="bkx_fname"> %1$s </span><span id="bkx_lname"> %2$s </span></p>', esc_html( $order_meta['first_name'] ), esc_html( $order_meta['last_name'] ) );
				$order_summary .= sprintf( '<p>Phone : <a href="callto:%1$s" id="bkx_phone"><span id="bkx_phone">%2$s</span></a></p>', esc_attr( $order_meta['phone'] ), esc_html( $order_meta['phone'] ) );
				$order_summary .= sprintf( '<p>Email : <a href="mailto:%1$s" id="bkx_email"><span id="bkx_email">%2$s</span></a></p>', esc_attr( $order_meta['email'] ), $order_meta['email'] );
				$order_summary .= sprintf( '<p><span id="bkx_address"> ' . esc_html( '%1$s %2$s' ) . ' </span></p>', esc_html( $order_meta['city'] ), esc_html( $order_meta['state'] ) );
				$order_summary .= sprintf( '<p><span id="bkx_postcode"> ' . esc_html( '%s ' ) . ' </span> </p>', esc_html( $order_meta['postcode'] ) );
				$order_summary .= sprintf( '</div>', 'bookingx' );

				$order_summary .= sprintf( '<div class="bkx-general_full">', 'bookingx' );
				$total_price    = apply_filters( 'bkx_booking_total_price_update', $order_meta['total_price'], $post->ID );
				$order_summary .= sprintf( '<p>' . esc_html( 'Total : %1$s' ) . ' <span id="bkx_booking_total"> ' . esc_html( '%2$s' ) . '  </span> </p> </p>', bkx_get_current_currency(), $total_price );
				$payment_source = $order_meta['bkx_payment_gateway_method'];
				if ( ! empty( $payment_source ) && $payment_source != 'cash' && $payment_source != 'N' ) {
					$BkxPaymentCore             = new BkxPaymentCore();
					$bkx_get_available_gateways = $BkxPaymentCore->bkx_get_available_gateways();
					if ( ! empty( $bkx_get_available_gateways ) ) {
						$gateways_name = $bkx_get_available_gateways[ $payment_source ]['title'];
					}
					$payment_source = sprintf( esc_html( '%s' ), esc_html( $gateways_name ) );
				} else {
					$payment_source = sprintf( esc_html__( 'Offline', 'bookingx' ) );
				}
				$order_summary .= sprintf( '<p id="bkx_payment_status"> ' . esc_html( 'Payment : %s ' ) . ' </p>', esc_html( $payment_status ) );
				if ( $payment_status == 'Completed' || $payment_status == 'completed' ) {
					$order_summary .= sprintf( '<p> ' . esc_html( 'Payment By : %s' ) . ' </p>', esc_html( $payment_source ) );
					$order_summary .= sprintf( '<p> ' . esc_html( 'Transaction Id : %s' ) . ' </p>', esc_html( $payment_meta['transactionID'] ) );
					if ( isset( $check_remaining_payment ) && $check_remaining_payment != 0 && $check_remaining_payment != '' ) {
						$order_summary_note .= sprintf( '<p> <b> ' . esc_html( 'Note : %1$s%2$s Payment will be made when the customer comes for the booking. ' ) . '  </b> </p>', bkx_get_current_currency(), $check_remaining_payment );
					}
				} else {
					$order_summary_note = sprintf( '<p> ' . esc_html__( 'Note  : Payment will be made when the customer comes for the booking.', 'bookingx' ) . '   </p>' );
				}
				$order_summary .= sprintf( '</div>', 'bookingx' );
				$extra_data     = sprintf( '<p id="bkx_extra_name">' . esc_html( '%s service\'s :' ), esc_html( $addition_alias ) );
				$extra_data    .= '<span id="bkx_extra_data">';
				if ( ! empty( $order_meta['extra_arr'] ) ) {
					foreach ( $order_meta['extra_arr'] as $key => $extra_arr ) {
						$extra_data .= sprintf( '&nbsp;<a href="%1$s" target="_blank">%2$s</a>&nbsp;', esc_url( $extra_arr['permalink'] ), esc_attr( $extra_arr['title'] ), esc_attr( $extra_arr['title'] ) );
					}
				} else {
					$extra_data .= sprintf( '&nbsp;None', 'bookingx' );
				}
				$extra_data    .= '</span>';
				$order_summary .= sprintf( '<div class="bkx-general_full">', 'bookingx' );
				$reschedule_url = get_edit_post_link( $order_id );
				$order_summary .= sprintf( '<p id="bkx_seat_name">' . esc_html( '%1$s Name :' ) . '  <a href="%2$s" title="%3$s">%4$s</a></p>', esc_attr( $seat_alias ), esc_url( $order_meta['seat_arr']['permalink'] ), esc_html( $order_meta['seat_arr']['title'] ), esc_html( $order_meta['seat_arr']['title'] ) );
				$order_summary .= sprintf( '<p id="bkx_base_name">' . esc_html( ' %1$s Name :' ) . '  <a href="%2$s">%3$s</a> </p>', esc_attr( $base_alias ), esc_url( $order_meta['base_arr']['permalink'] ), esc_attr( $order_meta['base_arr']['title'] ), esc_html( $order_meta['base_arr']['title'] ) );
				$order_summary .= $extra_data;
				$order_summary .= sprintf( '</div>', 'bookingx' );
				$order_summary .= sprintf( '<div style="clear:left;">&nbsp;</div>', 'bookingx' );

				$order_timezone = apply_filters( 'bkx_booking_time_zone_admin_content', '', $order_meta );
				if ( ! empty( $order_timezone ) ) {
					$order_summary .= '<h3>Timezone Details</h3>';
					$order_summary .= sprintf( '<p id="bkx_time_zone"> ' . esc_html( 'Your Time : %s' ) . '</p>', esc_html( $order_timezone['system']['booking_time'] ) );
					$order_summary .= sprintf( '<p id="bkx_time_zone"> ' . esc_html( 'Customer Time: %1$s (%2$s)' ) . '</p>', esc_html( $order_timezone['user']['booking_time'] ), esc_html( $order_timezone['user']['time_zone'] ) );
				}

				if ( isset( $order_summary_note ) && $order_summary_note != '' ) {
					$order_summary .= $order_summary_note;
				}
				$screen = get_current_screen();
				if ( ! is_wp_error( $screen ) && $screen->parent_file == 'edit.php?post_type=bkx_booking' ) {

				} else {
					if ( $return_type == 'ajax' && isset( $get_order_status, $booking_start_date ) && ( $get_order_status != 'Cancelled' || $get_order_status == 'Completed' || time() <= strtotime( $booking_start_date ) ) ) {
						$order_summary .= sprintf( '<a href="%s" class="button button-primary button-large note-btn" name="bkx_reschedule_booking"> ' . esc_html__( 'Reschedule Booking', 'bookingx' ) . ' </a>', esc_url( $reschedule_url ) );
					}
				}

				$order_summary .= sprintf( '</div>', 'bookingx' );
			}
			if ( $return_type == 'ajax' ) {
				return apply_filters( 'booking_form_extra_summary', $order_summary, $post->ID );
			}
			echo apply_filters( 'booking_form_extra_summary', $order_summary, $post->ID ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			echo do_shortcode( '[bkx_booking_form order_post_id=' . $post->ID . ']' );
		} else {
			echo do_shortcode( '[bkx_booking_form]' );
		}
     // phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date
	}

	/**
	 * @param  $post
	 * @param  null $return_type
	 * @return string|void
	 * @throws Exception
	 */
	public function bookingx_note_output( $post, $return_type = null ) {
		if ( empty( $post->ID ) ) {
			return;
		}

		$orderObj   = new BkxBooking();
		$order_meta = $orderObj->get_order_meta_data( $post->ID );

		if ( empty( $order_meta['seat_id'] ) ) {
			echo '<style>#bkx-order_summary { display : none; };</style>';
			return;
		}
		$script         = '<script type="text/javascript"> jQuery(document).ready(function(){ jQuery( "#bkx_id_add_custom_note" ).on( "click", function() {
		jQuery("#bkx_add_custom_note_loader").show();
		jQuery("#bkx_id_add_custom_note").hide();
		var booking_id = jQuery( "#bkx_booking_id" ).val();
		var bkx_custom_note = jQuery( "#bkx_custom_note" ).val();
		if(bkx_custom_note == ""){
			jQuery("#bkx_add_custom_note_err").html("Please add note.");
			jQuery("#bkx_add_custom_note_loader").hide();
			jQuery("#bkx_id_add_custom_note").show();
			return false;
		}
		if(confirm("Are you sure want to add note?")){
			jQuery("#bkx_add_custom_note_err").hide();
			var data = {
	            "action": "bkx_action_add_custom_note",
	            "bkx_custom_note": bkx_custom_note,
	            "booking_id" : booking_id
	        };
	        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	        jQuery.post(ajaxurl, data, function(response) {
	        	jQuery("#bkx_add_custom_note_loader").hide();
	        	jQuery("#bkx_id_add_custom_note").hide();
	             location.reload();
	        });
		}		
	});});</script>';
		$order_summary  = sprintf( '<div class="bkx-order_summary bkx-order-note">', 'bookingx' );
		$order_summary .= sprintf( '<h3>' . esc_html( 'Booking #%d Notes' ) . '</h3>', $post->ID );
		$order_summary .= sprintf( '<span id="bkx_add_custom_note_err"></span><textarea rows="4" id="bkx_custom_note"></textarea>' );
		$order_summary .= sprintf( '<input type="hidden" id="bkx_booking_id" value="%d">', esc_attr( $post->ID ) );
		$order_summary .= sprintf( '<div class="bkx-note-actions"><a class="button button-primary button-large note-btn" id="bkx_id_add_custom_note" name="bkx_add_custom_note"> Add note </a> <span id="bkx_add_custom_note_loader" style="display:none;"> Please wait.. </span></div>' );
		$order_summary .= $orderObj->get_booking_notes( $post->ID );
		$order_summary .= sprintf( '</div>' );
		$order_summary .= $script;
		if ( $return_type == 'ajax' ) {
			return $order_summary;
		}
		echo $order_summary; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * @param  $post
	 * @param  null $return_type
	 * @return string|void
	 * @throws Exception
	 */
	public function bookingx_reassign_output( $post, $return_type = null ) {
		if ( empty( $post->ID ) ) {
			return;
		}
		$seat_alias = bkx_crud_option_multisite( 'bkx_alias_seat' );
		$orderObj   = new BkxBooking();
		$order_meta = $orderObj->get_order_meta_data( $post->ID );

		if ( empty( $order_meta['seat_id'] ) ) {
			echo '<style>#bkx-order_reassign { display : none; };</style>';
			return;
		}
		$reassign_booking  = '';
		$reassign_booking .= sprintf(
			'<div class="bkx-order_summary"><p>Current %1$s assign : <a href="%2$s" title="%3$s">%4$s</a></p>',
			$seat_alias,
			$order_meta['seat_arr']['permalink'],
			$order_meta['seat_arr']['title'],
			$order_meta['seat_arr']['title']
		);
		// $source = $order_meta['booking_start_date'];
		// $date = new DateTime($source);
		// $booking_start_date = addslashes($date->format('m/d/Y'));
		// $start_time = addslashes($date->format('H:i:s'));
		$order_start_date    = isset( $order_meta['booking_start_date'] ) ? $order_meta['booking_start_date'] : '';
		$order_end_date      = isset( $order_meta['booking_end_date'] ) ? $order_meta['booking_end_date'] : '';
		$order_base_id       = isset( $order_meta['base_id'] ) ? $order_meta['base_id'] : '';
		$get_available_staff = bkx_reassign_available_emp_list( $order_meta['seat_id'], $order_start_date, $order_end_date, $order_base_id );
		$reassign_booking   .= '<b>Reassign ' . esc_html( $seat_alias ) . ' to booking: </b>
			 <select id="id_reassign_booking_' . esc_attr( $order_meta['order_id'] ) . '" name="reassign_booking" data-order-id="' . esc_attr( $order_meta['order_id'] ) . '">
					  <option value=""> --Reassign ' . esc_html( $seat_alias ) . ' --</option>';
		if ( ! empty( $get_available_staff ) ) :
			foreach ( $get_available_staff as $staff ) :
				if ( isset( $staff['name'] ) && $staff['name'] != '' ) :
					$reassign_booking .= ' <option value="' . esc_attr( $staff['id'] ) . '">' . esc_html( ucwords( $staff['name'] ) ) . ' </option>';
				endif;
			endforeach;
		endif;
		$reassign_booking .= '</select></div>';
		if ( $return_type == 'ajax' ) {
			return $reassign_booking;
		} else {
			echo $reassign_booking; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		}
	}
}

new Bkx_Meta_Boxes();

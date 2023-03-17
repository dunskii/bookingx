<?php
/**
 * BookingX Core functions Load.
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Includes/Core
 */

defined( 'ABSPATH' ) || exit;
/**
 * Booking Action Update in Admin side
 */
function bkx_action_status() { 	if ( empty( $_POST['status'] ) ) { //phpcs:ignore
		return;
}

	$bkx_action_status = explode( '_', sanitize_text_field( wp_unslash( $_POST['status'] ) ) ); //phpcs:ignore

	$order_id     = $bkx_action_status[0];
	$order_status = $bkx_action_status[1];
if ( is_multisite() ) :
	$blog_id = get_current_blog_id();
	switch_to_blog( $blog_id );
	endif;
	$bkx_booking = new BkxBooking( '', $order_id );
	$bkx_booking->update_status( $order_status );
	wp_die(); // this is required to terminate immediately and return a proper response.
}

add_action( 'wp_ajax_nopriv_bkx_action_status', 'bkx_action_status' );
add_action( 'wp_ajax_bkx_action_status', 'bkx_action_status' );

/**
 * Get Time Format Dropdown Auto Suggest
 */
function bkx_ajax_get_time_format() {
	$day_input = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : ''; //phpcs:disable WordPress.Security.NonceVerification
	if ( isset( $day_input ) && $day_input <= 12 ) {
		$day_input = isset( $_REQUEST['name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'] ) ) : ''; //phpcs:disable WordPress.Security.NonceVerification
	} else {
		$day_input = isset( $_REQUEST['name'][0] ) ? sanitize_text_field( wp_unslash( $_REQUEST['name'][0] ) ) : ''; //phpcs:disable WordPress.Security.NonceVerification
	}
	$am_array         = array_map(
		function ( $n ) {
			return sprintf( '%d:00 AM', $n );
		},
		range( $day_input, $day_input )
	);
	$pm_array         = array_map(
		function ( $n ) {
			return sprintf( '%d:00 PM', $n );
		},
		range( $day_input, $day_input )
	);
	$days_array_merge = array_merge( $am_array, $pm_array );
	$search_result1   = $days_array_merge;
	echo wp_json_encode( $search_result1 );
	wp_die();
}

add_action( 'wp_ajax_nopriv_get_time_format', 'bkx_ajax_get_time_format' );
add_action( 'wp_ajax_get_time_format', 'bkx_ajax_get_time_format' );

/**
 * Set Any Seat ID On Resource Listing Area
 */
function bkx_bookingx_set_as_any_seat_callback() { 	$seat_id             = absint( sanitize_text_field( wp_unslash( $_GET['seat_id'] ) ) ); //phpcs:ignore
	$select_default_seat                                     = bkx_crud_option_multisite( 'select_default_seat' );

	if ( 'bkx_seat' === get_post_type( $seat_id ) && '' === $select_default_seat ) {
		bkx_crud_option_multisite( 'select_default_seat', $seat_id, 'update' );
	} else {
		bkx_crud_option_multisite( 'select_default_seat', '', 'update' );
	}
	wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=bkx_seat' ) );
	wp_die();
}

add_action( 'wp_ajax_bookingx_set_as_any_seat', 'bkx_bookingx_set_as_any_seat_callback' );
add_action( 'wp_ajax_nopriv_bookingx_set_as_any_seat', 'bkx_bookingx_set_as_any_seat_callback' );

/**
 * Validate User Resource
 */
function bkx_validate_seat_get_user() {
	if ( is_multisite() ) :
		$blog_id = get_current_blog_id();
		switch_to_blog( $blog_id );
	endif;
	if ( empty( $_POST['data'] ) ) { //phpcs:ignore
		return;
	}

	$role          = sanitize_text_field( $_POST['data'] ); //phpcs:ignore
	$user_query    = new WP_User_Query(
		array(
			'role' => $role,
		)
	);
	$free_user_obj = array();
	$all_users     = $user_query->results;
	if ( ! empty( $all_users ) && ! is_wp_error( $all_users ) ) {
		foreach ( $all_users as $key => $user ) {
			$user_id  = $user->data->ID;
			$args     = array(
				'post_type'        => 'bkx_seat',
				'suppress_filters' => false,
				'meta_query'       => array(
					array(
						'key'     => 'seat_wp_user_id',
						'value'   => $user_id,
						'compare' => '=',
					),
				),
			);
			$seat_obj = new WP_Query( $args );
			if ( ! empty( $seat_obj ) && ! is_wp_error( $seat_obj ) ) {
				$free_user_obj[] = get_user_by( 'id', $user_id );
			}
		}
	}
	echo wp_json_encode( $free_user_obj );
	wp_die(); // this is required to return a proper result.
}

add_action( 'wp_ajax_bkx_validate_seat_get_user', 'bkx_validate_seat_get_user' );
add_action( 'wp_ajax_nopriv_bkx_validate_seat_get_user', 'bkx_validate_seat_get_user' );

/**
 * Get User Data when on change event on Resource edit page Admin side
 *
 * @return string
 */
function bkx_get_user_data() {
	if ( is_multisite() ) :
		$blog_id = get_current_blog_id();
		switch_to_blog( $blog_id );
	endif;

	if ( empty( $_POST['data'] ) ) { //phpcs:ignore
		return '';
	}

	$user_id = sanitize_text_field( $_POST['data'] ); //phpcs:ignore

	if ( empty( $user_id ) ) {
		return '';
	}

	$userobj      = get_user_by( 'id', $user_id );
	$user_prefill = array();
	if ( ! empty( $userobj ) && ! is_wp_error( $userobj ) ) {
		$user_email   = $userobj->user_email;
		$seat_post_id = get_user_meta( $user_id, 'seat_post_id', true );
		if ( isset( $seat_post_id ) && '' !== $seat_post_id ) {
			$user_phone = get_post_meta( $seat_post_id, 'seatPhone', true );
		}
		$user_prefill['email'] = $user_email;
		$user_prefill['phone'] = $user_phone;
	}
	echo wp_json_encode( $user_prefill );
	wp_die(); // this is required to return a proper result.
}

add_action( 'wp_ajax_bkx_get_user_data', 'bkx_get_user_data' );
add_action( 'wp_ajax_nopriv_bkx_get_user_data', 'bkx_get_user_data' );
/**
 * Bookings Listing Admin side When Click View Button call this function
 *
 * @return int
 * @throws Exception
 */
function bkx_action_view_summary_callback() {
	if ( is_multisite() ) :
		$blog_id = get_current_blog_id();
		switch_to_blog( $blog_id );
	endif;
	if ( empty( $_POST['post_id'] ) ) { //phpcs:ignore
		return 0;
	}
	$post_id = sanitize_text_field( $_POST['post_id'] ); //phpcs:ignore
	if ( empty( $post_id ) ) {
		return 0;
	}

	$get_post                      = get_post( $post_id );
	$bkx_meta_boxes                = new Bkx_Meta_Boxes();
	$order_full_output             = $bkx_meta_boxes->bookingx_output( $get_post, 'ajax' );
	$bookingx_note_output          = $bkx_meta_boxes->bookingx_note_output( $get_post, 'ajax' );
	$bookingx_reassign_output      = $bkx_meta_boxes->bookingx_reassign_output( $get_post, 'ajax' );
	$bkx_dashboard_column_selected = bkx_crud_option_multisite( 'bkx_dashboard_column' );
	$bkx_booking_columns_data      = ! empty( $bkx_dashboard_column_selected ) ? $bkx_dashboard_column_selected : Bookingx_Admin::bkx_booking_columns_data();
	$calculate_cols                = count( $bkx_booking_columns_data );
	$total_cols                    = ! empty( $bkx_booking_columns_data ) ? $calculate_cols - 3 : 10;

	$output = '<td class="section-1 ' . esc_attr( $calculate_cols ) . '" colspan="' . esc_attr( $total_cols ) . '">' . $order_full_output . '</td>
       <td colspan="3" class="section-2">' . $bookingx_note_output . '' . $bookingx_reassign_output . '</td>';
	echo apply_filters( 'bkx_order_summary_output', $output, esc_html( $post_id ) ); //phpcs:ignore
	wp_die();
}

add_action( 'wp_ajax_bkx_action_view_summary', 'bkx_action_view_summary_callback' );
/**
 * Admin Booking view add custom note.
 *
 * @return int
 */
function bkx_action_add_custom_note_callback() {
	if ( is_multisite() ) :
		$blog_id = get_current_blog_id();
		switch_to_blog( $blog_id );
	endif;
	if ( empty( $_POST['booking_id'] ) ) { //phpcs:ignore
		return '';
	}
	$booking_id      = sanitize_text_field( $_POST['booking_id'] ); //phpcs:ignore
	$bkx_custom_note = sanitize_text_field( $_POST['bkx_custom_note'] ); //phpcs:ignore

	if ( empty( $booking_id ) || empty( $bkx_custom_note ) ) {
		return 0;
	}

	$booking_obj = new BkxBooking( '', $booking_id );
	$booking_obj->add_order_note( sprintf( __( '%s', 'bookingx' ), $bkx_custom_note ), 0 ); //phpcs:ignore
	wp_die();
}

add_action( 'wp_ajax_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback' );
add_action( 'wp_ajax_nopriv_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback' );

<?php // phpcs:ignore
/**
 * Booking Setting Save
 *
 * @package Bookingx/admin
 * @since      1.0.6
 */

defined( 'ABSPATH' ) || exit;
/**
 * This Hook Perform While BKX Admin Save Button Perform
 */
function bkx_setting_save_action() {
	do_action( 'bxk_custom_setting_save_process' );
	$api_flag = isset( $_POST['api_flag'] ) && ! empty( $_POST['api_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['api_flag'] ) ) : 0;// phpcs:ignore
	if ( 1 === $api_flag || '1' === $api_flag ) {
		$paypal_status = ! empty( $_POST['paypal_status'] ) ? sanitize_text_field( wp_unslash( $_POST['paypal_status'] ) ) : ''; // phpcs:ignore
		$paypal_status = isset( $paypal_status ) && '1' === $paypal_status ? 1 : 0;
		bkx_crud_option_multisite( 'bkx_api_paypal_paypalmode', ! empty( $_POST['paypalmode'] ) ? sanitize_text_field( wp_unslash( $_POST['paypalmode'] ) ) : '', 'update' );// phpcs:ignore
		bkx_crud_option_multisite( 'bkx_api_paypal_username', ! empty( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '', 'update' );// phpcs:ignore
		bkx_crud_option_multisite( 'bkx_api_paypal_password', ! empty( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '', 'update' );// phpcs:ignore
		bkx_crud_option_multisite( 'bkx_api_paypal_signature', ! empty( $_POST['signature'] ) ? sanitize_text_field( wp_unslash( $_POST['signature'] ) ) : '', 'update' );// phpcs:ignore
		bkx_crud_option_multisite( 'bkx_gateway_paypal_express_status', $paypal_status, 'update' );// phpcs:ignore
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$redirect = add_query_arg( array( 'bkx_success' => 'PAU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
			wp_safe_redirect( $redirect );
		}
	}
	$bkx_emails_settings = ! empty( $_POST['bkx_emails_settings'] ) ? sanitize_text_field( wp_unslash( $_POST['bkx_emails_settings'] ) ) : ''; // phpcs:ignore
	if ( isset( $bkx_emails_settings ) && ( 1 === $bkx_emails_settings || '1' === $bkx_emails_settings ) ) {
		foreach ( $_POST as $field_key => $field_value ) { // phpcs:ignore
			if ( strpos( $field_key, 'additional_content' ) !== false ) {
				bkx_crud_option_multisite( $field_key, wp_kses_post( $field_value ), 'update' );
			} else {
				bkx_crud_option_multisite( $field_key, sanitize_text_field( $field_value ), 'update' );
			}
		}
		if ( ! empty( $_POST['bkx_new_booking_enabled'] ) ) { // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_new_booking_enabled', sanitize_text_field( wp_unslash( $_POST['bkx_new_booking_enabled'] ) ), 'update' ); // phpcs:ignore
		}
		$redirect = add_query_arg( array( 'bkx_success' => 'ESS' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$google_map_api_flag = ! empty( $_POST['google_map_api_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['google_map_api_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $google_map_api_flag ) && ( 1 === $google_map_api_flag || '1' === $google_map_api_flag  ) && ! empty( $_POST['gmap_key'] ) ) { // phpcs:ignore
		bkx_crud_option_multisite( 'bkx_api_google_map_key', sanitize_text_field( wp_unslash( $_POST['gmap_key'] ) ), 'update' ); // phpcs:ignore
		$redirect = add_query_arg( array( 'bkx_success' => 'GAI' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$other_setting_flag = ! empty( $_POST['other_setting_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['other_setting_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $other_setting_flag ) && ( 1 === $other_setting_flag || '1' === $other_setting_flag ) ) {
		if ( isset( $_POST['bkx_dashboard_column'] ) ) { // phpcs:ignore
			$bkx_dashboard_column = array_map( 'sanitize_text_field', wp_unslash( $_POST['bkx_dashboard_column'] ) );  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_dashboard_column', $bkx_dashboard_column, 'update' );
		}

		if ( isset( $_POST['enable_cancel_booking'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'enable_cancel_booking', sanitize_text_field( $_POST['enable_cancel_booking'] ), 'update' ); // phpcs:ignore
		}

		if ( isset( $_POST['cancellation_policy_page_id'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'cancellation_policy_page_id', sanitize_text_field( $_POST['page_id'] ), 'update' ); // phpcs:ignore
		}

		if ( isset( $_POST['enable_any_seat'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'enable_any_seat', sanitize_text_field( $_POST['enable_any_seat'] ), 'update' ); // phpcs:ignore
		}

		if ( isset( $_POST['bkx_enable_customer_dashboard'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'bkx_enable_customer_dashboard', sanitize_text_field( $_POST['bkx_enable_customer_dashboard'] ), 'update' ); // phpcs:ignore
		}
		$redirect = add_query_arg( array( 'bkx_success' => 'OSE' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}
	$payment_option_flag = ! empty( $_POST['payment_option_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_option_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $payment_option_flag ) && ( 1 === $payment_option_flag || '1' === $payment_option_flag ) && isset( $_POST['currency_option'] ) ) { // phpcs:ignore
		bkx_crud_option_multisite( 'currency_option', sanitize_text_field( $_POST['currency_option'] ), 'update' ); // phpcs:ignore
		$redirect = add_query_arg( array( 'bkx_success' => 'COU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$role_setting_flag = ! empty( $_POST['role_setting_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['role_setting_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $role_setting_flag ) && ( 1 === $role_setting_flag || '1' === $role_setting_flag ) && isset( $_POST['bkx_seat_role'] ) ) { // phpcs:ignore
		bkx_crud_option_multisite( 'bkx_seat_role', sanitize_text_field( $_POST['bkx_seat_role'] ), 'update' ); // phpcs:ignore
		$redirect = add_query_arg( array( 'bkx_success' => 'RAU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$alias_flag = ! empty( $_POST['alias_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['alias_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $alias_flag ) && ( 1 === $alias_flag || '1' === $alias_flag ) ) {
		if ( isset( $_POST['seat_alias'] ) ) { // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_alias_seat', sanitize_text_field( $_POST['seat_alias'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['base_alias'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'bkx_alias_base', sanitize_text_field( $_POST['base_alias'] ), 'update' ); // phpcs:ignore
		}
		if ( isset(  $_POST['addition_alias'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'bkx_alias_addition', sanitize_text_field( $_POST['addition_alias'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_notice_time_extended_text_alias'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'bkx_notice_time_extended_text_alias', sanitize_text_field( $_POST['bkx_notice_time_extended_text_alias'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_label_of_step1'] ) ) { // phpcs:ignore
 			bkx_crud_option_multisite( 'bkx_label_of_step1', sanitize_text_field( $_POST['bkx_label_of_step1'] ), 'update' ); // phpcs:ignore
		}

		$redirect = add_query_arg( array( 'bkx_success' => 'ALU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$google_calendar_flag = ! empty( $_POST['google_calendar_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['google_calendar_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $google_calendar_flag ) && ( 1 === $google_calendar_flag || '1' === $google_calendar_flag ) ) {
		if ( isset( $_POST['client_id'], $_POST['client_secret'], $_POST['redirect_uri'] ) ) { // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_client_id', sanitize_text_field( $_POST['client_id'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_client_secret', sanitize_text_field( $_POST['client_secret'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_redirect_uri', sanitize_text_field( $_POST['redirect_uri'] ), 'update' ); // phpcs:ignore
		}

		$redirect = add_query_arg( array( 'bkx_success' => 'GCD' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$google_calendar_id_flag = ! empty( $_POST['google_calendar_id_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['google_calendar_id_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $google_calendar_id_flag ) && ( 1 === $google_calendar_id_flag || '1' === $google_calendar_id_flag ) && isset( $_POST['calendar_id'] ) ) { // phpcs:ignore
		bkx_crud_option_multisite( 'bkx_google_calendar_id', sanitize_text_field( $_POST['calendar_id'] ), 'update' ); // phpcs:ignore
		$redirect = add_query_arg( array( 'bkx_success' => '' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}
	$template_flag = ! empty( $_POST['template_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['template_flag'] ) ) : ''; // phpcs:ignore

	if ( isset( $template_flag ) && ( 1 === $template_flag || '1' === $template_flag ) ) {
		if ( isset( $_POST['bkx_seat_taxonomy_status'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_seat_taxonomy_status', sanitize_text_field( $_POST['bkx_seat_taxonomy_status'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_set_booking_page'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_set_booking_page', sanitize_text_field( $_POST['bkx_set_booking_page'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_base_taxonomy_status'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_base_taxonomy_status', sanitize_text_field( $_POST['bkx_base_taxonomy_status'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_addition_taxonomy_status'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_addition_taxonomy_status', sanitize_text_field( $_POST['bkx_addition_taxonomy_status'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_legal_options'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_legal_options', sanitize_text_field( $_POST['bkx_legal_options'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_seat_taxonomy_status'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_seat_taxonomy_status', sanitize_text_field( $_POST['bkx_seat_taxonomy_status'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_term_cond_page'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_term_cond_page', sanitize_text_field( $_POST['bkx_term_cond_page'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_cancellation_policy_page'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_cancellation_policy_page', sanitize_text_field( $_POST['bkx_cancellation_policy_page'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['bkx_privacy_policy_page'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_privacy_policy_page', sanitize_text_field( $_POST['bkx_privacy_policy_page'] ), 'update' ); // phpcs:ignore
		}
		if ( isset( $_POST['enable_editor'] ) ) {  // phpcs:ignore
			bkx_crud_option_multisite( 'enable_editor', sanitize_text_field( $_POST['enable_editor'] ), 'update' ); // phpcs:ignore
		}
		$bkx_set_booking_page = ! empty( $_POST['bkx_set_booking_page'] ) ? sanitize_text_field( wp_unslash( $_POST['bkx_set_booking_page'] ) ) : ''; // phpcs:ignore
		if ( isset( $bkx_set_booking_page ) && '' !== $bkx_set_booking_page ) {

			$check_old_content      = get_post( $bkx_set_booking_page );
			$check_old_content_data = $check_old_content->post_content;
			$append_data            = $check_old_content_data;
			if ( strpos( $check_old_content_data, 'bkx_booking_form' ) !== false ) {
				$update_data = false;
			} else {
				$update_data = true;
			}
			if ( true === $update_data ) {
				$append_data .= '  [bkx_booking_form]';
				// Update post.
				$booking_post = array(
					'ID'           => sanitize_text_field( $_POST['bkx_set_booking_page'] ), // phpcs:ignore
					'post_content' => $append_data,
				);
				// Update the post into the database.
				wp_update_post( $booking_post );
			}
		}
		$redirect = add_query_arg( array( 'bkx_success' => 'CSU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$siteuser_flag = ! empty( $_POST['siteuser_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['siteuser_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $siteuser_flag ) && ( 1 === $siteuser_flag || '1' === $siteuser_flag ) ) {
		if ( isset( $_POST['can_edit_seat'], $_POST['can_edit_css'] ) ) { // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_siteuser_canedit_seat', sanitize_text_field( $_POST['can_edit_seat'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_siteclient_canedit_css', sanitize_text_field( $_POST['can_edit_css'] ), 'update' ); // phpcs:ignore
		}
		$redirect = add_query_arg( array( 'bkx_success' => 'OSU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$sitecss_flag = ! empty( $_POST['sitecss_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['sitecss_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $sitecss_flag ) && ( 1 === $sitecss_flag || '1' === $sitecss_flag ) ) {
		if ( isset(
			$_POST['bkx_booking_style'], // phpcs:ignore
			$_POST['bkx_text_color'], // phpcs:ignore
			$_POST['bkx_form_slot_time_color'], // phpcs:ignore
			$_POST['bkx_progressbar_color'], // phpcs:ignore
			$_POST['bkx_cal_day_color'], // phpcs:ignore
			$_POST['bkx_cal_day_selected_color'], // phpcs:ignore
			$_POST['bkx_time_available_color'], // phpcs:ignore
			$_POST['bkx_time_selected_color'], // phpcs:ignore
			$_POST['bkx_time_unavailable_color'], // phpcs:ignore
			$_POST['bkx_next_btn'], // phpcs:ignore
			$_POST['bkx_prev_btn'], // phpcs:ignore
			$_POST['bkx_pay_now_btn'] // phpcs:ignore
		) ) { // phpcs:ignore
			$bkx_booking_style = sanitize_text_field( $_POST['bkx_booking_style'] ); // phpcs:ignore
			$bkx_booking_style = ( ( ! isset( $bkx_booking_style ) || '' === $bkx_booking_style ) ? 'default' : $bkx_booking_style );
			bkx_crud_option_multisite( 'bkx_booking_style', sanitize_text_field( $bkx_booking_style ), 'update' );
			bkx_crud_option_multisite( 'bkx_form_text_color', sanitize_text_field( $_POST['bkx_text_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_form_slot_time_color', sanitize_text_field( $_POST['bkx_form_slot_time_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_active_step_color', sanitize_text_field( $_POST['bkx_progressbar_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_cal_day_color', sanitize_text_field( $_POST['bkx_cal_day_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_cal_day_selected_color', sanitize_text_field( $_POST['bkx_cal_day_selected_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_time_available_color', sanitize_text_field( $_POST['bkx_time_available_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_time_selected_color', sanitize_text_field( $_POST['bkx_time_selected_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_time_unavailable_color', sanitize_text_field( $_POST['bkx_time_unavailable_color'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_next_btn', sanitize_text_field( $_POST['bkx_next_btn'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_prev_btn', sanitize_text_field( $_POST['bkx_prev_btn'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_pay_now_btn', sanitize_text_field( $_POST['bkx_pay_now_btn'] ), 'update' ); // phpcs:ignore
		}
		$redirect = add_query_arg( array( 'bkx_success' => 'STU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$business_flag = ! empty( $_POST['business_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['business_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $business_flag ) && ( 1 === $business_flag || '1' === $business_flag ) ) {
		if ( isset(
			$_POST['bkx_business_name'], // phpcs:ignore
			$_POST['bkx_business_email'], // phpcs:ignore
			$_POST['bkx_business_phone'], // phpcs:ignore
			$_POST['bkx_business_address_1'], // phpcs:ignore
			$_POST['bkx_business_address_2'], // phpcs:ignore
			$_POST['bkx_business_city'], // phpcs:ignore
			$_POST['bkx_business_state'], // phpcs:ignore
			$_POST['bkx_business_zip'], // phpcs:ignore
			$_POST['bkx_business_country'] // phpcs:ignore
		) ) {
			bkx_crud_option_multisite( 'bkx_business_name', sanitize_text_field( $_POST['bkx_business_name'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_email', sanitize_text_field( $_POST['bkx_business_email'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_phone', sanitize_text_field( $_POST['bkx_business_phone'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_address_1', sanitize_text_field( $_POST['bkx_business_address_1'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_address_2', sanitize_text_field( $_POST['bkx_business_address_2'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_city', sanitize_text_field( $_POST['bkx_business_city'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_state', sanitize_text_field( $_POST['bkx_business_state'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_zip', sanitize_text_field( $_POST['bkx_business_zip'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_business_country', sanitize_text_field( $_POST['bkx_business_country'] ), 'update' ); // phpcs:ignore
		}

		$redirect = add_query_arg( array( 'bkx_success' => 'BIU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$days_operation_flag = ! empty( $_POST['days_operation_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['days_operation_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $days_operation_flag ) && ( 1 === $days_operation_flag || '1' === $days_operation_flag ) && isset( $_POST['biz_ph']  ) ) { // phpcs:ignore
		$biz_ph              = array_map( 'sanitize_text_field', wp_unslash( $_POST['biz_ph'] ) ); // phpcs:ignore
		$selected_days       = array();
		$current_value_index = ! empty( $_POST['current_value_index'] ) ? sanitize_text_field( $_POST['current_value_index'] ) - 1 : 7; // phpcs:ignore
		bkx_crud_option_multisite( 'bkx_business_days', '', 'update' );
		for ( $d = 1; $d <= $current_value_index; $d++ ) {
			if ( isset( $_POST[ 'days_' . $d ] ) && ! empty( $_POST[ 'days_' . $d ] ) ) { // phpcs:ignore
				$days_array = array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'days_' . $d ] ) ); // phpcs:ignore
				if ( ! empty( $days_array ) ) {
					$selected_days[ 'selected_days_' . $d ]['days'] = $days_array;
				}
			}
			if ( isset( $_POST[ 'opening_time_' . $d ] ) && ! empty( $_POST[ 'opening_time_' . $d ] ) && ! empty( $_POST[ 'days_' . $d ] ) ) { // phpcs:ignore
				$selected_days[ 'selected_days_' . $d ]['time']['open'] = sanitize_text_field( wp_unslash( $_POST[ 'opening_time_' . $d ] ) ); // phpcs:ignore
			}
			if ( isset( $_POST[ 'closing_time_' . $d ] ) && ! empty( $_POST[ 'closing_time_' . $d ] ) && ! empty( $_POST[ 'days_' . $d ] ) ) { // phpcs:ignore
				$selected_days[ 'selected_days_' . $d ]['time']['close'] = sanitize_text_field( wp_unslash( $_POST[ 'closing_time_' . $d ] ) ); // phpcs:ignore
			}
		}
		bkx_crud_option_multisite( 'bkx_business_days', $selected_days, 'update' );
		if ( isset( $_POST['bkx_biz_vac_sd'], $_POST['bkx_biz_vac_ed'] ) ) { // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_biz_vac_sd', sanitize_text_field( $_POST['bkx_biz_vac_sd'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_biz_vac_ed', sanitize_text_field( $_POST['bkx_biz_vac_ed'] ), 'update' ); // phpcs:ignore
		}
		bkx_crud_option_multisite( 'bkx_biz_pub_holiday', $biz_ph, 'update' );

		$redirect = add_query_arg( array( 'bkx_success' => 'DOP' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}

	$tax_option_flag = ! empty( $_POST['tax_option_flag'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_option_flag'] ) ) : ''; // phpcs:ignore
	if ( isset( $tax_option_flag ) && ( 1 === $tax_option_flag || '1' === $tax_option_flag ) ) {
		if ( isset( $_POST['bkx_tax_rate'], $_POST['bkx_tax_name'], $_POST['bkx_prices_include_tax'] ) ) { // phpcs:ignore
			$bkx_tax_rate = sanitize_text_field( $_POST['bkx_tax_rate'] ); // phpcs:ignore
			if ( is_numeric( $bkx_tax_rate ) ) {
				bkx_crud_option_multisite( 'bkx_tax_rate', $bkx_tax_rate, 'update' );
			}
			bkx_crud_option_multisite( 'bkx_tax_name', sanitize_text_field( $_POST['bkx_tax_name'] ), 'update' ); // phpcs:ignore
			bkx_crud_option_multisite( 'bkx_prices_include_tax', sanitize_text_field( $_POST['bkx_prices_include_tax'] ), 'update' ); // phpcs:ignore

		}
		$redirect = add_query_arg( array( 'bkx_success' => 'TSU' ), sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
		wp_safe_redirect( $redirect );
	}
}

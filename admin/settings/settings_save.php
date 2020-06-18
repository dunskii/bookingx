<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
function bkx_setting_save_action()
{
    do_action('bxk_custom_setting_save_process');
    if (isset($_POST['api_flag']) && ($_POST['api_flag'] == 1)) {
        $paypal_status = sanitize_text_field($_POST['paypal_status']);
        $paypal_status = isset($paypal_status) && $paypal_status == "1" ? 1 : 0;
        bkx_crud_option_multisite("bkx_api_paypal_paypalmode", sanitize_text_field($_POST['paypalmode']), 'update');
        bkx_crud_option_multisite("bkx_api_paypal_username", sanitize_text_field($_POST['username']), 'update');
        bkx_crud_option_multisite("bkx_api_paypal_password", sanitize_text_field($_POST['password']), 'update');
        bkx_crud_option_multisite("bkx_api_paypal_signature", sanitize_text_field($_POST['signature']), 'update');
        bkx_crud_option_multisite('bkx_gateway_paypal_express_status', $paypal_status, 'update');
        $redirect = add_query_arg(array('bkx_success' => 'PAU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }
    if (isset($_POST['bkx_emails_settings']) && $_POST['bkx_emails_settings'] == 1) {

        foreach ($_POST as $field_key => $field_value) {
            if (strpos($field_key, 'additional_content') !== false) {
                bkx_crud_option_multisite($field_key, wp_kses_post($field_value), 'update');
            } else {
                bkx_crud_option_multisite($field_key, sanitize_text_field($field_value), 'update');
            }

        }
        bkx_crud_option_multisite('bkx_new_booking_enabled', sanitize_text_field($_POST['bkx_new_booking_enabled']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'ESS'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }


    if (isset($_POST['google_map_api_flag']) && ($_POST['google_map_api_flag'] == 1)) {
        $temp_gmap_key = bkx_crud_option_multisite('bkx_api_google_map_key');
        bkx_crud_option_multisite("bkx_api_google_map_key", sanitize_text_field($_POST['gmap_key']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'GAI'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['other_setting_flag']) && ($_POST['other_setting_flag'] == 1)) {
        if (isset($_POST['bkx_dashboard_column'])) {
            $bkx_dashboard_column = array_map('sanitize_text_field', wp_unslash($_POST['bkx_dashboard_column']));
            if (!empty($bkx_dashboard_column)) {
                bkx_crud_option_multisite("bkx_dashboard_column", $bkx_dashboard_column, 'update');
            }
        }

        bkx_crud_option_multisite("enable_cancel_booking", sanitize_text_field($_POST['enable_cancel_booking']), 'update');
        bkx_crud_option_multisite("cancellation_policy_page_id", sanitize_text_field($_POST['page_id']), 'update');
        bkx_crud_option_multisite("enable_any_seat", sanitize_text_field($_POST['enable_any_seat']), 'update');
        bkx_crud_option_multisite("reg_customer_crud_op", sanitize_text_field($_POST['reg_customer_crud_op']), 'update');


        $redirect = add_query_arg(array('bkx_success' => 'OSE'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['payment_option_flag']) && ($_POST['payment_option_flag'] == 1)) {
        bkx_crud_option_multisite("currency_option", sanitize_text_field($_POST['currency_option']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'COU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }


    if (isset($_POST['role_setting_flag']) && ($_POST['role_setting_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_seat_role", sanitize_text_field($_POST['bkx_seat_role']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'RAU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['alias_flag']) && ($_POST['alias_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_alias_seat", sanitize_text_field($_POST['seat_alias']), 'update');
        bkx_crud_option_multisite("bkx_alias_base", sanitize_text_field($_POST['base_alias']), 'update');
        bkx_crud_option_multisite("bkx_alias_addition", sanitize_text_field($_POST['addition_alias']), 'update');
        bkx_crud_option_multisite("bkx_alias_notification", sanitize_text_field($_POST['notification_alias']), 'update');
        bkx_crud_option_multisite("bkx_notice_time_extended_text_alias", sanitize_text_field($_POST['bkx_notice_time_extended_text_alias']), 'update');
        bkx_crud_option_multisite("bkx_label_of_step1", sanitize_text_field($_POST['bkx_label_of_step1']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'ALU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }


    if (isset($_POST['google_calendar_flag']) && ($_POST['google_calendar_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_client_id", sanitize_text_field($_POST['client_id']), 'update');
        bkx_crud_option_multisite("bkx_client_secret", sanitize_text_field($_POST['client_secret']), 'update');
        bkx_crud_option_multisite("bkx_redirect_uri", sanitize_text_field($_POST['redirect_uri']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'GCD'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['google_calendar_id_flag']) && ($_POST['google_calendar_id_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_google_calendar_id", sanitize_text_field($_POST['calendar_id']), 'update');

        $redirect = add_query_arg(array('bkx_success' => ''), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['template_flag']) && ($_POST['template_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_seat_taxonomy_status", sanitize_text_field($_POST['bkx_seat_taxonomy_status']), 'update');
        bkx_crud_option_multisite("bkx_base_taxonomy_status", sanitize_text_field($_POST['bkx_base_taxonomy_status']), 'update');
        bkx_crud_option_multisite("bkx_addition_taxonomy_status", sanitize_text_field($_POST['bkx_addition_taxonomy_status']), 'update');


        bkx_crud_option_multisite("bkx_legal_options", sanitize_text_field($_POST['bkx_legal_options']), 'update');
        bkx_crud_option_multisite("bkx_term_cond_page", sanitize_text_field($_POST['bkx_term_cond_page']), 'update');
        bkx_crud_option_multisite("bkx_cancellation_policy_page", sanitize_text_field($_POST['bkx_cancellation_policy_page']), 'update');
        bkx_crud_option_multisite("bkx_privacy_policy_page", sanitize_text_field($_POST['bkx_privacy_policy_page']), 'update');
        bkx_crud_option_multisite("enable_editor", sanitize_text_field($_POST['enable_editor']), 'update');
        bkx_crud_option_multisite("bkx_set_booking_page", sanitize_text_field($_POST['bkx_set_booking_page']), 'update');
        if (isset($_POST['bkx_set_booking_page']) && $_POST['bkx_set_booking_page'] != '') {
            $append_data = '';
            $check_old_content = get_post($_POST['bkx_set_booking_page']);
            $check_old_content_data = $check_old_content->post_content;
            $append_data = $check_old_content_data;
            if (strpos($check_old_content_data, 'bkx_booking_form') !== false) {
            } else {
                $append_data .= '  [bkx_booking_form]';
                // Update post
                $booking_post = array(
                    'ID' => sanitize_text_field($_POST['bkx_set_booking_page']),
                    'post_content' => $append_data,
                );
                // Update the post into the database
                wp_update_post($booking_post);
            }
        }

        $redirect = add_query_arg(array('bkx_success' => 'CSU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['siteuser_flag']) && ($_POST['siteuser_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_siteuser_canedit_seat", sanitize_text_field($_POST['can_edit_seat']), 'update');
        bkx_crud_option_multisite("bkx_siteclient_canedit_css", sanitize_text_field($_POST['can_edit_css']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'OSU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }
    //@ruchira 8/1/2013 options for css customization
    if (isset($_POST['sitecss_flag']) && ($_POST['sitecss_flag'] == 1)) {
        $bkx_booking_style = $_POST['bkx_booking_style'];
        $bkx_booking_style = ((!isset($bkx_booking_style) || $bkx_booking_style == "") ? "default" : $bkx_booking_style);
        bkx_crud_option_multisite("bkx_booking_style", sanitize_text_field($bkx_booking_style), 'update');
        bkx_crud_option_multisite("bkx_form_text_color", sanitize_text_field($_POST['bkx_text_color']), 'update');
        //bkx_crud_option_multisite("bkx_form_background_color", sanitize_text_field($_POST['bkx_background_color']),'update');
        //bkx_crud_option_multisite("bkx_siteclient_css_border_color", sanitize_text_field($_POST['bkx_border_color']),'update');
        bkx_crud_option_multisite("bkx_active_step_color", sanitize_text_field($_POST['bkx_progressbar_color']), 'update');
        //bkx_crud_option_multisite("bkx_siteclient_css_cal_border_color", sanitize_text_field($_POST['bkx_cal_border_color']),'update');
        bkx_crud_option_multisite("bkx_cal_day_color", sanitize_text_field($_POST['bkx_cal_day_color']), 'update');
        bkx_crud_option_multisite("bkx_cal_day_selected_color", sanitize_text_field($_POST['bkx_cal_day_selected_color']), 'update');
        bkx_crud_option_multisite("bkx_time_available_color", sanitize_text_field($_POST['bkx_time_available_color']), 'update');
        bkx_crud_option_multisite("bkx_time_selected_color", sanitize_text_field($_POST['bkx_time_selected_color']), 'update');
        bkx_crud_option_multisite("bkx_time_unavailable_color", sanitize_text_field($_POST['bkx_time_unavailable_color']), 'update');
        //bkx_crud_option_multisite("bkx_time_block_bg_color", sanitize_text_field($_POST['bkx_time_block_bg_color']),'update');
        //bkx_crud_option_multisite("bkx_time_block_extra_color", sanitize_text_field($_POST['bkx_time_block_extra_color']),'update');
        //bkx_crud_option_multisite("bkx_time_block_service_color", sanitize_text_field($_POST['bkx_time_block_service_color']),'update');
        //bkx_crud_option_multisite("bkx_cal_month_title_color", sanitize_text_field($_POST['bkx_cal_month_title_color']),'update');
        //bkx_crud_option_multisite("bkx_cal_month_bg_color", sanitize_text_field($_POST['bkx_cal_month_bg_color']),'update');
        //bkx_crud_option_multisite("bkx_time_new_selected", sanitize_text_field($_POST['bkx_time_new_selected']),'update');
        bkx_crud_option_multisite("bkx_next_btn", sanitize_text_field($_POST['bkx_next_btn']), 'update');
        bkx_crud_option_multisite("bkx_prev_btn", sanitize_text_field($_POST['bkx_prev_btn']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'STU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['business_flag']) && ($_POST['business_flag'] == 1)) {
        bkx_crud_option_multisite("bkx_business_name", sanitize_text_field($_POST['bkx_business_name']), 'update');
        bkx_crud_option_multisite("bkx_business_email", sanitize_text_field($_POST['bkx_business_email']), 'update');
        bkx_crud_option_multisite("bkx_business_phone", sanitize_text_field($_POST['bkx_business_phone']), 'update');
        bkx_crud_option_multisite("bkx_business_address_1", sanitize_text_field($_POST['bkx_business_address_1']), 'update');
        bkx_crud_option_multisite("bkx_business_address_2", sanitize_text_field($_POST['bkx_business_address_2']), 'update');
        bkx_crud_option_multisite("bkx_business_city", sanitize_text_field($_POST['bkx_business_city']), 'update');
        bkx_crud_option_multisite("bkx_business_state", sanitize_text_field($_POST['bkx_business_state']), 'update');
        bkx_crud_option_multisite("bkx_business_zip", sanitize_text_field($_POST['bkx_business_zip']), 'update');
        bkx_crud_option_multisite("bkx_business_country", sanitize_text_field($_POST['bkx_business_country']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'BIU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['days_operation_flag']) && ($_POST['days_operation_flag'] == 1)) {

        //$biz_ph = array_unique(sanitize_text_field($_POST['biz_ph']));
        $biz_ph = array_map('sanitize_text_field', wp_unslash($_POST['biz_ph']));
        $selected_days = array();
        $current_value_index = !empty($_POST['current_value_index']) ? $_POST['current_value_index'] - 1 : 7;
        bkx_crud_option_multisite("bkx_business_days", '', 'update');
        for ($d = 1; $d <= $current_value_index; $d++) {
            if (isset($_POST['days_' . $d]) && !empty($_POST['days_' . $d])) {
                $days_array = array_map('sanitize_text_field', wp_unslash($_POST['days_' . $d]));
                if (!empty($days_array)) {
                    $selected_days['selected_days_' . $d]['days'] = $days_array;
                }
            }
            if (isset($_POST['opening_time_' . $d]) && !empty($_POST['opening_time_' . $d]) && !empty($_POST['days_' . $d])) {
                $selected_days['selected_days_' . $d]['time']['open'] = sanitize_text_field($_POST['opening_time_' . $d]);
            }
            if (isset($_POST['closing_time_' . $d]) && !empty($_POST['closing_time_' . $d]) && !empty($_POST['days_' . $d])) {
                $selected_days['selected_days_' . $d]['time']['close'] = sanitize_text_field($_POST['closing_time_' . $d]);
            }

        }

        bkx_crud_option_multisite("bkx_business_days", $selected_days, 'update');
        bkx_crud_option_multisite("bkx_biz_vac_sd", sanitize_text_field($_POST['bkx_biz_vac_sd']), 'update');
        bkx_crud_option_multisite("bkx_biz_vac_ed", sanitize_text_field($_POST['bkx_biz_vac_ed']), 'update');
        bkx_crud_option_multisite("bkx_biz_pub_holiday", $biz_ph, 'update');

        $redirect = add_query_arg(array('bkx_success' => 'DOP'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }

    if (isset($_POST['tax_option_flag']) && ($_POST['tax_option_flag'] == 1)) {
        if (is_numeric($_POST['bkx_tax_rate'])) {
            bkx_crud_option_multisite("bkx_tax_rate", sanitize_text_field($_POST['bkx_tax_rate']), 'update');
        }
        bkx_crud_option_multisite("bkx_tax_name", $_POST['bkx_tax_name'], 'update');

        bkx_crud_option_multisite("bkx_prices_include_tax", sanitize_text_field($_POST['bkx_prices_include_tax']), 'update');

        $redirect = add_query_arg(array('bkx_success' => 'TSU'), $_SERVER['HTTP_REFERER']);
        wp_safe_redirect($redirect);
    }
}
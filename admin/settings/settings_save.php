<?php

if(!empty($_POST))
{
	if(isset($_POST['api_flag']) && ($_POST['api_flag']==1))
	{
		$temp_paypalmode = crud_option_multisite('bkx_api_paypal_paypalmode');
 		crud_option_multisite("bkx_api_paypal_paypalmode", $_POST['paypalmode'],'update');

		$temp_username = crud_option_multisite('bkx_api_paypal_username');
		crud_option_multisite("bkx_api_paypal_username", $_POST['username'], 'update');

		$temp_password = crud_option_multisite('bkx_api_paypal_password'); 
		crud_option_multisite("bkx_api_paypal_password", $_POST['password'], 'update');

		$temp_signature = crud_option_multisite('bkx_api_paypal_signature');
		crud_option_multisite("bkx_api_paypal_signature", $_POST['signature'], 'update');

		$_SESSION['success']='PayPal API updated successfully.';
		
	}

	if(isset($_POST['google_map_api_flag']) && ($_POST['google_map_api_flag']==1))
	{
		$temp_gmap_key = crud_option_multisite('bkx_api_google_map_key');
		crud_option_multisite("bkx_api_google_map_key", $_POST['gmap_key'], 'update');

		$_SESSION['success']='Google Map API updated successfully.';
	}

	if(isset($_POST['other_setting_flag']) && ($_POST['other_setting_flag']==1))
	{
		$enable_cancel_booking = crud_option_multisite('enable_cancel_booking');
		$enable_any_seat = crud_option_multisite('enable_any_seat');
		$cancellation_policy_page_id = crud_option_multisite('cancellation_policy_page_id');
		
		$reg_customer_crud_op  = crud_option_multisite('reg_customer_crud_op');

		crud_option_multisite("enable_cancel_booking", $_POST['enable_cancel_booking'], 'update');
		crud_option_multisite("cancellation_policy_page_id", $_POST['page_id'],'update');
		crud_option_multisite("enable_any_seat", $_POST['enable_any_seat'],'update');	

		crud_option_multisite("reg_customer_crud_op", $_POST['reg_customer_crud_op'],'update');	

		$_SESSION['success']='Other Setting updated successfully.';
	}

	if(isset($_POST['payment_option_flag']) && ($_POST['payment_option_flag']==1))
	{
		$currency_option  = crud_option_multisite('currency_option');	
		crud_option_multisite("currency_option", $_POST['currency_option'],'update');

		$_SESSION['success']='Currency option updated successfully.';
	}

        
    if(isset($_POST['role_setting_flag']) && ($_POST['role_setting_flag']==1))
	{
		$bkx_seat_role = crud_option_multisite('bkx_seat_role');
		crud_option_multisite("bkx_seat_role", $_POST['bkx_seat_role'],'update');

		$_SESSION['success']='Role Assignment Setting updated successfully.';
	}
    
     
        
	if(isset($_POST['alias_flag']) && ($_POST['alias_flag']==1))
	{
		crud_option_multisite("bkx_alias_seat", $_POST['seat_alias'],'update');
		crud_option_multisite("bkx_alias_base", $_POST['base_alias'],'update');
		crud_option_multisite("bkx_alias_addition", $_POST['addition_alias'],'update');
		crud_option_multisite("bkx_alias_notification", $_POST['notification_alias'],'update');
		crud_option_multisite("notice_time_extended_text_alias", $_POST['notice_time_extended_text_alias'],'update');
		crud_option_multisite("label_of_step1", $_POST['label_of_step1'],'update');

		$_SESSION['success']='Alias updated successfully.';
	}


	if(isset($_POST['google_calendar_flag']) && ($_POST['google_calendar_flag']==1))
	{
		crud_option_multisite("bkx_client_id", $_POST['client_id'],'update');
		crud_option_multisite("bkx_client_secret", $_POST['client_secret'],'update');
		crud_option_multisite("bkx_redirect_uri", $_POST['redirect_uri'],'update');

		$_SESSION['success']='Google Calendar Details updated successfully.';
	}

	if(isset($_POST['google_calendar_id_flag']) && ($_POST['google_calendar_id_flag']==1))
	{
		crud_option_multisite("bkx_google_calendar_id", $_POST['calendar_id'],'update');
	}

	if(isset($_POST['template_flag']) && ($_POST['template_flag']==1))
	{
		crud_option_multisite("bkx_tempalate_status_pending", $_POST['status_pending'],'update');
		crud_option_multisite("bkx_tempalate_status_ack", $_POST['status_ack'],'update');
		crud_option_multisite("bkx_tempalate_status_complete", $_POST['status_complete'],'update');
		crud_option_multisite("bkx_tempalate_status_missed", $_POST['status_missed'],'update');
		crud_option_multisite("bkx_tempalate_status_cancelled", $_POST['status_cancelled'],'update');	
		crud_option_multisite("bkx_term_cond_page", $_POST['bkx_term_cond_page'],'update');
		crud_option_multisite("bkx_privacy_policy_page", $_POST['bkx_privacy_policy_page'],'update');
		crud_option_multisite("enable_editor", $_POST['enable_editor'],'update');


		$bkx_set_booking_page = crud_option_multisite('bkx_set_booking_page');
		crud_option_multisite("bkx_set_booking_page", $_POST['bkx_set_booking_page'],'update');

        if(isset($_POST['bkx_set_booking_page']) && $_POST['bkx_set_booking_page']!='')
        {
                $append_data ='';
                $check_old_content =  get_post($_POST['bkx_set_booking_page']);
                $check_old_content_data = $check_old_content->post_content;
                $append_data = $check_old_content_data;
                if (strpos($check_old_content_data, 'bookingform') !== false) {
                }
                else
                {
                    $append_data .='  [bookingform]';
                    // Update post
                    $booking_post = array(
                        'ID'           => $_POST['bkx_set_booking_page'],
                        'post_content' => $append_data,
                    );
                    // Update the post into the database
                    wp_update_post( $booking_post );
                }
        }


		$_SESSION['success']='Content Setting updated successfully.';
		
	}
	
	if(isset($_POST['siteuser_flag']) && ($_POST['siteuser_flag']==1))
	{
		$temp_option = crud_option_multisite('bkx_siteuser_canedit_seat');
		crud_option_multisite("bkx_siteuser_canedit_seat", $_POST['can_edit_seat'],'update');
		crud_option_multisite("bkx_siteclient_canedit_css", $_POST['can_edit_css'],'update');

		$_SESSION['success']='Option updated successfully.';
	}
	//@ruchira 8/1/2013 options for css customization 
	if(isset($_POST['sitecss_flag']) && ($_POST['sitecss_flag']==1))
	{
		//for editing the css file 
		crud_option_multisite("bkx_siteclient_css_text_color", $_POST['bkx_text_color'],'update');
		crud_option_multisite("bkx_siteclient_css_background_color", $_POST['bkx_background_color'],'update');
		crud_option_multisite("bkx_siteclient_css_border_color", $_POST['bkx_border_color'],'update');
		crud_option_multisite("bkx_siteclient_css_progressbar_color", $_POST['bkx_progressbar_color'],'update');
		crud_option_multisite("bkx_siteclient_css_cal_border_color", $_POST['bkx_cal_border_color'],'update');
		crud_option_multisite("bkx_siteclient_css_cal_day_color", $_POST['bkx_cal_day_color'],'update');
		crud_option_multisite("bkx_siteclient_css_cal_day_selected_color", $_POST['bkx_cal_day_selected_color'],'update');
		crud_option_multisite("bkx_time_available_color", $_POST['bkx_time_available_color'],'update');
		crud_option_multisite("bkx_time_selected_color", $_POST['bkx_time_selected_color'],'update');
		crud_option_multisite("bkx_time_unavailable_color", $_POST['bkx_time_unavailable_color'],'update');
		crud_option_multisite("bkx_time_block_bg_color", $_POST['bkx_time_block_bg_color'],'update');
		crud_option_multisite("bkx_time_block_extra_color", $_POST['bkx_time_block_extra_color'],'update');
		crud_option_multisite("bkx_time_block_service_color", $_POST['bkx_time_block_service_color'],'update');
		crud_option_multisite("bkx_cal_month_title_color", $_POST['bkx_cal_month_title_color'],'update');
		crud_option_multisite("bkx_cal_month_bg_color", $_POST['bkx_cal_month_bg_color'],'update');


		$_SESSION['success']='Styling updated successfully.';
	}

	if(isset($_POST['business_flag']) && ($_POST['business_flag']==1))
	{
		 
		crud_option_multisite("bkx_business_name", $_POST['business_name'],'update');
		crud_option_multisite("bkx_business_email", $_POST['business_email'],'update');
		crud_option_multisite("bkx_business_phone", $_POST['business_phone'],'update');
		crud_option_multisite("bkx_business_address_1", $_POST['business_address_1'],'update');
		crud_option_multisite("bkx_business_address_2", $_POST['business_address_2'],'update');
		crud_option_multisite("bkx_business_city", $_POST['business_city'],'update');
		crud_option_multisite("bkx_business_state", $_POST['business_state'],'update');
		crud_option_multisite("bkx_business_zip", $_POST['business_zip'],'update');
		crud_option_multisite("bkx_business_country", $_POST['business_country'],'update');

		$_SESSION['success']='Business Information updated successfully.';
	}

	if(isset($_POST['days_operation_flag']) && ($_POST['days_operation_flag']==1))
	{
			$biz_ph = array_unique($_POST['biz_ph']);
			$selected_days=array();
			for($d=1;$d <=7;$d++)
			{
				if(isset($_POST['days_'.$d]) && !empty($_POST['days_'.$d]))
					{
						$days_array =$_POST['days_'.$d];
						if(!empty($days_array))
						{
							$selected_days['selected_days_'.$d]['days'] =  $days_array;
						}						
					}
					if(isset($_POST['opening_time_'.$d]) && !empty($_POST['opening_time_'.$d]) && !empty($_POST['days_'.$d]))
					{
						$selected_days['selected_days_'.$d]['time']['open'] = $_POST['opening_time_'.$d];
					}
					if(isset($_POST['closing_time_'.$d]) && !empty($_POST['closing_time_'.$d]) && !empty($_POST['days_'.$d]))
					{
						$selected_days['selected_days_'.$d]['time']['close'] = $_POST['closing_time_'.$d];
					}
			}

		crud_option_multisite("bkx_business_days", $selected_days,'update');
		crud_option_multisite("bkx_biz_vac_sd", $_POST['bkx_biz_vac_sd'],'update');
		crud_option_multisite("bkx_biz_vac_ed", $_POST['bkx_biz_vac_ed'],'update');
		crud_option_multisite("bkx_biz_pub_holiday", $biz_ph,'update');

	}

	if(isset($_POST['tax_option_flag']) && ($_POST['tax_option_flag']==1))
	{
		if(is_numeric($_POST['bkx_tax_rate'])){
			crud_option_multisite("bkx_tax_rate", $_POST['bkx_tax_rate'],'update');
		}
		crud_option_multisite("bkx_tax_name", $_POST['bkx_tax_name'],'update');

		crud_option_multisite("bkx_prices_include_tax", $_POST['bkx_prices_include_tax'],'update');

		$_SESSION['success']='Tax settings updated successfully.';
	}
}
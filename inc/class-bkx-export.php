<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Description of BkxBase
 *
 * @author divyang
 */
class BkxExport {

	public $file_type ='xml'; //File Extention .xml

	public $export_type = array('all','seat','base','extra','booking','settings'); //Allowed export type

	public $file_size = 5; // In MB

	public $upload_dir = '';

	public $set_header = '';

	public $xmlobj = '';

	public $parent_root = '';

	var $errors = array();

	public function __construct($type = 'all') {

		$this->upload_dir = wp_upload_dir();
		$this->xmlobj = new DOMDocument();
		$this->parent_root = $this->xmlobj->appendChild($this->xmlobj->createElement("Root"));

		if(!in_array($type, $this->export_type)){
			$this->errors['type_error'] = "Oops.. , '$type' type does not exists.";
			return $this->errors;
		}
	}

	public function export_now(){
		if(empty($this->errors)):
			$seats_xml 		= $this->get_posts('bkx_seat','SeatPosts','Seat');
			$bases_xml 		= $this->get_posts('bkx_base','BasePosts','Base');
			$extras_xml 	= $this->get_posts('bkx_addition','ExtraPosts','Extra');
			$booking_xml 	= $this->get_posts('bkx_booking','BookingPosts','Booking');
			$settings_xml	= $this->get_settings();
			$this->generate_file();
		else:
			return $this->errors;
		endif;

	}

	public function generate_file(){

		$content = "";
		header('Content-type: text/plain');
		$this->xmlobj->formatOutput = true;

		$cur_date = date("Y-m-d_H:i:s");
		
		$content = $this->xmlobj->saveXML();

		$newfile_name = plugin_dir_path( __DIR__ )."uploads/newfile.xml";
		unlink($newfile_name);

		$file_handle = fopen($newfile_name, "w") or die("Please allow file permission to generate file on booking/uploads Directory.");
		fwrite($file_handle,$content);

		header('Content-Disposition: attachment; filename="bookingx-'.$cur_date.'.xml"');
		//read from server and write to buffer
		readfile($newfile_name);
	}

	public function get_settings(){

		$bkx_api_paypal_paypalmode = crud_option_multisite('bkx_api_paypal_paypalmode');
 		$bkx_api_paypal_username = crud_option_multisite('bkx_api_paypal_username');
		$bkx_api_paypal_password = crud_option_multisite('bkx_api_paypal_password'); 
		$bkx_api_paypal_signature = crud_option_multisite('bkx_api_paypal_signature');
		$bkx_api_google_map_key = crud_option_multisite('bkx_api_google_map_key');
		$enable_cancel_booking = crud_option_multisite('enable_cancel_booking');
		$enable_any_seat = crud_option_multisite('enable_any_seat');
		$cancellation_policy_page_id = crud_option_multisite('cancellation_policy_page_id');
		$currency_option  = crud_option_multisite('currency_option');
		$reg_customer_crud_op  = crud_option_multisite('reg_customer_crud_op');
		$bkx_seat_role = crud_option_multisite('bkx_seat_role');
		$bkx_set_booking_page = crud_option_multisite('bkx_set_booking_page');
		$bkx_alias_seat = crud_option_multisite('bkx_alias_seat');
		$bkx_alias_base = crud_option_multisite('bkx_alias_base');
		$bkx_alias_addition = crud_option_multisite('bkx_alias_addition');
		$bkx_alias_notification = crud_option_multisite('bkx_alias_notification');
		$bkx_client_id = crud_option_multisite('bkx_client_id');
		$bkx_client_secret = crud_option_multisite('bkx_client_secret');
		$bkx_redirect_uri = crud_option_multisite('bkx_redirect_uri');
		$bkx_google_calendar_id = crud_option_multisite('bkx_google_calendar_id');
		$bkx_tempalate_thankyou = crud_option_multisite("bkx_tempalate_thankyou");
		$bkx_tempalate_pending = crud_option_multisite("bkx_tempalate_pending");
		$bkx_tempalate_sucess = crud_option_multisite("bkx_tempalate_sucess");	
		$bkx_term_cond_page = crud_option_multisite("bkx_term_cond_page");
		$bkx_privacy_policy_page = crud_option_multisite("bkx_privacy_policy_page");
		$enable_editor = crud_option_multisite("enable_editor");
		$notice_time_extended_text_alias = crud_option_multisite("notice_time_extended_text_alias");
		$label_of_step1= crud_option_multisite("label_of_step1");
		$temp_option = crud_option_multisite('bkx_siteuser_canedit_seat');
		$can_edit_css = crud_option_multisite("bkx_siteclient_canedit_css");
		$bkx_siteclient_css_text_color = crud_option_multisite("bkx_siteclient_css_text_color");
		$bkx_siteclient_css_background_color = crud_option_multisite("bkx_siteclient_css_background_color");
		$bkx_siteclient_css_border_color = crud_option_multisite("bkx_siteclient_css_border_color");
		$bkx_siteclient_css_progressbar_color = crud_option_multisite("bkx_siteclient_css_progressbar_color");
		$bkx_siteclient_css_cal_border_color = crud_option_multisite("bkx_siteclient_css_cal_border_color");
		$bkx_siteclient_css_cal_day_color = crud_option_multisite("bkx_siteclient_css_cal_day_color");
		$bkx_siteclient_css_cal_day_selected_color = crud_option_multisite("bkx_siteclient_css_cal_day_selected_color");
		$time_available_color = crud_option_multisite("bkx_time_available_color");
		$time_selected_color = crud_option_multisite("bkx_time_selected_color");
		$time_unavailable_color = crud_option_multisite("bkx_time_unavailable_color");
		$time_block_bg_color = crud_option_multisite("bkx_time_block_bg_color");
		$time_block_extra_color = crud_option_multisite("time_block_extra_color");
		$time_block_service_color = crud_option_multisite("time_block_service_color");
		$time_block_extra_color = crud_option_multisite("bkx_time_block_extra_color");

		$bkx_time_block_service_color = crud_option_multisite("bkx_time_block_service_color");
		$bkx_cal_month_title_color = crud_option_multisite("bkx_cal_month_title_color");
		$bkx_cal_month_bg_color = crud_option_multisite("bkx_cal_month_bg_color");
		$bkx_time_new_selected = crud_option_multisite("bkx_time_new_selected");

		$bkx_tax_rate = crud_option_multisite("bkx_tax_rate");
		$bkx_tax_name = crud_option_multisite("bkx_tax_name");
		$bkx_prices_include_tax = crud_option_multisite("bkx_prices_include_tax");
		$bkx_business_name = crud_option_multisite("bkx_business_name");
		$bkx_business_email = crud_option_multisite("bkx_business_email");
		$bkx_business_phone = crud_option_multisite("bkx_business_phone");		
		$bkx_business_address_1 = crud_option_multisite("bkx_business_address_1");
		$bkx_business_address_2 = crud_option_multisite("bkx_business_address_2");
		$bkx_business_city = crud_option_multisite("bkx_business_city");
		$bkx_business_state = crud_option_multisite("bkx_business_state");
		$bkx_business_zip = crud_option_multisite("bkx_business_zip");
		$bkx_business_country = crud_option_multisite("bkx_business_country");
		$bkx_business_days = maybe_serialize(crud_option_multisite("bkx_business_days"));

		$settingTag = $this->parent_root->appendChild($this->xmlobj->createElement('Settings'));

		$settingTag->appendChild($this->xmlobj->createElement("bkx_google_calendar_id", $bkx_google_calendar_id));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_api_paypal_paypalmode", $bkx_api_paypal_paypalmode));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_api_paypal_username", $bkx_api_paypal_username));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_api_paypal_password", $bkx_api_paypal_password));		
		$settingTag->appendChild($this->xmlobj->createElement("bkx_api_paypal_signature", $bkx_api_paypal_signature));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_api_google_map_key", $bkx_api_google_map_key));
		$settingTag->appendChild($this->xmlobj->createElement("enable_cancel_booking", $enable_cancel_booking));
		$settingTag->appendChild($this->xmlobj->createElement("enable_any_seat", $enable_any_seat));
		$settingTag->appendChild($this->xmlobj->createElement("cancellation_policy_page_id", $cancellation_policy_page_id));
		$settingTag->appendChild($this->xmlobj->createElement("currency_option", $currency_option));
		$settingTag->appendChild($this->xmlobj->createElement("reg_customer_crud_op", $reg_customer_crud_op));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_seat_role", $bkx_seat_role));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_set_booking_page", $bkx_set_booking_page));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_alias_seat", $bkx_alias_seat));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_alias_base", $bkx_alias_base));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_alias_addition", $bkx_alias_addition));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_alias_notification", $bkx_alias_notification));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_client_id", $bkx_client_id));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_client_secret", $bkx_client_secret));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_tempalate_thankyou", $bkx_tempalate_thankyou));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_tempalate_pending", $bkx_tempalate_pending));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_tempalate_sucess", $bkx_tempalate_sucess));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_term_cond_page", $bkx_term_cond_page));		
		$settingTag->appendChild($this->xmlobj->createElement("bkx_privacy_policy_page", $bkx_privacy_policy_page));
		$settingTag->appendChild($this->xmlobj->createElement("enable_editor", $enable_editor));
		$settingTag->appendChild($this->xmlobj->createElement("notice_time_extended_text_alias", $notice_time_extended_text_alias));
		$settingTag->appendChild($this->xmlobj->createElement("label_of_step1", $label_of_step1));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteuser_canedit_seat", $bkx_siteuser_canedit_seat));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_canedit_css", $bkx_siteclient_canedit_css));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_text_color", $bkx_siteclient_css_text_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_background_color", $bkx_siteclient_css_background_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_border_color", $bkx_siteclient_css_border_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_progressbar_color", $bkx_siteclient_css_progressbar_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_cal_border_color", $bkx_siteclient_css_cal_border_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_cal_day_color", $bkx_siteclient_css_cal_day_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_css_cal_day_selected_color", $bkx_siteclient_css_cal_day_selected_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_available_color", $time_available_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_selected_color", $time_selected_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_unavailable_color", $time_unavailable_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_block_bg_color", $time_block_bg_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_block_extra_color", $time_block_extra_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_block_service_color", $time_block_service_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_block_service_color", $bkx_time_block_service_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_cal_month_title_color", $bkx_cal_month_title_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_cal_month_bg_color", $bkx_cal_month_bg_color));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_time_new_selected", $bkx_time_new_selected));

		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_name", $bkx_business_name));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_email", $bkx_business_email));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_phone", $bkx_business_phone));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_address_1", $bkx_business_address_1));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_address_2", $bkx_business_address_2));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_city", $bkx_business_city));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_state", $bkx_business_state));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_zip", $bkx_business_zip));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_country", $bkx_business_country));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_business_days", $bkx_business_days));

		$settingTag->appendChild($this->xmlobj->createElement("bkx_tax_rate", $bkx_tax_rate));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_tax_name", $bkx_tax_name));
		$settingTag->appendChild($this->xmlobj->createElement("bkx_prices_include_tax", $bkx_prices_include_tax));

	}


	public function get_posts($post_type = 'bkx_seat',$elements_name = 'SeatPosts',$element_name = 'Seat')
	{
		if($post_type == 'bkx_booking'){
			$post_status = array('bkx-pending','bkx-ack','bkx-completed','bkx-missed','bkx-cancelled','bkx-failed');
		}else{
			$post_status = 'publish';
		}
			$args = array('posts_per_page'=> -1,'post_type'=> $post_type,'post_status'=> $post_status);
			$get_posts = get_posts( $args );

			if(!empty($get_posts)){
				//create the root element
				$root = $this->parent_root->appendChild($this->xmlobj->createElement($elements_name));
				foreach ( $get_posts as $post ) {
					setup_postdata( $post );

				 	$PostTag = $root->appendChild($this->xmlobj->createElement($element_name));
					$seat_id = $post->ID;
					$metas = get_post_custom( $seat_id);

					$idAttr = $this->xmlobj->createAttribute('id');
					$idAttr->value = $seat_id;	

					$PostTag->appendChild($idAttr);
					$post_title = str_replace("&ndash;","-",get_the_title($seat_id));
					$PostTag->appendChild($this->xmlobj->createElement("Name", esc_html( $post_title )));
					$PostTag->appendChild($this->xmlobj->createElement("Description", get_the_content()));

					if($post_type == 'bkx_booking'){
						$PostTag->appendChild($this->xmlobj->createElement("post_status", $post->post_status));
						$PostTag->appendChild($this->xmlobj->createElement("comment_status", $post->comment_status));
						$PostTag->appendChild($this->xmlobj->createElement("ping_status", $post->ping_status));
						$PostTag->appendChild($this->xmlobj->createElement("post_date", $post->post_date));
						$PostTag->appendChild($this->xmlobj->createElement("post_date_gmt", $post->post_date_gmt));

				        $orderObj =  new BkxBooking();
				        $booking_note_data = $orderObj->get_booking_notes_for_export($post->ID);
				        $booking_note_data_json = json_encode($booking_note_data);

				        if(!empty($booking_note_data) && !is_wp_error($booking_note_data)){
				            $commentdata = $PostTag->appendChild($this->xmlobj->createElement("CommentData", $booking_note_data_json));
				            
				        }
					}

				    if(!empty($metas) && !is_wp_error($metas)){ 
				    	unset($metas['_edit_last']);
				    	unset($metas['_edit_lock']);			    
				    	$postmeta = $PostTag->appendChild($this->xmlobj->createElement("Postmeta"));
				    	foreach ($metas as $meta_key => $meta_value) {
				    		$postmeta_value = $meta_value[0];
				    		$postmeta->appendChild($this->xmlobj->createElement($meta_key, $postmeta_value));  
				    	}				 
					}
				}
			}
	}

}
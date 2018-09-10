<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Description of BkxBase
 *
 * @author divyang
 */

class BkxExport {

    /**
     * @var string
     */
    public $file_type ='xml'; //File Extention .xml

    /**
     * @var array
     */
    public $export_type = array('all','seat','base','extra','users','booking','settings'); //Allowed export type

    /**
     * @var int
     */
    public $file_size = 5; // In MB

    /**
     * @var array|string
     */
    public $upload_dir = '';

    /**
     * @var string
     */
    public $set_header = '';

    /**
     * @var DOMDocument|string
     */
    public $xmlobj = '';

    /**
     * @var DOMNode|string
     */
    public $parent_root = '';

    /**
     * @var array
     */
    var $errors = array();

    /**
     * BkxExport constructor.
     * @param string $type
     */
    public function __construct($type = 'all') {

		$this->upload_dir = wp_upload_dir();
		$this->xmlobj = new DOMDocument();
		$this->parent_root = $this->xmlobj->appendChild($this->xmlobj->createElement("Root"));

		if(!in_array($type, $this->export_type)){
			$this->errors['type_error'] = "Oops.. , '$type' type does not exists.";
			return $this->errors;
		}
	}

    /**
     * Generate Xml Build and Assign Value into this
     * @return array
     */
    public function export_now(){
		if(empty($this->errors)):
			$this->bkx_get_posts('bkx_seat','SeatPosts','Seat');
			$this->bkx_get_posts('bkx_base','BasePosts','Base');
			$this->bkx_get_posts('bkx_addition','ExtraPosts','Extra'); //ok
			$this->bkx_get_posts('bkx_booking','BookingPosts','Booking');
			$this->get_bkx_users();
			$this->get_settings();
			$this->generate_file();
		else:
			return $this->errors;
		endif;
	}

    /**
     * Generate File and Download Automatically
     */
    public function generate_file(){
		$this->xmlobj->formatOutput = true;
		$cur_date = date("Y-m-d_H:i:s");
		$content = $this->xmlobj->saveXML();
		$newfile_name = BKX_PLUGIN_DIR_PATH."uploads/newfile.xml";
		unlink($newfile_name);
		$file_handle = fopen($newfile_name, "w") or die("Please allow file permission to generate file on booking/uploads Directory.");
		fwrite($file_handle,$content);
        header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="bookingx-'.$cur_date.'.xml"');
		//read from server and write to buffer
		readfile($newfile_name);
	}

    /**
     *
     */
    public function get_settings(){

		$bkx_api_paypal_paypalmode = bkx_crud_option_multisite('bkx_api_paypal_paypalmode');
 		$bkx_api_paypal_username = bkx_crud_option_multisite('bkx_api_paypal_username');
		$bkx_api_paypal_password = bkx_crud_option_multisite('bkx_api_paypal_password'); 
		$bkx_api_paypal_signature = bkx_crud_option_multisite('bkx_api_paypal_signature');
		$bkx_api_google_map_key = bkx_crud_option_multisite('bkx_api_google_map_key');
		$enable_cancel_booking = bkx_crud_option_multisite('enable_cancel_booking');
		$enable_any_seat = bkx_crud_option_multisite('enable_any_seat');
		$cancellation_policy_page_id = bkx_crud_option_multisite('cancellation_policy_page_id');
		$currency_option  = bkx_crud_option_multisite('currency_option');
		$reg_customer_crud_op  = bkx_crud_option_multisite('reg_customer_crud_op');
		$bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
		$bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
		$bkx_alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
		$bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
		$bkx_alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
		$bkx_alias_notification = bkx_crud_option_multisite('bkx_alias_notification');
		$bkx_client_id = bkx_crud_option_multisite('bkx_client_id');
		$bkx_client_secret = bkx_crud_option_multisite('bkx_client_secret');
		$bkx_redirect_uri = bkx_crud_option_multisite('bkx_redirect_uri');
		$bkx_google_calendar_id = bkx_crud_option_multisite('bkx_google_calendar_id');
		$bkx_tempalate_thankyou = bkx_crud_option_multisite("bkx_tempalate_thankyou");
		$bkx_tempalate_pending = bkx_crud_option_multisite("bkx_tempalate_pending");
		$bkx_tempalate_sucess = bkx_crud_option_multisite("bkx_tempalate_sucess");	
		$bkx_term_cond_page = bkx_crud_option_multisite("bkx_term_cond_page");
		$bkx_privacy_policy_page = bkx_crud_option_multisite("bkx_privacy_policy_page");
		$enable_editor = bkx_crud_option_multisite("enable_editor");
		$notice_time_extended_text_alias = bkx_crud_option_multisite("notice_time_extended_text_alias");
		$label_of_step1= bkx_crud_option_multisite("label_of_step1");
		$temp_option = bkx_crud_option_multisite('bkx_siteuser_canedit_seat');
		$can_edit_css = bkx_crud_option_multisite("bkx_siteclient_canedit_css");
		$bkx_siteclient_css_text_color = bkx_crud_option_multisite("bkx_siteclient_css_text_color");
		$bkx_siteclient_css_background_color = bkx_crud_option_multisite("bkx_siteclient_css_background_color");
		$bkx_siteclient_css_border_color = bkx_crud_option_multisite("bkx_siteclient_css_border_color");
		$bkx_siteclient_css_progressbar_color = bkx_crud_option_multisite("bkx_siteclient_css_progressbar_color");
		$bkx_siteclient_css_cal_border_color = bkx_crud_option_multisite("bkx_siteclient_css_cal_border_color");
		$bkx_siteclient_css_cal_day_color = bkx_crud_option_multisite("bkx_siteclient_css_cal_day_color");
		$bkx_siteclient_css_cal_day_selected_color = bkx_crud_option_multisite("bkx_siteclient_css_cal_day_selected_color");
		$time_available_color = bkx_crud_option_multisite("bkx_time_available_color");
		$time_selected_color = bkx_crud_option_multisite("bkx_time_selected_color");
		$time_unavailable_color = bkx_crud_option_multisite("bkx_time_unavailable_color");
		$time_block_bg_color = bkx_crud_option_multisite("bkx_time_block_bg_color");
		$time_block_extra_color = bkx_crud_option_multisite("time_block_extra_color");
		$time_block_service_color = bkx_crud_option_multisite("time_block_service_color");
		$time_block_extra_color = bkx_crud_option_multisite("bkx_time_block_extra_color");

		$bkx_time_block_service_color = bkx_crud_option_multisite("bkx_time_block_service_color");
		$bkx_cal_month_title_color = bkx_crud_option_multisite("bkx_cal_month_title_color");
		$bkx_cal_month_bg_color = bkx_crud_option_multisite("bkx_cal_month_bg_color");
		$bkx_time_new_selected = bkx_crud_option_multisite("bkx_time_new_selected");

		$bkx_tax_rate = bkx_crud_option_multisite("bkx_tax_rate");
		$bkx_tax_name = bkx_crud_option_multisite("bkx_tax_name");
		$bkx_prices_include_tax = bkx_crud_option_multisite("bkx_prices_include_tax");
		$bkx_business_name = bkx_crud_option_multisite("bkx_business_name");
		$bkx_business_email = bkx_crud_option_multisite("bkx_business_email");
		$bkx_business_phone = bkx_crud_option_multisite("bkx_business_phone");		
		$bkx_business_address_1 = bkx_crud_option_multisite("bkx_business_address_1");
		$bkx_business_address_2 = bkx_crud_option_multisite("bkx_business_address_2");
		$bkx_business_city = bkx_crud_option_multisite("bkx_business_city");
		$bkx_business_state = bkx_crud_option_multisite("bkx_business_state");
		$bkx_business_zip = bkx_crud_option_multisite("bkx_business_zip");
		$bkx_business_country = bkx_crud_option_multisite("bkx_business_country");
		$bkx_business_days = maybe_serialize(bkx_crud_option_multisite("bkx_business_days"));

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
		//$settingTag->appendChild($this->xmlobj->createElement("bkx_siteuser_canedit_seat", $bkx_siteuser_canedit_seat));
		//$settingTag->appendChild($this->xmlobj->createElement("bkx_siteclient_canedit_css", $bkx_siteclient_canedit_css));
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


	public function get_bkx_users(){

		$args = array(
			'meta_key'     => 'seat_post_id',
			'orderby'      => 'ID',
			'order'        => 'ASC',
			'fields'       => 'all',
		 ); 
		$get_users_obj = get_users( $args );
		$BkxUsersTag = $this->parent_root->appendChild($this->xmlobj->createElement('BkxUsers'));
		if(!empty($get_users_obj) && !is_wp_error($get_users_obj)){
			foreach ($get_users_obj as $key => $user_obj) {
				$UserPostTag = $BkxUsersTag->appendChild($this->xmlobj->createElement('BkxUsers'));
				$user_data_obj = $user_obj->data;
				$user_caps_obj = $user_obj->caps;
				$user_roles_obj = $user_obj->roles;
 
				$user_meta_data = get_user_meta($user_obj->ID);
				$get_post_seat_id = get_user_meta( $user_obj->ID, 'seat_post_id', true);
				$seat_data = get_post($get_post_seat_id); 
				$seat_slug = $seat_data->post_name;
				
				$user_attr = $this->xmlobj->createAttribute('id');
				$user_attr->value = $user_obj->ID;
				$UserPostTag->appendChild($user_attr);

				if(!empty($user_data_obj)){
					$UserDataTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserData"));
					foreach ($user_data_obj as $user_data_key => $user_data_val) {
						$UserDataTag->appendChild($this->xmlobj->createElement( $user_data_key, esc_html( $user_data_val )));
					}
				}

				if(!empty($user_caps_obj)){
					$UserCapsTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserCaps"));
					foreach ($user_caps_obj as $user_caps_key => $user_caps_val) {
						$UserCapsTag->appendChild($this->xmlobj->createElement( $user_caps_key, esc_html( $user_caps_val )));
					}
					$UserCapsTag->appendChild($this->xmlobj->createElement( 'cap_key', esc_html( $user_obj->cap_key )));
				}

				if(!empty($user_roles_obj)){
					$UserRoleTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserRoles"));
					foreach ($user_roles_obj as $user_roles_key => $user_roles_val) {
						$UserRoleTag->appendChild($this->xmlobj->createElement( 'roles', esc_html( $user_roles_val )));
					}
				}
 
				if(!empty($user_meta_data)){
					$UserMetaTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserMetaData"));
					foreach ($user_meta_data as $user_meta_key => $user_meta_val) {
						$UserMetaTag->appendChild($this->xmlobj->createElement( $user_meta_key, esc_html( $user_meta_val[0] )));
					}
					$UserMetaTag->appendChild($this->xmlobj->createElement( 'bkx_seat_slug', $seat_slug) );
				}
			}
		}
	}


    /**
     * @param string $post_type
     * @param string $elements_name
     * @param string $element_name
     */
    public function bkx_get_posts($post_type = 'bkx_seat', $elements_name = 'SeatPosts', $element_name = 'Seat')
	{
            global $wpdb, $wp_query;

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
					$metas = get_post_custom( $post->ID);
					$idAttr = $this->xmlobj->createAttribute('id');
					$idAttr->value = $post->ID;

					$PostTag->appendChild($idAttr);
					$post_title = str_replace("&ndash;","-",get_the_title($post->ID));
					$PostTag->appendChild($this->xmlobj->createElement("Name", esc_html( $post_title )));
					$PostTag->appendChild($this->xmlobj->createElement("Description", $post->post_content));

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
				           $PostTag->appendChild($this->xmlobj->createElement("CommentData", $booking_note_data_json));
				        }
					}
				    if(!empty($metas) && !is_wp_error($metas)){ 
				    	unset($metas['_edit_last']);
				    	unset($metas['_edit_lock']);			    
				    	$postmeta = $PostTag->appendChild($this->xmlobj->createElement("Postmeta"));

				    	foreach ($metas as $meta_key => $meta_value) {

				    		if($post_type == 'bkx_booking')
				    		{															
				    			delete_post_meta('order_seat_slug');
				    			delete_post_meta('order_base_slug');
				    			delete_post_meta('order_addition_slugs');
			    				if($meta_key == 'seat_id'){
					    			$seat_id = $meta_value[0];
									$seat_data = get_post($seat_id); 
									$seat_slug = $seat_data->post_name;
				 	  			} 
			 	  				if($meta_key == 'base_id'){
			 	  					$base_id = $meta_value[0];
									$base_data = get_post($base_id); 
									$base_slug = $base_data->post_name;
			 	  				}
			 	  				if($meta_key == 'addition_ids'){
			 	  					$addition_ids = explode(",", $meta_value[0]);
			 	  					if(!empty($addition_ids)){
			 	  						foreach ($addition_ids as $key => $addition_id) {
			 	  							$addition_data = get_post($addition_id); 
											$addition_slug[] = $addition_data->post_name;
			 	  						}
			 	  					}
			 	  				}
				    		}
				    		if($meta_key == 'order_meta_data'){
		 	  					$postmeta_data = maybe_unserialize($meta_value[0]);
		 	  					unset($postmeta_data['currency']);
		 	  					$postmeta_value = maybe_serialize($postmeta_data);
		 	  				}else{
		 	  					$postmeta_value = $meta_value[0];
		 	  				}
				    		$postmeta->appendChild($this->xmlobj->createElement($meta_key, $postmeta_value));  
				    	}
				    	$postmeta->appendChild($this->xmlobj->createElement('order_seat_slug', $seat_slug)); 
				    	$postmeta->appendChild($this->xmlobj->createElement('order_base_slug', $base_slug));
				    	$postmeta->appendChild($this->xmlobj->createElement('order_addition_slugs', implode(",", $addition_slug)));		 
					}
				}
			}
	}

}
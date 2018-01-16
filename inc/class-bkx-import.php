<?php
/**
* Bookingx Import tool real xml file only supported which export by Bookingx.
*/
class BkxImport
{
	public $file_type ='xml'; //File Extention .xml

	public $import_type = array('all','seat','base','extra','booking','settings'); //Allowed export type

	public $max_file_size = 5; // In MB

	public $upload_dir = '';

	public $target_file = '';

    var $errors = array();


	function __construct($type = 'all')
	{

		$this->upload_dir = plugin_dir_path( __DIR__ ).'uploads/importXML';
		$this->errors = $this->errors['errors'];



		// if(!is_dir($this->upload_dir)):
		// 	mkdir($this->upload_dir, 0777);
		// else:
		// 	chmod($this->upload_dir, 0777);
		// endif;

		if(!in_array($type, $this->import_type)){
			$this->errors['type_error'] = "Oops.. , '$type' type does not exists.";
			return $this->errors;
		}
	}

	function import_now( $fileobj = null , $post_data = null)
	{

		$truncate_records = $post_data['truncate_records'];
		$file_data =  $this->check_file_is_ok($fileobj);

		if(!empty($this->errors))
			return $this->errors;

		$truncate_flag = $this->truncate_old_data($truncate_records);
		if(!empty($file_data)){
			$loadxml = new SimpleXMLElement($file_data);
				if(isset($loadxml) && COUNT($loadxml) > 0)
				{
					$SeatPosts 		= $loadxml->SeatPosts;
					$BasePosts 		= $loadxml->BasePosts;
					$ExtraPosts 	= $loadxml->ExtraPosts;
					$BookingPosts 	= $loadxml->BookingPosts;
					$Settings 		= $loadxml->Settings;

					$bkx_seat 		= $this->generate_post( $SeatPosts, 'bkx_seat' );
					$bkx_base 		= $this->generate_post( $BasePosts, 'bkx_base' );
					$bkx_addition 	= $this->generate_post( $ExtraPosts, 'bkx_addition' );
					$generate_setting = $this->generate_setting($Settings);
					$bkx_booking 	= $this->generate_post( $BookingPosts, 'bkx_booking' );

					unlink($this->target_file);
					$_SESSION['success'] =  'File imported successfully..';
					wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
					exit;
				}
		}	
	}

	function generate_setting($xml_data = null)
	{
		if(!empty($xml_data)){

			foreach ($xml_data as $value) {
			  	foreach ($value as $key => $setting) {
			  		$setting = maybe_unserialize(reset($setting));
			  		crud_option_multisite($key, $setting, 'update');
				}
			}
		}

	}

	function generate_post( $xml_data = null, $type = null )
	{
		$created_by = get_current_user_id();

		if(!empty($xml_data) && !empty($type)){

			 foreach ($xml_data as $data) {

			 	if(!empty($data)) {

			 		foreach ($data as $posts) {

			 	  		$post_name = $posts->Name;
			 	  		$description = $posts->Description;

			 	  		//Create Post
						$import_post = array(
							  'post_title'    => wp_strip_all_tags( $post_name ),
							  'post_content'  => $description,
							  'post_status'   => 'publish',
							  'post_type'	  => $type
							);

			 	  		if($type == 'bkx_booking')
			 	  		{
							$import_post['post_status'] 	=  	$posts->post_status;
							$import_post['comment_status'] 	=  	$posts->comment_status;
							$import_post['ping_status'] 	= 	$posts->ping_status;
							$import_post['post_date'] 		= 	$posts->post_date;
							$import_post['post_date_gmt'] 	=  	$posts->post_date_gmt;
						}
 
						// Insert the post into the database
						$post_id = wp_insert_post( $import_post );

						// Generate Post Meta
			 	  		$postmetaObj = $posts->Postmeta;

			 	  		if(!empty($postmetaObj) && !empty($post_id)){
			 	  		//if(!empty($postmetaObj)){
			 	  			foreach ($postmetaObj as $postmeta_arr) {

		 	  					if(!empty($postmeta_arr))
		 	  					{
		 	  						//print_r($postmeta_arr);
		 	  						foreach ($postmeta_arr as $key => $postmeta) {	 
		 	  							//echo $key."==".reset($postmeta)."<br>";
		 	  							$postmeta_data = maybe_unserialize(reset($postmeta));
		 	  							update_post_meta( $post_id, $key, $postmeta_data);		  
				 	  				}
		 	  					}
				 	  		}
			 	  		}			
			 		}

			 	}
			 	  	
			 }
		}
	}

	function check_file_is_ok($fileobj)
	{
		if(isset($fileobj["import_file"]) && $fileobj["import_file"]["size"] > 0) :
				
				if($fileobj["import_file"]["size"] > 10048576) :
						$this->errors['file_size_not_in_range'] = "Upload file size should be upto 5 MB.";
				else:
					$this->target_file = $this->upload_dir . basename($fileobj["import_file"]["name"]);					
					$filetype = pathinfo($this->target_file,PATHINFO_EXTENSION);
					if(isset($filetype) && $filetype == "xml"):
						if(move_uploaded_file($fileobj['import_file']['tmp_name'], "$this->target_file")):
							chmod($this->target_file,777);
							$file_data = file_get_contents($this->target_file);
							return $file_data;
						endif;
					else:
						$this->errors['file_typo'] = "File type not supported, upload only xml file.";
					endif;
				endif;
		else:
			$this->errors['file_empty'] = "File does not exists.";
		endif;

		return $this->errors;
	}

	function truncate_old_data($flag = null)
	{	
		$truncate_flag = 0;

		if(isset($flag) && $flag == 'on'){

			global $wpdb;

			// Delete all Posts in posts table.
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_seat' ) );
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_base' ) );
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_addition' ) );
		    //$wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_booking' ) );

		    // Delete all postmeta which not exists in posts table.
		     $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->postmeta.'  
		    	WHERE '.$wpdb->postmeta.'.post_id NOT IN ( SELECT '.$wpdb->posts.'.ID FROM '.$wpdb->posts.' )','') );

		     //print_r($this->print_error());

		     //Delete all setting
		     $setting_array = array('bkx_google_calendar_id','bkx_api_paypal_paypalmode','bkx_api_paypal_username','bkx_api_paypal_password','bkx_api_paypal_signature','bkx_api_google_map_key','enable_cancel_booking','enable_any_seat','cancellation_policy_page_id','currency_option','reg_customer_crud_op','bkx_seat_role','bkx_set_booking_page','bkx_alias_seat','bkx_alias_base','bkx_alias_addition','bkx_alias_notification','bkx_client_id','bkx_client_secret','bkx_google_calendar_id','bkx_tempalate_thankyou','bkx_tempalate_pending','bkx_tempalate_sucess','bkx_term_cond_page','bkx_privacy_policy_page','enable_editor','notice_time_extended_text_alias','label_of_step1','bkx_siteuser_canedit_seat','bkx_siteclient_canedit_css','bkx_siteclient_css_text_color','bkx_siteclient_css_background_color','bkx_siteclient_css_border_color','bkx_siteclient_css_progressbar_color','bkx_siteclient_css_cal_border_color','bkx_siteclient_css_cal_day_color','bkx_siteclient_css_cal_day_selected_color','time_available_color','time_selected_color','time_unavailable_color','time_block_bg_color','time_block_extra_color','time_block_service_color','bkx_business_name','bkx_business_email','bkx_business_phone','bkx_business_address_1','bkx_business_address_2','bkx_business_city','bkx_business_state','bkx_business_zip','bkx_business_country','bkx_business_days');

		    $setting_array =  apply_filters( 'bkx_setting_options_array', $setting_array, $this );

		    //delete previous setting options value from database
		     if(!empty($setting_array)){
		     	foreach ($setting_array as $opt_key) {
		     		 delete_option($opt_key);
		     	}
		     }
		    $truncate_flag = 1; // Success
		}

		return $truncate_flag;
	}

	function print_error(){

	    global $wpdb;

	    if($wpdb->last_error !== '') :

	        $str   = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
	        $query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );

	        print "<div id='error'>
	        <p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
	        <code>$query</code></p>
	        </div>";

	    endif;

	}
}
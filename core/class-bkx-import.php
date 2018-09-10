<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
* Bookingx Import tool real xml file only supported which export by Bookingx.
*/
class BkxImport
{
    /**
     * @var string
     */
    public $file_type ='xml'; //File Extention .xml

    /**
     * @var array
     */
    public $import_type = array('all','seat','base','extra','users','booking','settings'); //Allowed export type

    /**
     * @var int
     */
    public $max_file_size = 5; // In MB

    /**
     * @var string
     */
    public $upload_dir = '';

    /**
     * @var string
     */
    public $target_file = '';

    /**
     * @var array
     */
    var $errors = array();


    /**
     * BkxImport constructor.
     * @param string $type
     */
    function __construct($type = 'all')
	{

		$this->upload_dir = BKX_PLUGIN_DIR_PATH.'uploads/importXML';
		$this->errors = $this->errors['errors'];
		if(!in_array($type, $this->import_type)){
			$this->errors['type_error'] = "Oops.. , '$type' type does not exists.";
			return $this->errors;
		}
	}

    /**
     * @param null $fileobj
     * @param null $post_data
     * @return array
     */
    function import_now($fileobj = null , $post_data = null)
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
					$BkxUsers 		= $loadxml->BkxUsers;
					$Settings 		= $loadxml->Settings;

					$bkx_seat 		= $this->generate_post( $SeatPosts, 'bkx_seat' );
					$bkx_base 		= $this->generate_post( $BasePosts, 'bkx_base' );
					$bkx_addition 	= $this->generate_post( $ExtraPosts, 'bkx_addition' );
					$generate_setting = $this->generate_setting($Settings);
					$bkx_booking 	= $this->generate_post( $BookingPosts, 'bkx_booking' );
					$bkx_users 		= $this->generate_bkx_users( $BkxUsers );

					unlink($this->target_file);
					$_SESSION['bkx_success'] =  'File imported successfully..';
					wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
					exit;
				}
		}	
	}

    /**
     * @param null $xml_data
     */
    function generate_setting($xml_data = null)
	{
		if(!empty($xml_data)){

			foreach ($xml_data as $value) {
			  	foreach ($value as $key => $setting) {
			  		$setting = maybe_unserialize(reset($setting));
			  		bkx_crud_option_multisite($key, $setting, 'update');
				}
			}
		}
	}

	function generate_bkx_users( $xml_data = null ){
		global $wpdb;
		if(!empty($xml_data)){
			foreach ($xml_data as $value) {
			  	foreach ($value as $key => $user_obj) {
			  		$create_user_data = $create_user_meta = array();
			  		$UserDataObj = $user_obj->UserData;
			  		$UserCapsObj = $user_obj->UserCaps;
			  		$UserRole = reset($user_obj->UserRoles);
			  		$UserMetaDataObj = $user_obj->UserMetaData;
			  		if(!empty($UserDataObj)){
			  			foreach ($UserDataObj as $key => $UserData) {
			  				 foreach ($UserData as $key => $UserDataVal) {
			  				 	$create_user_data[$key] = reset($UserDataVal);
			  				}
			  			}
			  		}
			  		$posts = $wpdb->get_results("SELECT * FROM $wpdb->postmeta 
			  									 WHERE meta_key = 'seatEmail'
			  									 AND meta_value =  '".$create_user_data['user_email']."'
			  									 LIMIT 1", ARRAY_A);
			  		$post_obj = reset($posts);
			  		 
			  		if ( isset($create_user_data['user_login']) && username_exists( $create_user_data['user_login'] ) ){
				        $user_id = username_exists( $create_user_data['user_login'] );

				        if ( is_multisite() ) {
				        	if(is_user_member_of_blog($user_id) == false){
				        		add_user_to_blog( get_current_blog_id(), $user_id, $UserRole);
				        	}

				        	$userobj = get_user_by('login', $create_user_data['user_login']);

				        	if(!empty($userobj) && !is_wp_error($userobj)){
				        		$user_id = $userobj->ID;
				        	}
				        }

				    }else{
				        $user_id = wp_insert_user( $create_user_data ) ;
				    }
				     
				    if(isset($user_id) && $user_id!=""){

				    	if(!empty($UserMetaDataObj)){
				  			foreach ($UserMetaDataObj as $key => $UserMetaData) {
				  				foreach ($UserMetaData as $key => $UserMetaDataVal) {
				  					$meta_value = reset($UserMetaDataVal);
				  					update_user_meta( $user_id, $key, $meta_value );
				  				}
				  			}
				  		}
				  		if(!empty($post_obj) && isset($post_obj->post_id) && $post_obj->post_id != ""){
				  			update_user_meta($user_id , 'seat_post_id', $post_obj->post_id);
        					update_post_meta($post_obj->post_id , 'seat_user_id', $user_id);
				  		}

				    	$user = get_userdata( $user_id );
					    if( $user && $user->exists() ) {
						  // Add a new role to the user, while retaining the previous ones
						  $user->add_role( $UserRole );
						}
				    }
				}
			}
		}
	}

    /**
     * @param null $xml_data
     * @param null $type
     */
    function generate_post($xml_data = null, $type = null )
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
						$post_data = get_post($post_id); 
						$post_data_slug = $post_data->post_name;
						update_post_meta( $post_id, "{$type}_slug", $post_data_slug);

						// Generate Post Meta
			 	  		$postmetaObj = $posts->Postmeta;

			 	  		// Generate Comment 
						if($type == 'bkx_booking' && !empty($posts->CommentData))
			 	  		{
				 	  		$commentObj = json_decode($posts->CommentData);
				 	  		$BkxBookingObj = new BkxBooking('',$post_id);
				 	  		if(!empty($commentObj) && !empty($post_id)){
				 	  			foreach ($commentObj as $key => $comment_arr) {				 	  				
				 	  				$BkxBookingObj->add_order_note( $comment_arr->comment_content , 0, $manual );
					 	  		}
				 	  		}
			 	  		}

			 	  		if(!empty($postmetaObj) && !empty($post_id)){

			 	  			foreach ($postmetaObj as $postmeta_arr) {
		 	  					if(!empty($postmeta_arr))
		 	  					{
		 	  						foreach ($postmeta_arr as $key => $postmeta) {
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

    /**
     * @param $fileobj
     * @return array|bool|string
     */
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

    /**
     * @param null $flag
     * @return int
     */
    function truncate_old_data($flag = null)
	{	
		$truncate_flag = 0;

		if(isset($flag) && $flag == 'on'){

			global $wpdb;

			// Delete all Posts in posts table.
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_seat' ) );
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_base' ) );
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_addition' ) );
		    $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->posts.' WHERE '.$wpdb->posts.'.post_type = %s', 'bkx_booking' ) );

		    // Delete all postmeta which not exists in posts table.
		     $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->postmeta.'  
		    	WHERE '.$wpdb->postmeta.'.post_id NOT IN ( SELECT '.$wpdb->posts.'.ID FROM '.$wpdb->posts.' )','') );

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

    /**
     *
     */
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
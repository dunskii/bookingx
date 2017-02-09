<script>
function generate_xml()
{
	//alert("xml generation");
	var values = new Array();
	jQuery.each(jQuery("input[name='post[]']:checked"), function() {
	  values.push(jQuery(this).val());
	});
	
	//console.log(values);
	jQuery("#id_addition_list").val(values);
	document.forms["xml_export"].submit();
}
</script>
<?php
// session_start();
require_once('booking_general_functions.php');
global $wpdb;

//start calendar service
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
$client        = new Google_Client();
$client_id     = trim(crud_option_multisite('bkx_client_id'));
$client_secret = trim(crud_option_multisite('bkx_client_secret'));
//$client_secret = trim(crud_option_multisite('bkx_redirect_uri'));


function curPageURL() {
	$pageURL = 'http';

	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

$currPageUrl =  trim(crud_option_multisite('bkx_redirect_uri'));
$curr = curPageURL();
$client->setApplicationName("Google Calendar PHP Starter Application");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);

$client->setRedirectUri($currPageUrl);
 
$cal = new Google_CalendarService($client);


if (isset($_GET['logout'])) {
	unset($_SESSION['token']);
}

if (isset($_GET['code'])) { 
	$res = $client->authenticate($_GET['code']);

	$_SESSION['token'] = $client->getAccessToken();
	$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	echo '<META HTTP-EQUIV="Refresh" Content="2; URL='.$client->getRedirectUri().'">';    
	echo '<div class="updated"><p><strong>Connected to Google API</strong></p></div>';
	//header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

//end calendar service

$objListTemplateThankyou=$wpdb->get_results('SELECT * FROM bkx_booking_template WHERE template_name="thankyou"');

$objListTemplateEmail=$wpdb->get_results('SELECT * FROM bkx_booking_template WHERE template_name="emailconfirmation"');
/* Updated By :Madhuri Rokade
		   Reason : To get the success message from the template.
		*/ 
$objListTemplateSuccess=$wpdb->get_results('SELECT * FROM bkx_booking_template WHERE template_name="successconfirmation"');

$temp_option = crud_option_multisite('bkx_siteuser_canedit_seat');
$temp_css = crud_option_multisite('bkx_siteclient_canedit_css');

if(!empty($_POST))
{

	if(isset($_POST['connect_google_api_flag']) && ($_POST['connect_google_api_flag']==1))
	{
		if ($client->getAccessToken()) {
		  $calList = $cal->calendarList->listCalendarList();
			
			$query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_seat ON bkx_seat.seat_id = bkx_booking_record.seat_id';
			$objBooking = $wpdb->get_results($query);
			$strTableName = "bkx_booking_record";
			$addedFlag = 0;
			foreach($objBooking as $temp)
			{
			if($temp->addedtocalendar==0)
				{
					//echo "Need to be entered";
					$event = new Google_Event();
					$event->setSummary($temp->first_name." ".$temp->last_name.",".$temp->phone.",".$temp->email);
					$event->setLocation($temp->seat_city.",".$temp->seat_state.",".$temp->seat_country );
					$start = new Google_EventDateTime();
					$startDate = date(DATE_RFC3339, strtotime($temp->booking_start_date));
					$endDate = date(DATE_RFC3339, strtotime($temp->booking_end_date));
					//$startDate = date(DATE_RFC3339, strtotime("06/26/2013 01:45"));
					//$endDate = date(DATE_RFC3339, strtotime("06/27/2013 12:55"));
					$start->setDateTime($startDate);
					$event->setStart($start);
					$end = new Google_EventDateTime();
					$end->setDateTime($endDate);
					$event->setEnd($end);
					
					$event->attendees = $attendees;
					$calender_id_google = crud_option_multisite('bkx_google_calendar_id', "primary");
					if($calender_id_google=="")
					{
						$calender_id_google = "primary";
					}
					try{
					$createdEvent = $cal->events->insert($calender_id_google, $event);
					//update the entry in database
					$arrTabledata = array('addedtocalendar'=>1);
					$where = array('booking_record_id' => $temp->booking_record_id );
					$wpdb->update( $strTableName, $arrTabledata, $where);
					$addedFlag = $addedFlag + 1;
					}
					catch(Exception $e){
						echo '<div class="error"><p><strong>Caught exception: '.  $e->getMessage(). '\n;</strong></p></div>';
					}
				}

			}
		if($addedFlag > 0)
			{
				echo '<div class="updated"><p><strong>'.$addedFlag .' bookings Added to Google Calendar</strong></p></div>';
			}


		$_SESSION['token'] = $client->getAccessToken();
		} else {
		  $authUrl = $client->createAuthUrl();
		   echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$authUrl.'">';    
		   exit;    
		}
	}

if(isset($_POST['connect_google_api_flag_seat']) && ($_POST['connect_google_api_flag_seat']==1))
	{
		if($_POST['seat_id']=="")
		{
			echo '<div class="error"><p><strong>First select a seat</strong></p></div>';
		}
		if ($client->getAccessToken()) {
		  $calList = $cal->calendarList->listCalendarList();		 
			
			$query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_seat ON bkx_seat.seat_id = bkx_booking_record.seat_id where bkx_booking_record.seat_id ='.trim($_POST['seat_id']);
			$objBooking = $wpdb->get_results($query);
			$strTableName = "bkx_booking_record";
			$addedFlag = 0;
			foreach($objBooking as $temp)
			{
			if($temp->addedtocalendarseat==0)
				{
					//echo "Need to be entered";
					$event = new Google_Event();
					$event->setSummary($temp->first_name." ".$temp->last_name.",".$temp->phone.",".$temp->email);
					$event->setLocation($temp->seat_city.",".$temp->seat_state.",".$temp->seat_country );
					$start = new Google_EventDateTime();
					$startDate = date(DATE_RFC3339, strtotime($temp->booking_start_date));
					$endDate = date(DATE_RFC3339, strtotime($temp->booking_end_date));
					//$startDate = date(DATE_RFC3339, strtotime("06/26/2013 01:45"));
					//$endDate = date(DATE_RFC3339, strtotime("06/27/2013 12:55"));
					$start->setDateTime($startDate);
					$event->setStart($start);
					$end = new Google_EventDateTime();
					$end->setDateTime($endDate);
					$event->setEnd($end);
					
					$event->attendees = $attendees;
					//$calender_id_google = crud_option_multisite('bkx_google_calendar_id', "primary");
					$calender_id_google = $temp->seat_ical_address;
					if($calender_id_google=="")
					{
						$calender_id_google = "primary";
					}
					try{
						$createdEvent = $cal->events->insert($calender_id_google, $event);
						//update the entry in database
						$arrTabledata = array('addedtocalendarseat'=>1);
						$where = array('booking_record_id' => $temp->booking_record_id );
						$wpdb->update( $strTableName, $arrTabledata, $where);
						$addedFlag = $addedFlag + 1;
					}
					catch(Exception $e){
						echo '<div class="error"><p><strong>Caught exception: '.  $e->getMessage(). '\n;</strong></p></div>';
					}
				}

			}
		if($addedFlag > 0)
			{
				echo '<div class="updated"><p><strong>'.$addedFlag .' bookings Added to Google Calendar for the seat</strong></p></div>';
			}


		$_SESSION['token'] = $client->getAccessToken();
		} else {
		  $authUrl = $client->createAuthUrl();
		   echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$authUrl.'">';    
		   exit;    
		  //print "<a class='login' href='$authUrl'>Connect Me!</a>";
		}
	}

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
		$currency_option  = crud_option_multisite('currency_option');
		$reg_customer_crud_op  = crud_option_multisite('reg_customer_crud_op');

		

		crud_option_multisite("enable_cancel_booking", $_POST['enable_cancel_booking'], 'update');
		crud_option_multisite("cancellation_policy_page_id", $_POST['page_id'],'update');
		crud_option_multisite("enable_any_seat", $_POST['enable_any_seat'],'update');		
		crud_option_multisite("currency_option", $_POST['currency_option'],'update');
		crud_option_multisite("reg_customer_crud_op", $_POST['reg_customer_crud_op'],'update');	

		$_SESSION['success']='Other Setting updated successfully.';
	}
        
    if(isset($_POST['role_setting_flag']) && ($_POST['role_setting_flag']==1))
	{
		$bkx_seat_role = crud_option_multisite('bkx_seat_role');
		crud_option_multisite("bkx_seat_role", $_POST['bkx_seat_role'],'update');

		$_SESSION['success']='Role Assignment Setting updated successfully.';
	}
    
    if(isset($_POST['page_setting_flag']) && ($_POST['page_setting_flag']==1))
	{
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
                
		$_SESSION['success']='Page Setting updated successfully.';
	}
        
        

	if(isset($_POST['alias_flag']) && ($_POST['alias_flag']==1))
	{
		$temp_seat = crud_option_multisite('bkx_alias_seat');
		crud_option_multisite("bkx_alias_seat", $_POST['seat_alias'],'update');

		$temp_base = crud_option_multisite('bkx_alias_base');
		crud_option_multisite("bkx_alias_base", $_POST['base_alias'],'update');
		
		$temp_addition = crud_option_multisite('bkx_alias_addition');
		crud_option_multisite("bkx_alias_addition", $_POST['addition_alias'],'update');

		$temp_notification = crud_option_multisite('bkx_alias_notification');
		crud_option_multisite("bkx_alias_notification", $_POST['notification_alias'],'update');

		$_SESSION['success']='Alias updated successfully.';
	}


	if(isset($_POST['google_calendar_flag']) && ($_POST['google_calendar_flag']==1))
	{
		$temp_client_id = crud_option_multisite('bkx_client_id');
		crud_option_multisite("bkx_client_id", $_POST['client_id'],'update');

		$temp_client_secret = crud_option_multisite('bkx_client_secret');
		crud_option_multisite("bkx_client_secret", $_POST['client_secret'],'update');

		$temp_redirect_uri = crud_option_multisite('bkx_redirect_uri');
		crud_option_multisite("bkx_redirect_uri", $_POST['redirect_uri'],'update');

		$_SESSION['success']='Google Calendar Details updated successfully.';

	}

	if(isset($_POST['google_calendar_id_flag']) && ($_POST['google_calendar_id_flag']==1))
	{
		$temp_client_id = crud_option_multisite('bkx_google_calendar_id');
		crud_option_multisite("bkx_google_calendar_id", $_POST['calendar_id'],'update');
	}

	if(isset($_POST['template_flag']) && ($_POST['template_flag']==1))
	{		
		$thankyou_page = $_POST['thankyou_page'];
		$email_confirmation = $_POST['email_confirmation'];
		$success_confirmation = $_POST['success_confirmation'];

		$strTableName = "bkx_booking_template";

		$arrTabledata = array('template_value' => $thankyou_page,'template_name' => 'thankyou' );
		$where = array( 'template_name' => 'thankyou' );
	
		$queryTableData = "select * from ".trim($strTableName)." where template_name='thankyou'";
		$resTableData = 	$wpdb->get_results($queryTableData);
		if(sizeof($resTableData)==0)
		{
			$wpdb->insert( $strTableName, $arrTabledata);
		}
		else
		{
			$wpdb->update( $strTableName, $arrTabledata, $where);
		}

		$arrTabledataEmail = array('template_value' => $email_confirmation,'template_name' => 'emailconfirmation');
		$whereEmail = array( 'template_name' => 'emailconfirmation' );
		
		$queryTableDataEmail = "select * from ".trim($strTableName)." where template_name='emailconfirmation'";
		$resTableDataEmail= 	$wpdb->get_results($queryTableDataEmail);
		if(sizeof($resTableDataEmail)==0)
		{
			$wpdb->insert( $strTableName, $arrTabledataEmail);
		}
		else
		{
			$wpdb->update( $strTableName, $arrTabledataEmail, $whereEmail);
		}

		/* Updated By :Madhuri Rokade
		   Reason : To set the success message for the success message template.
		*/ 
		$arrTabledataSuccess = array('template_value' => $success_confirmation,'template_name' => 'successconfirmation');
		$whereSuccess = array( 'template_name' => 'successconfirmation' );
		
		$queryTableDataSuccess = "select * from ".trim($strTableName)." where template_name='successconfirmation'";
		$resTableDataSuccess= 	$wpdb->get_results($queryTableDataSuccess);
		if(sizeof($resTableDataSuccess)==0)
		{
			$wpdb->insert( $strTableName, $arrTabledataSuccess);
		}
		else
		{
			$wpdb->update( $strTableName, $arrTabledataSuccess, $whereSuccess);
		}

		$_SESSION['success']='Template updated successfully.';
		
	}
	
	if(isset($_POST['siteuser_flag']) && ($_POST['siteuser_flag']==1))
	{
		$temp_option = crud_option_multisite('bkx_siteuser_canedit_seat');
		crud_option_multisite("bkx_siteuser_canedit_seat", $_POST['can_edit_seat'],'update');

		//for editing the css file 
		crud_option_multisite("bkx_siteclient_canedit_css", $_POST['can_edit_css'],'update');

		$_SESSION['success']='Option updated successfully.';
	}
	//@ruchira 8/1/2013 options for css customization 
	if(isset($_POST['sitecss_flag']) && ($_POST['sitecss_flag']==1))
	{
		$temp_text_color = crud_option_multisite('bkx_siteclient_css_text_color');
		$temp_background_color = crud_option_multisite('bkx_siteclient_css_background_color');
		$temp_border_color = crud_option_multisite('bkx_siteclient_css_border_color');
		$temp_progressbar_color = crud_option_multisite('bkx_siteclient_css_progressbar_color');
		//for editing the css file 
		crud_option_multisite("bkx_siteclient_css_text_color", $_POST['text_color'],'update');
		
		crud_option_multisite("bkx_siteclient_css_background_color", $_POST['background_color'],'update');

		crud_option_multisite("bkx_siteclient_css_border_color", $_POST['border_color'],'update');

		crud_option_multisite("bkx_siteclient_css_progressbar_color", $_POST['progressbar_color'],'update');

		$_SESSION['success']='Option updated successfully.';
	}

}
custom_load_scripts(array('jquery-1.7.1.js','jquery-ui.js'));
color_load_scripts(array('iris.min.js'));
?>
<!-- <script type="text/javascript" src="<?php echo plugins_url( 'js/jquery-1.7.1.js' , __FILE__ ); ?>"></script>
<script src="<?php echo plugins_url( 'js/jquery-ui.js' , __FILE__ ); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( 'color/js/iris.min.js' , __FILE__ ); ?>"></script> -->

<script type="text/javascript" src="<?php echo plugins_url( 'tinymce/jscripts/tiny_mce/tiny_mce.js' , __FILE__ ); ?>"></script>
<script type="text/javascript">
tinyMCE.init({
        mode : "textareas"
});
jQuery(document).ready(function(){
	/**********************start script for color picker******************************/

	jQuery('#id_text_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});	
	
	jQuery('#id_background_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});

	jQuery('#id_border_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	
	jQuery('#id_progressbar_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	/********************end script for color picker*******************************/
});
</script>

<div class="wrap">

<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>

<h2>Settings
	
</h2>


<?php
	/*
	 *  Print the error or success messages if any while processing.
	*/	
	if(isset($_SESSION['success']) && $_SESSION['success'] != '')
	{
		echo '<div class="updated"><p><strong>'.$_SESSION['success'].'</strong></p></div>';
		$_SESSION['success']	=	'';
	}
	else if(isset($_SESSION['error']) && $_SESSION['error'] != '')
	{
		echo '<div class="error"><p><strong>'.$_SESSION['error'].'</strong></p></div>';
		$_SESSION['error']	=	'';
	}
?>


<?php 

?>
<!---Alias settings-->
<h3>
Alias
</h3>
<form name="form_alias" id="id_form_alias" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="alias_flag" value="1">
		<tr class="active">
			<td>
				Seat:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="seat_alias" id="id_seat_alias" value="<?php echo crud_option_multisite('bkx_alias_seat'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			Base:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="base_alias" id="id_base_alias" value="<?php echo crud_option_multisite('bkx_alias_base'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			Addition:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="addition_alias" id="id_addition_alias" value="<?php echo crud_option_multisite('bkx_alias_addition'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			Notification:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="notification_alias" id="id_notification_alias" value="<?php echo crud_option_multisite('bkx_alias_notification'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				<input type="submit" onclick="" name="save_alias" id="id_save_alias" value="Save" />
			</td>
		</tr>
	</tbody>
</table>
</form>
<!---End Alias settings-->

<!--Page Setting-->
<h3> Page Setting </h3>
<form name="form_page_setting" id="id_role_setting" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
<tbody>
            <tr class="active">
                    <td>Set Booking Page : </td>
                    <td class="plugin-description">
                         <?php
                         $bkx_set_booking_page = crud_option_multisite('bkx_set_booking_page');
                          $args = array(
                                    'depth'                 => 0,
                                    'child_of'              => 0,
                                    'selected'              => $bkx_set_booking_page,
                                    'echo'                  => 1,
                                    'name'                  => 'bkx_set_booking_page',
                                    'id'                    => null, // string
                                    'class'                 => null, // string
                                    'show_option_none'      => null, // string
                                    'show_option_no_change' => null, // string
                                    'option_none_value'     => null, // string
                                );
                         wp_dropdown_pages($args); ?> Shortcode  : [bookingform]
                    </td>
                    
                    <tr class="active">
				<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
					<input type="submit" onclick="" name="save_page_setting" id="id_save_page_setting" value="Save" />
				</td>
			</tr>
            </tr>
</tbody>
</table>
<input type="hidden" name="page_setting_flag" value="1">
	</form> 
<!--End Page Setting-->
<?php $alias_seat = crud_option_multisite('bkx_alias_seat'); 
    $bkx_seat_role = crud_option_multisite('bkx_seat_role');
    if(isset($bkx_seat_role) && $bkx_seat_role!=''){
        $default_seat_role = $bkx_seat_role;
    }else{
        $default_seat_role = 'resource';
    }
?>
<h3> Role Assignment </h3>
<form name="form_role_setting" id="id_role_setting" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
<tbody>
            <tr class="active">
                    <td>
                        Set Role for <?php echo $alias_seat;?>
                   </td>
                    <td class="plugin-description">
                        <?php 
                        $roles = get_editable_roles(); ?>
                        <select name="bkx_seat_role">
                        <?php if(!empty($roles)){ foreach ($roles as $role_name => $role_info): ?>
                            <option value="<?php echo $role_name;?>" <?php if($role_name == $default_seat_role){ echo "selected";} ?>><?php echo ucwords($role_name); ?></option>
                        <?php endforeach;} ?>
                        </select>    
                    </td>
                    
                    <tr class="active">
				<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
					<input type="submit" onclick="" name="save_role_setting" id="id_save_role_setting" value="Save" />
				</td>
			</tr>
            </tr>
</tbody>
</table>
<input type="hidden" name="role_setting_flag" value="1">
	</form>                        

<!---Other settings--><!--Created By : Divyang Parekh ; Date : 2-11-2015 -->

	<h3> Other Setting </h3>
	<form name="form_other_setting" id="id_other_setting" method="post">
		<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
			<tbody>
			<tr class="active">
				<td>
					Enable Cancel Booking
				</td>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_yes" value="1" <?php if(crud_option_multisite('enable_cancel_booking')==1) echo "checked"; ?>>Yes </br>
						<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_no" value="0" <?php if(crud_option_multisite('enable_cancel_booking')==0) echo "checked"; ?>> No
					</div>
				</td>
			</tr>
			<tr class="active" style="display:none;" id="page_drop_down_cancel_booking">
				<td>
					Select Page for Cancellation Policy :
				</td>
				<td class="plugin-description">
					<div class="plugin-description">
					<?php $args = array('selected'=> crud_option_multisite('cancellation_policy_page_id'));wp_dropdown_pages( $args );?>
					</div>
				</td>
			</tr>
			<tr class="active">
				<td>
					Do you want to add the option "ANY" when site users select a <?php echo crud_option_multisite('bkx_alias_seat'); ?>?
				</td>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_any_seat" id="id_enable_any_seat_yes" value="1" <?php if(crud_option_multisite('enable_any_seat')==1) echo "checked"; ?>>Yes </br>
						<input type="radio" name="enable_any_seat" id="id_enable_any_seat_no" value="0" <?php if(crud_option_multisite('enable_any_seat')==0) echo "checked"; ?>> No
					</div>
				</td>
			</tr>
			<tr class="active">
				<td> Currency Option </td>

			<td class="plugin-description">
				<div class="plugin-description">
				<select name="currency_option">
					<?php
						$currency_code_options = get_bookingx_currencies();
						foreach ( $currency_code_options as $code => $name ) {
							$currency_option = crud_option_multisite('currency_option');
							if($currency_option == $code) $cur_selected = "selected"; else $cur_selected = '';
							echo '<option value="'.$code.'" '.$cur_selected.' >'.$name . ' (' . get_bookingx_currency_symbol( $code ) . ')'.'</option>';
						}
					?>
				</select>
				</div>
			</td>
			</tr>


			<tr class="active">
				<td>
					Enable registered customers to view edit and delete bookings
				</td>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="reg_customer_crud_op" id="id_reg_customer_crud_op_yes" value="1" <?php if(crud_option_multisite('reg_customer_crud_op')==1) echo "checked"; ?>>Yes </br>
						<input type="radio" name="reg_customer_crud_op" id="id_reg_customer_crud_op_no" value="0" <?php if(crud_option_multisite('reg_customer_crud_op')==0) echo "checked"; ?>> No
					</div>
				</td>
			</tr>



			<tr class="active">
				<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
					<input type="submit" onclick="" name="save_other_setting" id="id_save_other_setting" value="Save" />
				</td>
			</tr>


			</tbody>
		</table>
		<input type="hidden" name="other_setting_flag" value="1">
	</form>
	<!---End Other settings-->
<!---Start Paypal settings settings-->
<h3>Paypal express checkout options</h3>
<a href="https://developer.paypal.com/docs/classic/api/apiCredentials/" target="_blank">Click here</a> to create your paypal account and get API Credentials.
<form name="form_api" id="id_form_api" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="api_flag" value="1">
		<tr class="active">
			<td>
				Paypal Mode:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="paypalmode" id="id_modesandbox" value="sandbox" <?php if(crud_option_multisite('bkx_api_paypal_paypalmode')=="sandbox") echo "checked"; ?>>Sandbox </br>
					<input type="radio" name="paypalmode" id="id_modelive" value="live" <?php if(crud_option_multisite('bkx_api_paypal_paypalmode')=="live") echo "checked"; ?>> Live
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
				API username:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="username" id="id_username" value="<?php echo crud_option_multisite('bkx_api_paypal_username'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			API password:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="password" id="id_password" value="<?php echo crud_option_multisite('bkx_api_paypal_password'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			API signature:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="signature" id="id_signature" value="<?php echo crud_option_multisite('bkx_api_paypal_signature'); ?>">
				</div>
			</td>
		</tr>
		
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				<input type="submit" onclick="" name="save_api" id="id_save_api" value="Save" />
			</td>
		</tr>
	</tbody>
</table>
</form>
<!---End Paypal settings settings-->


<?php if(crud_option_multisite('bkx_client_id')!="" && crud_option_multisite('bkx_client_secret')!=""){ ?>
<h3>
Google Calendar for entry of bookings:
</h3>

<li> <b>account :-</b>  the "Email address", which was used for login to the Google API console.</li>
<li><b> key :-</b> the path to your private key </li>
<li><b> calendar_id :- </b> the calendar's id (can be taken from the settings of your Google Calendar account) </li>


<form name="form_google_calendar" id="id_form_google_calendar" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="google_calendar_id_flag" value="1">
		<tr class="active">
			<td>
				Calendar ID:
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="calendar_id" id="id_calendar_id" value="<?php echo crud_option_multisite('bkx_google_calendar_id'); ?>">
				</div>
			</td>
			<td></td>
		</tr>
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				<input type="submit" onclick="" name="save_calendar_id" id="id_save_calendar_id" value="Save" />
			</td>
		</tr>
		
	</tbody>
</table>
</form>
<?php } /* ?>

<h3>
Insert Into Google Calendar:
</h3>

<table>
	<tbody>
		<?php				
			$objListSeat=$wpdb->get_results('SELECT * FROM bkx_seat WHERE seat_ical_address!=""');

			?>
				<tr class="active">
				<td>	
				
				<?php if (isset($_SESSION['token'])) {
					$btnText = "Insert Into Google Calendar";
				}
				else
				{
					$btnText = "Google Connect";
				}	
				?>
					<form name="form_google_calendar_insert" id="id_form_google_calendar_insert" method="post">
				<input type="hidden" name="connect_google_api_flag" value="1">	
				<input type="image" src= "//www.google.com/calendar/images/ext/gc_button1.gif" name="insert" id="id_insert" />
				<!-- <input type="submit" onclick="" name="insert" id="id_insert" value="<?php echo $btnText; ?>" /> -->					
				
				<!-- <img src ="<?php echo plugins_url( '/', __FILE__ ); ?>/images/google_cal.png" width="30" /> -->
				</form>
				<!-- <a href="http://www.google.com/calendar/render?cid=" target="_blank"><img src="//www.google.com/calendar/images/ext/gc_button1.gif" border=0></a> -->
				</td>
				</tr>
				<?php if (isset($_SESSION['token'])) { 
				if(sizeof($objListSeat)>0){						
				?>
				<tr><td>
				<form name="form_google_calendar_insert_seat" id="id_form_google_calendar_insert_seat" method="post">
				<input type="hidden" name="connect_google_api_flag_seat" value="1">
				<select name="seat_id" id="id_seat_id">
					<option value="">Select Seat</option>
					<?php
						foreach($objListSeat as $temp)
						{
						echo "<option value='".$temp->seat_id."'>".$temp->seat_name."</option>";
						}
					?>
				</select>
				<input type="submit" onclick="" name="insert_seat" id="id_insert_seat" value="<?php echo $btnText; ?>" />
				</form>

				<input type="button" value="Logout and connect with another account" onclick="window.location.href='<?php echo $currPageUrl."&logout=1"; ?>'">
			</td></tr>
			<?php } } ?>
			<?php
		//}
	?>
	</tbody>
</table>

<?php */ ?>
<h3>
Templates:
</h3>
(These tags can be placed in the email form and they will pull data from the database to include in the email.<br/>
[fname], [lname], [total_price], [order_id] , [txn_id], [seat_name], [base_name], [additions_list], [time_of_booking], [date_of_booking], [location_of_booking].
)
<form name="form_template" id="id_form_template" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="template_flag" value="1">
		<tr class="active">
			<td>
				Thank you page:(Payment Confirmed)
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<textarea name="thankyou_page"><?php if(isset($objListTemplateThankyou[0]->template_name)){echo $objListTemplateThankyou[0]->template_value;} ?></textarea>
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			Email Confirmation:(Payment Pending)
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<textarea name="email_confirmation"><?php if(isset($objListTemplateEmail[0]->template_name)){echo $objListTemplateEmail[0]->template_value;} ?></textarea>
				</div>
			</td>
		</tr>
		<tr class="active">
			<td>
			Transaction Success Message:(Payment Success)
			</td>
			<td class="plugin-description">
				<div class="plugin-description">
					<textarea name="success_confirmation"><?php if(isset($objListTemplateSuccess[0]->template_name)){echo $objListTemplateSuccess[0]->template_value;} ?></textarea>
				</div>
			</td>
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				<input type="submit" onclick="" name="save_template" id="id_save_template" value="Save" />
			</td>
		</tr>
	</tbody>
</table>
</form>

<h3>
Site Users option
</h3>
<form name="form_siteuser" id="id_form_siteuser" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="siteuser_flag" value="1">
		<tr class="active">
			<td>Can site user add/edit their own seat:</td>
			<td class="plugin-description">
				
					<select name="can_edit_seat" id="id_can_edit_seat">
						<option value="Y" <?php if($temp_option=="Y"){echo "selected";} ?> >Yes</option>
						<option value="N" <?php if($temp_option=="N" || $temp_option == "default"){echo "selected";} ?> >No</option>
					</select>
				
			</td>
		</tr>
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				<input type="submit" onclick="" name="save_siteuser" id="id_save_siteuser" value="Save" />
			</td>
		</tr>
	</tbody>
</table>
</form>

<h3>
CSS Customization Options
</h3>
<form name="form_siteuser" id="id_form_sitecss" method="post">
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="sitecss_flag" value="1">
		<tr class="active">
			<td>Booking Form Text Colour:</td>
			<td class="plugin-description">
				<input type="text" readonly name="text_color" id="id_text_color" value="<?php echo crud_option_multisite('bkx_siteclient_css_text_color'); ?>">
			</td>
		</tr>
		<tr class="active">
			<td>Booking Form Background Colour:</td>
			<td class="plugin-description">
				<input type="text" readonly name="background_color" id="id_background_color" value="<?php echo crud_option_multisite('bkx_siteclient_css_background_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<td>Booking Form Border Colour:</td>
			<td class="plugin-description">
				<input type="text" readonly name="border_color" id="id_border_color" value="<?php echo crud_option_multisite('bkx_siteclient_css_border_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<td>Booking Form Progress Bar Colour:</td>
			<td class="plugin-description">
				<input type="text" readonly name="progressbar_color" id="id_progressbar_color" value="<?php echo crud_option_multisite('bkx_siteclient_css_progressbar_color'); ?>">
			</td>
		</tr>
		
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				<input type="submit" onclick="" name="save_sitecss" id="id_save_sitecss" value="Save" />
			</td>
		</tr>
	</tbody>
</table>
</form>

<!-- This is for export button   -->

	<div>
		<table class="wp-list-table widefat plugins" style="margin-top:20px;">
			<tr class="active">
				<td>
		<h3> Export XML </h3>
	
		<form name="xml_export" method="post" action="<?php echo plugins_url('',__FILE__); ?>/generate_xml.php">
			<input type="hidden" id="id_addition_list" name="addition_list" value="">
			<input type="hidden" id="id_type" name="type" value="all">
			<input type="button" value="Export xml" name="export_xml" onclick="generate_xml();" >
		</form>
				</td>
			</tr>
		</table>
	</div>
<!--  End export functionality -->

<!--Start Import Functionality --> 

	<div>
		<table class="wp-list-table widefat plugins" style="margin-top:20px;">
			<tr class="active">
				<td>
		<h3>Import XML</h3>
		<form name="xml_export" method="post" action="<?php echo plugins_url('',__FILE__); ?>/importXML.php" enctype="multipart/form-data">
			<!--<input type="hidden" id="id_addition_list" name="addition_list" value="">
			<input type="hidden" id="id_type" name="type" value="all">-->
			<div><input type="checkbox" name="truncate_records" ><small>(Check to delete all previous value)</small></div>
			<input type="file" name="import_file" >
			<input type="submit" value="Import Xml" name="import_xml">
		</form>
				</td>
			</tr>
		</table>
	</div>
		
<!--End Import Functionality-->

</div><!-- WRAP ENDS -->

<?php

if(isset($_REQUEST['save_template'])){
	 ?>
      <script>window.location = "<?php echo admin_url().'admin.php?page=settings'; ?>";</script>
	 <?php 
}

?>
<script>
	jQuery( document ).ready(function() {
		<?php if(crud_option_multisite('enable_cancel_booking')==1):?>
		jQuery("#page_drop_down_cancel_booking").show();
		<?php else:?>
		jQuery("#page_drop_down_cancel_booking").hide();
		<?php endif;?>
		jQuery('#id_enable_cancel_booking_yes').click(function ()
		{
			var $id_enable_cancel_booking = jQuery("#id_enable_cancel_booking_yes").val();
			if($id_enable_cancel_booking == 1)
			{
				jQuery("#page_drop_down_cancel_booking").show();
			}
		});
		jQuery('#id_enable_cancel_booking_no').click(function ()
		{
			var $id_enable_cancel_booking = jQuery("#id_enable_cancel_booking_no").val();
			if($id_enable_cancel_booking == 0)
			{
				jQuery("#page_drop_down_cancel_booking").hide();
			}
		});
	});
</script>
<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$bkx_export_file_name = BKX_PLUGIN_DIR_PATH."uploads/newfile.xml";
$seat_alias = bkx_crud_option_multisite("bkx_alias_seat");
$base_alias = bkx_crud_option_multisite("bkx_alias_base");
$addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
if(!empty($current_submenu_active) && $current_submenu_active == 'alias') :?>
<!---Alias settings-->
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
<form name="form_alias" id="id_form_alias" method="post">
<table cellspacing="0" class="widefat " style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="alias_flag" value="1">
		<tr class="active">
			<th scope="row"><label for="seat"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Seat' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="seat_alias" id="id_seat_alias" value="<?php echo bkx_crud_option_multisite('bkx_alias_seat'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
		<th scope="row"><label for="Base"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Base' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="base_alias" id="id_base_alias" value="<?php echo bkx_crud_option_multisite('bkx_alias_base'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="Addition"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Addition' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="addition_alias" id="id_addition_alias" value="<?php echo bkx_crud_option_multisite('bkx_alias_addition'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="Notification"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Notification' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="notification_alias" id="id_notification_alias" value="<?php echo bkx_crud_option_multisite('bkx_alias_notification'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="bkx_label_of_step1 Text"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Label for booking form Step 1 ' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<textarea name="bkx_label_of_step1" cols="50"><?php echo bkx_crud_option_multisite('bkx_label_of_step1'); ?></textarea>
				</div>
			</td>
		</tr> 

		<tr class="active">
			<th scope="row"><label for="extended Text">Label for <?php echo bkx_crud_option_multisite('bkx_alias_base');?> time be extended text</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<textarea name="bkx_notice_time_extended_text_alias" cols="50"><?php echo bkx_crud_option_multisite('bkx_notice_time_extended_text_alias'); ?></textarea>
				</div>
			</td>
		</tr>
	
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_alias" id="id_save_alias" value="Save Changes" /></p>
</form>
<!---End Alias settings-->
<?php endif; ?>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'page_setting') :?>
<!--Page Setting-->
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
 
<!--End Page Setting-->
<form name="form_template" id="id_form_template" method="post">
<table cellspacing="0" class="widefat" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="template_flag" value="1">
		 <tr class="active">
            	<th scope="row"><label for="Set Booking Page"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Set Booking Page' ); ?></label></th>
                    
                    <td class="plugin-description">
                         <?php
                        $bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
                          $args = array(
                                    'depth'                 => 0,
                                    'child_of'              => 0,
                                    'selected'              => $bkx_set_booking_page,
                                    'echo'                  => 1,
                                    'name'                  => 'bkx_set_booking_page',
                                    'id'                    => null, // string
                                    'class'                 => null, // string
                                    'show_option_none'      => null, // string
                                    'show_option_no_change' => null, // stringx
                                    'option_none_value'     => null, // string
                                );
                         wp_dropdown_pages($args); ?> Shortcode  : [bookingform]
                    </td>
                    
                    <tr class="active">
				<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
					
				</td>
			</tr>

		<tr class="active">
			<th scope="row"><label for="Status Pending (default)"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Status Pending (default)' ); ?> </label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">				
					<?php $bkx_tempalate_status_pending = bkx_crud_option_multisite('bkx_tempalate_status_pending');
                          $args = array('selected'=> $bkx_tempalate_status_pending,'name'=> 'status_pending');
                          wp_dropdown_pages($args); ?>

				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="Email Confirmation"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Status Acknowledge ' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
				<?php $bkx_tempalate_status_ack = bkx_crud_option_multisite('bkx_tempalate_status_ack');
                          $args = array('selected'=> $bkx_tempalate_status_ack,'name'=> 'status_ack');
                          wp_dropdown_pages($args); ?>					 
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Transaction Success"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Status Complete' ); ?> </label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
						<?php $bkx_tempalate_status_complete = bkx_crud_option_multisite('bkx_tempalate_status_complete');
                          $args = array('selected'=> $bkx_tempalate_status_complete,'name'=> 'status_complete');
                          wp_dropdown_pages($args); ?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Transaction Success"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Status Missed' ); ?> </label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
						<?php $bkx_tempalate_status_missed = bkx_crud_option_multisite('bkx_tempalate_status_missed');
                          $args = array('selected'=> $bkx_tempalate_status_missed,'name'=> 'status_missed');
                          wp_dropdown_pages($args); ?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Transaction Success"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Status Cancelled' ); ?> </label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
						<?php $bkx_tempalate_status_cancelled = bkx_crud_option_multisite('bkx_tempalate_status_cancelled');
                          $args = array('selected'=> $bkx_tempalate_status_cancelled,'name'=> 'status_cancelled');
                          wp_dropdown_pages($args); ?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Terms and conditions"> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'Terms & conditions Page' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
				<?php $bkx_term_cond_page = bkx_crud_option_multisite('bkx_term_cond_page');
                          $args = array('show_option_none' => 'Select Terms & conditions Page', 'selected'=> $bkx_term_cond_page,'name'=> 'bkx_term_cond_page');
                          wp_dropdown_pages($args); ?>					 
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Privacy Policy"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Privacy policy Page' ); ?> </label></th>
			<td class="plugin-description">
				<div class="plugin-description">
				<?php $bkx_privacy_policy_page = bkx_crud_option_multisite('bkx_privacy_policy_page');
                          $args = array('show_option_none' => 'Select Privacy Policy Page', 'selected'=> $bkx_privacy_policy_page,'name'=> 'bkx_privacy_policy_page');
                          wp_dropdown_pages($args); ?>					 
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="enable_editor"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Display Booking X shortcode icon on visual editor and text editor?' ); ?></label></th>
			 
			<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_editor" id="id_enable_editor_yes" value="1" <?php if(bkx_crud_option_multisite('enable_editor')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes'  ); ?></br>
						<input type="radio" name="enable_editor" id="id_enable_editor_no" value="0" <?php if(bkx_crud_option_multisite('enable_editor')==0) echo "checked"; ?>>
						<?php printf( esc_html__( '%1$s', 'bookingx' ),  'No'  ); ?> 
					</div>
			</td>
		</tr>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_template" id="id_save_template" value="Save Changes" /></p>
</form>
<?php endif; ?>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'css') :?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
 
<form name="form_siteuser" id="id_form_sitecss" method="post">
<table cellspacing="0" class="widefat" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="sitecss_flag" value="1">
		<tr class="active">
			<th scope="row"><label for="Text Colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Text Colour' ); ?></label></th>
			 
			<td class="plugin-description">
				<input type="text" name="bkx_text_color" id="id_bkx_text_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_text_color'); ?>">
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="Background Colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Background Colour' ); ?></label></th>
			 
			<td class="plugin-description">
				<input type="text" name="bkx_background_color" id="id_bkx_background_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_background_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Border Colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Border Colour' ); ?></label></th>
			 
			<td class="plugin-description">
				<input type="text" name="bkx_border_color" id="id_bkx_border_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_border_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Progress Bar"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Progress Bar Colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_progressbar_color" id="id_bkx_progressbar_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_progressbar_color'); ?>">
			</td>
		</tr>
 
		<tr class="active">
			<th scope="row"><label for="Calendar Month title color"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add Calendar  Month title color' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_month_title_color" id="id_bkx_cal_month_title_color" value="<?php echo bkx_crud_option_multisite('bkx_cal_month_title_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Calendar Month background color"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add Calendar Month background color' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_month_bg_color" id="id_bkx_cal_month_bg_color" value="<?php echo bkx_crud_option_multisite('bkx_cal_month_bg_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Calendar Border colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add Calendar Border colour option' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_border_color" id="id_bkx_cal_border_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_cal_border_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Calendar day background colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add Calendar day background colour option' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_day_color" id="id_bkx_cal_day_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_cal_day_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Calendar day selected colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add Current Selcted colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_day_selected_color" id="id_bkx_cal_day_selected_color" value="<?php echo bkx_crud_option_multisite('bkx_siteclient_css_cal_day_selected_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Add time available colour option"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add time available colour option' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_available_color" id="id_bkx_time_available_color" value="<?php echo bkx_crud_option_multisite('bkx_time_available_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Add time selected colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add time selected colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_selected_color" id="id_bkx_time_selected_color" value="<?php echo bkx_crud_option_multisite('bkx_time_selected_color'); ?>">
			</td>
		</tr>

		<tr class="active"> 
			<th scope="row"><label for="Add time unavailable colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Add time unavailable colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_unavailable_color" id="id_bkx_time_unavailable_color" value="<?php echo bkx_crud_option_multisite('bkx_time_unavailable_color'); ?>">
			</td>
		</tr>

		<!-- <tr class="active"> 
			<th scope="row"><label for="Add New Selected colour"><?php //printf( esc_html__( '%1$s', 'bookingx' ),  'Add Current Selcted colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_new_selected" id="id_bkx_time_new_selected" value="<?php //echo bkx_crud_option_multisite('bkx_time_new_selected'); ?>">
			</td>
		</tr> -->

		<tr class="active">
			<th scope="row"><label for="Time Block BG colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Time Block BG colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_block_bg_color" id="id_bkx_time_block_bg_color" value="<?php echo bkx_crud_option_multisite('bkx_time_block_bg_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Time Block service colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Time Block service colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_block_service_color" id="id_bkx_time_block_service_color" value="<?php echo bkx_crud_option_multisite('bkx_time_block_service_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Time Block extra colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Time Block extra colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_block_extra_color" id="id_bkx_time_block_extra_color" value="<?php echo bkx_crud_option_multisite('bkx_time_block_extra_color'); ?>">
			</td>
		</tr>
		
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				
			</td>
		</tr>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_sitecss" id="id_save_sitecss" value="Save Changes" /></p>
</form>
<?php endif; ?>


<?php if(!empty($current_submenu_active) && $current_submenu_active == 'other_settings') :?>
<!---Other settings--><!--Created By : Divyang Parekh ; -->
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
<form name="form_other_setting" id="id_other_setting" method="post">
	<table cellspacing="0" class="widefat" style="margin-top:20px;">
		<tbody>
		<tr class="active">
			<th scope="row"><label for="Cancel Booking"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Enable Cancel Booking' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_yes" value="1" <?php if(bkx_crud_option_multisite('enable_cancel_booking')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?> </br>
					<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_no" value="0" <?php if(bkx_crud_option_multisite('enable_cancel_booking')==0) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'No' ); ?> 
				</div>
			</td>
		</tr>
		<tr class="active" style="display:none;" id="page_drop_down_cancel_booking">
			<th scope="row"><label for="Cancellation"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Select Page for Cancellation Policy ' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
				<?php $args = array('selected'=> bkx_crud_option_multisite('cancellation_policy_page_id'));wp_dropdown_pages( $args );?>
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="ANY"> <?php printf( esc_html__( 'Allow "Any" for %1$s option', 'bookingx' ),  bkx_crud_option_multisite('bkx_alias_seat') ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="enable_any_seat" id="id_enable_any_seat_yes" value="1" <?php if(bkx_crud_option_multisite('enable_any_seat')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?> </br>
					<input type="radio" name="enable_any_seat" id="id_enable_any_seat_no" value="0" <?php if(bkx_crud_option_multisite('enable_any_seat')==0) echo "checked"; ?>> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'No' ); ?>
				</div>
			</td>
		</tr>
	
		<tr class="active">
			<th scope="row"><label for="edit and delete bookings"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Enable customer dashboard' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="reg_customer_crud_op" id="id_reg_customer_crud_op_yes" value="1" <?php if(bkx_crud_option_multisite('reg_customer_crud_op')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?> </br>
					<input type="radio" name="reg_customer_crud_op" id="id_reg_customer_crud_op_no" value="0" <?php if(bkx_crud_option_multisite('reg_customer_crud_op')==0) echo "checked"; ?>> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'No' ); ?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="dashboard column's bookings"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Dashboard Column\'s' ); ?></label></th>

				<td class="plugin-description">
				<div class="plugin-description">
					<input type="checkbox" class="bkx-dashboard-column-all"> Select All <br />
			 <?php 
			 $bkx_dashboard_column_selected = bkx_crud_option_multisite("bkx_dashboard_column");

			 $bkx_dashboard_column_html = "";
			 $bkx_booking_columns = bkx_booking_columns_data();
			 if(!empty($bkx_booking_columns)){
		 		foreach ($bkx_booking_columns as $key => $booking_columns) {
	 			 	if($key != "cb")
	 			 	{
	 			 		$bkx_booking_column_checked = ( !empty($bkx_dashboard_column_selected) && in_array($key, $bkx_dashboard_column_selected) ? " checked='checked' " : '');
	 			 		$bkx_dashboard_column_html .= '<input type="checkbox" class="bkx-dashboard-column" name="bkx_dashboard_column[]" value="'.$key.'" '.$bkx_booking_column_checked.'>'.$booking_columns.'<br />';
	 			 	}
		 		}
			 }
			 echo $bkx_dashboard_column_html;
			 ?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
			</td>
		</tr>
		</tbody>
	</table>
	<input type="hidden" name="other_setting_flag" value="1">
	<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_other_setting" id="id_save_other_setting" value="Save Changes" /></p>
</form>
<!---End Other settings-->
<?php endif; ?>


<?php if(!empty($current_submenu_active) && $current_submenu_active == 'exim') :?>
<!-- This is for export button   -->
<div>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'Export XML' ); ?> </h3>
<?php $bkx_export_enable = is_writable($bkx_export_file_name) ? "" : "disabled";
if(!is_writable($bkx_export_file_name)) { echo "<strong> Please allow file permission to generate file on ".BKX_PLUGIN_DIR_PATH."uploads Directory.</strong>";}
?>
<form name="xml_export" method="post" action="">
		<table class="widefat" style="margin-top:20px;">
			<tr class="active">
			<input type="hidden" id="id_addition_list" name="addition_list" value="">
			<input type="hidden" id="id_type" name="type" value="all">
			</tr>
		</table>
		<p class="submit"><input type="submit" value="Export xml" <?php echo $bkx_export_enable;?> class='button-primary' name="export_xml"></p>
		</form>
</div>
<!--  End export functionality -->
<!--Start Import Functionality --> 
<div>		
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'Import XML' ); ?> </h3>
<form name="xml_export" method="post" action="" enctype="multipart/form-data">
<table class="widefat" style="margin-top:20px;">
			<tr class="active">
			<td>
				<div><input type="checkbox" name="truncate_records" ><small><?php printf( esc_html__( '( %1$s %2$s\'s , %3$s\'s , %4$s\'s , Booking data and All Setting\'s  )', 'bookingx' ),  'Check to delete all existing',$seat_alias,$base_alias,$addition_alias); ?></small></div>
				<input type="file" name="import_file" >
			</td>
			</tr>
</table>
<p class="submit"><input type="submit" value="Import Xml" class='button-primary' name="import_xml"></p>
</form>
</div>
<!--End Import Functionality-->
<?php endif; ?>
<?php
if(isset($_POST['export_xml']) && sanitize_text_field($_POST['export_xml']) == "Export xml") :
    ob_clean();
    $BkxExportObj = new BkxExport();
    $BkxExportObj->export_now();
    die;
endif;
if(isset($_POST['import_xml']) && sanitize_text_field($_POST['import_xml']) == "Import Xml") :
    $BkxImport = new BkxImport();
	$import_now = $BkxImport->import_now($_FILES,$_POST);
endif;
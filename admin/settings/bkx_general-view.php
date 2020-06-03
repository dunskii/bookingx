<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$bkx_export_file_name = BKX_PLUGIN_DIR_PATH."public/uploads/newfile.xml";
$seat_alias = bkx_crud_option_multisite("bkx_alias_seat");
$base_alias = bkx_crud_option_multisite("bkx_alias_base");
$addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
$Bookingx_Admin = new Bookingx_Admin();
if(!empty($current_submenu_active) && $current_submenu_active == 'alias') :?>
<!---Alias settings-->
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
<form name="form_alias" id="id_form_alias" method="post">
<table cellspacing="0" class="widefat " style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="alias_flag" value="1">
        <input type="hidden" name="bkx_setting_form_init" value="1">
		<tr class="active">
			<th scope="row"><label for="Resource"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Resource' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="seat_alias" id="id_seat_alias" value="<?php echo bkx_crud_option_multisite('bkx_alias_seat'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
		<th scope="row"><label for="Service"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Service' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="base_alias" id="id_base_alias" value="<?php echo bkx_crud_option_multisite('bkx_alias_base'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for='Extras'><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Extras' ); ?></label></th>
			 
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
        <?php do_action('bkx_general_alias_settings');?>
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
<table cellspacing="0" class="widefat bkx-content-settings" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="template_flag" value="1">
        <input type="hidden" name="bkx_setting_form_init" value="1">

        <tr class="active header">
            <th scope="row"><label for="<?php echo esc_html__( 'Taxonomy Settings', 'bookingx' ); ?>"><?php echo esc_html__( 'Taxonomy Settings', 'bookingx' ); ?></label></th>
        </tr>

        <tr class="active">
            <td class="plugin-description" colspan="3">
                <div class="plugin-description">
                    <?php echo __(' <b>Note :</b> If Select "Yes" than Category option enable for this post type.', 'bookingx'); ?>
                </div>
            </td>
        </tr>

		<tr class="active">
			<th scope="row"><label for="Enable <?php printf( esc_html__( '%1$s', 'bookingx' ),  $seat_alias ); ?> Taxonomy"><?php printf( esc_html__( '%1$s', 'bookingx' ),  "Enable $seat_alias Taxonomy" ); ?></label></th>
			<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_seat_taxonomy_status" id="id_enable_seat_taxonomy_yes" value="1" <?php if( bkx_crud_option_multisite('bkx_seat_taxonomy_status') == 1 ) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes'  ); ?>
						<input type="radio" name="bkx_seat_taxonomy_status" id="id_enable_seat_taxonomy_no" value="0" <?php if(bkx_crud_option_multisite('bkx_seat_taxonomy_status') == 0 ) echo "checked"; ?>>
						<?php printf( esc_html__( '%1$s', 'bookingx' ),  'No'  ); ?>
					</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Enable <?php printf( esc_html__( '%1$s', 'bookingx' ),  $base_alias ); ?> Taxonomy"><?php printf( esc_html__( '%1$s', 'bookingx' ),  "Enable $base_alias Taxonomy" ); ?></label></th>
			 
			<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_base_taxonomy_status" id="id_enable_base_taxonomy_yes" value="1" <?php if( bkx_crud_option_multisite('bkx_base_taxonomy_status') == 1 ) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes'  ); ?>
						<input type="radio" name="bkx_base_taxonomy_status" id="id_enable_base_taxonomy_no" value="0" <?php if(bkx_crud_option_multisite('bkx_base_taxonomy_status') == 0 ) echo "checked"; ?>>
						<?php printf( esc_html__( '%1$s', 'bookingx' ),  'No'  ); ?> 
					</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Enable <?php printf( esc_html__( '%1$s', 'bookingx' ),  $addition_alias ); ?> Taxonomy"><?php printf( esc_html__( '%1$s', 'bookingx' ),  "Enable $addition_alias Taxonomy" ); ?></label></th>
			 
			<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_addition_taxonomy_status" id="id_bkx_addition_taxonomy_status_yes" value="1" <?php if( bkx_crud_option_multisite('bkx_addition_taxonomy_status') == 1 ) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes'  ); ?>
						<input type="radio" name="bkx_addition_taxonomy_status" id="id_bkx_addition_taxonomy_status_no" value="0" <?php if(bkx_crud_option_multisite('bkx_addition_taxonomy_status') == 0 ) echo "checked"; ?>>
						<?php printf( esc_html__( '%1$s', 'bookingx' ),  'No'  ); ?> 
					</div>
			</td>
		</tr>
        <tr class="active header">
            <th scope="row"><label for="<?php echo esc_html__( 'Other Settings', 'bookingx' ); ?>"><?php echo esc_html__( 'Other Settings', 'bookingx' ); ?></label></th>
        </tr>
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
                         wp_dropdown_pages($args); ?> Shortcode  : [bkx_booking_form]
                    </td>
        </tr>

        <tr class="active">
            <th scope="row"><label for="Enable Legal options"> <?php printf( esc_html__( 'Enable Legal options', 'bookingx' ),  bkx_crud_option_multisite('bkx_legal_options') ); ?></label></th>

            <td class="plugin-description">
                <div class="plugin-description">
                    <input type="radio" name="bkx_legal_options" id="id_bkx_legal_options_yes" value="1" <?php if(bkx_crud_option_multisite('bkx_legal_options') == 1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?>
                    <input type="radio" name="bkx_legal_options" id="id_bkx_legal_options_no" value="0" <?php if(bkx_crud_option_multisite('bkx_legal_options') == 0) echo "checked"; ?>> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'No' ); ?>
                </div>
            </td>
        </tr>

		<tr class="active">
			<th scope="row"><label for="Terms and conditions"> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'Set Terms & conditions Page' ); ?></label></th>
			<td class="plugin-description">
				<div class="plugin-description">
				<?php $bkx_term_cond_page = bkx_crud_option_multisite('bkx_term_cond_page');
                          $args = array('show_option_none' => 'Select Page', 'selected'=> $bkx_term_cond_page,'name'=> 'bkx_term_cond_page');
                          wp_dropdown_pages($args); ?>					 
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Privacy Policy"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Set Privacy policy' ); ?> </label></th>
			<td class="plugin-description">
				<div class="plugin-description">
				<?php $bkx_privacy_policy_page = bkx_crud_option_multisite('bkx_privacy_policy_page');
                          $args = array('show_option_none' => 'Select Page', 'selected'=> $bkx_privacy_policy_page,'name'=> 'bkx_privacy_policy_page');
                          wp_dropdown_pages($args); ?>					 
				</div>
			</td>
		</tr>

        <tr class="active">
            <th scope="row"><label for="Cancellation Policy"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Set Cancellation policy' ); ?> </label></th>
            <td class="plugin-description">
                <div class="plugin-description">
                    <?php $bkx_cancellation_policy = bkx_crud_option_multisite('bkx_cancellation_policy_page');
                    $args = array('show_option_none' => 'Select Page', 'selected'=> $bkx_cancellation_policy,'name'=> 'bkx_cancellation_policy_page');
                    wp_dropdown_pages($args); ?>
                </div>
            </td>
        </tr>

		<tr class="active">
			<th scope="row"><label for="enable_editor"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Display Booking X shortcode icon on visual editor and text editor?' ); ?></label></th>
			 
			<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="enable_editor" id="id_enable_editor_yes" value="1" <?php if(bkx_crud_option_multisite('enable_editor')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes'  ); ?>
						<input type="radio" name="enable_editor" id="id_enable_editor_no" value="0" <?php if(bkx_crud_option_multisite('enable_editor')==0) echo "checked"; ?>>
						<?php printf( esc_html__( '%1$s', 'bookingx' ),  'No'  ); ?> 
					</div>
			</td>
		</tr>
        <?php do_action('bkx_general_page_settings');?>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_template" id="id_save_template" value="Save Changes" /></p>
</form>
<?php endif; ?>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'emails') :?>
    <?php
    $current_main_tab = sanitize_text_field($_GET['bkx_tab']);
    $section = sanitize_text_field($_GET['section']);
    $sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field($_GET['sub_tab']) : '';
    $mailer          = new BKX_Emails_Setup();
    $generate_tab_url = $screen->parent_file.'&page=bkx-setting&bkx_tab=bkx_general&section=emails';
    if( $current_main_tab == 'bkx_general' && $section == 'emails' && isset($sub_tab) && $sub_tab != '') {
        $email_templates = $mailer->get_emails();
        if ( $sub_tab ) {
            foreach ( $email_templates as $email_key => $email ) {
                if ( strtolower( $email_key ) === $sub_tab ) {
                    ?>
                    <form name="bkx_emails_settings" id="bkx_emails_settings" method="post">
                    <h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $email->title ); ?>  <a href="<?php echo $generate_tab_url;?>"> Back </a></h3>
                    <p><?php printf( esc_html__( '%1$s', 'bookingx' ),  $email->description ); ?></p>
                    <?php
                    $email->admin_options();
                    break;
                }
            }
        }
        ?>
        <input type="hidden" name="bkx_emails_settings" value="1">
        <input type="hidden" name="bkx_setting_form_init" value="1">
        <p class="submit"><input type="submit" onclick="" class='button-primary' name="bkx_save_<?php echo $sub_tab;?>" id="bkx_save_<?php echo $sub_tab;?>" value="Save Changes" /></p>
        </form>
        <?php
        }else{ ?>
        <h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
        <p>Email notifications sent from Booking X are listed below. Click on an email to configure it.</p>
        <?php
            $email_settings = new BookingX_Email_Content();
            $email_settings->email_notification_setting();
         } ?>
<?php endif;?>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'css') :?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
 
<form name="form_siteuser" id="id_form_sitecss" method="post">
<table cellspacing="0" class="widefat" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="sitecss_flag" value="1">
        <input type="hidden" name="bkx_setting_form_init" value="1">
        <?php $bkx_booking_style = bkx_crud_option_multisite('bkx_booking_style'); ?>
        <tr class="active">
            <th scope="row"><label for="Set Booking Style"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Calendar Layout' ); ?></label></th>
            <td class="plugin-description">
                <select name="bkx_booking_style" class="bkx-booking-style">
                    <option value="default"   <?php echo ( ( $bkx_booking_style == "" || $bkx_booking_style == 'default' ) ? "selected" : "" ); ?>> Classic Calendar</option>
                    <option value="day_style" <?php echo ( ( $bkx_booking_style == 'day_style' ) ? "selected" : "" ); ?>> Sliding Calendar</option>
                </select>
                <?php if($bkx_booking_style == "" || $bkx_booking_style == 'default'){?>
                    <a href="<?php echo BKX_PLUGIN_PUBLIC_URL; ?>/images/preview/2.jpg" class="bkx-preview-<?php echo $bkx_booking_style;?> thickbox">Preview</a>
                <?php }?>
                <?php if($bkx_booking_style == 'day_style'){?>
                    <a href="<?php echo BKX_PLUGIN_PUBLIC_URL; ?>/images/preview/2b.jpg" class="bkx-preview-<?php echo $bkx_booking_style;?> thickbox">Preview</a>
                <?php }?>
            </td>
        </tr>

		<tr class="active">
			<th scope="row"><label for="Text Colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Text Colour' ); ?></label></th>

			<td class="plugin-description">
				<input type="text" name="bkx_text_color" id="id_bkx_text_color" value="<?php echo bkx_crud_option_multisite('bkx_form_text_color'); ?>">
			</td>
		</tr>
		<!--<tr class="active">
			<th scope="row"><label for="Background Colour"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Background Colour' ); */?></label></th>
			 
			<td class="plugin-description">
				<input type="text" name="bkx_background_color" id="id_bkx_background_color" value="<?php /*echo bkx_crud_option_multisite('bkx_form_background_color'); */?>">
			</td>
		</tr>-->

		<!--<tr class="active">
			<th scope="row"><label for="Border Colour"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Border Colour' ); */?></label></th>
			 
			<td class="plugin-description">
				<input type="text" name="bkx_border_color" id="id_bkx_border_color" value="<?php /*echo bkx_crud_option_multisite('bkx_siteclient_css_border_color'); */?>">
			</td>
		</tr>-->

		<tr class="active">
			<th scope="row"><label for="Active Step Color"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Active Step Colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_progressbar_color" id="id_bkx_progressbar_color" value="<?php echo bkx_crud_option_multisite('bkx_active_step_color'); ?>">
			</td>
		</tr>
 
		<!--<tr class="active">
			<th scope="row"><label for="Calendar Month title color"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Calendar Month title color' ); */?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_month_title_color" id="id_bkx_cal_month_title_color" value="<?php /*echo bkx_crud_option_multisite('bkx_cal_month_title_color'); */?>">
			</td>
		</tr>-->

		<!--<tr class="active">
			<th scope="row"><label for="Calendar Month background color"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Add Calendar Month background color' ); */?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_month_bg_color" id="id_bkx_cal_month_bg_color" value="<?php /*echo bkx_crud_option_multisite('bkx_cal_month_bg_color'); */?>">
			</td>
		</tr>-->

		<!--<tr class="active">
			<th scope="row"><label for="Calendar Border colour"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Add Calendar Border colour option' ); */?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_border_color" id="id_bkx_cal_border_color" value="<?php /*echo bkx_crud_option_multisite('bkx_siteclient_css_cal_border_color'); */?>">
			</td>
		</tr>-->

		<tr class="active">
			<th scope="row"><label for="Calendar day background colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Calendar Day Background Colour' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_day_color" id="id_bkx_cal_day_color" value="<?php echo bkx_crud_option_multisite('bkx_cal_day_color'); ?>">
			</td>
		</tr>
        <tr class="active">
			<th scope="row"><label for="Booking Previous Button"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Previous Button' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_prev_btn" id="id_bkx_prev_btn" value="<?php echo bkx_crud_option_multisite('bkx_prev_btn'); ?>">
			</td>
		</tr>

        <tr class="active">
			<th scope="row"><label for="Booking Next Button"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Next Button' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_next_btn" id="id_bkx_next_btn" value="<?php echo bkx_crud_option_multisite('bkx_next_btn'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Calendar day selected colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Calendar Day Selected' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_cal_day_selected_color" id="id_bkx_cal_day_selected_color" value="<?php echo bkx_crud_option_multisite('bkx_cal_day_selected_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Add time available colour option"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Time available' ); ?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_available_color" id="id_bkx_time_available_color" value="<?php echo bkx_crud_option_multisite('bkx_time_available_color'); ?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Add time selected colour"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Booking Form Selected Time' ); ?></label></th>
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

		<!--<tr class="active">
			<th scope="row"><label for="Time Block BG colour"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Time Block BG colour' ); */?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_block_bg_color" id="id_bkx_time_block_bg_color" value="<?php /*echo bkx_crud_option_multisite('bkx_time_block_bg_color'); */?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Time Block service colour"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Time Block service colour' ); */?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_block_service_color" id="id_bkx_time_block_service_color" value="<?php /*echo bkx_crud_option_multisite('bkx_time_block_service_color'); */?>">
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Time Block extra colour"><?php /*printf( esc_html__( '%1$s', 'bookingx' ),  'Time Block extra colour' ); */?></label></th>
			<td class="plugin-description">
				<input type="text" name="bkx_time_block_extra_color" id="id_bkx_time_block_extra_color" value="<?php /*echo bkx_crud_option_multisite('bkx_time_block_extra_color'); */?>">
			</td>
		</tr>-->
    <?php do_action('bkx_general_styling_settings');?>
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
        <input type="hidden" name="bkx_setting_form_init" value="1">
		<tr class="active">
			<th scope="row"><label for="Cancel Booking"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Enable Cancel Booking' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="enable_cancel_booking" id="id_enable_cancel_booking_yes" value="1" <?php if(bkx_crud_option_multisite('enable_cancel_booking')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?>
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
					<input type="radio" name="enable_any_seat" id="id_enable_any_seat_yes" value="1" <?php if(bkx_crud_option_multisite('enable_any_seat')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?>
					<input type="radio" name="enable_any_seat" id="id_enable_any_seat_no" value="0" <?php if(bkx_crud_option_multisite('enable_any_seat')==0) echo "checked"; ?>> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'No' ); ?>
				</div>
			</td>
		</tr>
	
		<tr class="active">
			<th scope="row"><label for="edit and delete bookings"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Enable customer dashboard' ); ?></label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="reg_customer_crud_op" id="id_reg_customer_crud_op_yes" value="1" <?php if(bkx_crud_option_multisite('reg_customer_crud_op')==1) echo "checked"; ?>><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Yes' ); ?>
					<input type="radio" name="reg_customer_crud_op" id="id_reg_customer_crud_op_no" value="0" <?php if(bkx_crud_option_multisite('reg_customer_crud_op')==0) echo "checked"; ?>> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'No' ); ?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="dashboard column's bookings"><?php printf( esc_html__( '%1$s', 'bookingx' ),  'Dashboard Column\'s' ); ?></label></th>
				<td class="plugin-description">
				<div class="plugin-description">
				<?php
				$bkx_dashboard_column_selected = bkx_crud_option_multisite("bkx_dashboard_column");
				if(empty($bkx_dashboard_column_selected)){
					$bkx_dashboard_column_selected = $Bookingx_Admin->bkx_booking_columns_data();
				}
				$bkx_dashboard_column_html = "";
				$bkx_booking_columns = $Bookingx_Admin->bkx_booking_columns_data();
				if(!empty($bkx_booking_columns)){
					foreach ($bkx_booking_columns as $key => $booking_columns) {
					 	if($key != "cb") {
					 		$bkx_booking_column_checked = ( !empty($bkx_dashboard_column_selected) && in_array($key, $bkx_dashboard_column_selected) ? " checked='checked' " : '');
					 		$bkx_dashboard_column_html .= '<input type="checkbox" class="bkx-dashboard-column" name="bkx_dashboard_column[]" value="'.$key.'" '.$bkx_booking_column_checked.'>'.$booking_columns.'<br />';
					 	}
					}
				}
				$count_bkx_dashboard_column_selected = sizeof( $bkx_dashboard_column_selected );
				$count_bkx_booking_columns = sizeof( $bkx_booking_columns )-1;
                $column_checked = "";
				if( $count_bkx_dashboard_column_selected ==  $count_bkx_booking_columns ){
			 		$column_checked ="checked";
			 	}
				?>
				<input type="checkbox" class="bkx-dashboard-column-all" <?php echo $column_checked; ?>> Select All <br />
				<?php echo $bkx_dashboard_column_html;?>
				</div>
			</td>
		</tr>

		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
			</td>
		</tr>
        <?php do_action('bkx_general_other_settings');?>
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
                <input type="hidden" name="bkx_setting_form_init" value="1">
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
                <input type="hidden" name="bkx_setting_form_init" value="1">
			</td>
			</tr>
</table>
<p class="submit"><input type="submit" value="Import Xml" class='button-primary' name="import_xml"></p>
</form>
</div>
<!--End Import Functionality-->
<?php endif;
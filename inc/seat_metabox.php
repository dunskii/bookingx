<?php
add_action( 'add_meta_boxes', 'add_bkx_seat_metaboxes' );
function add_bkx_seat_metaboxes()
{
    $alias_seat = get_option('bkx_alias_seat', "default");
    add_meta_box('bkx_seat_boxes',__("$alias_seat Details", 'bookingx'),'bkx_seat_boxes_metabox_callback','bkx_seat','normal');
}
 
function bkx_seat_boxes_metabox_callback($post)
{   
    wp_nonce_field('bkx_seat_boxes_metabox','bkx_seat_boxes_metabox_nonce' );
    if ( is_multisite() ) 
    {
        $current_blog_id = get_current_blog_id();
        $alias_seat = get_blog_option($current_blog_id,'bkx_alias_seat');
    }
    else
    {
        $alias_seat = get_option('bkx_alias_seat');
    }
    $values = get_post_custom( $post->ID );
    if(!empty($values) && !is_wp_error($values)){
    $seat_street = isset( $values['seat_street'] ) ? esc_attr( $values['seat_street'][0] ) : "";   
    $seat_city = isset( $values['seat_city'] ) ? esc_attr( $values['seat_city'][0] ) : "";
    $seat_state = isset( $values['seat_state'] ) ? esc_attr( $values['seat_state'][0] ) : "";
    $seat_zip = isset( $values['seat_zip'] ) ? esc_attr( $values['seat_zip'][0] ) : "";
    $seat_country = isset( $values['seat_country'] ) ? esc_attr( $values['seat_country'][0] ) : "";
    $seat_is_certain_month = $values['seat_is_certain_month'][0];
    $seat_months = isset($values['seat_months'][0]) && $values['seat_months'][0]!='' ? $temp_seat_months = explode(',',$values['seat_months'][0]) :  '';
    $seat_days = isset($values['seat_days'][0]) && $values['seat_days'][0]!='' ? $temp_seat_days = explode(',',$values['seat_days'][0]) :  '';
    $seat_is_certain_day = $values['seat_is_certain_day'][0];
    $seat_days_time = maybe_unserialize($values['seat_days_time'][0]);
    $res_seat_time= maybe_unserialize($seat_days_time);
    
    if(!empty($res_seat_time)){
        $res_seat_time_arr = array();
	foreach($res_seat_time as $temp)
	{
		$res_seat_time_arr[strtolower($temp['day'])]['time_from'] = $temp['time_from']; 				
                $res_seat_time_arr[strtolower($temp['day'])]['time_till'] = $temp['time_till'];
	}
    }
    $seat_is_pre_payment = isset( $values['seatIsPrePayment'] ) ? esc_attr( $values['seatIsPrePayment'][0] ) : "";
    $seat_payment_type = isset( $values['seat_payment_type'] ) ? esc_attr( $values['seat_payment_type'][0] ) : ""; //Payment Type
    
    $seat_deposite_type = isset( $values['seat_payment_option'] ) ? esc_attr( $values['seat_payment_option'][0] ) : ""; // Deposite Type
    $seat_fixed_amount = isset( $values['seat_amount'] ) ? esc_attr( $values['seat_amount'][0] ) : "";
    $seat_percentage = isset( $values['seat_percentage'] ) ? esc_attr( $values['seat_percentage'][0] ) : "";
    $seat_phone = isset( $values['seatPhone'] ) ? esc_attr( $values['seatPhone'][0] ) : "";
    $seat_notification_email = isset( $values['seatEmail'] ) ? esc_attr( $values['seatEmail'][0] ) : "";
    $seat_notification_alternate_email = isset( $values['seatIsAlternateEmail'] ) ? esc_attr( $values['seatIsAlternateEmail'][0] ) : "";
    $seatAlternateEmail = isset( $values['seatAlternateEmail'] ) ? esc_attr( $values['seatAlternateEmail'][0] ) : "";
    $seat_ical_address = isset( $values['seatIsIcal'] ) ? esc_attr( $values['seatIsIcal'][0] ) : "";
    $seatIcalAddress = isset( $values['seatIcalAddress'] ) ? esc_attr( $values['seatIcalAddress'][0] ) : "";
    }    
    custom_load_scripts(array('jquery-1.7.1.js','jquery-ui.js','jquery.timePicker.js','timepicker_implementation_1.js'));
    color_load_scripts(array('iris.min.js'));
    custom_load_styles(array('basic-style.css','redmond/jquery-ui-timepicker.css','bkx-admin.css'));
    ?>

<div class="error" id="error_list" style="display:none;"></div>
    <p><label for="seat_street"><?php esc_html_e( 'Street name', 'bookingx' ); ?> : </label>
        <textarea name="seat_street" id="seat_street"><?php echo esc_attr( $seat_street );  ?></textarea>
    </p>

    <p><label for="seat_city"><?php esc_html_e( 'City', 'bookingx' ); ?> :</label>
    <input type="text" name="seat_city" id="seat_city" value="<?php echo esc_attr( $seat_city ); ?>" />
    </p>

    <p><label for="seat_state"><?php esc_html_e( 'State', 'bookingx' ); ?> :</label>
    <input type="text" name="seat_state" id="seat_state" value="<?php echo esc_attr( $seat_state ); ?>" />
    </p>

    <p><label for="seat_zip"><?php esc_html_e( 'Zip / Postal Code', 'bookingx' ); ?> :</label>
    <input type="text" name="seat_zip" id="seat_zip" value="<?php echo esc_attr( $seat_zip ); ?>" />
    </p>

    <p><label for="seat_country"><?php esc_html_e( 'Country', 'bookingx' ); ?> :</label>
    <input type="text" name="seat_country" id="seat_country" value="<?php echo esc_attr( $seat_country ); ?>" />
    </p>
    <strong> <?php esc_html_e( 'Set Times, Days and Months Available', 'bookingx' ); ?></strong>
    <p><label for="seat_country"><?php esc_html_e( 'Will this Seat only be available certain months of the year', 'bookingx' ); ?> :</label></p>
    <p><input type="radio" name="seat_is_certain_month" value="Y" <?php if($seat_is_certain_month=="Y"){ echo "checked='checked'"; } ?> />Yes 
        <input type="radio" name="seat_is_certain_month" value="N" <?php if($seat_is_certain_month=="N"){ echo "checked='checked'"; } ?>  <?php if(!isset($seat_is_certain_month)){ echo "checked='checked'"; } ?>/>No</p>
    <p><?php esc_html_e( '(Note: selection of \'No\' indicates that it will be available throughout the year.)', 'bookingx' ); ?></p>

    <div id="certain_month"> 
        <label><?php esc_html_e( 'If yes, Select months of the year', 'bookingx' ); ?> :</label>

				<ul class="gfield_checkbox" id="input_1_16">
				<li class="gchoice_16_0">
				<input name="seat_certain_month_all" type="checkbox" value="January" id="choice_month_all" tabindex="31" >
				<label for="choice_16_0"><?php esc_html_e( 'All', 'bookingx' ); ?></label>
				</li>

				<li class="gchoice_16_1">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="January" id="choice_16_1" tabindex="31" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("January",$temp_seat_months)){ echo
"checked='checked'"; } } ?> >
				<label for="choice_16_1"><?php esc_html_e( 'January', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_2">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="February" id="choice_16_2" tabindex="32" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("February",$temp_seat_months)){ echo
"checked='checked'"; } } ?> >
				<label for="choice_16_2"><?php esc_html_e( 'February', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_3">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="March" id="choice_16_3" tabindex="33" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("March",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_3"><?php esc_html_e( 'March', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_4">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="April" id="choice_16_4" tabindex="34" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("April",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_4"><?php esc_html_e( 'April', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_5">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="May" id="choice_16_5" tabindex="35" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("May",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_5"><?php esc_html_e( 'May', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_6">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="June" id="choice_16_6" tabindex="36" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("June",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_6"><?php esc_html_e( 'June', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_7">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="July" id="choice_16_7" tabindex="37" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("July",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_7"><?php esc_html_e( 'July', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_8">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="August" id="choice_16_8" tabindex="38" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("August",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_8"><?php esc_html_e( 'August', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_9">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="September" id="choice_16_9" tabindex="39" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("September",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_9"><?php esc_html_e( 'September', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_11">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="October" id="choice_16_11" tabindex="40" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("October",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_11"><?php esc_html_e( 'October', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_12">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="November" id="choice_16_12" tabindex="41" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("November",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_12"><?php esc_html_e( 'November', 'bookingx' ); ?></label>
				</li>
				<li class="gchoice_16_13">
				<input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="December" id="choice_16_13" tabindex="42" <?php if(isset($temp_seat_months) && ($temp_seat_months!='')){ if(in_array("December",$temp_seat_months)){ echo
"checked='checked'"; } } ?>>
				<label for="choice_16_13"><?php esc_html_e( 'December', 'bookingx' ); ?></label>
				</li>
				</ul>
			 
    </div>
  <p><?php printf( esc_html__( 'Will %1$s be available at certain days only ', 'bookingx' ), $alias_seat ); ?></p>
    <p><div class="plugin-description">
<input type="radio" name="seat_is_certain_day" value="Y" <?php if(isset($seat_is_certain_day) && $seat_is_certain_day!='' &&  $seat_is_certain_day=="Y"){ echo "checked='checked'"; } ?> />Yes 
<input type="radio" name="seat_is_certain_day" value="N" <?php if(isset($seat_is_certain_day) && $seat_is_certain_day!='' &&  $seat_is_certain_day=="N"){ echo "checked='checked'"; } ?>  <?php if(!isset($seat_is_certain_day)){ echo "checked='checked'"; } ?> />No
<p><?php esc_html_e( '(Note: selection of \'No\' indicates that it will be available 7 days a week, and 24 hours a day)', 'bookingx' ); ?></p>
    </div></p>
    <div id="certain_day">
    <p><?php esc_html_e( 'If yes, Select days of the week :', 'bookingx' ); ?></p>
	<div class="plugin-description">
				<ul class="gfield_checkbox ul-timepicker" id="input_1_10">
				<li class="gchoice_10_1">
				<input name="seat_certain_day[]" type="checkbox" value="Sunday" id="id_choice_sunday" tabindex="22" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Sunday",$temp_seat_days)){ echo "checked='checked'"; } } ?> >
				<label for="choice_10_1"><?php esc_html_e( 'Sunday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" class="add_seat_time_from" name="seat_time_from_sunday" id="id_seat_time_from_sunday" value="<?php if(isset($res_seat_time_arr['sunday']['time_from']) && ($res_seat_time_arr['sunday']['time_from']!='')){ echo $res_seat_time_arr['sunday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" class="add_seat_time_till" name="seat_time_till_sunday"  id="id_seat_time_till_sunday" value="<?php if(isset($res_seat_time_arr['sunday']['time_till']) && ($res_seat_time_arr['sunday']['time_till']!='')){ echo $res_seat_time_arr['sunday']['time_till']; } ?>" />
				</li>
				<li class="gchoice_10_2">
				<input name="seat_certain_day[]" type="checkbox" value="Monday" id="id_choice_monday" tabindex="23" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Monday",$temp_seat_days)){ echo "checked='checked'"; } } ?> >
				<label for="choice_10_2"><?php esc_html_e( 'Monday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" class="add_seat_time_from" name="seat_time_from_monday" id="id_seat_time_from_monday" value="<?php if(isset($res_seat_time_arr['monday']['time_from']) && ($res_seat_time_arr['monday']['time_from']!='')){ echo $res_seat_time_arr['monday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" class="add_seat_time_till" name="seat_time_till_monday"  id="id_seat_time_till_monday" value="<?php if(isset($res_seat_time_arr['monday']['time_till']) && ($res_seat_time_arr['monday']['time_till']!='')){ echo $res_seat_time_arr['monday']['time_till']; } ?>" />
				</li>
				<li class="gchoice_10_3">
				<input name="seat_certain_day[]" type="checkbox" value="Tuesday" id="id_choice_tuesday" tabindex="24" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Tuesday",$temp_seat_days)){ echo "checked='checked'"; } } ?>>
				<label for="choice_10_3"><?php esc_html_e( 'Tuesday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" name="seat_time_from_tuesday" id="id_seat_time_from_tuesday" value="<?php if(isset($res_seat_time_arr['tuesday']['time_from']) && ($res_seat_time_arr['tuesday']['time_from']!='')){ echo $res_seat_time_arr['tuesday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" name="seat_time_till_tuesday"  id="id_seat_time_till_tuesday" value="<?php if(isset($res_seat_time_arr['tuesday']['time_till']) && ($res_seat_time_arr['tuesday']['time_till']!='')){ echo $res_seat_time_arr['tuesday']['time_till']; } ?>" />
				</li>
				<li class="gchoice_10_4">
				<input name="seat_certain_day[]" type="checkbox" value="Wednesday" id="id_choice_wednesday" tabindex="25" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Wednesday",$temp_seat_days)){ echo "checked='checked'"; } } ?>>
				<label for="choice_10_4"><?php esc_html_e( 'Wednesday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" name="seat_time_from_wednesday" id="id_seat_time_from_wednesday" value="<?php if(isset($res_seat_time_arr['wednesday']['time_from']) && ($res_seat_time_arr['wednesday']['time_from']!='')){ echo $res_seat_time_arr['wednesday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" name="seat_time_till_wednesday"  id="id_seat_time_till_wednesday" value="<?php if(isset($res_seat_time_arr['wednesday']['time_till']) && ($res_seat_time_arr['wednesday']['time_till']!='')){ echo $res_seat_time_arr['wednesday']['time_till']; } ?>" />
				</li>
				<li class="gchoice_10_5">
				<input name="seat_certain_day[]" type="checkbox" value="Thursday" id="id_choice_thursday" tabindex="26" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Thursday",$temp_seat_days)){ echo "checked='checked'"; } } ?>>
				<label for="choice_10_5"><?php esc_html_e( 'Thursday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" name="seat_time_from_thursday" id="id_seat_time_from_thursday" value="<?php if(isset($res_seat_time_arr['thursday']['time_from']) && ($res_seat_time_arr['thursday']['time_from']!='')){ echo $res_seat_time_arr['thursday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" name="seat_time_till_thursday"  id="id_seat_time_till_thursday" value="<?php if(isset($res_seat_time_arr['thursday']['time_till']) && ($res_seat_time_arr['thursday']['time_till']!='')){ echo $res_seat_time_arr['thursday']['time_till']; } ?>" />
				</li>
				<li class="gchoice_10_6">
				<input name="seat_certain_day[]" type="checkbox" value="Friday" id="id_choice_friday" tabindex="27" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Friday",$temp_seat_days)){ echo "checked='checked'"; } } ?>>
				<label for="choice_10_6"><?php esc_html_e( 'Friday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" name="seat_time_from_friday" id="id_seat_time_from_friday" value="<?php if(isset($res_seat_time_arr['friday']['time_from']) && ($res_seat_time_arr['friday']['time_from']!='')){ echo $res_seat_time_arr['friday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" name="seat_time_till_friday"  id="id_seat_time_till_friday" value="<?php if(isset($res_seat_time_arr['friday']['time_till']) && ($res_seat_time_arr['friday']['time_till']!='')){ echo $res_seat_time_arr['friday']['time_till']; } ?>" />
				</li>
				<li class="gchoice_10_7">
				<input name="seat_certain_day[]" type="checkbox" value="Saturday" id="id_choice_saturday" tabindex="28" <?php if(isset($temp_seat_days)&& ($temp_seat_days!="")){ if(in_array("Saturday",$temp_seat_days)){ echo "checked='checked'"; } } ?>>
				<label for="choice_10_7"><?php esc_html_e( 'Saturday', 'bookingx' ); ?></label>
					<?php esc_html_e( 'From', 'bookingx' ); ?>
					<input type="text" name="seat_time_from_saturday" id="id_seat_time_from_saturday" value="<?php if(isset($res_seat_time_arr['saturday']['time_from']) && ($res_seat_time_arr['saturday']['time_from']!='')){ echo $res_seat_time_arr['thursday']['time_from']; } ?>" />
					<?php esc_html_e( 'Till', 'bookingx' ); ?>
					<input type="text" name="seat_time_till_saturday"  id="id_seat_time_till_saturday" value="<?php if(isset($res_seat_time_arr['saturday']['time_till']) && ($res_seat_time_arr['saturday']['time_till']!='')){ echo $res_seat_time_arr['saturday']['time_till']; } ?>" />
				</li>
				</ul>
			</div>
    </div>
    <p><strong><?php esc_html_e( 'Payment Options', 'bookingx' ); ?></strong></p>
    <p><?php printf( esc_html__( 'Will the %1$sbooking require pre payment : ', 'bookingx' ),$alias_seat); ?></p>
    <div class="plugin-description">
				<input type="radio" name="seat_is_pre_payment" value="Y" <?php if(isset($seat_is_pre_payment) && ($seat_is_pre_payment!='')){if($seat_is_pre_payment=='Y'){ echo "checked='checked'";}} ?> />Yes 
				<input type="radio" name="seat_is_pre_payment" value="N" <?php if(isset($seat_is_pre_payment) && ($seat_is_pre_payment!='')){if($seat_is_pre_payment=='N'){ echo "checked='checked'";}} ?>  <?php if(!isset($seat_is_pre_payment)){ echo "checked='checked'"; } ?>/>No
			</div>
    <div id="deposit_fullpayment">
        <p><?php esc_html_e( 'Will payment be deposit or full payment', 'bookingx' ); ?></p>        
			<div class="plugin-description">
				<select name="seat_deposit_full" id="id_seat_deposit_full" onchange="hide_fixed_percentage(this.value);" class="medium gfield_select" tabindex="76">
				    <option value="Full Payment"><?php esc_html_e( 'Select', 'bookingx' ); ?></option>
					<option value="Deposit" <?php if(isset($seat_payment_type) && ($seat_payment_type!='')){if($seat_payment_type=='D'){ echo "selected='selected'";}} ?>><?php esc_html_e( 'Deposit', 'bookingx' ); ?></option>
					<option value="Full Payment" <?php if(isset($seat_payment_type) && ($seat_payment_type!='')){if($seat_payment_type=='FP'){ echo "selected='selected'";}} ?>><?php esc_html_e( 'Full Payment', 'bookingx' ); ?></option>
				</select>
			</div>
    </div>
    
    <div class="active" id="fixed_percentage">
        <p><?php esc_html_e( 'Is deposit a fixed amount ($) or percentage (%)? :', 'bookingx' ); ?></p>		
			<div class="plugin-description">
				<select name="seat_payment_option" id="id_seat_payment_type" onchange="change_amount_option(this.value);" >
					<option value="FixedAmount" <?php if(isset($seat_deposite_type) && ($seat_deposite_type!='')){if($seat_deposite_type=='FA'){ echo "selected='selected'";}} ?> ><?php esc_html_e( 'Fixed Amount ($)', 'bookingx' ); ?></option>
					<option value="Percentage" <?php if(isset($seat_deposite_type) && ($seat_deposite_type!='')){if($seat_deposite_type=='P'){ echo "selected='selected'";}} ?> ><?php esc_html_e( 'Percentage (%)', 'bookingx' ); ?></option>
				</select>
			</div>
    </div>
    <div  id="fixedamount">
        <p><?php esc_html_e( 'Enter Amount ($):', 'bookingx' ); ?></p>
			<div class="plugin-description">
				<input type="text" name="seat_amount" value="<?php if(isset($seat_fixed_amount) && ($seat_fixed_amount!='')){ echo $seat_fixed_amount; } ?>">
			</div>
    </div>
    <div  id="percentage">
        <p><?php esc_html_e( 'Enter Percentage (%):', 'bookingx' ); ?></p>
		<div class="plugin-description">
				<input type="text" name="seat_percentage" value="<?php if(isset($seat_percentage) && ($seat_percentage!='')){ echo $seat_percentage; } ?>">
			</div>
    </div>
    <p><strong><?php esc_html_e( 'User Options', 'bookingx' ); ?></strong></p>
    <p><?php printf( esc_html__( 'Do you want to associate this %1$s with a user ? : ', 'bookingx' ),$alias_seat); ?></p>
    <select name="selOpt" id="selOpt">
					<option value="N"><?php esc_html_e( 'NO', 'bookingx' ); ?></option>
					<option value="Y" <?php if(isset($user_type) && ($user_type!='')){
						echo "selected='selected'";} ?>><?php esc_html_e( 'YES', 'bookingx' ); ?></option>
    </select>
    
    <div class="active" id="selRoles" style="<?php if(isset($user_type) && ($user_type!='')){echo "display:table-row;";}else{echo "display:none";} ?>">
       <?php esc_html_e( 'Please select user type :', 'bookingx' ); ?> 
			<div class="plugin-description">
				<select id="role" name="role">
					<option value=""> <?php esc_html_e( 'Select Role', 'bookingx' ); ?></option>
					<?php 
					    global $wp_roles;
					    $roles = $wp_roles->get_names(); // Below code will print the all list of roles.
					    foreach($roles as $key => $role){   echo $user_type;  
					?>
						<option value="<?php echo $key; ?>"
						<?php if(isset($user_type) && ($user_type ==$key) ){echo "selected='selected'";} ?>
						><?php echo $role; ?></option>
					<?php  } ?>
				</select>
			</div>			
    </div>
    
    <div class="active" id="selUsers" style="<?php if(isset($user_name) && ($user_name!='')){echo "display:table-row;";}else{echo "display:none";} ?>"> <?php esc_html_e( 'Please select user :', 'bookingx' ); ?>
            <div class="plugin-description">
                    <select id="users" name="users">
                            <option value="<?php echo $user_name; ?>"><?php echo $user_name; ?></option>
                    </select>

            </div>
    </div>
    <p><strong><?php esc_html_e( 'Notification Details', 'bookingx' ); ?></strong></p>
    
    <p><?php esc_html_e( 'Phone :', 'bookingx' ); ?></p>
    <p><input type="text" name="seat_phone" id="id_seat_phone" value="<?php if(isset($seat_phone) && ($seat_phone!='')){ echo $seat_phone; } ?>" /></p>
    
    <p><?php esc_html_e( 'Email :', 'bookingx' ); ?></p>
    <p><input type="text" name="seat_email" id="id_seat_email" value="<?php if(isset($seat_notification_email) && ($seat_notification_email!='')){ echo $seat_notification_email; } ?>" /></p>
    
    <p><input name="seat_is_alternate_email" type="checkbox" onclick="display_secondary_email(this);" value="Yes" id="id_seat_is_alternate_email" tabindex="83" <?php if(isset($seat_notification_alternate_email) && ($seat_notification_alternate_email!='')){ echo "checked='checked'"; } ?> ><?php esc_html_e( 'Would you like to notify a second email address? :', 'bookingx' ); ?></p>

        <div class="active" id="secondary_email" style="display:none;">
	 <?php esc_html_e( 'Secondary Email :', 'bookingx' ); ?>
			<div class="plugin-description">
				<input type="text" name="seat_alternate_email" id="id_seat_alternate_email" value="<?php if(isset($seat_notification_alternate_email) && ($seat_notification_alternate_email!='')){ echo $seatAlternateEmail; } ?>" />
			</div>
	</div>
        <p><input name="seat_is_ical" type="checkbox" onclick="display_ical_address(this);" value="Yes" id="id_seat_is_ical" tabindex="83" <?php if(isset($seat_ical_address) && ($seat_ical_address!='')){ echo "checked='checked'"; } ?> >
            <?php esc_html_e( 'Do you want a Google Calendar address to enable you to view the bookings in Google calendar etc? :', 'bookingx' ); ?></p>
 
	<div class="active" id="ical_address" style="display:none;">
		<?php esc_html_e( 'Google Calendar ID :', 'bookingx' ); ?>
			<div class="plugin-description">
				<input name="seat_ical_address" type="text"  value="<?php if(isset($seatIcalAddress) && ($seatIcalAddress!='')){ echo $seatIcalAddress; } ?>" id="id_seat_ical_address" size="60" >
			</div>
	</div>
        <input type="hidden" name="is_user_valid" id="id_is_user_valid">
        <input type="hidden" name="seat_alias" id="seat_alias" value="<?php echo $alias_seat; ?>">
		
<?php
}

add_action( 'save_post', 'save_bkx_seat_metaboxes',3,10 );
function save_bkx_seat_metaboxes( $post_id, $post, $update )
{

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
    
    if($post->post_type!='bkx_seat')  return;
    if ( is_multisite() ) 
    {
        $current_blog_id = get_current_blog_id();
        switch_to_blog( $current_blog_id );
    }
    $seatCertainMonth      = $_POST['seat_certain_month'];
    $seatCertainDay        = $_POST['seat_certain_day'];
    $seatIsPrePayment      = trim($_POST['seat_is_pre_payment']);
    $seatDepositFull     = trim($_POST['seat_deposit_full']);
    $seatPaymentOption   = trim($_POST['seat_payment_option']);
    $seat_amount           = trim($_POST['seat_amount']);
    $seat_percentage       = trim($_POST['seat_percentage']);
    $seatPhone             = trim($_POST['seat_phone']);
    $seatEmail             = trim($_POST['seat_email']);
    $seatIsAlternateEmail  = trim($_POST['seat_is_alternate_email']);
    $seatAlternateEmail    = trim($_POST['seat_alternate_email']);
    $seatIsIcal            = trim($_POST['seat_is_ical']);
    $seatIcalAddress       = trim($_POST['seat_ical_address']);
 
    
     if($seatDepositFull=="Deposit")
		{
			$tempDepositFull = "D";
		}
		else if($seatDepositFull=="Full Payment")
		{
			$tempDepositFull = "FP";
		}
		$seatDepositFull = $tempDepositFull;

		if($seatPaymentOption=="FixedAmount")
		{
			$tempPaymentOption = "FA";
		}
		elseif($seatPaymentOption=="Percentage")
		{

			$tempPaymentOption = "P";
		}
		$seatPaymentOption = $tempPaymentOption;
                
                
    $seatCertainMonthTemp = '';
    if(sizeof($seatCertainMonth)>0)
    {
            foreach($seatCertainMonth as $key=>$temp)
            {
                    $seatCertainMonthTemp .=$temp.",";
            }
            $seatCertainMonth = $seatCertainMonthTemp;
    }
    else
    {
            $seatCertainMonth = "";
    }
    
    $seatCertainDayTemp = '';
    if(sizeof($seatCertainDay)>0)
    {
            $arr_seat_time = array();
            foreach($seatCertainDay as $key=>$seat_temp)
            {
                    $arr_seat_time[$key]['day'] = strtolower($seat_temp);
                    $arr_seat_time[$key]['time_from'] = $_POST['seat_time_from_'.strtolower($seat_temp)];
                    $arr_seat_time[$key]['time_till'] = $_POST['seat_time_till_'.strtolower($seat_temp)];
            }
            foreach($seatCertainDay as $key=>$temp)
            {
                    $seatCertainDayTemp .=$temp.",";
            }
            $seatCertainDay = $seatCertainDayTemp;
    }
    else
    {
            $seatCertainDay = "";
    }
    
    if(sizeof($arr_seat_time)>0)
            {
                $seat_days_time = array();
                foreach($arr_seat_time as $key => $temp_seat_days){					
                        if($temp_seat_days['time_from']=="" || $temp_seat_days['time_till']==""){
                                $temp_seat_days['time_from'] = "12:00 AM";
                                $temp_seat_days['time_till'] = "12:00 PM";
                                $seat_days_time[] =$temp_seat_days;
                        }else{
                            $seat_days_time[]=$temp_seat_days;
                        }                      
                }
                $seat_days_time_data = $seat_days_time;
            }       
     // Make sure your data is set before trying to save it
    if( isset( $_POST['seat_street'] ) )
        update_post_meta( $post_id, 'seat_street', esc_attr( $_POST['seat_street'] ) );
         
    if( isset( $_POST['seat_city'] ) )
        update_post_meta( $post_id, 'seat_city', esc_attr( $_POST['seat_city'] ) );
    
    if( isset( $_POST['seat_state'] ) )
        update_post_meta( $post_id, 'seat_state', esc_attr( $_POST['seat_state'] ) );
         
    if( isset( $_POST['seat_zip'] ) )
        update_post_meta( $post_id, 'seat_zip', esc_attr( $_POST['seat_zip'] ) );
    
    if( isset( $_POST['seat_country'] ) )
        update_post_meta( $post_id, 'seat_country', esc_attr( $_POST['seat_country'] ) );
         
    if( isset( $_POST['seat_city'] ) )
        update_post_meta( $post_id, 'seat_city', esc_attr( $_POST['seat_city'] ) );
    
    if( isset( $_POST['seat_is_certain_month'] ) )
        update_post_meta( $post_id, 'seat_is_certain_month', esc_attr( $_POST['seat_is_certain_month'] ) );
    
    if( isset( $seatCertainMonth ) )
        update_post_meta( $post_id, 'seat_months', esc_attr( $seatCertainMonth ) );
    
    if( isset( $_POST['seat_is_certain_day'] ) )
        update_post_meta( $post_id, 'seat_is_certain_day', esc_attr( $_POST['seat_is_certain_day'] ) );
    
    if( isset( $seatCertainDay ) )
        update_post_meta( $post_id, 'seat_days',  $seatCertainDay);
    
    if( isset( $seat_days_time_data ) )
        update_post_meta( $post_id, 'seat_days_time',$seat_days_time_data );
    
    if( isset( $seatIsPrePayment ) )
        update_post_meta( $post_id, 'seatIsPrePayment',  $seatIsPrePayment);
    
    if( isset( $seatDepositFull ) )
        update_post_meta( $post_id, 'seat_payment_type',  $seatDepositFull);
    
    if( isset( $seatPaymentOption ) )
        update_post_meta( $post_id, 'seat_payment_option',$seatPaymentOption );
    
    if( isset( $seat_amount ) )
        update_post_meta( $post_id, 'seat_amount',  $seat_amount);    
    
    if( isset( $seat_percentage ) )
        update_post_meta( $post_id, 'seat_percentage',  $seat_percentage);    
    
    if( isset( $seatPhone ) )
        update_post_meta( $post_id, 'seatPhone',$seatPhone );
    
    if( isset( $seatEmail ) )
        update_post_meta( $post_id, 'seatEmail',  $seatEmail);
    
    if( isset( $seatIsAlternateEmail ) )
        update_post_meta( $post_id, 'seatIsAlternateEmail',$seatIsAlternateEmail );
    
    if( isset( $seatAlternateEmail ) )
        update_post_meta( $post_id, 'seatAlternateEmail',$seatAlternateEmail );
    
    if( isset( $seatIsIcal ) )
        update_post_meta( $post_id, 'seatIsIcal',  $seatIsIcal);
    
    if( isset( $seatIcalAddress ) )
        update_post_meta( $post_id, 'seatIcalAddress',$seatIcalAddress );  
    
    
    /**
     * Create New User and Role as Resource by default or Get by get_option('bkx_seat_role');
     */
    if ( is_multisite() ) 
    {
        $bkx_seat_role = get_blog_option($current_blog_id,'bkx_seat_role');
    }
    else
    {
         $bkx_seat_role = get_option('bkx_seat_role');
    }
    if(isset($bkx_seat_role) && $bkx_seat_role!=''){
        $role = $bkx_seat_role;
    }else{
       $role = 'resource';
    }
        $seat_wp_user_id = get_post_meta( $post_id, 'seat_wp_user_id',true );
        $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );

        if ( is_multisite() ) 
        {

        	$SeatWpuserObj = get_blogs_of_user($seat_wp_user_id);
        	if(empty($SeatWpuserObj)){
        		$user_id = wpmu_create_user($seatEmail, $random_password, $seatEmail);
        	}
        	else
        	{
        		$user_id = $seat_wp_user_id;
        	}
        	
        }
        else
        {
        			$SeatWpuserObj = WP_User::get_data_by( 'ID', $seat_wp_user_id );

		        	 if ( $SeatWpuserObj === false || empty($SeatWpuserObj)) {
		            //user id does not exist
		                $check_user_id = username_exists( $seatEmail );
		                if ( !$check_user_id && email_exists($seatEmail) == false ) {
		                        
		                        $user_id = wp_create_user( $seatEmail, $random_password, $seatEmail );
		                } else {
		                        $random_password = __('User already exists.  Password inherited.');
		                }
			        } else {
			            $user_id= $seat_wp_user_id;
			        }
        }
 

        update_post_meta( $post_id, 'seat_wp_user_id',$user_id ); 


   
    //$seatEmail
     
        if(isset($user_id) && $user_id!='')
        {
            $userdata = array(
                    'ID' => $user_id,
                    'first_name'    =>  $_POST['post_title'],
                    'role'          =>  $role ,
                    'display_name'  => $_POST['post_title'],                
            );

            $user_id = wp_update_user( $userdata);

            update_user_meta( $user_id, 'seat_post_id',$post_id ); 

            if( isset( $_POST['seat_street'] ) )
                update_user_meta( $user_id, 'seat_street', esc_attr( $_POST['seat_street'] ) );

            if( isset( $_POST['seat_city'] ) )
                update_user_meta( $user_id, 'seat_city', esc_attr( $_POST['seat_city'] ) );

            if( isset( $_POST['seat_state'] ) )
                update_user_meta( $user_id, 'seat_state', esc_attr( $_POST['seat_state'] ) );

            if( isset( $_POST['seat_zip'] ) )
                update_user_meta( $user_id, 'seat_zip', esc_attr( $_POST['seat_zip'] ) );

            if( isset( $_POST['seat_country'] ) )
                update_user_meta( $user_id, 'seat_country', esc_attr( $_POST['seat_country'] ) );

            if( isset( $_POST['seat_city'] ) )
                update_user_meta( $user_id, 'seat_city', esc_attr( $_POST['seat_city'] ) );

            if( isset( $_POST['seat_is_certain_month'] ) )
                update_user_meta( $user_id, 'seat_is_certain_month', esc_attr( $_POST['seat_is_certain_month'] ) );

            if( isset( $seatCertainMonth ) )
                update_user_meta( $user_id, 'seat_months', esc_attr( $seatCertainMonth ) );

            if( isset( $_POST['seat_is_certain_day'] ) )
                update_user_meta( $user_id, 'seat_is_certain_day', esc_attr( $_POST['seat_is_certain_day'] ) );

            if( isset( $seatCertainDay ) )
                update_user_meta( $user_id, 'seat_days',  $seatCertainDay);

            if( isset( $seat_days_time_data ) )
                update_user_meta( $user_id, 'seat_days_time',$seat_days_time_data );

            if( isset( $seatIsPrePayment ) )
                update_user_meta( $user_id, 'seatIsPrePayment',  $seatIsPrePayment);

            if( isset( $seatDepositFull ) )
                update_user_meta( $user_id, 'seat_payment_type',  $seatDepositFull);

            if( isset( $seatPaymentOption ) )
                update_user_meta( $user_id, 'seat_payment_option',$seatPaymentOption );

            if( isset( $seat_amount ) )
                update_user_meta( $user_id, 'seat_amount',  $seat_amount);    

            if( isset( $seat_percentage ) )
                update_user_meta( $user_id, 'seat_percentage',  $seat_percentage);    

            if( isset( $seatPhone ) )
                update_user_meta( $user_id, 'seatPhone',$seatPhone );

            if( isset( $seatEmail ) )
                update_user_meta( $user_id, 'seatEmail',  $seatEmail);

            if( isset( $seatIsAlternateEmail ) )
                update_user_meta( $user_id, 'seatIsAlternateEmail',$seatIsAlternateEmail );

            if( isset( $seatAlternateEmail ) )
                update_user_meta( $user_id, 'seatAlternateEmail',$seatAlternateEmail );

            if( isset( $seatIsIcal ) )
                update_user_meta( $user_id, 'seatIsIcal',  $seatIsIcal);

            if( isset( $seatIcalAddress ) )
                update_user_meta( $user_id, 'seatIcalAddress',$seatIcalAddress ); 
        }



}

add_filter('manage_bkx_seat_posts_columns', 'bkx_seat_columns_head');
add_action('manage_bkx_seat_posts_custom_column', 'bkx_seat_columns_content', 10, 2);

/**
 * 
 * @param array $defaults
 * @return string
 */
function bkx_seat_columns_head($defaults) {
	$enable_any_seat  = crud_option_multisite('enable_any_seat');
	if(isset($enable_any_seat) && $enable_any_seat == 1) :
	$defaults['any_seat'] = '<span class="any_seat tips" data-tip="' . esc_attr__( 'Set as any Seat', 'bookingx' ) . '">' . esc_attr__( 'Set as any seat', 'bookingx' ) . '</span>';
	endif;
    $defaults['display_shortcode_all'] = 'Shortcode [bookingx seat-id="all"]';    
    return $defaults;
}
 
/**
 * 
 * @param type $column_name
 * @param type $post_ID
 */
function bkx_seat_columns_content($column_name, $post_ID) {
	$enable_any_seat  = crud_option_multisite('enable_any_seat');
	if($column_name == 'any_seat' && isset($enable_any_seat) && $enable_any_seat == 1) :
		$_enable_any_seat_id = 0;
		$_enable_any_seat_id = crud_option_multisite('select_default_seat');

 $url = wp_nonce_url( admin_url( 'admin-ajax.php?action=bookingx_set_as_any_seat&seat_id=' . $post_ID ), 'bookingx-seat-as-any' );
				echo '<a href="' . esc_url( $url ) . '" title="'. __( 'Set as any Seat', 'bookingx' ) . '">';
				if ($_enable_any_seat_id == $post_ID ) {
					echo '<span class="bks-any-seat tips" data-tip="' . esc_attr__( 'Yes', 'bookingx' ) . '">' . __( 'Yes', 'bookingx' ) . '</span>';
				} else {
					echo '<span class="bks-any-seat not-any-seat tips" data-tip="' . esc_attr__( 'No', 'bookingx' ) . '">' . __( 'No', 'bookingx' ) . '</span>';
				}
				echo '</a>';
	endif;

    if ($column_name == 'display_shortcode_all') {
        echo '[bookingx seat-id="'.$post_ID.'" description="yes" image="yes" extra-info="no"]';
    }
}

add_action( 'wp_ajax_bookingx_set_as_any_seat', 'bookingx_set_as_any_seat_callback' );
add_action( 'wp_ajax_nopriv_bookingx_set_as_any_seat', 'bookingx_set_as_any_seat_callback' );

function bookingx_set_as_any_seat_callback() {
		
			$seat_id = absint( $_GET['seat_id'] );

			if ( 'bkx_seat' === get_post_type( $seat_id ) ) {
				crud_option_multisite('select_default_seat',$seat_id,'update');
			}
		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=bkx_seat' ) );
		die();
}
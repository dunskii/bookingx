<?php if(!empty($current_submenu_active) && $current_submenu_active == 'biz_info') :?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
<form name="form_alias" id="id_form_alias" method="post">
<table cellspacing="0" class="widefat " style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="business_flag" value="1">
		<tr class="active">
			<th scope="row"><label for="business name">Business name</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_name" id="business_name" value="<?php echo crud_option_multisite('bkx_business_name'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="business email">Business Email</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_email" id="business_email" value="<?php echo crud_option_multisite('bkx_business_email'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="business phone">Business Phone</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_phone" id="business_phone" value="<?php echo crud_option_multisite('bkx_business_phone'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
		<th scope="row"><label for="Address line 1">Address line 1</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_address_1" id="business_address_1" value="<?php echo crud_option_multisite('bkx_business_address_1'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="Address line 2">Address line 2</label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_address_2" id="business_address_2" value="<?php echo crud_option_multisite('bkx_business_address_2'); ?>">
				</div>
			</td>
		</tr>


		<tr class="active">
			<th scope="row"><label for="City">City</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_city" id="business_city" value="<?php echo crud_option_multisite('bkx_business_city'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="state">State</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_state" id="business_state" value="<?php echo crud_option_multisite('bkx_business_state'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Postal code">Postal code</label></th>
			 
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="business_zip" id="business_zip" value="<?php echo crud_option_multisite('bkx_business_zip'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Country">Country</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
				<select name="business_country">
					<?php if(!empty($country)){
							foreach ($country as $code => $country_name) {?>
								 <option value="<?php echo $code;?>" <?php if($code == crud_option_multisite('bkx_business_country')){ echo "selected";} ?>><?php echo $country_name;?></option>
							<?php 
							}
						}?>
					</select>
					
				</div>
			</td>
		</tr>
 	
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_business" id="id_save_business" value="Save Changes" /></p>
</form>
<?php endif; ?>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'days_ope') :?>
<?php 
$bkx_business_days = crud_option_multisite("bkx_business_days");
if(!is_array($bkx_business_days)){ $bkx_business_days = maybe_unserialize($bkx_business_days);}
$selected = !empty($bkx_business_days) ? sizeof($bkx_business_days)+1 : 2;
$add_more_status = ($selected >= 7) ? 'display:none' : 'display:block';
 
$bkx_biz_pub_holiday = crud_option_multisite('bkx_biz_pub_holiday');
$temp_pu_h_cnt = !empty($bkx_biz_pub_holiday) ? sizeof($bkx_biz_pub_holiday) : 1;
$bkx_biz_pub_holiday = array_values($bkx_biz_pub_holiday);

?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
<form name="form_alias" id="id_form_alias" method="post">
	<input type="hidden" name="days_operation_flag" value="1">
	<input type="hidden" id="current_value" value="<?php echo $selected;?>">
	<input type="hidden" id="temp_pu_h_cnt" value="<?php echo $temp_pu_h_cnt;?>">
	<ul class="setting-bookingx">
        <?php echo generate_days_section(7 , $bkx_business_days);?>
		<li class="standard" id="add_more_days" style="<?php echo $add_more_status; ?>;">
		<a href="javascript:add_more_days()" class='button-primary'> Add another set of hours</a>
		</li> 
    </ul>
	<div class="clear"></div>
    <h3> Business Vacation </h3>
    <input type="text" name="bkx_biz_vac_sd" id="id_biz_vac_sd"  value="<?php echo crud_option_multisite('bkx_biz_vac_sd'); ?>">
    <input type="text" name="bkx_biz_vac_ed" id="id_biz_vac_ed"  value="<?php echo crud_option_multisite('bkx_biz_vac_ed'); ?>">
    <div class="clear"></div>
    <h3> Public Holidays </h3>
    <input type="text" name="biz_ph[]" id="id_biz_ph_1" value="<?php echo $bkx_biz_pub_holiday[0];?>">
    <a href="javascript:add_more_ph()" class='button-primary'> Add more </a>
    <div class="clear"></div>
    <?php
    	if(!empty($bkx_biz_pub_holiday)){
    		
    		foreach ($bkx_biz_pub_holiday as $key => $pub_holiday) {
    			 if($key > 0 ){
    			 	$key = $key + 1;
    			 	echo '<input type="text" name="biz_ph[]" id="id_clone_ph_'.$key.'" value="'.$pub_holiday.'"><div class="clear"></div>';
    			 	echo '<script type="text/javascript">jQuery( document ).ready(function() {
    			 		jQuery( "#id_clone_ph_'.$key.'").datepicker();
    			 	});</script>';
    			 }    			  
    		}
    	}
    ?>
    
    <div class="bkx_more_pub_holiday"></div>
    <div class="clear"></div>
    <p class="submit"><input type="submit" onclick="" class='button-primary' name="save_days_ope" id="id_save_days_ope" value="Save Changes" /></p>
</form>
<?php endif; ?>

<div class="clear"></div>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'user_opt') :?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'Site User Option' ); ?> </h3>
<form name="form_siteuser" id="id_form_siteuser" method="post">
<table cellspacing="0" class="widefat" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="siteuser_flag" value="1">
		<tr class="active">
			<th scope="row"><label for="modify seat owner">Can site user add/edit their own seat</label></th>
			 
			<td class="plugin-description">
				
					<select name="can_edit_seat" id="id_can_edit_seat">
						<option value="Y" <?php if($temp_option=="Y"){echo "selected";} ?> >Yes</option>
						<option value="N" <?php if($temp_option=="N" || $temp_option == "default"){echo "selected";} ?> >No</option>
					</select>				
			</td>
		</tr>
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				
			</td>
		</tr>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_siteuser" id="id_save_siteuser" value="Save Changes" /></p>
</form>

<?php
$alias_seat = crud_option_multisite('bkx_alias_seat'); 
$bkx_seat_role = crud_option_multisite('bkx_seat_role');
if(isset($bkx_seat_role) && $bkx_seat_role!=''){
    $default_seat_role = $bkx_seat_role;
}else{
    $default_seat_role = 'resource';
}
?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  'Role Assignment' ); ?> </h3>
<form name="form_role_setting" id="id_role_setting" method="post">
<table cellspacing="0" class="widefat" style="margin-top:20px;">
<tbody>
            <tr class="active">
            	<th scope="row"><label for="role">Set Role for <?php echo $alias_seat;?></label></th>
                    
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
					
				</td>
			</tr>
            </tr>
</tbody>
</table>
<input type="hidden" name="role_setting_flag" value="1">
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_role_setting" id="id_save_role_setting" value="Save Changes" /></p>
</form> 
<?php endif; ?>
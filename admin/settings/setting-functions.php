<?php

if(!function_exists('bkx_setting_tabs'))
{
	function bkx_setting_tabs()
	{
		$tab = array();

		$tab['bkx_general'] = array('label' => 'General' , 
								'submenu' => array( 
									'alias' => 'Alias', 
									'page_setting' => 'Content Settings',
									'css'	=> 'Styling',
									'other_settings' => 'Other Settings',
									'exim' => 'Export/Import'),
								'default' => apply_filters( 'bkx_setting_general_default', 'alias' )
								);

		$tab['bkx_payment'] = array('label' => 'Payment' , 
								'submenu' => array( 
									'paypal_express' => 'Paypal Express', 
									'tax_settings' => 'Tax Settings',
									'currency'	=> 'Currency'),
								'default' =>  apply_filters( 'bkx_setting_payment_default', 'paypal_express' )
								);

		$tab['bkx_biz'] = array('label' => 'Business Information' , 
								'submenu' => array( 
									'biz_info' => 'General Details', 
									'days_ope' => 'Days of Operations',
									'user_opt'	=> 'User Options'),
								'default' => apply_filters( 'bkx_setting_biz_default', 'biz_info' )
								);

		return apply_filters( 'bkx_setting_tabs', $tab );

	}
}

function generate_days_section($set = 7 ,$selected = array())
{
	$generate_html = '';
	$next = 1;
	if(!empty($selected)){
		$gen = 1;
		 $next = sizeof($selected) +1;
		foreach ($selected as $key => $value) {
				
				$days = $value['days'];
				$time = $value['time'];

				$sun_selected = ''; 
			    $mon_selected = ''; 
			    $tue_selected = '';
			    $wed_selected = '';
			    $thu_selected = '';
			    $fri_selected = '';
			    $sat_selected = '';

				$generate_html .=  '<div class="day_section_'.$gen.'" '.$display_hide.'><li class="standard"><label for="dayselection" class="lable">Choose a day  </label>
			       <select id="business_days_'.$gen.'" name="days_'.$gen.'[]" multiple="multiple" style="display: none;">';
			       if(in_array('Sunday', $days)){ $sun_selected = 'selected="selected"';}
			       if(in_array('Monday', $days)){ $mon_selected = 'selected="selected"';}
			       if(in_array('Tuesday', $days)){ $tue_selected = 'selected="selected"';}
			       if(in_array('Wednesday', $days)){ $wed_selected = 'selected="selected"';}
			       if(in_array('Thursday', $days)){ $thu_selected = 'selected="selected"';}
			       if(in_array('Friday', $days)){ $fri_selected = 'selected="selected"';}
			       if(in_array('Saturday', $days)){ $sat_selected = 'selected="selected"';}

				$generate_html .= '<option value="Sunday" '.$sun_selected.'>Sunday</option>
									<option value="Monday" '.$mon_selected.'>Monday</option>
									<option value="Tuesday" '.$tue_selected.'>Tuesday</option>
									<option value="Wednesday" '.$wed_selected.'>Wednesday</option>
									<option value="Thursday" '.$thu_selected.'>Thursday</option>
									<option value="Friday" '.$fri_selected.'>Friday</option>
									<option value="Saturday" '.$sat_selected.'>Saturday</option>';

				$generate_html .='</select>
				</li>

				<li class="standard"><label for="opening time" class="lable">Open </label>
				<input name="opening_time_'.$gen.'" id="id_opening_time_'.$gen.'" type="text" value="'.$time['open'].'">
				</li>

				<li class="standard"><label for="closing time" class="lable">Close </label>
				<input name="closing_time_'.$gen.'" id="id_closing_time_'.$gen.'" type="text" value="'.$time['close'].'">
				</li>';
			if($gen > 1){
				$generate_html .='<li class="standard" style="width:100px;"><label for="opening time" class="lable">&nbsp; </label><a class="business_hour_close" href="javascript:remove_more_days('.$gen.');">X</a></li>';
			}
			$generate_html .='<div class="clear"></div></div>';

			$gen++;
		}

		

	}
	 
	for($gen = $next; $gen <= $set ; $gen++ ){
	if($gen > 1){ $display_hide = 'style="display:none;"'; }
	$generate_html .=  '<div class="day_section_'.$gen.'" '.$display_hide.'><li class="standard"><label for="dayselection" class="lable">Choose a day  </label>
       <select id="business_days_'.$gen.'" name="days_'.$gen.'[]" multiple="multiple" style="display: none;">
						<option value="Sunday">Sunday</option>
						<option value="Monday">Monday</option>
						<option value="Tuesday">Tuesday</option>
						<option value="Wednesday">Wednesday</option>
						<option value="Thursday">Thursday</option>
						<option value="Friday">Friday</option>
						<option value="Saturday">Saturday</option>
		</select>
	</li>

	<li class="standard"><label for="opening time" class="lable">Open </label>
	<input name="opening_time_'.$gen.'" id="id_opening_time_'.$gen.'" type="text" value="10:00 AM">
	</li>

	<li class="standard"><label for="closing time" class="lable">Close </label>
	<input name="closing_time_'.$gen.'" id="id_closing_time_'.$gen.'" type="text" value="5:00 PM">
	</li>';
	if($gen > 1){
		$generate_html .='<li class="standard" style="width:100px;"><label for="opening time" class="lable">&nbsp; </label><a class="business_hour_close" href="javascript:remove_more_days('.$gen.');">X</a></li>';
	}
	$generate_html .='<div class="clear"></div></div>';
}

	return $generate_html;
}

<?php
	$objBooking = $val;
	$BkxSeatObj    = $objBooking['seat_arr']['main_obj'];
	$BkxBaseObj    = $objBooking['base_arr']['main_obj'];
	$BkxBaseMetaData = $BkxBaseObj->meta_data;
	//print_r($BkxBaseMetaData);
	$base_is_location_fixed = $BkxBaseMetaData['base_is_location_fixed'][0];
	$base_is_location_differ_seat = $BkxBaseMetaData['base_is_location_differ_seat'][0];
	 
	$BkxExtraObj   = $objBooking['extra_arr'];
	
	$booking_record_id =$objBooking['booking_record_id'];
	$name = $objBooking['first_name']." ".$objBooking['last_name'];
 
	$source = $objBooking['booking_start_date'];
	$date = new DateTime($source);
	$booking_start_date = addslashes($date->format('m/d/Y'));
	$start_time = addslashes($date->format('H:i:s'));

	if(isset($base_is_location_fixed) && $base_is_location_fixed == 'Y' && isset($base_is_location_differ_seat) && $base_is_location_differ_seat == 'Y')
	{
		$objBooking['street'] 	= $BkxBaseMetaData['base_street'][0];
		$objBooking['city']		= $BkxBaseMetaData['base_city'][0];
		$objBooking['state']	= $BkxBaseMetaData['base_state'][0];
		$objBooking['postcode'] = $BkxBaseMetaData['base_postcode'][0];
	}

?>
	<input type="hidden" id="address_val<?php echo $objBooking['booking_record_id']; ?>" value="<?php echo $objBooking['street'].",".$objBooking['city'].",".$objBooking['state']; ?>">
<div class="wrap">
	<div id="reassign_msg"></div>
	<table>
		<tr> 
			<td >
				<b>Client Name:</b> &nbsp;&nbsp;&nbsp; <?php echo $objBooking['first_name']." ".$objBooking['last_name']; ?>
			</td>
			<td colspan="">
				<b class='lblamt'>Total Cost:</b> &nbsp;&nbsp;&nbsp; <a href="javascript:void(0)">$<?php echo $objBooking['total_price']; ?></a>
			</td>
		</tr>
		<tr> 
			<td >
				<b><?php echo get_option("bkx_alias_base","Base"); ?> Type:</b> &nbsp;&nbsp;&nbsp; <?php echo $BkxBaseObj->get_title(); ?>
			</td>
			<td>
               <b class='lblamt'>Amount paid:</b> &nbsp;&nbsp;&nbsp; <a href="javascript:void(0)">$<?php echo ($objBooking['pay_amt']!='')?$objBooking['pay_amt']:0; ?></a>
			</td>
		</tr>
		<tr> 
			<td>
				<b style="float:left;"><?php echo get_option('bkx_alias_addition','Addition'); ?>:</b> <?php 
					if(!empty($BkxExtraObj))
						{
							$addition_arr = array();
							echo "<ol style='float:left;'>";
							foreach ($BkxExtraObj as $key => $ExtraObj) {
								$ExtraMetaData = $ExtraObj['main_obj']->meta_data;	
											 
								echo "<li>".$ExtraObj['title']."</li>";
								$time_option = $ExtraMetaData['addition_time_option'][0];

								if($time_option=="H")
								{
									$time_taken = $ExtraMetaData['addition_hours'][0];
								}
								if($time_option=="D")
								{
									$time_taken = $ExtraMetaData['addition_hours'][0]*24;
								}
								if($time_option=="M")
								{
									$time_taken = $ExtraMetaData['addition_hours'][0]*24*30;
								}
								$addition_arr[$value->addition_id]['time'] = $time_taken;
					 
							}
							echo "</ol>";
						}
				?>
			</td>
			<td>
                <b class='lblamt'>Balance Amount:</b> &nbsp;&nbsp;&nbsp; <a href="javascript:void(0)">$<?php echo ($objBooking['total_price']-$objBooking['pay_amt']); ?></a>
			</td>
		</tr>
		<?php
		if($objBooking['street']!='' && $objBooking['city']!='' && $objBooking['state']!=''){ ?>
		<tr> 
			<td colspan="3">
				<div style="width:44.4%;float:left;">
				<b>Address:</b> &nbsp;&nbsp;&nbsp; <?php echo $objBooking['street'].",<br/>".$objBooking['city'].",".$objBooking['state'].",<br/> Postcode-".$objBooking['postcode']; ?>
				</div>
				<div id="map_canvas_<?php echo $booking_record_id; ?>" style="width: 50%; float:left; height: 300px"></div>
			</td>
		</tr>
		<?php } ?>

		<?php if(get_option('enable_any_seat') == 1 && get_option('select_default_seat')!= ''):
		$get_available_staff	=  reassign_available_emp_list($objBooking['seat_id'],$objBooking['booking_start_date'],$objBooking['booking_end_date'],$objBooking['base_id']);
		?>
		<tr>
			<td width="100%" colspan="3">
			<b>Reassign <?php echo $seat_alias;?> to booking: </b>
			 <select id="id_reassign_booking_<?php echo $objBooking['booking_record_id'];?>" name="reassign_booking_<?php echo $objBooking['booking_record_id'];?>">
					  <option value=""> --Reassign <?php echo $seat_alias;?> --</option>
							<?php if(!empty($get_available_staff)):
									foreach ( $get_available_staff as $staff ):
										if(isset($staff['name']) && $staff['name'] != "") :?>
									 <option value="<?php echo $staff['id']; ?>"><?php echo ucwords($staff['name']); ?></option>
									 <?php endif;
									endforeach;
							endif; ?>
             </select>
			</td>
		</tr>
	<?php endif; ?>
		<tr> 
			<td width="100%" colspan="3">
			<style>.seat_rectangle{font-size:12px;}</style>
				<b>Time Breakdown: </b> 
				<div id="seat_rect_<?php echo $objBooking['booking_record_id']; ?>" class="seat_rectangle" style="padding: 5px;width: 1010px; min-height: 150px;height:auto;border:1px solid #62A636;float:left">
				</div>
			</td>
		</tr>
	</table>
</div>
<?php if(get_option('enable_any_seat') == 1): ?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery( "#id_reassign_booking_<?php {echo $objBooking['booking_record_id'];}?>").change(function() {
			var booking_date  = "<?php echo $booking_start_date;?>";
			var start_time     = "<?php echo $start_time;?>";
			var durations     = "<?php echo $objBooking['booking_time'];?>";
			var seat_id     = jQuery( "#id_reassign_booking_<?php echo $objBooking['booking_record_id'];?>").val();
			var booking_record_id = '<?php echo $objBooking['booking_record_id'];?>';
			var name = "<?php echo $objBooking['first_name']." ".$objBooking['last_name']; ?>";
			var start_date  ="<?php echo $objBooking['booking_start_date'];?>";
			var end_date  ="<?php echo $objBooking['booking_end_date'];?>";
			var base_id  ="<?php echo $objBooking['base_id'];?>";
			if(seat_id!="")
			{
				check_staff_availability(seat_id,start_time,durations,booking_date,booking_record_id,name,start_date,end_date,base_id);
			}

		});
	});
</script>
<?php endif;?>
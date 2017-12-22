<?php	session_start();
require_once('../../../wp-load.php');
require_once('booking_general_functions.php');
global $wpdb;
$base_alias = crud_option_multisite("bkx_alias_base");
/**
 * Converts number of seconds to hours:mins acc to the WP time format setting
 * @return string
 */
function secs2hours( $secs ) {
	$min = (int)($secs / 60);
	$hours = "00";
	if ( $min < 60 )
		$hours_min = $hours . ":" . $min;
	else {
		$hours = (int)($min / 60);
		if ( $hours < 10 )
			$hours = "0" . $hours;
		$mins = $min - $hours * 60;
		if ( $mins < 10 )
			$mins = "0" . $mins;
		$hours_min = $hours . ":" . $mins;
	}
	//if ( $this->time_format )
	$hours_min = date( 'H:i', strtotime( $hours_min . ":00" ) );

	return $hours_min;
}

function getDayName($day)
{
	if($day ==0)
	{
		$day_name = sprintf( __( 'Sunday', 'bookingx' ), '');
	}
	else if($day == 1)
	{
		$day_name = sprintf( __( 'Monday', 'bookingx' ), '');
	}
	else if($day == 2)
	{
		$day_name = sprintf( __( 'Tuesday', 'bookingx' ), '');
	}
	else if($day == 3)
	{
		$day_name = sprintf( __( 'Wednesday', 'bookingx' ), '');
	}
	else if($day == 4)
	{
		$day_name = sprintf( __( 'Thursday', 'bookingx' ), '');
	}
	else if($day == 5)
	{
		$day_name = sprintf( __( 'Friday', 'bookingx' ), '');
	}
	else if($day == 6)
	{
		$day_name = sprintf( __( 'Saturday', 'bookingx' ), '');
	}
	return $day_name;
}

function getMinsSlot($mins)
{
	if(intval($mins) == 0 )
	{
		$slot = 1;
	}
	else if(intval($mins) == 15 )
	{
		$slot = 2;
	}
	else if(intval($mins) == 30 )
	{
		$slot = 3;
	}
	else if(intval($mins) == 45 )
	{
		$slot = 4;
	}
	return $slot;
}

	$bookigndate = '';
	$full_day = '';
	$order_statuses = array('bkx-pending','bkx-ack','bkx-completed','bkx-missed');

	if(isset($_POST['bookigndate']))
	{
		/**
		 *    Added By : Divyang Parekh
		 *    Reason For : Any Seat Functionality
		 *    Date : 4-11-2015
		 */
		$service_id = $_POST['service_id'];
		$bookigndate = $_POST['bookigndate'];
		$_SESSION['_session_base_id'] = $service_id;

		if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && $service_id !='' && crud_option_multisite('enable_any_seat') == 1 && crud_option_multisite('select_default_seat') != ''):


			$_enable_any_seat_selected_id = (int)crud_option_multisite('select_default_seat'); // Default Seat id
 
			$search = array('bookigndate' => $bookigndate,
				'service_id'=>$service_id ,
				'status' => $order_statuses,
				'seat_id' => 'any' ,'display'=> 1);
			$BkxBooking =  new BkxBooking();
			$objBookigntime = $BkxBooking->GetBookedRecords($search);

			//echo "<pre>";
			//print_r($objBookigntime);
			//$objBookigntime = $BkxBooking->get_order_time_data('',$search);
		
			/*$objBookigntime = $wpdb->get_results('SELECT * FROM bkx_booking_time inner join bkx_booking_record on bkx_booking_record.booking_record_id =  bkx_booking_time.booking_record_id WHERE  payment_status = "Completed" AND bkx_booking_time.booking_date = "' . trim($bookigndate) . '" AND  seat_id  IN (SELECT seat_id FROM bkx_seat_base where base_id ='.$service_id.')');*/
			
			//echo '<pre>' . print_r($get_order_time_data, true) . '</pre>';

			$BkxBase = new BkxBase('',$service_id);
			$seat_by_base = $BkxBase->get_seat_by_base();

			if(!empty($seat_by_base)){
				$total_free_seat_all  = sizeof($seat_by_base);
				$free_seat_all_arr = $seat_by_base;
			}
			
			if(!empty($free_seat_all_arr)):
				foreach($free_seat_all_arr as $seat_id)
				{
					$get_range_by_seat[$seat_id]= get_range($bookigndate,$seat_id);
				}
			endif;
			
			if(!empty($objBookigntime)):
				foreach ($objBookigntime as $objBooknig):
					$slot_id= $objBooknig['full_day'];
					$duration = ($objBooknig['booking_time'])/60;
					$seatslots = intval($duration/15);
					$_SESSION['_display_seat_slots']=$seatslots;
					$booking_slot_range=array();
					if($seatslots>1)
					{
						for($i=0; $i<$seatslots; $i++) {
							$slot_id = $objBooknig['full_day'];
							$booking_slot_range[] = $slot_id + $i;
						}
					}
					else
					{
						$booking_slot_range[] =	$slot_id;
					}
					$booking_seat_id= $objBooknig['seat_id'];
					$get_booked_range[$slot_id][$booking_seat_id] =$booking_slot_range;
				endforeach;
			endif;

			
			if(!empty($get_booked_range)):
				foreach($get_booked_range as $slot_id=>$get_booked_slot)
				{
					if(function_exists('_get_booked_slot')):
						$get_data[] =_get_booked_slot($slot_id,$get_booked_range,$free_seat_all_arr,$total_free_seat_all);
					endif;
				}
				if(!empty($get_data)):
					foreach ($get_data as $slots_booked_data)
					{
						if(!empty($slots_booked_data['slots_blocked']))
						{
							foreach($slots_booked_data as $slot_ids)
							{
								foreach($slot_ids as $_slot_id)
								{
									$slots_are_booked[]=$_slot_id;
									$slots_are_booked= array_unique($slots_are_booked);
								}
							}
						}
					}
				endif;
			endif;
		else:

			$search = array('bookigndate' => $bookigndate,
				'service_id'=>$service_id ,
				'status' => $order_statuses,
				'seat_id' => $_POST['seatid'] ,'display'=> 1);
			$BkxBooking =  new BkxBooking();
			$objBookigntime = $BkxBooking->GetBookedRecords($search);

			//print_r($objBookigntime);
			if (!empty($objBookigntime)) {
				//$full_day = $objBookigntime->full_day; change by Arif Khan
				$full_day = $objBookigntime[0]->full_day;
			}

		endif;
	}

	$BkxBase = new BkxBase('', $_POST['service_id'] );
	$base_meta_data = $BkxBase->meta_data;
 
	$base_time_option = $base_meta_data['base_time_option'][0];
	$base_day = $base_meta_data['base_day'][0];
	$base_extended = isset( $base_meta_data['base_is_extended'] ) ? esc_attr( $base_meta_data['base_is_extended'][0] ) : "";

	
	$booking_slot_temp = '';
	$booking_slot_arr=array();
	$i=0; 
	$booking_slot = array();

	$objBookigntime = apply_filters('customise_display_booking_data',$objBookigntime);

	if(!empty($objBookigntime))
	{
		$cnt=count($objBookigntime);
		//echo "<pre>";
		//print_r($objBookigntime);
		foreach($objBookigntime as $temp)
		{

			$wp_postobj = get_post( $temp['booking_record_id']);

			/**
			 * Updated By  : Divyang Parekh
			 * For  : Add Any Seat functionality.*/
			if(crud_option_multisite('enable_any_seat') == 1 && crud_option_multisite('select_default_seat')!= '' && $_POST['seatid'] == 'any'):

				if(!empty($slots_are_booked)):
					$booking_slot_arr= $slots_are_booked;
				endif;

			else:
				/* Updated By : Madhuri Rokade*/
				/* Reason : To get the multiple booking slots. */
				$duration = ($temp['booking_time'])/60;
				$seatslots = intval($duration/15);
				/**
				 * Updated By  : Divyang Parekh
				 * For  : Add Any Seat functionality.
				 */
				if($seatslots>1){
					for($i=0; $i<$seatslots+1; $i++){
						$booking_slot_arr[] = $temp['full_day']+$i;
						$checked_booked_slots[] = array('created_by'=> $wp_postobj->post_author,'slot_id'=> $temp['full_day']+$i , 'order_id' => $temp['booking_record_id']) ;
					}
				} else {
					$booking_slot_arr[] = $temp['full_day'];
					$checked_booked_slots[] = array('created_by'=> $wp_postobj->post_author,'slot_id'=> $temp['full_day'] , 'order_id' => $temp['booking_record_id']) ;
				}
			endif;

		}
		 
		foreach($booking_slot_arr as $slot)
		{
			if($slot!="")
			{
				array_push($booking_slot,$slot);
			}
		}
	}
	/*if (isset($_POST['seatid']) && $_POST['seatid'] == 'any' && $_SESSION['free_seat_id']!=''  && crud_option_multisite('enable_any_seat') == 1 && crud_option_multisite('select_default_seat') != ''):
		$seatid = $_SESSION['free_seat_id'];
	else:
		$seatid = $_POST['seatid'];
	endif;*/

	function check_slot_order_id ( $slot_id, $checked_booked_slots){
		 if(!empty($checked_booked_slots)){
		 	foreach ($checked_booked_slots as $key => $slot_data) {
		 		  if($slot_data['slot_id'] == $slot_id )
		 		  {
		 		  		return $slot_data;
		 		  }
		 	}
		 }
	}
	$bookingdate = $_POST['bookigndate'];
	$range= get_range($bookingdate,$_POST['seatid']);

	//print_r($range);
	//$_SESSION['range_by_seat']=$range;
	//start and end of hours of a day
	$start = 0;
	$end = 24;
			
	$first = $start *3600; // Timestamp of the first cell
	$last = $end *3600; // Timestamp of the last cell
			
	$step = 15 * 60; // Timestamp increase interval to one cell ahead
	$counter = 1;

	$time_available_color = crud_option_multisite('bkx_time_available_color');
	$time_available_color = ($time_available_color) ? $time_available_color : '#368200';

	$time_selected_color = crud_option_multisite('bkx_time_selected_color');
	$time_selected_color = ($time_selected_color) ? $time_selected_color : '#e64754';

	$time_unavailable_color = crud_option_multisite('bkx_time_unavailable_color');
	$time_unavailable_color = ($time_unavailable_color) ? $time_unavailable_color : 'gray';

	$bkx_time_new_selected = crud_option_multisite('bkx_time_new_selected');
	$bkx_time_new_selected = ($bkx_time_new_selected) ? $bkx_time_new_selected : 'gray';

	if($base_time_option == 'H') { ?>
	<style type="text/css">
	.booking-status-booked{ background-color:<?php echo $time_unavailable_color;?> ; }
	.booking-status-open{ background-color:<?php echo $time_available_color;?> ; }
	.booking-status-new-selected, .booking-status-selected{ background-color:<?php echo $bkx_time_new_selected;?>!important; }
	</style>
	<p><label><?php echo sprintf(esc_html__('Booking of the day','bookingx'), '');?></label></p>
	<div class="booking-status-div">
		<div class="booking-status"><?php echo sprintf(esc_html__('Booked','bookingx'), '');?> <div class="booking-status-booked"></div></div>
		<div class="booking-status"><?php echo sprintf(esc_html__('Open','bookingx'), ''); ?> 
		<div class="booking-status-open"></div></div>
		<div class="booking-status"><?php echo sprintf(esc_html__('New Selected','bookingx'), ''); ?> 
		<div class="booking-status-selected"></div></div>
	</div>
	<br/>
		<div id="date_time_display">
			<?php
			/**
			 * Updated By :  Divyang Parekh
			 * Date : 16-11-2015
			 * Please do not edit below code Or carefully
			 * Please don't change below div class name, its very sensitive and its depend on some functionality.
			 */
			//print_r($booking_slot);
			for ( $t=$first; $t<$last; $t=$t+$step ) {
					$ccs = $t; 				// Current cell starts
					$cce = $ccs + $step;	// Current cell ends
					$empty = 'free';

					if(!in_array($counter, $range))
					{
						$empty = 'notavailable';
					}
					else
					{
						if(in_array($counter, $booking_slot))//if that slot booked or not
						{
							$empty = 'full';
						}
					}
					$order_data = check_slot_order_id($counter,$checked_booked_slots);
					$order_id = isset($order_data['order_id'])  && $order_data['order_id']!='' ? $order_data['order_id'] : 0;
					$created_by = isset($order_data['created_by'])  && $order_data['created_by']!='' ? $order_data['created_by'] : 0;
					
					?>
					<div data-booking-id="<?php echo $order_id; ?>" data-user-id="<?php echo $created_by; ?>" class="<?php echo $counter; ?>-is-<?php echo $empty; ?> app_timetable_cell <?php echo $counter; ?> <?php echo $empty; ?> <?php echo $_POST['bookigndate'].'-'.$counter; ?>" id="<?php echo secs2hours( $ccs ); ?>" data-slotnumber="<?php echo $_POST['bookigndate'].'-'.$counter; ?>">
					<input type="hidden" value="<?php echo secs2hours( $ccs ); ?>" />
					<?php
						echo secs2hours($ccs);
					?>
					</div>
					<?php
					$counter = $counter + 1;
			}
		?>
		</div>
		<br/>
	<?php }	?> 
<!-- || $base_time_option == 'M' -->
<?php if( $base_time_option == 'D' && $base_day > 1  && $base_extended == 'Y' ) : ?>
<div class="gfield_description"> If <?php echo $base_alias;?> can be extended time is set in days/months display second calender for end date.</div>
<div class="ginput_container" id="base_extended_calender">
<input name="datepicker_extended" id="id_datepicker_extended" type="text" value="" class="" tabindex="8"> </div>
<?php endif;?>
<script type="text/javascript">
var loader = jQuery('.bookingx-loader');
loader.hide();
</script>
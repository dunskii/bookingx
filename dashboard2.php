 <script type="text/javascript" src="<?php echo plugins_url( 'js/jquery-1.7.1.js' , __FILE__ ); ?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url( 'js/jquery-ui.css' , __FILE__ ); ?>" />
<script type="text/javascript" src="<?php echo plugins_url( 'js/jquery-ui.js' , __FILE__ ); ?>"></script>
<?php
// session_start();
require_once('booking_general_functions.php');
custom_load_scripts(array('jquery-1.7.1.js','jquery-ui.js'));
custom_load_styles(array('jquery-ui.css'));
global $wpdb; ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWj_y315MvVm1l30ArFr0sh4tZjuK6I4w&sensor=false" ></script>
<style>
tr.view-details{display:none;}
</style>
<script>
function showDetails(id){
	jQuery(".view-details").hide();
	jQuery("#details-"+id).slideToggle("slow");
}
</script>
<?php 

/*
   code edit by Arif Khan only change if condition
   Purpose: to show current user data
*/


$current_user = wp_get_current_user();
$bkx_seat_role = crud_option_multisite('bkx_seat_role');


$BookedRecords= new BkxBooking();

if ( !($current_user instanceof WP_User) )
	return;
	$roles = $current_user->roles; 
	$data = $current_user->data;

if (isset( $roles['0'] ) && $roles['0']=='administrator') {

	// $query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_base ON bkx_base.base_id = bkx_booking_record.base_id WHERE (payment_status="Completed" or payment_status="cancelled")  order by bkx_booking_record.booking_record_id desc ';

}
else if(isset( $roles['0'] ) && $roles['0']== $bkx_seat_role)
{
	$get_seat_post_id = get_user_meta($current_user->ID,'seat_post_id',true);
	$search['seat_id'] = $get_seat_post_id;
	$search['role'] = $roles['0'];
}
else
{
	$search['user_id'] = $current_user->ID;
	$search['role'] = $roles['0'];
	
}
$GetBookedRecords = $BookedRecords->GetBookedRecordsByUser($search);
/**
 * Created By : Divyang Parekh
 * Add Functionality for Cancel Booking
 */
if(crud_option_multisite('enable_cancel_booking')== 1 
	&& crud_option_multisite('reg_customer_crud_op') == 1) :

	$cancel_booking_text = "Delete Booking";
	$complete_booking_text = "Complete Booking";
	$alert_cancel_booking_text = "Are you sure want to delete this booking?";
	$alert_complete_booking_text = "Are you sure want to Complete this booking?";

	$cancellation_policy_page_id = get_option('cancellation_policy_page_id');
	$page_data = get_page( $cancellation_policy_page_id );
	$cancellation_policy_content = $page_data->post_content;
	$cancellation_policy_page_link = get_permalink($cancellation_policy_page_id);

	if(isset($cancellation_policy_page_link)) : $cancellation_policy_page_link = $cancellation_policy_page_link; else: $cancellation_policy_page_link= "#"; endif;
	$cancellation_policy_link ='<b>Read more about cancellation policy , <a href="'.$cancellation_policy_page_link.'" target="_blank">Click here</a></b>';
	$cancellation_policy_title= $page_data->post_title;
	
endif;
//End Divyang Parekh

$limit = '';
$curr_page_number = get_pagenum();
$per_page = get_per_page();
//print_r($GetBookedRecords);
//$wpdb->get_results($query);
$total_items = sizeof($GetBookedRecords);
$total_pages = floor($total_items/$per_page);
$mod_val = $total_items%$per_page;
if($mod_val>0)
{
$total_pages = $total_pages+1;
}
if($total_pages>1)
{
	//$limit = " LIMIT ".(($curr_page_number-1)*$per_page).", ".$per_page;
}
$query = $query.$limit;
//$GetBookedRecords
//$objListBase=$wpdb->get_results($query);
?>
<?php
	if(isset($_GET['booking_id']))
	{
		include('view-booking.php');
	}
	else{
?>
<div class="wrap">
<?php if(sizeof($GetBookedRecords)>0){ 
	$seat_alias = get_option("bkx_alias_seat","Seat");
	$base_alias = get_option("bkx_alias_base","Base");
	$addition_alias = get_option('bkx_alias_addition','Addition');
?>
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;" id="id-wp-list-table-booking">
	<thead>
		<tr>

			<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input style="display: none;" type="checkbox"></th>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style=""># Booking ID</th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan="">Booking<?php //echo $seat_alias; ?> Date</th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan="">Booking<?php //echo $seat_alias; ?> Time</th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan="">Client Name</th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan="">Client Phone </th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan=""><?php echo $base_alias; ?></th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan=""># of <?php echo $addition_alias; ?></th>
			<th style="" class="manage-column column-name" id="name" scope="col" colspan="">Action</th>
		</tr>
	</thead>
	<div id="dialog-confirm" title="<?php echo $cancellation_policy_title; ?>" style="display: none;"><?php echo $cancellation_policy_content.'<br>'.$cancellation_policy_link;?></div>

<?php
$counter = 0;
foreach($GetBookedRecords as $key=>$val)
{
	$order_status = $val->order_status;
	$counter++;

	if($counter%2==0){
		$alternate = "alternate";
	}else{
		$alternate = "";
	}
	//echo "<pre>";
	//print_r($val);
	$result_count = 0 ;

	$Baseobj = $val['base_arr']['main_obj'];
//	echo $Baseobj->get_title();
	if($val['addition_ids']!="")
	{
		$BkxExtra = new BkxExtra();
		$ExtraObj = $BkxExtra->get_extra_by_ids($val['addition_ids']);
		// if(!empty($ExtraObj))
		// {
		// 	foreach ($ExtraObj as $key => $extra) {
		// 		# code...
		// 	}
		// }
		$result_count = sizeof($ExtraObj);
	}

	echo '<tr href="'.$val['booking_record_id'].'" class="'.$alternate.'">';
	echo '<th scope="row" class="check-column"><input type="checkbox" style="display: none;" name="post[]" value="<?php echo $value->addition_id; ?>"></th>';
	echo '<td id="record7_'.$val['booking_record_id'].'">#'.$val['booking_record_id'].'</td><td id="record6_'.$val['booking_record_id'].'">'.$val['booking_date'].'</td>
	<td id="record1_'.$val['booking_record_id'].'">'.date('h:i A', strtotime($val['booking_start_date'])).'-'.date('h:i A ',strtotime($val['booking_end_date'])).'</td>
	<td id="record2_'.$val['booking_record_id'].'">'.$val['first_name'].' '.$val['last_name'].'</td>
	<td id="record3_'.$val['booking_record_id'].'">'.$val['phone'].'</td>
	<td id="record4_'.$val['booking_record_id'].'">'.$Baseobj->get_title().'</td>
	<td id="record5_'.$val['booking_record_id'].'">'.$result_count.'</td>';
	?>
	<td><a href="javascript:void(0);" onclick="showDetails(<?php echo $val['booking_record_id']; ?>);" id="view-<?php echo $val['booking_record_id']; ?>">View</a>
		<?php if(get_option('enable_cancel_booking')==1) :?>
			<?php if($order_status =='completed'):?>
				<?php $display_order_cancelled= 'block';
					  $display_order_completed= 'none;';?>
			<?php endif;?>

			<?php if($order_status =='cancelled'):?>
				<?php
				if(isset( $roles['0'] ) && $roles['0']=='administrator'):
						$display_order_completed= 'block';
					else:
						$display_order_completed= 'none;';
					endif;

				$display_order_cancelled= 'none;';
				 ?>
			<?php endif;?>
			|<a href="javascript:void(0)" style="text-decoration: none;display: <?php echo $display_order_cancelled;?>" id="booking_cancel_<?php echo $val['booking_record_id']; ?>" onclick="Cancel_booking(<?php echo $val['booking_record_id']; ?>);"><?php echo $cancel_booking_text;?></a>

			<!-- <a href="javascript:void(0)" style="text-decoration: none;display: <?php //echo $display_order_completed;?>" id="booking_complete_<?php //echo $val['booking_record_id']; ?>" onclick="Complete_booking(<?php //echo $val['booking_record_id']; ?>);"><?php //echo $complete_booking_text;?></a> -->

		<?php endif;?>
	</td>
	<?php 
	echo '</tr>';
	echo "<tr class='view-details' id='details-".$val['booking_record_id']."'>
	<td colspan='8'>"; ?>
<script>

jQuery( document ).ready(function() {
    var id_temp = "<?php echo $val['booking_record_id']; ?>";

	jQuery("#view-"+id_temp).on("click",function(e){

		initialize(id_temp);
		codeAddress(id_temp);
		draw_rectangle("<?php echo $val['booking_record_id']; ?>", "<?php echo $val['first_name']." ".$val['last_name']; ?>");
	});
});

/*jQuery(function() {
	
});*/
function initialize(counter) {
    geocoder = new google.maps.Geocoder();
    //var latlng = new google.maps.LatLng(-34.397, 150.644);
    //var address = "A/5,Paradise,Godrej Hill,KalYan,Maharashtra,421301";
    var address = document.getElementById("address_val"+counter).value;

    geocoder.geocode( { 'address': address}, function(results, status) {
	
      if (status == google.maps.GeocoderStatus.OK) {
     		var latlng = new google.maps.LatLng(results[0].geometry.location.Ya,results[0].geometry.location.Za);
		    var mapOptions = {
		      zoom: 12,
		      center: latlng,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    }
		    map = new google.maps.Map(document.getElementById("map_canvas_"+counter), mapOptions);
      } else {
       alert("Geocode was not successful for the following reason: " + status);
      }
    });
}

function codeAddress(counter) {
    var address = document.getElementById("address_val"+counter).value;
    //address = "A/5,Paradise,Godrej Hill,KalYan,Maharashtra,421301";
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        //alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
function getContrast50(hexcolor){
    return (parseInt(hexcolor, 16) > 0xffffff/2) ? 'black':'black';
}

function draw_rectangle(id_temp, name)
{
		//alert("inside function call");
		jQuery.post('<?php echo plugins_url( "" , __FILE__ ); ?>/get_booking_details.php', { booking_record_id: id_temp }, function(data) {	
		
		var temp_data =	jQuery.parseJSON(data);
		//console.log(temp_data);
		var temp_addition = temp_data.addition;
		//console.log(temp_data.booking);
		var base_time_option = temp_data.booking;
		//console.log(base_time_option);

			//console.log("addition data start");
			//console.log(temp_addition);
			var counter = 0;
			var addition_time_arr = new Array();
			var addition_minutes_arr = new Array();
			var addition_minutes = 0;
			var addition_minutes_total = 0;

			for(var x in temp_addition)
			{
				var addition_time = '';
				if(temp_addition[x].addition_time_option=="H")
				{
					addition_time = temp_addition[x].addition_hours;
					addition_minutes = parseInt(temp_addition[x].addition_hours)*60 + parseInt(temp_addition[x].addition_minutes);
					addition_time_display = temp_addition[x].addition_hours+" Hours "+ temp_addition[x].addition_minutes + "Minutes";
				}
				if(temp_addition[x].addition_time_option=="D")
				{
					addition_time = temp_addition[x].addition_days*24;
					addition_minutes = addition_time * 60;
					addition_time_display = temp_addition[x].addition_days+" Days";
				}
				if(temp_addition[x].addition_time_option=="Months")
				{
					addition_time = temp_addition[x].addition_months*24*30;
					addition_time_display = temp_addition[x].addition_months+" Months";
				}
				addition_minutes_total = addition_minutes_total + addition_minutes;
				addition_time_arr[temp_addition[x].addition_id] = addition_time;
				addition_minutes_arr[temp_addition[x].addition_id] = addition_minutes;
				
			}
			//console.log("addition");
			//console.log(base_time_minutes);
			//console.log(addition_minutes_total);
			/*end addition time calculation*/
		//console.log(base_time_option.seat_name);
		//for(x in base_time_option)
		//{
			jQuery(".seat_rectangle").html("");
			jQuery("#seat_rect_"+id_temp).css('background-color', "#"+base_time_option.seat_color);
			var divSeat = document.createElement("div");
			headerColor = getContrast50(base_time_option.seat_color);
			divSeat.innerHTML = name+ " - "+base_time_option.seat_name+" - From "+base_time_option.booking_start_date+" To "+base_time_option.booking_end_date+" - Duration:"+base_time_option.total_duration;
			divSeat.style.color = headerColor;
			//console.log(divSeat);
			document.getElementById('seat_rect_'+id_temp).appendChild(divSeat);
			var base_option = base_time_option.base_time_option;
			var base_time = '';
			var base_time_minutes = '';
			var base_time_display = "";
			if(parseInt(extended_base_time)>0)
			{
				//base_time_display = parseInt(base_time_display) + (base_time_display*parseInt(extended_base_time));
			}

			if(base_option=='H')
			{
				base_time = base_time_option.base_hours;
				base_mins = base_time_option.base_minutes;
				base_time_minutes = parseInt(base_time_option.base_hours)*60 + parseInt(base_time_option.base_minutes);
				base_time_display = (parseInt(base_time_option.base_hours)+parseInt(base_time_option.base_hours*base_time_option.extended_base_time))+" Hours "+ parseInt(base_time_option.base_minutes) + "Minutes";
			}
			if(base_option=='D')
			{
				base_time = base_time_option.base_day*24;
				base_time_minutes = parseInt(base_time) * 60;
				base_time_display = (parseInt(base_time_option.base_day)+parseInt(base_time_option.base_day*base_time_option.extended_base_time))+" Days"
			}
			if(base_option=="M")
			{
				base_time = base_time_option.base_month*24*30;
				base_time_display =(parseInt(base_time_option.base_month)+parseInt(base_time_option.base_month*base_time_option.extended_base_time))+" Months"
			}
			var extended_base_time = base_time_option.extended_base_time;

			///console.log(base_time_minutes);
			var ratioBase = base_time_minutes/parseInt(addition_minutes_total+base_time_minutes);
			var ratioAddition = addition_minutes_total/parseInt(addition_minutes_total+base_time_minutes);
			var total_width = 1000;
			var baseWidth = ratioBase * total_width;
			var additionWidth = ratioAddition * total_width;
			var divTag = document.createElement("div");
			divTag.id = "div1";
			divTag.setAttribute("align","center");
			divTag.style.border = "1px solid #ccc";
			divTag.style.height = "50px";
			divTag.style.width = baseWidth+"px";
			var expected_color = getContrast50(base_time_option.base_color);
			divTag.style.color = expected_color;			
			divTag.style.background = '#'+base_time_option.base_color;
			divTag.innerHTML = base_time_option.base_name+" - Duration: "+base_time_display;

			document.getElementById('seat_rect_'+id_temp).appendChild(divTag);

			//creating divAddition
			var divAddition = document.createElement("div");
			divAddition.setAttribute("align","center");
			divAddition.setAttribute("id","addition_container");
			divAddition.style.border = "0px solid #ccc";
			divAddition.style.height = "auto";
			divAddition.style.width = "300px";
			divAddition.style.background = 'Yellow';
			divAddition.style.position = 'relative';
			divAddition.style.left = +baseWidth+'px';
			
			document.getElementById('seat_rect_'+id_temp).appendChild(divAddition);
			jQuery("#seat_rect_"+id_temp).css('min-height','0');
		//}
		addition_time_arr.sort(function(a,b){return b-a});
		//console.log("addition time array");
		var temp_width_factor = 340/addition_time_arr[0];
		var leftAddition = 0;
		var topAddition = 0;
		for(var x in temp_addition)
		{			
			var addition_time = '';
			var addition_minute_current = 0;
			if(temp_addition[x].addition_time_option=="H")
			{
				addition_time = temp_addition[x].addition_hours;
				addition_minute_current = parseInt(temp_addition[x].addition_hours) * 60 + parseInt(temp_addition[x].addition_minutes);
				if(parseInt(temp_addition[x].addition_minutes) == 0)
				{
					addition_time_display = temp_addition[x].addition_hours+" Hours ";
				}
				else if(parseInt(temp_addition[x].addition_hours)==0)
				{
					addition_time_display = temp_addition[x].addition_minutes +" Minutes";
				}
				else
				{
					addition_time_display = temp_addition[x].addition_hours+" Hours "+temp_addition[x].addition_minutes +" Minutes";
				}
			}
			if(temp_addition[x].addition_time_option=="D")
			{
				addition_time = temp_addition[x].addition_days*24;
				addition_minute_current = parseInt(addition_time) * 60;
				addition_time_display = temp_addition[x].addition_days+" Days";
			}
			if(temp_addition[x].addition_time_option=="M")
			{
				addition_time = temp_addition[x].addition_months*24*30;
				addition_time_display = temp_addition[x].addition_months+" Months";
			}
			var ratioAdditionMinutes = addition_minute_current/addition_minutes_total;
			var currentWidthAddition = additionWidth * ratioAdditionMinutes;
			var divAddition = document.createElement("div");
			divAddition.style.border = "1px solid #ccc";
			divAddition.style.position = "";
			divAddition.style.float = "left";
			
			var add_overlap = temp_addition[x].addition_overlap;			

			if(add_overlap == 'Y'){
				divAddition.style.left = "none";
			}else{
				divAddition.style.left = leftAddition+"px";
			}
			
			//divAddition.style.left = leftAddition+"px";
			divAddition.style.top = topAddition+"px";
			var expected_color = getContrast50(temp_addition[x].addition_color);
			divAddition.style.color = expected_color;
			divAddition.style.height = "50px";
			divAddition.style.width = currentWidthAddition+"px";
			divAddition.style.background = "#"+temp_addition[x].addition_color;			
			divAddition.innerHTML = temp_addition[x].addition_name+"<br>"+addition_time_display;
			
			document.getElementById('addition_container').appendChild(divAddition);
			counter=counter+1;			
			leftAddition = parseInt(leftAddition) + currentWidthAddition;
			topAddition = parseInt(topAddition) + 50;
		}
	});
}

<?php if(get_option('enable_any_seat') == 1): ?>
	/**
	 * Added function for Reassign Staff of Booking
	 * By : Divyang Parekh
	 * Date : 16-11-2015
	 */
	function check_staff_availability(seat_id,start_time,durations,booking_date,booking_record_id,name,start_date,end_date,base_id)
	{

//seatid: seat_temp, start:start_time, bookingduration: booking_duration,bookingdate:  booking_date
		var seat_id = seat_id;
		var booking_date  = booking_date;
		var start_time     = start_time;
		var durations     = durations;
		var booking_record_id = booking_record_id;
		var name = name;
		var start_date  =start_date
		var end_date  =end_date;
		var base_id = base_id;
		$("#reassign_msg").fadeIn();
		jQuery.post("<?php echo plugins_url( "" , __FILE__ ); ?>/check_staff_availability.php",{seat_id: seat_id, start:start_time, bookingduration: durations,bookingdate:  booking_date ,booking_record_id : booking_record_id,start_date:start_date,end_date:end_date,base_id:base_id}, function(data) {

			var temp_obj = $.parseJSON(data);
			var result = temp_obj['data'];
			var result_staff = temp_obj['staff'];
			var booking_record_id = temp_obj['booking_record_id'];
			if(parseInt(result) == 1)
			{
				jQuery('#reassign_msg').addClass('updated');
				jQuery('#reassign_msg').html('<b><?php echo $seat_alias;?> Successfully Reassign.</b>');
				$("#reassign_msg").fadeOut(3000,function(){jQuery('#reassign_msg').removeClass('updated');});

				draw_rectangle(booking_record_id,name);
				if(result_staff.length)
				{
					var temp_option='<option value="">Reassign Booking: </option>';
					for(x in result_staff)
					{
						temp_option += "<option value='"+result_staff[x]['seat_id']+"'>"+result_staff[x]['name']+"</option>";
					}
					jQuery('#id_reassign_booking_'+booking_record_id).empty().append(temp_option);
				}
			}
			else{
				alert('Sorry ! Something went wrong , Please try again.');
			}
		});

	}
<?php endif;?>
<?php if(get_option('enable_cancel_booking')==1 && $order_status =='cancelled') :?>
var thCount = $('.wrap #id-wp-list-table-booking th').length;
for (var i = 1; i <= thCount;i++)
{
	var $booking_id_loop =<?php echo $val['booking_record_id'];?>;
	jQuery("#record"+i+"_"+$booking_id_loop).css('text-decoration','line-through');
}
<?php endif; ?>
	function Cancel_booking(booking_id)
	{
		var thCount = $('.wrap #id-wp-list-table-booking th').length;
		var $booking_id = booking_id;
		<?php if (isset( $roles['0'] ) && $roles['0']!='administrator') :?>
			jQuery( "#dialog-confirm" ).dialog({
				resizable: true,
				width:600,
				height:500,
				modal: true,
				buttons: {
					"<?php echo 'I Agree';?>": function() {
						jQuery( this ).dialog( "close" );
						jQuery.post('<?php echo plugins_url( "" , __FILE__ ); ?>/cancel_booking.php', { booking_record_id: $booking_id,mode:'cancelled' }, function(data) {
							if(data ==1)
							{
								alert('Order Successfully Cancelled.');
								jQuery("#booking_cancel_"+$booking_id).hide();
								<?php if(isset( $roles['0'] ) && $roles['0']=='administrator'):?>
								jQuery("#booking_complete_"+$booking_id).show();
								<?php endif;?>
								for (var i = 1; i <= thCount;i++)
								{
									jQuery("#record"+i+"_"+$booking_id).css('text-decoration','line-through');
								}
							}
							else
							{
								alert('Something went wrong , Please try again.')
							}
						});
					},
					Cancel: function() {
						jQuery( this ).dialog( "close" );
					}
				}
			});

		<?php else: ?>
		var confirm_cancel_booking = confirm("<?php echo $alert_cancel_booking_text; ?>");
		if (confirm_cancel_booking == true)
		{
			var thCount = $('.wrap #id-wp-list-table-booking th').length;
			jQuery.post('<?php echo plugins_url( "" , __FILE__ ); ?>/cancel_booking.php', { booking_record_id: $booking_id,mode:'cancelled' }, function(data) {

				if(data ==1)
				{
					//alert('admin');
					alert('Order Successfully Cancelled.');
					jQuery("#booking_cancel_"+$booking_id).hide();
					jQuery("#booking_complete_"+$booking_id).show();
					for (var i = 1; i <= thCount;i++)
					{
						jQuery("#record"+i+"_"+$booking_id).css('text-decoration','line-through');
					}
				}
				else
				{
					alert('Something went wrong , Please try again.')
				}
			});
		}
		<?php endif;?>
	}
function Complete_booking(booking_id)
{
	var thCount = $('.wrap #id-wp-list-table-booking th').length;
	var $booking_id = booking_id;
	var confirm_cancel_booking = confirm("<?php echo $alert_complete_booking_text; ?>");
	if (confirm_cancel_booking == true) {
		jQuery.post('<?php echo plugins_url( "" , __FILE__ ); ?>/cancel_booking.php', { booking_record_id: $booking_id,mode:'completed' }, function(data) {
			if(data ==1)
			{
				jQuery("#booking_complete_"+$booking_id).hide();
				jQuery("#booking_cancel_"+$booking_id).show();
				alert('Order Successfully Completed.');
				for (var i = 1; i <= thCount;i++)
				{
					jQuery("#record"+i+"_"+$booking_id).css('text-decoration','none');
				}
			}
			else if(data ==2)
			{
				alert('Sorry ! You cant complete this booking ,already booked this slot.');
			}
			else
			{
				alert('Something went wrong , Please try again.')
			}
		});
	}

}
</script>
		<?php require('view-booking.php'); ?>
	<?php	echo "</td></tr>";
}
?>
</table>
<?php pagination('bottom',$total_items,$total_pages,$per_page); ?>
<?php }else{ echo "<h4>No Record Found</h4>"; } ?>
</div>
<?php } ?>
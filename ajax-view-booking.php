<?php
require_once('../../../wp-load.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
global $wpdb;

	$booking_record_id = $_POST["bid"];
	$query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_base ON bkx_base.base_id = bkx_booking_record.base_id WHERE booking_record_id = '.trim($booking_record_id);;
	$objBooking=$wpdb->get_results($query);
	$name = $objBooking[0]->first_name." ".$objBooking[0]->last_name;

		$business_address_1 = crud_option_multisite("bkx_business_address_1");
		$business_address_2 = crud_option_multisite("bkx_business_address_2");
		$bkx_business_city = crud_option_multisite("bkx_business_city");
		$bkx_business_state = crud_option_multisite("bkx_business_state");
		$bkx_business_zip = crud_option_multisite("bkx_business_zip");
		$bkx_business_country = crud_option_multisite("bkx_business_country");

		$full_address = $business_address_1.",".$bkx_business_city.",".$bkx_business_state.",".$bkx_business_zip.",".$bkx_business_country;
?>


<style>
.leftalign
{
width: 50%;
float: left;
}
.centeralign
{
background:#DADADA;
}
.seat_rectangle
{
width:572px !important;
overflow-x:hidden;
}
.accordion-heading
{
font-weight:bold;
}
.accordion-header
{
font-weight:bold;
}
</style>


<script>
jQuery(function() {
	//alert("hello");
	var id_temp = "<?php echo $_GET['booking_id']; ?>";
	initialize(id_temp);
	codeAddress(id_temp);
	draw_rectangle("<?php echo $_GET['booking_id']; ?>", "<?php echo $name; ?>");
});
 function initialize(counter) {
    geocoder = new google.maps.Geocoder();
    //var latlng = new google.maps.LatLng(-34.397, 150.644);
    //var address = "A/5,Paradise,Godrej Hill,KalYan,Maharashtra,421301";
    var address = document.getElementById("address_val"+counter).value;
    geocoder.geocode( { 'address': address}, function(results, status) {
	
      if (status == google.maps.GeocoderStatus.OK) {
		   //console.log(address);
		   //console.log(results[0].geometry.location);
     		var latlng = new google.maps.LatLng(results[0].geometry.location.Ya,results[0].geometry.location.Za);
		    var mapOptions = {
		      zoom: 12,
		      center: latlng,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    }
		    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
      } else {
        //alert("Geocode was not successful for the following reason: " + status);
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
    return (parseInt(hexcolor, 16) > 0xffffff/2) ? 'black':'white';
}

function draw_rectangle(id_temp, name)
{
		alert("inside function call");
		jQuery.post('<?php echo plugins_url( "" , __FILE__ ); ?>/get_booking_details.php', { booking_record_id: id_temp }, function(data) {	
		
		var temp_data =	jQuery.parseJSON(data);
		//console.log(temp_data);
		var temp_addition = temp_data.addition;
		//console.log(temp_data.booking);
		var base_time_option = temp_data.booking;
		console.log(base_time_option);

		/*addition time calculation*/
			console.log("addition data start");
			console.log(temp_addition);
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
				//alert(addition_minutes);
				addition_minutes_total = addition_minutes_total + addition_minutes;
				addition_time_arr[temp_addition[x].addition_id] = addition_time;
				addition_minutes_arr[temp_addition[x].addition_id] = addition_minutes;
				
			}
			console.log("addition");
			//console.log(addition_minutes_arr);
			console.log(base_time_minutes);
			console.log(addition_minutes_total);
			/*end addition time calculation*/

		for(x in base_time_option)
		{
			jQuery(".seat_rectangle").html("");
			//console.log(base_time_option[x].addition_ids);
			jQuery("#seat_rect_"+id_temp).css('background-color', "#"+base_time_option[x].seat_color);
			var divSeat = document.createElement("div");
			headerColor = getContrast50(base_time_option[x].seat_color);
			divSeat.innerHTML = name+ " - "+base_time_option[x].seat_name+" - From "+base_time_option[x].booking_start_date+" To "+base_time_option[x].booking_end_date+" - Duration:"+base_time_option[x].total_duration;
			divSeat.style.color = headerColor;
			document.getElementById('seat_rect_'+id_temp).appendChild(divSeat);
			var base_option = base_time_option[x].base_time_option;
			var base_time = '';
			var base_time_minutes = '';
			var base_time_display = "";
			if(parseInt(extended_base_time)>0)
			{
				//base_time_display = parseInt(base_time_display) + (base_time_display*parseInt(extended_base_time));
			}

			//alert(base_option);
			if(base_option=='H')
			{
				//alert("hours");
				base_time = base_time_option[x].base_hours;
				base_mins = base_time_option[x].base_minutes;
				base_time_minutes = parseInt(base_time_option[x].base_hours)*60 + parseInt(base_time_option[x].base_minutes);
				base_time_display = (parseInt(base_time_option[x].base_hours)+parseInt(base_time_option[x].base_hours*base_time_option[x].extended_base_time))+" Hours "+ parseInt(base_time_option[x].base_minutes) + "Minutes";
			}
			if(base_option=='D')
			{
				//alert("days");
				base_time = base_time_option[x].base_day*24;
				base_time_minutes = parseInt(base_time) * 60;
				base_time_display = (parseInt(base_time_option[x].base_day)+parseInt(base_time_option[x].base_day*base_time_option[x].extended_base_time))+" Days"
			}
			if(base_option=="M")
			{
				//alert("months");
				base_time = base_time_option[x].base_month*24*30;
				base_time_display =(parseInt(base_time_option[x].base_month)+parseInt(base_time_option[x].base_month*base_time_option[x].extended_base_time))+" Months"
			}
			//alert(base_time_minutes);
			//alert(base_time);
			var extended_base_time = base_time_option[x].extended_base_time;

			console.log(base_time_minutes);
			var ratioBase = base_time_minutes/parseInt(addition_minutes_total+base_time_minutes);
			var ratioAddition = addition_minutes_total/parseInt(addition_minutes_total+base_time_minutes);
			var total_width = 1000;
			var baseWidth = ratioBase * total_width;
			var additionWidth = ratioAddition * total_width;
			//alert(baseWidth);
			//alert(additionWidth);
			var divTag = document.createElement("div");
			divTag.id = "div1";
			divTag.setAttribute("align","center");
			divTag.style.border = "1px solid #ccc";
			divTag.style.height = "50px";
			divTag.style.width = baseWidth+"px";
			var expected_color = getContrast50(base_time_option[x].base_color);
			divTag.style.color = expected_color;
			//alert(base_time_option[x].base_color);
			divTag.style.background = '#'+base_time_option[x].base_color;
			divTag.innerHTML = base_time_option[x].base_name+" - Duration: "+base_time_display;

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
		}
		//console.log(temp_addition);
		
		//console.log(addition_time_arr);
		addition_time_arr.sort(function(a,b){return b-a});
		console.log("addition time array");
		//console.log(addition_time_arr);
		//console.log(addition_time_arr[0]);
		var temp_width_factor = 340/addition_time_arr[0];//alert(temp_width_factor);
		var leftAddition = 0;
		var topAddition = 0;
		for(var x in temp_addition)
		{
			
			//console.log(temp_addition[x]);
			//console.log(temp_addition[x].addition_color);
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
			
			//alert(additionWidth);
			var ratioAdditionMinutes = addition_minute_current/addition_minutes_total;
			//alert(addition_minute_current+"Hii"+addition_minutes_total);
			var currentWidthAddition = additionWidth * ratioAdditionMinutes;
			//alert(currentWidthAddition);
			var divAddition = document.createElement("div");
			//divAddition.setAttribute("align","center");
			//divAddition.setAttribute("id","addition_container");
			divAddition.style.border = "1px solid #ccc";
			divAddition.style.position = "absolute";
			divAddition.style.float = "left";
			
			/*
			var add_overlap = temp_addition[x].addition_overlap;
			alert(add_overlap);

			if(add_overlap == 'Y'){
			divAddition.style.left = "none";
			}else{
			divAddition.style.left = leftAddition+"px";
			}*/

			divAddition.style.left = leftAddition+"px";
			divAddition.style.top = topAddition+"px";
			var expected_color = getContrast50(temp_addition[x].addition_color);
			divAddition.style.color = expected_color;
			divAddition.style.height = "50px";
			//divAddition.style.width = (addition_time*temp_width_factor)+"px";
			divAddition.style.width = currentWidthAddition+"px";
			divAddition.style.background = "#"+temp_addition[x].addition_color;
			//divAddition.style.position = 'relative';
			//divAddition.style.left = '200px';
			divAddition.innerHTML = temp_addition[x].addition_name+"<br>"+addition_time_display;
			//alert(divAddition);document.getElementById('addition_container').innerHTML ="testing content";
			//alert("after inner html");
			//alert($("#seat_rect_"+id_temp).length);$("#addition_container").attr("class","testinggg");
			document.getElementById('addition_container').appendChild(divAddition);
			counter=counter+1;
			//alert(currentWidthAddition);
			leftAddition = parseInt(leftAddition) + currentWidthAddition;
			topAddition = parseInt(topAddition) + 50;
		}
	
	});
      /*
      var canvas = document.getElementById('seat_rect_'+id_temp);
      var context = canvas.getContext('2d');

      context.beginPath();
      context.rect(0, 0, 200, 100);
      context.fillStyle = 'yellow';
      context.fill();
      context.lineWidth = 1;
      context.strokeStyle = 'black';
      context.stroke();*/
}
</script>
	<table>
	<tr>
	<td width="100%" colspan="3">
	<div class="centeralign" style="">
	<div class="leftalign">
	<div class="leftalign">
	Client Name: <?php echo $objBooking[0]->first_name." ".$objBooking[0]->last_name; ?>
	</div>
	<div>	
	 Client Phone: <?php echo $objBooking[0]->phone; ?> 
	</div>
	</div>
	<div>	
	Client Email: <?php echo $objBooking[0]->email; ?>
	</div>
	</div> <!--end centeralign-->
	<br/>
	<div class="centeralign">
	<div class="leftalign">
	Base Type: <?php echo $objBooking[0]->base_name; ?>&nbsp;&nbsp;
	</div>
	<div>	
	 Client Comments:
	</div>
	</div>
	<br/>
	<div style="float: right;background:#DADADA;">
	Payment Method : <?php echo $objBooking[0]->payment_method; ?><br/>
	Payment Status : <?php echo $objBooking[0]->payment_status; ?><br/>
	Total Price : $<?php echo $objBooking[0]->total_price; ?><br/>
	</div>
	<div class="centeralign">
	<div>
	Additions:
	<ul>
	<?php
	if(isset($result_addition) and sizeof($result_addition)>0)
	{
		$addition_arr = array();
		foreach($result_addition as $index=>$objBooking[0])
		{
			echo "<li>".$objBooking[0]->addition_name."</li>";
			$time_option = $objBooking[0]->addition_time_option;
			if($time_option=="H")
			{
				$time_taken = $objBooking[0]->addition_hours;
			}
			if($time_option=="D")
			{
				$time_taken = $objBooking[0]->addition_hours*24;
			}
			if($time_option=="M")
			{
				$time_taken = $objBooking[0]->addition_hours*24*30;
			}
			$addition_arr[$objBooking[0]->addition_id]['time'] = $time_taken;
			  
		}
	}
	?>
	</ul>
	</div>
	</div>
	</br>

	<input type="hidden" id="address_val<?php echo $objBooking[0]->booking_record_id; ?>"
	value="<?php echo $full_address; ?>">
	<div class="centeralign">
	<div class="leftalign">
	Address: <?php echo $full_address; ?>
	</div>
	<div id="map_canvas<?php echo $objBooking[0]->booking_record_id; ?>" ></div>
	</div> <!--end centeralign-->
	</br>
	<div class="centeralign">
	<div class="leftalign">
	Time Breakdown:
	</div>
	<div id="seat_rect_<?php echo $objBooking[0]->booking_record_id; ?>" class="seat_rectangle" style="padding: 5px;min-height: 300px;height:auto;background-color:white;float:left">	
	
	</div>
	
    </div>
	
	</td></tr></table>
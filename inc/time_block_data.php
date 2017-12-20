<?php

		$business_address_1 = crud_option_multisite("bkx_business_address_1");
		$business_address_2 = crud_option_multisite("bkx_business_address_2");
		$bkx_business_city = crud_option_multisite("bkx_business_city");
		$bkx_business_state = crud_option_multisite("bkx_business_state");
		$bkx_business_zip = crud_option_multisite("bkx_business_zip");
		$bkx_business_country = crud_option_multisite("bkx_business_country");
		$time_block_bg_color = crud_option_multisite("time_block_bg_color");
		$time_block_extra_color  = crud_option_multisite("time_block_extra_color");
		$time_block_service_color = crud_option_multisite("time_block_service_color");
		$full_address = $business_address_1.",".$bkx_business_city.",".$bkx_business_state.",".$bkx_business_zip.",".$bkx_business_country;
		$base_id = get_post_meta($post->ID,'base_id',true );
		$seat_id = get_post_meta($post->ID,'seat_id',true );
		$addition_ids = get_post_meta($post->ID,'addition_ids',true );

		$seat_colour = get_post_meta($seat_id, 'seat_colour', true );
		$base_colour = get_post_meta($base_id, 'base_colour', true );
		 
		$seat_colour = empty($seat_colour) ? $time_block_bg_color : $seat_colour;
		$base_colour = empty($base_colour) ? $time_block_service_color : $base_colour;
		 
		$extra_colour = empty($extra_colour) ? $time_block_extra_color : $extra_colour;


		?>
		<input type="hidden" id="address_val<?php echo $post->ID; ?>" value="<?php echo $full_address; ?>">
		<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWj_y315MvVm1l30ArFr0sh4tZjuK6I4w&sensor=false" ></script> -->
		<script>
		 
    	var id_temp = "<?php echo $post->ID; ?>";

    	setTimeout(function(){ 
    		//initialize(id_temp);
		//codeAddress(id_temp);
		draw_rectangle("<?php echo $post->ID; ?>", "<?php echo $order_meta['first_name']." ".$order_meta['last_name']; ?>");

    		  }, 500);

		
		 
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
    //return hexcolor;
}

function draw_rectangle( id_temp, name )
{
        var base_bg  = '<?php echo crud_option_multisite("time_block_service_color");?>';
        var extra_bg = '<?php echo crud_option_multisite("time_block_extra_color");?>';
        var main_bg  = '<?php echo crud_option_multisite("time_block_bg_color");?>';
        //alert("inside function call");
        jQuery.post('<?php echo plugins_url( "" , __DIR__ ); ?>/get_booking_details.php', { booking_record_id: id_temp }, function(data) {

        
        var temp_data = jQuery.parseJSON(data);
        var colour_json = temp_data.colour;
        //console.log(temp_data);
        var temp_addition = temp_data.addition;
       // console.log(temp_data.booking);
        var base_time_option = temp_data.booking;

        main_bg     = colour_json.seat;
        base_bg     = colour_json.base;
        //extra_bg    = colour_json.seat;
 
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
 
            //jQuery(".seat_rectangle").html("");
            jQuery("#seat_rect_"+id_temp).css('background-color', main_bg);
            var divSeat = document.createElement("div");
            headerColor = getContrast50(base_bg);
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
            var total_width = 100;
            //var baseWidth = ratioBase * total_width;
            var baseWidth = 40;
            var additionWidth = ratioAddition * total_width;
            var divTag = document.createElement("div");
            divTag.id = "div1";
            divTag.setAttribute("align","center");
            divTag.style.border = "1px solid #ccc";
            divTag.style.height = "50px";
            divTag.style.width = baseWidth+"%";
            var expected_color = getContrast50(base_bg);
            divTag.style.color = expected_color;            
            divTag.style.background = base_bg;

            divTag.innerHTML = base_time_option.base_name+" - Duration: "+base_time_display;

            document.getElementById('seat_rect_'+id_temp).appendChild(divTag);


            jQuery("#seat_rect_"+id_temp).css('min-height','0');
        //}
        addition_time_arr.sort(function(a,b){return b-a});
        //console.log("addition time array");
        var temp_width_factor = 340/addition_time_arr[0];
        var leftAddition = baseWidth;
        var topAddition = 0;
        for(var x in temp_addition)
        {
            //creating divAddition
            var divAddition = document.createElement("div");
            divAddition.setAttribute("align","center");
            divAddition.setAttribute("id","addition_container-"+id_temp+'-'+x);
            divAddition.style.border = "0px solid #ccc";
            divAddition.style.height = "auto";
            divAddition.style.width = "100%";
            divAddition.style.float = "left";
            //divAddition.style.background = extra_bg;
            divAddition.style.position = 'relative';

            var add_overlap = temp_addition[x].addition_overlap;   
        
            if( x == 0 ) 
            {
                 divAddition.style.left = +baseWidth+'%';
            }
            else
            {
                if(add_overlap == 'Y'){
                    divAddition.style.left = +baseWidth+'%';
                }else{
                    divAddition.style.left = +leftAddition+'%';
                }
            }
           
            document.getElementById('seat_rect_'+id_temp).appendChild(divAddition);

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
            
            divAddition.style.left = leftAddition+"px";

            currentWidthAddition = currentWidthAddition + 5;

            divAddition.style.top = topAddition+"px";
            var expected_color = getContrast50(temp_addition[x].colour);
            divAddition.style.color = expected_color;
            divAddition.style.height = "50px";
            divAddition.style.width = currentWidthAddition+"%";
           // divAddition.style.width = "100%";
            divAddition.style.background = temp_addition[x].colour;            
            divAddition.innerHTML = temp_addition[x].addition_name+"<br>"+addition_time_display;
            
            document.getElementById("addition_container-"+id_temp+'-'+x).appendChild(divAddition);
            counter=counter+1;      
            if( x == 0 ) 
            {
                 divAddition.style.left = +baseWidth+'%';
                 leftAddition = parseInt(leftAddition) + currentWidthAddition;
            }
            else
            {
                if(add_overlap == 'Y'){
                    
                }else{
                    leftAddition = parseInt(leftAddition) + currentWidthAddition;
                }
            }
            topAddition = parseInt(topAddition) + 50;
        }
    });
}
</script>
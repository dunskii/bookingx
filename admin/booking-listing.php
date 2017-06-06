<?php

add_filter( 'manage_bkx_booking_posts_columns', 'bkx_booking_columns' , 99, 2 );

function bkx_booking_columns( $existing_columns )
{
    
    $seat_alias     = crud_option_multisite( 'bkx_alias_seat' );
    $base_alias     = crud_option_multisite( 'bkx_alias_base' );
    $addition_alias = crud_option_multisite( 'bkx_alias_addition' );
    unset($existing_columns);
    $columns                    = array();
    $columns[ 'cb' ]            = '<input type="checkbox" />';
    $columns[ 'order_status' ]  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'bookingx' ) . '">' . esc_attr__( 'Current Status', 'bookingx' ) . '</span>';
    $columns[ 'booking_date' ]  = __( 'Date', 'bookingx' );
    $columns[ 'booking_time' ]  = __( 'Time', 'bookingx' );
    $columns[ 'duration' ]      = __( 'Duration', 'bookingx' );
    $columns[ 'client_name' ]   = __( 'Name', 'bookingx' );
    $columns[ 'client_phone' ]  = __( 'Phone', 'bookingx' );
    $columns[ 'seat' ]          = __( $seat_alias, 'bookingx' );
    $columns[ 'service' ]       = __( $base_alias, 'bookingx' );
    $columns[ 'extra' ]         = __( '# of ' . $addition_alias, 'bookingx' );
    $columns[ 'order_total' ]   = __( 'Total', 'bookingx' );
    $columns[ 'order_actions' ] = __( 'Actions', 'bookingx' );
    $columns[ 'order_view' ]    = __( 'View', 'bookingx' );

    return $columns;
}

add_action( 'manage_bkx_booking_posts_custom_column', 'render_bkx_booking_columns' );
function render_bkx_booking_columns( $column )
{
    global $post, $wpdb;

    $orderObj   = new BkxBooking();
    $order_meta = $orderObj->get_order_meta_data( $post->ID );

    //print_r($order_meta);
    switch ( $column ) {
        case 'order_status':
            $order_status = $orderObj->get_order_status( $post->ID );
            echo sprintf( __( '%s', 'bookingx' ), $order_status );
            break;
        case 'booking_date':
            $date_format = get_option( 'date_format' );
            echo sprintf( __( '%s', 'bookingx' ), date( $date_format, strtotime( $order_meta[ 'booking_date' ] ) ) );
            break;
        case 'booking_time':
            echo sprintf( __( '%s', 'bookingx' ), date( 'h:i A', strtotime( $order_meta[ 'booking_start_date' ] ) ), date( 'h:i A ', strtotime( $order_meta[ 'booking_end_date' ] ) ) );
            break;
        case 'duration':
            echo sprintf( __( '%s', 'bookingx' ), $order_meta['total_duration'] );
            break;
        case 'client_name':
            echo sprintf( __( '%s %s', 'bookingx' ), $order_meta[ 'first_name' ], $order_meta[ 'last_name' ] );
            break;
        case 'client_phone':
            $client_phone = $order_meta[ 'phone' ];
            echo sprintf( __( '%s', 'bookingx' ), $client_phone );
            break;
        case 'seat':
            $seat_arr = $order_meta[ 'seat_arr' ];
            printf( __( '%s', 'bookingx' ), $seat_arr[ 'title' ] );
            break;
        case 'service':
            $base_arr = $order_meta[ 'base_arr' ];
            printf( __( '%s', 'bookingx' ),$base_arr[ 'title' ] );
            break;
        case 'extra':
            $extra_arr = $order_meta[ 'extra_arr' ];
            echo sizeof($extra_arr);
            break;
        case 'order_total':
            $currency    = isset( $order_meta[ 'currency' ] ) && $order_meta[ 'currency' ] != '' ? $order_meta[ 'currency' ] : get_current_currency();
            $total_price = $order_meta[ 'total_price' ];
            //echo sprintf( __( '<a href="#" title="%s(%s)">%s%d</a>', 'bookingx' ), get_option( 'currency_option' ), $currency, $currency, $total_price );
            echo sprintf( __( '%s%d %s', 'bookingx' ), $currency, $total_price, get_option( 'currency_option' ) );
            break;
        case 'order_actions':
             $order_status = strtolower($orderObj->get_order_status( $post->ID ) );
             $exclude_status = array('completed','missed','cancelled');
             $pending   = ($order_status == 'pending') ? "selected" : '';
             $ack       = ($order_status == 'acknowledged') ? "selected" : '';
             $completed = ($order_status == 'completed') ? "selected" : '';
             $missed    = ($order_status == 'missed') ? "selected" : '';
             $cancelled = ($order_status == 'cancelled') ? "selected" : '';

            $business_address_1 = crud_option_multisite("bkx_business_address_1");
            $business_address_2 = crud_option_multisite("bkx_business_address_2");
            $bkx_business_city = crud_option_multisite("bkx_business_city");
            $bkx_business_state = crud_option_multisite("bkx_business_state");
            $bkx_business_zip = crud_option_multisite("bkx_business_zip");
            $bkx_business_country = crud_option_multisite("bkx_business_country");

            $full_address = $business_address_1.",".$bkx_business_city.",".$bkx_business_state.",".$bkx_business_zip.",".$bkx_business_country;
            echo '<input type="hidden" id="post_ids" value="'.$post_json.'">';
            echo '<input type="hidden" id="address_val'.$post->ID.'" value="'.$full_address.'">';
            $status_dropdown = '';
            $status_dropdown .= '<select name="bkx-action" class="bkx_action_status" style="width:91px;" id="bulk-action-selector-top">';
            $status_dropdown .= '<option value="'.$post->ID.'_pending" '.$pending.'>Pending</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_ack" '.$ack .'>Acknowledged</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_completed" '.$completed.'>Completed</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_missed" '.$missed.'>Missed</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_cancelled" '.$cancelled.'>Cancelled</option></select>';
            if(!in_array($order_status, $exclude_status)){ echo $status_dropdown;}else{ 
            ?>
             <script type="text/javascript">
             jQuery(function() {
                jQuery("#cb-select-<?php echo $post->ID;?>").attr('disabled','disabled');
             });
             </script>
            <?php
                echo ucwords($order_status); 
            }
        break;

        case 'order_view' :

echo sprintf( __( '<span class="view_href_booking-'.$post->ID.'"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx' ), 'javascript:view_summary(\''.$post->ID.'\',\''.$order_meta[ 'first_name' ].' '.$order_meta[ 'last_name' ].'\')','View', 'View' );

echo sprintf( __( '<span class="close_booking-'.$post->ID.'" style="display:none;"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx' ), 'javascript:close_booking(\''.$post->ID.'\')','Close', 'Close' );

echo '<div class="loader-'.$post->ID.'" style="display:none;"> Please wait..</div>';
           // echo '<div class="view_summary-'.$post->ID.' view_summary"></div>';
            break;
    }
}

function bkx_change_view_link( $permalink, $post ) {
    if( $post->post_type == 'bkx_booking' ) { 
         $orderObj   = new BkxBooking();
        $order_meta = $orderObj->get_order_meta_data( $post->ID );
        $permalink = 'javascript:view_summary(\''.$post->ID.'\',\''.$order_meta[ 'first_name' ].' '.$order_meta[ 'last_name' ].'\')';
    }
    return $permalink;
}
add_filter('post_type_link',"bkx_change_view_link",10,2);

add_action( 'admin_footer', 'bkx_booking_bulk_admin_footer', 10 );
function bkx_booking_bulk_admin_footer()
{
    global $post_type;

    if ( 'bkx_booking' == $post_type ) {
        echo '<style> #order_actions { width: 80px; }
#calendar {
        max-width: 900px;
        margin: 0 auto;
        background: #fff;
    padding: 20px;
    }
.bkx-order_summary, .bkx-order_summary_full {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    margin : 3px;

}
.search_by_dates_section input {
    width: 93px;
    margin-right: 5px;
}
</style>';
?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWj_y315MvVm1l30ArFr0sh4tZjuK6I4w&sensor=false" ></script>
<script type="text/javascript">
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
        //console.log(temp_data);
        var temp_addition = temp_data.addition;
        //console.log(temp_data.booking);
        var base_time_option = temp_data.booking;
 
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
            var baseWidth = ratioBase * total_width;
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
        var leftAddition = 0;
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
            divAddition.style.left = +baseWidth+'%';
            
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
            
            var add_overlap = temp_addition[x].addition_overlap;            

            if(add_overlap == 'Y'){
                divAddition.style.left = "none";
            }else{
                divAddition.style.left = leftAddition+"px";
            }
            
            divAddition.style.left = leftAddition+"px";

            divAddition.style.top = topAddition+"px";
            var expected_color = getContrast50(extra_bg);
            divAddition.style.color = expected_color;
            divAddition.style.height = "50px";
            divAddition.style.width = currentWidthAddition+"%";
           // divAddition.style.width = "100%";
            divAddition.style.background = extra_bg;            
            divAddition.innerHTML = temp_addition[x].addition_name+"<br>"+addition_time_display;
            
            document.getElementById("addition_container-"+id_temp+'-'+x).appendChild(divAddition);
            counter=counter+1;          
            leftAddition = parseInt(leftAddition) + currentWidthAddition;
            topAddition = parseInt(topAddition) + 50;
        }
    });
}
     function view_summary( post_id, name ){
        
     	jQuery('.loader-'+ post_id).show();
     		var data = {
	            'action': 'bkx_action_view_summary',
	            'post_id': post_id
	        };

        setTimeout(function(){  initialize(post_id); codeAddress(post_id);draw_rectangle(post_id, name); }, 500);

	        jQuery.post(ajaxurl, data, function(response) {

	        	jQuery('.loader-'+ post_id).hide();
	        	jQuery('.close_booking-'+ post_id).show();
	        	jQuery('.view_href_booking-'+ post_id).hide();
	        	//jQuery('.view_summary-'+ post_id).html(response); 
                var postobj = jQuery('#post-'+ post_id);

                jQuery( "<tr class='view_summary view_summary-"+post_id+"'>"+response ).insertAfter( postobj).slideDown('slow');
      
	        });
     }

     function close_booking(post_id) {
     			jQuery('.close_booking-'+ post_id).hide();
	        	jQuery('.view_href_booking-'+ post_id).show();
	        	jQuery('.view_summary-'+ post_id).hide();
	}

    jQuery(function() {

        var bkx_view_id = '<?php echo $_GET["view"]; ?>';
        var bkx_view_name = '<?php echo $_GET["bkx_name"]; ?>';
        if( bkx_view_id  )
        {
            jQuery('#post-'+ bkx_view_id).focus();
            view_summary( bkx_view_id, bkx_view_name );
        }

       
        jQuery('.search_by_dates').on('change', function() {
            var search_by_dates = jQuery(this).val();
            if(search_by_dates == 'choose_date'){
                jQuery('.search_by_dates_section').html('');
                jQuery('.search_by_dates_section').append('<input type="hidden" name="search_by_dates" value="choose_date" >');
                jQuery('.search_by_dates_section').append('<input type="text" name="search_by_selected_date" placeholder="Select a date" class="search_by_selected_date">');
                jQuery( ".search_by_selected_date" ).datepicker(); 
            }  
        });

       
       var span_trash =  jQuery('.row-actions').find('span.trash').find('a');

        jQuery(".listing_view").next('#post-query-submit').remove();

        jQuery( span_trash ).click(function() {
          if(confirm('Are you sure want to Cancel this booking?')){}
          else{ return false;}
        });

        jQuery('.row-actions').find('span.trash').find('a').text('Cancel');
        jQuery('.row-actions').find('span.edit').find('a').text('Reschedule');

        jQuery('<option>').val('mark_pending').text('<?php _e( 'Pending', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_ack').text('<?php _e( 'Acknowledged', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_completed').text('<?php _e( 'Completed', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_missed').text('<?php _e( 'Missed', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_cancelled').text('<?php _e( 'Cancelled', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_pending').text('<?php _e( 'Pending', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_ack').text('<?php _e( 'Acknowledged', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_completed').text('<?php _e( 'Completed', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_missed').text('<?php _e( 'Missed', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_cancelled').text('<?php _e( 'Cancelled', 'bookingx' );?>').appendTo('select[name="action2"]');


      });

     jQuery('.bkx_action_status').on('change', function() {
     	if(confirm('Are you sure want to change status?')){
     		var bkx_action_status = jQuery(this).val();
	        var data = {
	            'action': 'bkx_action_status',
	            'status': bkx_action_status
	        };

	        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	        jQuery.post(ajaxurl, data, function(response) {
	             location.reload();
	        });
     	}else{
     		return false;
     	}
    });
     <?php
     $listing_view = isset($_GET['listing_view'])? $_GET['listing_view']:'';
     if($listing_view == 'weekly' || $listing_view == 'monthly'){ 
         generate_listing_view($listing_view);
        ?>
        jQuery("<div class='weekly-data' id='calendar'></div>").insertBefore( ".wp-list-table" ).slideDown('slow');
        jQuery(".wp-list-table").hide();
        
    <?php } ?>
      </script>
      <?php
       
    }
}

add_filter('months_dropdown_results', '__return_empty_array');

add_action( 'restrict_manage_posts', 'bkx_booking_search_by_dates', 2, 10 );
function bkx_booking_search_by_dates( $post_type, $which){

    //only add filter to post type you want
    if ('bkx_booking' == $post_type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $values = array(
            'All dates' => '', 
            'Today' => 'today', 
            'Tomorrow' => 'tomorrow',
            'This Week' => 'this_week',
            'Next Week' => 'next_week',
            'This Month' => 'this_month',
            'Select a Date' => 'choose_date'
        );
        $search_by_dates = isset($_GET['search_by_dates'])? $_GET['search_by_dates']:'';
 
        if($search_by_dates == 'choose_date'){
           $search_by_dates = '';
        }

        ?>
        <div class="search_by_dates_section" style="float: left;">
            <select name="search_by_dates" class="search_by_dates">
            <?php
                foreach ($values as $label => $value) {
                    printf
                        ('<option value="%s"%s>%s</option>',
                            $value,
                            $value == $search_by_dates? ' selected="selected"':'',
                            $label
                        );
                    }
            ?>
            </select>
        </div>
        <!-- <input type="text" name="search_by_selected_date" placeholder="Select a date" class="search_by_selected_date" style="<?php //echo $display; ?>"> -->
        <input type="submit" name="filter_action" style="float: left;" id="post-query-submit" class="button" value="Filter">     
        <?php    
    }
}
add_action( 'restrict_manage_posts', 'bkx_booking_seat_view', 2, 10 );
function bkx_booking_seat_view( $post_type, $which){

if ('bkx_booking' == $post_type){

    $args = array(
    'posts_per_page'   => -1,
    'post_type'        => 'bkx_seat',
    'post_status'      => 'publish',
    );
    $get_seat_array = get_posts( $args );
    $alias_seat = crud_option_multisite('bkx_alias_seat');
    if(!empty($get_seat_array)){

        foreach ($get_seat_array as $key => $seat_data) {
            $seat_obj[$seat_data->ID] = $seat_data->post_title ;
        }
    }

     if(!empty($seat_obj)) : ?>
        <select name="seat_view" class="seat_view">
        <option> Select <?php echo $alias_seat;?> </option>
        <?php
            $seat_view = isset($_GET['seat_view'])? $_GET['seat_view']:'';
            foreach ($seat_obj as $seat_id => $seat_name) {
                printf
                    ('<option value="%s"%s>%s</option>',
                        $seat_id,
                        $seat_id == $seat_view? ' selected="selected"':'',
                        $seat_name
                    );
                }
        ?>
        </select>
        <input type="submit" name="filter_action" style="float: left;" class="button seat_view" value="view"> 
        <?php endif; 

    }


}

add_action( 'restrict_manage_posts', 'bkx_booking_listing_view', 2, 10 );
function bkx_booking_listing_view( $post_type, $which){

    //only add filter to post type you want
    if ('bkx_booking' == $post_type){
        
        $values = array(
            'Agenda' => 'agenda', 
            'Weekly' => 'weekly',
            'Monthly' => 'monthly'
            );
        ?>       

        <select name="listing_view" class="listing_view">
        <?php
            $listing_view = isset($_GET['listing_view'])? $_GET['listing_view']:'';
            foreach ($values as $label => $value) {
                printf
                    ('<option value="%s"%s>%s</option>',
                        $value,
                        $value == $listing_view? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <input type="submit" name="filter_action" style="float: left;" class="button listing_view" value="Change view"> 
        <?php            
    }
}

function generate_listing_view( $type )
{
    if($type == 'weekly' || $type == 'monthly' ){ 
      
    $defaultView = ($type) && $type == 'weekly' ?  'agendaWeek' : 'month';
    $bkx_calendar_json_data = crud_option_multisite('bkx_calendar_json_data');
    ?>

    jQuery(document).ready(function() {
         
        jQuery('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            defaultView: '<?php echo $defaultView;?>',
            defaultDate: '2017-05-12',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            events: [<?php echo $bkx_calendar_json_data; ?>]
        });
        
    });
    <?php
    }
}
// Disable wpseo_use_page_analysis
add_filter( 'wpseo_use_page_analysis', function( $arg ) { return ''; } );
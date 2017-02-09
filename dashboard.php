<?php
session_start();
require_once('booking_general_functions.php');
global $wpdb;
$query = 'SELECT * FROM bkx_booking_record INNER JOIN bkx_base ON bkx_base.base_id = bkx_booking_record.base_id WHERE payment_status="Completed"';

$limit = '';
$curr_page_number = get_pagenum();
$per_page = get_per_page();
$wpdb->get_results($query);
$total_items = $wpdb->num_rows;
$total_pages = floor($total_items/$per_page);
$mod_val = $total_items%$per_page;
if($mod_val>0)
{
$total_pages = $total_pages+1;
}
if($total_pages>1)
{
$limit = " LIMIT ".(($curr_page_number-1)*$per_page).", ".$per_page;
}
$query = $query.$limit;
$objListBase=$wpdb->get_results($query);
?>

<script type="text/javascript" src="<?php echo plugins_url( 'js/jquery-1.7.1.js' , __FILE__ ); ?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url( 'js/jquery-ui.css' , __FILE__ ); ?>" />
<script type="text/javascript" src="<?php echo plugins_url( 'js/jquery-ui.js' , __FILE__ ); ?>"></script>

<script type="text/javascript">
$(function() {
	//alert('<?php echo get_option("bkx_api_google_map_key"); ?>');
	var gmap_key = '<?php echo get_option("bkx_api_google_map_key"); ?>';
	if(gmap_key=="")
	{
		//alert("First set google map api key in the setting section");
	}
	$("th#cb.manage-column input").bind( "change", function(event, ui) {
		
		if($(this).attr('checked')){
			
			$("input[name='post[]']").attr('checked','checked');
		}
		else{
			$("input[name='post[]']").removeAttr('checked','checked');	
		}
	});
	
});
</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('bkx_api_google_map_key'); ?>&sensor=false" ></script>

<!---
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgPti3hvlgcVqpnUgBcbzq8PYex9mKNOQ&sensor=false" ></script>
---->
<script>
$(function() {
$( "#accordion" ).accordion();
$("#hidden_row").css('display','none');
$("#hidden_row").removeClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active");

$( "#accordion" ).accordion({
    activate: function( event, ui ) {
	
	var id_temp = $(ui.newPanel.context).attr('href');
	initialize(id_temp);
	codeAddress(id_temp);
	draw_rectangle(id_temp);
    }
});
$("#ui-accordion-accordion-header-0").unbind("click");
//jQuery("#ui-accordion-accordion-header-0").live("click", function (){return false;});
//initialize();


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
		    map = new google.maps.Map(document.getElementById("map_canvas"+counter), mapOptions);
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
function draw_rectangle(id_temp)
{
      //alert("insode");
      $.post('<?php echo plugins_url( "" , __FILE__ ); ?>/get_booking_details.php', { booking_record_id: id_temp }, function(data) {	
		
		var temp_data =	$.parseJSON(data);
		var temp_addition = temp_data.addition;
		//console.log(temp_data.booking);
		var base_time_option = temp_data.booking;
		//console.log(base_time_option);
		for(x in base_time_option)
		{
			$(".seat_rectangle").html("");
			//console.log(base_time_option[x].addition_ids);
			$("#seat_rect_"+id_temp).css('background-color', "#"+base_time_option[x].seat_color);
			var divSeat = document.createElement("div");
			divSeat.innerHTML = base_time_option[x].seat_name+" - Duration:";
			document.getElementById('seat_rect_'+id_temp).appendChild(divSeat);
			var base_option = base_time_option[x].base_time_option;
			var base_time = '';
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
				base_time_display = (parseInt(base_time_option[x].base_hours)+parseInt(base_time_option[x].base_hours*base_time_option[x].extended_base_time))+" Hours";
			}
			if(base_option=='D')
			{
				//alert("days");
				base_time = base_time_option[x].base_day*24;
				base_time_display = (parseInt(base_time_option[x].base_day)+parseInt(base_time_option[x].base_day*base_time_option[x].extended_base_time))+" Days"
			}
			if(base_option=="M")
			{
				//alert("months");
				base_time = base_time_option[x].base_month*24*30;
				base_time_display =(parseInt(base_time_option[x].base_month)+parseInt(base_time_option[x].base_month*base_time_option[x].extended_base_time))+" Months"
			}
			//alert(base_time_display);
			var extended_base_time = base_time_option[x].extended_base_time;
			
			var divTag = document.createElement("div");
			divTag.id = "div1";
			divTag.setAttribute("align","center");
			divTag.style.border = "1px solid #ccc";
			divTag.style.height = "50px";
			divTag.style.width = "200px";
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
			divAddition.style.left = '200px';
			
			document.getElementById('seat_rect_'+id_temp).appendChild(divAddition);
		}
		//console.log(temp_addition);
		var counter = 0;
		var addition_time_arr = new Array();
		for(var x in temp_addition)
		{
			var addition_time = '';
			if(temp_addition[x].addition_time_option=="H")
			{
				addition_time = temp_addition[x].addition_hours;
				addition_time_display = temp_addition[x].addition_hours+" Hours";
			}
			if(temp_addition[x].addition_time_option=="D")
			{
				addition_time = temp_addition[x].addition_days*24;
				addition_time_display = temp_addition[x].addition_days+" Days";
			}
			if(temp_addition[x].addition_time_option=="Months")
			{
				addition_time = temp_addition[x].addition_months*24*30;
				addition_time_display = temp_addition[x].addition_months+" Months";
			}
			//alert(addition_time);
			addition_time_arr[temp_addition[x].addition_id] = addition_time;
			
		}
		//console.log(addition_time_arr);
		addition_time_arr.sort(function(a,b){return b-a});
		//console.log(addition_time_arr);console.log(addition_time_arr[0]);
		var temp_width_factor = 340/addition_time_arr[0];//alert(temp_width_factor);
		for(var x in temp_addition)
		{
			
			//console.log(temp_addition[x]);
			//console.log(temp_addition[x].addition_color);
			var addition_time = '';
			if(temp_addition[x].addition_time_option=="H")
			{
				addition_time = temp_addition[x].addition_hours;
				addition_time_display = temp_addition[x].addition_hours+" Hours";
			}
			if(temp_addition[x].addition_time_option=="D")
			{
				addition_time = temp_addition[x].addition_days*24;
				addition_time_display = temp_addition[x].addition_days+" Days";
			}
			if(temp_addition[x].addition_time_option=="M")
			{
				addition_time = temp_addition[x].addition_months*24*30;
				addition_time_display = temp_addition[x].addition_months+" Months";
			}
			//alert(addition_time);
			var divAddition = document.createElement("div");
			//divAddition.setAttribute("align","center");
			//divAddition.setAttribute("id","addition_container");
			divAddition.style.border = "1px solid #ccc";
			divAddition.style.position = "relative";
			divAddition.style.float = "left";
			divAddition.style.left = "0px";
			divAddition.style.top = "0px";
			var expected_color = getContrast50(temp_addition[x].addition_color);
			divAddition.style.color = expected_color;
			divAddition.style.height = "50px";
			divAddition.style.width = (addition_time*temp_width_factor)+"px";
			divAddition.style.background = "#"+temp_addition[x].addition_color;
			//divAddition.style.position = 'relative';
			//divAddition.style.left = '200px';
			divAddition.innerHTML = temp_addition[x].addition_name+"<br>"+addition_time_display;
			//alert(divAddition);document.getElementById('addition_container').innerHTML ="testing content";
			//alert("after inner html");
			//alert($("#seat_rect_"+id_temp).length);$("#addition_container").attr("class","testinggg");
			document.getElementById('addition_container').appendChild(divAddition);
			counter=counter+1;
			
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

function generate_xml()
{
	alert("generate xml data ");
	var values = new Array();
	$.each($("input[name='post[]']:checked"), function() {
	  values.push($(this).val());
	});
	//console.log(values);
	$("#id_addition_list").val(values);
	document.forms["xml_export"].submit();
}


</script>
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
<div class="wrap">

<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>

<h2>
Dashboard	
</h2>

<div class="btn_container" style="float:right;margin-right: 10px;">
	<!-- commented code for import xml 11th January
	<form name="xml_import" id="id_xml_import" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" id="id_list_type" name="list_type" value="addition" >
		<input type="file" name="xml_file_val" id="id_xml_file" >
		<input type="submit" value="Import xml" name="import_xml" >
		
	</form>--->
	<form name="xml_export" id="id_xml_export" method="post" action="<?php echo plugins_url('',__FILE__); ?>/generate_xml.php" >
		<input type="hidden" id="id_addition_list" name="booking_list" value="">

		<input type="hidden" id="id_type" name="type" value="booking">
		<input type="button" value="Export xml" name="export_xml" onclick="generate_xml();" style="margin-top:4px;">
	</form>
	
</div>
<!--
<table width="1171px"><tr width="100%"><td width='20%'>Seat Date</td><td width='15%'>Seat Time</td><td width='15%'>Client Name</td><td width='10%'>Client Phone</td><td width='15%'>Base</td><td width='15%'># of Additions</td><td width='10%'></td></tr></table>
-->
<?php if(sizeof($objListBase)>0){ 
	$seat_alias = get_option("bkx_alias_seat","Seat");
	$base_alias = get_option("bkx_alias_base","Base");
	$addition_alias = get_option('bkx_alias_addition','Addition');
	?>
<table width="100%">
<tbody id="accordion">

<tr width="100%" class="accordion-header">
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
	<td></td>
	<td width='20%' style='width:221px;'><?php echo $seat_alias; ?> Date</td>
	<td width='15%'><?php echo $seat_alias; ?> Time</td>
	<td width='15%'>Client Name</td>
	<td width='15%'>Client Phone</td>
	<td width='15%'><?php echo $base_alias; ?></td>
	<td width='15%'># of <?php echo $addition_alias; ?></td>
	<td width='5%'></td>
</tr>

<tr id="hidden_row" style="height:0px !important;display:none !important;"><td></td></tr>
<?php
foreach($objListBase as $key=>$val)
{
	$result_count = 0 ;
	if($val->addition_ids!="")
	{
		$addition_ids = $val->addition_ids;
		$addition_ids = rtrim($addition_ids,",");
		$query_addition = "SELECT * FROM `bkx_addition` WHERE addition_id IN(".trim($addition_ids).")";
		$result_addition = $wpdb->get_results($query_addition);
		$result_count = $wpdb->num_rows;
	}
	
	//echo "<tr width='100%' class='accordion-heading' href='".$val->booking_record_id."'>";
	echo '<tr width="100%" class="accordion-heading" href="'.$val->booking_record_id.'">';
	echo '<th scope="row" class="check-column"><input type="checkbox" name="post[]" value="<?php echo $value->addition_id; ?>"></th>';	
	//echo "<td width='20%'>".$val->booking_date."</td><td width='15%'>".$val->booking_time."</td><td width='15%'>".$val->first_name." ".$val->last_name."</td><td width='15%'>".$val->phone."</td><td width='15%'>".$val->base_name."</td><td width='15%'>".$result_count."</td><td width='5%'>More Details</td></tr>";
	echo '<td width="20%">'.$val->booking_date.'</td>
	<td width="15%">'.$val->booking_time.'</td>
	<td width="15%">'.$val->first_name.' '.$val->last_name.'</td>
	<td width="15%">'.$val->phone.'</td>
	<td width="15%">'.$val->base_name.'</td>
	<td width="15%">'.$result_count.'</td>
	<td width="5%">More Details</td>
	</tr>';
		
    ?>
	<tr> 
	<td width="100%" colspan="3">
	<div class="centeralign" style="">
	<div class="leftalign">
	<div class="leftalign">
	Client Name: <?php echo $val->first_name." ".$val->last_name; ?>
	</div>
	<div>	
	 Client Phone: <?php echo $val->phone; ?> 
	</div>
	</div>
	<div>	
	Client Email: <?php echo $val->email; ?>
	</div>
	</div> <!--end centeralign-->
	<br/>
	<div class="centeralign">
	<div class="leftalign">
	Base Type: <?php echo $val->base_name; ?>&mbsp;&nbsp;
	</div>
	<div>	
	 Client Comments:
	</div>
	</div>
	<br/>
	<div class="centeralign">
	<div>
	Additions:
	<ul>
	<?php
	if(isset($result_addition) and sizeof($result_addition)>0)
	{
		$addition_arr = array();
		foreach($result_addition as $index=>$value)
		{
			echo "<li>".$value->addition_name."</li>";
			$time_option = $value->addition_time_option;
			if($time_option=="H")
			{
				$time_taken = $value->addition_hours;
			}
			if($time_option=="D")
			{
				$time_taken = $value->addition_hours*24;
			}
			if($time_option=="M")
			{
				$time_taken = $value->addition_hours*24*30;
			}
			$addition_arr[$value->addition_id]['time'] = $time_taken;
			  
		}
	}
	?>
	</ul>
	</div>
	</div>
	</br>
	<input type="hidden" id="address_val<?php echo $val->booking_record_id; ?>" value="<?php echo $val->street.",".$val->city.",".$val->state; ?>">
	<div class="centeralign">
	<div class="leftalign">
	Address: <?php echo $val->street.",".$val->city.",".$val->state.", Postcode-".$val->postcode; ?>
	</div>
	<div id="map_canvas<?php echo $val->booking_record_id; ?>" style="width: 50%; height: 300px"></div>
	</div> <!--end centeralign-->
	</br>
	<div class="centeralign">
	<div class="leftalign">
	Time Breakdown:
	</div>
	<div id="seat_rect_<?php echo $val->booking_record_id; ?>" class="seat_rectangle" style="padding: 5px;width: 50%; min-height: 300px;height:auto;background-color:white;float:left">	
	
	</div>
	<div style="width: 46%;
float: right;
min-height: 309px;background:#DADADA;">
	Payment Method : <?php echo $val->payment_method; ?><br/>
	Payment Status : <?php echo $val->payment_status; ?><br/>
	Total Price : $<?php echo $val->total_price; ?><br/>
	</div>
    </div>
	
	</td>
	</tr>
<?php } ?>

</tbody>
</table>
<?php pagination('bottom',$total_items,$total_pages,$per_page); ?>
<?php
}
else
{ 
	echo "<h4>No Record Found</h4>";
}
?>

<!--
<iframe src="https://www.google.com/calendar/embed?src=hd5msnmsdr47h0osp1eb2doh88%40group.calendar.google.com&ctz=Asia/Calcutta" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
-->
</div><!--WRAP ENDS-->

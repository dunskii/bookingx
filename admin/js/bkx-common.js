/*----------------------------------------------------------*/

/**
 * DHTML phone number validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

// Declaring required variables
var digits = "0123456789";
// non-digit characters which are allowed in phone numbers
var phoneNumberDelimiters = "()- ";
// characters which are allowed in international phone numbers
// (a leading + is OK)
var validWorldPhoneChars = phoneNumberDelimiters + "+";
// Minimum no of digits in an international phone no.
var minDigitsInIPhoneNumber = 10;

/**
 *	While Edit order below variable used 
 */


function isInteger(s)
{   var i;
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}
function trim(s)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not a whitespace, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (c != " ") returnString += c;
    }
    return returnString;
}
function stripCharsInBag(s, bag)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function checkInternationalPhone(strPhone){
var bracket=3
strPhone=trim(strPhone)
if(strPhone.indexOf("+")>1) return false
if(strPhone.indexOf("-")!=-1)bracket=bracket+1
if(strPhone.indexOf("(")!=-1 && strPhone.indexOf("(")>bracket)return false
var brchr=strPhone.indexOf("(")
if(strPhone.indexOf("(")!=-1 && strPhone.charAt(brchr+2)!=")")return false
if(strPhone.indexOf("(")==-1 && strPhone.indexOf(")")!=-1)return false
s=stripCharsInBag(strPhone,validWorldPhoneChars);
return (isInteger(s) && s.length >= minDigitsInIPhoneNumber);
}

function ValidateForm(){
	var Phone=document.frmSample.txtPhone
	
	if ((Phone.value==null)||(Phone.value=="")){
		alert("Please Enter your Phone Number")
		Phone.focus()
		return false
	}

	return true
 }
/**
 *method disableMonths
 *This function is used to disable the particular days of weeks based on seat selected
 *@params date
 *@return boolean true or false
 */
function disableMonths(date)
{
		var month = date.getMonth();
		var monthsToDisable = new Array();
		monthsToDisable = [1,2];
		for (i = 0; i < monthsToDisable.length; i++) {
		            if (jQuery.inArray(month, monthsToDisable) == -1) {
		                return [false];
		            }
		        }
		return [true];

}
/**
 *method zeroPad
 *This method returns the number in input after leading zero padding upto the given places in the second parameters specified
 *@params num, places 
 *@return number after padding
 */
function zeroPad(num, places) {
  var zero = places - num.toString().length + 1;
  return Array(+(zero > 0 && zero)).join("0") + num;
}

/**
 *method bkx_disableSpecificWeekDays
 *This function checks wether the date of the datepicker should be disabled or not,
 *based on the months and  days avaialability of selected seat
 *@params date 
 *@return boolean True or False with current input date and class to be applied to the current date cell
 */
function bkx_disableSpecificWeekDays(date) {
		
		var x=new Date();

		x.setFullYear(2014,0,27);
		//var comp = dates.compare(x,date)
		//alert(date);
		if(date.getTime() == x.getTime())
		{
			return [false];
		}
		
		var flag_val = jQuery("#id_days_availability_certain").val();
		var option_val = jQuery("#id_days_availability").val();
		var flag_month_val = jQuery("#id_months_availability").val();
		var option_month_val = jQuery("#id_months_availability_certain").val();

		var booked_days = edit_order_data.booked_days;
		var biz_vac_days = edit_order_data.biz_vac_days;
		var biz_pub_days = edit_order_data.biz_pub_days;
		var collect_disable_days = new Array();

		var base_days 	= jQuery('#id_base_days').val();

		// if(booked_days === undefined || booked_days == 0 ){}
		// else{ var booked_days_arr = booked_days.split(",");}

		// if(booked_days_arr === undefined || booked_days_arr == '' ){}
		// else{for (var i = 0; i <= booked_days_arr.length; i++){collect_disable_days.push(booked_days_arr[i]);}}

		if(biz_vac_days === undefined || biz_vac_days == '' ){}
		else{ var biz_vac_arr = biz_vac_days.split(","); for (var i = 0; i <= biz_vac_arr.length; i++){collect_disable_days.push(biz_vac_arr[i]);}}

		//console.log(biz_vac_arr);				

		if(biz_pub_days === undefined || biz_pub_days == '' ){}
		else{ var biz_pub_arr = biz_pub_days.split(",");  for (var i = 0; i <= biz_pub_arr.length; i++){collect_disable_days.push(biz_pub_arr[i]);}}

		//alert(jQuery("#id_days_availability").val());
		var daysToDisable = new Array();
		var temp_option_val = option_val.split(",");
		//console.log(option_val);alert(option_val);
		for (x in temp_option_val)
		{
			if(temp_option_val[x]=="Monday")
			{
			daysToDisable.push(1);	
			}
			if(temp_option_val[x]=="Tuesday")
			{
			daysToDisable.push(2);	
			}
			if(temp_option_val[x]=="Wednesday")
			{
			daysToDisable.push(3);	
			}
			if(temp_option_val[x]=="Thursday")
			{
			daysToDisable.push(4);	
			}
			if(temp_option_val[x]=="Friday")
			{
			daysToDisable.push(5);	
			}
			if(temp_option_val[x]=="Saturday")
			{
			daysToDisable.push(6);	
			}
			if(temp_option_val[x]=="Sunday")
			{
			daysToDisable.push(0);	
			}

		}

		var temp_option_month_val = option_month_val.split(",");
		var monthsToDisable = new Array();

		for (x in temp_option_month_val)
		{
			if(temp_option_month_val[x]=="January")
			{
			monthsToDisable.push(0);	
			}
			if(temp_option_month_val[x]=="February")
			{
			monthsToDisable.push(1);	
			}
			if(temp_option_month_val[x]=="March")
			{
			monthsToDisable.push(2);	
			}
			if(temp_option_month_val[x]=="April")
			{
			monthsToDisable.push(3);	
			}
			if(temp_option_month_val[x]=="May")
			{
			monthsToDisable.push(4);	
			}
			if(temp_option_month_val[x]=="June")
			{
			monthsToDisable.push(5);	
			}
			if(temp_option_month_val[x]=="July")
			{
			monthsToDisable.push(6);	
			}
			if(temp_option_month_val[x]=="August")
			{
			monthsToDisable.push(7);	
			}
			if(temp_option_month_val[x]=="September")
			{
			monthsToDisable.push(8);	
			}
			if(temp_option_month_val[x]=="October")
			{
			monthsToDisable.push(9);	
			}
			if(temp_option_month_val[x]=="November")
			{
			monthsToDisable.push(10);	
			}
			if(temp_option_month_val[x]=="December")
			{
			monthsToDisable.push(11);	
			}
		}
	
		 var day = date.getDay();
	
		if(flag_month_val=="Y")
		{
			var day = date.getDay();

			var month = date.getMonth();
	
			for (i = 0; i < monthsToDisable.length; i++)
			{
					 if (jQuery.inArray(month, monthsToDisable) == -1)
					{
							return [false];
					}
					else
					{
						for (i = 0; i < daysToDisable.length; i++) {
							if (jQuery.inArray(day, daysToDisable) == -1) {
							return [false];
							}
						}
						
						var temp_date = zeroPad(date.getMonth()+1, 2)+"/"+zeroPad(date.getDate(),2)+"/"+date.getFullYear();
						var classname = '';
						/*jQuery.post(url_obj.plugin_url+'/if_date_booked.php',{'bookigndate':temp_date}, function(data) {
							///jQuery("#booking_details_value").html(data);
							//alert(data);
							classname = data;
						 });*/
						return [true];
						
					}
		      }
			 
		}
		else
		{

			 
			// //alert('else');alert(daysToDisable);console.log(daysToDisable);
			// //alert(day);alert(date);
			// for (i = 0; i < daysToDisable.length; i++) {
			// 	    if (jQuery.inArray(day, daysToDisable) == -1) {
			// 		return [false];
			// 	    }
			// }
			
			// var temp_date = zeroPad(date.getMonth()+1, 2)+"/"+zeroPad(date.getDate(),2)+"/"+date.getFullYear();
			// var classname = '';
			// return [true];
			$checkdate = jQuery.datepicker.formatDate('mm/dd/yy', date);
			//console.log($checkdate);
			for (i = 0; i < daysToDisable.length; i++) {
				    if (jQuery.inArray(day, daysToDisable) == -1) {
						return [false];
				    }
			}
			var booked_days = jQuery('#id_booked_days').val();
			var base_days 	= jQuery('#id_base_days').val();
			// Days disable functionality disable for now
			if(collect_disable_days === undefined || collect_disable_days =='' ){  }
			else
			{
				for (var i = 0; i <= collect_disable_days.length; i++)
				{
					//console.log(collect_disable_days[i]+'=='+$checkdate );
					if(collect_disable_days[i] == $checkdate)
					{
						return [false];
					}
				}
			}
			var temp_date = zeroPad(date.getMonth()+1, 2)+"/"+zeroPad(date.getDate(),2)+"/"+date.getFullYear();
			var classname = '';
			return [true];
		}
   }
/**
 *method bkx_change_timepicker_val
 *This function is triggered after timepicked valu changed
 *@params date
 *@return void
 */
function bkx_change_timepicker_val(date)
{
	//	alert(date);
	var edit_base_id,base_id;
	edit_base_id = edit_order_data.base_id;
	seat_id = edit_order_data.seat_id;
	order_id = edit_order_data.order_id;
	jQuery("#booking_details_value").css("display","block");
	jQuery("#booking_details_value").html("<img src='"+url_obj.plugin_url+"/images/loading.gif' width='200px;'>");
	//jQuery("#booking_details_value").html("hello");
	jQuery("#id_input_date").val(date);
	//get time display options
	if(edit_base_id!=''){
		base_id = edit_base_id;
	}else{
		base_id = jQuery('#id_base_selector').val();
	}

	jQuery.post(url_obj.bkx_ajax_url,{
				action : 'bkx_displaytime_options',
				bookigndate:date,
				service_id:base_id,
				order_id: order_id,
				seatid: seat_id
	}, function(data) {
		jQuery("#booking_details_value").html(data);
	 });

	//unset all timepicker selected values
	jQuery("#id_booking_time_from").val('');
	jQuery("#id_display_success").html("");
	jQuery("#id_can_proceed").val(0);

	jQuery("#time_options").html("<option>Select a time</option>");
	var myTime=new Array("12:00AM","12:30AM","01:00AM", "01:30AM");
	var option_text = "";
	for(temp in myTime)
	{
		option_text = option_text+"<option>"+myTime[temp]+"</option>";
	}
	jQuery("#time_options").append(option_text);
	
	
	var fromtime = jQuery('#id_time_availability_certain_from').val();
	var tilltime = jQuery('#id_time_availability_certain_till').val();
	//alert(fromtime+"And "+tilltime)
	if(fromtime!="" && tilltime!="")
	{
		jQuery('#input_4_11_2').timepicker({minTime: fromtime, maxTime: tilltime, interval: 10});
	}
	else
	{
		jQuery('#input_4_11_2').timepicker();
	}
}
/**
 *method bkx_displaySecondForm
 *This function is to display second step form for booking
 *@access public
 *@return void
 */
function bkx_displaySecondForm()
{

	//alert(jQuery("#id_booking_duration_insec").attr("display-date-picker"));
	if(jQuery("#id_booking_duration_insec").attr("display-date-picker") == "1")
	 {
		jQuery( "#id_datepicker_extended" ).datepicker();
		var today = new Date();
		var booking_start_date = edit_order_data.booking_start_date;
		var $today_date = jQuery.datepicker.formatDate('mm/dd/yy', new Date());
		//jQuery('#id_datepicker').datepicker('setDate', booking_start_date);
		if(edit_order_data.order_id == ''){
			jQuery('#id_input_date').val($today_date);
		}
		if(booking_start_date == ""){
			booking_start_date = today;
		}
		jQuery( "#id_datepicker" ).datepicker({setDate: booking_start_date, minDate: today,beforeShowDay: bkx_disableSpecificWeekDays, onSelect: function(date){bkx_change_timepicker_val(date);}});

	 }
	 else
	 {
		jQuery("#id_display_success").html("You Can Diretly Contact Administrator and proceed with booking payment");
		jQuery("#id_can_proceed").val(1);
	 }
}
function bkx_KeyUpFun( cp_from, paste_here, seprater )
{
	jQuery("#"+cp_from).keyup(function () {
      var paste_val = jQuery(this).val(); 
       if(paste_val == undefined){
       }else{
      	if(seprater == 'a'){jQuery("#"+paste_here).append(paste_val+' , ');}else{ jQuery("#"+paste_here).text(paste_val); }
      	
      }
    }).keyup();
}
 /*--------------------------------------------------------------------------------------------------------*/
jQuery(document).ready(function(){


	var get_width = jQuery(".bookingx_form_container").parent().width();
	if( get_width <= 650 ){
		jQuery('#field_4_display_booking').css("width","auto");
	}

	jQuery( "#bkx_id_add_custom_note" ).on( "click", function() {
		jQuery("#bkx_add_custom_note_loader").show();
		jQuery("#bkx_id_add_custom_note").hide();
		
		var booking_id = jQuery( "#bkx_booking_id" ).val();
		var bkx_custom_note = jQuery( "#bkx_custom_note" ).val();
		if(bkx_custom_note == "")
		{
			jQuery("#bkx_add_custom_note_err").html("Please add note.");
			jQuery("#bkx_add_custom_note_loader").hide();
			jQuery("#bkx_id_add_custom_note").show();
			return false;
		}
 		if(confirm("Are you sure want to add note?"))
		{
			jQuery("#bkx_add_custom_note_err").hide();
			var data = {
	            "action": "bkx_action_add_custom_note",
	            "bkx_custom_note": bkx_custom_note,
	            "booking_id" : booking_id
	        };
 	        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	        jQuery.post(ajaxurl, data, function(response) {
	        	jQuery("#bkx_add_custom_note_loader").hide();
	        	jQuery("#bkx_id_add_custom_note").hide();
	             location.reload();
	        });
 		}
	});
 		var order_id 	= edit_order_data.order_id,
	 	edit_seat_id 	= edit_order_data.seat_id,
	 	edit_base_id 	= edit_order_data.base_id,
	 	edit_extra_id 	= edit_order_data.extra_id,
	 	extended 		= edit_order_data.extended,
 		seat_base_edit_mode = edit_order_data.seat_base_edit_mode;

if(order_id!= ''){
	jQuery(".select_date_n_time").hide();
}
 	//alert(edit_extra_id)
		jQuery(".fc-widget-content[data-date='20130105']").addClass("disabled-slot");
		
		jQuery("#time_options").change(function(){
			alert("time selected");
		});


  jQuery(function() {		
        jQuery( "#bkx_progressbar_wrapper_4" ).progressbar({
            value: 25
        });
     });

		 jQuery(function() {
			 jQuery("#bkx_next_button_4_7").click(function(){
  			 });
		});
		 
 		jQuery('#id_selected_seat').val(edit_seat_id);
 		jQuery("#myInputSeat").val(edit_seat_id);
 		jQuery("#id_base_selector").val(edit_base_id);
 		jQuery('#myInputSeat option[value="'+edit_seat_id+'"]').attr('selected', true);
 		jQuery('#id_input_extended_time').val(extended);
 		
		
		if(edit_seat_id !='' && seat_base_edit_mode == 0){
			bkx_get_total_price('');
			jQuery('#bkx_target_page_number_4').val('2');
 			bkx_validate_form(1,2);
 		}
 		if( typeof jQuery('#id_selected_seat').val() === 'undefined' || jQuery('#id_selected_seat').val() === null ){
		    // Do stuff
		}
 		else
        {
            var seat_temp = jQuery('#id_selected_seat').val();
             jQuery("#id_seat_name").val(jQuery(this).find('option:selected').text());
            if(seat_temp!=""){
                   jQuery('#id_base_selector').attr('disabled',false);
            }
            if(seat_temp==""){
                   jQuery('#id_base_selector').attr('disabled',true);
            }                     
            
		 jQuery.post(url_obj.bkx_ajax_url, { seatid: seat_temp , action : 'bkx_get_base_on_seat' , order : 'edit'}, function(data) {
		 		 
				if(data!="error")
				{
 					var temp_obj = jQuery.parseJSON(data);
 					var temp_option='<option value="">'+url_obj.select_a_text+' '+url_obj.base+'</option>';
					var base_obj = temp_obj['base_list'];
					var $selected = '';
 					for(x in base_obj)
					{
						if(edit_base_id == base_obj[x]['base_id']){
							 temp_option += "<option value='"+base_obj[x]['base_id']+"' selected='selected'>"+base_obj[x]['base_name']+" - $"+base_obj[x]['base_price']+" - "+base_obj[x]['base_time']+"</option>";
						}else
						{
							temp_option += "<option value='"+base_obj[x]['base_id']+"'>"+base_obj[x]['base_name']+" - $"+base_obj[x]['base_price']+" - "+base_obj[x]['base_time']+"</option>";
						}
 					}
					/*  Updated By : Madhuri Rokade 
						Reason : To check the Prepayment process
					*/
					jQuery('#id_base_selector option[value="'+edit_base_id+'"]').attr('selected', true);

					if(temp_obj['seat_is_booking_prepayment']=='N')
					{
						jQuery("#flag_stop_redirect").val('stop');
					}
					else{
						
						jQuery("#flag_stop_redirect").val('');
					}
 					jQuery('#id_base_selector').empty().append(temp_option);
					jQuery("#id_time_availability").val(temp_obj['seat_is_certain_time']);
					jQuery("#id_time_availability_certain_from").val(temp_obj['seat_time_from']);
					jQuery("#id_time_availability_certain_till").val(temp_obj['seat_time_till']);
					jQuery("#id_days_availability").val(temp_obj['seat_days']);
					jQuery("#id_days_availability_certain").val(temp_obj['seat_is_certain_days']);
					jQuery("#id_months_availability").val(temp_obj['seat_is_certain_month']);
					jQuery("#id_months_availability_certain").val(temp_obj['seat_months']);
 				}
				else
				{	
					alert("Couldn't list bases");
				}
			});
    	}
		 jQuery('#myInputSeat').bind('change', function(){
			//var oldvalue = ???
				 var seat_temp = jQuery(this).val();
				 var seat_temp_name = jQuery(this).html();
				 //alert(seat_temp);
				 jQuery("#id_seat_name").val(jQuery(this).find('option:selected').text());
				 if(seat_temp!=""){
						jQuery('#id_base_selector').attr('disabled',false);
				 }
				 if(seat_temp==""){
						jQuery('#id_base_selector').attr('disabled',true);
				 }

		jQuery('#bkx_seat_name').html(edit_order_data.seat_alias+ ' name : ' +  jQuery(this).find('option:selected').text());
			   bkx_seat_to_base(seat_temp);
		});
                        
		jQuery('#id_base_selector').bind('change', function(){
			var base_temp1 = jQuery(this).val();
			jQuery('#bkx_extra_data').html('');
			jQuery('#bkx_base_name').html(edit_order_data.base_alias+ ' name : ' + jQuery(this).find('option:selected').text());
			jQuery.post(url_obj.bkx_ajax_url, { baseid1: base_temp1, mob: 'no'  , action : 'bkx_get_base_on_seat' }, function(data) {
				var base_data = jQuery.parseJSON(data);
  				jQuery('#base_location_type').val(base_data.base_location_type);
 				if(base_data.base_location_type == 'FM')
				{
 						jQuery('#field_4_18').hide();
						jQuery('#mobile_only').css('display', 'block');
						jQuery('#user_details').css('display', 'none');
						jQuery('#bkx_page_footer_details').css('display', 'none');
				}
 				if(base_data.base_location_type == 'Mobile')
				{
					jQuery('#mobile_only').css('display', 'none');
					jQuery('#user_details').css('display', 'block');
					jQuery('#field_4_18').show();
 				}
 				if(base_data.base_location_type == 'Fixed Location')
				{
					jQuery('#mobile_only').css('display', 'none');
					jQuery('#user_details').css('display', 'block');
					jQuery('#field_4_18').hide();
 				}
 				if(data!="error")
				{
					if(data == 'N')
					{
						jQuery('#field_4_18').remove();
						jQuery('#mobile_only').css('display', 'block');
						jQuery('#user_details').css('display', 'none');
						jQuery('#bkx_page_footer_details').css('display', 'none');				
					}
				}
				else
				{	
					alert("Couldn't list bases");
				}
			});
 			jQuery("input[name=mobile_only_choice]:radio").change(function () {
				jQuery('#user_details').css('display', 'block');
				jQuery('#bkx_page_footer_details').css('display', 'block');
				jQuery('li#field_4_18').remove();
 			var selected_radio = jQuery("input[name='mobile_only_choice']:checked").val();
			if(selected_radio == "YES" )
			{				
				var x = jQuery('ul#4_form_user_details');
				x.append('<li id="field_4_18" class="gfield"><label class="gfield_label" for="input_4_18_1">Address</label><div class="ginput_complex ginput_container" id="input_4_18"><span class="ginput_full" id="input_4_18_1_container"><input type="text" name="input_street" id="id_street" value="" tabindex="18"><label for="input_4_18_1" id="input_4_18_1_label">Street</label></span><br><span class="ginput_left" id="input_4_18_3_container"><input type="text" name="input_city" id="id_city" value="" tabindex="19"><label for="input_4_18_3" id="input_4_18.3_label">City</label></span><br><span class="ginput_right" id="input_4_18_4_container"><input type="text" name="input_state" id="id_state" value="" tabindex="21"><label for="input_4_18_4" id="input_4_18_4_label">State / Province / Region</label></span><br><span class="ginput_left" id="input_4_18_5_container"><input type="text" name="input_postcode" id="id_postcode" value="" tabindex="22"><label for="input_4_18_5" id="input_4_18_5_label">Zip / Postal Code</label></span><input type="hidden" class="bkx_hidden" name="input_18.6" id="input_4_18_6" value=""></div></li>');				
			}
			else 
			{
				jQuery('li#field_4_18').remove();
 			}
 		});
 		});

		jQuery('#id_base_selector').bind('change', function(){
			var base_temp = jQuery(this).val();
			jQuery.post(url_obj.bkx_ajax_url, {baseid: base_temp, loc: 'fixed', action : 'bkx_get_base_on_seat' }, function(data) {
				if(data!="error")
				{
					if(data == 'Y')
					{
 						jQuery('#field_4_18').remove();
					}
				}
				else
				{	
					alert("Couldn't list bases");
				}
			});

			
		});
 	var availableTags =
		[
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10"
        ];
        jQuery( "#id_input_extended_time" ).autocomplete({
            source: availableTags,
	    minLength: 0,
	    change: function( event, ui ) {
 			{
				jQuery("#id_clear_extended_time").css('visibility','visible');
 				bkx_get_total_price('');
			}		
		}
        }).focus(function(){
           	if (this.value == "")
                {
                    jQuery(this).autocomplete("search");
                }
         });
   	jQuery("#id_clear_extended_time").css('left','0px');
	jQuery("#id_clear_extended_time").click(function(){
		jQuery( "#id_input_extended_time" ).val('');
		jQuery("#id_clear_extended_time").css('visibility','hidden');
		bkx_get_total_price('');
	});
	jQuery("#id_clear_extended_time").css('visibility','hidden');

	jQuery('#id_base_selector').bind('change', function(){
		var base_temp = jQuery(this).val();
		bkx_base_to_addition(base_temp);
			
	});
	if(edit_order_data.base_id!='')
	{		
		bkx_base_to_addition(edit_order_data.base_id);
	}
 	jQuery(":checkbox").bind("click",function(event, ui){

	});
	
     });//close document.ready function 
	function bkx_get_total_price(thisval)
	{
		var temp = jQuery(thisval).val();
		var edit_base_id = 0;
		var edit_extra_arr = 0 ;
		var base_temp;

		edit_base_id = edit_order_data.base_id;
		edit_extra_arr = edit_order_data.extra_id;
		edit_base_extended = edit_order_data.extended;

		var addition_list = new Array();
			if(edit_base_id !='' && edit_base_id != 0){
				base_temp = edit_base_id;
			}else{
				 base_temp = jQuery('#id_base_selector').val();
			}

			if(edit_extra_arr !='' && edit_extra_arr != 0){
					addition_list = edit_extra_arr.split(',');
				}
				else
				{
					jQuery('.addition_name_class').each(function(index) {
						//alert(jQuery(this).val());
						if(jQuery(this).is(':checked')==true)
						{
							//console.log(jQuery(this).closest('label'));
							addition_list.push(jQuery(this).val());
						}
						else
						{
						//alert('not checked');
						}
					});
				}
		 if(addition_list != '')
		 {
			var bkx_extra_data_op = '';
			for (x in addition_list)
				{
					jQuery.post(url_obj.bkx_ajax_url, { action : 'bkx_get_extra_data', extra_id : addition_list[x]},
						function(data) {
									bkx_extra_data_op += ' '+ data + ',';
									jQuery('#bkx_extra_data').html(bkx_extra_data_op);
						});
				}
		 }
 		var base_extended = 0;
		if(edit_base_extended == '0'){
				var x=document.getElementById("id_input_extended_time");
				base_extended = x.value;
 		}else{
			base_extended = edit_base_extended;
		}
  		var seat_temp = jQuery('#myInputSeat').val();

		jQuery.post(url_obj.bkx_ajax_url, { action: 'bkx_get_total_price', seatid : seat_temp, baseid: base_temp,additionid : addition_list,extended : base_extended}, 	function(data) {
			jQuery('h4 span#total_price').html(data['total_price']);
			jQuery('#hidden_total_price').val(data['total_price']);
			jQuery('#deposit_price').val(data['deposit_price']);
			jQuery('h4 span#total_time_of_services_formatted').html(data['total_time_of_services_formatted']);
			jQuery('#seat_is_booking_prepayment').val(data['seat_is_booking_prepayment']);
		}, "json");

	}

function bkx_base_to_addition(base_temp)
{
	var edit_extra_arr = 0 ,$checked='';
	edit_extra_arr = edit_order_data.extra_id;
	var addition_list = new Array();
		if(edit_extra_arr !='' && edit_extra_arr != 0){
				 	addition_list = edit_extra_arr.split(',');
				 	var action = 'edit';
		}
		if(base_temp=="")
		{
			jQuery('#extended_time').hide();
			jQuery('#base_extended_calender').hide();
			jQuery('#id_addition_checkbox').html('');
		}
		else
		{
			jQuery.post(url_obj.bkx_ajax_url , { baseid: base_temp, action : 'bkx_get_if_base_extended' }, function(data) {
			
				if(data=="Y")
				{
					jQuery('#extended_time').show();
					jQuery('#base_extended_calender').show();
				}
				else if(data == 'N')
				{
					jQuery('#extended_time').hide();
					jQuery('#base_extended_calender').hide();
				}
			});
		}
		bkx_get_total_price('');
		var seat_id = jQuery('#myInputSeat').val();
		jQuery.post(url_obj.bkx_ajax_url, { baseid: base_temp, seat_id : seat_id, action : 'bkx_get_addition_on_base',order : action }, function(data) {
			var temp_obj = jQuery.parseJSON(data);
 			var temp_option='';
			if(temp_obj){
			temp_option='<label class="gchoice_5_2_1 gfield_label ">'+url_obj.select_a_text+' '+temp_obj[0].addition_alies+' '+url_obj.string_you_require+'</label>';
			}
			for (x in temp_obj)
			{
				if(edit_extra_arr !='' && edit_extra_arr != 0 && jQuery.inArray(temp_obj[x]['addition_id'], addition_list))
				{
					$checked = "checked";
				}
 			  temp_option += '<li class="gchoice_5_1"><input name="addition_name[]" '+$checked+' class="addition_name_class" id="addition'+temp_obj[x]['addition_id']+'" type="checkbox" value="'+temp_obj[x]['addition_id']+'" id="choice_5_1" tabindex="4" onchange="bkx_get_total_price(this);"><label for="choice_5_1">'+temp_obj[x]['addition_name']+' - $'+temp_obj[x]['addition_price']+' - '+temp_obj[x]['addition_time']+'</label></li>';
			}
 			jQuery('#id_addition_checkbox').html(temp_option);
 		});
 }
 function bkx_seat_to_base(seat_temp)
{
 	jQuery.post(url_obj.bkx_ajax_url, { action : 'bkx_get_base_on_seat', seatid: seat_temp }, function(data) {

        if(data!="error")
        {
                 var temp_obj = jQuery.parseJSON(data);
                 var temp_option='<option value="">'+url_obj.select_a_text+' '+url_obj.base+'</option>';
                var base_obj = temp_obj['base_list'];
                 for(x in base_obj)
                {
                     temp_option += "<option value='"+base_obj[x]['base_id']+"'>"+base_obj[x]['base_name']+" - $"+base_obj[x]['base_price']+" - "+base_obj[x]['base_time']+"</option>";
                }
                if(temp_obj['seat_is_booking_prepayment']=='N')
                {
                	jQuery("#flag_stop_redirect").val('stop');
                }
                else {
                     jQuery("#flag_stop_redirect").val('');
                }
                 jQuery('#id_base_selector').empty().append(temp_option);
                jQuery("#id_time_availability").val(temp_obj['seat_is_certain_time']);
                jQuery("#id_time_availability_certain_from").val(temp_obj['seat_time_from']);
                jQuery("#id_time_availability_certain_till").val(temp_obj['seat_time_till']);
                jQuery("#id_days_availability").val(temp_obj['seat_days']);
                jQuery("#id_days_availability_certain").val(temp_obj['seat_is_certain_days']);
                jQuery("#id_months_availability").val(temp_obj['seat_is_certain_month']);
                jQuery("#id_months_availability_certain").val(temp_obj['seat_months']);

        }
        else
        {	
                alert("Couldn't list bases");
        }
    });
}


function bkx_validate_form(source_val,destination_val)
{
	var error_list = new Array();

	var edit_base_id = 0;
 
 	edit_base_id = edit_order_data.base_id;
 	var edit_base_extended = edit_order_data.extended;
 	var seat_base_edit_mode = edit_order_data.seat_base_edit_mode;
	 

	if(source_val==1 && destination_val == 2)
	{
		var addition_list =  new Array();

		if(jQuery('#myInputSeat').val()=="")
		{
			error_list.push("Please select a seat");
		}
		 
		if(jQuery('#id_base_selector').val()=="" && edit_base_id == 0)
		{
			error_list.push("Please select a base");
		}
		if(jQuery("#extended_time").is(":visible"))
		{
			var extended_time = jQuery("#id_input_extended_time").val();
						
			if(extended_time== "" || extended_time== undefined)
			{
				error_list.push("Please enter extended base time");
			}
		}
		//alert(jQuery('.addition_name_class').length);
		if(jQuery('.addition_name_class').length>0)
		{
			jQuery('.addition_name_class').each(function(index) {
				//alert(jQuery(this).is(':checked'));
				if(jQuery(this).is(':checked')==true)
				{
					addition_list.push(jQuery(this).val());
				}				

			});
		}
	}

	if(source_val==2 && destination_val == 3)
	{
		//alert(jQuery("#id_can_proceed").val());
		if(jQuery("#id_can_proceed").val() == "0")
		{
			if(jQuery("#id_datepicker").val()=="")
			{
				error_list.push("Please choose a date");
			}
			if(jQuery("#base_extended_calender").is(":visible"))
			{
				if(jQuery("#id_datepicker_extended").val()=="")
				{
					error_list.push("Please choose end date");		

				}
			}
 			if(jQuery("#time_options").val()=="")
			{
				error_list.push("Please choose time");		
			}
			if(jQuery("#id_can_proceed").val()==0)
			{
				error_list.push("Please select appropriate date time");		
			}
		}


	}
	if(source_val==3 && destination_val == 4)
	{
			bkx_KeyUpFun('id_firstname','bkx_fname','');
			bkx_KeyUpFun('id_lastname','bkx_lname','');
			bkx_KeyUpFun('id_phonenumber','bkx_phone','');
			bkx_KeyUpFun('id_email','bkx_email','');
			bkx_KeyUpFun('id_street','bkx_address','a');
			bkx_KeyUpFun('id_city','bkx_address','a');
			bkx_KeyUpFun('id_state','bkx_address','a');
			bkx_KeyUpFun('id_postcode','bkx_postcode','');

		if(jQuery("#id_firstname").val()=="")
		{
			error_list.push("Please enter your first name");
		}
		if(jQuery("#id_lastname").val()=="")
		{
			error_list.push("Please enter your last name");
		}
		if(jQuery("#id_phonenumber").val()=="")
		{
			error_list.push("Please enter your phone");
		}
		else
		{
 		}
		if(jQuery("#id_email").val()=="")
		{
			error_list.push("Please enter your email");
		}
		else
		{
			var temp_err = bkx_ValidateEmail(jQuery("#id_email").val());
			if(temp_err != true)
			{
				error_list.push(temp_err);
			}
		}
		var selected_radio = jQuery("input[name='mobile_only_choice']:checked").val();
		var base_location_type = jQuery("#base_location_type").val();

		if(selected_radio == "YES" || base_location_type == "Mobile"  )
		{
			if(jQuery("#id_street").val()== "")
			{
				error_list.push("Please enter your Street.");
			}
			if(jQuery("#id_city").val()== "")
			{
				error_list.push("Please enter your City.");
			}
			if(jQuery("#id_state").val()== "")
			{
				error_list.push("Please enter your State / Province / Region.");
			}
			if(jQuery("#id_postcode").val()== "")
			{
				error_list.push("Please enter your Zip / Postal Code.");
			}
		}

	}

	temp_err='';
	for(var i=0; i<error_list.length; i++)
	{
		temp_err = temp_err+"<p><strong>"+error_list[i]+"</strong></p>";
	}
	if(temp_err!='')
	{
		jQuery(".error").html(temp_err);
 		return false;
	}
	else
	{
		var seat_temp = jQuery('#myInputSeat').val();
		var booking_date = jQuery("#id_input_date").val();
		var start_time = jQuery("#id_booking_time_from").val();
		var booking_duration = jQuery("#id_booking_duration_insec").val();

		jQuery(".error").html('');
		jQuery( "#bkx_progressbar_wrapper_4" ).progressbar({
			value: 25*destination_val
		});
		if(destination_val == 3){
			jQuery('h3.bkx_progressbar_title').html('');
		}else{
			jQuery('h3.bkx_progressbar_title').html(url_obj.step_text+' '+destination_val+' '+url_obj.step_text_of+' 4');
		}
		
 		var edit_extra_arr = 0 ;
		edit_extra_arr = edit_order_data.extra_id;
		var addition_list = new Array();
		if(edit_extra_arr !='' && edit_extra_arr != 0)
		{
			addition_list = edit_extra_arr.split(',');
		}
		else
		{
			jQuery('.addition_name_class').each(function(index) {
 				if(jQuery(this).is(':checked')==true)
				{
					addition_list.push(jQuery(this).val());
				}
				else
				{
 				}
			});
		}
 
		if(source_val==1 && destination_val == 2)
		{
  			var seat_temp = jQuery('#myInputSeat').val();

			var base_temp = jQuery('#id_base_selector').val();
			var mob_only = jQuery('#id_selected_base').val();

			if(edit_base_id!='')
			{
				base_temp = mob_only = edit_base_id;
			}
			//end code for getting selected seat, base and addition
 			jQuery.post(url_obj.bkx_ajax_url, { action : 'bkx_get_duration_booking', seatid : seat_temp, baseid: base_temp,additionid : addition_list}, 	function(data) {
 				var temp_data = jQuery.parseJSON(data);
 				jQuery("#display_total_duration").html(temp_data['time_output']);
				jQuery("#id_booking_duration_insec").val(temp_data['totalduration_in_seconds']);
				var result = temp_data['result'];
				jQuery("#id_booking_duration_insec").attr('display-date-picker', result);
				bkx_displaySecondForm();
				jQuery("#id_total_duration").val(temp_data['time_output']);
				jQuery("span#id_selected_addition").html(temp_data['addition_list']);
			});
			var booking_start_date = edit_order_data.booking_start_date;
  			bkx_change_timepicker_val(booking_start_date);
		}
		jQuery("span#id_selected_base").html(jQuery("#id_base_selector option:selected").text());
 		if(source_val==3 && destination_val == 4)
		{	
 			var seat_temp = jQuery('#myInputSeat').val();
			var base_temp = jQuery('#id_base_selector').val();
			if(edit_base_id!='')
			{
				base_temp = mob_only = edit_base_id;
			}
 			var base_extended = 0;
			if(edit_base_extended == '' || edit_base_extended == 0){
					var input_extended = document.getElementById("id_input_extended_time");
					base_extended = input_extended.value;
			}else{
					base_extended = edit_base_extended;
			}
 			jQuery.post(url_obj.bkx_ajax_url, { action : 'bkx_get_duration_booking', seatid : seat_temp, baseid: base_temp, extended : base_extended, additionid : addition_list}, 	function(data) {
				var temp_data = jQuery.parseJSON(data);
				jQuery("#id_estimated_time").html(temp_data['time_output']);
				var currency = temp_data['currency'];
				jQuery("#id_total_duration").val(temp_data['time_output']);
 				jQuery(".booking_summary_data").html(temp_data['booked_summary']);
				jQuery(".booking_summary_cost").html('<li> <b> '+url_obj.total_cost+' : </b>'+temp_data['currency'] + ' '+ temp_data['total_price'] +'</li>');
				jQuery(".booking_summary_grand_total").html('<li> <b> '+url_obj.total_tax+' ('+ temp_data['tax_rate'] +'% '+ temp_data['tax_name'] +' ): </b>'+temp_data['currency'] + ' ' + temp_data['total_tax'] +'</li><li> <b> '+url_obj.grand_total+' : </b>'+temp_data['currency'] + ' '+ temp_data['grand_total'] +'</li>');

				if(temp_data['booking_customer_note']!= 'zero'){
				jQuery(".booking_customer_note").html('<li> <b> '+url_obj.note+' : </b>'+ temp_data['booking_customer_note'] +'</li>');
				}
			});
 			jQuery(".booking_summary_date").html('<li> <b> '+url_obj.date_time+' : </b>'+ jQuery("#id_datepicker").val() +' at '+ jQuery("#id_booking_time_from").val() +'</li>');
			jQuery("span#id_selected_date").html(jQuery("#id_datepicker").val());
 			jQuery("span#id_selected_time").html(jQuery("#id_booking_time_from").val());
			jQuery("span#id_selected_seat").html(jQuery("#myInputSeat option:selected").text());
 			jQuery("#id_totalling").html("$"+jQuery("#total_price").html());
  			var seat_is_booking_prepayment = jQuery("#seat_is_booking_prepayment").val();
 		}
		bkx_check_slot_is_available(seat_temp,start_time,booking_duration,booking_date,source_val,destination_val);
 	}
}
   function bkx_ValidateEmail(mail)
    {  
     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))  
      {  
        return (true)  
      }  
        var error = "You have entered an invalid email address!"; 
        return (error);  
    }  
 jQuery('div.app_timetable_cell.free').bind('click', function() {
    //alert("hii+++");
	return false;
});
	//on click of any time slot
jQuery("div").on("click", "div.app_timetable_cell.free", function() {
 	//get the clicked time
	jQuery("#booking_details_value div").removeClass("selected-slot");
	var start_time = jQuery(this).attr("id");
	var booking_duration = jQuery("#id_booking_duration_insec").val();
	var seat_temp = jQuery('#myInputSeat').val();
	var booking_date = jQuery("#id_input_date").val();

	jQuery("#bkx_booking_date").html(booking_date);
	jQuery("#bkx_booking_time").html(jQuery("#id_total_duration").val());
	jQuery("#bkx_booking_status").html('Pending');
	
	jQuery("#bkx_booking_total").html(jQuery("#total_price").html());
 	jQuery.post(url_obj.bkx_ajax_url, { action: 'bkx_check_ifit_canbe_booked', seatid: seat_temp, start:start_time, bookingduration: booking_duration,bookingdate:  booking_date}, function(data) {
		var temp_obj = jQuery.parseJSON(data);
		var result = temp_obj['result'];
		var starting_slot = temp_obj['starting_slot'];
		var $process ;

		if(parseInt(result) == 1)
		{
			jQuery("#id_booking_time_from").val(start_time);
			//jQuery("#id_display_success").html("You Can book this slot");
			jQuery("#id_can_proceed").val(1);
			var selected_start = starting_slot + 1;
			var selected_end = starting_slot + temp_obj['no_of_slots']+1;

			/**
			 * Added functionality for Check Booking Dates and slot is Available or not
			 * Also check if slot get out of Range on displayed time then restrict
			 * By : Divyang Parekh
 			 */

			//Highlight Selected Time Slots
			var good_ids = new Array();
			for (var i = selected_start; i <= selected_end; i++)
			{
				var $not_available = jQuery("."+i+"-is-notavailable").val();
				var $check_date_available = jQuery("."+i+"-is-free").val();
				var $full_date = jQuery("."+i+"-is-full").val();
				 if($check_date_available == undefined)
				 {
					 $process = 0;
					 break;
				 }
				else
				 {
					 if($not_available == undefined && $full_date == undefined )
					 {
						 $process = 1;
						 good_ids.push(i);
					 }
					 else
					 {
						 $process = 0;
						 break;
					 }
				 }
			}
 			if($process == 0)
			{
 				jQuery("#id_can_proceed").val(0);
			}
			else
			{
				for (var sel = 0; sel <= good_ids.length; sel++)
				{
					jQuery("#booking_details_value ."+good_ids[sel]).addClass("selected-slot");
				}
			}

		}
		else
		{
			//alert("You Cannot book this slot.");
			//jQuery("#id_display_success").html("<span class='error_booking'>You Cannot book this slot</span>");
			jQuery("#id_can_proceed").val(0);
		}
	});
	
	return false;
});

/**
 * Added function for Reassign Staff of Booking
 * By : Divyang Parekh
 */
function check_staff_availability(seat_id,start_time,durations,booking_date,booking_record_id)
{

//seatid: seat_temp, start:start_time, bookingduration: booking_duration,bookingdate:  booking_date
	var seat_id = seat_id;
	var booking_date  = booking_date;
	var start_time     = start_time;
	var durations     = durations;
	var booking_record_id = booking_record_id;

	jQuery.post(url_obj.bkx_ajax_url,{ action: 'bkx_check_staff_availability', seat_id: seat_id, start:start_time, bookingduration: durations,bookingdate:  booking_date ,booking_record_id : booking_record_id}, function(data) {

		var temp_obj = jQuery.parseJSON(data);
		var result = temp_obj['data'];
		if(parseInt(result) == 1)
		{
			alert('Booking Successfully Reassign.');
			window.location.reload();
		}
		else{
			alert('Sorry ! Something went wrong , Please try again.');
		}
	});

}
function bkx_check_slot_is_available(seat_temp,start_time,booking_duration,booking_date,source_val,destination_val)
{
	if(booking_date!='') {
		jQuery.post(url_obj.bkx_ajax_url, { 
			action: 'bkx_check_ifit_canbe_booked',
			seatid: seat_temp,
			start: start_time,
			bookingduration: booking_duration,
			bookingdate: booking_date
		}, function (data) {
			var temp_obj = jQuery.parseJSON(data);
			var result = temp_obj['result'];
			var starting_slot = temp_obj['starting_slot'];
			var $process;

			if (parseInt(result) == 1) {
				jQuery("#id_booking_time_from").val(start_time);
				//jQuery("#id_display_success").html("You Can book this slot");
				jQuery("#id_can_proceed").val(1);
				var selected_start = starting_slot + 1;
				var selected_end = starting_slot + temp_obj['no_of_slots'];

				/**
				 * Added functionality for Check Booking Dates and slot is Available or not
				 * Also check if slot get out of Range on displayed time then restrict
				 * By : Divyang Parekh
				 */

				//Highlight Selected Time Slots
				var good_ids = new Array();
				for (var i = selected_start; i <= selected_end; i++) {
					var $not_available = jQuery("." + i + "-is-notavailable").val();
					var $check_date_available = jQuery("." + i + "-is-free").val();
					var $full_date = jQuery("." + i + "-is-full").val();
					if ($check_date_available == undefined) {
						$process = 0;
						break;
					}
					else {
						if ($not_available == undefined && $full_date == undefined) {
							$process = 1;
							good_ids.push(i);
						}
						else {
							$process = 0;
							break;
						}
					}
				}
				if ($process == 0) {
					jQuery("#id_can_proceed").val(0);
				}
				else {
					for (var sel = 0; sel <= good_ids.length; sel++) {
						jQuery("#booking_details_value ." + good_ids[sel]).addClass("selected-slot");
					}
				}
				jQuery("#bkx_page_4_"+source_val).css('display','none');
				jQuery("#bkx_page_4_"+destination_val).css('display','block');

			}
			else
			{
				jQuery("#id_can_proceed").val(0);
				bkx_change_timepicker_val(booking_date);
				return false;
			}
		});
	}
	else
	{
		jQuery("#bkx_page_4_"+source_val).css('display','none');
		jQuery("#bkx_page_4_"+destination_val).css('display','block');
	}
}
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

var body = $('body');
var loader = $('.bookingx-loader');

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
	// if (checkInternationalPhone(Phone.value)==false){
	// 	alert("Please Enter a Valid Phone Number")
	// 	Phone.value=""
	// 	Phone.focus()
	// 	return false
	// }
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
		            if ($.inArray(month, monthsToDisable) == -1) {
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
 *method disableSpecificWeekDays
 *This function checks wether the date of the datepicker should be disabled or not,
 *based on the months and  days avaialability of selected seat
 *@params date 
 *@return boolean True or False with current input date and class to be applied to the current date cell
 */
function disableSpecificWeekDays(date) {

	var loader = $('.bookingx-loader');

	loader.show();

		var x=new Date();
		x.setFullYear(2014,0,27);
		//var comp = dates.compare(x,date)
		//alert(date);
		if(date.getTime() == x.getTime())
		{
			 loader.hide();
			return [false];
		}
		 
		var flag_val = $("#id_days_availability_certain").val();
		var option_val = $("#id_days_availability").val();
		var flag_month_val = $("#id_months_availability").val();
		var option_month_val = $("#id_months_availability_certain").val();
		var booked_days = url_obj.booked_days;
		var biz_vac_days = url_obj.biz_vac_days;
		var biz_pub_days = url_obj.biz_pub_days;
		var collect_disable_days = new Array();

		var base_days 	= $('#id_base_days').val();

		//console.log(booked_days);


		if(booked_days === undefined || booked_days == 0 ){}
		else{ var booked_days_arr = booked_days.split(",");}

		if(booked_days_arr === undefined || booked_days_arr == '' ){}
		else{for (var i = 0; i <= booked_days_arr.length; i++){collect_disable_days.push(booked_days_arr[i]);}}

		if(biz_vac_days === undefined || biz_vac_days == '' ){}
		else{ //$('#id_booked_days').val('yes'); 
				var biz_vac_arr = biz_vac_days.split(","); for (var i = 0; i <= biz_vac_arr.length; i++){collect_disable_days.push(biz_vac_arr[i]);}}

		if(biz_pub_days === undefined || biz_pub_days == '' ){}
		else{ //$('#id_booked_days').val('yes'); 
				var biz_pub_arr = biz_pub_days.split(",");  for (var i = 0; i <= biz_pub_arr.length; i++){collect_disable_days.push(biz_pub_arr[i]);}}

		//alert($("#id_days_availability").val());
		var daysToDisable = new Array();
		var temp_option_val = option_val.split(",");
		//console.log(option_val);alert(option_val);
		for (x in temp_option_val)
		{
			if(temp_option_val[x]=="Monday"){ daysToDisable.push(1);}
			if(temp_option_val[x]=="Tuesday"){ daysToDisable.push(2);}
			if(temp_option_val[x]=="Wednesday"){ daysToDisable.push(3);}
			if(temp_option_val[x]=="Thursday"){ daysToDisable.push(4); }
			if(temp_option_val[x]=="Friday"){ daysToDisable.push(5); }
			if(temp_option_val[x]=="Saturday") { daysToDisable.push(6);	 }
			if(temp_option_val[x]=="Sunday") { daysToDisable.push(0); }

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
					if ($.inArray(month, monthsToDisable) == -1)
					{
							 loader.hide();
							return [false];
					}
					else
					{
						for (i = 0; i < daysToDisable.length; i++) {
							if ($.inArray(day, daysToDisable) == -1) {
								 loader.hide();
								return [false];
							}
						}
						
						var temp_date = zeroPad(date.getMonth()+1, 2)+"/"+zeroPad(date.getDate(),2)+"/"+date.getFullYear();
						var classname = '';
						loader.hide(); 
						return [true];
						
					}
		    }
			
		}
		else
		{
			$checkdate = $.datepicker.formatDate('mm/dd/yy', date);
			for (i = 0; i < daysToDisable.length; i++) {
				    if ($.inArray(day, daysToDisable) == -1) {
				    	loader.hide();
						return [false];
				    }
			}

			var booked_days = $('#id_booked_days').val();
			var base_days 	= $('#id_base_days').val();
			if( booked_days == 'yes' && base_days != '' ) {
				if(collect_disable_days === undefined || collect_disable_days==''){}
				else
				{
					for (var i = 0; i <= collect_disable_days.length; i++)
					{
						if(collect_disable_days[i] == $checkdate)
						{
							return [false];
						}
					}
				}
			}
			
			var temp_date = zeroPad(date.getMonth()+1, 2)+"/"+zeroPad(date.getDate(),2)+"/"+date.getFullYear();
			var classname = '';

			loader.hide();
			return [true];
		
		}
		//var temp_date= new date(Y-m-d);

		 
		loader.hide();

   }
/**
 *method change_timepicker_val
 *This function is triggered after timepicked valu changed
 *@params date
 *@return void
 */
function change_timepicker_val( date, inst)
{
	var booked_days = $('#id_booked_days').val();
	var base_days = parseInt($('#id_base_days').val());
	var option_val = $("#id_days_availability").val();
	var generate_checked_dates = Array();
	var generate_checked_days = Array();
	var disable_array = Array();
	var error = Array();
	var selected_dates = Array();
	if( booked_days == 'yes' && base_days != '' )
	{
		for (var a = 0; a < base_days; a++)
		{
			var dateObject = new Date(date);
	        dateObject.setDate(dateObject.getDate()+a); 
	 
	        var dd = dateObject.getDate();
	    	var mm = dateObject.getMonth() + 1;//January is 0! 
	    	var yyyy = dateObject.getFullYear();
		    if (dd < 10) { dd = '0' + dd }
		    if (mm < 10) { mm = '0' + mm }
	     	var checked_date= mm + '/' + dd + '/' + yyyy;

	        generate_checked_dates.push( checked_date );    
		}
 
		$('#id_datepicker td.ui-datepicker-unselectable').each( function() {
		
		  	var find_unselectable = $.trim($(this).text());
			if( find_unselectable.length > 0 && find_unselectable != '' && !isNaN(find_unselectable) ){
				var dateObject = new Date(date);
		        dateObject.setDate(dateObject.getDate()); 
		        var dd = find_unselectable;
		    	var mm = dateObject.getMonth() + 1;//January is 0! 
		    	var yyyy = dateObject.getFullYear();
			    if (dd < 10) { dd = '0' + dd }
			    if (mm < 10) { mm = '0' + mm }
		     	var checked_date= mm + '/' + dd + '/' + yyyy;
		     	disable_array.push(checked_date);
			}		
	});
 
		if(disable_array === undefined || disable_array==''){}
		else
		{
			var booking_flag = 1;
			for (var b = 0; b <= base_days; b++)
			{
				for (var i = 0; i <= disable_array.length; i++)
				{
					if(disable_array[i] === undefined || generate_checked_dates[b]=== undefined){}
					else
					{
						if(disable_array[i] == generate_checked_dates[b])
						{
							alert('Please select other day to booking');
							booking_flag = 0;
						}
						else
						{
							selected_dates.push(generate_checked_dates[b]);
						}	 
					}
				}
			}
		}
		if( booking_flag == 1 )
		{
			//selected_dates = jQuery.unique( selected_dates );
			//console.log(checked_date);
			var start_date_obj 	= new Date(date);
			start_date_obj.setDate(start_date_obj.getDate()); 
	        var dd = start_date_obj.getDate();
	    	var mm = start_date_obj.getMonth() + 1;//January is 0! 
	    	var yyyy = start_date_obj.getFullYear();
		    if (dd < 10) { dd = '0' + dd }
		    if (mm < 10) { mm = '0' + mm }

		    var start_date = mm + '/' + dd + '/' + yyyy;
			var end_date 	= checked_date;
			//console.log('Start Date == '+ start_date + ' == End Date == '+ end_date);
			var booking_data = '<div class="booking_dates"><b> Booking Dates : </b> <p> Start Date :'+ start_date + ' To ' + end_date + '</p></div>';
			$("#booking_details_value").html(booking_data);
			$("#id_can_proceed").val(1);
			$("#id_booking_multi_days").val(start_date +','+ end_date);
			//console.log(booked_days);
		}
		
	}
	else
	{

			$("#booking_details_value").css("display","block");
			$("#booking_details_value").html("<img src='"+url_obj.plugin_url+"/images/loading.gif' width='200px;'>");
			//$("#booking_details_value").html("hello");
			$("#id_input_date").val(date);
			//get time display options
			$.post(url_obj.bkx_ajax_url,{
				action : 'bkx_displaytime_options',
				bookigndate : date,
				service_id : $('#id_base_selector').val(),
				'seatid': $('#myInputSeat').val()
			}, function(data) {
				$("#booking_details_value").html(data);
			 });

			//unset all timepicker selected values
			$("#id_booking_time_from").val('');
			$("#id_display_success").html("");
			$("#id_can_proceed").val(0);

			$("#time_options").html("<option>Select a time</option>");
			var myTime=new Array("12:00AM","12:30AM","01:00AM", "01:30AM");
			var option_text = "";
			for(temp in myTime)
			{
				option_text = option_text+"<option>"+myTime[temp]+"</option>";
			}
			$("#time_options").append(option_text);
			
			
			var fromtime = $('#id_time_availability_certain_from').val();
			var tilltime = $('#id_time_availability_certain_till').val();
			//alert(fromtime+"And "+tilltime)
			if(fromtime!="" && tilltime!="")
			{
				$('#input_4_11_2').timepicker({minTime: fromtime, maxTime: tilltime, interval: 10});
			}
			else
			{
				$('#input_4_11_2').timepicker();
			}
	}
 

	 
}
/**
 *method displaySecondForm
 *This function is to display second step form for booking
 *@access public
 *@return void
 */
function displaySecondForm()
{
	 var loader = $('.bookingx-loader');

	loader.show();

	//alert($("#id_booking_duration_insec").attr("display-date-picker"));
	 
	if($("#id_booking_duration_insec").attr("display-date-picker") == "1")
	 {
		$( "#id_datepicker_extended" ).datepicker();
		 var today = new Date();
		 var $today_date =$.datepicker.formatDate('mm/dd/yy', new Date());
		 $('#id_input_date').val($today_date);
		 $('#id_datepicker').datepicker('setDate', $today_date);
		 $( "#id_datepicker" ).datepicker({minDate: today,setDate: $today_date,beforeShowDay: disableSpecificWeekDays, onSelect: function( date, inst){change_timepicker_val(date, inst);}});
	 }
	 else
	 {
		$("#id_display_success").html("You Can Diretly Contact Administrator and proceed with booking payment");
		$("#id_can_proceed").val(1);
	 }
	  
}

function get_content_by_page_id( page_id, class_name )
{
	var loader = $('.bookingx-loader');
	    loader.show();
		var data = {
			'action': 'bkx_get_page_content',
			'page_id': page_id
		};
		jQuery.post(url_obj.admin_ajax, data, function(response) {
			loader.hide();
			 response = JSON.parse(response);
			 var content = response.post_content;
			 var title = response.post_title;
			 jQuery('.'+class_name).attr('title',title);
			 jQuery('.'+class_name).html(content);
			 jQuery( "."+class_name ).dialog({
				  modal: true,
				  width: "auto",
				      // maxWidth: 660, // This won't work
				      create: function( event, ui ) {
				        // Set maxWidth
				        $(this).css("maxWidth", "660px");
				      }
				});
			 jQuery( "."+class_name ).siblings('div.ui-dialog-titlebar').removeClass('ui-widget-header');
			 jQuery( "."+class_name ).siblings('div.ui-dialog-titlebar').find('span').html('');
			 jQuery( "."+class_name ).siblings('div.ui-dialog-titlebar').find('span').html('<h2>'+title+'</h2>');
		});
		 

}
/*--------------------------------------------------------------------------------------------------------*/
$(document).ready(function(){

	var get_width = $(".bookingx_form_container").parent().width();
	if( get_width <= 650 ){
		$('#field_4_display_booking').css("width","auto");
	}

		$(".fc-widget-content[data-date='20130105']").addClass("disabled-slot");
		
		$("#time_options").change(function(){
			alert("time selected");
		});


		
  $(function() {		
        $( "#bkx_progressbar_wrapper_4" ).progressbar({
            value: 25
        });
	//alert("<?php echo "dfgfd"; ?>");
    });




	 
		 $(function() {
			 $("#bkx_next_button_4_7").click(function(){

				 //alert($("#id_booking_duration_insec").attr("display-date-picker"));

			 });
		});
                if($('#id_selected_seat').val()!='' && $('#id_selected_seat').val()!='zero')
                {
                    var seat_temp = $('#id_selected_seat').val()
 
                    $("#id_seat_name").val($(this).find('option:selected').text());
                    if(seat_temp!=""){
                           $('#id_base_selector').attr('disabled',false);
                    }
                    if(seat_temp==""){
                           $('#id_base_selector').attr('disabled',true);
                    }
		 $.post(url_obj.bkx_ajax_url, { seatid: seat_temp , action : 'bkx_get_base_on_seat' }, function(data) {
				if(data!="error")
				{
					//alert(data);
					//alert('<?php echo __("Error-login") ?>'); 
					var temp_obj = $.parseJSON(data);
					//console.log(temp_obj);
					var temp_option='<option value="">Select a '+url_obj.base+'</option>';
					var base_obj = temp_obj['base_list'];
					//console.log(base_obj);
					for(x in base_obj)
					{
						//console.log(base_obj[x]['seat_id']);
					  //console.log(temp_obj[x]['seat_id']+temp_obj[x]['seat_name']+temp_obj[x]['seat_price']);
						//if(x!="seat_is_certain_days" && x!="seat_days" && x!="seat_is_certain_month" && x!="seat_months" && x!='seat_is_certain_time' && x!='seat_time_from' && x!='seat_time_till')
						//{
						  temp_option += "<option value='"+base_obj[x]['base_id']+"'>"+base_obj[x]['base_name']+" - $"+base_obj[x]['base_price']+" - "+base_obj[x]['base_time']+"</option>";
						//}
					}
					/*  Updated By : Madhuri Rokade 
						Reason : To check the Prepayment process
					*/

					if(temp_obj['seat_is_booking_prepayment']=='N')
					{
							$("#flag_stop_redirect").val('stop');
					}
					else{
						
							$("#flag_stop_redirect").val('');
					}
					//alert(temp_option);
					$('#id_base_selector').empty().append(temp_option);
					$("#id_time_availability").val(temp_obj['seat_is_certain_time']);
					$("#id_time_availability_certain_from").val(temp_obj['seat_time_from']);
					$("#id_time_availability_certain_till").val(temp_obj['seat_time_till']);
					$("#id_days_availability").val(temp_obj['seat_days']);
					$("#id_days_availability_certain").val(temp_obj['seat_is_certain_days']);
					$("#id_months_availability").val(temp_obj['seat_is_certain_month']);
					$("#id_months_availability_certain").val(temp_obj['seat_months']);
					
				}
				else
				{	
					alert("Couldn't list bases");
				}
			});
                    }
                            $('#myInputSeat').bind('change', function(){
                            	loader.show();
                                //var oldvalue = ???
                                     var seat_temp = $(this).val();
                                     var seat_temp_name = $(this).html();
                                     //alert(seat_temp);
                                     $("#id_seat_name").val($(this).find('option:selected').text());
                                     if(seat_temp!=""){
                                            $('#id_base_selector').attr('disabled',false);
                                     }
                                     if(seat_temp==""){
                                            $('#id_base_selector').attr('disabled',true);
                                     }


                                    $.post(url_obj.bkx_ajax_url, { seatid: seat_temp , action : 'bkx_get_base_on_seat' }, function(data) {

                                                    if(data!="error")
                                                    {
                                                            //alert(data);
                                                            //alert('<?php echo __("Error-login") ?>'); 
                                                            var temp_obj = $.parseJSON(data);
                                                            //console.log(temp_obj);
                                                            var temp_option='<option value="">Select a '+url_obj.base+'</option>';
                                                            var base_obj = temp_obj['base_list'];
                                                            //console.log(base_obj);
                                                            for(x in base_obj)
                                                            {
                                                                    //console.log(base_obj[x]['seat_id']);
                                                              //console.log(temp_obj[x]['seat_id']+temp_obj[x]['seat_name']+temp_obj[x]['seat_price']);
                                                                    //if(x!="seat_is_certain_days" && x!="seat_days" && x!="seat_is_certain_month" && x!="seat_months" && x!='seat_is_certain_time' && x!='seat_time_from' && x!='seat_time_till')
                                                                    //{
                                                                      temp_option += "<option value='"+base_obj[x]['base_id']+"'>"+base_obj[x]['base_name']+" - $"+base_obj[x]['base_price']+" - "+base_obj[x]['base_time']+"</option>";
                                                                    //}
                                                            }
                                                            /*  Updated By : Madhuri Rokade 
                                                                    Reason : To check the Prepayment process
                                                            */

                                                            if(temp_obj['seat_is_booking_prepayment']=='N')
                                                            {
                                                                            $("#flag_stop_redirect").val('stop');
                                                            }
                                                            else{

                                                                            $("#flag_stop_redirect").val('');
                                                            }
                                                            //alert(temp_option);
                                                            $('#id_base_selector').empty().append(temp_option);
                                                            $("#id_time_availability").val(temp_obj['seat_is_certain_time']);
                                                            $("#id_time_availability_certain_from").val(temp_obj['seat_time_from']);
                                                            $("#id_time_availability_certain_till").val(temp_obj['seat_time_till']);
                                                            $("#id_days_availability").val(temp_obj['seat_days']);
                                                            $("#id_days_availability_certain").val(temp_obj['seat_is_certain_days']);
                                                            $("#id_months_availability").val(temp_obj['seat_is_certain_month']);
                                                            $("#id_months_availability_certain").val(temp_obj['seat_months']);

                                                    }
                                                    else
                                                    {	
                                                            alert("Something went wrong..");
                                                    }

                                                    loader.hide();
                                            });
                            });
                        
        

		$('#id_base_selector').bind('change', function(){
			var base_temp1 = $(this).val();
			$.post(url_obj.bkx_ajax_url, { baseid1: base_temp1, mob: 'no'  , action : 'bkx_get_base_on_seat' }, function(data) {
				var base_data = $.parseJSON(data);

				$('#base_location_type').val(base_data.base_location_type);

				if(base_data.base_location_type == 'FM' )
				{
						$('#field_4_18').hide();
						$('#mobile_only').css('display', 'block');
						$('#user_details').css('display', 'none');
						$('#bkx_page_footer_details').css('display', 'none');
				}

				if(base_data.base_location_type == 'Mobile')
				{
					$('#mobile_only').css('display', 'none');
					$('#user_details').css('display', 'block');
					$('#field_4_18').show();

				}
				if(base_data.base_location_type == 'Fixed Location')
				{
					$('#mobile_only').css('display', 'none');
					$('#user_details').css('display', 'block');
					$('#field_4_18').hide();

				}
				if(data!="error")
				{
					if(data == 'N')
					{
						$('#field_4_18').hide();
						$('#mobile_only').css('display', 'block');
						$('#user_details').css('display', 'none');
						$('#bkx_page_footer_details').css('display', 'none');
					}
				}
				else
				{	
					alert("Couldn't list bases");
				}
			});

			
			$("input[name=mobile_only_choice]:radio").change(function () {
				$('#user_details').css('display', 'block');
				$('#bkx_page_footer_details').css('display', 'block');
				$('li#field_4_18').hide();
			
			var selected_radio = $("input[name='mobile_only_choice']:checked").val();
			if(selected_radio == "YES" )
			{
				$('li#field_4_18').show();
				// var x = $('ul#4_form_user_details');
				// x.append('<li id="field_4_18" class="gfield"><label class="gfield_label" for="input_4_18_1">Address</label><div class="ginput_complex ginput_container" id="input_4_18"><span class="ginput_full" id="input_4_18_1_container"><input type="text" name="input_street" id="id_street" value="" tabindex="18"><label for="input_4_18_1" id="input_4_18_1_label">Street</label></span><br><span class="ginput_left" id="input_4_18_3_container"><input type="text" name="input_city" id="id_city" value="" tabindex="19"><label for="input_4_18_3" id="input_4_18.3_label">City</label></span><br><span class="ginput_right" id="input_4_18_4_container"><input type="text" name="input_state" id="id_state" value="" tabindex="21"><label for="input_4_18_4" id="input_4_18_4_label">State / Province / Region</label></span><br><span class="ginput_left" id="input_4_18_5_container"><input type="text" name="input_postcode" id="id_postcode" value="" tabindex="22"><label for="input_4_18_5" id="input_4_18_5_label">Zip / Postal Code</label></span><input type="hidden" class="bkx_hidden" name="input_18.6" id="input_4_18_6" value=""></div></li>');
				
			}
			else 
			{
				$('li#field_4_18').hide();

			}
						
		});

		});

		

		

		$('#id_base_selector').bind('change', function(){
					$('#id_booked_days').val(''); 
					$('#id_base_days').val('');
			var base_temp = $(this).val();
			$.post(url_obj.bkx_ajax_url, { baseid: base_temp , action : 'bkx_get_booked_days' }, function(data) {
				 var base_data = $.parseJSON(data);
				 if(base_data.disable == 'yes')
				 {
				 	$('#id_booked_days').val('yes'); 
				 	$('#id_base_days').val(base_data.time); 
				 }

			});
			
			$.post(url_obj.bkx_ajax_url, {baseid: base_temp, loc: 'fixed', action : 'bkx_get_base_on_seat' }, function(data) {
				if(data!="error")
				{
					if(data == 'Y')
					{
						//alert(data);
						$('#field_4_18').remove();
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
        $( "#id_input_extended_time" ).autocomplete({
            source: availableTags,
	    minLength: 0,
	    change: function( event, ui ) {
			//if($(this).val()!="")
			{
				$("#id_clear_extended_time").css('visibility','visible');
				//alert(event);
				get_total_price('');
			}		
		}
        }).focus(function(){
           	if (this.value == "")
                {
                    $(this).autocomplete("search");
                }
		//$(this).trigger('keydown.autocomplete');
        });

	
	//$("#id_clear_extended_time").css('position','relative');
	$("#id_clear_extended_time").css('left','0px');
	$("#id_clear_extended_time").click(function(){
		$( "#id_input_extended_time" ).val('');
		$("#id_clear_extended_time").css('visibility','hidden');
		get_total_price('');
	});
	$("#id_clear_extended_time").css('visibility','hidden');

	$('#id_base_selector').bind('change', function(){
		var base_temp = $(this).val();
		if(base_temp=="")
		{
			$('#extended_time').hide();
			$('#base_extended_calender').hide();
			$('#id_addition_checkbox').html('');
		}
		else
		{
			$('#extended_time').hide();
			$.post(url_obj.bkx_ajax_url , { baseid: base_temp, action : 'bkx_get_if_base_extended' }, function(data) {
			
				if(data=="Y")
				{
					$('#extended_time').show();
					$('#base_extended_calender').show();
				}
				else if(data == 'N')
				{
					$('#extended_time').hide();
					$('#base_extended_calender').hide();
				}
			});
		}
		get_total_price('');

		var seat_id = $('#myInputSeat').val();
		$.post(url_obj.bkx_ajax_url, { baseid: base_temp, seat_id : seat_id, action : 'bkx_get_addition_on_base' }, function(data) {
			
			var temp_obj = $.parseJSON(data);
				//console.log(temp_obj[0].addition_alies);
			var temp_option='';
			if(temp_obj){
			temp_option='<label class="gchoice_5_2_1 gfield_label ">Select '+temp_obj[0].addition_alies+' you require</label>';
			}
			for (x in temp_obj)
			{
			  //console.log(temp_obj[x]['seat_id']+temp_obj[x]['seat_name']+temp_obj[x]['seat_price']);
			  temp_option += '<li class="gchoice_5_1"><input name="addition_name[]" class="addition_name_class" id="addition'+temp_obj[x]['addition_id']+'" type="checkbox" value="'+temp_obj[x]['addition_id']+'" id="choice_5_1" tabindex="4" onchange="get_total_price(this);"><label for="choice_5_1">'+temp_obj[x]['addition_name']+' - $'+temp_obj[x]['addition_price']+' - '+temp_obj[x]['addition_time']+'</label></li>';
			}
			//temp_option += "<script>$(".addition_name_class").change(function(){alert("hello");});</script>"; 
			$('#id_addition_checkbox').html(temp_option);
			
		});
		
		
	});

	/*$("input[type='checkbox',name='input_addition']").bind("click", function(event, ui) {
		alert('change detected');
	 });
	*/
	
	/*$(":checkbox").bind("click",function(event, ui){
		alert('clicked');
		
	});*/
	
    });//close document.ready function 

function get_total_price(thisval)
{
	var temp = $(thisval).val();
	var addition_list = new Array();
	var loader = $('.bookingx-loader');


	$('.addition_name_class').each(function(index) {
		//alert($(this).val());
		if($(this).is(':checked')==true)
		{
			addition_list.push($(this).val());
		}
		else
		{
		//alert('not checked');
		}

	});
	var base_extended = 0;
	var x=document.getElementById("id_input_extended_time");
	base_extended = x.value;
	//alert(base_extended);
	var seat_temp = $('#myInputSeat').val();
	var base_temp = $('#id_base_selector').val();
	loader.show();

	$.post(url_obj.bkx_ajax_url, { action: 'bkx_get_total_price', seatid : seat_temp, baseid: base_temp,additionid : addition_list,extended : base_extended}, 	function(data) {
		$('h4 span#total_price').html(data['total_price']);
		$('#hidden_total_price').val(data['total_price']);
		$('#deposit_price').val(data['deposit_price']);
		$('#seat_is_booking_prepayment').val(data['seat_is_booking_prepayment']);
		loader.hide();
	}, "json");
	
}

function validate_form(source_val,destination_val)
{
	var error_list = new Array();
	var loader = $('.bookingx-loader');

	if(source_val==1 && destination_val == 2)
	{
		var addition_list =  new Array();
		if($('#myInputSeat').val()=="")
		{
			error_list.push("Please select a seat");
		}
		if($('#id_base_selector').val()=="")
		{
			error_list.push("Please select a base");
		}
		if($("#extended_time").is(":visible"))
		{
			var extended_time = $("#id_input_extended_time").val();
						
			if(extended_time== "" || extended_time== undefined)
			{
				error_list.push("Please enter extended base time");
			}
		}
		//alert($('.addition_name_class').length);
		if($('.addition_name_class').length>0)
		{
			$('.addition_name_class').each(function(index) {
				//alert($(this).is(':checked'));
				if($(this).is(':checked')==true)
				{
					addition_list.push($(this).val());
				}				

			});
			/*if(addition_list.length == 0)
			{
				error_list.push("Please select atleast one addition");
			}*/
		}
	}

	if(source_val==2 && destination_val == 3)
	{

		//alert($("#id_can_proceed").val());
		if($("#id_can_proceed").val() == "0")
		{
			if($("#id_datepicker").val()=="")
			{
				error_list.push("Please choose a date");
			}
			if($("#base_extended_calender").is(":visible"))
			{
				if($("#id_datepicker_extended").val()=="")
				{
					error_list.push("Please choose end date");		

				}
			}
			/*if($("#input_4_11_2").val()=="")
			{
				error_list.push("Please choose time");		
			}*/
			if($("#time_options").val()=="")
			{
				error_list.push("Please choose time");		
			}
			if($("#id_can_proceed").val()==0)
			{
				error_list.push("Please select appropriate date time");		
			}
		}


	}
	if(source_val==3 && destination_val == 4)
	{
		if($("#id_firstname").val()=="")
		{
			error_list.push("Please enter your first name");
		}
		if($("#id_lastname").val()=="")
		{
			error_list.push("Please enter your last name");
		}
		if($("#id_phonenumber").val()=="")
		{
			error_list.push("Please enter your phone");
		}
		else
		{
			// if (checkInternationalPhone($("#id_phonenumber").val())==false)
			// {
			// 	error_list.push("Please Enter a Valid Phone Number");
			// }
		}
		if($("#id_email").val()=="")
		{
			error_list.push("Please enter your email");
		}
		else
		{
			var temp_err = ValidateEmail($("#id_email").val());
			if(temp_err != true)
			{
				error_list.push(temp_err);
			}
		}

		var selected_radio = $("input[name='mobile_only_choice']:checked").val();
		var base_location_type = $("#base_location_type").val();
		var enable_cancel_status = $("#id_enable_cancel_status").val();

		if(selected_radio == "YES" || base_location_type == "Mobile"  )
		{
			if($("#id_street").val()== "")
			{
				error_list.push("Please enter your Street.");
			}
			if($("#id_city").val()== "")
			{
				error_list.push("Please enter your City.");
			}
			if($("#id_state").val()== "")
			{
				error_list.push("Please enter your State / Province / Region.");
			}
			if($("#id_postcode").val()== "")
			{
				error_list.push("Please enter your Zip / Postal Code.");
			}
		}

		if ($("#id_terms").is(":checked")) {}
	    else {
	        error_list.push("Please checked our terms & condtion.");
	    }

	    if ($("#id_privacy").is(":checked")) {}
	    else {
	        error_list.push("Please checked our privacy policy.");
	    }

	    if(enable_cancel_status == 1 ){
	    	if ($("#id_cancellation").is(":checked")) {}
		    else {
		        error_list.push("Please checked our cancellation policy.");
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

		$(".error").html(temp_err);
		$(".error").show();

		return false;
	}
	else
	{
		var seat_temp = $('#myInputSeat').val();
		var booking_date = $("#id_input_date").val();
		var start_time = $("#id_booking_time_from").val();
		var booking_duration = $("#id_booking_duration_insec").val();

		$(".error").html('');
		$( "#bkx_progressbar_wrapper_4" ).progressbar({
			value: 25*destination_val
		});
		$('h3.bkx_progressbar_title').html('Step '+destination_val+' of 4');
		//alert("writing html here");
		//start code for getting selected seat,base and additions			
			var addition_list = new Array();
			$('.addition_name_class').each(function(index) {
				//alert($(this).val());
				if($(this).is(':checked')==true)
				{
					addition_list.push($(this).val());
				}
				else
				{
				//alert('not checked');
				}

			});
		if(source_val==1 && destination_val == 2)
		{
			
			//alert(addition_list);
			//console.log(addition_list);
			var seat_temp = $('#myInputSeat').val();
			var base_temp = $('#id_base_selector').val();
			var mob_only = $('#id_selected_base').val();
			 

			var base_extended  = 0;
			var input_extended = document.getElementById("id_input_extended_time");
			base_extended = input_extended.value;

			//end code for getting selected seat, base and addition

			loader.show();
			$.post(url_obj.bkx_ajax_url, { action : 'bkx_get_duration_booking', seatid : seat_temp, baseid: base_temp, extended: base_extended, additionid : addition_list}, 	function(data) {
				//alert(data);
				var temp_data = $.parseJSON(data);
				//alert(temp_data);console.log(temp_data);
				//alert(data);alert(addition_list);console.log(addition_list);
				$("#display_total_duration").html(temp_data['booked_summary']);
				$("#id_booking_duration_insec").val(temp_data['totalduration_in_seconds']);
				var result = temp_data['result'];
				$("#id_booking_duration_insec").attr('display-date-picker', result);
				
				displaySecondForm();
				loader.hide();
				//$("#id_total_duration").val(temp_data['time_output']);
				//$("span#id_selected_addition").html(temp_data['addition_list']);
			});
			 
			var $today_date =$.datepicker.formatDate('mm/dd/yy', new Date());
			change_timepicker_val($today_date);
			 
		}
		$("span#id_selected_base").html($("#id_base_selector option:selected").text());
 
		if(source_val==3 && destination_val == 4)
		{	
 			
 			loader.show();
			var seat_temp = $('#myInputSeat').val();
			var base_temp = $('#id_base_selector').val();
			 
			var input_extended = document.getElementById("id_input_extended_time");
			base_extended = input_extended.value;
			//alert($('#id_base_selector').val());
			//end code for getting selected seat, base and addition			
			
			$.post(url_obj.bkx_ajax_url, { action : 'bkx_get_duration_booking', seatid : seat_temp, baseid: base_temp, extended : base_extended, additionid : addition_list}, 	function(data) {
				var temp_data = $.parseJSON(data);
				//alert(temp_data);console.log(temp_data);
				//alert(data);alert(addition_list);console.log(addition_list);
				$("#id_estimated_time").html(temp_data['time_output']);
				var currency = temp_data['currency'];
				$("#id_total_duration").val(temp_data['time_output']);
				// $("span#id_selected_addition").html(temp_data['addition_list']);
				$(".booking_summary_data").html(temp_data['booked_summary']);
				$(".booking_summary_cost").html('<li> <b> Total Cost : </b>'+temp_data['currency'] + ' '+ temp_data['total_price'] +'</li>');
				$(".booking_summary_grand_total").html('<li> <b> Total Tax ('+ temp_data['tax_rate'] +'% '+ temp_data['tax_name'] +' ): </b>'+temp_data['currency'] + ' ' + temp_data['total_tax'] +'</li><li> <b> Grand Total : </b>'+temp_data['currency'] + ' '+ temp_data['grand_total'] +'</li>');
				 
				if(temp_data['booking_customer_note']!= 'zero'){
				$(".booking_customer_note").html('<li> <b> Note : </b>'+ temp_data['booking_customer_note'] +'</li>');
				}
				//$("span#id_selected_base11").html(temp_data['mob_only']);

			});
			var booked_days = $('#id_booked_days').val();
			var base_days 	= $('#id_base_days').val();
			if( booked_days == 'yes' && base_days != '' ) {
				$("span#id_selected_date").html($("#id_datepicker").val());
				if(booking_multi_days !=''){
					var booking_multi_days = $("#id_booking_multi_days").val();
					booking_multi_days = booking_multi_days.split(",");
					var start_date 	= booking_multi_days[0];
					var end_date 	= booking_multi_days[1];
				}
				$(".booking_summary_date").html('<li> <b> Date / Time : </b> '+ start_date +' To '+ end_date +'</li>');
			}else{
				$(".booking_summary_date").html('<li> <b> Date / Time : </b>'+ $("#id_datepicker").val() +' at '+ $("#id_booking_time_from").val() +'</li>');
				$("span#id_selected_date").html($("#id_datepicker").val() + ' at ');
			}
			
			//$("span#id_selected_time").html($("#input_4_11_2").val());
			$("span#id_selected_time").html($("#id_booking_time_from").val());
			$("span#id_selected_seat").html($("#myInputSeat option:selected").text());
			$("#id_totalling").html("$"+$("#total_price").html());

			//$("#id_deposit_val").html("$"+$("#total_price").html());
			var seat_is_booking_prepayment = $("#seat_is_booking_prepayment").val();
			//console.log("payment: "+seat_is_booking_prepayment);
			if(seat_is_booking_prepayment == 'Y'){
				$("#id_deposit_val").html("Please Note a deposit of $"+$("#deposit_price").val()+" is required to process and confirm this booking.");
				$("#bkx_submit_button_4").addClass("paypal_button");
				$("#bkx_submit_button_4").val("");
			} else {
				$("#id_deposit_val").html("");
				if($("#bkx_submit_button_4").hasClass("paypal_button"))
					$("#bkx_submit_button_4").removeClass("paypal_button");
			}

		}
		check_slot_is_available(seat_temp,start_time,booking_duration,booking_date,source_val,destination_val);


	}
}

    function ValidateEmail(mail)   
    {  
     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))  
      {  
        return (true)  
      }  
        var error = "You have entered an invalid email address!"; 
        return (error);  
    }  


$('div.app_timetable_cell.free').bind('click', function() {
    //alert("hii+++");
	return false;
});

//on click of any time slot
$("div").on("click", "div.app_timetable_cell.free", function() {
	var loader = $('.bookingx-loader');
	loader.show();
    //alert("hii+++");
	//get the clicked time
	$("#booking_details_value div").removeClass("selected-slot");
	var start_time = $(this).attr("id");
	var booking_duration = $("#id_booking_duration_insec").val();
	var seat_temp = $('#myInputSeat').val();
	var booking_date = $("#id_input_date").val();
	//alert(booking_date);
	$.post(url_obj.bkx_ajax_url, { action: 'bkx_check_ifit_canbe_booked', seatid: seat_temp, start:start_time, bookingduration: booking_duration,bookingdate:  booking_date}, function(data) {
		var temp_obj = $.parseJSON(data);
		var result = temp_obj['result'];
		var starting_slot = temp_obj['starting_slot'];
		var $process ;

		if(parseInt(result) == 1)
		{
			$("#id_booking_time_from").val(start_time);
			//$("#id_display_success").html("You Can book this slot");
			$("#id_can_proceed").val(1);
			var selected_start = starting_slot + 1;
			var selected_end = starting_slot + temp_obj['no_of_slots']+1;

			/**
			 * Added functionality for Check Booking Dates and slot is Available or not
			 * Also check if slot get out of Range on displayed time then restrict
			 * By : Divyang Parekh
			 * Date : 23-11-2015
			 */

			//Highlight Selected Time Slots
			var good_ids = new Array();
			for (var i = selected_start; i <= selected_end; i++)
			{
				var $not_available = $("."+i+"-is-notavailable").val();
				var $check_date_available = $("."+i+"-is-free").val();
				var $full_date = $("."+i+"-is-full").val();
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
			//alert($process);
			//console.log(good_ids);
			if($process == 0)
			{
				//alert("You Cannot book this slot.");
				//$("#id_display_success").html("<span class='error_booking'>You Cannot book this slot</span>");
				$("#id_can_proceed").val(0);
			}
			else
			{
				for (var sel = 0; sel <= good_ids.length; sel++)
				{
					$("#booking_details_value ."+good_ids[sel]).addClass("selected-slot");
				}
			}

		}
		else
		{
			//alert("You Cannot book this slot.");
			//$("#id_display_success").html("<span class='error_booking'>You Cannot book this slot</span>");
			$("#id_can_proceed").val(0);
		}
		loader.hide();
	});
	
	return false;
});

 

/**
 * Added function for Reassign Staff of Booking
 * By : Divyang Parekh
 * Date : 16-11-2015
 */
function check_staff_availability(seat_id,start_time,durations,booking_date,booking_record_id)
{

//seatid: seat_temp, start:start_time, bookingduration: booking_duration,bookingdate:  booking_date
	var seat_id 		= seat_id;
	var booking_date  	= booking_date;
	var start_time     	= start_time;
	var durations     	= durations;
	var booking_record_id = booking_record_id;

	$.post(url_obj.bkx_ajax_url,{ action: 'bkx_check_staff_availability', seat_id: seat_id, start:start_time, bookingduration: durations,bookingdate:  booking_date ,booking_record_id : booking_record_id}, function(data) {
	
		var temp_obj = $.parseJSON(data);
			var result = temp_obj['data'];
			var result_staff = temp_obj['staff'];
			var booking_record_id = temp_obj['booking_record_id'];
			var name = temp_obj['name'];
			if(parseInt(result) == 1)
			{

				jQuery('#reassign_msg').addClass('updated');
				jQuery('#reassign_msg').show();
				jQuery('#reassign_msg').html('<b>Successfully Reassign.</b>');
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


function check_slot_is_available(seat_temp,start_time,booking_duration,booking_date,source_val,destination_val)
{
	var loader = $('.bookingx-loader');
	loader.show();

	if(booking_date!='') {
		$.post(url_obj.bkx_ajax_url, { 
			action: 'bkx_check_ifit_canbe_booked',
			seatid: seat_temp,
			start: start_time,
			bookingduration: booking_duration,
			bookingdate: booking_date
		}, function (data) {
			var temp_obj = $.parseJSON(data);
			var result = temp_obj['result'];
			var starting_slot = temp_obj['starting_slot'];
			var $process;
			loader.hide();
			if (parseInt(result) == 1) {
				$("#id_booking_time_from").val(start_time);
				//$("#id_display_success").html("You Can book this slot");
				$("#id_can_proceed").val(1);
				var selected_start = starting_slot + 1;
				var selected_end = starting_slot + temp_obj['no_of_slots'];

				/**
				 * Added functionality for Check Booking Dates and slot is Available or not
				 * Also check if slot get out of Range on displayed time then restrict
				 * By : Divyang Parekh
				 * Date : 23-11-2015
				 */

				//Highlight Selected Time Slots
				var good_ids = new Array();
				for (var i = selected_start; i <= selected_end; i++) {
					var $not_available = $("." + i + "-is-notavailable").val();
					var $check_date_available = $("." + i + "-is-free").val();
					var $full_date = $("." + i + "-is-full").val();
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
				//alert($process);
				//console.log(good_ids);
				if ($process == 0) {
					//alert("You Cannot book this slot.");
					//$("#id_display_success").html("<span class='error_booking'>You Cannot book this slot</span>");
					$("#id_can_proceed").val(0);
				}
				else {
					for (var sel = 0; sel <= good_ids.length; sel++) {
						$("#booking_details_value ." + good_ids[sel]).addClass("selected-slot");
					}
				}
				$("#bkx_page_4_"+source_val).css('display','none');
				$("#bkx_page_4_"+destination_val).css('display','block');

			}
			else
			{

				//alert("You Cannot book this slot.");
				//$("#id_display_success").html("<span class='error_booking'>You Cannot book this slot</span>");
				$("#id_can_proceed").val(0);
				change_timepicker_val(booking_date);
				return false;
			}
		});
	}
	else
	{
		loader.hide();
		$("#bkx_page_4_"+source_val).css('display','none');
		$("#bkx_page_4_"+destination_val).css('display','block');
	}

	
}

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
       //alert("Geocode was not successful for the following reason: " + status);
      }
    });
}


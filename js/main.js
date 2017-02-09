$(document).ready(function(){
		/**********************start script for color picker******************************/

		/*$('#id_base_color').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});*/
		$('#id_base_color').iris({
			width: 300,
			hide: true,
			palettes: true
		});
		/********************end script for color picker*******************************/


		var temp_months_days_times = $("#id_base_months_days_times").val();
		if(temp_months_days_times == "Months")
		{
				$("tr#months").show();
				$("tr#days").hide();
				$("tr#hours_minutes").hide();
		}
		if(temp_months_days_times == "Days")
		{
				$("tr#months").hide();
				$("tr#days").show();
				$("tr#hours_minutes").hide();
		}
		if(temp_months_days_times == "HourMinutes")
		{
				$("tr#months").hide();
				$("tr#days").hide();
				$("tr#hours_minutes").show();
		}

		$("#id_base_months_days_times").bind( "change", function(event, ui) {
			var temp_months_days_times = $(this).val();
			if(temp_months_days_times == "Months")
			{
					$("tr#months").show();
					$("tr#days").hide();
					$("tr#hours_minutes").hide();
			}
			if(temp_months_days_times == "Days")
			{
					$("tr#months").hide();
					$("tr#days").show();
					$("tr#hours_minutes").hide();
			}
			if(temp_months_days_times == "HourMinutes")
			{
					$("tr#months").hide();
					$("tr#days").hide();
					$("tr#hours_minutes").show();
			}
		});

		$("input[name=base_seat_all]").bind("change",function(event, ui){
			if($(this).attr('checked'))
			{
				$('.myCheckbox').attr('checked','checked');
			}
			else
			{
				$('.myCheckbox').removeAttr('checked','checked');
			}
		});

		if($("input[name=base_location_type]:radio:checked").val()=="Mobile")
		{
			$("tr#location_differ").hide();
		}
		else if($("input[name=base_location_type]:radio:checked").val()=="Fixed Location")
		{
			$("tr#location_differ").show();
		}

		$("input[name=base_location_type]:radio").bind( "change", function(event, ui) {
				var this_val = $(this).val();
				if(this_val=="Mobile")
				{
					$("tr#location_differ").hide();
					$("tr#location_address").hide();
				}
				else if(this_val=="Fixed Location")
				{
					$("tr#location_differ").show();
				}
		});
		
		$("input[name=base_location_differ_seat]:radio").bind( "change", function(event, ui) {
				var this_val = $(this).val();
				
				if(this_val=="No")
				{
					$("tr#location_address").hide();
				}
				else if(this_val=="Yes")
				{
					$("tr#location_address").show();
				}
		});


		//to change the addition unavailability form input type visibility
		$("input[name=base_is_unavailable]").bind("change",function(event, ui){
			
			if($(this).attr('checked')=="checked")
			{
				$("tr#unavailable_from").show();
				$("tr#unavailable_till").show();
			}
			if($(this).attr('checked')==undefined)
			{
				$("tr#unavailable_from").hide();
				$("tr#unavailable_till").hide();
			}
		});

			if($("input[name=base_is_unavailable]").attr('checked')=="checked")
			{
				$("tr#unavailable_from").show();
				$("tr#unavailable_till").show();
			}
			if($("input[name=base_is_unavailable]").attr('checked')==undefined)
			{
				$("tr#unavailable_from").hide();
				$("tr#unavailable_till").hide();
			}

			$( "#id_base_unavailable_from" ).datepicker({onSelect: function(date){$( "#id_base_unavailable_till" ).val('');$( "#id_base_unavailable_till" ).datepicker( "option", "minDate", date );}});
			$( "#id_base_unavailable_till" ).datepicker();
		
  }); // End Document ready 


function validate_base_form_bkp(baseidval)
{
	console.log("validate form");
	return false;
}
/*
*method validate_base_form
*to validate the base form before submission
*@param baseidval 
*@return boolean true
*/
function validate_base_form(baseidval)
{
	//console.log("validate form original form ");
	//return false;
	var error_list = new Array();
	
	if($('#id_base_name').val()=="")
	{
		error_list.push("Please enter base name");
	}
	//alert($('#id_base_description').val());

	// @ruchira removed the description field to be mandatory 7/4/2013
	/*
	if($('#id_base_description').val()=="")
	{
		error_list.push("Please enter base description");
	}
	*/

	/*3rd June 2013 commented to th below code as required image not to be mandatory 
	if($('#id_base_image').val()=="")
	{
		if($("#id_base_image_hidden").val()=="")
		{
			error_list.push("Please upload an image");
		}
	}
	*/
	
	if($('#id_base_color').val()=="")
	{
		error_list.push("Please enter base color");
		
		var temp_color = $('#id_base_color').val();
		$.post(url_obj.plugin_url+'/validate_color.php', { base_color: temp_color,base_id:baseidval }, function(data) {
			

		});
	}
	else
	{	
		var temp_color = $('#id_base_color').val();
		$.post(url_obj.plugin_url+'/validate_color.php', { base_color: temp_color,base_id:baseidval }, function(data) {
			
			if(data>0)
			{
				error_list.push("Please choose diffrent color");
			}
			else if(data==0)
			{
				
			}
		});
	}

	if($('select[name=base_months_days_times]').val()=="")
	{
		error_list.push("Please select your time option");
	}
	//seat selected validation for atleast one 
	var true_flag = false;
	$.each($("input[name='base_seats[]']"), function(val) {
		if(this.checked==true)
		{
			true_flag = true;
		}
	});
	if(true_flag==false)
	{
		error_list.push("Please select atleast one seat you have not selected any");
	}

	if($('#id_base_price').val()=="")
	{
		error_list.push("Please enter base price");
	}
	else if(isNaN($('#id_base_price').val())===true)
	{
		error_list.push("Please enter only numbers for price");
	}
	if($('#id_base_months_days_times').val()=="Months")
	{
		if($("#id_base_months").val()=="")
		{
		error_list.push("Please enter no of months");
		}
		else if(isNaN($('#id_base_months').val())===true)
		{
		error_list.push("Please enter months in number");
		}
	}
	if($('#id_base_months_days_times').val()=="Days")
	{
		if($("#id_base_days").val()=="")
		{
		error_list.push("Please enter no of days");
		}
	}
	if($('#id_base_months_days_times').val()=="HourMinutes")
	{
		if($("#id_base_hours_minutes").val()=="")
		{
		error_list.push("Please enter no of hours and minutes");
		}
		if(($("#id_base_hours_minutes").val()==0||$("#id_base_hours_minutes").val()=="" ) && $("#id_base_minutes").val()==0)
		{
			error_list.push("Please enter proper time, both hours and minutes should not be zero");
		}
	}
	if($("input[name=base_location_type]:radio:checked").val()=="Fixed Location")
	{
		
		if($("input[name=base_location_differ_seat]:radio:checked").val()==undefined)
		{
			error_list.push("Select Does the Base Location differ from Seat Location");
		}
		else
		{
			
			if($("input[name=base_location_differ_seat]:radio:checked").val()=="Yes")
			{
				if($("#id_base_street").val()=="")
				{
				error_list.push("Enter base street");
				}
				if($("#id_base_city").val()=="")
				{
				error_list.push("Enter city");
				}
				if($("#id_base_state").val()=="")
				{
				error_list.push("Enter state");
				}
				if($("#id_base_postcode").val()=="")
				{
				error_list.push("Enter Postal Code");
				}
			}
		}
	}
	
	$("#error_list").ajaxStop(function(){
		
		var temp_err="";
		for(var i=0; i<error_list.length; i++)
		{
			temp_err = temp_err+"<p><strong>"+error_list[i]+"</strong></p>";
		}
		if(temp_err=="")
		{
			$("#error_list").hide();
			if(error_list.length==0)
			{
				$("#id_base_add").submit();
			}
		}
		else
		{
			$("#error_list").html(temp_err);
			$("#error_list").show();
			return false;
		}
	});

}



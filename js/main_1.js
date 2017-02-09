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
				$("#months").show();
				$("#days").hide();
				$("#hours_minutes").hide();
		}
		if(temp_months_days_times == "Days")
		{
				$("#months").hide();
				$("#days").show();
				$("#hours_minutes").hide();
		}
		if(temp_months_days_times == "HourMinutes")
		{
				$("#months").hide();
				$("#days").hide();
				$("#hours_minutes").show();
		}

		$("#id_base_months_days_times").bind( "change", function(event, ui) {
                    
                   //  alert($(this).val());
			var temp_months_days_times = $(this).val();
			if(temp_months_days_times == "Months")
			{
					$("#months").show();
					$("#days").hide();
					$("#hours_minutes").hide();
			}
			if(temp_months_days_times == "Days")
			{
					$("#months").hide();
					$("#days").show();
					$("#hours_minutes").hide();
			}
			if(temp_months_days_times == "HourMinutes")
			{
					$("#months").hide();
					$("#days").hide();
					$("#hours_minutes").show();
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
			$("#location_differ").hide();
		}
		else if($("input[name=base_location_type]:radio:checked").val()=="Fixed Location")
		{
			$("#location_differ").show();
		}

		$("input[name=base_location_type]:radio").bind( "change", function(event, ui) {
				var this_val = $(this).val();
				if(this_val=="Mobile")
				{
					$("#location_differ").hide();
					$("#location_address").hide();
				}
				else if(this_val=="Fixed Location")
				{
					$("#location_differ").show();
				}
		});
		
		$("input[name=base_location_differ_seat]:radio").bind( "change", function(event, ui) {
				var this_val = $(this).val();
				//alert(this_val)
				if(this_val=="No")
				{
					$("#location_address").hide();
				}
				else if(this_val=="Yes")
				{
					$("#location_address").show();
				}
		});


		//to change the addition unavailability form input type visibility
		$("input[name=base_is_unavailable]").bind("change",function(event, ui){
			
			if($(this).attr('checked')=="checked")
			{
				$("#unavailable_from").show();
				$("#unavailable_till").show();
			}
			if($(this).attr('checked')==undefined)
			{
				$("#unavailable_from").hide();
				$("#unavailable_till").hide();
			}
		});

			

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
 $(document).on('submit', '#post', function (e) {

	
	var error_list = new Array();

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
				//$("#id_base_add").submit();
                                return true;
			}
		}
		else
		{
			$("#error_list").html(temp_err);
			$("#error_list").show();
                        $(this).find('.button-primary').removeClass('disabled');
                        $(this).find('.spinner').removeClass('is-active');
			return false;
		}
	 

});



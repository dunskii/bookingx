jQuery(document).ready(function(){

	jQuery( "#id_addition_unavailable_from" ).datepicker({
		onSelect: function(date){
			jQuery( "#id_addition_unavailable_to" ).val('');
			jQuery( "#id_addition_unavailable_to" ).datepicker( "option", "minDate", date );
		}
	});

	jQuery( "#id_addition_unavailable_to" ).datepicker();

	var temp_months_days_times = jQuery("#id_addition_time_option").val();		
	if(temp_months_days_times == "Months")
	{
			jQuery("tr#months").show();
			jQuery("tr#days").hide();
			jQuery("tr#hours_minutes").hide();
			jQuery("tr#overlap").show();
	}
	if(temp_months_days_times == "Days")
	{
			jQuery("tr#months").hide();
			jQuery("tr#days").show();
			jQuery("tr#hours_minutes").hide();
			jQuery("tr#overlap").show();
	}
	if(temp_months_days_times == "Hour and Minutes")
	{
			jQuery("tr#months").hide();
			jQuery("tr#days").hide();
			jQuery("tr#hours_minutes").show();
			jQuery("tr#overlap").show();
	}
	if(temp_months_days_times == "None")
	{
			jQuery("tr#months").hide();
			jQuery("tr#days").hide();
			jQuery("tr#hours_minutes").hide();
			jQuery("tr#overlap").hide();
	}

	jQuery("#id_addition_time_option").bind( "change", function(event, ui) {
			var temp_months_days_times_change = jQuery("#id_addition_time_option").val();
	
			if(temp_months_days_times_change == "Months")
			{
					jQuery("tr#months").show();
					jQuery("tr#days").hide();
					jQuery("tr#hours_minutes").hide();
					jQuery("tr#overlap").show();
			}
			if(temp_months_days_times_change == "Days")
			{
					jQuery("tr#months").hide();
					jQuery("tr#days").show();
					jQuery("tr#hours_minutes").hide();
					jQuery("tr#overlap").show();
			}
			if(temp_months_days_times_change == "Hour and Minutes")
			{
					jQuery("tr#months").hide();
					jQuery("tr#days").hide();
					jQuery("tr#hours_minutes").show();
					jQuery("tr#overlap").show();
			}
			if(temp_months_days_times_change == "None")
			{
					jQuery("tr#months").hide();
					jQuery("tr#days").hide();
					jQuery("tr#hours_minutes").hide();
					jQuery("tr#overlap").hide();
			}
	});

	jQuery("input[name=addition_base_all]").bind("change",function(event, ui){
		if(jQuery(this).attr('checked'))
		{
			jQuery('.myCheckbox').attr('checked','checked');
		}
		else
		{
			jQuery('.myCheckbox').removeAttr('checked','checked');
		}
	});

	if(jQuery("input[name=addition_base_all]").attr('checked'))
	{
		jQuery('.myCheckbox').attr('checked','checked');
	}


	//to change the addition unavailability form input type visibility
	jQuery("input[name=addition_is_unavailable]").bind("change",function(event, ui){
		
		if(jQuery(this).attr('checked')=="checked")
		{
			jQuery("tr#unavailable_from").show();
		}
		if(jQuery(this).attr('checked')==undefined)
		{
			jQuery("tr#unavailable_from").hide();
		}
	});

	if(jQuery("input[name=addition_is_unavailable]").attr('checked')=="checked")
	{
		jQuery("tr#unavailable_from").show();
	}
	if(jQuery("input[name=addition_is_unavailable]").attr('checked')==undefined)
	{
		jQuery("tr#unavailable_from").hide();
	}
});


function validate_addition_form(additionidval)
{		
	var error_list = new Array();
	
	if(jQuery('#id_addition_name').val()=="")
	{
		error_list.push("Please enter addition name");
	}

	if(jQuery('#id_addition_color').val()=="")
	{
		error_list.push("Please enter color");
		var temp_color = jQuery('#id_addition_color').val();
		jQuery.post(url_obj.plugin_url+'/validate_color.php', { addition_color: temp_color,addition_id:additionidval }, function(data) {

		});
	}
	else
	{	
		var temp_color = jQuery('#id_addition_color').val();
		jQuery.post(url_obj.plugin_url+'/validate_color.php', { addition_color: temp_color,addition_id:additionidval }, function(data) {
			
			if(data>0)
			{
				error_list.push("Please choose diffrent color");
			}
			else if(data==0)
			{
				
			}


		});

		
	}
	if(jQuery('#id_addition_price').val()=="")
	{
		error_list.push("Please enter addition price");
	}
	else if(isNaN(jQuery('#id_addition_price').val())===true)
	{
		error_list.push("Please enter only numbers for price");
	}
	
	if(jQuery('#id_addition_time_option').val()=="Months")
	{
		if(jQuery("#id_addition_months").val()=="")
		{
			error_list.push("Please enter no of months");
		}
		else if(isNaN(jQuery('#id_addition_months').val())===true)
		{
			error_list.push("Please enter months as integer");
		}
	}
	if(jQuery('#id_addition_time_option').val()=="Days")
	{
		if(jQuery("#id_addition_days").val()=="")
		{
			error_list.push("Please enter no of days");
		}
		else if(isNaN(jQuery('#id_addition_days').val())===true)
		{
			error_list.push("Please enter days as integer");
		}
	}
	if(jQuery('#id_addition_time_option').val()=="Hour and Minutes")
	{
		if(jQuery("#id_addition_hours_minutes").val()=="")
		{
			error_list.push("Please enter no of hours and minutes");
		}
		else if(isNaN(jQuery('#id_addition_hours_minutes').val())===true)
		{
			error_list.push("Please enter hours as integer");
		}
		if(jQuery("#id_addition_hours_minutes").val()==0 && jQuery("#id_addition_minutes").val()==0)
		{
			error_list.push("Please enter proper time, both hours and minutes should not be zero");
		}
	}
var counter = 0;
jQuery(".myCheckbox").each(function(){
	if(jQuery(this).attr('checked'))
	{
		//alert("test");
		counter += 1;
	}
});

	if(counter==0)
	{
		error_list.push("Please select at least one base associated with this addition");
	}

	
	 jQuery("#error_list").ajaxStop(function(){
  
		setTimeout('moomoo()', 90000000);
	
		var temp_err="";

		for(var i=0; i<error_list.length; i++)
		{
			temp_err = temp_err+"<p><strong>"+error_list[i]+"</strong></p>";
		}
		if(temp_err=="")
		{
			jQuery("#error_list").hide();
			if(error_list.length==0)
			{
				jQuery("#id_addition_add").submit();
			}
		}
		else
		{
			jQuery("#error_list").html(temp_err);
			jQuery("#error_list").show();
			return false;
		}
	});
}





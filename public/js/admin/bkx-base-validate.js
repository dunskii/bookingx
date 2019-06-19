	jQuery(document).ready(function(){
		if (jQuery("input[name=base_location_differ_seat]:radio:checked").val() == "Yes") {
			jQuery("#location_address").show();
		}
		if (jQuery("input[name=base_extended]:radio:checked").val() == "Yes") {
			jQuery("#base_extended_input").show();
		}
		if (jQuery("input[name=base_location_type]:radio:checked").val() == "Mobile") {
			jQuery("#mobile_only").show();
		}
		else if (jQuery("input[name=base_location_type]:radio:checked").val() == "Fixed Location") {
			jQuery("#mobile_only").hide();
		}
		else if (jQuery("input[name=base_location_type]:radio:checked").val() == "FM") {
			jQuery("#mobile_only").show();
			jQuery("#location_differ").show();
		}

		if (jQuery("input[name=base_is_unavailable]").attr('checked') == "checked") {
			jQuery("#unavailable_from").show();
			jQuery("#unavailable_till").show();
		}
		if (jQuery("input[name=base_is_unavailable]").attr('checked') == undefined) {
			jQuery("#unavailable_from").hide();
			jQuery("#unavailable_till").hide();
		}
		jQuery("input[name=base_location_type]:radio").bind("change", function (event, ui) {
			var this_val = jQuery(this).val();
			if (this_val == "Mobile") {
				jQuery("#mobile_only").show();
			}
			else if (this_val == "Fixed Location") {
				jQuery("#mobile_only").hide();
			}
		});
		jQuery('#id_base_colour').iris({
			width: 300,
			hide: true,
			palettes: true
		});
		jQuery( "#id_base_colour").click(function() {
			jQuery('.id_base_colour').show();
		});
		jQuery( ".id_base_colour").mouseenter(function() {
			jQuery('.id_base_colour').show();
		  }).mouseleave(function() {
			jQuery('.id_base_colour').hide();
		});

		jQuery("input[name='base_extended']").change(function(){
			if( this.value == 'Yes' ){
				jQuery('#base_extended_input').show();
			}
			if( this.value == 'No' ){
				jQuery('#base_extended_input').hide();
			}
		});
		var temp_months_days_times = jQuery("#id_base_months_days_times").val();
		if(temp_months_days_times == "Months") {
			jQuery("#months").show();
			jQuery("#days").hide();
			jQuery("#hours_minutes").hide();
		}
		if(temp_months_days_times == "Days") {
			jQuery("#months").hide();
			jQuery("#days").show();
			jQuery("#hours_minutes").hide();
		}
		if(temp_months_days_times == "HourMinutes") {
			jQuery("#months").hide();
			jQuery("#days").hide();
			jQuery("#hours_minutes").show();
		}
		jQuery("#id_base_months_days_times").bind( "change", function(event, ui) {
			var temp_months_days_times = jQuery(this).val();
			if(temp_months_days_times == "Months") {
				jQuery("#months").show();
				jQuery("#days").hide();
				jQuery("#hours_minutes").hide();
			}
			if(temp_months_days_times == "Days") {
				jQuery("#months").hide();
				jQuery("#days").show();
				jQuery("#hours_minutes").hide();
			}
			if(temp_months_days_times == "HourMinutes") {
				jQuery("#months").hide();
				jQuery("#days").hide();
				jQuery("#hours_minutes").show();
			}
		});
		jQuery("input[name=base_seat_all]").bind("change",function(event, ui){
			if(jQuery(this).attr('checked')) {
				jQuery('.myCheckbox').attr('checked','checked');
			} else {
				jQuery('.myCheckbox').removeAttr('checked','checked');
			}
		});
		if(jQuery("input[name=base_location_type]:radio:checked").val()=="Mobile") {
			jQuery("#location_differ").hide();
		}
		else if(jQuery("input[name=base_location_type]:radio:checked").val()=="Fixed Location") {
			jQuery("#location_differ").show();
		}
		else if(jQuery("input[name=base_location_type]:radio:checked").val()=="FM") {
			jQuery("#mobile_only").show();
			jQuery("#location_differ").show();
		}
		jQuery("input[name=base_location_type]:radio").bind( "change", function(event, ui) {
			var this_val = jQuery(this).val();
			if(this_val=="Mobile") {
				jQuery("#location_differ").hide();
				jQuery("#location_address").hide();
			} else if(this_val=="Fixed Location") {
				jQuery("#location_differ").show();
			} else if(this_val=="FM") {
				jQuery("#mobile_only").hide();
				jQuery("#location_differ").show();
			}
		});
		jQuery("input[name=base_location_differ_seat]:radio").bind( "change", function(event, ui) {
			var this_val = jQuery(this).val();
			if(this_val=="No") {
				jQuery("#location_address").hide();
			} else if(this_val=="Yes") {
				jQuery("#location_address").show();
			}
		});
		//to change the addition unavailability form input type visibility
		jQuery("input[name=base_is_unavailable]").bind("change",function(event, ui){
			if(jQuery(this).attr('checked')=="checked") {
				jQuery("#unavailable_from").show();
				jQuery("#unavailable_till").show();
			}
			if(jQuery(this).attr('checked')==undefined) {
				jQuery("#unavailable_from").hide();
				jQuery("#unavailable_till").hide();
			}
		});
		jQuery( "#id_base_unavailable_from" ).datepicker({
				onSelect: function(date) {
				jQuery( "#id_base_unavailable_till" ).val('');
				jQuery( "#id_base_unavailable_till" ).datepicker( "option", "minDate", date );
				} });
		jQuery( "#id_base_unavailable_till" ).datepicker();
	}); // End Document ready

	jQuery(document).on('submit', '#post', function (e) {
		var base_alias = base_obj.base_alias;
		var seat_alias = base_obj.seat_alias;
		var error_list = new Array();
		if(jQuery('select[name=base_months_days_times]').val()=="") {
			error_list.push("Please select your time option");
		}
		//seat selected validation for atleast one
		var true_flag = false;
		jQuery.each(jQuery("input[name='base_seats[]']"), function(val) {
			if(this.checked==true) {
				true_flag = true;
			}
		});
		if(true_flag==false) {error_list.push("Please select atleast one "+seat_alias+". You have\'t selected any.");}
		if(jQuery('#id_base_price').val()=="") {
			error_list.push("Please enter "+base_alias+" price");
		} else if(isNaN(jQuery('#id_base_price').val())===true) {
			error_list.push("Please enter only numbers for price");
		}
		if(jQuery('#id_base_months_days_times').val()=="Months") {
			if(jQuery("#id_base_months").val()=="") {error_list.push("Please enter no of months");
			} else if(isNaN(jQuery('#id_base_months').val())===true) {error_list.push("Please enter months in number");}
		}
		if(jQuery('#id_base_months_days_times').val()=="Days") {
			if(jQuery("#id_base_days").val()=="") {error_list.push("Please enter no of days");}
		}
		if(jQuery('#id_base_months_days_times').val()=="HourMinutes") {
			if(jQuery("#id_base_hours_minutes").val()=="") {error_list.push("Please enter no of hours and minutes");}
			if((jQuery("#id_base_hours_minutes").val()==0||jQuery("#id_base_hours_minutes").val()=="" ) && jQuery("#id_base_minutes").val()==0) {
				error_list.push("Please enter proper time, both hours and minutes should not be zero");
			}
		}
		if(jQuery("input[name=base_location_type]:radio:checked").val()=="Fixed Location")
		{
			if(jQuery("input[name=base_location_differ_seat]:radio:checked").val()==undefined) {
				error_list.push("Select Does the "+base_alias+" Location differ from "+seat_alias+" Location");
			} else {
				if(jQuery("input[name=base_location_differ_seat]:radio:checked").val()=="Yes") {
					if(jQuery("#id_base_street").val()=="") {
					error_list.push("Enter "+base_alias+" street");
					}
					if(jQuery("#id_base_city").val()=="") {
					error_list.push("Enter  city");
					}
					if(jQuery("#id_base_state").val()=="") {
					error_list.push("Enter state");
					}
					if(jQuery("#id_base_postcode").val()=="") {
					error_list.push("Enter Postal Code");
					}
				}
			}
		}
		var temp_err="";
		for(var i=0; i<error_list.length; i++) {
			temp_err = temp_err+"<p><strong>"+error_list[i]+"</strong></p>";
		}
		if(temp_err=="") {
			jQuery("#error_list").hide();
			if(error_list.length==0) { return true; }
		} else {
			jQuery("#error_list").html(temp_err);
			jQuery("#error_list").show();
			jQuery(this).find('.button-primary').removeClass('disabled');
			jQuery(this).find('.spinner').removeClass('is-active');
			return false;
		}
	});
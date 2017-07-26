jQuery(document).ready(function(){
jQuery('#wpseo_meta').remove();
	//diasable all timepicker if day checkbox is not checked
	jQuery.each(jQuery("input[name='seat_certain_day[]']"), function(val) {
		if(this.checked===false)
		{
			var n=jQuery(this).attr('id').split("_");
			var dayName = n[2];
			jQuery("#id_seat_time_from_"+dayName).attr("disabled",true);
			jQuery("#id_seat_time_till_"+dayName).attr("disabled",true);
		}
	});

	//disable and enable the timepicker textbox on change of the input chekcbox for days
	jQuery("input[name='seat_certain_day[]']").change(function(){
		//alert("changing");
		if(this.checked===false)
		{
			var n=jQuery(this).attr('id').split("_");
			var dayName = n[2];
			jQuery("#id_seat_time_from_"+dayName).attr("disabled",true);
			jQuery("#id_seat_time_till_"+dayName).attr("disabled",true);
			//23rd April 2013 reset the value of time from and time till field if checkbox is unchecked
			jQuery("#id_seat_time_from_"+dayName).val('');
			jQuery("#id_seat_time_till_"+dayName).val('');
		}
		else if(this.checked===true)
		{
			var n=jQuery(this).attr('id').split("_");
			var dayName = n[2];
			jQuery("#id_seat_time_till_"+dayName).attr("disabled",false);
			jQuery("#id_seat_time_from_"+dayName).attr("disabled",false);
		}
	});
	

	//Set Timpicker Options for all days
	// var weekdays=new Array("sunday","monday","tuesday","wednesday","thursday","friday","saturday");
	// for (var i=0;i<weekdays.length;i++)
	// {
	// 	var day = weekdays[i];
		
	// 	jQuery('input#id_seat_time_from_'+day).timepicker({
	// 		timeFormat: 'hh:mm p',
	// 		// items in the dropdown are separated by at interval minutes
	// 		interval: 30,
	// 		scrollbar: true
	// 	});
		
	// 	jQuery('input#id_seat_time_till_'+day).timepicker({
	// 		timeFormat: 'hh:mm p',
	// 		// year, month, day and seconds are not important
	// 		interval: 30,
	// 		scrollbar: true
	// 	});		
	// }	
	

	// //Onchange Sunday From Time
	// jQuery('#id_seat_time_from_sunday').timepicker().option('change', function(time) {	            
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_sunday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_sunday').val("");			
	// });
	// // Validate Sunday Till Time
	// jQuery("input#id_seat_time_till_sunday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });	

	// //Onchange Monday From Time
	// jQuery('input#id_seat_time_from_monday').timepicker().option('change', function(time) {		
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_monday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_monday').val("");			
	// });
	// // Validate Monday Till Time
	// jQuery("input#id_seat_time_till_monday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });	
	

	// //Onchange Tuesday From Time
	// jQuery('input#id_seat_time_from_tuesday').timepicker().option('change', function(time) {		
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_tuesday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_tuesday').val("");			
	// });
	// // Validate Tuesday Till Time
	// jQuery("input#id_seat_time_till_tuesday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });	
	

	// //Onchange Wednesday From Time
	// jQuery('input#id_seat_time_from_wednesday').timepicker().option('change', function(time) {		
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_wednesday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_wednesday').val("");			
	// });
	// // Validate Wednesday Till Time
	// jQuery("input#id_seat_time_till_wednesday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });	

	
	// //Onchange Thursday From Time
	// jQuery('input#id_seat_time_from_thursday').timepicker().option('change', function(time) {		
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_thursday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_thursday').val("");			
	// });
	// // Validate Thursday Till Time
	// jQuery("input#id_seat_time_till_thursday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });	
	

	// //Onchange Friday From Time
	// jQuery('input#id_seat_time_from_friday').timepicker().option('change', function(time) {		
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_friday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_friday').val("");			
	// });
	// // Validate Friday Till Time
	// jQuery("input#id_seat_time_till_friday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });	
	
	// //Onchange Saturday From Time
	// jQuery('input#id_seat_time_from_saturday').timepicker().option('change', function(time) {		
	// 		time.setMinutes (time.getMinutes() + 30 );	
	// 		window.from_time = time.getTime();			
	// 		jQuery('#id_seat_time_till_saturday').timepicker('option', 'startTime', time);
	// 		jQuery('#id_seat_time_till_saturday').val("");			
	// });
	// // Validate Saturday Till Time
	// jQuery("input#id_seat_time_till_saturday").timepicker().option('change', function(time) {
	// 	var till_time = time.getTime();
	// 	if(from_time > till_time) {
	// 		if(time.getHours() == 0 && time.getMinutes()==0){
	// 			jQuery(this).removeClass("time_error");
	// 		}else{
	// 			jQuery(this).addClass("time_error");		
	// 			jQuery(this).val("");
	// 		}
	// 	}
	// 	else{
	// 		jQuery(this).removeClass("time_error");
	// 	}
	// });		
});

///////////////////////////////add seat form scripts///////////////////////////////////////////////////////////////////
function ValidateEmail(mail)   
    {  
     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))  
      {  
        return (true)  
      }  
        var error = "You have entered an invalid email address!"; 
        return (error);  
    } 
    
 jQuery(document).on('submit', '#post', function (e) {
     
   
	var error_list = new Array();
	//alert(jQuery('#id_seat_image').val());

	
		 
	 

	if(jQuery('#id_seat_name').val()=="")
	{
		error_list.push("Please enter seat name");
	}
	if(jQuery('#selOpt').val()=="")
	{
		error_list.push("Please select associate option");
	}

	if(jQuery("input[name=bkx_user_auto]:radio:checked").val()=="N"){

			if(jQuery('#selOpt').val()=='Y'){
				if(jQuery('#role').val() ==""){
					error_list.push("Please select user type");
				}
				if(jQuery('#role').val() !=""){
					if(jQuery('#users').val() ==""){
						error_list.push("Please select user");
					}
				}
			}
	}

	if(jQuery('textarea#id_seat_description').val()=="")
	{
		error_list.push("Please enter seat description");
	}
	if(jQuery('textarea#id_seat_image').val()=="")
	{
		error_list.push("Please upload seat image");
	}
	if(jQuery('#id_seat_color').val()=="")
	{
		error_list.push("Please choose seat color");
	}
	else
	{
//		var temp_color = jQuery('#id_seat_color').val();
//		jQuery.post('<?php echo plugins_url( "validate_color.php" , __FILE__ ); ?>', { seat_color: temp_color }, function(data) {
//			if(parseInt(data)>0)
//			{
//				error_list.push("Please choose different seat color, this color already exists");
//			}
//
//		});
	}
	/*
	if(jQuery("#id_seat_location").val()=="")
	{
		error_list.push("Please enter seat location");
	}
	*/
	if(jQuery("#seat_street").val()=="")
	{
		error_list.push("Please enter street");
	}
	if(jQuery("#seat_city").val()=="")
	{
		error_list.push("Please enter city");
	}
	if(jQuery("#seat_state").val()=="")
	{
		error_list.push("Please enter state");
	}
	if(jQuery("#seat_zip").val()=="")
	{
		error_list.push("Please enter zip/postal code");
	}
	
	if(jQuery("input[name=seat_is_certain_time]:radio:checked").val()=="Y")
	{
		if(jQuery("#id_seat_certain_time_from").val()=="")
		{
		error_list.push("Please enter time from");
		}
		if(jQuery("#id_seat_certain_time_till").val()=="")
		{
		error_list.push("Please enter time till");
		}
	}
	
	if(jQuery("input[name=seat_is_alternate_time]:radio:checked").val()=="Y")
	{
		if(jQuery("#id_seat_alternate_time_from").val()=="")
		{
		error_list.push("Please enter alternate time from");
		}
		if(jQuery("#id_seat_alternate_time_till").val()=="")
		{
		error_list.push("Please enter alternate time till");
		}
	}

	if(jQuery("input[name=seat_is_certain_day]:radio:checked").val()=="Y")
	{
		
		jQuery("input[name=seat_certain_day,type=checkbox]:checked").each(function(){
			//alert(jQuery(this).val()+"value");
		});
	}
	
	if(jQuery("#id_seat_email").val()=="" || jQuery("#id_seat_email")==undefined)
	{
		error_list.push("Please enter your email address");
	}
	else
	{
                var seat_email = jQuery("#id_seat_email").val();
                var seat_alias = jQuery("#seat_alias").val();
                var res = ValidateEmail(jQuery("#id_seat_email").val());
                var $post_new_php = jQuery( "body" ).hasClass( "post-new-php" );
		if(res!=true)
		{
                    error_list.push("Please enter valid email address");
		}
                else
                {
                    if($post_new_php == true)
                    {
                        jQuery.post(
                                ajaxurl, 
                                {
                                    'action': 'check_seat_exixts',
                                    'email':   seat_email,
                                    
                                }, 
                                function(response){
                                    if(response == 'error')
                                    {
                                        alert(seat_alias+ " already exists.");
                                        
                                    }  
                                }                             
                            );
                    }
                                          
                }               
   
                
	}
	if(jQuery("#id_seat_is_alternate_email").attr('checked')=="checked")
	{
		if(jQuery("#id_seat_alternate_email").val()!="" || jQuery("#id_seat_alternate_email").val()!=undefined)
		{
			var res_alt = ValidateEmail(jQuery("#id_seat_alternate_email").val());
			if(res_alt!=true)
			{
				error_list.push("Please enter valid alternate email address");
			}
		}
	}
	if(jQuery("#id_seat_alternate_email").val()==jQuery("#id_seat_email").val())
	{
		error_list.push("Email and Alternate Email should not be same");
	}
	jQuery.each(jQuery("input[name='seat_certain_day[]']"), function(val) {
		if(this.checked===true)
		{
			var n=jQuery(this).attr('id').split("_");
			var dayName = n[2];
			//added if condition to check if both time from and till value is blank then consider it as valid time as it will be available for full day
			if(jQuery("#id_seat_time_from_"+dayName).val()=="" && jQuery("#id_seat_time_till_"+dayName).val()=="" )
			{
				
			}
			else
			{
				if(jQuery("#id_seat_time_from_"+dayName).val()=="")
				{
					error_list.push("Please enter time from value for "+dayName.toUpperCase());
				}
				if(jQuery("#id_seat_time_till_"+dayName).val()=="")
				{
					error_list.push("Please enter time till value for "+dayName.toUpperCase());
				}
			}
		}
	});

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
			//jQuery("#id_seat_add").submit();
                        return true;
		}
	}
	else
	{
		jQuery("#error_list").html(temp_err);
		jQuery("#error_list").show();
                jQuery("#error_list").scrollTop( 300 );
                jQuery(this).find('.button-primary').removeClass('disabled');
                jQuery(this).find('.spinner').removeClass('is-active');
                
		return false;
	}
	//jQuery("#id_seat_add").submit();


     
});

 


function validate_form()
{
	var error_list = new Array();
	//alert(jQuery('#id_seat_image').val());
	if(jQuery('#id_seat_name').val()=="")
	{
		error_list.push("Please enter seat name");
	}



	if(jQuery('#selOpt').val()=="")
	{
		error_list.push("Please select associate option");
	}

	if(jQuery('#selOpt').val()=='Y'){
		if(jQuery('#role').val() ==""){
			error_list.push("Please select user type");
		}
		if(jQuery('#role').val() !=""){
			if(jQuery('#users').val() ==""){
				error_list.push("Please select user");
			}
		}
	}

	if(jQuery('textarea#id_seat_description').val()=="")
	{
		error_list.push("Please enter seat description");
	}
	if(jQuery('textarea#id_seat_image').val()=="")
	{
		error_list.push("Please upload seat image");
	}
	if(jQuery('#id_seat_color').val()=="")
	{
		error_list.push("Please choose seat color");
	}
	else
	{
		var temp_color = jQuery('#id_seat_color').val();
		jQuery.post('<?php echo plugins_url( "validate_color.php" , __FILE__ ); ?>', { seat_color: temp_color }, function(data) {
			if(parseInt(data)>0)
			{
				error_list.push("Please choose different seat color, this color already exists");
			}

		});
	}

	if(jQuery("#id_seat_street").val()=="")
	{
		error_list.push("Please enter street");
	}
	if(jQuery("#id_seat_city").val()=="")
	{
		error_list.push("Please enter city");
	}
	if(jQuery("#id_seat_state").val()=="")
	{
		error_list.push("Please enter state");
	}
	if(jQuery("#id_seat_postcode").val()=="")
	{
		error_list.push("Please enter zip/postal code");
	}
	
	if(jQuery("input[name=seat_is_certain_time]:radio:checked").val()=="Y")
	{
		if(jQuery("#id_seat_certain_time_from").val()=="")
		{
		error_list.push("Please enter time from");
		}
		if(jQuery("#id_seat_certain_time_till").val()=="")
		{
		error_list.push("Please enter time till");
		}
	}
	
	if(jQuery("input[name=seat_is_alternate_time]:radio:checked").val()=="Y")
	{
		if(jQuery("#id_seat_alternate_time_from").val()=="")
		{
		error_list.push("Please enter alternate time from");
		}
		if(jQuery("#id_seat_alternate_time_till").val()=="")
		{
		error_list.push("Please enter alternate time till");
		}
	}

	if(jQuery("input[name=seat_is_certain_day]:radio:checked").val()=="Y")
	{
		
		jQuery("input[name=seat_certain_day,type=checkbox]:checked").each(function(){
			//alert(jQuery(this).val()+"value");
		});
	}
	
	if(jQuery("#id_seat_email").val()=="" || jQuery("#id_seat_email")==undefined)
	{
		error_list.push("Please enter your email address");
	}
	else
	{
		var res = ValidateEmail(jQuery("#id_seat_email").val());
		if(res!=true)
		{
			error_list.push("Please enter valid email address");
		}
	}
	if(jQuery("#id_seat_is_alternate_email").attr('checked')=="checked")
	{
		if(jQuery("#id_seat_alternate_email").val()!="" || jQuery("#id_seat_alternate_email").val()!=undefined)
		{
			var res_alt = ValidateEmail(jQuery("#id_seat_alternate_email").val());
			if(res_alt!=true)
			{
				error_list.push("Please enter valid alternate email address");
			}
		}
	}
	if(jQuery("#id_seat_alternate_email").val()==jQuery("#id_seat_email").val())
	{
		error_list.push("Email and Alternate Email should not be same");
	}
	jQuery.each(jQuery("input[name='seat_certain_day[]']"), function(val) {
		if(this.checked===true)
		{
			var n=jQuery(this).attr('id').split("_");
			var dayName = n[2];
			//added if condition to check if both time from and till value is blank then consider it as valid time as it will be available for full day
			if(jQuery("#id_seat_time_from_"+dayName).val()=="" && jQuery("#id_seat_time_till_"+dayName).val()=="" )
			{
				
			}
			else
			{
				if(jQuery("#id_seat_time_from_"+dayName).val()=="")
				{
					error_list.push("Please enter time from value for "+dayName.toUpperCase());
				}
				if(jQuery("#id_seat_time_till_"+dayName).val()=="")
				{
					error_list.push("Please enter time till value for "+dayName.toUpperCase());
				}
			}
		}
	});

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
			jQuery("#id_seat_add").submit();
		}
	}
	else
	{
		jQuery("#error_list").html(temp_err);
		jQuery("#error_list").show();
		return false;
	}
	//jQuery("#id_seat_add").submit();

}

function change_amount_option(thisval)
{
	//alert(thisval);
	if(thisval=="FixedAmount")
	{
		jQuery('#fixedamount').show();
		jQuery('#percentage').hide();
	}

	if(thisval=="Percentage")
	{
		jQuery('#fixedamount').hide();
		jQuery('#percentage').show();
	}

}

function display_ical_address(thisval)
{
/*
alert(thisval);
var temp = jQuery(thisval).checked();
alert(temp);
*/
var checked_val = jQuery("#id_seat_is_ical").attr('checked');
	if(checked_val=="checked")
	{
		jQuery("#ical_address").show();
	}
	else
	{
		jQuery("#ical_address").hide();
	}
}

function hide_fixed_percentage(thisval)
{
//alert(thisval);
if(thisval=="Full Payment")
	{
		jQuery("#fixed_percentage").hide();
		jQuery("#percentage").hide();
		jQuery("#fixedamount").hide();
	}
	else if(thisval == "Deposit")
	{
		jQuery("#fixed_percentage").show();
		jQuery("#id_seat_payment_type").val("FixedAmount");
		//jQuery("#percentage").show();
		jQuery("#fixedamount").show();
	}
}


function display_secondary_email(thisval)
{

var checked_val_email = jQuery("#id_seat_is_alternate_email").attr('checked');
//alert(checked_val_email);
if(checked_val_email=="checked")
	{
		jQuery("#secondary_email").show();
	}
else
	{
		jQuery("#secondary_email").hide();
	}
}

/////////////////////////////document ready scripts///////////////////////////////////////////////////////
jQuery(document).ready(function(){

	/**********************start script for color picker******************************/

	jQuery('#id_seat_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	/********************end script for color picker*******************************/

	jQuery("input[name=bkx_user_auto]").bind("change",function(event, ui){
					
			 var bkx_user_auto = jQuery(this).val();

			 if(bkx_user_auto=="Y")
				{
					jQuery(".bkx_user_mannual").hide();
				}

				if(bkx_user_auto=="N")
				{
					jQuery(".bkx_user_mannual").show();
				}
		});


	/*----------------------start script for checkbox select all-----------------------*/
	jQuery("input[name=seat_certain_month_all]").bind("change",function(event, ui){
					
			if(jQuery(this).attr('checked'))
			{
				jQuery('.month_checkbox').attr('checked','checked');
			}
			else
			{
				jQuery('.month_checkbox').removeAttr('checked','checked');
			}
		});
	
	var payment_type = jQuery("#id_seat_deposit_full").val();

	if(payment_type=="Deposit")
	{

	}
	if(payment_type=="Full Payment")
	{
		jQuery("#fixed_percentage").hide();
		jQuery("#fixedamount").show();
		jQuery("#percentage").hide();
	}

	
	/*show hide availability of certain times*/
	var temp = jQuery('input[name=seat_is_certain_time]:checked').val();
	//alert(temp);

	if(temp=="Y")
	{
		jQuery("#certain_time").show();
	}

	if(temp=="N")
	{
		jQuery("#certain_time").hide();
	}

	jQuery("input[name=seat_is_certain_time]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#certain_time").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#certain_time").hide();
		}
		// Call function here
	});

	/*show hide availability of pre payment*/
	var temp_pre_payment = jQuery('input[name=seat_is_pre_payment]:checked').val();

	if(temp_pre_payment=="Y")
	{
		jQuery("#deposit_fullpayment").show();
		jQuery("#fixed_percentage").show();
		jQuery("#fixedamount").show();
		jQuery("#percentage").show();
	}

	if(temp_pre_payment=="N")
	{
		jQuery("#deposit_fullpayment").hide();
		jQuery("#fixed_percentage").hide();
		jQuery("#fixedamount").hide();
		jQuery("#percentage").hide();
	}

	jQuery("input[name=seat_is_pre_payment]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#deposit_fullpayment").show();
			/*jQuery("#fixed_percentage").show();
			jQuery("#fixedamount").show();
			jQuery("#percentage").show();*/
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#deposit_fullpayment").hide();
			jQuery("#fixed_percentage").hide();
			jQuery("#fixedamount").hide();
			jQuery("#percentage").hide();
		}
		// Call function here
	});


	/*show hide availability of alternate times*/
	var temp_alternate_time = jQuery('input[name=seat_is_alternate_time]:checked').val();
	//alert(temp);

	if(temp_alternate_time=="Y")
	{
		jQuery("#alternate_time").show();
	}

	if(temp_alternate_time=="N")
	{
		jQuery("#alternate_time").hide();
	}

	jQuery("input[name=seat_is_alternate_time]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#alternate_time").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#alternate_time").hide();
		}
		// Call function here
	});

	display_secondary_email(jQuery("#id_seat_is_alternate_email").val());
	display_ical_address(jQuery("#id_seat_is_ical").val());

	/*show hide availability of certain days*/
	var temp_certain_days = jQuery('input[name=seat_is_certain_day]:checked').val();
	//alert(temp);

	if(temp_certain_days=="Y")
	{
		jQuery("#certain_day").show();
	}

	if(temp_certain_days=="N")
	{
		jQuery("#certain_day").hide();
	}

	jQuery("input[name=seat_is_certain_day]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#certain_day").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#certain_day").hide();
		}
		// Call function here
	});


	/*show hide availability of alternate days*/
	var temp_alternate_day = jQuery('input[name=seat_is_alternate_day]:checked').val();
	//alert(temp);

	if(temp_alternate_day=="Y")
	{
		jQuery("#alternate_day").show();
	}

	if(temp_alternate_day=="N")
	{
		jQuery("#alternate_day").hide();
	}

	jQuery("input[name=seat_is_alternate_day]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#alternate_day").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#alternate_day").hide();
		}
		// Call function here
	});

	/*show hide availability of certain months*/
	var temp_certain_month = jQuery('input[name=seat_is_certain_month]:checked').val();
	
	if(temp_certain_month=="Y")
	{
		jQuery("#certain_month").show();
	}

	if(temp_certain_month=="N")
	{
		jQuery("#certain_month").hide();
	}

	jQuery("input[name=seat_is_certain_month]:radio").bind( "change", function(event, ui) {
	 
		if(jQuery(this).val()=="Y")
		{
			jQuery("#certain_month").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#certain_month").hide();
		}
		// Call function here
	});


	/*show hide availability of alternate months*/
	var temp_alternate_month = jQuery('input[name=seat_is_alternate_month]:checked').val();
	//alert(temp);

	if(temp_alternate_month=="Y")
	{
		jQuery("#alternate_month").show();
	}

	if(temp_alternate_month=="N")
	{
		jQuery("#alternate_month").hide();
	}

	 


	jQuery("input[name=seat_is_different_loc]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery(".seat_is_different_loc").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery(".seat_is_different_loc").hide();
		}
		// Call function here
	});


	jQuery("input[name=seat_is_alternate_month]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#alternate_month").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#alternate_month").hide();
		}
		// Call function here
	});

	/*
		FUNCTION WRITTEN BY ARIF KHAN. TO SHOW AND HIDE ROW 
	*/
	if(jQuery('#selOpt').val() == 'Y'){
		jQuery('#selRoles').show();
		jQuery('#selUsers').show();
	}
	jQuery('#selOpt').on('change', function() {
  		// alert(jQuery(this).val() ); // or jQuery(this).val()
  		if(jQuery(this).val() == 'Y'){
  			jQuery('#selRoles').show();
  		}
  		if(jQuery(this).val() == 'N'){
  			jQuery('#selRoles').hide();
  			jQuery('#selUsers').remove();
  		}
	});

	jQuery('#role').on('change', function() {
  		// alert(jQuery(this).val() ); // or jQuery(this).val()
  		if(jQuery(this).val() != ''){
  			 var data = {
		        action: 'get_user',
		        data: jQuery(this).val(),
		        dataType : 'json'
		    };

		    jQuery.post(ajaxurl, data, function(response) {
  				response = JSON.parse(response);
				content = '<option value="">Select User</option>';
  				jQuery.each(response, function(i, item){
  					if(item.data.user_login !=''){
	 					content += '<option value="'+item.data.ID+'">' + item.data.user_login + '</option>';
  					}
		         });
  				// console.log(content);
  				jQuery( "#users" ).html( content );
  				jQuery('#selUsers').show();
		    });
  		}
  		
	});

	var seat_pyt_type = jQuery("#id_seat_deposit_full").val();
	if(seat_pyt_type=='Full Payment'){
       jQuery('#fixed_percentage').hide();
	   jQuery('#fixedamount').hide();
	   jQuery('#percentage').hide();
	}else{
        var Select1 = jQuery("#id_seat_payment_type").val();
		if(Select1=="FixedAmount")
		{
			jQuery('#fixedamount').show();
			jQuery('#percentage').hide();
		}

		if(Select1=="Percentage")
		{
			jQuery('#fixedamount').hide();
			jQuery('#percentage').show();
		}
	}



});//close document ready

jQuery(document).ready(function(){
/**********************start script for color picker******************************/
	jQuery('#id_seat_colour').iris({
		width: 300,
		hide: true,
		palettes: true
	});	
	jQuery( "#id_seat_colour").click(function() {
    	jQuery('.id_seat_colour').show();
	});

	jQuery( ".id_seat_colour").mouseenter(function() {
    	jQuery('.id_seat_colour').show();
	  }).mouseleave(function() {
	    jQuery('.id_seat_colour').hide();
	});

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
	
});

///////////////////////////////add seat form scripts///////////////////////////////////////////////////////////////////
function bkx_ValidateEmail(mail)   
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
	 
	if(jQuery("input[name=seat_is_different_loc]:radio:checked").val()=="Y")
	{
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

	
	if(jQuery("#id_seat_email").val()=="" || jQuery("#id_seat_email")==undefined)
	{
		error_list.push("Please enter your email address");
	}
	else
	{
                var seat_email = jQuery("#id_seat_email").val();
                var seat_alias = jQuery("#seat_alias").val();
                var res = bkx_ValidateEmail(jQuery("#id_seat_email").val());
                var $post_new_php = jQuery( "body" ).hasClass( "post-new-php" );
				if(res!=true)
				{
		            error_list.push("Please enter valid email address");
				}
                else
                {
                    //if($post_new_php == true)
                    //{
                        // jQuery.post(
                        //         ajaxurl, 
                        //         {
                        //             'action': 'check_seat_exixts',
                        //             'email':   seat_email,
                                    
                        //         }, 
                        //         function(response){
                        //             if(response == 'error')
                        //             {
                        //                 //alert(seat_alias+ " already exists.");
                        //                 error_list.push("Please enter valid email address");
                        //                 return false; 
                        //             }  
                        //         }                             
                        //     );
                    //}
                                          
                }         
	}
	if(jQuery("#id_seat_is_alternate_email").attr('checked')=="checked")
	{
		if(jQuery("#id_seat_alternate_email").val()!="" || jQuery("#id_seat_alternate_email").val()!=undefined)
		{
			var res_alt = bkx_ValidateEmail(jQuery("#id_seat_alternate_email").val());
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

 


function bkx_validate_form()
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
 
	if(jQuery("input[name=seat_is_different_loc]:radio:checked").val()=="Y")
	{
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

	
	if(jQuery("#id_seat_email").val()=="" || jQuery("#id_seat_email")==undefined)
	{
		error_list.push("Please enter your email address");
	}
	else
	{
		var res = bkx_ValidateEmail(jQuery("#id_seat_email").val());
		if(res!=true)
		{
			error_list.push("Please enter valid email address");
		}
	}
	if(jQuery("#id_seat_is_alternate_email").attr('checked')=="checked")
	{
		if(jQuery("#id_seat_alternate_email").val()!="" || jQuery("#id_seat_alternate_email").val()!=undefined)
		{
			var res_alt = bkx_ValidateEmail(jQuery("#id_seat_alternate_email").val());
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
		jQuery(".spacer").show();
	}

	if(temp_certain_days=="N")
	{
		jQuery("#certain_day").hide();
		jQuery(".spacer").hide();
	}

	jQuery("input[name=seat_is_certain_day]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+jQuery(this).val());
		if(jQuery(this).val()=="Y")
		{
			jQuery("#certain_day").show();
			jQuery(".spacer").show();
		}
		if(jQuery(this).val()=="N")
		{
			jQuery("#certain_day").hide();
			jQuery(".spacer").hide();
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
		//jQuery('#selRoles').show();
		jQuery('#selUsers').show();
		jQuery('#crete_user_auto').show();
	}
	jQuery('#selOpt').on('change', function() {
  		// alert(jQuery(this).val() ); // or jQuery(this).val()
  		if(jQuery(this).val() == 'Y'){
  			jQuery('#crete_user_auto').show();
  			jQuery('#selRoles').show();
  		}
  		if(jQuery(this).val() == 'N'){
  			jQuery('#crete_user_auto').hide();
  			jQuery('#selUsers').hide();
  			jQuery('#selRoles').hide();
  		}
	});
	var bkx_user_auto = jQuery('input[name=bkx_user_auto]:checked').val();

	if(bkx_user_auto  == 'Y')
	{
		jQuery('#selUsers').hide();
  		jQuery('#selRoles').hide();
	}
	if(bkx_user_auto  == 'N' && jQuery('#selOpt').val() == 'Y')
	{
		jQuery('#selRoles').show();
	}


	jQuery("input[name=bkx_user_auto]").bind("change",function(event, ui){
					
			 var bkx_user_auto = jQuery(this).val();

			 if(bkx_user_auto=="Y")
				{
					jQuery('#selUsers').hide();
  					jQuery('#selRoles').hide();
				}
				if(bkx_user_auto=="N")
				{
					jQuery('#selRoles').show();
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

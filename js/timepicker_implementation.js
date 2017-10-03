$(document).ready(function(){

	//diasable all timepicker if day checkbox is not checked
	$.each($("input[name='seat_certain_day[]']"), function(val) {
		if(this.checked===false)
		{
			var n=$(this).attr('id').split("_");
			var dayName = n[2];
			$("#id_seat_time_from_"+dayName).attr("disabled",true);
			$("#id_seat_time_till_"+dayName).attr("disabled",true);
		}
	});

	//disable and enable the timepicker textbox on change of the input chekcbox for days
	$("input[name='seat_certain_day[]']").change(function(){
		//alert("changing");
		if(this.checked===false)
		{
			var n=$(this).attr('id').split("_");
			var dayName = n[2];
			$("#id_seat_time_from_"+dayName).attr("disabled",true);
			$("#id_seat_time_till_"+dayName).attr("disabled",true);
			//23rd April 2013 reset the value of time from and time till field if checkbox is unchecked
			$("#id_seat_time_from_"+dayName).val('');
			$("#id_seat_time_till_"+dayName).val('');
		}
		else if(this.checked===true)
		{
			var n=$(this).attr('id').split("_");
			var dayName = n[2];
			$("#id_seat_time_till_"+dayName).attr("disabled",false);
			$("#id_seat_time_from_"+dayName).attr("disabled",false);
		}
	});
	

	//Set Timpicker Options for all days
	var weekdays=new Array("sunday","monday","tuesday","wednesday","thursday","friday","saturday");
	for (var i=0;i<weekdays.length;i++)
	{
		var day = weekdays[i];
		
		$('input#id_seat_time_from_'+day).timepicker({
			timeFormat: 'hh:mm p',
			// items in the dropdown are separated by at interval minutes
			interval: 30,
			scrollbar: true
		});
		
		$('input#id_seat_time_till_'+day).timepicker({
			timeFormat: 'hh:mm p',
			// year, month, day and seconds are not important
			interval: 30,
			scrollbar: true
		});		
	}	
	

	//Onchange Sunday From Time
	$('input#id_seat_time_from_sunday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_sunday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_sunday').val("");			
	});
	// Validate Sunday Till Time
	$("input#id_seat_time_till_sunday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});	

	//Onchange Monday From Time
	$('input#id_seat_time_from_monday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_monday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_monday').val("");			
	});
	// Validate Monday Till Time
	$("input#id_seat_time_till_monday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});	
	

	//Onchange Tuesday From Time
	$('input#id_seat_time_from_tuesday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_tuesday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_tuesday').val("");			
	});
	// Validate Tuesday Till Time
	$("input#id_seat_time_till_tuesday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});	
	

	//Onchange Wednesday From Time
	$('input#id_seat_time_from_wednesday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_wednesday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_wednesday').val("");			
	});
	// Validate Wednesday Till Time
	$("input#id_seat_time_till_wednesday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});	

	
	//Onchange Thursday From Time
	$('input#id_seat_time_from_thursday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_thursday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_thursday').val("");			
	});
	// Validate Thursday Till Time
	$("input#id_seat_time_till_thursday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});	
	

	//Onchange Friday From Time
	$('input#id_seat_time_from_friday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_friday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_friday').val("");			
	});
	// Validate Friday Till Time
	$("input#id_seat_time_till_friday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});	
	
	//Onchange Saturday From Time
	$('input#id_seat_time_from_saturday').timepicker().option('change', function(time) {		
			time.setMinutes (time.getMinutes() + 30 );	
			window.from_time = time.getTime();			
			$('#id_seat_time_till_saturday').timepicker('option', 'startTime', time);
			$('#id_seat_time_till_saturday').val("");			
	});
	// Validate Saturday Till Time
	$("input#id_seat_time_till_saturday").timepicker().option('change', function(time) {
		var till_time = time.getTime();
		if(from_time > till_time) {
			if(time.getHours() == 0 && time.getMinutes()==0){
				$(this).removeClass("time_error");
			}else{
				$(this).addClass("time_error");		
				$(this).val("");
			}
		}
		else{
			$(this).removeClass("time_error");
		}
	});		
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
 
function validate_form()
{
	var error_list = new Array();
	//alert($('#id_seat_image').val());
	if($('#id_seat_name').val()=="")
	{
		error_list.push("Please enter seat name");
	}
	if($('#selOpt').val()=="")
	{
		error_list.push("Please select associate option");
	}

	if($('#selOpt').val()=='Y'){
		if($('#role').val() ==""){
			error_list.push("Please select user type");
		}
		if($('#role').val() !=""){
			if($('#users').val() ==""){
				error_list.push("Please select user");
			}
		}
	}

	if($('textarea#id_seat_description').val()=="")
	{
		error_list.push("Please enter seat description");
	}
	if($('textarea#id_seat_image').val()=="")
	{
		error_list.push("Please upload seat image");
	}
	if($('#id_seat_color').val()=="")
	{
		error_list.push("Please choose seat color");
	}
	else
	{
		var temp_color = $('#id_seat_color').val();
		$.post('<?php echo plugins_url( "validate_color.php" , __FILE__ ); ?>', { seat_color: temp_color }, function(data) {
			if(parseInt(data)>0)
			{
				error_list.push("Please choose different seat color, this color already exists");
			}

		});
	}
	/*
	if($("#id_seat_location").val()=="")
	{
		error_list.push("Please enter seat location");
	}
	*/
	console.log($("input[name=seat_is_different_loc]:radio:checked").val());
	
	if($("input[name=seat_is_different_loc]:radio:checked").val()=="Y")
	{
			if($("#id_seat_street").val()=="")
			{
				error_list.push("Please enter street");
			}
			if($("#id_seat_city").val()=="")
			{
				error_list.push("Please enter city");
			}
			if($("#id_seat_state").val()=="")
			{
				error_list.push("Please enter state");
			}
			if($("#id_seat_postcode").val()=="")
			{
				error_list.push("Please enter zip/postal code");
			}
	}


	
	if($("input[name=seat_is_certain_time]:radio:checked").val()=="Y")
	{
		if($("#id_seat_certain_time_from").val()=="")
		{
		error_list.push("Please enter time from");
		}
		if($("#id_seat_certain_time_till").val()=="")
		{
		error_list.push("Please enter time till");
		}
	}
	
	if($("input[name=seat_is_alternate_time]:radio:checked").val()=="Y")
	{
		if($("#id_seat_alternate_time_from").val()=="")
		{
		error_list.push("Please enter alternate time from");
		}
		if($("#id_seat_alternate_time_till").val()=="")
		{
		error_list.push("Please enter alternate time till");
		}
	}

	// if($("input[name=seat_is_certain_day]:radio:checked").val()=="Y")
	// {
		
	// 	$("input[name=seat_certain_day,type=checkbox]:checked").each(function(){
	// 		//alert($(this).val()+"value");
	// 	});
	// }
	
	if($("#id_seat_email").val()=="" || $("#id_seat_email")==undefined)
	{
		error_list.push("Please enter your email address");
	}
	else
	{
		var res = ValidateEmail($("#id_seat_email").val());
		if(res!=true)
		{
			error_list.push("Please enter valid email address");
		}
	}
	if($("#id_seat_is_alternate_email").attr('checked')=="checked")
	{
		if($("#id_seat_alternate_email").val()!="" || $("#id_seat_alternate_email").val()!=undefined)
		{
			var res_alt = ValidateEmail($("#id_seat_alternate_email").val());
			if(res_alt!=true)
			{
				error_list.push("Please enter valid alternate email address");
			}
		}
	}
	if($("#id_seat_alternate_email").val()==$("#id_seat_email").val())
	{
		error_list.push("Email and Alternate Email should not be same");
	}
	$.each($("input[name='seat_certain_day[]']"), function(val) {
		if(this.checked===true)
		{
			var n=$(this).attr('id').split("_");
			var dayName = n[2];
			//added if condition to check if both time from and till value is blank then consider it as valid time as it will be available for full day
			if($("#id_seat_time_from_"+dayName).val()=="" && $("#id_seat_time_till_"+dayName).val()=="" )
			{
				
			}
			else
			{
				if($("#id_seat_time_from_"+dayName).val()=="")
				{
					error_list.push("Please enter time from value for "+dayName.toUpperCase());
				}
				if($("#id_seat_time_till_"+dayName).val()=="")
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
		$("#error_list").hide();
		if(error_list.length==0)
		{
			$("#id_seat_add").submit();
		}
	}
	else
	{
		$("#error_list").html(temp_err);
		$("#error_list").show();
		return false;
	}
	//$("#id_seat_add").submit();

}

function change_amount_option(thisval)
{
	//alert(thisval);
	if(thisval=="FixedAmount")
	{
		$('tr#fixedamount').show();
		$('tr#percentage').hide();
	}

	if(thisval=="Percentage")
	{
		$('tr#fixedamount').hide();
		$('tr#percentage').show();
	}

}

function display_ical_address(thisval)
{
/*
alert(thisval);
var temp = $(thisval).checked();
alert(temp);
*/
var checked_val = $("#id_seat_is_ical").attr('checked');
	if(checked_val=="checked")
	{
		$("tr#ical_address").show();
	}
	else
	{
		$("tr#ical_address").hide();
	}
}

function hide_fixed_percentage(thisval)
{
//alert(thisval);
if(thisval=="Full Payment")
	{
		$("tr#fixed_percentage").hide();
		$("tr#percentage").hide();
		$("tr#fixedamount").hide();
	}
	else if(thisval == "Deposit")
	{
		$("tr#fixed_percentage").show();
		$("#id_seat_payment_type").val("FixedAmount");
		//$("tr#percentage").show();
		$("tr#fixedamount").show();
	}
}


function display_secondary_email(thisval)
{

var checked_val_email = $("#id_seat_is_alternate_email").attr('checked');
//alert(checked_val_email);
if(checked_val_email=="checked")
	{
		$("tr#secondary_email").show();
	}
else
	{
		$("tr#secondary_email").hide();
	}
}

/////////////////////////////document ready scripts///////////////////////////////////////////////////////
$(document).ready(function(){

	/**********************start script for color picker******************************/

	$('#id_seat_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	/********************end script for color picker*******************************/

	/*----------------------start script for checkbox select all-----------------------*/
	$("input[name=seat_certain_month_all]").bind("change",function(event, ui){
					
			if($(this).attr('checked'))
			{
				$('.month_checkbox').attr('checked','checked');
			}
			else
			{
				$('.month_checkbox').removeAttr('checked','checked');
			}
		});
	
	var payment_type = $("#id_seat_deposit_full").val();
	if(payment_type=="Deposit")
	{

	}
	if(payment_type=="Full Payment")
	{
		$("tr#fixed_percentage").hide();
		$("tr#fixedamount").show();
		$("tr#percentage").hide();
	}

	
	/*show hide availability of certain times*/
	var temp = $('input[name=seat_is_certain_time]:checked').val();
	//alert(temp);

	if(temp=="Y")
	{
		$("tr#certain_time").show();
	}

	if(temp=="N")
	{
		$("tr#certain_time").hide();
	}

	$("input[name=seat_is_certain_time]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#certain_time").show();
		}
		if($(this).val()=="N")
		{
			$("tr#certain_time").hide();
		}
		// Call function here
	});

	/*show hide availability of pre payment*/
	var temp_pre_payment = $('input[name=seat_is_pre_payment]:checked').val();
	if(temp_pre_payment=="Y")
	{
		$("tr#deposit_fullpayment").show();
		$("tr#fixed_percentage").show();
		$("tr#fixedamount").show();
		$("tr#percentage").show();
	}

	if(temp_pre_payment=="N")
	{
		$("tr#deposit_fullpayment").hide();
		$("tr#fixed_percentage").hide();
		$("tr#fixedamount").hide();
		$("tr#percentage").hide();
	}

	$("input[name=seat_is_pre_payment]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#deposit_fullpayment").show();
			/*$("tr#fixed_percentage").show();
			$("tr#fixedamount").show();
			$("tr#percentage").show();*/
		}
		if($(this).val()=="N")
		{
			$("tr#deposit_fullpayment").hide();
			$("tr#fixed_percentage").hide();
			$("tr#fixedamount").hide();
			$("tr#percentage").hide();
		}
		// Call function here
	});


	/*show hide availability of alternate times*/
	var temp_alternate_time = $('input[name=seat_is_alternate_time]:checked').val();
	//alert(temp);

	if(temp_alternate_time=="Y")
	{
		$("tr#alternate_time").show();
	}

	if(temp_alternate_time=="N")
	{
		$("tr#alternate_time").hide();
	}

	$("input[name=seat_is_alternate_time]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#alternate_time").show();
		}
		if($(this).val()=="N")
		{
			$("tr#alternate_time").hide();
		}
		// Call function here
	});

	display_secondary_email($("#id_seat_is_alternate_email").val());
	display_ical_address($("#id_seat_is_ical").val());

	/*show hide availability of certain days*/
	var temp_certain_days = $('input[name=seat_is_certain_day]:checked').val();
	//alert(temp);

	if(temp_certain_days=="Y")
	{
		$("tr#certain_day").show();
	}

	if(temp_certain_days=="N")
	{
		$("tr#certain_day").hide();
	}

	$("input[name=seat_is_certain_day]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#certain_day").show();
		}
		if($(this).val()=="N")
		{
			$("tr#certain_day").hide();
		}
		// Call function here
	});


	/*show hide availability of alternate days*/
	var temp_alternate_day = $('input[name=seat_is_alternate_day]:checked').val();
	//alert(temp);

	if(temp_alternate_day=="Y")
	{
		$("tr#alternate_day").show();
	}

	if(temp_alternate_day=="N")
	{
		$("tr#alternate_day").hide();
	}

	$("input[name=seat_is_alternate_day]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_time: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#alternate_day").show();
		}
		if($(this).val()=="N")
		{
			$("tr#alternate_day").hide();
		}
		// Call function here
	});

	/*show hide availability of certain months*/
	var temp_certain_month = $('input[name=seat_is_certain_month]:checked').val();
	
	if(temp_certain_month=="Y")
	{
		$("tr#certain_month").show();
	}

	if(temp_certain_month=="N")
	{
		$("tr#certain_month").hide();
	}

	$("input[name=seat_is_certain_month]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_month: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#certain_month").show();
		}
		if($(this).val()=="N")
		{
			$("tr#certain_month").hide();
		}
		// Call function here
	});


	/*show hide availability of alternate months*/
	var temp_alternate_month = $('input[name=seat_is_alternate_month]:checked').val();
	//alert(temp);

	if(temp_alternate_month=="Y")
	{
		$("tr#alternate_month").show();
	}

	if(temp_alternate_month=="N")
	{
		$("tr#alternate_month").hide();
	}

	$("input[name=seat_is_alternate_month]:radio").bind( "change", function(event, ui) {
		//console.log('seat_is_certain_time: '+$(this).val());
		if($(this).val()=="Y")
		{
			$("tr#alternate_month").show();
		}
		if($(this).val()=="N")
		{
			$("tr#alternate_month").hide();
		}
		// Call function here
	});

	/*
		FUNCTION WRITTEN BY ARIF KHAN. TO SHOW AND HIDE ROW 
	*/
	$('#selOpt').on('change', function() {
  		// alert($(this).val() ); // or $(this).val()
  		if($(this).val() == 'Y'){
  			$('tr#selRoles').show();
  			/* var data = {
		        action: 'my_action',
		        data: $(this).val()
		    };

		    jQuery.post(ajaxurl, data, function(response) {
		        alert('Got this from the server: ' + response);
		    });*/
  		}
  		if($(this).val() == 'N'){
  			$('tr#selRoles').hide();
  			$('tr#selUsers').remove();
  		}
	});

	$('#role').on('change', function() {
  		// alert($(this).val() ); // or $(this).val()
  		if($(this).val() != ''){
  			 var data = {
		        action: 'get_user',
		        data: $(this).val(),
		        dataType : 'json'
		    };

		    jQuery.post(ajaxurl, data, function(response) {
  				response = JSON.parse(response);
				content = '<option value="">Select User</option>';
  				$.each(response, function(i, item){
  					if(item.data.user_login !=''){
	 					content += '<option value="'+item.data.user_login+'">' + item.data.user_login + '</option>';
  					}
		         });
  				// console.log(content);
  				$( "#users" ).html( content );
  				$('tr#selUsers').show();
		    });
  		}
  		
	});

	var seat_pyt_type = $("#id_seat_deposit_full").val();
	if(seat_pyt_type=='Full Payment'){
       $('#fixed_percentage').hide();
	   $('#fixedamount').hide();
	   $('#percentage').hide();
	}else{
        var Select1 = $("#id_seat_payment_type").val();
		if(Select1=="FixedAmount")
		{
			$('tr#fixedamount').show();
			$('tr#percentage').hide();
		}

		if(Select1=="Percentage")
		{
			$('tr#fixedamount').hide();
			$('tr#percentage').show();
		}
	}



});//close document ready
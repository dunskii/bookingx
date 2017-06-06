<script type="text/javascript" src="<?php echo PLUGIN_DIR_URL.'js/sol.js' ?>"></script>
<link rel='stylesheet' id='sol-css'  href='<?php echo PLUGIN_DIR_URL.'css/sol.css?ver='.rand(1,99999).'' ?>' type='text/css' media='all' />
<script type="text/javascript">
function generate_xml()
{
	//alert("xml generation");
	var values = new Array();
	jQuery.each(jQuery("input[name='post[]']:checked"), function() {
	  values.push(jQuery(this).val());
	});
	
	//console.log(values);
	jQuery("#id_addition_list").val(values);
	document.forms["xml_export"].submit();
}

jQuery(document).ready(function(){
	/**********************start script for color picker******************************/
var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
	jQuery('#id_bkx_text_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});	
	jQuery( "#id_bkx_text_color").click(function() {
    	jQuery('.id_bkx_text_color').show();
	});

jQuery( ".id_bkx_text_color").mouseenter(function() {
    	jQuery('.id_bkx_text_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_text_color').hide();
	});

	
	jQuery('#id_bkx_background_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_background_color").click(function() {
    	jQuery('.id_bkx_background_color').show();
	  });
	jQuery( ".id_bkx_background_color").mouseenter(function() {
    	jQuery('.id_bkx_background_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_background_color').hide();
	});


	jQuery('#id_bkx_border_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_border_color").click(function() {
    	jQuery('.id_bkx_border_color').show();
	});
	jQuery( ".id_bkx_border_color").mouseenter(function() {
    	jQuery('.id_bkx_border_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_border_color').hide();
	});

	
	jQuery('#id_bkx_progressbar_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_progressbar_color").click(function() {
    	jQuery('.id_bkx_progressbar_color').show();
	});
	jQuery( ".id_bkx_progressbar_color").mouseenter(function() {
    	jQuery('.id_bkx_progressbar_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_progressbar_color').hide();
	});


	jQuery('#id_bkx_cal_border_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_cal_border_color").click(function() {
    	jQuery('.id_bkx_cal_border_color').show();
	  });
	jQuery( ".id_bkx_cal_border_color").mouseenter(function() {
    	jQuery('.id_bkx_cal_border_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_cal_border_color').hide();
	});

	jQuery('#id_bkx_cal_day_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_cal_day_color").click(function() {
    	jQuery('.id_bkx_cal_day_color').show();
	  });
	jQuery( ".id_bkx_cal_day_color").mouseenter(function() {
    	jQuery('.id_bkx_cal_day_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_cal_day_color').hide();
	});


	jQuery('#id_bkx_cal_day_selected_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_cal_day_selected_color").click(function() {
    	jQuery('.id_bkx_cal_day_selected_color').show();
	  });
	jQuery( ".id_bkx_cal_day_selected_color").mouseenter(function() {
    	jQuery('.id_bkx_cal_day_selected_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_cal_day_selected_color').hide();
	});

	jQuery('#id_bkx_time_available_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_time_available_color").click(function() {
    	jQuery('.id_bkx_time_available_color').show();
	  });
	jQuery( ".id_bkx_time_available_color").mouseenter(function() {
    	jQuery('.id_bkx_time_available_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_time_available_color').hide();
	});

/***************************************************************/
	jQuery('#id_bkx_time_selected_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_time_selected_color").click(function() {
    	jQuery('.id_bkx_time_selected_color').show();
	  });
	jQuery( ".id_bkx_time_selected_color").mouseenter(function() {
    	jQuery('.id_bkx_time_selected_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_time_selected_color').hide();
	});
/*****************************************************************/

	jQuery('#id_bkx_time_block_bg_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_time_block_bg_color").click(function() {
    	jQuery('.id_bkx_time_block_bg_color').show();
	  });
	jQuery( ".id_bkx_time_block_bg_color").mouseenter(function() {
    	jQuery('.id_bkx_time_block_bg_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_time_block_bg_color').hide();
	    });

/*****************************************************************/

jQuery('#id_bkx_time_block_extra_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_time_block_extra_color").click(function() {
    	jQuery('.id_bkx_time_block_extra_color').show();
	  });
	jQuery( ".id_bkx_time_block_extra_color").mouseenter(function() {
    	jQuery('.id_bkx_time_block_extra_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_time_block_extra_color').hide();
	    });
/*****************************************************************/

jQuery('#id_bkx_time_block_service_color').iris({
		width: 300,
		hide: true,
		palettes: true
	});
	jQuery( "#id_bkx_time_block_service_color").click(function() {
    	jQuery('.id_bkx_time_block_service_color').show();
	  });
	jQuery( ".id_bkx_time_block_service_color").mouseenter(function() {
    	jQuery('.id_bkx_time_block_service_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_time_block_service_color').hide();
	    });
	    
/*****************************************************************/ 		
		

	jQuery('#id_bkx_time_unavailable_color').iris({
		width: 300,
		hide: true,
		palettes: true,
	});
	jQuery( "#id_bkx_time_unavailable_color").click(function() {
    	jQuery('.id_bkx_time_unavailable_color').show();
	  });
  	 
  	 /** Mouse out and color picker out */

	jQuery( ".id_bkx_time_unavailable_color").mouseenter(function() {
    	jQuery('.id_bkx_time_unavailable_color').show();
	  }).mouseleave(function() {
	    jQuery('.id_bkx_time_unavailable_color').hide();
	});



	/********************end script for color picker*******************************/


	/* Business Form Jquery*/
	 
	    for(var count = 0; count < 10; count++){
	        jQuery('#business_days_'+count).change(function() {
	            // console.log($(this).val());
	        }).multipleSelect({
	            width: '100%'
	        });

	        jQuery( "#id_opening_time_"+count ).autocomplete({
			source:  	function(name, response) {
						jQuery.ajax({
							type: 'POST',
							dataType: 'json',
							url: ajax_url,
							data: 'action=get_time_format&name='+name.term,
							success: function(data) {
								response(data);
							}
						});
					},
			minLength: 1
			});
			jQuery( "#id_closing_time_"+count ).autocomplete({
				source:  function(name, response) {
					jQuery.ajax({
						type: 'POST',
						dataType: 'json',
						url: ajax_url,
						data: 'action=get_time_format&name='+name.term,
						success: function(data) {
							response(data);
						}
					});
				},
				minLength: 1
			});
	    }
});
 
jQuery( document ).ready(function() {
		<?php if(crud_option_multisite('enable_cancel_booking')==1):?>
		jQuery("#page_drop_down_cancel_booking").show();
		<?php else:?>
		jQuery("#page_drop_down_cancel_booking").hide();
		<?php endif;?>
		jQuery('#id_enable_cancel_booking_yes').click(function ()
		{
			var $id_enable_cancel_booking = jQuery("#id_enable_cancel_booking_yes").val();
			if($id_enable_cancel_booking == 1)
			{
				jQuery("#page_drop_down_cancel_booking").show();
			}
		});
		jQuery('#id_enable_cancel_booking_no').click(function ()
		{
			var $id_enable_cancel_booking = jQuery("#id_enable_cancel_booking_no").val();
			if($id_enable_cancel_booking == 0)
			{
				jQuery("#page_drop_down_cancel_booking").hide();
			}
		});
	});
	function add_more_days()
	{
	    var current_value = jQuery('#current_value').val();
	    var set_next_val = current_value;
	    jQuery( ".day_section_"+current_value).show();
	    if(current_value == '7'){ jQuery("#add_more_days").hide();}
	     
	    if(current_value <= '6')
	    {
	        set_next_val++;
	    }
	    jQuery('#current_value').val(set_next_val);
	}

	function remove_more_days(val)
	{
		var now_value = jQuery('#current_value').val();
		var set_next_val = now_value - 1;
	    var current_value = val;
	    jQuery( ".day_section_"+current_value).hide(); 
	    jQuery('#current_value').val(set_next_val);

	    if(set_next_val == 1){
	    	jQuery("#add_more_days").show();
	    }
	}
</script>
<style type="text/css">
	.setting-bookingx{
		background :#fff;
	    border: 1px solid #e5e5e5;
	    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
    	box-shadow: 0 1px 1px rgba(0,0,0,.04);
    	margin-top : 20px;
    	height: auto;
	}
	.widefat th {
		vertical-align: top;
	    text-align: left;
	    width: 300px;
	    line-height: 1.3;
	    font-weight: 600;
	    font-size : 14px;
	    color: #555;
	    padding: 8px 10px;

	}
	.setting-bookingx .standard{
		vertical-align: top;
	    text-align: left;
	    width: 240px;
	    line-height: 2.3;
	    font-weight: 600;
	    font-size : 14px;
	    color: #555;
	    padding: 8px 10px;
	    float: left;
 
	}

	.setting-bookingx .lable{
		vertical-align: top;
	    text-align: left;
	    width: 96%;
	    line-height: 2.3;
	    font-weight: 600;
	    font-size : 14px;
	    color: #555;
	    padding: 8px 10px;
	    float: left;
	} 
</style>
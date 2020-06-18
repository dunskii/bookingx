jQuery(document).ready(function () {

    jQuery(".bkx-booking-style").on('change', function () {
        if (jQuery(this).val() == 'day_style') {
            jQuery(".thickbox").addClass('bkx-preview-' + jQuery(this).val());
            jQuery(".thickbox").removeClass('bkx-preview-default');
            jQuery(".bkx-preview-" + jQuery(this).val()).attr("href", bkx_admin.day_style + '?style=day');
        }
        if (jQuery(this).val() == 'default') {
            jQuery(".thickbox").addClass('bkx-preview-' + jQuery(this).val());
            jQuery(".thickbox").removeClass('bkx-preview-day_style');
            jQuery(".bkx-preview-" + jQuery(this).val()).attr("href", bkx_admin.default_style + '?style=default');
        }
    });


    jQuery('.bkx-dashboard-column-all').on('click', function () {
        if (this.checked) {
            jQuery('.bkx-dashboard-column').each(function () {
                this.checked = true;
            });
        } else {
            jQuery('.bkx-dashboard-column').each(function () {
                this.checked = false;
            });
        }
    });

    jQuery('.bkx-dashboard-column').on('click', function () {
        if (jQuery('.bkx-dashboard-column:checked').length == jQuery('.bkx-dashboard-column').length) {
            jQuery('.bkx-dashboard-column-all').prop('checked', true);
        } else {
            jQuery('.bkx-dashboard-column-all').prop('checked', false);
        }
    });

    /**********************start script for color picker******************************/
    var ajax_url = bkx_admin.ajax_url;
    jQuery('#id_bkx_text_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_text_color").click(function () {
        jQuery('.id_bkx_text_color').show();
    });

    jQuery(".id_bkx_text_color").mouseenter(function () {
        jQuery('.id_bkx_text_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_text_color').hide();
    });


    jQuery('#id_bkx_background_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_background_color").click(function () {
        jQuery('.id_bkx_background_color').show();
    });
    jQuery(".id_bkx_background_color").mouseenter(function () {
        jQuery('.id_bkx_background_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_background_color').hide();
    });


    jQuery('#id_bkx_border_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_border_color").click(function () {
        jQuery('.id_bkx_border_color').show();
    });
    jQuery(".id_bkx_border_color").mouseenter(function () {
        jQuery('.id_bkx_border_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_border_color').hide();
    });


    jQuery('#id_bkx_progressbar_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_progressbar_color").click(function () {
        jQuery('.id_bkx_progressbar_color').show();
    });
    jQuery(".id_bkx_progressbar_color").mouseenter(function () {
        jQuery('.id_bkx_progressbar_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_progressbar_color').hide();
    });


    jQuery('#id_bkx_cal_border_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_cal_border_color").click(function () {
        jQuery('.id_bkx_cal_border_color').show();
    });
    jQuery(".id_bkx_cal_border_color").mouseenter(function () {
        jQuery('.id_bkx_cal_border_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_cal_border_color').hide();
    });

    jQuery('#id_bkx_cal_day_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_cal_day_color").click(function () {
        jQuery('.id_bkx_cal_day_color').show();
    });
    jQuery(".id_bkx_cal_day_color").mouseenter(function () {
        jQuery('.id_bkx_cal_day_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_cal_day_color').hide();
    });


    jQuery('#id_bkx_cal_day_selected_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_cal_day_selected_color").click(function () {
        jQuery('.id_bkx_cal_day_selected_color').show();
    });
    jQuery(".id_bkx_cal_day_selected_color").mouseenter(function () {
        jQuery('.id_bkx_cal_day_selected_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_cal_day_selected_color').hide();
    });

    jQuery('#id_bkx_time_available_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_time_available_color").click(function () {
        jQuery('.id_bkx_time_available_color').show();
    });
    jQuery(".id_bkx_time_available_color").mouseenter(function () {
        jQuery('.id_bkx_time_available_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_available_color').hide();
    });

    /***************************************************************/
    jQuery('#id_bkx_time_selected_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_time_selected_color").click(function () {
        jQuery('.id_bkx_time_selected_color').show();
    });
    jQuery(".id_bkx_time_selected_color").mouseenter(function () {
        jQuery('.id_bkx_time_selected_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_selected_color').hide();
    });
    /*****************************************************************/

    jQuery('#id_bkx_time_block_bg_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_time_block_bg_color").click(function () {
        jQuery('.id_bkx_time_block_bg_color').show();
    });
    jQuery(".id_bkx_time_block_bg_color").mouseenter(function () {
        jQuery('.id_bkx_time_block_bg_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_block_bg_color').hide();
    });

    /*****************************************************************/

    jQuery('#id_bkx_time_block_extra_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_time_block_extra_color").click(function () {
        jQuery('.id_bkx_time_block_extra_color').show();
    });
    jQuery(".id_bkx_time_block_extra_color").mouseenter(function () {
        jQuery('.id_bkx_time_block_extra_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_block_extra_color').hide();
    });
    /*****************************************************************/

    jQuery('#id_bkx_time_block_service_color').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_bkx_time_block_service_color").click(function () {
        jQuery('.id_bkx_time_block_service_color').show();
    });
    jQuery(".id_bkx_time_block_service_color").mouseenter(function () {
        jQuery('.id_bkx_time_block_service_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_block_service_color').hide();
    });

    /*****************************************************************/


    jQuery('#id_bkx_time_unavailable_color').iris({
        width: 300,
        hide: true,
        palettes: true,
    });
    jQuery("#id_bkx_time_unavailable_color").click(function () {
        jQuery('.id_bkx_time_unavailable_color').show();
    });

    /** Mouse out and color picker out */

    jQuery(".id_bkx_time_unavailable_color").mouseenter(function () {
        jQuery('.id_bkx_time_unavailable_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_unavailable_color').hide();
    });

    /**************************************************************************/

    jQuery('#id_bkx_cal_month_title_color').iris({
        width: 300,
        hide: true,
        palettes: true,
    });
    jQuery("#id_bkx_cal_month_title_color").click(function () {
        jQuery('.id_bkx_cal_month_title_color').show();
    });

    /** Mouse out and color picker out */

    jQuery(".id_bkx_cal_month_title_color").mouseenter(function () {
        jQuery('.id_bkx_cal_month_title_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_cal_month_title_color').hide();
    });

    /**********************************************************************/
    jQuery('#id_bkx_cal_month_bg_color').iris({
        width: 300,
        hide: true,
        palettes: true,
    });
    jQuery("#id_bkx_cal_month_bg_color").click(function () {
        jQuery('.id_bkx_cal_month_bg_color').show();
    });

    /** Mouse out and color picker out */

    jQuery(".id_bkx_cal_month_bg_color").mouseenter(function () {
        jQuery('.id_bkx_cal_month_bg_color').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_cal_month_bg_color').hide();
    });

    /**********************************************************************/
    jQuery('#id_bkx_time_new_selected').iris({
        width: 300,
        hide: true,
        palettes: true,
    });
    jQuery("#id_bkx_time_new_selected").click(function () {
        jQuery('.id_bkx_time_new_selected').show();
    });

    /** Mouse out and color picker out */

    jQuery(".id_bkx_time_new_selected").mouseenter(function () {
        jQuery('.id_bkx_time_new_selected').show();
    }).mouseleave(function () {
        jQuery('.id_bkx_time_new_selected').hide();
    });

    jQuery('#id_bkx_next_btn').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery('#id_bkx_prev_btn').iris({
        width: 300,
        hide: true,
        palettes: true
    });


    /********************end script for color picker*******************************/


    /* Business Form Jquery*/

    for (var count = 0; count < 10; count++) {
        jQuery('#business_days_' + count).change(function () {
            // console.log($(this).val());
        }).multipleSelect({
            width: '100%'
        });

        jQuery("#id_opening_time_" + count).autocomplete({
            source: function (name, response) {
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajax_url,
                    data: 'action=get_time_format&name=' + name.term,
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1
        });
        jQuery("#id_closing_time_" + count).autocomplete({
            source: function (name, response) {
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajax_url,
                    data: 'action=get_time_format&name=' + name.term,
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1
        });
    }


    var is_cancel = bkx_admin.is_cancel;
    if (is_cancel == 1) {
        jQuery("#page_drop_down_cancel_booking").show();
    } else {
        jQuery("#page_drop_down_cancel_booking").hide();
    }
    jQuery('#id_enable_cancel_booking_yes').click(function () {
        var $id_enable_cancel_booking = jQuery("#id_enable_cancel_booking_yes").val();
        if ($id_enable_cancel_booking == 1) {
            jQuery("#page_drop_down_cancel_booking").show();
        }
    });
    jQuery("#id_biz_vac_sd").datepicker({dateFormat: "mm/dd/yy"});

    jQuery('#id_enable_cancel_booking_no').click(function () {
        var $id_enable_cancel_booking = jQuery("#id_enable_cancel_booking_no").val();
        if ($id_enable_cancel_booking == 0) {
            jQuery("#page_drop_down_cancel_booking").hide();
        }
    });

    jQuery("#id_biz_vac_sd").datepicker({
        onSelect: function (date) {
            jQuery("#id_biz_vac_ed").val('');
            jQuery("#id_biz_vac_ed").datepicker("option", "minDate", date);
        }
    });
    jQuery("#id_biz_vac_ed").datepicker({dateFormat: "mm/dd/yy"});

    jQuery("#id_biz_ph_1").datepicker({dateFormat: "mm/dd/yy"});


});

function add_more_ph() {
    var current_value = jQuery('#temp_pu_h_cnt').val();
    var set_next_val = current_value;
    set_next_val++;
    jQuery('#temp_pu_h_cnt').val(set_next_val);
    jQuery(".bkx_more_pub_holiday").after('<input type="text" name="biz_ph[]" id="id_clone_ph_' + set_next_val + '"><div class="clear"></div>');
    jQuery("#id_clone_ph_" + set_next_val).datepicker({dateFormat: "mm/dd/yy"});


}

function add_more_days() {
    var current_value = jQuery('#current_value').val();
    var set_next_val = current_value;
    jQuery(".day_section_" + current_value).show();
    if (current_value >= 7) {
        jQuery("#add_more_days").hide();
    } else {
        jQuery("#add_more_days").show();
    }
    if (current_value <= '6') {
        set_next_val++;
    }
    jQuery('#current_value').val(set_next_val);
}

function remove_more_days(val) {
    jQuery("#remove_biz_days").val(jQuery("#remove_biz_days").val() + val + ',');
    var now_value = jQuery('#current_value').val();
    var set_next_val = now_value - 1;
    var current_value = val;
    jQuery("#business_days_" + current_value).css('visibility', 'hidden');
    jQuery(".day_section_" + current_value).hide();
    jQuery('#current_value').val(set_next_val);
    if (set_next_val <= 7) {
        jQuery("#add_more_days").show();
    }
}
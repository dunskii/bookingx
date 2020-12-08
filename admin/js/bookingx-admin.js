
jQuery(function ($) {
    extracted_legal(bkx_admin.is_legal_options);
    $(".bkx-booking-style").on('change', function () {
        if ($(this).val() == 'day_style') {
            $(".thickbox").addClass('bkx-preview-' + $(this).val());
            $(".thickbox").removeClass('bkx-preview-default');
            $(".bkx-preview-" + $(this).val()).attr("href", bkx_admin.day_style + '?style=day');
        }
        if ($(this).val() == 'default') {
            $(".thickbox").addClass('bkx-preview-' + $(this).val());
            $(".thickbox").removeClass('bkx-preview-day_style');
            $(".bkx-preview-" + $(this).val()).attr("href", bkx_admin.default_style + '?style=default');
        }
    });

    $('#id_bkx_enable_customer_dashboard_yes').on('click',function () {
        var cust_dash = $("#id_bkx_enable_customer_dashboard_yes").val();
        if (cust_dash == 1) {
            $(".shortcode-section").show();
        }
    });

    $('#id_bkx_enable_customer_dashboard_no').on('click',function () {
        var cust_dash = $("#id_bkx_enable_customer_dashboard_no").val();
        if (cust_dash == 0) {
            $(".shortcode-section").hide();
        }
    });

});


function extracted_legal( val ) {
    if (val == 1) {
        jQuery(".legal-dropdown").show();
    }else{
        jQuery(".legal-dropdown").hide();
    }
}

jQuery(document).ready(function () {


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

    jQuery('#id_bkx_legal_options_yes').on('click',function () {
        var legal_options = jQuery("#id_bkx_legal_options_yes").val();
        extracted_legal(legal_options);
    });

    jQuery('#id_bkx_legal_options_no').on('click',function () {
        var legal_options = jQuery("#id_bkx_legal_options_no").val();
        extracted_legal(legal_options);
    });

    /**********************start script for color picker******************************/
    var ajax_url = bkx_admin.ajax_url;

    var bkx_color_picker, bkx_color_picker_total, cp, cp_id;
    bkx_color_picker = ["id_bkx_text_color", "id_bkx_background_color", "id_bkx_border_color",
        "id_bkx_progressbar_color", "id_bkx_cal_border_color", "id_bkx_cal_day_color", "id_bkx_cal_day_selected_color",
        "id_bkx_time_available_color", "id_bkx_time_selected_color", "id_bkx_time_block_bg_color", "id_bkx_time_block_extra_color",
        "id_bkx_time_block_service_color", "id_bkx_time_unavailable_color", "id_bkx_cal_month_title_color",
        "id_bkx_cal_month_bg_color", "id_bkx_time_new_selected","id_bkx_next_btn","id_bkx_prev_btn", "id_bkx_pay_now_btn", "id_bkx_form_slot_time_color"];

    bkx_color_picker_total = bkx_color_picker.length;
    for (cp = 0; cp < bkx_color_picker_total; cp++) {
        cp_id = bkx_color_picker[cp];
        jQuery("#" + cp_id).iris(
            {width: 300, hide: true, palettes: true});
        jQuery("#" + cp_id).on('click', function () {
            jQuery("#" + cp_id).iris('show');
        });
    }


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
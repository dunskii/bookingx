jQuery(document).on('submit', '#post', function (e) {
    var base_text = extra_obj.base_alias;
    var extra_text = extra_obj.extra_alias;
    var error_list = new Array();
    if (jQuery('#id_addition_name').val() == "") {
        error_list.push("Please enter " + extra_text + " name");
    }
    if (jQuery('#id_addition_price').val() == "") {
        error_list.push("Please enter " + extra_text + " price");
    } else if (isNaN(jQuery('#id_addition_price').val()) === true) {
        error_list.push("Please enter only numbers for price");
    }
    if (jQuery('#id_addition_time_option').val() == "Months") {
        if (jQuery("#id_addition_months").val() == "") {
            error_list.push("Please enter no of months");
        } else if (isNaN(jQuery('#id_addition_months').val()) === true) {
            error_list.push("Please enter months as integer");
        }
    }
    if (jQuery('#id_addition_time_option').val() == "Days") {
        if (jQuery("#id_addition_days").val() == "") {
            error_list.push("Please enter no of days");
        } else if (isNaN(jQuery('#id_addition_days').val()) === true) {
            error_list.push("Please enter days as integer");
        }
    }
    if (jQuery('#id_addition_time_option').val() == "Hour and Minutes") {
        if (jQuery("#id_addition_hours_minutes").val() == "") {
            error_list.push("Please enter no of hours and minutes");
        } else if (isNaN(jQuery('#id_addition_hours_minutes').val()) === true) {
            error_list.push("Please enter hours as integer");
        }
        if (jQuery("#id_addition_hours_minutes").val() == 0 && jQuery("#id_addition_minutes").val() == 0) {
            error_list.push("Please enter proper time, both hours and minutes should not be zero");
        }
    }
    var counter = 0;
    jQuery(".extra-base-checkboxes").each(function () {
        if (jQuery(this).attr('checked')) {
            counter += 1;
        }
    });
    if (counter == 0) {
        error_list.push("Please select at least one " + base_text + " associated with this " + extra_text + "");
    }
    var temp_err = "";
    for (var i = 0; i < error_list.length; i++) {
        temp_err = temp_err + "<p><strong>" + error_list[i] + "</strong></p>";
    }
    if (temp_err == "") {
        jQuery("#error_list").hide();
        return true;
    } else {
        jQuery("#error_list").html(temp_err);
        jQuery("#error_list").show();
        jQuery(this).find('.button-primary').removeClass('disabled');
        jQuery(this).find('.spinner').removeClass('is-active');
        return false;
    }
});
jQuery(document).ready(function () {
    /**********************start script for color picker******************************/
    jQuery('#id_extra_colour').iris({
        width: 300,
        hide: true,
        palettes: true
    });
    jQuery("#id_extra_colour").click(function () {
        jQuery('.id_extra_colour').show();
    });
    jQuery(".id_extra_colour").mouseenter(function () {
        jQuery('.id_extra_colour').show();
    }).mouseleave(function () {
        jQuery('.id_extra_colour').hide();
    });
    jQuery("#id_addition_unavailable_from").datepicker({
        onSelect: function (date) {
            jQuery("#id_addition_unavailable_to").val('');
            jQuery("#id_addition_unavailable_to").datepicker("option", "minDate", date);
        }
    });
    jQuery("#id_addition_unavailable_to").datepicker();
    var temp_months_days_times = jQuery("#id_addition_time_option").val();
    if (temp_months_days_times == "Months") {
        jQuery("#months").show();
        jQuery("#days").hide();
        jQuery("#hours_minutes").hide();
        //jQuery("#overlap").show();
    }
    if (temp_months_days_times == "Days") {
        jQuery("#months").hide();
        jQuery("#days").show();
        jQuery("#hours_minutes").hide();
        //jQuery("#overlap").show();
    }
    if (temp_months_days_times == "Hour and Minutes") {
        jQuery("#months").hide();
        jQuery("#days").hide();
        jQuery("#hours_minutes").show();
        //jQuery("#overlap").show();
    }
    if (temp_months_days_times == "None") {
        jQuery("#months").hide();
        jQuery("#days").hide();
        jQuery("#hours_minutes").hide();
        jQuery("#overlap").hide();
    }
    jQuery("#id_addition_time_option").bind("change", function (event, ui) {
        var temp_months_days_times_change = jQuery("#id_addition_time_option").val();
        if (temp_months_days_times_change == "Months") {
            jQuery("#months").show();
            jQuery("#days").hide();
            jQuery("#hours_minutes").hide();
            //jQuery("#overlap").show();
        }
        if (temp_months_days_times_change == "Days") {
            jQuery("#months").hide();
            jQuery("#days").show();
            jQuery("#hours_minutes").hide();
            //jQuery("#overlap").show();
        }
        if (temp_months_days_times_change == "Hour and Minutes") {
            jQuery("#months").hide();
            jQuery("#days").hide();
            jQuery("#hours_minutes").show();
            //jQuery("#overlap").show();
        }
        if (temp_months_days_times_change == "None") {
            jQuery("#months").hide();
            jQuery("#days").hide();
            jQuery("#hours_minutes").hide();
            jQuery("#overlap").hide();
        }
    });


    jQuery('#id_addition_base_all').click(function () {
        jQuery('.extra-base-checkboxes').prop('checked', this.checked);
    });

    jQuery('#id_seat_all').click(function () {
        jQuery('.extra-seat-checkboxes').prop('checked', this.checked);
    });

    //to change the addition unavailability form input type visibility
    jQuery("input[name=addition_is_unavailable]").bind("change", function (event, ui) {
        if (jQuery(this).attr('checked') == "checked") {
            jQuery("#unavailable_from").show();
        }
        if (jQuery(this).attr('checked') == undefined) {
            jQuery("#unavailable_from").hide();
        }
    });
    if (jQuery("input[name=addition_is_unavailable]").attr('checked') == "checked") {
        jQuery("#unavailable_from").show();
    }
    if (jQuery("input[name=addition_is_unavailable]").attr('checked') == undefined) {
        jQuery("#unavailable_from").hide();
    }
});
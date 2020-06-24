jQuery(function ($) {
    var get_url = function (endpoint) {
        return bkx_booking_form_admin_params.bkx_ajax_url.toString().replace(
            '%%endpoint%%',
            endpoint
        );
    };

    var listToArray = function (input) {
        var _arr = [];
        $.each($(input), function () {
            _arr.push($(this).val());
        });
        return (_arr.length > 0) ? _arr : 'None';
    };

    var IsJsonString = function (str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    };

    var GetCurrentStep = function () {
        var current_obj = $(".bkx-booking-form").find(".bkx-form-active").data("active");
        return current_obj;
    };

    var CheckIsActiveForm = function (step = 1) {
        return $('.bkx-booking-form .step-' + step).hasClass('bkx-form-active');
    };

    /**
     * Object to handle Booking form Ajax Call.
     */
    var booking_form = {
        $error_group: $('.bkx-booking-form .bookingx-error-group'),
        seat_id: "",
        base_id: "",
        extra_id: "",
        total_slots: 0,
        starting_slot: 0,
        booking_style: 'default',
        time_option: "",
        date: "",
        flag: 0,
        days_selected: "",
        disable_days_note: $('.bkx-booking-form .step-1 .bkx-disable-days-note'),
        /**
         * Initialize Booking Form UI events.
         */
        init: function () {
            booking_form.update_booking_total();
            if (bkx_booking_form_admin_params.seat_id > 0) {
                booking_form.skip_step_1();
            }
            $(document).on('change select', '.bkx-booking-form .step-1 .bkx-staff', this.seat_change);
            $(document).on('change select', '.bkx-booking-form .step-1 .bkx-services-lists', this.base_change);
            $(document).on('change select', '.bkx-booking-form .step-1 .bkx-services-extend', this.update_booking_total);
            $(document).on('change', '.bkx-booking-form .step-1 :input[name^=bkx-extra-service][type=checkbox]', this.extra_checked);
            $(document).on('change', '.bkx-order_summary :input[name^=reassign_booking]', this.reassign_booking);
            $(document).on('click button', '.bkx-booking-form .bkx-form-submission-next', this.submit);
            $(document).on('click button', '.bkx-booking-form .bkx-form-submission-final', this.submit);
            $(document).on('click button', '.bkx-booking-form .bkx-form-submission-previous', this.submit_previous);
            $(document).on('click a', '.bkx-booking-form .step-2 .select-time .available', this.verify_slots);
            $(document).on('change', '.bkx-booking-form .step-4 :input[name^=bkx_payment_gateway_method]', this.payment_method_on_change);
            $(document).on('click button', '.bkx-booking-form .bkx-form-submission-final', this.book_now);
            $('.booking-slots').data('total_slots', 0);
            $('.booking-slots').data('starting_slot', 0);
        },
        skip_step_1: function () {
            booking_form.seat_change();
        },
        /**
         * After enable the On Change Seat Select.
         */
        seat_change: function () {
            if (bkx_booking_form_admin_params.seat_id > 0) {
                $('.bkx-booking-form .step-1 .bkx-staff-lists').val(bkx_booking_form_admin_params.seat_id);
            }
            var data = {
                security: bkx_booking_form_admin_params.on_seat_change_nonce,
                seat_id: parseInt($('.bkx-booking-form .step-1 .bkx-staff-lists').val())
            };
            var service_list_obj = $('.bkx-booking-form .step-1 .bkx-services-lists');
            var extra_list_obj = $('.bkx-booking-form .step-1 .bkx-extra-lists');
            service_list_obj.val(0);

            booking_form.update_booking_total();
            booking_form.disable_days_note.html('');
            $('.bkx-booking-form .step-1 .service-extended').hide();
            $('.bkx-booking-form .step-1 .bkx-service-extend').html('');
            $.ajax({
                type: 'POST',
                url: get_url('on_seat_change'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        service_list_obj.removeAttr("disabled");
                        service_list_obj.html(response);
                    } else {
                        service_list_obj.html("");
                        extra_list_obj.html("");
                        service_list_obj.attr("disabled", 'disabled');
                        service_list_obj.prop("disabled", true);
                    }
                },
                complete: function () {
                    if (bkx_booking_form_admin_params.seat_id > 0) {
                        $('.bkx-admin-loading-edit h2').html('Still Loading, Please Wait..');
                        booking_form.base_change();
                    }
                }
            });
        },
        reassign_booking: function () {
            var order_id = $(this).data('order-id');
            var seat_id = parseInt($(this).val());
            var data = {
                security: bkx_booking_form_admin_params.check_staff_availability,
                seat_id: seat_id,
                booking_record_id: order_id
            };
            $.ajax({
                type: 'POST',
                url: get_url('check_staff_availability'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response) {
                        var temp_obj = $.parseJSON(response);
                        var result = temp_obj['data'];
                        if (parseInt(result) == 1) {
                            alert(bkx_booking_form_admin_params.string_success_reassign);
                            window.location.reload();
                        } else {
                            alert(bkx_booking_form_admin_params.string_something_went);
                        }
                    } else {
                    }
                },
                complete: function () {

                }
            });

        },
        /**
         *  On Change Base Select.
         */
        base_change: function () {
            if (bkx_booking_form_admin_params.base_id > 0) {
                $('.bkx-booking-form .step-1 .bkx-services-lists').val(bkx_booking_form_admin_params.base_id);
                booking_form.base_id = parseInt($('.bkx-booking-form .step-1 .bkx-services-lists').val());
            }
            booking_form.disable_days_note.html('');
            $('.bkx-booking-form .step-1 :input[name^=bkx-extra-service][type=checkbox]').removeAttr('checked');
            $('.bkx-booking-form .step-1 :input[name^=bkx-extra-service][type=checkbox]').prop('checked', false);

            var data = {
                security: bkx_booking_form_admin_params.on_base_change_nonce,
                base_id: $('.bkx-booking-form .step-1 .bkx-services-lists').val()
            };
            var extra_list_obj = $('.bkx-booking-form .step-1 .bkx-extra-lists');
            var service_extend = $('.bkx-booking-form .step-1 .bkx-services-extend');

            service_extend.html('');
            booking_form.update_booking_total();
            $.ajax({
                type: 'POST',
                url: get_url('on_base_change'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        $('.bkx-booking-form .step-1 .service-extended').show();
                        extra_list_obj.removeAttr("disabled");
                        var result = $.parseJSON(response);
                        extra_list_obj.html(result.extra_html);
                        booking_form.booking_style = result.booking_style;
                        booking_form.time_option = result.time_option;
                        if (result.extended != "") {
                            service_extend.removeAttr("disabled");
                            service_extend.html(result.extended);
                        } else {
                            $('.bkx-booking-form .step-1 .service-extended').hide();
                        }

                    } else {
                        extra_list_obj.html("");
                        service_extend.html('');
                    }
                },
                complete: function () {
                    if (bkx_booking_form_admin_params.addition_ids != 0) {
                        var extra_selected = bkx_booking_form_admin_params.addition_ids.split(',');
                        for (id in extra_selected) {
                            $("#bkx-extra-service-" + extra_selected[id]).prop('checked', true);
                            $("#bkx-extra-service-" + extra_selected[id]).attr('checked', 'checked');
                        }
                    }
                    booking_form.extra_checked();
                    if (bkx_booking_form_admin_params.extended_base > 0) {
                        $('.bkx-booking-form .step-1 .bkx-services-extend').val(bkx_booking_form_admin_params.extended_base);
                        booking_form.update_booking_total();
                    }
                }
            });
        },
        extra_checked: function () {
            booking_form.update_booking_total();
            booking_form.disable_days_note.html('');
            if (listToArray(".bkx-booking-form .step-1 :input[name^=bkx-extra-service]:checked") != 'None') {
                booking_form.disable_days_note.html("<div class=\"row\"><div class=\"col-lg-12\"> <strong> Note : </strong> " + bkx_booking_form_admin_params.disable_days_note + "</div></div>");
            }
            booking_form.form_progress();
        },
        submit: function (e) {
            e.preventDefault();
            booking_form.form_progress();
        },
        form_progress: function () {
            booking_form.seat_id = parseInt($('.bkx-booking-form .step-1 .bkx-staff-lists').val());
            booking_form.base_id = parseInt($('.bkx-booking-form .step-1 .bkx-services-lists').val());
            booking_form.extra_id = listToArray(".bkx-booking-form .step-1 :input[name^=bkx-extra-service]:checked");

            if ($('.booking-slots').data('starting_slot') > 0) {
                booking_form.total_slots = ($('.booking-slots').data('total_slots')) ? $('.booking-slots').data('total_slots') : 0;
                booking_form.starting_slot = ($('.booking-slots').data('starting_slot')) ? $('.booking-slots').data('starting_slot') : 0;
            }

            booking_form.date = ($('.booking-slots').data('date')) ? $('.booking-slots').data('date') : "";
            booking_form.flag = 0;
            booking_form.validate_form_fields();

            if (GetCurrentStep() < 2 && booking_form.booking_style == 'default') {
                $('.bkx-booking-form .step-2.default').show();
                $('.bkx-booking-form .step-2.day-style').hide();
                booking_form.BookingFormGetCalendarAvailability((bkx_booking_form_admin_params.booking_date) ? bkx_booking_form_admin_params.booking_date : moment().format("Y-M-D"));
            }

            //Booking Step 2B Design
            if (GetCurrentStep() < 2 && booking_form.booking_style == 'day_style') {
                $('.bkx-booking-form .step-2.day-style').show();
                $('.bkx-booking-form .step-2.default').hide();
                booking_form.BookingFormGetCalendarAvailabilityDayStyle((bkx_booking_form_admin_params.booking_date) ? bkx_booking_form_admin_params.booking_date : moment().format("Y-M-D"));
            }

            if (booking_form.flag == 1 && GetCurrentStep() < 2) {
                booking_form.BookingFormProgressNext();
                booking_form.scroll_to_notices();
            }
        },
        book_now: function () {
            if (confirm('Are you sure want to update booking?')) {
                booking_form.validate_form_fields();
                $('.bkx-form-submission-final').text('Please wait..');
                $('.bkx-form-submission-final').prop('disabled', true);
                if ($('.booking-slots').data('starting_slot') > 0) {
                    booking_form.total_slots = ($('.booking-slots').data('total_slots')) ? $('.booking-slots').data('total_slots') : 0;
                    booking_form.starting_slot = ($('.booking-slots').data('starting_slot')) ? $('.booking-slots').data('starting_slot') : 0;
                }
                var is_admin_edit;
                if (bkx_booking_form_admin_params.booking_id) {
                    is_admin_edit = true;
                }
                //var bkx_payment_gateway_method = listToArray(".bkx-booking-form .step-4 :input[name^=bkx_payment_gateway_method]:checked");
                var service_extend = parseInt($('.bkx-booking-form .step-1 .bkx-services-extend').val());
                var form = $('#bkx-booking-generate');
                var data = {
                    security: bkx_booking_form_admin_params.book_now_nonce,
                    seat_id: booking_form.seat_id,
                    base_id: booking_form.base_id,
                    extra_id: booking_form.extra_id,
                    date: booking_form.date,
                    time_option: booking_form.time_option,
                    booking_multi_days: booking_form.days_selected,
                    booking_time: $('.booking-slots').find("a[data-slot='" + booking_form.starting_slot + "']").data('time'),
                    starting_slot: booking_form.starting_slot,
                    total_slots: booking_form.total_slots,
                    service_extend: service_extend,
                    is_admin_edit: is_admin_edit,
                    //payment_method : bkx_payment_gateway_method,
                    bkx_first_name: $("input[name=bkx_first_name]").val(),
                    bkx_last_name: $("input[name=bkx_last_name]").val(),
                    bkx_phone_number: $("input[name=bkx_phone_number]").val(),
                    bkx_email_address: $("input[name=bkx_email_address]").val(),
                    last_page_url: bkx_booking_form_admin_params.last_page_url,
                    order_id: bkx_booking_form_admin_params.booking_id,
                    post_data: form.serializeArray()
                };

                $.ajax({
                    type: 'POST',
                    url: get_url('book_now'),
                    data: data,
                    dataType: 'html',
                    success: function (response) {
                        if (response != 'NORF' && response != 'SWR' && response != 'BTB') {
                            var result = $.parseJSON(response);
                            if (result.meta_data.redirect_to) {
                                window.location.href = result.meta_data.redirect_to;
                            } else {
                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'order-id',
                                    name: 'order_id',
                                    value: result.meta_data.order_id
                                }).appendTo('#bkx-booking-generate');

                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'payment-gateway',
                                    name: 'bkx_payment_gateway_method',
                                    value: result.meta_data.bkx_payment_gateway_method
                                }).appendTo('#bkx-booking-generate');

                                $("#bkx-booking-generate").submit();
                            }

                        } else if (response == 'BTB') {
                            booking_form.$error_group.prepend('<div class="row"><div class="col-md-12"><ul class="bookingx-error">' + bkx_booking_form_admin_params.string_select_date_time + '</ul></div></div>');
                            booking_form.scroll_to_notices();
                            $('.bkx-form-submission-final').prop('disabled', false);
                            $('.bkx-form-submission-final').text('Booking Update');
                        }  else if (response == 'SWR') {
                            booking_form.$error_group.prepend('<div class="row"><div class="col-md-12"><ul class="bookingx-error">' + bkx_booking_form_admin_params.string_something_went + '</ul></div></div>');
                            booking_form.scroll_to_notices();
                            $('.bkx-form-submission-final').prop('disabled', false);
                            $('.bkx-form-submission-final').text('Booking Update');
                        } else {
                            booking_form.$error_group.prepend('<div class="row"><div class="col-md-12"><ul class="bookingx-error">' + bkx_booking_form_admin_params.string_something_went + '</ul></div></div>');
                            booking_form.scroll_to_notices();
                            $('.bkx-form-submission-final').prop('disabled', false);
                            $('.bkx-form-submission-final').text('Booking Update');
                        }
                    },
                    complete: function () {

                    }
                });
            } else {
                $('.bkx-form-submission-final').prop('disabled', false);
                $('.bkx-form-submission-final').text('Booking Update');
                return false;
            }
        },
        payment_method: function () {
            var data = {
                security: bkx_booking_form_admin_params.get_payment_method_nonce,
                seat_id: booking_form.seat_id
            };
            var payment_methods = $('.bkx-booking-form .step-4 .bkx-payment-method');
            payment_methods.html('');
            $.ajax({
                type: 'POST',
                url: get_url('get_payment_method'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        payment_methods.html(response);
                    } else {

                    }
                },
                complete: function () {

                }
            });
        },
        payment_method_on_change: function () {
            var data = {
                security: bkx_booking_form_admin_params.payment_method_on_change_nonce,
                payment_id: $(this).val()
            };
            //bkx-form-submission-final
            var image_section = $('.bkx-booking-form .step-4 .bkx-form-submission-final .payment-gateway-image');
            $.ajax({
                type: 'POST',
                url: get_url('payment_method_on_change'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        var result = $.parseJSON(response);
                        image_section.attr('src', result.image_url);
                    } else {

                    }
                },
                complete: function () {

                }
            });
        },
        submit_previous: function (e) {
            e.preventDefault();
            booking_form.validate_form_fields();
            if (booking_form.flag == 1) {
                booking_form.BookingFormProgressPrevious();
                booking_form.scroll_to_notices();
            }
        },
        validate_form_fields: function () {

            var $error_messages = "";
            /*if(booking_form.seat_id !== booking_form.seat_id){
                $error_messages +=" <li>" +bkx_booking_form_admin_params.string_select_a_seat+ "</li>"
            }
            if(booking_form.base_id !== booking_form.base_id){
                $error_messages +=" <li>" +bkx_booking_form_admin_params.string_select_a_base+ "</li>"
            }*/

            if ($('.booking-slots').data('starting_slot') == 0 && GetCurrentStep() == 2 && booking_form.time_option == "M") {
                $error_messages += " <li> " + bkx_booking_form_params.string_select_date_time + "</li>"
            }

            if (booking_form.days_selected == "" && booking_form.time_option == "D" && GetCurrentStep() == 2) {
                $error_messages += " <li> " + bkx_booking_form_params.bkx_validate_day_select + "</li>"
            }

            /*if( GetCurrentStep() == 3 ){
                if( booking_form.validate_user_details() == true){
                    $error_messages += "Please complete below form.";
                }
                if( listToArray(".bkx-booking-form .step-3 :input[name^=bkx_terms_and_conditions]:checked") == 'None'){
                    $error_messages +=" <li> " +bkx_booking_form_admin_params.bkx_terms_and_conditions+ "</li>"
                }
                if( listToArray(".bkx-booking-form .step-3 :input[name^=bkx_privacy_policy]:checked") == 'None'){
                    $error_messages +=" <li> " +bkx_booking_form_admin_params.bkx_privacy_policy+ "</li>"
                }
                if( listToArray(".bkx-booking-form .step-3 :input[name^=bkx_cancellation_policy]:checked") == 'None'){
                    $error_messages +=" <li> " +bkx_booking_form_admin_params.bkx_cancellation_policy+ "</li>"
                }
            }*/

            // if( GetCurrentStep() == 4 ){
            //     var bkx_payment_gateway_method = listToArray(".bkx-booking-form .step-4 :input[name^=bkx_payment_gateway_method]:checked");
            //     if(bkx_payment_gateway_method == "None"){
            //         $error_messages +=" <li> Please select payment method.</li>"
            //     }
            // }

            if ($error_messages) {
                booking_form.submit_error($error_messages);
            } else {
                $('.bkx-booking-form .bookingx-error-group .bookingx-error').remove();
                booking_form.flag = 1;
            }
        },
        /**
         * Booking form validate and move on next step
         */
        submit_error: function (error_message) {
            $('.bkx-booking-form .bookingx-error-group .bookingx-error').remove();
            booking_form.$error_group.prepend('<div class="row"><div class="col-md-12"><ul class="bookingx-error">' + error_message + '</ul></div></div>');
            booking_form.scroll_to_notices();
        },
        scroll_to_notices: function () {
            var scrollElement = $('.bkx-booking-form .progress-wrapper');

            if (!scrollElement.length) {
                scrollElement = $('.booking-x .bkx-booking-form');
            }
            $.bkx_scroll_to_notices(scrollElement);
        },
        validate_user_details: function () {
            var user_form_fields = [
                {'key': 'bkx_first_name', 'val': bkx_booking_form_admin_params.bkx_first_name},
                {'key': 'bkx_last_name', 'val': bkx_booking_form_admin_params.bkx_last_name},
                {'key': 'bkx_phone_number', 'val': bkx_booking_form_admin_params.bkx_phone_number},
                {'key': 'bkx_email_address', 'val': bkx_booking_form_admin_params.bkx_email_address},
                {'key': 'bkx_terms_and_conditions', 'val': bkx_booking_form_admin_params.bkx_terms_and_conditions},
                {'key': 'bkx_privacy_policy', 'val': bkx_booking_form_admin_params.bkx_privacy_policy},
                {'key': 'bkx_cancellation_policy', 'val': bkx_booking_form_admin_params.bkx_cancellation_policy}
            ];
            var error = false;
            $.each(user_form_fields, function (index, value) {
                $("input[name=" + value.key + "]").next("span").remove();
                $("input[name=" + value.key + "]").parent().removeClass('has-error');
                if ($("input[name=" + value.key + "]").val() == "") {
                    $("input[name=" + value.key + "]").parent().addClass('has-error');
                    $("input[name=" + value.key + "]").after('<span class="error-text">' + value.val + '</span>');
                    error = true;
                }

                if (value.key == 'bkx_email_address' && $("input[name=bkx_email_address]").val()) {
                    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);

                    if (!pattern.test($("input[name=bkx_email_address]").val())) {
                        $("input[name=" + value.key + "]").parent().addClass('has-error');
                        $("input[name=" + value.key + "]").after('<span class="error-text">' + bkx_booking_form_admin_params.bkx_email_valid + '</span>');
                        error = true;
                    }
                }

                if (value.key == 'bkx_phone_number' && $("input[name=bkx_phone_number]").val()) {
                    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
                    if (!numericReg.test($("input[name=bkx_phone_number]").val()) || $("input[name=bkx_phone_number]").val().length < 10) {
                        $("input[name=" + value.key + "]").parent().addClass('has-error');
                        $("input[name=" + value.key + "]").after('<span class="error-text">' + bkx_booking_form_admin_params.bkx_valid_phone_number + '</span>');
                        error = true;
                    }
                }
            });
            return error;
        },
        update_booking_total: function () {
            var extra_service = listToArray(".bkx-booking-form .step-1 :input[name^=bkx-extra-service]:checked");
            var service_extend = parseInt($('.bkx-booking-form .step-1 .bkx-services-extend').val());
            var data = {
                security: bkx_booking_form_admin_params.update_booking_total_nonce,
                seat_id: $('.bkx-booking-form .step-1 .bkx-staff-lists').val(),
                base_id: parseInt($('.bkx-services-lists').val()),
                extra_ids: extra_service,
                service_extend: service_extend
            };
            $('.bkx-booking-form .step-4 .tax-section').hide();
            var booking_form_total = $('.bkx-booking-form .bkx-booking-total span');
            var total_cost = $('.bkx-booking-form .bkx-booking-total .total');
            var tax_name = $('.bkx-booking-form .bkx-booking-total .tax-name span');
            var total_tax = $('.bkx-booking-form .bkx-booking-total .total-tax');
            var grand_total = $('.bkx-booking-form .step-4 .grand-total strong');
            var deposit_note = $('.bkx-booking-form .step-4 .bkx-deposite-note');

            $.ajax({
                type: 'POST',
                url: get_url('update_booking_total'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        var result = $.parseJSON(response);
                        booking_form_total.html(result.total_price_formatted);
                        if (result.tax != 0) {
                            $('.bkx-booking-form .step-4 .tax-section').show();
                            tax_name.html("(" + result.tax.tax_rate + "% " + result.tax.tax_name + " )");
                            total_tax.html(result.total_tax_formatted);
                        }
                        total_cost.html(result.total_price_formatted);
                        grand_total.html(result.grand_total_formatted);
                        deposit_note.html(result.deposit_note);
                    } else {

                    }
                },
                complete: function () {

                }
            });
        },
        get_step_2_details: function () {
            var extra_service = listToArray(".bkx-booking-form .step-1 :input[name^=bkx-extra-service]:checked");
            var data = {
                security: bkx_booking_form_admin_params.get_step_2_details_nonce,
                seat_id: parseInt(booking_form.seat_id),
                base_id: parseInt(booking_form.base_id),
                extra_ids: booking_form.extra_id
            };

            var step_2_details = $('.bkx-booking-form .step-2 .user-detail');

            $.ajax({
                type: 'POST',
                url: get_url('get_step_2_details'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        step_2_details.html(response);
                    } else {

                    }
                },
                complete: function () {

                }
            });
        },
        get_step_3_details: function () {
            var start_time, end_time, booking_time = "";

            if (GetCurrentStep() <= 2 && booking_form.booking_style == 'default') {
                $('.bkx-booking-form .step-2.default').show();
                $('.bkx-booking-form .step-2.day-style').hide();
            }
            //Booking Step 2B Design
            if (GetCurrentStep() <= 2 && booking_form.booking_style == 'day_style') {
                $('.bkx-booking-form .step-2.day-style').show();
                $('.bkx-booking-form .step-2.default').hide();
            }
            var service_extend = parseInt($('.bkx-booking-form .step-1 .bkx-services-extend').val());
            if ($('.booking-slots').data('starting_slot') > 0) {
                var end_slot = $('.booking-slots').data('total_slots') + $('.booking-slots').data('starting_slot');
                start_time = $('.booking-slots').find("a[data-slot='" + $('.booking-slots').data('starting_slot') + "']").data('time');
                end_time = $('.booking-slots').find("a[data-slot='" + end_slot + "']").data('time');
                booking_time = start_time + "|" + end_time;
            }
            var data = {
                security: bkx_booking_form_admin_params.get_step_3_details_nonce,
                seat_id: booking_form.seat_id,
                base_id: booking_form.base_id,
                extra_id: booking_form.extra_id,
                booking_date: booking_form.date,
                booking_time: booking_time,
                service_extend: service_extend,
                time_option: booking_form.time_option,
                booking_multi_days: booking_form.days_selected,
                is_admin: true
            };
            var step_4_details = $('.bkx-booking-form .step-4 .bkx-booking-summary');
            var step_3_details = $('.bkx-booking-form .step-3 .user-detail');
            var step_2_details = $('.bkx-booking-form .step-2 .user-detail');
            $.ajax({
                type: 'POST',
                url: get_url('get_step_3_details'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        step_3_details.html(response);
                        step_2_details.html(response);
                        step_4_details.html(response);
                    } else {

                    }
                },
                complete: function () {

                }
            });
        },
        BookingFormProgressNext: function () {
            var step = parseInt(GetCurrentStep());

            if (CheckIsActiveForm(step) == true) {
                $('.bkx-booking-form .step-' + step).addClass('bkx-form-deactivate').removeClass('bkx-form-active');
                $('.bkx-booking-form .progress-wrapper .progress-step-' + step).removeClass('is-active');
                step = step + 1;
                $('.bkx-booking-form .step-' + step).addClass('bkx-form-active').removeClass('bkx-form-deactivate');
                $('.bkx-booking-form .progress-wrapper .progress-step-' + step).addClass('is-active');
                if (bkx_booking_form_admin_params.booking_id && bkx_booking_form_admin_params.booking_id != 0 && bkx_booking_form_admin_params.first_name) {
                    $("input[name=bkx_first_name]").val(bkx_booking_form_admin_params.first_name);
                    $("input[name=bkx_last_name]").val(bkx_booking_form_admin_params.last_name);
                    $("input[name=bkx_phone_number]").val(bkx_booking_form_admin_params.phone);
                    $("input[name=bkx_email_address]").val(bkx_booking_form_admin_params.email);
                }
                switch (step)     // Passing the variable to switch condition
                {
                    case 2:
                        booking_form.get_step_3_details();
                        break;

                    case 3:
                        booking_form.get_step_3_details();
                        $('.bkx-booking-form .step-2.day-style').hide();
                        $('.bkx-booking-form .step-2.default').hide();
                        break;

                    case 4:
                        booking_form.update_booking_total();
                        booking_form.get_step_3_details();
                        $('.bkx-booking-form .step-2.day-style').hide();
                        $('.bkx-booking-form .step-2.default').hide();
                        //booking_form.payment_method();
                        break;

                    default:
                        break;

                }
            }
        },
        BookingFormProgressPrevious: function () {
            var step = parseInt(GetCurrentStep());
            if (CheckIsActiveForm(step) == true) {
                $('.bkx-booking-form .step-' + step).addClass('bkx-form-deactivate').removeClass('bkx-form-active');
                $('.bkx-booking-form .progress-wrapper .progress-step-' + step).removeClass('is-active');
                step = step - 1;
                $('.bkx-booking-form .step-' + step).addClass('bkx-form-active').removeClass('bkx-form-deactivate');
                $('.bkx-booking-form .progress-wrapper .progress-step-' + step).addClass('is-active');
            }

            switch (step)     // Passing the variable to switch condition
            {
                case 2:
                    booking_form.get_step_3_details();
                    break;
                case 3:
                    booking_form.get_step_3_details();
                    $('.bkx-booking-form .step-2.day-style').hide();
                    $('.bkx-booking-form .step-2.default').hide();
                    break;
                case 4:
                    booking_form.update_booking_total();
                    booking_form.get_step_3_details();
                    booking_form.payment_method();
                    $('.bkx-booking-form .step-2.day-style').hide();
                    $('.bkx-booking-form .step-2.default').hide();
                    break;

                default:
                    break;
            }
        },
        verify_slots: function () {
            var data = {
                security: bkx_booking_form_admin_params.get_verify_slot_nonce,
                seat_id: booking_form.seat_id,
                base_id: booking_form.base_id,
                extra_id: booking_form.extra_id,
                slot: $(this).data('slot'),
                date: $(this).data('date'),
                time: $(this).data('time'),
            };

            $.ajax({
                type: 'POST',
                url: get_url('get_verify_slot'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        var result = $.parseJSON(response);
                        var total_slots = result.no_of_slots;
                        var starting_slot = result.starting_slot;
                        var booking_date = result.booking_date;
                        var end_slot = (starting_slot + total_slots);
                        $('.booking-slots').find('.available').removeClass("selected");
                        $('.booking-slots').data("total_slots", total_slots);
                        $('.booking-slots').data("starting_slot", starting_slot);
                        $('.booking-slots').data("date", booking_date);
                        for (var slot = starting_slot; slot <= end_slot; slot++) {
                            //var slot_elem = $('.booking-slots').find("a[data-slot='" + slot + "']").addClass('selected');
                            if ($('.booking-slots').find("a[data-date='" + booking_date + "']").attr('data-date') == booking_date) {
                                $('.booking-slots').find("a[data-verify='" + booking_date + "-" + slot + "']").addClass('selected');
                            }
                        }
                        booking_form.get_step_3_details();
                    } else {

                    }
                },
                complete: function () {

                }
            });
        },
        BookingFormGetCalendarAvailabilityDayStyle: function (currentSelected, type = "") {
            booking_form.total_slots = 0;
            booking_form.starting_slot = 0;
            var availability_slots = $('.bkx-booking-form .step-2 .schedule-wrap .bkx-available-days');
            $('.booking-slots').data("total_slots", 0);
            $('.booking-slots').data("starting_slot", 0);
            booking_form.date = currentSelected;

            var data = {
                security: bkx_booking_form_admin_params.display_availability_slots_by_dates_nonce,
                seat_id: booking_form.seat_id,
                base_id: booking_form.base_id,
                extra_id: booking_form.extra_id,
                booking_date: currentSelected,
                type: type
            };

            $.ajax({
                type: 'POST',
                url: get_url('availability_slots_by_dates'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        //( type =="" ) ? availability_slots.html( response ) : availability_slots.append( response );
                        availability_slots.html(response)
                    }
                },
                complete: function () {
                    $('.bkx-admin-loading-edit h2').hide();
                    var owl = $('.owl-carousel');
                    owl.owlCarousel('destroy');
                    owl.owlCarousel({
                        loop: false,
                        nav: true,
                        responsive: {
                            0: {
                                items: 1
                            },
                            900: {
                                items: 2
                            },
                            1000: {
                                items: 3
                            }
                        }
                    });

                    $('.schedule-wrap').find('.owl-hidden').removeClass("owl-hidden");
                }
            });
        },
        BookingFormGetCalendarAvailability: function (currentSelected) {
            var data = {
                security: bkx_booking_form_admin_params.get_calendar_availability_nonce,
                seat_id: booking_form.seat_id,
                base_id: booking_form.base_id,
                extra_id: booking_form.extra_id
            };
            var current_day = new Object();
            current_day.currentSelected = currentSelected;
            booking_form.BookingFormGetAvailableSlotByDate(current_day);
            $.ajax({
                type: 'POST',
                url: get_url('get_calendar_availability'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        //console.log(response);
                        var availability = $.parseJSON(response);
                        var staffAvailableCertainMonths = availability.seat.months;
                        var staffAvailableCertainDays = availability.seat.days;

                        var calendar = new CalendarYvv("#bkx-calendar", (bkx_booking_form_admin_params.booking_date) ? bkx_booking_form_admin_params.booking_date : moment().format("Y-M-D"), "Monday");
                        calendar.funcPer = function (ev) {
                            booking_form.BookingFormGetAvailableSlotByDate(ev);
                        };
                        ///calendar.diasResal = [4, 15, 26]; //Selected Days
                        //calendar.staffAvailableCertainMonths    = staffAvailableCertainMonths;
                        calendar.staffAvailableCertainDays = staffAvailableCertainDays;
                        calendar.unavailable_days = availability.unavailable_days;
                        calendar.createCalendar();
                    } else {

                    }
                },
                complete: function () {
                    $('.bkx-admin-loading-edit h2').hide();
                }
            });
        },
        BookingFormGetAvailableSlotByDate: function (calendar, availability_slots = "") {
            booking_form.total_slots = 0;
            booking_form.starting_slot = 0;
            $('.booking-slots').data("total_slots", 0);
            $('.booking-slots').data("starting_slot", 0);
            booking_form.date = calendar.currentSelected;

            var data = {
                security: bkx_booking_form_admin_params.display_availability_slots_nonce,
                seat_id: booking_form.seat_id,
                base_id: booking_form.base_id,
                extra_id: booking_form.extra_id,
                booking_date: calendar.currentSelected,
            };

            if (availability_slots == "") {
                availability_slots = $('.bkx-booking-form .step-2 .select-time .booking-slots');
            }
            availability_slots.html("Please Wait...");
            booking_form.days_selected = "";
            $.ajax({
                type: 'POST',
                url: get_url('display_availability_slots'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        if (IsJsonString(response) == true) {
                            var result = jQuery.parseJSON(response);
                            if (typeof result == 'object') {
                                if (result.data) {
                                    var date_range = result.data;
                                    booking_form.days_selected = date_range;
                                    $.each(date_range, function (index, value) {
                                        $('.calendar-day').find("[data-date='" + value + "']").addClass('day-click-selected');
                                    });
                                } else if (result.error) {
                                    availability_slots.html(result.error);
                                }
                            }
                        } else {
                            $('.bkx-booking-form .step-2 .select-time .booking-slots').html(response);
                        }
                    } else {

                    }
                },
                complete: function () {
                    if (calendar.currentSelected) {
                        booking_form.get_step_3_details();
                    }
                }
            });
        }
    }
    booking_form.init();
});
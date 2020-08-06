jQuery(function ($) {
    // Common scroll to element code.
    $.bkx_scroll_to_notices = function (scrollElement) {
        if (scrollElement.length) {
            $('html, body').animate({
                scrollTop: (scrollElement.offset().top - 100)
            }, 1000);
        }
    };

    var owl = $('.owl-carousel');
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

    const get_url = function (endpoint) {
        return bookingx_params.bkx_ajax_url.toString().replace(
            '%%endpoint%%',
            endpoint
        );
    };

    /**
     * Check if a node is blocked for processing.
     *
     * @param {JQuery Object} $node
     * @return {bool} True if the DOM Element is UI Blocked, false if not.
     */
    const is_blocked = function ($node) {
        return $node.is('.processing') || $node.parents('.processing').length;
    };

    /**
     * Block a node visually for processing.
     *
     * @param {JQuery Object} $node
     */
    const block = function ($node) {
        if (!is_blocked($node)) {
            $node.addClass('processing').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        }
    };

    /**
     * Unblock a node after processing is complete.
     *
     * @param {JQuery Object} $node
     */
    const unblock = function ($node) {
        $node.removeClass('processing').unblock();
    };

    const my_account = {
        init: function () {
            $(document).on('click button', '.booking-x .bkx-change-password-main .bkx-change-password-btn', this.change_password);
            $(document).on('click button', '.booking-x .bkx-customer-details-main .bkx-customer-details-btn', this.customer_details);
            //$(document).on('click', '.booking-x .bkx-dashboard-booking .bkx-booking-cancel-view', this.booking_cancel);
            $(document).on('click button', '.booking-x .bkx-dashboard-booking #bkx-booking-cancel-modal', this.booking_cancel);
        },
        booking_cancel : function (){
            $('#bkx-booking-cancel-view').text('Please wait...');
                block($('.bkx-dashboard-tabContent'));
                $('.bkx-booking-cancelled-success-message').hide();
                $('.bkx-booking-cancelled-success-message').html('');
                var booking_id = $(this).data('booking-id');
                var data = {
                    security: bookingx_params.booking_cancel_nonce,
                    booking_id: booking_id
                }
                $.ajax({
                    type: 'POST',
                    url: get_url('booking_cancel'),
                    data: data,
                    dataType: 'html',
                    success: function (response) {
                        if (response == true) {
                            $('.bkx-booking-cancelled-success-message').show();
                            window.location.reload();
                        }else{
                            $('.bkx-booking-cancelled-error-message').show();
                            $('#bkx-booking-cancel-view').text('Cancel Booking');
                        }
                        unblock($('.bkx-dashboard-tabContent'));
                    },
                    complete: function () {
                        unblock($('.bkx-dashboard-tabContent'));
                    },
                    error: function () {
                        $('#bkx-booking-cancel-view').text('Cancel Booking');
                        unblock($('.bkx-dashboard-tabContent'));
                    }
                });

        },
        change_password: function (e) {
            block($('.bkx-change-password-main'));
            e.preventDefault();
            $('.bkx-change-password-success-message').hide();
            $('.bkx-change-password-success-message').html('');
            var data = {
                security: bookingx_params.change_password_nonce,
                form_data: $("#bkx-change-password").serialize()
            }
            $.ajax({
                type: 'POST',
                url: get_url('change_password'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        var result = $.parseJSON(response);
                        if (result.errors) {
                            $('input').removeClass('is-invalid');
                            $('.invalid-feedback').html('');
                            $.each(result.errors, function (key, val) {
                                $('.bkx-' + key).addClass('is-invalid');
                                $('.' + key + '-feedback').html(val);
                                if (key == 'not_match') {
                                    $('.bkx-new_password').addClass('is-invalid');
                                    $('.bkx-confirm_password').addClass('is-invalid');
                                    $('.new_password-feedback').html(val);
                                    $('.confirm_password-feedback').html(val);
                                }
                                if (key == 'all') {
                                    $('.bkx-new_password').addClass('is-invalid');
                                    $('.bkx-current_password').addClass('is-invalid');
                                    $('.bkx-confirm_password').addClass('is-invalid');
                                }
                            });
                        }
                        unblock($('.bkx-change-password-main'));
                    }
                    if (result.success) {
                        //$('.bkx-change-password-form').hide();
                        $('.bkx-change-password-success-message').show();
                        $('.bkx-change-password-success-message').html(result.success.feedback);
                    }
                },
                complete: function () {
                    unblock($('.bkx-change-password-main'));
                },
                error: function () {
                    unblock($('.bkx-change-password-main'));
                }
            });
        },
        customer_details: function (e) {
            block($('.bkx-customer-details-main'));
            e.preventDefault();
            $('.bkx-customer-details-success-message').hide().html('');
            $('.bkx-customer-details-error-message').hide().html('');
            $('input').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            var data = {
                security: bookingx_params.customer_details_nonce,
                form_data: $("#bkx-customer-details").serialize()
            }
            $.ajax({
                type: 'POST',
                url: get_url('customer_details'),
                data: data,
                dataType: 'html',
                success: function (response) {
                    if (response != 'NORF') {
                        var result = $.parseJSON(response);
                        if (result.errors) {
                            $.each(result.errors, function (key, val) {
                                $('.bkx-' + key).addClass('is-invalid');
                                $('.' + key + '-feedback').html(val);
                                if (key == 'all') {
                                    $('.bkx-first_name').addClass('is-invalid');
                                    $('.bkx-last_name').addClass('is-invalid');
                                    $('.bkx-email_address').addClass('is-invalid');
                                }
                                if (key == 'top_error') {
                                    $('.bkx-customer-details-error-message').show();
                                    $('.bkx-customer-details-error-message').html(val);
                                }
                            });
                        }
                        unblock($('.bkx-customer-details-main'));
                    }
                    if (result.success) {
                        //$('.bkx-change-password-form').hide();
                        $('.bkx-customer-details-success-message').show();
                        $('.bkx-customer-details-success-message').html(result.success.feedback);
                    }
                },
                complete: function () {
                    unblock($('.bkx-customer-details-main'));
                },
                error: function () {
                    unblock($('.bkx-customer-details-main'));
                }
            });
        },
    }

    my_account.init();
});




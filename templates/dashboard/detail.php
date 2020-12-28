<?php
$booking_id = $_REQUEST['view-booking'];
$booking_detail = apply_filters('bkx_booking_detail_load_before', $booking_id );
extract($booking_detail);
extract($booking_detail['booking_info']);
extract($booking_detail['booking_payment']);
extract($booking_detail['booking_business']);
?>
<div class="bkx-dashboard-booking">
    <div class="container">
        <div class="booking-wrapper">
            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 col-12">
                    <div class="white-block">
                        <h2 class="title-block"><?php echo $first_header; ?> </h2>
                        <div class="content-block">
                            <?php echo sprintf( __('<p> <label>Booking ID: </label> #%s','bookingx'), $booking_id );?>
                            <?php echo sprintf( __('<p> <label>Booking Date: </label> %s','bookingx'), $date );?>
                            <?php echo sprintf( __('<p> <label>Service name: </label> %s','bookingx'), $service );?>
                            <?php echo sprintf( __('%s','bookingx'), $extra );?>
                            <?php echo sprintf( __('<p> <label>Staff name: </label> %s','bookingx'), $staff );?>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-8 col-xl-9 col-12">
                    <div class="white-block">
                        <div class="row">
                            <?php if ($is_mobile == 0): ?>
                                <div class="col-8">
                                    <h2 class="title-block"><?php echo sprintf( __('%s','bookingx'), $second_header );?></h2>
                                </div>
                                <div class="col-4">
                                    <h2 class="title-block"><a href="<?php echo get_permalink() ?>"><?php echo esc_html('Back to Bookings', 'bookingx'); ?></a>
                                    </h2>
                                </div>
                            <?php else: ?>
                                <div class="col-12">
                                    <h2 class="title-block"><?php echo $second_header; ?></h2>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="content-block booking-info" style="margin: 0;">
                            <p><label><?php echo esc_html('Booking Date', 'bookingx'); ?> : </label> <?php echo esc_html($date, 'bookingx'); ?>   </p>
	                        <?php if(!empty($timezone_data)){ ?>
		                        <?php echo sprintf( __('<p> <label>Your Time : </label> %s (%s)','bookingx'), $timezone_data['user']['booking_time'] ,$timezone_data['user']['time_zone']);?>
		                        <?php echo sprintf( __('<p> <label>%s Time : </label> %s (%s)','bookingx'), $booking_detail['booking_business']['name'],$timezone_data['system']['booking_time'],$timezone_data['system']['time_zone'] );?>
	                        <?php } ?>
                            <p><label><?php echo esc_html('Booking Duration', 'bookingx'); ?> : </label> <?php echo esc_html($duration); ?></p>
                            <p><label><?php echo esc_html('Booking Total', 'bookingx'); ?> : </label> <?php echo esc_html("{$currency}{$total}"); ?></p>
                            <p><label><?php echo esc_html('Booking Status', 'bookingx'); ?> : </label> <?php echo esc_html($status); ?> <?php
                                if(isset($is_able_cancelled) && $is_able_cancelled == true ){?>
                                    <a data-toggle="modal" data-target="#cancelBookingAlert" href="javascript:void(0);"><?php echo esc_html('Cancel', 'bookingx');?></a>
                                <?php } ?>
                            </p>
                        </div>
                        <?php do_action('bkx_booking_payment_before', $booking_id); ?>
                        <?php if (!empty($booking_detail['booking_payment']) && is_array($booking_detail['booking_payment'])) { ?>
                            <h2 class="title-block"> <?php echo esc_html('Payment Information', 'bookingx'); ?></h2>
                            <div class="content-block booking-info" style="margin: 0;">
                                <p><label><?php echo esc_html('Payment Gateway', 'bookingx'); ?> : </label> <?php echo esc_html($gateway, 'bookingx'); ?>   </p>
                                <?php if (isset($transaction_id) && $transaction_id != "") : ?>
                                    <p><label><?php echo esc_html('Transaction ID', 'bookingx'); ?>: </label> <?php echo esc_html($transaction_id, 'bookingx'); ?>  </p>
                                <?php endif; ?>
                                <p><label><?php echo esc_html('Payment Status', 'bookingx'); ?>: </label> <?php echo esc_html(ucwords($payment_status), 'bookingx'); ?>  </p>
                                <?php echo $message; ?>
                            </div>
                        <?php } ?>
                        <?php do_action('bkx_booking_payment_after', $booking_id); ?>
                        <?php do_action('bkx_booking_business_info_before', $booking_id); ?>
                        <h2 class="title-block"> <?php echo esc_html('Business Information', 'bookingx') ?> </h2>
                        <div class="content-block booking-info" style="margin: 0;">
                            <p>
                                <label> <?php echo esc_html('Business Name :', 'bookingx'); ?> </label> <?php echo esc_html($name); ?>
                            </p>
                            <p>
                                <label> <?php echo esc_html('Phone :','bookingx'); ?></label> <?php echo esc_html($phone); ?>
                            </p>
                            <p>
                                <label> <?php echo esc_html('Email :','bookingx'); ?> </label> <?php echo esc_html($email); ?>
                            </p>
                            <p>
                                <label> <?php echo esc_html('Address :','bookingx'); ?></label> <?php echo esc_html($address); ?>
                            </p>
                        </div>
                        <?php do_action('bkx_booking_business_info_after', $booking_id); ?>
                        <?php if ($is_mobile == 1): ?>
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="title-block"><a href="<?php echo get_permalink() ?>"><?php echo esc_html('Back to Bookings', 'bookingx'); ?></a>
                                    </h2>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if(isset($is_able_cancelled) && $is_able_cancelled == true ){
        $cancel_policy_data = "";
        if(isset($cancel_policy_url) && $cancel_policy_url !=""){
            $cancel_policy_data = __("<a target='_blank' href='{$cancel_policy_url}'>You can review our cancellation policy here.</a>", 'bookingx');
        }
        ?>
        <div class="modal fade" id="cancelBookingAlert" tabindex="-1" role="dialog" aria-labelledby="cancelBookingAlert" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Booking</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo __('Are you sure you want to cancel this booking?', 'bookingx');?> <?php echo $cancel_policy_data;?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-booking-id="<?php echo $booking_id;?>" id="bkx-booking-cancel-modal">Cancel Booking</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

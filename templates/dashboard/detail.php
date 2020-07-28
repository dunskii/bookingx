<?php
$user = get_userdata(get_current_user_id());
$user_id = get_current_user_id();
$is_mobile = 0;
if (wp_is_mobile()) {
    $is_mobile = 1;
}
$is_able_review = false;

$booking_id = $_REQUEST['view-booking'];
$orderObj = new BkxBooking();
try {
    $order_meta = $orderObj->get_order_meta_data($booking_id);
} catch (Exception $e) {
}
$booking_created_by = $order_meta['created_by'];

$order_status = $orderObj->get_order_status($booking_id);


$payment_meta = get_post_meta($booking_id, 'payment_meta', true);
$current_currency = $order_meta['currency'];
$payment_status = isset($payment_meta['payment_status']) && $payment_meta['payment_status'] != "" ? $payment_meta['payment_status'] : "";
$check_total_payment = isset($payment_meta['pay_amt']) && $payment_meta['pay_amt'] != "" ? $payment_meta['pay_amt'] : 0;
$transaction_id = isset($payment_meta['transactionID']) && $payment_meta['transactionID'] != "" ? $payment_meta['transactionID'] : "";
$payment_status = (isset($payment_status)) ? $payment_status : 'Pending';
if (isset($check_total_payment) && $check_total_payment != '' && $check_total_payment != 0) {
    $check_remaining_payment = $order_meta['total_price'] - $check_total_payment;
}

$payment_source_method = $order_meta['bkx_payment_gateway_method'];

list($pending_paypal_message, $payment_source) = getPaymentInfo($payment_source_method, $payment_status);

$booking_date_converted = strtotime(date('Y-m-d', strtotime($order_meta['booking_date']))) . ' ';
$current_date = strtotime(date('Y-m-d'));

if ($booking_date_converted < $current_date || $order_status == 'Completed') {
    $is_able_review = true;
}

$extra_html = getExtraHtml($order_meta);
list($bkx_business_name, $bkx_business_email, $bkx_business_phone, $bkx_business_address) = getBusinessInfo();

$first_header = esc_html("Booking Information", 'bookingx');
$second_header = sprintf(__('Your Booking with %s', 'bookingx'), $bkx_business_name);
if ($is_mobile == 1) {
    $first_header = sprintf(__('Your Booking with %s', 'bookingx'), $bkx_business_name);
    $second_header = esc_html("Booking Information", 'bookingx');
}

$base_id = $order_meta['base_id'];
$BkxBaseObj = new BkxBase('', $base_id);
$base_time = $BkxBaseObj->get_time($base_id);
$base_time_option = get_post_meta($base_id, 'base_time_option', true);
$base_time_option = (isset($base_time_option) && $base_time_option != "") ? $base_time_option : "H";
$date_format = bkx_crud_option_multisite('date_format');

if (isset($base_time_option) && $base_time_option == "H") {
    $total_time = getDateDuration($order_meta);
    $duration = getDuration($order_meta);
    $date_data = sprintf(__('%s', 'bookingx'), date($date_format, strtotime($order_meta['booking_date'])));
} else {
    list($date_data, $duration) = getDayDateDuration($booking_id);
}
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
                            <?php echo sprintf( __('<p> <label>Booking Date: </label> %s','bookingx'), $date_data );?>
                            <?php echo sprintf( __('<p> <label>Service name: </label> %s','bookingx'), $order_meta['base_arr']['main_obj']->post->post_title );?>
                            <?php echo sprintf( __('%s','bookingx'), $extra_html );?>
                            <?php echo sprintf( __('<p> <label>Staff name: </label> %s','bookingx'), $order_meta['seat_arr']['main_obj']->post->post_title );?>
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
                            <p><label><?php echo esc_html('Booking Date', 'bookingx'); ?> : </label> <?php echo esc_html($date_data, 'bookingx'); ?>   </p>
                            <p><label><?php echo esc_html('Booking Duration', 'bookingx'); ?> : </label> <?php echo esc_html($duration); ?></p>
                            <p><label><?php echo esc_html('Booking Total', 'bookingx'); ?> : </label> <?php echo esc_html("{$current_currency}{$order_meta['total_price']}"); ?></p>
                            <p><label><?php echo esc_html('Booking Status', 'bookingx'); ?> : </label> <?php echo esc_html($order_status); ?>   </p>
                        </div>
                        <?php do_action('bkx_booking_payment_before', $booking_id); ?>
                        <?php if (!empty($payment_meta) && is_array($payment_meta)) { ?>
                            <h2 class="title-block"> <?php echo esc_html('Payment Information', 'bookingx'); ?></h2>
                            <div class="content-block booking-info" style="margin: 0;">
                                <p><label><?php echo esc_html('Payment Gateway', 'bookingx'); ?> : </label> <?php echo esc_html($payment_source, 'bookingx'); ?>   </p>
                                <?php if (isset($payment_source) && $payment_source != "Offline Payment") : ?>
                                    <p><label><?php echo esc_html('Transaction ID', 'bookingx'); ?>: </label> <?php echo esc_html($transaction_id, 'bookingx'); ?>  </p>
                                <?php endif; ?>
                                <p><label><?php echo esc_html('Payment Status', 'bookingx'); ?>: </label> <?php echo esc_html(ucwords($payment_status), 'bookingx'); ?>  </p>
                                <?php echo $pending_paypal_message; ?>
                            </div>
                        <?php } ?>
                        <?php do_action('bkx_booking_payment_after', $booking_id); ?>
                        <?php do_action('bkx_booking_business_info_before', $booking_id); ?>
                        <h2 class="title-block"> <?php echo esc_html('Business Information', 'bookingx') ?> </h2>
                        <div class="content-block booking-info" style="margin: 0;">
                            <p>
                                <label> <?php echo esc_html('Business Name :', 'bookingx'); ?> </label> <?php echo esc_html($bkx_business_name); ?>
                            </p>
                            <p>
                                <label> <?php echo esc_html('Phone :','bookingx'); ?></label> <?php echo esc_html($bkx_business_phone); ?>
                            </p>
                            <p>
                                <label> <?php echo esc_html('Email :','bookingx'); ?> </label> <?php echo esc_html($bkx_business_email); ?>
                            </p>
                            <p>
                                <label> <?php echo esc_html('Address :','bookingx'); ?></label> <?php echo esc_html($bkx_business_address); ?>
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
</div>
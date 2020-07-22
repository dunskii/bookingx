<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
global $bkx_seat;
$personal_info = $bkx_seat->seat_personal_info;
$seat_available_months = $bkx_seat->seat_available_months;
$seat_available_days = $bkx_seat->seat_available_days;
$seat_notifiaction_info = $bkx_seat->seat_notifiaction_info;
$seat_payment_info = $bkx_seat->seat_payment_info;
$booking_url = $bkx_seat->booking_url;
$currency_option = (bkx_crud_option_multisite('currency_option') ? bkx_crud_option_multisite('currency_option') : 'AUD');

if (!empty($seat_available_months) || !empty($seat_available_days) || !empty($seat_payment_info)):
    echo sprintf('<h3>%s</h3>', __('Additional Information', 'bookingx')); ?>
    <ul class="additional-info"><?php
        if (!empty($seat_available_days)) {
            $days = '<ul>';
            foreach ($seat_available_days as $day => $timing) {
                $from = $timing['time_from'];
                $to = $timing['time_till'];
                $days .= sprintf(__('<li><span> %s : </span><span> %s  To %s </span></li>', 'bookingx'), ucwords($day), $from, $to);
            }
            $days .= '</ul>';
        }
        $payment_deposite = "";

        if (isset($seat_payment_info['D'])) {
            $payment_deposite = $seat_payment_info['D'];
        }
        if (isset($seat_payment_info['FP'])) {
            $payment_full_pay = $seat_payment_info['FP'];
        }
        if (!empty($payment_deposite)):
            $payment = bkx_get_formatted_price($payment_deposite['amount']);
            $payment_details = isset($payment_deposite['type']) && $payment_deposite['type'] == 'FA' ? $payment  : "{$payment_deposite['percentage']}&#37;";
            $payment_info = sprintf(__(' %s', 'bookingx'), $payment_details);
        endif;
        if (!empty($payment_full_pay)):
            $payment_info = sprintf(__('<span> %s </span>', 'bookingx'), $payment_full_pay['value']);
        endif;
        if (!empty($seat_available_months)):
            echo sprintf( __('<li><div class="months"><label>%s : %s </label> </li>','bookingx'),'Months', implode(", ", $seat_available_months));
        endif;
        if (!empty($seat_available_days)):
            echo sprintf( __('<li><div class="days"><label>%s : %s </label></li>','bookingx'), 'Days of operations',$days);
        endif;
        if (!empty($seat_payment_info)):
            echo sprintf( __('<li><div class="payment-info"><label> %s : &nbsp; %s</label></li>','bookingx'), 'Booking Require Pre Payment', $payment_info);
        endif; ?>
    </ul>
<?php
else :
    echo "";
endif; ?>
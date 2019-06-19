<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $bkx_seat;
    $personal_info = $bkx_seat->seat_personal_info;
    $seat_available_months = $bkx_seat->seat_available_months;
    $seat_available_days = $bkx_seat->seat_available_days;
    $seat_notifiaction_info = $bkx_seat->seat_notifiaction_info;
    $seat_payment_info = $bkx_seat->seat_payment_info;
    $booking_url = $bkx_seat->booking_url;
    if(!empty($seat_available_months) || !empty($seat_available_days) || !empty($seat_payment_info)):
        echo sprintf('<h3>%s</h3>',__('Additional Information : ', 'bookingx')); ?>
    <ul class="additional-info"><?php
        if(!empty($seat_available_days)) {
            $days='<ul>';
            foreach ($seat_available_days as $day=>$timing) {
                $from = $timing['time_from'];
                $to = $timing['time_till'];
                $days .= sprintf( __('<li><span> %s : </span><span> %s  To %s </span></li>','Bookingx'),ucwords($day), $from, $to);
            }$days .='</ul>';
        }
        $payment_deposite = "";

        if(isset($seat_payment_info['D'])){
            $payment_deposite = $seat_payment_info['D'];
        }
        if(isset($seat_payment_info['FP'])){
            $payment_full_pay = $seat_payment_info['FP'];
        }
        if(!empty($payment_deposite)):
            $payment_details  = isset($payment_deposite['type']) && $payment_deposite['type']=='FA' ? '($'.$payment_deposite['amount'].')' : $payment_deposite['percentage'];
            $payment_info  = sprintf( __(' %s%s','Bookingx'), $payment_details, "&#37;");
        endif;
        if(!empty($payment_full_pay)):
            $payment_info  = sprintf( __('<span> %s </span>','Bookingx'), $payment_full_pay['value']);
        endif;
        if(!empty($seat_available_months)):
            echo sprintf('<li><div class="months"><label>%s : %s </label> </li>','Months',implode(", ",$seat_available_months));
        endif;
        if(!empty($seat_available_days)):
            echo sprintf('<li><div class="days"><label>%s : %s </label></li>','<strong>Days of operations</strong>',$days);
        endif;
        if(!empty($seat_payment_info)):
            echo sprintf('<li><div class="payment-info"><label> %s : &nbsp; %s</label></li>','Booking Require Pre Payment',$payment_info);
        endif; ?>
    </ul>
   <?php
    else :
        echo "";
   endif; ?>
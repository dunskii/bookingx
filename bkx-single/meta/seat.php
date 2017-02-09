<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $bkx_seat;
$personal_info = $bkx_seat->seat_personal_info;
$seat_available_months = $bkx_seat->seat_available_months;
$seat_available_days = $bkx_seat->seat_available_days;
$seat_notifiaction_info = $bkx_seat->seat_notifiaction_info;
$seat_payment_info = $bkx_seat->seat_payment_info;
$booking_url = $bkx_seat->booking_url;
if(!empty($seat_available_months) || !empty($seat_available_days) || !empty($seat_payment_info)):
?>
<ul class="additional-info">
<?php
echo sprintf('<li><h3>%s</h3></li>',__('Additional Information : ', 'bookingx'));



if(!empty($seat_available_days))
{
    $days='<ul>';
    foreach ($seat_available_days as $day=>$timing)
    {
        $from = $timing['time_from'];
        $to = $timing['time_till'];
        $days .='<li><span>'.$day.' : </span><span>'.$from.' To '.$to.'</span></li>';
    }
    $days .='</ul>';
}

$payment_deposite = $seat_payment_info['D'];
$payment_full_pay = $seat_payment_info['FP'];

if(!empty($payment_deposite)):
    $payment_details  = isset($payment_deposite['type']) && $payment_deposite['type']=='FA' ? '($'.$payment_deposite['amount'].')' : $payment_deposite['percentage'];
    $payment_info = '<span>'.$payment_deposite['label'].''.$payment_details.'</span>';
endif;
if(!empty($payment_full_pay)):
    $payment_info = '<span>'.$payment_deposite['label'].'</span>';
endif;

//echo sprintf('<li><label>%s Availability : </label></li>',$bkx_seat->alias);

if(!empty($seat_available_months)):
echo sprintf('<li><div class="months"><label>%s :</label><span> %s</span></li>','Months',implode(", ",$seat_available_months));
endif;

if(!empty($seat_available_days)):
echo sprintf('<li><div class="days"><label>%s :</label>%s</li>','Days of operations',$days);
endif;

if(!empty($seat_payment_info)):
echo sprintf('<li><div class="payment-info"><label>%s :</label>%s</li>','Booking Require Pre Payment',$payment_info);
endif;
?>
</ul>
<?php endif;?>
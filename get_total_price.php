<?php
// session_start();
require_once('../../../wp-load.php');

global $wpdb;

$total_price = 0;
$base_price = 0;
$addition_price = 0;
if(isset($_POST['seatid']))
{
    $seat = $_POST['seatid'];
    $objSeat = get_post($seat);
        if(!empty($objSeat) && !is_wp_error($objSeat))
        {
            $SeatMetaObj = get_post_custom( $objSeat->ID );
            if(!empty($SeatMetaObj) && !is_wp_error($SeatMetaObj)){
                 $booking_require_prepayment = isset( $SeatMetaObj['seatIsPrePayment'] ) ? esc_attr( $SeatMetaObj['seatIsPrePayment'][0] ) : "";
                 $deposit_type = isset( $SeatMetaObj['seat_payment_option'] ) ? esc_attr( $SeatMetaObj['seat_payment_option'][0] ) : ""; // Deposite Type
                 $payment_type = isset( $SeatMetaObj['seat_payment_type'] ) ? esc_attr( $SeatMetaObj['seat_payment_type'][0] ) : ""; //Payment Type
                 $fixed_amount = isset( $SeatMetaObj['seat_amount'] ) ? esc_attr( $SeatMetaObj['seat_amount'][0] ) : "";
                 $percentage = isset( $SeatMetaObj['seat_percentage'] ) ? esc_attr( $SeatMetaObj['seat_percentage'][0] ) : "";
             }
        }
}
$total_price = bkx_cal_total_price( $_POST['baseid'], $_POST['extended'] , $_POST['additionid'] );

if($booking_require_prepayment=="Y"){
     
	if($payment_type=="FP")
	{
	   $deposit_price = $total_price;
	}
        
	if($payment_type=="D")
	{
        if($deposit_type=="FA")
        {
                $deposit_price = $fixed_amount;
        }
        else if($deposit_type=="P")
        {
            $deposit_price = ($percentage / 100) * $total_price;
        }
	}
} else{
	$deposit_price = 0;
}

$result['total_tax'] = bkx_cal_total_tax($total_price);
$result['total_price'] = number_format((float)$total_price, 2, '.', '');
$result['seat_is_booking_prepayment'] = $booking_require_prepayment;
$result['deposit_price'] = number_format((float)$deposit_price, 2, '.', '');

$output = json_encode($result);
echo $output;
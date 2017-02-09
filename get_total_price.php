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

if(isset($_POST['baseid']))
{
	    $base = $_POST['baseid'];
        $BaseObj = get_post($base);
        $BkxBase    = new BkxBase($BaseObj);
        if(!empty($BkxBase) && !is_wp_error($BkxBase))
        {
            $base_price = $BkxBase->get_price();
        }
}
if(isset($_POST['extended'])){
	$base_extended = $_POST['extended'];
}


if(isset($base_extended) && ($base_extended!=0))
{
	$base_price = $base_price + ($base_extended*$base_price);
}
 
if(isset($_POST['additionid']) && sizeof($_POST['additionid'])>0)
{        
    $BkxExtraObj  = new BkxExtra();
    $addition_price = $BkxExtraObj->get_total_price_by_ids($_POST['additionid']);
}
 
$total_price = $base_price+$addition_price;

if($booking_require_prepayment=="Y")
{
     
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
	 
$result['total_price'] = number_format((float)$total_price, 2, '.', '');
$result['seat_is_booking_prepayment'] = $booking_require_prepayment;
$result['deposit_price'] = number_format((float)$deposit_price, 2, '.', '');

$output = json_encode($result);
echo $output;
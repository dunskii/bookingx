<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
/**
 *This Function generates list of seats
 *@access public
 *@param 
 *@return array $arr_seat
 */

 /*
 following changes made by krishna
 * removed anchor tag from next and prev button
 * onclick on next/prev button scroll to top of from
 */

/**
 * 
 * @param type $by_base
 * @return type
 */
function get_seat_list($by_base=NULL){
    
        if(isset($by_base) && $by_base!='')
        {
            $base_selected_seats = get_post_meta($by_base,'base_selected_seats',true);  
        }
	//Get Seat post Array
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'bkx_seat',
            'post_status'      => 'publish',
            'include'          => $base_selected_seats,
            'order'			   => 'ASC',
            'orderby'		   => 'title'
        );
        $get_seat_array = get_posts( $args );
        
	    $arr_seat = '';
        if(!empty($get_seat_array) && !is_wp_error($get_seat_array))
        {
           foreach($get_seat_array as $key=>$value)
            {
                $seat_name = $value->post_title;
                $seat_id = $value->ID;
                $arr_seat[$seat_id] = $seat_name;
            } 
        }
	
	return $arr_seat;
}
/**
 *This Function generates list of bases
 *@access public
 *@param 
 *@return array $arr_seat
 */
function get_base_list(){
	//Get Base post Array
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'bkx_base',
            'post_status'      => 'publish',
            'order'			   => 'ASC',
            'orderby'		   => 'title'
        );
        $get_base_array = get_posts( $args );
    
	$arr_base = '';
        if(!empty($get_base_array) && !is_wp_error($get_base_array))
        {
           foreach($get_base_array as $key=>$value)
            {
                $base_name = $value->post_title;
                $base_id = $value->ID;
                $arr_base[$base_id] = $base_name;
            } 
        }
	return $arr_base;
}


/**
 *This Function calculates booking time records 
 *@access public
 *@param $insertedid, $bookingdate, $bookingtime, $bookingduration, $currentdate
 *@return array $bookingTime
 */
function bookingTimeRecord($insertedid,$bookingdate,$bookingtime,$bookingduration,$currentdate)
{
	$bookinghour      = explode(':',$bookingtime);
	$hours_temp       = $bookinghour[0];
	$minutes_temp     = $bookinghour[1];
	//$minutes_temp	  = $bookingduration/60;
	$sec_hours_temp   = $hours_temp*60*60;
	$sec_minutes_temp = $minutes_temp*60;
	$total_sec_passed = $sec_hours_temp + $sec_minutes_temp;
	$remainingtime    = (24*60*60 - $total_sec_passed);
	$counter = '';
	if($minutes_temp=='00')
        {
                $counter = 0;
        }
        else if($minutes_temp=='15')
        {
                $counter = 1;
        }
        else if($minutes_temp=='30')
        {
                $counter = 2;
        }
        else if($minutes_temp=='45')
        {
                $counter = 3;
        }
	$bookingTime = array();
	if($bookingduration <= $remainingtime)
	{
		$passed_full_day = intval($hours_temp)*4+$counter;
		$actual_full_day = '';
		$counter_full_day = $passed_full_day+1;
		for($i =0; $i<=intval($bookingduration) ; $i=$i+(15*60))
		{
			$actual_full_day = $actual_full_day.$counter_full_day.","; 
			$counter_full_day = $counter_full_day + 1;
		}
		$arrDataTimeTemp = array('booking_record_id'=>$insertedid,'booking_date' =>$bookingdate,'booking_time_from'=>$bookingtime,'booking_time_till'=>'','full_day'=>$actual_full_day,'created_date'=> $currentdate);
		array_push($bookingTime, $arrDataTimeTemp);
	}
	else
	{
		//for the current day booking
		$passed_full_day = intval($hours_temp)*4+$counter;
		$actual_full_day = '';
		$counter_full_day = $passed_full_day+1;
		for($i =0; $i<=intval($remainingtime) ; $i=$i+(15*60))
		{
				$actual_full_day = $actual_full_day.$counter_full_day.","; 
				$counter_full_day = $counter_full_day + 1;
		}
		$arrDataTimeTemp = array('booking_record_id'=>$insertedid,'booking_date' =>$bookingdate,'booking_time_from'=>$bookingtime,'booking_time_till'=>'','full_day'=>$actual_full_day,'created_date'=> $currentdate);
		array_push($bookingTime, $arrDataTimeTemp);		
		//now starts with the remaining days booking
		$remainingduration = ($bookingduration - $remainingtime);
		$remainingBookingDuration = $bookingduration - $remainingtime;
		$remainingBookingDurationMinutes = $remainingBookingDuration/60;
		$noOfSlotsRemaining = $remainingBookingDurationMinutes/15;
		
		$totalDays = floor($noOfSlotsRemaining /96); //to find number of days 
		$totalDaysMod = $noOfSlotsRemaining %96; //to find if extend to next day
		if($totalDaysMod > 0) //if goes to next day add 1 to the days value
		{
			$totalDays = $totalDays +1;
		}
		$bookedDate = $bookingdate;
		$nextDate ='';
		for($i=1;$i<=$totalDays;$i=$i+1)
		{
			if($remainingduration>0)
			{
				$nextDate = date('m/d/Y', strtotime("+1 day", strtotime($bookedDate)));
				
				$passed_full_day = 0;
				$counter_full_day = $passed_full_day+1;
				if($remainingduration>(96*15*60))
				{
					$remainingtime_temp = (96*15*60);
				}
				else
				{
					$remainingtime_temp = $remainingduration;
				}
				
				$remainingtime_temp_slot =  floor($remainingtime_temp/(15*60));
				$actual_full_day = "";
				for($j =1; $j<=$remainingtime_temp_slot; $j++)
				{
					$actual_full_day .= $j.","; 					
				}
				//$actual_full_day;
				$arrBookingtimeNext = array('booking_record_id'=>$insertedid,'booking_date' =>$nextDate,'booking_time_from'=>'00:00','booking_time_till'=>'','full_day'=>$actual_full_day,'created_date'=> $currentdate);
				
				array_push($bookingTime, $arrBookingtimeNext);
				$bookedDate = $nextDate;
				$remainingduration = ($remainingduration - 96*15*60);
			}
		}
	}
	return $bookingTime;
}


/**
 *This Function for testing the reuqest post data after form submit
 *@access public
 *@return Void
 */
function submit_form()
{
	echo "<pre>";print_r($_POST);
}
/**
 *This Function calls and processes paypal API
 *@access public
 *@param $seat_name, $total_price, $item_number, $quantity, $arrPaypal
 *@return Void
 */
function do_paypal_api_call($seat_name,$total_price,$item_number,$quantity,$arrPaypal)
{
	
	$PayPalMode 			= $arrPaypal['PayPalMode']; // sandbox or live
	$PayPalApiUsername 		= $arrPaypal['PayPalApiUsername']; //PayPal API Username
	$PayPalApiPassword 		= $arrPaypal['PayPalApiPassword']; //Paypal API password
	$PayPalApiSignature 	= $arrPaypal['PayPalApiSignature']; //Paypal API Signature
	$PayPalCurrencyCode 	= $arrPaypal['PayPalCurrencyCode']; //Paypal Currency Code
	$PayPalReturnURL 		= $arrPaypal['PayPalReturnURL']; //Point to process.php page
	$PayPalCancelURL 		= $arrPaypal['PayPalCancelURL']; //Cancel URL if user clicks cancel
	
	$ItemName = $seat_name; //Item Name
	$ItemPrice = $total_price; //Item Price
	$ItemNumber = $item_number; //Item Number
	$ItemQty = $quantity; // Item Quantity
	 
	$ItemTotalPrice = $ItemPrice;

	//Data to be sent to paypal
	$padata = 	'&CURRENCYCODE='.urlencode($PayPalCurrencyCode).
				'&PAYMENTACTION=Sale'.
				'&ALLOWNOTE=1'.
				'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
				'&PAYMENTREQUEST_0_AMT='.urlencode($ItemTotalPrice).
				'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice). 
				'&L_PAYMENTREQUEST_0_QTY0='. urlencode($ItemQty).
				'&L_PAYMENTREQUEST_0_AMT0='.urlencode($ItemPrice).
				'&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
				'&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($ItemNumber).
				'&AMT='.urlencode($ItemTotalPrice).				
				'&RETURNURL='.urlencode($PayPalReturnURL ).
				'&CANCELURL='.urlencode($PayPalCancelURL);	

		//We need to execute the "SetExpressCheckOut" method to obtain paypal token
		
		$paypal= new MyPayPal();
		$httpParsedResponseAr = $paypal->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
		//Respond according to message we receive from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
					
				// If successful set some session variable we need later when user is redirected back to page from paypal. 
				$_SESSION['itemprice']   =  $ItemPrice;
				$_SESSION['totalamount'] = 	$ItemTotalPrice;
				$_SESSION['itemName']    =  $ItemName;
				$_SESSION['itemNo']      =  $ItemNumber;
				$_SESSION['itemQTY']     =  $ItemQty;
				
				if($PayPalMode=='sandbox'){
					$paypalmode 	=	'.sandbox';
				}else{
					$paypalmode 	=	'';
				}
				//Redirect user to PayPal store with Token received.
				
			 	   $paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
				   //header('Location: '.$paypalurl);
				   echo '<meta http-equiv="refresh" content="0;url='.$paypalurl.'">'; 
			 
		}else{
			//Show error message
			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo '<pre>';
			print_r($httpParsedResponseAr);
			echo '</pre>';
		}
}
/**
 *This Function Send Emails to users aftre booking the seat
 *@access public
 *@param $last_inserted_id
 *@return String $message_body
 */
function do_send_mail($last_inserted_id)
{
	global $wpdb;
	$bookingObj = new BkxBooking();
	$order_meta  = $bookingObj->get_order_meta_data($last_inserted_id);
 
	$BkxBaseObj = $order_meta['base_arr']['main_obj'];
	$BkxSeatObj = $order_meta['seat_arr']['main_obj'];
	$BkxSeatInfo = $BkxSeatObj->seat_personal_info;
 

		// multiple recipients
		$to  = $order_meta['email']; 
		$admin_email = get_settings('admin_email');

		// subject
		$subject = 'Thank you for your booking';
		$email_template = "";

		if(strtolower($order_meta['payment_status'])== "pending" || $order_meta['payment_status'] == "Not Completed")
		{
			$email_template = 'thankyou';
		}
		else if(strtolower($order_meta['payment_status'])=="completed")
		{
			$email_template = 'thankyou';			
		} 


		$objBookingTemplate = $wpdb->get_results("SELECT * FROM `bkx_booking_template` WHERE template_name='".trim($email_template)."'");
		$message_body = "";

		foreach($objBookingTemplate as $template)
		{
			if(sizeof($objBookingTemplate)>0)
			{
				$message_body = $template->template_value;
			}
			else
			{
				$message_body = "Thank you for your booking";
			}
		}
		 
		//change request for template tags calculate booking duration 7/4/2013
		$booking_time = $order_meta['booking_time'];
		//conver second into minutes 
		$booking_time_minutes = $booking_time/60;
		$flag_hours = false;
		if($booking_time_minutes>60)
		{
			$booking_time_hours = $booking_time/(60*60);
			$flag_hours = true;
		}
		$booking_duration ="";
		if($flag_hours==true){ $booking_duration = $booking_time_hours."Hours ";}
		else{ $booking_duration = $booking_time_minutes."Minutes ";}
		
		// end booking duration calculation 7/4/2013
		$addition_list = '-';
		if(isset($order_meta['addition_ids']) && $order_meta['addition_ids']!=''){
			$addition_list = "";
			$BkxExtra  = new BkxExtra();
			$BkxExtraObj = $BkxExtra->get_extra_by_ids(rtrim($order_meta['addition_ids'],","));
			foreach($BkxExtraObj as $addition){
				$addition_list .= $addition->get_title().",";
			}
		}
		 
	 	$payment_data = get_post_meta($order_meta['order_id'],'payment_meta',true);
	 	if(!empty($payment_data)){$transactionID = $payment_data['transactionID'];}else{$transactionID = '-';}
		$message_body = str_replace("[fname]", $order_meta['first_name'], $message_body);
		$message_body = str_replace('[lname]', $order_meta['last_name'], $message_body);
		$message_body = str_replace('[total_price]', "$".$order_meta['total_price'], $message_body);
		$message_body = str_replace('[txn_id]', $transactionID, $message_body);
		$message_body = str_replace('[order_id]', $order_meta['order_id'], $message_body);
		$message_body = str_replace('[total_duration]', $order_meta['total_duration'], $message_body);

        //@Added by kanhaiya to show paid amount and balance amount
        $pay_amt = ($order_meta['pay_amt'] != '')?$order_meta['pay_amt']:0;
        $total_price = ($order_meta['total_price']!='')?$order_meta['total_price']:0;
		$bal = $total_price - $pay_amt;
		$pay_amt = "$".$pay_amt;
		$bal ="$".$bal;
		
		$message_body = str_replace('[total_price]', $total_price, $message_body);
		$message_body = str_replace('[paid_price]', $pay_amt, $message_body);
		$message_body = str_replace('[bal_price]', $bal, $message_body);
		$message_body = str_replace('[siteurl]', site_url(), $message_body);
		 
		//@ruchira adding additional template tags for email template as per chnage request 7/4/2013
		$message_body = str_replace('[seat_name]', $order_meta['seat_arr']['title'], $message_body);
			
		$message_body = str_replace('[base_name]', $order_meta['base_arr']['title'], $message_body);
		$message_body = str_replace('[additions_list]', $addition_list, $message_body);
		$message_body = str_replace('[time_of_booking]', $booking_duration, $message_body);
		$message_body = str_replace('[date_of_booking]', $order_meta['booking_start_date'], $message_body);
		  
		if($BkxSeatInfo['seat_street'])
			$event_address = $BkxSeatInfo['seat_street'].", ";
		if($BkxSeatInfo['seat_city'])
			$event_address .= $BkxSeatInfo['seat_city'].", ";
		if($BkxSeatInfo['seat_state'])
			$event_address .= $BkxSeatInfo['seat_state'].", ";
		if($BkxSeatInfo['seat_postcode'])
			$event_address .= $BkxSeatInfo['seat_postcode'];
		if($BkxSeatInfo['seat_country'])
			$event_address .= $BkxSeatInfo['seat_country'];
		
		$message_body = str_replace('[location_of_booking]', $event_address, $message_body);
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		$headers .= 'To: '.ucfirst($order_meta['first_name']).' <'.$order_meta['email'].'>' . "\r\n";
		$headers .= 'From: Booking Admin <'.$admin_email.'>' . "\r\n";		 
		// Mail it
		wp_mail($to, $subject, $message_body, $headers);
		wp_mail($admin_email, $subject, $message_body, $headers);
 
	 
		return $message_body;
	//}
}


function mail_format_and_send_process( $subject, $data_html, $to_mail, $cc_mail = null, $bcc_mail = null )
{
	if(isset($to_mail))
	{
		$subject 	= $subject;	
		$admin_email = get_option('admin_email');

		    $headers[] = 'Content-Type: text/html; charset=UTF-8';
		    $headers[] = 'From: Booking Admin <'.$admin_email.'>';
		    if($cc_mail!='')
		    {
		    	$headers[] = 'Cc: '.$cc_mail.' <'.$cc_mail.'>';
		    }

		    if($bcc_mail!='')
		    {
		    	$headers[] = 'Bcc: '.$bcc_mail; 
		    }
		     
    		wp_mail( $to_mail, $subject, $data_html, $headers );
	}
			
}
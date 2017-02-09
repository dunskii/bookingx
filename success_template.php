<?php 
/*
 * Template Name: success page
 * Description: A Page Template with a darker design.
 */
//start of token handle and do express checkout api operation call
	//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
	if(isset($_GET["token"]) && isset($_GET["PayerID"]))
	{
		/* Updated By :Madhuri Rokade
		   Reason : To get the success message from the template.
		*/ 
		$objListTemplateSuccess=$wpdb->get_results('SELECT * FROM bkx_booking_template WHERE template_name="successconfirmation"');
		// session_start();
		//we will be using these two variables to execute the "DoExpressCheckoutPayment"
		//Note: we haven't received any payment yet.
		$token    = $_GET["token"];
		$playerid = $_GET["PayerID"];
		
		//get session variables
		$ItemPrice 		= $_SESSION['itemprice'];
		$ItemTotalPrice = $_SESSION['totalamount'];
		$ItemName 		= $_SESSION['itemName'];
		$ItemNumber 	= $_SESSION['itemNo'];
		$ItemQTY 		= $_SESSION['itemQTY'];
		

		/** Commented by Jiten **/
		$query = "SELECT * FROM  `bkx_booking_record` where `booking_record_id`=".$ItemNumber; 
		$objRecord=$wpdb->get_row($query);
		$seatquery = "SELECT * FROM  `bkx_seat` where `seat_id`=".$objRecord->seat_id; 
		$objSeat=$wpdb->get_row($seatquery);
		$start_date = $objRecord->booking_start_date;
		$end_date = $objRecord->booking_end_date;
		//$payment_method=$objRecord->payment_method;
		$dtString1 = str_replace("-", "", $start_date);
		$dtString2 = str_replace(" ", "", $dtString1);
		$dtString3 = str_replace(":", "", $dtString2);
		
		$stringB="T";
		$length=strlen($dtString3);
		$temp1=substr($dtString3,0,$length-6);
		$temp2=substr($dtString3,$length-6,$length);
		$dt1 = $temp1.$stringB.$temp2;   
		

		$fnl_dt= $dt1."Z";			

		$edString1 = str_replace("-", "", $end_date);
		$edString2 = str_replace(" ", "", $edString1);
		$edString3 = str_replace(":", "", $edString2);
		$edstrB="T";
		$length=strlen($edString3);
		$edtemp1=substr($edString3,0,$length-6);
		$edtemp2=substr($edString3,$length-6,$length);
		$ed_dt1 = $edtemp1.$edstrB.$edtemp2;   
		

		$ed_fnl_dt=$ed_dt1."Z";
		
		$padata = 	'&TOKEN='.urlencode($token).
							'&PAYERID='.urlencode($playerid).
							'&PAYMENTACTION='.urlencode("SALE").
							'&AMT='.urlencode($ItemTotalPrice).
							'&CURRENCYCODE='.urlencode($PayPalCurrencyCode);
		
		//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
		$paypal= new MyPayPal();
		$httpParsedResponseAr = $paypal->PPHttpPost('DoExpressCheckoutPayment', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
		//Check if everything went ok..
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
		{
					echo '<h2>Success</h2>';
					echo 'Your Transaction ID :'.urldecode($httpParsedResponseAr["TRANSACTIONID"]);
				
					/*
					//Sometimes Payment are kept pending even when transaction is complete. 
					//May be because of Currency change, or user choose to review each payment etc.
					//hence we need to notify user about it and ask him manually approve the transiction
					*/
					
					if('Completed' == $httpParsedResponseAr["PAYMENTSTATUS"])
					{
						/* Updated By :Madhuri Rokade
		  				   Reason : To get the success message from the template.
						*/ 
						echo '<div style="color:green">Payment Received!'.strip_tags($objListTemplateSuccess[0]->template_value).'</div>';
					}
					elseif('Pending' == $httpParsedResponseAr["PAYMENTSTATUS"])
					{
						echo '<div style="color:red">Transaction Complete, but payment is still pending! You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
					}
				
					$transactionID = urlencode($httpParsedResponseAr["TRANSACTIONID"]);
					$nvpStr = "&TRANSACTIONID=".$transactionID;
					$paypal= new MyPayPal();
					$httpParsedResponseAr = $paypal->PPHttpPost('GetTransactionDetails', $nvpStr, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

					if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
						
						
						//process response of paypal to store in database
						$booking_record_id = $_SESSION["itemNo"];
						$payment_data= json_encode($httpParsedResponseAr);
						$strTableNamePayment = "bkx_payment";
						$arrDataPayment = array('booking_record_id' => $booking_record_id, 'payment_data' => $payment_data);
						$resPayment = $wpdb->insert( $strTableNamePayment, $arrDataPayment );
						$last_inserted_id_payment = $wpdb->insert_id;

						//update payment status info into booking_record table
						$strTableName = "bkx_booking_record";
						$whereBooking = array('booking_record_id' => $booking_record_id );
						$arrTabledataBooking = array('payment_status' => $httpParsedResponseAr["PAYMENTSTATUS"]);
						$wpdb->update( $strTableName, $arrTabledataBooking, $whereBooking);

						//send email that booking confirmed
						$res = do_send_mail($booking_record_id);
						
						echo "<h2>Your booking is successful.</h2><br/>
						<span>You can add this booking to your google calendar <a href='https://www.google.com/calendar/render?action=TEMPLATE&text=".$objSeat->seat_name."&dates=".$fnl_dt."/".$ed_fnl_dt."&details=For+details,+link+here:+".site_url()."&location=".$objSeat->seat_city."&sf=true&output=xml' target='_blank'>
							here
						</a>
						</span>";
					exit;

					} else  {
						echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
						echo '<pre>';
						print_r($httpParsedResponseAr);
						echo '</pre>';

					}
		
		}else{
			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo '<pre>';
			print_r($httpParsedResponseAr);
			echo '</pre>';
		}
	}
?>
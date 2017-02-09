<?php
/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @global type $bid
 * @param type $atts
 * @return string
 * default shortcode : [bookingform]
 * To display form pre set to particular seat e.g. [bookingx seat-id="2"]
 */
function bookingform_shortcode_function($atts)
{ 	
	global $wpdb,$current_user;
	ob_start();
	//session_start();
	require_once(PLUGIN_DIR_PATH.'booking_general_functions.php'); //when needed	

	$get_current_blog_id = get_current_blog_id();
   
    $bookingx_type = get_query_var( 'bookingx_type', 1 );

    $type = isset($bookingx_type) && $bookingx_type!='' ? $bookingx_type : '';
    
    $type_id = get_query_var( 'bookingx_type_id', 1 );

    $type_id= isset($type_id) && $type_id!=1 ? $type_id : $_POST['id'];
    
    if (isset($type) && $type!='' && ($type == 'seat' || $_POST['type']=='bkx_seat')){ $selected_seat = $type_id;}
    else { $selected_seat ='zero';}

    if(isset($type) && $type!='' && $type == 'base' || $_POST['type']=='bkx_base'){ $by_base = $type_id; }

	$option_for_link = crud_option_multisite('my_plugin_page_id');
	$permalink = get_permalink($option_for_link);
	$seat_alias = crud_option_multisite("bkx_alias_seat");
	$base_alias = crud_option_multisite("bkx_alias_base");
	$addition_alias = crud_option_multisite('bkx_alias_addition');
	$_enable_any_seat_id = crud_option_multisite('select_default_seat');
	$enable_any_seat  = crud_option_multisite('enable_any_seat');

	//set paypal configuration here
	$PayPalMode = crud_option_multisite('bkx_api_paypal_paypalmode'); // sandbox or live
	$PayPalApiUsername = crud_option_multisite('bkx_api_paypal_username'); //PayPal API Username
	$PayPalApiPassword = crud_option_multisite('bkx_api_paypal_password'); //Paypal API password
	$PayPalApiSignature = crud_option_multisite('bkx_api_paypal_signature'); //Paypal API Signature

	$PayPalCurrencyCode = 'USD'; //Paypal Currency Code
	$PayPalReturnURL = $permalink; //Point to process.php page
	$PayPalCancelURL = $permalink; //Cancel URL if user clicks cancel

	//store paypal credentials in an array
	$arrPaypal["PayPalMode"] = $PayPalMode;
	$arrPaypal["PayPalApiUsername"] = $PayPalApiUsername;
	$arrPaypal["PayPalApiPassword"] = $PayPalApiPassword;
	$arrPaypal["PayPalApiSignature"] = $PayPalApiSignature;
	$arrPaypal["PayPalCurrencyCode"] = $PayPalCurrencyCode;
	$arrPaypal["PayPalReturnURL"] = $PayPalReturnURL;
	$arrPaypal["PayPalCancelURL"] = $PayPalCancelURL;

            
	$arr_seat = get_seat_list($by_base);        
	$arr_base = get_base_list();
 
	$option = '<option value="">Select a ' . $seat_alias . '</option>';

	/**
	 *    Added By : Divyang Parekh
	 *    Reason For : Any Seat Functionality
	 *    Date : 4-11-2015
	 */
	if ($enable_any_seat == 1) :
		if (isset($_enable_any_seat_id) && $_enable_any_seat_id != ''):
			$option .= '<option value="any">Any ' . $seat_alias . '</option>';
		endif;
	endif;

	/**
	 * Created By : Divyang Parekh
	 * Date : 10-11-2015
	 * Set seat id catch from session when system selected.. 
	 */
	//echo $free_seat_id;
	//die;
	if (isset($_POST['input_seat']) && $_POST['input_seat'] == 'any' && $_SESSION['free_seat_id'] != '' && $enable_any_seat == 1 && $_enable_any_seat_id != ''):
		$_POST['input_seat'] = $_SESSION['free_seat_id'];
	endif;

	if (isset($arr_seat)) {
		foreach ($arr_seat as $key => $temp) {
                    if($selected_seat == $key):
			$option .= "<option value='" . $key . "' selected='selected'>" . $temp . "</option>";
                    else:
                        $option .= "<option value='" . $key . "'>" . $temp . "</option>";
                    endif;
		}
	}
	$curr_date = date("Y-m-d H:i:s");
 
	if (isset($_POST) && $_POST['is_submit_4'] == 1 && sizeof($_POST) > 0 && $_SESSION['dup_order_prevent'] == '') {
		$strTableName = 'bkx_booking_record';
		
		$strAdditionName = '';
		
		if (isset($_POST['addition_name'])) {
			foreach ($_POST['addition_name'] as $key => $value) {
				$strAdditionName .= $value . ",";
			}
		}
		$booking_start_date = date("Y-m-d H:i:s", strtotime($_POST['input_date'] . " " . $_POST['booking_time_from']));
		$booking_end_date = date("Y-m-d H:i:s", strtotime($_POST['input_date'] . " " . $_POST['booking_time_from']) + $_POST['booking_duration_insec']);
		if (!empty($_POST['flag_stop_redirect'])) {
			$payment_method = 'cash';
		} else {
			$payment_method = 'paypal';
		}

		$booking_set_array = array('seat_id' => $_POST['input_seat'], 'base_id' => $_POST['input_base'],'extra_id'=>$strAdditionName);

		$bookingObj = new BkxBooking(); // Create Booking object

		$arrData = array(
				'seat_id' => $_POST['input_seat'],
				'base_id' => $_POST['input_base'],
				'addition_ids' => $strAdditionName,
				'first_name' => $_POST['input_firstname'],
				'last_name' => $_POST['input_lastname'],
				'phone' => $_POST['input_phonenumber'],
				'email' => $_POST['input_email'],
				'street' => $_POST['input_street'],
				'city' => $_POST['input_city'],
				'state' => $_POST['input_state'],
				'postcode' => $_POST['input_postcode'],
				'total_price' => $_POST['tot_price_val'],
				'total_duration' => str_replace('"', "", $_POST['total_duration']),
				'payment_method' => $payment_method,
				'payment_status' => 'Not Completed',
				'created_date' => $curr_date,
				'created_by' => $current_user->ID,
				'booking_date' => $_POST['input_date'],
				'booking_time' => $_POST['booking_duration_insec'],
				'extended_base_time' => $_POST['input_extended_time'],
				'booking_start_date' => $booking_start_date,
				'booking_end_date' => $booking_end_date,
				'addedtocalendar' => 0,
				'booking_time_from' =>  $_POST['booking_time_from'],
				'currency' => get_current_currency()
			);
 
 			$bookingObj->generate_order($arrData);
 			$booking_data_live = $globalS['bkx_booking_data'];

 
			$order_id = $booking_data_live['meta_data']['order_id'];

			//echo "<pre>";
			$BkxSeatObj = new BkxSeat('',$_POST['input_seat']);
			$BkxBaseObj = new BkxBase('',$_POST['input_base']);
			$seat_payment_info = $BkxSeatObj->seat_payment_info;

			$seat_deposite = $seat_payment_info['D'];

			$base_name = $BkxBaseObj->get_title();
			$seat_name = $BkxSeatObj->get_title();

			//echo "<pre>";
			//print_r($BkxBaseObj);
			$seat_personal_info = $BkxSeatObj->seat_personal_info;
			// print_r($BkxBaseObj);
			// print_r($booking_data_live);
			// die;

			if(isset($order_id) && $order_id!='') {				
				$_SESSION['total_duration'] = $_POST['total_duration'];
				$_SESSION['input_date'] = $_POST['input_date'];
				$_SESSION['booking_time_from'] = $_POST['booking_time_from'];
				$_SESSION['tot_price_val'] = $_POST['tot_price_val'];

				if ($PayPalApiUsername != "" && $PayPalApiPassword != "" && $PayPalApiSignature != "") //if paypal credentials are set in the meta value options
				{
					if ($seat_deposite['type'] == 'P') {
						$tot_prc1 = ($seat_deposite['percentage'] / 100) * $_POST['tot_price_val'];
						$tot_prc = round($tot_prc1, 2);
					} elseif ($seat_deposite['type'] == 'FA') {
						$tot_prc = $seat_deposite['amount'];
					} else {
						$tot_prc = $_POST['tot_price_val'];
					}

					if (!$_REQUEST['flag_stop_redirect']) {
						do_paypal_api_call($base_name, $tot_prc, $order_id, $quantity, $arrPaypal);
						echo "<h3>Processing booking, you will be forwarded to PayPal soon....</h3>";
						exit; // To avoid the redirection through 1st form.
					} else {

						$ItemPrice = $_SESSION['itemprice'];
						$ItemTotalPrice = $tot_prc;
						$ItemName = $base_name;
						$ItemNumber = $order_id;
						$ItemQTY = $quantity;
						 
						$start_date = $booking_data_live['booking_start_date'];
						$end_date = $booking_data_live['booking_end_date'];

						$strt_dt = $arrData['booking_start_date'];
						$end_dt = $arrData['booking_end_date'];


						$dtString1 = str_replace("-", "", $strt_dt);
						$dtString2 = str_replace(" ", "", $dtString1);
						$dtString3 = str_replace(":", "", $dtString2);

						$stringB = "T";
						$length = strlen($dtString3);
						$temp1 = substr($dtString3, 0, $length - 6);
						$temp2 = substr($dtString3, $length - 6, $length);
						$dt1 = $temp1 . $stringB . $temp2;


						$fnl_dt = $dt1 . "Z";

						$edString1 = str_replace("-", "", $end_dt);
						$edString2 = str_replace(" ", "", $edString1);
						$edString3 = str_replace(":", "", $edString2);
						$edstrB = "T";
						$length = strlen($edString3);
						$edtemp1 = substr($edString3, 0, $length - 6);
						$edtemp2 = substr($edString3, $length - 6, $length);
						$ed_dt1 = $edtemp1 . $edstrB . $edtemp2;


						$ed_fnl_dt = $ed_dt1 . "Z";

						// Updated By : Madhuri Rokade
						 $BkxBookingUpdateStatus = new BkxBooking('',$order_id);
						 $BkxBookingUpdateStatus->update_status('processing');
						 $BkxBookingObj = $BkxBookingUpdateStatus->get_order_meta_data($order_id);

						// print_r($BkxBookingObj);
						//$arrTabledataBooking = array('payment_status' => 'Completed');
						//$wpdb->update($strTableName, $arrTabledataBooking, $whereBooking);
						//send email that booking confirmed
						$res = do_send_mail($order_id);

						// Added by Kanhaiya

						$base_name = $base_price = $base_minutes = '';
						//echo "<pre>";

						
						if (isset($_POST['input_base'])) {
 
							$base_name = ($base_name != '') ? $base_name : '';
							$base_price = ($BkxBaseObj->get_price() != '') ? get_current_currency() . $BkxBaseObj->get_price() : '';

							$base_minutes = $BkxBaseObj->get_sevice_time_data();
							$base_is_location_fixed  = get_post_meta($_POST['input_base'],'base_is_location_fixed',true);
							$loc_fixed = ($base_is_location_fixed != '') ? $base_is_location_fixed : '';


							if ($loc_fixed == 'N') {

								$loc_add = "&nbsp;  <span id='id_totalling'>" . $BkxBookingObj['first_name'] . " " . $BkxBookingObj['last_name'] . "</span><br/>";
								$loc_add .= "street: <span id='id_totalling'>" . $BkxBookingObj['street'] . " </span><br/>";
								$loc_add .= "City: <span id='id_totalling'>" . $BkxBookingObj['city'] . " </span><br/>";
								$loc_add .= "State: <span id='id_totalling'>" . $BkxBookingObj['state'] . " - " . $BkxBookingObj['postcode'] . " </span>";

							} else {

								$loc_add = "<br/>street: <span id='id_totalling'>" . $seat_personal_info['seat_street'] . " </span><br/>";
								$loc_add .= "City: <span id='id_totalling'>" .$seat_personal_info['seat_city'] . " </span><br/>";
								$loc_add .= "State: <span id='id_totalling'>" .$seat_personal_info['seat_state'] . " - " . $seat_personal_info['seat_zip'] . " </span><br/>";
								$loc_add .= "Country: <span id='id_totalling'>" .$seat_personal_info['seat_country'] . " </span>";
							}
						}

						$sel_add = '';
						if (isset($_POST['addition_name'])) {
							$addition_arr = $_POST['addition_name'];
							$additionstr = implode(",", $addition_arr);

							$BkxExtra  = new BkxExtra();
							$BkxExtraObj = $BkxExtra->get_extra_by_ids(rtrim($additionstr,","));
							$sel_add = "- ";
							if(!empty($BkxExtraObj)){
									foreach($BkxExtraObj as $addition) {
										$sel_add .= $addition->get_title().",";
									}
									$sel_add =rtrim($sel_add,",");	
							}							 
						}

						 
						$base_addc = '' . $base_name . ' - ' . $base_price . ' - ' . $base_minutes . '';
						$tot_price_val = $_POST["tot_price_val"];
						$total_duration = $_POST["total_duration"];
						$bkDet = $seat_name . "\n" . $base_addc . "\n" . $sel_add . "\n" . $tot_price_val;


						$bookingdet = '<div><div class="gform_page_fields">
					<ul class="gform_fields top_label">
					   <li id="field_4_20" class="gfield  gsection">
						 <h2 class="gsection_title">
						   Please find your booking detail.
						 </h2>
						  <br/>
						  <span id="id_selected_seat"> Event : ' . $seat_name . ' - ' . $base_name . ' ' . $sel_add . ' </span>
						  <br>
						  Time:  <span id="id_totalling">' . $_POST["input_date"] . ' ' . $_POST["booking_time_from"] . '</span>
						  <br>Duration - <span id="id_estimated_time">' . $total_duration . ' </span>(estimated)
						  <br/>Total Cost:  <span id="id_totalling">$' . $tot_price_val . '</span>
						  <br/><br/>Address:  <span id="id_totalling"><p>' . $loc_add . '</p></span>
						  <br/>';


						$bookingdet .= '</li></ul></div></div>';

						echo $bookingdet;
						 
						$base_addc = '' . $base_name . '';
						$tot_price_val = "Total Cost: $" . $_POST["tot_price_val"];
						$total_duration = $_POST["total_duration"];
						$bkDet = $seat_name . " - " . $base_addc . " " . $sel_add . " %0a" . $tot_price_val;
						$bkDet = str_replace(' ', '+', $bkDet);

						echo "<span>You can add this booking to your google calendar <a href='https://www.google.com/calendar/render?action=TEMPLATE&text=" . $base_name . "&dates=" . $fnl_dt . "/" . $ed_fnl_dt . "&details=" . $bkDet . "&location=" . $seat_personal_info['seat_street'] . "," . $seat_personal_info['seat_city'] . "," . $seat_personal_info['seat_state'] . "," . $seat_personal_info['seat_country'] . "," . $seat_personal_info['seat_zip'] . "&sf=true&output=xml' target='_blank'>
						here
						</a>
					</span>";
						//unset($_SESSION['dup_order_prevent']);
						exit;
					}
				} else //else direct user to succes and send email for seat confirmation
				{


					$ItemPrice = $_SESSION['itemprice'];
					$ItemTotalPrice = $_SESSION['totalamount'];
					$ItemName = $_SESSION['itemName'];
					$ItemNumber = $_SESSION['itemNo'];
					$ItemQTY = $_SESSION['itemQTY'];				


					/** Commented by Jiten **/
					// $query = "SELECT * FROM  `bkx_booking_record` where `booking_record_id`=" . $ItemNumber;
					// $objRecord = $wpdb->get_row($query);
					// $seatquery = "SELECT * FROM  `bkx_seat` where `seat_id`=" . $objRecord->seat_id;
					// $objSeat = $wpdb->get_row($seatquery);

					$start_date = $booking_data_live['booking_start_date'];
					$end_date = $booking_data_live['booking_end_date'];

					$strt_dt = $arrData['booking_start_date'];
					$end_dt = $arrData['booking_end_date'];


					$dtString1 = str_replace("-", "", $strt_dt);
					$dtString2 = str_replace(" ", "", $dtString1);
					$dtString3 = str_replace(":", "", $dtString2);

					$stringB = "T";
					$length = strlen($dtString3);
					$temp1 = substr($dtString3, 0, $length - 6);
					$temp2 = substr($dtString3, $length - 6, $length);
					$dt1 = $temp1 . $stringB . $temp2;


					$fnl_dt = $dt1 . "Z";

					$edString1 = str_replace("-", "", $end_dt);
					$edString2 = str_replace(" ", "", $edString1);
					$edString3 = str_replace(":", "", $edString2);
					$edstrB = "T";
					$length = strlen($edString3);
					$edtemp1 = substr($edString3, 0, $length - 6);
					$edtemp2 = substr($edString3, $length - 6, $length);
					$ed_dt1 = $edtemp1 . $edstrB . $edtemp2;


					$ed_fnl_dt = $ed_dt1 . "Z";

					$BkxSeatObj = new BkxSeat('',$_POST['input_seat']);
					$BkxBaseObj = new BkxBase('',$_POST['input_base']);
					$seat_payment_info = $BkxSeatObj->seat_payment_info;

					// Updated By : Madhuri Rokade
					$BkxBookingUpdateStatus = new BkxBooking('',$order_id);
					$BkxBookingUpdateStatus->update_status('pending');
					$BkxBookingObj = $BkxBookingUpdateStatus->get_order_meta_data($order_id);

					//send email that booking confirmed
					$res = do_send_mail($order_id);					 
					
					//display message to user that booking is successful and allow them to add booking in google calendar
				echo "<h2>Your booking is successful.</h2><br/>
						<span>You can add this booking to your google calendar <a href='https://www.google.com/calendar/render?action=TEMPLATE&text=" . $base_name . "&dates=" . $fnl_dt . "/" . $ed_fnl_dt . "&details=" . $bkDet . "&location=" . $seat_personal_info['seat_street'] . "," . $seat_personal_info['seat_city'] . "," . $seat_personal_info['seat_state'] . "," . $seat_personal_info['seat_country'] . "," . $seat_personal_info['seat_zip'] . "&sf=true&output=xml' target='_blank'>
						here
						</a>
					</span>";
					//unset($_SESSION['dup_order_prevent']);
					exit;

				}

			} else {
				echo "There was some error";
			}
		//endif; /// Check Duplicate end
	}

		//start of token handle and do express checkout api operation call
		//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
		if (isset($_GET["token"]) && isset($_GET["PayerID"])) {
			/* Updated By :Madhuri Rokade
               Reason : To get the success message from the template.
            */
 
			//we will be using these two variables to execute the "DoExpressCheckoutPayment"
			//Note: we haven't received any payment yet.
			$token = $_GET["token"];
			$playerid = $_GET["PayerID"];

			//get session variables
			$ItemPrice = $_SESSION['itemprice'];
			$ItemTotalPrice = $_SESSION['totalamount'];
			$ItemName = $_SESSION['itemName'];
			$ItemNumber = $order_id =  $_SESSION['itemNo'];
			$ItemQTY = $_SESSION['itemQTY'];


			$start_date = $booking_data_live['booking_start_date'];
			$end_date = $booking_data_live['booking_end_date'];

			$strt_dt = $arrData['booking_start_date'];
			$end_dt = $arrData['booking_end_date'];

			$dtString1 = str_replace("-", "", $start_date);
			$dtString2 = str_replace(" ", "", $dtString1);
			$dtString3 = str_replace(":", "", $dtString2);

			$stringB = "T";
			$length = strlen($dtString3);
			$temp1 = substr($dtString3, 0, $length - 6);
			$temp2 = substr($dtString3, $length - 6, $length);
			$dt1 = $temp1 . $stringB . $temp2;


			$fnl_dt = $dt1 . "Z";

			$edString1 = str_replace("-", "", $end_date);
			$edString2 = str_replace(" ", "", $edString1);
			$edString3 = str_replace(":", "", $edString2);
			$edstrB = "T";
			$length = strlen($edString3);
			$edtemp1 = substr($edString3, 0, $length - 6);
			$edtemp2 = substr($edString3, $length - 6, $length);
			$ed_dt1 = $edtemp1 . $edstrB . $edtemp2;


			$ed_fnl_dt = $ed_dt1 . "Z";

			$padata = '&TOKEN=' . urlencode($token) .
				'&PAYERID=' . urlencode($playerid) .
				'&PAYMENTACTION=' . urlencode("SALE") .
				'&AMT=' . urlencode($ItemTotalPrice) .
				'&CURRENCYCODE=' . urlencode($PayPalCurrencyCode);

			//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
			$paypal = new MyPayPal();
			$httpParsedResponseAr = $paypal->PPHttpPost('DoExpressCheckoutPayment', 
				$padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
			// echo "<pre>ddddddd";print_r($httpParsedResponseAr);
			//Check if everything went ok..

			if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {

				echo '<h2>Success</h2>';				
				echo 'Your Transaction ID :' . urldecode($httpParsedResponseAr["TRANSACTIONID"]);

				/*
                //Sometimes Payment are kept pending even when transaction is complete.
                //May be because of Currency change, or user choose to review each payment etc.
                //hence we need to notify user about it and ask him manually approve the transiction
                */

				//$res = do_send_mail($order_id);		

				if ('Completed' == $httpParsedResponseAr["PAYMENTSTATUS"]) {
					/* Updated By :Madhuri Rokade
                         Reason : To get the success message from the template.
                    */
					echo '<div style="color:green">Payment Received!</div>';
				} elseif ('Pending' == $httpParsedResponseAr["PAYMENTSTATUS"]) {
					echo '<div style="color:red">Transaction Complete, but payment is still pending! You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
				}


				//echo '<br /><b>Stuff to store in database :</b><br /><pre>';

				$transactionID = urlencode($httpParsedResponseAr["TRANSACTIONID"]);
				$nvpStr = "&TRANSACTIONID=" . $transactionID;
				$paypal = new MyPayPal();
				$httpParsedResponseAr = $paypal->PPHttpPost('GetTransactionDetails', $nvpStr, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

				if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {

					//process response of paypal to store in database
					$booking_record_id = $order_id = $_SESSION["itemNo"];

					$totalamount = $_SESSION['totalamount'];
					$payment_data = json_encode($httpParsedResponseAr);
					 					
					$final_payment_meta = array('order_id' => $order_id,
												'token' => $_GET["token"],
												'PayerID' => $_GET["PayerID"],
												'transactionID' => $transactionID,
												'payment_data' => $payment_data,
												'payment_status' => $httpParsedResponseAr["PAYMENTSTATUS"],
												'pay_amt' => $totalamount );

					$BkxBooking = new BkxBooking('',$order_id);
					$BkxBooking->update_payment_meta($final_payment_meta);	
					$BkxBooking->update_status('processing');	
					$BkxBookingObj = $BkxBooking->get_order_meta_data($order_id);

					//send email that booking confirmed
					$res = do_send_mail($order_id);				

					if (isset($_SESSION['baseid'])) {
						$base_id = $_SESSION['baseid'];
						$seat_id = $BkxBookingObj['seat_id'];
						$addition_ids = $BkxBookingObj['addition_ids'];

						$BkxBaseObj = new BkxBase('',$base_id);
						$base_name = $BkxBaseObj->get_title();

						$BkxSeatObj = new BkxSeat('',$seat_id);
						$seat_payment_info = $BkxSeatObj->seat_payment_info;
						$seat_deposite = $seat_payment_info['D'];
						$seat_name = $BkxSeatObj->get_title();

						 
						$base_name = ($base_name != '') ? $base_name : '';
						$base_price = ($BkxBaseObj->get_price() != '') ? get_current_currency() . $BkxBaseObj->get_price() : '';

						$base_minutes = $BkxBaseObj->get_sevice_time_data();
						$base_is_location_fixed  = get_post_meta($base_id,'base_is_location_fixed',true);
						$loc_fixed = ($base_is_location_fixed != '') ? $base_is_location_fixed : '';


						if ($loc_fixed == 'N') {

							$loc_add = "&nbsp;  <span id='id_totalling'>" . $BkxBookingObj['first_name'] . " " . $BkxBookingObj['last_name'] . "</span><br/>";
							$loc_add .= "street: <span id='id_totalling'>" . $BkxBookingObj['street'] . " </span><br/>";
							$loc_add .= "City: <span id='id_totalling'>" . $BkxBookingObj['city'] . " </span><br/>";
							$loc_add .= "State: <span id='id_totalling'>" . $BkxBookingObj['state'] . " - " . $BkxBookingObj['postcode'] . " </span>";

						} else {

							$loc_add = "<br/>street: <span id='id_totalling'>" . $seat_personal_info['seat_street'] . " </span><br/>";
							$loc_add .= "City: <span id='id_totalling'>" .$seat_personal_info['seat_city'] . " </span><br/>";
							$loc_add .= "State: <span id='id_totalling'>" .$seat_personal_info['seat_state'] . " - " . $seat_personal_info['seat_zip'] . " </span><br/>";
							$loc_add .= "Country: <span id='id_totalling'>" .$seat_personal_info['seat_country'] . " </span>";
						}

						$addition_name = explode(",", $addition_ids);								

						$sel_add = '';
						if (isset($addition_name)) {
							$addition_arr = $addition_name;
							$additionstr = implode(",", $addition_arr);

							$BkxExtra  = new BkxExtra();
							$BkxExtraObj = $BkxExtra->get_extra_by_ids(rtrim($additionstr,","));
							$sel_add = "- ";
							if(!empty($BkxExtraObj)){
									foreach($BkxExtraObj as $addition) {
										$sel_add .= $addition->get_title().",";
									}
									$sel_add =rtrim($sel_add,",");	
							}							 
						}

						$base_addc = $_SESSION['itemName'];
						$paidAmt = $_SESSION['totalamount'];
						$tot_price_val = $_SESSION['tot_price_val'];
						$total_duration = $_SESSION['total_duration'];
						$bkDet = $seat_name . "\n" . $base_addc . "\n" . $sel_add . "\n" . $tot_price_val;

						/*
                         * code to display confirmation message
                         *
                         */
						$bookingdet = '<div><div class="gform_page_fields"><ul class="gform_fields top_label"><li id="field_4_20" class="gfield  gsection">
											<h2 class="gsection_title">
											Please find your booking detail.
											</h2><br/>
											<span id="id_selected_seat"> Event : ' . $seat_name . ' - ' . $base_name . ' ' . $sel_add . ' </span>
											<br>
											Time:  <span id="id_totalling">' . $_SESSION['input_date'] . ' ' . $_SESSION['booking_time_from'] . '</span>
											<br>Duration - <span id="id_estimated_time">' . $total_duration . ' </span>(estimated)
											<br/>
											<br/>
											Total Cost:  <span id="id_totalling">$' . $tot_price_val . '</span>
											<br/>
											Paid Amt.:  <span id="id_totalling">$' . $paidAmt . '</span>
                                            <br/>
											Balance Amt.:  <span id="id_totalling">$' . ($tot_price_val - $paidAmt) . '</span>
											<br/><br/>Address:  <span id="id_totalling">' . $loc_add . '</span>
											<br/>';
						$bookingdet .= '</li></ul></div></div>';

						echo $bookingdet;
				
						$base_addc = '' . $base_addc . '';
						$tot_price_val = "Total Cost: $" . $tot_price_val;
						$total_duration = $total_duration;
						$bkDet = $seat_name . " - " . $base_addc . " " . $sel_add . " %0a" . $tot_price_val;
						$bkDet = str_replace(' ', '+', $bkDet);

	          echo "<span>You can add this booking to your google calendar <a href='https://www.google.com/calendar/render?action=TEMPLATE&text=" . $base_name . "&dates=" . $fnl_dt . "/" . $ed_fnl_dt . "&details=" . $bkDet . "&location=" . $seat_personal_info['seat_street'] . "," . $seat_personal_info['seat_city'] . "," . $seat_personal_info['seat_state'] . "," . $seat_personal_info['seat_country'] . "," . $seat_personal_info['seat_zip'] . "&sf=true&output=xml' target='_blank'>here</a></span>";
						 
					}#end if
					exit;

				} else {
					echo '<div style="color:red"><b>GetTransactionDetails failed:</b>' . urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) . '</div>';
					echo '<pre>';
					print_r($httpParsedResponseAr);
					echo '</pre>';

				}

			} else {
				echo '<div style="color:red"><b>Error : </b>' . urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) . '</div>';
				echo '<pre>';
				print_r($httpParsedResponseAr);
				echo '</pre>';
			}
		}
		else{
			//echo "Order already exists..";
		}
	//}

	if($_GET['token']!='' && $_GET['PayerID']=='')
	{
		echo $message_error = "<span style='color: red;margin: 0 0 0 25%;'>Payment and booking was cancelled via PayPal</span>";
	}
	$text_color = crud_option_multisite('bkx_siteclient_css_text_color', "000000");
	$bg_color = crud_option_multisite('bkx_siteclient_css_background_color', "ffffff");
	$border_color = crud_option_multisite('bkx_siteclient_css_border_color', "ffffff");
	$progressbar_color = crud_option_multisite('bkx_siteclient_css_progressbar_color', "");
		if(crud_option_multisite('enable_cancel_booking')==1 && is_user_logged_in()):
			$the_account_page = get_page_by_title("My Account");
			$the_account_page_id= $the_account_page->ID;
			if($the_account_page_id!=''):$the_account_page_link = get_permalink($the_account_page_id);endif;
			$display_booking_link ='<div style="float:right;"><a href="'.$the_account_page_link.'" target="_blank">Booking History</a></div>';
		endif;

	$temp = "<style>.form_container{padding:10px;color:".$text_color.";background-color: ".$bg_color.";border: 2px solid ".$border_color."; } #gf_progressbar_wrapper_4 .ui-widget-header{background:".$progressbar_color." !important;}</style>";
	$temp .= '<div class="form_container" style=""><style>div.error p{margin-bottom:0px !important;}</style>
<style>.clear-multi{clear:both} 
    .ui-progressbar .ui-progressbar-value { background-image: url(images/pbar-ani.gif); }
    #loading {
	    background-color: #000000;
	    display: none;
	    height: 100%;
	    left: 50%;
	    opacity: 0.5;
	    position: absolute;
	    top: 50%;
	    width: 100%;
	}
    </style>
<form method="post" enctype="multipart/form-data" id="gform_4" action="">
                        <div class="gform_heading">
                            <h4>Customer Booking Form</h4>
                        </div>
                        <!-- Updated By :Divyang Parekh
		     				Reason : To set the Booking Link to see Customer Own booking. -->
                        '.$display_booking_link.'

                        <h3 class="gf_progressbar_title">Step 1 of 4
        </h3>
		<!-- Updated By :Madhuri Rokade
		     Reason : To set the pre Payment value for the payment process.
		  --> 
       	<input type="hidden" value="" name="flag_stop_redirect" id="flag_stop_redirect" />
        <input type="hidden" value="'.$selected_seat.'" name="selected_seat" id="id_selected_seat" />
        <div id="gf_progressbar_wrapper_4" class="gf_progressbar_wrapper">
            
            </div><div class="error" style="color:red"></div>
                        <div class="gform_body"><div id="gform_page_4_1" class="gform_page">
                          <div class="gform_page_fields">
                            <ul id="gform_fields_4" class="gform_fields top_label description_below"><li id="field_4_6" class="gfield  gsection"><h4 class="gsection_title">Please select what you would like to book</h4></li><li id="field_4_1" class="gfield"><label class="gfield_label" for="input_4_1">Select a '.$seat_alias.'</label><div class="ginput_container"><input type="hidden" name="seat_name" id="id_seat_name" value=""><select name="input_seat" id="myInputSeat" class="medium gfield_select" tabindex="1">'.$option.'</select><input type="hidden" name="time_availability" id="id_time_availability" value=""><input type="hidden" name="time_availability_certain_from" id="id_time_availability_certain_from" value=""><input type="hidden" name="time_availability_certain_till" id="id_time_availability_certain_till" value=""><input type="hidden" name="days_availability" id="id_days_availability" value=""><input type="hidden" name="days_availability_certain" id="id_days_availability_certain" value=""><input type="hidden" name="months_availability" id="id_months_availability" value=""><input type="hidden" name="months_availability_certain" id="id_months_availability_certain" value=""><input type="hidden" name="total_duration" id="id_total_duration" value="" /><input type="hidden" id="seat_is_booking_prepayment" name="seat_is_booking_prepayment" value="" /><input type="hidden" id="deposit_price" name="deposit_price" value="" /></div><div class="gfield_description">If only one '.$seat_alias.' available dont display</div></li><li id="field_4_4" class="gfield"><label class="gfield_label" for="input_4_4">Select '.$base_alias.'</label><div class="ginput_container"><select name="input_base" id="id_base_selector" class="medium gfield_select" tabindex="2" disabled><option value="">Select a '.$base_alias.'</option></select></div><div class="gfield_description">Only display '.$base_alias.'s of '.$seat_alias.' chosen, display price of '.$base_alias.'</div></li><li id="extended_time" class="gfield" style="display:none;"><label class="gfield_label" for="input_4_9">For how many "'.$base_alias.' times"</label><div class="ginput_container"><input name="input_extended_time" id="id_input_extended_time" type="text" class="medium" tabindex="3"><input type="button" name="clear_extended_time" id="id_clear_extended_time" value="X" ></div><div class="gfield_description">If '.$base_alias.' can be extended display this.Enable customer to book multiple days etc</div></li><li id="field_4_5" class="gfield"><div class="ginput_container"><ul class="addition_checkbox" id="id_addition_checkbox"></ul></div><div class="gfield_description">Only display '.$addition_alias.' available to '.$seat_alias.' and '.$base_alias.' selected, Display price next to '.$addition_alias.'</div></li><li id="field_4_12" class="gfield  gsection"><h4 class="gsection_title" id="total_price_container">The total is - $<span id="total_price"></span></h4><div class="gsection_description">Calculate total as selections are made.</div></li></ul>

							
                    </div>
                    <div class="gform_page_footer">
                        <input type="button" id="gform_next_button_4_7" class="button gform_next_button" value="Next" tabindex="7" onclick="jQuery(\'#gform_target_page_number_4\').val(\'2\');validate_form(1,2);jQuery(\'html,body\').animate({ scrollTop: jQuery(\'#gform_4\').offset().top -50 }, 500);">
                    </div>
                </div>
                <div id="gform_page_4_2" class="gform_page" style="display:none;">
								
                    <div class="gform_page_fields">
                        <ul class="gform_fields top_label"><li id="field_4_8" class="gfield  gsection"><h4 class="gsection_title">Select a date and time for your booking</h4><div class="gsection_description" id="display_total_duration">Depending on '.$seat_alias.', '.$base_alias.' and additions times, use total time to show availability.</div></li><li id="field_4_10" class="gfield">
						<div id="display_calendar_full"></div>
						<label class="gfield_label" for="input_4_10">Choose a date</label><div class="ginput_container"><input type="hidden" name="input_date" value="" id="id_input_date"><div  id="id_datepicker" type="text" value="" class="" tabindex="8"> </div> </div><input type="hidden" id="gforms_calendar_icon_input_4_10" class="gform_hidden" value="http://yfbiz.net.au/wp-content/plugins/gravityforms/images/calendar.png">
<!--
<div class="gfield_description">If '.$base_alias.' can be extended time is set in days/months display second calender for end date.</div>
<div class="ginput_container" id="base_extended_calender"><input name="datepicker_extended" id="id_datepicker_extended" type="text" value="" class="" tabindex="8"> </div>-->
</li><li id="field_4_display_booking" style=""><div id="booking_details">Booking of the day</div><div id="booking_details_value"></div></li><!--<li id="field_4_11" class="gfield"><label class="gfield_label" for="input_4_11">Choose a Time</label>--><div class="clear-multi"><div class="gfield_time_minute ginput_container"><!--<input type="select" maxlength="2" name="input_timevalue" id="input_4_11_2" value="" tabindex="10" readonly="readonly">--><!--<select id="time_options"></select>--></div></div><br/><div id="id_display_success" class="display_success"></div><input type="hidden" value="0" name="can_proceed" id="id_can_proceed"><input type="hidden" name="booking_time_from" id="id_booking_time_from" value=""><input type="hidden" name="booking_duration_insec" id="id_booking_duration_insec" value="0"><!-- </li> --></ul>
						
                    </div>

                    <div class="gform_page_footer">
                        <input type="button" id="gform_previous_button_4_13" class="button gform_previous_button" value="Previous" tabindex="13" onclick="jQuery(\'#gform_target_page_number_4\').val(\'1\');validate_form(2,1);jQuery(\'html,body\').animate({ scrollTop: jQuery(\'#gform_4\').offset().top -50 }, 500);"> 
						<input type="button" id="gform_next_button_4_13" class="button gform_next_button" value="Next" tabindex="12" onclick="jQuery(\'#gform_target_page_number_4\').val(\'3\');validate_form(2,3);jQuery(\'html,body\').animate({ scrollTop: jQuery(\'#gform_4\').offset().top -50 }, 500); ">
                    </div>
                </div>';

				
				global $bid;
				

                $temp.= '<div id="gform_page_4_3" class="gform_page" style="display:none;">

					<span id="mobile_only" style="display:none; font-size:18px; font-weight:bold;" >The service you have chosen is mobile, would you like us to come to you?<br>
						<label class="gfield_label" for="mobile_yes">YES</label>
						<input id="mobile_yes" type="radio" value="YES" name="mobile_only_choice"><br>
						<label class="gfield_label" for="mobile_no">NO</label>
						<input id="mobile_no" type="radio" value="NO" name="mobile_only_choice">
					</span>

                    <div class="gform_page_fields">
                        <ul class="gform_fields top_label" id="4_form_user_details">
						
						<div id="user_details">
							<li id="field_4_14" class="gfield  gsection"><h2 class="gsection_title">Add your details</h2>';/*<div class="gsection_description">If user logged in, prefill details</div>*/

							$temp .= '</li><li id="field_4_15" class="gfield"><label class="gfield_label" for="input_4_15_3">Name</label><div class="ginput_complex ginput_container" id="input_4_15"><span id="input_4_15_3_container" class="ginput_left">
							<label for="input_4_15_3">First: </label>
							<input type="text" name="input_firstname" id="id_firstname" value="'.$current_user->user_firstname.'" tabindex="14"></span><span id="input_4_15_6_container" class="ginput_right">
							
							<label for="input_4_15_6">Last: </label>
							<input type="text" name="input_lastname" id="id_lastname" value="'.$current_user->user_lastname.'" tabindex="15"></span></div></li><li id="field_4_16" class="gfield"><label class="gfield_label" for="input_4_16">Phone</label><div class="ginput_container"><input name="input_phonenumber" id="id_phonenumber" type="text" value="" class="medium" tabindex="16"></div></li><li id="field_4_17" class="gfield"><label class="gfield_label" for="input_4_17">Email</label><div class="ginput_container"><input name="input_email" id="id_email" type="text" value="" class="medium" tabindex="17"></div></li>
						
						</div>

						<li id="field_4_18" class="gfield">
							<label class="gfield_label" for="input_4_18_1">Address</label><div class="ginput_complex ginput_container" id="input_4_18"><span class="ginput_full" id="input_4_18_1_container"><input type="text" name="input_street" id="id_street" value="" tabindex="18"><label for="input_4_18_1" id="input_4_18_1_label">Street</label></span><br><span class="ginput_left" id="input_4_18_3_container"><input type="text" name="input_city" id="id_city" value="" tabindex="19"><label for="input_4_18_3" id="input_4_18.3_label">City</label></span><br><span class="ginput_right" id="input_4_18_4_container"><input type="text" name="input_state" id="id_state" value="" tabindex="21"><label for="input_4_18_4" id="input_4_18_4_label">State / Province / Region</label></span><br><span class="ginput_left" id="input_4_18_5_container"><input type="text" name="input_postcode" id="id_postcode" value="" tabindex="22"><label for="input_4_18_5" id="input_4_18_5_label">Zip / Postal Code</label></span><input type="hidden" class="gform_hidden" name="input_18.6" id="input_4_18_6" value=""></div>
						</li>';
						//<div class="gfield_description">If '.$base_alias.' or '.$addition_alias.' is set to Mobile display address</div>
						$temp .= '</ul>
                    </div>
                    <div class="gform_page_footer" id="gform_page_footer_details">
                        <input type="button" id="gform_previous_button_4_19" class="button gform_previous_button" value="Previous" tabindex="24" onclick="jQuery(\'#gform_target_page_number_4\').val(\'2\');validate_form(3,2);jQuery(\'html,body\').animate({ scrollTop: jQuery(\'#gform_4\').offset().top -50}, 500);"> <input type="button" id="gform_next_button_4_19" class="button gform_next_button" value="Next" tabindex="23" onclick="jQuery(\'#gform_target_page_number_4\').val(\'4\');validate_form(3,4);jQuery(\'html,body\').animate({ scrollTop: jQuery(\'#gform_4\').offset().top -50}, 500);">
                    </div>
                </div>
                <div id="gform_page_4_4" class="gform_page" style="display:none;">
                    <div class="gform_page_fields">
                        <ul class="gform_fields top_label">
						<li id="field_4_20" class="gfield  gsection"><h2 class="gsection_title">Please check your booking before continuing</h2></li>
						<li id="field_4_21" class="gfield gfield_html gfield_html_formatted gfield_no_follows_desc">You are about to book the following on the <span id="id_selected_date">dd/mm/yyyy</span> at <span id="id_selected_time">hh:mm</span><br><br><span id="id_selected_seat">'.$seat_alias.' 1</span><br><span id="id_selected_base" name="base_alias">'.$base_alias.' 2</span><br><span id="id_selected_addition">Additions 3 and 7</span><br><br><br>Totalling - <span id="id_totalling">$****</span><br>Duration - <span id="id_estimated_time">3 hours </span>(estimated)<br><br><br><span id="id_deposit_val">Please Note a deposit of *** is required to process and confirm this booking.</span></li>
                            </ul></div>
        <div class="gform_page_footer top_label">
		<input type="button" id="gform_previous_button_4" class="button gform_previous_button" value="Previous" tabindex="25" onclick="validate_form(4,3);jQuery(\'html,body\').animate({ scrollTop: jQuery(\'#gform_4\').offset().top -50}, 500);">';
						
        $temp.='<input type="submit" id="gform_submit_button_4" class="button gform_button" value="Submit">';
							

		$temp.='<input type="hidden" name="gform_ajax" value="form_id=4&amp;title=1&amp;description=1">
            <input type="hidden" class="gform_hidden" name="is_submit_4" value="1">
            <input type="hidden" class="gform_hidden" name="gform_submit" value="4">
            <input type="hidden" class="gform_hidden" name="gform_unique_id" value="50c98331d2052">
            <input type="hidden" name="bxbooking_form_is_ok" id="id_bxbooking_form_is_ok">
            <input type="hidden" class="gform_hidden" name="tot_price_val" id="hidden_total_price" value="">
            <input type="hidden" class="gform_hidden" name="gform_target_page_number_4" id="gform_target_page_number_4" value="2">
            <input type="hidden" class="gform_hidden" name="gform_source_page_number_4" id="gform_source_page_number_4" value="1">
            <input type="hidden" name="gform_field_values" value="">
            
        </div>
    </div></div>
 </form></div>';
	//echo  $temp;
        return $temp;
}
add_shortcode('bookingform', 'bookingform_shortcode_function');

/**
 *This Function call for teh successform callback after booking done
 *@access public
 *@return String $temp
 */
function successform_shortcode_function()
{
	$temp = "Success";
	$r = new PayPal();
	
	$final = $r->doPayment();

	if ($final['ACK'] == 'Success') {
		echo 'Succeed!';
		print_r($final);
	} else {
		print_r($final);
	}

	die();
	return $temp;
}
add_shortcode('successform', 'successform_shortcode_function');



add_shortcode('bookingx', 'process_bookingx');
/**
 * This shortcode will perform diffrent types of process like 
 * [bookingx seat-id = 'all']
 * [bookingx seat-id="75" description="yes" image="yes" extra-info="no"]
 * [bookingx base-id = 'all]
 * [bookingx base-id="100,110" description="yes" image="yes" extra-info="no"]
 * @param type $attr
 */
function process_bookingx($attr)
{ 
    if(!empty($attr))
    {
        foreach ($attr as $type=>$value)
        {
            $type =strtolower($type);
            
            switch ($type) {
                case 'seat-id':
                        generate_seat_data($attr);
                    break;
                 case 'base-id':
                        generate_base_data($attr);
                    break;
                 case 'extra-id':
                        generate_addition_data($attr);
                    break;
                
                default:
                
                    break;
            }
        }
    }
    else
    {
        $attr = array('seat-id'=>'all');
        generate_seat_data($attr);
    }    
}

function generate_seat_data($data){
                
    $post_type ='bkx_seat';
    $url_arg_name = 'seat';
    $alias = get_option('bkx_alias_seat', "default");
    
    $seat_id = $data['seat-id'];
    $is_description = isset( $data['description'] ) ? esc_attr( $data['description'] ) : "yes";
    $is_image = isset( $data['image'] ) ? esc_attr( $data['image'] ) : "yes";
    $is_extra_info = isset( $data['extra-info'] ) ? esc_attr( $data['extra-info'] ) : "no";
    $per_page    = isset( $data['per-page'] ) ? esc_attr( $data['per-page'] ) : -1;

    if(isset($seat_id) && $seat_id!='' && $seat_id!='all'){$in_array = explode(',',$seat_id);}
    require_once PLUGIN_DIR_PATH.'templates/archive.php';  
}

function generate_base_data($data){
    
    $post_type ='bkx_base';
    $url_arg_name = 'base';
    $alias = get_option('bkx_alias_base', "default");
  
    $base_id = $data['base-id'];
    $is_description = isset( $data['description'] ) ? esc_attr( $data['description'] ) : "yes";
    $is_image = isset( $data['image'] ) ? esc_attr( $data['image'] ) : "yes";
    $is_extra_info = isset( $data['extra-info'] ) ? esc_attr( $data['extra-info'] ) : "no";
    $per_page    = isset( $data['per-page'] ) ? esc_attr( $data['per-page'] ) : -1;
    
    if(isset($base_id) && $base_id!='' && $base_id!='all'){$in_array = explode(',',$base_id);}
    require_once PLUGIN_DIR_PATH.'templates/archive.php'; 
}

function generate_addition_data($data){
    
    $post_type ='bkx_addition';
    $url_arg_name = 'extra';
    $alias = get_option('bkx_alias_addition', "default");
    
    $extra_id = $data['extra-id'];
    $is_description = isset( $data['description'] ) ? esc_attr( $data['description'] ) : "yes";
    $is_image = isset( $data['image'] ) ? esc_attr( $data['image'] ) : "yes";
    $is_extra_info = isset( $data['extra-info'] ) ? esc_attr( $data['extra-info'] ) : "no";
    $per_page    = isset( $data['per-page'] ) ? esc_attr( $data['per-page'] ) : -1;
    
    if(isset($extra_id) && $extra_id!='' && $extra_id!='all'){$in_array = explode(',',$extra_id);}
    require_once PLUGIN_DIR_PATH.'templates/archive.php'; 
}

function get_custom_post_type_template($single_template) {
     global $post;
     if ($post->post_type == 'bkx_seat' || 'bkx_base' || 'bkx_addition') {
          $single_template = PLUGIN_DIR_PATH . 'templates/single.php';
     }
     return $single_template;
}
add_filter( 'single_template', 'get_custom_post_type_template' );
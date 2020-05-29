<?php
    $user = get_userdata( get_current_user_id() );
    $user_id = get_current_user_id();
    $is_mobile = 0;
    if ( wp_is_mobile() ) {
        $is_mobile = 1;
    }
    $is_able_review = false;

    $booking_id     = $_REQUEST['view-booking'];
    $orderObj       = new BkxBooking();
    $order_meta     = $orderObj->get_order_meta_data( $booking_id );
    $booking_created_by = $order_meta['created_by'];

    $order_status   = $orderObj->get_order_status( $booking_id );


    $payment_meta = get_post_meta( $booking_id,'payment_meta',true);
    $current_currency       = $order_meta['currency'];
    $payment_status         = isset($payment_meta['payment_status']) && $payment_meta['payment_status'] != "" ? $payment_meta['payment_status'] : "";
    $check_total_payment    = isset($payment_meta['pay_amt']) && $payment_meta['pay_amt'] != "" ? $payment_meta['pay_amt'] : 0;
    $transaction_id         = isset($payment_meta['transactionID']) && $payment_meta['transactionID'] != "" ? $payment_meta['transactionID'] : "";
    $payment_status = (isset($payment_status)) ? $payment_status : 'Pending';
    if(isset($check_total_payment) && $check_total_payment !='' && $check_total_payment != 0){
        $check_remaining_payment = $order_meta['total_price'] - $check_total_payment;
    }

    $payment_source_method = $order_meta['bkx_payment_gateway_method'];
    $pending_paypal_message = "";
    if(isset($payment_source_method) && $payment_source_method == "bkx_gateway_paypal_express"  && $payment_status == "Pending"){
        $pending_paypal_message = "<p> <label>Note : </label> Transaction Incomplete and payment is still in pending status! You need to manually authorize this payment in your Paypal Account </p>";
    }

    if(!empty($payment_source_method)){
        $BkxPaymentCore = new BkxPaymentCore();
        $bkx_get_available_gateways = $BkxPaymentCore->bkx_get_available_gateways();
        if(isset($payment_source_method) && $payment_source_method == 'cash'){
            $payment_source_name = "Offline Payment";
        }else{
            $payment_source_name = $bkx_get_available_gateways[$payment_source_method];
        }

        $payment_source = sprintf(__("%s",'Bookingx'),$payment_source_name);
    }else{
        $payment_source = sprintf(__("%s",'Bookingx'),"Offline Payment");
    }

    $booking_date_converted = strtotime(date('Y-m-d', strtotime( $order_meta['booking_date'] ) ) ).' ';
    $current_date = strtotime(date('Y-m-d'));

    if( $booking_date_converted < $current_date || $order_status == 'Completed') {
        $is_able_review = true;
    }
    $extra_html = "";
    if(!empty($order_meta['addition_ids']) && !empty($order_meta['extra_arr'])){
        $extra_html = sprintf("<p><label>Extra Services : </label>", 'bookingx');
        $extra_data = "";
        foreach ($order_meta['extra_arr'] as $extra_obj ){
            $main_obj = $extra_obj['main_obj']->post;
            $extra_title = $main_obj->post_title;
            $extra_data .= " {$extra_title} ".",";
        }
        $extra_data = rtrim($extra_data,",");
        $extra_html .= $extra_data."</p>";
    }

    $bkx_business_name = bkx_crud_option_multisite('bkx_business_name');
    $bkx_business_email = bkx_crud_option_multisite('bkx_business_email');
    $bkx_business_phone = bkx_crud_option_multisite('bkx_business_phone');
    $bkx_business_address_1 = bkx_crud_option_multisite('bkx_business_address_1');
    $bkx_business_address_2 = bkx_crud_option_multisite('bkx_business_address_2');
    $bkx_business_address = sprintf( __( '%s %s', 'bookingx' ), $bkx_business_address_1, $bkx_business_address_2);
    $first_header = esc_html("Booking Information", 'bookingx');
    $second_header = sprintf( __( 'Your Booking with %s', 'bookingx' ), $bkx_business_name);
    if($is_mobile == 1 ){
        $first_header = sprintf( __( 'Your Booking with %s', 'bookingx' ), $bkx_business_name);
        $second_header = esc_html("Booking Information", 'bookingx');
    }

    $base_id = $order_meta['base_id'];
    $BkxBaseObj = new BkxBase('',$base_id);
    $base_time = $BkxBaseObj->get_time( $base_id );
    $base_time_option   = get_post_meta( $base_id, 'base_time_option', true );
    $base_time_option   = ( isset($base_time_option) && $base_time_option != "" )  ?  $base_time_option : "H";
    $date_format = bkx_crud_option_multisite( 'date_format' );
    if( isset( $base_time_option ) && $base_time_option == "H"){
        $total_time =  sprintf( __( '%s', 'bookingx' ), date( 'h:i A', strtotime( $order_meta[ 'booking_start_date' ] ) ), date( 'h:i A ', strtotime( $order_meta[ 'booking_end_date' ] ) ) );
        //$duration   = sprintf( __( '%s', 'bookingx' ), $order_meta['total_duration'] );
        $booking_duration = str_replace('(', '', $order_meta['total_duration'] );
        $booking_duration = str_replace(')', '', $booking_duration );
        $duration   = sprintf( __( '%s', 'bookingx' ), $booking_duration );
        $date_data = sprintf( __( '%s', 'bookingx' ), date( $date_format, strtotime( $order_meta[ 'booking_date' ] ) ) );
    }else{
        $days_selected           = get_post_meta( $booking_id, 'booking_multi_days', true );
        if(!empty($days_selected)){
            $last_key           = sizeof($days_selected) - 1;
            $start_date         = date('F d, Y',strtotime($days_selected[0]));
            $end_date           = date('F d, Y',strtotime($days_selected[$last_key]));
            $date_data          =  "{$start_date} To {$end_date}";
            $booking_duration       = ( sizeof($days_selected) > 1 ? sizeof($days_selected)." Days" : sizeof($days_selected)." Day");
            //$duration   = sprintf( __( '%s', 'bookingx' ), $base_time['formatted'] );
            $duration   = sprintf( __( '%s', 'bookingx' ), $booking_duration );
        }
    }
    ?>
    <div class="bkx-dashboard-booking">
        <div class="container">
            <div class="booking-wrapper">
                <div class="row">
                    <div class="col-md-5 col-lg-4 col-xl-3 col-12">
                        <div class="white-block">
                            <h1 class="title-block"><?php echo $first_header;?> </h1>
                            <div class="content-block" style="font-weight: bold;">
                                <?php echo '<p> <label>Booking ID: </label> #'.$booking_id.'</p>
                                      <p> <label>Booking Date : </label>'.$date_data.'</p>
                                      <p> <label>Service name : </label>'.$order_meta['base_arr']['main_obj']->post->post_title.'</p>
                                      '.$extra_html.'
                                      <p> <label>Staff name : </label>'.$order_meta['seat_arr']['main_obj']->post->post_title.'</p>';?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-lg-8 col-xl-9 col-12">
                        <div class="white-block">
                            <div class="row">
                                <?php if($is_mobile == 0 ): ?>
                                    <div class="col-8">
                                        <h2 class="title-block"><?php echo $second_header;?></h2>
                                    </div>
                                    <div class="col-4">
                                        <h2 class="title-block"><a href="<?php echo get_permalink()?>">Back to Bookings</a></h2>
                                    </div>
                                <?php else: ?>
                                    <div class="col-12">
                                        <h2 class="title-block"><?php echo $second_header;?></h2>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="content-block booking-info" style="margin: 0;">
                                <p> <label>Booking Date :   </label> <?php echo esc_html($date_data, 'bookingx');?>   </p>
                                <p> <label>Booking Duration :   </label> <?php echo esc_html($duration);?></p>
                                <p> <label>Booking Total :   </label> <?php echo esc_html("{$current_currency}{$order_meta['total_price']}");?>   </p>
                                <p> <label>Booking Status :   </label> <?php echo esc_html($order_status);?>   </p>
                            </div>
                            <?php do_action('bkx_booking_payment_before', $booking_id);?>
                            <?php if(!empty($payment_meta) && is_array($payment_meta)) { ?>
                                <h2 class="title-block"> Payment Information</h2>
                                <div class="content-block booking-info" style="margin: 0;">
                                    <p> <label>Payment Gateway :   </label> <?php echo esc_html($payment_source, 'bookingx');?>   </p>
                                    <?php if( isset($payment_source) && $payment_source != "Offline Payment" ) : ?>
                                        <p> <label>Transaction ID : </label> <?php echo esc_html($transaction_id, 'bookingx');?>  </p>
                                    <?php endif; ?>
                                    <p> <label>Payment Status : </label> <?php echo esc_html(ucwords($payment_status), 'bookingx') ;?>  </p>
                                    <?php echo $pending_paypal_message;?>
                                </div>
                            <?php } ?>
                            <?php do_action('bkx_booking_payment_before', $booking_id);?>

                            <h2 class="title-block"> Business Information </h2>
                            <div class="content-block booking-info" style="margin: 0;">
                                <p> <label> <?php echo esc_html('Business Name :');?> </label> <?php echo esc_html($bkx_business_name);?>   </p>
                                <p> <label> <?php echo esc_html('Phone :');?></label> <?php echo esc_html($bkx_business_phone);?></p>
                                <p> <label> <?php echo esc_html('Email :');?> </label> <?php echo esc_html($bkx_business_email);?></p>
                                <p> <label> <?php echo esc_html('Address :');?></label> <?php echo esc_html($bkx_business_address);?>   </p>
                            </div>

                            <?php if($is_mobile == 1 ): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <h2 class="title-block"><a href="<?php echo get_permalink()?>">Back to Bookings</a></h2>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
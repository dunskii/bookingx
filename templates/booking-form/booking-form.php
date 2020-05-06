<?php
/**
 * Booking Form Page
 */
defined( 'ABSPATH' ) || exit;
$order_id = ( isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : 0 );
$status = ( (isset($order_id) && $order_id != "") ? true : false ) ;
?>
<div class="booking-x">
    <div class="main-container">
        <div class="booking-x container">
            <div class="bkx-booking-form">
                <!--  Start Progress Bar     -->
                <?php bkx_get_template('booking-form/progress-bar/progress-bar.php');?>
                <!--  End Progress Bar     -->
                    <?php if(isset($status) && $status == false ): ?>
                        <div class="bookingx-error-group"></div>
                        <form method="post" name="bkx-booking-generate" id="bkx-booking-generate" class="bkx-booking-generate">
                        <!-- Start Initialized Steps -->
                        <?php bkx_get_template('booking-form/steps/step-1.php');?>
                        <?php bkx_get_template('booking-form/steps/step-2.php');?>
                        <?php bkx_get_template('booking-form/steps/step-2b.php');?>
                        <?php bkx_get_template('booking-form/steps/step-3.php');?>
                        <?php bkx_get_template('booking-form/steps/step-4.php');?>
                        </form>
                    <?php endif;?>
                    <?php if(isset($_POST['order_id']) && isset($_POST['bkx_payment_gateway_method']) && !empty($_POST['order_id'])){
                        $args = array( 'order_id' => $_POST['order_id'], 'bkx_payment_gateway_method' => $_POST['bkx_payment_gateway_method']);
                        do_action('bkx_payment_gateway_process_hook', $args );
                    }
                    ?>
                    <?php if( isset($status) && $status == true && isset($_GET['order_id']) && $_GET['order_id'] != "" ) {
                        $order_id = base64_decode($_GET['order_id']);
                        $action_data = array('order_id' => $order_id);
                        $payment_method = get_post_meta( $order_id, 'payment_method', true );
                        if(!isset($payment_method)  || $payment_method != 'bkx_gateway_pay_later'){
                            $check_payment_data = get_post_meta( $order_id, 'payment_meta', true );
                            $transactionID = 0;
                            if(!empty($check_payment_data)){
                                $transactionID = $check_payment_data['transactionID'];
                            }
                            if( $transactionID == 0 ){
                                do_action('bkx_payment_gateway_capture_process_hook', $action_data);
                            }
                        }
                       // echo '<pre>',print_r(get_post_meta($order_id),1),'</pre>';
                        $capture_payment = get_post_meta($order_id, 'bkx_capture_payment', true);
                        if (!empty($capture_payment) && $capture_payment['success'] == true) {
                            $BkxPaymentCore = new BkxPaymentCore();
                            echo $BkxPaymentCore->bkx_success_payment($capture_payment);
                        }
                    }?>
                <!-- End Initialized Steps -->
            </div>
        </div>
    </div>
</div>
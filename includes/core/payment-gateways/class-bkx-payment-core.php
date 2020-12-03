<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Payment Core Library for Payment procedure and pass to Booking
 *
 */
class BkxPaymentCore
{

    public $payment_gateways;

    public $order_id;

    public function __construct($order_id = null)
    {

        if (isset($_GET['order_id']) && $_GET['order_id'] != "") {
            $order_id = base64_decode($_GET['order_id']);
        }

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        if (is_multisite()):
            restore_current_blog();
        endif;

        $this->order_id = $order_id;
        add_action('bkx_payment_gateway_process_hook', array($this, 'bkx_paypal_payment_gateway_action'), 10);
        add_action('bkx_payment_gateway_capture_process_hook', array($this, 'bkx_paypal_payment_gateway_capture_action'), 10);
     }

    /**
     * @param $args
     */
    public function bkx_paypal_payment_gateway_action($args)
    {
        $order_id = $args['order_id'];
        $this->order_id = $args['order_id'];
        $booking_meta = array('order_id' => $order_id);
        $process_response = array('success' => true, 'data' => $booking_meta);
        if ($order_id != "" && isset($_POST['bkx_payment_gateway_method']) && $_POST['bkx_payment_gateway_method'] == 'bkx_gateway_paypal_express'
            && (!isset($_GET['token']) || $_GET['token'] == '')) {
            $BkxPaymentPayPalExpress = new BkxPaymentPayPalExpress($this->order_id);
            $payment_request_call = $BkxPaymentPayPalExpress->payment_request_call($this->order_id, 'SetExpressCheckout');
            if (!empty($payment_request_call) && !isset($payment_request_call['error'])) {
                $method = $payment_request_call['method'];
                if ($method == 'SetExpressCheckout' && "SUCCESS" == strtoupper($payment_request_call["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($payment_request_call["ACK"])) {
                    echo __("<h3>Please wait Booking Processing, you will be forwarded to PayPal soon....</h3>", 'bookingx');
                    $BkxPaymentPayPalExpress->ready_for_redirect_to_paypal($order_id, $payment_request_call);
                    die();
                } else {
                    $error = $payment_request_call['L_LONGMESSAGE0'];
                    update_post_meta($order_id, 'bkx_payment_error_log', $payment_request_call);
                    update_post_meta($order_id, 'bkx_payment_message', $error);
                    update_post_meta($order_id, 'bkx_capture_payment', $process_response);
                    $bkx_return_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
                    $bkx_return_url = apply_filters('bkx_return_url', $bkx_return_url);
                    $bkx_return_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_return_url);
                    echo __('<h3>Please wait redirecting soon..</h3>', 'bookingx');
                    echo '<meta http-equiv="refresh" content="0;url=' . $bkx_return_url . '">';
                    die();
                }
            }
        } else if ($order_id != "" && !isset($_POST['bkx_payment_gateway_method'])) {
            update_post_meta($order_id, 'bkx_capture_payment', $process_response);
            $bkx_return_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
            $bkx_return_url = apply_filters('bkx_return_url', $bkx_return_url);
            $bkx_return_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_return_url);
            echo '<meta http-equiv="refresh" content="0;url=' . $bkx_return_url . '">';
        } else {
            update_post_meta($order_id, 'bkx_capture_payment', $process_response);
            $bkx_return_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
            $bkx_return_url = apply_filters('bkx_return_url', $bkx_return_url);
            $bkx_return_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_return_url);
            echo '<meta http-equiv="refresh" content="0;url=' . $bkx_return_url . '">';
        }
    }

    public function bkx_paypal_payment_gateway_capture_action()
    {
        if (isset($_GET['order_id']) && $_GET['order_id'] != "") {
            $order_id = base64_decode($_GET['order_id']);
        }
        $capture_payment = get_post_meta($order_id, 'bkx_capture_payment', true);
        if (!empty($capture_payment))
            return;

        $i18n = bkx_localize_string_text();
        //Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
        if (isset($_GET["token"]) && isset($_GET["PayerID"])) {
            $token = sanitize_text_field($_GET["token"]);
            $payerid = sanitize_text_field($_GET["PayerID"]);
            $args["TOKEN"] = urlencode($token);
            $args["PayerID"] = urlencode($payerid);
            $order_id = $this->order_id;
            $BkxPaymentPayPalExpress = new BkxPaymentPayPalExpress($order_id);
            $capture_payment = $BkxPaymentPayPalExpress->capture_payment($order_id, $args);
            update_post_meta($order_id, 'bkx_capture_payment', $capture_payment);
        } else {
            $booking_meta = array('order_id' => $order_id);
            $process_response = array('success' => true, 'data' => $booking_meta);
            update_post_meta($order_id, 'bkx_payment_error_log', 'PayerID Blank');
            update_post_meta($order_id, 'bkx_payment_message', $i18n['paypal_error']);
            update_post_meta($order_id, 'bkx_capture_payment', $process_response);
            $bkx_return_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
            $bkx_return_url = apply_filters('bkx_return_url', $bkx_return_url);
            $bkx_return_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_return_url);
            echo __('<h3>Please wait redirecting soon..</h3>', 'bookingx');
            echo '<meta http-equiv="refresh" content="0;url=' . $bkx_return_url . '">';
            die();
        }

    }

    /**
     * @return mixed
     */
    public function is_activate_payment_gateway(){
        return is_array($this->PaymentGateways()) ? $this->PaymentGateways() : array();
    }

    /**
     * @return array[]
     */
    public function default_payment_gateway(){
        $check_status = bkx_crud_option_multisite('bkx_gateway_paypal_express_status');
        $load_gateways = array();
        if(isset($check_status) && $check_status == 1){
            $load_gateways = array('bkx_gateway_paypal_express' => array( 'title' => 'Paypal', 'class' => new BkxPaymentPayPalExpress() ));
        }
        return apply_filters('bkx_payment_gateways',$load_gateways );

    }

    /**
     * @return mixed
     */
    public function PaymentGateways()
    {
        $load_gateways = $this->default_payment_gateway();

        foreach ($load_gateways as $key_name => $gateway) {
            $this->payment_gateways[$key_name] = $gateway;
        }

        return $this->payment_gateways;
    }

    /**
     * @param array $post
     * @return mixed|void
     */
    public function bkx_get_available_gateways($post = array()){

        if (empty($post))
            return;

        $prepayment = false;
        if (isset($post['seat_id']) && $post['seat_id'] > 0) {
            $seat_id = sanitize_text_field(wp_unslash($_POST['seat_id']));
            $seatIsPrePayment = get_post_meta($seat_id, 'seatIsPrePayment', true);
            if (isset($seatIsPrePayment) && esc_attr($seatIsPrePayment) == 'Y') {
                $prepayment = true;
            } else {
                $prepayment = false;
            }
        }
        if ($prepayment == false)
            return;

        $load_gateways = $this->default_payment_gateway();
        foreach ($load_gateways as $key_name => $gateway) {
            $this->payment_gateways[$key_name] = $gateway;
        }

        $_available_gateways = array();
        if (!empty($this->payment_gateways)) {
            foreach ($this->payment_gateways as $key_name => $gateway) {
                $check_status = bkx_crud_option_multisite($key_name . '_status');
                if (isset($check_status) && $check_status == 1) {
                    $_available_gateways[$key_name] = $gateway;
                }
            }
        }
        $this->payment_gateways = $_available_gateways;
        return apply_filters('bkx_available_payment_gateways', $_available_gateways);
    }

    public function bkx_get_available_gateways_html($prepayment = false)
    {
        if (!empty($this->payment_gateways)) {
            if ($prepayment == false) {
                return;
            }
            $count = !empty($this->payment_gateways) ? sizeof($this->payment_gateways) : 0;
            $checked = ($count == 1) ? 'checked = checked' : '';
            $_available_gateways_html = "<div class=\"row bkx-gateways\"><div class=\"col-lg-8\"><div class=\"check-text\">Payment Method </div>";
            foreach ($this->payment_gateways as $key_name => $gateway) {
                $check_status = bkx_crud_option_multisite($key_name . '_status');
                $gateway_name = $gateway['title'];
                if (isset($check_status) && $check_status == 1 || $key_name == 'bkx_gateway_pay_later') {
                    $_available_gateways_html .= "<div class=\"row mt-2\"><div class=\"col-md-6\"><div class=\"custom-control custom-radio\">
                                                  <input type=\"radio\" {$checked} class=\"custom-control-input\" id=\"{$key_name}\" name=\"bkx_payment_gateway_method\" value=\"{$key_name}\" data-payment-method=\"{$key_name}\">
                                                  <label class=\"custom-control-label bkx-gateway-label\" for=\"{$key_name}\">{$gateway_name}</label>
                                                </div></div></div>";
                }
            }
            $_available_gateways_html .= "</div></div>";
        }
        return apply_filters('bkx_get_available_gateways_html', $_available_gateways_html, $prepayment);
    }

    /**
     * @param $booking_data
     * @return mixed|void
     * @throws Exception
     */
    public function bkx_success_payment($booking_data)
    {
        $payment_success_html = "";
	    $order_id = "";
        if ($booking_data['success'] == true || $booking_data['success'] == 1) {
            $seat_alias = bkx_crud_option_multisite("bkx_alias_seat");
            $base_alias = bkx_crud_option_multisite("bkx_alias_base");
            $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');

            $current_currency = bkx_get_current_currency();
            $booking_order_data = $booking_data['data'];
            $transaction_id = isset($booking_order_data['transactionID']) ? $booking_order_data['transactionID'] : "";
            $payment_status = isset($booking_order_data['payment_status']) ? $booking_order_data['payment_status'] : "";
            $pay_amt = isset($booking_order_data['pay_amt']) ? $booking_order_data['pay_amt'] : 0;
            $order_id = $booking_order_data['order_id'];
            if (empty($order_id)) {
                return;
            }
            $BkxBookingObj = new BkxBooking();
            $booking_meta_data = $BkxBookingObj->get_order_meta_data($order_id);
            $base_id = $booking_meta_data['base_id'];
            $seat_id = $booking_meta_data['seat_id'];
            $addition_ids = rtrim($booking_meta_data['addition_ids'], ",");

            $BkxBaseObj = new BkxBase('', $base_id);
            $base_name = $BkxBaseObj->get_title();
            $base_time = $BkxBaseObj->get_time($base_id);
            $BkxSeatObj = new BkxSeat('', $seat_id);
            $seat_address = $BkxSeatObj->seat_personal_info;
            $business_address['street'] = bkx_crud_option_multisite("bkx_business_address_1");
            $business_address['city'] = bkx_crud_option_multisite("bkx_business_city");
            $business_address['state'] = bkx_crud_option_multisite("bkx_business_state");
            $business_address['postcode'] = bkx_crud_option_multisite("bkx_business_zip");
            $business_address['country'] = bkx_crud_option_multisite("bkx_business_country");
            //$seat_address       = array('street' => $booking_meta_data['street'],'city' => $booking_meta_data['city'], 'state' => $booking_meta_data['state'], 'postcode' => $booking_meta_data['postcode'], 'country' => $booking_meta_data['seat_country']);
            $seat_name = $BkxSeatObj->get_title();
            $extra_data = "";
            $remaining_cost = ($booking_meta_data['total_price'] - $pay_amt);
            if (!empty($addition_ids)) {
                $BkxExtra = new BkxExtra();
                $BkxExtraObj = $BkxExtra->get_extra_by_ids($addition_ids);
                if (!empty($BkxExtraObj)) {
                    foreach ($BkxExtraObj as $addition) {
                        $extra_temp = $addition->get_title();
                        $extra_temp = str_replace('(', '', $extra_temp);
                        $extra_temp = str_replace(')', '', $extra_temp);
                        $extra_data .= $extra_temp . ",";
                    }
                    $extra_data = rtrim($extra_data, ",");
                }
            }
            $payment_success_html = sprintf(__('<h2>Congratulations! %s, Your booking has been confirmed!</h2>','bookingx'),$booking_meta_data['first_name']);
            if (strtolower($payment_status) == "completed") {
                 $payment_success_html = sprintf(__('<h2>Congratulations! %s, Your booking has been confirmed!</h2>','bookingx'),$booking_meta_data['first_name']);
            } elseif ('Pending' == $payment_status) {

                $payment_success_html = sprintf(__('<h2>Dear %s, Transaction Complete, but payment is still pending! You need to manually authorize this payment in your <a target="_new" href="%s">PayPal Account</a></h2>','bookingx'),$booking_meta_data['first_name'], esc_url('http://www.paypal.com') );
            }
            $payment_success_html .= "<div class='bkx-booking-success'><div class='bkx-booking-detail user-detail'>";
            if (isset($order_id)) {
                $base_name = str_replace('(', '', $base_name);
                $base_name = str_replace(')', '', $base_name);
                $date_format = "";
                $end_time = date('H:i', strtotime($booking_meta_data['booking_end_date']));
                $base_time_option = get_post_meta($order_id, 'base_time_option', true);
                if (isset($base_time_option) && $base_time_option == "D") {
                    list($date_format, $booking_duration) = getDayDateDuration($order_id);
                } else {
                    $date_format = date('F d, Y', strtotime($booking_meta_data['booking_date']));
                    $booking_duration = str_replace('(', '', $booking_meta_data['total_duration']);
                    $booking_duration = str_replace(')', '', $booking_duration);
                }
                $payment_success_html .= __('<div class="row"><div class="col-sm-12"><dl> <dt"> Details of your booking are as follow : </dt></dl></div></div>','bookingx');
                $payment_success_html .= "<div class=\"row\"><div class=\"col-sm-6\">";
                $payment_success_html .= "<dl> <dt> {$seat_alias}</dt> :  <dd>{$seat_name}</dd></dl>";
                $payment_success_html .= "<dl> <dt> {$base_alias} </dt> : <dd>{$base_name}</dd></dl>";
                if (isset($extra_data) && $extra_data != "") {
                    $payment_success_html .= "<dl> <dt> {$addition_alias} </dt> :  <dd>{$extra_data}</dd></dl>";
                }
                $payment_success_html .= "</div>";

                $payment_success_html .= "<div class=\"col-sm-6\">";
                $payment_success_html .= "<dl> <dt> Date </dt> : <dd>  {$date_format} </dd></dl>";
                if (isset($base_time_option) && $base_time_option == "H") {
                    $payment_success_html .= __("<dl> <dt> Time </dt> : <dd>  {$booking_meta_data['booking_time_from']} - {$end_time} </dd></dl>", 'bookingx');
                }
                $payment_success_html .= __("<dl> <dt> Duration </dt> :  <dd> {$booking_duration}  </dd></dl>", 'bookingx');
                $payment_success_html .= "</div></div>";

                $payment_success_html .= "<div class=\"row\"><div class=\"col-sm-6\">";
                $payment_success_html .= __("<dl> <dt> Total Cost </dt>: <dd> {$current_currency}{$booking_meta_data['total_price']}</dd></dl>", 'bookingx');
                $payment_success_html .= __("<dl> <dt> Remaining Cost </dt>: <dd> {$current_currency}{$remaining_cost}</dd></dl>", 'bookingx');
                if (isset($payment_status) && $payment_status != "") {
                    $payment_success_html .= __("<dl> <dt> Payment Status</dt>: <dd> {$payment_status}</dd></dl>", 'bookingx');
                }
                $payment_success_html .= "</div>";
                $payment_success_html .= "<div class=\"col-sm-6\">";
                $payment_success_html .= __("<dl> <dt> Total Paid</dt>: <dd>  {$current_currency}{$pay_amt}</dd></dl>", 'bookingx');
                if (isset($transaction_id) && $transaction_id != "") {
                    $payment_success_html .= __("<dl><dt>Transaction ID</dt>: <dd>  {$transaction_id}</dd></dl>", 'bookingx');
                }
                $payment_success_html .= "</div></div>";
                if($BkxSeatObj->seat_address()){
	                $payment_success_html .= "<div class=\"row\"><div class=\"col-sm-12 mt-3\">";
	                $payment_success_html .= __("{$BkxSeatObj->seat_address_html(false)}", 'bookingx');
	                $payment_success_html .= "</div></div>";
                }
	            $payment_success_html .= apply_filters('bkx_booking_thank_you_content', '', $order_id );

            }
            if (is_multisite()):
                $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
                switch_to_blog($blog_id);
            endif;
            $get_return_url = get_post_meta($order_id, 'bkx_return_page_url', true);
            if (is_multisite()):
                restore_current_blog();
            endif;
            $bkx_booking_page_return = apply_filters('bkx_booking_page_return', $get_return_url);
            $back_to_booking_text = apply_filters('bkx_back_booking_text', "Back to Booking Page");
            $payment_success_html .= "</div></div><div>  </div>";
            $payment_success_html .= "<div class='bkx-email-sent'></div>";
            $payment_success_html .= "<div class=\"button-wrapper\"><button type=\"submit\" class=\"btn btn-default bkx-send-email-receipt\" data-order-id='" . $order_id . "'>Send Email Receipt </button>
                                                                    <button type=\"submit\" class=\"btn btn-default bkx-back-to-booking\">{$back_to_booking_text}</button></div>";
        }
        return apply_filters('bkx_success_payment', $payment_success_html, $order_id);
    }
}

new BkxPaymentCore();
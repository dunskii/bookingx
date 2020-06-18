<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
class BkxPaymentPayPalExpress
{
    public $order_id;

    public function __construct($order_id = null)
    {

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        if (isset($_GET['order_id']) && $_GET['order_id'] != "") {
            $order_id = base64_decode($_GET['order_id']);
        }
        //set paypal configuration here
        $api_mode = bkx_crud_option_multisite('bkx_api_paypal_paypalmode'); // sandbox or live
        $api_username = bkx_crud_option_multisite('bkx_api_paypal_username'); //PayPal API Username
        $api_password = bkx_crud_option_multisite('bkx_api_paypal_password'); //Paypal API password
        $api_signature = bkx_crud_option_multisite('bkx_api_paypal_signature'); //Paypal API Signature
        $currency_option = bkx_crud_option_multisite('currency_option');

        $bkx_paypal_return_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
        $bkx_paypal_cancel_url = get_permalink(bkx_crud_option_multisite('bkx_plugin_page_id'));
        $currency_option = apply_filters('bkx_currency_option', $currency_option);
        $bkx_paypal_return_url = apply_filters('bkx_paypal_return_url', $bkx_paypal_return_url);
        $bkx_paypal_cancel_url = apply_filters('bkx_paypal_cancel_url', $bkx_paypal_cancel_url);
        $bkx_paypal_return_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_paypal_return_url);
        $bkx_paypal_cancel_url = add_query_arg(array('order_id' => base64_encode($order_id)), $bkx_paypal_cancel_url);

        if (is_multisite()):
            restore_current_blog();
        endif;

        $this->api_username = $api_username;
        $this->api_password = $api_password;
        $this->api_mode = $api_mode;
        $this->api_signature = $api_signature;
        $this->api_endpoint = $this->get_api_endpoint();
        $this->version = urlencode('76.0');
        $this->setMethod = 'SetExpressCheckout';
        $this->doMethod = 'DoExpressCheckoutPayment';
        $this->getTransaction = 'GetTransactionDetails';
        $this->currency = $currency_option;
        $this->return_url = $bkx_paypal_return_url;
        $this->cancel_url = $bkx_paypal_cancel_url;
        $this->order_id = $order_id;
        $this->image_url = "https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png";
    }

    public function get_transaction_url($token)
    {

        if ($this->api_mode == 'sandbox') {
            $view_transaction_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $token . '';
        } else {
            $view_transaction_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $token . '';
        }
        return $view_transaction_url;
    }

    public function get_api_endpoint()
    {
        if ($this->api_mode == 'sandbox') {
            $api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
        } else {
            $api_endpoint = 'https://api-3t.paypal.com/nvp';
        }
        return $api_endpoint;
    }

    public function paypal_request_args($method = 'SetExpressCheckout', $args = null)
    {
        $order_id = $this->order_id;
        $seat_id = get_post_meta($order_id, 'seat_id', true);
        $base_id = get_post_meta($order_id, 'base_id', true);
        $extra_id = get_post_meta($order_id, 'addition_ids', true);
        $service_extend = get_post_meta($order_id, 'extended_base_time', true);

        $args['seat_id'] = $seat_id;
        $args['base_id'] = $base_id;
        $args['extra_ids'] = $extra_id;
        $args['service_extend'] = $service_extend;

        $BkxBooking = new BkxBooking();
        $generate_total = $BkxBooking->booking_form_generate_total($args);

        if (!empty($generate_total)) {
            $order_total = (!empty($generate_total['deposit_price']) && $generate_total['deposit_price'] > 0 ? $generate_total['deposit_price'] : $generate_total['total_price']);
        }
        $BkxBaseObj = new BkxBase('', $base_id);
        $service_name = $BkxBaseObj->get_title();
        if ($method == $this->setMethod) {
            $process_args = '&CURRENCYCODE=' . urlencode($this->currency) .
                '&PAYMENTACTION=SALE' .
                '&ALLOWNOTE=1' .
                '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($this->currency) .
                '&PAYMENTREQUEST_0_AMT=' . urlencode($order_total) .
                '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode($order_total) .
                '&L_PAYMENTREQUEST_0_QTY0=' . urlencode(1) .
                '&L_PAYMENTREQUEST_0_AMT0=' . urlencode($order_total) .
                '&L_PAYMENTREQUEST_0_NAME0=' . urlencode($service_name) .
                '&L_PAYMENTREQUEST_0_NUMBER0=' . urlencode($base_id) .
                '&AMT=' . urlencode($order_total) .
                '&RETURNURL=' . urlencode($this->return_url) .
                '&CANCELURL=' . urlencode($this->cancel_url);
        } elseif ($method == $this->doMethod) {
            $get_token = sanitize_text_field($_GET["token"]);
            $get_payer_id = sanitize_text_field($_GET["PayerID"]);

            $process_args = '&TOKEN=' . urlencode($get_token) .
                '&PAYERID=' . urlencode($get_payer_id) .
                '&PAYMENTACTION=SALE' .
                '&AMT=' . urlencode($order_total) .
                '&CURRENCYCODE=' . urlencode($this->currency);

        } elseif ($method == $this->getTransaction) {
            $payment_meta = get_post_meta($this->order_id, $this->doMethod . '_response', true);
            $transaction_id = urldecode($payment_meta["TRANSACTIONID"]);
            $process_args = "&TRANSACTIONID=" . $transaction_id;
        }
        $paypal_req = "METHOD={$method}&VERSION={$this->version}&PWD={$this->api_password}&USER={$this->api_username}&SIGNATURE={$this->api_signature}$process_args";
        $received_values = array_filter(explode('&', $paypal_req));
        $generate_paypal_req = array();
        if (!empty($received_values)) {
            foreach ($received_values as $string_data) {
                $pay_arg = explode("=", $string_data);
                if ($pay_arg[0] == 'RETURNURL' || $pay_arg[0] == 'CANCELURL') {
                    $generate_paypal_req[$pay_arg[0]] = urldecode($pay_arg[1]);
                } else {
                    $generate_paypal_req[$pay_arg[0]] = $pay_arg[1];
                }
            }
        }
        return $generate_paypal_req;
    }

    public function payment_request_call($order_id, $method = null)
    {
        $paypal_request_args = $this->paypal_request_args($method);

        // Post back to get a response.
        $params = array(
            'body' => $paypal_request_args,
            'timeout' => 60,
            'httpversion' => '1.1',
            'compress' => false,
            'decompress' => false,
            'user-agent' => 'BookingX/' . BKX_PLUGIN_VER,
        );
        $response = wp_safe_remote_post($this->api_endpoint, $params);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return $response = array('error' => $error_message);
        } else {
            $response_data = explode('&', $response['body']);
            if (!empty($response_data)) {
                $generate_paypal_response_data = array();
                foreach ($response_data as $string_res_data) {
                    $res_arg = explode("=", $string_res_data);
                    $generate_paypal_response_data[$res_arg[0]] = urldecode($res_arg[1]);
                }
            }
            $generate_paypal_response_data['method'] = $method;
            if ($method == $this->setMethod && "SUCCESS" == strtoupper($generate_paypal_response_data["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($generate_paypal_response_data["ACK"])) {
                return $generate_paypal_response_data;
            } else {
                return $generate_paypal_response_data;
            }
        }
    }

    public function ready_for_redirect_to_paypal($order_id, $generate_paypal_response_data)
    {
        $order_id = $this->order_id;
        $token_ack_response = $generate_paypal_response_data;
        //Respond according to message we receive from Paypal
        if ("SUCCESS" == strtoupper($token_ack_response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($token_ack_response["ACK"])) {
            //Redirect user to PayPal store with Token received.
            $paypalurl = $this->get_transaction_url($token_ack_response["TOKEN"]);
            echo '<meta http-equiv="refresh" content="0;url=' . $paypalurl . '">';
        } else {
            //Show error message
            return $response = array('error' => $token_ack_response);
        }
    }

    public function BkxPayPalGetPaymentStatusByTID($order_id)
    {
        if (empty($order_id))
            return;
        $this->order_id = $order_id;
        $getTransaction = $this->payment_request_call($order_id, $this->getTransaction);
        if ("SUCCESS" == strtoupper($getTransaction["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($getTransaction["ACK"])) {
            return $getTransaction;
        }
    }

    public function capture_payment($order_id, $args = null)
    {
        $get_token = sanitize_text_field($args["TOKEN"]);
        $get_payer_id = sanitize_text_field($args["PayerID"]);
        if (isset($get_token) && isset($get_payer_id)) {
            $response = $this->payment_request_call($order_id, $this->doMethod);
            update_post_meta($this->order_id, $this->doMethod . '_response', $response);
            $response['test'] = 1;
            if (!empty($response) && "SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])) {
                $transaction_id = urldecode($response["TRANSACTIONID"]);
                $getTransaction = $this->payment_request_call($order_id, $this->getTransaction);
                $getTransaction['getTransactiontest'] = 1;
                if ("SUCCESS" == strtoupper($getTransaction["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($getTransaction["ACK"])) {
                    $payment_data = json_encode($getTransaction);
                    update_post_meta($this->order_id, $this->getTransaction . '_response', $getTransaction);
                    $final_payment_meta = array('order_id' => $order_id,
                        'token' => sanitize_text_field($get_token),
                        'PayerID' => sanitize_text_field($get_payer_id),
                        'transactionID' => $transaction_id,
                        'payment_data' => $payment_data,
                        'payment_status' => $getTransaction["PAYMENTSTATUS"],
                        'pay_amt' => $getTransaction["AMT"]);

                    $BkxBooking = new BkxBooking('', $order_id);
                    if (isset($order_id) && $order_id != '') {
                        $payment_status = get_post_meta($order_id, 'payment_status', true);
                    }

                    if (isset($payment_status) && $payment_status != 'completed') {
                        $BkxBooking->update_payment_meta($final_payment_meta);
                        $BkxBooking->add_order_note(sprintf(__('Payment has been done via Paypal successfully', 'bookingx'), ''), 0, "", $order_id);
                    }
                    $current_status = $BkxBooking->get_order_status($order_id);
                    //send email that booking confirmed
                    do_action('bkx_order_edit_status', $order_id, $current_status);
                    return $process_response = array('success' => true, 'data' => $final_payment_meta);
                } else {
                    return $getTransaction;
                }
            } else {
                return $response;
            }
        }
    }
}
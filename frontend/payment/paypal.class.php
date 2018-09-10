<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class BkxPayPalGateway {

    function bkx_PPHttpPost($methodName_, $nvpStr_, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode) {
        // Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode($PayPalApiUsername);
        $API_Password = urlencode($PayPalApiPassword);
        $API_Signature = urlencode($PayPalApiSignature);
        $paypalmode = ( isset($PayPalMode) && $PayPalMode!="" && $PayPalMode == 'sandbox' ) ? '.sandbox' : '';
        $version = urlencode('76.0');
        $API_Endpoint = "https://api-3t{$paypalmode}.paypal.com/nvp";
        $paypal_req = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
        $received_values = array_filter( explode('&',$paypal_req));
        $generate_paypal_req = array();
        if(!empty($received_values)){
            foreach ($received_values as $string_data) {
                $pay_arg = explode("=", $string_data );
                if( $pay_arg[0] == 'RETURNURL' || $pay_arg[0] == 'CANCELURL' ){
                    $generate_paypal_req[$pay_arg[0]] = urldecode($pay_arg[1]);
                }else{
                    $generate_paypal_req[$pay_arg[0]] = $pay_arg[1];
                }
            }
        }
        // Post back to get a response.
        $params = array(
            'body'        => $generate_paypal_req,
            'timeout'     => 60,
            'httpversion' => '1.1',
            'compress'    => false,
            'decompress'  => false,
            'user-agent'  => 'BookingX/' . BKX_PLUGIN_VER,
        );
        $response = wp_safe_remote_post($API_Endpoint, $params);
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            $response_data = explode('&', $response['body']);
            if(!empty($response_data)){
                $generate_paypal_response_data = array();
                foreach ($response_data as $string_res_data) {
                    $res_arg = explode("=", $string_res_data );
                    $generate_paypal_response_data[$res_arg[0]] = urldecode($res_arg[1]);
                }
            }
            return $generate_paypal_response_data;
        }
    }
}
?>
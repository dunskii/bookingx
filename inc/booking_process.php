<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/* 
 * Booking Process
 * Payment Gateway : Paypal | Stripe
 * 
 */

$seat_alias = get_option("bkx_alias_seat", "Resource");
$base_alias = get_option("bkx_alias_base", "Service");
$addition_alias = get_option('bkx_alias_addition', 'Addition');

//set paypal configuration here
$PayPalMode = get_option('bkx_api_paypal_paypalmode'); // sandbox or live
$PayPalApiUsername = get_option('bkx_api_paypal_username'); //PayPal API Username
$PayPalApiPassword = get_option('bkx_api_paypal_password'); //Paypal API password
$PayPalApiSignature = get_option('bkx_api_paypal_signature'); //Paypal API Signature
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

//Order Insert Process

// Create post object
$order_post_array = array(
    'post_title' => wp_strip_all_tags($_POST['post_title']),
    'post_content' => sanitize_text_field($_POST['post_content']),
    'post_status' => 'publish',
    'post_author' => 1,
    'post_category' => array(8, 39)
);
// Insert the post into the database
wp_insert_post($my_post);


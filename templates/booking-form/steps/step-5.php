<?php
/**
 * Booking Form Step 5
 */
defined('ABSPATH') || exit;
if (!isset($_GET['order_id']) || $_GET['order_id'] == "")
    return;

$status = (isset($_GET['order_id']) && !empty($_GET['order_id']) && $_GET['order_id'] != 0) ? "active" : "deactivate"; ?>
<div class="step-5 <?php echo $status; ?>" data-active="5">
    <?php

    echo $order_id = base64_decode($_GET['order_id']);
    $order_data = get_post_meta($order_id);
    echo '<pre>', print_r($order_data, 1), '</pre>';


    $action_data = array('order_id' => $order_id);
    do_action('bkx_payment_gateway_capture_process_hook', $action_data);

    $capture_payment = get_post_meta($order_id, 'bkx_capture_payment', true);
    if (!empty($capture_payment) && $capture_payment['success'] == true) {
        $BkxPaymentCore = new BkxPaymentCore();
        echo $BkxPaymentCore->bkx_success_payment($capture_payment);
    }
    ?>
</div>
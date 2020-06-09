<?php
/**
 * My Account Page
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

do_action('bkx_before_my_account');

do_action('bkx_my_account_content');

do_action('bkx_after_my_account');
<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$text_color = bkx_crud_option_multisite('bkx_siteclient_css_text_color');
$text_color = ($text_color) ? $text_color : '#000';

$bg_color = bkx_crud_option_multisite('bkx_siteclient_css_background_color');
$bg_color = ($bg_color) ? $bg_color : '#ffffff';

$border_color = bkx_crud_option_multisite('bkx_siteclient_css_border_color');
$border_color = ($border_color) ? $border_color : '#ffffff';

$progressbar_color = bkx_crud_option_multisite('bkx_siteclient_css_progressbar_color');
$progressbar_color = ($progressbar_color) ? $progressbar_color : '';


$cal_border_color = bkx_crud_option_multisite('bkx_siteclient_css_cal_border_color');
$cal_border_color = ($cal_border_color) ? $cal_border_color : '';

$cal_day_color = bkx_crud_option_multisite('bkx_siteclient_css_cal_day_color');
$cal_day_color = ($cal_day_color) ? $cal_day_color : '';

$cal_day_selected_color = bkx_crud_option_multisite('bkx_siteclient_css_cal_day_selected_color');
$cal_day_selected_color = ($cal_day_selected_color) ? $cal_day_selected_color : '#73ad3a';


$time_available_color = bkx_crud_option_multisite('bkx_time_available_color');
$time_available_color = ($time_available_color) ? $time_available_color : '#368200';

$time_selected_color = bkx_crud_option_multisite('bkx_time_selected_color');
$time_selected_color = ($time_selected_color) ? $time_selected_color : '#e64754';

$time_unavailable_color = bkx_crud_option_multisite('bkx_time_unavailable_color');
$time_unavailable_color = ($time_unavailable_color) ? $time_unavailable_color : 'gray';

$bkx_cal_month_title_color = bkx_crud_option_multisite('bkx_cal_month_title_color') != '' ? bkx_crud_option_multisite('bkx_cal_month_title_color') : '#222222'; //  Calendar Month title 

$bkx_cal_month_bg_color = bkx_crud_option_multisite('bkx_cal_month_bg_color') != '' ? bkx_crud_option_multisite('bkx_cal_month_bg_color') : '#875428'; //  Calendar Month title 
?>
<style type="text/css">
    .bookingx_display_calendar {
        width: 48%;
        float: left;
    }

    #field_4_display_booking {
        width: 48%;
        float: left;
    }

    #booking_details_value {
        padding: 0 5px;
    }

    <?php if ( ! is_admin() ) { ?>
    .bookingx_form_container {
        padding: 10px;
        color: <?php echo $text_color; ?>;
        background-color: <?php echo  $bg_color;?>;
        border: 2px solid<?php echo  $border_color;?>;
    }

    #bkx_progressbar_wrapper_4 .ui-widget-header {
        background: <?php echo $progressbar_color;?> !important;
    }

    #id_datepicker .ui-widget-content {
        border: 1px solid <?php echo  $cal_border_color;?> !important;
        margin-top: 36px;
    }

    #id_datepicker .ui-state-default {
        background: <?php echo  $cal_day_color;?> !important;
    }

    #id_datepicker .ui-state-active {
        background: <?php echo  $cal_day_selected_color;?> !important
    }

    .ui-widget-header {
        background: <?php echo  $bkx_cal_month_bg_color;?> !important;
        color: <?php echo  $bkx_cal_month_title_color;?> !important;
    }

    <?php }?>

    .app_timetable_cell {
        text-align: center;
        width: 30%;
        border: none;
        margin: 1px;
        float: left;
    }

    .free {
        cursor: pointer;
        background: <?php echo $time_available_color;?>;
    }

    .full {
        background: <?php echo $time_unavailable_color;?>;
    }

    .selected-slot {
        background: <?php echo $time_selected_color;?>;
    }

    .notavailable {
        background: lightgray;
        display: none;
    }

    .error_booking {
        color: #e64754;
        font-weight: bold;
    }

    .booking-status-div {
        display: inline-block;
    }

    .booking-status {
        float: left;
        text-align: center;
        padding: 0 10px;
    }

    .booking-status div {
        width: 20px;
        height: 20px;
        display: inline-block;
    }

    div.error p {
        margin-bottom: 0px !important;
    }

    .clear-multi {
        clear: both
    }

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

    .bkx-checkbox {
        float: left;
    }

    li {
        display: block;
        padding-top: 5px;
    }

    /* Absolute Center Spinner */
    .bookingx-loader {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /* Transparent Overlay */
    .bookingx-loader:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.3);
    }

    /* :not(:required) hides these rules from IE9 and below */
    .bookingx-loader:not(:required) {
        /* hide "loading..." text */
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .bookingx-loader:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /* Animation */

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    /**Booking Calendar CSS*/
    .ui-datepicker td {
        border: 0 !important;
        padding: 1px !important;
    }

    .booking_dates {
        position: relative;
        top: 115px;
        padding: 0 25px;
    }

    label {
        display: inline-block;
    }
</style>
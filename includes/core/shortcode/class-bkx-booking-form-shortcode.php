<?php
/**
 *  BookingX Booking Form Short code
 *  Used on Booking Page to make a booking
 */
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'BkxBookingFormShortCode' ) ) {
    class BkxBookingFormShortCode
    {
        function __construct() {
            //Script Loader
            require_once(BKX_PLUGIN_DIR_PATH.'includes/core/script/class-bkx-script-loader.php');
            //Ajax Loader
            require_once(BKX_PLUGIN_DIR_PATH.'includes/core/ajax/class-bkx-ajax-loader.php');
            add_shortcode( 'bookingform', array( $this, 'BkxBookingFormShortCodeView' ) );
            do_action( 'before_bookingx_init' );
        }
        function load_global_variables(){
            $seat_alias     = bkx_crud_option_multisite("bkx_alias_seat");
            $base_alias     = bkx_crud_option_multisite("bkx_alias_base");
            $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
            $bkx_booking_style = bkx_crud_option_multisite('bkx_booking_style');
            $currency_sym   =  bkx_get_current_currency();
            $currency_name  = get_option( 'currency_option' );
            $global_variables =  array(
                'seat'          =>  ucfirst($seat_alias),
                'base'          =>  ucfirst($base_alias),
                'extra'         =>  ucfirst($addition_alias),
                'currency_sym'  =>  $currency_sym,
                'currency_name' =>  $currency_name,
                'booking_style' => ( ( !isset($bkx_booking_style) || $bkx_booking_style == "" ) ? "default" : $bkx_booking_style)
            );
            $global_variables = array_merge(bkx_localize_string_text(),$global_variables);

            return (object) $global_variables;
        }

        function BkxBookingFormShortCodeView(){
            ob_start();
                if(is_admin()){
                    $screen = get_current_screen();
                    $base = $screen->base;
                    $post_type = $screen->post_type;
                    if($base == "post" && $post_type == "bkx_booking"){
                        bkx_get_template('booking-form/update-booking-form.php');
                    }
                }else{
                    bkx_get_template('booking-form/booking-form.php');
                }
            return ob_get_clean();
        }
    }
    new BkxBookingFormShortCode();
}
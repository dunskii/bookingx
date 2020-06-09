<?php
/**
 * Handle All scripts
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Bkx_Script_Loader
{
    /**
     * Contains an array of script handles registered by BookingX.
     *
     * @var array
     */
    private static $scripts = array();

    /**
     * Contains an array of script handles registered by BookingX.
     *
     * @var array
     */
    private static $styles = array();

    /**
     * Contains an array of script handles localized by BookingX.
     *
     * @var array
     */
    private static $wp_localize_scripts = array();

    public static function init() {

        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        if(is_admin()){
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        }
        add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
        add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
    }

    /**
     * Register all BookingX scripts.
     */
    private static function register_scripts() {
        $register_scripts = array(
            'moment-with-locales'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/moment-with-locales.min.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'bootstrap'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/bootstrap.min.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'owl.carousel'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/owl.carousel.min.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'calendar'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/calendar.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'bookingx'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/bookingx.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'bkx-edit-booking-form'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/bkx-edit-booking-form.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'bkx-booking-form'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/booking-form/bkx-booking-form.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'jquery-blockui'             => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/jquery-blockui/jquery.blockUI.js',
                'deps'    => array( 'jquery' ),
                'version' => '2.70',
            ),
            'bkx-booking-form-admin'                 => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/js/admin/booking-form/bkx-booking-form.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            ),
            'fullcalendar'                 => array(
                'src'     => '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.3/fullcalendar.min.js',
                'deps'    => array( 'jquery' ),
                'version' => BKX_PLUGIN_VER,
            )
        );

        foreach ( $register_scripts as $name => $props ) {
            self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
        }
    }

    /**
     * register_styles
     * Register all BookingX Style.
     */
    private static function register_styles() {

        $register_styles = array(
            'bkx-google-fonts'                  => array(
                'src'     => 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900',
                'deps'    => array(),
                'version' => BKX_PLUGIN_VER,
                'has_rtl' => false,
            ),
            'owl-carousel-min'                  => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/owl.carousel.min.css',
                'deps'    => array(),
                'version' => BKX_PLUGIN_VER,
                'has_rtl' => false,
            ),
            'line-awesome-min'                  => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/line-awesome.min.css',
                'deps'    => array(),
                'version' => BKX_PLUGIN_VER,
                'has_rtl' => false,
            ),
            'style-main'                  => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/style.css',
                'deps'    => array('bkx-google-fonts','line-awesome-min'),
                'version' => BKX_PLUGIN_VER,
                'has_rtl' => false,
            ),
            'blue'                  => array(
                'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/blue.css',
                'deps'    => array('style-main'),
                'version' => BKX_PLUGIN_VER,
                'has_rtl' => false,
            ),
            'fullcalendarcss'                  => array(
                'src'     => '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.3/fullcalendar.min.css',
                'deps'    => array('style-main'),
                'version' => BKX_PLUGIN_VER,
                'has_rtl' => false,
            )
        );

        foreach ( $register_styles as $name => $props ) {
            self::register_style($name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl']);
        }
    }

    /**
     * @return mixed|void
     */
    public static function get_styles() {
        $register_styles =  apply_filters(
            'bookingx_enqueue_styles',
            array(
                'bkx-google-fonts'                  => array(
                    'src'     => 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900',
                    'deps'    => array(),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
                'owl-carousel-min'                  => array(
                    'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/owl.carousel.min.css',
                    'deps'    => array(),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
                'bootstrap'                  => array(
                    'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/bootstrap.min.css',
                    'deps'    => array(),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
                'line-awesome-min'                  => array(
                    'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/line-awesome.min.css',
                    'deps'    => array(),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
                'style-main'                  => array(
                    'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/style.css',
                    'deps'    => array('bkx-google-fonts','line-awesome-min'),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
                'blue'                  => array(
                    'src'     => BKX_PLUGIN_PUBLIC_URL . '/css/style.css',
                    'deps'    => array('style-main'),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
                'fullcalendarcss'                  => array(
                    'src'     => '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.3/fullcalendar.min.css',
                    'deps'    => array('style-main'),
                    'version' => BKX_PLUGIN_VER,
                    'has_rtl' => false,
                ),
            )
        );
        return $register_styles;
    }

    /**
     * @param $handle
     * @param $path
     * @param array $deps
     * @param string $version
     * @param bool $in_footer
     */
    private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = BKX_PLUGIN_VER, $in_footer = true ) {
        self::$scripts[] = $handle;
        wp_register_script( $handle, $path, $deps, $version, $in_footer );
    }

    /**
     * @param $handle
     * @param string $path
     * @param array $deps
     * @param int $version
     * @param bool $in_footer
     */
    private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = BKX_PLUGIN_VER, $in_footer = true ) {
        if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
            self::register_script( $handle, $path, $deps, $version, $in_footer );
        }
        wp_enqueue_script( $handle );
    }

    /**
     * @param $handle
     * @param $path
     * @param array $deps
     * @param string $version
     * @param string $media
     * @param bool $has_rtl
     */
    private static function register_style( $handle, $path, $deps = array(), $version = BKX_PLUGIN_VER, $media = 'all', $has_rtl = false ) {
        self::$styles[] = $handle;
        wp_register_style( $handle, $path, $deps, $version, $media );

        if ( $has_rtl ) {
            wp_style_add_data( $handle, 'rtl', 'replace' );
        }
    }

    /**
     * @param $handle
     * @param string $path
     * @param array $deps
     * @param int $version
     * @param string $media
     * @param bool $has_rtl
     */
    private static function enqueue_style( $handle, $path = '', $deps = array(), $version = BKX_PLUGIN_VER, $media = 'all', $has_rtl = false ) {
        if ( ! in_array( $handle, self::$styles, true ) && $path ) {
            self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
        }
        wp_enqueue_style( $handle );
    }

    /**
     * Register/queue frontend scripts.
     */

    public static function load_scripts() {
        if ( ! did_action( 'before_bookingx_init' ) ) {
            return;
        }

        if(is_admin() && !isset($_GET['listing_view']) ){
            $screen = get_current_screen();
            $base = $screen->base;
            $post_type = $screen->post_type;
            if($base == "edit" && $post_type == "bkx_booking"){
                self::register_scripts();
                self::enqueue_script( 'bkx-booking-form-admin' );
            }
            if($base == "post" && $post_type == "bkx_booking"){
            }else{return;}
        }

        self::register_scripts();
        self::register_styles();
        // Global frontend scripts.
        self::enqueue_script( 'bookingx' );
        self::enqueue_script( 'moment-with-locales' );
        self::enqueue_script( 'bootstrap' );
        self::enqueue_script( 'owl.carousel' );
        self::enqueue_script( 'calendar' );
        self::enqueue_script( 'jquery-blockui' );


        if(is_admin()){
            $screen = get_current_screen();
            $base = $screen->base;
            $post_type = $screen->post_type;
            if($base == "post" && $post_type == "bkx_booking"){
                self::enqueue_script( 'bkx-booking-form-admin' );
            }elseif(isset($_GET['listing_view']) && ( $_GET['listing_view'] == "monthly" || $_GET['listing_view'] == "weekly" )){
                self::enqueue_script( 'fullcalendar' );
            }else{return;}
        }elseif( isset($_REQUEST['edit_booking_nonce']) && wp_verify_nonce( $_REQUEST['edit_booking_nonce'], 'edit_booking_'.$_REQUEST['id'] )) {
            self::enqueue_script( 'bkx-edit-booking-form' );
        } else{
            if(is_booking_page()){
                self::enqueue_script( 'bkx-booking-form' );
            }
        }

        if(is_admin() && isset($_GET['listing_view']) ){
            self::enqueue_style('fullcalendarcss');
        }else{
            // CSS Styles.
            $enqueue_styles = self::get_styles();
            if ( $enqueue_styles ) {
                foreach ( $enqueue_styles as $handle => $args ) {
                    if ( ! isset( $args['has_rtl'] ) ) {
                        $args['has_rtl'] = false;
                    }
                    $args['media'] = "";
                    self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
                }
            }
        }
        $custom_css = bkx_generate_inline_style();
        wp_add_inline_style( 'style-main', $custom_css );
    }

    /**
     * Return data for script handles.
     *
     * @param  string $handle Script handle the data will be attached to.
     * @return array|bool
     */
    private static function get_script_data( $handle ) {
        //Load Global Variables
        $BkxBookingFormShortCode = new BkxBookingFormShortCode();
        $global_variables = (array) $BkxBookingFormShortCode->load_global_variables();
        if( isset($_REQUEST['edit_booking_nonce']) && wp_verify_nonce( $_REQUEST['edit_booking_nonce'], 'edit_booking_'.$_REQUEST['id'] )) {
            $booking_id = $_REQUEST['id'];
        }else{
            $booking_id = ( is_admin() ? ( isset($_GET['post']) ? $_GET['post'] : 0 ) : (isset($_GET['booking_id']) ? $_GET['booking_id'] : 0 ) );
        }
        $bkx_legal = bkx_crud_option_multisite('bkx_legal_options');
        $defaults_params = array(
            'last_page_url'                             => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'ajax_url'                                  => admin_url( 'admin-ajax.php', 'relative' ),
            'bkx_legal'                                 => $bkx_legal,
            'bkx_ajax_url'                              => Bkx_Ajax_Loader::get_endpoint( '%%endpoint%%' ),
            'on_seat_change_nonce'                      => wp_create_nonce( "on-seat-change" ),
            'on_base_change_nonce'                      => wp_create_nonce( "on-base-change" ),
            'update_booking_total_nonce'                => wp_create_nonce( "update-booking-total" ),
            'get_step_2_details_nonce'                  => wp_create_nonce( "get-step-2-details" ),
            'get_step_3_details_nonce'                  => wp_create_nonce( "get-step-3-details" ),
            'get_calendar_availability_nonce'           => wp_create_nonce( "get-calendar-availability" ),
            'display_availability_slots_nonce'          => wp_create_nonce( "display-availability-slots" ),
            'display_availability_slots_by_dates_nonce' => wp_create_nonce( "display-availability-slots-by-dates" ),
            'get_verify_slot_nonce'                     => wp_create_nonce( "get-verify-slot" ),
            'payment_method_on_change_nonce'            => wp_create_nonce( "payment-method-on-change" ),
            'get_payment_method_nonce'                  => wp_create_nonce( "get-payment-method" ),
            'book_now_nonce'                            => wp_create_nonce( "book-now" ),
            'back_to_booking_page_nonce'                => wp_create_nonce( "back-to-booking-page" ),
            'send_email_receipt_nonce'                  => wp_create_nonce( "send-email-receipt" ),
            'check_staff_availability'                  => wp_create_nonce('check-staff-availability'),
            'change_password_nonce'                     => wp_create_nonce('change-password'),
            'customer_details_nonce'                     => wp_create_nonce('customer-details')
        );

        if(isset($booking_id) && $booking_id > 0 ){
            $BkxBooking = new BkxBooking();
            $order_meta_data = $BkxBooking->get_order_meta_data( $booking_id );
            if(!empty($order_meta_data)){
                $seat_id        = $order_meta_data['seat_id'];
                $base_id        = $order_meta_data['base_id'];
                $addition_ids   = $order_meta_data['addition_ids'];
                $extended_base  = $order_meta_data['extended_base_time'];
                $booking_date   = date('Y-m-d', strtotime($order_meta_data['booking_date']));
                $first_name     = $order_meta_data['first_name'];
                $last_name      = $order_meta_data['last_name'];
                $phone          = $order_meta_data['phone'];
                $email          = $order_meta_data['email'];
            }
            $defaults_params['is_admin']            = ( !is_admin() ? false : true );
            $defaults_params['booking_id']          = ( ( isset($booking_id) && $booking_id > 0 ) ? $booking_id : 0 );
            $defaults_params['seat_id']             = ( ( isset($seat_id) && $seat_id > 0 ) ? $seat_id : 0 );
            $defaults_params['base_id']             = ( ( isset($base_id) && $base_id > 0 ) ? $base_id : 0 );
            $defaults_params['extended_base']       = ( ( isset($extended_base) && $extended_base > 0 ) ? $extended_base : 0 );
            $defaults_params['addition_ids']        = ( ( isset($addition_ids) && $addition_ids > 0 ) ? $addition_ids : 0 );
            $defaults_params['booking_date']        = ( ( isset($booking_date) && $booking_date != "" ) ? $booking_date : "" );
            $defaults_params['first_name']          = ( ( isset($booking_date) && $booking_date != "" ) ? $booking_date : "" );
            $defaults_params['booking_date']        = ( ( isset($booking_date) && $booking_date != "" ) ? $booking_date : "" );
            $defaults_params['first_name']          = ( ( isset($first_name) && $first_name != "" ) ? $first_name : "" );
            $defaults_params['last_name']           = ( ( isset($last_name) && $last_name != "" ) ? $last_name : "" );
            $defaults_params['phone']               = ( ( isset($phone) && $phone != "" ) ? $phone : "" );
            $defaults_params['email']               = ( ( isset($email) && $email != "" ) ? $email : "" );
        }

        switch ( $handle ) {
            case 'bkx-booking-form-admin':
            case 'bkx-booking-form':
            case 'bkx-edit-booking-form':
            case 'bookingx':
                $params = array_merge( $global_variables , $defaults_params );
                break;
            default:
                $params = false;
        }

        return apply_filters( 'bkx_get_script_data', $params, $handle );
    }

    /**
     * Localize a BookingX script once.
     *
     */
    private static function localize_script( $handle ) {
        if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
            $data = self::get_script_data( $handle );
            if ( ! $data ) {
                return;
            }
            $name  = str_replace( '-', '_', $handle ) . '_params';
            self::$wp_localize_scripts[] = $handle;
            wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
        }
    }

    /**
     * Localize scripts only when enqueued.
     */
    public static function localize_printed_scripts() {
        foreach ( self::$scripts as $handle ) {
            self::localize_script( $handle );
        }
    }
}
Bkx_Script_Loader::init();
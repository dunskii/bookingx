<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
    /** Single Page ********************************************************/

    if (!function_exists('bookingx_show_post_images')) {
        function bookingx_show_post_images($data)
        {
            if (is_single() || (!empty($data) || $data['id'] == 'all') || (!empty($data) && strtolower($data['image']) == 'yes' && $data['image'] != '')) {
                bkx_get_template('bkx-single/image.php');
            }
            return;
        }
    }

    if (!function_exists('bookingx_template_single_title')) {

        /**
         * Output the post title.
         *
         * @subpackage    Post
         */
        function bookingx_template_single_title($data)
        {
            bkx_get_template('bkx-single/title.php');
        }
    }

    if (!function_exists('bookingx_template_single_price')) {

        /**
         * Output the post price.
         *
         * @subpackage    Post
         */
        function bookingx_template_single_price($data)
        {
            bkx_get_template('bkx-single/price.php');
        }
    }
    if (!function_exists('bookingx_template_single_excerpt')) {

        /**
         * Output the post short description (excerpt).
         *
         * @subpackage    Post
         */
        function bookingx_template_single_excerpt($data)
        {
            if (is_single() || (!empty($data) || $data['id'] == 'all') || (!empty($data) && strtolower($data['extra-info']) == 'yes' && $data['extra-info'] != '')) {
                bkx_get_template('bkx-single/short-description.php');
            }

        }
    }
    if (!function_exists('bookingx_template_single_meta')) {

        /**
         * Output the post meta.
         *
         * @subpackage    Post
         */
        function bookingx_template_single_meta($data)
        {
            if (!is_single())
                return;
            bkx_get_template('bkx-single/meta.php');
        }
    }
    if (!function_exists('bookingx_template_single_descriptions')) {

        /**
         * Output the post sharing.
         *
         * @subpackage    Post
         */
        function bookingx_template_single_descriptions($data)
        {

            if (is_single() || (!empty($data) && isset($data['description']) && strtolower($data['description']) == 'yes' && $data['description'] != '')) {
                bkx_get_template('bkx-single/descriptions.php');
            }
        }
    }

    if (!function_exists('bookingx_template_single_booking_url')) {
        /**
         *
         */
        function bookingx_template_single_booking_url()
        {
            bkx_get_template('bkx-single/add-to-booking.php');
        }
    }

    if (!function_exists('bookingx_template_single_pagination')) {
        /**
         *
         */
        function bookingx_template_single_pagination($data)
        {
            if (!is_single())
                return;
            bkx_get_template('bkx-single/pagination.php');
        }
    }

    /** global ****************************************************************/
    if (!function_exists('bookingx_output_content_wrapper')) {

        /**
         * Output the start of the page wrapper.
         *
         */
        function bookingx_output_content_wrapper($data)
        {
            bkx_get_template('global/wrapper-start.php');
        }
    }
    if (!function_exists('bookingx_output_content_wrapper_end')) {

        /**
         * Output the end of the page wrapper.
         *
         */
        function bookingx_output_content_wrapper_end($data)
        {
            bkx_get_template('global/wrapper-end.php');
        }
    }

    if ( ! function_exists( 'bookingx_breadcrumb' ) ) {

        /**
         * Output the Booking X Breadcrumb.
         *
         * @param array $args Arguments.
         */
        function bookingx_breadcrumb( $args = array() ) {
            $args = wp_parse_args(
                $args,
                apply_filters(
                    'bookingx_breadcrumb_defaults',
                    array(
                        'delimiter'   => '&nbsp;&#47;&nbsp;',
                        'wrap_before' => '<nav class="bookingx-breadcrumb" aria-label="breadcrumb"><ol class="breadcrumb">',
                        'wrap_after'  => '</ol></nav>',
                        'before'      => '<li class="breadcrumb-item">',
                        'after'       => '<li>',
                        'home'        => _x( 'Home', 'breadcrumb', 'bookingx' ),
                    )
                )
            );

            $breadcrumbs = array();

            if ( ! empty( $args['home'] ) ) {
                $breadcrumbs[] = array('title' => $args['home'], 'hyperlink' => apply_filters( 'bookingx_breadcrumb_home_url', home_url() ) ) ;
            }
            if ( is_search() ) {
                $breadcrumbs[] = array('title' => booking_x_page_title(), 'hyperlink' => '' ) ;
            } elseif ( is_tax() ) {
                $breadcrumbs[] = array('title' => booking_x_page_title(), 'hyperlink' => '' ) ;
            }elseif(is_post_type_archive() ){
                $post_type = get_post_type();
                $breadcrumbs[] = array('title' => post_type_archive_title('', false), 'hyperlink' => get_post_type_archive_link($post_type) ) ;
            } elseif(is_singular()){
                $post_type = get_post_type();
                $post_type_obj = get_post_type_object( $post_type );
                if($post_type_obj){
                    $breadcrumbs[] = array('title' => $post_type_obj->label, 'hyperlink' => get_post_type_archive_link($post_type) ) ;
                }
                $breadcrumbs[] = array('title' => get_the_title(), 'hyperlink' => get_permalink() ) ;
            } else {
                $breadcrumbs[] = array('title' => booking_x_page_title(), 'hyperlink' => get_permalink() ) ;
            }

            do_action( 'bookingx_breadcrumb', $breadcrumbs, $args );

            $args['breadcrumb'] = $breadcrumbs;

            bkx_get_template( 'global/breadcrumb.php', $args );
        }
    }


    if (!function_exists('bookingx_get_sidebar')) {

        /**
         * Get the Service sidebar template.
         *
         */
        function bookingx_get_sidebar($data)
        {
            bkx_get_template('global/sidebar.php');
        }
    }

    if (!function_exists('bookingx_form_additional_fields')) {

        /**
         * Get the Service sidebar template.
         *
         */
        function bookingx_form_additional_fields()
        {
            bkx_get_template('booking-form/additional/fields.php');
        }
    }


    function bkx_get_formatted_price($price)
    {
        $currencyBlock = '';
        $currency_option = (bkx_crud_option_multisite('currency_option') ? bkx_crud_option_multisite('currency_option') : 'AUD');
        if (!empty($price)) :
            $currencyBlock = '<span class="currencyBlock"><currency itemprop="priceCurrency price" content="' . $currency_option . '' . $price . '" style="color:#7BBD4D!important"> ' . get_bookingx_currency_symbol($currency_option) . '' . $price . '</currency></span>';
        endif;

        return $currencyBlock;
    }

    function bkx_get_post_price_duration_plain($postobj, $alias)
    {
        if (!empty($postobj)) {

            $base_post = $postobj->post;
            $base_id = $base_post->ID;
            $base_name = $base_post->post_title;

            if (!empty($postobj->get_service_time)) {
                $base_service_time = $postobj->get_service_time;
            }

            if (!empty($postobj->get_extra_time)) {
                $base_service_time = $postobj->get_extra_time;
            }
            $currencyBlock = bkx_get_formatted_price($postobj->get_price);
            $base_service_timeHMObj = $base_service_time['H']; //Hours and minutes
            if (isset($base_service_time['D'])) {
                $base_service_timeDObj = $base_service_time['D']; // Days
            }
            if (!empty($base_service_timeHMObj)):
                $hours = isset($base_service_timeHMObj['hours']) && $base_service_timeHMObj['hours'] > 0 && $base_service_timeHMObj['hours'] != '' ? $base_service_timeHMObj['hours'] . ' Hours ' : '';
                $mins = isset($base_service_timeHMObj['minutes']) && $base_service_timeHMObj['minutes'] > 0 && $base_service_timeHMObj['minutes'] != '' ? $base_service_timeHMObj['minutes'] . ' Minutes' : '';
                $base_service_time_text = $hours . ' ' . $mins;
            endif;

            if (!empty($base_service_timeDObj)):
                $day = isset($base_service_timeDObj['days']) && $base_service_timeDObj['days'] != '' ? $base_service_timeDObj['days'] . ' Days ' : '';
                $base_service_time_text = $day;
            endif;

            $available_services['time'] = $base_service_time_text;
            $available_services['price'] = $currencyBlock;
        }

        return $available_services;
    }


    function bkx_get_post_with_price_duration($get_base_by_seat, $alias, $type = null)
    {
        $available_services = '';
        $bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
        if (!empty($bkx_set_booking_page)) {
            $booking_url = esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page)));
        }

        if (!empty($get_base_by_seat)) {
            $available_services .= '<h3>Available ' . $alias . '</h3><hr class="mt-1"/>';

            foreach ($get_base_by_seat as $key => $BaseObj) {
                if(!empty($BaseObj->post)){
                    $available_services .= '<div class="row"> ';
                    $base_post = $BaseObj->post;
                    $base_id = $base_post->ID;
                    $base_name = $base_post->post_title;
                    if($base_post->post_type != 'bkx_booking') {
                        if ($type == 'seat') {
                            $ClassObj = new BkxBase('', $base_id);
                            $post_type = $base_post->post_type;
                        } elseif ($type == 'base') {
                            $ClassObj = new BkxBase('', $base_id);
                            $post_type = $base_post->post_type;
                        } else {
                            $post_type = $base_post->post_type;
                        }

                        if (!empty($BaseObj->get_service_time)) {
                            $base_service_time = $BaseObj->get_service_time;

                        }

                        if (!empty($BaseObj->get_extra_time)) {
                            $base_service_time = $BaseObj->get_extra_time;
                        }
                        $currencyBlock = "";
                        if (!empty($BaseObj->get_price)) {
                            $currencyBlock = bkx_get_formatted_price($BaseObj->get_price);
                        }

                        if (isset($base_service_time['H'])) {
                            $base_service_timeHMObj = $base_service_time['H']; //Hours and minutes
                        }
                        if (isset($base_service_time['D'])) {
                            $base_service_timeDObj = $base_service_time['D']; // Days
                        }
                        $base_service_time_text = "";
                        if (isset($base_service_timeHMObj) && !empty($base_service_timeHMObj)):
                            $hours  = isset($base_service_timeHMObj['hours']) && $base_service_timeHMObj['hours'] > 0 && $base_service_timeHMObj['hours'] != '' ? $base_service_timeHMObj['hours'] . ' Hours ' : '';
                            $mins = isset($base_service_timeHMObj['minutes']) && $base_service_timeHMObj['minutes'] > 0 && $base_service_timeHMObj['minutes'] != '' ? $base_service_timeHMObj['minutes'] . ' Minutes' : '';
                            $base_service_time_text = " - {$hours} {$mins}";
                        endif;

                        if (isset($base_service_timeDObj) && !empty($base_service_timeDObj)):
                            $day = isset($base_service_timeDObj['days']) && $base_service_timeDObj['days'] != '' ? $base_service_timeDObj['days'] . ' Days ' : '';
                            $base_service_time_text = $day;
                        endif;
                        if (!empty($base_id)):
                            $available_services .= '<div class="col-md-8 mt-1 mb-1"><a href="' . get_permalink($base_id) . '">' . $base_name . '</a> ' . $base_service_time_text . ' ' . $currencyBlock . '</div>
                                                    <div class="col-md-4 mt-1 mb-1"><form method="post" enctype="multipart/form-data" action="' . $booking_url . '">
                                                    <input type="hidden" name="type" value="' . $post_type . '" /><input type="hidden" name="id" value="' . $base_id . '" />';
                            if ($post_type != 'bkx_addition') :
                                $available_services .= '<button type="submit" class="btn btn-primary">' . sprintf(__('Book now', 'Bookingx')) . '</button>';
                            endif;
                            $available_services .= '</form></div>';
                        endif;
                    }
                    $available_services .= '</div><hr class="mt-1"/>';
                }
            }
        }
        return $available_services;
    }

if ( ! function_exists( 'booking_x_page_title' ) ) {

    /**
     * Page Title function.
     *
     * @param  bool $echo Should echo title.
     * @return string
     */
    function booking_x_page_title( $echo = true ) {

        if ( is_search() ) {
            /* translators: %s: search query */
            $page_title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'bookingx' ), get_search_query() );

            if ( get_query_var( 'paged' ) ) {
                /* translators: %s: page number */
                $page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'bookingx' ), get_query_var( 'paged' ) );
            }
        } elseif ( is_tax() ) {

            $page_title = single_term_title( '', false );

        }elseif(is_post_type_archive()){
            $page_title = post_type_archive_title('', false);
        } else {
            $page_title   = get_the_title();
        }

        $page_title = apply_filters( 'booking_x_page_title', $page_title );

        if ( $echo ) {
            echo $page_title;
        } else {
            return $page_title;
        }
    }
}

if (!function_exists('bookingx_card_width_setting')) {
    function bookingx_card_width_setting( ){
        $width = '18rem';
        switch ( get_template() ) {
            case 'twentytwenty':
                $width = '38rem';
                break;

        }
        return $width;
    }
}


if (!function_exists('bookingx_block_grid_setting')) {

    /**
     * @param $args
     * Get args array from block and related column setting
     * @return array
     */
    function bookingx_block_grid_setting($args)
    {
        $class = "col-lg-4 col-12 mb-2";
        $settings = array('class' => $class );
        if(!empty($args)){
            $columns = isset($args['columns']) ? $args['columns'] : 3;
            $rows = isset($args['rows']) ? $args['rows'] : 3;
            $col = block_column_cal($columns);
            $block = "";
            if(isset($columns)){
                $class = "col-lg-{$col} col-12 mb-2";
            }
            if(isset($args['block']) && $args['block'] == 1 ){
                $block = "-block";
            }
            $settings['class'] = $class;
            $settings['block'] = $block;
            $settings['rows']   = $rows;
        }
        return $settings;
    }
}

if(!function_exists('bookingx_base_meta_data_html')){
    function bookingx_base_meta_data_html( $bkx_base ){
        $base_id = $bkx_base->id;
        $BaseObj = new BkxBase(null, $base_id);
        echo $BaseObj->get_meta_details_html($base_id);
    }
}

if(!function_exists('bkx_dashboard_nav')){
    function bkx_dashboard_nav(){
        bkx_get_template('dashboard/nav.php');
    }
}

if(!function_exists('bkx_dashboard_content')){
    function bkx_dashboard_content(){
        bkx_get_template('dashboard/nav-content.php');
    }
}

if(!function_exists('bkx_dashboard_booking_view')){
    function bkx_dashboard_booking_view(){
        bkx_get_template('dashboard/detail.php');
    }
}



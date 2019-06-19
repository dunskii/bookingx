<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Description of BkxBase
 *
 * @author Divyang Parekh
 */
class BkxBase {
    /**
     * @var mixed|void
     */
    public $alias;

    /**
     * @var array
     */
    public $meta_data = array();

    /**
     * @var string
     */
    public $bkx_post_type = 'bkx_base';

    /**
     * @var array|null|type|WP_Post
     */
    public $post;

    /**
     * @var type
     */
    public $get_price;

    /**
     * @var type
     */
    public $get_service_time;

    /**
     * @var type
     */
    public $get_seat_by_base;

    /**
     * @var type
     */
    public $service_extended;

    /**
     * @var type
     */
    public $get_service_fixed_mobile;

    /**
     * @var mixed|void
     */
    public $get_service_unavailable;

    /**
     * @var mixed|void
     */
    public $booking_url; // Booking url with arguments

    /**
     * @var mixed|void
     */
    public $booking_page_url; // Just Booking Page Url

    /**
     * @var
     */
    public $url_arg_name; // Just Booking Page Url

    /**
     * @var null
     */
    public $id;

    public $load_global = array();

    /**
     *
     * @param type $bkx_post
     */
    public function __construct($bkx_post = null, $post_id = null)
    {
        if(is_multisite()):
            $blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
            switch_to_blog($blog_id);
        endif;

        if (!empty($bkx_post->post_type)) {
            $this->bkx_post_type = $bkx_post->post_type;
        }

        if (!empty($bkx_post)) {
            $post_id = $bkx_post->ID;
        }

        if (isset($post_id) && $post_id != '') {
            $post_id = $post_id;
            $bkx_post = get_post($post_id);
        }
        $alias_base = bkx_crud_option_multisite('bkx_alias_base', "default");
        $this->alias = $alias_base;
        $this->post = $bkx_post;
        $this->meta_data = get_post_custom($post_id);
        $this->get_price = $this->get_price();
        $this->get_service_time = $this->get_service_time();
        $this->get_seat_by_base = $this->get_seat_by_base();
        $this->service_extended = $this->service_extended();
        $this->get_service_fixed_mobile = $this->get_service_fixed_mobile();
        $this->get_service_unavailable = $this->get_service_unavailable();
        $this->booking_url = $this->get_booking_url();
        $this->booking_page_url = $this->get_booking_page_url();
        $this->id = $post_id;

        //Load Global Variables
        $BkxBookingFormShortCode = new BkxBookingFormShortCode();
        $this->load_global = $BkxBookingFormShortCode->load_global_variables();

         if(is_multisite()):
             restore_current_blog();
         endif;
    }

    public function get_base_options_for_booking_form( $seat_id = null ){

        if( !isset($seat_id) && $seat_id == "" )
            return;

        if(is_multisite()):
            $blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
            switch_to_blog($blog_id);
        endif;
        $base_lists = $this->get_base_by_seat( $seat_id );
        if(!empty($base_lists)){
            $base_html = sprintf( __( '<option> Select %s </option>', 'bookingx' ), esc_html( $this->load_global->base ) );
            foreach ( $base_lists as $base_data ){
                $base       = $base_data->post;
                $base_name  = $base->post_title;
                $base_id    = $base->ID;
                $base_price         = get_post_meta( $base_id, 'base_price', true );
                $base_time_option   = get_post_meta( $base_id, 'base_time_option', true );
                $base_day           = get_post_meta( $base_id, 'base_day', true );
                $base_hours         = get_post_meta( $base_id, 'base_hours', true );
                $base_hours         = isset($base_time_option) && $base_time_option == "H" && $base_hours > 0 ? "{$base_hours} Hour": "" ;
                $base_minutes       = get_post_meta( $base_id, 'base_minutes', true );
                $base_hours         .=  isset($base_hours) && isset($base_minutes) && $base_minutes > 0 ? " {$base_minutes} Minutes" : "";
                $base_html .= sprintf( __( '<option value="%d"> %s - %s%s%d - %s</option>', 'bookingx' ), $base_id, esc_html( $base_name ) ,$this->load_global->currency_name, $this->load_global->currency_sym, $base_price, $base_hours );
            }
        }else{
            $base_html = esc_html("NORF");
        }
        if(is_multisite()):
            restore_current_blog();
        endif;

        return $base_html;

    }

    /**
     * Get the title of the post.
     *
     * @return string
     */
    public function get_title() {
        if(empty($this->post))
            return;

        $post_id            = $this->post->ID;
        $base_price         = get_post_meta( $post_id, 'base_price', true );
        $base_time = $this->get_time();
        $base_title = "{$this->post->post_title} - {$this->load_global->currency_name}{$this->load_global->currency_sym}{$base_price} - {$base_time['formatted']}";
        return apply_filters( 'bkx_addition_title', $base_title, $this );

    }

    public function get_time(){
        $post_id            = $this->post->ID;
        $base_time_option   = get_post_meta( $post_id, 'base_time_option', true );
        $base_day           = get_post_meta( $post_id, 'base_day', true );
        $base_months        = get_post_meta( $post_id, 'months', true );

        $base_time = 0;
        if ($base_time_option == "H") {
            $base_hours         = get_post_meta( $post_id, 'base_hours', true );
            $base_minutes       = get_post_meta( $post_id, 'base_minutes', true );
            $base_hours         = isset($base_time_option) && $base_time_option == "H" && ($base_hours > 0 && $base_hours <= 23) ? $base_hours: 0 ;
            $base_minutes       = isset($base_minutes) && $base_minutes > 0 ? $base_minutes : 0;

            $total_base_duration = ($base_hours * 60) + $base_minutes;
            $base_time          =  $total_base_duration;
            $base['formatted']  = bkx_total_time_of_services_formatted( $total_base_duration );
            $base['type']       = $base_time_option;
        } else if ($base_time_option == "D" ) {
            $base['formatted']  = sprintf(__('%1$s Days', 'bookingx'), $base_day);
            $base_time          = $base_day * 24;
            $base['type']       = $base_time_option;
        } else if ($base_time_option == "M") {
            $base['formatted']  = sprintf(__('%1$s Months', 'bookingx'), $base_months);
            $base_time          = $base_months * 24 * 30;
            $base['type']       = $base_time_option;
        }
        $base['in_sec']     = $base_time * 60 ;
        return apply_filters( 'bkx_addition_time', $base, $this );
    }

    /**
     *
     * @return type
     */
    public function get_description() {
        $post_content = $this->post->post_content;
        return apply_filters('bkx_base_description', $post_content, $this);
    }

    /**
     *
     * @return type
     */
    public function get_excerpts() {
        $post_excerpt = $this->post->post_excerpt;
        return apply_filters('bkx_base_price', $post_excerpt, $this);
    }

    /**
     *
     * @return type
     */
    public function get_thumb() {
        if (has_post_thumbnail($this->post)) {
            $gallery = "";
            $image = get_the_post_thumbnail($this->post->ID, apply_filters('bkx_single_post_large_thumbnail_size', 'bkx_single'), array(
                'title' => $this->get_title(),
                'alt' => $this->get_title(),
            ));
            return apply_filters(
                'bookingx_single_post_image_html',
                sprintf(
                    '<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="prettyPhoto%s">%s</a>',
                    esc_url(get_permalink()),
                    esc_attr($this->get_title()),
                    $gallery,
                    $image
                ),
                $this->post->ID
            );
        }
    }

    /**
     *
     * @return type
     */
    public function get_thumb_url() {
        $get_thumb_url = the_post_thumbnail_url($this->post);
        return apply_filters('bkx_base_thumb_url', $get_thumb_url, $this);
    }

    /**
     *
     * @return type
     */
    public function get_price() {
        $meta_data = $this->meta_data;
        $base_price = isset($meta_data['base_price']) ? esc_attr($meta_data['base_price'][0]) : 0;
        return apply_filters('bkx_base_price', $base_price, $this);
    }

    /**
     *
     * @return type
     */
    public function get_service_time() {
        $meta_data = $this->meta_data;
        $base_time_option = isset($meta_data['base_time_option']) ? esc_attr($meta_data['base_time_option'][0]) : "";
        //$base_month = isset($meta_data['base_month']) ? esc_attr($meta_data['base_month'][0]) : "";
        $base_day = isset($meta_data['base_day']) ? esc_attr($meta_data['base_day'][0]) : "";
        $base_hours = isset($meta_data['base_hours']) ? esc_attr($meta_data['base_hours'][0]) : "";
        $base_minutes = isset($meta_data['base_minutes']) ? esc_attr($meta_data['base_minutes'][0]) : "";
        $time_data = array();

        if ($base_time_option == 'H'):
            $time_data[$base_time_option]['hours'] = $base_hours;
            if ($base_minutes != ''):
                $time_data[$base_time_option]['minutes'] = $base_minutes;
            endif;
        endif;
        if ($base_time_option == 'D'):
            $time_data[$base_time_option]['days'] = $base_day;
        endif;
        return apply_filters('bkx_base_service_time', $time_data, $this);
    }
    /**
     * @return mixed|void
     */
    public function get_sevice_time_data() {
        $service_time = $this->get_service_time();
        $get_sevice_time_data = "";
        if (isset($service_time) && !empty($service_time['H'])) {
            $hours = $service_time['H']['hours'];
            $minutes = $service_time['H']['minutes'];
            if (isset($hours) && $hours != '') {
                if ($hours > 1) {
                    $hs = "s";
                }
                $hour_data = sprintf(__('%s hour %s', 'Bookingx'), $hours, $hs);
            }
            if (isset($minutes) && $minutes != '' && $minutes > 0) {
                if ($minutes > 1) {
                    $ms = "s";
                }
                $minute_data = sprintf(__('%s minute %s', 'Bookingx'), $minutes, $ms);
            }
            $get_sevice_time_data = $hour_data . ' ' . $minute_data;
        }
        return apply_filters('bkx_base_sevice_time_data', $get_sevice_time_data, $this);
    }
    /**
     *
     * @return type
     */
    public function service_extended() {
        $meta_data = $this->meta_data;
        $base_is_extended = isset($meta_data['base_is_extended']) ? esc_attr($meta_data['base_is_extended'][0]) : "";
        return apply_filters('bkx_base_service_extended', $base_is_extended, $this);
    }

    /**
     *
     * @return type
     */
    public function get_seat_by_base() {
        $meta_data = $this->meta_data;
        if (isset($meta_data['base_selected_seats'][0]) && $meta_data['base_selected_seats'][0] != "") {
            $seat_by_base = maybe_unserialize($meta_data['base_selected_seats'][0]);
            return apply_filters('bkx_base_seat_by_base', $seat_by_base, $this);
        }
    }

    /**
     *
     * @return type
     */
    public function get_service_fixed_mobile() {
        $meta_data = $this->meta_data;
        $base_is_location_fixed = isset($meta_data['base_is_location_fixed']) ? esc_attr($meta_data['base_is_location_fixed'][0]) : "";
        $base_is_mobile_only = isset($meta_data['base_is_mobile_only']) ? esc_attr($meta_data['base_is_mobile_only'][0]) : "";
        $base_is_location_differ_seat = isset($meta_data['base_is_location_differ_seat']) ? esc_attr($meta_data['base_is_location_differ_seat'][0]) : "";
        $service_fixed_mobile = array();
        if (isset($base_is_location_fixed) && $base_is_location_fixed == 'Y') {
            if ($base_is_location_differ_seat == 'Y') {
                $service_fixed_mobile['fixed']['service_location']['label'] = 'Fixed Location';
                $base_street = isset($meta_data['base_street']) ? esc_attr($meta_data['base_street'][0]) : "";
                $base_city = isset($meta_data['base_city']) ? esc_attr($meta_data['base_city'][0]) : "";
                $base_state = isset($meta_data['base_state']) ? esc_attr($meta_data['base_state'][0]) : "";
                $base_postcode = isset($meta_data['base_postcode']) ? esc_attr($meta_data['base_postcode'][0]) : "";
                $service_fixed_mobile['fixed']['service_location']['street'] = $base_street;
                $service_fixed_mobile['fixed']['service_location']['city'] = $base_city;
                $service_fixed_mobile['fixed']['service_location']['state'] = $base_state;
                $service_fixed_mobile['fixed']['service_location']['postcode'] = $base_postcode;
            }
        }

        if (isset($base_is_mobile_only) && $base_is_mobile_only == 'Y') {
            $service_fixed_mobile['mobile']['value'] = 'Yes';
            $service_fixed_mobile['mobile']['label'] = 'Mobile Location';
        } else {
            $service_fixed_mobile['mobile']['value'] = 'No';
            $service_fixed_mobile['mobile']['label'] = 'Mobile Location';
        }
        return apply_filters('bkx_base_service_fixed_mobile', $service_fixed_mobile, $this);
    }
    /**
     *
     */
    public function get_service_unavailable(){
        $meta_data = $this->meta_data;
        $service_unavailable = array();
        $base_is_unavailable = isset($meta_data['base_is_unavailable']) ? esc_attr($meta_data['base_is_unavailable'][0]) : "";
        $base_unavailable_from = isset($meta_data['base_unavailable_from']) ? esc_attr($meta_data['base_unavailable_from'][0]) : "";
        $base_unavailable_till = isset($meta_data['base_unavailable_till']) ? esc_attr($meta_data['base_unavailable_till'][0]) : "";
        if ($base_is_unavailable == 'Y'):
            $service_unavailable['from'] = $base_unavailable_from;
            $service_unavailable['till'] = $base_unavailable_till;
        endif;
        return apply_filters('bkx_base_service_unavailable', $service_unavailable, $this);
    }
    /**
     * @param $seat_id
     * @return array|void
     */
    public function get_base_by_seat( $seat_id ){

        if( !isset($seat_id) && $seat_id == "" )
            return;

        if(is_multisite()):
            $blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
            switch_to_blog($blog_id);
        endif;

        $enable_any_seat = bkx_crud_option_multisite('enable_any_seat');
        $enable_any_seat = isset($enable_any_seat) && $enable_any_seat > 0 ? $enable_any_seat : 0;

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'bkx_base',
            'post_status' => 'publish',
            'suppress_filters' => false,
        );
        if($enable_any_seat == 0 ){
            $args['meta_query'] =
                array(
                    array(
                        'key' => 'base_selected_seats',
                        'value' => serialize($seat_id),
                        'compare' => 'like'
                    )
                );
        }

        $baseresult = new WP_Query($args);
        $BaseData = array();
        if ($baseresult->have_posts()) :
            while ($baseresult->have_posts()) : $baseresult->the_post();
                $base_id = get_the_ID();
                $BkxBaseObj = new BkxBase('', $base_id);
                $BaseData[] = $BkxBaseObj;
            endwhile;
            wp_reset_query();
            wp_reset_postdata();
        endif;
        return $BaseData;
    }

    /**
     * @param $base_id
     * @return array|void
     */
    public function get_seat_obj_by_base( $base_id ){
        if (empty($base_id))
            return;
        $SeatData = array();
        $base_selected_seats = get_post_meta($base_id, 'base_selected_seats', true);
        if (!empty($base_selected_seats)) {
            foreach ($base_selected_seats as $key => $seat) {
                $BkxSeatObj = new BkxSeat('', $seat);
                $SeatData[] = $BkxSeatObj;
            }
        }
        return $SeatData;
    }
    /**
     * @param $extra_id
     * @return array|void
     */
    public function get_base_by_extra($extra_id) {
        if (empty($extra_id))
            return;
        $BaseData = array();
        $extra_selected_base = get_post_meta($extra_id, 'extra_selected_base', true);
        if (!empty($extra_selected_base)) {
            foreach ($extra_selected_base as $key => $base_id) {
                $BkxBaseObj = new BkxBase('', $base_id);
                $BaseData[] = $BkxBaseObj;
            }
        }
        return $BaseData;
    }
    /**
     * @return mixed|void
     */
    public function get_booking_url() {
        $bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
        $url_arg_name = "";
        if (bkx_crud_option_multisite('permalink_structure')) {
            $booking_url = esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page) . '' . $this->url_arg_name . '/' . get_the_ID()));
        } else {
            $arg = array('bookingx_type' => $url_arg_name, 'bookingx_type_id' => get_the_ID());
            $booking_url = esc_url(add_query_arg($arg, get_permalink($bkx_set_booking_page)));
        }
        return apply_filters('bkx_booking_url', $booking_url, $this);
    }
    /**
     * @return mixed|void
     */
    public function get_booking_page_url(){
        if (is_multisite()) {
            $current_blog_id = get_current_blog_id();
            $bkx_set_booking_page = get_blog_option($current_blog_id, 'bkx_set_booking_page');
        } else {
            $bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
        }
        return apply_filters('bkx_booking_page_url', esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page))), $this);
    }
}
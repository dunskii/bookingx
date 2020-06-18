<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class BkxBase
 */
class BkxBase
{
    /**
     * @var mixed|void
     */
    public $alias;
    /**
     * @var string
     */
    public $post_title;

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

    /**
     * @var array|object
     */
    public $load_global = array();

    /**
     * BkxBase constructor.
     * @param null $bkx_post
     * @param null $post_id
     */
    public function __construct($bkx_post = null, $post_id = null)
    {
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        //Load Global Variables
        $BkxBookingFormShortCode = new BkxBookingFormShortCode();
        $this->load_global = $BkxBookingFormShortCode->load_global_variables();

        if (!empty($bkx_post->post_type)) {
            $this->bkx_post_type = $bkx_post->post_type;
        }

        if (!empty($bkx_post)) {
            $this->id = $bkx_post->ID;
            $post_id = $bkx_post->ID;
        }
        if (isset($post_id) && $post_id != '') {
            $this->id = $post_id;
            $bkx_post = get_post($post_id);
        }
        //echo '<pre>',print_r($bkx_post,1),'</pre>';die;
        $alias_base = bkx_crud_option_multisite('bkx_alias_base', "default");
        $this->alias = $alias_base;
        $this->post = $bkx_post;
        $this->meta_data = get_post_custom($post_id);
        $this->get_price = $this->get_price();
        $this->get_service_time = $this->get_service_time();
        $this->get_seat_by_base = $this->get_seat_by_base();
        $this->service_extended = $this->service_extended();
        $this->get_service_unavailable = $this->get_service_unavailable();
        $this->booking_url = $this->get_booking_url();
        $this->booking_page_url = $this->get_booking_page_url();
        $this->post_title = $this->get_title();
        $this->id = $post_id;

        if (is_multisite()):
            restore_current_blog();
        endif;
    }

    /**
     * @param $base_id
     * @return string
     */
    public function get_meta_details_html($base_id)
    {
        $booking_url = $this->booking_page_url;
        $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
        $meta_data = $this->GetMetData($base_id);
        $Duration = $this->get_service_time($base_id);
        if ((isset($Duration['H']))) {
            $DurationHMObj = $Duration['H'];
        }
        if ((isset($Duration['D']))) {
            $DurationDObj = $Duration['D'];
        }

        if (!empty($DurationHMObj)):
            $hours = isset($DurationHMObj['hours']) && $DurationHMObj['hours'] > 0 && $DurationHMObj['hours'] != '' ? $DurationHMObj['hours'] . ' Hours ' : '';
            $hours = isset($DurationHMObj['hours']) && $DurationHMObj['hours'] < 2 && $DurationHMObj['hours'] != '' ? $DurationHMObj['hours'] . ' Hour ' : $hours;
            $mins = isset($DurationHMObj['minutes']) && $DurationHMObj['minutes'] > 0 && $DurationHMObj['minutes'] != '' ? " and {$DurationHMObj['minutes']} Minutes " : '';
            $duration_text = "{$hours} {$mins}";
        endif;
        if (!empty($DurationDObj)):
            $day = isset($DurationDObj['days']) && $DurationDObj['days'] != '' ? $DurationDObj['days'] . ' Days ' : '';
            $duration_text = $day;
        endif;
        $extended = $this->service_extended();
        $extended_label = isset($extended) && $extended == 'Y' ? 'Yes' : 'No';

        $html = "";
        $html .= "<div class=\"row\">
                    <div class=\"col-md-8\">" . sprintf('<h3>%s</h3>', __('Additional Information', 'bookingx')) . "</div>
                    <div class=\"col-md-4\">
                        <form method=\"post\" enctype='multipart/form-data' action=\"$booking_url\">
                            <input type=\"hidden\" name=\"type\" value=\"bkx_seat\" />
                            <input type=\"hidden\" name=\"id\" value=\"esc_attr( $base_id )\" />
                            <button type=\"submit\" class=\"btn btn-primary\">" . esc_html('Book now', 'bookingx') . "</button>
                        </form>
                    </div>
                <div class=\"col-md-12\"><hr class=\"mt-1\"/></div>
                </div>";
        $html .= "<div class=\"row\">
                    <div class=\"col-md-12\">
                            <ul class=\"additional-info\">";
        $html .= sprintf('<li><div class="durations"><label>%s :</label><span> %s</span></li>', 'Duration ', $duration_text);
        $html .= sprintf(__('<li><div class="extended"><label>%2$s Time Extended :</label><span> %1$s</span></li>', 'bookingx'), $extended_label, $bkx_alias_base);
        $html .= "</ul>
                    </div>
                </div>";
        return $html;
    }

    /**
     * @param null $seat_id
     * @return string|void
     */
    public function get_base_options_for_booking_form($seat_id = null)
    {

        if (!isset($seat_id) && $seat_id == "")
            return;

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $base_lists = $this->get_base_by_seat($seat_id);

        if (!empty($base_lists)) {
            $base_html = sprintf(__('<option> Select %s </option>', 'bookingx'), esc_html($this->load_global->base));
            foreach ($base_lists as $base_data) {
                $base = $base_data->post;
                $base_name = $base_data->post_title;
                $base_id = $base->ID;
                $base_price = get_post_meta($base_id, 'base_price', true);
                $base_time_option = get_post_meta($base_id, 'base_time_option', true);
                $base_day = get_post_meta($base_id, 'base_day', true);
                $base_hours = get_post_meta($base_id, 'base_hours', true);
                $base_hours = isset($base_time_option) && $base_time_option == "H" && $base_hours > 0 ? "{$base_hours} Hour" : "";
                $base_minutes = get_post_meta($base_id, 'base_minutes', true);
                $base_hours .= isset($base_hours) && isset($base_minutes) && $base_minutes > 0 ? " {$base_minutes} Minutes" : "";
                $base_html .= sprintf(__('<option value="%d"> %s </option>', 'bookingx'), $base_id, esc_html($base_name));
            }
        } else {
            $base_html = esc_html("NORF");
        }
        if (is_multisite()):
            restore_current_blog();
        endif;

        return $base_html;

    }

    /**
     * Get the title of the post.
     * @return mixed|void
     * @throws Exception
     */
    public function get_title()
    {
        $post_id = $this->id;
        if (empty($post_id))
            return;
        $bkx_post = get_post($post_id);
        $base_price = get_post_meta($post_id, 'base_price', true);
        $base_time = $this->get_time($post_id);

        $base_title = "{$bkx_post->post_title} - {$this->load_global->currency_name}{$this->load_global->currency_sym}{$base_price} - {$base_time['formatted']}";
        return apply_filters('bkx_addition_title', $base_title);
    }

    /**
     * @param null $post_id
     * @return mixed|void
     * @throws Exception
     */
    public function get_time($post_id = null)
    {

        $post_id = isset($post_id) ? $post_id : $this->id;

        if (empty($post_id))
            return;

        $base_time_option = get_post_meta($post_id, 'base_time_option', true);
        $base_time = 0;
        if ($base_time_option == "H") {
            $base_hours = get_post_meta($post_id, 'base_hours', true);
            $base_minutes = get_post_meta($post_id, 'base_minutes', true);
            $base_hours = isset($base_time_option) && $base_time_option == "H" && ($base_hours > 0 && $base_hours <= 23) ? $base_hours : 0;
            $base_minutes = isset($base_minutes) && $base_minutes > 0 ? $base_minutes : 0;

            $total_base_duration = ($base_hours * 60) + $base_minutes;
            $base_time = $total_base_duration;
            $base['formatted'] = bkx_total_time_of_services_formatted($total_base_duration);
            $base['type'] = $base_time_option;
        }

        if ($base_time_option == "D") {
            $base_day = get_post_meta($post_id, 'base_day', true);
            if (isset($base_day) && $base_day > 0 && $base_day < 2) {
                $base['formatted'] = sprintf(__('%1$s Day', 'bookingx'), $base_day);
            }
            if (isset($base_day) && $base_day > 0 && $base_day > 1) {
                $base['formatted'] = sprintf(__('%1$s Days', 'bookingx'), $base_day);
            }
            $base_time = $base_day * 24 * 60;
            $base['type'] = $base_time_option;
        }

        if ($base_time_option == "M") {
            $base_months = get_post_meta($post_id, 'months', true);
            $base['formatted'] = sprintf(__('%1$s Months', 'bookingx'), $base_months);
            $base_time = $base_months * 24 * 30;
            $base['type'] = $base_time_option;
        }
        $base['in_sec'] = $base_time * 60;
        return apply_filters('bkx_addition_time', $base);
    }

    /**
     * @return mixed|void
     */
    public function get_description()
    {
        $post_content = apply_filters('the_content', get_the_content($this->post->ID));
        return apply_filters('bkx_base_description', $post_content);
    }

    /**
     * @return mixed|void
     */
    public function get_excerpts()
    {
        $post_excerpt = $this->post->post_excerpt;
        return apply_filters('bkx_base_price', $post_excerpt);
    }

    /**
     * @return mixed|void
     * @throws Exception
     */
    public function get_thumb()
    {
        if (has_post_thumbnail($this->post)) {
            $image = get_the_post_thumbnail($this->post->ID, apply_filters('bkx_single_post_large_thumbnail_size', 'bkx_single'), array(
                'title' => $this->get_title(),
                'class' => 'img-thumbnail',
                'alt' => $this->get_title(),
            ));
        } else {
            $image = '<img src="' . BKX_PLUGIN_PUBLIC_URL . '/images/placeholder.png" class="img-thumbnail wp-post-image" alt="' . $this->get_title() . '" title="' . $this->get_title() . '">';
        }

        return apply_filters(
            'bookingx_single_post_image_html',
            sprintf(
                '<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="%s">%s</a>',
                esc_url(get_permalink()),
                esc_attr($this->get_title()),
                "",
                $image
            ),
            $this->post->ID
        );
    }

    /**
     *
     */
    public function get_thumb_url()
    {
        $get_thumb_url = the_post_thumbnail_url($this->post);
        // return apply_filters('bkx_base_thumb_url', $get_thumb_url, $this);
    }

    /**
     *
     * @return type
     */
    public function get_price()
    {
        $meta_data = $this->meta_data;
        $base_price = isset($meta_data['base_price']) ? esc_attr($meta_data['base_price'][0]) : 0;
        return apply_filters('bkx_base_price', $base_price, $this);
    }

    /**
     * @param $base_id
     * @return mixed
     */
    public function GetMetData($base_id)
    {

        if (empty($base_id))
            return;
        return get_post_meta($base_id);
    }

    /**
     * @param $base_id
     * @return type
     */
    public function get_service_time($base_id = null)
    {
        if (empty($base_id)) {
            $base_id = $this->id;
        }
        $meta_data = $this->GetMetData($base_id);

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

        return apply_filters('bkx_base_service_time', $time_data);
    }

    /**
     * @return mixed|void
     */
    public function get_sevice_time_data()
    {
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
    public function service_extended()
    {
        $meta_data = $this->meta_data;
        $base_is_extended = isset($meta_data['base_is_extended']) ? esc_attr($meta_data['base_is_extended'][0]) : "";
        return apply_filters('bkx_base_service_extended', $base_is_extended, $this);
    }

    /**
     *
     * @return type
     */
    public function get_seat_by_base()
    {
        $meta_data = $this->meta_data;
        if (isset($meta_data['base_selected_seats'][0]) && $meta_data['base_selected_seats'][0] != "") {
            $seat_by_base = maybe_unserialize($meta_data['base_selected_seats'][0]);
            return apply_filters('bkx_base_seat_by_base', $seat_by_base, $this);
        }
    }

    /**
     * @return mixed|void
     */
    public function get_service_unavailable()
    {
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
    public function get_base_by_seat($seat_id)
    {
        if (!isset($seat_id) && $seat_id == "")
            return;

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
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
        if ($enable_any_seat == 0 || ($seat_id != "any" && $seat_id > 0)) {
            $args['meta_query'] =
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'base_selected_seats',
                        'value' => maybe_serialize($seat_id),
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => 'base_selected_seats',
                        'value' => serialize($seat_id),
                        'compare' => 'IN'
                    ),
                );
        }
        $base_result = new WP_Query($args);

        $BaseData = array();
        if ($base_result->have_posts()) :
            while ($base_result->have_posts()) : $base_result->the_post();
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
    public function get_seat_obj_by_base($base_id)
    {
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
    public function get_base_by_extra($extra_id)
    {
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
    public function get_booking_url()
    {
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
    public function get_booking_page_url()
    {
        if (is_multisite()) {
            $current_blog_id = get_current_blog_id();
            $bkx_set_booking_page = get_blog_option($current_blog_id, 'bkx_set_booking_page');
        } else {
            $bkx_set_booking_page = bkx_crud_option_multisite('bkx_set_booking_page');
        }
        return apply_filters('bkx_booking_page_url', esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page))), $this);
    }
}
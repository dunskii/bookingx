<?php
function bkx_scripts_init()
{
    $seat_alias = bkx_crud_option_multisite("bkx_alias_seat");
    $base_alias = bkx_crud_option_multisite("bkx_alias_base");
    $extra_alias = bkx_crud_option_multisite('bkx_alias_addition');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-progressbar');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('wp-jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script("jquery-timepicker", '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js', false, null, true);

    $booking_edit_process_page_id = bkx_crud_option_multisite("booking_edit_process_page_id");
    if (get_the_ID() == $booking_edit_process_page_id) {

        $nonce = $_REQUEST['_wpnonce'];
        if (isset($_REQUEST['i'])) {
            $encrypted_booking_id = $_REQUEST['i'];
        }

        //Booking Variables
        $decoded_booking_data = base64_decode($encrypted_booking_id);
        $decoded_booking_data_arr = explode("|", $decoded_booking_data);
        $booking_id = $decoded_booking_data_arr[0];
        $seat_id = $decoded_booking_data_arr[1];
        $base_id = $decoded_booking_data_arr[2];
        //$extra_id = $decoded_booking_data_arr[3];
        $return_page_id = $decoded_booking_data_arr[4];

        $BkxBooking = new BkxBooking();
        $order_meta_data = $BkxBooking->get_order_meta_data($booking_id);
        $base_extended = $order_meta_data['extended_base_time'];

        $extra_id = get_post_meta($booking_id, 'addition_ids', true);
        $extra_id = rtrim($extra_id, ",");
        $base_extended = ($base_extended) ? $base_extended : 0;

        wp_deregister_script('et-shortcodes-js');

        wp_enqueue_script("edit_booking_common_script", BKX_PLUGIN_DIR_URL . "frontend/js/edit_booking_common_script.js", false, BKX_PLUGIN_VER, true);

        $translation_array = array(
            'plugin_url' => BKX_PLUGIN_DIR_URL,
            'bkx_ajax_url' => admin_url('admin-ajax.php'),
            'base' => $base_alias,
            'order_id' => $booking_id,
            'seat_id' => $seat_id,
            'base_id' => $base_id,
            'extra_id' => !empty($extra_id) ? $extra_id : '0',
            'extended' => $base_extended,
            'return_page_id' => $return_page_id,
            'step_text' => sprintf(__('%s','bookingx'), 'Step'),
            'step_text_of' => sprintf(__('%s','bookingx'), 'of'),
            'select_a_text'     => sprintf(__('%s','bookingx'), 'Select a'),
            'total_cost'     => sprintf(__('%s','bookingx'), 'Total Cost'),
            'total_tax'     => sprintf(__('%s','bookingx'), 'Total Tax'),
            'note'     => sprintf(__('%s','bookingx'), 'Note'),
            'date_time'     => sprintf(__('%s','bookingx'), 'Date / Time'),
            'grand_total'     => sprintf(__('%s','bookingx'), 'Grand Total'),
            'action' => 'edit');
        $validation_array = bkx_localize_string_text();
        $wp_localize_array = array_merge($translation_array,$validation_array);
        wp_localize_script('edit_booking_common_script', 'edit_booking_obj', $wp_localize_array);

    } else {

        wp_enqueue_script("common_script", BKX_PLUGIN_DIR_URL . "frontend/js/bkx-common.js?ver=" . BKX_PLUGIN_VER, false, BKX_PLUGIN_VER, true);
        $BkxBooking = new BkxBooking();
        $search['search_by'] = 'future';
        $search['search_date'] = date('Y-m-d');

        $booked_days = '';
        $BookedRecords = $BkxBooking->GetBookedRecordsByUser($search);

        if (!empty($BookedRecords)) {
            foreach ($BookedRecords as $bookings) :
                $booking_multi_days = $bookings['booking_multi_days'];
                $booking_multi_days_arr = explode(",", $booking_multi_days);
                if (!empty($booking_multi_days_arr)) {
                    $booking_multi_days_data = !empty($booking_multi_days_arr[0]) ? $booking_multi_days_arr[0] : "";
                    $booking_multi_days_data_1 = !empty($booking_multi_days_arr[1]) ? $booking_multi_days_arr[1] : "";
                    $booking_multi_days_range = bkx_getDatesFromRange($booking_multi_days_data, $booking_multi_days_data_1, "m/d/Y");
                    if (!empty($booking_multi_days_range)) {
                        foreach ($booking_multi_days_range as $multi_days) {
                            $booked_days .= $multi_days . ",";
                        }
                    }
                }
                $booking_start_date = date('m/d/Y', strtotime($bookings['booking_start_date']));
                $booked_days .= $booking_start_date . ",";
            endforeach;
            $booked_days = rtrim($booked_days, ',');
        } else {
            $booked_days = 0;
        }
        $booked_days_arr = explode(",", $booked_days);
        $booked_days_filtered = implode(",", array_unique($booked_days_arr));

        $bkx_biz_vac_sd = bkx_crud_option_multisite('bkx_biz_vac_sd');
        $bkx_biz_vac_ed = bkx_crud_option_multisite('bkx_biz_vac_ed');
        $bkx_biz_pub_holiday = bkx_crud_option_multisite('bkx_biz_pub_holiday');
        $biz_pub_days = '';
        $bkx_biz_pub_holiday = array_values($bkx_biz_pub_holiday);
        if (!empty($bkx_biz_pub_holiday)) {
            foreach ($bkx_biz_pub_holiday as $key => $bkx_biz_pub_holiday_day) {
                if(isset($bkx_biz_pub_holiday_day) && $bkx_biz_pub_holiday_day!=""){
                    $pub_hd_obj = DateTime::createFromFormat('d/m/Y', $bkx_biz_pub_holiday_day);
                    $final_bkx_biz_pub_holiday_day[] = $pub_hd_obj->format('m/d/Y');
                }
            }
            if(!empty($final_bkx_biz_pub_holiday_day)){
                $biz_pub_days = implode(",", $final_bkx_biz_pub_holiday_day);
            }
        }
        $biz_vac_days = "";
        if (!empty($bkx_biz_vac_sd) && !empty($bkx_biz_vac_ed)) {
            $biz_vac_days = bkx_getDatesFromRange($bkx_biz_vac_sd, $bkx_biz_vac_ed, "m/d/Y");
            if (!empty($biz_vac_days)) {
                $biz_vac_days = implode(",", $biz_vac_days);
            }
        }

        $translation_array = array(
            'plugin_url' => BKX_PLUGIN_DIR_URL,
            'bkx_ajax_url' => admin_url('admin-ajax.php'),
            'admin_ajax' => admin_url('admin-ajax.php'),
            'base' => $base_alias,
            'booked_days' => $booked_days_filtered,
            'biz_vac_days' => $biz_vac_days,
            'biz_pub_days' => $biz_pub_days,
            'step_text' => sprintf(__('%s','bookingx'), 'Step'),
            'step_text_of' => sprintf(__('%s','bookingx'), 'of'),
            'select_a_text'     => sprintf(__('%s','bookingx'), 'Select a'),
            'total_cost'     => sprintf(__('%s','bookingx'), 'Total Cost'),
            'total_tax'     => sprintf(__('%s','bookingx'), 'Total Tax'),
            'note'     => sprintf(__('%s','bookingx'), 'Note'),
            'date_time'     => sprintf(__('%s','bookingx'), 'Date / Time'),
            'grand_total'     => sprintf(__('%s','bookingx'), 'Grand Total')
        );
        $validation_array = bkx_localize_string_text();
        $wp_localize_array = array_merge($translation_array,$validation_array);
        wp_localize_script('common_script', 'url_obj', $wp_localize_array);

    }
    wp_enqueue_style('bookingform-css', BKX_PLUGIN_DIR_URL . 'css/bookingform-style.css', false, false);
    wp_enqueue_style('bkx-cal-ui-css', BKX_PLUGIN_DIR_URL . 'css/bkx-cal-ui.css', false, false);
    wp_enqueue_style('bkx-upgrade', BKX_PLUGIN_DIR_URL . 'css/bkx-upgrade.css', '', BKX_PLUGIN_VER);
}

add_action('wp_enqueue_scripts', 'bkx_scripts_init');


function bkx_load_custom_wp_admin_style()
{
    $screen = get_current_screen();

    $post_type = $screen->post_type;
    $get_allowed = bkx_get_allowed();
    $base_alias = get_option("bkx_alias_base", "Base");
    if (!empty($get_allowed) && !empty($post_type) && in_array($post_type, $get_allowed)) {
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script('iris');
        wp_enqueue_script("jquery-fullcalendar-moment", BKX_PLUGIN_DIR_URL . "frontend/js/fullcalendar/lib/moment.min.js", false, BKX_PLUGIN_VER, true);
        wp_enqueue_script("jquery-fullcalendar-min", BKX_PLUGIN_DIR_URL . "frontend/js/fullcalendar/fullcalendar.min.js", false, BKX_PLUGIN_VER, true);
        wp_enqueue_script("jquery-timepicker", '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js', false, null, true);
        wp_enqueue_script("common_script", BKX_PLUGIN_DIR_URL . "admin/js/bkx-common.js", false, BKX_PLUGIN_VER , true);

        $edit_order_data_array = array(
            'order_id' => '',
            'seat_id' => '',
            'base_id' => '',
            'extra_id' => 0,
            'action' => 'add',
            'extended' => 0,
            'admin_ajax' => admin_url('admin-ajax.php'),
            'step_text' => sprintf(__('%s','bookingx'), 'Step'),
            'step_text_of' => sprintf(__('%s','bookingx'), 'of'),
            'select_a_text'     => sprintf(__('%s','bookingx'), 'Select a'),
            'total_cost'     => sprintf(__('%s','bookingx'), 'Total Cost'),
            'total_tax'     => sprintf(__('%s','bookingx'), 'Total Tax'),
            'note'     => sprintf(__('%s','bookingx'), 'Note'),
            'date_time'     => sprintf(__('%s','bookingx'), 'Date / Time'),
            'grand_total'     => sprintf(__('%s','bookingx'), 'Grand Total')
        );

        $translation_array = array(
            'plugin_url' => BKX_PLUGIN_DIR_URL,
            'bkx_ajax_url' => admin_url('admin-ajax.php'),
            'base' => $base_alias,
            'admin_ajax' => admin_url('admin-ajax.php'),
            'step_text' => sprintf(__('%s','bookingx'), 'Step'),
            'step_text_of' => sprintf(__('%s','bookingx'), 'of'),
            'select_a_text'     => sprintf(__('%s','bookingx'), 'Select a'),
            'total_cost'     => sprintf(__('%s','bookingx'), 'Total Cost'),
            'total_tax'     => sprintf(__('%s','bookingx'), 'Total Tax'),
            'note'     => sprintf(__('%s','bookingx'), 'Note'),
            'date_time'     => sprintf(__('%s','bookingx'), 'Date / Time'),
            'grand_total'     => sprintf(__('%s','bookingx'), 'Grand Total'),
            bkx_localize_string_text()
        );
        $validation_array = bkx_localize_string_text();
        $wp_localize_array_1 = array_merge($translation_array,$validation_array);
        $wp_localize_array_2 = array_merge($edit_order_data_array,$validation_array);
        wp_localize_script('common_script', 'url_obj', $wp_localize_array_1);
        wp_localize_script('common_script', 'edit_order_data', $wp_localize_array_2);
    }
    wp_enqueue_style('fullcalendar-css', BKX_PLUGIN_DIR_URL . 'css/fullcalendar.min.css', false, false);
    wp_enqueue_style('bookingform-css', BKX_PLUGIN_DIR_URL . 'css/bookingform-style.css', false, false);
    wp_enqueue_style('bkx-cal-ui.css', BKX_PLUGIN_DIR_URL . 'css/bkx-cal-ui.css', false, false);
}
add_action('admin_enqueue_scripts', 'bkx_load_custom_wp_admin_style');
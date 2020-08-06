<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class BkxBooking
 * Core Class for Bookings and Managements
 */
class BkxBooking
{

    /**
     * @var int|mixed|void
     */
    public $calculate_total = 0;

    /**
     * @var null
     */
    public $order_id;

    /**
     * @var string
     */
    protected $post_type;


    public $load_global = array();

    /**
     *
     * @param type $booking_set_array
     */
    /**
     * BkxBooking constructor.
     * @param null $booking_set_array
     * @param null $order_id
     */
    public function __construct($booking_set_array = null, $order_id = null)
    {

        if (!empty($booking_set_array)) {
            $seat_id = $booking_set_array['seat_id'];
            $base_id = $booking_set_array['base_id'];
            $extra_str = $booking_set_array['extra_id'];
            $extra_ids = array_filter(array_unique(explode(',', $extra_str)));
            $seat_post = get_post($seat_id);
            $base_post = get_post($base_id);
            $BkxSeat = new BkxSeat($seat_post);
            $BkxBase = new BkxBase($base_post);
            $BkxExtraObj = array();
            if (!empty($extra_ids)) {
                foreach ($extra_ids as $key => $value) {
                    $extra_post = get_post($value);
                    $BkxExtraObj[] = new BkxExtra($extra_post);
                }
            }
            $this->BkxSeat = $BkxSeat;
            $this->BkxBase = $BkxBase;
            $this->BkxExtra = $BkxExtraObj;
            $this->calculate_total = $this->get_calculate_total();
            //$this->order_id = $this->generate_order();
        }

        //Load Global Variables
        $BkxBookingFormShortCode = new BkxBookingFormShortCode();
        $this->load_global = $BkxBookingFormShortCode->load_global_variables();

        $this->order_id = $order_id;
        if (isset($order_id) && $order_id > 0) {
            $this->setId($order_id);
        }
        $this->post_type = 'bkx_booking';
        //add_filter('comments_clauses', array($this, 'bkx_comments_clauses'), 99, 2);
        add_action('transition_post_status', array($this, 'bkx_on_all_status_transitions'), 10, 3);
        add_action('bkx_order_edit_status', array($this, 'bkx_order_edit_status'), 10, 2);
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->order_id = absint($id);
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->order_id;
    }

    /**
     * @param $new_status
     * @param $old_status
     * @param $post
     * @throws Exception
     */
    public function bkx_on_all_status_transitions($new_status, $old_status, $post)
    {
        $post_id = $post->ID;
        if ($new_status != $old_status && get_post_type($post) == 'bkx_booking') {
            if ($new_status == 'trash') {
                $order = new BkxBooking('', $post_id);
                $order->update_status('cancelled');
                wp_safe_redirect(wp_get_referer());
                die;
            }
        }
    }

    public function booking_cancelled(){
        $booking_id = $this->order_id;

        if(empty($booking_id))
            return;

        $order_status = $this->get_order_status($booking_id);
        if(isset($order_status) && $order_status == "" || $order_status == "Completed")
            return;


    }

    public function booking_email($booking_id, $status)
    {
        $mailer = new BKX_Emails_Setup();
        $email_templates = $mailer->get_emails();
        $is_customer = false;
        if ($status) {
            if (strpos($status, 'bkx') !== false) {
                $status = str_replace('bkx-', '', $status);
            }
            if (strpos($status, 'customer') !== false) {
                $is_customer = true;
            }
            $email_status = "bkx_email_{$status}_booking"; //bkx_email_bkx-pending_booking
            foreach ($email_templates as $email_key => $email) {
                if (strtolower($email_key) === $email_status) {
                    $subject = $email->subject;
                    $content = $email->additional_content;
                    bkx_process_mail_by_status($booking_id, $subject, $content, $email, $is_customer);
                    break;
                }
            }
        }
    }

    public function bkx_order_edit_status($booking_id, $status)
    {
        if ($status) {
            $this->booking_email($booking_id, $status);
        }
    }

    function CalendarJsonData($search)
    {

        $get_posts = $this->GetBookedRecords($search);
        $timezone_string = bkx_crud_option_multisite('timezone_string');
        if (isset($timezone_string) && $timezone_string != "") {
            date_default_timezone_set($timezone_string);
        }
        $generate_json = '';
        if (!empty($get_posts)) {
            $generate_info = array();
            foreach ($get_posts as $key => $order_meta_data) {
                $time_block_bg_color = bkx_crud_option_multisite("time_block_bg_color");
                $first_name = $order_meta_data['first_name'];
                $last_name = $order_meta_data['last_name'];
                $booking_date = $order_meta_data['booking_date'];
                $booking_start_date = $order_meta_data['booking_start_date'];
                $booking_end_date = $order_meta_data['booking_end_date'];
                $total_duration = $order_meta_data['total_duration'];
                $base_arr = $order_meta_data['base_arr']['main_obj']->post;
                $service_name = $base_arr->post_title;
                $seat_id = $order_meta_data['seat_id'];
                $seat_colour = get_post_meta($seat_id, 'seat_colour', true);
                $seat_colour = empty($seat_colour) ? $time_block_bg_color : $seat_colour;
                $start_date_obj = new DateTime($booking_start_date);
                $end_date_obj = new DateTime($booking_end_date);
                $formatted_start_date = date_format($start_date_obj, 'Y-m-d') . "T" . date_format($start_date_obj, 'H:i:s');
                $formatted_end_date = date_format($end_date_obj, 'Y-m-d') . "T" . date_format($end_date_obj, 'H:i:s');
                if ($search['type'] == 'monthly') {
                    $generate_info['title'] = $first_name . ' ' . $last_name;
                    $generate_info['start'] = $formatted_start_date;
                    $generate_info['backgroundColor'] = $seat_colour;
                } else {
                    $generate_info['title'] = $first_name . ' ' . $last_name . ' - ( ' . $service_name . ')';
                    $generate_info['start'] = $formatted_start_date;
                    $generate_info['end'] = $formatted_end_date;
                    $generate_info['backgroundColor'] = $seat_colour;
                }
                $generate_info['url'] = admin_url('edit.php?post_type=bkx_booking&view=' . $order_meta_data['booking_record_id'] . '&bkx_name=' . $first_name . ' ' . $last_name . '#post-' . $order_meta_data['booking_record_id']);
                $generate_json .= json_encode($generate_info) . ',';
            }
            $generate_json = rtrim($generate_json, ",");
        }
        return $generate_json;
    }

    /**
     * @param $data
     * @param bool $ajax
     * @return false|string|void
     * @throws Exception
     */
    function MakeBookingProcess( $data , $ajax = true){
        if(empty($data))
            return;
        $result = "";
        $_POST = $data;
        $booking = array();
        $booking['meta_data']['redirect_to'] = "";
        if (isset($_POST['seat_id'], $_POST['base_id'], $_POST['starting_slot'], $_POST['time_option'])) {
            $args['seat_id'] = sanitize_text_field(wp_unslash($_POST['seat_id']));
            $args['base_id'] = sanitize_text_field(wp_unslash($_POST['base_id']));
            $args['extra_ids'] = sanitize_text_field(wp_unslash($_POST['extra_id']));
            $args['service_extend'] = sanitize_text_field(wp_unslash($_POST['service_extend']));
            $args['date'] = sanitize_text_field(wp_unslash($_POST['date']));
            $args['slot'] = sanitize_text_field(wp_unslash($_POST['starting_slot']));
            $args['time'] = isset($_POST['booking_time']) ? sanitize_text_field(wp_unslash($_POST['booking_time'])) : "";
            $args['time_option'] = sanitize_text_field(wp_unslash($_POST['time_option']));
            $args['booking_multi_days'] = wp_unslash($_POST['booking_multi_days']);

            $get_verify_slot = json_decode($this->get_verify_slot($args, false));
            if (!empty($get_verify_slot) && $get_verify_slot->result == 1 || !empty($args['booking_multi_days'])) {
                $booking = $this->generate_order($_POST, null, true);
                //send email that edit booking confirmed
                if (isset($_POST['edit_booking']) && $_POST['edit_booking'] == true) {
                    $this->booking_email($booking['meta_data']['order_id'], 'edit');
                    $this->booking_email($booking['meta_data']['order_id'], 'customer_edit');
                } else {
                    //send email that new booking confirmed
                    $this->booking_email($booking['meta_data']['order_id'], 'pending');
                    $this->booking_email($booking['meta_data']['order_id'], 'customer_pending');
                }

                $is_new = strpos($booking['meta_data']['last_page_url'], "post-new.php");
                $is_edited = (isset($_POST['is_admin_edit']) && $_POST['is_admin_edit'] != "" && $_POST['is_admin_edit'] == true ? 1 : 0);
                $is_customer_edit = (isset($_POST['is_customer_edit']) && $_POST['is_customer_edit'] != "" && $_POST['is_customer_edit'] == true ? 1 : 0);
                if ($is_new !== false || $is_edited == 1) {
                    $booking['meta_data']['redirect_to'] = get_edit_post_link($booking['meta_data']['order_id'], '&');
                }
                if ($is_new !== false || $is_customer_edit == 1) {
                    $return_url = $_POST['last_page_url'];
                    if(isset($_POST['return_id']) && $_POST['return_id']!= ""){
                        $return_url = get_permalink($_POST['return_id']);
                    }
                    $booking['meta_data']['redirect_to'] = $return_url;
                }
            }
            if(empty($args['time']) && $args['time_option'] ==  'H'){
                $result =  __("BTB", 'bookingx');
            }elseif($args['time_option'] ==  'D' &&  empty($args['booking_multi_days']) ){
                $result =  __("BTB", 'bookingx');
            }else{
                $result =  (!empty($booking['meta_data']['order_id']) ? json_encode($booking) : "NORF");
            }
        } else {
            $result =  __("SWR", 'bookingx');
        }
        if($ajax == true){
            echo $result;
        }else{
            return $result;
        }
    }

    function UpdateBookingData($order_id, $post_data)
    {

        $args['seat_id'] = $post_data['seat_id'];
        $args['base_id'] = $post_data['base_id'];
        $args['extra_ids'] = $post_data['extra_id'];
        $args['service_extend'] = $post_data['service_extend'];
        $total_time = $this->booking_form_generate_total_time($args);
        $total_time_formatted = bkx_total_time_of_services_formatted($total_time['in_sec'] / 60);
        $booking_start_date = date("Y-m-d H:i:s", strtotime(sanitize_text_field($post_data['date']) . " " . sanitize_text_field($post_data['booking_time'])));
        $booking_end_date = date("Y-m-d H:i:s", strtotime(sanitize_text_field($post_data['date']) . " " . sanitize_text_field($post_data['booking_time'])) + sanitize_text_field($total_time['in_sec']));

        $return_page_id = !empty($post_data['return_page_id']) ? $post_data['return_page_id'] : "";
        $return_page_url = isset($return_page_id) ? get_permalink($return_page_id) : "";
        $return_url = apply_filters('bkx_return_page_url', $return_page_url);
        $current_user = wp_get_current_user();
        $curr_date = date("Y-m-d H:i:s");
        $order_meta_data = get_post_meta($order_id, 'order_meta_data', true);
        $order_meta_data = !empty($order_meta_data) ? $order_meta_data : array();
        if (!empty($post_data['input_date'])) {
            $booking_start_date = date("Y-m-d H:i:s", strtotime(sanitize_text_field($post_data['input_date']) . " " . sanitize_text_field($post_data['booking_time_from'])));
            $booking_end_date = date("Y-m-d H:i:s", strtotime(sanitize_text_field($post_data['input_date']) . " " . sanitize_text_field($post_data['booking_time_from'])) + sanitize_text_field($post_data['booking_duration_insec']));
        }
        if (isset($_POST['booking_multi_days']) && !empty($post_data['booking_multi_days'])) {

            $booking_multi_days = wp_unslash($post_data['booking_multi_days']);
            $booking_start_date = date('Y-m-d H:i:s', strtotime($booking_multi_days[0]));
            $booking_end_date = date('Y-m-d H:i:s', strtotime($booking_multi_days[1]));
            $base_days = get_post_meta($post_data['base_id'], 'base_day', true);
            $post_data['date'] = date('Y-m-d', strtotime($booking_multi_days[0]));
        }
        $booking_date = !empty($post_data['date']) ? $post_data['date'] : "";
        $booking_time = !empty($total_time) ? $total_time : "";
        $booking_time_from = !empty($post_data['booking_time']) ? $post_data['booking_time'] : "";
        $booking_order_meta_data = array(
            'order_id' => $order_id,
            'booking_date' => sanitize_text_field($booking_date),
            'booking_time' => $booking_time,
            'total_duration' => $total_time_formatted,
            'booking_start_date' => $booking_start_date,
            'booking_end_date' => $booking_end_date,
            'booking_time_from' => sanitize_text_field($booking_time_from),
            'booking_multi_days' => (isset($booking_multi_days) ? $booking_multi_days : ""),
             'updated_by' => $current_user->ID,
            'updated_time' => $curr_date,
            'bkx_return_page_url' => $return_url
        );
        $final_order_data = array_merge($order_meta_data, $booking_order_meta_data);
        wp_update_post(array('ID' => $order_id, 'post_status' => 'bkx-' . apply_filters('bkx_default_order_status', 'pending')));
        return apply_filters('bkx_booking_collection_update_post', $final_order_data, $post_data);
    }

    /**
     * @param $post_data
     * @return mixed|void
     * @throws Exception
     */
    function CreateBookingData($post_data)
    {

        if (empty($post_data))
            return;
        global $current_user;

        $curr_date = date("Y-m-d H:i:s");
        $booking_id = isset($post_data['order_id']) ? $post_data['order_id'] : "";
        $args['seat_id'] = $post_data['seat_id'];
        $args['base_id'] = $post_data['base_id'];
        $args['extra_ids'] = $post_data['extra_id'];
        $args['service_extend'] = $post_data['service_extend'];

        $total_time = $this->booking_form_generate_total_time($args);
        $total_time_formatted = bkx_total_time_of_services_formatted($total_time['in_sec'] / 60);
        $booking_start_date = date("Y-m-d H:i:s", strtotime(sanitize_text_field($post_data['date']) . " " . sanitize_text_field($post_data['booking_time'])));
        $booking_end_date = date("Y-m-d H:i:s", strtotime(sanitize_text_field($post_data['date']) . " " . sanitize_text_field($post_data['booking_time'])) + sanitize_text_field($total_time['in_sec']));
        $post_data['bkx_payment_gateway_method'] = isset($post_data['payment_method'][0]) ? $post_data['payment_method'][0] : 'bkx_gateway_pay_later';

        $booking_set_array = array('seat_id' => sanitize_text_field($post_data['seat_id']), 'base_id' => sanitize_text_field($post_data['base_id']), 'extra_id' => (!empty($post_data['extra_id']) && $post_data['extra_id'] != "None") ? implode(",", $post_data['extra_id']) : "");
        $total_tax = 0;
        $total_price = 0;
        $extended_time = isset($post_data['input_extended_time']) ? sanitize_text_field($post_data['input_extended_time']) : "";
        //$total_price    = bkx_cal_total_price( sanitize_text_field ($post_data['base_id']), $extended_time , implode(",",$post_data['extra_id']) );

        //echo '<pre>',print_r($post_data,1),'</pre>';die;
        $generate_total = $this->booking_form_generate_total($args);
        $total_price = $generate_total['total_price'];
        $bkx_cal_total_tax = bkx_cal_total_tax($total_price);

        $bkx_prices_include_tax = bkx_crud_option_multisite("bkx_prices_include_tax");

        if ($bkx_prices_include_tax == 1) { //Yes, I will enter prices inclusive of tax
            $include_tax = 'minus';
        }
        if ($bkx_prices_include_tax == 0) { //No, I will enter prices exclusive of tax
            $include_tax = 'plus';
        }

        if (!empty($bkx_cal_total_tax)) {
            $total_tax = $bkx_cal_total_tax['total_tax'];
            $tax_rate = $bkx_cal_total_tax['tax_rate'];
            $grand_total = $bkx_cal_total_tax['grand_total'];
            $tax_name = $bkx_cal_total_tax['tax_name'];
            $total_price = $bkx_cal_total_tax['total_price'];

            $total_tax = number_format((float)$total_tax, 2, '.', '');
            $total_price = number_format((float)$total_price, 2, '.', '');
            $grand_total = number_format((float)$grand_total, 2, '.', '');
        } else {
            $total_tax = 0;
            $total_price = number_format((float)$total_price, 2, '.', '');
            $grand_total = number_format((float)$total_price, 2, '.', '');
        }
        $base_time_option = get_post_meta($args['base_id'], 'base_time_option', true);
        if (isset($_POST['booking_multi_days']) && !empty($post_data['booking_multi_days'])) {
            $booking_multi_days = wp_unslash($post_data['booking_multi_days']);
            $booking_start_date = date('Y-m-d H:i:s', strtotime($booking_multi_days[0]));
            $booking_end_date = date('Y-m-d H:i:s', strtotime($booking_multi_days[1]));
            $base_days = get_post_meta($post_data['base_id'], 'base_day', true);
            $post_data['date'] = date('Y-m-d', strtotime($booking_multi_days[0]));
        }

        $arrData = array(
            'seat_id' => sanitize_text_field($post_data['seat_id']),
            'base_id' => sanitize_text_field($post_data['base_id']),
            'addition_ids' => (!empty($post_data['extra_id']) && $post_data['extra_id'] != "None") ? implode(",", $post_data['extra_id']) : "",
            'first_name' => sanitize_text_field($post_data['bkx_first_name']),
            'last_name' => sanitize_text_field($post_data['bkx_last_name']),
            'phone' => sanitize_text_field($post_data['bkx_phone_number']),
            'email' => sanitize_text_field($post_data['bkx_email_address']),
            'street' => (isset($post_data['input_street']) ? sanitize_text_field($post_data['input_street']) : ""),
            'city' => (isset($post_data['input_city']) ? sanitize_text_field($post_data['input_city']) : ""),
            'state' => (isset($post_data['input_state']) ? sanitize_text_field($post_data['input_state']) : ""),
            'postcode' => (isset($post_data['input_postcode']) ? sanitize_text_field($post_data['input_postcode']) : ""),
            'total_price' => $grand_total,
            'total_tax' => $total_tax,
            'tax_rate' => isset($tax_rate) ? $tax_rate : "",
            'include_tax' => $include_tax,
            'total_duration' => $total_time_formatted,
            'payment_method' => sanitize_text_field($post_data['bkx_payment_gateway_method']),
            'payment_status' => 'Not Completed',
            'bkx_payment_gateway_method' => sanitize_text_field($post_data['bkx_payment_gateway_method']),
            'created_date' => $curr_date,
            'created_by' => $current_user->ID,
            'booking_date' => sanitize_text_field($post_data['date']),
            'booking_time' => $total_time,
            'extended_base_time' => (isset($post_data['service_extend']) ? sanitize_text_field($post_data['service_extend']) : 0),
            'booking_start_date' => $booking_start_date,
            'booking_end_date' => $booking_end_date,
            'addedtocalendar' => 0,
            'last_page_url' => sanitize_text_field($post_data['last_page_url']),
            'booking_time_from' => sanitize_text_field($post_data['booking_time']),
            'currency' => bkx_get_current_currency(),
            'currency_option' => bkx_crud_option_multisite('currency_option'),
            'order_id' => isset($booking_id) ? $booking_id : "",
            'base_time_option' => (isset($base_time_option) ? sanitize_text_field($base_time_option) : ""),
            'booking_multi_days' => (isset($booking_multi_days) ? $booking_multi_days : ""),
            'base_days' => (isset($base_days) ? sanitize_text_field($base_days) : ""),
            'update_order_slot' => (isset($post_data['update_order_slot']) ? sanitize_text_field($post_data['update_order_slot']) : "")
        );
        return apply_filters('bkx_booking_collection_posts', $arrData, $post_data);
    }

    /**
     * @param $pieces
     * @param $wp_query
     * @return mixed
     */
    function bkx_comments_clauses($pieces, $wp_query){
        if (is_admin()) {
            $current_screen = get_current_screen();
            if ( isset($current_screen->base) &&  ( 'edit' === $current_screen->base || 'edit' === $current_screen->parent_base) && 'bkx_booking' === $current_screen->post_type
                ||
                ( isset($_POST['action'], $_POST['post_id']) && $_POST['action'] == 'bkx_action_view_summary')) {
                global $wpdb;
                $post_id = $wp_query->query_vars['post_id'];
                $pieces['where'] = "( ( comment_approved = '0' OR comment_approved = '1' ) ) AND comment_post_ID = {$post_id} AND  {$wpdb->prefix}posts.post_type NOT IN ('bkx_seat','bkx_base','bkx_addition') ";
                return $pieces;
            }
        }
        return $pieces;
    }

    /**
     * @return mixed|void
     */
    public function get_calculate_total()
    {
        $total = 0;
        $base_price = 0;
        $extra_price = 0;

        /** Get Base price */
        $BkxBase = $this->BkxBase;
        $base_price = $BkxBase->get_price();

        /** Get Extra service price if selected multiple then calculate base on selection */
        $BkxExtra = $this->BkxExtra;
        if (!empty($BkxExtra)) {
            foreach ($BkxExtra as $key => $ExtraObj) {
                $extra_price += $ExtraObj->get_price();
            }
        }
        $total = $base_price + $extra_price;
        return apply_filters('bkx_booking_calculate_total', $total, $this);
    }

    public function get_step_2_booking_form_details($args)
    {

        if (empty($args['base_id']))
            return;
        $dl_class = "";
        $seat_id = $args['seat_id'];
        $base_id = $args['base_id'];

        $seat_obj = get_post($seat_id);
        $BkxBase = new BkxBase("", $base_id);
        $base_time_option = get_post_meta($base_id, 'base_time_option', true);

        $total_time = $this->booking_form_generate_total_time($args); // In Sec
        $total_time_in_sec = $total_time['in_sec'] / 60;
        $total_time_formatted = bkx_total_time_of_services_formatted($total_time_in_sec, $base_time_option);
        $extra_data = "";

        if (isset($args['extra_ids']) && !empty($args['extra_ids'])) {
            $BkxExtra = new BkxExtra();
            $extra_obj = $BkxExtra->get_extra_by_ids($args['extra_ids'], "string");
            $extra_data = implode(", ", $extra_obj);
        }

        if (is_admin()) {
            $dl_class = "admin";
        }
        $seat_name = apply_filters('bkx_form_step_info_seat', $seat_obj->post_title, $seat_obj );
        $step_2_details = "<dl class='{$dl_class}'>
            <dt>{$this->load_global->seat} name : </dt>
            <dd class=\"seat_name\"> {$seat_name} </dd>
        </dl>";
        $base_name = apply_filters('bkx_form_step_info_base', $BkxBase->get_title(), $BkxBase );
        $step_2_details .= "<dl>
            <dt>{$this->load_global->base} name : </dt>
            <dd class=\"base_name\"> {$base_name} </dd>
        </dl>";
        if (isset($extra_data) && !empty($extra_data)) {
            $step_2_details .= "<dl>
                                    <dt>{$this->load_global->extra} Service : </dt>
                                    <dd class=\"extra_name\"> {$extra_data} </dd>
                                </dl>";
        }
        $step_2_details .= "<dl>
            <dt>Total Time : </dt>
            <dd class=\"total_hours\">{$total_time_formatted}</dd>
        </dl>";

        return $step_2_details;
    }

    public function get_step_3_booking_form_details($args)
    {

        if (empty($args['base_id']))
            return;

        $seat_id = $args['seat_id'];
        $base_id = $args['base_id'];
        $service_extend = $args['service_extend'];
        $booking_date = $args['booking_date']; //September 27, 2018

        if (isset($args['time_option']) && $args['time_option'] == "D") {
            $days_selected = $args['days_selected'];
            $date_format = "";
            if (!empty($days_selected)) {
                $last_key = sizeof($days_selected) - 1;
                $start_date = date('F d, Y', strtotime($days_selected[0]));
                $end_date = date('F d, Y', strtotime($days_selected[$last_key]));
                $date_format = "{$start_date} To {$end_date}";
            }
        } else {
            $date_format = date('F d, Y', strtotime($booking_date));
        }
        $booking_time = !empty($args['booking_time']) ? explode("|", $args['booking_time']) : array();
        $base_time_option = get_post_meta($base_id, 'base_time_option', true);
        $seat_obj = get_post($seat_id);
        $BkxBase = new BkxBase("", $base_id);
        $total_time = $this->booking_form_generate_total_time($args); // In Sec
        $total_time_in_sec = $total_time['in_sec'] / 60;
        $total_time_formatted = bkx_total_time_of_services_formatted($total_time_in_sec, $base_time_option);
        $extra_data = $dl_class = "";
        $dl_class = ((isset($args['is_admin']) && $args['is_admin'] == true) ? "admin" : "");
        if (isset($args['extra_ids']) && !empty($args['extra_ids'])) {
            $BkxExtra = new BkxExtra();
            $extra_obj = $BkxExtra->get_extra_by_ids($args['extra_ids'], "string");
            $extra_data = implode(", ", $extra_obj);
        }
        $seat_name = apply_filters('bkx_form_step_info_seat', $seat_obj->post_title, $seat_obj );
        $step_3_details = "<div class=\"row\"><div class=\"col-sm-12\"><dl class='{$dl_class}'>
            <dt>{$this->load_global->seat} name : </dt>
            <dd class=\"seat_name\"> {$seat_name} </dd>
        </dl>";
        $base_name = apply_filters('bkx_form_step_info_base', $BkxBase->get_title(), $BkxBase );
        $step_3_details .= "<dl class='{$dl_class}'>
            <dt>{$this->load_global->base} name : </dt>
            <dd class=\"base_name\"> {$base_name} </dd>
        </dl>";

        if (isset($service_extend) && $service_extend > 0) {
            $extend_label = ($service_extend > 1) ? "Time's" : "Time";
            $step_3_details .= "<dl class='{$dl_class}'>
                <dt>{$this->load_global->base} Extend : </dt>
                <dd class=\"base_name\"> {$service_extend} {$extend_label} </dd>
            </dl>";
        }

        if (isset($extra_data) && !empty($extra_data)) {
            $step_3_details .= "<dl class='{$dl_class}'>
                                    <dt>{$this->load_global->extra} Service : </dt>
                                    <dd class=\"extra_name\"> {$extra_data} </dd>
                                </dl>";
        }
        $step_3_details .= "<dl class='{$dl_class}'>
            <dt>Total Time : </dt>
            <dd class=\"total_hours\">{$total_time_formatted}</dd>
        </dl></div><div class=\"col-sm-12\">
                <dl class='{$dl_class}'>
                    <dt>Date: </dt>
                    <dd>{$date_format}</dd>
                </dl>";
        if (!empty($booking_time)) {
            $step_3_details .= "<dl class='{$dl_class}'>
                    <dt>Time : </dt>
                    <dd>{$booking_time[0]} - {$booking_time[1]}</dd>
                </dl>
            </div></div>";
        }

        return $step_3_details;
    }

    public function booking_form_generate_total_time($selected_ids)
    {
        if (empty($selected_ids))
            return;
        $base_id = $selected_ids['base_id'];
        $generate_total_time = array();
        $total_time = 0;
        if (!empty($base_id)) {
            $BkxBase = new BkxBase("", $base_id);
            $base_time = $BkxBase->get_time();
            if ($base_time['type'] == 'D') {
                $total_time += $base_time['in_sec'] * 60;
            } else {
                $total_time += $base_time['in_sec'];
            }
        }
        if (!empty($selected_ids['extra_ids'])) {
            $extra_ids = $selected_ids['extra_ids'];
            if (!empty($extra_ids) && $extra_ids != "None") {
                foreach ($extra_ids as $extra_id) {
                    $BkxExtra = new BkxExtra("", $extra_id);
                    $extra_time = $BkxExtra->get_time();
                    if ($extra_time['type'] == 'D') {
                        $total_time += $extra_time['in_sec'] * 60;
                    } else {
                        $total_time += $extra_time['in_sec'];
                    }
                }
            }
        }
        $generate_total_time['in_sec'] = $total_time;
        return $generate_total_time;
    }

    public function booking_form_generate_total($selected_ids)
    {

        if (empty($selected_ids))
            return;

        $base_id = $selected_ids['base_id'];
        $extra_ids = $selected_ids['extra_ids'];
        $service_extend = $selected_ids['service_extend'];
        $generate_total = 0;
        $service_extend_total = 0;
        if (!empty($base_id) && $base_id != "" && $base_id != "NaN") {
            $base_price = get_post_meta($base_id, 'base_price', true);
            if (isset($service_extend) && $service_extend > 0) {
                $service_extend_total = $base_price * $service_extend;
            }
            $generate_total += $base_price;
            $generate_total += $service_extend_total;
            if (!empty($extra_ids)) {
                $BkxExtra = new BkxExtra();
                $extra_total = $BkxExtra->get_total_price_by_ids($extra_ids);
                $generate_total += $extra_total;
            }
        }

        $result['total_price'] = number_format((float)$generate_total, 2, '.', '');

        $total_with_tax = bkx_cal_total_tax($generate_total);
        if (!empty($total_with_tax)) {
            $generate_total = $total_with_tax['grand_total'];
        }

        if (isset($selected_ids['seat_id'])) {
            $seat = sanitize_text_field($selected_ids['seat_id']);
            $objSeat = get_post($seat);
            if (!empty($objSeat) && !is_wp_error($objSeat)) {
                $SeatMetaObj = get_post_custom($objSeat->ID);
                if (!empty($SeatMetaObj) && !is_wp_error($SeatMetaObj)) {
                    $booking_require_prepayment = isset($SeatMetaObj['seatIsPrePayment']) ? esc_attr($SeatMetaObj['seatIsPrePayment'][0]) : "";
                    $deposit_type = isset($SeatMetaObj['seat_payment_option']) ? esc_attr($SeatMetaObj['seat_payment_option'][0]) : ""; // Deposit Type
                    $payment_type = isset($SeatMetaObj['seat_payment_type']) ? esc_attr($SeatMetaObj['seat_payment_type'][0]) : ""; //Payment Type
                    $fixed_amount = isset($SeatMetaObj['seat_amount']) ? esc_attr($SeatMetaObj['seat_amount'][0]) : "";
                    $percentage = isset($SeatMetaObj['seat_percentage']) ? esc_attr($SeatMetaObj['seat_percentage'][0]) : "";
                }
            }
        }

        $booking_require_prepayment = isset($booking_require_prepayment) ? apply_filters('bkx_seat_is_booking_prepayment', $booking_require_prepayment) : "";
        $payment_type = isset($payment_type) ? apply_filters('bxk_booking_prepayment_payment_type', $payment_type) : "";
        $deposit_type = isset($deposit_type) ? apply_filters('bxk_booking_prepayment_deposit_type', $deposit_type) : "";
        $percentage = isset($percentage) ? apply_filters('bxk_booking_prepayment_percentage', $percentage) : "";
        $fixed_amount = isset($fixed_amount) ? apply_filters('bxk_booking_prepayment_fixed_amount', $fixed_amount) : "";
        $note = "";
        if ($booking_require_prepayment == "Y") {
            if ($payment_type == "FP") {
                $deposit_price = $generate_total;
                $note = __('This booking requires full payment to process.', 'bookingx');
            }
            if ($payment_type == "D") {
                if ($deposit_type == "FA") {
                    $deposit_price = bkx_get_formatted_price($fixed_amount);
                    $note = __("This booking requires a {$deposit_price} deposit to process.", 'bookingx');
                } else if ($deposit_type == "P") {
                    $deposit_price = bkx_get_formatted_price(($percentage / 100) * $generate_total);
                    $note = __("This booking requires a {$percentage}% ({$deposit_price}) deposit to process.", 'bookingx');
                }
            }
        } else {
            $deposit_price = 0;
        }

        $result['tax'] = $total_with_tax;
        $result['note'] = $note;
        $result['seat_is_booking_prepayment'] = $booking_require_prepayment;
        $result['deposit_price'] = number_format((float)$deposit_price, 2, '.', '');

        return $result;
    }

    public function get_booking_form_calendar_availability($args)
    {

        if (empty($args['seat_id']))
            return;

        $seat_id = $args['seat_id'];
        $seat_obj = new BkxSeat("", $seat_id);
        $seat_available_months = $seat_obj->get_seat_available_months();
        $seat_available_days_time = $seat_obj->get_seat_available_days();
        $seat_days = get_post_meta($seat_id, "seat_days", true);

        $availability['seat']['months'] = $seat_available_months;
        $availability['seat']['days'] = $seat_days;
        $availability['seat']['day_time'] = $seat_available_days_time;

        $get_service_unavailable = array();
        if (isset($args['base_id']) && $args['base_id'] != "") {
            $base_id = $args['base_id'];
            $BkxBase = new BkxBase("", $base_id);
            $get_service_unavailable = $BkxBase->get_service_unavailable();
        }
        $availability['base']['unavailable'] = $get_service_unavailable;

        $extra_unavailable = array();
        if (!empty($args['extra_ids']) && $args['extra_ids'] != "None") {
            $extra_ids = $args['extra_ids'];
            foreach ($extra_ids as $extra_id) {
                $BkxExtra = new BkxExtra("", $extra_id);
                $extra_unavailable[$extra_id] = $BkxExtra->get_addition_unavailable();
            }
        }
        $availability['extra']['unavailable'] = $extra_unavailable;
        $base_time_option = get_post_meta($base_id, 'base_time_option', true);
        $base_day = get_post_meta($base_id, 'base_day', true);
        $booked_days = array();
        $days_selected = array();
        if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0) {
            $search['by'] = 'future';
            $search['seat_id'] = $seat_id;
            $search['booking_date'] = date('Y-m-d');
            $booked_data = $this->GetBookedRecords($search);
            //echo '<pre>',print_r($booked_data,1),'</pre>';
            if (!empty($booked_data)) {
                foreach ($booked_data as $booking) {
                    $booking_id = $booking['booking_record_id'];
                    $base_time_option = get_post_meta($booking_id, 'base_time_option', true);
                    $base_time_option = (isset($base_time_option) && $base_time_option != "") ? $base_time_option : "H";
                    if (isset($base_time_option) && $base_time_option == 'D') {
                        $days_booked = get_post_meta($booking_id, 'booking_multi_days', true);
                        if (!empty($days_booked)) {
                            $last_key = sizeof($days_booked) - 1;
                            $start_date = date('F d, Y', strtotime($days_booked[0]));
                            $end_date = date('F d, Y', strtotime($days_booked[$last_key]));
                            $days_selected = bkx_getDatesFromRange($start_date, $end_date, 'm/d/Y');
                        }
                    }
                    $date = new DateTime($booking['booking_date']);
                    $now = new DateTime();
                    if ($date >= $now) {
                        $booked_days[] = date('m/d/Y', strtotime($booking['booking_date']));
                    }
                    $booked_days = array_merge($booked_days, $days_selected);
                }
                $booked_days = array_unique($booked_days);
            }
        }
        //echo '<pre>',print_r($seat_days,1),'</pre>';
        //echo '<pre>',print_r($booked_days,1),'</pre>';
        $unavailable_days = $this->get_all_unavailable_days($availability);
        $availability['unavailable_days'] = array_merge($unavailable_days, $booked_days);
        $availability['seat_id'] = $args['seat_id'];
        $availability['base_id'] = $args['base_id'];
        $availability['extra_ids'] = !empty($args['extra_ids']) ? $args['extra_ids'] : '';

        //echo '<pre>',print_r($availability,1),'</pre>';
        return apply_filters('bkx_calender_unavailable_days', $availability);
    }

    public function get_all_unavailable_days($availability)
    {
        $days = array();
        $extra_days = array();
        if (!empty($availability)) {
            $availability_base = $availability['base']['unavailable'];
            $availability_extra = $availability['extra']['unavailable'];
            $base_range = !empty($availability_base) ? bkx_getDatesFromRange($availability_base['from'], $availability_base['till']) : array();
            if (!empty($availability_extra)) {
                foreach ($availability_extra as $extra) {
                    if (!empty($extra)) {
                        $extra_range = bkx_getDatesFromRange($extra['from'], $extra['till']);
                        if (!empty($extra_range)) {
                            foreach ($extra_range as $extra_date) {
                                array_push($extra_days, $extra_date);
                            }
                        }
                    }
                }
            }
            $days = array_filter(array_unique(array_merge($base_range, $extra_days)));
        }
        return apply_filters('bkx_get_all_unavailable_days', $days);
    }

    public function get_display_availability_slots($args)
    {

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $booking_default_date = date("d/m/Y");
        $args['booking_date'] = isset($args['booking_date']) ? $args['booking_date'] : $booking_default_date;
        $selected_date = date("m/d/Y", strtotime($args['booking_date']));
        $availability = $this->get_booking_form_calendar_availability($args);
        $availability_slot_flag = true;
        if (!empty($availability['unavailable_days']) && in_array($selected_date, $availability['unavailable_days'])) {
            $availability_slot_flag = false;
        }

        if ($availability_slot_flag == false)
            return "None";

        $base_alias = bkx_crud_option_multisite("bkx_alias_base");
        $order_statuses = array('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed', 'bkx-ack');
        $current_order_id = isset($_POST['order_id']) && $_POST['order_id'] != "" ? $_POST['order_id'] : 0;
        $checked_booked_slots = array();

        if (isset($args['booking_date'])) {
            $base_id = sanitize_text_field($args['base_id']);
            $booking_date = sanitize_text_field($args['booking_date']);
            if (isset($args['seat_id'])
                && $args['seat_id'] == 'any' && $base_id != ''
                && bkx_crud_option_multisite('enable_any_seat') == 1
                && bkx_crud_option_multisite('select_default_seat') != ''):
                $_enable_any_seat_selected_id = (int)bkx_crud_option_multisite('select_default_seat'); // Default Seat id
                $search = array('booking_date' => $booking_date,
                    'service_id' => $base_id,
                    'status' => $order_statuses,
                    'seat_id' => 'any', 'display' => 1);

                $BkxBooking = new BkxBooking();
                $BookingTime = $BkxBooking->GetBookedRecords($search);
                $BkxBase = new BkxBase('', $base_id);
                $seat_by_base = $BkxBase->get_seat_by_base();

                if (!empty($seat_by_base)) {
                    $total_free_seat_all = sizeof($seat_by_base);
                    $free_seat_all_arr = $seat_by_base;
                }
                if (!empty($free_seat_all_arr)):
                    foreach ($free_seat_all_arr as $seat_id) {
                        $get_range_by_seat[$seat_id] = bkx_get_range($booking_date, $seat_id);
                    }
                endif;

                if (!empty($BookingTime)):
                    foreach ($BookingTime as $objBooking):
                        $slot_id = $objBooking['full_day'];
                        $duration = ($objBooking['booking_time']) / 60;
                        $seat_slots = intval($duration / 15);
                        $_SESSION['_display_seat_slots'] = $seat_slots;
                        $booking_slot_range = array();
                        if ($seat_slots > 1) {
                            for ($i = 0; $i < $seat_slots; $i++) {
                                $slot_id = $objBooking['full_day'];
                                $booking_slot_range[] = $slot_id + $i;
                            }
                        } else {
                            $booking_slot_range[] = $slot_id;
                        }
                        $booking_seat_id = $objBooking['seat_id'];
                        $get_booked_range[$slot_id][$booking_seat_id] = $booking_slot_range;
                    endforeach;
                endif;
                if (!empty($get_booked_range)):
                    foreach ($get_booked_range as $slot_id => $get_booked_slot) {
                        if (function_exists('bkx_get_booked_slot')):
                            $get_data[] = bkx_get_booked_slot($slot_id, $get_booked_range, $free_seat_all_arr, $total_free_seat_all);
                        endif;
                    }
                    if (!empty($get_data)):
                        foreach ($get_data as $slots_booked_data) {
                            if (!empty($slots_booked_data['slots_blocked'])) {
                                foreach ($slots_booked_data as $slot_ids) {
                                    foreach ($slot_ids as $_slot_id) {
                                        $slots_are_booked[] = $_slot_id;
                                        $slots_are_booked = array_unique($slots_are_booked);
                                    }
                                }
                            }
                        }
                    endif;
                endif;
            else:
                $seat_id = isset($args['seat_id']) ? sanitize_text_field($args['seat_id']) : "";
                $search = array(
                    'booking_date' => $booking_date,
                    'service_id' => $base_id,
                    'status' => $order_statuses,
                    'seat_id' => $seat_id,
                    'display' => 1
                );
                //echo '<pre>', print_r($search, 1), '</pre>';
                $BkxBooking = new BkxBooking();
                $BookingTime = $BkxBooking->GetBookedRecords($search);
                if (!empty($BookingTime)) {
                    if (isset($BookingTime[0]->full_day)) {
                        $full_day = $BookingTime[0]->full_day;
                    }
                }
            endif;
        }
        //$BkxBase = new BkxBase('', sanitize_text_field($_POST['service_id']));
        //$base_meta_data = $BkxBase->meta_data;
        //$base_day = $base_meta_data['base_day'][0];
        $booking_slot_arr = array();
        $booking_slot = array();
        $BookingTime = !empty($BookingTime) || isset($BookingTime) ? $BookingTime : "";
        $BookingTime = apply_filters('bkx_customise_display_booking_data', $BookingTime);

        if (!empty($BookingTime)) {
            foreach ($BookingTime as $temp) {
                //echo '<pre>', print_r($temp, 1), '</pre>';
                $PostObj = get_post($temp['order_id']);
                /**
                 * For  : Add Any Seat functionality.*/
                if (bkx_crud_option_multisite('enable_any_seat') == 1
                    && bkx_crud_option_multisite('select_default_seat') != ''
                    && sanitize_text_field($_POST['seat_id']) == 'any'):
                    if (!empty($slots_are_booked)):
                        $booking_slot_arr = $slots_are_booked;
                    endif;
                else:

                    /* Reason : To get the multiple booking slots. */
                    $duration = ($temp['booking_time']['in_sec']) / 60;
                    $seat_slots = intval($duration / 15);
                    $seat_slots = isset($seat_slots) && $seat_slots > 0 && $seat_slots > 100 ? 100 : $seat_slots;
                    /**
                     * Updated By  : Divyang Parekh
                     * For  : Add Any Seat functionality.
                     */
                    if ($seat_slots > 1) {
                        for ($i = 0; $i < $seat_slots + 1; $i++) {
                            if (isset($temp['full_day'])) {
                                $booking_slot_arr[] = (int)$temp['full_day'] + $i;
                                $checked_booked_slots[] = array('created_by' => $PostObj->post_author, 'slot_id' => $temp['full_day'] + $i, 'order_id' => $temp['order_id']);
                            }
                        }
                    } else {
                        if (isset($temp['full_day'])) {
                            $booking_slot_arr[] = (int)$temp['full_day'];
                            $checked_booked_slots[] = array('created_by' => $PostObj->post_author, 'slot_id' => $temp['full_day'], 'order_id' => $temp['order_id']);
                        }
                    }
                endif;
            }
            foreach ($booking_slot_arr as $slot) {
                if ($slot != "") {
                    array_push($booking_slot, $slot);
                }
            }
        }
        //echo '<pre>', print_r($booking_slot_arr, 1), '</pre>';
        $booking_date = sanitize_text_field($args['booking_date']);
        $range = bkx_get_range($booking_date, sanitize_text_field($args['seat_id']));
        //echo '<pre>', print_r($range, 1), '</pre>';
        //start and end of hours of a day

        $start = 0;
        $end = 24;
        $first = $start * 3600; // Timestamp of the first cell
        $last = $end * 3600; // Timestamp of the last cell

        $availability_slots['first'] = $first; // Timestamp of the first cell
        $availability_slots['last'] = $last; // Timestamp of the last cell
        $availability_slots['range'] = $range;
        $availability_slots['booked_slots'] = array_unique($booking_slot);
        $availability_slots['booked_slots_in_details'] = $checked_booked_slots;
        return $availability_slots;
    }

    public function get_verify_slot($args, $echo = true)
    {

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $start = sanitize_text_field($args['slot']);
        $selected_time = sanitize_text_field($args['time']);
        $args['booking_date'] = sanitize_text_field($args['date']);
        $booking_duration = $this->booking_form_generate_total_time($args); // In Sec
        $availability_slots = $this->get_display_availability_slots($args);

        $range_total = sizeof($availability_slots['range']);
        $booking_duration = $booking_duration['in_sec'];
        $booking_date = sanitize_text_field($args['date']);
        $order_statuses = array('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed');
        $update_order_slot = isset($args['update_order_slot']) ? sanitize_text_field($args['update_order_slot']) : "";
        $selected_order_id = isset($args['selected_order_id']) ? sanitize_text_field($args['selected_order_id']) : "";
        $current_order_id = isset($args['order_id']) ? sanitize_text_field($args['order_id']) : "";

        if (isset($args['seat_id']) && $args['seat_id'] == 'any'
            && bkx_crud_option_multisite('enable_any_seat') == 1
            && bkx_crud_option_multisite('select_default_seat') != ''):
            $base_id = $_SESSION['_session_base_id'];
            //get current booking start time slot
            $bookinghour = explode(':', $selected_time);
            $hours_temp = $bookinghour[0];
            $minutes_temp = $bookinghour[1];
            if (intval($minutes_temp) == 0) {
                $counter = 1;
            } else if (intval($minutes_temp) == 15) {
                $counter = 2;
            } else if (intval($minutes_temp) == 30) {
                $counter = 3;
            } else if (intval($minutes_temp) == 45) {
                $counter = 4;
            }
            $startingSlotNumber = intval($hours_temp) * 4 + $counter;

            $_enable_any_seat_selected_id = (int)bkx_crud_option_multisite('select_default_seat'); // Default Seat id
            $search = array('booking_date' => $booking_date, 'service_id' => $base_id, 'seat_id' => 'any', 'status' => $order_statuses, 'display' => 1);
            $BkxBooking = new BkxBooking();
            $objBookigntime = $BkxBooking->GetBookedRecords($search);

            if (!empty($objBookigntime)):
                foreach ($objBookigntime as $objBooknig):
                    $slot_id = $objBooknig['full_day'];
                    $duration = ($objBooknig['booking_time']) / 60;
                    $seatslots = intval($duration / 15);
                    $_SESSION['_seatslots'] = $seatslots;
                    $booking_slot_range = array();
                    if ($seatslots > 1) {
                        for ($i = 0; $i < $seatslots; $i++) {
                            $slot_id = $objBooknig['full_day'];
                            $booking_slot_range[] = $slot_id + $i;
                            $checked_booked_slots[] = array('slot_id' => $objBooknig['full_day'] + $i, 'order_id' => $objBooknig['booking_record_id']);
                        }
                    } else {
                        $booking_slot_range[] = $slot_id;
                        $checked_booked_slots[] = array('slot_id' => $objBooknig['full_day'], 'order_id' => $objBooknig['booking_record_id']);
                    }
                    $booking_seat_id = $objBooknig['seat_id'];
                    $get_booked_range[$slot_id][$booking_seat_id] = $booking_slot_range;
                endforeach;

                if ($_SESSION['_seatslots'] > 1) {
                    for ($i = 0; $i < $_SESSION['_seatslots']; $i++) {
                        $find_slot_value_in_booked_array[] = ($startingSlotNumber + 1) + $i;
                    }
                } else {
                    $find_slot_value_in_booked_array[] = $startingSlotNumber + 1;
                }

                if (!empty($get_booked_range)):
                    //$slot_id = $startingSlotNumber + 1;
                    foreach ($get_booked_range as $slot_id => $get_booked_slot) {
                        //$free_seat_all_arr = $get_free_seats;
                        $send_seat_block_data = array();
                        $slots_blocked = array();
                        $slot_id = $startingSlotNumber + 1;
                        if (!empty($get_booked_slot)) :
                            foreach ($get_booked_slot as $seat_id => $booked_slot) {
                                foreach ($find_slot_value_in_booked_array as $_find_slot) {
                                    if (in_array($_find_slot, $booked_slot)) {
                                        $total_booked_slot_count = sizeof($booked_slot);
                                        $now_booked_slots_is[] = $booked_slot;
                                        $now_booked_slots_with_seats[$_find_slot][] = $seat_id;
                                    }
                                }
                            }
                        endif;
                    }

                    if (!empty($now_booked_slots_with_seats)) {
                        foreach ($now_booked_slots_with_seats as $booked_slot_id => $booked_seats) {
                            foreach ($booked_seats as $now_seat_booked) {
                                $booked_all_seat_id_arr[] = $now_seat_booked;
                                $booked_all_seat_id_arr = array_unique($booked_all_seat_id_arr);
                            }
                        }
                    }

                    $BkxBase = new BkxBase('', $base_id);
                    $seat_by_base = $BkxBase->get_seat_by_base();

                    if (!empty($seat_by_base)) {
                        $total_free_seat_all = sizeof($seat_by_base);
                        $free_seat_all_arr = $seat_by_base;
                    }

                    if (!empty($free_seat_all_arr)) :
                        foreach ($free_seat_all_arr as $get_seat_id) :
                            if (!in_array($get_seat_id, $booked_all_seat_id_arr)):
                                $total_frees_seats[] = $get_seat_id;
                            endif;
                        endforeach;
                    endif;
                    if (!empty($total_frees_seats) && sizeof($total_frees_seats) > 1) {
                        if (in_array($_enable_any_seat_selected_id, $total_frees_seats)):
                            $free_seat_id = $_enable_any_seat_selected_id;
                        else :
                            $seat_id_key = array_rand($total_frees_seats);
                            $free_seat_id = $total_frees_seats[$seat_id_key];
                        endif;
                    } else {
                        $free_seat_id = $total_frees_seats[0];
                    }

                /**
                 * Find Seat where other slot are available
                 */

                else:

                    $BkxBase = new BkxBase('', $base_id);
                    $seat_by_base = $BkxBase->get_seat_by_base();

                    if (!empty($seat_by_base)) {
                        $total_free_seat_all = sizeof($seat_by_base);
                        $free_seat_all_arr = $seat_by_base;
                    }
                    if (!empty($free_seat_all_arr) && sizeof($free_seat_all_arr) > 1) {
                        if (in_array($_enable_any_seat_selected_id, $free_seat_all_arr)):
                            $free_seat_id = $_enable_any_seat_selected_id;
                        else :
                            $seat_id_key = array_rand($free_seat_all_arr);
                            $free_seat_id = $free_seat_all_arr[$seat_id_key];
                        endif;
                    } else {
                        $free_seat_id = $free_seat_all_arr[0];
                    }
                endif;
            else:

                $BkxBase = new BkxBase('', $base_id);
                $seat_by_base = $BkxBase->get_seat_by_base();

                if (!empty($seat_by_base)) {
                    $total_free_seat_all = sizeof($seat_by_base);
                    $free_seat_all_arr = $seat_by_base;
                }

                if (!empty($free_seat_all_arr) && sizeof($free_seat_all_arr) > 1) {

                    if (in_array($_enable_any_seat_selected_id, $free_seat_all_arr)):
                        $free_seat_id = $_enable_any_seat_selected_id;
                    else :
                        $seat_id_key = array_rand($free_seat_all_arr);
                        $free_seat_id = $free_seat_all_arr[$seat_id_key];
                    endif;
                } else {
                    $free_seat_id = $free_seat_all_arr[0];
                }
            endif;
            $_SESSION['free_seat_id'] = $free_seat_id;
            $search = array('booking_date' => $booking_date,
                'seat_id' => $free_seat_id,
                'status' => $order_statuses,
                'free_seat_id' => $free_seat_id,
                'display' => 1
            );
            $BkxBooking = new BkxBooking();
            $resBookingTime = $BkxBooking->GetBookedRecords($search);
        else:
            $search = array('booking_date' => $booking_date,
                'status' => $order_statuses,
                'seat_id' => sanitize_text_field($args['seat_id']),
                'display' => 1
            );
            $BkxBooking = new BkxBooking();
            $resBookingTime = $BkxBooking->GetBookedRecords($search);
        endif;
        $bookingSlot = "";
        $arrTempExplode = array();
        $resBookingTime = apply_filters('bkx_customise_check_booking_data', $resBookingTime);
        $order_id_by_date = array();
        $base_id = sanitize_text_field($args['base_id']);
        $base_time_option = get_post_meta($base_id, 'base_time_option', true);
        $base_day = get_post_meta($base_id, 'base_day', true);

        if (!empty($resBookingTime)) {
            foreach ($resBookingTime as $temp) {
                $order_id_by_date[] = $temp['booking_record_id'];
                if (isset($current_order_id) && $current_order_id != "" && $current_order_id != $temp['booking_record_id']) {
                    $bookingSlot .= $temp['full_day'];
                }
            }
            $arrTempExplode = explode(',', $bookingSlot);
            $arrTempExplode = array_unique($arrTempExplode);
        }

        //get current booking start time slot
        $startingSlotNumber = GenerateSlotByTime($selected_time);
        $res = 1;
        $numberOfSlotsToBeBooked = 0;


        /**
         * Return false if the start time exist in the booked slots
         */
        if (in_array($startingSlotNumber, $arrTempExplode) && ($update_order_slot == 0 || $update_order_slot == '')) {
            $res = 0;
        } else {
            $bookingDurationMinutes = ($booking_duration / 60);
            $numberOfSlotsToBeBooked = ($bookingDurationMinutes / 15);
            $lastSlotNumber = $startingSlotNumber + $numberOfSlotsToBeBooked;

            if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0) {
                $booking_duration = ($range_total * $base_day * 15);
                $numberOfSlotsToBeBooked = ($booking_duration / 15);
                $lastSlotNumber = $startingSlotNumber + $numberOfSlotsToBeBooked;
                //echo " STart Slot :$startingSlotNumber AND  BD : $booking_duration AND NSBOOKED : $numberOfSlotsToBeBooked LAst slot : $lastSlotNumber";
            }

            if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0 && $base_day < 2) {
                $oneDayBooking = 1; ////for Single Day booking
            }

            if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0 && $base_day > 1) {
                $oneDayBooking = 0; //for Multi Day booking
            }

            if (isset($oneDayBooking) && $oneDayBooking == 1) {
                $arrBookingSlot = range($startingSlotNumber, $lastSlotNumber);
                foreach ($arrBookingSlot as $temp) {
                    if (in_array($temp, $arrTempExplode) && ($update_order_slot == 0 || $update_order_slot == '')) {
                        $res = 0;
                        continue;
                    }
                }
            } else {
                //first check for current day only
                $lastSlotNumberTemp = $range_total;
                $bookedInCurrentDay = ($range_total - $startingSlotNumber);
                $numberOfSlotsRemaining = ($numberOfSlotsToBeBooked - $bookedInCurrentDay);
                //for current day slots
                $arrBookingSlot = range($startingSlotNumber, $lastSlotNumberTemp);
                foreach ($arrBookingSlot as $temp) {
                    if (in_array($temp, $arrTempExplode)) {
                        $res = 0;
                        continue;
                    }
                }
                //for remaining slots
                if ($res == 0) {

                } else if ($res == 1) {

                    $totalDays = floor($numberOfSlotsRemaining / $range_total); //to find number of days
                    $totalDaysMod = $numberOfSlotsRemaining % $range_total; //to find if extend to next day
                    if ($totalDaysMod > 0) //if goes to next day add 1 to the days value
                    {
                        $totalDays = $totalDays + 1;
                    }

                    for ($i = 1; $i <= $totalDays; $i++) {
                        if ($numberOfSlotsRemaining > 0) //to prevent slot to be negative
                        {
                            $nextDate = date('m/d/Y', strtotime("+$i day", strtotime($booking_date)));

                            $search = array('booking_date' => $nextDate, 'seat_id' => sanitize_text_field($args['seat_id']), 'display' => 1);
                            $BkxBooking = new BkxBooking();
                            $resBookingTimeNext = $BkxBooking->get_order_time_data('', $search);

                            $bookingSlotNext = "";
                            $arrTempExplodeNext = array();
                            if (!empty($resBookingTimeNext)) {
                                foreach ($resBookingTimeNext as $temp) {
                                    $bookingSlotNext .= $temp['full_day'];
                                }
                                $arrTempExplodeNext = explode(',', $bookingSlotNext);
                                $arrTempExplodeNext = array_unique($arrTempExplodeNext);

                            }

                            $availableSlots = range($start, $range_total);
                            $lastSlotRequired = 0;
                            if ($numberOfSlotsRemaining > $range_total) {
                                $lastSlotRequired = $range_total;
                            } else {
                                $lastSlotRequired = $numberOfSlotsRemaining;
                            }
                            $requiredSlots = range($start, $lastSlotRequired);
                            if (!empty($requiredSlots)) {
                                foreach ($requiredSlots as $temp) {
                                    if (in_array($temp, $arrTempExplodeNext)) {
                                        $res = 0;
                                        continue;
                                    }
                                }
                            }
                            $numberOfSlotsRemaining = $numberOfSlotsRemaining - $range_total;//get remaining slots after current loop
                        }
                    }
                }
            }
        }
        //echo $startingSlotNumber;
        $get_require_free_range = range($startingSlotNumber, $lastSlotNumber);
        //echo '<pre>',print_r($availability_slots,1),'</pre>';die;
        if (!empty($get_require_free_range)) {
            foreach ($get_require_free_range as $req_slot) {
                $res = (in_array($req_slot, $availability_slots['booked_slots'])) ? 0 : 1;
                if ($res == 0)
                    break;
            }
        }
        if ($res == 1) {
            $res = (in_array($lastSlotNumber, $availability_slots['range'])) ? 1 : 0;
        }
        $arr_output['result'] = $res;
        $arr_output['no_of_slots'] = $numberOfSlotsToBeBooked;
        $arr_output['starting_slot'] = $startingSlotNumber;
        $arr_output['booking_date'] = $booking_date;
        $arr_output['order_id_by_date'] = $order_id_by_date;
        $arr_output['selected_order_id'] = $selected_order_id;
        $arr_output['current_order_id'] = $current_order_id;
        $output = ($res == 1) ? json_encode($arr_output) : "NORF";
        if ($echo == true) {
            echo $output;
        } else {
            return $output;
        }
        wp_die();

    }

    public function display_availability_slots_html($args){

        $base_time_option = get_post_meta($args['base_id'], 'base_time_option', true);
        $base_day = get_post_meta($args['base_id'], 'base_day', true);
        $availability_slots = "";
        $results = "";
        $error = "";
        $type = "";
        if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0) {
            $type = "days";
            if (in_array(0, $args['allowed_day_book'])) {
                $availability_slots = "None";
            } else {
                $results = $args['days_range'];
            }
        } else {
            $args['allowed_day_book'][] = 0;
            $availability_slots = $this->get_display_availability_slots($args);
        }

        $day_style_header = "";
        if ($this->load_global->booking_style == 'day_style' && in_array(0, $args['allowed_day_book'])) {
            $day_style_header = '<thead><tr><th colspan="4">' . date('F d, Y, l', strtotime($args['booking_date'])) . '</th></tr></thead>';
        }
        if ($availability_slots == "None") {
            $results = "<tr><td colspan=\"4\"><a href=\"javascript:void(0);\" class=\"disabled\">{$this->load_global->disable_slots_note}</a></td></tr>";
            if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0) {
                $error = $this->load_global->disable_slots_note;
            }
        } else if (!empty($availability_slots)) {
            $step = 15 * 60; // Timestamp increase interval to one cell ahead
            $counter = 1;
            $first = $availability_slots['first'];
            $last = $availability_slots['last'];
            $range = $availability_slots['range'];
            $booked_slots = $availability_slots['booked_slots'];
            $booked_slots_in_details = $availability_slots['booked_slots_in_details'];
            $columns = 4;
            $booked_day_dates = $this->find_booked_dates_in_days($args);
            //echo '<pre>',print_r($booked_slots_in_details,1),'</pre>';
            $booked_slots = apply_filters('bookingx_booked_slots', $booked_slots, $args);
            if (!empty($range)) {
                for ($cell_start = $first; $cell_start < $last; $cell_start = $cell_start + $step) {
                    if (in_array($counter, $range)) {
                        if ($counter % $columns == 1) {
                            $results .= "<tr>";
                        }
                        if (in_array($counter, $booked_slots)) {
                            $results .= "<td> <a href=\"javascript:void(0);\" class=\"disabled\">" . bkx_secs2hours($cell_start) . "</a></td>";
                        } else {
                            if (!empty($booked_day_dates)) {
                                if (in_array($args['booking_date'], $booked_day_dates)) {
                                    $results .= "<td> <a href=\"javascript:void(0);\" class=\"disabled\">" . bkx_secs2hours($cell_start) . "</a></td>";
                                } else {
                                    $results .= "<td> <a href=\"javascript:void(0);\" class=\"available\" data-verify='" . $args['booking_date'] . "-" . $counter . "' data-date='" . $args['booking_date'] . "' data-time='" . bkx_secs2hours($cell_start) . "' data-slot='" . $counter . "'>" . bkx_secs2hours($cell_start) . "</a></td>";
                                }
                            } else {
                                $results .= "<td> <a href=\"javascript:void(0);\" class=\"available\" data-verify='" . $args['booking_date'] . "-" . $counter . "' data-date='" . $args['booking_date'] . "' data-time='" . bkx_secs2hours($cell_start) . "' data-slot='" . $counter . "'>" . bkx_secs2hours($cell_start) . "</a></td>";
                            }
                        }
                        if ($counter % $columns == 0) {
                            $results .= "</tr>";
                        }
                    }
                    $counter = $counter + 1;
                }
            } else {
                $results = "<tr><td colspan=\"4\"><a href=\"javascript:void(0);\" class=\"disabled\">{$this->load_global->disable_slots_note}</a></td></tr>";
            }
        }
        if (isset($base_time_option) && $base_time_option == 'D' && isset($base_day) && $base_day > 0) {
            $results = isset($error) && $error != "" ? "" : $results;
            echo json_encode(array('data' => $results, 'error' => $error, 'type' => $type));
        } else {
            echo $day_style_header . $results;
        }
    }

    public function find_booked_dates_in_days($args)
    {
        $booked_dates = array();
        $search['by'] = 'future';
        $search['seat_id'] = $args['seat_id'];
        $search['booking_date'] = date('Y-m-d');
        $booked_records = $this->GetBookedRecords($search);
        if (!empty($booked_records)) {
            foreach ($booked_records as $booked) {
                if (!empty($booked['booking_multi_days'])) {
                    $booked_dates = array_merge($booked_dates, $booked['booking_multi_days']);
                }
            }
        }
        return $booked_dates;
    }

    public function booking_form_generate_total_formatted($selected_ids){
        $result = $this->booking_form_generate_total($selected_ids);
        $result['currency_name'] = $this->load_global->currency_name;
        $result['currency_sym'] = $this->load_global->currency_sym;
        $grand_total = $result['total_price'];
        if (!empty($result['tax'])) {
            $grand_total = $result['tax']['grand_total'];
            $result['total_tax_formatted'] = "{$result['currency_name']}{$result['currency_sym']}{$result['tax']['total_tax']}";
        }
        $short_currency_name = substr($result['currency_name'], 0, 1);
        $result['total_price_formatted'] = "{$result['currency_name']}{$result['currency_sym']}{$result['total_price']}";
        $result['grand_total_formatted'] = "{$short_currency_name}{$result['currency_sym']}{$grand_total}";

        $result['deposit_note'] = (isset($result['note']) && $result['note']!="") ? sprintf("<div class=\"row\"><div class=\"col-lg-12\"> <strong> Note : </strong> %s</div></div>", $result['note']) : "";


        return $result;
    }

    /**
     * @param $form_post_data
     * @param null $order_id
     * @param bool $send_mail
     * @return array|mixed|void
     * @throws Exception
     */
    public function generate_order($form_post_data, $order_id = null, $send_mail = true)
    {
        if (empty($form_post_data))
            return;

        global $bkx_booking_data, $wpdb, $current_user;

        if (isset($form_post_data['order_id']) && $form_post_data['order_id'] != "" && $form_post_data['order_id'] != 0) {
            $post_data = $this->UpdateBookingData($form_post_data['order_id'], $form_post_data);
        } else {
            $post_data = $this->CreateBookingData($form_post_data);
        }
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        if (isset($post_data['order_id']) && $post_data['order_id'] != '' && $post_data['order_id'] > 0) {
            $order_id = $post_data['order_id'];
            $BkxBookingObj = new BkxBooking('', $order_id);
            $BkxBookingObj->add_order_note(sprintf(__('Booking has been successfully updated..', 'bookingx'), ''), 0, null, $order_id);
            //send email that booking confirmed
            /*$post_status = 'bkx-' . apply_filters( 'bkx_default_order_status', 'pending');
            do_action( 'bkx_order_edit_status', $order_id, $post_status);*/

            //do_action( 'bkx_order_edit_status',$order_id, 'customer-' . apply_filters( 'bkx_default_order_status', 'pending') );
            //do_action( 'bkx_order_edit_status',$order_id, apply_filters( 'bkx_default_order_status', 'pending') );
        }

        if (isset($post_data['update_order_slot']) && $post_data['update_order_slot'] != '') {
            $order_id = $post_data['update_order_slot'];
        }

        $bkx_enable_customer_dashboard = bkx_crud_option_multisite('bkx_enable_customer_dashboard');
        // If 1 means enable to create customer

        if (isset($bkx_enable_customer_dashboard) == 1 && !is_user_logged_in()) {
            $user_id = BkxBooking::create_new_user($post_data);
        }else{
            $user_id = BkxBooking::update_new_user( $post_data  );
        }

        $order_data['post_status'] = 'bkx-' . apply_filters('bkx_default_order_status', 'pending');

        if (!isset($order_id) || $order_id == '' || $order_id == 0) {
            $order_data['post_type'] = $this->post_type;
            $order_data['ping_status'] = 'closed';
            $order_data['post_password'] = uniqid('order_');
            $order_data['post_title'] = sprintf(__('Order &ndash; %s', 'bookingx'), strftime(_x('%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'bookingx')));

            $post_date = current_time('mysql');
            $post_date_gmt = get_gmt_from_date($post_date);
            // Insert the booking into the database
            $wpdb->query($wpdb->prepare(
                "
                                    INSERT INTO $wpdb->posts
                                    ( post_type, post_date, post_date_gmt, post_author, post_status, ping_status, post_password, post_title, post_name)
                                    VALUES ( %s, %s, %s ,%s, %s, %s ,%s, %s, %s)
                                ",
                $this->post_type,
                $post_date,
                $post_date_gmt,
                $current_user->ID,
                'bkx-' . apply_filters('bkx_default_order_status', 'pending'),
                'closed',
                uniqid('order_'),
                sprintf(__('Order &ndash; %s', 'bookingx'), strftime(_x('%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'bookingx'))),
                sanitize_title($order_data['post_title'])
            )
            );
            $order_id = $wpdb->insert_id;
            $BkxBookingObj = new BkxBooking('', $order_id);
            $BkxBookingObj->add_order_note(sprintf(__('Successfully Initial # %1$s Order Created..', 'bookingx'), $order_id), 0);
        }
        $post_data['post_status'] = $order_data['post_status'];

        if (empty($order_id))
            return;

        $post_data['order_id'] = $order_id;
        $GLOBALS['bkx_booking_data'] = $post_data;

        if (is_multisite()):
            restore_current_blog();
        endif;
        //Update order meta
        return $this->generate_order_meta($post_data, $send_mail);
    }

    /**
     * @param $post_data
     * @return array|mixed|void
     */
    public function generate_order_meta($post_data, $send_mail = true)
    {
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $order_id = $post_data['order_id']; // Parent Id

        global $bkx_booking_data;

        if (empty($order_id))
            return;

        if (!empty($post_data)) {
            foreach ($post_data as $order_key => $order_val) {
                if (!empty($order_val)) {
                    update_post_meta($order_id, $order_key, $order_val);
                }
            }
            update_post_meta($order_id, 'order_meta_data', $post_data);
        }
        $bookingTimeRecord['order_id'] = $order_id;
        $bookingTimeRecord['bookingdate'] = $post_data['booking_date'];
        $bookingTimeRecord['booking_time_from'] = $post_data['booking_time_from'];
        $bookingTimeRecord['bookingduration'] = $post_data['booking_time'];
        $bookingTimeRecord['currentdate'] = $post_data['created_date'];

        //Formated Booking Date/Time Records by order id
        $bookingTimeRecord = $this->bkx_bookingTimeRecord($bookingTimeRecord);

        if (!empty($bookingTimeRecord)) {
            update_post_meta($order_id, 'bookingTimeRecord', $bookingTimeRecord);
        }
        $GLOBALS['bkx_booking_data'] = array('meta_data' => $bkx_booking_data, 'time_data' => $bookingTimeRecord);

        if (is_multisite()):
            restore_current_blog();
        endif;

        return $GLOBALS['bkx_booking_data'];
    }

    /**
     * @param $post_data
     * @return int|WP_Error
     */
    public function create_new_user($post_data)
    {
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $user_email     = $post_data['email'];
        $first_name     = $post_data['first_name'];
        $last_name      = $post_data['last_name'];

        $display_name   = isset($post_data['display_name']) ? $post_data['display_name'] : __( ucwords("{$first_name} {$last_name}"), 'bookingx');
        $check_user_id  = username_exists($user_email);
        $user_id = "";
        if (!$check_user_id && email_exists($user_email) == false) {
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
            $user_id = wp_create_user($user_email, $random_password, $user_email);
            $userdata = array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $display_name,
            );
            $user_id = wp_update_user($userdata);

            $dashboard_id = bkx_crud_option_multisite('bkx_dashboard_page_id');
            $dashboard_url = isset($dashboard_id) && $dashboard_id != '' ? get_permalink($dashboard_id) : get_site_url();
            $subject = "BookingX | Congratulation! Your Account created Successfully";
            $data_html = '';
            $data_html .= '<p> Dear ' . ucwords("{$first_name} {$last_name}") . ',<p>';
            $data_html .= '<p>'.__('Your Account created Successfully and check below details', 'bookingx').' : </p>';
            $data_html .= '<p> Username : ' . $user_email . '</p>';
            $data_html .= '<p> Password : ' . $random_password . '</p>';
            $data_html .= '<p>'.__('Also now you can check your order details on My Dashboard section', 'bookingx').' <a href="' . $dashboard_url . '" target="_blank">'.esc_html('click here', 'bookingx').'</a>.</p>';
            bkx_mail_format_and_send_process($subject, $data_html, $user_email);
        }

        if (is_multisite()):
            restore_current_blog();
        endif;

        return $user_id;
    }

    /**
     * @param $post_data
     * @return int|void|WP_Error|null
     */
    public function update_new_user( $post_data  )
    {
        if(!is_user_logged_in()){
            return;
        }
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $user_id = get_current_user_id();
        $first_name     = $post_data['first_name'];
        $last_name      = $post_data['last_name'];
        $display_name   = isset($post_data['display_name']) ? $post_data['display_name'] : __( ucwords("{$first_name} {$last_name}"), 'bookingx');
        if (isset($user_id) &&  $user_id > 0 ) {
            $userdata = array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $display_name,
            );
            update_user_meta($user_id , 'phone', $post_data['phone']);
            $user_id = wp_update_user($userdata);
        }
        if (is_multisite()):
            restore_current_blog();
        endif;
        return $user_id;
    }

    /**
     * @param $status
     * @return null
     */
    public function update_status($status)
    {
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        global $wpdb;
        $order_id = $this->order_id;
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $date = date("Y-m-d H:i:s");

        if ((isset($order_id) && $order_id != '') && (isset($status) && $status != '')) {
            $BkxBookingObj = new BkxBooking('', $order_id);
            $new_status_obj = get_post_status_object('bkx-' . $status);
            $new_status = $new_status_obj->label;
            $old_status = $BkxBookingObj->get_order_status($order_id);
            if ($old_status != $new_status) {
                $BkxBookingObj->add_order_note(sprintf(__('Successfully update order from %1$s to %2$s.', 'bookingx'), $old_status, $new_status), 0);
            }
            $post_update = $wpdb->update($wpdb->posts, array('post_status' => 'bkx-' . $status,),
                array('ID' => $order_id));
            update_post_meta($order_id, 'last_updated_date', $date);
            update_post_meta($order_id, 'updated_by', $current_user_id);
            do_action('bkx_order_edit_status', $order_id, "customer_{$status}");
            do_action('bkx_order_edit_status', $order_id, $status);

            if (!is_wp_error($post_update))
                return $order_id;
        }
        if (is_multisite()):
            restore_current_blog();
        endif;
    }

    /**
     * @param $note
     * @param int $is_customer_note
     * @param bool $added_by_user
     * @param null $order_id
     * @param string $comment_type
     * @return false|int
     */
    public function add_order_note($note, $is_customer_note = 0, $added_by_user = false, $order_id = null, $comment_type = 'booking_note')
    {

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        if (is_user_logged_in() && $added_by_user) {
            $user = get_user_by('id', get_current_user_id());
            $comment_author = $user->display_name;
            $comment_author_email = $user->user_email;
        } else {
            $comment_author = __('BookingX', 'bookingx');
            $comment_author_email = strtolower(__('BookingX', 'bookingx')) . '@';
            $comment_author_email .= isset($_SERVER['HTTP_HOST']) ? str_replace('www.', '', $_SERVER['HTTP_HOST']) : 'noreply.com';
            $comment_author_email = sanitize_email($comment_author_email);
        }

        if (!empty($order_id)) {
            $this->order_id = $order_id;
        }

        $comment_post_ID = $this->order_id;
        $comment_author_url = '';
        $comment_content = $note;
        $comment_agent = 'BookingX';
        $comment_parent = 0;
        $comment_approved = 1;
        $commentdata = apply_filters('bookingx_new_order_note_data', compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved'), array('order_id' => $this->order_id, 'is_customer_note' => $is_customer_note));
        $comment_id = wp_insert_comment($commentdata);

        if ($is_customer_note) {
            add_comment_meta($comment_id, 'is_customer_note', 1);
            do_action('bookingx_new_customer_note', array('order_id' => $this->order_id, 'customer_note' => $commentdata['comment_content']));
        }
        if (is_multisite()):
            restore_current_blog();
        endif;
        return $comment_id;
    }

    /**
     * @param $payment_data
     * @return null
     */
    public function update_payment_meta($payment_data)
    {
        if (empty($this->order_id))
            return;

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $order_id = $this->order_id;
        if ((isset($order_id) && $order_id != '') && (!empty($payment_data))) {
            $payment_meta_update = update_post_meta($order_id, 'payment_meta', $payment_data);
            update_post_meta($order_id, 'payment_status', 'completed');

            if (!is_wp_error($payment_meta_update))
                return $order_id;
        }
        if (is_multisite()):
            restore_current_blog();
        endif;
    }

    /**
     * @param $order_id
     */
    public function get_order_status($order_id)
    {
        if (empty($order_id) || empty($this->order_id))
            return;

        if(isset($this->order_id) && $this->order_id!=""){
            $order_id = $this->order_id;
        }

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $StatusObj = get_post_status_object(get_post_status($order_id));
        $order_status = $StatusObj->label;
        if (is_multisite()):
            restore_current_blog();
        endif;
        return $order_status;
    }

    /**
     * @param $order_id
     * @return mixed|void
     * @throws Exception
     */
    public function get_order_meta_data( $order_id = null )
    {
        if (empty($order_id) && empty($this->order_id))
            return;

        if(isset($this->order_id) && $this->order_id!=""){
            $order_id = $this->order_id;
        }

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $order_meta_data = get_post_meta($order_id, 'order_meta_data', true);
        $bookingTimeRecord = get_post_meta($order_id, 'bookingTimeRecord', true);

        if (empty($order_meta_data))
            return;

        $seat_id = get_post_meta($order_id, 'seat_id', true);
        $base_id = get_post_meta($order_id, 'base_id', true);
        $addition_ids = get_post_meta($order_id, 'addition_ids', true);

        $order_meta_data['seat_id'] = $seat_id;
        $order_meta_data['base_id'] = $base_id;
        $order_meta_data['addition_ids'] = $addition_ids;

        $order_seat_slug = get_post_meta($order_id, 'order_seat_slug', true);
        $order_base_slug = get_post_meta($order_id, 'order_base_slug', true);
        $order_addition_slugs = get_post_meta($order_id, 'order_addition_slugs', true);
        if (isset($order_seat_slug) && $order_seat_slug != "" && $order_seat_slug != "imported") {
            $args = array(
                'name' => $order_seat_slug,
                'post_type' => 'bkx_seat',
                'post_status' => 'publish',
                'numberposts' => 1
            );
            $seat_obj = get_posts($args);
            if (!empty($seat_obj) && !is_wp_error($seat_obj)) {
                $bkx_seat_post = reset($seat_obj);
                if (!empty($bkx_seat_post)) {
                    $seat_id = $bkx_seat_post->ID;
                    update_post_meta($order_id, 'seat_id', $seat_id);
                    update_post_meta($order_id, 'order_seat_slug', 'imported');
                }
            }
        }
        if (isset($order_base_slug) && $order_base_slug != "" && $order_base_slug != "imported") {
            $args = array(
                'name' => $order_base_slug,
                'post_type' => 'bkx_base',
                'post_status' => 'publish',
                'numberposts' => 1
            );
            $base_obj = get_posts($args);
            if (!empty($base_obj) && !is_wp_error($base_obj)) {
                $bkx_base_post = reset($base_obj);
                if (!empty($bkx_base_post)) {
                    $base_id = $bkx_base_post->ID;
                    update_post_meta($order_id, 'base_id', $base_id);
                    update_post_meta($order_id, 'order_base_slug', 'imported');
                }
            }
        }
        if (isset($order_addition_slugs) && $order_addition_slugs != "" && $order_addition_slugs != "imported") {
            $addition_slugs = array_filter(explode(",", $order_addition_slugs));
            if (!empty($addition_slugs)) {
                foreach ($addition_slugs as $key => $addition_slug) {
                    $args = array(
                        'name' => $addition_slug,
                        'post_type' => 'bkx_addition',
                        'post_status' => 'publish',
                        'numberposts' => 1
                    );
                    $addition_obj = get_posts($args);
                    if (!empty($addition_obj) && !is_wp_error($addition_obj)) {
                        $bkx_addition_post = reset($addition_obj);
                        if (!empty($bkx_addition_post) && !is_wp_error($bkx_addition_post)) {
                            $addiion_post_id = $bkx_addition_post->ID;
                            $addition_slug_data[] = $addiion_post_id;
                        }
                    }
                }
                update_post_meta($order_id, 'addition_ids', implode(",", $addition_slug_data));
                update_post_meta($order_id, 'order_addition_slugs', 'imported');
            }
        }
        if (isset($seat_id) && $seat_id != '') {
            $BkxSeat = new BkxSeat('', $seat_id);
            $seat_arr = array('main_obj' => $BkxSeat, 'title' => $BkxSeat->get_title(), 'permalink' => get_edit_post_link($BkxSeat->id));
            $order_meta_data['seat_arr'] = $seat_arr;
        }
        if (isset($base_id) && $base_id != '') {
            $BkxBase = new BkxBase('', $base_id);
            $base_arr = array('main_obj' => $BkxBase, 'title' => $BkxBase->get_title(), 'permalink' => get_edit_post_link($BkxBase->id));
            $order_meta_data['base_arr'] = $base_arr;
        }
        if (isset($addition_ids) && $addition_ids != '') {
            $BkxExtra = new BkxExtra();
            $extra_obj = $BkxExtra->get_extra_by_ids($addition_ids);
            if (!empty($extra_obj)) {
                $extra_arr = array();
                foreach ($extra_obj as $key => $extra) {
                    $extra_arr[] = array('main_obj' => $extra, 'title' => $extra->get_title(), 'permalink' => get_edit_post_link($extra->id));
                }
            }
            $order_meta_data['extra_arr'] = $extra_arr;
        }
        $order_meta_data['bookingTimeRecord'] = $bookingTimeRecord;
        if (is_multisite()):
            restore_current_blog();
        endif;

        return $order_meta_data;
    }

    /**
     * @param null $order_id
     * @param null $search
     * @return mixed|void
     */
    public function get_order_time_data($order_id = null, $search = null)
    {

        if (empty($order_id) || empty($this->order_id))
            return;

        if(isset($this->order_id) && $this->order_id!=""){
            $order_id = $this->order_id;
        }
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $order_time_data = get_post_meta($order_id, 'bookingTimeRecord', true);

        if (is_multisite()):
            restore_current_blog();
        endif;
        return $order_time_data;
    }

    /**
     * @param $order_id
     * @return string|void
     */
    public function get_booking_notes($order_id)
    {
        if (empty($order_id) || empty($this->order_id))
            return;

        if(isset($this->order_id) && $this->order_id!=""){
            $order_id = $this->order_id;
        }

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $args = array(
            'post_id' => $order_id,
            'type' => 'booking_note'
        );
       //$comments_query = new WP_Comment_Query( $args );
        $booking_note_data = get_comments($args);
        $booking_notes = '';
        if (!empty($booking_note_data) && !is_wp_error($booking_note_data)) {
            $booking_notes .= '<ul>';
            foreach ($booking_note_data as $key => $notes) {
                $system_notes = $notes->comment_content;
                $note_date = $notes->comment_date;
                $booking_notes .= '<li> <b>' . $note_date . ' : </b> ' . $system_notes . '</li>';
            }
            $booking_notes .= '</ul>';
        }
        if (is_multisite()):
            restore_current_blog();
        endif;
        return $booking_notes;
    }

    /**
     * @param $order_id
     * @return array|int|void
     */
    public function get_booking_notes_for_export($order_id)
    {
        if (empty($order_id) || empty($this->order_id))
            return;

        if(isset($this->order_id) && $this->order_id!=""){
            $order_id = $this->order_id;
        }

        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $booking_note_data = get_comments(array(
            'post_id' => $order_id,
        ));
        if (is_multisite()):
            restore_current_blog();
        endif;
        if (!empty($booking_note_data) && !is_wp_error($booking_note_data)) {
            return $booking_note_data;
        }
    }

    /**
     * @param null $search
     * @return array
     */
    public function GetBookedRecords($search = null)
    {
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;
        $finalarr = array();
        $date = $search['booking_date'];
        $status = (empty($search['status'])) ? 'bkx-pending' : $search['status'];
        $seat_by_base = "";
        if (isset($search['service_id']) && $search['service_id'] != "") {
            $base_id = $search['service_id'];
            $BkxBase = new BkxBase('', $base_id);
            $seat_by_base = $BkxBase->get_seat_by_base();
        }
        $seat_id = ($search['seat_id'] == 'any') ? $seat_by_base : $search['seat_id'];

        $args = array(
            'post_type' => $this->post_type,
            'post_status' => $status,
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'booking_date',
                        'value' => date('F j, Y', strtotime($date)),
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('m/d/Y', strtotime($date)),
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-d', strtotime($date)),
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-j', strtotime($date)),
                        'compare' => '=',
                    )
                ),
            ),
        );
        if (isset($search['by']) && $search['by'] == 'future') {
            $args['meta_query'] = array();
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'booking_date',
                        'value' => date('F j, Y', strtotime($date)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('m/d/Y', strtotime($date)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-d', strtotime($date)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-j', strtotime($date)),
                        'compare' => '>=',
                    )
                ),
            );
        }
        if (isset($search['by']) && $search['by'] == 'this_month') {
            $args['meta_query'] = array();
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'booking_date',
                        'value' => date('F 01, Y', strtotime($date)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('m/01/Y', strtotime($date)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-01', strtotime($date)),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'booking_date',
                        'value' => date('Y-m-01', strtotime($date)),
                        'compare' => '>=',
                    )
                ),
            );
        }
        if (isset($seat_id) && $seat_id != "" && $seat_id > 0) {
            $args['meta_query'][] = array(
                'key' => 'seat_id',
                'value' => $seat_id,
                'compare' => 'IN',
            );
        }
        $bookedresult = new WP_Query($args);
        //echo '<pre>',print_r($bookedresult,1),'</pre>';
        if ($bookedresult->have_posts()) :
            while ($bookedresult->have_posts()) : $bookedresult->the_post();
                $order_id = get_the_ID();
                if (!empty($order_id)) {
                    $order_data = self::get_order_meta_data($order_id);
                    $order_time_data = self::get_order_time_data($order_id);
                    //echo '<pre>', print_r($order_time_data, 1), '</pre>';
                    $order_time_data = (!empty($order_time_data) ? reset($order_time_data) : array());
                    if (!empty($order_time_data['full_day'])) {
                        $full_day = explode(',', $order_time_data['full_day']);
                        if (!empty($full_day)) {
                            $order_time_data['full_day'] = $full_day[0];
                        }
                    }
                    $finalarr[] = array_merge($order_time_data, $order_data);
                }
            endwhile;
        endif;

        if (is_multisite()):
            restore_current_blog();
        endif;

        return $finalarr;
    }

    /**
     * @param null $search
     * @return array
     */
    public function GetBookedRecordsByUser($search = null)
    {
        if (is_multisite()):
            $blog_id = apply_filters('bkx_set_blog_id', get_current_blog_id());
            switch_to_blog($blog_id);
        endif;

        $final_arr = array();
        $bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
        $status = array('bkx-pending', 'bkx-ack', 'bkx-missed', 'bkx-completed', 'bkx-cancelled');
        $user_id = (empty($search['user_id'])) ? '' : $search['user_id'];
        $seat_id = (empty($search['seat_id'])) ? '' : $search['seat_id'];
        $role = (empty($search['role'])) ? '' : $search['role'];
        $search_by = (empty($search['search_by'])) ? '' : $search['search_by'];
        $search_date = (empty($search['search_date'])) ? '' : $search['search_date'];
        if (is_admin()) {
            $seat_id = get_current_user_id();
        }
        $current_user = get_current_user_id();
        $search_by_user = array();
        if( is_user_logged_in() && !current_user_can('administrator') ) {
            $user_obj = get_user_by('id', $current_user);
            $user_email = $user_obj->user_email;
            $search_by_user = array(
                'relation' => 'OR',
                array(
                    'key' => 'email',
                    'value' => $user_email,
                    'compare' => '='
                ),
                array(
                    'key' => 'created_by',
                    'value' => $current_user,
                    'compare' => '='
                )
            );

        }
        //$search_date = date('F j, Y',strtotime($search_date));
        $args = array();
        switch ($search_by) {
            case $bkx_seat_role: //Query for Seat user booking data
                $args = array(
                    'post_type' => $this->post_type,
                    'post_status' => $status,
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'seat_id',
                            'value' => $seat_id,
                            'compare' => 'IN',
                        ),
                    ),
                );
                break;
            case ($search_by == "future") :
                //echo $search_by;
                $args = array(
                    'post_type' => $this->post_type,
                    'post_status' => $status,
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'booking_start_date',
                            'value' => $search_date,
                            'compare' => '>='
                        ),$search_by_user
                    ),
                );
                break;
            case ($search_by == 'past') :
                //echo $search_date;
                $args = array(
                    'post_type' => $this->post_type,
                    'post_status' => $status,
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'booking_start_date',
                            'value' => $search_date,
                            'compare' => '<'
                        ),$search_by_user
                    ),
                );
                break;
            case 'administrator': // Query for all booking data
                break;
            default:  // Query for user specific data
                $args = array(
                    'post_type' => $this->post_type,
                    'author' => $user_id,
                    'posts_per_page' => -1,
                    'post_status' => $status
                );
                break;
        }
        $args = apply_filters('bkx_get_bookings_by_user', $args);
        //echo "<pre>".print_r($args, true)."</pre>";
        $booked_result = new WP_Query($args);

        //echo "<pre>".print_r($booked_result, true)."</pre>";
        if ($booked_result->have_posts()) :
            while ($booked_result->have_posts()) : $booked_result->the_post();
                $order_id = get_the_ID();
                if (!empty($order_id)) {
                    $order_data = $this->get_order_meta_data($order_id);
                    $get_order_time_data = $this->get_order_time_data($order_id);
                    $order_time_data = array();
                    if(!empty($get_order_time_data)){
                        $order_time_data = reset($get_order_time_data);
                        if (!empty($order_time_data['full_day'])) {
                            $full_day = explode(',', $order_time_data['full_day']);
                            if (!empty($full_day)) {
                                $order_time_data['full_day'] = $full_day[0];
                            }
                        }
                    }

                    $final_arr[] = $order_time_data + $order_data;
                }
            endwhile;
        endif;
        if (is_multisite()):
            restore_current_blog();
        endif;
        return $final_arr;
    }

    /**
     * @param $booking_id
     * @param $page_id
     * @return mixed|void
     */
    public function get_view_booking_url($booking_id, $page_id)
    {
        $url = add_query_arg(array(
            'view-booking' => $booking_id
        ), get_permalink($page_id));

        $view_url = wp_nonce_url($url, 'view_booking_' . $booking_id, 'view_booking_nonce');
        return apply_filters('bkx_get_view_booking_url', $view_url, $this);
    }

    /**
     * @return mixed
     */
    public function get_booking_date(){
        return get_post_meta($this->order_id, 'booking_start_date', true);
    }

    /**
     * This Function calculates booking time records
     * @access public
     * @param $bookingTimeRecord
     * @return array $bookingTime
     */
    public function bkx_bookingTimeRecord($bookingTimeRecord)
    {
        $order_id = $bookingTimeRecord['order_id'];
        $bookingdate = $bookingTimeRecord['bookingdate'];
        $bookingtime = $bookingTimeRecord['booking_time_from'];
        $bookingduration = $bookingTimeRecord['bookingduration']['in_sec'];
        $currentdate = $bookingTimeRecord['currentdate'];

        $bookinghour = explode(':', $bookingtime);
        $hours_temp = $bookinghour[0];
        $minutes_temp = $bookinghour[1];
        $sec_hours_temp = $hours_temp * 60 * 60;
        $sec_minutes_temp = $minutes_temp * 60;
        $total_sec_passed = $sec_hours_temp + $sec_minutes_temp;
        $remainingtime = (24 * 60 * 60 - $total_sec_passed);
        $counter = 0;

        if ($minutes_temp == '00') {
            $counter = 0;
        } else if ($minutes_temp == '15') {
            $counter = 1;
        } else if ($minutes_temp == '30') {
            $counter = 2;
        } else if ($minutes_temp == '45') {
            $counter = 3;
        }
        $bookingTime = array();
        if ($bookingduration <= $remainingtime) {
            $passed_full_day = intval($hours_temp) * 4 + $counter;
            $actual_full_day = '';
            $counter_full_day = $passed_full_day + 1;
            for ($i = 0; $i <= intval($bookingduration); $i = $i + (15 * 60)) {
                $actual_full_day = $actual_full_day . $counter_full_day . ",";
                $counter_full_day = $counter_full_day + 1;
            }
            $arrDataTimeTemp = array('booking_record_id' => $order_id, 'booking_date' => $bookingdate, 'booking_time_from' => $bookingtime, 'booking_time_till' => '', 'full_day' => $actual_full_day, 'created_date' => $currentdate);
            array_push($bookingTime, $arrDataTimeTemp);
        } else {
            //for the current day booking
            $passed_full_day = intval($hours_temp) * 4 + $counter;
            $actual_full_day = '';
            $counter_full_day = $passed_full_day + 1;
            for ($i = 0; $i <= intval($remainingtime); $i = $i + (15 * 60)) {
                $actual_full_day = $actual_full_day . $counter_full_day . ",";
                $counter_full_day = $counter_full_day + 1;
            }
            $arrDataTimeTemp = array(
                'booking_record_id' => $order_id,
                'booking_date' => $bookingdate,
                'booking_time_from' => $bookingtime,
                'booking_time_till' => "",
                'full_day' => $actual_full_day,
                'created_date' => $currentdate
            );
            array_push($bookingTime, $arrDataTimeTemp);
            //now starts with the remaining days booking
            $remainingduration = ($bookingduration - $remainingtime);
            $remainingBookingDuration = $bookingduration - $remainingtime;
            $remainingBookingDurationMinutes = $remainingBookingDuration / 60;
            $noOfSlotsRemaining = $remainingBookingDurationMinutes / 15;
            $totalDays = floor($noOfSlotsRemaining / 96); //to find number of days
            $totalDaysMod = $noOfSlotsRemaining % 96; //to find if extend to next day
            if ($totalDaysMod > 0) { //if goes to next day add 1 to the days value
                $totalDays = $totalDays + 1;
            }
            $bookedDate = $bookingdate;
            $nextDate = '';
            for ($i = 1; $i <= $totalDays; $i = $i + 1) {
                if ($remainingduration > 0) {
                    $nextDate = date('m/d/Y', strtotime("+1 day", strtotime($bookedDate)));
                    $passed_full_day = 0;
                    $counter_full_day = $passed_full_day + 1;
                    if ($remainingduration > (96 * 15 * 60)) {
                        $remainingtime_temp = (96 * 15 * 60);
                    } else {
                        $remainingtime_temp = $remainingduration;
                    }
                    $remainingtime_temp_slot = floor($remainingtime_temp / (15 * 60));
                    $actual_full_day = "";
                    for ($j = 1; $j <= $remainingtime_temp_slot; $j++) {
                        $actual_full_day .= $j . ",";
                    }
                    //$actual_full_day;
                    $arrBookingtimeNext = array('booking_record_id' => $order_id, 'booking_date' => $nextDate, 'booking_time_from' => '00:00', 'booking_time_till' => '', 'full_day' => $actual_full_day, 'created_date' => $currentdate);

                    array_push($bookingTime, $arrBookingtimeNext);
                    $bookedDate = $nextDate;
                    $remainingduration = ($remainingduration - 96 * 15 * 60);
                }
            }
        }
        return $bookingTime;
    }

    /**
     * @param $term
     * @return array
     */
    public function order_search($term)
    {
        global $wpdb;
        $term = str_replace('Order #', '', bkx_clean($term));
        // Search fields.
        $search_fields = array_map('bkx_clean', apply_filters('bk_bkx_booking_search_fields', array(
            'order_id', 'seat_id', 'base_id',
            'addition_ids', 'first_name', 'last_name', 'phone', 'email', 'street', 'city', 'state', 'postcode', 'total_price',
            'total_duration', 'payment_method', 'payment_status', 'created_date', 'created_by', 'booking_date', 'booking_time',
            'booking_start_date', 'booking_end_date', 'booking_time_from', 'currency')));
        // Search orders.
        $post_ids = array_unique(array_merge(
            $wpdb->get_col(
                $wpdb->prepare("
                        SELECT DISTINCT p1.post_id FROM {$wpdb->postmeta} p1 INNER JOIN {$wpdb->posts} p2 ON p1.post_id = p2.ID WHERE( p1.meta_key IN ('" . implode("','", array_map('esc_sql', $search_fields)) . "') 
                            AND p1.meta_value LIKE %s )",
                    '%' . $wpdb->esc_like(bkx_clean($term)) . '%'
                )
            )
        ));
        return $post_ids;
    }
}
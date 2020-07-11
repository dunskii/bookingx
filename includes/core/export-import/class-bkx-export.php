<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Description of BkxBase
 *
 * @author divyang
 */
class BkxExport
{

    /**
     * @var string
     */
    public $file_type = 'xml'; //File Extention .xml

    /**
     * @var array
     */
    public $export_type = array('all', 'seat', 'base', 'extra', 'users', 'booking', 'settings'); //Allowed export type

    /**
     * @var int
     */
    public $file_size = 5; // In MB

    /**
     * @var array|string
     */
    public $upload_dir = '';

    /**
     * @var string
     */
    public $set_header = '';

    /**
     * @var DOMDocument|string
     */
    public $xmlobj = '';

    /**
     * @var DOMNode|string
     */
    public $parent_root = '';

    /**
     * @var array
     */
    var $errors = array();

    /**
     * BkxExport constructor.
     * @param string $type
     */
    public function __construct($type = 'all')
    {
        $this->upload_dir = wp_upload_dir();
        $this->xmlobj = new DOMDocument();
        $this->parent_root = $this->xmlobj->appendChild($this->xmlobj->createElement("Root"));

        if (!in_array($type, $this->export_type)) {
            $this->errors['type_error'] = "Oops.. , '$type' type does not exists.";
            return $this->errors;
        }
    }

    /**
     * Generate Xml Build and Assign Value into this
     * @return array
     */
    public function export_now()
    {
        if (isset($_POST['export_xml']) && sanitize_text_field($_POST['export_xml']) == "Export xml") {
            if (empty($this->errors)) {
                $this->bkx_get_posts('bkx_seat', 'SeatPosts', 'Resource');
                $this->bkx_get_posts('bkx_base', 'BasePosts', 'Service');
                $this->bkx_get_posts('bkx_addition', 'ExtraPosts', 'Extra'); //ok
                $this->bkx_get_posts('bkx_booking', 'BookingPosts', 'Booking');
                $this->get_bkx_users();
                $this->get_bkx_settings();
                $this->generate_file();
            } else {
                return $this->errors;
            }
        } else {
            return $this->errors;
        }
    }

    /**
     * Generate File and Download Automatically
     */
    public function generate_file()
    {
        $this->xmlobj->formatOutput = true;
        $cur_date = date("Y-m-d_H:i:s");
        $content = $this->xmlobj->saveXML();
        $newfile_name = BKX_PLUGIN_DIR_PATH . "public/uploads/newfile.xml";
        unlink($newfile_name);
        $file_handle = fopen($newfile_name, "w") or die("Please allow file permission to generate file on booking/uploads Directory.");
        fwrite($file_handle, $content);
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="bookingx-' . $cur_date . '.xml"');
        //read from server and write to buffer
        readfile($newfile_name);
    }

    /**
     * Bkx Settings Key Array
     * @return mixed|void
     */
    public function settings_fields()
    {
        $options = array('bkx_api_paypal_paypalmode', 'bkx_api_paypal_username', 'bkx_api_paypal_password',
            'bkx_api_paypal_signature', 'bkx_gateway_paypal_express_status', 'bkx_new_booking_enabled', 'bkx_dashboard_column', 'enable_cancel_booking', 'cancellation_policy_page_id', 'enable_any_seat',
            'bkx_enable_customer_dashboard', 'currency_option', 'bkx_seat_role', 'bkx_alias_seat', 'bkx_alias_base', 'bkx_alias_addition', 'bkx_alias_notification', 'bkx_notice_time_extended_text_alias',
            'bkx_label_of_step1', 'bkx_seat_taxonomy_status', 'bkx_base_taxonomy_status', 'bkx_addition_taxonomy_status',
            'bkx_legal_options', 'bkx_term_cond_page', 'bkx_cancellation_policy_page', 'bkx_privacy_policy_page', 'enable_editor', 'bkx_set_booking_page', 'bkx_siteuser_canedit_seat', 'bkx_siteclient_canedit_css',
            'bkx_booking_style', 'bkx_form_text_color', 'bkx_active_step_color', 'bkx_cal_day_color', 'bkx_cal_day_selected_color',
            'bkx_time_available_color', 'bkx_time_selected_color', 'bkx_time_unavailable_color', 'bkx_next_btn', 'bkx_prev_btn', 'bkx_business_name', 'bkx_business_email', 'bkx_business_phone', 'bkx_business_address_1', 'bkx_business_address_2', 'bkx_business_city',
            'bkx_business_state', 'bkx_business_zip', 'bkx_business_country', 'bkx_business_days', 'bkx_biz_vac_sd', 'bkx_biz_vac_ed', 'bkx_biz_pub_holiday', 'bkx_tax_rate', 'bkx_tax_name',
            'bkx_prices_include_tax', 'bkx_customer_cancelled_booking_subject', 'bkx_customer_cancelled_booking_heading', 'bkx_customer_cancelled_booking_additional_content', 'bkx_customer_cancelled_booking_email_type', 'bkx_save_bkx_email_customer_cancelled_booking',
            'bkx_customer_completed_booking_subject', 'bkx_customer_completed_booking_heading', 'bkx_customer_completed_booking_additional_content', 'bkx_customer_completed_booking_email_type', 'bkx_save_bkx_email_customer_completed_booking',
            'bkx_customer_ack_booking_subject', 'bkx_customer_ack_booking_heading', 'bkx_customer_ack_booking_additional_content', 'bkx_customer_ack_booking_email_type', 'bkx_save_bkx_email_customer_ack_booking',
            'bkx_customer_pending_booking_enabled', 'bkx_customer_pending_booking_subject', 'bkx_customer_pending_booking_heading', 'bkx_customer_pending_booking_additional_content', 'bkx_customer_pending_booking_email_type', 'bkx_save_bkx_email_customer_pending_booking',
            'bkx_cancelled_booking_enabled', 'bkx_cancelled_booking_recipient', 'bkx_cancelled_booking_subject', 'bkx_cancelled_booking_heading', 'bkx_cancelled_booking_additional_content', 'bkx_cancelled_booking_email_type', 'bkx_save_bkx_email_cancelled_booking',
            'bkx_customer_edit_booking_enabled', 'bkx_customer_edit_booking_subject', 'bkx_customer_edit_booking_heading', 'bkx_customer_edit_booking_additional_content', 'bkx_customer_edit_booking_email_type', 'bkx_save_bkx_email_customer_edit_booking',
            'bkx_edit_booking_enabled', 'bkx_edit_booking_recipient', 'bkx_edit_booking_subject', 'bkx_edit_booking_heading', 'bkx_edit_booking_additional_content', 'bkx_edit_booking_email_type', 'bkx_save_bkx_email_edit_booking',
            'bkx_pending_booking_enabled', 'bkx_pending_booking_recipient', 'bkx_pending_booking_subject', 'bkx_pending_booking_heading', 'bkx_pending_booking_additional_content', 'bkx_pending_booking_email_type', 'bkx_save_bkx_email_pending_booking', 'bkx_emails_settings', 'bkx_setting_form_init');
        return apply_filters('bkx_settings_import', $options);
    }

    /**
     *
     */
    public function get_bkx_settings()
    {
        $settingTag = $this->parent_root->appendChild($this->xmlobj->createElement('Settings'));
        foreach ($this->settings_fields() as $option_key) {
            if (!empty($option_key)) {
                $option_value = bkx_crud_option_multisite($option_key);
                $option_value = isset($option_value) ? $option_value : '';
                if (is_array($option_value)) {
                    $settingTag->appendChild($this->xmlobj->createElement($option_key, maybe_serialize($option_value)));
                } else {
                    $settingTag->appendChild($this->xmlobj->createElement($option_key, $option_value));
                }
            }
        }
    }

    public function get_bkx_users()
    {
        $args = array(
            'meta_key' => 'seat_post_id',
            'orderby' => 'ID',
            'order' => 'ASC',
            'fields' => 'all',
        );
        $get_users_obj = get_users($args);
        $BkxUsersTag = $this->parent_root->appendChild($this->xmlobj->createElement('BkxUsers'));
        if (!empty($get_users_obj) && !is_wp_error($get_users_obj)) {
            foreach ($get_users_obj as $key => $user_obj) {
                $UserPostTag = $BkxUsersTag->appendChild($this->xmlobj->createElement('BkxUsers'));
                $user_data_obj = $user_obj->data;
                $user_caps_obj = $user_obj->caps;
                $user_roles_obj = $user_obj->roles;

                $user_meta_data = get_user_meta($user_obj->ID);
                $get_post_seat_id = get_user_meta($user_obj->ID, 'seat_post_id', true);
                $seat_data = get_post($get_post_seat_id);
                $seat_slug = $seat_data->post_name;

                $user_attr = $this->xmlobj->createAttribute('id');
                $user_attr->value = $user_obj->ID;
                $UserPostTag->appendChild($user_attr);

                if (!empty($user_data_obj)) {
                    $UserDataTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserData"));
                    foreach ($user_data_obj as $user_data_key => $user_data_val) {
                        $UserDataTag->appendChild($this->xmlobj->createElement($user_data_key, esc_html($user_data_val)));
                    }
                }

                if (!empty($user_caps_obj)) {
                    $UserCapsTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserCaps"));
                    foreach ($user_caps_obj as $user_caps_key => $user_caps_val) {
                        $UserCapsTag->appendChild($this->xmlobj->createElement($user_caps_key, esc_html($user_caps_val)));
                    }
                    $UserCapsTag->appendChild($this->xmlobj->createElement('cap_key', esc_html($user_obj->cap_key)));
                }

                if (!empty($user_roles_obj)) {
                    $UserRoleTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserRoles"));
                    foreach ($user_roles_obj as $user_roles_key => $user_roles_val) {
                        $UserRoleTag->appendChild($this->xmlobj->createElement('roles', esc_html($user_roles_val)));
                    }
                }

                if (!empty($user_meta_data)) {
                    $UserMetaTag = $UserPostTag->appendChild($this->xmlobj->createElement("UserMetaData"));
                    foreach ($user_meta_data as $user_meta_key => $user_meta_val) {
                        $UserMetaTag->appendChild($this->xmlobj->createElement($user_meta_key, esc_html($user_meta_val[0])));
                    }
                    $UserMetaTag->appendChild($this->xmlobj->createElement('bkx_seat_slug', $seat_slug));
                }
            }
        }
    }


    /**
     * @param string $post_type
     * @param string $elements_name
     * @param string $element_name
     */
    public function bkx_get_posts($post_type = 'bkx_seat', $elements_name = 'SeatPosts', $element_name = 'Resource')
    {
        global $wpdb, $wp_query;

        if ($post_type == 'bkx_booking') {
            $post_status = array('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed', 'bkx-cancelled', 'bkx-failed');
        } else {
            $post_status = 'publish';
        }
        $args = array('posts_per_page' => -1, 'post_type' => $post_type, 'post_status' => $post_status);
        $get_posts = get_posts($args);
        if (!empty($get_posts)) {
            //create the root element
            $root = $this->parent_root->appendChild($this->xmlobj->createElement($elements_name));
            foreach ($get_posts as $post) {
                setup_postdata($post);
                $PostTag = $root->appendChild($this->xmlobj->createElement($element_name));
                $metas = get_post_custom($post->ID);
                $idAttr = $this->xmlobj->createAttribute('id');
                $idAttr->value = $post->ID;

                $PostTag->appendChild($idAttr);
                $post_title = str_replace("&ndash;", "-", get_the_title($post->ID));
                $PostTag->appendChild($this->xmlobj->createElement("Name", esc_html($post_title)));
                $PostTag->appendChild($this->xmlobj->createElement("Description", $post->post_content));

                if ($post_type == 'bkx_booking') {
                    $PostTag->appendChild($this->xmlobj->createElement("post_status", $post->post_status));
                    $PostTag->appendChild($this->xmlobj->createElement("comment_status", $post->comment_status));
                    $PostTag->appendChild($this->xmlobj->createElement("ping_status", $post->ping_status));
                    $PostTag->appendChild($this->xmlobj->createElement("post_date", $post->post_date));
                    $PostTag->appendChild($this->xmlobj->createElement("post_date_gmt", $post->post_date_gmt));

                    $orderObj = new BkxBooking();
                    $booking_note_data = $orderObj->get_booking_notes_for_export($post->ID);
                    $booking_note_data_json = json_encode($booking_note_data);

                    if (!empty($booking_note_data) && !is_wp_error($booking_note_data)) {
                        $PostTag->appendChild($this->xmlobj->createElement("CommentData", $booking_note_data_json));
                    }
                }
                if (!empty($metas) && !is_wp_error($metas)) {
                    unset($metas['_edit_last']);
                    unset($metas['_edit_lock']);
                    $postmeta = $PostTag->appendChild($this->xmlobj->createElement("Postmeta"));
                    foreach ($metas as $meta_key => $meta_value) {
                        if ($post_type == 'bkx_booking') {
                            delete_post_meta_by_key('order_seat_slug');
                            delete_post_meta_by_key('order_base_slug');
                            delete_post_meta_by_key('order_addition_slugs');
                            if ($meta_key == 'seat_id') {
                                $seat_id = $meta_value[0];
                                $seat_data = get_post($seat_id);
                                $seat_slug = $seat_data->post_name;
                            }
                            if ($meta_key == 'base_id') {
                                $base_id = $meta_value[0];
                                $base_data = get_post($base_id);
                                $base_slug = $base_data->post_name;
                            }
                            if ($meta_key == 'addition_ids') {
                                $addition_ids = explode(",", $meta_value[0]);
                                if (!empty($addition_ids)) {
                                    foreach ($addition_ids as $key => $addition_id) {
                                        $addition_data = get_post($addition_id);
                                        $addition_slug[] = $addition_data->post_name;
                                    }
                                }
                            }
                        }
                        if ($meta_key == 'order_meta_data') {
                            $postmeta_data = maybe_unserialize($meta_value[0]);
                            unset($postmeta_data['currency']);
                            $postmeta_value = maybe_serialize($postmeta_data);
                        } else {
                            $postmeta_value = $meta_value[0];
                        }
                        $postmeta->appendChild($this->xmlobj->createElement($meta_key, $postmeta_value));
                    }
                    if (isset($seat_slug)) {
                        $postmeta->appendChild($this->xmlobj->createElement('order_seat_slug', $seat_slug));
                    }
                    if (isset($base_slug)) {
                        $postmeta->appendChild($this->xmlobj->createElement('order_base_slug', $base_slug));
                    }
                    if (isset($addition_slug)) {
                        $addition_data = implode(",", $addition_slug);
                        $postmeta->appendChild($this->xmlobj->createElement('order_addition_slugs', $addition_data));
                    }
                }
            }
        }
    }
}

new BkxExport();
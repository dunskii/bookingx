<?php
/**
 * Class BKX_Email_Ack_Booking file
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BKX_Email_Customer_Ack_Booking')) :

    /**
     * Ack Booking Email.
     *
     * An email sent to the admin when a ack booking is received/paid for.
     */
    class BKX_Email_Customer_Ack_Booking extends BookingX_Email_Content
    {

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->id = 'customer_ack_booking';
            $this->customer_email = true;
            $this->title = __('Customer Acknowledge booking', 'bookingx');
            $this->description = __('Acknowledge booking emails are sent to chosen recipient(s) when a ack booking is received.', 'bookingx');
            $this->template_html = 'emails/customer-ack-booking.php';
            $this->template_plain = 'emails/plain/customer-ack-booking.php';
            $this->placeholders = array(
                '{booking_date}' => '',
                '{booking_number}' => '',
            );
            // Triggers for this email.
            add_action('bookingx_booking_status_cancelled_to_ack_notification', array($this, 'trigger'), 10, 2);
            add_action('bookingx_booking_status_pending_to_ack_notification', array($this, 'trigger'), 10, 2);

            // Call parent constructor.
            parent::__construct();

            // Other settings.
            //$this->recipient = $this->get_option( 'admin_email');
            $this->email_type = $this->get_option($this->plugin_id . $this->id . '_email_type');
            $this->enabled = $this->get_option($this->plugin_id . $this->id . '_enabled');
            $this->heading = $this->get_option($this->plugin_id . $this->id . '_heading');
            $subject = $this->get_option($this->plugin_id . $this->id . '_subject');
            $this->subject = isset($subject) && !empty($subject) ? $subject : $this->get_default_subject();
            $content = $this->get_option($this->plugin_id . $this->id . '_additional_content');
            $this->additional_content = isset($content) && !empty($content) ? $content : $this->get_default_additional_content();
        }

        /**
         * @return mixed|void
         */
        public function is_enabled()
        {
            return apply_filters('bookingx_email_enabled_' . $this->id, 'yes' === $this->enabled || 1 === $this->enabled, $this->object, $this);
        }

        /**
         * Get email subject.
         */
        public function get_default_subject()
        {
            return __('[{site_title}]: Ack booking #{booking_number}', 'bookingx');
        }

        /**
         * Get email heading.
         */
        public function get_default_heading()
        {
            return __('Ack Booking: #{booking_number}', 'bookingx');
        }

        /**
         * Trigger the sending of this email.
         */
        public function trigger($booking_id, $booking = false)
        {


        }

        /**
         * Get content html.
         *
         * @return string
         */
        public function get_content_html()
        {
            return wc_get_template_html(
                $this->template_html,
                array(
                    'booking' => $this->object,
                    'email_heading' => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin' => true,
                    'plain_text' => false,
                    'email' => $this,
                )
            );
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        public function get_content_plain()
        {
            return wc_get_template_html(
                $this->template_plain,
                array(
                    'booking' => $this->object,
                    'email_heading' => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin' => true,
                    'plain_text' => true,
                    'email' => $this,
                )
            );
        }

        /**
         * Initialise settings form fields.
         */
        public function init_form_fields()
        {
            /* translators: %s: list of placeholders */
            $placeholder_text = sprintf(__('Available placeholders: %s', 'bookingx'), '<code>' . implode('</code>, <code>', array_keys($this->placeholders)) . '</code>');
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'bookingx'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification', 'bookingx'),
                    'default' => 'yes',
                    'option_key' => 'bkx_customer_ack_booking_enabled'
                ),
                'subject' => array(
                    'title' => __('Subject', 'bookingx'),
                    'type' => 'text',
                    'css' => 'width:400px;',
                    'description' => $placeholder_text,
                    'placeholder' => $this->get_default_subject(),
                    'default' => '',
                    'option_key' => 'bkx_customer_ack_booking_subject'
                ),
                'heading' => array(
                    'title' => __('Email heading', 'bookingx'),
                    'type' => 'text',
                    'css' => 'width:400px;',
                    'description' => $placeholder_text,
                    'placeholder' => $this->get_default_heading(),
                    'default' => '',
                    'option_key' => 'bkx_customer_ack_booking_heading'
                ),
                'additional_content' => array(
                    'title' => __('Main Body', 'bookingx'),
                    'description' => __('Text to appear below the main email content.', 'bookingx') . ' ' . $placeholder_text,
                    'placeholder' => __('N/A', 'bookingx'),
                    'type' => 'wp_editor',
                    'default_editor' => 'html',
                    'default' => $this->get_default_additional_content(),
                    'option_key' => 'bkx_customer_ack_booking_additional_content'
                ),
                'email_type' => array(
                    'title' => __('Email type', 'bookingx'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to send.', 'bookingx'),
                    'default' => 'html',
                    'css' => 'width:400px;',
                    'class' => 'email_type',
                    'options' => $this->get_email_type_options(),
                    'option_key' => 'bkx_customer_ack_booking_email_type'
                ),
            );
        }
    }

endif;

return new BKX_Email_Customer_Ack_Booking();
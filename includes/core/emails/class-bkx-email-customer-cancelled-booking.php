<?php
/**
 * Class BKX_Email_Cancelled_Booking file
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BKX_Email_Customer_Cancelled_Booking')) :

    /**
     * Cancelled Booking Email.
     *
     * An email sent to the admin when a cancelled booking is received/paid for.
     */
    class BKX_Email_Customer_Cancelled_Booking extends BookingX_Email_Content
    {

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->id = 'customer_cancelled_booking';
            $this->customer_email = true;
            $this->title = __('Customer Cancelled booking', 'bookingx');
            $this->description = __('Cancelled booking emails are sent to chosen recipient(s) when a cancelled booking is received.', 'bookingx');
            $this->template_html = 'emails/customer-cancelled-booking.php';
            $this->template_plain = 'emails/plain/customer-cancelled-booking.php';
            $this->placeholders = array(
                '{booking_date}' => '',
                '{booking_number}' => '',
            );
            // Triggers for this email.
            add_action('bookingx_booking_status_cancelled_to_cancelled_notification', array($this, 'trigger'), 10, 2);
            add_action('bookingx_booking_status_pending_to_cancelled_notification', array($this, 'trigger'), 10, 2);

            // Call parent constructor.
            parent::__construct();

            // Other settings.
            $this->recipient = $this->get_option('admin_email');
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
            return __('[{site_title}]: Cancelled booking #{booking_number}', 'bookingx');
        }

        /**
         * Get email heading.
         */
        public function get_default_heading()
        {
            return __('Cancelled Booking: #{booking_number}', 'bookingx');
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
            return bkx_get_template_html(
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
         * Default content to show below main email content.
         */
        public function get_default_additional_content()
        {

            return __(' <p>Hi {fname},</p>
                        <p>Your booking has been cancelled. If you think this is an error please contact us.</p>
                        <p>Here are your booking details: </p>
                        <p>Booking ID: {order_id}</p>
                        <p>Resource: {seat_name}</p>
                        <p>Service: {base_name}</p>
                        <p>Extras: {additions_list} </p>    
                        <p>Time: {time_of_booking}</p>
                        <p>Date: {date_of_booking}</p>
                        <p>Location: {location_of_booking}</p>
                        <p>Price: {total_price}</p>
                        <p>Amount Paid : {amount_paid}</p>', 'bookingx');
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        public function get_content_plain()
        {
            return bkx_get_template_html(
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
                    'option_key' => 'bkx_customer_cancelled_booking_enabled'
                ),
                'subject' => array(
                    'title' => __('Subject', 'bookingx'),
                    'type' => 'text',
                    'css' => 'width:400px;',
                    'description' => $placeholder_text,
                    'placeholder' => $this->get_default_subject(),
                    'default' => '',
                    'option_key' => 'bkx_customer_cancelled_booking_subject'
                ),
                'heading' => array(
                    'title' => __('Email heading', 'bookingx'),
                    'type' => 'text',
                    'css' => 'width:400px;',
                    'description' => $placeholder_text,
                    'placeholder' => $this->get_default_heading(),
                    'default' => '',
                    'option_key' => 'bkx_customer_cancelled_booking_heading'
                ),
                'additional_content' => array(
                    'title' => __('Main Body', 'bookingx'),
                    'description' => __('Text to appear below the main email content.', 'bookingx') . ' ' . $placeholder_text,
                    'placeholder' => __('N/A', 'bookingx'),
                    'type' => 'wp_editor',
                    'default_editor' => 'html',
                    'default' => $this->get_default_additional_content(),
                    'option_key' => 'bkx_customer_cancelled_booking_additional_content'
                ),
                'email_type' => array(
                    'title' => __('Email type', 'bookingx'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to send.', 'bookingx'),
                    'default' => 'html',
                    'css' => 'width:400px;',
                    'class' => 'email_type',
                    'options' => $this->get_email_type_options(),
                    'option_key' => 'bkx_customer_cancelled_booking_email_type'
                ),
            );
        }
    }

endif;

return new BKX_Email_Customer_Cancelled_Booking();
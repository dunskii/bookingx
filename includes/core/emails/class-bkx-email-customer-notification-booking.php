<?php
/**
 * Class BKX_Email_New_Customer_Notification_Booking file
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'BKX_Email_New_Customer_Notification_Booking' ) ) :

    /**
     * Pending Booking Email.
     *
     * An email sent to the admin when a pending booking is received/paid for.
     */
    class BKX_Email_New_Customer_Notification_Booking extends BookingX_Email_Content {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id             = 'new_customer_notification';
            $this->customer_email = true;
            $this->title          = __( 'New Customer Notification', 'bookingx' );
            $this->description    = __( 'New Customer Notification.', 'bookingx' );
            //$this->template_html  = 'emails/customer-pending-booking.php';
            //$this->template_plain = 'emails/plain/customer-pending-booking.php';
            $this->placeholders   = array( '{dashboard_url}'   => '', '{reset_password_link}' => '' );
            // Triggers for this email.
           // add_action( 'bookingx_booking_status_cancelled_to_ack_notification', array( $this, 'trigger' ), 10, 2 );
           // add_action( 'bookingx_booking_status_pending_to_ack_notification', array( $this, 'trigger' ), 10, 2 );

            // Call parent constructor.
            parent::__construct();

            // Other settings.
            // $this->recipient = $this->get_option( 'admin_email');
            $this->email_type         = $this->get_option( $this->plugin_id . $this->id . '_email_type' );
            $this->enabled            = $this->get_option( $this->plugin_id . $this->id . '_enabled' );
            $this->heading            = $this->get_option( $this->plugin_id . $this->id . '_heading' );
            $subject                  = $this->get_option( $this->plugin_id . $this->id . '_subject' );
            $this->subject            = isset( $subject ) && ! empty( $subject ) ? $subject : $this->get_default_subject();
            $content                  = $this->get_option( $this->plugin_id . $this->id . '_additional_content' );
            $this->additional_content = isset( $content ) && ! empty( $content ) ? $content : $this->get_default_additional_content();
        }

        public function is_enabled() {
            return apply_filters( 'bookingx_email_enabled_' . $this->id, 'yes' === $this->enabled || 1 === $this->enabled, $this->object, $this );
        }

        /**
         * Get email subject.
         */
        public function get_default_subject() {
            return __( '[{site_title}]: Congratulations! Your Account has been created Successfully.', 'bookingx' );
        }

        /**
         * Get email heading.
         */
        public function get_default_heading() {
            return __( 'Congratulations! Your Account has been created Successfully.', 'bookingx' );
        }

        /**
         * Trigger the sending of this email.
         */
        public function trigger( $booking_id, $booking = false ) {
        }

        /**
         * Get content html.
         *
         * @return string
         */
        public function get_content_html() {
            return bkx_get_template_html(
                $this->template_html,
                array(
                    'booking'            => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin'      => true,
                    'plain_text'         => false,
                    'email'              => $this,
                )
            );
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        public function get_content_plain() {
            return bkx_get_template_html(
                $this->template_plain,
                array(
                    'booking'            => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin'      => true,
                    'plain_text'         => true,
                    'email'              => $this,
                )
            );
        }

        /**
         * Default content to show below main email content.
         */
        public function get_default_additional_content() {

            return __(
                '<p>Dear {fname} {lname},</p>
                 <p>Your account has been created successfully. Please check below details : </p>
                 <p>Username : {email}</p>
                 <p>Reset your password, <a href="{reset_password_link}">click here</a>.</p> 
                 <p>Also now you can check your booking details on My Dashboard section, <a href="{dashboard_url}">click here</a>.</p>',
                'bookingx'
            );
        }



        /**
         * Initialise settings form fields.
         */
        public function init_form_fields() {
            /* translators: %s: list of placeholders */
            $placeholder_text  = sprintf( __( 'Available placeholders: %s', 'bookingx' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );
            $this->form_fields = array(
                'enabled'            => array(
                    'title'      => __( 'Enable/Disable', 'bookingx' ),
                    'type'       => 'checkbox',
                    'label'      => __( 'Enable this email notification', 'bookingx' ),
                    'default'    => 'yes',
                    'option_key' => 'bkx_new_customer_notification_booking_enabled',
                ),
                'subject'            => array(
                    'title'       => __( 'Subject', 'bookingx' ),
                    'type'        => 'text',
                    'css'         => 'width:400px;',
                    'description' => $placeholder_text,
                    'placeholder' => $this->get_default_subject(),
                    'default'     => '',
                    'option_key'  => 'bkx_new_customer_notification_booking_subject',
                ),
                'heading'            => array(
                    'title'       => __( 'Email heading', 'bookingx' ),
                    'type'        => 'text',
                    'css'         => 'width:400px;',
                    'description' => $placeholder_text,
                    'placeholder' => $this->get_default_heading(),
                    'default'     => '',
                    'option_key'  => 'bkx_new_customer_notification_booking_heading',
                ),
                'additional_content' => array(
                    'title'          => __( 'Main Body', 'bookingx' ),
                    'description'    => __( 'Text to appear below the main email content.', 'bookingx' ) . ' ' . $placeholder_text,
                    'placeholder'    => __( 'N/A', 'bookingx' ),
                    'type'           => 'wp_editor',
                    'default_editor' => 'html',
                    'default'        => $this->get_default_additional_content(),
                    'option_key'     => 'bkx_new_customer_notification_booking_additional_content',
                ),
                'email_type'         => array(
                    'title'       => __( 'Email type', 'bookingx' ),
                    'type'        => 'select',
                    'description' => __( 'Choose which format of email to send.', 'bookingx' ),
                    'default'     => 'html',
                    'css'         => 'width:400px;',
                    'class'       => 'email_type',
                    'options'     => $this->get_email_type_options(),
                    'option_key'  => 'bkx_new_customer_notification_booking_email_type',
                ),
            );
        }
    }

endif;

return new BKX_Email_New_Customer_Notification_Booking();
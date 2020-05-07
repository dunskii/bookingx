<?php defined( 'ABSPATH' ) || exit;
/**
 * Email Content Settings
 * A quick shout-out to those who worked on WooCommerce for the inspiration on a good way to handle email content
 */

/**
 * Class BookingX_Email_Content
 */
class BookingX_Email_Content{

    /**
     * Email method ID.
     *
     * @var String
     */
    public $id;

    /**
    * @var string
     */
    public $plugin_id = 'bkx_';

    /**
    * @var array
    */
    public $emails = array();

    /**
    * @var array
    */
    public $form_fields = array();

    /**
     * Email method title.
     *
     * @var string
     */
    public $title;

    /**
     * 'yes' if the method is enabled.
     *
     * @var string yes, no
     */
    public $enabled;

    /**
     * Description for the email.
     *
     * @var string
     */
    public $description;

    /**
     * Default heading.
     * @var string
     */
    public $heading = '';

    /**
     * Default subject.
     * @var string
     */
    public $subject = '';

    /**
     * Plain text template path.
     *
     * @var string
     */
    public $template_plain;

    /**
     * HTML template path.
     *
     * @var string
     */
    public $template_html;

    /**
     * Template path.
     *
     * @var string
     */
    public $template_base;

    /**
     * Recipients for the email.
     *
     * @var string
     */
    public $recipient;

    /**
     * Object this email is for, for example a customer, product, or email.
     *
     * @var object|bool
     */
    public $object;

    /**
     * True when email is being sent.
     *
     * @var bool
     */
    public $sending;

    /**
     * True when the email notification is sent manually only.
     *
     * @var bool
     */
    protected $manual = false;

    /**
     * True when the email notification is sent to customers.
     *
     * @var bool
     */
    protected $customer_email = false;

    /**
     * Strings to find/replace in subjects/headings.
     *
     * @var array
     */
    protected $placeholders = array();

    /**
    * @var array
    */
    public $find = array();

    /**
    * @var array
    */
    public $replace = array();

    /**
    * @var array
    */
    public $additional_content = array();

    /**
    * @var mixed|string|void
    */
    public $email_type = "";

    /**
     * BookingX_Email_Content constructor.
     */
    public function __construct()
    {
        // Find/replace.
        $this->placeholders = array_merge(
            array(
                '{site_title}'   => $this->get_blogname(),
                '{site_address}' => wp_parse_url( home_url(), PHP_URL_HOST ),
                '{fname}' => '','{lname}' => '',
                '{total_price}' => '','{txn_id}' => '',
                '{seat_name}' => '','{base_name}' => '',
                '{additions_list}' => '','{time_of_booking}' => '',
                '{date_of_booking}' => '','{location_of_booking}' => ''
            ),
            $this->placeholders
        );

        // Init settings.
        $this->init_form_fields();

        add_action( 'phpmailer_init', array( $this, 'handle_multipart' ) );
        add_action( 'bookingx_update_options_email_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function admin_options() {
        echo '<table class="form-table">' . $this->generate_settings_html( $this->get_form_fields(), false ) . '</table>'; 
    }

    public function generate_settings_html( $form_fields = array(), $echo = true ) {
        if ( empty( $form_fields ) ) {
            $form_fields = $this->get_form_fields();
        }

        $html = '';
        foreach ( $form_fields as $k => $v ) {
            $type = $this->get_field_type( $v );

            if ( method_exists( $this, 'generate_' . $type . '_html' ) ) {
                $html .= $this->{'generate_' . $type . '_html'}( $k, $v );
            } else {
                $html .= $this->generate_text_html( $k, $v );
            }
        }

        if ( $echo ) {
            echo $html; 
        } else {
            return $html;
        }
    }

    public function get_field_key( $key ) {
        return $this->plugin_id . $this->id . '_' . $key;
    }

    public function generate_text_html( $key, $data ) {

        $field_key = $this->get_field_key( $key );

        $defaults  = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'placeholder'       => '',
            'type'              => 'text',
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array(),
        );

        $data = wp_parse_args( $data, $defaults );

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data );  ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                    <input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $data['option_key'] ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data );  ?> />
                    <?php echo $this->get_description_html( $data );  ?>
                </fieldset>
            </td>
        </tr>
        <?php

        return ob_get_clean();
    }

    public function generate_title_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title' => '',
			'class' => '',
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
			</table>
			<h3 class="bkx-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h3>
			<?php if ( ! empty( $data['description'] ) ) : ?>
				<p><?php echo wp_kses_post( $data['description'] ); ?></p>
			<?php endif; ?>
			<table class="form-table">
		<?php

		return ob_get_clean();
	}

	public function generate_textarea_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data );  ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<textarea rows="3" cols="20" class="input-text wide-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data );  ?>><?php echo esc_textarea( $this->get_option( $data['option_key'] ) ); ?></textarea>
					<?php echo $this->get_description_html( $data );  ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	public function generate_wp_editor_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data );  ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                     <?php
                     $default = $this->get_default_additional_content();
                    $content   = $this->get_option( $data['option_key'], $default );
                    $editor_id = esc_attr( $field_key );
                    $settings  = array( 'media_buttons' => false );
                    wp_editor( $content, $editor_id, $settings );
                     echo $this->get_description_html( $data );
                     ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	public function generate_checkbox_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);
		$data = wp_parse_args( $data, $defaults );

		if ( ! $data['label'] ) {
			$data['label'] = $data['title'];
		}
        $checkbox_value = $this->get_option($data['option_key']);
		$checkbox_value = isset($checkbox_value) && $checkbox_value == 1 ? 'yes' : 'no';
		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data );  ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<label for="<?php echo esc_attr( $field_key ); ?>">
					<input <?php disabled( $data['disabled'], true ); ?> class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="1" <?php checked( $checkbox_value, 'yes'); ?> <?php echo $this->get_custom_attribute_html( $data );  ?> /> <?php echo wp_kses_post( $data['label'] ); ?></label><br/>
					<?php echo $this->get_description_html( $data );  ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	public function generate_select_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data );  ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data );  ?>>
						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
							<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( (string) $option_key, esc_attr( $this->get_option( $data['option_key'] ) ) ); ?>><?php echo esc_attr( $option_value ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php echo $this->get_description_html( $data );  ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	public function generate_multiselect_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'select_buttons'    => false,
			'options'           => array(),
		);

		$data  = wp_parse_args( $data, $defaults );
		$value = (array) $this->get_option( $key, array() );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data );  ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<select multiple="multiple" class="multiselect <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>[]" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data );  ?>>
						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
							<?php if ( is_array( $option_value ) ) : ?>
								<optgroup label="<?php echo esc_attr( $option_key ); ?>">
									<?php foreach ( $option_value as $option_key_inner => $option_value_inner ) : ?>
										<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( in_array( (string) $option_key_inner, $value, true ), true ); ?>><?php echo esc_attr( $option_value_inner ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php else : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( in_array( (string) $option_key, $value, true ), true ); ?>><?php echo esc_attr( $option_value ); ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<?php echo $this->get_description_html( $data );  ?>
					<?php if ( $data['select_buttons'] ) : ?>
						<br/><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'bookingx' ); ?></a> <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'bookingx' ); ?></a>
					<?php endif; ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}



    public function get_custom_attribute_html( $data ) {
        $custom_attributes = array();

        if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {
            foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        return implode( ' ', $custom_attributes );
    }

    public function get_tooltip_html( $data ) {
        if ( true === $data['desc_tip'] ) {
            $tip = $data['description'];
        } elseif ( ! empty( $data['desc_tip'] ) ) {
            $tip = $data['desc_tip'];
        } else {
            $tip = '';
        }

        return $tip ? bkx_help_tip( $tip, true ) : '';
    }

    public function get_description_html( $data ) {
        if ( true === $data['desc_tip'] ) {
            $description = '';
        } elseif ( ! empty( $data['desc_tip'] ) ) {
            $description = $data['description'];
        } elseif ( ! empty( $data['description'] ) ) {
            $description = $data['description'];
        } else {
            $description = '';
        }

        return $description ? '<p class="description">' . wp_kses_post( $description ) . '</p>' . "\n" : '';
    }

    public function get_field_type( $field ) {
        return empty( $field['type'] ) ? 'text' : $field['type'];
    }

    protected function set_defaults( $field ) {
        if ( ! isset( $field['default'] ) ) {
            $field['default'] = '';
        }
        return $field;
    }

    public function get_form_fields() {
        return apply_filters( 'bookingx_settings_api_form_fields_' . $this->id, array_map( array( $this, 'set_defaults' ), $this->form_fields ) );
    }

    public function get_option( $key, $empty_value = null ) {
        $value = bkx_crud_option_multisite($key) ? bkx_crud_option_multisite($key) : $empty_value;
        return apply_filters( 'bookingx_get_option', $value, $this, $value, $key, $empty_value );
    }

    public function handle_multipart( $mailer ) {
        if ( $this->sending && 'multipart' === $this->get_email_type() ) {
            $mailer->AltBody = wordwrap( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
                preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) )
            );
            $this->sending   = false;
        }
        return $mailer;
    }

    public function format_string( $string ) {
        $find    = array_keys( $this->placeholders );
        $replace = array_values( $this->placeholders );

        // If using legacy find replace, add those to our find/replace arrays first.
        $find    = array_merge( (array) $this->find, $find );
        $replace = array_merge( (array) $this->replace, $replace );

        // Take care of blogname which is no longer defined as a valid placeholder.
        $find[]    = '{blogname}';
        $replace[] = $this->get_blogname();

        // If using the older style filters for find and replace, ensure the array is associative and then pass through filters.
        if ( has_filter( 'bookingx_email_format_string_replace' ) || has_filter( 'bookingx_email_format_string_find' ) ) {
            $legacy_find    = $this->find;
            $legacy_replace = $this->replace;

            foreach ( $this->placeholders as $find => $replace ) {
                $legacy_key                    = sanitize_title( str_replace( '_', '-', trim( $find, '{}' ) ) );
                $legacy_find[ $legacy_key ]    = $find;
                $legacy_replace[ $legacy_key ] = $replace;
            }

            $string = str_replace( apply_filters( 'bookingx_email_format_string_find', $legacy_find, $this ), apply_filters( 'bookingx_email_format_string_replace', $legacy_replace, $this ), $string );
        }

        /**
         * Filter for main find/replace.
         */
        return apply_filters( 'bookingx_email_format_string', str_replace( $find, $replace, $string ), $this );
    }

    /**
     * Return email type.
     *
     * @return string
     */
    public function get_email_type() {
        return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
    }

    /**
     * Get email heading.
     */
    public function get_default_heading() {
        return $this->heading;
    }

    /**
     * @return mixed
     */
    public function get_default_subject() {
        return $this->subject;
    }

    /**
     * Default content to show below main email content.
     */
    public function get_default_additional_content() {

        return __( 'Thanks [fname] for booking [base_name],
                Here are your booking details.
        <ul>
            <li>    Booking ID: [order_id]</li>
            <li>    Resource: [seat_name]</li>
            <li>    Service: [base_name]</li>
            <li>    Extras: [additions_list]</li>        
            <li>    Time: [time_of_booking]</li>
            <li>    Date: [date_of_booking]</li>
            <li>    Location: [location_of_booking]</li>
            <li>    Price: [total_price]</li>
            <li>    Amount Paid : [amount_paid]</li>
            <li>    Amount Pending : [amount_pending]</li>
            <li>    Business Name : [business_name]</li>
            <li>    Business Phone : [business_phone]</li>
            <li>    Business Email : [business_email]</li>
            <li>    Booking Status : [booking_status]</li>
        </ul>', 'bookingx' );
    }

    public function get_additional_content() {
        $content = $this->get_option( 'additional_content', '' );

        return apply_filters( 'bookingx_email_additional_content_' . $this->id, $this->format_string( $content ), $this->object, $this );
    }

    public function get_subject() {
        return apply_filters( 'bookingx_email_subject_' . $this->id, $this->format_string( $this->get_option( 'subject', $this->get_default_subject() ) ), $this->object, $this );
    }

    public function get_heading() {
        return apply_filters( 'bookingx_email_heading_' . $this->id, $this->format_string( $this->get_option( 'heading', $this->get_default_heading() ) ), $this->object, $this );
    }

    public function get_headers() {
        $header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

        if ( in_array( $this->id, array( 'new_booking', 'cancelled_booking', 'failed_booking' ), true ) ) {
            if ( $this->object && $this->object->get_billing_email() && ( $this->object->get_billing_first_name() || $this->object->get_billing_last_name() ) ) {
                $header .= 'Reply-to: ' . $this->object->get_billing_first_name() . ' ' . $this->object->get_billing_last_name() . ' <' . $this->object->get_billing_email() . ">\r\n";
            }
        } elseif ( $this->get_from_address() && $this->get_from_name() ) {
            $header .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_address() . ">\r\n";
        }

        return apply_filters( 'bookingx_email_headers', $header, $this->id, $this->object, $this );
    }

    public function get_attachments() {
        return apply_filters( 'bookingx_email_attachments', array(), $this->id, $this->object, $this );
    }

    public function get_title() {
        return apply_filters( 'bookingx_email_title', $this->title, $this );
    }

    public function get_description() {
        return apply_filters( 'bookingx_email_description', $this->description, $this );
    }

    public function get_recipient() {
        $recipient  = apply_filters( 'bookingx_email_recipient_' . $this->id, $this->recipient, $this->object, $this );
        $recipients = array_map( 'trim', explode( ',', $recipient ) );
        $recipients = array_filter( $recipients, 'is_email' );
        return implode( ', ', $recipients );
    }

    public function is_enabled() {
        return apply_filters( 'bookingx_email_enabled_' . $this->id, 'yes' === $this->enabled || 1 === $this->enabled, $this->object, $this );
    }

    public function is_manual() {
        return $this->manual;
    }

    public function is_customer_email() {
        return $this->customer_email;
    }

    public function get_blogname() {
        return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    }


    /**
     * @param string $default_content_type
     * @return mixed|void
     */
    public function get_content_type( $default_content_type = '' ) {
        switch ( $this->get_email_type() ) {
            case 'html':
                $content_type = 'text/html';
                break;
            case 'multipart':
                $content_type = 'multipart/alternative';
                break;
            default:
                $content_type = 'text/plain';
                break;
        }

        return apply_filters( 'bookingx_email_content_type', $content_type, $this, $default_content_type );
    }

    /**
     * Get email content.
     *
     * @return string
     */
    public function get_content() {
        $this->sending = true;

        if ( 'plain' === $this->get_email_type() ) {
            $email_content = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) ), 70 );
        } else {
            $email_content = $this->get_content_html();
        }

        return $email_content;
    }

    /**
     * @param $content
     * @return string
     */
    public function style_inline( $content ) {
        if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ), true ) ) {
            ob_start();
            bkx_get_template( 'emails/email-styles.php' );
            $css = apply_filters( 'bookingx_email_styles', ob_get_clean(), $this );
            $content = '<style type="text/css">' . $css . '</style>' . $content;
        }
        return $content;
    }

    public function get_content_plain() {
        return ''; }

    public function get_content_html() {
        return ''; }

    public function get_from_name( $from_name = '' ) {
        $from_name = apply_filters( 'bookingx_email_from_name', get_option( 'bookingx_email_from_name' ), $this, $from_name );
        return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
    }

    public function get_from_address( $from_email = '' ) {
        $from_email = apply_filters( 'bookingx_email_from_address', get_option( 'bookingx_email_from_address' ), $this, $from_email );
        return sanitize_email( $from_email );
    }

    public function send( $to, $subject, $message, $headers, $attachments ) {
        add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
        add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

        $message              = apply_filters( 'bookingx_mail_content', $this->style_inline( $message ) );
        $mail_callback        = apply_filters( 'bookingx_mail_callback', 'wp_mail', $this );
        $mail_callback_params = apply_filters( 'bookingx_mail_callback_params', array( $to, $subject, $message, $headers, $attachments ), $this );
        $return               = $mail_callback( ...$mail_callback_params );

        remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
        remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
        remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

        return $return;
    }

    /**
     * Initialise Settings Form Fields - these are generic email options most will use.
     */
    public function init_form_fields() {
        /* translators: %s: list of placeholders */
        $placeholder_text  = sprintf( __( 'Available placeholders: %s', 'bookingx' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
        $this->form_fields = array(
            'enabled'            => array(
                'title'   => __( 'Enable/Disable', 'bookingx' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable this email notification', 'bookingx' ),
                'default' => 'yes',
            ),
            'subject'            => array(
                'title'       => __( 'Subject', 'bookingx' ),
                'type'        => 'text',
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_subject(),
                'default'     => '',
            ),
            'heading'            => array(
                'title'       => __( 'Email heading', 'bookingx' ),
                'type'        => 'text',
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_heading(),
                'default'     => '',
            ),
            'additional_content' => array(
                'title'       => __( 'Additional content', 'bookingx' ),
                'description' => __( 'Text to appear below the main email content.', 'bookingx' ) . ' ' . $placeholder_text,
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => __( 'N/A', 'bookingx' ),
                'type'        => 'textarea',
                'default'     => $this->get_default_additional_content(),
            ),
            'email_type'         => array(
                'title'       => __( 'Email type', 'bookingx' ),
                'type'        => 'select',
                'description' => __( 'Choose which format of email to send.', 'bookingx' ),
                'default'     => 'html',
                'class'       => 'email_type bkx-enhanced-select',
                'options'     => $this->get_email_type_options(),
            ),
        );
    }

    public function get_email_type_options() {
        $types = array( 'plain' => __( 'Plain text', 'bookingx' ) );

        if ( class_exists( 'DOMDocument' ) ) {
            $types['html']      = __( 'HTML', 'bookingx' );
            $types['multipart'] = __( 'Multipart', 'bookingx' );
        }

        return $types;
    }

    public function email_notification_setting() {
        $current_main_tab = sanitize_text_field($_GET['bkx_tab']);
        $section = sanitize_text_field($_GET['section']);
        $sub_tab = sanitize_text_field($_GET['sub_tab']);
        if( $current_main_tab == 'bkx_general' && $section == 'emails' && isset($sub_tab) && $sub_tab != '')
            return;

        // Define emails that can be customised here.
        $screen = get_current_screen();
        $mailer          = new BKX_Emails_Setup();
        $email_templates = $mailer->get_emails();
        ?>
        <tr valign="top">
            <td class="bkx_emails_wrapper" colspan="2">
                <table class="bkx_emails widefat" cellspacing="0">
                    <thead>
                    <tr>
                        <?php
                        $columns = apply_filters(
                            'bookingx_email_setting_columns',
                            array(
                                'status'     => __( 'Activation Status', 'bookingx' ),
                                'name'       => __( 'Email', 'bookingx' ),
                                'email_type' => __( 'Content type', 'bookingx' ),
                                'recipient'  => __( 'Recipient(s)', 'bookingx' ),
                                'actions'    => '',
                            )
                        );
                        foreach ( $columns as $key => $column ) {
                            echo '<th class="bkx-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ( $email_templates as $email_key => $email ) {
                        echo '<tr>';

                        foreach ( $columns as $key => $column ) {
                            $generate_tab_url = $screen->parent_file.'&page=bkx-setting&bkx_tab=bkx_general&section=emails&sub_tab='.strtolower( $email_key );
                            switch ( $key ) {
                                case 'name':
                                    echo '<td class="bkx-email-settings-table-' . esc_attr( $key ) . '">
										<a href="' . esc_url( $generate_tab_url ) . '">' . esc_html( $email->get_title() ) . '</a>
										' . bkx_help_tip( $email->get_description() ) . '
									</td>';
                                    break;
                                case 'recipient':
                                    echo '<td class="bkx-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->is_customer_email() ? __( 'Customer', 'bookingx' ) : $email->get_recipient() ) . '
									</td>';
                                    break;
                                case 'status':
                                    echo '<td class="bkx-email-settings-table-' . esc_attr( $key ) . '">';
                                    //echo '<pre>',print_r($email,1),'</pre>';
                                    if ( $email->is_manual() ) {
                                        echo '<span class="status-manual tips" data-tip="' . esc_attr__( 'Manually sent', 'bookingx' ) . '">' . esc_html__( 'Manual', 'bookingx' ) . '</span>';
                                    } elseif ( $email->enabled ) {
                                        echo '<span class="status-enabled tips" data-tip="' . esc_attr__( 'Enabled', 'bookingx' ) . '">' . esc_html__( 'Yes', 'bookingx' ) . '</span>';
                                    } else {
                                        echo '<span class="status-disabled tips" data-tip="' . esc_attr__( 'Disabled', 'bookingx' ) . '">-</span>';
                                    }

                                    echo '</td>';
                                    break;
                                case 'email_type':
                                    echo '<td class="bkx-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->get_content_type() ) . '
									</td>';
                                    break;
                                case 'actions':
                                    echo '<td class="bkx-email-settings-table-' . esc_attr( $key ) . '">
										<a class="button alignright" href="' . esc_url($generate_tab_url ) . '">' . esc_html__( 'Manage', 'bookingx' ) . '</a>
									</td>';
                                    break;
                                default:
                                    do_action( 'bookingx_email_setting_column_' . $key, $email );
                                    break;
                            }
                        }

                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php
    }
}
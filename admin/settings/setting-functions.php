<?php
/**
 * Template load for Save all setting action
 *
 * @package Bookingx/admin
 * @since      1.0.7
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'bkx_setting_save_init' );
/**
 * Save Setting Action Hook
 * bkx_setting_save_init()
 */
function bkx_setting_save_init() {
	if ( isset( $_POST['bkx_setting_form_init'] ) && ( 1 === sanitize_text_field( wp_unslash( $_POST['bkx_setting_form_init'] ) ) || '1' === sanitize_text_field( wp_unslash( $_POST['bkx_setting_form_init'] ) ) ) ) { // phpcs:ignore
		bkx_setting_save_action();
	}
	if ( isset( $_POST['bkx_license_activation'] ) ) {
		bookingx_addon_activate_license();
	}
}

if ( ! function_exists( 'bkx_setting_tabs' ) ) {
	/**
	 * Bkx_setting_tabs
	 *
	 * @return mixed|void
	 */
	function bkx_setting_tabs() {
		$tab = array();

		$tab['bkx_general'] = array(
			'label'   => 'General',
			'submenu' => array(
				'alias'           => 'Alias',
				'content_setting' => 'Content Settings',
				'emails'          => 'Email notifications',
				'css'             => 'Styling',
				'page_settings'   => 'Page Settings',
				'exim'            => 'Export/Import',
				//'other'           => 'Other Settings',
			),
			'default' => apply_filters( 'bkx_setting_general_default', 'alias' ),
		);
		$bkx_payment_tab    = array(
			'bkx_gateway_paypal_express' => 'PayPal Express',
			'tax_settings'               => 'Tax Settings',
			'currency'                   => 'Currency',
		);
		$tab['bkx_payment'] = array(
			'label'   => 'Payment',
			'submenu' => apply_filters( 'bkx_payment_tab', $bkx_payment_tab ),
			'default' => apply_filters( 'bkx_setting_payment_default', 'bkx_gateway_paypal_express' ),
		);

		$tab['bkx_biz'] = array(
			'label'   => 'Business Information',
			'submenu' => array(
				'biz_info' => esc_html( 'General Details' ),
				'days_ope' => esc_html( 'Days of Operations' ),
				'user_opt' => esc_html( 'User Options' ),
			),
			'default' => apply_filters( 'bkx_setting_biz_default', 'biz_info' ),
		);

		$tab['bkx_licence'] = array(
			'label'   => 'Licences',
			'submenu' => array(
				'all_license' => esc_html( 'Booking X Addon Licences' ),
			),
			'default' => apply_filters( 'bkx_setting_licence_default', 'all_license' ),
		);

		return apply_filters( 'bkx_setting_tabs', $tab );
	}
}

/**
 * Generate Business Days of Operation
 *
 * @param int   $set Set Default Value.
 * @param array $selected Selected.
 *
 * @return string
 */
function bkx_generate_days_section( $set = 7, $selected = array() ): string {
	$generate_html = '';
	$next          = 1;
	if ( ! empty( $selected ) && is_array( $selected ) ) {
		$gen          = 1;
		$next         = count( $selected ) + 1;
		$display_hide = '';
		foreach ( $selected as $key => $value ) {

			$days           = $value['days'];
			$time           = $value['time'];
			$sun_selected   = '';
			$mon_selected   = '';
			$tue_selected   = '';
			$wed_selected   = '';
			$thu_selected   = '';
			$fri_selected   = '';
			$sat_selected   = '';
			$generate_html .= '<div class="day_section_' . esc_attr( $gen ) . '" ' . esc_attr( $display_hide ) . '><li class="standard"><label for="dayselection" class="lable">Choose a day  </label>
			       <select id="business_days_' . esc_attr( $gen ) . '" name="days_' . esc_attr( $gen ) . '[]" multiple="multiple" style="display: none;">';
			if ( ! empty( $days ) && is_array( $days ) ) {
				if ( in_array( 'Sunday', $days, true ) ) {
					$sun_selected = 'selected="selected"';
				}
				if ( in_array( 'Monday', $days, true ) ) {
					$mon_selected = 'selected="selected"';
				}
				if ( in_array( 'Tuesday', $days, true ) ) {
					$tue_selected = 'selected="selected"';
				}
				if ( in_array( 'Wednesday', $days, true ) ) {
					$wed_selected = 'selected="selected"';
				}
				if ( in_array( 'Thursday', $days, true ) ) {
					$thu_selected = 'selected="selected"';
				}
				if ( in_array( 'Friday', $days, true ) ) {
					$fri_selected = 'selected="selected"';
				}
				if ( in_array( 'Saturday', $days, true ) ) {
					$sat_selected = 'selected="selected"';
				}
			}
			$generate_html .= '<option value="Sunday" ' . esc_attr( $sun_selected ) . '>Sunday</option>
									<option value="Monday" ' . esc_attr( $mon_selected ) . '>Monday</option>
									<option value="Tuesday" ' . esc_attr( $tue_selected ) . '>Tuesday</option>
									<option value="Wednesday" ' . esc_attr( $wed_selected ) . '>Wednesday</option>
									<option value="Thursday" ' . esc_attr( $thu_selected ) . '>Thursday</option>
									<option value="Friday" ' . esc_attr( $fri_selected ) . '>Friday</option>
									<option value="Saturday" ' . esc_attr( $sat_selected ) . '>Saturday</option>';

			$generate_html .= '</select>
				</li>

				<li class="standard"><label for="opening time" class="lable">Open </label>
				<input name="opening_time_' . esc_attr( $gen ) . '" id="id_opening_time_' . esc_attr( $gen ) . '" type="text" value="' . esc_attr( $time['open'] ) . '">
				</li>

				<li class="standard"><label for="closing time" class="lable">Close </label>
				<input name="closing_time_' . esc_attr( $gen ) . '" id="id_closing_time_' . esc_attr( $gen ) . '" type="text" value="' . esc_attr( $time['close'] ) . '">
				</li>';
			if ( $gen > 1 ) {
				$generate_html .= '<li class="standard close" style="width:100px;"><label for="opening time" class="lable">&nbsp; </label><a class="business_hour_close" href="javascript:remove_more_days(' . esc_attr( $gen ) . ');">X</a></li>';
			}
			$generate_html .= '<div class="clear"></div></div>';

			$gen ++;
		}
	}
	for ( $gen = $next; $gen <= $set; $gen ++ ) {
		$display_hide = '';
		if ( $gen > 1 ) {
			$display_hide = 'style="display:none;"';
		}
		$generate_html .= '<div class="day_section_' . esc_attr( $gen ) . '" ' . $display_hide . '><li class="standard"><label for="dayselection" class="lable">Choose a day  </label>
               <select id="business_days_' . esc_attr( $gen ) . '" name="days_' . esc_attr( $gen ) . '[]" multiple="multiple" style="display: none;">
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                </select>
            </li>
        
            <li class="standard"><label for="opening time" class="lable">Open </label>
            <input name="opening_time_' . esc_attr( $gen ) . '" id="id_opening_time_' . esc_attr( $gen ) . '" type="text" value="10:00 AM">
            </li>
        
            <li class="standard"><label for="closing time" class="lable">Close </label>
            <input name="closing_time_' . esc_attr( $gen ) . '" id="id_closing_time_' . esc_attr( $gen ) . '" type="text" value="5:00 PM">
            </li>';
		if ( $gen > 1 ) {
			$generate_html .= '<li class="standard close" style="width:100px;"><label for="opening time" class="lable">&nbsp; </label><a class="business_hour_close" href="javascript:remove_more_days(' . esc_attr( $gen ) . ');">X</a></li>';
		}
		$generate_html .= '<div class="clear"></div></div>';
	}

	return $generate_html;
}

/**
 * Bkx_admin_success_message
 *
 * @param null $key Success Key.
 *
 * @return mixed
 */
function bkx_admin_success_message( $key = null ) {
	$admin_success_msg = array(
		'FIS'  => esc_html__( 'File Imported successfully', 'bookingx' ),
		'PAU'  => esc_html__( 'PayPal API Settings updated successfully.', 'bookingx' ),
		'GAI'  => esc_html__( 'Google Map API Settings updated successfully.', 'bookingx' ),
		'OSE'  => esc_html__( 'Other Settings updated successfully.', 'bookingx' ),
		'COU'  => esc_html__( 'Currency Settings updated successfully.', 'bookingx' ),
		'RAU'  => esc_html__( 'Role Assignment Settings updated successfully.', 'bookingx' ),
		'ALU'  => esc_html__( 'Alias Settings updated successfully.', 'bookingx' ),
		'GCD'  => esc_html__( 'Google Calendar Settings updated successfully.', 'bookingx' ),
		'CSU'  => esc_html__( 'Content Setting updated successfully.', 'bookingx' ),
		'OSU'  => esc_html__( 'Option Settings updated successfully.', 'bookingx' ),
		'STU'  => esc_html__( 'Styling Settings updated successfully.', 'bookingx' ),
		'BIU'  => esc_html__( 'Business Information Settings updated successfully.', 'bookingx' ),
		'TSU'  => esc_html__( 'Tax Settings updated successfully.', 'bookingx' ),
		'DOP'  => esc_html__( 'Days of Operations Settings updated successfully.', 'bookingx' ),
		'ESS'  => esc_html__( 'Email Settings updated successfully.', 'bookingx' ),
		'OTHS' => esc_html__( 'Other Settings updated successfully.', 'bookingx' ),
	);
	$admin_success_msg = apply_filters( 'bkx_admin_success_message', $admin_success_msg );

	return $admin_success_msg[ $key ];
}

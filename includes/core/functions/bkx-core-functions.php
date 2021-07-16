<?php
/**
 * @param $name
 * @param $value
 */
function bkx_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * @param  $seat_id
 * @param  $start_date
 * @param  $end_date
 * @param  $service_id
 * @return array
 */
function bkx_reassign_available_emp_list( $seat_id, $start_date, $end_date, $service_id ) {
	if ( is_multisite() ) :
		$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
		switch_to_blog( $blog_id );
	endif;

	$return         = array();
	$booked_seat_id = '';
	$bkx_baseObj    = new BkxBase( '', $service_id );
	$get_employees  = $bkx_baseObj->get_seat_by_base();
	$key_rm         = array_search( $seat_id, $get_employees );
	if ( $key_rm !== false ) {
		unset( $get_employees[ $key_rm ] );
	}
	$order_statuses = array( 'bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed' );
	if ( ! empty( $get_employees ) ) {
		foreach ( $get_employees as $get_employee ) :
			$args          = array(
				'post_type'   => 'bkx_booking',
				'post_status' => $order_statuses,
				'meta_key'    => 'seat_id',
				'meta_value'  => $get_employee,
				'meta_query'  => array(
					array(
						'key'     => 'booking_start_date',
						'value'   => date( 'Y-m-d H:i:s', strtotime( $start_date ) ), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
						'compare' => '<=',
					),
					array(
						'key'     => 'booking_end_date',
						'value'   => date( 'Y-m-d H:i:s', strtotime( $end_date ) ), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
						'compare' => '>=',
					),
				),
			);
			$booked_result = new WP_Query( $args );
			// echo "<pre>".print_r($booked_result, true)."</pre>";
			if ( $booked_result->have_posts() ) {
				$BookingObj     = $booked_result->post;
				$booked_seat_id = get_post_meta( $BookingObj->ID, 'seat_id', true );
			}
			if ( $booked_seat_id == '' ) {
				$bkx_seatObj = new BkxSeat( '', $get_employee );
				$return[]    = array(
					'id'   => $bkx_seatObj->post->ID,
					'name' => $bkx_seatObj->post->post_title,
				);
			}
		endforeach;
	}
	if ( is_multisite() ) :
		restore_current_blog();
	endif;
	return $return;
}

/**
 * @param  $slot_id
 * @param  $get_booked_slot
 * @param  $get_free_seats
 * @param  $total_seats
 * @param  $bookigndate
 * @param  $seat_id
 * @return array
 */
function bkx_get_booked_slot( $slot_id, $get_booked_slot, $get_free_seats, $total_seats, $bookigndate = null, $seat_id = null ): array {
	if ( is_multisite() ) :
		$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
		switch_to_blog( $blog_id );
	endif;
	$send_seat_block_data = array();
	$slots_blocked        = array();
	if ( $_SESSION['_display_seat_slots'] > 1 ) {
		$_display_seat_slots = array_map( 'sanitize_text_field', wp_unslash( $_SESSION['_display_seat_slots'] ) );
		for ( $i = 0; $i < $_display_seat_slots; $i++ ) {
			$find_slot_value_in_booked_array[] = ( $slot_id ) + $i;
		}
	} else {
		$find_slot_value_in_booked_array[] = $slot_id;
	}
	// echo '<pre>' . print_r($find_slot_value_in_booked_array, true) . '</pre>';
	if ( ! empty( $get_booked_slot ) ) :
		foreach ( $get_booked_slot as $_slot_id => $booked_slot ) {
			foreach ( $booked_slot as $seat_id => $slots ) {
				foreach ( $find_slot_value_in_booked_array as $_find_slot ) {
					if ( in_array( $_find_slot, $slots ) ) {
						// echo 'Now find slot id is : ' . $_find_slot ."<br>";
						$now_booked_slots_is[]                        = $slots;
						$now_booked_slots_with_seats[ $_find_slot ][] = $seat_id;
					}
				}
			}
		}
		// echo '<pre> $now_booked_slots_with_seats ' . print_r($now_booked_slots_with_seats, true) . '</pre>';
		if ( ! empty( $now_booked_slots_with_seats ) ) {
			foreach ( $now_booked_slots_with_seats as $booked_slot_id => $booked_seats ) {
				// echo '<pre>'.$booked_slot_id.'==Total ='.$total_seats.'=='.count($booked_seats).'=== $booked_seats' . print_r($booked_seats, true) . '</pre>';
				if ( count( $booked_seats ) > 0 && $total_seats > 0 && ( count( $booked_seats ) == $total_seats ) ) {
					$slots_blocked[] = $booked_slot_id;
				}
			}
		}
		/**
		 * Find Seat where other slot are available
		 */
	endif;

	if ( ! empty( $slots_blocked ) ) :
		$send_seat_block_data['slots_blocked'] = $slots_blocked;
	endif;

	if ( is_multisite() ) :
		restore_current_blog();
	endif;
	return $send_seat_block_data;
}

/**
 * @param  $booking_date
 * @param  $seatid
 * @return array
 */
function bkx_get_range( $booking_date, $seatid, $array = false ) {
	if ( is_multisite() ) :
		$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
		switch_to_blog( $blog_id );
	endif;

	$booking_date = date( 'm/d/Y', strtotime( $booking_date ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
	$explode_date = explode( '/', $booking_date );
	$timestamp    = strtotime( "{$explode_date[2]}-{$explode_date[0]}-{$explode_date[1]}" );
	$day          = date( 'w', $timestamp ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
	$day_name     = bkx_getDayName( $day );

	$GetSeatObj = get_post( $seatid );
	$res_seat   = get_post_custom( $GetSeatObj->ID );

	$db_res_seat_time_arr = array();
	$bkx_business_days    = bkx_crud_option_multisite( 'bkx_business_days' );
	if ( ! is_array( $bkx_business_days ) ) {
		$bkx_business_days = maybe_unserialize( $bkx_business_days );
	}

	// $seat_days_time = maybe_unserialize($res_seat['seat_days_time'][0]);
	$seat_is_certain_day = $res_seat['seat_is_certain_day'][0];

	if ( $seat_is_certain_day == 'Y' ) {
		$selected_days = get_post_meta( $GetSeatObj->ID, 'selected_days', true );
	}

	$selected_days = ( ! empty( $selected_days ) ? $selected_days : $bkx_business_days );
	if ( ! empty( $selected_days ) ) {
		foreach ( $selected_days as $key => $days_obj ) {
			$days_data = $days_obj['days'];
			$time_data = $days_obj['time'];
			foreach ( $days_data as $key => $day ) {
				$days_ope[ $day ] = array(
					'day'       => $day,
					'time_from' => $time_data['open'],
					'time_till' => $time_data['close'],
				);
			}
		}
	}

	$db_res_seat_time_arr = $days_ope;
	if ( ! empty( $db_res_seat_time_arr ) ) {
		foreach ( $db_res_seat_time_arr as $day_db => $time_data ) {
			$day_is    = strtolower( $time_data['day'] );
			$time_from = $time_data['time_from'];
			$time_till = $time_data['time_till'];
			if ( isset( $day_is ) && $day_is == strtolower( $day_name ) ) {
				$res_seat_time_arr['time_from'] = $time_from;
				$res_seat_time_arr['time_till'] = $time_till;
			}
		}
	}

	if ( isset( $res_seat_time_arr['time_from'] ) && $res_seat_time_arr['time_from'] != '' ) {
		$time_value     = '';
		$time_from      = $res_seat_time_arr['time_from'];
		$time_till      = $res_seat_time_arr['time_till'];
		$time_from_arr  = explode( ' ', $time_from );
		$time_till_arr  = explode( ' ', $time_till );
		$hrsMinsFromArr = explode( ':', $time_from_arr[0] );
		$hrsFrom        = $hrsMinsFromArr[0];
		$minsFrom       = $hrsMinsFromArr[1];

		if ( $time_from_arr[1] == 'AM' ) {
			if ( $hrsFrom == 12 ) {
				$hrsFrom = '00';
			}
			$time_from = "{$hrsFrom}:{$minsFrom}";
		} elseif ( $time_from_arr[1] == 'PM' ) {
			if ( $hrsFrom == 12 ) {
				$hrsFrom == 12;
			} else {
				$hrsFrom = intval( $hrsFrom ) + 12;
			}
			$time_from = "{$hrsFrom}:{$minsFrom}";
		}
		$hrsMinsTillArr = explode( ':', $time_till_arr[0] );
		$hrsTill        = $hrsMinsTillArr[0];
		$minsTill       = $hrsMinsTillArr[1];
		// print_r($time_till_arr);
		if ( $time_till_arr[1] == 'AM' ) {
			if ( $hrsTill == 12 && $time_from_arr[1] == 'AM' ) {
				$hrsTill = '24';
			}
			$time_till = "{$hrsTill}:{$minsTill}";
		} elseif ( $time_till_arr[1] == 'PM' ) {
			if ( $hrsTill == 12 ) {
				$hrsTill == 12;
			} else {
				$hrsTill = intval( $hrsTill ) + 12;
			}
			$time_till = "{$hrsTill}:{$minsTill}";
		}

		$timefromArr = explode( ':', $time_from );

		$minsCounter   = bkx_getMinsSlot( $timefromArr[1] );
		$timefrom_slot = $timefromArr[0] * 4 + $minsCounter;

		$timetillArr     = explode( ':', $time_till );
		$minsCounterTill = bkx_getMinsSlot( $timetillArr[1] );
		$timetill_slot   = $timetillArr[0] * 4 + $minsCounterTill;
		$range           = range( $timefrom_slot, $timetill_slot - 1 );
		if ( $array == true ) {
			$range['range']     = $range;
			$range['time_data'] = $res_seat_time_arr;
		}
	} else {
		// $range = range(1, 96);
		$range = array();
	}

	if ( is_multisite() ) :
		restore_current_blog();
	endif;

	return $range;
}

/**
 * @param $template_name
 * @param string        $args
 */
function bkx_get_template( $template_name, $args = '' ) {
	if ( isset( $template_name ) && $template_name != '' ) {
		$bkx_plugin_dir_path = apply_filters( 'bkx_plugin_dir_path_override', BKX_PLUGIN_DIR_PATH );
		$plugin_path         = $bkx_plugin_dir_path . 'templates/';
		$file_path           = $plugin_path . '' . $template_name;
		if ( ! file_exists( $file_path ) ) {
			return;
		}
		$action_args = array(
			'template_name' => $template_name,
			'template_path' => $plugin_path,
			'located'       => $file_path,
			'args'          => $args,
		);

		if ( ! empty( $args ) && is_array( $args ) ) {
			if ( isset( $args['action_args'] ) ) {
				unset( $args['action_args'] );
			}
			extract( $args );
		}
		do_action( 'bkx_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
		include $action_args['located'];

		do_action( 'bkx_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
	}
}

/**
 * @param  $column
 * @return int
 */
function block_column_cal( $column ) {
	$base = 12;
	if ( $column == 1 ) {
		$value = $base;
	} else {
		$value = intval( $base / $column );
	}
	return $value;
}

/**
 * When the_post is called, put Bookingx post into a global.
 *
 * @param  mixed $post
 * @return post object
 */
function bkx_setup_post_data( $post ) {
	if ( is_multisite() ) :
		$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
		switch_to_blog( $blog_id );
	endif;

	unset( $GLOBALS['bkx_seat'] );
	unset( $GLOBALS['bkx_base'] );
	unset( $GLOBALS['bkx_addition'] );
	unset( $GLOBALS['bkx_post'] );

	global $bkx_seat, $bkx_base, $bkx_addition;

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'bkx_seat', 'bkx_base', 'bkx_addition' ) ) ) {
		return;
	}

	if ( isset( $post->post_type ) && $post->post_type == 'bkx_seat' ) {
		$GLOBALS[ $post->post_type ] = new BkxSeat( $post );
	} elseif ( isset( $post->post_type ) && $post->post_type == 'bkx_base' ) {
		$GLOBALS[ $post->post_type ] = new BkxBase( $post );
	} elseif ( isset( $post->post_type ) && $post->post_type == 'bkx_addition' ) {
		$GLOBALS[ $post->post_type ] = new BkxExtra( $post );
	}
	if ( is_multisite() ) :
		restore_current_blog();
	endif;
	return $GLOBALS[ $post->post_type ];
}

add_action( 'the_post', 'bkx_setup_post_data' );

/**
 * @param string $size
 *
 * @return mixed|void
 */
function bkx_placeholder_img_src( $size = 'service-thumb' ) {
	return apply_filters( 'bkx_placeholder_img_src', BKX_PLUGIN_DIR_URL . 'images/placeholder.png' );
}

/**
 * @param string $size
 *
 * @return mixed|void
 */
function bkx_placeholder_img( $size = 'service-thumb' ) {
	if ( is_multisite() ) :
		$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
		switch_to_blog( $blog_id );
	endif;
	$dimensions['width']  = 300;
	$dimensions['height'] = 300;
	if ( is_multisite() ) :
		restore_current_blog();
	endif;
	return apply_filters( 'bkx_placeholder_img', '<img src="' . bkx_placeholder_img_src() . '" alt="' . esc_attr__( 'Placeholder', 'bookingx' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="bkx-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
}

/**
 * @return mixed|void
 */
function bkx_get_wp_country() {
	$all_countries = array(
		'AF' => 'Afghanistan',
		'AX' => 'Aland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia, Plurinational State of',
		'BQ' => 'Bonaire, Sint Eustatius and Saba',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, the Democratic Republic of the',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => "Cote d'Ivoire",
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CW' => 'Curacao',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island and McDonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KP' => "Korea, Democratic People's Republic",
		'KR' => 'Korea, Republic of',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => "Lao People's Democratic Republic",
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States of',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestine, State of',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint-Barthelemy',
		'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin (French part)',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome and Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SX' => 'Sint Maarten (Dutch part)',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia and the South Sandwich Islands',
		'SS' => 'South Sudan',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan, Province of China',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania, United Republic of',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Minor Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'WF' => 'Wallis and Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);
	return array_unique( apply_filters( 'bookingx_country', $all_countries ) );
}

/**
 * Get full list of currency codes.
 *
 * @return array
 */
function get_bookingx_currencies() {
	$country_arr = array(
		'AED' => __( 'United Arab Emirates dirham', 'bookingx' ),
		'AFN' => __( 'Afghan afghani', 'bookingx' ),
		'ALL' => __( 'Albanian lek', 'bookingx' ),
		'AMD' => __( 'Armenian dram', 'bookingx' ),
		'ANG' => __( 'Netherlands Antillean guilder', 'bookingx' ),
		'AOA' => __( 'Angolan kwanza', 'bookingx' ),
		'ARS' => __( 'Argentine peso', 'bookingx' ),
		'AUD' => __( 'Australian dollar', 'bookingx' ),
		'AWG' => __( 'Aruban florin', 'bookingx' ),
		'AZN' => __( 'Azerbaijani manat', 'bookingx' ),
		'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'bookingx' ),
		'BBD' => __( 'Barbadian dollar', 'bookingx' ),
		'BDT' => __( 'Bangladeshi taka', 'bookingx' ),
		'BGN' => __( 'Bulgarian lev', 'bookingx' ),
		'BHD' => __( 'Bahraini dinar', 'bookingx' ),
		'BIF' => __( 'Burundian franc', 'bookingx' ),
		'BMD' => __( 'Bermudian dollar', 'bookingx' ),
		'BND' => __( 'Brunei dollar', 'bookingx' ),
		'BOB' => __( 'Bolivian boliviano', 'bookingx' ),
		'BRL' => __( 'Brazilian real', 'bookingx' ),
		'BSD' => __( 'Bahamian dollar', 'bookingx' ),
		'BTC' => __( 'Bitcoin', 'bookingx' ),
		'BTN' => __( 'Bhutanese ngultrum', 'bookingx' ),
		'BWP' => __( 'Botswana pula', 'bookingx' ),
		'BYR' => __( 'Belarusian ruble', 'bookingx' ),
		'BZD' => __( 'Belize dollar', 'bookingx' ),
		'CAD' => __( 'Canadian dollar', 'bookingx' ),
		'CDF' => __( 'Congolese franc', 'bookingx' ),
		'CHF' => __( 'Swiss franc', 'bookingx' ),
		'CLP' => __( 'Chilean peso', 'bookingx' ),
		'CNY' => __( 'Chinese yuan', 'bookingx' ),
		'COP' => __( 'Colombian peso', 'bookingx' ),
		'CRC' => __( 'Costa Rican col&oacute;n', 'bookingx' ),
		'CUC' => __( 'Cuban convertible peso', 'bookingx' ),
		'CUP' => __( 'Cuban peso', 'bookingx' ),
		'CVE' => __( 'Cape Verdean escudo', 'bookingx' ),
		'CZK' => __( 'Czech koruna', 'bookingx' ),
		'DJF' => __( 'Djiboutian franc', 'bookingx' ),
		'DKK' => __( 'Danish krone', 'bookingx' ),
		'DOP' => __( 'Dominican peso', 'bookingx' ),
		'DZD' => __( 'Algerian dinar', 'bookingx' ),
		'EGP' => __( 'Egyptian pound', 'bookingx' ),
		'ERN' => __( 'Eritrean nakfa', 'bookingx' ),
		'ETB' => __( 'Ethiopian birr', 'bookingx' ),
		'EUR' => __( 'Euro', 'bookingx' ),
		'FJD' => __( 'Fijian dollar', 'bookingx' ),
		'FKP' => __( 'Falkland Islands pound', 'bookingx' ),
		'GBP' => __( 'Pound sterling', 'bookingx' ),
		'GEL' => __( 'Georgian lari', 'bookingx' ),
		'GGP' => __( 'Guernsey pound', 'bookingx' ),
		'GHS' => __( 'Ghana cedi', 'bookingx' ),
		'GIP' => __( 'Gibraltar pound', 'bookingx' ),
		'GMD' => __( 'Gambian dalasi', 'bookingx' ),
		'GNF' => __( 'Guinean franc', 'bookingx' ),
		'GTQ' => __( 'Guatemalan quetzal', 'bookingx' ),
		'GYD' => __( 'Guyanese dollar', 'bookingx' ),
		'HKD' => __( 'Hong Kong dollar', 'bookingx' ),
		'HNL' => __( 'Honduran lempira', 'bookingx' ),
		'HRK' => __( 'Croatian kuna', 'bookingx' ),
		'HTG' => __( 'Haitian gourde', 'bookingx' ),
		'HUF' => __( 'Hungarian forint', 'bookingx' ),
		'IDR' => __( 'Indonesian rupiah', 'bookingx' ),
		'ILS' => __( 'Israeli new shekel', 'bookingx' ),
		'IMP' => __( 'Manx pound', 'bookingx' ),
		'INR' => __( 'Indian rupee', 'bookingx' ),
		'IQD' => __( 'Iraqi dinar', 'bookingx' ),
		'IRR' => __( 'Iranian rial', 'bookingx' ),
		'ISK' => __( 'Icelandic kr&oacute;na', 'bookingx' ),
		'JEP' => __( 'Jersey pound', 'bookingx' ),
		'JMD' => __( 'Jamaican dollar', 'bookingx' ),
		'JOD' => __( 'Jordanian dinar', 'bookingx' ),
		'JPY' => __( 'Japanese yen', 'bookingx' ),
		'KES' => __( 'Kenyan shilling', 'bookingx' ),
		'KGS' => __( 'Kyrgyzstani som', 'bookingx' ),
		'KHR' => __( 'Cambodian riel', 'bookingx' ),
		'KMF' => __( 'Comorian franc', 'bookingx' ),
		'KPW' => __( 'North Korean won', 'bookingx' ),
		'KRW' => __( 'South Korean won', 'bookingx' ),
		'KWD' => __( 'Kuwaiti dinar', 'bookingx' ),
		'KYD' => __( 'Cayman Islands dollar', 'bookingx' ),
		'KZT' => __( 'Kazakhstani tenge', 'bookingx' ),
		'LAK' => __( 'Lao kip', 'bookingx' ),
		'LBP' => __( 'Lebanese pound', 'bookingx' ),
		'LKR' => __( 'Sri Lankan rupee', 'bookingx' ),
		'LRD' => __( 'Liberian dollar', 'bookingx' ),
		'LSL' => __( 'Lesotho loti', 'bookingx' ),
		'LYD' => __( 'Libyan dinar', 'bookingx' ),
		'MAD' => __( 'Moroccan dirham', 'bookingx' ),
		'MDL' => __( 'Moldovan leu', 'bookingx' ),
		'MGA' => __( 'Malagasy ariary', 'bookingx' ),
		'MKD' => __( 'Macedonian denar', 'bookingx' ),
		'MMK' => __( 'Burmese kyat', 'bookingx' ),
		'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'bookingx' ),
		'MOP' => __( 'Macanese pataca', 'bookingx' ),
		'MRO' => __( 'Mauritanian ouguiya', 'bookingx' ),
		'MUR' => __( 'Mauritian rupee', 'bookingx' ),
		'MVR' => __( 'Maldivian rufiyaa', 'bookingx' ),
		'MWK' => __( 'Malawian kwacha', 'bookingx' ),
		'MXN' => __( 'Mexican peso', 'bookingx' ),
		'MYR' => __( 'Malaysian ringgit', 'bookingx' ),
		'MZN' => __( 'Mozambican metical', 'bookingx' ),
		'NAD' => __( 'Namibian dollar', 'bookingx' ),
		'NGN' => __( 'Nigerian naira', 'bookingx' ),
		'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'bookingx' ),
		'NOK' => __( 'Norwegian krone', 'bookingx' ),
		'NPR' => __( 'Nepalese rupee', 'bookingx' ),
		'NZD' => __( 'New Zealand dollar', 'bookingx' ),
		'OMR' => __( 'Omani rial', 'bookingx' ),
		'PAB' => __( 'Panamanian balboa', 'bookingx' ),
		'PEN' => __( 'Peruvian nuevo sol', 'bookingx' ),
		'PGK' => __( 'Papua New Guinean kina', 'bookingx' ),
		'PHP' => __( 'Philippine peso', 'bookingx' ),
		'PKR' => __( 'Pakistani rupee', 'bookingx' ),
		'PLN' => __( 'Polish z&#x142;oty', 'bookingx' ),
		'PRB' => __( 'Transnistrian ruble', 'bookingx' ),
		'PYG' => __( 'Paraguayan guaran&iacute;', 'bookingx' ),
		'QAR' => __( 'Qatari riyal', 'bookingx' ),
		'RON' => __( 'Romanian leu', 'bookingx' ),
		'RSD' => __( 'Serbian dinar', 'bookingx' ),
		'RUB' => __( 'Russian ruble', 'bookingx' ),
		'RWF' => __( 'Rwandan franc', 'bookingx' ),
		'SAR' => __( 'Saudi riyal', 'bookingx' ),
		'SBD' => __( 'Solomon Islands dollar', 'bookingx' ),
		'SCR' => __( 'Seychellois rupee', 'bookingx' ),
		'SDG' => __( 'Sudanese pound', 'bookingx' ),
		'SEK' => __( 'Swedish krona', 'bookingx' ),
		'SGD' => __( 'Singapore dollar', 'bookingx' ),
		'SHP' => __( 'Saint Helena pound', 'bookingx' ),
		'SLL' => __( 'Sierra Leonean leone', 'bookingx' ),
		'SOS' => __( 'Somali shilling', 'bookingx' ),
		'SRD' => __( 'Surinamese dollar', 'bookingx' ),
		'SSP' => __( 'South Sudanese pound', 'bookingx' ),
		'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'bookingx' ),
		'SYP' => __( 'Syrian pound', 'bookingx' ),
		'SZL' => __( 'Swazi lilangeni', 'bookingx' ),
		'THB' => __( 'Thai baht', 'bookingx' ),
		'TJS' => __( 'Tajikistani somoni', 'bookingx' ),
		'TMT' => __( 'Turkmenistan manat', 'bookingx' ),
		'TND' => __( 'Tunisian dinar', 'bookingx' ),
		'TOP' => __( 'Tongan pa&#x2bb;anga', 'bookingx' ),
		'TRY' => __( 'Turkish lira', 'bookingx' ),
		'TTD' => __( 'Trinidad and Tobago dollar', 'bookingx' ),
		'TWD' => __( 'New Taiwan dollar', 'bookingx' ),
		'TZS' => __( 'Tanzanian shilling', 'bookingx' ),
		'UAH' => __( 'Ukrainian hryvnia', 'bookingx' ),
		'UGX' => __( 'Ugandan shilling', 'bookingx' ),
		'USD' => __( 'United States dollar', 'bookingx' ),
		'UYU' => __( 'Uruguayan peso', 'bookingx' ),
		'UZS' => __( 'Uzbekistani som', 'bookingx' ),
		'VEF' => __( 'Venezuelan bol&iacute;var', 'bookingx' ),
		'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'bookingx' ),
		'VUV' => __( 'Vanuatu vatu', 'bookingx' ),
		'WST' => __( 'Samoan t&#x101;l&#x101;', 'bookingx' ),
		'XAF' => __( 'Central African CFA franc', 'bookingx' ),
		'XCD' => __( 'East Caribbean dollar', 'bookingx' ),
		'XOF' => __( 'West African CFA franc', 'bookingx' ),
		'XPF' => __( 'CFP franc', 'bookingx' ),
		'YER' => __( 'Yemeni rial', 'bookingx' ),
		'ZAR' => __( 'South African rand', 'bookingx' ),
		'ZMW' => __( 'Zambian kwacha', 'bookingx' ),
	);
	asort( $country_arr, SORT_STRING | SORT_FLAG_CASE );
	return array_unique( apply_filters( 'bookingx_currencies', $country_arr ) );
}

/**
 * Get Currency symbol.
 *
 * @param  string $currency (default: '')
 * @return string
 */
function get_bookingx_currency_symbol( $currency = '' ) {
	$symbols         = apply_filters(
		'bookingx_currency_symbols',
		array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => '&fnof;',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x10da;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'ISK' => 'Kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x43b;&#x432;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;. &#x645;.',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'L',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => '&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STD' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'Fr',
			'XCD' => '&#36;',
			'XOF' => 'Fr',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		)
	);
	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';
	return apply_filters( 'bookingx_currency_symbol', $currency_symbol, $currency );
}

/**
 * @return string
 */
function bkx_get_current_currency() {
	if ( is_multisite() ) :
		$blog_id = apply_filters( 'bkx_set_blog_id', get_current_blog_id() );
		switch_to_blog( $blog_id );
	endif;
	if ( is_multisite() ) {
		$current_blog_id = get_current_blog_id();
		$currency_option = get_blog_option( $current_blog_id, 'currency_option' );
	} else {
		$currency_option = bkx_crud_option_multisite( 'currency_option' );
	}
	if ( is_multisite() ) :
		restore_current_blog();
	endif;
	return get_bookingx_currency_symbol( $currency_option );
}

/**
 * @param $array_or_string
 *
 * @return array|string
 */
function bkx_sanitize_text_or_array_field( $array_or_string ) {
	if ( is_string( $array_or_string ) ) {
		$array_or_string = sanitize_text_field( $array_or_string );
	} else {
		$array_or_string = array_map( 'sanitize_text_field', wp_unslash( $array_or_string ) );
	}
	return $array_or_string;
}

/**
 *    For bkx_crud_option_multisite() update_option() and add_option() for compatible for multisite
 *
 * @param  $option_name
 * @param  null        $option_val
 * @param  string      $type
 * @return mixed
 */
function bkx_crud_option_multisite( $option_name, $option_val = null, $type = 'get' ) {
	$get_current_blog_id = get_current_blog_id();
	if ( is_multisite() ) {
		switch_to_blog( $get_current_blog_id );
	}
	switch ( $type ) {
		case 'update':
		case 'add':
			$arg = ( is_multisite() ) ? array( $get_current_blog_id, $option_name, $option_val ) : array( $option_name, $option_val );
			break;
		case 'get':
			$arg = ( is_multisite() ) ? array( $get_current_blog_id, $option_name ) : array( $option_name );
			break;
		default:
			break;
	}
	return ( is_multisite() ) ? call_user_func_array( $type . '_blog_option', $arg ) : call_user_func_array( $type . '_option', $arg );
}

/**
 * @return string
 */
function bkx_get_loader() {
	return '<div class="bookingx-loader" style="display:none">Loading&#8230;</div>';
}

/**
 * @param $tip
 * @param false $allow_html
 *
 * @return string
 */
function bkx_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = bkx_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="bookingx-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Skip Step 1 if Admin Edit booking
 *
 * @return string
 */
function SkipStep() {
	$skip_this_step = false;
	if ( is_admin() ) {
		$screen    = get_current_screen();
		$base      = $screen->base;
		$post_type = $screen->post_type;
		if ( $base == 'post' && $post_type == 'bkx_booking' && isset( $_REQUEST['post'] ) && sanitize_text_field( $_REQUEST['post'] ) > 0 ) { // phpcs:disable
			$skip_this_step = true;
		}
	} elseif ( isset( $_REQUEST['edit_booking_nonce'] ) && wp_verify_nonce( $_REQUEST['edit_booking_nonce'], 'edit_booking_' . sanitize_text_field( $_REQUEST['id'] ) ) ) {
		$skip_this_step = true;
	}
	return $skip_this_step;
}

/**
 * @param $var
 *
 * @return string
 */
function bkx_sanitize_tooltip( $var ) {
	return htmlspecialchars(
		wp_kses(
			html_entity_decode( $var ),
			array(
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'small'  => array(),
				'span'   => array(),
				'ul'     => array(),
				'li'     => array(),
				'ol'     => array(),
				'p'      => array(),
			)
		)
	);
}

/**
 * @param $start
 * @param $end
 * @param string $format
 *
 * @return array|void
 * @throws Exception
 */
function bkx_getDatesFromRange( $start, $end, $format = 'm/d/Y' ) {
	if ( empty( $start ) && empty( $end ) ) {
		return;
	}
	$range         = array();
	$start         = strtotime( $start );
	$end           = date( 'Y-m-d', strtotime( '+1 day', strtotime( $end ) ) );
	$new['first']  = date( 'Y-m-d', $start );
	$new['last']   = date( 'Y-m-d', strtotime( $end ) );
	$interval      = '+1 days';
	$date_interval = DateInterval::createFromDateString( $interval );
	$periods       = new DatePeriod( new DateTime( $new['first'] ), $date_interval, new DateTime( $new['last'] ) );
	foreach ( $periods as $period ) {
		$range[] = $period->format( $format );
	}
	return $range;
}

/**
 * @param $var
 *
 * @return array|mixed|string
 */
function bkx_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'bkx_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 *
 */
function bkx_get_order_statuses() {
	$order_statuses = array(
		'bkx-pending'   => _x( 'Pending', 'Order status', 'bookingx' ),
		'bkx-ack'       => _x( 'Acknowledged', 'Order status', 'bookingx' ),
		'bkx-completed' => _x( 'Completed', 'Order status', 'bookingx' ),
		'bkx-missed'    => _x( 'Missed', 'Order status', 'bookingx' ),
		'bkx-cancelled' => _x( 'Cancelled', 'Order status', 'bookingx' ),
		'bkx-failed'    => _x( 'Failed', 'Order status', 'bookingx' ),
	);
	// return apply_filters('bkx_order_statuses', $order_statuses);
}

/**
 *
 */
function bkx_setting_page_callback() {
	include BKX_PLUGIN_DIR_PATH . 'admin/settings.php';
}

/**
 * @param $subject
 * @param $data_html
 * @param $to_mail
 * @param string $mail_type
 */
function bkx_mail_format_and_send_process( $subject, $data_html, $to_mail, $mail_type = 'text/html' ) {
	if ( isset( $to_mail ) ) {
		$admin_email = bkx_crud_option_multisite( 'admin_email' );
		$headers[]   = 'Content-Type: ' . $mail_type . '; charset=UTF-8';
		$headers[]   = 'From: ' . get_bloginfo() . ' <' . $admin_email . '>';
		/*
		if ($cc_mail != '') {
		 $headers[] = 'Cc: ' . $cc_mail . ' <' . $cc_mail . '>';
		}
		if ($bcc_mail != '') {
		 $headers[] = 'Bcc: ' . $bcc_mail;
		}*/
		wp_mail( $to_mail, $subject, $data_html, $headers );
	}
}

/**
 * @return mixed|void
 */
function bkx_localize_string_text() {
	$seat_alias          = bkx_crud_option_multisite( 'bkx_alias_seat' );
	$base_alias          = bkx_crud_option_multisite( 'bkx_alias_base' );
	$extra_alias         = bkx_crud_option_multisite( 'bkx_alias_addition' );
	$validation_js_array = array(
		'string_enter_other_day'       => sprintf( __( '%s.', 'bookingx' ), 'Please select other day to booking' ),
		'string_booking_dates'         => sprintf( __( '%s', 'bookingx' ), 'Booking Dates' ),
		'string_selecte_a_time'        => sprintf( __( '%s', 'bookingx' ), 'Select a time' ),
		'email_sent'                   => sprintf( __( '%s', 'bookingx' ), 'Email sent successfully.' ),
		'disable_days_note'            => sprintf( __( '%s', 'bookingx' ), 'Days available if applicable.' ),
		'disable_slots_note'           => sprintf( __( '%s', 'bookingx' ), 'Please try to select available date in Calendar.' ),
		'string_contact_administrator' => sprintf( __( '%s.', 'bookingx' ), 'You Can Diretly Contact Administrator and proceed with booking payment' ),
		'string_list_not_found'        => sprintf( __( '%1$s %2$s', 'bookingx' ), 'Couldn\'t list', esc_html__( $base_alias, 'bookingx' ) ),
		'string_select_a_seat'         => sprintf( __( '%1$s %2$s.', 'bookingx' ), 'Please select a', esc_html__( $seat_alias, 'bookingx' ) ),
		'string_select_a_base'         => sprintf( __( '%1$s %2$s.', 'bookingx' ), 'Please select a', esc_html__( $base_alias, 'bookingx' ) ),
		'string_extend_base'           => sprintf( __( '%1$s %2$s %3$s.', 'bookingx' ), 'Please enter extended', esc_html__( $base_alias, 'bookingx' ), 'time' ),
		'string_choose_date'           => sprintf( __( '%s.', 'bookingx' ), 'Please choose a date' ),
		'string_choose_enddate'        => sprintf( __( '%s.', 'bookingx' ), 'Please choose a end date' ),
		'string_choose_time'           => sprintf( __( '%s.', 'bookingx' ), 'Please choose time' ),
		'string_select_date_time'      => sprintf( __( '%s', 'bookingx' ), 'Please select date and time.' ),
		'bkx_validate_day_select'      => sprintf( __( '%s', 'bookingx' ), 'Please select valid dates.' ),
		'bkx_first_name'               => sprintf( __( '%s.', 'bookingx' ), 'Please enter your first name' ),
		'bkx_last_name'                => sprintf( __( '%s.', 'bookingx' ), 'Please enter your last name' ),
		'bkx_phone_number'             => sprintf( __( '%s.', 'bookingx' ), 'Please enter phone number' ),
		'bkx_valid_phone_number'       => sprintf( __( '%s.', 'bookingx' ), 'Please enter valid phone number' ),
		'bkx_email_address'            => sprintf( __( '%s.', 'bookingx' ), 'Please enter email address' ),
		'bkx_email_valid'              => sprintf( __( '%s.', 'bookingx' ), 'Please enter valid email address' ),
		'string_enter_street'          => sprintf( __( '%s.', 'bookingx' ), 'Please enter your Street.' ),
		'string_enter_city'            => sprintf( __( '%s.', 'bookingx' ), 'Please enter your City.' ),
		'string_enter_state'           => sprintf( __( '%s.', 'bookingx' ), 'Please enter your State / Province / Region' ),
		'string_enter_zip'             => sprintf( __( '%s.', 'bookingx' ), 'Please enter your Zip / Postal Code.' ),
		'bkx_terms_and_conditions'     => sprintf( __( '%s.', 'bookingx' ), 'Please checked our terms & condtion.' ),
		'bkx_privacy_policy'           => sprintf( __( '%s.', 'bookingx' ), 'Please checked our privacy policy.' ),
		'bkx_cancellation_policy'      => sprintf( __( '%s.', 'bookingx' ), 'Please checked our cancellation policy.' ),
		'string_invalid_email'         => sprintf( __( '%s.', 'bookingx' ), 'You have entered an invalid email address!' ),
		'string_success_reassign'      => sprintf( __( '%.s', 'bookingx' ), 'Booking Successfully Reassign.' ),
		'string_reassign_booking'      => sprintf( __( '%s', 'bookingx' ), 'Reassign Booking' ),
		'booking_button'               => sprintf( __( '%s', 'bookingx' ), 'Process Booking' ),
		'payment_method'               => sprintf( __( '%s', 'bookingx' ), 'Please select payment method.' ),
		'complete_form'                => sprintf( __( '%s', 'bookingx' ), 'Please complete below form.' ),
		'paypal_error'                 => sprintf( __( '%s', 'bookingx' ), 'Payment was cancelled via PayPal.' ),
		'string_do_you_want_to_cont'   => sprintf( __( '%s', 'bookingx' ), 'Do you want to continue update booking?' ),
		'string_something_went'        => sprintf( __( '%s', 'bookingx' ), 'Sorry ! Something went wrong , Please try again.' ),
		'string_you_require'           => sprintf( __( '%s', 'bookingx' ), 'you require' ),
	);
	return apply_filters( 'bkx_localized_validation_js_array', $validation_js_array );
}

/**
 * @param int    $get_total_time_of_services
 * @param string $type
 * @return string
 * @throws Exception
 */
function bkx_total_time_of_services_formatted( $get_total_time_of_services = 0, $type = 'H' ): string {
	$total_time_of_services_formatted = '';
	if ( ! empty( $get_total_time_of_services ) && $get_total_time_of_services != 0 ) {
		$minutes = $get_total_time_of_services;

		if ( $type == 'H' ) {
			$zero   = new DateTime( '@0' );
			$offset = new DateTime( '@' . $minutes * 60 );
			$diff   = $zero->diff( $offset );
			if ( $minutes > 60 ) {
				$total_time_of_services_formatted = " ({$diff->format('%h Hours %i Minutes')})";
			} elseif ( $minutes == 60 ) {
				$total_time_of_services_formatted = " ({$diff->format('%h Hour')})";
			} else {
				$total_time_of_services_formatted = " ({$diff->format('%i Minutes')})";
			}
		}
		if ( $type == 'D' ) {
			// $base_day = $minutes / 24;
			$total_time_of_services_formatted = bkx_convert_seconds( $get_total_time_of_services );
			/*
			if(isset($base_day) && $base_day > 0 && $base_day < 2 ){
			 $total_time_of_services_formatted  = sprintf(__('%1$s Day', 'bookingx'), $base_day);
			}
			if(isset($base_day) && $base_day > 0 && $base_day > 1 ){
			 $total_time_of_services_formatted  = sprintf(__('%1$s Days', 'bookingx'), $base_day);
			}*/
		}
	}

	return $total_time_of_services_formatted;
}

/**
 * @param $seconds
 *
 * @return string
 */
function bkx_convert_seconds( $seconds ): string {
	$dt1       = new DateTime( '@0' );
	$dt2       = new DateTime( "@$seconds" );
	$days      = $dt1->diff( $dt2 )->d;
	$hours     = $dt1->diff( $dt2 )->h;
	$minutes   = $dt1->diff( $dt2 )->i;
	$formatted = '';
	if ( $hours > 0 && $minutes < 1 ) {
		$formatted = $dt1->diff( $dt2 )->format( ' %a Days %h Hours ' );
	} elseif ( $hours > 0 && $minutes > 0 ) {
		$formatted = $dt1->diff( $dt2 )->format( ' %a Days %h Hours and %i Minutes ' );
	} elseif ( $hours < 1 && $minutes > 0 ) {
		$formatted = $dt1->diff( $dt2 )->format( ' %a Days %i Minutes ' );
	} elseif ( $hours < 1 && $minutes < 1 && $days > 1 ) {
		$formatted = $dt1->diff( $dt2 )->format( ' %a Days ' );
	} elseif ( $days == 1 ) {
		$formatted = $dt1->diff( $dt2 )->format( ' %a Day ' );
	} else {
		$formatted = $dt1->diff( $dt2 )->format( ' %a Days %h Hours and %i Minutes ' );
	}
	return $formatted;
}

/**
 * @param $seconds
 *
 * @return string
 */
function bkx_convert_for_hours( $seconds ): string {
	$dt1 = new DateTime( '@0' );
	$dt2 = new DateTime( "@$seconds" );
	return $dt1->diff( $dt2 )->format( ' %h Hours and %i Minutes ' );
}

/**
 * @return mixed|void
 */
function bkx_generate_inline_style() {
	$my_theme    = wp_get_theme();
	$text_domain = esc_html( $my_theme->get( 'TextDomain' ) );
	$booked      = $open = $current = $selected_time_color = '';
	// if(!empty($my_theme) && $text_domain == 'twentytwenty'){
	if ( class_exists( 'BKX_Twenty_Twenty' ) ) {
		$BKX_Twenty_Twenty = new BKX_Twenty_Twenty();
		$default_colors    = $BKX_Twenty_Twenty->default_colors();
		if ( ! empty( $default_colors ) ) {
			$booked              = $default_colors['booked'];
			$open                = $default_colors['open'];
			$current             = $default_colors['current'];
			$selected_time_color = $default_colors['selected_time_color'];
			$day_selected        = sanitize_hex_color( twentytwenty_get_color_for_area( 'content', 'accent' ) );
			$day_selected        = isset( $day_selected ) && $day_selected != '' ? $day_selected : '#cd2653';

		}
	}

	if ( class_exists( 'BKX_Twenty_Nineteen' ) ) {
		$BKX_Twenty_Nineteen = new BKX_Twenty_Nineteen();
		$default_colors      = $BKX_Twenty_Nineteen->default_colors();
		if ( ! empty( $default_colors ) ) {
			$booked              = $default_colors['booked'];
			$open                = $default_colors['open'];
			$current             = $default_colors['current'];
			$selected_time_color = $default_colors['selected_time_color'];
			$day_selected        = '#0073aa';
		}
	}

	if ( class_exists( 'BKX_Twenty_Seventeen' ) ) {
		$BKX_Twenty_Seventeen = new BKX_Twenty_Seventeen();
		$default_colors       = $BKX_Twenty_Seventeen->default_colors();
		if ( ! empty( $default_colors ) ) {
			$booked              = $default_colors['booked'];
			$open                = $default_colors['open'];
			$current             = $default_colors['current'];
			$selected_time_color = $default_colors['selected_time_color'];
			$day_selected        = '#222';
		}
	}

	if ( class_exists( 'BKX_Twenty_Twenty_One' ) ) {
		$BKX_Twenty_Seventeen = new BKX_Twenty_Twenty_One();
		$default_colors       = $BKX_Twenty_Seventeen->default_colors();
		if ( ! empty( $default_colors ) ) {
			$booked              = $default_colors['booked'];
			$open                = $default_colors['open'];
			$current             = $default_colors['current'];
			$selected_time_color = $default_colors['selected_time_color'];
			$day_selected        = '#222';
		}
	}

	$custom_css = '';
	$text_color = bkx_crud_option_multisite( 'bkx_form_text_color' );
	$text_color = ( $text_color ) ? $text_color : '';

	$cal_day_color = bkx_crud_option_multisite( 'bkx_cal_day_color' );
	$cal_day_color = ( $cal_day_color ) ? $cal_day_color : '';

	$cal_day_selected_color = bkx_crud_option_multisite( 'bkx_cal_day_selected_color' );
	$cal_day_selected_color = ( $cal_day_selected_color ) ? $cal_day_selected_color : '';

	$time_available_color = bkx_crud_option_multisite( 'bkx_time_available_color' );
	$time_available_color = ( $time_available_color ) ? $time_available_color : "{$open}";

	$bkx_active_step_color = bkx_crud_option_multisite( 'bkx_active_step_color' );
	$bkx_active_step_color = ( $bkx_active_step_color ) ? $bkx_active_step_color : 'currentColor';

	$time_selected_color = bkx_crud_option_multisite( 'bkx_time_selected_color' );
	$time_selected_color = ( $time_selected_color ) ? $time_selected_color : "{$current}";

	$time_unavailable_color = bkx_crud_option_multisite( 'bkx_time_unavailable_color' );
	$time_unavailable_color = ( $time_unavailable_color ) ? $time_unavailable_color : "{$booked}";

	$slot_time_color = bkx_crud_option_multisite( 'bkx_form_slot_time_color' );
	$slot_time_color = ( $slot_time_color ) ? $slot_time_color : '';

	$bkx_cal_month_title = bkx_crud_option_multisite( 'bkx_cal_month_title_color' ) != '' ? bkx_crud_option_multisite( 'bkx_cal_month_title_color' ) : '#222222'; // Calendar Month title
	$bkx_cal_month_title = ( $bkx_cal_month_title ) ? $bkx_cal_month_title : '';

	$bkx_previous_btn = bkx_crud_option_multisite( 'bkx_prev_btn' );
	$bkx_previous_btn = ( $bkx_previous_btn ) ? $bkx_previous_btn : '';
	$bkx_next_btn     = bkx_crud_option_multisite( 'bkx_next_btn' );
	$bkx_next_btn     = ( $bkx_next_btn ) ? $bkx_next_btn : '';
	$bkx_pay_now_btn  = bkx_crud_option_multisite( 'bkx_pay_now_btn' );
	$bkx_pay_now_btn  = ( $bkx_pay_now_btn ) ? $bkx_pay_now_btn : '';

	if ( is_user_logged_in() && is_admin() ) {
		$color_schema = adminColorSchema();
		if ( ! empty( $color_schema ) ) {
			$bkx_previous_btn       = $color_schema['button'];
			$bkx_previous_btn       = ( $bkx_previous_btn ) ? $bkx_previous_btn : 'currentColor';
			$bkx_next_btn           = $color_schema['button'];
			$bkx_next_btn           = ( $bkx_next_btn ) ? $bkx_next_btn : '';
			$time_available_color   = $color_schema['open'];
			$time_unavailable_color = $color_schema['booked'];
			$time_selected_color    = $color_schema['current'];
			$bkx_active_step_color  = $color_schema['button'];
		}
	}

	if ( isset( $time_available_color, $slot_time_color ) && $slot_time_color != '' && $time_available_color != '' ) {
		$custom_css .= " .booking-x .indicator ul li.open::before, .booking-x .select-time .table td a.available { background: {$time_available_color}; color: {$slot_time_color} } ";
	}
	if ( isset( $time_unavailable_color ) && $time_unavailable_color != '' ) {
		$custom_css .= " .booking-x .indicator ul li.booked::before, .booking-x .select-time .table td a.disabled { background: {$time_unavailable_color};} ";
	}
	if ( is_user_logged_in() && is_admin() ) {
		$custom_css .= " .bkx-admin-new-booking .indicator ul li.current::before, .bkx-admin-new-booking .select-time .table td a.selected { background: linear-gradient(to bottom right, {$time_available_color} 0%, {$time_available_color} 50%, {$time_selected_color} 50%, {$time_selected_color} 100%); } ";
		//$custom_css .= " .booking-x .indicator ul li.current::before, .booking-x .select-time .table td a.selected { background: {$time_selected_color}; } ";
		$custom_css .= " .booking-x .indicator ul li.current::before, .booking-x .select-time .table td a.bkx-current-edit-slot { background: linear-gradient(to bottom right, {$time_available_color} 0%, {$time_available_color} 50%, {$time_selected_color} 50%, {$time_selected_color} 100%); } ";
		$custom_css .= " .bkx-admin-edit-booking .select-time .table td a.selected { color : #fff;background: {$time_available_color}; } ";
		$custom_css .= " .booking-x .indicator ul li.selected::before { background: {$time_available_color}; }";
		$custom_css .= " .bkx-admin-edit-booking .indicator ul li::before { width: 30px !important; } .bkx-admin-edit-booking .indicator ul li {margin-right : 5px !important; font-size: 13px !important;}";
	} else {
		$custom_css .= " .booking-x .indicator ul li.current::before, .booking-x .select-time .table td a.selected { background: {$time_selected_color}; } ";
	}
	$custom_css .= " .bkx-front-edit-booking .indicator ul li.selected::before { background: {$time_selected_color}; }";
	$custom_css .= " .bkx-front-new-booking .indicator ul li.selected::before { background: {$time_selected_color}; }";
	$custom_css .= " .bkx-front-edit-booking .indicator ul li::before { width: 44px !important; } .bkx-front-edit-booking .indicator ul li {margin-right : 5px !important; font-size: 18px !important;}";
	$custom_css .= " .bkx-front-edit-booking .indicator ul li.current::before, .bkx-front-edit-booking .select-time .table td a.bkx-current-edit-slot { color:#fff; background: linear-gradient(to bottom right, {$time_selected_color} 0%, {$time_selected_color} 50%, {$time_unavailable_color} 50%, {$time_unavailable_color} 100%); } ";
	$custom_css .= " .bkx-front-edit-booking .select-time .table td a.selected { color : #fff;background: {$time_selected_color}; } ";
	if ( isset( $text_color ) && $text_color != '' ) {
		$custom_css .= " .booking-x-form h1 ,.booking-x-form h2, .booking-x-form h3, .booking-x-form h4, .booking-x-form h5 , .booking-x-form .progress-title, .booking-x-form .form-group label, .booking-x-form .user-detail dl dt, .booking-x .indicator ul li, .booking-x .total-text { color: {$text_color} }";
	}
	if ( isset( $bkx_active_step_color ) && $bkx_active_step_color != '' ) {
		$custom_css .= " .booking-x-form .booking-x .progress-step.is-active::after { background-color: {$bkx_active_step_color} }";
	}
	if ( isset( $cal_day_selected_color ) && $cal_day_selected_color != '' ) {
		$custom_css .= " .booking-x-form .booking-x .on-click-selected { background: {$cal_day_selected_color} }";
	}
	if ( isset( $cal_day_color ) && $cal_day_color != '' ) {
		$custom_css .= " .booking-x-form .booking-x .bkx-cal-enable { background: {$cal_day_color} }";
	}
	if ( isset( $bkx_active_step_color ) && $bkx_active_step_color != '' ) {
		$custom_css .= " .booking-x .progress-step.is-active .progress-title, .booking-x-form .booking-x .progress-step.is-active::after { color: {$bkx_active_step_color} }";
	}
	if ( isset( $bkx_active_step_color ) && $bkx_active_step_color != '' ) {
		$custom_css .= " .booking-x .progress-step.is-active .progress-title, .booking-x-form .booking-x .progress-step.is-active::after { color: {$bkx_active_step_color} }";
	}
	if ( isset( $bkx_next_btn ) && $bkx_next_btn != '' ) {
		$custom_css .= " .booking-x-form .booking-x .bkx-form-submission-next { background: {$bkx_next_btn} }";
	}
	if ( isset( $bkx_previous_btn ) && $bkx_previous_btn != '' ) {
		$custom_css .= " .booking-x-form .booking-x .bkx-form-submission-previous { background: {$bkx_previous_btn} }";
	}
	if ( isset( $bkx_pay_now_btn ) && $bkx_pay_now_btn != '' ) {
		$custom_css .= " .post-type-bkx_booking .bkx-booking-form .bkx-form-submission-final { background: {$bkx_pay_now_btn} }";
	}
	if ( isset( $bkx_pay_now_btn ) && $bkx_pay_now_btn != '' ) {
		$custom_css .= " .booking-x-form .booking-x .bkx-form-submission-final, .booking-x-form .booking-x .bkx-send-email-receipt, .booking-x-form .booking-x .bkx-back-to-booking{ background: {$bkx_pay_now_btn} }";
	}
	$custom_css .= " .booking-x-form .booking-x .bkx-calendar .calendar-month .text-uppercase { color: {$bkx_cal_month_title} }";

	if ( isset( $day_selected ) ) {
		$custom_css .= " .booking-x-form .booking-x .day-click-selected { background: {$day_selected}; color : #fff; }";
	}

	return apply_filters( 'bkx_generate_inline_style', $custom_css );
}

/**
 * @param $post
 *
 * @return mixed|void
 */
function bkx_generate_thumbnail( $post ) {
	if ( empty( $post ) ) {
		return;
	}

	if ( has_post_thumbnail( $post ) ) {
		$image = get_the_post_thumbnail(
			$post->post->ID,
			apply_filters( 'bkx_single_post_large_thumbnail_size', 'bkx_single' ),
			array(
				'title' => $post->get_title(),
				'class' => 'img-thumbnail',
				'alt'   => $post->get_title(),
			)
		);
	} else {
		$image = '<img width="250" height="180" src="' . BKX_PLUGIN_PUBLIC_URL . '"/images/placeholder.png\'" class="img-thumbnail wp-post-image" alt="' . $post->get_title() . '" title="' . $post->get_title() . '">';
	}

	return apply_filters(
		'bookingx_single_post_image_html',
		sprintf(
			'<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="%s">%s</a>',
			esc_url( get_permalink() ),
			esc_attr( $post->get_title() ),
			'',
			$image
		),
		$post->post->ID
	);
}

/**
 * @param $date
 * @param string $format
 * @return false|int|string
 */
function date_format_correct( $date, $format = 'm/d/Y' ) {
	$date          = date( 'm/d/Y', strtotime( $date ) );
	$date_arr      = explode( '/', $date );
	$date_formated = strtotime( "{$date_arr[2]}/{$date_arr[1]}/{$date_arr[0]}" );// "Y-m-d"
	$date_formated = date( $format, $date_formated );
	return $date_formated;
}

/**
 * @param $selected_time
 * @return float|int|void
 */
function GenerateSlotByTime( $selected_time ) {
	if ( empty( $selected_time ) ) {
		return;
	}
	$counter      = 0;
	$booking_hour = explode( ':', $selected_time );
	$hours_temp   = $booking_hour[0];
	if ( isset( $booking_hour[1] ) ) {
		$minutes_temp = $booking_hour[1];
		$counter      = BkxGenerateCounter( $minutes_temp );
	}
	return intval( $hours_temp ) * 4 + $counter;
}

/**
 * @param $minutes_temp
 * @return int
 */
function BkxGenerateCounter( $minutes_temp ) {
	$counter = 0;
	if ( intval( $minutes_temp ) == 0 ) {
		$counter = 1;
	} elseif ( intval( $minutes_temp ) == 15 ) {
		$counter = 2;
	} elseif ( intval( $minutes_temp ) == 30 ) {
		$counter = 3;
	} elseif ( intval( $minutes_temp ) == 45 ) {
		$counter = 4;
	}
	return $counter;
}

/**
 * @return mixed|void
 */
function is_bookingx() {
	return apply_filters(
		'is_bookingx',
		is_bkx_singular() ||
		is_singular( array( 'bkx_seat', 'bkx_base', 'bkx_addition' ) ) ||
		is_my_account() ||
		is_booking_page() || is_bkx_service() || is_bkx_staff() || is_bkx_extra() || is_dashboard()
	);
}

/**
 * @return bool
 */
function is_booking_page() {
	if ( is_page() || is_single() ) {
		global $post;
		return ( has_shortcode( $post->post_content, 'bkx_booking_form' ) );
	}
}

/**
 * @return bool
 */
function is_bkx_singular() {
	if ( is_page() || is_single() ) {
		global $post;
		return ( has_shortcode( $post->post_content, 'bookingx' ) );
	}
}

/**
 * @return bool
 */
function is_dashboard() {
	if ( is_page() || is_single() ) {
		global $post;
		return ( has_shortcode( $post->post_content, 'bkx_dashboard' ) );
	}
}

/**
 * @return bool
 */
function is_my_account() {
	if ( is_page() || is_single() ) {
		global $post;
		return ( has_shortcode( $post->post_content, 'bkx_my_account' ) );
	}
}

/**
 * @return bool
 */
function is_bkx_service() {
	return ( is_post_type_archive( 'bkx_base' ) || is_singular( 'bkx_base' ) );
}

/**
 * @return bool
 */
function is_bkx_extra() {
	return ( is_post_type_archive( 'bkx_addition' ) || is_singular( 'bkx_addition' ) );
}

/**
 * @return bool
 */
function is_bkx_staff() {
	return ( is_post_type_archive( 'bkx_seat' ) || is_singular( 'bkx_seat' ) );
}

/**
 * @return bool
 */
function is_bookingx_admin() {
	$status             = false;
	$current_screen     = get_current_screen();
	$admin_base_screens = array(
		'base'      => array( 'bkx_booking_page_bkx-setting', 'edit', 'add' ),
		'post_type' => array( 'bkx_booking', 'bkx_seat', 'bkx_base', 'bkx_addition' ),
	);
	$admin_base_screens = apply_filters( 'bkx_admin_screens_access', $admin_base_screens );
	if ( is_admin() && in_array( $current_screen->base, $admin_base_screens['base'] )
	     || in_array( $current_screen->post_type, $admin_base_screens['post_type'] ) ) {
		$status = true;
	}
	return $status;
}

/**
 * @param $booking_id
 * @param null       $retun_page
 * @return string|void
 */
function edit_booking_url( $booking_id, $retun_page = null ) {
	if ( empty( $booking_id ) ) {
		return;
	}
	$edit_id      = bkx_crud_option_multisite( 'bkx_edit_booking_page_id' );
	$edit_booking = add_query_arg(
		array(
			'id'     => $booking_id,
			'return' => $retun_page,
		),
		get_permalink( $edit_id )
	);
	return wp_nonce_url( $edit_booking, 'edit_booking_' . $booking_id, 'edit_booking_nonce' );
}

/**
 * @return mixed|void
 */
function bkx_get_dashboard_orders_columns() {
	return apply_filters(
		'bkx_listing_default_headers',
		array(
			'booking-number'  => esc_html__( 'Booking #', 'bookingx' ),
			'booking-date'    => esc_html__( 'Date & Time', 'bookingx' ),
			// 'booking-status' => esc_html__('Status', 'bookingx'),
			'booking-service' => esc_html__( 'Service', 'bookingx' ),
			'booking-total'   => esc_html__( 'Total', 'bookingx' ),
			'booking-actions' => '&nbsp;',
		)
	);
}

/**
 * @return mixed|void
 */
function bkx_get_dashboard_navigation_menu() {
	$dashboard_id  = bkx_crud_option_multisite( 'bkx_dashboard_page_id' );
	$my_account_id = bkx_crud_option_multisite( 'bkx_my_account_page_id' );

	return apply_filters(
		'bkx_dashboard_navigation_menu',
		array(
			'bookings'   => array(
				'label'  => esc_html__( 'Bookings', 'bookingx' ),
				'link'   => esc_url( get_permalink( $dashboard_id ), 'bookingx' ),
				'active' => true,
			),
			'my_account' => array(
				'label'  => esc_html__( 'My Account', 'bookingx' ),
				'link'   => esc_url( get_permalink( $my_account_id ), 'bookingx' ),
				'active' => false,
			),
		)
	);
}

/**
 * @param $booking_id
 * @return array
 */
function getDayDateDuration( $booking_id ) {
	$days_selected = get_post_meta( $booking_id, 'booking_multi_days', true );
	if ( isset( $days_selected ) && ! empty( $days_selected ) ) {
		$last_key         = count( $days_selected ) - 1;
		$start_date       = date( 'F d, Y', strtotime( $days_selected[0] ) );
		$end_date         = date( 'F d, Y', strtotime( $days_selected[ $last_key ] ) );
		$date_data        = "{$start_date} To {$end_date}";
		$booking_duration = ( count( $days_selected ) > 1 ? count( $days_selected ) . ' Days' : count( $days_selected ) . ' Day' );
		$duration         = sprintf( __( '%s', 'bookingx' ), $booking_duration );
	}
	return array( $date_data, $duration, $start_date, $end_date );
}

/**
 * @param $order_meta
 * @return string
 */
function getDateDuration( $order_meta ) {
	return sprintf( __( '%s', 'bookingx' ), date( 'h:i A', strtotime( $order_meta['booking_start_date'] ) ), date( 'h:i A ', strtotime( $order_meta['booking_end_date'] ) ) );
}

/**
 * @param $order_meta
 * @return string
 */
function getDuration( $order_meta ) {
	$booking_duration = str_replace( '(', '', $order_meta['total_duration'] );
	$booking_duration = str_replace( ')', '', $booking_duration );
	return sprintf( __( '%s', 'bookingx' ), $booking_duration );
}

/**
 * @return array
 */
function getBusinessInfo() {
	$bkx_business_name      = bkx_crud_option_multisite( 'bkx_business_name' );
	$bkx_business_email     = bkx_crud_option_multisite( 'bkx_business_email' );
	$bkx_business_phone     = bkx_crud_option_multisite( 'bkx_business_phone' );
	$bkx_business_address_1 = bkx_crud_option_multisite( 'bkx_business_address_1' );
	$bkx_business_address_2 = bkx_crud_option_multisite( 'bkx_business_address_2' );
	$bkx_business_city      = bkx_crud_option_multisite( 'bkx_business_city' );
	$bkx_business_state     = bkx_crud_option_multisite( 'bkx_business_state' );
	$bkx_business_zip       = bkx_crud_option_multisite( 'bkx_business_zip' );
	$bkx_business_country   = bkx_crud_option_multisite( 'bkx_business_country' );
	$bkx_business_city      = sprintf( __( '%s', 'bookingx' ), $bkx_business_city );
	$bkx_business_state     = sprintf( __( '%s', 'bookingx' ), $bkx_business_state );
	$bkx_business_zip       = sprintf( __( '%d', 'bookingx' ), $bkx_business_zip );
	$bkx_business_country   = sprintf( __( '%s', 'bookingx' ), $bkx_business_country );
	$bkx_business_address   = sprintf( __( '%1$s %2$s %3$s %4$s %5$s %6$s ', 'bookingx' ), $bkx_business_address_1, $bkx_business_address_2, $bkx_business_city, $bkx_business_state, $bkx_business_zip, $bkx_business_country );
	return array( $bkx_business_name, $bkx_business_email, $bkx_business_phone, $bkx_business_address );
}

/**
 * @param $order_meta
 * @return string
 */
function getExtraHtml( $order_meta ) {
	$extra_html = '';
	if ( ! empty( $order_meta['addition_ids'] ) && ! empty( $order_meta['extra_arr'] ) ) {
		$extra_html = sprintf( __( '%s', 'bookingx' ), '<p><label>Extra Services : </label>' );
		$extra_data = '';
		foreach ( $order_meta['extra_arr'] as $extra_obj ) {
			if(!empty($extra_obj) && !empty($extra_obj['main_obj']->post)){
				$main_obj    = $extra_obj['main_obj']->post;
				$extra_title = $main_obj->post_title;
				$extra_data .= " {$extra_title} " . ',';
			}
		}
		$extra_data  = rtrim( $extra_data, ',' );
		$extra_html .= $extra_data . '</p>';
	}
	return $extra_html;
}

/**
 * @param $payment_source_method
 * @param $payment_status
 * @return array
 */
function getPaymentInfo( $payment_source_method, $payment_status ) {
	$pending_paypal_message = '';
	if ( isset( $payment_source_method ) && $payment_source_method == 'bkx_gateway_paypal_express' && $payment_status == 'Pending' ) {
		$pending_paypal_message = sprintf( __( '%s', 'bookingx' ), '<p> <label>Note : </label> Transaction Incomplete and payment is still in pending status! You need to manually authorize this payment in your Paypal Account </p>' );
	}

	if ( ! empty( $payment_source_method ) ) {
		$BkxPaymentCore             = new BkxPaymentCore();
		$bkx_get_available_gateways = $BkxPaymentCore->PaymentGateways();
		if ( ! empty( $bkx_get_available_gateways[ $payment_source_method ] ) ) {
			$payment_source_name = $bkx_get_available_gateways[ $payment_source_method ]['title'];
		} else {
			$payment_source_name = esc_html__( 'Offline Payment', 'bookingx' );
		}
		$payment_source = sprintf( __( '%s', 'Bookingx' ), $payment_source_name );
	} else {
		$payment_source = sprintf( __( '%s', 'Bookingx' ), 'Offline Payment' );
	}
	return array( $pending_paypal_message, $payment_source );
}

/**
 * @return mixed|void
 */
function adminColorSchema() {
	if ( is_user_logged_in() && is_admin() ) {
		global $_wp_admin_css_colors;
		$admin_color = get_user_option( 'admin_color' );
		$colors      = $_wp_admin_css_colors[ $admin_color ]->colors;
		switch ( $admin_color ) {
			case 'fresh':
				$button  = $colors[2];
				$booked  = $colors[1];
				$open    = $colors[3];
				$current = $colors[0];
				break;
			case 'light':
				$button  = $colors[3];
				$booked  = $colors[1];
				$open    = $colors[2];
				$current = $colors[0];
				break;
			case 'blue':
				$button  = '#e1a948';
				$booked  = $colors[1];
				$open    = $colors[2];
				$current = $colors[0];
				break;
			case 'ectoplasm':
			case 'coffee':
			case 'ocean':
			case 'sunrise':
				$button  = $colors[2];
				$booked  = $colors[0];
				$open    = $colors[3];
				$current = $colors[1];
				break;
			case 'midnight':
				$button  = $colors[3];
				$booked  = $colors[0];
				$open    = $colors[2];
				$current = $colors[1];
				break;
		}
		$color_schema = array(
			'button'  => $button,
			'booked'  => $booked,
			'open'    => $open,
			'current' => $current,
		);

		return apply_filters( 'bkx_admin_form_color_schema', $color_schema );
	}
}

/**
 * @param $obj
 * @return mixed|void
 */
function service_hours_formatted( $obj ) {
	if ( empty( $obj ) ) {
		return;
	}
	$duration_text = '';
	if ( ( isset( $obj['H'] ) ) ) {
		$hour_min_obj = $obj['H'];
	}
	if ( ( isset( $obj['D'] ) ) ) {
		$day_obj = $obj['D'];
	}

	if ( ! empty( $hour_min_obj ) ) :
		$hours_val   = (int) $hour_min_obj['hours'];
		$minutes_val = (int) $hour_min_obj['minutes'];
		$hours = "";
		if ( isset( $hours_val ) && $hours_val != '' && $hours_val > 1 ) {
			$hours = "{$hours_val} Hours";
		} elseif ( isset( $hours_val ) && $hours_val != '' && $hours_val == 1 ) {
			$hours = "{$hours_val} Hour";
		}
		$mins = '';
		if ( isset( $minutes_val ) && $minutes_val != '' && $minutes_val > 1 ) {
			$mins = "{$minutes_val} minutes";
		}
		$duration_text = "{$hours} {$mins}";
	endif;
	if ( ! empty( $day_obj ) ) :
		$days_val = (int) $day_obj['days'];
		if ( isset( $days_val ) && $days_val != '' && $days_val > 1 ) {
			$day = "{$days_val} Days";
		} elseif ( isset( $days_val ) && $days_val != '' && $days_val == 1 ) {
			$day = "{$days_val} Day";
		}
		$duration_text = $day;
	endif;

	return apply_filters( 'bkx_service_time_formatted', $duration_text, $obj );
}

/**
 * @param $user_id
 * @param $booking_id
 * @return bool|void
 */
function check_booking_owner( $user_id, $booking_id ) {
	if ( ! isset( $user_id, $booking_id ) ) {
		return;
	}

	$BkxBooking = new BkxBooking( null, $booking_id );
	$BkxBooking->booking_cancelled();
	$user_info = get_userdata( $user_id );

	try {
		$email         = $user_info->data->user_email;
		$order_meta    = $BkxBooking->get_order_meta_data();
		$created_by    = $order_meta['created_by'];
		$created_email = $order_meta['email'];
		if ( $created_by == $user_id || $created_email == $email ) {
			return true;
		}
	} catch ( Exception $e ) {
		return;
	}
}

function bkx_check_is_active_addons( $info ){
	if(empty($info))
		return;
	$is_our_addon = false;
	if( trim( $info['AuthorName'] ) == 'Booking X' && trim($info['AuthorURI']) == 'https://dunskii.com' && $info['Name'] != 'Booking X'){
		$is_our_addon = true;
	}
	return $is_our_addon;
}

function generate_addon_license_section_fields( $info ){
	$license = get_option( $info['key_name'] );
	$status  = get_option( $info['status'] );
	if ( $status !== false && $status == 'valid' ) {
		$addon_status_text = '<span style="color:green;">'.__('Active').'</span>';
	}else{
		$addon_status_text = '<input type="submit" class="button-secondary" name="bkx_license_activation" value="'.__( 'Activate License' ).'"/>';
	}

		return '<tr valign="top">
				<th scope="row" valign="top">
					'.__( $info['addon_name']).'
				</th>
				<td>
					<input placeholder="'.__( 'Enter your license key' ).'" id="'.$info['key_name'].'" name="'.$info['key_name'].'" type="text" class="regular-text" value="'.esc_attr( $license ).'" />
					<input name="'.$info['key_name'].'_data" type="hidden" value="'.esc_attr( $license ).'|'.$info['key_name'].'|'.$info['status'].'|'.$info['addon_name'].'" />
					'.$addon_status_text.'
				</td>
			</tr>';
}

function bookingx_sanitize_license( $new , $key ,$status) {
	$old = get_option( $key );
	if ( $old != $new ) {
		delete_option( $status ); // new license has been entered, so must reactivate
		update_option( $key, $new );
	}
	return $new;
}

function bookingx_addon_activate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['bkx_license_activation'] ) ) {
		// run a quick security check
		if ( ! check_admin_referer( 'bkx_license_activation_nonce', 'bkx_license_activation_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if(!empty($_POST)){
			foreach ($_POST as $key => $license_value ){

				if (strpos($key, 'license_key_data') !== false) {
					$license_key_data = explode("|", $license_value );
					$license_key       = $license_key_data[1];
					$license_status    = $license_key_data[2];
					$addon_name        = $license_key_data[3];
					if ( isset( $_POST[$license_key] ) ) {
						bookingx_sanitize_license( $_POST[$license_key], $license_key, $license_status );
					}
					// retrieve the license from the database
					$license = trim( get_option( $license_key ) );

					// data to send in our API request
					$api_params = array(
						'edd_action' => 'activate_license',
						'license'    => $license,
						'item_name'  => urlencode( $addon_name ), // the name of our product in EDD
						'url'        => home_url(),
					);
					// Call the custom API.
					$response = wp_remote_post(
						BKX_STORE_URL,
						array(
							'timeout'   => 15,
							'sslverify' => false,
							'body'      => $api_params,
						)
					);

					// make sure the response came back okay
					if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

						if ( is_wp_error( $response ) ) {
							$message = $response->get_error_message();
						} else {
							$message = __( 'An error occurred, please try again.' );
						}
					} else {
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( false === $license_data->success ) {

							switch ( $license_data->error ) {

								case 'expired':
									$message = sprintf(
										__( 'Your license key expired on %s.' ),
										date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
									);
									break;

								case 'disabled':
								case 'revoked':
									$message = __( 'Your license key has been disabled.' );
									break;

								case 'missing':
									$message = __( 'Invalid license.' );
									break;

								case 'invalid':
								case 'site_inactive':
									$message = __( 'Your license is not active for this URL.' );
									break;

								case 'item_name_mismatch':
									$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), TIMEZONE_ITEM_NAME );
									break;

								case 'no_activations_left':
									$message = __( 'Your license key has reached its activation limit.' );
									break;

								default:
									$message = __( 'An error occurred, please try again.' );
									break;
							}
						}
						update_option( $license_status, $license_data->license );
					}
				}
			}
		}
	}
}

function booking_license_setting_page(){
	return admin_url('edit.php?post_type=bkx_booking&page=bkx-setting&bkx_tab=bkx_licence');
}

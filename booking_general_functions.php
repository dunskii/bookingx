<?php

/**
 *This Function includes/loads the scripts in footer
 *@access public
 * author: jiten
 */
function custom_load_scripts($scripts = array()) {
 
    foreach( $scripts as $script ){
        wp_enqueue_script( $script,  plugins_url('/js/' . $script, __FILE__),false,rand(1,9999999),false ); 

    }
    
}

/**
 *This Function includes/loads the scripts for color picker in footer
 *@access public
 * author: jiten
 */
function color_load_scripts($scripts = array()) {
  
    foreach( $scripts as $script ){
        wp_enqueue_script( $script,  plugins_url('/color/js/' . $script, __FILE__) ); 

    }
    
}


/**
 *This Function includes/loads the CSS files in footer
 *@access public
 * author: jiten
 */
function custom_load_styles($styles = array()) {
    foreach($styles as $style){
        wp_enqueue_style( $style,  plugins_url('/css/' . $style, __FILE__) );
    }
}

/**
 * Reassign Booking process
 * Date : 16-11-2015
 * Created by : Divyang Parekh
 */
function reassign_available_emp_list($seat_id,$start_date,$end_date,$service_id)
{
	$return = array();
	global $wpdb;
	$BkxBaseObj = new BkxBase('',$service_id);

	// $get_employees = $wpdb->get_results("SELECT s.seat_id,s.seat_name FROM bkx_seat_base sb INNER JOIN  bkx_seat s ON sb.seat_id = s.seat_id WHERE sb.base_id = {$service_id} AND sb.seat_id != {$seat_id}");
 	
	$get_employees = $BkxBaseObj->get_seat_by_base();
	$key_rm = array_search($seat_id, $get_employees);
	if($key_rm !== false){ unset($get_employees[$key_rm]);}

	$order_statuses = array('bkx-pending','bkx-ack','bkx-completed','bkx-missed');
 
	if(!empty($get_employees))
	{
		foreach($get_employees as $get_employee):


			/*$employee_have_booking = $wpdb->get_var("SELECT booking_record_id FROM bkx_booking_record WHERE seat_id = {$get_employee->seat_id} AND  (order_status != 'cancelled' OR order_status != 'pending')  
				AND (

				'{$start_date}' BETWEEN booking_start_date AND booking_end_date 

				OR '{$end_date}' BETWEEN booking_start_date AND booking_end_date 

				OR ('{$start_date}' <= booking_start_date AND '{$end_date}' >= booking_end_date))");*/



			$args = array(
            'post_type'  => 'bkx_booking',
            'post_status'=> $status,
            'meta_key'	 => 'seat_id',
            'meta_value' => $get_employee,
            'meta_query' => array(
                array(
                    'key'     => 'booking_start_date',
                    'value'   => $start_date,
                    'compare' => '<=',
            	),
                array(
                    'key'     => 'booking_end_date',
                    'value'   => $end_date,
                    'compare' => '>=',          		 
                ),
            ),
        );

        $bookedresult = new WP_Query( $args );        
		if($bookedresult->have_posts()){
			$BookingObj = $bookedresult->post;
			$booked_seat_id = get_post_meta($BookingObj->ID,'seat_id',true);
		}

		if($booked_seat_id == '')
		{
			$BkxSeatObj = new BkxSeat('',$get_employee);
			$return[] = array(
				'id' => $BkxSeatObj->post->ID,
				'name' => $BkxSeatObj->post->post_title,
			);
		}
			
		endforeach;
	}
	return $return;
}

/**
 * Function developed By Divyang Parekh
 * Date : 26-11-2015
 *
 */
/**
 * @param $slot_id
 * @param $get_booked_slot
 * @param $get_free_seats
 * @param $total_seats
 * @param null $bookigndate
 * @param null $seat_id
 * @return array
 */
function _get_booked_slot($slot_id,$get_booked_slot,$get_free_seats,$total_seats,$bookigndate=null,$seat_id=null)
{
	//$_enable_any_seat_selected_id = (int)get_option('select_default_seat'); // Default Seat id
	//$free_seat_all_arr= $get_free_seats;
	$send_seat_block_data= array();
	$slots_blocked=array();
	if($_SESSION['_display_seat_slots']>1)
	{
		for($i=0; $i<$_SESSION['_display_seat_slots']; $i++) {
			$find_slot_value_in_booked_array[] = ($slot_id)+ $i;
		}
	}
	else
	{
		$find_slot_value_in_booked_array[] =$slot_id;
	}
	//echo '<pre>' . print_r($find_slot_value_in_booked_array, true) . '</pre>';
	if(!empty($get_booked_slot)) :
		foreach ($get_booked_slot as $_slot_id=>$booked_slot)
		{
			foreach($booked_slot as $seat_id=>$slots)
			{
				foreach($find_slot_value_in_booked_array as $_find_slot)
				{
					if(in_array($_find_slot,$slots)) {
						//echo 'Now find slot id is : ' . $_find_slot ."<br>";
						$now_booked_slots_is[] = $slots;
						$now_booked_slots_with_seats[$_find_slot][] = $seat_id;
					}
				}
			}
		}
		//echo '<pre> $now_booked_slots_with_seats ' . print_r($now_booked_slots_with_seats, true) . '</pre>';
		if(!empty($now_booked_slots_with_seats))
		{
			foreach($now_booked_slots_with_seats as $booked_slot_id=>$booked_seats)
			{
				//echo '<pre>'.$booked_slot_id.'==Total ='.$total_seats.'=='.sizeof($booked_seats).'=== $booked_seats' . print_r($booked_seats, true) . '</pre>';
				if(sizeof($booked_seats) > 0 && $total_seats > 0 && (sizeof($booked_seats) == $total_seats))
				{
					$slots_blocked[]=$booked_slot_id;
				}
			}
		}
		/**
		 * Find Seat where other slot are available
		 */
	endif;

	if(!empty($slots_blocked)):	$send_seat_block_data['slots_blocked']=$slots_blocked; endif;

	return $send_seat_block_data;
}

function get_range($bookingdate,$seatid)
{
  
	global $wpdb;
	$arrBookingdate = explode('/', $bookingdate);
	$timestamp = strtotime($arrBookingdate[2]."-".$arrBookingdate[0]."-".$arrBookingdate[1]);
	$day = date('w', $timestamp);
	$day_name = getDayName($day);
        
        $GetSeatObj = get_post($seatid);
        $res_seat = get_post_custom( $GetSeatObj->ID );
        
//	$seat_time_query = "SELECT * FROM bkx_seat_time WHERE seat_id = ".trim($seatid)." AND day = '".trim($day_name)."'";
//	$res_seat_time = $wpdb->get_results($seat_time_query);
        $db_res_seat_time_arr = array();

                $seat_days_time = maybe_unserialize($res_seat['seat_days_time'][0]);
                $db_res_seat_time_arr= maybe_unserialize($seat_days_time);
                //print_r($res_seat_time_arr);
            if(!empty($db_res_seat_time_arr)){
                
                 foreach ($db_res_seat_time_arr as $day_db=>$time_data)
                 {
                      $day_is = strtolower($time_data['day']);
                      $time_from = $time_data['time_from'];
                      $time_till = $time_data['time_till'];
                      if(isset($day_is) && $day_is ==strtolower($day_name))
                      {
                           $res_seat_time_arr['time_from'] = $time_from;
                           $res_seat_time_arr['time_till'] = $time_till;
                      }
                 }
            }
           

	if(isset($res_seat_time_arr['time_from']) && $res_seat_time_arr['time_from']!="")
	{
		$time_value = '';
		$time_from = $res_seat_time_arr['time_from'];
		$time_till = $res_seat_time_arr['time_till'];
		$time_from_arr = explode(" ",$time_from);
		$time_till_arr = explode(" ",$time_till);
		$hrsMinsFromArr = explode(":",$time_from_arr[0]);
		$hrsFrom = $hrsMinsFromArr[0];
		$minsFrom = $hrsMinsFromArr[1];
                
		if($time_from_arr[1] == "AM")
                    {
                            if($hrsFrom == 12)
                                    $hrsFrom = "00";
                            $time_from = $hrsFrom.":".$minsFrom;
                    }
                    else if($time_from_arr[1] == "PM")
                    {
                            if($hrsFrom == 12)
                                    $hrsFrom == 12;
                            else
                                    $hrsFrom = intval($hrsFrom) + 12;
                            $time_from = $hrsFrom.":".$minsFrom;
                    }
                    $hrsMinsTillArr = explode(":",$time_till_arr[0]);
                    $hrsTill = $hrsMinsTillArr[0];
                    $minsTill = $hrsMinsTillArr[1];
                    //print_r($time_till_arr);
                    if($time_till_arr[1] == "AM")
                    {
                            if($hrsTill == 12 && $time_from_arr[1] == "AM")
                                    $hrsTill = "24";

                           $time_till = $hrsTill.":".$minsTill;
                    }
                    else if($time_till_arr[1] == "PM")
                    {
                            if($hrsTill == 12)
                                    $hrsTill == 12;
                            else
                                    $hrsTill = intval($hrsTill) + 12;
                            $time_till = $hrsTill.":".$minsTill;
                    }
                    $timefromArr = explode(":",$time_from);


                    $minsCounter = getMinsSlot($timefromArr[1]);
                    $timefrom_slot = $timefromArr[0] * 4 + $minsCounter;

                    $timetillArr = explode(":",$time_till);

                    $minsCounterTill = getMinsSlot($timetillArr[1]);
                    $timetill_slot = $timetillArr[0] * 4 + $minsCounterTill;
		$range = range($timefrom_slot, $timetill_slot-1);
	}
	else
	{
		$range = range(1,96);
	}
	return $range;
}

function bkx_get_template($template_name,$template_path = '')
{
    if(isset($template_name) && $template_name!='')
    {
        $plugin_path = PLUGIN_DIR_PATH;
        $file_path = $plugin_path.''.$template_name;
        if ( ! file_exists( $file_path ) ) {
          return;  
        }
        include( $file_path );
    }
}


/**
 * When the_post is called, put Bookingx post into a global.
 *
 * @param mixed $post
 * @return post object
 */
function wc_setup_bkx_post_data( $post ) {
		unset( $globalS['bkx_seat'] );
        unset( $globalS['bkx_base'] );
        unset( $globalS['bkx_addition'] );
        unset( $globalS['bkx_post'] );

        global $bkx_seat,$bkx_base,$bkx_addition;

	if ( is_int($post))
            $post = get_post( $post );

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'bkx_seat', 'bkx_base','bkx_addition' ) ) )
            return;
        
        if(isset($post->post_type) && $post->post_type =='bkx_seat')
            $GLOBALS[$post->post_type] = new BkxSeat($post);
        elseif(isset($post->post_type) && $post->post_type =='bkx_base')
            $GLOBALS[$post->post_type] = new BkxBase($post);
        elseif(isset($post->post_type) && $post->post_type =='bkx_addition')
            $GLOBALS[$post->post_type] = new BkxExtra($post);

	return $GLOBALS[$post->post_type];
}
add_action( 'the_post', 'wc_setup_bkx_post_data' );

function bkx_placeholder_img_src($size='service-thumb')
{
    return apply_filters( 'bkx_placeholder_img_src',PLUGIN_DIR_URL. 'images/placeholder.png' );
}

function bkx_placeholder_img($size='service-thumb')
{
  $dimensions['width'] = 300;
  $dimensions['height'] = 300;
    
  return apply_filters('bkx_placeholder_img', '<img src="'.bkx_placeholder_img_src().'" alt="' . esc_attr__( 'Placeholder', 'bookingx' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="bkx-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />',$size, $dimensions );
}

function get_wp_country()
{

	$all_countries = array(
		"AF" => "Afghanistan",
		"AX" => "Aland Islands",
		"AL" => "Albania",
		"DZ" => "Algeria",
		"AS" => "American Samoa",
		"AD" => "Andorra",
		"AO" => "Angola",
		"AI" => "Anguilla",
		"AQ" => "Antarctica",
		"AG" => "Antigua and Barbuda",
		"AR" => "Argentina",
		"AM" => "Armenia",
		"AW" => "Aruba",
		"AU" => "Australia",
		"AT" => "Austria",
		"AZ" => "Azerbaijan",
		"BS" => "Bahamas",
		"BH" => "Bahrain",
		"BD" => "Bangladesh",
		"BB" => "Barbados",
		"BY" => "Belarus",
		"BE" => "Belgium",
		"BZ" => "Belize",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BT" => "Bhutan",
		"BO" => "Bolivia, Plurinational State of",
		"BQ" => "Bonaire, Sint Eustatius and Saba",
		"BA" => "Bosnia and Herzegovina",
		"BW" => "Botswana",
		"BV" => "Bouvet Island",
		"BR" => "Brazil",
		"IO" => "British Indian Ocean Territory",
		"BN" => "Brunei Darussalam",
		"BG" => "Bulgaria",
		"BF" => "Burkina Faso",
		"BI" => "Burundi",
		"KH" => "Cambodia",
		"CM" => "Cameroon",
		"CA" => "Canada",
		"CV" => "Cape Verde",
		"KY" => "Cayman Islands",
		"CF" => "Central African Republic",
		"TD" => "Chad",
		"CL" => "Chile",
		"CN" => "China",
		"CX" => "Christmas Island",
		"CC" => "Cocos (Keeling) Islands",
		"CO" => "Colombia",
		"KM" => "Comoros",
		"CG" => "Congo",
		"CD" => "Congo, the Democratic Republic of the",
		"CK" => "Cook Islands",
		"CR" => "Costa Rica",
		"CI" => "Cote d'Ivoire",
		"HR" => "Croatia",
		"CU" => "Cuba",
		"CW" => "Curacao",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"DK" => "Denmark",
		"DJ" => "Djibouti",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"EC" => "Ecuador",
		"EG" => "Egypt",
		"SV" => "El Salvador",
		"GQ" => "Equatorial Guinea",
		"ER" => "Eritrea",
		"EE" => "Estonia",
		"ET" => "Ethiopia",
		"FK" => "Falkland Islands (Malvinas)",
		"FO" => "Faroe Islands",
		"FJ" => "Fiji",
		"FI" => "Finland",
		"FR" => "France",
		"GF" => "French Guiana",
		"PF" => "French Polynesia",
		"TF" => "French Southern Territories",
		"GA" => "Gabon",
		"GM" => "Gambia",
		"GE" => "Georgia",
		"DE" => "Germany",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GR" => "Greece",
		"GL" => "Greenland",
		"GD" => "Grenada",
		"GP" => "Guadeloupe",
		"GU" => "Guam",
		"GT" => "Guatemala",
		"GG" => "Guernsey",
		"GN" => "Guinea",
		"GW" => "Guinea-Bissau",
		"GY" => "Guyana",
		"HT" => "Haiti",
		"HM" => "Heard Island and McDonald Islands",
		"VA" => "Holy See (Vatican City State)",
		"HN" => "Honduras",
		"HK" => "Hong Kong",
		"HU" => "Hungary",
		"IS" => "Iceland",
		"IN" => "India",
		"ID" => "Indonesia",
		"IR" => "Iran, Islamic Republic of",
		"IQ" => "Iraq",
		"IE" => "Ireland",
		"IM" => "Isle of Man",
		"IL" => "Israel",
		"IT" => "Italy",
		"JM" => "Jamaica",
		"JP" => "Japan",
		"JE" => "Jersey",
		"JO" => "Jordan",
		"KZ" => "Kazakhstan",
		"KE" => "Kenya",
		"KI" => "Kiribati",
		"KP" => "Korea, Democratic People's Republic",
		"KR" => "Korea, Republic of",
		"KW" => "Kuwait",
		"KG" => "Kyrgyzstan",
		"LA" => "Lao People's Democratic Republic",
		"LV" => "Latvia",
		"LB" => "Lebanon",
		"LS" => "Lesotho",
		"LR" => "Liberia",
		"LY" => "Libya",
		"LI" => "Liechtenstein",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"MO" => "Macao",
		"MK" => "Macedonia",
		"MG" => "Madagascar",
		"MW" => "Malawi",
		"MY" => "Malaysia",
		"MV" => "Maldives",
		"ML" => "Mali",
		"MT" => "Malta",
		"MH" => "Marshall Islands",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MU" => "Mauritius",
		"YT" => "Mayotte",
		"MX" => "Mexico",
		"FM" => "Micronesia, Federated States of",
		"MD" => "Moldova",
		"MC" => "Monaco",
		"MN" => "Mongolia",
		"ME" => "Montenegro",
		"MS" => "Montserrat",
		"MA" => "Morocco",
		"MZ" => "Mozambique",
		"MM" => "Myanmar",
		"NA" => "Namibia",
		"NR" => "Nauru",
		"NP" => "Nepal",
		"NL" => "Netherlands",
		"NC" => "New Caledonia",
		"NZ" => "New Zealand",
		"NI" => "Nicaragua",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"NU" => "Niue",
		"NF" => "Norfolk Island",
		"MP" => "Northern Mariana Islands",
		"NO" => "Norway",
		"OM" => "Oman",
		"PK" => "Pakistan",
		"PW" => "Palau",
		"PS" => "Palestine, State of",
		"PA" => "Panama",
		"PG" => "Papua New Guinea",
		"PY" => "Paraguay",
		"PE" => "Peru",
		"PH" => "Philippines",
		"PN" => "Pitcairn",
		"PL" => "Poland",
		"PT" => "Portugal",
		"PR" => "Puerto Rico",
		"QA" => "Qatar",
		"RE" => "Reunion",
		"RO" => "Romania",
		"RU" => "Russian Federation",
		"RW" => "Rwanda",
		"BL" => "Saint-Barthelemy",
		"SH" => "Saint Helena, Ascension and Tristan da Cunha",
		"KN" => "Saint Kitts and Nevis",
		"LC" => "Saint Lucia",
		"MF" => "Saint Martin (French part)",
		"PM" => "Saint Pierre and Miquelon",
		"VC" => "Saint Vincent and the Grenadines",
		"WS" => "Samoa",
		"SM" => "San Marino",
		"ST" => "Sao Tome and Principe",
		"SA" => "Saudi Arabia",
		"SN" => "Senegal",
		"RS" => "Serbia",
		"SC" => "Seychelles",
		"SL" => "Sierra Leone",
		"SG" => "Singapore",
		"SX" => "Sint Maarten (Dutch part)",
		"SK" => "Slovakia",
		"SI" => "Slovenia",
		"SB" => "Solomon Islands",
		"SO" => "Somalia",
		"ZA" => "South Africa",
		"GS" => "South Georgia and the South Sandwich Islands",
		"SS" => "South Sudan",
		"ES" => "Spain",
		"LK" => "Sri Lanka",
		"SD" => "Sudan",
		"SR" => "Suriname",
		"SJ" => "Svalbard and Jan Mayen",
		"SZ" => "Swaziland",
		"SE" => "Sweden",
		"CH" => "Switzerland",
		"SY" => "Syrian Arab Republic",
		"TW" => "Taiwan, Province of China",
		"TJ" => "Tajikistan",
		"TZ" => "Tanzania, United Republic of",
		"TH" => "Thailand",
		"TL" => "Timor-Leste",
		"TG" => "Togo",
		"TK" => "Tokelau",
		"TO" => "Tonga",
		"TT" => "Trinidad and Tobago",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TM" => "Turkmenistan",
		"TC" => "Turks and Caicos Islands",
		"TV" => "Tuvalu",
		"UG" => "Uganda",
		"UA" => "Ukraine",
		"AE" => "United Arab Emirates",
		"GB" => "United Kingdom",
		"US" => "United States",
		"UM" => "United States Minor Outlying Islands",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VU" => "Vanuatu",
		"VE" => "Venezuela",
		"VN" => "Viet Nam",
		"VG" => "Virgin Islands, British",
		"VI" => "Virgin Islands, U.S.",
		"WF" => "Wallis and Futuna",
		"EH" => "Western Sahara",
		"YE" => "Yemen",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe",
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

	 asort($country_arr, SORT_STRING | SORT_FLAG_CASE);
	return array_unique( apply_filters( 'bookingx_currencies', $country_arr ) );
}

/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 * @return string
 */
function get_bookingx_currency_symbol( $currency = '' ) {


	$symbols = apply_filters( 'bookingx_currency_symbols', array(
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
	) );

	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

	return apply_filters( 'bookingx_currency_symbol', $currency_symbol, $currency );
}


function get_current_currency()
{	
	if ( is_multisite() ){
        $current_blog_id = get_current_blog_id();
        $currency_option = get_blog_option($current_blog_id,'currency_option');
    }else{
        $currency_option = get_option('currency_option');
    }
	return get_bookingx_currency_symbol( $currency_option );
}

/**
*	For get_option() update_option() and add_option() for compatible for multisite
*/
function crud_option_multisite($option_name,$option_val=null,$type='get')
{
	$get_current_blog_id = get_current_blog_id();

	switch ($type) {
		case 'add':
			$arg = (is_multisite()) ? array($get_current_blog_id,$option_name,$option_val) : array($option_name,$option_val);
			break;
		case 'update':
			$arg = (is_multisite()) ? array($get_current_blog_id,$option_name,$option_val) : array($option_name,$option_val);
			break;
		case 'get':
			$arg = (is_multisite()) ? array($get_current_blog_id,$option_name) : array($option_name);
			break;		
		default:

			break;
	}
	$option_data = (is_multisite()) ? call_user_func_array($type.'_blog_option',$arg) : call_user_func_array($type.'_option',$arg);

    return $option_data;
}


/**
 * 
 * @param type $string
 * @return type
 */
function to_slug($string){
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

function get_allowed()
{
	$get_alllowed = array('bkx_booking','bkx_seat','bkx_base','bkx_addition');

	return apply_filters( 'bookingx_get_allowed_on_page',$get_alllowed);
}


function get_loader() { 
	return '<div class="bookingx-loader" style="display:none">Loading&#8230;</div>';
}


function getDatesFromRange($start, $end, $format = 'Y-m-d') {
    $array = array();
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach($period as $date) { 
        $array[] = $date->format($format); 
    }

    return $array;
}

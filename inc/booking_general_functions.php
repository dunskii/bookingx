<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_role('resource', __('Resource'), array('read' => true, 'edit_posts' => true, 'delete_posts' => false));
/**
 * This Function includes/loads the scripts in footer
 * @param array $scripts
 *
 */
function bkx_custom_load_scripts($scripts = array())
{

    foreach ($scripts as $script) {
        wp_enqueue_script($script, BKX_PLUGIN_DIR_URL . 'frontend/js/' . $script, false, BKX_PLUGIN_VER, false);
    }
}

/**
 * Reassign Booking process
 *
 */
function bkx_reassign_available_emp_list($seat_id, $start_date, $end_date, $service_id)
{
    $return = array();
    global $wpdb;
    $BkxBaseObj = new BkxBase('', $service_id);
    $get_employees = $BkxBaseObj->get_seat_by_base();
    $key_rm = array_search($seat_id, $get_employees);
    if ($key_rm !== false) {
        unset($get_employees[$key_rm]);
    }

    $order_statuses = array('bkx-pending', 'bkx-ack', 'bkx-completed', 'bkx-missed');

    if (!empty($get_employees)) {
        foreach ($get_employees as $get_employee):
            $args = array(
                'post_type' => 'bkx_booking',
                'post_status' => $status,
                'meta_key' => 'seat_id',
                'meta_value' => $get_employee,
                'meta_query' => array(
                    array(
                        'key' => 'booking_start_date',
                        'value' => $start_date,
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'booking_end_date',
                        'value' => $end_date,
                        'compare' => '>=',
                    ),
                ),
            );

            $bookedresult = new WP_Query($args);
            if ($bookedresult->have_posts()) {
                $BookingObj = $bookedresult->post;
                $booked_seat_id = get_post_meta($BookingObj->ID, 'seat_id', true);
            }

            if ($booked_seat_id == '') {
                $BkxSeatObj = new BkxSeat('', $get_employee);
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
function bkx_get_booked_slot($slot_id, $get_booked_slot, $get_free_seats, $total_seats, $bookigndate = null, $seat_id = null)
{
    //$_enable_any_seat_selected_id = (int)get_option('select_default_seat'); // Default Seat id
    //$free_seat_all_arr= $get_free_seats;
    $send_seat_block_data = array();
    $slots_blocked = array();
    if ($_SESSION['_display_seat_slots'] > 1) {
        for ($i = 0; $i < $_SESSION['_display_seat_slots']; $i++) {
            $find_slot_value_in_booked_array[] = ($slot_id) + $i;
        }
    } else {
        $find_slot_value_in_booked_array[] = $slot_id;
    }
    //echo '<pre>' . print_r($find_slot_value_in_booked_array, true) . '</pre>';
    if (!empty($get_booked_slot)) :
        foreach ($get_booked_slot as $_slot_id => $booked_slot) {
            foreach ($booked_slot as $seat_id => $slots) {
                foreach ($find_slot_value_in_booked_array as $_find_slot) {
                    if (in_array($_find_slot, $slots)) {
                        //echo 'Now find slot id is : ' . $_find_slot ."<br>";
                        $now_booked_slots_is[] = $slots;
                        $now_booked_slots_with_seats[$_find_slot][] = $seat_id;
                    }
                }
            }
        }
        //echo '<pre> $now_booked_slots_with_seats ' . print_r($now_booked_slots_with_seats, true) . '</pre>';
        if (!empty($now_booked_slots_with_seats)) {
            foreach ($now_booked_slots_with_seats as $booked_slot_id => $booked_seats) {
                //echo '<pre>'.$booked_slot_id.'==Total ='.$total_seats.'=='.sizeof($booked_seats).'=== $booked_seats' . print_r($booked_seats, true) . '</pre>';
                if (sizeof($booked_seats) > 0 && $total_seats > 0 && (sizeof($booked_seats) == $total_seats)) {
                    $slots_blocked[] = $booked_slot_id;
                }
            }
        }
        /**
         * Find Seat where other slot are available
         */
    endif;

    if (!empty($slots_blocked)): $send_seat_block_data['slots_blocked'] = $slots_blocked; endif;

    return $send_seat_block_data;
}

function bkx_get_range($bookingdate, $seatid)
{

    global $wpdb;

    $bookingdate = date('m/d/Y' , strtotime($bookingdate) );
    $arrBookingdate = explode('/', $bookingdate);
    $timestamp = strtotime("{$arrBookingdate[2]}-{$arrBookingdate[0]}-{$arrBookingdate[1]}");
    $day = date('w', $timestamp);
    $day_name = bkx_getDayName($day);

    $GetSeatObj = get_post($seatid);
    $res_seat = get_post_custom($GetSeatObj->ID);

    $db_res_seat_time_arr = array();

    $bkx_business_days = bkx_crud_option_multisite("bkx_business_days");
    if (!is_array($bkx_business_days)) {
        $bkx_business_days = maybe_unserialize($bkx_business_days);
    }

    $seat_days_time = maybe_unserialize($res_seat['seat_days_time'][0]);
    $seat_is_certain_day = $res_seat['seat_is_certain_day'][0];
    if ($seat_is_certain_day == "Y") {
        $selected_days = get_post_meta($GetSeatObj->ID, 'selected_days', true);
    } else {
        $selected_days = $bkx_business_days;
    }

    if (!empty($selected_days)) {
        foreach ($selected_days as $key => $days_obj) {
            $days_data = $days_obj['days'];
            $time_data = $days_obj['time'];
            foreach ($days_data as $key => $day) {
                $days_ope[$day] = array('day' => $day, 'time_from' => $time_data['open'], 'time_till' => $time_data['close']);
            }
        }
    }

    $db_res_seat_time_arr = $days_ope;
    if (!empty($db_res_seat_time_arr)) {

        foreach ($db_res_seat_time_arr as $day_db => $time_data) {
            $day_is = strtolower($time_data['day']);
            $time_from = $time_data['time_from'];
            $time_till = $time_data['time_till'];
            if (isset($day_is) && $day_is == strtolower($day_name)) {
                $res_seat_time_arr['time_from'] = $time_from;
                $res_seat_time_arr['time_till'] = $time_till;
            }
        }
    }
    if (isset($res_seat_time_arr['time_from']) && $res_seat_time_arr['time_from'] != "") {
        $time_value = '';
        $time_from = $res_seat_time_arr['time_from'];
        $time_till = $res_seat_time_arr['time_till'];
        $time_from_arr = explode(" ", $time_from);
        $time_till_arr = explode(" ", $time_till);
        $hrsMinsFromArr = explode(":", $time_from_arr[0]);
        $hrsFrom = $hrsMinsFromArr[0];
        $minsFrom = $hrsMinsFromArr[1];

        if ($time_from_arr[1] == "AM") {
            if ($hrsFrom == 12)
                $hrsFrom = "00";
            $time_from = "{$hrsFrom}:{$minsFrom}";
        } else if ($time_from_arr[1] == "PM") {
            if ($hrsFrom == 12)
                $hrsFrom == 12;
            else
                $hrsFrom = intval($hrsFrom) + 12;
            $time_from = "{$hrsFrom}:{$minsFrom}";
        }
        $hrsMinsTillArr = explode(":", $time_till_arr[0]);
        $hrsTill = $hrsMinsTillArr[0];
        $minsTill = $hrsMinsTillArr[1];
        //print_r($time_till_arr);
        if ($time_till_arr[1] == "AM") {
            if ($hrsTill == 12 && $time_from_arr[1] == "AM")
                $hrsTill = "24";
            $time_till = "{$hrsTill}:{$minsTill}";
        } else if ($time_till_arr[1] == "PM") {
            if ($hrsTill == 12)
                $hrsTill == 12;
            else
                $hrsTill = intval($hrsTill) + 12;
            $time_till = "{$hrsTill}:{$minsTill}";
        }
        $timefromArr = explode(":", $time_from);

        $minsCounter = bkx_getMinsSlot($timefromArr[1]);
        $timefrom_slot = $timefromArr[0] * 4 + $minsCounter;

        $timetillArr = explode(":", $time_till);

        $minsCounterTill = bkx_getMinsSlot($timetillArr[1]);
        $timetill_slot = $timetillArr[0] * 4 + $minsCounterTill;
        $range = range($timefrom_slot, $timetill_slot - 1);
    } else {
        $range = range(1, 96);
    }
    return $range;
}

function bkx_get_template($template_name, $template_path = '')
{
    if (isset($template_name) && $template_name != '') {
        $plugin_path = BKX_PLUGIN_DIR_PATH . 'frontend/';
        $file_path = $plugin_path . '' . $template_name;
        if (!file_exists($file_path)) {
            return;
        }
        include($file_path);
    }
}


/**
 * When the_post is called, put Bookingx post into a global.
 *
 * @param mixed $post
 * @return post object
 */
function wc_setup_bkx_post_data($post)
{
    unset($GLOBALS['bkx_seat']);
    unset($GLOBALS['bkx_base']);
    unset($GLOBALS['bkx_addition']);
    unset($GLOBALS['bkx_post']);

    global $bkx_seat, $bkx_base, $bkx_addition;

    if (is_int($post))
        $post = get_post($post);

    if (empty($post->post_type) || !in_array($post->post_type, array('bkx_seat', 'bkx_base', 'bkx_addition')))
        return;

    if (isset($post->post_type) && $post->post_type == 'bkx_seat')
        $GLOBALS[$post->post_type] = new BkxSeat($post);
    elseif (isset($post->post_type) && $post->post_type == 'bkx_base')
        $GLOBALS[$post->post_type] = new BkxBase($post);
    elseif (isset($post->post_type) && $post->post_type == 'bkx_addition')
        $GLOBALS[$post->post_type] = new BkxExtra($post);

    return $GLOBALS[$post->post_type];
}

add_action('the_post', 'wc_setup_bkx_post_data');

function bkx_placeholder_img_src($size = 'service-thumb')
{
    return apply_filters('bkx_placeholder_img_src', BKX_PLUGIN_DIR_URL . 'images/placeholder.png');
}

function bkx_placeholder_img($size = 'service-thumb')
{
    $dimensions['width'] = 300;
    $dimensions['height'] = 300;

    return apply_filters('bkx_placeholder_img', '<img src="' . bkx_placeholder_img_src() . '" alt="' . esc_attr__('Placeholder', 'bookingx') . '" width="' . esc_attr($dimensions['width']) . '" class="bkx-placeholder wp-post-image" height="' . esc_attr($dimensions['height']) . '" />', $size, $dimensions);
}

function bkx_get_wp_country()
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

    return array_unique(apply_filters('bookingx_country', $all_countries));
}

/**
 * Get full list of currency codes.
 *
 * @return array
 */
function get_bookingx_currencies()
{

    $country_arr = array(
        'AED' => __('United Arab Emirates dirham', 'bookingx'),
        'AFN' => __('Afghan afghani', 'bookingx'),
        'ALL' => __('Albanian lek', 'bookingx'),
        'AMD' => __('Armenian dram', 'bookingx'),
        'ANG' => __('Netherlands Antillean guilder', 'bookingx'),
        'AOA' => __('Angolan kwanza', 'bookingx'),
        'ARS' => __('Argentine peso', 'bookingx'),
        'AUD' => __('Australian dollar', 'bookingx'),
        'AWG' => __('Aruban florin', 'bookingx'),
        'AZN' => __('Azerbaijani manat', 'bookingx'),
        'BAM' => __('Bosnia and Herzegovina convertible mark', 'bookingx'),
        'BBD' => __('Barbadian dollar', 'bookingx'),
        'BDT' => __('Bangladeshi taka', 'bookingx'),
        'BGN' => __('Bulgarian lev', 'bookingx'),
        'BHD' => __('Bahraini dinar', 'bookingx'),
        'BIF' => __('Burundian franc', 'bookingx'),
        'BMD' => __('Bermudian dollar', 'bookingx'),
        'BND' => __('Brunei dollar', 'bookingx'),
        'BOB' => __('Bolivian boliviano', 'bookingx'),
        'BRL' => __('Brazilian real', 'bookingx'),
        'BSD' => __('Bahamian dollar', 'bookingx'),
        'BTC' => __('Bitcoin', 'bookingx'),
        'BTN' => __('Bhutanese ngultrum', 'bookingx'),
        'BWP' => __('Botswana pula', 'bookingx'),
        'BYR' => __('Belarusian ruble', 'bookingx'),
        'BZD' => __('Belize dollar', 'bookingx'),
        'CAD' => __('Canadian dollar', 'bookingx'),
        'CDF' => __('Congolese franc', 'bookingx'),
        'CHF' => __('Swiss franc', 'bookingx'),
        'CLP' => __('Chilean peso', 'bookingx'),
        'CNY' => __('Chinese yuan', 'bookingx'),
        'COP' => __('Colombian peso', 'bookingx'),
        'CRC' => __('Costa Rican col&oacute;n', 'bookingx'),
        'CUC' => __('Cuban convertible peso', 'bookingx'),
        'CUP' => __('Cuban peso', 'bookingx'),
        'CVE' => __('Cape Verdean escudo', 'bookingx'),
        'CZK' => __('Czech koruna', 'bookingx'),
        'DJF' => __('Djiboutian franc', 'bookingx'),
        'DKK' => __('Danish krone', 'bookingx'),
        'DOP' => __('Dominican peso', 'bookingx'),
        'DZD' => __('Algerian dinar', 'bookingx'),
        'EGP' => __('Egyptian pound', 'bookingx'),
        'ERN' => __('Eritrean nakfa', 'bookingx'),
        'ETB' => __('Ethiopian birr', 'bookingx'),
        'EUR' => __('Euro', 'bookingx'),
        'FJD' => __('Fijian dollar', 'bookingx'),
        'FKP' => __('Falkland Islands pound', 'bookingx'),
        'GBP' => __('Pound sterling', 'bookingx'),
        'GEL' => __('Georgian lari', 'bookingx'),
        'GGP' => __('Guernsey pound', 'bookingx'),
        'GHS' => __('Ghana cedi', 'bookingx'),
        'GIP' => __('Gibraltar pound', 'bookingx'),
        'GMD' => __('Gambian dalasi', 'bookingx'),
        'GNF' => __('Guinean franc', 'bookingx'),
        'GTQ' => __('Guatemalan quetzal', 'bookingx'),
        'GYD' => __('Guyanese dollar', 'bookingx'),
        'HKD' => __('Hong Kong dollar', 'bookingx'),
        'HNL' => __('Honduran lempira', 'bookingx'),
        'HRK' => __('Croatian kuna', 'bookingx'),
        'HTG' => __('Haitian gourde', 'bookingx'),
        'HUF' => __('Hungarian forint', 'bookingx'),
        'IDR' => __('Indonesian rupiah', 'bookingx'),
        'ILS' => __('Israeli new shekel', 'bookingx'),
        'IMP' => __('Manx pound', 'bookingx'),
        'INR' => __('Indian rupee', 'bookingx'),
        'IQD' => __('Iraqi dinar', 'bookingx'),
        'IRR' => __('Iranian rial', 'bookingx'),
        'ISK' => __('Icelandic kr&oacute;na', 'bookingx'),
        'JEP' => __('Jersey pound', 'bookingx'),
        'JMD' => __('Jamaican dollar', 'bookingx'),
        'JOD' => __('Jordanian dinar', 'bookingx'),
        'JPY' => __('Japanese yen', 'bookingx'),
        'KES' => __('Kenyan shilling', 'bookingx'),
        'KGS' => __('Kyrgyzstani som', 'bookingx'),
        'KHR' => __('Cambodian riel', 'bookingx'),
        'KMF' => __('Comorian franc', 'bookingx'),
        'KPW' => __('North Korean won', 'bookingx'),
        'KRW' => __('South Korean won', 'bookingx'),
        'KWD' => __('Kuwaiti dinar', 'bookingx'),
        'KYD' => __('Cayman Islands dollar', 'bookingx'),
        'KZT' => __('Kazakhstani tenge', 'bookingx'),
        'LAK' => __('Lao kip', 'bookingx'),
        'LBP' => __('Lebanese pound', 'bookingx'),
        'LKR' => __('Sri Lankan rupee', 'bookingx'),
        'LRD' => __('Liberian dollar', 'bookingx'),
        'LSL' => __('Lesotho loti', 'bookingx'),
        'LYD' => __('Libyan dinar', 'bookingx'),
        'MAD' => __('Moroccan dirham', 'bookingx'),
        'MDL' => __('Moldovan leu', 'bookingx'),
        'MGA' => __('Malagasy ariary', 'bookingx'),
        'MKD' => __('Macedonian denar', 'bookingx'),
        'MMK' => __('Burmese kyat', 'bookingx'),
        'MNT' => __('Mongolian t&ouml;gr&ouml;g', 'bookingx'),
        'MOP' => __('Macanese pataca', 'bookingx'),
        'MRO' => __('Mauritanian ouguiya', 'bookingx'),
        'MUR' => __('Mauritian rupee', 'bookingx'),
        'MVR' => __('Maldivian rufiyaa', 'bookingx'),
        'MWK' => __('Malawian kwacha', 'bookingx'),
        'MXN' => __('Mexican peso', 'bookingx'),
        'MYR' => __('Malaysian ringgit', 'bookingx'),
        'MZN' => __('Mozambican metical', 'bookingx'),
        'NAD' => __('Namibian dollar', 'bookingx'),
        'NGN' => __('Nigerian naira', 'bookingx'),
        'NIO' => __('Nicaraguan c&oacute;rdoba', 'bookingx'),
        'NOK' => __('Norwegian krone', 'bookingx'),
        'NPR' => __('Nepalese rupee', 'bookingx'),
        'NZD' => __('New Zealand dollar', 'bookingx'),
        'OMR' => __('Omani rial', 'bookingx'),
        'PAB' => __('Panamanian balboa', 'bookingx'),
        'PEN' => __('Peruvian nuevo sol', 'bookingx'),
        'PGK' => __('Papua New Guinean kina', 'bookingx'),
        'PHP' => __('Philippine peso', 'bookingx'),
        'PKR' => __('Pakistani rupee', 'bookingx'),
        'PLN' => __('Polish z&#x142;oty', 'bookingx'),
        'PRB' => __('Transnistrian ruble', 'bookingx'),
        'PYG' => __('Paraguayan guaran&iacute;', 'bookingx'),
        'QAR' => __('Qatari riyal', 'bookingx'),
        'RON' => __('Romanian leu', 'bookingx'),
        'RSD' => __('Serbian dinar', 'bookingx'),
        'RUB' => __('Russian ruble', 'bookingx'),
        'RWF' => __('Rwandan franc', 'bookingx'),
        'SAR' => __('Saudi riyal', 'bookingx'),
        'SBD' => __('Solomon Islands dollar', 'bookingx'),
        'SCR' => __('Seychellois rupee', 'bookingx'),
        'SDG' => __('Sudanese pound', 'bookingx'),
        'SEK' => __('Swedish krona', 'bookingx'),
        'SGD' => __('Singapore dollar', 'bookingx'),
        'SHP' => __('Saint Helena pound', 'bookingx'),
        'SLL' => __('Sierra Leonean leone', 'bookingx'),
        'SOS' => __('Somali shilling', 'bookingx'),
        'SRD' => __('Surinamese dollar', 'bookingx'),
        'SSP' => __('South Sudanese pound', 'bookingx'),
        'STD' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'bookingx'),
        'SYP' => __('Syrian pound', 'bookingx'),
        'SZL' => __('Swazi lilangeni', 'bookingx'),
        'THB' => __('Thai baht', 'bookingx'),
        'TJS' => __('Tajikistani somoni', 'bookingx'),
        'TMT' => __('Turkmenistan manat', 'bookingx'),
        'TND' => __('Tunisian dinar', 'bookingx'),
        'TOP' => __('Tongan pa&#x2bb;anga', 'bookingx'),
        'TRY' => __('Turkish lira', 'bookingx'),
        'TTD' => __('Trinidad and Tobago dollar', 'bookingx'),
        'TWD' => __('New Taiwan dollar', 'bookingx'),
        'TZS' => __('Tanzanian shilling', 'bookingx'),
        'UAH' => __('Ukrainian hryvnia', 'bookingx'),
        'UGX' => __('Ugandan shilling', 'bookingx'),
        'USD' => __('United States dollar', 'bookingx'),
        'UYU' => __('Uruguayan peso', 'bookingx'),
        'UZS' => __('Uzbekistani som', 'bookingx'),
        'VEF' => __('Venezuelan bol&iacute;var', 'bookingx'),
        'VND' => __('Vietnamese &#x111;&#x1ed3;ng', 'bookingx'),
        'VUV' => __('Vanuatu vatu', 'bookingx'),
        'WST' => __('Samoan t&#x101;l&#x101;', 'bookingx'),
        'XAF' => __('Central African CFA franc', 'bookingx'),
        'XCD' => __('East Caribbean dollar', 'bookingx'),
        'XOF' => __('West African CFA franc', 'bookingx'),
        'XPF' => __('CFP franc', 'bookingx'),
        'YER' => __('Yemeni rial', 'bookingx'),
        'ZAR' => __('South African rand', 'bookingx'),
        'ZMW' => __('Zambian kwacha', 'bookingx'),
    );

    asort($country_arr, SORT_STRING | SORT_FLAG_CASE);
    return array_unique(apply_filters('bookingx_currencies', $country_arr));
}

/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 * @return string
 */
function get_bookingx_currency_symbol($currency = '')
{


    $symbols = apply_filters('bookingx_currency_symbols', array(
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
    ));

    $currency_symbol = isset($symbols[$currency]) ? $symbols[$currency] : '';

    return apply_filters('bookingx_currency_symbol', $currency_symbol, $currency);
}


/**
 * @return string
 */
function bkx_get_current_currency()
{
    if (is_multisite()) {
        $current_blog_id = get_current_blog_id();
        $currency_option = get_blog_option($current_blog_id, 'currency_option');
    } else {
        $currency_option = get_option('currency_option');
    }
    return get_bookingx_currency_symbol($currency_option);
}

function bkx_sanitize_text_or_array_field($array_or_string)
{
    if (is_string($array_or_string)) {
        $array_or_string = sanitize_text_field($array_or_string);
    } else {
        $array_or_string = array_map('sanitize_text_field', wp_unslash($array_or_string));
    }

    return $array_or_string;
}

/**
 *    For get_option() update_option() and add_option() for compatible for multisite
 */
function bkx_crud_option_multisite($option_name, $option_val = null, $type = 'get')
{
    $get_current_blog_id = get_current_blog_id();
    if(is_multisite()){
        switch_to_blog($get_current_blog_id);
    }
    switch ($type) {
        case 'add':
            $arg = (is_multisite()) ? array($get_current_blog_id, $option_name, $option_val) : array($option_name, $option_val);
            break;
        case 'update':
            $arg = (is_multisite()) ? array($get_current_blog_id, $option_name, $option_val) : array($option_name, $option_val);
            break;
        case 'get':
            $arg = (is_multisite()) ? array($get_current_blog_id, $option_name) : array($option_name);
            break;
        default:

            break;
    }
    $option_data = (is_multisite()) ? call_user_func_array($type . '_blog_option', $arg) : call_user_func_array($type . '_option', $arg);

    return $option_data;
}

function bkx_get_allowed()
{
    $get_alllowed = array('bkx_booking', 'bkx_seat', 'bkx_base', 'bkx_addition');

    return apply_filters('bookingx_get_allowed_on_page', $get_alllowed);
}


function bkx_get_loader()
{
    return '<div class="bookingx-loader" style="display:none">Loading&#8230;</div>';
}


function bkx_getDatesFromRange($start, $end, $format = 'm/d/Y')
{

    if (empty($start) && empty($end))
        return;

    $array = array();

    $interval = new DateInterval('P1D');

    $realStart = DateTime::createFromFormat('d/m/Y', $start);

    $realEnd = DateTime::createFromFormat('d/m/Y', $end);

    $realEnd->add($interval);

    $period = new DatePeriod( $realStart , $interval, $realEnd );
   
    foreach ($period as $date) {

        $array[] = $date->format('m/d/Y');
    }

    return $array;
}

function bkx_validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function bkx_clean($var)
{
    if (is_array($var)) {
        return array_map('bkx_clean', $var);
    } else {
        return is_scalar($var) ? sanitize_text_field($var) : $var;
    }
}

function bkx_get_order_statuses()
{
    $order_statuses = array(
        'bkx-pending' => _x('Pending', 'Order status', 'bookingx'),
        'bkx-ack' => _x('Acknowledged', 'Order status', 'bookingx'),
        'bkx-completed' => _x('Completed', 'Order status', 'bookingx'),
        'bkx-missed' => _x('Missed', 'Order status', 'bookingx'),
        'bkx-cancelled' => _x('Cancelled', 'Order status', 'bookingx'),
        'bkx-failed' => _x('Failed', 'Order status', 'bookingx')
    );
    return apply_filters('bkx_order_statuses', $order_statuses);
}

function bkx_setting_page_callback()
{
    include(BKX_PLUGIN_DIR_PATH . 'admin/settings.php');
}

/**
 * @param null $by_base
 * @return array
 */
function bkx_get_seat_list($by_base = null)
{
    $base_selected_seats = "";
    if (isset($by_base) && $by_base != '') {
        $base_selected_seats = get_post_meta($by_base, 'base_selected_seats', true);
    }
    //Get Seat post Array
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'bkx_seat',
        'post_status' => 'publish',
        'include' => $base_selected_seats,
        'order' => 'ASC',
        'orderby' => 'title',
        'suppress_filters' => false
    );
    $get_seat_array = get_posts($args);

    $arr_seat = array();
    if (!empty($get_seat_array) && !is_wp_error($get_seat_array)) {
        foreach ($get_seat_array as $key => $value) {
            $seat_name = $value->post_title;
            $seat_id = $value->ID;
            $arr_seat[$seat_id] = $seat_name;
        }
    }

    return $arr_seat;
}

/**
 *This Function generates list of bases
 * @access public
 * @param
 * @return array $arr_seat
 */
function bkx_get_base_list()
{
    //Get Base post Array
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'bkx_base',
        'post_status' => 'publish',
        'order' => 'ASC',
        'orderby' => 'title',
        'suppress_filters' => false
    );
    $get_base_array = get_posts($args);

    $arr_base = array();
    if (!empty($get_base_array) && !is_wp_error($get_base_array)) {
        foreach ($get_base_array as $key => $value) {
            $base_name = $value->post_title;
            $base_id = $value->ID;
            $arr_base[$base_id] = $base_name;
        }
    }
    return $arr_base;
}


/**
 *This Function calculates booking time records
 * @access public
 * @param $insertedid , $bookingdate, $bookingtime, $bookingduration, $currentdate
 * @return array $bookingTime
 */
function bkx_bookingTimeRecord($insertedid, $bookingdate, $bookingtime, $bookingduration, $currentdate)
{
    $bookinghour = explode(':', $bookingtime);
    $hours_temp = $bookinghour[0];
    $minutes_temp = $bookinghour[1];
    //$minutes_temp	  = $bookingduration/60;
    $sec_hours_temp = $hours_temp * 60 * 60;
    $sec_minutes_temp = $minutes_temp * 60;
    $total_sec_passed = $sec_hours_temp + $sec_minutes_temp;
    $remainingtime = (24 * 60 * 60 - $total_sec_passed);
    $counter = '';
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
        $arrDataTimeTemp = array('booking_record_id' => $insertedid, 'booking_date' => $bookingdate, 'booking_time_from' => $bookingtime, 'booking_time_till' => '', 'full_day' => $actual_full_day, 'created_date' => $currentdate);
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
        $arrDataTimeTemp = array('booking_record_id' => $insertedid, 'booking_date' => $bookingdate, 'booking_time_from' => $bookingtime, 'booking_time_till' => '', 'full_day' => $actual_full_day, 'created_date' => $currentdate);
        array_push($bookingTime, $arrDataTimeTemp);
        //now starts with the remaining days booking
        $remainingduration = ($bookingduration - $remainingtime);
        $remainingBookingDuration = $bookingduration - $remainingtime;
        $remainingBookingDurationMinutes = $remainingBookingDuration / 60;
        $noOfSlotsRemaining = $remainingBookingDurationMinutes / 15;

        $totalDays = floor($noOfSlotsRemaining / 96); //to find number of days
        $totalDaysMod = $noOfSlotsRemaining % 96; //to find if extend to next day
        if ($totalDaysMod > 0) //if goes to next day add 1 to the days value
        {
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
                $arrBookingtimeNext = array('booking_record_id' => $insertedid, 'booking_date' => $nextDate, 'booking_time_from' => '00:00', 'booking_time_till' => '', 'full_day' => $actual_full_day, 'created_date' => $currentdate);

                array_push($bookingTime, $arrBookingtimeNext);
                $bookedDate = $nextDate;
                $remainingduration = ($remainingduration - 96 * 15 * 60);
            }
        }
    }
    return $bookingTime;
}

/**
 *This Function calls and processes paypal API
 * @access public
 * @param $seat_name , $total_price, $item_number, $quantity, $arrPaypal
 * @return Void
 */
function bkx_do_paypal_api_call($seat_name, $total_price, $item_number, $quantity, $arrPaypal)
{

    $PayPalMode = $arrPaypal['PayPalMode']; // sandbox or live
    $PayPalApiUsername = $arrPaypal['PayPalApiUsername']; //PayPal API Username
    $PayPalApiPassword = $arrPaypal['PayPalApiPassword']; //Paypal API password
    $PayPalApiSignature = $arrPaypal['PayPalApiSignature']; //Paypal API Signature
    $PayPalCurrencyCode = $arrPaypal['PayPalCurrencyCode']; //Paypal Currency Code
    $PayPalReturnURL = $arrPaypal['PayPalReturnURL']; //Point to process.php page
    $PayPalCancelURL = $arrPaypal['PayPalCancelURL']; //Cancel URL if user clicks cancel

    $ItemName = $seat_name; //Item Name
    $ItemPrice = $total_price; //Item Price
    $ItemNumber = $item_number; //Item Number
    $ItemQty = $quantity; // Item Quantity

    $ItemTotalPrice = $ItemPrice;

    //Data to be sent to paypal
    $padata = '&CURRENCYCODE=' . urlencode($PayPalCurrencyCode) .
        '&PAYMENTACTION=Sale' .
        '&ALLOWNOTE=1' .
        '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($PayPalCurrencyCode) .
        '&PAYMENTREQUEST_0_AMT=' . urlencode($ItemTotalPrice) .
        '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode($ItemTotalPrice) .
        '&L_PAYMENTREQUEST_0_QTY0=' . urlencode($ItemQty) .
        '&L_PAYMENTREQUEST_0_AMT0=' . urlencode($ItemPrice) .
        '&L_PAYMENTREQUEST_0_NAME0=' . urlencode($ItemName) .
        '&L_PAYMENTREQUEST_0_NUMBER0=' . urlencode($ItemNumber) .
        '&AMT=' . urlencode($ItemTotalPrice) .
        '&RETURNURL=' . urlencode($PayPalReturnURL) .
        '&CANCELURL=' . urlencode($PayPalCancelURL);

    //We need to execute the "SetExpressCheckOut" method to obtain paypal token
    $paypal = new BkxPayPalGateway();
    $httpParsedResponseAr = $paypal->bkx_PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

    //Respond according to message we receive from Paypal
    if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {

        // If successful set some session variable we need later when user is redirected back to page from paypal.
        $_SESSION['itemprice'] = $ItemPrice;
        $_SESSION['totalamount'] = $ItemTotalPrice;
        $_SESSION['itemName'] = $ItemName;
        $_SESSION['itemNo'] = $ItemNumber;
        $_SESSION['itemQTY'] = $ItemQty;

        if ($PayPalMode == 'sandbox') {
            $paypalmode = '.sandbox';
        } else {
            $paypalmode = '';
        }
        //Redirect user to PayPal store with Token received.

        $paypalurl = 'https://www' . $paypalmode . '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $httpParsedResponseAr["TOKEN"] . '';

        echo '<meta http-equiv="refresh" content="0;url=' . $paypalurl . '">';

    } else {
        //Show error message
        echo '<div style="color:red"><b>Error : </b>' . urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) . '</div>';
        echo '<pre>';
        print_r($httpParsedResponseAr);
        echo '</pre>';
    }
}

/**
 *This Function Send Emails to users aftre booking the seat
 * @access public
 * @param $last_inserted_id
 * @return String $message_body
 */
function bkx_do_send_mail($last_inserted_id)
{
    global $wpdb;
    $bookingObj = new BkxBooking();
    $order_meta = $bookingObj->get_order_meta_data($last_inserted_id);

    $BkxBaseObj = $order_meta['base_arr']['main_obj'];
    $BkxSeatObj = $order_meta['seat_arr']['main_obj'];
    $BkxSeatInfo = $BkxSeatObj->seat_personal_info;


    // multiple recipients
    $to = $order_meta['email'];
    $admin_email = get_settings('admin_email');

    // subject
    $subject = 'Thank you for your booking';
    $email_template = "";

    if (strtolower($order_meta['payment_status']) == "pending" || $order_meta['payment_status'] == "Not Completed") {
        $email_template = 'thankyou';
    } else if (strtolower($order_meta['payment_status']) == "completed") {
        $email_template = 'thankyou';
    }


    $objBookingTemplate = $wpdb->get_results("SELECT * FROM `bkx_booking_template` WHERE template_name='" . trim($email_template) . "'");
    $message_body = "";

    foreach ($objBookingTemplate as $template) {
        if (sizeof($objBookingTemplate) > 0) {
            $message_body = $template->template_value;
        } else {
            $message_body = "Thank you for your booking";
        }
    }

    //change request for template tags calculate booking duration 7/4/2013
    $booking_time = $order_meta['booking_time'];
    //conver second into minutes
    $booking_time_minutes = $booking_time / 60;
    $flag_hours = false;
    if ($booking_time_minutes > 60) {
        $booking_time_hours = $booking_time / (60 * 60);
        $flag_hours = true;
    }
    $booking_duration = "";
    if ($flag_hours == true) {
        $booking_duration = $booking_time_hours . "Hours ";
    } else {
        $booking_duration = $booking_time_minutes . "Minutes ";
    }

    // end booking duration calculation 7/4/2013
    $addition_list = '-';
    if (isset($order_meta['addition_ids']) && $order_meta['addition_ids'] != '') {
        $addition_list = "";
        $BkxExtra = new BkxExtra();
        $BkxExtraObj = $BkxExtra->get_extra_by_ids(rtrim($order_meta['addition_ids'], ","));
        foreach ($BkxExtraObj as $addition) {
            $addition_list .= $addition->get_title() . ",";
        }
    }

    $payment_data = get_post_meta($order_meta['order_id'], 'payment_meta', true);
    if (!empty($payment_data)) {
        $transactionID = $payment_data['transactionID'];
    } else {
        $transactionID = '-';
    }
    $currency_option = get_option('currency_option');
    $currency = bkx_get_current_currency();
    $message_body = str_replace("[fname]", $order_meta['first_name'], $message_body);
    $message_body = str_replace('[lname]', $order_meta['last_name'], $message_body);
    $message_body = str_replace('[total_price]', "{$currency}{$order_meta['total_price']} {$currency_option}", $message_body);
    $message_body = str_replace('[txn_id]', $transactionID, $message_body);
    $message_body = str_replace('[order_id]', $order_meta['order_id'], $message_body);
    $message_body = str_replace('[total_duration]', $order_meta['total_duration'], $message_body);

    //@Added by kanhaiya to show paid amount and balance amount
    $pay_amt = ($order_meta['pay_amt'] != '') ? $order_meta['pay_amt'] : 0;
    $total_price = ($order_meta['total_price'] != '') ? $order_meta['total_price'] : 0;
    $bal = $total_price - $pay_amt;
    $pay_amt = "{$currency}{$pay_amt} {$currency_option}";
    $bal = "{$currency}{$bal} {$currency_option}";

    $message_body = str_replace('[total_price]', $total_price, $message_body);
    $message_body = str_replace('[paid_price]', $pay_amt, $message_body);
    $message_body = str_replace('[bal_price]', $bal, $message_body);
    $message_body = str_replace('[siteurl]', site_url(), $message_body);

    //@ruchira adding additional template tags for email template as per chnage request 7/4/2013
    $message_body = str_replace('[seat_name]', $order_meta['seat_arr']['title'], $message_body);

    $message_body = str_replace('[base_name]', $order_meta['base_arr']['title'], $message_body);
    $message_body = str_replace('[additions_list]', $addition_list, $message_body);
    $message_body = str_replace('[time_of_booking]', $booking_duration, $message_body);
    $message_body = str_replace('[date_of_booking]', $order_meta['booking_start_date'], $message_body);

    if ($BkxSeatInfo['seat_street'])
        $event_address = $BkxSeatInfo['seat_street'] . ", ";
    if ($BkxSeatInfo['seat_city'])
        $event_address .= $BkxSeatInfo['seat_city'] . ", ";
    if ($BkxSeatInfo['seat_state'])
        $event_address .= $BkxSeatInfo['seat_state'] . ", ";
    if ($BkxSeatInfo['seat_postcode'])
        $event_address .= $BkxSeatInfo['seat_postcode'];
    if ($BkxSeatInfo['seat_country'])
        $event_address .= $BkxSeatInfo['seat_country'];

    $message_body = str_replace('[location_of_booking]', $event_address, $message_body);

    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Additional headers
    $headers .= 'To: ' . ucfirst($order_meta['first_name']) . ' <' . $order_meta['email'] . '>' . "\r\n";
    $headers .= 'From: Booking Admin <' . $admin_email . '>' . "\r\n";
    // Mail it
    wp_mail($to, $subject, $message_body, $headers);
    wp_mail($admin_email, $subject, $message_body, $headers);


    return $message_body;
    //}
}


function bkx_mail_format_and_send_process($subject, $data_html, $to_mail, $cc_mail = null, $bcc_mail = null)
{
    if (isset($to_mail)) {
        $subject = $subject;
        $admin_email = get_option('admin_email');

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: Booking Admin <' . $admin_email . '>';
        if ($cc_mail != '') {
            $headers[] = 'Cc: ' . $cc_mail . ' <' . $cc_mail . '>';
        }

        if ($bcc_mail != '') {
            $headers[] = 'Bcc: ' . $bcc_mail;
        }

        wp_mail($to_mail, $subject, $data_html, $headers);
    }

}

/**
 * @return bool
 */
function bkx_is_session_started()
{
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}


function generate_array_from_meta( $metadata )
{
    if ( isset($metadata) ) {
        //return maybe_unserialize( $metadata );
        return array_map('maybe_unserialize', $metadata);
    }  
}

function bkx_localize_string_text(){
    $seat_alias = bkx_crud_option_multisite("bkx_alias_seat");
    $base_alias = bkx_crud_option_multisite("bkx_alias_base");
    $extra_alias = bkx_crud_option_multisite('bkx_alias_addition');
$validation_js_array = array(   'string_enter_phone'=> sprintf(__('%s','bookingx'), 'Please Enter your Phone Number'),
                                        'string_enter_other_day'=> sprintf(__('%s','bookingx'), 'Please select other day to booking'),
                                        'string_booking_dates'=> sprintf(__('%s','bookingx'), 'Booking Dates'),
                                        'string_selecte_a_time'=> sprintf(__('%s','bookingx'), 'Select a time'),
                                        'string_contact_administrator'=> sprintf(__('%s','bookingx'), 'You Can Diretly Contact Administrator and proceed with booking payment'),
                                        'string_list_not_found'=> sprintf(__('%s %s','bookingx'), 'Couldn\'t list', $base_alias),
                                        'string_select_a_seat'=> sprintf(__('%s %s','bookingx'), 'Please select a',$seat_alias),
                                        'string_select_a_base'=> sprintf(__('%s %s','bookingx'), 'Please select a',$base_alias),
                                        'string_extend_base'=> sprintf(__('%s %s %s','bookingx'), 'Please enter extended',$base_alias,'time'),
                                        'string_choose_date'=> sprintf(__('%s','bookingx'), 'Please choose a date'),
                                        'string_choose_enddate'=> sprintf(__('%s','bookingx'), 'Please choose a end date'),
                                        'string_choose_time'=> sprintf(__('%s','bookingx'), 'Please choose time'),
                                        'string_select_appropriate_date_time'=> sprintf(__('%s','bookingx'), 'Please select appropriate date time'),
                                        'string_first_name'=> sprintf(__('%s','bookingx'), 'Please enter your first name'),
                                        'string_last_name'=> sprintf(__('%s','bookingx'), 'Please enter your last name'),
                                        'string_enter_phone'=> sprintf(__('%s','bookingx'), 'Please enter your phone'),
                                        'string_enter_email'=> sprintf(__('%s','bookingx'), 'Please enter your email'),
                                        'string_enter_street'=> sprintf(__('%s','bookingx'), 'Please enter your Street.'),
                                        'string_enter_city'=> sprintf(__('%s','bookingx'), 'Please enter your City.'),
                                        'string_enter_state'=> sprintf(__('%s','bookingx'), 'Please enter your State / Province / Region'),
                                        'string_enter_zip'=> sprintf(__('%s','bookingx'), 'Please enter your Zip / Postal Code.'),
                                        'string_checked_terms'=> sprintf(__('%s','bookingx'), 'Please checked our terms & condtion.'),
                                        'string_checked_privacy'=> sprintf(__('%s','bookingx'), 'Please checked our privacy policy.'),
                                        'string_checked_cancelltion'=> sprintf(__('%s','bookingx'), 'Please checked our cancellation policy.'),
                                        'string_invalid_email'=> sprintf(__('%s','bookingx'), 'You have entered an invalid email address!'),
                                        'string_success_reassign'=> sprintf(__('%s','bookingx'), 'Booking Successfully Reassign.'),
                                        'string_reassign_booking'=> sprintf(__('%s','bookingx'), 'Reassign Booking'),
                                        'string_do_you_want_to_cont'=> sprintf(__('%s','bookingx'), 'Do you want to continue update booking?'),
                                        'string_something_went' => sprintf(__('%s','bookingx'), 'Sorry ! Something went wrong , Please try again.'),
                                        'string_you_require' => sprintf(__('%s','bookingx'), 'you require')    
                            );

    return apply_filters('bkx_localized_validation_js_array', $validation_js_array);
}
<?php
require_once('../../../wp-load.php');
global $wpdb;
$term = '';
if(isset($_POST['seatid'])){
 	$term = $_POST['seatid'];
}
if(isset($_POST['loc'])){	
	$_SESSION["baseid"] = $baseid = $_POST['baseid'];
        $BaseObj = get_post($baseid);
        if(!empty($BaseObj) && !is_wp_error($BaseObj))
        {
            $BaseMetaObj = get_post_custom( $BaseObj->ID ); 
            $base_is_location_fixed = isset( $BaseMetaObj['base_is_location_fixed'] ) ? esc_attr( $BaseMetaObj['base_is_location_fixed'][0] ) : "";
            print_r($base_is_location_fixed);
        }
	//$query = "SELECT base_is_location_fixed FROM bkx_base WHERE base_id = ".trim($baseid); 
	//$objListSeat= $wpdb->get_results($query);
	//print_r($objListSeat[0]->base_is_location_fixed);

}
else
if(isset($_POST['mob'])){

	$baseid1 = $_POST['baseid1'];
        $BaseObj = get_post($baseid);
        if(!empty($BaseObj) && !is_wp_error($baseid1))
        {
            $BaseMetaObj = get_post_custom( $BaseObj->ID ); 
            $base_is_mobile_only = isset( $BaseMetaObj['base_is_mobile_only'] ) ? esc_attr( $BaseMetaObj['base_is_mobile_only'][0] ) : "";
            print_r($base_is_mobile_only);
        }
//	$query_mob = "SELECT base_is_mobile_only FROM bkx_base WHERE base_id = ".trim($baseid1); 
//	$objListMob= $wpdb->get_results($query_mob);
//	print_r($objListMob[0]->base_is_mobile_only);
}
else {
	/**
	 *	Added By : Divyang Parekh
	 *	Reason For : Any Seat Functionality
	 *	Date : 4-11-2015
	 */
	$arr_base = '';
	if($term =='any' && get_option('enable_any_seat')==1):
		$args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'bkx_base',
                        'post_status'      => 'publish',
                );
        
	else:
		//$where_clause ='WHERE seat_id = '.trim($term);
                $args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'bkx_base',
                        'post_status'      => 'publish',
                        'meta_query' => array(
                                                array(
                                                        'key' => 'base_selected_seats',
                                                        'value' => serialize($term),
                                                        'compare' => 'like'
                                                )
                                        )
                );
		//$query = "SELECT bkx_seat_base.base_id,bkx_base.base_name,bkx_base.base_price,bkx_base.base_time_option,bkx_base.base_month,bkx_base.base_day,bkx_base.base_hours, bkx_base.base_minutes FROM `bkx_seat_base` INNER JOIN bkx_base ON bkx_base.base_id = bkx_seat_base.base_id " .$where_clause;
	endif;
        if(isset($term) && $term!='')
        {
            $objSeat = get_post($term);
            if(!empty($objSeat) && !is_wp_error($objSeat))
            {
               $values = get_post_custom( $objSeat->ID ); 
               
                if(!empty($values) && !is_wp_error($values)){
                $seat_street = isset( $values['seat_street'] ) ? esc_attr( $values['seat_street'][0] ) : "";   
                $seat_city = isset( $values['seat_city'] ) ? esc_attr( $values['seat_city'][0] ) : "";
                $seat_state = isset( $values['seat_state'] ) ? esc_attr( $values['seat_state'][0] ) : "";
                $seat_zip = isset( $values['seat_zip'] ) ? esc_attr( $values['seat_zip'][0] ) : "";
                $seat_country = isset( $values['seat_country'] ) ? esc_attr( $values['seat_country'][0] ) : "";
                $seat_is_certain_month = $values['seat_is_certain_month'][0];
                $seat_months = isset($values['seat_months'][0]) && $values['seat_months'][0]!='' ? $temp_seat_months = explode(',',$values['seat_months'][0]) :  '';
                $seat_days = isset($values['seat_days'][0]) && $values['seat_days'][0]!='' ? $temp_seat_days = explode(',',$values['seat_days'][0]) :  '';
                $seat_is_certain_day = $values['seat_is_certain_day'][0];
                $seat_days_time = maybe_unserialize($values['seat_days_time'][0]);
                $res_seat_time= maybe_unserialize($seat_days_time);

                if(!empty($res_seat_time)){
                    $res_seat_time_arr = array();
                    foreach($res_seat_time as $temp)
                    {
                            $res_seat_time_arr[strtolower($temp['day'])]['time_from'] = $temp['time_from']; 				
                            $res_seat_time_arr[strtolower($temp['day'])]['time_till'] = $temp['time_till'];
                    }
                }
                $seat_is_pre_payment = isset( $values['seatIsPrePayment'] ) ? esc_attr( $values['seatIsPrePayment'][0] ) : "";
                $seat_payment_type = isset( $values['seat_payment_type'] ) ? esc_attr( $values['seat_payment_type'][0] ) : ""; //Payment Type

                $seat_deposite_type = isset( $values['seat_payment_option'] ) ? esc_attr( $values['seat_payment_option'][0] ) : ""; // Deposite Type
                $seat_fixed_amount = isset( $values['seat_amount'] ) ? esc_attr( $values['seat_amount'][0] ) : "";
                $seat_percentage = isset( $values['seat_percentage'] ) ? esc_attr( $values['seat_percentage'][0] ) : "";
                $seat_phone = isset( $values['seatPhone'] ) ? esc_attr( $values['seatPhone'][0] ) : "";
                $seat_notification_email = isset( $values['seatEmail'] ) ? esc_attr( $values['seatEmail'][0] ) : "";
                $seat_notification_alternate_email = isset( $values['seatIsAlternateEmail'] ) ? esc_attr( $values['seatIsAlternateEmail'][0] ) : "";
                $seatAlternateEmail = isset( $values['seatAlternateEmail'] ) ? esc_attr( $values['seatAlternateEmail'][0] ) : "";
                $seat_ical_address = isset( $values['seatIsIcal'] ) ? esc_attr( $values['seatIsIcal'][0] ) : "";
                $seatIcalAddress = isset( $values['seatIcalAddress'] ) ? esc_attr( $values['seatIcalAddress'][0] ) : "";
                }
            }
            
                
//            $arr_base['seat_is_certain_time'] = $objSeat[0]->seat_is_certain_time;
//            $arr_base['seat_time_from'] = $objSeat[0]->seat_time_from;
//            $arr_base['seat_time_till'] = $objSeat[0]->seat_time_till;
            $arr_base['seat_is_certain_days'] = $seat_is_certain_day;
            $arr_base['seat_days'] = $seat_days;
            $arr_base['seat_is_certain_month'] = $seat_is_certain_month;
            $arr_base['seat_months'] = $seat_months;
            $arr_base['seat_is_booking_prepayment'] = $seat_is_pre_payment;
        }
	///end seat data fetch
 
        $get_base_array = get_posts( $args );
        if(!empty($get_base_array))
        {
                $counter = 0 ;
                foreach($get_base_array as $key=>$value)
                {
                        $base_name = $value->post_title;
                        $base_id = $value->ID;
                        
                        $values = get_post_custom( $value->ID );
   
                        if(!empty($values) && !is_wp_error($values)){
                        $base_price = isset( $values['base_price'] ) ? esc_attr( $values['base_price'][0] ) : 0;   
                        $base_time_option = isset( $values['base_time_option'] ) ? esc_attr( $values['base_time_option'][0] ) : "";
                        $base_month = isset( $values['base_month'] ) ? esc_attr( $values['base_month'][0] ) : "";
                        $base_day = isset( $values['base_day'] ) ? esc_attr( $values['base_day'][0] ) : "";
                        $base_hours = isset( $values['base_hours'] ) ? esc_attr( $values['base_hours'][0] ) : "";

                        $base_minutes = isset( $values['base_minutes'] ) ? esc_attr( $values['base_minutes'][0] ) : "";   
                        $base_is_extended = isset( $values['base_is_extended'] ) ? esc_attr( $values['base_is_extended'][0] ) : "";
                        $base_is_location_fixed = isset( $values['base_is_location_fixed'] ) ? esc_attr( $values['base_is_location_fixed'][0] ) : "";
                        $base_is_mobile_only = isset( $values['base_is_mobile_only'] ) ? esc_attr( $values['base_is_mobile_only'][0] ) : "";
                        $base_is_location_differ_seat = isset( $values['base_is_location_differ_seat'] ) ? esc_attr( $values['base_is_location_differ_seat'][0] ) : "";
                        $base_street = isset( $values['base_street'] ) ? esc_attr( $values['base_street'][0] ) : "";   
                        $base_city = isset( $values['base_city'] ) ? esc_attr( $values['base_city'][0] ) : "";
                        $base_state = isset( $values['base_state'] ) ? esc_attr( $values['base_state'][0] ) : "";   
                        $base_postcode = isset( $values['base_postcode'] ) ? esc_attr( $values['base_postcode'][0] ) : "";
                        $base_is_allow_addition = isset( $values['base_is_allow_addition'] ) ? esc_attr( $values['base_is_allow_addition'][0] ) : "";   
                        $base_is_unavailable = isset( $values['base_is_unavailable'] ) ? esc_attr( $values['base_is_unavailable'][0] ) : "";   
                        $base_unavailable_from = isset( $values['base_unavailable_from'] ) ? esc_attr( $values['base_unavailable_from'][0] ) : "";
                        $base_unavailable_till = isset( $values['base_unavailable_till'] ) ? esc_attr( $values['base_unavailable_till'][0] ) : "";    
                        $res_seat_final = maybe_unserialize($values['base_selected_seats'][0]);
                        $res_seat_final= maybe_unserialize($res_seat_final);
                        }
                        $base_time = '';
                        if($base_time_option == "H")
                                {
                                        if($base_hours!=0)
                                        {
                                                $base_time .= $base_hours. "Hours ";
                                        }
                                        if($base_minutes!=0)
                                        {
                                                $base_time .= $base_minutes. "Minutes ";
                                        }
                                }
                                else if($base_time_option == "D")
                                {
                                        $base_time .= $base_day. "Days ";
                                }
                        $arr_base['base_list'][$counter]['base_id'] = $base_id;
                        $arr_base['base_list'][$counter]['base_name'] = $base_name;
                        $arr_base['base_list'][$counter]['base_price'] = $base_price;
                        $arr_base['base_list'][$counter]['base_time'] = $base_time;
                        $counter++;
                } 
        }
	$output = json_encode($arr_base);
	echo $output;
}
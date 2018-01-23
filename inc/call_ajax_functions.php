<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action( 'wp_ajax_bkx_action_status', 'bkx_action_status' );
function bkx_action_status() {
    global $wpdb; // this is how you get access to the database
    $bkx_action_status = explode("_", sanitize_text_field($_POST['status']));

    $order_id = $bkx_action_status[0];
    $order_status = $bkx_action_status[1];

    $BkxBooking = new BkxBooking( '', $order_id );
    $BkxBooking->update_status( $order_status );
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_check_seat_exixts', 'prefix_ajax_check_seat_exixts' );
add_action( 'wp_ajax_nopriv_check_seat_exixts', 'prefix_ajax_check_seat_exixts' );
function prefix_ajax_check_seat_exixts()
{
    if ( is_multisite() ) { 
        $current_blog_id = get_current_blog_id();
        switch_to_blog( $current_blog_id );
    }
    $seatEmail = sanitize_text_field($_POST[ 'email' ]);
    $user_id   = username_exists( $seatEmail );
    if ( !$user_id && email_exists( $seatEmail ) == false ) {
        $data = 'success';
    } else {
        $data = 'error';
    }
    echo $data;

    wp_die();
}


add_action('wp_ajax_nopriv_get_time_format', 'ajax_get_time_format');
add_action('wp_ajax_get_time_format', 'ajax_get_time_format');
function ajax_get_time_format()
{
	 
	 $day_input = sanitize_text_field($_REQUEST['name']);
	 if(isset($day_input) && $day_input <= 12){
	  $day_input = sanitize_text_field($_REQUEST['name']);
	 }else{
	  $day_input = sanitize_text_field($_REQUEST['name'][0]);
	 }
	 $am_array = array_map(function($n) { return sprintf('%d:00 AM', $n); }, range($day_input, $day_input) );
	 $pm_array = array_map(function($n) { return sprintf('%d:00 PM', $n); }, range($day_input, $day_input) );
	 $days_array_merge =array_merge($am_array,$pm_array);
	 $search_result1= $days_array_merge;

	echo json_encode($search_result1);

	wp_die();
}

add_action( 'wp_ajax_bookingx_set_as_any_seat', 'bookingx_set_as_any_seat_callback' );
add_action( 'wp_ajax_nopriv_bookingx_set_as_any_seat', 'bookingx_set_as_any_seat_callback' );

function bookingx_set_as_any_seat_callback() {
		
			$seat_id = absint(sanitize_text_field($_GET['seat_id']) );

			if ( 'bkx_seat' === get_post_type( $seat_id ) ) {
				crud_option_multisite('select_default_seat',$seat_id,'update');
			}
		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=bkx_seat' ) );
		wp_die();
}

add_action( 'wp_ajax_get_user', 'get_user' );
function get_user()
{
    global $wpdb; // this is how you get access to the database
    $role       = sanitize_text_field($_POST[ 'data' ]);
    $user_query = new WP_User_Query( array(
         'role' => $role 
    ) );
    $free_user_obj = '';
    $all_users = $user_query->results;

    if(!empty($all_users) && !is_wp_error($all_users)){
        foreach ($all_users as $key => $user) {
            $user_id = $user->data->ID;

            $args = array(
            'post_type'  => 'bkx_seat',
            'meta_query' => array(
                array(
                    'key'     => 'seat_wp_user_id',
                    'value'   => $user_id,
                    'compare' => '=',
                )
            ),
         );

        $seat_obj = new WP_Query( $args );

            if(!empty($seat_obj) && !is_wp_error($seat_obj)){
                if (!$seat_obj->have_posts() ) :
                     $free_user_obj[] = get_user_by('id', $user_id);
                endif;
            }
        }
    }
    echo json_encode( $free_user_obj);
    
    wp_die(); // this is required to return a proper result
}

add_action( 'wp_ajax_bkx_action_view_summary', 'bkx_action_view_summary_callback' );
add_action( 'wp_ajax_nopriv_bkx_action_view_summary', 'bkx_action_view_summary_callback' );

function bkx_action_view_summary_callback()
{
    $post_id = sanitize_text_field($_POST['post_id']);

    if(empty($post_id))
        require '0';

       $get_post = get_post( $post_id );
       $Bkx_Meta_Boxes = new Bkx_Meta_Boxes();

       $order_full_output = $Bkx_Meta_Boxes->bookingx_output( $get_post, 'ajax' );
       $bookingx_note_output = $Bkx_Meta_Boxes->bookingx_note_output( $get_post, 'ajax');
       $bookingx_reassign_output = $Bkx_Meta_Boxes->bookingx_reassign_output( $get_post, 'ajax');
      // $order_summary_output = '<div class="bkx-order_summary"> <h3>Booking #'.$post_id.' Notes </h3></div>';
       echo '<td class="section-1" colspan="10">'.$order_full_output.'</td>
       <td colspan="3" class="section-2">'.$bookingx_note_output.''.$bookingx_reassign_output.'</td>';
    wp_die();
}

add_action( 'wp_ajax_bkx_action_listing_view', 'bkx_action_listing_view_callback' );
add_action( 'wp_ajax_nopriv_bkx_action_listing_view', 'bkx_action_listing_view_callback' );

function bkx_get_page_content_callback()
{
    $page_id = sanitize_text_field($_POST['page_id']);

    if(empty($page_id))
        require '0';

    $get_content = get_post( $page_id );
    $get_data['post_title'] = $get_content->post_title;
    $get_data['post_content'] = apply_filters('the_content', $get_content->post_content);
    echo json_encode( $get_data);
    wp_die();
}

add_action( 'wp_ajax_bkx_get_page_content', 'bkx_get_page_content_callback' );
add_action( 'wp_ajax_nopriv_bkx_get_page_content', 'bkx_get_page_content_callback' );


function bkx_action_add_custom_note_callback()
{
    $booking_id = sanitize_text_field($_POST['booking_id']);
    $bkx_custom_note = $_POST['bkx_custom_note'];

    if(empty($booking_id) || empty($bkx_custom_note))
        require '0';

    $bookingObj =  new BkxBooking( '', $booking_id );
    $bookingObj->add_order_note( sprintf( __(  $bkx_custom_note , 'bookingx' ), ''), 0, $manual );
 
    wp_die();
}

add_action( 'wp_ajax_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback' );
add_action( 'wp_ajax_nopriv_bkx_action_add_custom_note', 'bkx_action_add_custom_note_callback' );


function bkx_cust_setting_callback()
{
    parse_str($_POST['cust_data'], $cust_data);

    $user_id  = get_current_user_id();
    
    $data = array();
    $password_is_ok = 0;
    


    if( empty($cust_data['bkx_cust_name']) ){ $error[] = sprintf(__('%s','Bookingx'), 'Please fill your name.');}
    if( empty($cust_data['bkx_cust_email']) ){ $error[] = 'Please fill your email.';}
    if (filter_var($cust_data['bkx_cust_email'], FILTER_VALIDATE_EMAIL) === false) {
        $error[] = $cust_data['bkx_cust_email']." is not a valid email address";
    }
    if( empty($cust_data['bkx_cust_phone']) ){ $error[] = 'Please fill your phone number.';}
    if( empty($cust_data['bkx_cust_gc']) ){ $error[] = 'Please fill your google calendar id.';}
    if( (empty($cust_data['bkx_cust_pwd']) && !empty($cust_data['bkx_cust_cnf_pwd'])) || (!empty($cust_data['bkx_cust_pwd']) && empty($cust_data['bkx_cust_cnf_pwd'])) || $cust_data['bkx_cust_pwd'] != $cust_data['bkx_cust_cnf_pwd'] ) { $error[] = "Password not match "; }
    if($cust_data['bkx_cust_pwd'] == $cust_data['bkx_cust_cnf_pwd']) { $password_is_ok = 1; }

    if( empty($cust_data['bkx_cust_add_street']) ){ $error[] = 'Please fill your street address.';}
    if( empty($cust_data['bkx_cust_add_city']) ){ $error[] = 'Please fill your city.';}
    if( empty($cust_data['bkx_cust_add_state']) ){ $error[] = 'Please fill your state.';}
    if( empty($cust_data['bkx_cust_add_zip']) ){ $error[] = 'Please fill your zip.';}
     
    if(empty($error) && !empty($user_id)){

        $userdata = array(
                    'ID' => $user_id,
                    'first_name'    =>  $cust_data['bkx_cust_name'],
                    'user_email'    =>  $cust_data['bkx_cust_email']                               
            );
            wp_update_user( $userdata);
            update_user_meta( $user_id, 'bkx_cust_name', $cust_data['bkx_cust_name']  ); 
            update_user_meta( $user_id, 'bkx_cust_email', $cust_data['bkx_cust_email']  ); 
            update_user_meta( $user_id, 'bkx_cust_phone', $cust_data['bkx_cust_phone']  ); 
            update_user_meta( $user_id, 'bkx_cust_gc', $cust_data['bkx_cust_gc']  ); 
            update_user_meta( $user_id, 'bkx_cust_add_street', $cust_data['bkx_cust_add_street']  ); 
            update_user_meta( $user_id, 'bkx_cust_add_city', $cust_data['bkx_cust_add_city']  ); 
            update_user_meta( $user_id, 'bkx_cust_add_state', $cust_data['bkx_cust_add_state']  ); 
            update_user_meta( $user_id, 'bkx_cust_add_zip', $cust_data['bkx_cust_add_zip']  ); 

        if($password_is_ok == 1 && !is_admin()) {
            wp_set_password( $cust_data['bkx_cust_pwd'] , $user_id );
        }

       $data['success']  = 'Your settings saved successfully.';

    }else{
       $data['error'] =  $error; 
    }

    echo json_encode($data);
    wp_die();
    
}
add_action( 'wp_ajax_bkx_cust_setting', 'bkx_cust_setting_callback' );
add_action( 'wp_ajax_nopriv_bkx_cust_setting', 'bkx_cust_setting_callback' );

//bkx_get_base_on_seat
function bkx_get_base_on_seat_callback()
{
    $term = '';
    if(isset($_POST['seatid'])){
        $term = sanitize_text_field($_POST['seatid']);
    }
    if(isset($_POST['loc'])){   
        $_SESSION["baseid"] = $baseid = sanitize_text_field($_POST['baseid']);
            $BaseObj = get_post($baseid);
            if(!empty($BaseObj) && !is_wp_error($BaseObj))
            {
                $BaseMetaObj = get_post_custom( $BaseObj->ID ); 
                $base_is_location_fixed = isset( $BaseMetaObj['base_is_location_fixed'] ) ? esc_attr( $BaseMetaObj['base_is_location_fixed'][0] ) : "";
                 
            }
     

    }elseif(isset($_POST['mob'])){

            $baseid1 = sanitize_text_field($_POST['baseid1']);
            $BaseObj = get_post($baseid1);
            if(!empty($BaseObj) && !is_wp_error($BaseObj))
            {
                $BaseMetaObj = get_post_custom( $BaseObj->ID ); 
                $base_is_mobile_only = isset( $BaseMetaObj['base_is_mobile_only'] ) ? esc_attr( $BaseMetaObj['base_is_mobile_only'][0] ) : "";
                $base_location_type = isset( $BaseMetaObj['base_location_type'] ) ? esc_attr( $BaseMetaObj['base_location_type'][0] ) : "";
                $arr_base['base_location_type'] = $base_location_type;
                

                //print_r($base_is_mobile_only);
            }
     
    }
    else 
    {
        /**
         *  Added By : Divyang Parekh
         *  Reason For : Any Seat Functionality
         *  Date : 4-11-2015
         */
        $arr_base = array();
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
                                                    $base_time .= $base_hours. " Hours ";
                                            }
                                            if($base_minutes!=0)
                                            {
                                                    $base_time .= $base_minutes. " Minutes ";
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
        
    }
    $output = json_encode($arr_base);
    echo $output;
    
    wp_die();
}
add_action( 'wp_ajax_bkx_get_base_on_seat', 'bkx_get_base_on_seat_callback' );
add_action( 'wp_ajax_nopriv_bkx_get_base_on_seat', 'bkx_get_base_on_seat_callback' );



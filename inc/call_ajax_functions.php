<?php

add_action( 'wp_ajax_bkx_action_status', 'bkx_action_status' );
function bkx_action_status() {
    global $wpdb; // this is how you get access to the database
    $bkx_action_status = explode("_", $_POST['status']);

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
    $seatEmail = $_POST[ 'email' ];
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
	 
	 $day_input = $_REQUEST['name'];
	 if(isset($day_input) && $day_input <= 12){
	  $day_input = $_REQUEST['name'];
	 }else{
	  $day_input = $_REQUEST['name'][0];
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
		
			$seat_id = absint( $_GET['seat_id'] );

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
    $role       = $_POST[ 'data' ];
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
    $post_id = $_POST['post_id'];

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
    $page_id = $_POST['page_id'];

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
    $booking_id = $_POST['booking_id'];
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
 



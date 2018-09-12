<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Description of BkxBooking
 *
 * @author Divyang Parekh
 */
class BkxBooking {

    /**
     * @var int|mixed|void
     */
    public $calculate_total = 0;

    /**
     * @var null
     */
    public $order_id;

    /**
     * @var string
     */
    protected $post_type;

     /**
     *
     * @param type $booking_set_array
     */
    public function __construct( $booking_set_array=null,$order_id =null) {

        if(!empty($booking_set_array)){
                $seat_id = $booking_set_array['seat_id'];
                $base_id = $booking_set_array['base_id'];
                $extra_str = $booking_set_array['extra_id'];
                $extra_ids = array_filter(array_unique(explode(',', $extra_str)));

                $seat_post = get_post($seat_id);
                $base_post = get_post($base_id);

                $BkxSeat    = new BkxSeat($seat_post);
                $BkxBase    = new BkxBase($base_post);

                $BkxExtraObj = array();
                if(!empty($extra_ids)){
                    foreach ($extra_ids as $key => $value) {
                        $extra_post = get_post($value);
                        $BkxExtraObj[]  = new BkxExtra($extra_post);
                    }
                }

                $this->BkxSeat  = $BkxSeat;
                $this->BkxBase  = $BkxBase;
                $this->BkxExtra = $BkxExtraObj;

                $this->calculate_total = $this->get_calculate_total();
                $this->order_id = $this->generate_order();
        }
        $this->order_id = $order_id;
        $this->post_type = 'bkx_booking';
        add_filter( 'comments_clauses', array( $this, 'bkx_comments_clauses' ) , 10, 2);
    }

    /**
     * @param $pieces
     * @param $wp_query
     * @return mixed
     */
    function bkx_comments_clauses($pieces, $wp_query)
    {
        global $wpdb;

        $post_id = $wp_query->query_vars['post_id'];
        $pieces['where'] = "( ( comment_approved = '0' OR comment_approved = '1' ) ) AND comment_post_ID = $post_id AND  {$wpdb->prefix}posts.post_type NOT IN ('bkx_seat','bkx_base','bkx_addition') ";
        return $pieces;
    }

    /**
     * @return mixed|void
     */
    public function get_calculate_total()
    {

    	$total = 0;
    	$base_price = 0;
    	$extra_price = 0;

    	/** Get Base price */
    	$BkxBase = $this->BkxBase;
    	$base_price = $BkxBase->get_price();

    	/** Get Extra service price if selected multiple then calculate base on selection */
    	$BkxExtra = $this->BkxExtra;
    	if(!empty($BkxExtra)){
    		foreach ($BkxExtra as $key => $ExtraObj) {
    			$extra_price += $ExtraObj->get_price();
    		}
    	}

    	$total = $base_price + $extra_price;
    	return apply_filters( 'bkx_booking_calculate_total', $total, $this );
    }

    /**
     * @param null $post_data
     * @param null $order_id
     */
    public function generate_order($post_data = null, $order_id = null)
    {
        if(empty($post_data))
            return;

        global $bkx_booking_data;

        if(isset($post_data['order_id']) && $post_data['order_id']!=''){
            $order_id = $post_data['order_id'];
        }

        if(isset($post_data['update_order_slot']) && $post_data['update_order_slot']!=''){
            $order_id = $post_data['update_order_slot'];
        }

        $reg_customer_crud_op = bkx_crud_option_multisite('reg_customer_crud_op');
        // If 1 means enable to create customer

        if(isset($reg_customer_crud_op) == 1 && !is_user_logged_in()) :
                $user_id = BkxBooking::create_new_user($post_data);
        endif;

        if(!isset($order_id) || $order_id == '')
        {
            $order_data['post_type']     = $this->post_type;
            if(isset($user_id) && $user_id!=''){ $order_data['post_author']   = $user_id;}
            $order_data['post_status']   = 'bkx-' . apply_filters( 'bkx_default_order_status', 'pending' );
            $order_data['ping_status']   = 'closed';
            $order_data['post_password'] = uniqid( 'order_' );
            $order_data['post_title']    = sprintf( __( 'Order &ndash; %s', 'bookingx' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'bookingx' ) ) );

            // Insert the booking into the database
            $order_id = wp_insert_post( $order_data );
            $BkxBookingObj = new BkxBooking('',$order_id);
            $BkxBookingObj->add_order_note( sprintf( __( 'Successfully Initial # %1$s Order Created..', 'bookingx' ), $order_id), 0 );
        }

        if(empty($order_id))
            return;

        $post_data['order_id'] = $order_id;
        $post_data['post_status'] = $order_data['post_status'];
        $GLOBALS['bkx_booking_data'] = $post_data;

        //Update order meta
        $BkxBooking = BkxBooking::generate_order_meta($post_data);

    }


    /**
     * @param $post_data
     * @return array|mixed|void
     */
    public function generate_order_meta($post_data)
    {
        $order_id = $post_data['order_id']; // Parent Id

        global $bkx_booking_data;

        if(empty($order_id))
            return;

        if(!empty($post_data)){

            foreach ($post_data as $order_key => $order_val) {
                if(!empty($order_val))
                    update_post_meta($order_id,$order_key,$order_val);
            }
            update_post_meta($order_id,'order_meta_data',$post_data);
        }
        $bookingTimeRecord['order_id'] = $order_id;
        $bookingTimeRecord['bookingdate'] = $post_data['booking_date'];
        $bookingTimeRecord['booking_time_from'] = $post_data['booking_time_from'];
        $bookingTimeRecord['bookingduration'] = $post_data['booking_time'];
        $bookingTimeRecord['currentdate'] = $post_data['created_date'];

        //Formated Booking Date/Time Records by order id
        $bookingTimeRecord = BkxBooking::bkx_bookingTimeRecord($bookingTimeRecord);

        if(!empty($bookingTimeRecord)){
            update_post_meta($order_id,'bookingTimeRecord',$bookingTimeRecord);
        }
        $GLOBALS['bkx_booking_data'] = array('meta_data' => $bkx_booking_data,'time_data' => $bookingTimeRecord);

        //send email that booking confirmed
        do_action( 'bkx_order_edit_status', $order_id, $post_data['post_status'] );

        return $GLOBALS['bkx_booking_data'];
    }

    /**
     * @param $post_data
     * @return int|WP_Error
     */
    public function create_new_user($post_data)
    {
        $user_email = $post_data['email'];
        $display_name = $post_data['display_name'];
        $first_name = $post_data['first_name'];
        $last_name = $post_data['last_name'];

        $check_user_id = username_exists( $user_email );
        if ( !$check_user_id && email_exists($user_email) == false ) {
            $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
                $user_id = wp_create_user( $user_email, $random_password, $user_email );
                $userdata = array(
                    'ID'            => $user_id,
                    'first_name'    =>  $first_name,
                    'last_name'     =>  $last_name ,
                    'display_name'  => ucwords("{$first_name} {$last_name}"),
            );
            $user_id = wp_update_user( $userdata);
        }
        $my_account_page = bkx_crud_option_multisite('bkx_plugin_page_id');
        $account_url = isset($my_account_page) && $my_account_page!='' ? get_permalink($my_account_page) : get_site_url();
        $subject = "BookingX | Congratulation! Your Account created Successfully";
        $data_html ='';
        $data_html .='<p> Dear '.ucwords("{$first_name} {$last_name}").',<p>';
        $data_html .='<p> Your Account created Successfully and check below details :</p>';
        $data_html .='<p> Username : '.$user_email.'</p>';
        $data_html .='<p> Password : '.$random_password.'</p>';
        $data_html .='<p> Also now you can check your order details on my accout section <a href="'.$account_url.'" target="_blank">click here</a>.</p>';

        bkx_mail_format_and_send_process($subject,$data_html,$user_email,$cc_mail,$bcc_mail);

        return $user_id;
    }


    /**
     * @param $status
     * @return null
     */
    public function update_status($status)
    {
        global $wpdb;
        $order_id = $this->order_id;
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $date = date("Y-m-d H:i:s");

        if((isset($order_id) && $order_id!='') && (isset($status) && $status!=''))
        {

            $BkxBookingObj = new BkxBooking('',$order_id);
            $new_status_obj = get_post_status_object('bkx-'.$status);
            $new_status = $new_status_obj->label;
            $old_status = $BkxBookingObj->get_order_status($order_id);

            if($old_status != $new_status){
                $BkxBookingObj->add_order_note( sprintf( __( 'Successfully update order from %1$s to %2$s.', 'bookingx' ), $old_status, $new_status ), 0, $manual );
            }


            $post_update =  $wpdb->update( $wpdb->posts,array('post_status' => 'bkx-'.$status,),
            array( 'ID' => $order_id ));

            update_post_meta($order_id,'last_updated_date',$date);
            update_post_meta($order_id,'updated_by',$current_user_id);
            do_action( 'bkx_order_edit_status', $order_id, $status );

            if(!is_wp_error($post_update))
                return $order_id;
        }
    }

    /**
     * @param $note
     * @param int $is_customer_note
     * @param bool $added_by_user
     * @param null $order_id
     * @param string $comment_type
     * @return false|int
     */
    public function add_order_note($note, $is_customer_note = 0, $added_by_user = false, $order_id = null, $comment_type =  'booking_note') {

        if ( is_user_logged_in() && $added_by_user ) {
            $user                 = get_user_by( 'id', get_current_user_id() );
            $comment_author       = $user->display_name;
            $comment_author_email = $user->user_email;
        } else {
            $comment_author       = __( 'BookingX', 'bookingx' );
            $comment_author_email = strtolower( __( 'BookingX', 'bookingx' ) ) . '@';
            $comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
            $comment_author_email = sanitize_email( $comment_author_email );
        }

        if(!empty($order_id)){
            $this->order_id = $order_id;
        }

        $comment_post_ID        = $this->order_id;
        $comment_author_url     = '';
        $comment_content        = $note;
        $comment_agent          = 'BookingX';
        $comment_type           = $comment_type;
        $comment_parent         = 0;
        $comment_approved       = 1;
        $commentdata            = apply_filters( 'bookingx_new_order_note_data', compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' ), array( 'order_id' => $this->order_id, 'is_customer_note' => $is_customer_note ) );

        $comment_id = wp_insert_comment( $commentdata );

        if ( $is_customer_note ) {
            add_comment_meta( $comment_id, 'is_customer_note', 1 );

            do_action( 'bookingx_new_customer_note', array( 'order_id' => $this->order_id, 'customer_note' => $commentdata['comment_content'] ) );
        }

        return $comment_id;
    }

    /**
     * @param $payment_data
     * @return null
     */
    public function update_payment_meta($payment_data)
    {
        $order_id = $this->order_id;

        if((isset($order_id) && $order_id!='') && (!empty($payment_data)))
        {
            $payment_meta_update =  update_post_meta($order_id,'payment_meta',$payment_data);
            update_post_meta($order_id,'payment_status','completed');


            if(!is_wp_error($payment_meta_update))
                return $order_id;
        }
    }

    /**
     * @param $order_id
     */
    public function get_order_status($order_id)
    {
        if(empty($order_id))
            return;

        $StatusObj = get_post_status_object( get_post_status( $order_id ));
        $order_status = $StatusObj->label;

        return $order_status;
    }

    /**
     * @param $order_id
     * @return mixed|void
     */
    public function get_order_meta_data($order_id)
    {

        if(empty($order_id))
            return;

        $order_meta_data = get_post_meta($order_id,'order_meta_data',true);
        $bookingTimeRecord = get_post_meta($order_id,'bookingTimeRecord',true);


        if(empty($order_meta_data))
            return;
        
        $seat_id = get_post_meta($order_id,'seat_id',true );
        $base_id = get_post_meta($order_id,'base_id',true );
        $addition_ids = get_post_meta($order_id,'addition_ids',true );

        $order_meta_data['seat_id'] = $seat_id;
        $order_meta_data['base_id'] = $base_id;
        $order_meta_data['addition_ids'] = $addition_ids;
        
        $order_seat_slug = get_post_meta($order_id,'order_seat_slug',true );
        $order_base_slug = get_post_meta($order_id,'order_base_slug',true );
        $order_addition_slugs = get_post_meta($order_id,'order_addition_slugs',true );

        if(isset($order_seat_slug) && $order_seat_slug!="" && $order_seat_slug != "imported"){
            $args = array(
              'name'        => $order_seat_slug,
              'post_type'   => 'bkx_seat',
              'post_status' => 'publish',
              'numberposts' => 1
            );
            if(!empty(get_posts($args) && !is_wp_error(get_posts($args)))){
                $bkx_seat_posts = get_posts($args);
                $bkx_seat_post = reset($bkx_seat_posts);
                if(!empty($bkx_seat_post)){
                   $seat_id = $bkx_seat_post->ID;
                   update_post_meta( $order_id, 'seat_id', $seat_id );
                   update_post_meta($order_id,'order_seat_slug', 'imported' );
                }
            }
        }

        if(isset($order_base_slug) && $order_base_slug!="" && $order_base_slug != "imported"){
            $args = array(
              'name'        => $order_base_slug,
              'post_type'   => 'bkx_base',
              'post_status' => 'publish',
               'numberposts' => 1
            );
            if(!empty(get_posts($args) && !is_wp_error(get_posts($args)))){
                $bkx_base_posts = get_posts($args);
                $bkx_base_post = reset($bkx_base_posts);
                if(!empty($bkx_base_post)){
                    $base_id = $bkx_base_post->ID; 
                    update_post_meta( $order_id, 'base_id', $base_id );
                    update_post_meta($order_id,'order_base_slug','imported' );
                }
            }
        }

        if(isset($order_addition_slugs) && $order_addition_slugs!="" && $order_addition_slugs != "imported"){
            $addition_slugs = array_filter(explode(",", $order_addition_slugs));
            if(!empty($addition_slugs)){
                foreach ($addition_slugs as $key => $addition_slug) {
                    $args = array(
                      'name'        => $addition_slug,
                      'post_type'   => 'bkx_addition',
                      'post_status' => 'publish',
                      'numberposts' => 1
                    );

                    if(!empty(get_posts($args) && !is_wp_error(get_posts($args)))){
                        $bkx_addition_posts = get_posts($args);
                        $bkx_addition_post = reset($bkx_addition_posts);
                        if(!empty($bkx_addition_post) && !is_wp_error($bkx_addition_post)){
                            $addiion_post_id = $bkx_addition_post->ID;
                            $addition_slug_data[] = $addiion_post_id;
                        }
                    }
                }
                update_post_meta( $order_id, 'addition_ids', implode(",", $addition_slug_data) );
                update_post_meta($order_id,'order_addition_slugs','imported' );
            }
        }


        if(isset($seat_id) && $seat_id!=''){
            $BkxSeat = new BkxSeat('',$seat_id);
            $seat_arr = array('main_obj'=>$BkxSeat,'title' => $BkxSeat->get_title(),'permalink'=>get_edit_post_link($BkxSeat->id) );
            $order_meta_data['seat_arr'] = $seat_arr;
        }
        if(isset($base_id) && $base_id!=''){
            $BkxBase = new BkxBase('',$base_id);
            $base_arr = array('main_obj'=>$BkxBase,'title' => $BkxBase->get_title(),'permalink'=>get_edit_post_link($BkxBase->id) );
            $order_meta_data['base_arr'] = $base_arr;
        }

        if(isset($addition_ids) && $addition_ids!=''){
            $BkxExtra = new BkxExtra();
            $extra_obj = $BkxExtra->get_extra_by_ids($addition_ids);
            if(!empty($extra_obj)){
                $extra_arr = array();
                foreach ($extra_obj as $key => $extra) {
                    $extra_arr[] = array('main_obj'=> $extra,'title' => $extra->get_title(),'permalink'=>get_edit_post_link($extra->id) );
                }

            }
            $order_meta_data['extra_arr']= $extra_arr;
        }
        $order_meta_data['bookingTimeRecord'] = $bookingTimeRecord;

        return $order_meta_data;
    }

    /**
     * @param null $order_id
     * @param null $search
     * @return mixed|void
     */
    public function get_order_time_data($order_id=null, $search=null)
    {
        if(empty($order_id))
            return;

        $order_time_data = get_post_meta($order_id,'bookingTimeRecord',true);
        return $order_time_data;
    }

    /**
     * @param $order_id
     * @return string|void
     */
    public function get_booking_notes($order_id ){

        global $wpdb;

        if(empty($order_id))
            return;

        $booking_note_data = get_comments( array(
            'post_id'   => $order_id,
        ) );


        $booking_notes = '';
        if(!empty($booking_note_data) && !is_wp_error($booking_note_data)){
             $booking_notes .= '<ul>';
            foreach ($booking_note_data as $key => $notes) {
                $system_notes = $notes->comment_content;
                $note_date    = $notes->comment_date;
                $booking_notes .= '<li> <b>'.$note_date.' : </b> '.$system_notes.'</li>';
            }
            $booking_notes .= '</ul>';
        }
        return $booking_notes;

    }

    /**
     * @param $order_id
     * @return array|int|void
     */
    public function get_booking_notes_for_export($order_id ){

        global $wpdb;

        if(empty($order_id))
            return;
        $booking_note_data = get_comments( array(
            'post_id'   => $order_id,
        ) );
        if(!empty($booking_note_data) && !is_wp_error($booking_note_data)){ return $booking_note_data; }
    }

    /**
     * @param null $search
     * @return array
     */
    public function GetBookedRecords($search=null)
    {
        global $wpdb;

        $date = $search['bookigndate'];
        $service_id = $search['service_id'];
        $status = (empty($search['status'])) ? 'bkx-pending' : $search['status'];
        $BkxBase = new BkxBase('',$service_id);
        $seat_by_base = $BkxBase->get_seat_by_base();
        $seat_id = ($search['seat_id'] == 'any') ? $seat_by_base : $search['seat_id'];

        $args = array(
            'post_type'  => $this->post_type,
            'post_status' => $status,
            'meta_query' => array(
                array(
                    'key'     => 'booking_date',
                    'value'   => date('F j, Y',strtotime($date)),
                    'compare' => '=',
                ),
                array(
                    'key' => 'seat_id',
                    'value'   => $seat_id,
                    'compare' => 'IN',
                ),
            ),
        );

        $bookedresult = new WP_Query( $args );

        if ( $bookedresult->have_posts() ) :
            while ( $bookedresult->have_posts() ) : $bookedresult->the_post();
                $order_id = get_the_ID();
                if(!empty($order_id))
                {
                    $order_data = self::get_order_meta_data($order_id);
                    $order_time_data = reset(self::get_order_time_data($order_id));
                    if(!empty($order_time_data['full_day']))
                    {
                        $full_day = explode(',', $order_time_data['full_day']);
                        if(!empty($full_day)){
                            $order_time_data['full_day'] = $full_day[0];
                        }
                    }
                    $finalarr[]  = $order_time_data+$order_data;
                }
            endwhile;
                return $finalarr;
            endif;
    }

    /**
     * @param null $search
     * @return array
     */
    public function GetBookedRecordsByUser($search=null)
    {
        global $wpdb;
        $bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
        $status  = array('bkx-pending','bkx-ack','bkx-missed','bkx-completed','bkx-cancelled');
        $user_id = (empty($search['user_id'])) ? '' : $search['user_id'];
        $seat_id = (empty($search['seat_id'])) ? '' : $search['seat_id'];
        $role = (empty($search['role'])) ? '' : $search['role'];
        $search_by = (empty($search['search_by'])) ? '' : $search['search_by'];
        $search_date = (empty($search['search_date'])) ? '' : $search['search_date'];
        if(is_admin()){
            $seat_id = get_current_user_id();
        }
        //echo $search_date = date('m/d/Y',strtotime($search_date));
        $search_date = date('F j, Y',strtotime($search_date));
        switch ($search_by) {
            case $bkx_seat_role: //Query for Seat user booking data
                    $args = array(
                        'post_type'  => $this->post_type,
                        'post_status' => $status,
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => 'seat_id',
                                'value'   => $seat_id,
                                'compare' => 'IN',
                            ),
                        ),
                    );
                break;
            case ($search_by == "future") :
                //echo $search_by;
                     $args = array(
                        'post_type'  => $this->post_type,
                        'post_status' => $status,
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => 'booking_date',
                                'value'   => $search_date,
                                'compare' => '>='

                            ),
                        ),
                    );
                break;
            case ($search_by == 'past') :
                //echo $search_date;
                    $args = array(
                        'post_type'  => $this->post_type,
                        'post_status' => $status,
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => 'booking_date',
                                'value'   => $search_date,
                                'compare' => '<'

                            ),
                        ),
                    );
                break;
            case 'administrator': // Query for all booking data
                break;
            default:  // Query for user specific data
                    $args = array(
                        'post_type'  => $this->post_type,
                        'author' => $user_id,
                        'posts_per_page' => -1,
                        'post_status' => $status
                    );
                break;
        }
        $bookedresult = new WP_Query( $args );

        if ( $bookedresult->have_posts() ) :
            while ( $bookedresult->have_posts() ) : $bookedresult->the_post();
                $order_id = get_the_ID();
                if(!empty($order_id))
                {
                    $order_data = self::get_order_meta_data($order_id);
                    $get_order_time_data = self::get_order_time_data($order_id);
                    $order_time_data = reset($get_order_time_data );
                    if(!empty($order_time_data['full_day']))
                    {
                        $full_day = explode(',', $order_time_data['full_day']);
                        if(!empty($full_day)){
                            $order_time_data['full_day'] = $full_day[0];
                        }
                    }
                    $finalarr[]  = $order_time_data+$order_data;
                }
            endwhile;
                return $finalarr;
            endif;
    }


    /**
     *This Function calculates booking time records
     *@access public
     *@param $order_id, $bookingdate, $bookingtime, $bookingduration, $currentdate
     *@return array $bookingTime
     */
    public function bkx_bookingTimeRecord($bookingTimeRecord){

        $order_id = $bookingTimeRecord['order_id'];
        $bookingdate = $bookingTimeRecord['bookingdate'];
        $bookingtime = $bookingTimeRecord['booking_time_from'];
        $bookingduration = $bookingTimeRecord['bookingduration'];
        $currentdate = $bookingTimeRecord['currentdate'];

        $bookinghour      = explode(':',$bookingtime);
        $hours_temp       = $bookinghour[0];
        $minutes_temp     = $bookinghour[1];
        //$minutes_temp   = $bookingduration/60;
        $sec_hours_temp   = $hours_temp*60*60;
        $sec_minutes_temp = $minutes_temp*60;
        $total_sec_passed = $sec_hours_temp + $sec_minutes_temp;
        $remainingtime    = (24*60*60 - $total_sec_passed);
        $counter = '';

        if($minutes_temp=='00'){ $counter = 0;}
        else if($minutes_temp=='15'){ $counter = 1;}
        else if($minutes_temp=='30'){$counter = 2;}
        else if($minutes_temp=='45'){$counter = 3;}

        $bookingTime = array();
        if($bookingduration <= $remainingtime){
            $passed_full_day = intval($hours_temp)*4+$counter;
            $actual_full_day = '';
            $counter_full_day = $passed_full_day+1;
            for($i =0; $i<=intval($bookingduration) ; $i=$i+(15*60))
            {
                $actual_full_day = $actual_full_day.$counter_full_day.",";
                $counter_full_day = $counter_full_day + 1;
            }
            $arrDataTimeTemp = array('booking_record_id'=>$order_id,'booking_date' =>$bookingdate,'booking_time_from'=>$bookingtime,'booking_time_till'=>'','full_day'=>$actual_full_day,'created_date'=> $currentdate);
            array_push($bookingTime, $arrDataTimeTemp);
        }
        else
        {
            //for the current day booking
            $passed_full_day = intval($hours_temp)*4+$counter;
            $actual_full_day = '';
            $counter_full_day = $passed_full_day+1;
            for($i =0; $i<=intval($remainingtime) ; $i=$i+(15*60)){
                    $actual_full_day = $actual_full_day.$counter_full_day.",";
                    $counter_full_day = $counter_full_day + 1;
            }
            $arrDataTimeTemp = array('booking_record_id'=>$order_id,'booking_date' =>$bookingdate,'booking_time_from'=>$bookingtime,'booking_time_till'=>'','full_day'=>$actual_full_day,'created_date'=> $currentdate);
            array_push($bookingTime, $arrDataTimeTemp);
            //now starts with the remaining days booking
            $remainingduration = ($bookingduration - $remainingtime);
            $remainingBookingDuration = $bookingduration - $remainingtime;
            $remainingBookingDurationMinutes = $remainingBookingDuration/60;
            $noOfSlotsRemaining = $remainingBookingDurationMinutes/15;

            $totalDays = floor($noOfSlotsRemaining /96); //to find number of days
            $totalDaysMod = $noOfSlotsRemaining %96; //to find if extend to next day
            if($totalDaysMod > 0) //if goes to next day add 1 to the days value
            {
                $totalDays = $totalDays +1;
            }
            $bookedDate = $bookingdate;
            $nextDate ='';
            for($i=1;$i<=$totalDays;$i=$i+1){
                if($remainingduration>0)
                {
                    $nextDate = date('m/d/Y', strtotime("+1 day", strtotime($bookedDate)));

                    $passed_full_day = 0;
                    $counter_full_day = $passed_full_day+1;
                    if($remainingduration>(96*15*60))
                    {
                        $remainingtime_temp = (96*15*60);
                    }
                    else
                    {
                        $remainingtime_temp = $remainingduration;
                    }

                    $remainingtime_temp_slot =  floor($remainingtime_temp/(15*60));
                    $actual_full_day = "";
                    for($j =1; $j<=$remainingtime_temp_slot; $j++)
                    {
                        $actual_full_day .= $j.",";
                    }
                    //$actual_full_day;
                    $arrBookingtimeNext = array('booking_record_id'=>$order_id,'booking_date' =>$nextDate,'booking_time_from'=>'00:00','booking_time_till'=>'','full_day'=>$actual_full_day,'created_date'=> $currentdate);

                    array_push($bookingTime, $arrBookingtimeNext);
                    $bookedDate = $nextDate;
                    $remainingduration = ($remainingduration - 96*15*60);
                }
            }
        }

        return $bookingTime;
    }

    /**
     * @param $search
     * @return array
     */
    public function order_search_for_filter($search ){

        global $wpdb;

        switch ($search) {
            case 'today':
                $term = date('Y-m-d');
                break;

            default:
                # code...
                break;
        }

        $search_fields = array('booking_start_date', 'booking_date');

        $post_ids = array_unique( array_merge(
                $wpdb->get_col(
                    $wpdb->prepare( "
                        SELECT DISTINCT p1.post_id
                        FROM {$wpdb->postmeta} p1
                        INNER JOIN {$wpdb->postmeta} p2 ON p1.post_id = p2.post_id
                        WHERE ( p1.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "') AND p1.meta_value LIKE '%%%s%%' )
                        ",
                        $term
                    )
                )
            ) );

        return $post_ids;

    }


    /**
     * @param $term
     * @return array
     */
    public function order_search($term ){


        global $wpdb;

        $term     = str_replace( 'Order #', '', bkx_clean( $term ) );
        $post_ids = array();

        // Search fields.
        $search_fields = array_map( 'bkx_clean', apply_filters( 'bk_bkx_booking_search_fields', array(
            'order_id',
            'seat_id',
            'base_id' ,
            'addition_ids',
            'first_name',
            'last_name' ,
            'phone',
            'email',
            'street',
            'city',
            'state',
            'postcode',
            'total_price' ,
            'total_duration',
            'payment_method',
            'payment_status',
            'created_date' ,
            'created_by',
            'booking_date',
            'booking_time',
            'extended_base_time',
            'booking_start_date',
            'booking_end_date',
            'addedtocalendar',
            'booking_time_from',
            'currency'
        ) ) );

        // Search orders.
        if ( is_numeric( $term ) ) {
            $post_ids = array_unique( array_merge(
                $wpdb->get_col(
                    $wpdb->prepare( "SELECT DISTINCT p1.post_id FROM {$wpdb->postmeta} p1 
                        INNER JOIN {$wpdb->posts} p2 ON p1.post_id = p2.ID
                        WHERE p1.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "')
                        OR p2.post_title AND p1.meta_value LIKE '%%%s%%';", bkx_clean( $term ) )
                ),
                array( absint( $term ) )
            ) );
        } elseif ( ! empty( $search_fields ) ) {
            $post_ids = array_unique( array_merge(
                $wpdb->get_col(
                    $wpdb->prepare( "
                        SELECT DISTINCT p1.post_id
                        FROM {$wpdb->postmeta} p1
                        INNER JOIN {$wpdb->posts} p2 ON p1.post_id = p2.ID
                        WHERE
                            
                            ( p1.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "') 
                            OR p2.post_title
                            AND p1.meta_value LIKE '%%%s%%' )
                        ",
                        $term, $term, $term
                    )
                )
            ) );
        }

        return $post_ids;
    }

}

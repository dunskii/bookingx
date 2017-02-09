<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Bkx_Meta_Boxes {


	public $post_type = 'bkx_booking';

	protected static $billing_fields = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
	}

	public static function init_address_fields() {

			$billing_fields = apply_filters( 'bookingx_admin_billing_fields', array(
					'first_name' => array(
						'label' => __( 'First Name', 'bookingx' ),
						'show'  => false
					),
					'last_name' => array(
						'label' => __( 'Last Name', 'bookingx' ),
						'show'  => false
					),
					'address_1' => array(
						'label' => __( 'Address 1', 'bookingx' ),
						'show'  => false
					),
					'city' => array(
						'label' => __( 'City', 'bookingx' ),
						'show'  => false
					),
					'postcode' => array(
						'label' => __( 'Postcode', 'bookingx' ),
						'show'  => false
					),
					'country' => array(
						'label'   => __( 'Country', 'bookingx' ),
						'show'    => false,
					),
					'email' => array(
						'label' => __( 'Email', 'bookingx' ),
					),
					'phone' => array(
						'label' => __( 'Phone', 'bookingx' ),
					),
				) );

			return $billing_fields;
	}

		/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {

		// Orders
			$order_type_object = get_post_type_object( $this->post_type );

			add_meta_box( 'bkx-general-data', sprintf( __( '%s Data', 'bookingx' ), $order_type_object->labels->singular_name ), 'Bkx_Meta_Boxes::order_output', $this->post_type, 'normal', 'high' );

			add_meta_box( 'bkx-order_summary', sprintf( __( '%s Order Summary', 'bookingx' ), $order_type_object->labels->singular_name ), 'Bkx_Meta_Boxes::order_summary_output', $this->post_type, 'side', 'high' );

			add_meta_box( 'bkx-order_reassign', sprintf( __( '%s Reassign', 'bookingx' ), $order_type_object->labels->singular_name ), 'Bkx_Meta_Boxes::order_reassign_output', $this->post_type, 'side' );
		
	}

	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		global $post;

		// Comments/Reviews
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) ) {
			remove_meta_box( 'commentsdiv', 'product', 'normal' );

			add_meta_box( 'commentsdiv', __( 'Reviews', 'Bookingx' ), 'post_comment_meta_box', 'product', 'normal' );
		}
	}


		/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		
		remove_meta_box( 'commentsdiv', $this->post_type, 'normal' );
		remove_meta_box( 'commentstatusdiv', $this->post_type, 'normal' );
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'submitdiv', $this->post_type, 'side' );
	
	
	}

	public function order_output($post)
	{
		$orderObj =  new BkxBooking();
    	$order_meta = $orderObj->get_order_meta_data($post->ID);
    	//print_r($order_meta );
		$order_type_object = get_post_type_object( $post->post_type );
		wp_nonce_field( 'bookingx_save_data', 'bookingx_meta_nonce' );
		if(!empty($order_meta)):
				$translation_array = array('order_id' => $post->ID,
											'seat_id' => $order_meta['seat_id'],
											'base_id' => $order_meta['base_id'],
											'extra_id' => !empty($order_meta['addition_ids']) ? $order_meta['addition_ids'] : '0',
											'action' => 'edit');
		else:
			$translation_array = array('order_id' => '',
											'seat_id' => '',
											'base_id' => '',
											'extra_id' => 0,
											'action' => 'add');
		endif;
		
		wp_localize_script('common_script', 'edit_order_data', $translation_array);


		echo do_shortcode('[bookingform]');?>
			<style type="text/css">
			#post-body-content, #titlediv { display:none }
			</style>
		 <?php
	}


	public function order_reassign_output($post)
	{
		$seat_alias = crud_option_multisite('bkx_alias_seat'); 

		$orderObj =  new BkxBooking();
		$order_meta = $orderObj->get_order_meta_data($post->ID);

		echo sprintf(__('<p>Current %s assign : <a href="%s" title="%s">%s</a></p>','Bookingx'),
			$seat_alias,$order_meta['seat_arr']['permalink'],
			$order_meta['seat_arr']['title'],
			$order_meta['seat_arr']['title']);

		$source = $order_meta['booking_start_date'];
		$date = new DateTime($source);
		$booking_start_date = addslashes($date->format('m/d/Y'));
		$start_time = addslashes($date->format('H:i:s'));

		$get_available_staff = reassign_available_emp_list($order_meta['seat_id'],$order_meta['start_date'],$order_meta['end_date'],$order_meta['base_id']);?>

		<b>Reassign <?php echo $seat_alias;?> to booking: </b>
			 <select id="id_reassign_booking_<?php echo $order_meta['order_id'];?>" name="reassign_booking_<?php echo $order_meta['order_id'];?>">
					  <option value=""> --Reassign <?php echo $seat_alias;?> --</option>
							<?php if(!empty($get_available_staff)):
									foreach ( $get_available_staff as $staff ):
										if(isset($staff['name']) && $staff['name'] != "") :?>
									 <option value="<?php echo $staff['id']; ?>"><?php echo ucwords($staff['name']); ?></option>
									 <?php endif;
									endforeach;
							endif; ?>
             </select>
             <script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery( "#id_reassign_booking_<?php {echo $order_meta['order_id'];}?>").change(function() {
			var booking_date  = "<?php echo $order_meta['booking_start_date'];?>";
			var start_time     = "<?php echo $start_time;?>";
			var durations     = "<?php echo $order_meta['booking_time'];?>";
			var seat_id     = jQuery( "#id_reassign_booking_<?php echo $order_meta['order_id'];?>").val();
			var booking_record_id = '<?php echo $order_meta['order_id'];?>';
			var name = "<?php echo $order_meta['first_name']." ".$order_meta['last_name']; ?>";
			var start_date  ="<?php echo $order_meta['booking_start_date'];?>";
			var end_date  ="<?php echo $order_meta['booking_end_date'];?>";
			var base_id  ="<?php echo $order_meta['base_id'];?>";
			if(seat_id!="")
			{
				check_staff_availability(seat_id,start_time,durations,booking_date,booking_record_id,name,start_date,end_date,base_id);
			}

		});
	});
</script>

             <?php

	}

	public function order_summary_output($post)
	{
		$orderObj =  new BkxBooking();
		$order_meta = $orderObj->get_order_meta_data($post->ID);

		if(empty($order_meta))
			return;

		$seat_alias = crud_option_multisite('bkx_alias_seat');      
    	$base_alias = crud_option_multisite('bkx_alias_base');
    	$addition_alias = crud_option_multisite('bkx_alias_addition');
		
		//print_r($order_meta['extra_arr']);
    	$extra_data = sprintf( __('<p>%s service\'s :','Bookingx'),$addition_alias);
    	if(!empty($order_meta['extra_arr'])){
    		foreach ($order_meta['extra_arr'] as $key => $extra_arr) {
    			$extra_data .=  sprintf(__('&nbsp;<a href="%s" target="_blank">%s</a>&nbsp;','Bookingx'),$extra_arr['permalink'],$extra_arr['title'],$extra_arr['title']);
    		}	
    	}
    	//$extra_data .= sprintf( __('</p>'));

		$order_summary = sprintf('<div class="bkx-order_summary">','Bookingx');
		$order_summary .= sprintf( __('<h3>Order #%d details</h3>','Bookingx'),$post->ID);

		$order_summary .= sprintf(__('<p>Booking Date : %s </p>','Bookingx'),$order_meta['booking_start_date']);
		$order_summary .= sprintf(__('<p>Time : %s </p>','Bookingx'),$order_meta['total_duration']);
		$order_summary .= sprintf(__('<p>Status : %s </p>','Bookingx'),$orderObj->get_order_status($post->ID));
		$order_summary .= sprintf(__('<p>Total : %s%s </p>','Bookingx'),$order_meta['currency'],$order_meta['total_price']);
		$order_summary .= sprintf( __('<p>Customer Name : %s %s</p>','Bookingx'),$order_meta['first_name'],$order_meta['last_name']);

		$order_summary .= sprintf( __('<h3>Service details</h3>','Bookingx'));
		$order_summary .= sprintf(__('<p>%s Name : <a href="%s" title="%s">%s</a></p>','Bookingx'),$seat_alias,$order_meta['seat_arr']['permalink'],$order_meta['seat_arr']['title'],$order_meta['seat_arr']['title']);
		$order_summary .= sprintf(__('<p>%s Name : <a href="%s">%s</a> </p>','Bookingx'),$base_alias,$order_meta['base_arr']['permalink'],$order_meta['base_arr']['title'],$order_meta['base_arr']['title']);
		$order_summary .= $extra_data;

		$order_summary .= sprintf('<div class="bkx-general">','Bookingx');
		$order_summary .= sprintf( __('<h4>General details</h4>','Bookingx'));
		$order_summary .= sprintf( __('<p>Full Name : %s %s</p>','Bookingx'),$order_meta['first_name'],$order_meta['last_name']);
		$order_summary .= sprintf( __('<p>Phone : <a href="callto:%s">%s</a></p>','Bookingx'),$order_meta['phone'],$order_meta['phone']);
		$order_summary .= sprintf( __('<p>Email : <a href="mailto:%s">%s</a></p>','Bookingx'),$order_meta['email'],$order_meta['email']);
		$order_summary .= sprintf( __('<p>Address : %s %s </p>','Bookingx'),$order_meta['city'],$order_meta['state']);
		$order_summary .= sprintf( __('<p>Postcode : %s </p>','Bookingx'),$order_meta['postcode']);
		$order_summary .= sprintf('</div>','Bookingx');
		$order_summary .= sprintf('</div>','Bookingx');

		echo $order_summary;

	}

	public function save_meta_boxes($post_id, $post)
	{
		global $wpdb;

		$curr_date = date("Y-m-d H:i:s");	        
		if (isset($_POST) && $_POST['is_submit_4'] == 1) 
		{
			$strTableName = 'bkx_booking_record';

			$strAdditionName = '';
			if (isset($_POST['addition_name'])) {
				foreach ($_POST['addition_name'] as $key => $value) {
					$strAdditionName .= $value . ",";
				}
			}
			$booking_start_date = date("Y-m-d H:i:s", strtotime($_POST['input_date'] . " " . $_POST['booking_time_from']));
			$booking_end_date = date("Y-m-d H:i:s", strtotime($_POST['input_date'] . " " . $_POST['booking_time_from']) + $_POST['booking_duration_insec']);
			if (!empty($_POST['flag_stop_redirect'])) {
				$payment_method = 'cash';
			} else {
				$payment_method = 'paypal';
			}

			$booking_set_array = array('seat_id' => $_POST['input_seat'], 'base_id' => $_POST['input_base'],'extra_id'=>$strAdditionName);
			
			$arrData = array(
				'seat_id' => $_POST['input_seat'],
				'base_id' => $_POST['input_base'],
				'addition_ids' => $strAdditionName,
				'first_name' => $_POST['input_firstname'],
				'last_name' => $_POST['input_lastname'],
				'phone' => $_POST['input_phonenumber'],
				'email' => $_POST['input_email'],
				'street' => $_POST['input_street'],
				'city' => $_POST['input_city'],
				'state' => $_POST['input_state'],
				'postcode' => $_POST['input_postcode'],
				'total_price' => $_POST['tot_price_val'],
				'total_duration' => str_replace('"', "", $_POST['total_duration']),
				'payment_method' => $payment_method,
				'payment_status' => 'Not Completed',
				'created_date' => $curr_date,
				'created_by' => $current_user->ID,
				'booking_date' => $_POST['input_date'],
				'booking_time' => $_POST['booking_duration_insec'],
				'extended_base_time' => $_POST['input_extended_time'],
				'booking_start_date' => $booking_start_date,
				'booking_end_date' => $booking_end_date,
				'addedtocalendar' => 0,
				'booking_time_from' =>  $_POST['booking_time_from'],
				'currency' => get_current_currency(),
				'order_id' => $post_id
			);

	       $post_update =  $wpdb->update( 
						$wpdb->posts, 
						array( 
							'post_status' => 'bkx-' . apply_filters( 'bkx_default_order_status', 'pending' ),	// string
							'ping_status' => 'closed',
							'post_password' => uniqid( 'order_' ),
							'post_title' => sprintf( __( 'Order &ndash; %s', 'bookingx' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'bookingx' ) ) ),			
						), 
						array( 'ID' => $post_id )
					);
	       	if(!is_wp_error($post_update)){
	       		$order_id  = BkxBooking::generate_order($arrData);
	       		if(isset($order_id) && $order_id!='')
	       		{
	       			//send email that booking confirmed
					$res = do_send_mail($order_id);	

	       			$get_blog_id = get_current_blog_id();
	       			$return = get_admin_url($get_blog_id,'edit.php?post_type=bkx_booking');
	       			wp_safe_redirect($return);
	       			die;
	       		}
	       		 
	       	}else{
	       		return;
	       	}
		}
	}
}

new Bkx_Meta_Boxes();
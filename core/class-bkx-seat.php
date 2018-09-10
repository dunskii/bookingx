<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Description of Class Bkx_Seat
 * Get All seat information
 */
class BkxSeat {

    /**
     * @var mixed
     */
    public $alias;

    /**
     * @var array
     */
    public $meta_data= array();

    /**
     * @var string
     */
    public $bkx_post_type;

    /**
     * @var array|null|WP_Post
     */
    public $post;

    /**
     * @var type
     */
    public $wp_seat_id;

    /**
     * @var type
     */
    public $seat_personal_info;

    /**
     * @var type
     */
    public $seat_available_months;

    /**
     * @var type
     */
    public $seat_payment_info;

    /**
     * @var type
     */
    public $seat_notifiaction_info;

    /**
     * @var mixed|void
     */
    public $booking_url; // Booking url with arguments

    /**
     * @var mixed|void
     */
    public $booking_page_url; // Just Booking Page Url

    /**
     * @var
     */
    public $url_arg_name; // Just Booking Page Url


    /**
     * @var null
     */
    public $id;

    /**
     * BkxSeat constructor.
     * @param null $bkx_post
     * @param null $post_id
     */
    public function __construct($bkx_post = null, $post_id=null) {
            $this->bkx_post_type = 'bkx_seat';
            if(isset($post_id) && $post_id!=''){
                $post_id = $post_id;
                $bkx_post = get_post($post_id);
            }
            if(!empty($bkx_post)){
                $post_id = $bkx_post->ID;
            }
            $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
            $this->alias = $alias_seat;
            $this->post = $bkx_post;
            $this->meta_data = get_post_custom( $post_id );
            $this->wp_seat_id = $this->get_seat_user_id();
            $this->seat_personal_info = $this->get_seat_personal_info();
            $this->seat_available_months = $this->get_seat_available_months();
            $this->seat_available_days = $this->get_seat_available_days();
            $this->seat_payment_info = $this->get_seat_payment_info();
            $this->seat_notifiaction_info = $this->get_seat_notifiaction_info();
            $this->booking_url = $this->get_booking_url();
            $this->booking_page_url = $this->get_booking_page_url();
            $this->id = $post_id;
    }
            
    /**
     *
     * @return type
     */
    public function get_seat_user_id() {
    $meta_data = $this->meta_data;
            $seat_wp_user_id = isset( $meta_data['seat_wp_user_id'][0] ) ? esc_attr( $meta_data['seat_wp_user_id'][0] ) : "";
            return apply_filters( 'bkx_seat_user_id', $seat_wp_user_id, $this );
	}
        
    /**
    * Get the title of the post.
    *
    * @return string
    */
	public function get_title() {
		$title = $this->post->post_title;
		return apply_filters( 'bkx_seat_title', $title, $this );
	}
    /**
     *
     * @return type
     */
        public function get_description() {
		$post_content = $this->post->post_content;
		return apply_filters( 'bkx_seat_description', $post_content, $this );
	}
        
        /**
         * 
         * @return type
         */
        public function get_excerpts() {
		$post_excerpt = $this->post->post_excerpt;
		return apply_filters( 'bkx_seat_price', $post_excerpt, $this );
	}
        /**
         * 
         * @return type
         */
        public function get_thumb() {
		if ( has_post_thumbnail($this->post) ) {
			$image            = get_the_post_thumbnail( $this->post->ID, apply_filters( 'bkx_single_post_large_thumbnail_size', 'bkx_single' ), array(
				'title'	 => $this->get_title(),
				'alt'    => $this->get_title(),
			) );
                    return apply_filters(
				'bookingx_single_post_image_html',
				sprintf(
					'<a href="%s" itemprop="image" class="bookingx-main-image zoom" title="%s" data-rel="prettyPhoto%s">%s</a>',
					esc_url(get_permalink() ),
					esc_attr($this->get_title() ),
					$gallery,
					$image
				),
				$this->post->ID
			);
		}
	}
        /**
         * 
         * @return type
         */
         public function get_thumb_url() {
                $get_thumb_url = the_post_thumbnail_url($this->post);
		return apply_filters( 'bkx_seat_thumb_url', $get_thumb_url, $this );
	}
        /**
         * 
         * @return type
         */
        public function get_seat_personal_info()
        {
            $meta_data = $this->meta_data;
            
            $get_seat_personal_info = array();
            $get_seat_personal_info['seat_street']      = ( isset($meta_data['seat_street'][0] ) ? $meta_data['seat_street'][0] : "" );
            $get_seat_personal_info['seat_city']        = ( isset($meta_data['seat_city'][0] ) ? $meta_data['seat_city'][0] : "" );
            $get_seat_personal_info['seat_state']       = ( isset($meta_data['seat_state'][0] ) ? $meta_data['seat_state'][0] : "" );
            $get_seat_personal_info['seat_zip']         = ( isset($meta_data['seat_zip'][0] ) ? $meta_data['seat_zip'][0] : "" );
            $get_seat_personal_info['seat_country']     = ( isset($meta_data['seat_country'][0] ) ? $meta_data['seat_country'][0] : "" );
            return apply_filters( 'bkx_seat_personal_info', $get_seat_personal_info, $this );
        }
        
        /**
         * 
         * @return type
         */
        public function get_seat_available_months()
        {
            $meta_data = $this->meta_data;
            $seat_is_certain_month =  ( isset($meta_data['seat_is_certain_month'][0] ) ? $meta_data['seat_is_certain_month'][0] : "" ); // Check is enable or not
            $seat_available_months = array();
            $seat_certain_month = "";
            if(isset($seat_is_certain_month) && $seat_is_certain_month == 'Y'):
                $seat_certain_month = array_filter(explode(',',$meta_data['seat_months'][0]));
            endif;
            return apply_filters( 'bkx_seat_available_months', $seat_certain_month, $this );
        }
        
        /**
         * 
         * @return type
         */
        public function get_seat_available_days()
        {
            $meta_data = $this->meta_data;
            $seat_is_certain_day = ( isset($meta_data['seat_is_certain_day'][0] ) ? $meta_data['seat_is_certain_day'][0] : "" ); // Check is enable or not
            $seat_days_time_data = array();
            
            if(isset($seat_is_certain_day) && $seat_is_certain_day == 'Y'):
                $seat_days_time = maybe_unserialize($meta_data['seat_days_time'][0]);
                if(!is_array($seat_days_time)){
                    $seat_days_time = maybe_unserialize($seat_days_time);
                }
                    if(!empty($seat_days_time) ){
                        foreach($seat_days_time as $temp)
                        {
                                $seat_days_time_data[strtolower($temp['day'])]['time_from'] = $temp['time_from']; 				
                                $seat_days_time_data[strtolower($temp['day'])]['time_till'] = $temp['time_till'];
                        }
                    }       
                $seat_days_time_data = array_filter($seat_days_time_data);
            endif;
            
            return apply_filters( 'bkx_seat_available_days', $seat_days_time_data, $this );
        }
        /**
         * 
         * @return type
         */
        public function get_seat_payment_info()
        {
            $meta_data = $this->meta_data;
            $seat_is_pre_payment = isset( $meta_data['seatIsPrePayment'] ) ? esc_attr( $meta_data['seatIsPrePayment'][0] ) : "N";
            $seat_payment_type = isset( $meta_data['seat_payment_type'] ) ? esc_attr( $meta_data['seat_payment_type'][0] ) : "";
            $seat_fixed_amount = isset( $meta_data['seat_amount'] ) ? esc_attr( $meta_data['seat_amount'][0] ) : 0;
            $seat_percentage = isset( $meta_data['seat_percentage'] ) ? esc_attr( $meta_data['seat_percentage'][0] ) : 0;
            $seat_deposite_type = isset( $meta_data['seat_payment_option'] ) ? esc_attr( $meta_data['seat_payment_option'][0] ) : ""; // Deposite Type
            $payment_data = array();
            
            if(isset($seat_is_pre_payment) && $seat_is_pre_payment == 'Y')
            {
                if(isset($seat_payment_type) && ($seat_payment_type =='D'))
                {
                    
                    if(isset($seat_deposite_type) && ($seat_deposite_type == 'FA') && $seat_fixed_amount!=0)
                    {
                        $payment_data['D'] = array('type'=>'FA','amount'=>$seat_fixed_amount,'label'=>'Fixed Amount');
                    } 
                    if(isset($seat_deposite_type) && ($seat_deposite_type == 'P') && $seat_percentage!=0)
                    {
                        $payment_data['D'] = array('type'=>'P','percentage'=>$seat_percentage,'label'=>'Percentage(%)');
                    } 
                }
                if(isset($seat_payment_type) && ($seat_payment_type =='FP'))
                {
                   $payment_data['FP'] = array('type'=>'FP','value'=> 'Full Payment'); 
                }
            }
             return apply_filters( 'bkx_seat_payment_info', $payment_data, $this );
        }
        
        /**
         * 
         * @return type
         */
        public function get_seat_notifiaction_info()
        {
            $meta_data = $this->meta_data;
            
            $seat_phone                         = isset( $meta_data['seatPhone'] ) ? esc_attr( $meta_data['seatPhone'][0] ) : "";
            $seat_notification_email            = isset( $meta_data['seatEmail'] ) ? esc_attr( $meta_data['seatEmail'][0] ) : "";
            $seatAlternateEmail                 = isset( $meta_data['seatAlternateEmail'] ) ? esc_attr( $meta_data['seatAlternateEmail'][0] ) : "";
            $seatIcalAddress                    = isset( $meta_data['seatIcalAddress'] ) ? esc_attr( $meta_data['seatIcalAddress'][0] ) : "";
    
            $get_seat_notifiaction_info = array();
            $get_seat_notifiaction_info['seat_phone']           = $seat_phone;
            $get_seat_notifiaction_info['seat_email']           = $seat_notification_email;
            $get_seat_notifiaction_info['seat_alternate_email'] = $seatAlternateEmail;
            $get_seat_notifiaction_info['seat_ical_address']    = $seatIcalAddress;
            
            return apply_filters( 'bkx_seat_notifiaction_info', $get_seat_notifiaction_info, $this );
        }


    /**
     * @param $extra_id
     * @return array|void
     */
    public function get_seat_by_extra($extra_id )
        {
            if(empty($extra_id))
                return;
            
            $extra_selected_base = get_post_meta( $extra_id, 'extra_selected_base', true );
            
            if(!empty($extra_selected_base))
            {
                        foreach ($extra_selected_base as $key => $base_id){
                           $base_selected_seats = get_post_meta( $base_id, 'base_selected_seats', true );
                            if(!empty($base_selected_seats)){
                                foreach ($base_selected_seats as $key => $seat_id) {
                                    $seat_array[] = $seat_id;
                                    $seat_unique=array_unique($seat_array);
                                }
                            }
                        }  

                    if(!empty($seat_unique)) {
                        foreach ($seat_unique as $key => $seat_id) {
                            $BkxSeatObj =  new BkxSeat('',$seat_id);
                            $seat_array[] = $BkxSeatObj;
                        }
                    } 
            }

            return $seat_array;
        }

    /**
     * @return mixed|void
     */
    public function get_booking_url()
        {
            if ( is_multisite() ) 
            {
                $current_blog_id = get_current_blog_id();
                $bkx_set_booking_page = get_blog_option($current_blog_id,'bkx_set_booking_page');
            }
            else 
            {
                $bkx_set_booking_page = get_option('bkx_set_booking_page');
            }
            if ( get_option('permalink_structure') ) 
            {
                $booking_url = esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page).''.$this->url_arg_name.'/'.get_the_ID()));   
            }
            else
            {
                $arg = array('bookingx_type'=> $url_arg_name,'bookingx_type_id'=>get_the_ID());
                $booking_url = esc_url(add_query_arg( $arg, get_permalink($bkx_set_booking_page) ));   
            }
            
            return apply_filters( 'bkx_booking_url', $booking_url, $this );
        }

    /**
     * @return mixed|void
     */
    public function get_booking_page_url()
        {
            if ( is_multisite() ) 
            {
                $current_blog_id = get_current_blog_id();
                $bkx_set_booking_page = get_blog_option($current_blog_id,'bkx_set_booking_page'); 
            }
            else 
            {
                $bkx_set_booking_page = get_option('bkx_set_booking_page'); 
            }

            return apply_filters( 'bkx_booking_page_url', esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page))), $this );
        }
}

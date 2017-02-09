<?php
/**
 * Description of BkxBase
 *
 * @author indianic
 */
class BkxBase {
    
    public $alias;
    
    public $meta_data= array();
    
    public $bkx_post_type;
    
    public $post;
    
    public $get_price;
    
    public $get_service_time;

    public $get_seat_by_base;
    
    public $service_extended;
    
    public $get_service_fixed_mobile;
    
    public $get_service_unavailable;

    public $booking_url; // Booking url with arguments
    
    public $booking_page_url; // Just Booking Page Url

    public $id;
    

    /**
     * 
     * @param type $bkx_post
     */
    public function __construct( $bkx_post = null , $post_id = null) {
        
                $this->bkx_post_type = $bkx_post->post_type;
                if(isset($post_id) && $post_id!=''){
                    $post_id = $post_id;
                    $bkx_post = get_post($post_id);
                }else{
                    $post_id = $bkx_post->ID;
                }

                $alias_base = get_option('bkx_alias_base', "default");
                $this->alias = $alias_base;
                $this->post = $bkx_post;
                $this->meta_data = get_post_custom( $post_id ); 
                $this->get_price = $this->get_price();
                $this->get_service_time = $this->get_service_time();
                $this->get_seat_by_base = $this->get_seat_by_base();
                $this->service_extended = $this->service_extended();
                $this->get_service_fixed_mobile = $this->get_service_fixed_mobile();
                $this->get_service_unavailable = $this->get_service_unavailable();
                $this->booking_url = $this->get_booking_url();  
                $this->booking_page_url = $this->get_booking_page_url();
                $this->id = $post_id;
    }
    
    /**
    * Get the title of the post.
    *
    * @return string
    */
    public function get_title() {
            $title = $this->post->post_title;
            return apply_filters( 'bkx_base_title', $title, $this );
    }
    /**
    * 
    * @return type
    */
    public function get_description() {
           $post_content = $this->post->post_content;
           return apply_filters( 'bkx_base_description', $post_content, $this );
    }

    /**
    * 
    * @return type
    */
    public function get_excerpts() {
           $post_excerpt = $this->post->post_excerpt;
           return apply_filters( 'bkx_base_price', $post_excerpt, $this );
    }
    /**
    * 
    * @return type
    */
    public function get_thumb() {
           if ( has_post_thumbnail($this->post) ) {
                   $image            = get_the_post_thumbnail( $this->post->ID, apply_filters( 'single_post_large_thumbnail_size', 'bkx_single' ), array(
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
           return apply_filters( 'bkx_base_thumb_url', $get_thumb_url, $this );
    }
    /**
     * 
     * @return type
     */
    public function get_price(){
        $meta_data = $this->meta_data;
        $base_price = isset( $meta_data['base_price'] ) ? esc_attr( $meta_data['base_price'][0] ) : 0; 
        return apply_filters( 'bkx_base_price', $base_price, $this );
    }
    /**
     * 
     * @return type
     */
    public function get_service_time()
    {
        $meta_data = $this->meta_data;
        $base_time_option = isset( $meta_data['base_time_option'] ) ? esc_attr( $meta_data['base_time_option'][0] ) : "";
        $base_month = isset( $meta_data['base_month'] ) ? esc_attr( $meta_data['base_month'][0] ) : "";
        $base_day = isset( $meta_data['base_day'] ) ? esc_attr( $meta_data['base_day'][0] ) : "";
        $base_hours = isset( $meta_data['base_hours'] ) ? esc_attr( $meta_data['base_hours'][0] ) : "";
        $base_minutes = isset( $meta_data['base_minutes'] ) ? esc_attr( $meta_data['base_minutes'][0] ) : "";
        $time_data = array();
        
        if($base_time_option == 'H'):
                $time_data[$base_time_option]['hours']=$base_hours;

                if($base_minutes!=''):
                    $time_data[$base_time_option]['minutes']=$base_minutes;
                endif;  
        endif;
        
        if($base_time_option == 'D'):
            $time_data[$base_time_option]['days']=$base_day;
        endif;
        
        return apply_filters( 'bkx_base_service_time', $time_data, $this );

    }

    public function get_sevice_time_data()
    {
        $service_time = $this->get_service_time();

        if(isset($service_time) && !empty($service_time['H']))
        {
            $hours = $service_time['H']['hours'];
            $minutes = $service_time['H']['minutes'];
            if(isset($hours) && $hours!='')
            {
                if($hours > 1){ $hs = "s"; }
                $hour_data = $hours ." hour".$hs;
            }
            if(isset($minutes) && $minutes!='' && $minutes > 0)
            {
                if($minutes > 1){ $ms = "s"; }
                $minute_data = $minutes ." minute".$ms;
            }

           $get_sevice_time_data = $hour_data .' '. $minute_data;
        }

        return apply_filters( 'bkx_base_sevice_time_data', $get_sevice_time_data, $this );
         
    }

    /**
     * 
     * @return type
     */
    public function service_extended()
    {
        $meta_data = $this->meta_data;
        $base_is_extended = isset( $meta_data['base_is_extended'] ) ? esc_attr( $meta_data['base_is_extended'][0] ) : "";
        return apply_filters( 'bkx_base_service_extended', $base_is_extended, $this );
    }
    /**
     * 
     * @return type
     */
    public function get_seat_by_base()
    {
        $meta_data = $this->meta_data;
        $seat_by_base = maybe_unserialize($meta_data['base_selected_seats'][0]);
        return apply_filters( 'bkx_base_seat_by_base', $seat_by_base, $this );
    }
    /**
     * 
     * @return type
     */
    public function get_service_fixed_mobile()
    {
        $meta_data = $this->meta_data;
        
        $base_is_location_fixed = isset( $meta_data['base_is_location_fixed'] ) ? esc_attr( $meta_data['base_is_location_fixed'][0] ) : "";
        $base_is_mobile_only = isset( $meta_data['base_is_mobile_only'] ) ? esc_attr( $meta_data['base_is_mobile_only'][0] ) : "";
        $base_is_location_differ_seat = isset( $meta_data['base_is_location_differ_seat'] ) ? esc_attr( $meta_data['base_is_location_differ_seat'][0] ) : "";

        $service_fixed_mobile = array();
        
        if(isset($base_is_location_fixed) && $base_is_location_fixed =='Y')
        {
            if($base_is_location_differ_seat == 'Y')
            {
                $service_fixed_mobile['fixed']['service_location']['label'] = 'Fixed Location';
                $base_street = isset( $meta_data['base_street'] ) ? esc_attr( $meta_data['base_street'][0] ) : "";   
                $base_city = isset( $meta_data['base_city'] ) ? esc_attr( $meta_data['base_city'][0] ) : "";
                $base_state = isset( $meta_data['base_state'] ) ? esc_attr( $meta_data['base_state'][0] ) : "";   
                $base_postcode = isset( $meta_data['base_postcode'] ) ? esc_attr( $meta_data['base_postcode'][0] ) : "";
        
                $service_fixed_mobile['fixed']['service_location']['street'] = $base_street;
                $service_fixed_mobile['fixed']['service_location']['city'] = $base_city;
                $service_fixed_mobile['fixed']['service_location']['state'] = $base_state;
                $service_fixed_mobile['fixed']['service_location']['postcode'] = $base_postcode;
            }
        }
        
        if(isset($base_is_mobile_only) && $base_is_mobile_only =='Y')
        {
            $service_fixed_mobile['mobile']['value'] = 'Yes';
            $service_fixed_mobile['mobile']['label'] = 'Mobile Location';
        }
        else
        {
            $service_fixed_mobile['mobile']['value'] = 'No';
            $service_fixed_mobile['mobile']['label'] = 'Mobile Location';
        }
        return apply_filters( 'bkx_base_service_fixed_mobile', $service_fixed_mobile, $this );
    }


    /**
     * 
     */
    public function get_service_unavailable()
    {
        $meta_data = $this->meta_data;
         
        $service_unavailable = array();
        $base_is_unavailable    = isset( $meta_data['base_is_unavailable'] )   ? esc_attr( $meta_data['base_is_unavailable'][0] ) : "";   
        $base_unavailable_from  = isset( $meta_data['base_unavailable_from'] ) ? esc_attr( $meta_data['base_unavailable_from'][0] ) : "";
        $base_unavailable_till  = isset( $meta_data['base_unavailable_till'] ) ? esc_attr( $meta_data['base_unavailable_till'][0] ) : "";    
        if($base_is_unavailable == 'Y'):
            $service_unavailable['from'] = $base_unavailable_from;
            $service_unavailable['till'] = $base_unavailable_till;
        endif;
        return apply_filters( 'bkx_base_service_unavailable', $service_unavailable, $this );
    }

    public function get_booking_url()
        {
             
            $bkx_set_booking_page = get_option('bkx_set_booking_page');
            if ( get_option('permalink_structure') ) 
            {
                $booking_url = esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page).''.$this->url_arg_name.'/'.get_the_ID()));   
            }
            else
            {
                $arg = array('bookingx_type'=>$url_arg_name,'bookingx_type_id'=>get_the_ID());
                $booking_url = esc_url(add_query_arg( $arg, get_permalink($bkx_set_booking_page) ));   
            }
            
            return apply_filters( 'bkx_booking_url', $booking_url, $this );
        }
        
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

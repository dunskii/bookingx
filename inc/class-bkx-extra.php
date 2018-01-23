<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Description of Class Bkx_Seat
 * Get All seat information
 * @author Divyang Parekh
 */
class BkxExtra {

    public  $id;

    public  $post;

    public  $alias;

    public  $bkx_post_type;

    public  $meta_data= array();

    public  $get_price;

    public  $get_extra_time;

    public  $get_extra_overlap;

    public  $get_service_by_extra;
    
    public  $get_addition_unavailable;
    
    public  $booking_url; // Booking url with arguments
    
    public  $booking_page_url; // Just Booking Page Url
    
    

    
    public function __construct( $bkx_post=null , $post_id = null) {
                $this->bkx_post_type = 'bkx_addition';

                 if(isset($post_id) && $post_id!=''){
                    $post_id = $post_id;
                    $bkx_post = get_post($post_id);
                }else{
                    $post_id = $bkx_post->ID;
                }

                if ( is_multisite() ) 
                {
                    $current_blog_id = get_current_blog_id();
                    $alias_addition = get_blog_option( $current_blog_id,'bkx_alias_addition');
                }
                else
                {
                    $alias_addition = get_option('bkx_alias_addition');
                }
                $this->alias        = $alias_addition;
                $this->post         = $bkx_post;
                $this->meta_data    = get_post_custom( $post_id );
                $this->get_price = $this->get_price();
                $this->get_extra_time = $this->get_extra_time();
                $this->get_extra_overlap = $this->get_extra_overlap();
                $this->get_service_by_extra = $this->get_service_by_extra();
                $this->get_addition_unavailable = $this->get_addition_unavailable();
                $this->booking_page_url = $this->get_booking_page_url();
                $this->id = $post_id;
            }
            
    public function get_title() {
		$title = $this->post->post_title;
		return apply_filters( 'bkx_addition_title', $title, $this );
	}
    /**
     * 
     * @return type
     */
    public function get_description() {
		$post_content = $this->post->post_content;
		return apply_filters( 'bkx_addition_description', $post_content, $this );
	}
        
    /**
     * 
     * @return type
     */
    public function get_excerpts() {
		$post_excerpt = $this->post->post_excerpt;
		return apply_filters( 'bkx_addition_price', $post_excerpt, $this );
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
		return apply_filters( 'bkx_addition_thumb_url', $get_thumb_url, $this );
	}
        
    /**
     * 
     * @return type
     */
    public function get_price(){
            $meta_data = $this->meta_data;
            $base_price = isset( $meta_data['addition_price'] ) ? esc_attr( $meta_data['addition_price'][0] ) : 0; 
            return apply_filters( 'bkx_addition_price', $base_price, $this );
    }

    public function get_total_price_by_ids($ids)
    {
        $extra_price = 0;

        if(is_array($ids)){
            $extra_ids = $ids;
        }
        else{
            $extra_ids = array_filter(array_unique(explode(',', $ids)));
        }
        
         if(!empty($extra_ids)){
            foreach ($extra_ids as $key => $value) {
                $extra_post = get_post($value);
                $ExtraObj  = new BkxExtra($extra_post);
                $extra_price += $ExtraObj->get_price();
            }
        }

        return $extra_price;
    }


    public function get_extra_by_ids($ids)
    {
        if(is_array($ids)){
            $extra_ids = $ids;
        }
        else{
            $extra_ids = array_filter(array_unique(explode(',', $ids)));
        }
        
         if(!empty($extra_ids)){
            foreach ($extra_ids as $key => $value) {
                $ExtraObj[]  = new BkxExtra('',$value);
            }
        }

        return $ExtraObj;
    }


    /**
     * 
     * @return type
     */
    public function get_extra_time(){

            $meta_data = $this->meta_data;
            $addition_time_option = isset( $meta_data['addition_time_option'] ) ? esc_attr( $meta_data['addition_time_option'][0] ) : "";
            $addition_months = isset( $meta_data['addition_months'] ) ? esc_attr( $meta_data['addition_months'][0] ) : "";
            $addition_days = isset( $meta_data['addition_days'] ) ? esc_attr( $meta_data['addition_days'][0] ) : "";
            $addition_hours = isset( $meta_data['addition_hours'] ) ? esc_attr( $meta_data['addition_hours'][0] ) : "";
            $addition_minutes = isset( $meta_data['addition_minutes'] ) ? esc_attr( $meta_data['addition_minutes'][0] ) : "";
            $time_data = array();

            if($addition_time_option == 'H'):
                    $time_data[$addition_time_option]['hours']=$addition_hours;

                    if($addition_minutes!=''):
                        $time_data[$addition_time_option]['minutes']=$addition_minutes;
                    endif;  
            endif;

            if($addition_time_option == 'D'):
                $time_data[$addition_time_option]['days']=$addition_days;
            endif;

            return apply_filters( 'bkx_addition_extra_time', $time_data, $this );

    }
        
    public function get_extra_overlap(){

           $meta_data = $this->meta_data;
           $addition_overlap = isset( $meta_data['addition_overlap'] ) ? esc_attr( $meta_data['addition_overlap'][0] ) : "";
           $overlap_array = array();
           
            if($addition_overlap == 'N'):
              $overlap_array[$addition_overlap] = "Own Time Bracket";
            endif;

            if($addition_overlap == 'Y'):
              $overlap_array[$addition_overlap] = "Overlap";
            endif;
           
           return apply_filters( 'bkx_addition_extra_overlap', $overlap_array, $this );
           
    } 

    public function get_service_by_extra(){

           $meta_data = $this->meta_data;
           $service_by_extra = maybe_unserialize($meta_data['extra_selected_base'][0]);
           return apply_filters( 'bkx_addition_service_by_extra', $service_by_extra, $this );
    }

    public function get_extra_by_seat( $data){

    // Process Flow : seat_id ---> Base Data ---> base_id ----> Extra data
    // Seat id to Base data and Base data to Extra
        if(empty($data))
            return;

        $ExtraData = array();
        foreach ($data as $key => $base) {
            
            $base_id = $base->id;

            $args = array(
                'post_type'  => $this->bkx_post_type,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'extra_selected_base',
                        'value'   => $base_id,
                        'compare' => 'LIKE',
                    ),
                ),
            );

            $extraresult = new WP_Query( $args );
          
            if ( $extraresult->have_posts() ) :
                while ( $extraresult->have_posts() ) : $extraresult->the_post();
                        $extra_array[] = get_the_ID();
                        $extra_unique = array_unique($extra_array);
                endwhile;
                wp_reset_query();
                wp_reset_postdata();
            endif;
        }

        if(!empty($extra_unique)){
                foreach ($extra_unique as $key => $extra_id)
                {                    
                    $BkxExtraObj =  new BkxExtra('',$extra_id);
                    $ExtraData[] = $BkxExtraObj;
                }
        }


        return $ExtraData;
    }


    public function get_extra_by_base( $base_id ){

        if(empty($base_id))
            return;

        $ExtraData = array();
         
        $args = array(
            'post_type'  => $this->bkx_post_type,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'extra_selected_base',
                    'value'   => $base_id,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $extraresult = new WP_Query( $args );
      
        if ( $extraresult->have_posts() ) :
            while ( $extraresult->have_posts() ) : $extraresult->the_post();
                    $extra_array[] = get_the_ID();
                    $extra_unique = array_unique($extra_array);
            endwhile;
            wp_reset_query();
            wp_reset_postdata();
        endif;

        if(!empty($extra_unique)){
                foreach ($extra_unique as $key => $extra_id)
                {                    
                    $BkxExtraObj =  new BkxExtra('',$extra_id);
                    $ExtraData[] = $BkxExtraObj;
                }
        }
       
        return $ExtraData;
    }
        
    public function get_addition_unavailable(){
            $meta_data = $this->meta_data;

            $addition_unavailable = array();
            $addition_is_unavailable    = isset( $meta_data['addition_is_unavailable'] )   ? esc_attr( $meta_data['addition_is_unavailable'][0] ) : "";   
            $addition_unavailable_from  = isset( $meta_data['addition_unavailable_from'] ) ? esc_attr( $meta_data['addition_unavailable_from'][0] ) : "";
            $addition_unavailable_till  = isset( $meta_data['addition_unavailable_to'] ) ? esc_attr( $meta_data['addition_unavailable_to'][0] ) : ""; 
            
            if($addition_is_unavailable == 'Y'):
                $addition_unavailable['from'] = $addition_unavailable_from;
                $addition_unavailable['till'] = $addition_unavailable_till;
            endif;
          
            return apply_filters( 'bkx_addition_unavailable', $addition_unavailable, $this );
    }
    
    public function get_booking_page_url(){
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

<?php

/** Single Page ********************************************************/

if ( ! function_exists( 'bookingx_show_post_images' ) ) {
	function bookingx_show_post_images($data) {
            if(is_single() || (!empty($data) || $data['id'] == 'all') || (!empty($data) && strtolower($data['image']) =='yes' && $data['image']!=''))
            {
                bkx_get_template( 'bkx-single/image.php' ); 
            }
            return;
	}
}


if ( ! function_exists( 'bookingx_template_single_title' ) ) {

	/**
	 * Output the post title.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_title($data) {
		bkx_get_template( 'bkx-single/title.php' );
	}
}

if ( ! function_exists( 'bookingx_template_single_price' ) ) {

	/**
	 * Output the post price.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_price($data) {
		bkx_get_template( 'bkx-single/price.php' );
	}
}
if ( ! function_exists( 'bookingx_template_single_excerpt' ) ) {

	/**
	 * Output the post short description (excerpt).
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_excerpt($data) {
            if(is_single() ||  (!empty($data) || $data['id'] == 'all') || (!empty($data) && strtolower($data['extra-info']) =='yes' && $data['extra-info']!=''))
            {
                bkx_get_template( 'bkx-single/short-description.php' );
            }
		
	}
}
if ( ! function_exists( 'bookingx_template_single_meta' ) ) {

	/**
	 * Output the post meta.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_meta($data) {
            if(!is_single())
                return;
	    bkx_get_template( 'bkx-single/meta.php' );
	}
}
if ( ! function_exists( 'bookingx_template_single_descriptions' ) ) {

	/**
	 * Output the post sharing.
	 *
	 * @subpackage	Post
	 */
	function bookingx_template_single_descriptions($data) {
            
           if(is_single() || (!empty($data) && strtolower($data['description']) =='yes' && $data['description']!=''))
           {
               bkx_get_template( 'bkx-single/descriptions.php' );
           }	
	}
}

if(!function_exists('bookingx_template_single_booking_url')){
    /**
     * 
     */
    function bookingx_template_single_booking_url() {
		bkx_get_template( 'bkx-single/add-to-booking.php' );
	}
}

if(!function_exists('bookingx_template_single_pagination')){
    /**
     * 
     */
    function bookingx_template_single_pagination($data) {
        if(!is_single())
                return;
		bkx_get_template( 'bkx-single/pagination.php' );
	}
}

/** global ****************************************************************/
if ( ! function_exists( 'bookingx_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function bookingx_output_content_wrapper($data) {
		bkx_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'bookingx_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function bookingx_output_content_wrapper_end($data) {
		bkx_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'bookingx_get_sidebar' ) ) {

	/**
	 * Get the Service sidebar template.
	 *
	 */
	function bookingx_get_sidebar($data) {
		bkx_get_template( 'global/sidebar.php' );
	}
}


function get_formatted_price($price)
{
	$currencyBlock = '';
	$currency_option = (crud_option_multisite('currency_option') ? crud_option_multisite('currency_option') : 'AUD' );
	if(!empty($price)) :
		$currencyBlock = '<span class="currencyBlock"> 
			<currency itemprop="priceCurrency price" content="'.$currency_option.''.$price.'" style="color:#7BBD4D!important"> '.get_bookingx_currency_symbol( $currency_option ).''.$price.'</currency>
			 
		</span>';
	endif;

	return $currencyBlock;
}

function get_post_price_duration_plain( $postobj, $alias)
{
	if(!empty($postobj)){

			$base_post =  $postobj->post;
			$base_id = $base_post->ID;
			$base_name = $base_post->post_title;

			if(!empty($postobj->get_service_time)){
				$base_service_time = $postobj->get_service_time;
			}

			if(!empty($postobj->get_extra_time)){
				$base_service_time = $postobj->get_extra_time;
			}
			$currencyBlock = get_formatted_price($postobj->get_price);
			$base_service_timeHMObj = $base_service_time['H']; //Hours and minutes
			$base_service_timeDObj = $base_service_time['D']; // Days

			if(!empty($base_service_timeHMObj)):
			    $hours  = isset($base_service_timeHMObj['hours']) && $base_service_timeHMObj['hours'] > 0 && $base_service_timeHMObj['hours']!='' ? $base_service_timeHMObj['hours'].' Hours ' : '';
			    $mins   = isset($base_service_timeHMObj['minutes'])  && $base_service_timeHMObj['minutes'] > 0 && $base_service_timeHMObj['minutes']!='' ? $base_service_timeHMObj['minutes'].' Minutes' : '';
			    $base_service_time_text = $hours.' '.$mins;
			endif;

			if(!empty($base_service_timeDObj)):
			    $day    = isset($base_service_timeDObj['days']) && $base_service_timeDObj['days']!='' ? $base_service_timeDObj['days'].' Days ' : '';
			    $base_service_time_text = $day;
			endif;

			$available_services['time'] = $base_service_time_text;
			$available_services['price'] = $currencyBlock;
	}

	return $available_services;
}


function get_post_with_price_duration( $get_base_by_seat, $alias, $type = null)
{
	$available_services = '';
	$bkx_set_booking_page = crud_option_multisite('bkx_set_booking_page');
	if(!empty($bkx_set_booking_page)){
		$booking_url = esc_url(user_trailingslashit(get_permalink($bkx_set_booking_page)));
	}

	if(!empty($get_base_by_seat)){
		$available_services .= '<h3>Availabel '.$alias.'</h3>';
		$available_services .= '<ul>';
		foreach ($get_base_by_seat as $key => $BaseObj) {
			$base_post =  $BaseObj->post;
			$base_id = $base_post->ID;
			$base_name = $base_post->post_title;
			if($type == 'seat'){
				$ClassObj = new BkxBase( '', $base_id );
				$post_type = $base_post->post_type;
			}

			if($type == 'base'){
				$ClassObj = new BkxBase( '', $base_id );
				$post_type = $base_post->post_type;
			}

			if(!empty($BaseObj->get_service_time)){
				$base_service_time = $BaseObj->get_service_time;

			}

			if(!empty($BaseObj->get_extra_time)){
				$base_service_time = $BaseObj->get_extra_time;
			}
			if(!empty($BaseObj->get_price)){
				$currencyBlock = get_formatted_price($BaseObj->get_price);				
			}


			$base_service_timeHMObj = $base_service_time['H']; //Hours and minutes
			$base_service_timeDObj = $base_service_time['D']; // Days

			if(!empty($base_service_timeHMObj)):
		    $hours  = isset($base_service_timeHMObj['hours']) && $base_service_timeHMObj['hours'] > 0 && $base_service_timeHMObj['hours']!='' ? $base_service_timeHMObj['hours'].' Hours ' : '';
		    $mins   = isset($base_service_timeHMObj['minutes'])  && $base_service_timeHMObj['minutes'] > 0 && $base_service_timeHMObj['minutes']!='' ? $base_service_timeHMObj['minutes'].' Minutes' : '';
		    $base_service_time_text = " - ".$hours.' '.$mins;
			endif;

			if(!empty($base_service_timeDObj)):
			    $day    = isset($base_service_timeDObj['days']) && $base_service_timeDObj['days']!='' ? $base_service_timeDObj['days'].' Days ' : '';
			    $base_service_time_text = $day;
			endif;
 			if(!empty($base_id)):
			$available_services .= '<li style="padding:8px;"><a href="'.get_permalink($base_id).'">'.$base_name.'</a> '.$base_service_time_text.' '.$currencyBlock.'<form style="float:right;" method="post" enctype="multipart/form-data" action="'.$booking_url.'">
					 	<input type="hidden" name="type" value="'.$post_type.'" />
				                <input type="hidden" name="id" value="'. $base_id.'" />
					 	<button type="submit" class="small-button smallblue alt">'.sprintf( __('Book now','Bookingx')).'</button>
				</form></li>';
			endif;

		}

		$available_services .= '</ul>';
	}

	return $available_services;
}
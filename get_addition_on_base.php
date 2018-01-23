<?php
session_start();
require_once('../../../wp-load.php');
global $wpdb;
$term = '';
$seat_id = sanitize_text_field($_POST['seat_id']);
if(isset($_POST['baseid']))
{
    $term = sanitize_text_field($_POST['baseid']);
    
    $args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'bkx_addition',
                        'post_status'      => 'publish',
                        'meta_query' => array(
                                                array(
                                                        'key' => 'extra_selected_base',
                                                        'value' => serialize($term),
                                                        'compare' => 'like'
                                                )
                                        )
                );
     $get_extra_array = get_posts( $args );
}

if(!empty($get_extra_array) && !is_wp_error($get_extra_array))
{
        $arr_seat = '';
        $counter = 0 ;
        foreach($get_extra_array as $key=>$value)
        {

        $extra_name = $value->post_title;
        $extra_id = $value->ID;

        $values = get_post_custom( $extra_id );

            if(!empty($values) && !is_wp_error($values)){ 
            $addition_price = isset( $values['addition_price'] ) ? esc_attr( $values['addition_price'][0] ) : "";   
            $addition_time_option = isset( $values['addition_time_option'] ) ? esc_attr( $values['addition_time_option'][0] ) : "";   
            $addition_overlap = isset( $values['addition_overlap'] ) ? esc_attr( $values['addition_overlap'][0] ) : "";   
            $addition_months = isset( $values['addition_months'] ) ? esc_attr( $values['addition_months'][0] ) : "";   

            $addition_days = isset( $values['addition_days'] ) ? esc_attr( $values['addition_days'][0] ) : "";   
            $addition_hours = isset( $values['addition_hours'] ) ? esc_attr( $values['addition_hours'][0] ) : "";   
            $addition_minutes = isset( $values['addition_minutes'] ) ? esc_attr( $values['addition_minutes'][0] ) : "";   
            $addition_is_unavailable = isset( $values['addition_is_unavailable'] ) ? esc_attr( $values['addition_is_unavailable'][0] ) : "";   
            $addition_available_from = isset( $values['addition_unavailable_from'] ) ? esc_attr( $values['addition_unavailable_from'][0] ) : "";   
            $addition_available_to = isset( $values['addition_unavailable_to'] ) ? esc_attr( $values['addition_unavailable_to'][0] ) : "";   
            $res_base_final = maybe_unserialize($values['extra_selected_base'][0]);
            $res_base_final= maybe_unserialize($res_base_final);

            $extra_selected_seats = maybe_unserialize($values['extra_selected_seats'][0]);
            $extra_selected_seats = maybe_unserialize($extra_selected_seats);
            }

            if( !empty($extra_selected_seats) && in_array($seat_id, $extra_selected_seats)){

                $extra_time = '';
                    if($addition_time_option == "H")
                    {
                            if($addition_hours!=0)
                            {
                                    $extra_time .= $addition_hours. " Hours ";
                            }
                            if($addition_minutes!=0)
                            {
                                    $extra_time .= $addition_minutes. " Minutes ";
                            }
                    }
                    else if($seat_time_option == "D")
                    {
                            $extra_time .= $addition_days. "Days ";
                    }

                    $arr_seat[$counter]['addition_alies'] = get_option('bkx_alias_addition','Addition');
                    $arr_seat[$counter]['addition_id'] = $extra_id;
                    $arr_seat[$counter]['addition_name'] = $extra_name;
                    $arr_seat[$counter]['addition_price'] = $addition_price;
                    $arr_seat[$counter]['addition_time'] = $extra_time;
                    $counter++;

            }
                    
        }  
}

$output = json_encode($arr_seat);
echo $output;
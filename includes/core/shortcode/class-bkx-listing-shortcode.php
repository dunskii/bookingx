<?php
/**
 * Listing ShortCodes for Seat, Base and Extra Services
 *
 * @package Bookingx/Core/Classes
 */

defined( 'ABSPATH' ) || exit;
class BKX_Listing_ShortCodes
{
    private static $post_type = array();

    public static function init() {
        add_shortcode( 'bookingx', array( __CLASS__, 'bookingx_shortcode_callback' ) );
        self::$post_type = array('bkx_seat', 'bkx_base', 'bkx_addition');
    }

    static function bookingx_shortcode_callback( $atts ){
        ob_start();
        if(!empty($atts)){
            $post_type = self::find_post_type($atts);
            $id = 0;
            if( isset($atts['seat-id']) && $atts['seat-id'] != ""){
                $id = isset( $atts['seat-id'] ) && $atts['seat-id'] > 0 ? $atts['seat-id'] : 0;
            }elseif(isset($atts['base-id']) && $atts['base-id'] != ""){
                $id = isset( $atts['base-id'] ) && $atts['base-id'] > 0 ? $atts['base-id'] : 0;
            }elseif(isset($atts['extra-id']) && $atts['extra-id'] != ""){
                $id = isset( $atts['extra-id'] ) && $atts['extra-id'] > 0 ? $atts['extra-id'] : 0;
            }

            $class = "";
            if($id > 0 ){
                $query = new WP_Query( array( 'post_type' => $post_type, 'post__in' => array($id)) );
            }else{
                $query = new WP_Query( array( 'post_type' => $post_type) );
                $class = "booking-x-lists";
            }
            if ($query->have_posts()) :?>
            <div class="container <?php echo $class;?>">
                <div class="row">
                    <?php while ( $query->have_posts() ) : $query->the_post();
                        if($id > 0 ){
                            bkx_get_template("content-single-{$post_type}.php", $atts);
                        }else{
                            bkx_get_template("content-{$post_type}.php", $atts);
                        }
                    endwhile;?>
                </div>
            </div>
            <?php
            else:
                do_action("bookingx_no_{$post_type}_found");
            endif;

            return ob_get_clean();
        }
    }

    private static function find_post_type( $atts ){
        if(empty($atts))
            return;
        $find_archive = array('seat-id' => 'bkx_seat', 'base-id' => 'bkx_base', 'extra-id' => 'bkx_addition');
        foreach ($find_archive as $key => $data ){
            if (array_key_exists($key, $atts)){
                return $find_archive[$key];
            }
        }
    }
}
add_action( 'init', array( 'BKX_Listing_ShortCodes', 'init' ) );
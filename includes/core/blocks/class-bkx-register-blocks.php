<?php defined( 'ABSPATH' ) || exit;

class Bkx_Register_Blocks
{
    public $plugin_name = 'booking-x';

    public function __construct() {
        add_filter( 'block_categories', array( $this ,'bkx_block_categories') );
    }

    function bkx_block_categories( $categories ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'booking-x',
                    'title' => __( 'Booking X', 'bookingx' ),
                    'icon'  => 'calendar-alt',
                ),
            )
        );
    }
}
new Bkx_Register_Blocks();
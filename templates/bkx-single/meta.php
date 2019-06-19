<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $bkx_seat,$bkx_base;
    if(!empty($bkx_seat)):
        bkx_get_template( 'bkx-single/meta/seat.php' );
    endif;
    if(!empty($bkx_base)):
        bkx_get_template( 'bkx-single/meta/base.php' );
    endif;
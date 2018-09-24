<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $bkx_base;
    $bkx_alias_base = bkx_crud_option_multisite('bkx_alias_base');
    ?>
    <ul class="additional-info">
    <?php
    $meta_data = $bkx_base->meta_data;

    $base_location_type = isset($meta_data['base_location_type'][0]) ? esc_attr($meta_data['base_location_type'][0]) : "Fixed Location";
    
    $Duration = $bkx_base->get_service_time();
    if((isset($Duration['H']))){
        $DurationHMObj = $Duration['H'];
    }
    if((isset($Duration['D']))){
        $DurationDObj = $Duration['D'];
    }
    echo sprintf('<li><h3>%s</h3></li>',__('Additional Information : ', 'bookingx'));
    if(!empty($DurationHMObj)):
        $hours  = isset($DurationHMObj['hours']) && $DurationHMObj['hours'] > 0 && $DurationHMObj['hours']!='' ? $DurationHMObj['hours'].' Hours ' : '';
        $mins   = isset($DurationHMObj['minutes'])  && $DurationHMObj['minutes'] > 0 && $DurationHMObj['minutes']!='' ? $DurationHMObj['minutes'].' Minutes' : '';
        $duration_text = $hours.' and '.$mins;
    endif;

    if(!empty($DurationDObj)):
        $day    = isset($DurationDObj['days']) && $DurationDObj['days']!='' ? $DurationDObj['days'].' Days ' : '';
        $duration_text = $day;
    endif;

    $extended = $bkx_base->service_extended();

    $extended_label = isset($extended) && $extended == 'Y' ? 'Yes' : 'No';

    echo sprintf('<li><div class="durations"><label>%s :</label><span> %s</span></li>','Duration ',$duration_text);

    echo sprintf(__('<li><div class="extended"><label>%2$s Time Extended :</label><span> %1$s</span></li>', 'bookingx'), $extended_label , $bkx_alias_base);

    $fixed_mobile = $bkx_base->get_service_fixed_mobile();

    $fixedObj   = $fixed_mobile['fixed'];
    $mobileObj  = $fixed_mobile['mobile'];

    if ($base_location_type == "FM")
    {
        $base_location_type = "Fixed & Mobile";
    }

    echo sprintf(__('<li><div class="fixed-mobile"><label>This is a %1$s %2$s </label></li>', 'bookingx'), $base_location_type , $bkx_alias_base);
    ?>
    </ul>
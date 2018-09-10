<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    global $bkx_base;
    ?>
    <ul class="additional-info">
    <?php
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

    echo sprintf('<li><div class="extended"><label>%s :</label><span> %s</span></li>','Can Service time be extended?',$extended_label);

    $fixed_mobile = $bkx_base->get_service_fixed_mobile();

    $fixedObj   = $fixed_mobile['fixed'];
    $mobileObj  = $fixed_mobile['mobile'];

    echo sprintf('<li><div class="fixed-mobile"><label>%s :</label><span> %s</span></li>','Is this Service in a fixed location or mobile?',$extended_label);
    ?>
    </ul>
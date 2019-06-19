<?php
/**
 * Booking Form Progress Bar
 */
defined( 'ABSPATH' ) || exit;
$bkx_label_step_1 =  (!empty(bkx_crud_option_multisite("bkx_label_of_step1"))) ? bkx_crud_option_multisite("bkx_label_of_step1") : 'Please select what you would like to book';
$bkx_label_step_1 = sprintf(esc_html__('%1$s','bookingx'), $bkx_label_step_1 );
?>
<div class="progress-wrapper">
    <h1>Default booking form</h1>
    <ul class="progress-tracker progress-tracker--text">
        <li class="progress-step is-active left-curve progress-step-1">
            <span class="progress-text">
              <h4 class="progress-title">Step 1 : <?php echo $bkx_label_step_1;?></h4>
            </span>
        </li>
        <li class="progress-step progress-step-2">
            <span class="progress-text">
              <h4 class="progress-title">Step 2 : Date & time</h4>
            </span>
        </li>
        <li class="progress-step progress-step-3">
            <span class="progress-text">
              <h4 class="progress-title">Step 3 : Details</h4>
            </span>
        </li>
        <li class="progress-step right-curve progress-step-4">
            <span class="progress-text">
              <h4 class="progress-title">Step 4 : Check booking</h4>
            </span>
        </li>
    </ul>
</div>

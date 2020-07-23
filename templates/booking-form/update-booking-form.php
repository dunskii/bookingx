<?php
/**
 * Update Booking Form Page
 */
defined('ABSPATH') || exit;
?>
<div class="booking-x update-form">
    <div class="main-container">
        <div class="booking-x container">
            <div class="bkx-booking-form">
                <!--  Start Progress Bar     -->
                <?php bkx_get_template('booking-form/progress-bar/progress-bar.php'); ?>
                <!--  End Progress Bar     -->
                <div class="bookingx-error-group"></div>
                <!-- Start Initialized Steps -->
                <?php bkx_get_template('booking-form/steps/step-1.php'); ?>
                <?php bkx_get_template('booking-form/steps/step-2.php') ?>
                <?php bkx_get_template('booking-form/steps/step-2b.php')?>
                <?php bkx_get_template('booking-form/steps/step-3.php') ?>
                <?php bkx_get_template('booking-form/steps/step-4.php') ?>
                <!-- End Initialized Steps -->
            </div>
        </div>
    </div>
</div>
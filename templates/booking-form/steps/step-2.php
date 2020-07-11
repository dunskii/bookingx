<?php
/**
 * Booking Form Step 2
 */
defined('ABSPATH') || exit;
$skip_this_step = SkipStep();
$skip_this_step_style = ($skip_this_step == true) ? 'style = "display: none;"' : "";
?>
<div class="step-2 bkx-form-deactivate default" data-active="2" data-booking-style="default">
    <div class="user-detail">
    </div>
    <div class="row px-lg-1 py-1">
        <div class="col-5 col-md-6 calender-setup"> <!--remove col-lg-5 lg -->
            <div class="form-group">
                <label><?php esc_html_e("Select Date", 'bookingx'); ?></label>
            </div>
            <div id="bkx-calendar"></div>
        </div>
        <div class="col-7 col-md-6 slots-setup"> <!--remove col-lg-7 lg -->
            <div class="form-group">
                <label><?php esc_html_e("Select Time Slot", 'bookingx'); ?></label>
            </div>
            <div class="indicator">
                <ul>
                    <li class="booked"><?php esc_html_e("Booked", 'bookingx'); ?></li>
                    <li class="open"><?php esc_html_e("Open", 'bookingx'); ?></li>
                    <li class="current"><?php esc_html_e("Current", 'bookingx'); ?></li>
                </ul>
            </div>
            <div class="select-time">
                <table class="table table-bordered booking-slots" data-total_slots="0" data-starting_slot="0"
                       data-date="">
                </table>
            </div>
        </div>
    </div>
    <div class="button-wrapper">
        <?php if ($skip_this_step == true) : ?>
            <button type="submit" class="btn btn-default bkx-form-submission-final">
                <?php esc_html_e("Booking Update", 'bookingx'); ?></button>
        <?php else : ?>
            <button type="submit" class="btn btn-default bkx-form-submission-next">
                <?php esc_html_e("Next", 'bookingx'); ?></button>
        <?php endif; ?>
        <button <?php echo $skip_this_step_style; ?> type="submit" class="btn btn-default bkx-form-submission-previous">
            <?php esc_html_e("Previous", 'bookingx'); ?>
        </button>
    </div>
</div>
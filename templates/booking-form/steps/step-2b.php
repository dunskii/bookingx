<?php
/**
 * Booking Form Step 2b
 */
defined('ABSPATH') || exit;
?>
<div class="step-2 bkx-form-deactivate day-style" data-active="2" data-booking-style="day_style">
    <div class="user-detail mb-5">

    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label><?php esc_html_e("Select schedule", 'bookingx'); ?>:</label>
            </div>
            <div class="indicator schedule-indicator">
                <ul>
                    <li class="booked"><?php esc_html_e("Booked", 'bookingx'); ?></li>
                    <li class="open"><?php esc_html_e("Open", 'bookingx'); ?></li>
                    <li class="current"><?php esc_html_e("Current", 'bookingx'); ?></li>
                </ul>
            </div>
            <div class="schedule-wrap">
                <div class="owl-carousel owl-theme bkx-available-days"></div>
            </div>
        </div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default button bkx-form-submission-next"><?php esc_html_e("Next", 'bookingx'); ?></button></button>
        <button type="submit" class="btn btn-default button bkx-form-submission-previous"><?php esc_html_e("Previous", 'bookingx'); ?></button>
    </div>
</div>

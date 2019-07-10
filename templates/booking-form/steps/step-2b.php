<?php
/**
 * Booking Form Step 2b
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="step-2 bkx-form-deactivate day-style" data-active="2" data-booking-style="day_style">
    <div class="user-detail mb-5">

    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Select schedule:</label>
            </div>
            <div class="indicator schedule-indicator">
                <ul>
                    <li class="booked">Booked</li>
                    <li class="open">Open</li>
                    <li class="current">Current</li>
                </ul>
            </div>
            <div class="schedule-wrap">
                <div class="owl-carousel owl-theme bkx-available-days"> </div>
            </div>
        </div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-next">Next</button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous">Previous</button>
    </div>
</div>

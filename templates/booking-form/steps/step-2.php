<?php
/**
 * Booking Form Step 2
 */
defined( 'ABSPATH' ) || exit;?>
<div class="step-2 bkx-form-deactivate default" data-active="2" data-booking-style="default">
    <div class="user-detail">
    </div>
    <div class="row px-lg-5 py-3">
        <div class="col-lg-5 col-md-6">
            <div class="form-group">
                <label>Select date:</label>
            </div>
            <div id="bkx-calendar"></div>
        </div>
        <div class="col-lg-7 col-md-6">
            <div class="form-group">
                <label>Select time slot:</label>
            </div>
            <div class="indicator">
                <ul>
                    <li class="booked">Booked</li>
                    <li class="open">Open</li>
                    <li class="current">Current</li>
                </ul>
            </div>
            <div class="select-time">
                <table class="table table-bordered booking-slots" data-total_slots="0" data-starting_slot="0" data-date="">
                </table>
            </div>
        </div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-next">Next</button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous">Previous</button>
    </div>
</div>
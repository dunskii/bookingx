<?php
/**
 * Booking Form Step 2
 */
defined( 'ABSPATH' ) || exit;?>
<div class="step-2 bkx-form-deactivate" data-active="2">
    <div class="user-detail">
        <dl>
            <dt>Staff name :</dt>
            <dd>Resource 1</dd>
        </dl>
        <dl>
            <dt>Service name :</dt>
            <dd>Service QA 2018 - (1 Hour)</dd>
        </dl>
        <dl>
            <dt>Total Time :</dt>
            <dd>1 hour 15 minutes</dd>
        </dl>
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
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);" class="selected">0.00</a></td>
                        <td><a href="javascript:void(0);" class="selected">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);" class="selected">0.00</a></td>
                        <td><a href="javascript:void(0);" class="selected">0.00</a></td>
                        <td><a href="javascript:void(0);" class="selected">0.00</a></td>
                        <td><a href="javascript:void(0);" class="selected">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                        <td><a href="javascript:void(0);">0.00</a></td>
                    </tr>
                    <tr>
                        <td><a href="javascript:void(0);" class="disabled">0.00</a></td>
                        <td><a href="javascript:void(0);" class="disabled">0.00</a></td>
                        <td><a href="javascript:void(0);" class="disabled">0.00</a></td>
                        <td><a href="javascript:void(0);" class="disabled">0.00</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-next">Next</button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous">Previous</button>
    </div>
</div>
<?php
/**
 * Booking Form Step 3
 */
defined( 'ABSPATH' ) || exit;?>
<div class="step-3 bkx-form-deactivate" data-active="3">
    <div class="user-detail">
        <div class="row">
            <div class="col-sm-6">
                <dl>
                    <dt>Staff name :</dt>
                    <dd>Resource 1</dd>
                </dl>
                <dl>
                    <dt>Service name : </dt>
                    <dd>Service QA 2018 - (1 Hour)</dd>
                </dl>
                <dl>
                    <dt>Total Time :</dt>
                    <dd>1 hour 15 minutes</dd>
                </dl>
            </div>
            <div class="col-sm-6">
                <dl>
                    <dt>Date:</dt>
                    <dd>September 27, 2018</dd>
                </dl>
                <dl>
                    <dt>Time :</dt>
                    <dd>9:30 - 10:45</dd>
                </dl>
            </div>
        </div>

    </div>
    <div class="form-wrapper">
        <form id="bkx-form-step-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="bkx_first_name" class="form-control" placeholder="First name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="mbl-blank">&nbsp;</label>
                        <input type="text" name="bkx_last_name" class="form-control" placeholder="Last name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact details:</label>
                        <input type="text" name="bkx_phone_number" class="form-control" placeholder="Phone">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="mbl-blank">&nbsp;</label>
                        <input type="email" name="bkx_email_address" class="form-control" placeholder="Email">
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="bkx-terms-and-conditions" name="bkx_terms_and_conditions">
                        <label class="custom-control-label" for="bkx-terms-and-conditions">Do you agree with our <a href="javascript:void(0);">terms and conditions</a>?</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="bkx-privacy-policy" name="bkx_privacy_policy">
                        <label class="custom-control-label" for="bkx-privacy-policy">Do you agree with our <a href="javascript:void(0);">privacy policy</a>?</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="bkx-cancellation-policy" name="bkx_cancellation_policy">
                        <label class="custom-control-label" for="bkx-cancellation-policy">Do you agree with our <a href="javascript:void(0);">cancellation policy</a>?</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-next">Next</button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous">Previous</button>
    </div>
</div>

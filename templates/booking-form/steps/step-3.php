<?php
/**
 * Booking Form Step 3
 */
defined( 'ABSPATH' ) || exit;?>
<?php
$privacy_policy = bkx_crud_option_multisite('bkx_privacy_policy_page');
$cancellation = bkx_crud_option_multisite('bkx_cancellation_page');
$term_cond = bkx_crud_option_multisite('bkx_term_cond_page');
$privacy_policy_url = (isset($privacy_policy)) && $privacy_policy != "" ? get_permalink($privacy_policy) : "javascript:void(0);";
$term_cond_url = (isset($term_cond)) && $term_cond != "" ? get_permalink($term_cond) : "javascript:void(0);";
$cancellation_url = (isset($cancellation)) && $cancellation != "" ? get_permalink($cancellation) : "javascript:void(0);";
?>
<div class="step-3 bkx-form-deactivate" data-active="3">
    <div class="user-detail">
        <div class="row">
        </div>
    </div>
    <div class="form-wrapper">
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
                        <label class="custom-control-label" for="bkx-terms-and-conditions">Do you agree with our <a target="_blank" href="<?php echo $term_cond_url;?>">terms and conditions</a>?</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="bkx-privacy-policy" name="bkx_privacy_policy">
                        <label class="custom-control-label" for="bkx-privacy-policy">Do you agree with our <a target="_blank" href="<?php echo $privacy_policy_url;?>">privacy policy</a>?</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="bkx-cancellation-policy" name="bkx_cancellation_policy">
                        <label class="custom-control-label" for="bkx-cancellation-policy">Do you agree with our <a target="_blank" href="<?php echo $cancellation_url;?>">cancellation policy</a>?</label>
                    </div>
                </div>
            </div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-next">Next</button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous">Previous</button>
    </div>
</div>

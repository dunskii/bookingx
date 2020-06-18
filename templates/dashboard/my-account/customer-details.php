<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
do_action('bkx_before_customer_details_form');
$user_id = get_current_user_id();
$user = get_user_by('ID', $user_id);
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$user_email = isset($user->data->user_email) ? $user->data->user_email : '';
$phone_number = get_user_meta($user_id, 'bkx_phone_number', true);
?>
    <div class="col-md-12 bkx-customer-details-main">
        <span class="anchor" id="formChangePassword"></span>
        <!-- form card customer details -->
        <div class="card card-outline-secondary">
            <div class="card-header">
                <h2 class="mb-0"><?php esc_html_e('Customer Details', 'bookingx'); ?></h2>
            </div>
            <div class="card-body">
                <div class="alert alert-success bkx-customer-details-success-message" style="display: none;"
                     role="alert"></div>
                <div class="alert alert-danger bkx-customer-details-error-message" style="display: none;"
                     role="alert"></div>
                <form class="form bkx-customer-details-form" method="post" id="bkx-customer-details" role="form"
                      autocomplete="off">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label for="inputFirstName"><?php esc_html_e('First name', 'bookingx'); ?></label>
                                <input type="text" name="first_name"
                                       value="<?php esc_html_e($first_name, 'bookingx'); ?>" id="input-first_name"
                                       class="form-control bkx-first_name" placeholder="First name" required="">
                                <div class="invalid-feedback first_name-feedback"><?php esc_html_e('Please enter first name.', 'bookingx'); ?></div>
                            </div>
                            <div class="col">
                                <label for="inputLastName"><?php esc_html_e('Last name', 'bookingx'); ?></label>
                                <input type="text" name="last_name" value="<?php esc_html_e($last_name, 'bookingx'); ?>"
                                       id="last_name" class="form-control bkx-last_name" placeholder="Last name"
                                       required="">
                                <div class="invalid-feedback last_name-feedback"><?php esc_html_e('Please enter last name.', 'bookingx'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label for="inputEmail"><?php esc_html_e('Email Address', 'bookingx'); ?></label>
                                <input type="email" name="email_address"
                                       value="<?php esc_html_e($user_email, 'bookingx'); ?>" placeholder="Email Address"
                                       class="form-control bkx-email_address" id="input-email_address" required="">
                                <div class="invalid-feedback email_address-feedback"><?php esc_html_e('Please enter email address.', 'bookingx'); ?></div>
                            </div>
                            <div class="col">
                                <label for="inputPhone"><?php esc_html_e('Phone', 'bookingx'); ?></label>
                                <input type="tel" name="phone_number"
                                       value="<?php esc_html_e($phone_number, 'bookingx'); ?>"
                                       placeholder="Phone Number" class="form-control bkx-phone_number"
                                       id="input-phone_number">
                                <div class="invalid-feedback phone_number-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <?php do_action('bkx_customer_details_form'); ?>
                    <?php wp_nonce_field('bkx-customer-details', 'bkx-customer-details-nonce'); ?>
                    <div class="form-group">
                        <button type="submit" name="customer_details"
                                class="btn btn-success btn-lg float-right bkx-customer-details-btn"><?php esc_html_e('Save', 'bookingx'); ?></button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /form card customer details -->
    </div>
<?php
do_action('bkx_after_customer_details_form');
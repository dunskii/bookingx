<?php
/**
 * Booking Form Step 4
 */
defined('ABSPATH') || exit; ?>
<div class="step-4 bkx-form-deactivate" data-active="4">
    <div class="check-text"><label><?php echo esc_html('Please check your booking before proceeding','bookingx');?>:</label></div>
    <div class="user-detail bkx-booking-summary bg-gray px-3 py-4">
        <div class="row">
        </div>
        <div class="bkx-additional-detail"></div>
    </div>
	<?php do_action('bkx_booking_form_before_payment_method'); ?>
    <div class="bkx-payment-method user-detail bg-white px-3 py-4"></div>
	<?php do_action('bkx_booking_form_after_payment_method'); ?>
    <div class="user-detail p-0">
        <div class="row">
            <div class="col-lg-7">
                <div class="px-3 py-2 bkx-booking-total">
                    <dl>
                        <dt> <?php echo esc_html('Sub Total','bookingx');?></label> :</dt>
                        <dd class="total">$ 45.00</dd>
                    </dl>
                    <dl class="tax-section" style="display: none;">
                        <dt class="tax-name"> Tax <span>(10% GST )</span> :</dt>
                        <dd class="total-tax">$ 5.00</dd>
                    </dl>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-blue grand-total"><?php esc_html_e("Grand total", 'bookingx'); ?>: <strong></strong>
                </div>
            </div>
        </div>
        <div class="bkx-deposite-note user-detail bg-white px-3 py-4"></div>
        <div class="bkx-seat-location-note user-detail px-3 py-4"></div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-final"><?php esc_html_e("Process Booking", 'bookingx'); ?></button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous"><?php esc_html_e("Previous", 'bookingx'); ?></button>
    </div>
</div>
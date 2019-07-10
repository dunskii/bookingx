<?php
/**
 * Booking Form Step 4
 */
defined( 'ABSPATH' ) || exit; ?>
<div class="step-4 bkx-form-deactivate" data-active="4">
    <div class="check-text">Please check your booking before proceeding:</div>
    <div class="user-detail bkx-booking-summary bg-gray px-3 py-4">
        <div class="row">
        </div>
    </div>
    <div class="bkx-payment-method user-detail bg-white px-3 py-4"></div>
    <div class="user-detail p-0">
        <div class="row">
            <div class="col-lg-7">
                <div class="px-3 py-2 bkx-booking-total">
                    <dl>
                        <dt> Sub Total : </dt>
                        <dd class="total">$ 45.00</dd>
                    </dl>
                    <dl class="tax-section" style="display: none;">
                        <dt class="tax-name"> Tax <span>(10% GST )</span> : </dt>
                        <dd class="total-tax">$ 5.00</dd>
                    </dl>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-blue grand-total">Grand total: <strong></strong>
                </div>
            </div>
        </div>
        <div class="bkx-deposite-note user-detail bg-white px-3 py-4"></div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-final">Proceed with <img class="payment-gateway-image" src="" alt=""  class="img-fluid"></button>
        <button type="submit" class="btn btn-default bkx-form-submission-previous">Previous</button>
    </div>
    <form id="bkx-book-now" method="post"></form>
</div>
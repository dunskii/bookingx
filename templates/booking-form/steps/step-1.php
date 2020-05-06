<?php
/**
 * Booking Form Step 1
 */
defined( 'ABSPATH' ) || exit;
$BkxBookingFormShortCode = new BkxBookingFormShortCode();
$load_global = $BkxBookingFormShortCode->load_global_variables();
?>
<div class="step-1 bkx-form-active" data-active="1">
    <div class="form-wrapper">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group bkx-staff">
                        <label for="staff"><?php  printf( __( 'Select %s', 'bookingx' ), esc_html( $load_global->seat ) );?>
                            <abbr class="required" title="required">*</abbr></label>
                        <select name="staff-lists" class="custom-select bkx-staff-lists">
                            <option><?php printf(__('Select a %s', 'bookingx'), esc_html($load_global->seat)); ?></option>
                            <?php
                                $SeatObj = new BkxSeat();
                                $seat_lists = $SeatObj->get_seat_lists();
                                if(!empty($seat_lists)) {
                                    foreach ($seat_lists as $seat_id => $seat_name) { ?>
                                        <option value="<?php echo esc_attr( $seat_id ); ?>"><?php echo esc_html( $seat_name ); ?></option>
                                    <?php
                                    }
                                }?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group bkx-services">
                        <label for="email"><?php esc_html_e( __( "Select $load_global->base", 'bookingx' ) );?> <abbr class="required" title="required">*</abbr></label>
                        <select name="service-lists" class="custom-select bkx-services-lists" disabled>
                        </select>
                    </div>
                    <span class="bkx-extra-lists"></span>
                </div>
                <div class="col-md-6">
                    <div class="form-group service-extended" style="display: none;">
                        <label for="email"><?php esc_html_e( __( bkx_crud_option_multisite('bkx_notice_time_extended_text_alias'), 'bookingx' ) );?> <abbr class="required" title="required">*</abbr></label>
                        <select name="service-extend" class="custom-select bkx-services-extend" disabled>
                        </select>
                    </div>

                </div>
            </div>
            <div class="bkx-booking-total total-text">
                Total: <span></span>
            </div>
            <!--user-detail bg-white px-3 py-4-->
            <div class="bkx-disable-days-note"></div>
    </div>
    <div class="button-wrapper">
        <button type="submit" class="btn btn-default bkx-form-submission-next" >Next</button>
    </div>
</div>
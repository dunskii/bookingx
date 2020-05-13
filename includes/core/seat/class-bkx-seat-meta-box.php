<?php !defined( 'ABSPATH' ) AND exit;
if ( ! class_exists( 'BkxSeatMetaBox' ) ) {
    add_action('load-post.php', array('BkxSeatMetaBox', 'init'));
    add_action('load-post-new.php', array('BkxSeatMetaBox', 'init'));
    add_action('load-edit.php', array('BkxSeatMetaBox', 'init'));
    class BkxSeatMetaBox
    {
        protected static $instance;

        protected $post_type = "bkx_seat";

        /**
         * @return BkxSeatMetaBox
         * @var BkxSeatMetaBox
         */

        public static function init(){
            null === self::$instance AND self::$instance = new self;
            return self::$instance;
        }

        public function __construct(  ) {
            if(is_admin() == false )
                return;

            add_action( 'add_meta_boxes', array( $this, 'add_bkx_seat_metaboxes' ), 10, 2);
            add_action( 'save_post', array( $this, 'save_bkx_seat_metaboxes' ) ,10 , 3  );
            add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, $this->post_type . '_columns_head' ) , 10, 1);
            add_filter( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, $this->post_type . '_columns_content' ) , 10, 2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'bkx_seat_wp_enqueue_scripts' ) );
        }

        public function bkx_seat_wp_enqueue_scripts(){
            global $post;

            if( !empty($post) && $post->post_type != 'bkx_seat')
                return;

            $enable_cancel_booking = bkx_crud_option_multisite('enable_cancel_booking');
            $enable_cancel_booking = isset($enable_cancel_booking) ? $enable_cancel_booking : 0 ;
            $wp_localize_array = array('ajax_url' => admin_url( 'admin-ajax.php' ), 'is_cancel' => $enable_cancel_booking);

            wp_register_script ("bkx-settings", BKX_PLUGIN_DIR_URL . "public/js/admin/bkx-settings.js", false, BKX_PLUGIN_VER , true);
            wp_localize_script('bkx-settings', 'bkx_settings', $wp_localize_array);
            wp_enqueue_script( 'bkx-settings' );
            wp_enqueue_script("bkx-seat-validate", BKX_PLUGIN_DIR_URL."public/js/admin/bkx-seat-validate.js", array( 'jquery' ), BKX_PLUGIN_VER , true);
            wp_enqueue_script('jquery-ui-datepicker');
        }

        public function add_bkx_seat_metaboxes( $post_type, $post  ){
            $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
            add_meta_box('bkx_base_boxes', __("$alias_seat Details", 'bookingx'), array( $this, 'bkx_seat_render_meta_box_content'), 'bkx_seat', 'core', 'default');
        }

        public function bkx_seat_render_meta_box_content( $post ){
            if(empty($post))
                return;

            wp_nonce_field('bkx_seat_boxes_metabox', 'bkx_seat_boxes_metabox_nonce');
            $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
            $bkx_user_auto = 'N';
            $seat_is_certain_day = "Y";
            $country = bkx_get_wp_country();
            $values = get_post_custom($post->ID);

            $business_address_1     = bkx_crud_option_multisite("bkx_business_address_1");
            $business_address_2     = bkx_crud_option_multisite("bkx_business_address_2");
            $bkx_business_city      = bkx_crud_option_multisite("bkx_business_city");
            $bkx_business_state     = bkx_crud_option_multisite("bkx_business_state");
            $bkx_business_zip       = bkx_crud_option_multisite("bkx_business_zip");
            $bkx_business_country   = bkx_crud_option_multisite("bkx_business_country");
            $seat_street            = isset($business_address_1) ? esc_attr("{$business_address_1},{$business_address_2}") : "";
            $seat_city              = isset($bkx_business_city) ? esc_attr($bkx_business_city) : "";
            $seat_state             = isset($bkx_business_state) ? esc_attr($bkx_business_state) : "";
            $seat_zip               = isset($bkx_business_zip) ? esc_attr($bkx_business_zip) : "";
            $seat_country           = isset($bkx_business_country) ? esc_attr($bkx_business_country) : "";
            $seat_street            = isset($values['seat_street']) ? esc_attr($values['seat_street'][0]) : $seat_street;
            $seat_city              = isset($values['seat_city']) ? esc_attr($values['seat_city'][0]) : $seat_city;
            $seat_state             = isset($values['seat_state']) ? esc_attr($values['seat_state'][0]) : $seat_state;
            $seat_zip               = isset($values['seat_zip']) ? esc_attr($values['seat_zip'][0]) : $seat_zip;
            $seat_is_certain_month  = isset($values['seat_is_certain_month']) ? esc_attr($values['seat_is_certain_month'][0]) : "";
            if (!empty($values)) {

                $seat_days_time = !empty($values['seat_days_time'][0]) ? maybe_unserialize($values['seat_days_time'][0]) : "";
                $res_seat_time  = maybe_unserialize($seat_days_time);
                $seat_days_arr  = !empty($values['seat_days'][0]) ? $values['seat_days'][0] : "";
                $seat_days      = isset($values['seat_days'][0]) && $values['seat_days'][0] != '' ? $temp_seat_days = explode(',', $seat_days_arr) : '';
                $seat_months    = isset($values['seat_months'][0]) && $values['seat_months'][0] != '' ? $temp_seat_months = explode(',', $values['seat_months'][0]) : '';
            }
            $associate_with_user = isset($values['associate_with_user']) ? esc_attr($values['associate_with_user'][0]) : "N";
            $associate_with_user_role = isset($values['associate_with_user_role']) ? esc_attr($values['associate_with_user_role'][0]) : "";
            $associate_with_username = isset($values['associate_with_username']) ? esc_attr($values['associate_with_username'][0]) : "";
            if (!empty($res_seat_time)) {
                $res_seat_time_arr = array();
                foreach ($res_seat_time as $temp) {
                    $res_seat_time_arr[strtolower($temp['day'])]['time_from'] = $temp['time_from'];
                    $res_seat_time_arr[strtolower($temp['day'])]['time_till'] = $temp['time_till'];
                }
            }
            $seat_is_pre_payment                = isset($values['seatIsPrePayment']) ? esc_attr($values['seatIsPrePayment'][0]) : "";
            $seat_payment_type                  = isset($values['seat_payment_type']) ? esc_attr($values['seat_payment_type'][0]) : ""; //Payment Type
            $seat_deposite_type                 = isset($values['seat_payment_option']) ? esc_attr($values['seat_payment_option'][0]) : ""; // Deposite Type
            $seat_fixed_amount                  = isset($values['seat_amount']) ? esc_attr($values['seat_amount'][0]) : "";
            $seat_percentage                    = isset($values['seat_percentage']) ? esc_attr($values['seat_percentage'][0]) : "";
            $seat_phone                         = isset($values['seatPhone']) ? esc_attr($values['seatPhone'][0]) : "";
            $seat_notification_email            = isset($values['seatEmail']) ? esc_attr($values['seatEmail'][0]) : "";
            $seat_notification_alternate_email  = isset($values['seatIsAlternateEmail']) ? esc_attr($values['seatIsAlternateEmail'][0]) : "";
            $seatAlternateEmail                 = isset($values['seatAlternateEmail']) ? esc_attr($values['seatAlternateEmail'][0]) : "";
            $seat_ical_address                  = isset($values['seatIsIcal']) ? esc_attr($values['seatIsIcal'][0]) : "";
            $seatIcalAddress                    = isset($values['seatIcalAddress']) ? esc_attr($values['seatIcalAddress'][0]) : "";
            $bkx_user_auto                      = isset($values['bkx_user_auto']) ? esc_attr($values['bkx_user_auto'][0]) : "N";
            $seat_is_different_loc              = isset($values['seat_is_different_loc']) ? esc_attr($values['seat_is_different_loc'][0]) : "N";
            $seat_is_certain_day                = isset($values['seat_is_certain_day']) ? esc_attr($values['seat_is_certain_day'][0]) : "N";
            $selected_days                      = get_post_meta($post->ID, 'selected_days', true);
            $seat_colour                        = get_post_meta($post->ID, 'seat_colour', true);
            if (empty($selected_days)) {
                $bkx_business_days = bkx_crud_option_multisite("bkx_business_days");
            } else {
                $bkx_business_days = $selected_days;
            }
            $selected = !empty($bkx_business_days) ? sizeof($bkx_business_days) : 2;
            $add_more_status = ($selected >= 7) ? 'display:none' : 'display:block';
            $display_location = ($seat_is_different_loc == "Y") ? 'display: block;' : 'display: none;';
            ?>
            <style type="text/css">
                .setting-bookingx .standard {
                    width: 215px !important;
                }

                .setting-bookingx .close {
                    width: 10px !important;
                }
            </style>
            <input type="hidden" id="current_value" value="<?php echo $selected; ?>">
            <input type="hidden" class="remove_biz_days" name="remove_biz_days" value="" id="remove_biz_days">
            <div class="error" id="error_list" style="display:none;"></div>
            <p>
                <label for="seat_is_different_loc"><strong><?php printf(esc_html__('Is %1$s at a different location ? ', 'bookingx'), $alias_seat); ?>
                        :</strong></label></p>
            <p><input type="radio" name="seat_is_different_loc" value="Y" <?php if ($seat_is_different_loc == "Y") {
                    echo "checked='checked'";
                } ?> />Yes
                <input type="radio" name="seat_is_different_loc" value="N" <?php if ($seat_is_different_loc == "N") {
                    echo "checked='checked'";
                } ?>  <?php if (!isset($seat_is_different_loc)) {
                    echo "checked='checked'";
                } ?>/>No</p>

            <div class="seat_is_different_loc" style="<?php echo $display_location; ?>">
                <p><label for="seat_street"><?php esc_html_e('Street name', 'bookingx'); ?> : </label>
                    <textarea name="seat_street" id="seat_street"><?php echo esc_attr($seat_street); ?></textarea>
                </p>

                <p><label for="seat_city"><?php esc_html_e('City', 'bookingx'); ?> :</label>
                    <input type="text" name="seat_city" id="seat_city" value="<?php echo esc_attr($seat_city); ?>"/>
                </p>

                <p><label for="seat_state"><?php esc_html_e('State', 'bookingx'); ?> :</label>
                    <input type="text" name="seat_state" id="seat_state" value="<?php echo esc_attr($seat_state); ?>"/>
                </p>

                <p><label for="seat_zip"><?php esc_html_e('Zip / Postal Code', 'bookingx'); ?> :</label>
                    <input type="text" name="seat_zip" id="seat_zip" value="<?php echo esc_attr($seat_zip); ?>"/>
                </p>

                <p><label for="seat_country"><?php esc_html_e('Country', 'bookingx'); ?> :</label>
                    <select name="seat_country">
                        <?php if (!empty($country)) {
                            foreach ($country as $code => $country_name) { ?>
                                <option value="<?php echo $code; ?>" <?php if ($code == esc_attr($seat_country)) {
                                    echo "selected";
                                } ?>><?php echo $country_name; ?></option>
                                <?php
                            }
                        } ?>
                    </select>
                </p>
            </div>

            <strong> <?php esc_html_e('Set Times, Days and Months Available', 'bookingx'); ?></strong>
            <p>
                <label for="seat_country"><?php esc_html_e('Will this Seat only be available certain months of the year', 'bookingx'); ?>
                    :</label></p>
            <p><input type="radio" name="seat_is_certain_month" value="Y" <?php if ($seat_is_certain_month == "Y") {
                    echo "checked='checked'";
                } ?> />Yes
                <input type="radio" name="seat_is_certain_month" value="N" <?php if ($seat_is_certain_month == "N") {
                    echo "checked='checked'";
                } ?>  <?php if ($seat_is_certain_month == "") {
                    echo "checked='checked'";
                } ?>/>No</p>
            <p><?php esc_html_e('(Note: selection of \'No\' indicates that it will be available throughout the year.)', 'bookingx'); ?></p>

            <div id="certain_month">
                <label><?php esc_html_e('If yes, Select months of the year', 'bookingx'); ?> :</label>

                <ul class="gfield_checkbox" id="input_1_16">
                    <li class="bkx_choice_16_0">
                        <input name="seat_certain_month_all" type="checkbox" value="January" id="choice_month_all"
                               tabindex="31">
                        <label for="choice_16_0"><?php esc_html_e('All', 'bookingx'); ?></label>
                    </li>

                    <li class="bkx_choice_16_1">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="January"
                               id="choice_16_1" tabindex="31" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("January", $temp_seat_months)) {
                                echo "checked='checked'";
                            }
                        } ?> >
                        <label for="choice_16_1"><?php esc_html_e('January', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_2">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="February"
                               id="choice_16_2" tabindex="32" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("February", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?> >
                        <label for="choice_16_2"><?php esc_html_e('February', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_3">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="March" id="choice_16_3"
                               tabindex="33" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("March", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_3"><?php esc_html_e('March', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_4">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="April" id="choice_16_4"
                               tabindex="34" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("April", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_4"><?php esc_html_e('April', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_5">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="May" id="choice_16_5"
                               tabindex="35" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("May", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_5"><?php esc_html_e('May', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_6">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="June" id="choice_16_6"
                               tabindex="36" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("June", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_6"><?php esc_html_e('June', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_7">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="July" id="choice_16_7"
                               tabindex="37" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("July", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_7"><?php esc_html_e('July', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_8">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="August"
                               id="choice_16_8" tabindex="38" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("August", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_8"><?php esc_html_e('August', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_9">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="September"
                               id="choice_16_9" tabindex="39" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("September", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_9"><?php esc_html_e('September', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_11">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="October"
                               id="choice_16_11"
                               tabindex="40" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("October", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_11"><?php esc_html_e('October', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_12">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="November"
                               id="choice_16_12"
                               tabindex="41" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("November", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_12"><?php esc_html_e('November', 'bookingx'); ?></label>
                    </li>
                    <li class="bkx_choice_16_13">
                        <input name="seat_certain_month[]" class="month_checkbox" type="checkbox" value="December"
                               id="choice_16_13"
                               tabindex="42" <?php if (isset($temp_seat_months) && ($temp_seat_months != '')) {
                            if (in_array("December", $temp_seat_months)) {
                                echo
                                "checked='checked'";
                            }
                        } ?>>
                        <label for="choice_16_13"><?php esc_html_e('December', 'bookingx'); ?></label>
                    </li>
                </ul>
            </div>
            <p><?php printf(esc_html__('Will %1$s be available at certain days only ', 'bookingx'), $alias_seat); ?></p>
            <div class="plugin-description">
                <input type="radio" name="seat_is_certain_day" value="Y" <?php if ($seat_is_certain_day == "Y") {
                    echo "checked='checked'";
                } ?> />Yes
                <input type="radio" name="seat_is_certain_day" value="N" <?php if ($seat_is_certain_day == "N") {
                    echo "checked='checked'";
                } ?> <?php if ($seat_is_certain_day == "") {
                    echo "checked='checked'";
                } ?> />No
                <p><?php esc_html_e('(Note: selection of \'No\' indicates that it will be available 7 days a week, and 24 hours a day)', 'bookingx'); ?></p>
                <div id="certain_day">
                    <ul class="setting-bookingx">
                        <?php echo bkx_generate_days_section(7, $bkx_business_days); ?>
                        <li class="standard" id="add_more_days" style="<?php echo $add_more_status; ?>;">
                            <a href="javascript:add_more_days()" class='button-primary'> Add another set of hours</a>
                        </li>
                    </ul>
                    <div class="spacer"><p>&nbsp;</p></div>
                </div>
            </div>
            <?php if ($seat_is_certain_day == "Y") : ?>
            <div class="spacer"><p>&nbsp;</p>
                <p>&nbsp;</p></div>
        <?php endif; ?>
            <p><strong><?php esc_html_e('Payment Options', 'bookingx'); ?></strong></p>
            <p><?php printf(esc_html__('Will the %1$s booking require pre payment : ', 'bookingx'), $alias_seat); ?></p>
            <div class="plugin-description">
                <input type="radio" name="seat_is_pre_payment"
                       value="Y" <?php if (isset($seat_is_pre_payment) && ($seat_is_pre_payment != '')) {
                    if ($seat_is_pre_payment == 'Y') {
                        echo "checked='checked'";
                    }
                } ?> />Yes
                <input type="radio" name="seat_is_pre_payment"
                       value="N" <?php if (isset($seat_is_pre_payment) && ($seat_is_pre_payment != '')) {
                    if ($seat_is_pre_payment == 'N') {
                        echo "checked='checked'";
                    }
                } ?>  <?php if ($seat_is_pre_payment == "") {
                    echo "checked='checked'";
                } ?>/>No
            </div>
            <div id="deposit_fullpayment">
                <p><?php esc_html_e('Will payment be deposit or full payment', 'bookingx'); ?></p>
                <div class="plugin-description">
                    <select name="seat_deposit_full" id="id_seat_deposit_full" onchange="hide_fixed_percentage(this.value);"
                            class="medium gfield_select" tabindex="76">
                        <option value="Full Payment"><?php esc_html_e('Select', 'bookingx'); ?></option>
                        <option value="Deposit" <?php if (isset($seat_payment_type) && ($seat_payment_type != '')) {
                            if ($seat_payment_type == 'D') {
                                echo "selected='selected'";
                            }
                        } ?>><?php esc_html_e('Deposit', 'bookingx'); ?></option>
                        <option value="Full Payment" <?php if (isset($seat_payment_type) && ($seat_payment_type != '')) {
                            if ($seat_payment_type == 'FP') {
                                echo "selected='selected'";
                            }
                        } ?>><?php esc_html_e('Full Payment', 'bookingx'); ?></option>
                    </select>
                </div>
            </div>
            <div class="active" id="fixed_percentage">
                <p><?php esc_html_e('Is deposit a fixed amount ($) or percentage (%)? :', 'bookingx'); ?></p>
                <div class="plugin-description">
                    <select name="seat_payment_option" id="id_seat_payment_type" onchange="change_amount_option(this.value);">
                        <option value="FixedAmount" <?php if (isset($seat_deposite_type) && ($seat_deposite_type != '')) {
                            if ($seat_deposite_type == 'FA') {
                                echo "selected='selected'";
                            }
                        } ?> ><?php esc_html_e('Fixed Amount ($)', 'bookingx'); ?></option>
                        <option value="Percentage" <?php if (isset($seat_deposite_type) && ($seat_deposite_type != '')) {
                            if ($seat_deposite_type == 'P') {
                                echo "selected='selected'";
                            }
                        } ?> ><?php esc_html_e('Percentage (%)', 'bookingx'); ?></option>
                    </select>
                </div>
            </div>
            <div id="fixedamount">
                <p><?php esc_html_e('Enter Amount ($):', 'bookingx'); ?></p>
                <div class="plugin-description">
                    <input type="text" name="seat_amount"
                           value="<?php if (isset($seat_fixed_amount) && ($seat_fixed_amount != '')) {
                               echo $seat_fixed_amount;
                           } ?>">
                </div>
            </div>
            <div id="percentage">
                <p><?php esc_html_e('Enter Percentage (%):', 'bookingx'); ?></p>
                <div class="plugin-description">
                    <input type="text" name="seat_percentage"
                           value="<?php if (isset($seat_percentage) && ($seat_percentage != '')) {
                               echo $seat_percentage;
                           } ?>">
                </div>
            </div>
            <p><strong><?php esc_html_e('User Options', 'bookingx'); ?></strong></p>
            <?php if (!empty($associate_with_username)) {
                    $associate_with_username_obj = get_user_by('id', $associate_with_username);
                    if (!empty($associate_with_username_obj) && !is_wp_error($associate_with_username_obj)) {
                        echo 'This profile link to ' . $associate_with_username_obj->data->display_name . '<a href="' . get_edit_user_link($associate_with_username) . '" > view profile</a>';
                        $mannual = 'display:none';
                    }
                    $associate_with_email = get_user_by('email', $associate_with_username);
                    if (!empty($associate_with_email) && !is_wp_error($associate_with_email)) {
                        echo 'This profile link to ' . $associate_with_email->data->display_name . ' <a href="' . get_edit_user_link($associate_with_email->data->ID) . '" > view profile</a>';
                        $mannual = 'display:none';
                    }
                }
            if ($bkx_user_auto == 'Y') {
                $auto_user = get_user_by('email', $seat_notification_email);
                if (!empty($auto_user) && !is_wp_error($auto_user)) {
                    $associate_with_username = $values['seatEmail'];
                    echo 'This profile link to ' . $auto_user->data->display_name . ' <a href="' . get_edit_user_link($auto_user->data->ID) . '"> view profile</a>';
                    $mannual = 'display:none';
                }
            }
            $crete_user_auto_status = ($associate_with_user == 'Y') ? 'display:block;' : 'display:none;';
            $user_type_showhide     = ($bkx_user_auto == 'N') ? 'display:block;' : 'display:none;';
            ?>
            <div class="bkx_user_mannual" style="<?php echo $mannual; ?>">
                <p><?php printf(esc_html__('Do you want to associate this %1$s with a user ? : ', 'bookingx'), $alias_seat); ?></p>
                <select name="associate_with_user" id="selOpt">
                    <option value="N" <?php if (isset($associate_with_user) && ($associate_with_user == 'N')) {
                        echo "selected='selected'";
                    } ?>><?php esc_html_e('NO', 'bookingx'); ?></option>
                    <option value="Y" <?php if (isset($associate_with_user) && ($associate_with_user == 'Y')) {
                        echo "selected='selected'";
                    } ?>><?php esc_html_e('YES', 'bookingx'); ?></option>
                </select>
                <div class="crete_user_auto" id="crete_user_auto" style="<?php echo $crete_user_auto_status; ?>">
                    <p><?php printf(esc_html__('Create User automatically : ', 'bookingx')); ?></p>
                    <p><input type="radio" name="bkx_user_auto" id="id_bkx_user_auto_y"
                              value="Y" <?php if ($bkx_user_auto == "Y") {
                            echo "checked='checked'";
                        } ?> <?php if (!isset($bkx_user_auto)) {
                            echo "checked='checked'";
                        } ?>>Yes
                        <input type="radio" name="bkx_user_auto" id="id_bkx_user_auto_n"
                               value="N" <?php if ($bkx_user_auto == "N") {
                            echo "checked='checked'";
                        } ?> <?php if ($bkx_user_auto == "") {
                            echo "checked='checked'";
                        }?> />No
                    </p>
                </div>

                <div class="active" id="selRoles" style="<?php echo $crete_user_auto_status; ?>">
                    <?php esc_html_e('Please select user type :', 'bookingx'); ?>
                    <div class="plugin-description">
                        <select id="role" name="associate_with_user_role">
                            <option value=""> <?php esc_html_e('Select Role', 'bookingx'); ?></option>
                            <?php global $wp_roles;
                            $roles = $wp_roles->get_names(); // Below code will print the all list of roles.
                            unset($roles['administrator']);
                            foreach ($roles as $key => $role) {?>
                                <option value="<?php echo $key; ?>"
                                    <?php if (isset($associate_with_user_role) && ($associate_with_user_role == $key)) {
                                        echo "selected='selected'";
                                    } ?>><?php echo $role; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="active" id="selUsers" style="<?php if (isset($user_name) && ($user_name != '')) {
                    echo "display:table-row;";
                } else {
                    echo "display:none";
                } ?>"> <?php esc_html_e('Please select user :', 'bookingx'); ?>
                    <div class="plugin-description">
                        <select id="users" name="associate_with_username">
                            <option value="<?php echo $associate_with_username; ?>"><?php echo $associate_with_username; ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <p><strong><?php esc_html_e('Colour', 'bookingx'); ?></strong></p>
            <p><?php printf(esc_html__('%1$s Colour', 'bookingx'), $alias_seat); ?></p>
            <p><input type="text" name="seat_colour" id="id_seat_colour"
                      value="<?php if (isset($seat_colour) && ($seat_colour != '')) {
                          echo $seat_colour;
                      } ?>"/></p>
            <p><strong><?php esc_html_e('Notification Details', 'bookingx'); ?></strong></p>
            <p><?php esc_html_e('Phone :', 'bookingx'); ?></p>
            <p><input type="text" name="seat_phone" id="id_seat_phone"
                      value="<?php if (isset($seat_phone) && ($seat_phone != '')) {
                          echo $seat_phone;
                      } ?>"/></p>
            <p><?php esc_html_e('Email :', 'bookingx'); ?></p>
            <p><input type="text" name="seat_email" id="id_seat_email"
                      value="<?php if (isset($seat_notification_email) && ($seat_notification_email != '')) {
                          echo $seat_notification_email;
                      } ?>"/></p>
            <p><input name="seat_is_alternate_email" type="checkbox" onclick="display_secondary_email(this);" value="Yes"
                      id="id_seat_is_alternate_email"
                      tabindex="83" <?php if (isset($seat_notification_alternate_email) && ($seat_notification_alternate_email != '')) {
                    echo "checked='checked'";
                } ?> ><?php esc_html_e('Would you like to notify a second email address? :', 'bookingx'); ?></p>
            <div class="active" id="secondary_email" style="display:none;">
                <?php esc_html_e('Secondary Email :', 'bookingx'); ?>
                <div class="plugin-description">
                    <input type="text" name="seat_alternate_email" id="id_seat_alternate_email"
                           value="<?php if (isset($seat_notification_alternate_email) && ($seat_notification_alternate_email != '')) {
                               echo $seatAlternateEmail;
                           } ?>"/>
                </div>
            </div>
            <p><input name="seat_is_ical" type="checkbox" onclick="display_ical_address(this);" value="Yes" id="id_seat_is_ical"
                      tabindex="83" <?php if (isset($seat_ical_address) && ($seat_ical_address != '')) {
                    echo "checked='checked'";
                } ?> >
                <?php esc_html_e('Would you like to share your booking details with Google Calendar?', 'bookingx'); ?>
            </p>
            <div class="active" id="ical_address" style="display:none;">
                <?php esc_html_e('Google Calendar ID :', 'bookingx'); ?>
                <div class="plugin-description">
                    <input name="seat_ical_address" type="text"
                           value="<?php if (isset($seatIcalAddress) && ($seatIcalAddress != '')) {
                               echo $seatIcalAddress;
                           } ?>" id="id_seat_ical_address" size="60">
                </div>
            </div>
            <input type="hidden" name="is_user_valid" id="id_is_user_valid">
            <input type="hidden" name="seat_alias" id="seat_alias" value="<?php echo $alias_seat; ?>">
            <?php
        }

        public function save_bkx_seat_metaboxes($post_id, $post, $update = null ){
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            if ($post->post_type != 'bkx_seat') return;
            if ($post->post_status != 'publish') return;
            if (is_multisite()) {
                $current_blog_id = get_current_blog_id();
                switch_to_blog($current_blog_id);
            }

            if(isset($_POST['seat_certain_month'])){
                $seatCertainMonth = array_map('sanitize_text_field', wp_unslash($_POST['seat_certain_month']));
            }
            if(isset($_POST['seat_certain_day'])){
                $seatCertainDay = sanitize_text_field($_POST['seat_certain_day']);
            }
            if(isset($_POST['seat_is_pre_payment'])){
                $seatIsPrePayment = sanitize_text_field($_POST['seat_is_pre_payment']);
            }
            if(isset($_POST['seat_deposit_full'])){
                $seatDepositFull = sanitize_text_field($_POST['seat_deposit_full']);
            }
            if(isset($_POST['seat_payment_option'])){
                $seatPaymentOption = sanitize_text_field($_POST['seat_payment_option']);
            }
            if(isset($_POST['seat_amount'])){
                $seat_amount = sanitize_text_field($_POST['seat_amount']);
            }
            if(isset($_POST['seat_percentage'])){
                $seat_percentage = sanitize_text_field($_POST['seat_percentage']);
            }
            if(isset($_POST['seat_phone'])){
                $seatPhone = sanitize_text_field($_POST['seat_phone']);
            }
            if(isset($_POST['seat_email'])){
                $seatEmail = sanitize_text_field($_POST['seat_email']);
            }
            if(isset($_POST['seat_is_alternate_email'])){
                $seatIsAlternateEmail = sanitize_text_field($_POST['seat_is_alternate_email']);
            }
            if(isset($_POST['seat_alternate_email'])){
                $seatAlternateEmail = sanitize_text_field($_POST['seat_alternate_email']);
            }
            if(isset($_POST['seat_is_ical'])){
                $seatIsIcal = sanitize_text_field($_POST['seat_is_ical']);
            }
            if(isset($_POST['seat_ical_address'])){
                $seatIcalAddress = sanitize_text_field($_POST['seat_ical_address']);
            }
            if(isset($_POST['associate_with_user'])){
                $associate_with_user = sanitize_text_field($_POST['associate_with_user']);
            }
            if(isset($_POST['seat_colour'])){
                $seat_colour = sanitize_text_field($_POST['seat_colour']);
            }
            if(isset($_POST['associate_with_user_role'])){
                $associate_with_user_role = sanitize_text_field($_POST['associate_with_user_role']);
            }
            if(isset($_POST['associate_with_username'])){
                $associate_with_username = sanitize_text_field($_POST['associate_with_username']);
            }
            if(isset($_POST['bkx_user_auto'])){
                $bkx_user_auto = sanitize_text_field($_POST['bkx_user_auto']);
            }
            if(isset($_POST['seat_is_different_loc'])){
                $seat_is_different_loc = sanitize_text_field($_POST['seat_is_different_loc']);
            }
            if(isset($_POST['seat_certain_day'])){

            }

            if ($seatDepositFull == "Deposit") {
                $tempDepositFull = "D";
            } else if ($seatDepositFull == "Full Payment") {
                $tempDepositFull = "FP";
            }
            $seatDepositFull = $tempDepositFull;

            if ($seatPaymentOption == "FixedAmount") {
                $tempPaymentOption = "FA";
            } elseif ($seatPaymentOption == "Percentage") {
                $tempPaymentOption = "P";
            }
            $seatPaymentOption = $tempPaymentOption;
            $seatCertainMonthTemp = '';
            if (sizeof($seatCertainMonth) > 0) {
                foreach ($seatCertainMonth as $key => $temp) {
                    $seatCertainMonthTemp .= $temp . ",";
                }
                $seatCertainMonth = $seatCertainMonthTemp;
            } else {
                $seatCertainMonth = "";
            }
            $remove_biz_days_arr = array();
            $remove_biz_days = isset($_POST['remove_biz_days']) ? sanitize_text_field($_POST['remove_biz_days']) : "";
            if (!empty($remove_biz_days)) {
                $remove_biz_days_arr = explode(",", rtrim($remove_biz_days, ","));
            }
            // Generate comma seperated value for weekdays
            $selected_days = array();
            $seat_days_time_data = array();
            $bkx_day = 1;
            for ($d = 1; $d <= 7; $d++) {
                if (isset($_POST['days_' . $d]) && !empty($_POST['days_' . $d]) && !in_array($d, $remove_biz_days_arr)) {
                    //$days_array = sanitize_text_field ();
                    $days_array = array_map('sanitize_text_field', wp_unslash($_POST['days_' . $d]));
                    if (!empty($days_array)) {
                        if (!empty($seatCertainDay)) {
                            $seatCertainDay = array_unique($seatCertainDay);
                        }
                        $selected_days['selected_days_' . $d]['days'] = $days_array;
                        foreach ($days_array as $days_week) {
                            $seatCertainDay[] = $days_week;
                            $seat_days_time_data[$d]['day'] = strtolower($days_week);
                        }
                    }
                }
                if (!in_array($d, $remove_biz_days_arr)) {
                    if (isset($_POST['opening_time_' . $d]) && !empty($_POST['opening_time_' . $d]) && !empty($_POST['days_' . $d])) {
                        $selected_days['selected_days_' . $d]['time']['open'] = sanitize_text_field($_POST['opening_time_' . $d]);
                        $seat_days_time_data['selected_days_' . $d]['time_from'] = sanitize_text_field($_POST['opening_time_' . $d]);
                    }
                    if (isset($_POST['closing_time_' . $d]) && !empty($_POST['closing_time_' . $d]) && !empty($_POST['days_' . $d])) {
                        $selected_days['selected_days_' . $d]['time']['close'] = sanitize_text_field($_POST['closing_time_' . $d]);
                        $seat_days_time_data['selected_days_' . $d]['time_till'] = sanitize_text_field($_POST['closing_time_' . $d]);
                    }
                }
            }
            $selected_days_final = array();
            if (!empty($selected_days)) {
                $clean_d = 1;
                foreach ($selected_days as $key => $days_obj) {
                    $selected_days_final['selected_days_' . $clean_d] = $days_obj;
                    $days_data = $days_obj['days'];
                    $time_data = $days_obj['time'];
                    foreach ($days_data as $key => $day) {
                        $days_ope[$day] = array('day' => $day, 'time_from' => $time_data['open'], 'time_till' => $time_data['close']);
                    }
                    $clean_d++;
                }
            }
            // Make sure your data is set before trying to save it
            if (!empty($selected_days)) {
                update_post_meta($post_id, 'selected_days', $selected_days_final);
            }
            if (!empty($seat_colour)) {
                update_post_meta($post_id, 'seat_colour', $seat_colour);
            }
            if (isset($associate_with_user))
                update_post_meta($post_id, 'associate_with_user', $associate_with_user);
            if (isset($associate_with_user_role))
                update_post_meta($post_id, 'associate_with_user_role', $associate_with_user_role);
            if (isset($associate_with_username))
                update_post_meta($post_id, 'associate_with_username', $associate_with_username);
            if (isset($_POST['seat_street']))
                update_post_meta($post_id, 'seat_street', esc_attr($_POST['seat_street']));
            if (isset($_POST['seat_city']))
                update_post_meta($post_id, 'seat_city', esc_attr($_POST['seat_city']));
            if (isset($_POST['seat_state']))
                update_post_meta($post_id, 'seat_state', esc_attr($_POST['seat_state']));
            if (isset($_POST['seat_zip']))
                update_post_meta($post_id, 'seat_zip', esc_attr($_POST['seat_zip']));
            if (isset($_POST['seat_country']))
                update_post_meta($post_id, 'seat_country', esc_attr($_POST['seat_country']));
            if (isset($_POST['seat_city']))
                update_post_meta($post_id, 'seat_city', esc_attr($_POST['seat_city']));
            if (isset($_POST['seat_is_certain_month']))
                update_post_meta($post_id, 'seat_is_certain_month', esc_attr($_POST['seat_is_certain_month']));
            if (isset($seatCertainMonth))
                update_post_meta($post_id, 'seat_months', esc_attr($seatCertainMonth));
            if (isset($_POST['seat_is_certain_day']))
                update_post_meta($post_id, 'seat_is_certain_day', esc_attr($_POST['seat_is_certain_day']));
            if (isset($seatCertainDay))
                update_post_meta($post_id, 'seat_days', $seatCertainDay);
            if (isset($seat_days_time_data))
                update_post_meta($post_id, 'seat_days_time', $days_ope);
            if (isset($seatIsPrePayment))
                update_post_meta($post_id, 'seatIsPrePayment', $seatIsPrePayment);
            if (isset($seatDepositFull))
                update_post_meta($post_id, 'seat_payment_type', $seatDepositFull);
            if (isset($seatPaymentOption))
                update_post_meta($post_id, 'seat_payment_option', $seatPaymentOption);
            if (isset($seat_amount))
                update_post_meta($post_id, 'seat_amount', $seat_amount);
            if (isset($seat_percentage))
                update_post_meta($post_id, 'seat_percentage', $seat_percentage);
            if (isset($seatPhone))
                update_post_meta($post_id, 'seatPhone', $seatPhone);
            if (isset($seatEmail))
                update_post_meta($post_id, 'seatEmail', $seatEmail);
            if (isset($seatIsAlternateEmail))
                update_post_meta($post_id, 'seatIsAlternateEmail', $seatIsAlternateEmail);
            if (isset($seatAlternateEmail))
                update_post_meta($post_id, 'seatAlternateEmail', $seatAlternateEmail);
            if (isset($seatIsIcal))
                update_post_meta($post_id, 'seatIsIcal', $seatIsIcal);
            if (isset($seatIcalAddress))
                update_post_meta($post_id, 'seatIcalAddress', $seatIcalAddress);
            if (isset($bkx_user_auto))
                update_post_meta($post_id, 'bkx_user_auto', $bkx_user_auto);
            if (isset($seat_is_different_loc))
                update_post_meta($post_id, 'seat_is_different_loc', $seat_is_different_loc);
            /**
             * Create New User and Role as Resource by default or Get by bkx_crud_option_multisite('bkx_seat_role');
             */
            $bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');
            if (isset($bkx_seat_role) && $bkx_seat_role != '') {
                $role = $bkx_seat_role;
            } else {
                $role = 'resource';
            }
            if ($bkx_user_auto == 'N') {
                if (isset($associate_with_username) && $associate_with_username != '') {
                    $user = get_user_by('id', $associate_with_username);
                    $bkx_user_id = $associate_user_id = $user->ID;

                    if (isset($associate_user_id) && $associate_user_id != '') {
                        update_post_meta($post_id, 'seat_wp_user_id', $associate_user_id);
                    }
                }
            }
            if ($bkx_user_auto == 'Y') {
                $seat_wp_user_id = get_post_meta($post_id, 'seat_wp_user_id', true);
                $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                if (is_multisite()) {
                    $SeatWpuserObj = get_blogs_of_user($seat_wp_user_id);
                    if (empty($SeatWpuserObj)) {
                        $bkx_user_id = wpmu_create_user($seatEmail, $random_password, $seatEmail);
                    } else {
                        $bkx_user_id = $seat_wp_user_id;
                    }
                } else {
                    $SeatWpuserObj = WP_User::get_data_by('ID', $seat_wp_user_id);
                    if ($SeatWpuserObj === false || empty($SeatWpuserObj)) {
                        //echo "seat_wp_user_id: ". $seat_wp_user_id ."<br>";
                        $check_user_id = username_exists($seatEmail);
                        if (!$check_user_id && email_exists($seatEmail) == false) {

                            $bkx_user_id = wp_create_user($seatEmail, $random_password, $seatEmail);
                            //echo "wp_create_user: ". $user_id ."<br>";
                            update_post_meta($post_id, 'seat_wp_user_id', $bkx_user_id);
                        } else {
                            $random_password = __('User already exists.  Password inherited.');
                        }
                    } else {
                        $bkx_user_id = $seat_wp_user_id;
                    }
                }
            }
            if (isset($bkx_user_id) && $bkx_user_id != '') {
                $userdata = array(
                    'ID' => $bkx_user_id,
                    'first_name' => sanitize_text_field($_POST['post_title']),
                    'role' => $role,
                    'display_name' => sanitize_text_field($_POST['post_title']),
                );
                $bkx_user_id = wp_update_user($userdata);
                update_user_meta($bkx_user_id, 'seat_post_id', $post_id);
                update_post_meta($post_id, 'seat_user_id', $bkx_user_id);
                if (isset($_POST['seat_street']))
                    update_user_meta($bkx_user_id, 'seat_street', esc_attr($_POST['seat_street']));
                if (isset($_POST['seat_city']))
                    update_user_meta($bkx_user_id, 'seat_city', esc_attr($_POST['seat_city']));
                if (isset($_POST['seat_state']))
                    update_user_meta($bkx_user_id, 'seat_state', esc_attr($_POST['seat_state']));
                if (isset($_POST['seat_zip']))
                    update_user_meta($bkx_user_id, 'seat_zip', esc_attr($_POST['seat_zip']));
                if (isset($_POST['seat_country']))
                    update_user_meta($bkx_user_id, 'seat_country', esc_attr($_POST['seat_country']));
                if (isset($_POST['seat_city']))
                    update_user_meta($bkx_user_id, 'seat_city', esc_attr($_POST['seat_city']));
                if (isset($_POST['seat_is_certain_month']))
                    update_user_meta($bkx_user_id, 'seat_is_certain_month', esc_attr($_POST['seat_is_certain_month']));
                if (isset($seatCertainMonth))
                    update_user_meta($bkx_user_id, 'seat_months', esc_attr($seatCertainMonth));
                if (isset($_POST['seat_is_certain_day']))
                    update_user_meta($bkx_user_id, 'seat_is_certain_day', esc_attr($_POST['seat_is_certain_day']));
                if (isset($seatCertainDay))
                    update_user_meta($bkx_user_id, 'seat_days', $seatCertainDay);
                if (isset($seat_days_time_data))
                    update_user_meta($bkx_user_id, 'seat_days_time', $seat_days_time_data);
                if (isset($seatIsPrePayment))
                    update_user_meta($bkx_user_id, 'seatIsPrePayment', $seatIsPrePayment);
                if (isset($seatDepositFull))
                    update_user_meta($bkx_user_id, 'seat_payment_type', $seatDepositFull);
                if (isset($seatPaymentOption))
                    update_user_meta($bkx_user_id, 'seat_payment_option', $seatPaymentOption);
                if (isset($seat_amount))
                    update_user_meta($bkx_user_id, 'seat_amount', $seat_amount);
                if (isset($seat_percentage))
                    update_user_meta($bkx_user_id, 'seat_percentage', $seat_percentage);
                if (isset($seatPhone))
                    update_user_meta($bkx_user_id, 'seatPhone', $seatPhone);
                if (isset($seatEmail))
                    update_user_meta($bkx_user_id, 'seatEmail', $seatEmail);
                if (isset($seatIsAlternateEmail))
                    update_user_meta($bkx_user_id, 'seatIsAlternateEmail', $seatIsAlternateEmail);
                if (isset($seatAlternateEmail))
                    update_user_meta($bkx_user_id, 'seatAlternateEmail', $seatAlternateEmail);
                if (isset($seatIsIcal))
                    update_user_meta($bkx_user_id, 'seatIsIcal', $seatIsIcal);
                if (isset($seatIcalAddress))
                    update_user_meta($bkx_user_id, 'seatIcalAddress', $seatIcalAddress);
            }
        }

        /**
         *
         * @param array $defaults
         * @return string
         */
        public function bkx_seat_columns_head( $defaults ) {
            $enable_any_seat = bkx_crud_option_multisite('enable_any_seat');
            if (isset($enable_any_seat) && $enable_any_seat == 1) :
                $defaults['any_seat'] = '<span class="any_seat tips" data-tip="' . esc_attr__('Set as any Seat', 'bookingx') . '">' . esc_attr__('Set as any seat', 'bookingx') . '</span>';
            endif;
            $defaults['display_shortcode_all'] = 'Shortcode [bookingx seat-id="all"]';
            return $defaults;
        }

        /**
         *
         * @param type $column_name
         * @param type $post_ID
         */
        public function bkx_seat_columns_content($column_name, $post_ID){
            $enable_any_seat = bkx_crud_option_multisite('enable_any_seat');
            if ($column_name == 'any_seat' && isset($enable_any_seat) && $enable_any_seat == 1) :
                $_enable_any_seat_id = 0;
                $_enable_any_seat_id = bkx_crud_option_multisite('select_default_seat');
                $url = wp_nonce_url(admin_url('admin-ajax.php?action=bookingx_set_as_any_seat&seat_id=' . $post_ID), 'bookingx-seat-as-any');
                echo '<a href="' . esc_url($url) . '" title="' . __('Set as any Seat', 'bookingx') . '">';
                if ($_enable_any_seat_id == $post_ID) {
                    echo '<span class="bks-any-seat tips" data-tip="' . esc_attr__('Yes', 'bookingx') . '">' . __('Yes', 'bookingx') . '</span>';
                } else {
                    echo '<span class="bks-any-seat not-any-seat tips" data-tip="' . esc_attr__('No', 'bookingx') . '">' . __('No', 'bookingx') . '</span>';
                }
                echo '</a>';
            endif;

            if ($column_name == 'display_shortcode_all') {
                echo '[bookingx seat-id="' . $post_ID . '" description="yes" image="yes" extra-info="no"]';
            }
        }
    }
}
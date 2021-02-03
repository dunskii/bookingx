<?php !defined('ABSPATH') and exit;
if (!class_exists('BkxBaseMetabox')) {
    add_action('load-post.php', array('BkxBaseMetabox', 'init'));
    add_action('load-post-new.php', array('BkxBaseMetabox', 'init'));
    add_action('load-edit.php', array('BkxBaseMetabox', 'init'));

    /**
     * Class BkxBaseMetabox
     * @property BkxBaseMetabox instance
     */
    class BkxBaseMetabox
    {
        protected static $instance;

        protected $post_type = "bkx_base";

        public static function init()
        {
            null === self:: $instance and self:: $instance = new self;
            return self:: $instance;
        }

        public function __construct()
        {
            if (is_admin() == false)
                return;
            add_action('add_meta_boxes', array($this, 'add_bkx_base_metaboxes'));
            add_action('save_post', array($this, 'save_bkx_base_metaboxes'), 10, 3);
            add_filter('manage_' . $this->post_type . '_posts_columns', array($this, $this->post_type . '_columns_head'), 10, 1);
            add_filter('manage_' . $this->post_type . '_posts_custom_column', array($this, $this->post_type . '_columns_content'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'bkx_base_wp_enqueue_scripts'));
        }

        /**
         * Enqueue Scripts for Service Meta Box
         */
        public function bkx_base_wp_enqueue_scripts()
        {
            global $post;

            if (!empty($post) && $post->post_type != 'bkx_base')
                return;

            $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');
            $base_alias = bkx_crud_option_multisite('bkx_alias_base');
            wp_enqueue_script('iris');
	        wp_enqueue_style( 'jquery-ui-style' );
            wp_register_script("bkx-base-validate", BKX_PLUGIN_DIR_URL . "public/js/admin/bkx-base-validate.js", false, BKX_PLUGIN_VER, true);
            $translation_array = array('plugin_url' => BKX_PLUGIN_DIR_URL, 'seat_alias' => $seat_alias, 'base_alias' => $base_alias);
            wp_localize_script('bkx-base-validate', 'base_obj', $translation_array);
            wp_enqueue_script('bkx-base-validate');
        }

        /**
         * Adding Meta Box
         */
        public function add_bkx_base_metaboxes()
        {
            $alias = bkx_crud_option_multisite('bkx_alias_base');
            add_meta_box('bkx_base_boxes', __("{$alias} Details", 'bookingx'), array($this, 'bkx_base_boxes_metabox_callback'), 'bkx_base', 'normal', 'high');
        }

        /**
         * Adding Meta Box Call Back
         */
        public function bkx_base_boxes_metabox_callback($post)
        {
            $base_alias = bkx_crud_option_multisite('bkx_alias_base');
            $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
            wp_nonce_field('bkx_base_boxes_metabox', 'bkx_base_boxes_metabox_nonce');
            //Get Seat post Array
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'bkx_seat',
                'post_status' => 'publish',
                'suppress_filters' => false
            );
            $get_seat_array = get_posts($args);
            $values = get_post_custom($post->ID);

            //if(!empty($values) && !is_wp_error($values)){
            $base_price = isset($values['base_price']) ? esc_attr($values['base_price'][0]) : "";
            $base_time_option = isset($values['base_time_option']) ? esc_attr($values['base_time_option'][0]) : "";
            $base_month = isset($values['base_month']) ? esc_attr($values['base_month'][0]) : "";
            $base_day = isset($values['base_day']) ? esc_attr($values['base_day'][0]) : "";
            $base_hours = isset($values['base_hours']) ? esc_attr($values['base_hours'][0]) : 0;
            $base_extended_limit = isset($values['base_extended_limit']) ? esc_attr($values['base_extended_limit'][0]) : "";

            $base_minutes = isset($values['base_minutes']) ? esc_attr($values['base_minutes'][0]) : "";
            $base_is_extended = isset($values['base_is_extended']) ? esc_attr($values['base_is_extended'][0]) : "N";
            $base_is_allow_addition = isset($values['base_is_allow_addition']) ? esc_attr($values['base_is_allow_addition'][0]) : "Y";
            $base_is_unavailable = isset($values['base_is_unavailable']) ? esc_attr($values['base_is_unavailable'][0]) : "";
            $base_unavailable_from = isset($values['base_unavailable_from']) ? esc_attr($values['base_unavailable_from'][0]) : "";
            $base_unavailable_till = isset($values['base_unavailable_till']) ? esc_attr($values['base_unavailable_till'][0]) : "";
            if (!empty($values['base_selected_seats'][0])) {
                $res_seat_final = maybe_unserialize($values['base_selected_seats'][0]);
                $res_seat_final = maybe_unserialize($res_seat_final);
            }
            $base_location_type = isset($values['base_location_type']) ? esc_attr($values['base_location_type'][0]) : "Fixed Location";
           $base_seat_all = isset($values['base_seat_all']) ? esc_attr($values['base_seat_all'][0]) : "";
            //$base_colour = get_post_meta($post->ID, 'base_colour', true);
            //}
            $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
            ?>
            <div class="error" id="error_list" style="display:none;"></div>
            <div class="active" id="base_name">
                <?php printf(__('%1$s  Price <span class="bkx-require">(*) Required</span>', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input name="base_price" type="text" value="<?php if (isset($base_price) && $base_price != '') {
                        echo $base_price;
                    } ?>" id="id_base_price">
                </div>
            </div>

            <div class="active" id="months_days_times">
                <?php printf(__('Is %1$s  time in  days, hours or minutes <span class="bkx-require">(*) Required</span>', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <select name="base_months_days_times" id="id_base_months_days_times" onchange=""
                            class="medium gfield_select" tabindex="4">
                        <option value=""><?php esc_html_e('Select Time Option', 'bookingx'); ?></option>
                        <option value="HourMinutes" <?php if ($base_time_option == "H") {
                            echo "selected='selected'";
                        } ?>><?php esc_html_e('Hour and Minutes', 'bookingx'); ?></option>
                        <option value="Days" <?php if ($base_time_option == "D") {
                            echo "selected='selected'";
                        } ?>><?php esc_html_e('Days', 'bookingx'); ?></option>
                    </select>
                </div>
            </div>
            <div class="active" id="months" style="display: none">
                <?php printf(__('Number of Months for %1$s Time <span class="bkx-require">(*) Required</span>', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input name="base_months" type="text" value="<?php if (isset($base_month) && $base_month != "") {
                        echo $base_month;
                    } ?>" id="id_base_months">
                </div>
            </div>
            <div class="active" id="days" style="display: none">
                <?php printf(__('Number of Days for %1$s Time <span class="bkx-require">(*) Required</span>', 'bookingx'), $base_alias); ?>

                <div class="plugin-description">
                    <input name="base_days" type="text" value="<?php if (isset($base_day) && $base_day != "") {
                        echo $base_day;
                    } ?>" id="id_base_days">
                </div>

            </div>
            <div class="active" id="hours_minutes" style="display: none">
                <?php printf(__('%1$s  Time In Hours and Minutes <span class="bkx-require">(*) Required</span>', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input name="base_hours_minutes" size="1" type="text"
                           value="<?php printf(esc_html__('%1$s', 'bookingx'), $base_hours); ?>"
                           id="id_base_hours_minutes">
                    <?php
                    if (isset($base_minutes)) {
                        $baseMinute = $base_minutes;
                    } ?>
                    <select name="base_minutes" id="id_base_minutes">
                        <option value=00 <?php if ($baseMinute == 15) {
                            echo "selected";
                        } ?>><?php esc_html_e('00', 'bookingx'); ?></option>
                        <option value=15 <?php if ($baseMinute == 15) {
                            echo "selected";
                        } ?>><?php esc_html_e('15', 'bookingx'); ?></option>
                        <option value=30 <?php if ($baseMinute == 30) {
                            echo "selected";
                        } ?>><?php esc_html_e('30', 'bookingx'); ?></option>
                        <option value=45 <?php if ($baseMinute == 45) {
                            echo "selected";
                        } ?>><?php esc_html_e('45', 'bookingx'); ?></option>
                    </select>
                </div>
            </div>
            <div class="active" id="extended">
                <?php printf(esc_html__('Can %1$s time be extended', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <ul class="bkx_radio" id="input_2_15">
                        <li class="bkx_choice_15_0">
                            <input name="base_extended" type="radio" value="Yes" id="id_base_extended_yes"
                                   tabindex="10" <?php if ($base_is_extended == "Y") {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_15_0"><?php esc_html_e('Yes', 'bookingx'); ?></label>
                        </li>
                        <li class="bkx_choice_15_1">
                            <input name="base_extended" type="radio" value="No" id="id_base_extended_no"
                                   tabindex="11" <?php if ($base_is_extended == "N") {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_15_1"><?php esc_html_e('No', 'bookingx'); ?></label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="active" id="base_extended_input" style="display: none;">
                <?php printf(__('Max limit of %1$s extends', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input name="base_extended_limit" type="number"
                           value="<?php if (isset($base_extended_limit) && $base_extended_limit != "") {
                               echo $base_extended_limit;
                           } ?>" id="id_base_extended_limit">
                </div>
            </div>
            <div class="active" id="base_name">
                <?php printf(__('This %1$s  is available to the following %2$s <span class="bkx-require">(*) Required</span>', 'bookingx'), $base_alias, $alias_seat); ?>
                <div class="plugin-description">
                    <ul class="gfield_checkbox" id="input_2_9">
                        <?php
                        $selected = "";
                        if (!empty($get_seat_array) && !is_wp_error($get_seat_array)) :?>
                            <li class="bkx_choice_9_1">
                                <input name="base_seat_all" <?php echo ($base_seat_all) ? 'checked' : ''; ?>
                                       type="checkbox"
                                       value="All" id="id_base_seat_all" tabindex="12">
                                <label for="choice_9_1"><?php esc_html_e('All', 'bookingx'); ?></label>
                            </li>
                            <?php
                            foreach ($get_seat_array as $value) {
                                if (isset($res_seat_final) && sizeof($res_seat_final) > 0) {
                                    $selected = false;
                                    if (in_array($value->ID, $res_seat_final) || isset($base_seat_all) && $base_seat_all == 'All') {
                                        $selected = true;
                                    }
                                } ?>
                                <li class="bkx_choice_9_2">
                                    <input name="base_seats[]" type="checkbox" value="<?php echo $value->ID; ?>"
                                           tabindex="13" class="base-seat-checkboxes" <?php if ($selected == true) {
                                        echo "checked='checked'";
                                    } ?>>
                                    <label for="choice_9_2"><?php echo $value->post_title; ?></label>
                                </li>
                            <?php }
                        else: ?>
                            <li class="bkx_choice_9_1">
                                <label for="choice_9_1"><b><a
                                                href="<?php echo admin_url('post-new.php?post_type=bkx_seat') ?>"><?php printf(esc_html__('You haven\'t any %1$s, Please create %1$s now :', 'bookingx'), $alias_seat); ?></a></b></label>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="active" id="allow_addition">
                <?php printf(esc_html__('Does this %1$s allow for %2$ss :', 'bookingx'), $base_alias, $addition_alias); ?>
                <div class="plugin-description">
                    <ul class="bkx_radio" id="input_2_13">
                        <li class="bkx_choice_13_0">
                            <input name="base_allow_addition" type="radio" value="Yes" id="choice_13_0" tabindex="18"
                                   onclick="" <?php if ($base_is_allow_addition == "Y") {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_13_0"><?php esc_html_e('Yes', 'bookingx'); ?></label>
                        </li>
                        <li class="bkx_choice_13_1">
                            <input name="base_allow_addition" type="radio" value="No" id="choice_13_1" tabindex="19"
                                   onclick="" <?php if ($base_is_allow_addition == "N") {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_13_1"><?php esc_html_e('No', 'bookingx'); ?></label>
                        </li>
                    </ul>
                </div>
            </div>
            <!--only for edit form  -->
            <!--<p><strong><?php /*esc_html_e('Colour', 'bookingx'); */?></strong></p>
            <p><?php /*printf(esc_html__('%1$s Colour', 'bookingx'), $base_alias); */?></p>
            <p><input type="text" name="base_colour" id="id_base_colour"
                      value="<?php /*if (isset($base_colour) && ($base_colour != '')) {
                          echo $base_colour;
                      } */?>"/></p>-->
            <div class="active" id="is_unavailable">
                <?php printf(esc_html__('Does the  %1$s  Unavailable? :', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input type="checkbox" name="base_is_unavailable" id="id_base_is_unavailable"
                           value="Yes" <?php if ($base_is_unavailable == "Y") {
                        echo "checked='checked'";
                    } ?>>
                    <label><?php esc_html_e('Yes', 'bookingx'); ?></label>
                </div>
            </div>
            <div class="active" id="unavailable_from">
                <?php printf(esc_html__('%1$s   Unavailable From :', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input type="text" autocomplete="off" name="base_unavailable_from" id="id_base_unavailable_from"
                           value="<?php if (isset($base_unavailable_from)) {
                               echo $base_unavailable_from;
                           } ?>">
                </div>
            </div>
            <div class="active" id="unavailable_till">
                <?php printf(esc_html__('%1$s  Unavailable Till :', 'bookingx'), $base_alias); ?>
                <div class="plugin-description">
                    <input autocomplete="off" type="text" name="base_unavailable_till" id="id_base_unavailable_till"
                           value="<?php if (isset($base_unavailable_till)) {
                               echo $base_unavailable_till;
                           } ?>">
                </div>
            </div>
            <?php
        }

        /**
         * @param $post_id
         * @param $post
         * @param null $update
         */
        public function save_bkx_base_metaboxes($post_id, $post, $update = null)
        {
            // Bail if we're doing an auto save
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            // if our current user can't edit this post, bail
            //if (!current_user_can('edit_post')) return;
            if ($post->post_type != 'bkx_base') return;
            if ($post->post_status != 'publish') return;
            do_action('bkx_base_meta_box_save', $post_id);
            $basePrice = isset($_POST['base_price']) ? sanitize_text_field($_POST['base_price']) : "";
            $baseMonthDaysTime = isset($_POST['base_months_days_times']) ? sanitize_text_field($_POST['base_months_days_times']) : "";
            $baseMonths = isset($_POST['base_months']) ? sanitize_text_field($_POST['base_months']) : "";
            $baseDays = isset($_POST['base_days']) ? sanitize_text_field($_POST['base_days']) : "";
            $baseHoursMinutes = isset($_POST['base_hours_minutes']) ? sanitize_text_field($_POST['base_hours_minutes']) : "";
            $baseMinutes = isset($_POST['base_minutes']) ? sanitize_text_field($_POST['base_minutes']) : "";
            $baseExtended = isset($_POST['base_extended']) ? sanitize_text_field($_POST['base_extended']) : "";
            $baseLocationType = isset($_POST['base_location_type']) ? sanitize_text_field($_POST['base_location_type']) : "";
            $baselocationmobile = isset($_POST['base_location_mobile']) ? sanitize_text_field($_POST['base_location_mobile']) : "";
            $baseLocationDifferSeat = isset($_POST['base_location_differ_seat']) ? sanitize_text_field($_POST['base_location_differ_seat']) : "";
            $baseStreet = isset($_POST['base_street']) ? sanitize_text_field($_POST['base_street']) : "";
            $baseCity = isset($_POST['base_city']) ? sanitize_text_field($_POST['base_city']) : "";
            $baseState = isset($_POST['base_state']) ? sanitize_text_field($_POST['base_state']) : "";
            $basePostcode = isset($_POST['base_postcode']) ? sanitize_text_field($_POST['base_postcode']) : "";
            $baseAllowAddition = isset($_POST['base_allow_addition']) ? sanitize_text_field($_POST['base_allow_addition']) : "";
            $baseSeatAll = isset($_POST['base_seat_all']) ? $_POST['base_seat_all'] : "";
            $base_is_unavailable = isset($_POST['base_is_unavailable']) ? sanitize_text_field($_POST['base_is_unavailable']) : "";
            $base_unavailable_from = isset($_POST['base_unavailable_from']) ? sanitize_text_field($_POST['base_unavailable_from']) : "";
            $base_unavailable_till = isset($_POST['base_unavailable_till']) ? sanitize_text_field($_POST['base_unavailable_till']) : "";
            $baseSeatsValue = isset($_POST['base_seats']) ? array_map('sanitize_text_field', wp_unslash($_POST['base_seats'])) : "";
            $seat_slug = array();
            if (!empty($baseSeatsValue)) {
                foreach ($baseSeatsValue as $key => $seat_id) {
                    $seat_data = get_post($seat_id);
                    $seat_slug[] = $seat_data->post_name;
                }
                update_post_meta($post_id, 'seat_slugs', $seat_slug);
            }
            $base_extended_limit = isset($_POST['base_extended_limit']) ? sanitize_text_field($_POST['base_extended_limit']) : "";
            $base_seat_all = isset($_POST['base_seat_all']) ? sanitize_text_field($_POST['base_seat_all']) : "";
            //$base_colour = isset($_POST['base_colour']) ? sanitize_text_field($_POST['base_colour']) : "";
            if ($baseMonthDaysTime == "Months") {
                $baseTimeOption = 'M';
            }
            if ($baseMonthDaysTime == "Days") {
                $baseTimeOption = 'D';
            }
            if ($baseMonthDaysTime == "HourMinutes") {
                $baseTimeOption = 'H';
            }
            if ($baseExtended == "Yes") {
                $baseIsExtended = 'Y';
            }
            if ($baseExtended == "No") {
                $baseIsExtended = 'N';
            }
            if ($baseLocationType == "Fixed Location" || "FM") {
                $isLocationFixed = 'Y';
                $isMobileOnly = 'N';
            }
            if ($baseLocationType == "Mobile") {
                $isLocationFixed = 'N';
                if ($baselocationmobile == "Yes") {
                    $isMobileOnly = 'Y';
                }
                if ($baselocationmobile == "No") {
                    $isMobileOnly = 'N';
                }
            }
            if ($baseLocationDifferSeat == "Yes") {
                $isLocationDifferent = 'Y';
            }
            if ($baseLocationDifferSeat == "No") {
                $isLocationDifferent = 'N';
            }
            if ($baseAllowAddition == "Yes") {
                $isAllowAddition = 'Y';
            }
            if ($baseAllowAddition == "No") {
                $isAllowAddition = 'N';
            }
            if (isset($base_is_unavailable) && $base_is_unavailable == "Yes") {
                $base_is_unavailable = 'Y';
            } else {
                $base_is_unavailable = 'N';
            }
            // Make sure your data is set before trying to save it
            if (isset($basePrice))
                update_post_meta($post_id, 'base_price', $basePrice);
           /* if (!empty($base_colour))
                update_post_meta($post_id, 'base_colour', $base_colour);*/
            if (isset($baseTimeOption))
                update_post_meta($post_id, 'base_time_option', $baseTimeOption);
            if (isset($baseMonths))
                update_post_meta($post_id, 'base_month', $baseMonths);
            if (isset($baseDays))
                update_post_meta($post_id, 'base_day', $baseDays);
            if (isset($baseHoursMinutes))
                update_post_meta($post_id, 'base_hours', $baseHoursMinutes);
            if (isset($baseMinutes))
                update_post_meta($post_id, 'base_minutes', $baseMinutes);
            if (isset($baseSeatAll))
                update_post_meta($post_id, 'base_seats', $baseSeatAll);
            if (isset($isLocationFixed))
                update_post_meta($post_id, 'base_is_location_fixed', $isLocationFixed);
            if (isset($isMobileOnly))
                update_post_meta($post_id, 'base_is_mobile_only', $isMobileOnly);
            if (isset($isLocationDifferent))
                update_post_meta($post_id, 'base_is_location_differ_seat', $isLocationDifferent);
            if (isset($baseCity))
                update_post_meta($post_id, 'base_city', $baseCity);
            if (isset($baseStreet))
                update_post_meta($post_id, 'base_street', $baseStreet);
            if (isset($baseState))
                update_post_meta($post_id, 'base_state', $baseState);
            if (isset($basePostcode))
                update_post_meta($post_id, 'base_postcode', $basePostcode);
            if (isset($isAllowAddition))
                update_post_meta($post_id, 'base_is_allow_addition', $isAllowAddition);
            if (isset($baseIsExtended))
                update_post_meta($post_id, 'base_is_extended', $baseIsExtended);
            if (isset($base_is_unavailable))
                update_post_meta($post_id, 'base_is_unavailable', $base_is_unavailable);
            if (isset($base_unavailable_from))
                update_post_meta($post_id, 'base_unavailable_from', $base_unavailable_from);
            if (isset($base_unavailable_till))
                update_post_meta($post_id, 'base_unavailable_till', $base_unavailable_till);
            if (!empty($baseSeatsValue))
                update_post_meta($post_id, 'base_selected_seats', $baseSeatsValue);
            if (!empty($base_extended_limit))
                update_post_meta($post_id, 'base_extended_limit', $base_extended_limit);
            if (!empty($baseLocationType))
                update_post_meta($post_id, 'base_location_type', $baseLocationType);
            update_post_meta($post_id, 'base_seat_all', $base_seat_all);
        }

        /**
         * @param $defaults
         * @return mixed
         */
        public function bkx_base_columns_head($defaults)
        {
            $defaults['display_shortcode_all'] = 'Shortcode [bookingx base-id="all"]';
            return $defaults;
        }

        /**
         * @param $column_name
         * @param $post_id
         */
        public function bkx_base_columns_content($column_name, $post_id)
        {
            if ($column_name == 'display_shortcode_all') {
                echo '[bookingx base-id="' . $post_id . '" description="yes" image="yes" extra-info="no"]';
            }
        }
    }
}
<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
add_action('add_meta_boxes', 'add_bkx_base_metaboxes');
/**
 * Add MetaBox on Seat Post Type
 */
function add_bkx_base_metaboxes()
{
    $alias_seat = bkx_crud_option_multisite('bkx_alias_base');
    add_meta_box('bkx_base_boxes', __("$alias_seat Details", 'bookingx'), 'bkx_base_boxes_metabox_callback', 'bkx_base', 'normal', 'high');
}

/**
 * @param $post
 */
function bkx_base_boxes_metabox_callback($post)
{
    wp_nonce_field('bkx_base_boxes_metabox', 'bkx_base_boxes_metabox_nonce');
    //load custom scripts & styles

    //register and access the script handle in main.js
    $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');
    $base_alias = bkx_crud_option_multisite('bkx_alias_base');
    wp_enqueue_script("main_script", BKX_PLUGIN_DIR_URL."admin/js/main_1.js", false, BKX_PLUGIN_VER , true);
    $translation_array = array('plugin_url' => BKX_PLUGIN_DIR_URL, 'seat_alias' => $seat_alias, 'base_alias' => $base_alias);
    wp_localize_script('main_script', 'url_obj', $translation_array);

    $base_alias = bkx_crud_option_multisite('bkx_alias_base');
    $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');


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
    $base_hours = isset($values['base_hours']) ? esc_attr($values['base_hours'][0]) : "";
    $base_extended_limit = isset($values['base_extended_limit']) ? esc_attr($values['base_extended_limit'][0]) : "";

    $base_minutes = isset($values['base_minutes']) ? esc_attr($values['base_minutes'][0]) : "";
    $base_is_extended = isset($values['base_is_extended']) ? esc_attr($values['base_is_extended'][0]) : "N";
    $base_is_location_fixed = isset($values['base_is_location_fixed']) ? esc_attr($values['base_is_location_fixed'][0]) : "";
    $base_is_mobile_only = isset($values['base_is_mobile_only']) ? esc_attr($values['base_is_mobile_only'][0]) : "N";
    $base_is_location_differ_seat = isset($values['base_is_location_differ_seat']) ? esc_attr($values['base_is_location_differ_seat'][0]) : "N";
    $base_street = isset($values['base_street']) ? esc_attr($values['base_street'][0]) : "";
    $base_city = isset($values['base_city']) ? esc_attr($values['base_city'][0]) : "";
    $base_state = isset($values['base_state']) ? esc_attr($values['base_state'][0]) : "";
    $base_postcode = isset($values['base_postcode']) ? esc_attr($values['base_postcode'][0]) : "";
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
    $base_colour = get_post_meta($post->ID, 'base_colour', true);
    //}
    $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
    ?>
    <div class="error" id="error_list" style="display:none;"></div>

    <div class="active" id="base_name">
        <?php printf(esc_html__('%1$s  Price:', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <input name="base_price" type="text" value="<?php if (isset($base_price) && $base_price != '') {
                echo $base_price;
            } ?>" id="id_base_price">
        </div>
    </div>

    <div class="active" id="months_days_times">
        <?php printf(esc_html__('Is %1$s  time in  days, hours or minutes :', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <select name="base_months_days_times" id="id_base_months_days_times" onchange=""
                    class="medium gfield_select" tabindex="4">
                <option value=""><?php esc_html_e('Select Time Option', 'bookingx'); ?></option>
                <option value="HourMinutes" <?php if ($base_time_option == "H") {
                    echo "selected='selected'";
                } ?>><?php esc_html_e('Hour and Minutes', 'bookingx'); ?></option>
            </select>
        </div>

    </div>

    <div class="active" id="months" style="display: none">
        <?php printf(esc_html__('Number of Months for %1$s   Time :', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <input name="base_months" type="text" value="<?php if (isset($base_month) && $base_month != "") {
                echo $base_month;
            } ?>" id="id_base_months">
        </div>
    </div>
    <div class="active" id="days" style="display: none">
        <?php printf(esc_html__('Number of Days for %1$s   Time :', 'bookingx'), $base_alias); ?>

        <div class="plugin-description">
            <input name="base_days" type="text" value="<?php if (isset($base_day) && $base_day != "") {
                echo $base_day;
            } ?>" id="id_base_days">
        </div>

    </div>
    <div class="active" id="hours_minutes" style="display: none">
        <?php printf(esc_html__('%1$s  Time In Hours and Minutes :', 'bookingx'), $base_alias); ?>

        <div class="plugin-description">
            <input name="base_hours_minutes" size="1" type="text" value="<?php if (!isset($base_hours)) {
                echo 0;
            }
            if (isset($base_hours) && $base_hours != "") {
                echo $base_hours;
            } ?>" id="id_base_hours_minutes">
            <?php
            if (isset($base_minutes)) {
                $baseMinute = $base_minutes;
            }

            ?>
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
        <?php printf(esc_html__('Can %1$s  time be extended :', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <ul class="gfield_radio" id="input_2_15">
                <li class="gchoice_15_0"><?php //echo $base_is_extended;
                    ?>
                    <input name="base_extended" type="radio" value="Yes" id="id_base_extended_yes"
                           tabindex="10" <?php if ($base_is_extended == "Y") {
                        echo "checked='checked'";
                    } ?>>
                    <label for="choice_15_0"><?php esc_html_e('Yes', 'bookingx'); ?></label>
                </li>
                <li class="gchoice_15_1">
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
        <?php printf(esc_html__('Max limit of %1$s extends', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <input name="base_extended_limit" type="number"
                   value="<?php if (isset($base_extended_limit) && $base_extended_limit != "") {
                       echo $base_extended_limit;
                   } ?>" id="id_base_extended_limit">
        </div>
    </div>
    <div class="active" id="base_name">
        <?php printf(esc_html__('This %1$s  is available to the following %2$s :', 'bookingx'), $base_alias, $alias_seat); ?>
        <div class="plugin-description">
            <ul class="gfield_checkbox" id="input_2_9">
                
                <?php
                $selected = "";
                if (!empty($get_seat_array) && !is_wp_error($get_seat_array)) :?>
                    <li class="gchoice_9_1">
                        <input name="base_seat_all" <?php echo ($base_seat_all) ? 'checked' : ''; ?> type="checkbox"
                               value="All" id="id_base_seat_all" tabindex="12">
                        <label for="choice_9_1"><?php esc_html_e('All', 'bookingx'); ?></label>
                    </li>
                    <?php
                    foreach ($get_seat_array as $value) {
                        ?>
                        <?php
                        if (isset($res_seat_final) && sizeof($res_seat_final) > 0) {
                            $selected = false;
                            if (in_array($value->ID, $res_seat_final)) {
                                $selected = true;
                            }
                        }
                        ?>
                        <li class="gchoice_9_2">
                            <input name="base_seats[]" type="checkbox" value="<?php echo $value->ID; ?>" tabindex="13"
                                   class="myCheckbox" <?php if ($selected == true) {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_9_2"><?php echo $value->post_title; ?></label>
                        </li>
                    <?php }
                else: ?>
                    <li class="gchoice_9_1">
                        <label for="choice_9_1"><b><a href="<?php echo admin_url('post-new.php?post_type=bkx_seat')?>"><?php  printf(esc_html__('You haven\'t any %1$s, Please create %1$s now :', 'bookingx'), $alias_seat); ?></a></b></label>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="active" id="base_name">
        <?php printf(esc_html__('Is this  %1$s   in a fixed location or mobile? :', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <ul class="gfield_radio" id="input_2_11">
                <li class="gchoice_11_0">
                    <input name="base_location_type" type="radio" value="Fixed Location" id="choice_11_0" tabindex="16"
                           onclick="" <?php if ($base_location_type == "Fixed Location") {
                        echo "checked='checked'";
                    } ?>>
                    <label for="choice_11_0"><?php esc_html_e('Fixed Location', 'bookingx'); ?></label>
                </li>
                <li class="gchoice_11_1">
                    <input name="base_location_type" type="radio" value="Mobile" id="choice_11_1" tabindex="17"
                           onclick="" <?php if ($base_location_type == "Mobile") {
                        echo "checked='checked'";
                    } ?> >
                    <label for="choice_11_1"><?php esc_html_e('Mobile Location', 'bookingx'); ?></label>
                </li>
                <li class="gchoice_11_1">
                    <input name="base_location_type" type="radio" value="FM" id="id_base_type_fm" tabindex="17"
                           onclick="" <?php if ($base_location_type == "FM") {
                        echo "checked='checked'";
                    } ?> >
                    <label for="choice_11_1"><?php esc_html_e('Fixed and Mobile Location', 'bookingx'); ?></label>
                </li>
            </ul>
        </div>
    </div>
    <div class="active" id="mobile_only">
        <?php printf(esc_html__('Is this %1$s a mobile only option ? ', 'bookingx'), $alias_seat); ?>
        <div class="plugin-description">
            <ul class="gfield_radio" id="input_2_mob_only">
                <li class="gchoice_mob_only1">
                    <input name="base_location_mobile" type="radio" value="Yes" id="gchoice_mob_only_1" tabindex="20"
                           onclick="" <?php if ($base_is_mobile_only == "Y") {
                        echo "checked='checked'";
                    } ?> >
                    <label for="gchoice_mob_only1"><?php esc_html_e('Yes', 'bookingx'); ?></label>
                </li>
                <li class="gchoice_mob_only2">
                    <input name="base_location_mobile" type="radio" value="No" id="gchoice_mob_only_2" tabindex="21"
                           onclick="" <?php if ($base_is_mobile_only == "N") {
                        echo "checked='checked'";
                    } ?> >
                    <label for="gchoice_mob_only2"><?php esc_html_e('No', 'bookingx'); ?></label></li>
            </ul>
        </div>
    </div>
    <div class="active" id="location_differ" style="display:none;">
        <?php printf(esc_html__('Does the %1$s location differ from %2$s location ?', 'bookingx'), $base_alias, $alias_seat); ?>
        <div class="plugin-description">
            <ul class="gfield_radio" id="input_2_13">
                <li class="gchoice_13_0">
                    <input name="base_location_differ_seat" type="radio" value="Yes" id="choice_13_0" tabindex="18"
                           onclick="" <?php if ($base_is_location_differ_seat == "Y") {
                        echo "checked='checked'";
                    } ?> >
                    <label for="choice_13_0"><?php esc_html_e('Yes', 'bookingx'); ?></label>
                </li>
                <li class="gchoice_13_1">
                    <input name="base_location_differ_seat" type="radio" value="No" id="choice_13_1" tabindex="19"
                           onclick="" <?php if ($base_is_location_differ_seat == "N") {
                        echo "checked='checked'";
                    } ?>>
                    <label for="choice_13_1"><?php esc_html_e('No', 'bookingx'); ?></label>
                </li>
            </ul>
        </div>

    </div>

    <div class="active" id="location_address" style="display:none;">
        <?php esc_html_e('Base Location :', 'bookingx'); ?>
        <br/>
        <input type="text" name="base_street" value="<?php if (isset($base_street) && $base_street != "") {
            echo $base_street;
        } ?>" id="id_base_street"><?php esc_html_e('Street', 'bookingx'); ?> <br/>
        <input type="text" name="base_city" value="<?php if (isset($base_city) && $base_city != "") {
            echo $base_city;
        } ?>" id="id_base_city"> <?php esc_html_e('City', 'bookingx'); ?><br/>
        <input type="text" name="base_state" value="<?php if (isset($base_state) && $base_state != "") {
            echo $base_state;
        } ?>" id="id_base_state"> <?php esc_html_e('State / Province / Region', 'bookingx'); ?><br/>
        <input type="text" name="base_postcode" value="<?php if (isset($base_postcode) && $base_postcode != "") {
            echo $base_postcode;
        } ?>" id="id_base_postcode"> <?php esc_html_e('Zip / Postal Code', 'bookingx'); ?><br/>

    </div>

    <div class="active" id="allow_addition">
        <?php printf(esc_html__('Does this %1$s allow for %2$ss :', 'bookingx'), $base_alias, $addition_alias); ?>
        <div class="plugin-description">
            <ul class="gfield_radio" id="input_2_13">
                <li class="gchoice_13_0">
                    <input name="base_allow_addition" type="radio" value="Yes" id="choice_13_0" tabindex="18"
                           onclick="" <?php if ($base_is_allow_addition == "Y") {
                        echo "checked='checked'";
                    } ?>>
                    <label for="choice_13_0"><?php esc_html_e('Yes', 'bookingx'); ?></label>
                </li>
                <li class="gchoice_13_1">
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
    <p><strong><?php esc_html_e('Colour', 'bookingx'); ?></strong></p>
    <p><?php printf(esc_html__('%1$s Colour', 'bookingx'), $base_alias); ?></p>
    <p><input type="text" name="base_colour" id="id_base_colour"
              value="<?php if (isset($base_colour) && ($base_colour != '')) {
                  echo $base_colour;
              } ?>"/></p>
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
            <input type="text" name="base_unavailable_from" id="id_base_unavailable_from"
                   value="<?php if (isset($base_unavailable_from)) {
                       echo $base_unavailable_from;
                   } ?>">
        </div>
    </div>
    <div class="active" id="unavailable_till">
        <?php printf(esc_html__('%1$s  Unavailable Till :', 'bookingx'), $base_alias); ?>
        <div class="plugin-description">
            <input type="text" name="base_unavailable_till" id="id_base_unavailable_till"
                   value="<?php if (isset($base_unavailable_till)) {
                       echo $base_unavailable_till;
                   } ?>">
        </div>
    </div>

    <script>
        jQuery(document).ready(function () {

            if (jQuery("input[name=base_location_differ_seat]:radio:checked").val() == "Yes") {
                jQuery("#location_address").show();
            }

            if (jQuery("input[name=base_extended]:radio:checked").val() == "Yes") {
                jQuery("#base_extended_input").show();
            }

            if (jQuery("input[name=base_location_type]:radio:checked").val() == "Mobile") {
                jQuery("#mobile_only").show();
            }
            else if (jQuery("input[name=base_location_type]:radio:checked").val() == "Fixed Location") {
                jQuery("#mobile_only").hide();
            }
            else if (jQuery("input[name=base_location_type]:radio:checked").val() == "FM") {
                jQuery("#mobile_only").show();
                jQuery("#location_differ").show();
            }

            if (jQuery("input[name=base_is_unavailable]").attr('checked') == "checked") {
                jQuery("#unavailable_from").show();
                jQuery("#unavailable_till").show();
            }
            if (jQuery("input[name=base_is_unavailable]").attr('checked') == undefined) {
                jQuery("#unavailable_from").hide();
                jQuery("#unavailable_till").hide();
            }
            jQuery("input[name=base_location_type]:radio").bind("change", function (event, ui) {
                var this_val = jQuery(this).val();
                if (this_val == "Mobile") {
                    jQuery("#mobile_only").show();
                }
                else if (this_val == "Fixed Location") {
                    jQuery("#mobile_only").hide();
                }
            });
        });
    </script>
    <!--End Html-->
    <?php
}

add_action('save_post', 'save_bkx_base_metaboxes', 3, 10);
/**
 * @param $post_id
 * @param $post
 * @param $update
 */
function save_bkx_base_metaboxes($post_id, $post, $update)
{
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    // if our current user can't edit this post, bail
    if (!current_user_can('edit_post')) return;

    if ($post->post_type != 'bkx_base') return;

    $basePrice = sanitize_text_field($_POST['base_price']);
    $baseMonthDaysTime = sanitize_text_field($_POST['base_months_days_times']);
    $baseMonths = sanitize_text_field($_POST['base_months']);
    $baseDays = sanitize_text_field($_POST['base_days']);
    $baseHoursMinutes = sanitize_text_field($_POST['base_hours_minutes']);
    $baseMinutes = sanitize_text_field($_POST['base_minutes']);
    $baseExtended = sanitize_text_field($_POST['base_extended']);
    $baseLocationType = sanitize_text_field($_POST['base_location_type']);
    $baselocationmobile = sanitize_text_field($_POST['base_location_mobile']);
    $baseLocationDifferSeat = sanitize_text_field($_POST['base_location_differ_seat']);
    $baseStreet = sanitize_text_field($_POST['base_street']);
    $baseCity = sanitize_text_field($_POST['base_city']);
    $baseState = sanitize_text_field($_POST['base_state']);
    $basePostcode = sanitize_text_field($_POST['base_postcode']);
    $baseAllowAddition = sanitize_text_field($_POST['base_allow_addition']);
    $baseSeatAll = $_POST['base_seat_all'];
    $base_is_unavailable = sanitize_text_field($_POST['base_is_unavailable']);
    $base_unavailable_from = sanitize_text_field($_POST['base_unavailable_from']);
    $base_unavailable_till = sanitize_text_field($_POST['base_unavailable_till']);
    $baseSeatsValue = array_map('sanitize_text_field', wp_unslash($_POST['base_seats']));
    if(!empty($baseSeatsValue)){
        foreach ($baseSeatsValue as $key => $seat_id) {
                $seat_data = get_post($seat_id); 
                $seat_slug[] = $seat_data->post_name;
        }
        update_post_meta($post_id, 'seat_slugs', $seat_slug);
    }
    $base_extended_limit = sanitize_text_field($_POST['base_extended_limit']);
    $base_seat_all = sanitize_text_field($_POST['base_seat_all']);
    $base_colour = sanitize_text_field($_POST['base_colour']);


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

    if (!empty($base_colour))
        update_post_meta($post_id, 'base_colour', $base_colour);


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

add_filter('manage_bkx_base_posts_columns', 'bkx_base_columns_head');
add_action('manage_bkx_base_posts_custom_column', 'bkx_base_columns_content', 10, 2);

/**
 *
 * @param array $defaults
 * @return string
 */
function bkx_base_columns_head($defaults)
{
    $defaults['display_shortcode_all'] = 'Shortcode [bookingx display="rows" base-id="all"] (display : rows|columns)';
    return $defaults;
}

/**
 *
 * @param type $column_name
 * @param type $post_ID
 */
function bkx_base_columns_content($column_name, $post_ID)
{
    if ($column_name == 'display_shortcode_all') {
        echo '[bookingx base-id="' . $post_ID . '" description="yes" image="yes" extra-info="no"]';
    }
}
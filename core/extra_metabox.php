<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
add_action('add_meta_boxes', 'add_bkx_extra_metaboxes');
function add_bkx_extra_metaboxes()
{
    $alias_addition = bkx_crud_option_multisite('bkx_alias_addition');
    add_meta_box('bkx_base_boxes', __(ucwords($alias_addition) . " Details", 'bookingx'), 'bkx_extra_boxes_metabox_callback', 'bkx_addition', 'normal', 'high');
}

function bkx_extra_boxes_metabox_callback($post)
{
    wp_nonce_field('bkx_extra_boxes_metabox', 'bkx_extra_boxes_metabox_nonce');
    $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
    $base_alias = bkx_crud_option_multisite('bkx_alias_base');
    wp_enqueue_script("main_addition_script", BKX_PLUGIN_DIR_URL."admin/js/main_addition_1.js", false, BKX_PLUGIN_VER, true);
    $translation_array = array('plugin_url' => BKX_PLUGIN_DIR_URL);
    wp_localize_script('main_addition_script', 'url_obj', $translation_array);
    //Get Seat post Array
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'bkx_base',
        'post_status' => 'publish',
        'meta_key' => 'base_is_allow_addition',
        'meta_value' => 'Y',
        'suppress_filters' => false
    );
    $get_base_array = get_posts($args);

    $values = get_post_custom($post->ID);

    $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');

    // if(!empty($values) && !is_wp_error($values)){
    $addition_price = isset($values['addition_price']) ? esc_attr($values['addition_price'][0]) : "";
    $addition_time_option = isset($values['addition_time_option']) ? esc_attr($values['addition_time_option'][0]) : "";
    $addition_overlap = isset($values['addition_overlap']) ? esc_attr($values['addition_overlap'][0]) : "";
    $addition_months = isset($values['addition_months']) ? esc_attr($values['addition_months'][0]) : "";

    $addition_days = isset($values['addition_days']) ? esc_attr($values['addition_days'][0]) : "";
    $addition_hours = isset($values['addition_hours']) ? esc_attr($values['addition_hours'][0]) : "";
    $addition_minutes = isset($values['addition_minutes']) ? esc_attr($values['addition_minutes'][0]) : "";
    $addition_is_unavailable = isset($values['addition_is_unavailable']) ? esc_attr($values['addition_is_unavailable'][0]) : "";
    $addition_available_from = isset($values['addition_unavailable_from']) ? esc_attr($values['addition_unavailable_from'][0]) : "";
    $addition_available_to = isset($values['addition_unavailable_to']) ? esc_attr($values['addition_unavailable_to'][0]) : "";
    if (!empty($values['extra_selected_base'][0])) {
        $res_base_final = maybe_unserialize($values['extra_selected_base'][0]);
        $res_base_final = maybe_unserialize($res_base_final);
    }
    if (!empty($values['extra_selected_seats'][0])) {
        $extra_selected_seats = maybe_unserialize($values['extra_selected_seats'][0]);
        $extra_selected_seats = maybe_unserialize($extra_selected_seats);
    }
    $extra_colour = isset($values['extra_colour']) ? esc_attr($values['extra_colour'][0]) : "";
    // }

    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'bkx_seat',
        'post_status' => 'publish',
        'suppress_filters' => false
    );
    $get_seat_array = get_posts($args);
    ?>
    <div class="error" id="error_list" style="display:none;"></div>
    <div class="active" id="base_name">
        <?php echo $addition_alias; ?> Price:
        <div class="plugin-description">
            <input name="addition_price" type="text" id="id_addition_price"
                   value="<?php echo isset($addition_price) ? $addition_price : ''; ?>">
        </div>
    </div>
    <div class="active" id="months_days_times">
        Is <?php echo $addition_alias; ?> time in days, hours & minutes:
        <div class="plugin-description">
            <select name="addition_time_option" id="id_addition_time_option" class="medium gfield_select" tabindex="4">
                <!--  <option value="Months" <?php //if($addition_time_option == "M"){ echo "selected='selected'"; }
                ?> >Months</option> -->
                <option value="Hour and Minutes" <?php if ($addition_time_option == "H") {
                    echo "selected='selected'";
                } ?>>Hour and Minutes
                </option>
                <!-- <option value="Days" <?php //if($addition_time_option == "D"){ echo "selected='selected'"; }
                ?>>Days</option> -->

            </select>
        </div>
    </div>
    <div class="active" id="hours_minutes">
        <?php echo $addition_alias; ?> Time In Hours and Minutes :
        <div class="plugin-description">
            <input name="addition_hours_minutes" type="text" value="<?php if (isset($addition_hours)) {
                echo $addition_hours;
            } else {
                echo 0;
            } ?>" id="id_addition_hours_minutes">
            <?php
            if (isset($addition_minutes))
                $additionMinute = $addition_minutes; ?>
            <select name="addition_minutes" id="id_addition_minutes">
                <option value=00 <?php if ($additionMinute == 15) {
                    echo "selected";
                } ?>>00
                </option>
                <option value=15 <?php if ($additionMinute == 15) {
                    echo "selected";
                } ?>>15
                </option>
                <option value=30 <?php if ($additionMinute == 30) {
                    echo "selected";
                } ?>>30
                </option>
                <option value=45 <?php if ($additionMinute == 45) {
                    echo "selected";
                } ?>>45
                </option>
            </select>
        </div>
    </div>
    <div class="active" id="overlap">
        Does this <?php echo $addition_alias; ?> require its own time bracket or can it overlap with
        other <?php echo $addition_alias; ?> :
        <div class="plugin-description">
            <ul class="gfield_radio" id="input_3_14">
                <li class="gchoice_14_0">
                    <input name="addition_overlap" type="radio" value="Own Time Bracket" id="choice_14_0"
                           tabindex="5" <?php if ($addition_overlap == "N") echo "checked='true'"; ?> >
                    <label for="choice_14_0">Own Time Bracket</label>
                </li>
                <li class="gchoice_14_1">
                    <input name="addition_overlap" type="radio" value="Overlap" id="choice_14_1"
                           tabindex="6" <?php if ($addition_overlap == "Y") echo "checked='true'"; ?> >
                    <label for="choice_14_1">Overlap</label>
                </li>
            </ul>
        </div>
    </div>
    <div class="active" id="months">
        Number of Months for <?php echo $addition_alias; ?> Time :
        <div class="plugin-description">
            <input name="addition_months" type="text" value="<?php echo $addition_months; ?>" id="id_addition_months">
        </div>
    </div>
    <div class="active" id="days">
        Number of Days for <?php echo $addition_alias; ?> Time :
        <div class="plugin-description">
            <input name="addition_days" type="text" value="<?php if (isset($addition_days)) {
                echo $addition_days;
            } ?>" id="id_addition_days">
        </div>
    </div>

    <div class="active" id="base_name">
        This <?php echo $addition_alias; ?> is available to the following <?php echo $base_alias; ?>'s :
        <div class="plugin-description">
            <ul class="gfield_checkbox" id="input_2_9">
                <li class="gchoice_9_1">
                    <input name="addition_base_all" type="checkbox" value="All" id="id_addition_base_all"
                           tabindex="12" <?php /*if($addition_bases=="All"){ echo "checked='checked'"; }*/ ?> >
                    <label for="choice_9_1">All</label>
                </li>
                <?php
                if (!empty($get_base_array) && !is_wp_error($get_base_array)) :
                    foreach ($get_base_array as $value) { ?>
                        <?php
                        $selected = false;
                        if (isset($res_base_final) && sizeof($res_base_final) > 0) {
                            $selected = false;
                            if (in_array($value->ID, $res_base_final)) {
                                $selected = true;
                            }
                        }
                        ?>
                        <li class="gchoice_9_2">
                            <input name="addition_base[]" type="checkbox" value="<?php echo $value->ID; ?>"
                                   tabindex="13" class="myCheckbox" <?php if ($selected == true) {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_9_2"><?php echo $value->post_title; ?></label>
                        </li>
                    <?php } endif; ?>
            </ul>
        </div>
    </div>


    <div class="active" id="seat_name">
        This <?php echo $addition_alias; ?> is available to the following <?php echo $alias_seat; ?> :
        <div class="plugin-description">
            <ul class="gfield_checkbox" id="input_2_9">
                <li class="gchoice_9_1">
                    <input name="seat_all" type="checkbox" value="All" id="id_seat_all" tabindex="12">
                    <label for="choice_9_1">All</label>
                </li>
                <?php
                if (!empty($get_seat_array) && !is_wp_error($get_seat_array)) :

                    $selected = '';
                    foreach ($get_seat_array as $value) { ?>
                        <?php
                        if (!empty($extra_selected_seats)) {
                            if (in_array($value->ID, $extra_selected_seats)) {
                                $selected = '1';
                            } else {
                                $selected = '';
                            }
                        }
                        ?>
                        <li class="gchoice_9_2">
                            <input name="seat_on_extra[]" type="checkbox" value="<?php echo $value->ID; ?>"
                                   tabindex="13" class="seat_checked" <?php if ($selected == '1') {
                                echo "checked='checked'";
                            } ?>>
                            <label for="choice_9_2"><?php echo $value->post_title; ?></label>
                        </li>
                    <?php } endif; ?>
            </ul>
        </div>
    </div>

    <p><strong><?php esc_html_e('Colour', 'bookingx'); ?></strong></p>
    <p><?php printf(esc_html__('%1$s Colour', 'bookingx'), $alias_seat); ?></p>
    <p><input type="text" name="extra_colour" id="id_extra_colour"
              value="<?php if (isset($extra_colour) && ($extra_colour != '')) {
                  echo $extra_colour;
              } ?>"/></p>

    <!--only for edit form  -->
    <div class="active" id="is_unavailable">
        Does the <?php echo $addition_alias; ?> Unavailable? :
        <div class="plugin-description">
            <input type="checkbox" name="addition_is_unavailable" id="id_addition_is_unavailable"
                   value="Yes" <?php if ($addition_is_unavailable == "Y") {
                echo "checked='checked'";
            } ?>>
            <label>Yes</label>
        </div>
    </div>
    <div class="active" id="unavailable_from">
        <?php echo $addition_alias; ?> Unavailable :
        <div class="plugin-description">
            <input type="text" name="addition_unavailable_from" id="id_addition_unavailable_from"
                   value="<?php if (isset($addition_available_from)) {
                       echo $addition_available_from;
                   } ?>"> From
        </div>
        <div class="plugin-description">
            <input type="text" name="addition_unavailable_to" id="id_addition_unavailable_to"
                   value="<?php if (isset($addition_available_to)) {
                       echo $addition_available_to;
                   } ?>"> To
        </div>
    </div>
    <?php
}

add_action('save_post', 'save_bkx_addition_metaboxes', 3, 10);
function save_bkx_addition_metaboxes($post_id, $post, $update)
{
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    // if our current user can't edit this post, bail
    if (!current_user_can('edit_post')) return;

    if ($post->post_type != 'bkx_addition') return;

    $additionPrice = sanitize_text_field($_POST['addition_price']);
    $additionMonthDaysTime = sanitize_text_field($_POST['addition_time_option']);
    $additionMonths = sanitize_text_field($_POST['addition_months']);
    $additionDays = sanitize_text_field($_POST['addition_days']);
    $additionHoursMinutes = sanitize_text_field($_POST['addition_hours_minutes']);
    $additionMinutes = sanitize_text_field($_POST['addition_minutes']);
    $additionOverlap = sanitize_text_field($_POST['addition_overlap']);
    $additionBaseAll = sanitize_text_field($_POST['addition_base_all']);
    $additionLocationDifferSeat = 'N';
    $additionLocationDifferBase = 'N';
    $additionIsUnavailable = sanitize_text_field($_POST['addition_is_unavailable']);
    $additionUnavailableFrom = sanitize_text_field($_POST['addition_unavailable_from']);
    $additionUnavailableTo = sanitize_text_field($_POST['addition_unavailable_to']);

    $extraSeatsValue = array_map('sanitize_text_field', wp_unslash($_POST['addition_base']));
    $checked_seats = array_map('sanitize_text_field', wp_unslash($_POST['seat_on_extra']));
    $extra_colour = sanitize_text_field($_POST['extra_colour']);

    if (isset($_POST['addition_is_unavailable']) && ($_POST['addition_is_unavailable'] == "Yes")) {
        $additionIsUnavailable = 'Y';
    }
    $additionTimeOption = '';
    if ($additionMonthDaysTime == "Months") {
        $additionTimeOption = 'M';
    }
    if ($additionMonthDaysTime == "Days") {
        $additionTimeOption = 'D';
    }
    if ($additionMonthDaysTime == "Hour and Minutes") {
        $additionTimeOption = 'H';
    }
    $additionOverlapVal = '';
    if ($additionOverlap == "Overlap") {
        $additionOverlapVal = 'Y';
    }
    if ($additionOverlap == "Own Time Bracket") {
        $additionOverlapVal = 'N';
    }
    $additionDifferSeat = '';
    if ($additionLocationDifferSeat == "Yes") {
        $additionDifferSeat = 'Y';
    }
    if ($additionLocationDifferSeat == "No") {
        $additionDifferSeat = 'N';
    }
    $additionDifferBase = '';
    if ($additionLocationDifferBase == "Yes") {
        $additionDifferBase = 'Y';
    }
    if ($additionLocationDifferBase == "No") {
        $additionDifferBase = 'N';
    }

    if (!empty($extra_colour))
        update_post_meta($post_id, 'extra_colour', $extra_colour);

    if (isset($additionPrice))
        update_post_meta($post_id, 'addition_price', $additionPrice);

    if (isset($additionTimeOption))
        update_post_meta($post_id, 'addition_time_option', $additionTimeOption);

    if (isset($additionMonths))
        update_post_meta($post_id, 'addition_months', $additionMonths);

    if (isset($additionDays))
        update_post_meta($post_id, 'addition_days', $additionDays);

    if (isset($additionHoursMinutes))
        update_post_meta($post_id, 'addition_hours', $additionHoursMinutes);

    if (isset($additionMinutes))
        update_post_meta($post_id, 'addition_minutes', $additionMinutes);

    if (isset($additionOverlapVal))
        update_post_meta($post_id, 'addition_overlap', $additionOverlapVal);

    if (isset($additionBaseAll))
        update_post_meta($post_id, 'addition_bases', $additionBaseAll);

    if (isset($additionDifferSeat))
        update_post_meta($post_id, 'addition_differ_seat', $additionDifferSeat);

    if (isset($additionDifferBase))
        update_post_meta($post_id, 'addition_differ_base', $additionDifferBase);

    if (isset($additionIsUnavailable))
        update_post_meta($post_id, 'addition_is_unavailable', $additionIsUnavailable);

    if (isset($additionUnavailableFrom))
        update_post_meta($post_id, 'addition_unavailable_from', $additionUnavailableFrom);

    if (isset($additionUnavailableTo))
        update_post_meta($post_id, 'addition_unavailable_to', $additionUnavailableTo);

    if(!empty($_POST['addition_base'])){
        foreach ($_POST['addition_base'] as $key => $base_id) {
                $base_data = get_post($base_id); 
                $base_slug[] = $base_data->post_name;
        }
        update_post_meta($post_id, 'extra_selected_base_slugs', $base_slug);
    }

    if(!empty($_POST['seat_on_extra'])){
        foreach ($_POST['seat_on_extra'] as $key => $seat_id) {
                $seat_data = get_post($seat_id); 
                $seat_slug[] = $seat_data->post_name;
        }
        update_post_meta($post_id, 'extra_selected_seats_slugs', $seat_slug);
    }

    if (!empty($extraSeatsValue))
        update_post_meta($post_id, 'extra_selected_base', $_POST['addition_base']);

    if (!empty($checked_seats))
        update_post_meta($post_id, 'extra_selected_seats', $_POST['seat_on_extra']);


}

add_filter('manage_bkx_addition_posts_columns', 'bkx_addition_columns_head');
add_action('manage_bkx_addition_posts_custom_column', 'bkx_addition_columns_content', 10, 2);

/**
 *
 * @param array $defaults
 * @return string
 */
function bkx_addition_columns_head($defaults)
{
    $defaults['display_shortcode_all'] = 'Shortcode [bookingx display="rows" extra-id="all"] (display : rows|columns)';
    return $defaults;
}

/**
 *
 * @param type $column_name
 * @param type $post_ID
 */
function bkx_addition_columns_content($column_name, $post_ID)
{
    if ($column_name == 'display_shortcode_all') {
        echo '[bookingx extra-id="' . $post_ID . '" description="yes" image="yes" extra-info="no"]';
    }
}
 

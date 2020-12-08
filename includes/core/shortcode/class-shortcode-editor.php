<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class BKXShortcode_Tinymce
{
    var $enable_editor; // return 1 == enable or 0 == disable editor shortcode

    public function __construct()
    {
        add_action('admin_init', array($this, 'bkx_shortcode_button'));
        add_action('admin_head', array($this, 'bkx_add_simple_buttons'));
        add_action('admin_footer', array($this, 'bkx_get_shortcodes'));
    }

    /**
     * Create a shortcode button for tinymce
     *
     * @return [type] [description]
     */
    public function bkx_shortcode_button()
    {
        $enable_editor = bkx_crud_option_multisite('enable_editor');
        $this->enable_editor = $enable_editor;

        if (current_user_can('edit_posts') && current_user_can('edit_pages') &&
            ($this->enable_editor == 1 || $this->enable_editor == '1')) {
            add_filter('mce_external_plugins', array($this, 'bkx_add_buttons'));
            add_filter('mce_buttons', array($this, 'bkx_register_buttons'));
        }
    }

    /**
     * Add new Javascript to the plugin scrippt array
     *
     * @param Array $plugin_array - Array of scripts
     *
     * @return Array
     */
    public function bkx_add_buttons($plugin_array)
    {
        $plugin_array['pushortcodes'] = BKX_PLUGIN_DIR_URL . '/admin/js/shortcode-editor/shortcode-tinymce-button.js?ver=' . BKX_PLUGIN_VER;
        return $plugin_array;
    }

    /**
     * Add new button to tinymce
     *
     * @param Array $buttons - Array of buttons
     *
     * @return Array
     */
    public function bkx_register_buttons($buttons)
    {
        array_push($buttons, 'separator', 'pushortcodes');
        return $buttons;
    }

	/**
	 * Add shortcode JS to the page
	 *
	 * @return void
	 */
    public function bkx_get_shortcodes()
    {
        if (current_user_can('edit_posts') && current_user_can('edit_pages') &&
            ($this->enable_editor != 1 || $this->enable_editor != '1'))
            return;

        $shortcode_tags = $this->get_shortcodes();

        echo '<script type="text/javascript">
        var shortcodes_button = [];';
        $count = 0;
        if (!empty($shortcode_tags)) {
            foreach ($shortcode_tags as $tag => $code) {
                echo "shortcodes_button['{$tag}'] = '{$code}';";
                $count++;
            }
        }
        echo '</script>';
    }

    function bkx_add_simple_buttons()
    {
        if (current_user_can('edit_posts') && current_user_can('edit_pages') &&
            ($this->enable_editor != 1 || $this->enable_editor != '1'))
            return;
        wp_print_scripts('quicktags');
        $output = "<script type='text/javascript'>\n
                /* <![CDATA[ */ \n";
        $buttons = array();

	    if(is_admin() ) {
		    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
		    $current_screen = get_current_screen();
		    if(!empty($current_screen) && !is_wp_error($current_screen)){
			    $location = $current_screen->base;
		    }
	    }

        $shortcode_tags = $this->get_shortcodes();
        if (!empty($shortcode_tags)) {
            foreach ($shortcode_tags as $key => $value) {
	            if(isset($location) && $location == 'post'){
		            $buttons[] = array('name' => '' . $key . '',
		                               'options' => array(
			                               'display_name' => '' . $value . '',
			                               'open_tag' => '\n[' . $key . ']',
			                               //'close_tag' => '[/'.$key.']\n',
			                               'key' => ''
		                               ));

	            }elseif(isset($location) && $location == 'bkx_booking_page_bkx-setting'){
		            $buttons[] = array('name' => '' . $key . '',
		                               'options' => array(
			                               'display_name' => '' . $value . '',
			                               'open_tag' => '\n{' . $key . '}',
			                               //'close_tag' => '[/'.$key.']\n',
			                               'key' => ''
		                               ));
	            }

            }
        }
        for ($i = 0; $i <= (count($buttons) - 1); $i++) {
            $output .= "edButtons[edButtons.length] = new edButton('bkx_{$buttons[$i]['name']}'
                        ,'{$buttons[$i]['options']['display_name']}'
                        ,'{$buttons[$i]['options']['open_tag']}'
                        ,'{$buttons[$i]['options']['key']}'
                    ); \n";
        }
        $output .= "\n /* ]]> */ \n
			var bkx_shortcode_obj = {
			    'location': '{$location}',
			};
                </script>";
        echo $output;
    }

	/**
	 * @return mixed|void
	 */
    public function booking_email_shortcode(){

	    $shortcode_tags = array();
	    $shortcode_tags['fname'] = esc_html('First name', 'bookingx');
	    $shortcode_tags['lname'] = esc_html('Last name', 'bookingx');
	    $shortcode_tags['phone'] = esc_html('Phone', 'bookingx');
	    $shortcode_tags['email'] = esc_html('Email', 'bookingx');
	    $shortcode_tags['total_price'] = esc_html('Total price', 'bookingx');
	    $shortcode_tags['order_id'] = esc_html('Order id', 'bookingx');
	    $shortcode_tags['txn_id'] = esc_html('Transaction id', 'bookingx');
	    $shortcode_tags['seat_name'] = esc_html('Resource name', 'bookingx');
	    $shortcode_tags['base_name'] = esc_html('Service name', 'bookingx');
	    $shortcode_tags['additions_list'] = esc_html('Extra list', 'bookingx');
	    $shortcode_tags['time_of_booking'] = esc_html('Time of booking', 'bookingx');
	    $shortcode_tags['date_of_booking'] = esc_html('Date of booking', 'bookingx');
	    $shortcode_tags['location_of_booking'] = esc_html('Location of booking', 'bookingx');
	    $shortcode_tags['amount_paid'] = esc_html('Amount paid', 'bookingx');
	    $shortcode_tags['amount_pending'] = esc_html('Amount pending', 'bookingx');
	    $shortcode_tags['business_name'] = esc_html('Business name', 'bookingx');
	    $shortcode_tags['business_phone'] = esc_html('Business phone', 'bookingx');
	    $shortcode_tags['business_email'] = esc_html('Business email', 'bookingx');
	    $shortcode_tags['booking_status'] = esc_html('Booking status', 'bookingx');
	    $shortcode_tags['booking_edit_url'] = esc_html('Edit Booking Url', 'bookingx');
	    $shortcode_tags = apply_filters('bkx_booking_email_shortcodes', $shortcode_tags, $this);

	    return $shortcode_tags;
    }

    public function main_shortcodes(){
	    $shortcode_tags = array();
	    $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');
	    $base_alias = bkx_crud_option_multisite('bkx_alias_base');
	    $extra_alias = bkx_crud_option_multisite('bkx_alias_addition');

	    $shortcode_tags['bkx_booking_form'] = esc_html('Booking Form', 'bookingx');
	    $shortcode_tags['bookingx seat-id="all"'] = esc_html(ucwords($seat_alias). " Listing", 'bookingx');
	    $shortcode_tags['bookingx base-id="all"'] = esc_html(ucwords($base_alias). " Listing", 'bookingx');
	    $shortcode_tags['bookingx extra-id="all"'] = esc_html(ucwords($extra_alias). " Listing", 'bookingx');
	    $shortcode_tags['bkx_dashboard'] = esc_html("Dashboard", 'bookingx');
	    $shortcode_tags = apply_filters('bkx_main_shortcodes', $shortcode_tags, $this);

	    return $shortcode_tags;
    }

    public function get_shortcodes()
    {
	    if(is_admin() ) {
		    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
		    $current_screen = get_current_screen();
		    if(!empty($current_screen) && !is_wp_error($current_screen)){
			    $location = $current_screen->base;
		    }
	    }
	    if(isset($location) && $location == 'post'){
		    $shortcode_tags = $this->main_shortcodes();
	    }elseif(isset($location) && $location == 'bkx_booking_page_bkx-setting'){
		    $shortcode_tags = $this->booking_email_shortcode();
	    }else{
		    $shortcode_tags = $this->main_shortcodes();
	    }

        return $shortcode_tags;
    }
}

new BKXShortcode_Tinymce();
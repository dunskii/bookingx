<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.7.6.4
 * @package    Bookingx
 * @subpackage Bookingx/includes
 * @author     Dunskii Web Services <divyang@dunskii.com>
 */
class Bookingx_Activator
{
    /**
     * Short Description. (use period)
     * Long Description.
     * @since      0.7.6.4
     */
    public function activate(){
        add_action('init', array($this, 'bkx_basic_set_up'));
    }

    /**
     * Quick Settings Related to Post Type name and others
     */
    public function bkx_basic_set_up(){
        flush_rewrite_rules();
        add_role('resource', __('Resource'), array('read' => true, 'edit_posts' => true, 'delete_posts' => false));
        bkx_crud_option_multisite('bkx_alias_seat', esc_html('Resource', 'bookingx'), 'update');
        bkx_crud_option_multisite('bkx_alias_base', esc_html('Service', 'bookingx'), 'update');
        bkx_crud_option_multisite('bkx_alias_addition', esc_html('Extra', 'bookingx'), 'update');
        bkx_crud_option_multisite('bkx_alias_notification', esc_html('Extra', 'Notification'), 'update');
        bkx_crud_option_multisite('bkx_notice_time_extended_text_alias', esc_html('How many times you want to extend this service?', 'bookingx'), 'update');
        bkx_crud_option_multisite('bkx_label_of_step1', esc_html('Please select what you would like to book', 'bookingx'), 'update');
    }
}
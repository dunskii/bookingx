<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Shortcode_Tinymce
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
        $enable_editor = crud_option_multisite('enable_editor');
        $this->enable_editor = $enable_editor;
         
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') && 
            ($this->enable_editor == 1 || $this->enable_editor == '1') )
        {
            add_filter( 'mce_external_plugins', array($this, 'bkx_add_buttons' ));

            add_filter( 'mce_buttons', array($this, 'bkx_register_buttons' ));
        }
    }

    /**
     * Add new Javascript to the plugin scrippt array
     *
     * @param  Array $plugin_array - Array of scripts
     *
     * @return Array
     */
    public function bkx_add_buttons( $plugin_array )
    {
        $plugin_array['pushortcodes'] = plugin_dir_url( __DIR__ ) . 'js/shortcode-tinymce-button.js?v='.rand(9,999999);
        return $plugin_array;
    }

    /**
     * Add new button to tinymce
     *
     * @param  Array $buttons - Array of buttons
     *
     * @return Array
     */
    public function bkx_register_buttons( $buttons )
    {
        array_push( $buttons, 'separator', 'pushortcodes' );
        return $buttons;
    }

    /**
     * Add shortcode JS to the page
     *
     * @return HTML
     */
    public function bkx_get_shortcodes()
    {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') && 
            ($this->enable_editor != 1 || $this->enable_editor != '1') )
                return;
            
        $shortcode_tags =  $this->get_shortcodes();

        echo '<script type="text/javascript">
        var shortcodes_button = new Array();';

        $count = 0;

        if(!empty($shortcode_tags)){

            foreach($shortcode_tags as $tag => $code)
            {
                echo "shortcodes_button['{$tag}'] = '{$code}';";
                //echo "shortcodes_button[{$count}] = '{$code}';";
                $count++;
            }
        }
        echo '</script>';
    }

    function bkx_add_simple_buttons(){

        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') && 
            ($this->enable_editor != 1 || $this->enable_editor != '1') )
                return;

                wp_print_scripts( 'quicktags' );
                $output = "<script type='text/javascript'>\n
                /* <![CDATA[ */ \n";

                $buttons = array();

                $shortcode_tags =  $this->get_shortcodes();

                if(!empty($shortcode_tags)){

                    foreach ($shortcode_tags as $key => $value) {

                        $buttons[] = array('name' => ''.$key.'',
                                'options' => array(
                                    'display_name' => ''.$value.'',
                                    'open_tag' => '\n['.$key.']',
                                    'close_tag' => '[/'.$key.']\n',
                                    'key' => ''
                                ));
                    }
                }

                for ($i=0; $i <= (count($buttons)-1); $i++) {
                    $output .= "edButtons[edButtons.length] = new edButton('bkx_{$buttons[$i]['name']}'
                        ,'{$buttons[$i]['options']['display_name']}'
                        ,'{$buttons[$i]['options']['open_tag']}'
                        ,'{$buttons[$i]['options']['close_tag']}'
                        ,'{$buttons[$i]['options']['key']}'
                    ); \n";
                }

                $output .= "\n /* ]]> */ \n
                </script>";
                echo $output;
            }

    public function get_shortcodes()
    {
        $shortcode_tags = array();
        $shortcode_tags['fname']                = 'First name';
        $shortcode_tags['lname']                = 'Last name';
        $shortcode_tags['total_price']          = 'Total price';
        $shortcode_tags['order_id']             = 'Order id';
        $shortcode_tags['txn_id']               = 'Transaction id';
        $shortcode_tags['seat_name']            = 'Resource name';
        $shortcode_tags['base_name']            = 'Service name';
        $shortcode_tags['additions_list']       = 'Extra list';
        $shortcode_tags['time_of_booking']      = 'Time of booking';
        $shortcode_tags['date_of_booking']      = 'Date of booking';
        $shortcode_tags['location_of_booking']  = 'Location of booking';
        $shortcode_tags['amount_paid']          = 'Amount paid';
        $shortcode_tags['amount_pending']       = 'Amount pending';
        $shortcode_tags['business_name']        = 'Business name';
        $shortcode_tags['business_phone']       = 'Business phone';
        $shortcode_tags['business_email']       = 'Business email';
        $shortcode_tags['booking_status']       = 'Booking status';

        $shortcode_tags = apply_filters( 'bkx_get_more_shortcodes', $shortcode_tags, $this );

        return $shortcode_tags;
    }
}
new Shortcode_Tinymce();
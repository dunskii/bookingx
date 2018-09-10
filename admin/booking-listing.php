<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @return array
 */
function bkx_booking_columns_data()
{
    $seat_alias     = bkx_crud_option_multisite( 'bkx_alias_seat' );
    $base_alias     = bkx_crud_option_multisite( 'bkx_alias_base' );
    $addition_alias = bkx_crud_option_multisite( 'bkx_alias_addition' );
    
    $columns                    = array();
    $columns[ 'cb' ]            = '<input type="checkbox" />';
    $columns[ 'order_status' ]  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'bookingx' ) . '">' . esc_attr__( 'Current Status', 'bookingx' ) . '</span>';
    $columns[ 'booking_date' ]  = __( 'Date', 'bookingx' );
    $columns[ 'booking_time' ]  = __( 'Time', 'bookingx' );
    $columns[ 'duration' ]      = __( 'Duration', 'bookingx' );
    $columns[ 'client_name' ]   = __( 'Name', 'bookingx' );
    $columns[ 'client_phone' ]  = __( 'Phone', 'bookingx' );
    $columns[ 'seat' ]          = __( $seat_alias, 'bookingx' );
    $columns[ 'service' ]       = __( $base_alias, 'bookingx' );
    $columns[ 'extra' ]         = __( '# of ' . $addition_alias, 'bookingx' );
    $columns[ 'order_total' ]   = __( 'Total', 'bookingx' );
    $columns[ 'order_actions' ] = __( 'Actions', 'bookingx' );
    $columns[ 'order_view' ]    = __( 'View', 'bookingx' );

    $columns = apply_filters( 'bkx_dashboard_booking_columns', $columns );

    return $columns;
}
add_filter( 'manage_bkx_booking_posts_columns', 'bkx_booking_columns' , 99, 2 );

/**
 * @param $existing_columns
 * @return array
 */
function bkx_booking_columns($existing_columns )
{
    
    $seat_alias     = bkx_crud_option_multisite( 'bkx_alias_seat' );
    $base_alias     = bkx_crud_option_multisite( 'bkx_alias_base' );
    $addition_alias = bkx_crud_option_multisite( 'bkx_alias_addition' );
    unset($existing_columns);
    $columns                    = array();
    $columns[ 'cb' ]            = '<input type="checkbox" />';
    $columns[ 'order_status' ]  = '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'bookingx' ) . '">' . esc_attr__( 'Current Status', 'bookingx' ) . '</span>';
    $columns[ 'booking_date' ]  = __( 'Date', 'bookingx' );
    $columns[ 'booking_time' ]  = __( 'Time', 'bookingx' );
    $columns[ 'duration' ]      = __( 'Duration', 'bookingx' );
    $columns[ 'client_name' ]   = __( 'Name', 'bookingx' );
    $columns[ 'client_phone' ]  = __( 'Phone', 'bookingx' );
    $columns[ 'seat' ]          = __( $seat_alias, 'bookingx' );
    $columns[ 'service' ]       = __( $base_alias, 'bookingx' );
    $columns[ 'extra' ]         = __( '# of ' . $addition_alias, 'bookingx' );
    $columns[ 'order_total' ]   = __( 'Total', 'bookingx' );
    $columns[ 'order_actions' ] = __( 'Actions', 'bookingx' );
    $columns[ 'order_view' ]    = __( 'View', 'bookingx' );

    // Dashboard Column manage to show data ( Fetch Data from Other Setting Tab)
    $bkx_dashboard_column_selected = bkx_crud_option_multisite("bkx_dashboard_column");
    
    $bkx_booking_columns_data = bkx_booking_columns_data();
    if(!empty($bkx_booking_columns_data)){
        foreach ($bkx_booking_columns_data as $key => $booking_columns) {
            if ( $key != "cb" && !empty($bkx_dashboard_column_selected) && !in_array($key, $bkx_dashboard_column_selected) )
            {
                unset($columns[$key]);
            }         
        }
    }
    $columns = apply_filters( 'bkx_dashboard_booking_columns', $columns );

    return $columns;
}

add_action( 'manage_bkx_booking_posts_custom_column', 'render_bkx_booking_columns' );
/**
 * @param $column
 */
function render_bkx_booking_columns($column )
{
    global $post, $wpdb;

    $orderObj   = new BkxBooking();
    $order_meta = $orderObj->get_order_meta_data( $post->ID );
    switch ( $column ) {
        case 'order_status':
            $order_status = $orderObj->get_order_status( $post->ID );
            echo sprintf( __( '%s', 'bookingx' ), $order_status );
            break;
        case 'booking_date':
            $date_format = get_option( 'date_format' );
            echo sprintf( __( '%s', 'bookingx' ), date( $date_format, strtotime( $order_meta[ 'booking_date' ] ) ) );
            break;
        case 'booking_time':
            echo sprintf( __( '%s', 'bookingx' ), date( 'h:i A', strtotime( $order_meta[ 'booking_start_date' ] ) ), date( 'h:i A ', strtotime( $order_meta[ 'booking_end_date' ] ) ) );
            break;
        case 'duration':
            echo sprintf( __( '%s', 'bookingx' ), $order_meta['total_duration'] );
            break;
        case 'client_name':
            echo sprintf( __( '%s %s', 'bookingx' ), $order_meta[ 'first_name' ], $order_meta[ 'last_name' ] );
            break;
        case 'client_phone':
            $client_phone = $order_meta[ 'phone' ];
            echo sprintf( __( '%s', 'bookingx' ), $client_phone );
            break;
        case 'seat':
            $seat_arr = $order_meta[ 'seat_arr' ];
            printf( __( '%s', 'bookingx' ), $seat_arr[ 'title' ] );
            break;
        case 'service':
            $base_arr = $order_meta[ 'base_arr' ];
            printf( __( '%s', 'bookingx' ),$base_arr[ 'title' ] );
            break;
        case 'extra':
            if(isset($order_meta[ 'extra_arr' ]) && $order_meta[ 'extra_arr' ] !=""){
                $extra_arr = $order_meta[ 'extra_arr' ];
                echo sizeof($extra_arr);
            }
            break;
        case 'order_total':
            $currency    = isset( $order_meta[ 'currency' ] ) && $order_meta[ 'currency' ] != '' ? $order_meta[ 'currency' ] : bkx_get_current_currency();
            $total_price = $order_meta[ 'total_price' ];
             echo sprintf( __( '%s%d %s', 'bookingx' ), $currency, $total_price, get_option( 'currency_option' ) );
            break;
        case 'order_actions':
             $order_status = strtolower($orderObj->get_order_status( $post->ID ) );
             $exclude_status = array('completed','missed','cancelled');
             $pending   = ($order_status == 'pending') ? "selected" : '';
             $ack       = ($order_status == 'acknowledged') ? "selected" : '';
             $completed = ($order_status == 'completed') ? "selected" : '';
             $missed    = ($order_status == 'missed') ? "selected" : '';
             $cancelled = ($order_status == 'cancelled') ? "selected" : '';

            $business_address_1 = bkx_crud_option_multisite("bkx_business_address_1");
            $business_address_2 = bkx_crud_option_multisite("bkx_business_address_2");
            $bkx_business_city = bkx_crud_option_multisite("bkx_business_city");
            $bkx_business_state = bkx_crud_option_multisite("bkx_business_state");
            $bkx_business_zip = bkx_crud_option_multisite("bkx_business_zip");
            $bkx_business_country = bkx_crud_option_multisite("bkx_business_country");

            $full_address = "{$business_address_1},{$business_address_2},{$bkx_business_city},{$bkx_business_state},{$bkx_business_zip},{$bkx_business_country}";
            if(isset($post_json)){
                echo '<input type="hidden" id="post_ids" value="'.$post_json.'">';
            }
            echo '<input type="hidden" id="address_val'.$post->ID.'" value="'.$full_address.'">';
            $status_dropdown = '';
            $status_dropdown .= '<select name="bkx-action" class="bkx_action_status" style="width:91px;" id="bulk-action-selector-top">';
            $status_dropdown .= '<option value="'.$post->ID.'_pending" '.$pending.'>Pending</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_ack" '.$ack .'>Acknowledged</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_completed" '.$completed.'>Completed</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_missed" '.$missed.'>Missed</option>';
            $status_dropdown .= '<option value="'.$post->ID.'_cancelled" '.$cancelled.'>Cancelled</option></select>';
            if(!in_array($order_status, $exclude_status)){ echo $status_dropdown;}else{ 
            ?>
             <script type="text/javascript">
             jQuery(function() {
                jQuery("#cb-select-<?php echo $post->ID;?>").attr('disabled','disabled');
             });
             </script>
            <?php
                echo ucwords($order_status); 
            }
        break;

        case 'order_view' :

echo sprintf( __( '<span class="view_href_booking-'.$post->ID.'"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx' ), 'javascript:view_summary(\''.$post->ID.'\',\''.$order_meta[ 'first_name' ].' '.$order_meta[ 'last_name' ].'\')','View', 'View' );

echo sprintf( __( '<span class="close_booking-'.$post->ID.'" style="display:none;"><a href="%s" title="%s" style="padding: 0 5px;">%s</a></span>', 'bookingx' ), 'javascript:close_booking(\''.$post->ID.'\')','Close', 'Close' );

echo '<div class="loader-'.$post->ID.'" style="display:none;"> Please wait..</div>';
            break;
    }
}

/**
 * @param $permalink
 * @param $post
 * @return string
 */
function bkx_change_view_link($permalink, $post ) {
    if( $post->post_type == 'bkx_booking' ) { 
         $orderObj   = new BkxBooking();
        $order_meta = $orderObj->get_order_meta_data( $post->ID );

$permalink = 'javascript:view_summary(\''.$post->ID.'\',\''.$order_meta[ 'first_name' ].' '.$order_meta[ 'last_name' ].'\')';
    }
    return $permalink;
}
add_filter('post_type_link',"bkx_change_view_link",10,2);

add_action( 'admin_footer', 'bkx_booking_bulk_admin_footer', 10 );
/**
 *
 */
function bkx_booking_bulk_admin_footer()
{
    global $post_type;

    if ( 'bkx_booking' == $post_type ) {
        echo '<style> 
        .column-order_status{ display:block !important;}
                #order_actions { width: 80px; }
        #calendar {
                max-width: 900px;
                margin: 0 auto;
                background: #fff;
                padding: 20px;
        }
        .bkx-order_summary, .bkx-order_summary_full {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            margin : 3px;
            position: relative;
        
        }
        .search_by_dates_section input {
            width: 93px;
            margin-right: 5px;
        }
</style>';
$bkx_view_id = "";
$bkx_view_name = "";
if(isset($_GET["view"])){
    $bkx_view_id = sanitize_text_field($_GET["view"]);
}
if(isset($_GET["bkx_name"])){
    $bkx_view_name = sanitize_text_field($_GET["bkx_view_name"]);
}
?>
<script type="text/javascript">
     function view_summary( post_id, name, colour_json ){
        
        console.log(colour_json);
        jQuery('.loader-'+ post_id).show();
            var data = {
                'action': 'bkx_action_view_summary',
                'post_id': post_id
            };
        setTimeout(function(){  initialize(post_id); codeAddress(post_id);draw_rectangle(post_id, name); }, 500);
            jQuery.post(ajaxurl, data, function(response) {

                jQuery('.loader-'+ post_id).hide();
                jQuery('.close_booking-'+ post_id).show();
                jQuery('.view_href_booking-'+ post_id).hide();
                var postobj = jQuery('#post-'+ post_id);
                jQuery( "<tr class='view_summary view_summary-"+post_id+"'>"+response ).insertAfter( postobj).slideDown('slow');
            });
     }
     function close_booking(post_id) {
                jQuery('.close_booking-'+ post_id).hide();
                jQuery('.view_href_booking-'+ post_id).show();
                jQuery('.view_summary-'+ post_id).hide();
    }
    jQuery(function() {
        var bkx_view_id = '<?php $bkx_view_id; ?>';
        var bkx_view_name = '<?php echo $bkx_view_name; ?>';
        if( bkx_view_id  )
        {
            jQuery('#post-'+ bkx_view_id).focus();
            view_summary( bkx_view_id, bkx_view_name );
        }
        jQuery('.search_by_dates').on('change', function() {
            var search_by_dates = jQuery(this).val();
            if(search_by_dates == 'choose_date'){
                jQuery('.search_by_dates_section').html('');
                jQuery('.search_by_dates_section').append('<input type="hidden" name="search_by_dates" value="choose_date" >');
                jQuery('.search_by_dates_section').append('<input type="text" name="search_by_selected_date" placeholder="Select a date" class="search_by_selected_date">');
                jQuery( ".search_by_selected_date" ).datepicker(); 
            }  
        });
       var span_trash =  jQuery('.row-actions').find('span.trash').find('a');
        jQuery(".listing_view").nextAll('#post-query-submit').remove();
        jQuery( span_trash ).click(function() {
          if(confirm('Are you sure want to Cancel this booking?')){}
          else{ return false;}
        });

        jQuery('.row-actions').find('span.trash').find('a').text('Cancel');
        jQuery('.row-actions').find('span.edit').find('a').text('Reschedule');
        jQuery('<option>').val('mark_pending').text('<?php _e( 'Pending', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_ack').text('<?php _e( 'Acknowledged', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_completed').text('<?php _e( 'Completed', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_missed').text('<?php _e( 'Missed', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_cancelled').text('<?php _e( 'Cancelled', 'bookingx' );?>').appendTo('select[name="action"]');

        jQuery('<option>').val('mark_pending').text('<?php _e( 'Pending', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_ack').text('<?php _e( 'Acknowledged', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_completed').text('<?php _e( 'Completed', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_missed').text('<?php _e( 'Missed', 'bookingx' );?>').appendTo('select[name="action2"]');

        jQuery('<option>').val('mark_cancelled').text('<?php _e( 'Cancelled', 'bookingx' );?>').appendTo('select[name="action2"]');


      });

     jQuery('.bkx_action_status').on('change', function() {
        if(confirm('Are you sure want to change status?')){
            var bkx_action_status = jQuery(this).val();
            var data = {
                'action': 'bkx_action_status',
                'status': bkx_action_status
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                 location.reload();
            });
        }else{
            return false;
        }
    });
     <?php
     $listing_view = isset($_GET['listing_view'])? sanitize_text_field($_GET['listing_view']):'';
     if($listing_view == 'weekly' || $listing_view == 'monthly'){ 
         bkx_generate_listing_view($listing_view);
        ?>
        jQuery("<div class='weekly-data' id='calendar'></div>").insertBefore( ".wp-list-table" ).slideDown('slow');
        jQuery(".wp-list-table").hide();
    <?php } ?>
      </script>
      <?php
       
    }
}
add_filter('months_dropdown_results', '__return_empty_array');
add_action( 'restrict_manage_posts', 'bkx_booking_search_by_dates', 2, 10 );
/**
 * @param $post_type
 * @param $which
 */
function bkx_booking_search_by_dates($post_type, $which){
    //only add filter to post type you want
    if ('bkx_booking' == $post_type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $values = array(
            'All dates' => '', 
            'Today' => 'today', 
            'Tomorrow' => 'tomorrow',
            'This Week' => 'this_week',
            'Next Week' => 'next_week',
            'This Month' => 'this_month',
            'Select a Date' => 'choose_date'
        );
        $search_by_dates = isset($_GET['search_by_dates'])? sanitize_text_field($_GET['search_by_dates']):'';
 
        if($search_by_dates == 'choose_date'){
           $search_by_dates = '';
        }
        ?>
        <div class="search_by_dates_section" style="float: left;">
            <select name="search_by_dates" class="search_by_dates">
            <?php
                foreach ($values as $label => $value) {
                    printf
                        ('<option value="%s"%s>%s</option>',
                            $value,
                            $value == $search_by_dates? ' selected="selected"':'',
                            $label
                        );
                    }
            ?>
            </select>
        </div>
         <input type="submit" name="filter_action" style="float: left;" id="post-query-submit" class="button" value="Filter">
        <?php    
    }
}
add_action( 'restrict_manage_posts', 'bkx_booking_seat_view', 2, 10 );
/**
 * @param $post_type
 * @param $which
 */
function bkx_booking_seat_view($post_type, $which){
if ('bkx_booking' == $post_type){

    $args = array(
    'posts_per_page'   => -1,
    'post_type'        => 'bkx_seat',
    'post_status'      => 'publish',
    'suppress_filters' => false
    );
    $get_seat_array = get_posts( $args );
    $alias_seat = bkx_crud_option_multisite('bkx_alias_seat');
    if(!empty($get_seat_array)){

        foreach ($get_seat_array as $key => $seat_data) {
            $seat_obj[$seat_data->ID] = $seat_data->post_title ;
        }
    }

     if(!empty($seat_obj)) : ?>
        <select name="seat_view" class="seat_view">
        <option> Select <?php echo $alias_seat;?> </option>
        <?php
            $seat_view = isset($_GET['seat_view'])? sanitize_text_field($_GET['seat_view']):'';
            foreach ($seat_obj as $seat_id => $seat_name) {
                printf
                    ('<option value="%s"%s>%s</option>',
                        $seat_id,
                        $seat_id == $seat_view? ' selected="selected"':'',
                        $seat_name
                    );
                }
        ?>
        </select>
        <input type="submit" name="filter_action" style="float: left;" class="button seat_view" value="view"> 
        <?php endif; 
    }
}

add_action( 'restrict_manage_posts', 'bkx_booking_listing_view', 2, 10 );
/**
 * @param $post_type
 * @param $which
 */
function bkx_booking_listing_view($post_type, $which){
    //only add filter to post type you want
    if ('bkx_booking' == $post_type){
        
        $values = array(
            'Agenda' => 'agenda', 
            'Weekly' => 'weekly',
            'Monthly' => 'monthly'
            );
        ?>
        <select name="listing_view" class="listing_view">
        <?php
            $listing_view = isset($_GET['listing_view'])? sanitize_text_field($_GET['listing_view']):'';
            foreach ($values as $label => $value) {
                printf
                    ('<option value="%s"%s>%s</option>',
                        $value,
                        $value == $listing_view? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <input type="submit" name="filter_action" style="float: left;" class="button listing_view" value="Change view"> 
        <?php            
    }
}

/**
 * @param $type
 */
function bkx_generate_listing_view($type )
{
    if($type == 'weekly' || $type == 'monthly' ){
      
    $defaultView = ($type) && $type == 'weekly' ?  'agendaWeek' : 'month';
    $bkx_calendar_json_data = bkx_crud_option_multisite('bkx_calendar_json_data');
    ?>
    jQuery(document).ready(function() {
         
        jQuery('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            defaultView: '<?php echo $defaultView;?>',
            defaultDate: '<?php echo date('Y-m-d'); ?>',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            events: [<?php echo $bkx_calendar_json_data; ?>]
        });
    });
    <?php
    }
}
// Disable wpseo_use_page_analysis
add_filter( 'wpseo_use_page_analysis', function( $arg ) { return ''; } );
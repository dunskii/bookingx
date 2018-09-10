<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_enqueue_style( 'bodikea-css', BKX_PLUGIN_DIR_URL.'css/bookingx-style.css', false, BKX_PLUGIN_VER );
wp_enqueue_style( 'shortcodes', BKX_PLUGIN_DIR_URL.'css/shortcodes.css', false, false );
wp_enqueue_style( 'shortcodes_responsive', BKX_PLUGIN_DIR_URL.'css/shortcodes_responsive.css', false, false );
wp_enqueue_script( "et-shortcodes-js", BKX_PLUGIN_DIR_URL."frontend/js/et_shortcodes_frontend.dev.js?ver=".BKX_PLUGIN_VER, false, BKX_PLUGIN_VER, false );
wp_localize_script( 'et-shortcodes-js', 'bkx_shortcodes_strings', array( 'previous' => __( 'Previous', 'Bookingx' ), 'next' => __( 'Next', 'Bookingx' ) ) );

require_once(BKX_PLUGIN_DIR_PATH.'css/generate_css.php');
$current_user = wp_get_current_user();
$bkx_seat_role = bkx_crud_option_multisite('bkx_seat_role');

$account_page = get_page_by_title( "My Account" );
$my_account_id = $account_page->ID;

    $seat_alias = bkx_crud_option_multisite('bkx_alias_seat');      
    $base_alias = bkx_crud_option_multisite('bkx_alias_base');
    $addition_alias = bkx_crud_option_multisite('bkx_alias_addition');
    $business_address_1 = bkx_crud_option_multisite("bkx_business_address_1");
    $business_address_2 = bkx_crud_option_multisite("bkx_business_address_2");
    $bkx_business_city = bkx_crud_option_multisite("bkx_business_city");
    $bkx_business_state = bkx_crud_option_multisite("bkx_business_state");
    $bkx_business_zip = bkx_crud_option_multisite("bkx_business_zip");
    $bkx_business_country = bkx_crud_option_multisite("bkx_business_country");
    $full_address = "{$business_address_1},{$bkx_business_city},{$bkx_business_state},{$bkx_business_zip},{$bkx_business_country}";

    $BookedRecords= new BkxBooking();

    if ( !($current_user instanceof WP_User) )
      return;

    $roles = $current_user->roles;
    $data = $current_user->data;

    if (isset( $roles['0'] ) && $roles['0']=='administrator') {
        $search['seat_id'] = get_current_user_id();
    }
    else if(isset( $roles['0'] ) && $roles['0']== $bkx_seat_role)
    {
      $get_seat_post_id = get_user_meta($current_user->ID,'seat_post_id',true);
      $search['seat_id'] = $get_seat_post_id;
      $search['role'] = $roles['0'];
    }
    else
    {
      $search['user_id'] = $current_user->ID;
      $search['role'] = $roles['0'];
    }

    $bkx_cust_name = get_user_meta( $current_user->ID, 'bkx_cust_name', true  ); 
    $bkx_cust_email = get_user_meta( $current_user->ID, 'bkx_cust_email', true  ); 
    $bkx_cust_phone = get_user_meta( $current_user->ID, 'bkx_cust_phone', true  ); 
    $bkx_cust_gc = get_user_meta( $current_user->ID, 'bkx_cust_gc', true  ); 
    $bkx_cust_add_street = get_user_meta( $current_user->ID, 'bkx_cust_add_street', true  ); 
    $bkx_cust_add_city = get_user_meta( $current_user->ID, 'bkx_cust_add_city', true  ); 
    $bkx_cust_add_state = get_user_meta( $current_user->ID, 'bkx_cust_add_state', true ); 
    $bkx_cust_add_zip = get_user_meta( $current_user->ID, 'bkx_cust_add_zip', true  ); 

    /**
     * Created By : Divyang Parekh
     * Add Functionality for Cancel Booking
     */
    if(bkx_crud_option_multisite('enable_cancel_booking')== 1
      && bkx_crud_option_multisite('reg_customer_crud_op') == 1) :

      $cancel_booking_text = sprintf( __( 'Delete Booking', 'bookingx' ), '');
      $complete_booking_text = sprintf( __( 'Complete Booking', 'bookingx' ), '');
      $alert_cancel_booking_text = sprintf( __( 'Are you sure want to delete this booking ?', 'bookingx' ), '');
      $alert_complete_booking_text = sprintf( __( 'Are you sure want to Complete this booking ?', 'bookingx' ), '');

      $cancellation_policy_page_id = get_option('cancellation_policy_page_id');
      $page_data = get_page( $cancellation_policy_page_id );
      $cancellation_policy_content = $page_data->post_content;
      $cancellation_policy_page_link = get_permalink($cancellation_policy_page_id);

      if(isset($cancellation_policy_page_link)) : $cancellation_policy_page_link = $cancellation_policy_page_link; else: $cancellation_policy_page_link= "#"; endif;
      //$cancellation_policy_link = '<b>Read more about cancellation policy , <a href="'.$cancellation_policy_page_link.'" target="_blank">Click here</a></b>';
      $cancellation_policy_link = sprintf( __( '<b> Read more about cancellation policy , <a href="%1$s" target="_blank"> Click here </a> </b>', 'bookingx' ), $cancellation_policy_page_link);
      $cancellation_policy_title= $page_data->post_title;

    endif;
        //End Divyang Parekh
    if(!empty($_POST['_i_process']) && isset($_POST['_i_process']) && !empty($_POST['comment_is'])){
          $encrypted_booking_id = sanitize_text_field( $_POST['_i_process'] );
          //Booking Variables
          $decoded_booking_data= base64_decode($encrypted_booking_id);
          $decoded_booking_data_arr=  explode("|",$decoded_booking_data);
          $decoded_booking_id= $decoded_booking_data_arr[0];
          $comment_is = sanitize_text_field( $_POST['comment_is']);
         ?>
    <script type="text/javascript">
      jQuery(function() {
        $booking_id = '<?php echo $decoded_booking_id; ?>';
        $comment = '<?php echo sanitize_text_field( $_POST['comment_is']);?>';
        jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', { action : 'bkx_cancel_booking', booking_record_id: $booking_id, mode:'cancelled', comment : $comment }, function(data) {
           location.href = '<?php echo get_permalink($my_account_id);?>';
        });
      });
    </script>
     <?php
     die;
    }
$search['search_date'] = date('Y-m-d');
$search['search_by'] = 'future';
$GetFutureBookedRecords = $BookedRecords->GetBookedRecordsByUser($search);
$search['search_by'] = 'past';
$GetPastBookedRecords = $BookedRecords->GetBookedRecordsByUser($search);
echo bkx_get_loader();
?>
 <div id="bkx-booking-data">
 <div class="et-tabs-container et_sliderfx_fade et_sliderauto_false et_sliderauto_speed_5000 et_slidertype_top_tabs" style="position: relative;">
 <ul class="et_shortcodes_mobile_nav bookingx-mobile_nav"><li><a href="#" class="et_sc_nav_next"><?php echo __( 'Next', 'bookingx' ) ?><span></span></a></li><li><a href="#" class="et_sc_nav_prev"><?php echo __( 'Previous', 'bookingx' ) ?><span></span></a></li></ul>


              <a href="#" class="nav-tabs-dropdown mobile_menu_bar"></a>
              <ul class="et-tabs-control">
                 
                <li class="active"><a data-toggle="tab" onclick="bkx_tabsubmit('future');" id="bkx_future" href="javascript:void(0);"><?php echo __( 'Future Bookings', 'bookingx' ) ?><span>(<?php echo sizeof($GetFutureBookedRecords); ?>)</span></a></li>
                <li class=""><a data-toggle="tab" onclick="bkx_tabsubmit('past');" id="pbooktab" href="javascript:void(0);"><?php echo __( 'Past Bookings', 'bookingx' ) ?> <span>(<?php echo sizeof($GetPastBookedRecords); ?>)</span></a></li>

                 <li class=""><a data-toggle="tab" id="setting_tab" href="javascript:void(0);"> <?php echo __( 'Settings', 'bookingx' ) ?> </a></li>
     </ul>

              <div class="et-tabs-content bookingx" style="overflow: hidden; position: relative;">
                <div class="et-tabs-content-main-wrap">
                  <div class="et-tabs-content-wrapper">
                    
                  <!-- Future BOOKING TAB START -->
                    <div class="et_slidecontent et_shortcode_slide_active" id="ntab" style="opacity:1;display:block;">
                        <div class="mainBlock">
                          <input type="hidden" name="pasttabselected" id="pasttabselected" value="" />
                          <?php if(!empty($GetFutureBookedRecords)) : ?>
                            <?php foreach ($GetFutureBookedRecords as  $futurebooking) :
                              $serviceObj = $futurebooking['base_arr']['main_obj'];
                              $get_order_status = $BookedRecords->get_order_status($futurebooking['booking_record_id']);

                              $booking_note_data = get_comments( array('post_id'   => $futurebooking['booking_record_id'] ,'meta_key' => 'is_customer_note','meta_value' => 1 ,'number' => 1 ) );

                              $reason_for_cancelled = !empty($booking_note_data[0]) ? $booking_note_data[0]->comment_content : "";
                             // print_r($booking_note_data);
                              $cancelled_booking = '';
                              $reason_for_cancelled_html = '';
                              if($get_order_status =='Cancelled'){ 
                                //$cancelled_booking =  '<p>Booking Status : '.$get_order_status.'</p>';
                                $cancelled_booking =  sprintf( __( '<p> Booking Status :  %1$s </p>', 'bookingx' ), $get_order_status);

                                $reason_for_cancelled_html = sprintf( __( '<p><b> Reason for cancellation :<b>  %1$s </p>', 'bookingx' ), $reason_for_cancelled);
                              }
                            ?>
                             <div class="card staffCard services" id="ack-booking-43">
                              <h3><?php printf( esc_html__( 'Booking ID : #%d', 'bookingx' ), $futurebooking['booking_record_id'] );?></h3><?php echo $cancelled_booking;?>

                              <div style="float: left; padding: 0 20px;"><?php echo date('d/m/Y',strtotime($futurebooking['booking_start_date'])); ?> <?php echo date('h:i:s A', strtotime($futurebooking['booking_start_date'])); ?></div> 
                              <div style="float: left; padding: 0 20px;"><?php echo $futurebooking['total_duration']; ?> </div>   
                              <div style="float: left; padding: 0 20px;"><?php echo $serviceObj->post->post_title; ?></div> 
                               <div style="float: left; padding: 0 20px;"> <a id="view_href-<?php echo $futurebooking['booking_record_id']; ?>" href="javascript:view_booking(<?php echo $futurebooking['booking_record_id']; ?>);"> <?php echo __( 'view / hide', 'bookingx' );?></a> </div> <p> &nbsp;</p>
                               <div style="float: left;padding: 0 20px;padding-top: 10px;display: none;"  id="detail-view-<?php echo $futurebooking['booking_record_id']; ?>">
                                <?php

                                  $extra_data = '';
                                  if(!empty($futurebooking['extra_arr'])){
                                    $extra_data = sprintf( __('<p><b> %s service\'s : </b>','Bookingx'),$addition_alias);
                                    foreach ($futurebooking['extra_arr'] as $key => $extra_arr) {
                                      $extra_data .=  sprintf(__('&nbsp;%s &nbsp;','Bookingx'),$extra_arr['title']);
                                    } 
                                  }

           echo sprintf(__('<p><b> %s Name :</b> %s </p>','Bookingx'),$seat_alias,$futurebooking['seat_arr']['title']);

            echo  sprintf(__('<p><b> %s Name :</b> %s </p>','Bookingx'),$base_alias,$futurebooking['base_arr']['title']);

            echo $extra_data;

            echo "<p><b> ".__( 'Location :', 'bookingx' )."</b> {$full_address} </p>";

            echo '<p class="booking-'.$get_order_status.'"><b>'.__( 'Booking Status :', 'bookingx' ).''.$get_order_status.'</b></p>';
            echo $reason_for_cancelled_html;


            $booking_edit_process_page_id = bkx_crud_option_multisite("booking_edit_process_page_id");
 
            if(!empty($booking_edit_process_page_id)){
                $seat_id = $futurebooking['seat_id'];
                $base_id = $futurebooking['base_id'];
                $extra_id = !empty($futurebooking['addition_ids']) ? $futurebooking['addition_ids'] : '0';
                $edit_booking = get_permalink($booking_edit_process_page_id);
                $encrypted_booking_id = $futurebooking['booking_record_id'].'|'.$seat_id.'|'.$base_id.'|'.$extra_id.'|'.$my_account_id;
                $encrypted_booking_id= base64_encode($encrypted_booking_id);
                $edit_booking = add_query_arg( array( 'i' =>$encrypted_booking_id ) , $edit_booking );
                $booking_safe_link = wp_nonce_url( $edit_booking, 'booking_safe_link_'.$encrypted_booking_id);
            }
             

if($get_order_status!='Cancelled'){
  ?>
   <a href="<?php echo $booking_safe_link;?>"><?php echo __( 'Edit', 'bookingx' ) ?></a> 
   <?php if(bkx_crud_option_multisite('enable_cancel_booking')== 1 ) : ?>
   | <a href="javascript:cancel_booking(<?php echo $futurebooking['booking_record_id'];?>,'n');" ><?php echo __( 'Cancel', 'bookingx' ) ?></a>
 <?php endif;?>
    <span align="center" id="generate_pdf_data_spinner-<?php echo $futurebooking['booking_record_id'];?>" style="display:none;"><img src="<?php echo get_site_url();?>/bodikea-loader1.gif" /></span>
  <form id="cancel_process_booking_<?php echo $futurebooking['booking_record_id']; ?>_n" style="display: none;"   method="post">
    <textarea id="cancel_comment_<?php echo $futurebooking['booking_record_id'];?>_n" name="comment_is" style="width: 100%;height: 150px;"></textarea>
    <input type="hidden" name="_i_process" id="_i_process<?php echo $futurebooking['booking_record_id'];?>_n"  value="<?php echo $encrypted_booking_id;?>">
    <input style="display: none;" type="button" value="<?php echo __( 'Cancel Booking', 'bookingx' ) ?>" class="button" id="cancel_process_submit_<?php echo $futurebooking['booking_record_id']; ?>_n">
    <button class="button" id="i_agree_<?php echo $futurebooking['booking_record_id']; ?>_n"><?php echo __( 'I Agree', 'bookingx' ) ?></button>
    <input type="button" value="<?php echo __( 'Close', 'bookingx' ) ?>" class="button" id="cancel_<?php echo $futurebooking['booking_record_id'];?>_n">
    <p><?php echo $cancellation_policy_link;?></p>
  </form>
  <?php
}
?>
</div>
</div>

<?php endforeach;?>
        <?php else : ?>
          <div class="card staffCard services" id="ack-booking-43">
          <h3> <?php echo __( 'No booking\'s found.', 'bookingx' ) ?></h3>  
        </div>
    <?php endif;?>
  </div>
</div>
            <!-- Future BOOKING TAB END -->

                        <!-- Past Booking TAB START -->
                  <div class="et_slidecontent et_shortcode_slide_active" id="ptab" style="opacity:1;display:none;">
                        <div class="mainBlock">
                          <input type="hidden" name="pasttabselected" id="pasttabselected" value="" />
                           <?php if(!empty($GetPastBookedRecords)) : ?>
                            <?php foreach ($GetPastBookedRecords as  $pastbooking) : 
                                  $serviceObj = $pastbooking['base_arr']['main_obj'];
                                  $get_order_status = $BookedRecords->get_order_status($pastbooking['booking_record_id']);
                            ?>
                             <div class="card staffCard services" id="ack-booking-43">
                              <h3>Booking ID : #<?php echo $pastbooking['booking_record_id']; ?></h3>
                              <div style="float: left; padding: 0 20px;"><?php echo date('d/m/Y',strtotime($pastbooking['booking_start_date'])); ?> <?php echo date('h:i:s A', strtotime($pastbooking['booking_start_date'])); ?></div> 
                              <div style="float: left; padding: 0 20px;"><?php echo $pastbooking['total_duration']; ?> </div>   
                              <div style="float: left; padding: 0 20px;"><?php echo $serviceObj->post->post_title; ?></div> 
                              <div style="float: left; padding: 0 20px;"> <a class="view" href="javascript:view_booking('<?php echo $pastbooking['booking_record_id']; ?>');">view / hide</a> </div>

                              <p> &nbsp;</p>
                               <div style="float: left;padding: 0 20px;padding-top: 10px;display: none;"  id="detail-view-<?php echo $pastbooking['booking_record_id']; ?>">
                                <?php

                                  $extra_data = '';
                                  if(!empty($pastbooking['extra_arr'])){
                                    $extra_data = sprintf( __('<p><b> %s service\'s : </b>','Bookingx'),$addition_alias);
                                    foreach ($pastbooking['extra_arr'] as $key => $extra_arr) {
                                      $extra_data .=  sprintf(__('&nbsp;%s &nbsp;','Bookingx'),$extra_arr['title']);
                                    } 
                                  }

           echo sprintf(__('<p><b> %s Name :</b> %s </p>','Bookingx'),$seat_alias,$pastbooking['seat_arr']['title']);

            echo  sprintf(__('<p><b> %s Name :</b> %s </p>','Bookingx'),$base_alias,$pastbooking['base_arr']['title']);

            echo $extra_data;

            echo "<p> <b>Location :</b> {$full_address} </p>";

            echo '<p class="booking-'.$get_order_status.'">Booking Status : '.$get_order_status.'</p>';

?>                         
                              </div>


                            </div>
                          <?php endforeach;?>
                        <?php else : ?>
                            <div class="card staffCard services" id="ack-booking-43">
                              <h3><?php echo __( 'No booking\'s found.', 'bookingx' ) ?></h3>  
                            </div>
                        <?php endif;?>
                          </div>
                      </div>
               
          
  <!-- Past Booking TAB END -->

   <div class="et_slidecontent et_shortcode_slide_active" id="ptab" style="opacity:1;display:none;">
      <div class="mainBlock">
        <div class="bkx_cust_setting">
              <div class="bkx_cust_setting_err"></div>
              <div class="bkx_cust_setting_suc"></div>
              <form id="bkx_cust_setting" name="bkx_cust_setting" method="post" >
                  <p> <label> <?php echo __( 'Name', 'bookingx' ) ?> </label> <input type="text" name="bkx_cust_name" value="<?php echo $bkx_cust_name; ?>"> </p>
                  <p> <label> <?php echo __( 'Email', 'bookingx' ) ?> </label><input type="text" name="bkx_cust_email" value="<?php echo $bkx_cust_email; ?>"> </p>
                  <p> <label> <?php echo __( 'Phone', 'bookingx' ) ?> </label><input type="text" name="bkx_cust_phone" value="<?php echo $bkx_cust_phone; ?>"> </p>
                  <p> <label> <?php echo __( 'Google calendar', 'bookingx' ) ?> </label><input type="text" name="bkx_cust_gc" value="<?php echo $bkx_cust_gc; ?>"></p>
                  <p> <label> <?php echo __( 'Password', 'bookingx' ) ?>  </label><input type="password" name="bkx_cust_pwd" style="width: auto;"></p>
                  <p> <label> <?php echo __( 'Confirm Password', 'bookingx' ) ?>  </label><input type="password" name="bkx_cust_cnf_pwd" style="width: auto;"></p>
                  <p> <h3> <?php echo __( 'Address', 'bookingx' ) ?> </h3> </p>
                  <p> <label> <?php echo __( 'Street', 'bookingx' ) ?>  </label><input type="text" name="bkx_cust_add_street" value="<?php echo $bkx_cust_add_street; ?>"> </p>
                  <p> <label> <?php echo __( 'City', 'bookingx' ) ?>  </label>  <input type="text" name="bkx_cust_add_city" value="<?php echo $bkx_cust_add_city; ?>"> </p>
                  <p> <label> <?php echo __( 'State', 'bookingx' ) ?> </label>  <input type="text" name="bkx_cust_add_state" value="<?php echo $bkx_cust_add_state; ?>"> </p>
                  <p> <label> <?php echo __( 'zip', 'bookingx' ) ?>  </label>   <input type="text" name="bkx_cust_add_zip" value="<?php echo $bkx_cust_add_zip; ?>"> </p> 
                  <p> <label> &nbsp;  </label><input type="button" name="bkx_cust_save" id="bkx_cust_save" value="Update"> </p> 
              </form> 
          </div>
      </div>
   </div>

</div>
</div>
</div>
</div>
</div>


<script type="text/javascript">
jQuery( document ).ready(function() {
    setTimeout(function(){ jQuery('.et_shortcodes_mobile_nav').remove();}, 100);
});
jQuery(function() {
  jQuery('#bkx_cust_save').on('click', function(e) {

     e.preventDefault();

     jQuery('.bookingx-loader').show();

        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' );?>';
          var data = {
              'action': 'bkx_cust_setting',
              'cust_data': jQuery("#bkx_cust_setting").serialize(),
          };

          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
          jQuery.post(ajaxurl, data, function(response) {
                customer_obj =  jQuery.parseJSON(response);

                if(customer_obj.error)
                {
                  jQuery('.bkx_cust_setting_suc').html('');
                   var customer_err = customer_obj.error;
                   var error_data = '';
                   jQuery.each( customer_err, function( key, value ) {
                      if(value){
                        error_data += '<p> <label> &nbsp;  </label> '+ value +'</p>';
                      }
                  });
                  jQuery('.bkx_cust_setting_err').html(error_data);

                }


                if(customer_obj.success)
                {
                   jQuery('.bkx_cust_setting_err').html('');
                   var customer_success = customer_obj.success;                    
                   jQuery('.bkx_cust_setting_suc').html(customer_success);
                }

                jQuery('.bookingx-loader').hide();


          });

         
      
      });

});
/**
 *
 * @param booking_id
 * @param type
 */
 function cancel_booking(booking_id,type)
 {
  var $booking_id = booking_id;
  var $_type = type;

  var $hide_form_delete = jQuery('#delete_process_booking_'+$booking_id+'_'+$_type);
  var $cancel = jQuery('#cancel_'+$booking_id+'_'+$_type);
  var $cancel_process_submit = jQuery('#cancel_process_submit_'+$booking_id+'_'+$_type);
  var $i_agree = jQuery('#i_agree_'+$booking_id+'_'+$_type);
  var $cancel_show_form = jQuery('#cancel_process_booking_'+$booking_id+'_'+$_type);
  $cancel_show_form.toggle();
  $hide_form_delete.hide("slow",function(){ });

  jQuery($cancel).click(function(){
    $cancel_show_form.hide("slow");
  });

  $($cancel_process_submit).on( "click", function() {
     if(confirm('Are you sure want to cancel this booking?'))
     {
       validate_booking($booking_id,$_type);
     }
   });

  $($i_agree).on( "click", function(event) {
      event.preventDefault();
      $cancel_process_submit.show();
      $i_agree.hide();
   });
}
function validate_booking($booking_id,$_type)
{

  var $cancel_show_form = jQuery('#cancel_process_booking_'+$booking_id+'_'+$_type);
  var $comment =document.getElementById('cancel_comment_'+$booking_id+'_'+$_type).value;


  if($comment=='')
  {
    alert('Please fill the comments.');
    return false;
  }
  if($comment!='')
  {   
      $cancel_show_form.submit();
  }
}


/**
 *
 * @param booking_id
 * @param type
 */
 function delete_booking(booking_id,type)
 {
    //delete_process_submit_
    var $booking_id = booking_id;
    var $_type = type;
    jQuery('#delete_comment_'+$booking_id+'_'+$_type).val('');

    var $show_form = jQuery('#delete_process_booking_'+$booking_id+'_'+$_type);
    var $hide_form_cancel = jQuery('#cancel_process_booking_'+$booking_id+'_'+$_type);
    var $cancel = jQuery('#delete_'+$booking_id+'_'+$_type);
    var $delete_process_submit= jQuery('#delete_process_submit_'+$booking_id+'_'+$_type);


    $hide_form_cancel.trigger('reset');
    $show_form.find('input[type=text], textarea').val('');
    $hide_form_cancel.hide("slow",function(){
      $show_form.toggle();
    });

    jQuery($cancel).click(function(){
      $show_form.hide("slow");
    });

    jQuery($delete_process_submit).click(function(){
      var $comment =jQuery('#delete_comment_'+$booking_id+'_'+$_type).val();
      if($comment)
      {
        if(confirm('Are you sure want to delete this Booking?'))
        {
          $show_form.submit();
                //alert('the action is: ' + $show_form.attr('action'));
              }
            }
            else
            {
              alert('Please fill the comments.')
            }
          });
  }
 function view_booking($booking_id,obj)
 {
  //console.log(obj);
    var booking_id = $booking_id;
    var detail_obj = jQuery('#detail-view-'+booking_id);
    var view_href_obj = jQuery('#view_href-'+booking_id);
  
    detail_obj.toggle(); 
 }

</script>
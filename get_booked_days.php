<?php
require_once('../../../wp-load.php');
global $wpdb;

$BkxBooking = new BkxBooking();
$baseid = $_POST['baseid'];

$BaseMetaObj = get_post_custom($baseid ); 
$base_time_option = $BaseMetaObj['base_time_option'][0];
$base_extended = isset( $BaseMetaObj['base_is_extended'] ) ? esc_attr( $BaseMetaObj['base_is_extended'][0] ) : "";


if( $base_time_option=="D" || $base_time_option=="M" ){
	$base_data['disable'] = 'yes';
	$base_data['time'] = $BaseMetaObj['base_day'][0];
	echo json_encode($base_data);  
}else{
	echo json_encode('');
}
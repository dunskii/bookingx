<?php
session_start();
require_once('../../../wp-load.php');
global $wpdb;
$term = '';
if(isset($_POST['baseid']) && $_POST['baseid']!='')
{
	$term = $_POST['baseid'];
        $BaseObj = get_post($term);
        if(!empty($BaseObj) && !is_wp_error($BaseObj))
        {
            $BaseMetaObj = get_post_custom( $BaseObj->ID ); 
            $base_extended = isset( $BaseMetaObj['base_is_extended'] ) ? esc_attr( $BaseMetaObj['base_is_extended'][0] ) : "";
            echo $base_extended;
        }
}
?>

<?php 
require_once('../../../wp-load.php');

global $wpdb;
$term = '';
if(isset($_POST['seatid']))
{
$term = $_POST['seatid'];
}

echo $term;
?>
<?php
session_start();
require_once('../../../wp-load.php');
global $wpdb;
$term = '';

if(isset($_POST['addition_color']))
{
	$term = $_POST['addition_color'];


	$query = "SELECT * FROM `bkx_addition` WHERE addition_color ='".trim($term)."'";
	$objListBase = $wpdb->get_results($query);
	$no_of_rows =  $wpdb->num_rows;

	if($no_of_rows==1 && isset($_POST['addition_id']))
	{
		if($objListBase[0]->addition_id==$_POST['addition_id'])
		{
			$no_of_rows = 0;	
		}
	}
}
if(isset($_POST['base_color']))
{
	$term = $_POST['base_color'];
	$query = "SELECT * FROM `bkx_base` WHERE base_color ='".trim($term)."'";
	$objListBase = $wpdb->get_results($query);
	$no_of_rows =  $wpdb->num_rows;
	if($no_of_rows==1 && isset($_POST['base_id']))
	{	//print_r($objListBase);
		//echo $objListBase[0]->base_id."insode".$_POST['base_id'];
		if($objListBase[0]->base_id==$_POST['base_id'])
		{
			$no_of_rows = 0;	
		}
	}
}

echo $no_of_rows;
?>

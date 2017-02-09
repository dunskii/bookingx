<?php
session_start();
require_once('../../../wp-load.php');

global $wpdb;
if(!empty($_POST))
{
$addition_id = $_POST['id'];

$objListBase	=	$wpdb->get_results('SELECT status FROM `bkx_addition` WHERE addition_id='.trim($addition_id).'');

$curr_status = $objListBase[0]->status;

if($curr_status == 1)
	{
		$tobe_status = 0;
	}
if($curr_status == 0)
	{
		$tobe_status = 1;
	}

$strTableName = "bkx_addition";
$arrTabledata = array('status'=>$tobe_status);
$where = array('addition_id' => $addition_id );
$wpdb->update( $strTableName, $arrTabledata, $where);
if($tobe_status == 1)
	{
		echo "Deactivate";
	}
else if($tobe_status == 0)
	{
		echo "Activate";
	}

}
else
{
echo "error";
}
?>
<?php
session_start();
require_once('booking_general_functions.php');
global $wpdb;
?>

<div class="wrap">

<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>

<h2>Notifications
	
</h2>

<?php 
$query = 'SELECT * FROM bkx_booking_record';
//logic building for pagination
$limit = '';
$curr_page_number = get_pagenum();
$per_page = get_per_page();
$wpdb->get_results($query);
$total_items = $wpdb->num_rows;
$total_pages = floor($total_items/$per_page);
$mod_val = $total_items%$per_page;
if($mod_val>0)
{
	$total_pages = $total_pages+1;
}
if($total_pages>1)
{
	$limit = " LIMIT ".(($curr_page_number-1)*$per_page).", ".$per_page;
}
$query = $query.$limit;

$objListBase=$wpdb->get_results($query);

?>


<?php if(sizeof($objListBase)>0){ ?>
<table cellspacing="0" class="wp-list-table widefat plugins" style="margin-top:20px;">
	<tbody>
	
		
		<?php
		
		foreach($objListBase as $key=>$val)
		{
			
			echo "<tr><td>Seat ".$val->seat_id.", booked by ".$val->first_name." ".$val->last_name."</td></tr>";
		}
		 ?>
	
		
	</tbody>
</table>
<?php pagination('bottom',$total_items,$total_pages,$per_page); ?>
<?php }else{ echo "<h3>No notification</h3>"; } ?>
</div><!-- WRAP ENDS -->

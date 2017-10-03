<?php
require_once('../../../wp-load.php');

$extra_id = $_POST['extra_id'];
$BkxExtra  = new BkxExtra('', $extra_id);
 
$post_title = $BkxExtra->post->post_title;
if(isset($post_title) && $post_title !='')
{
	echo $post_title .' - '.get_current_currency().$BkxExtra->meta_data['addition_price'][0];
}
else
{
	echo 0;
}
die;
<?php
require_once('booking_general_functions.php');
global $wpdb;

$screen = get_current_screen();

$temp_option = crud_option_multisite('bkx_siteuser_canedit_seat');
$temp_css = crud_option_multisite('bkx_siteclient_canedit_css');
color_load_scripts(array('iris.min.js?asas'));
?>
<div class="wrap">
<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>
<h2>Bookingx Settings</h2>
<?php
	/*
	 *  Print the error or success messages if any while processing.
	*/	
	if(isset($_SESSION['success']) && $_SESSION['success'] != '')
	{
		echo '<div class="updated"><p><strong>'.$_SESSION['success'].'</strong></p></div>';
		$_SESSION['success']	=	'';
	}
	else if(isset($_SESSION['error']) && $_SESSION['error'] != '')
	{
		echo '<div class="error"><p><strong>'.$_SESSION['error'].'</strong></p></div>';
		$_SESSION['error']	=	'';
	}
?>
<?php

$bkx_prices_include_tax = crud_option_multisite('bkx_prices_include_tax');
$bkx_prices_include_tax = ($bkx_prices_include_tax) ? $bkx_prices_include_tax : 0 ;
$country = get_wp_country();

$bkx_setting_tabs = bkx_setting_tabs();
if(!empty($bkx_setting_tabs))
{
	$current_main_tab = isset($_GET['bkx_tab']) ? $_GET['bkx_tab']:'bkx_general';

	$bkx_setting_tabs_html = '';
	$bkx_setting_tabs_html .= '<h2 class="nav-tab-wrapper">';
	foreach ($bkx_setting_tabs as $key => $bkx_tabs) {
		$main_label = $bkx_setting_tabs[$key]['label'];
		$tab_is_active = ($current_main_tab == $key) ? 'nav-tab-active' : '' ;
		$generate_tab_url = $screen->parent_file.'&page=bkx-setting&bkx_tab='.$key;
		$bkx_setting_tabs_html .= '<a href="'.$generate_tab_url.'" class="nav-tab '.$tab_is_active.'">'.ucwords($main_label).'</a>';
	}
	$bkx_setting_tabs_html .= '</h2>';
}
echo $bkx_setting_tabs_html;

if(!empty($current_main_tab))
{
	$current_setting_tab_path = PLUGIN_DIR_PATH.'admin/settings/'.$current_main_tab.'-view.php';
	if ( ! file_exists( $current_setting_tab_path ) ) {
        return;  
    }

    $bkx_general_submenu = $bkx_setting_tabs[$current_main_tab]['submenu'];
    

	if(!empty($bkx_general_submenu)){

		$default_active = $bkx_setting_tabs[$current_main_tab]['default'];
		$current_submenu_active = isset($_GET['section']) ? $_GET['section']: $default_active;

		$bkx_general_submenu_html = '';
		$bkx_general_submenu_html .= '<ul class="subsubsub">';
		$gen_count = 1 ;
		$gen_max = sizeof($bkx_general_submenu);
		foreach ( $bkx_general_submenu as $general_key => $general_submenu ) {

			$general_label = explode("|", $general_submenu);
			$general_tab_url = $screen->parent_file.'&page=bkx-setting&bkx_tab='.$current_main_tab.'&section='.$general_key;
			$submenu_is_active = ($current_submenu_active == $general_key) ? 'current' : '' ;

			$seprater = ($gen_count < $gen_max) ? " | " : '';
			$bkx_general_submenu_html .= '<li><a href="'.$general_tab_url.'" class="'.$submenu_is_active.'">'.$general_label[0].'</a> '.$seprater.' </li>';	
			$gen_count ++;
		}
		$bkx_general_submenu_html .= '</ul><p> &nbsp;</p>';
	}
	echo $bkx_general_submenu_html;

	$bkx_general_submenu_label = $bkx_general_submenu[$current_submenu_active];
	$bkx_submenu_active_label = explode("|", $bkx_general_submenu_label);
	$bkx_general_submenu_label = $bkx_submenu_active_label[0];
	
	if(!empty($bkx_submenu_active_label[1]))
	{
		$bkx_general_submenu_label = $bkx_submenu_active_label[1];
	}

	require_once(PLUGIN_DIR_PATH.'admin/settings/settings_save.php');
	require_once(PLUGIN_DIR_PATH.'admin/settings/'.$current_main_tab.'-view.php');
	require_once(PLUGIN_DIR_PATH.'admin/settings/setting_js.php');
}
?>                 
</div><!-- WRAP ENDS -->
 

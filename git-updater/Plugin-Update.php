<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once( 'BFIGitHubPluginUploader.php' );
if ( is_admin() ) {
    new BKXGitHubPluginUpdater( __FILE__, 'bfintal', "BFI-Core" );
}

?>
<?php
require_once('../../../wp-load.php');
global $wpdb;
$BkxExportObj = new BkxExport();
$BkxExportObj->export_now();
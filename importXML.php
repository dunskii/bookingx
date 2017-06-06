<?php
require_once('../../../wp-load.php');
global $wpdb;
$BkxImport = new BkxImport();
$import_now = $BkxImport->import_now($_FILES,$_POST);


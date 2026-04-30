<?php
session_start();
require_once __DIR__ . '/db.php';
$_SESSION['JOGOLS'] = "";
unset($_SESSION['JOGOLS']);

if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
	$pageURL = 'https';

}else{
	$pageURL = 'http';

}

$pageURL .= '://';

if($_SERVER['SERVER_PORT']!='80'){
	$pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];//.''.$_SERVER['REQUEST_URI'];

}else{
	$pageURL .= $_SERVER['SERVER_NAME'];//.''.$_SERVER['REQUEST_URI'];
	$loginURL = OLS_BASE_URL . 'login.php';
}
echo '<meta http-equiv="refresh" content="0; url='.$loginURL.'" />';
?>
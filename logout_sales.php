<?php

session_start();



$_SESSION['JOGOLSSALE'] = "";

unset($_SESSION['JOGOLSSALE']);



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



}

echo '<meta http-equiv="refresh" content="0; url='.$pageURL.'/login.php" />';

?>
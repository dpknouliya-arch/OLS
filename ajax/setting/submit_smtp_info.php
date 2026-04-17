<?php
session_start();

if(!isset($_SESSION["JOGOLS"])){
	$a_result["msg"] = 'Please re-login again.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$s_email = base64_decode($_POST["s_email"]);
$s_password = base64_decode($_POST["s_password"]);

if($s_email=="" || $s_password==""){
	$a_result["msg"] = 'Please input account & password.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

$sql_update = "UPDATE tbl_email_setting SET email_name='".addslashes($s_email)."',email_password='".$s_password."' WHERE use_for='gmail_SMTP';";
if($conn->query($sql_update)){

	$a_result["result"] = "success";
}else{
	$a_result["msg"] = 'Fail to save Info.';
	$a_result["result"] = "fail";
}


echo json_encode($a_result);
?>
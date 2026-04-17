<?php
session_start();

if(!isset($_SESSION["JOGOLS"])){
	$a_result["msg"] = 'Please re-login again.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$size_id = $_POST["size_id"];
$size_name = base64_decode($_POST["size_name"]);

$sql_update = "UPDATE tbl_size SET size_name='".addslashes($size_name)."' WHERE size_id='".$size_id."';";
if($conn->query($sql_update)){

	$a_result["result"] = "success";
}else{
	$a_result["msg"] = 'Fail to save Size name.';
	$a_result["result"] = "fail";
}


echo json_encode($a_result);
?>
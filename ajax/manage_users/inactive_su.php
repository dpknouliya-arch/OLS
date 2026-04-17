<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

if( !isset($_POST["sub_user_id"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";

	echo json_encode($a_result);
	exit();
}

$status = $_POST['status']; 

$sql_update = "UPDATE tbl_sub_user SET enable='$status' WHERE sub_user_id='".$_POST["sub_user_id"]."';";
if($conn->query($sql_update)){
	$a_result["result"] = "success";
}else{
	$a_result["result"] = "fail";
	$a_result["msg"] = "Fail to Inactive User";
}

echo json_encode($a_result);
?>
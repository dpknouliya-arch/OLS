<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

if( !isset($_POST["s_user_name"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();
}

$s_user_name = base64_decode($_POST["s_user_name"]);

$sql_chk = "SELECT sales_user_id FROM tbl_sales_user WHERE s_user_name='".$s_user_name."';";
$rs_chk = $conn->query($sql_chk);

if($rs_chk->num_rows > 0){
	$a_result["result"] = "fail";
}else{
	$a_result["result"] = "success";
}

echo json_encode($a_result);
?>
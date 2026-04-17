<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

if( !isset($_POST["s_nick_name"]) || !isset($_POST["s_user_name"]) || !isset($_POST["s_password"]) || !isset($_POST["sub_email"]) || !isset($_POST["sub_user_id"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();
}

$nick_name = base64_decode($_POST["s_nick_name"]);
$s_user_name = base64_decode($_POST["s_user_name"]);
$s_user_pwd = base64_decode($_POST["s_password"]);
$sub_email = base64_decode($_POST["sub_email"]);
$sub_user_id = $_POST["sub_user_id"];

$sql_chk = "SELECT sub_user_id FROM tbl_sub_user WHERE s_user_name='".$s_user_name."' AND sub_user_id<>'".$sub_user_id."';";
$rs_chk = $conn->query($sql_chk);

if($rs_chk->num_rows > 0){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Duplicate User name.";

}else{

	$sql_update = "UPDATE tbl_sub_user SET s_user_name='".$s_user_name."',nick_name='".addslashes($nick_name)."',s_user_pwd='".$s_user_pwd."',sub_email='".$sub_email."' WHERE sub_user_id='".$sub_user_id."'; ";

	if($conn->query($sql_update)){

		$a_result["result"] = "success";

	}else{
		$a_result["result"] = "fail";
		$a_result["msg"] = "Fail to edit User";
	}

}

echo json_encode($a_result);
?>

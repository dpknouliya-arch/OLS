<?php
session_start();

if(!isset($_SESSION["JOGOLS"])){
	$a_result["msg"] = 'Please re-login again.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$pro_choice_id = $_POST["pro_choice_id"];
$choice_name = base64_decode($_POST["choice_name"]);

$sql_update = "UPDATE tbl_product_choices SET choice_name='".addslashes($choice_name)."' WHERE pro_choice_id='".$pro_choice_id."';";
if($conn->query($sql_update)){

	$a_result["result"] = "success";
}else{
	$a_result["msg"] = 'Fail to save Choice name.';
	$a_result["result"] = "fail";
}


echo json_encode($a_result);
?>
<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

if( !isset($_POST["of_id"]) || $_POST["of_id"]=="" ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();

}

include('../../db.php');

$of_id = $_POST["of_id"];

$unassign_sql = "UPDATE tbl_draft_of SET is_assigned='0',assign_user_id=NULL WHERE of_id='".$of_id."'; ";
if($conn->query($unassign_sql)){
	$a_result["result"] = "success";
}else{
	$a_result["result"] = "fail";
	$a_result["msg"] = "Error: update DB fail";
}

echo json_encode($a_result);
?>
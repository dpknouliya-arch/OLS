<?php
session_start();

if(!isset($_SESSION["JOGOLS"])){
	$a_result["msg"] = 'Please re-login again.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$size_id_up = $_POST["size_id_up"];
$size_id_down = $_POST["size_id_down"];

$sql_update = "UPDATE tbl_size SET sort_no=sort_no-1 WHERE size_id='".$size_id_up."';";
$conn->query($sql_update);

$sql_update2 = "UPDATE tbl_size SET sort_no=sort_no+1 WHERE size_id='".$size_id_down."';";
$conn->query($sql_update2);

$a_result["result"] = "success";

echo json_encode($a_result);
?>
<?php
session_start();

if(!isset($_SESSION["JOGOLS"])){
	$a_result["msg"] = 'Please re-login again.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$pro_choice_id_up = $_POST["pro_choice_id_up"];
$pro_choice_id_down = $_POST["pro_choice_id_down"];

$sql_update = "UPDATE tbl_product_choices SET sort_no=sort_no-1 WHERE pro_choice_id='".$pro_choice_id_up."';";
$conn->query($sql_update);

$sql_update2 = "UPDATE tbl_product_choices SET sort_no=sort_no+1 WHERE pro_choice_id='".$pro_choice_id_down."';";
$conn->query($sql_update2);

$a_result["result"] = "success";

echo json_encode($a_result);
?>
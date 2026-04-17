<?php
session_start();

if(!isset($_SESSION["JOGOLS"])){
	$a_result["msg"] = 'Please re-login again.';
	$a_result["result"] = "fail";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$prod_id = $_POST["prod_id"];
$choice_name = base64_decode($_POST["choice_name"]);

$sql_max_sort = "SELECT MAX(sort_no) AS max_sort FROM tbl_product_choices WHERE prod_id='".$prod_id."' AND enable=1; ";
$rs_max_sort = $conn->query($sql_max_sort);
$row_max_sort = $rs_max_sort->fetch_assoc();

$n_max_sort = intval($row_max_sort["max_sort"]);
$next_sort = $n_max_sort+1;

$sql_insert = "INSERT INTO tbl_product_choices (choice_name,prod_id,sort_no,date_add) VALUES ('".addslashes($choice_name)."','".$prod_id."','".$next_sort."','".date("Y-m-d H:i:s")."');";
if($conn->query($sql_insert)){

	$a_result["result"] = "success";
}else{
	$a_result["msg"] = 'Fail to add new Choice.';
	$a_result["result"] = "fail";
}

echo json_encode($a_result);
?>
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
$split_order = $_POST["split_order"];
$size_id = $_POST["size_id"];

$select_sort_no = "SELECT sort_no FROM tbl_size WHERE size_id='".$size_id."'; ";
$rs_sort_no = $conn->query($select_sort_no);
$row_sort_no = $rs_sort_no->fetch_assoc();

$sort_no = intval($row_sort_no["sort_no"]);

$sql_update = "UPDATE tbl_size SET sort_no=0,enable=0 WHERE size_id='".$size_id."';";
if($conn->query($sql_update)){

	$sql_update2 = "UPDATE tbl_size SET sort_no=(sort_no-1) WHERE sort_no>".$sort_no." AND prod_id='".$prod_id."' AND split_order='".$split_order."' AND enable=1; ";
	$conn->query($sql_update2);

	$a_result["result"] = "success";

}else{
	$a_result["msg"] = 'Fail to save Size name.';
	$a_result["result"] = "fail";
}


echo json_encode($a_result);
?>
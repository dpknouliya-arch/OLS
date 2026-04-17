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
$pro_choice_id = $_POST["pro_choice_id"];

$select_sort_no = "SELECT sort_no FROM tbl_product_choices WHERE pro_choice_id='".$pro_choice_id."'; ";
$rs_sort_no = $conn->query($select_sort_no);
$row_sort_no = $rs_sort_no->fetch_assoc();

$sort_no = intval($row_sort_no["sort_no"]);

$sql_update = "UPDATE tbl_product_choices SET sort_no=0,enable=0 WHERE pro_choice_id='".$pro_choice_id."';";
if($conn->query($sql_update)){

	$sql_update2 = "UPDATE tbl_product_choices SET sort_no=(sort_no-1) WHERE sort_no>".$sort_no." AND prod_id='".$prod_id."' AND enable=1; ";
	$conn->query($sql_update2);

	$a_result["result"] = "success";

}else{
	$a_result["msg"] = 'Fail to save Choice name.';
	$a_result["result"] = "fail";
}


echo json_encode($a_result);
?>
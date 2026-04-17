<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$draft_id = $_POST["draft_id"];

$add_date = date("Y-m-d H:i:s");

$sql_select = "SELECT of_id FROM tbl_draft_of WHERE draft_id='".$draft_id."'; ";
$rs_select = $conn->query($sql_select);
$a_of_id = array();
while($row_of_id = $rs_select->fetch_assoc()){
	$a_of_id[] = $row_of_id["of_id"];
}

$sql_insert1 = "INSERT INTO tbl_order_form SELECT * FROM tbl_draft_of WHERE draft_id='".$draft_id."' ORDER BY of_id ASC; ";
$is_insert1 = 0;
if($conn->query($sql_insert1)){
	$is_insert1 = 1;
}

$s_of_id = implode(",", $a_of_id);

$sql_insert2 = "INSERT INTO tbl_order_item SELECT * FROM tbl_draft_oi WHERE of_id IN (".$s_of_id.") ORDER BY oi_id ASC; ";
$is_insert2 = 0;
if($conn->query($sql_insert2)){
	$is_insert2 = 1;
}
//echo $is_insert2;

if($is_insert1==1){
	$sql_delete1 = "DELETE FROM tbl_draft_of WHERE draft_id='".$draft_id."'; ";
	$conn->query($sql_delete1);

	$sql_update1 = "UPDATE tbl_order_form SET date_add='".$add_date."' WHERE draft_id='".$draft_id."';";
	$conn->query($sql_update1);
}

if($is_insert2==1){
	$sql_delete2 = "DELETE FROM tbl_draft_oi WHERE of_id IN (".$s_of_id."); ";
	$conn->query($sql_delete2);
}

$a_result["result"] = "success";

echo json_encode($a_result);
?>
<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$a_of_id = array();

$sql_select = "SELECT DISTINCT of_id FROM tbl_draft_of WHERE draft_id='".$_POST["draft_id"]."'";
$rs_select = $conn->query($sql_select);
while($row_draft = $rs_select->fetch_assoc()){

	$a_of_id[] = $row_draft["of_id"];

}

$s_of_id = implode(",", $a_of_id);

$delete_item = "DELETE FROM tbl_draft_oi WHERE of_id IN (".$s_of_id."); ";
$conn->query($delete_item);

$delete_order_form = "DELETE FROM tbl_draft_of WHERE draft_id='".$_POST["draft_id"]."'; ";
$conn->query($delete_order_form);


$a_result["result"] = "success";

echo json_encode($a_result);
?>
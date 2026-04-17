<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$of_id = $_POST["of_id"];

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;
$draft_id = "JOG".date("YmdHis").$user_id;

$add_date = date("Y-m-d H:i:s");

$sql_insert1 = "INSERT INTO tbl_draft_of SELECT * FROM tbl_order_form WHERE of_id='".$of_id."'; ";

if($conn->query($sql_insert1)){

	$sql_get_next_id = "SELECT AUTO_INCREMENT AS next_of_id FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'tbl_draft_of'; ";
	$rs_next_id = $conn->query($sql_get_next_id);
	$row_next_id = $rs_next_id->fetch_assoc();
	$new_of_id = $row_next_id["next_of_id"];

	$sql_update1 = "UPDATE tbl_draft_of SET of_id='".$new_of_id."',draft_id='".$draft_id."',order_date='".date("Y-m-d")."',req_due_date=NULL,order_status='new',order_code=NULL,code_match=NULL,ship_status=NULL,date_add='".$add_date."',re_order_id=lkr_order_main_id,lkr_order_main_id=NULL WHERE of_id='".$of_id."';";
	$conn->query($sql_update1);

	$sql_insert2 = "INSERT INTO tbl_draft_oi (of_id,player_name,p_or_g,sex,product_size_id,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note) SELECT '".$new_of_id."',player_name,p_or_g,sex,product_size_id,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note FROM tbl_order_item WHERE of_id='".$of_id."' ORDER BY oi_id ASC; ";
	$conn->query($sql_insert2);

	/*$sql_update2 = "UPDATE tbl_draft_oi SET of_id='".$new_of_id."' WHERE of_id='".$of_id."';";
	$conn->query($sql_update2);*/

}

$a_result["result"] = "success";

echo json_encode($a_result);
?>
<?php
include('../../db.php');

if( !isset($_POST["of_id"]) || ($_POST["of_id"]=="") ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();
}

$of_id = $_POST["of_id"];

$sql_update = "UPDATE tbl_order_form SET order_status='archived' WHERE of_id='".$of_id."'; ";
if($conn->query($sql_update)){
	$a_result["result"] = "success";
}else{
	$a_result["result"] = "fail";
	$a_result["msg"] = "Fail to move to Archived.Please try again later.";
}

echo json_encode($a_result);

?>

<?php
session_start();

	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}

	if( !isset($_POST["order_id"]) || $_POST["order_id"]=="" ){

		$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid parameter.";
		echo json_encode($a_result);
		exit();
	}

include('../../db.php');
$strDate = date('Y-m-d H:i:s');
	
$final_approval_id = base64_decode($_POST['order_id']);
$sql = "SELECT * FROM tbl_final_approvals WHERE final_approval_id='$final_approval_id'";
$query = mysqli_query($conn,$sql);
if(mysqli_num_rows($query)>0){
    $row = mysqli_fetch_assoc($query);
    $update = "UPDATE tbl_final_approvals SET approval_from_customer = 2 WHERE final_approval_id='$final_approval_id'";
    if(mysqli_query($conn,$update)){
    $order_main_id=$row['order_main_id'];

	$sql_up_stat = 'UPDATE order_main SET 
				order_main_status = "4",
				order_main_update_date = "'.$strDate.'",
				order_main_update_user = "OLS",
				url_push_to_ols = "2"
				WHERE order_main_id = "'.$order_main_id.'" ';
	$query = mysqli_query($conn3,$sql_up_stat);
	$sql_update_ols = "UPDATE tbl_order_form SET order_status='producing' WHERE lkr_order_main_id='".$order_main_id."'; ";
	if($conn->query($sql_update_ols)){
	    $a_result["result"] = "success";
	    die(json_encode($a_result));
	}
    }
}
else{
    $a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();
}
?>
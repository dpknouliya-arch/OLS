<?php
session_start();

	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}

	if( !isset($_POST["dd_id"]) || $_POST["dd_id"]=="" ){

		$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid parameter.";
		echo json_encode($a_result);
		exit();
	}

include('../../db.php');
$strDate = date('Y-m-d H:i:s');
$final_approval_id = base64_decode($_POST['dd_id']);
$reject_textarea = $_POST['reject_textarea'];
$sql = "SELECT * FROM tbl_final_approvals WHERE final_approval_id='$final_approval_id'";
$query = mysqli_query($conn,$sql);
if(mysqli_num_rows($query)>0){
    $row = mysqli_fetch_assoc($query);
    $update = "UPDATE tbl_final_approvals SET approval_from_customer = 3 WHERE final_approval_id='$final_approval_id'";
    if(mysqli_query($conn,$update)){
    
    
    $order_main_id=$row['order_main_id'];

	$sql_up_stat = 'UPDATE order_main SET 
				order_main_update_date = "'.$strDate.'",
				order_main_update_user = "OLS",
				order_main_request_update = "1",
				reject_ols_comment = "'.$reject_textarea.'",
				url_push_to_ols = "3"
				WHERE order_main_id = "'.$order_main_id.'" ';
	$query = mysqli_query($conn3,$sql_up_stat);
	if($query){
	    $sql_insert = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id,noti_from_employee_id) ";
		$sql_insert .= " SELECT '".$order_main_id."','Customer rejected','".date("Y-m-d H:i:s")."',order_sale_id,'1' ";
		$sql_insert .= " FROM order_head WHERE order_id='".$order_main_id."' ";
		$query = mysqli_query($conn3,$sql_insert);
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
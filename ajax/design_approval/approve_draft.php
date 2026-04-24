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
	
	$approval_note = $_POST['approval_note'];
	
	$design_note = $_POST['design_note'];
	
	$dd_id = $_POST["dd_id"];
	
	$strDate = date('Y-m-d H:i:s');

	$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

    $user_id = $obj_user->user_id;
	$approve_status = 'approved';
	$approve_time = date("Y-m-d H:i:s");
	// $sql_update = "UPDATE tbl_design_draft SET approve_status='approved',approve_user_id='".$user_id."',design_name='".$design_note."',main_comment='".$approval_note."',approve_time='".date("Y-m-d H:i:s")."' WHERE dd_id='".$dd_id."'";


			$sql_update = "UPDATE tbl_design_draft 
		SET approve_status = ?, 
			approve_user_id = ?, 
			design_name = ?, 
			main_comment = ?, 
			approve_time = ? 
		WHERE dd_id = ?";

		$stmt = $conn->prepare($sql_update);

		

		$stmt->bind_param(
			"sisssi",
			$approve_status,
			$user_id,
			$design_note,
			$approval_note,
			$approve_time,
			$dd_id
		);

	if($stmt->execute()){

		$sql_select = "SELECT * FROM tbl_design_draft WHERE dd_id='".$dd_id."'; ";
		$rs_select = $conn->query($sql_select);
		$row_select = $rs_select->fetch_assoc();
		$order_file_id = $row_select['order_file_id'];
		$order_id = $row_select['order_id'];

		$sql_insert = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id,noti_from_employee_id) ";
		$sql_insert .= " SELECT '".$row_select["order_id"]."','Customer approved','".date("Y-m-d H:i:s")."',order_sale_id,'1' ";
		$sql_insert .= " FROM order_head WHERE order_id='".$row_select["order_id"]."' ";

		//---Connect to Lockerroom DB
		$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
		mysqli_set_charset($conn3, "utf8");

		$conn3->query($sql_insert);
		
		$sql_insert1 = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id,noti_from_employee_id) ";
		$sql_insert1 .= " SELECT '".$row_select["order_id"]."','Customer approved','".date("Y-m-d H:i:s")."',order_design_id,'1' ";
		$sql_insert1 .= " FROM order_head WHERE order_id='".$row_select["order_id"]."' ";
		
		$conn3->query($sql_insert1);
		
		$sql_up_stat = 'UPDATE order_head SET order_status = "5", order_update_date="'.$strDate.'",order_update_user="OLS" WHERE order_id = "'.$order_id.'" ';
		$query = mysqli_query($conn3,$sql_up_stat);

		$sql_up_cus_check = 'UPDATE order_file SET 
					order_file_customer_app = "2" , 
					order_file_customer_app_date = "'.$strDate.'"
					WHERE order_file_id = "'.$order_file_id.'" ';
		$query = mysqli_query($conn3,$sql_up_cus_check);

		$a_result["result"] = "success";
		
	}else{
		
		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to approve!";

	}

	echo json_encode($a_result);

?>

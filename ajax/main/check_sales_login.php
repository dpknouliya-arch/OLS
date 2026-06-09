<?php
	session_start();
	ob_start();
	include('../../db.php');
	//include('function.php');
	$strUser = "";
	$strPass = "";
	if(isset($_POST["user"])){ $strUser = base64_decode($_POST["user"]); }
    if(isset($_POST["password"])){ $strPass = $_POST["password"]; }
	
	$current_brand_id = get_ols_brand_id();
	$stmt = $conn->prepare("SELECT * FROM tbl_user WHERE user_email=? AND user_password=? AND brand_id=? AND enable=1");
	$stmt->bind_param("ssi", $strUser, $strPass, $current_brand_id);
	$stmt->execute();
	$rs = $stmt->get_result();
	$stmt->close();

	if($rs->num_rows==1){

		$row_user = $rs->fetch_assoc();

		$obj_data = array();
		$obj_data["user_id"] = $row_user['user_id'];
		$obj_data["full_name"] = $row_user['full_name'];
		$obj_data["customer_id"] = $row_user['customer_id'];
		$obj_data["user_email"] = $row_user['user_email'];
		$obj_data["user_level"] = $row_user['user_level'];
		$obj_data["brand_id"] = (int)$row_user['brand_id'];

		$s_obj = base64_encode(json_encode($obj_data));

		$_SESSION['JOGOLS'] = $s_obj;
		set_ols_brand_id((int)$row_user['brand_id']);

		echo json_encode(array("result"=>"success","first_login"=>$row_user['first_login']));
	}else{
		echo json_encode(array("result"=>"fail"));
	}

?>

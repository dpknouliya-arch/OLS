<?php

	session_start();

	ob_start();

	include('../../db.php');

	//include('function.php');

	$strUser = "";

	$strPass = "";

	if(isset($_POST["username"])){ $strUser = base64_decode($_POST["username"]); }

    if(isset($_POST["password"])){ $strPass = base64_decode($_POST["password"]); }

	

	$sql = 'SELECT * FROM tbl_sales_user WHERE sales_email="'.$strUser.'" AND s_user_pwd="'.$strPass.'" AND enable=1 ';

	$rs = $conn->query($sql);
	

	if($rs->num_rows==1){



		$row_user = $rs->fetch_assoc();



		$obj_data = array();

		$obj_data["sales_user_id"] = $row_user['sales_user_id'];

		$obj_data["parent_user_id"] = $row_user['parent_user_id'];

		$obj_data["s_user_name"] = $row_user['s_user_name'];

		$obj_data["nick_name"] = $row_user['nick_name'];



		$s_obj = base64_encode(json_encode($obj_data));



		$_SESSION['JOGOLSSALE'] = $s_obj;



		echo json_encode(array("result"=>"success"));

	}else{

		echo json_encode(array("result"=>"fail"));

	}



?>


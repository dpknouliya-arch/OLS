<?php
	session_start();

	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}

	if( !isset($_POST["dc_id_list"]) || $_POST["dc_id_list"]=="" ){

		/*$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid parameter.";
		echo json_encode($a_result);*/
		exit();
	}

	include('../../db.php');
	
	$dc_id_list = $_POST["dc_id_list"];

	$sql_update = "UPDATE tbl_design_comment SET is_read=1 WHERE dc_id IN (".$dc_id_list."); ";

	if($conn->query($sql_update)){

		$a_result["result"] = "success";
		
	}

	echo json_encode($a_result);

?>

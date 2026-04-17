<?php

session_start();



if( !isset($_SESSION["JOGOLS"]) ){



	$a_result["result"] = "fail";

	$a_result["msg"] = "Your login session expired. Please login again.";

	echo json_encode($a_result);

	exit();

}



include('../../db.php');



if( !isset($_POST["s_nick_name"]) || !isset($_POST["s_user_name"]) || !isset($_POST["s_password"]) || !isset($_POST["sub_email"]) ){



	$a_result["result"] = "fail";

	$a_result["msg"] = "Invalid parameter.";

	echo json_encode($a_result);

	exit();

}



$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;



$nick_name = base64_decode($_POST["s_nick_name"]);

$s_user_name = base64_decode($_POST["s_user_name"]);

$s_user_pwd = base64_decode($_POST["s_password"]);

$sub_email = base64_decode($_POST["sub_email"]);



$sql_chk = "SELECT sales_user_id FROM tbl_sales_user WHERE s_user_name='".$s_user_name."';";

$rs_chk = $conn->query($sql_chk);



if($rs_chk->num_rows > 0){



	$a_result["result"] = "fail";

	$a_result["msg"] = "Duplicate User name.";



}else{



	$sql_insert = "INSERT INTO tbl_sales_user (parent_user_id,s_user_name,nick_name,s_user_pwd,sales_email,date_add) VALUES (";

	$sql_insert .= "'".$user_id."','".$s_user_name."','".addslashes($nick_name)."','".$s_user_pwd."','".$sub_email."','".date("Y-m-d H:i:s")."'";

	$sql_insert .= "); ";



	if($conn->query($sql_insert)){



		$a_result["result"] = "success";



		$sub_user_id = $conn->insert_id;



		$inner = '<div class="singleUserBox">';

			$inner .= '<div class="userProfile grid2">';

				$inner .= '<figure class="text-start"><img src="images/pic-4.png" alt=""></figure>';

				$inner .= '<div class="inactive select">';

					$inner .= '<select name="format" class="active_inactive_select" >';

						$inner .= '<option value="Inactive">Inactive</option>';

						$inner .= '<option value="active">Active</option>';

					$inner .= '</select>';

				$inner .= '</div>';

			$inner .= '</div>';

			$inner .= '<div class="userdata">';

				$inner .= '<table class="table border-none overflow-hidden m-0 ">';

					$inner .= '<tbody id="tableBody  ">';

						$inner .= '<tr>';

							$inner .= '<th>Name :</th>';

							$inner .= '<td id="td_nick_name'.$sub_user_id.'">'.$nick_name.'</td>';

						$inner .= '</tr>';

						$inner .= '<tr>';

							$inner .= '<th>User :</th>';

							$inner .= '<td id="td_s_user_name'.$sub_user_id.'">'.$s_user_name.'</td>';

						$inner .= '<tr>';

							$inner .= '<th>Password :</th>';

							$inner .= '<td id="td_s_user_pwd'.$sub_user_id.'">'.$s_user_pwd.'</td>';

						$inner .= '</tr>';

						$inner .= '<tr>';

							$inner .= '<th>Email :</th>';

							$inner .= '<td id="td_sub_email'.$sub_user_id.'">'.$sub_email.'</td>';

						$inner .= '</tr>';

					$inner .= '</tbody>';

				$inner .= '</table>';

			$inner .= '</div>';

			$inner .= '<div class="userContact" id="th_btn_zone'.$sub_user_id.'">';

				$inner .= '<div class="d-flex gap-3 justify-content-end">';

				$inner .= '	<div class="goBackBtn   ">';

					$inner .= '	<button class="goback d-flex gap-3 justify-content-between btn btn-primary" onclick="return editSU('.$sub_user_id.');">';

					$inner .= '		<figure class="m-0">Edit';

					$inner .= '	</button>';

					$inner .= '</div>';

					$inner .= '<div class="goBackBtn   ">		';				

						$inner .= '<button class="btn btn-primary btn_action" onclick="return sendEmailSU('.$sub_user_id.');" title="Send user info to Email.">';

							$inner .= '<i class="fa fa-envelope-o"></i><i class="fa fa-angle-double-right" aria-hidden="true"></i> Mail';

						$inner .= '</button>';

					$inner .= '</div>';

				$inner .= '</div>';

			$inner .= '</div>';

		$inner .= '</div>';



		



		$a_result["inner_new_card"] = $inner;



		$sql_count = "SELECT COUNT(*) AS num_row_su FROM tbl_sub_user WHERE parent_user_id='".$user_id."';";

		$rs_count = $conn->query($sql_count);

		$row_count = $rs_count->fetch_assoc();



		$a_result["num_row_su"] = intval($row_count["num_row_su"]);



	}else{

		$a_result["result"] = "fail";

		$a_result["msg"] = "Fail to add new User";

	}





}



echo json_encode($a_result);



?>


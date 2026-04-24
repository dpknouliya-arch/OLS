<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

if( !isset($_POST["s_user_name"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();
}

$s_user_name = base64_decode($_POST["s_user_name"]);
$email = $_POST['email'] ?? NULL ; 


$stmt = $conn->prepare("SELECT sub_user_id FROM tbl_sub_user WHERE s_user_name=?");
$stmt->bind_param("s", $s_user_name);
$stmt->execute();
$rs_chk = $stmt->get_result();


$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

//Check if email already exists or email already exists for another user 
if($email){
		$stmt = $conn->prepare("SELECT user_email FROM  tbl_user  where  user_email = ? ");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$rs_chk_email = $stmt->get_result();

       if($rs_chk_email->num_rows == 0 ) {
				$stmt = $conn->prepare("SELECT sub_email  FROM  tbl_sub_user  where  sub_email = ?   AND   parent_user_id = ? ");
				$stmt->bind_param("si", $email , $user_id);
				$stmt->execute();
				$rs_chk_email = $stmt->get_result();
	   }

}




if($rs_chk->num_rows > 0  || $rs_chk_email->num_rows > 0){
	$a_result["result"] = "fail";
	$rs_chk->num_rows > 0 ? 	$a_result["username"] = "Fail" : "" ; 
	$rs_chk_email->num_rows > 0 ? 	$a_result["email"] = "Fail" : "" ; 

}else{
	$a_result["result"] = "success";
}

echo json_encode($a_result);
?>
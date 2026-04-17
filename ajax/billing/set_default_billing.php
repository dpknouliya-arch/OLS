<?php
	session_start();

	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}



	include('../../db.php');

	$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
	$user_id = $obj_user->user_id;

	$TOKEN_KEY = 'jogsports_secure_key_' . session_id();
	function decryptAddrId($encrypted_data, $key) {
		$data = base64_decode($encrypted_data);
		$parts = explode('::', $data); 
		if (count($parts) !== 2) {
			return false;
		}
		$encrypted = $parts[0];
		$iv = base64_decode($parts[1]);
		return openssl_decrypt($encrypted, 'AES-128-CBC', $key, 0, $iv);
	}

	$encrypted_addr_id = $_POST["addr_id"];

	$addr_id = decryptAddrId($encrypted_addr_id, $TOKEN_KEY);


	if( !isset($_POST["addr_id"]) || $_POST["addr_id"]=="" || !is_numeric($addr_id) ){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid parameter.";
		echo json_encode($a_result);
		exit();
	}


	$stmt1 = $conn->prepare("UPDATE tbl_address SET is_billing_addr=0 WHERE user_id=?");
	$stmt1->bind_param("i", $user_id);
	$stmt1->execute();
	$stmt1->close();

	$stmt2 = $conn->prepare("UPDATE tbl_address SET is_billing_addr=1 WHERE addr_id=? AND user_id=?");
	$stmt2->bind_param("ii", $addr_id, $user_id);

	if($stmt2->execute()){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Setting fail!";

	}

	$stmt2->close();
	echo json_encode($a_result);

?>

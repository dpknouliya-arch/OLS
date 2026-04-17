<?php
	session_start();

	$TOKEN_KEY = 'jogsports_secure_key_' . session_id();

	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}

	include('../../db.php');
	
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

	$stmt = $conn->prepare("UPDATE tbl_address SET enable=0 WHERE addr_id=?");
	$stmt->bind_param("i", $addr_id);

	if($stmt->execute()){  
		$a_result["result"] = "success";
	}else{
		$a_result["result"] = "fail";
		$a_result["msg"] = "Delete fail!";

	}

	echo json_encode($a_result);

?>

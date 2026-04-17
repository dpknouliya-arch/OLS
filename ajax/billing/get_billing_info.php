<?php
	session_start();
	$TOKEN_KEY = 'jogsports_secure_key_' . session_id();
	
	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}

	if( !isset($_POST["addr_id"]) || $_POST["addr_id"]=="" ){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid parameter.";
		echo json_encode($a_result);
		exit();
	}

	include('../../db.php');

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

	if ($addr_id === false || !is_numeric($addr_id)) {
		$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid address ID.";
		echo json_encode($a_result);
		exit();
	}

	// Use prepared statement to prevent SQL injection
	$stmt = $conn->prepare("SELECT * FROM tbl_address WHERE addr_id = ?");
	$stmt->bind_param("i", $addr_id);
	$stmt->execute();
	$rs_select = $stmt->get_result();
	$row_addr = $rs_select->fetch_assoc();

	$a_result["company_name"] = $row_addr["addr_name"];
	$a_result["contact"] = $row_addr["contact_name"];
	$a_result["address_info"] = $row_addr["address"];
	$a_result["city"] = $row_addr["city"];
	$a_result["country"] = $row_addr["country"];
	$a_result["zipcode"] = $row_addr["zip_code"];
	$a_result["tel"] = $row_addr["tel"];
	$a_result["email_info"] = $row_addr["email"];
	$a_result["tax_no"] = $row_addr["tax_id"];

	$a_result["result"] = "success";

	echo json_encode($a_result);

?>

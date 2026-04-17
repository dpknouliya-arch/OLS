<?php
	session_start();

	if(!isset($_SESSION["JOGOLS"])){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Your login session expired. Please login again.";
		echo json_encode($a_result);
		exit();
	}

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

	$encrypted_addr_id = $_POST["edit_addr_id"];

	$addr_id = decryptAddrId($encrypted_addr_id, $TOKEN_KEY);




	if( !isset($_POST["edit_addr_id"]) || $_POST["edit_addr_id"]=="" || !is_numeric($addr_id) ){
		$a_result["result"] = "fail";
		$a_result["msg"] = "Invalid parameter.";
		echo json_encode($a_result);
		exit();
	}

	include('../../db.php');

	$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
	$user_id = $obj_user->user_id;

	$addr_name = $_POST["company_name"];
	$contact_name = $_POST["contact"];
	$address = $_POST["address_info"];
	$city = $_POST["city"];
	$country = $_POST["country"];
	$zip_code = $_POST["zipcode"];
	$tel = $_POST["tel"];
	$email = $_POST["email_info"];
	$tax_id = $_POST["tax_no"];

	$stmt = $conn->prepare("UPDATE tbl_address SET addr_name=?, contact_name=?, address=?, city=?, country=?, zip_code=?, tel=?, email=?, tax_id=? WHERE addr_id=? AND user_id=?");
	$stmt->bind_param("sssssssssii", $addr_name, $contact_name, $address, $city, $country, $zip_code, $tel, $email, $tax_id, $addr_id, $user_id);

	if($stmt->execute()){

		$a_result["result"] = "saved";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Edit info fail!";

	}

	$stmt->close();
	echo json_encode($a_result);

?>

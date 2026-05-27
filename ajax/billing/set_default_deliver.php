<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$TOKEN_KEY = 'jogsports_secure_key_' . session_id();


function decryptAddrId($encrypted_data, $key)
{
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


$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;
$brand_id = get_ols_brand_id();

$sql_update1 = "UPDATE tbl_address SET is_deliver_addr = 0 WHERE user_id = ? AND brand_id = ?";
$stmt1 = $conn->prepare($sql_update1);

if ($stmt1) {
	$stmt1->bind_param("ii", $user_id, $brand_id);
	$stmt1->execute();
	$stmt1->close();
} else {
	$a_result["result"] = "fail";
	$a_result["msg"] = "Prepare failed (update1)";
	echo json_encode($a_result);
	exit;
}

// Update selected address to 1
$sql_update2 = "UPDATE tbl_address SET is_deliver_addr = 1 WHERE addr_id = ?";
$stmt2 = $conn->prepare($sql_update2);

if ($stmt2) {
	$stmt2->bind_param("i", $addr_id);

	if ($stmt2->execute()) {
		$a_result["result"] = "success";
	} else {
		$a_result["result"] = "fail";
		$a_result["msg"] = "Setting fail!";
	}

	$stmt2->close();
} else {
	$a_result["result"] = "fail";
	$a_result["msg"] = "Prepare failed (update2)";
}

echo json_encode($a_result);

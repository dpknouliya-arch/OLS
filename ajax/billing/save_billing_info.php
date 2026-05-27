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
    $brand_id = get_ols_brand_id();
    $date_add = date("Y-m-d H:i:s");

	$addr_name = $_POST["company_name"];
	$contact_name = $_POST["contact"];
    $address = $_POST["address_info"];
    $city = $_POST["city"];
    $country = $_POST["country"];
    $zip_code = $_POST["zipcode"];
    $tel = $_POST["tel"];
    $email = $_POST["email_info"];
    $tax_id = $_POST["tax_no"];

	$stmt = $conn->prepare("INSERT INTO tbl_address (addr_name,contact_name,address,city,country,zip_code,tel,email,tax_id,user_id,brand_id,date_add) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssssssiis", $addr_name, $contact_name, $address, $city, $country, $zip_code, $tel, $email, $tax_id, $user_id, $brand_id, $date_add);

	if($stmt->execute()){

		$a_result["result"] = "saved";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Save info fail!";

	}

	$stmt->close();
	echo json_encode($a_result);

?>

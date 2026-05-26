<?php

	session_start();

	ob_start();

	include('../../db.php');

	//include('function.php');

	$strUser = "";

	$strPass = "";


	if(isset($_POST["user"])){ $strUser = base64_decode($_POST["user"]); }

    if(isset($_POST["password"])){ $strPass = md5(base64_decode($_POST["password"])); }
	$brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : 1;

	// Step 1: check if email exists for this brand
	$rs = $conn->query("SELECT * FROM tbl_user WHERE user_email='$strUser' AND brand_id=$brand_id AND enable=1");


	if($rs->num_rows==1){


		$row_user = $rs->fetch_assoc();



		$obj_data = array();

		$obj_data["user_id"] = $row_user['user_id'];

		$obj_data["full_name"] = $row_user['full_name'];

		$obj_data["customer_id"] = $row_user['customer_id'];

		$obj_data["user_email"] = $row_user['user_email'];

		$obj_data["user_level"] = $row_user['user_level'];



		$s_obj = base64_encode(json_encode($obj_data));



		$_SESSION['JOGOLS'] = $s_obj;

		function apiLogin($user, $password) {

			$url = OLS_BASE_URL . "api/login.php";

			$postData = json_encode([
				"email" => base64_decode($user), // ✅ decode email
				"password" => base64_decode($password)          // ✅ plain password
			]);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Content-Type: application/json"
			]);

			$res = curl_exec($ch);
		
			if (curl_errno($ch)) {
				print_r($url); // ✅ debug: show what was sent
				echo "Curl Error: " . curl_error($ch);
				curl_close($ch);
				return null;
			}

			curl_close($ch);

			$data = json_decode($res, true);

			return $data['token'] ?? null;
		}

		$_SESSION['API_TOKEN'] = apiLogin($_POST["user"], $_POST["password"]);

		echo json_encode(array(
			"result" => "success",
			"first_login" => $row_user['first_login'],
			"s_obj" => $s_obj // ✅ return encoded user details
		));

	}else{

		echo json_encode(array("result"=>"fail"));

	}



?>


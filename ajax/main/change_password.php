<?php
session_start();
//ob_start();

$user_id = $_POST["user_id"];
$activate_key = $_POST["confirm_key"];
$new_password = $_POST["new_password"];
$confirm_password = $_POST["confirm_password"];

$is_ios = $_POST['is_ios']; 

if( ($new_password=="") || ($new_password!=$confirm_password) ){

	echo "Confirm password not match.";
	exit();
}

include('../../db.php');

$token = bin2hex(random_bytes(32)); // 64 char token
$expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));
$update_condition = $is_ios ?  " , auth_token = '$token' , token_expiry = '$expiry'" : "" ; 
 
$sql_update = "UPDATE tbl_user SET enable=1,user_password='".md5($new_password)."',activate_key='',last_active='".date("Y-m-d H:i:s")."'  $update_condition  WHERE user_id='".$user_id."' AND activate_key='".$activate_key."'; ";

// echo $sql_update ; die ;

if($conn->query($sql_update)){
	
	$sql_select = "SELECT user_id,full_name,customer_id,user_email,user_level FROM tbl_user WHERE user_id='".$user_id."'; ";
	$rs_select = $conn->query($sql_select);
	$row_user = $rs_select->fetch_assoc();

	$obj_data = array();
	$obj_data["user_id"] = $user_id;
	$obj_data["full_name"] = $row_user['full_name'];
	$obj_data["customer_id"] = $row_user['customer_id'];
	$obj_data["user_email"] = $row_user['user_email'];
	$obj_data["user_level"] = $row_user['user_level'];

	$s_obj = base64_encode(json_encode($obj_data));

	$_SESSION['JOGOLS'] = $s_obj;


	$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
	mysqli_set_charset($conn3, "utf8");

	$sql_select2 = "SELECT * FROM customer WHERE customer_id='".$obj_data["customer_id"]."'; ";
	$rs_customer = $conn3->query($sql_select2); //--Pull from LKR

	if( $rs_customer->num_rows > 0 ){
		$row_customer = $rs_customer->fetch_assoc();

		$tmp_address = $row_customer["customer_address"]."\n".$row_customer["customer_state"];

		$sql_insert = "INSERT INTO tbl_address (addr_name,address,city,country,zip_code,tel,email,tax_id,user_id,is_billing_addr,is_deliver_addr,date_add) VALUES ('".addslashes($row_customer["customer_name"])."','".addslashes($tmp_address)."','".addslashes($row_customer["customer_city"])."','".addslashes($row_customer["customer_country"])."','".addslashes($row_customer["customer_postcode"])."','".addslashes($row_customer["customer_tel"])."','".addslashes($row_customer["customer_email"])."','".addslashes($row_customer["customer_tax_id"])."','".$user_id."',1,1,'".date("Y-m-d H:i:s")."');";

		$conn->query($sql_insert); //--- Insert into OLS

	}

	/*if( (strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d 18:00:00"))) || (strtotime(date("Y-m-d H:i:s")) < strtotime(date("Y-m-d 08:00:00"))) ){
		$exp_in = 3600; // 1 hour
	}else{
		$exp_in = 36000; // 10 hours
	}*/


	/*if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
		$pageURL = 'https';

	}else{
		$pageURL = 'http';

	}

	$pageURL .= '://';

	if($_SERVER['SERVER_PORT']!='80'){
		$pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].''.$_SERVER['REQUEST_URI'];

	}else{
		$pageURL .= $_SERVER['SERVER_NAME'].''.$_SERVER['REQUEST_URI'];

	}*/
   if($is_ios){
	   echo json_encode(['status' => 'success' , 'authToken' => $token]); 
	   exit ; 
   }
	$result = "success";

}else{
	
	$result = "Fail to save password.";
}

echo $result;
?>
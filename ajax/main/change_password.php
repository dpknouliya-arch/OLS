<?php
session_start();
//ob_start();

$user_id = $_POST["user_id"];
$activate_key = $_POST["confirm_key"];
$new_password = $_POST["new_password"];
$confirm_password = $_POST["confirm_password"];
$brand_id = (int)($_POST["brand_id"] ?? 1);
$is_ios = $_POST['is_ios'];

if( ($new_password=="") || ($new_password!=$confirm_password) ){

	echo "Confirm password not match.";
	exit();
}

include('../../db.php');

$sql_current = "SELECT user_password FROM tbl_user WHERE user_id='" . $conn->real_escape_string($user_id) . "' AND activate_key='" . $conn->real_escape_string($activate_key) . "';";
$rs_current = $conn->query($sql_current);
if ($rs_current && $rs_current->num_rows > 0) {
	$row_current = $rs_current->fetch_assoc();
	if ($row_current['user_password'] === md5($new_password)) {
		echo "New password must be different from your current password.";
		exit();
	}
}

$token = bin2hex(random_bytes(32)); // 64 char token
$expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));
$update_condition = $is_ios ?  " , auth_token = '$token' , token_expiry = '$expiry'" : "" ;

$sql_update = "UPDATE tbl_user SET enable=1,user_password='".md5($new_password)."',activate_key='',last_active='".date("Y-m-d H:i:s")."'  $update_condition  WHERE user_id='".$user_id."' AND activate_key='".$activate_key."'; ";

// echo $sql_update ; die ;

if($conn->query($sql_update)){

	// Set brand cookie so the login page shows the correct brand
	set_ols_brand_id($brand_id);

	// Fetch customer_id for address sync (no session created)
	$sql_select = "SELECT customer_id FROM tbl_user WHERE user_id='".$user_id."'; ";
	$rs_select = $conn->query($sql_select);
	$row_user = $rs_select->fetch_assoc();
	$customer_id = $row_user['customer_id'] ?? 0;

	$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
	mysqli_set_charset($conn3, "utf8");

	$sql_select2 = "SELECT * FROM customer WHERE customer_id='".$customer_id."'; ";
	$rs_customer = $conn3->query($sql_select2); //--Pull from LKR

	if( $rs_customer->num_rows > 0 ){
		$row_customer = $rs_customer->fetch_assoc();

		$tmp_address = $row_customer["customer_address"]."\n".$row_customer["customer_state"];

		$sql_insert = "INSERT INTO tbl_address (addr_name,address,city,country,zip_code,tel,email,tax_id,user_id,is_billing_addr,is_deliver_addr,date_add) VALUES ('".addslashes($row_customer["customer_name"])."','".addslashes($tmp_address)."','".addslashes($row_customer["customer_city"])."','".addslashes($row_customer["customer_country"])."','".addslashes($row_customer["customer_postcode"])."','".addslashes($row_customer["customer_tel"])."','".addslashes($row_customer["customer_email"])."','".addslashes($row_customer["customer_tax_id"])."','".$user_id."',1,1,'".date("Y-m-d H:i:s")."');";

		$conn->query($sql_insert); //--- Insert into OLS

	}

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

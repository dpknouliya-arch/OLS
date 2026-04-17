<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

include('../../db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

$condition_case = $_GET["condition_case"];

$bi_addr_id = $_POST["bill_addr_id"];
$de_addr_id = $_POST["deli_addr_id"];

$bi_company_name = $_POST["company_name"];
$bi_contact = $_POST["contact"];
$bi_address = $_POST["address_info"];
$bi_city = $_POST["city"];
$bi_country = $_POST["country"];
$bi_zip_code = $_POST["zip_code"];
$bi_tel = $_POST["tel"];
$bi_email = $_POST["email"];
$bi_tax_id = $_POST["tax_id"];

$de_company_name = $_POST["d_company_name"];
$de_contact = $_POST["d_contact"];
$de_address = $_POST["d_address_info"];
$de_city = $_POST["d_city"];
$de_country = $_POST["d_country"];
$de_zip_code = $_POST["d_zip_code"];
$de_tel = $_POST["d_tel"];
$de_email = $_POST["d_email"];
$de_tax_id = $_POST["d_tax_id"];

if($condition_case=="1"){

	$sql_update = "UPDATE tbl_address SET addr_name='".addslashes($bi_company_name)."',contact_name='".addslashes($bi_contact)."',address='".addslashes($bi_address)."'";
	$sql_update .= ",city='".addslashes($bi_city)."',country='".addslashes($bi_country)."',zip_code='".addslashes($bi_zip_code)."',tel='".addslashes($bi_tel)."'";
	$sql_update .= ",email='".addslashes($bi_email)."',tax_id='".addslashes($bi_tax_id)."',is_billing_addr=1,is_deliver_addr=1 ";
	$sql_update .= " WHERE addr_id='".$bi_addr_id."'; ";

	if($conn->query($sql_update)){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to update address...";

	}
	
}else if($condition_case=="2"){

	$sql_update = "UPDATE tbl_address SET addr_name='".addslashes($bi_company_name)."',contact_name='".addslashes($bi_contact)."',address='".addslashes($bi_address)."'";
	$sql_update .= ",city='".addslashes($bi_city)."',country='".addslashes($bi_country)."',zip_code='".addslashes($bi_zip_code)."',tel='".addslashes($bi_tel)."'";
	$sql_update .= ",email='".addslashes($bi_email)."',tax_id='".addslashes($bi_tax_id)."',is_billing_addr=1,is_deliver_addr=0 ";
	$sql_update .= " WHERE addr_id='".$bi_addr_id."'; ";
	$conn->query($sql_update);

	$sql_insert = "INSERT INTO tbl_address (addr_name,contact_name,address,city,country,zip_code,tel,email,tax_id,is_billing_addr,is_deliver_addr,user_id,date_add) VALUES (";
	$sql_insert .= "'".addslashes($de_company_name)."','".addslashes($de_contact)."','".addslashes($de_address)."','".addslashes($de_city)."','".addslashes($de_country)."',";
	$sql_insert .= "'".addslashes($de_zip_code)."','".addslashes($de_tel)."','".addslashes($de_email)."','".addslashes($de_tax_id)."',0,1,'".$user_id."','".date("Y-m-d H:i:s")."'";
	$sql_insert .= "); ";

	if($conn->query($sql_insert)){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to update address...";

	}
}else if($condition_case=="3"){

	$sql_update = "UPDATE tbl_address SET addr_name='".addslashes($bi_company_name)."',contact_name='".addslashes($bi_contact)."',address='".addslashes($bi_address)."'";
	$sql_update .= ",city='".addslashes($bi_city)."',country='".addslashes($bi_country)."',zip_code='".addslashes($bi_zip_code)."',tel='".addslashes($bi_tel)."'";
	$sql_update .= ",email='".addslashes($bi_email)."',tax_id='".addslashes($bi_tax_id)."',is_billing_addr=1,is_deliver_addr=0 ";
	$sql_update .= " WHERE addr_id='".$bi_addr_id."'; ";
	$conn->query($sql_update);

	$sql_update2 = "UPDATE tbl_address SET addr_name='".addslashes($de_company_name)."',contact_name='".addslashes($de_contact)."',address='".addslashes($de_address)."'";
	$sql_update2 .= ",city='".addslashes($de_city)."',country='".addslashes($de_country)."',zip_code='".addslashes($de_zip_code)."',tel='".addslashes($de_tel)."'";
	$sql_update2 .= ",email='".addslashes($de_email)."',tax_id='".addslashes($de_tax_id)."',is_billing_addr=0,is_deliver_addr=1 ";
	$sql_update2 .= " WHERE addr_id='".$de_addr_id."'; ";
	
	if($conn->query($sql_update2)){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to update address...";

	}

}else if($condition_case=="4"){

	$sql_update = "UPDATE tbl_address SET is_billing_addr=0,is_deliver_addr=0 ";
	$sql_update .= " WHERE addr_id IN (".$bi_addr_id.",".$de_addr_id."); ";
	$conn->query($sql_update);

	$sql_insert = "INSERT INTO tbl_address (addr_name,contact_name,address,city,country,zip_code,tel,email,tax_id,is_billing_addr,is_deliver_addr,user_id,date_add) VALUES (";
	$sql_insert .= "'".addslashes($de_company_name)."','".addslashes($de_contact)."','".addslashes($de_address)."','".addslashes($de_city)."','".addslashes($de_country)."',";
	$sql_insert .= "'".addslashes($de_zip_code)."','".addslashes($de_tel)."','".addslashes($de_email)."','".addslashes($de_tax_id)."',1,1,'".$user_id."','".date("Y-m-d H:i:s")."'";
	$sql_insert .= "); ";

	if($conn->query($sql_insert)){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to update address...";

	}

}else if($condition_case=="5"){

	$sql_update = "UPDATE tbl_address SET is_billing_addr=1,is_deliver_addr=1 ";
	$sql_update .= " WHERE addr_id='".$bi_addr_id."'; ";
	$conn->query($sql_update);

	$sql_update2 = "UPDATE tbl_address SET is_billing_addr=0,is_deliver_addr=0 ";
	$sql_update2 .= " WHERE addr_id='".$de_addr_id."'; ";
	if($conn->query($sql_update2)){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to update address...";

	}

}else if($condition_case=="6"){

	$sql_update = "UPDATE tbl_address SET is_billing_addr=1,is_deliver_addr=1 ";
	$sql_update .= " WHERE addr_id='".$de_addr_id."'; ";
	$conn->query($sql_update);

	$sql_update2 = "UPDATE tbl_address SET is_billing_addr=0,is_deliver_addr=0 ";
	$sql_update2 .= " WHERE addr_id='".$bi_addr_id."'; ";
	if($conn->query($sql_update2)){

		$a_result["result"] = "success";

	}else{

		$a_result["result"] = "fail";
		$a_result["msg"] = "Error: Fail to update address...";

	}

}

echo json_encode($a_result);
?>
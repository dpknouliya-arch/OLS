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

	$sql_select = "SELECT * FROM tbl_address WHERE addr_id='".$bi_addr_id."'; ";
	$rs_select = $conn->query($sql_select);
	$row_select = $rs_select->fetch_assoc();

	$is_match = true;
	if($bi_company_name!=$row_select["addr_name"]){ $is_match = false; }
	if($bi_contact!=$row_select["contact_name"]){ $is_match = false; }
	if($bi_address!=$row_select["address"]){ $is_match = false; }
	if($bi_city!=$row_select["city"]){ $is_match = false; }
	if($bi_country!=$row_select["country"]){ $is_match = false; }
	if($bi_zip_code!=$row_select["zip_code"]){ $is_match = false; }
	if($bi_tel!=$row_select["tel"]){ $is_match = false; }
	if($bi_email!=$row_select["email"]){ $is_match = false; }
	if($bi_tax_id!=$row_select["tax_id"]){ $is_match = false; }

	if($is_match){

		$a_result["result"] = "no_update";

	}else{

		$a_result["result"] = "found_update";

	}
	
}else if($condition_case=="2"){

	$a_result["bi_update"] = "no";
	$a_result["de_update"] = "no";
	$n_count_update = 0;

	$sql_select = "SELECT * FROM tbl_address WHERE addr_id='".$bi_addr_id."'; ";
	$rs_select = $conn->query($sql_select);
	$row_select = $rs_select->fetch_assoc();

	$is_match = true;
	
	if($bi_company_name!=$row_select["addr_name"]){ $is_match = false; }
	if($bi_contact!=$row_select["contact_name"]){ $is_match = false; }
	if($bi_address!=$row_select["address"]){ $is_match = false; }
	if($bi_city!=$row_select["city"]){ $is_match = false; }
	if($bi_country!=$row_select["country"]){ $is_match = false; }
	if($bi_zip_code!=$row_select["zip_code"]){ $is_match = false; }
	if($bi_tel!=$row_select["tel"]){ $is_match = false; }
	if($bi_email!=$row_select["email"]){ $is_match = false; }
	if($bi_tax_id!=$row_select["tax_id"]){ $is_match = false; }

	if(!$is_match){

		$a_result["bi_update"] = "yes";
		$n_count_update++;

	}

	$sql_select2 = "SELECT * FROM tbl_address WHERE addr_id='".$de_addr_id."'; ";
	$rs_select2 = $conn->query($sql_select2);
	$row_select2 = $rs_select2->fetch_assoc();

	$is_match = true;
	
	if($de_company_name!=$row_select2["addr_name"]){ $is_match = false; }
	if($de_contact!=$row_select2["contact_name"]){ $is_match = false; }
	if($de_address!=$row_select2["address"]){ $is_match = false; }
	if($de_city!=$row_select2["city"]){ $is_match = false; }
	if($de_country!=$row_select2["country"]){ $is_match = false; }
	if($de_zip_code!=$row_select2["zip_code"]){ $is_match = false; }
	if($de_tel!=$row_select2["tel"]){ $is_match = false; }
	if($de_email!=$row_select2["email"]){ $is_match = false; }
	if($de_tax_id!=$row_select2["tax_id"]){ $is_match = false; }

	if(!$is_match){

		$a_result["de_update"] = "yes";
		$n_count_update++;

	}

	if($n_count_update==0){

		$a_result["result"] = "no_update";

	}else{

		$a_result["result"] = "found_update";

	}
}

echo json_encode($a_result);
?>
<?php

print_r($_POST);

exit();

/*include('../../db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

$add_date = date("Y-m-d H:i:s");

$draft_id = $_POST["edit_draft_id"];

$sql_update1 = "UPDATE tbl_draft_of SET order_date='".$_POST["order_date"]."',req_due_date='".$_POST["req_due_date"]."',customer_po='".addslashes($_POST["customer_po"])."',payment_opt='".$_POST["payment_opt"]."'";
$sql_update1 .= ",user_id='".$user_id."',bill_comp_name='".addslashes($_POST["company_name"])."',bill_contact_name='".addslashes($_POST["contact"])."',bill_address='".addslashes($_POST["address_info"])."'";
$sql_update1 .= ",bill_city='".addslashes($_POST["city"])."',bill_country='".addslashes($_POST["country"])."',bill_zip_code='".addslashes($_POST["zip_code"])."',bill_tel='".addslashes($_POST["tel"])."'";
$sql_update1 .= ",bill_email='".addslashes($_POST["email"])."',bill_tax_id='".addslashes($_POST["tax_id"])."',deli_comp_name='".addslashes($_POST["d_company_name"])."',deli_contact_name='".addslashes($_POST["d_contact"])."'";
$sql_update1 .= ",deli_address='".addslashes($_POST["d_address_info"])."',deli_city='".addslashes($_POST["d_city"])."',deli_country='".addslashes($_POST["d_country"])."',deli_zip_code='".addslashes($_POST["d_zip_code"])."'";
$sql_update1 .= ",deli_tel='".addslashes($_POST["d_tel"])."',deli_email='".addslashes($_POST["d_email"])."',deli_tax_id='".addslashes($_POST["d_tax_id"])."'";
$sql_update1 .= " WHERE draft_id='".$draft_id."'; ";
$conn->query($sql_update1);

if($_POST["oi_id_delete"]!=""){
	$sql_delete_oi = "DELETE FROM tbl_draft_oi WHERE oi_id IN (".$_POST["oi_id_delete"]."); ";
	$conn->query($sql_delete_oi);
}

if($_POST["of_id_delete"]!=""){
	$sql_delete_of = "DELETE FROM tbl_draft_of WHERE of_id IN (".$_POST["of_id_delete"]."); ";
	$conn->query($sql_delete_of);

	$sql_delete_oi2 = "DELETE FROM tbl_draft_oi WHERE of_id IN (".$_POST["of_id_delete"]."); ";
	$conn->query($sql_delete_oi2);
}

for( $i=0; $i<sizeof($_POST["prod_id_list"]); $i++ ){

	$prod_id = $_POST["prod_id_list"][$i];

	$loop = 0;
	if(isset($_POST["jersey_qty"][$prod_id])){
		$loop = sizeof($_POST["jersey_qty"][$prod_id]);
	}

	if($loop>0){

		$of_id = $_POST["edit_of_id"][$i];

		if($of_id=="new"){

			$sql_insert1 = "INSERT INTO tbl_draft_of (";
			$sql_insert1 .= "order_date,req_due_date,customer_po,payment_opt,prod_id";
			$sql_insert1 .= ",user_id,bill_comp_name,bill_contact_name,bill_address,bill_city,bill_country";
			$sql_insert1 .= ",bill_zip_code,bill_tel,bill_email,bill_tax_id,deli_comp_name,deli_contact_name";
			$sql_insert1 .= ",deli_address,deli_city,deli_country,deli_zip_code,deli_tel,deli_email";
			$sql_insert1 .= ",deli_tax_id,date_add,draft_id) VALUES (";
			$sql_insert1 .= "'".$_POST["order_date"]."','".$_POST["req_due_date"]."','".addslashes($_POST["customer_po"])."','".$_POST["payment_opt"]."','".$prod_id."'";
			$sql_insert1 .= ",'".$user_id."','".addslashes($_POST["company_name"])."','".addslashes($_POST["contact"])."','".addslashes($_POST["address_info"])."','".addslashes($_POST["city"])."','".addslashes($_POST["country"])."'";
			$sql_insert1 .= ",'".addslashes($_POST["zip_code"])."','".addslashes($_POST["tel"])."','".addslashes($_POST["email"])."','".addslashes($_POST["tax_id"])."','".addslashes($_POST["d_company_name"])."','".addslashes($_POST["d_contact"])."'";
			$sql_insert1 .= ",'".addslashes($_POST["d_address_info"])."','".addslashes($_POST["d_city"])."','".addslashes($_POST["d_country"])."','".addslashes($_POST["d_zip_code"])."','".addslashes($_POST["d_tel"])."','".addslashes($_POST["d_email"])."'";
			$sql_insert1 .= ",'".addslashes($_POST["d_tax_id"])."','".$add_date."','".$draft_id."'); ";

			$conn->query($sql_insert1);
			$of_id = $conn->insert_id;

		}

		for( $j=0; $j<$loop; $j++ ){

			$player_name = (isset($_POST["player_name"][$prod_id][$j]))?$_POST["player_name"][$prod_id][$j]:"";
			$p_or_g = (isset($_POST["select_pg"][$prod_id][$j]))?$_POST["select_pg"][$prod_id][$j]:"";
			$sex = (isset($_POST["select_mf"][$prod_id][$j]))?$_POST["select_mf"][$prod_id][$j]:"";
			$product_size = (isset($_POST["select_jsize"][$prod_id][$j]))?$_POST["select_jsize"][$prod_id][$j]:"";
			$jersey_number = (isset($_POST["jersey_number"][$prod_id][$j]))?$_POST["jersey_number"][$prod_id][$j]:"";
			$color_top1 = (isset($_POST["jersey_color"][$prod_id][$j]))?$_POST["jersey_color"][$prod_id][$j]:"";
			$qty_top1 = (isset($_POST["jersey_qty"][$prod_id][$j]))?$_POST["jersey_qty"][$prod_id][$j]:"";
			$color_top2 = (isset($_POST["jersey_color2"][$prod_id][$j]))?$_POST["jersey_color2"][$prod_id][$j]:"";
			$qty_top2 = (isset($_POST["jersey_qty2"][$prod_id][$j]))?$_POST["jersey_qty2"][$prod_id][$j]:"";
			$bottom_size = (isset($_POST["select_ssize"][$prod_id][$j]))?$_POST["select_ssize"][$prod_id][$j]:"";
			$color_bottom1 = (isset($_POST["sock_color"][$prod_id][$j]))?$_POST["sock_color"][$prod_id][$j]:"";
			$qty_bottom1 = (isset($_POST["sock_qty"][$prod_id][$j]))?$_POST["sock_qty"][$prod_id][$j]:"";
			$color_bottom2 = (isset($_POST["sock_color2"][$prod_id][$j]))?$_POST["sock_color2"][$prod_id][$j]:"";
			$qty_bottom2 = (isset($_POST["sock_qty2"][$prod_id][$j]))?$_POST["sock_qty2"][$prod_id][$j]:"";
			$c_or_a = (isset($_POST["select_ca"][$prod_id][$j]))?$_POST["select_ca"][$prod_id][$j]:"";
			$note = (isset($_POST["note"][$prod_id][$j]))?$_POST["note"][$prod_id][$j]:"";

			if($_POST["a_oi_id"][$prod_id][$j]=="new"){

				$sql_insert2 = "INSERT INTO tbl_draft_oi (";
				$sql_insert2 .= "of_id,player_name,p_or_g,sex,product_size_id,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note) VALUES (";
				$sql_insert2 .= "'".$of_id."','".addslashes($player_name)."','".$p_or_g."','".$sex."','".$product_size."','".addslashes($jersey_number)."','".addslashes($color_top1)."','".$qty_top1."','".addslashes($color_top2)."'";
				$sql_insert2 .= ",'".$qty_top2."','".$bottom_size."','".addslashes($color_bottom1)."','".$qty_bottom1."','".addslashes($color_bottom2)."','".$qty_bottom2."','".$c_or_a."','".addslashes($note)."');";
				//of_id,player_name,p_or_g,sex,product_size,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note
				$conn->query($sql_insert2);

			}else{

				$oi_id = $_POST["a_oi_id"][$prod_id][$j];

				$sql_update2 = "UPDATE tbl_draft_oi SET player_name='".addslashes($player_name)."',p_or_g='".$p_or_g."',sex='".$sex."',product_size_id='".$product_size."',jersey_number='".addslashes($jersey_number)."'";
				$sql_update2 .= ",color_top1='".addslashes($color_top1)."',qty_top1='".$qty_top1."',color_top2='".addslashes($color_top2)."',qty_top2='".$qty_top2."',bottom_size='".$bottom_size."',color_bottom1='".addslashes($color_bottom1)."'";
				$sql_update2 .= ",qty_bottom1='".$qty_bottom1."',color_bottom2='".addslashes($color_bottom2)."',qty_bottom2='".$qty_bottom2."',c_or_a='".$c_or_a."',note='".addslashes($note)."'";
				$sql_update2 .= " WHERE oi_id='".$oi_id."'; ";
				$conn->query($sql_update2);

			}

		}
		
	}

}

$a_result["result"] = "success";

echo json_encode($a_result);*/
?>
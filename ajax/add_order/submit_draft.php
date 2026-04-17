<?php

session_start();



if( !isset($_SESSION["JOGOLS"]) ){



	$a_result["result"] = "fail";

	$a_result["msg"] = "Your login session expired. Please login again.";

	echo json_encode($a_result);

	exit();

}



include('../../db.php');



// echo "<pre>";

// print_r($_POST);

// print_r($_FILES);

// echo "</pre>";

// die();

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;



$add_date = date("Y-m-d H:i:s");



$draft_id = "JOG".date("YmdHis").$user_id;



foreach($_POST["prod_id_list"] as $form_id => $prod_id){



	//$prod_id = $_POST["prod_id_list"][$i];



	$loop = 0;

	if(isset($_POST["jersey_qty"][$form_id])){

		$loop = sizeof($_POST["jersey_qty"][$form_id]);

	}



// 	if($loop>0){



		$form_name = $_POST["form_name_list"][$form_id];

		$on_team_name = $_POST["on_team_name_list"][$form_id];

		$on_year = $_POST["on_year_list"][$form_id];

		if(isset($_POST["special_comment"][$form_id])){

		    $special_comment = $_POST["special_comment"][$form_id];

		}

		else{

		    $special_comment = "";

		}



		$sql_insert1 = "INSERT INTO tbl_draft_of (";

		$sql_insert1 .= "order_date,req_due_date,game_event_date,customer_po,project_name,payment_opt,sales_rep_id,reorder_num,prod_id";

		$sql_insert1 .= ",user_id,bill_comp_name,bill_contact_name,bill_address,bill_city,bill_country";

		$sql_insert1 .= ",bill_zip_code,bill_tel,bill_email,bill_tax_id,deli_comp_name,deli_contact_name";

		$sql_insert1 .= ",deli_address,deli_city,deli_country,deli_zip_code,deli_tel,deli_email";

		$sql_insert1 .= ",deli_tax_id,date_add,draft_id,form_name,on_team_name,on_year,special_comment) VALUES (";

		$sql_insert1 .= "'".$_POST["order_date"]."','".$_POST["req_due_date"]."','".$_POST["game_event_date"]."','".addslashes($_POST["customer_po"])."','".addslashes($_POST["project_name"])."','".$_POST["payment_opt"]."','".$_POST["sales_rep"]."','".$_POST["reorder_num"]."','".$prod_id."'";

		$sql_insert1 .= ",'".$user_id."','".addslashes($_POST["company_name"])."','".addslashes($_POST["contact"])."','".addslashes($_POST["address_info"])."','".addslashes($_POST["city"])."','".addslashes($_POST["country"])."'";

		$sql_insert1 .= ",'".addslashes($_POST["zip_code"])."','".addslashes($_POST["tel"])."','".addslashes($_POST["email"])."','".addslashes($_POST["tax_id"])."','".addslashes($_POST["d_company_name"])."','".addslashes($_POST["d_contact"])."'";

		$sql_insert1 .= ",'".addslashes($_POST["d_address_info"])."','".addslashes($_POST["d_city"])."','".addslashes($_POST["d_country"])."','".addslashes($_POST["d_zip_code"])."','".addslashes($_POST["d_tel"])."','".addslashes($_POST["d_email"])."'";

		$sql_insert1 .= ",'".addslashes($_POST["d_tax_id"])."','".$add_date."','".$draft_id."','".addslashes($form_name)."','".addslashes($on_team_name)."','".addslashes($on_year)."','".addslashes($special_comment)."'); ";



	//order_date,order_status,req_due_date,customer_po,payment_opt,prod_id,user_id,bill_comp_name,bill_contact_name,bill_address,bill_city,bill_country,bill_zip_code,bill_tel,bill_email,bill_tax_id,deli_comp_name,deli_contact_name,deli_address,deli_city,deli_country,deli_zip_code,deli_tel,deli_email,deli_tax_id,enable,date_add

		$conn->query($sql_insert1);

		$of_id = $conn->insert_id;

        if(isset($_FILES['file_upload']['name'][$form_id])){

		$file_name = $_FILES['file_upload']['name'][$form_id];

		$tmp_file_name = explode(".", $file_name);

		$n_tmp_name = sizeof($tmp_file_name);

		$ext_file = $tmp_file_name[($n_tmp_name-1)];



		if(move_uploaded_file($_FILES['file_upload']['tmp_name'][$form_id],'../../src/upload/'.$of_id.".".$ext_file)){

			$sql_up_id = "UPDATE tbl_draft_of SET xls_name='".$of_id.".".$ext_file."' WHERE of_id='".$of_id."'; ";

			$conn->query($sql_up_id);

		}

        }



		



		for( $j=0; $j<$loop; $j++ ){



			$player_name = (isset($_POST["player_name"][$form_id][$j]))?$_POST["player_name"][$form_id][$j]:"";

			$p_or_g = (isset($_POST["select_pg"][$form_id][$j]))?$_POST["select_pg"][$form_id][$j]:"";

			$sex = (isset($_POST["select_mf"][$form_id][$j]))?$_POST["select_mf"][$form_id][$j]:"";

			$product_size = (isset($_POST["select_jsize"][$form_id][$j]))?$_POST["select_jsize"][$form_id][$j]:"";

			$jersey_number = (isset($_POST["jersey_number"][$form_id][$j]))?$_POST["jersey_number"][$form_id][$j]:"";

			$color_top1 = (isset($_POST["jersey_color"][$form_id][$j]))?$_POST["jersey_color"][$form_id][$j]:"";

			$qty_top1 = (isset($_POST["jersey_qty"][$form_id][$j]))?$_POST["jersey_qty"][$form_id][$j]:"";

			$color_top2 = (isset($_POST["jersey_color2"][$form_id][$j]))?$_POST["jersey_color2"][$form_id][$j]:"";

			$qty_top2 = (isset($_POST["jersey_qty2"][$form_id][$j]))?$_POST["jersey_qty2"][$form_id][$j]:"";

			$bottom_size = (isset($_POST["select_ssize"][$form_id][$j]))?$_POST["select_ssize"][$form_id][$j]:"";

			$color_bottom1 = (isset($_POST["sock_color"][$form_id][$j]))?$_POST["sock_color"][$form_id][$j]:"";

			$qty_bottom1 = (isset($_POST["sock_qty"][$form_id][$j]))?$_POST["sock_qty"][$form_id][$j]:"";

			$color_bottom2 = (isset($_POST["sock_color2"][$form_id][$j]))?$_POST["sock_color2"][$form_id][$j]:"";

			$qty_bottom2 = (isset($_POST["sock_qty2"][$form_id][$j]))?$_POST["sock_qty2"][$form_id][$j]:"";

			$c_or_a = (isset($_POST["select_ca"][$form_id][$j]))?$_POST["select_ca"][$form_id][$j]:"";

			$name_for_packing = (isset($_POST["name_for_packing"][$form_id][$j]))?$_POST["name_for_packing"][$form_id][$j]:"";

			$note = (isset($_POST["note"][$form_id][$j]))?$_POST["note"][$form_id][$j]:"";



			$sql_insert2 = "INSERT INTO tbl_draft_oi (";

			$sql_insert2 .= "of_id,player_name,p_or_g,sex,product_size_id,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,name_for_packing,note) VALUES (";

			$sql_insert2 .= "'".$of_id."','".addslashes($player_name)."','".$p_or_g."','".$sex."','".$product_size."','".addslashes($jersey_number)."','".addslashes($color_top1)."','".$qty_top1."','".addslashes($color_top2)."'";

			$sql_insert2 .= ",'".$qty_top2."','".$bottom_size."','".addslashes($color_bottom1)."','".$qty_bottom1."','".addslashes($color_bottom2)."','".$qty_bottom2."','".$c_or_a."','".addslashes($name_for_packing)."','".addslashes($note)."');";

			//of_id,player_name,p_or_g,sex,product_size,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note

			$conn->query($sql_insert2);

		}

// 	}



}



//for( $i=0; $i<sizeof($_POST["prod_id_list"]); $i++ ){

// foreach($_POST["prod_id_list"] as $form_id => $of_id){



// 	$prod_id = $_POST["prod_id_list"][$form_id];



// 	$form_name = $_POST["form_name_list"][$form_id];

	



// 		$on_team_name = $_POST["on_team_name_list"][$form_id];

// 		$on_year = $_POST["on_year_list"][$form_id];

		

// 		$s_insert = "INSERT INTO tbl_draft_of (";

// 		$s_insert .= "order_date,req_due_date,customer_po,payment_opt,prod_id";

// 		$s_insert .= ",user_id,bill_comp_name,bill_contact_name,bill_address,bill_city,bill_country";

// 		$s_insert .= ",bill_zip_code,bill_tel,bill_email,bill_tax_id,deli_comp_name,deli_contact_name";

// 		$s_insert .= ",deli_address,deli_city,deli_country,deli_zip_code,deli_tel,deli_email";

// 		if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){

// 			$s_insert .= ",is_assigned,assign_user_id";

// 		}

// 		$s_insert .= ",deli_tax_id,date_add,draft_id,form_name,on_team_name,on_year) VALUES (";

// 		$s_insert .= "'".$_POST["order_date"]."','".$_POST["req_due_date"]."','".addslashes($_POST["customer_po"])."','".$_POST["payment_opt"]."','".$prod_id."'";

// 		$s_insert .= ",'".$user_id."','".addslashes($_POST["company_name"])."','".addslashes($_POST["contact"])."','".addslashes($_POST["address_info"])."','".addslashes($_POST["city"])."','".addslashes($_POST["country"])."'";

// 		$s_insert .= ",'".addslashes($_POST["zip_code"])."','".addslashes($_POST["tel"])."','".addslashes($_POST["email"])."','".addslashes($_POST["tax_id"])."','".addslashes($_POST["d_company_name"])."','".addslashes($_POST["d_contact"])."'";

// 		$s_insert .= ",'".addslashes($_POST["d_address_info"])."','".addslashes($_POST["d_city"])."','".addslashes($_POST["d_country"])."','".addslashes($_POST["d_zip_code"])."','".addslashes($_POST["d_tel"])."','".addslashes($_POST["d_email"])."'";

// 		if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){

// 			$s_insert .= ",'1','".$_POST["select_assign"][$form_id]."'";

// 		}

// 		$s_insert .= ",'".addslashes($_POST["d_tax_id"])."','".$add_date."','".$draft_id."','".addslashes($form_name)."','".addslashes($on_team_name)."','".addslashes($on_year)."'); ";



// 		$conn->query($s_insert);

// 		$of_id = $conn->insert_id;

//         if(isset($_FILES['file_upload']['name'][$form_id])){

// 		$file_name = $_FILES['file_upload']['name'][$form_id];

// 		$tmp_file_name = explode(".", $file_name);

// 		$n_tmp_name = sizeof($tmp_file_name);

// 		$ext_file = $tmp_file_name[($n_tmp_name-1)];



// 		if(move_uploaded_file($_FILES['file_upload']['tmp_name'][$form_id],'../../src/upload/'.$of_id.".".$ext_file)){

// 			$sql_up_id = "UPDATE tbl_draft_of SET xls_name='".$of_id.".".$ext_file."' WHERE of_id='".$of_id."'; ";

// 			$conn->query($sql_up_id);

// 		}

//         }

// }



$url = "https://ols-test.jog-joinourgame.com/?vp=bWFuYWdlX29yZGVy";

header('Location: '.$url);

?>
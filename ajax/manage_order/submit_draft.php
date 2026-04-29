<?php
session_start();

if( !isset($_SESSION["JOGOLS"]) ){

	echo '<script type="text/javascript">';
	echo 'parent.window.saveDraftFail("Your login session expired. Please login again.");';
	echo '</script>';
	exit();
}

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();


include('../../db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

$add_date = date("Y-m-d H:i:s");
$draft_id = isset($_POST["edit_draft_id"]) ?  $_POST['edit_draft_id']  : 0;

if(isset($_POST['oi_id_delete']) && $_POST["oi_id_delete"]!=""){
		$ids = $_POST['oi_id_delete'] ?? '';

		$idArray = array_filter(array_map('intval', explode(',', $ids)));

		if (!empty($idArray)) {
		$placeholders = implode(',', array_fill(0, count($idArray), '?'));

		$sql = "DELETE FROM tbl_draft_oi WHERE oi_id IN ($placeholders)";
		$stmt = $conn->prepare($sql);

		$stmt->bind_param(str_repeat('i', count($idArray)), ...$idArray);
		$stmt->execute();
		}

}

if(isset($_POST['oi_id_delete'])  && $_POST["of_id_delete"]!=""){

	$sql_chk = "SELECT xls_name FROM tbl_draft_of WHERE of_id IN (".$_POST["of_id_delete"]."); ";
	$rs_chk = $conn->query($sql_chk);
	
	$path_file = "/home2/jogjoino/online.jog-joinourgame.com/src/upload/";

	while($row_chk = $rs_chk->fetch_assoc()){
		if($row_chk["xls_name"]!=""){		
			unlink($path_file.$row_chk["xls_name"]);
		}
	}

	$sql_delete_of = "DELETE FROM tbl_draft_of WHERE of_id IN (".$_POST["of_id_delete"]."); ";
	$conn->query($sql_delete_of);

	$sql_delete_oi2 = "DELETE FROM tbl_draft_oi WHERE of_id IN (".$_POST["of_id_delete"]."); ";
	$conn->query($sql_delete_oi2);
} 

if(isset($_POST['edit_of_id']) &&  !empty($_POST['edit_of_id'])){
	foreach($_POST["edit_of_id"] as $form_id => $of_id){

			$prod_id = $_POST["prod_id_list"][$form_id];

			$form_name = $_POST["form_name_list"][$form_id];
			
			$special_comment =  !empty($_POST['special_comment'][$form_id]) ?  $_POST["special_comment"][$form_id] : "";
			
			if($of_id=="new_file"){

				$on_team_name = $_POST["on_team_name_list"][$form_id];
				$on_year = $_POST["on_year_list"][$form_id];
				
				$s_insert = "INSERT INTO tbl_draft_of (";
				$s_insert .= "order_date,req_due_date,game_event_date,customer_po,project_name,payment_opt,sales_rep_id,reorder_num,prod_id";
				$s_insert .= ",user_id,bill_comp_name,bill_contact_name,bill_address,bill_city,bill_country";
				$s_insert .= ",bill_zip_code,bill_tel,bill_email,bill_tax_id,deli_comp_name,deli_contact_name";
				$s_insert .= ",deli_address,deli_city,deli_country,deli_zip_code,deli_tel,deli_email";
				if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){
					$s_insert .= ",is_assigned,assign_user_id";
				}
				$s_insert .= ",deli_tax_id,date_add,draft_id,form_name,on_team_name,on_year,special_comment) VALUES (";
				$s_insert .= "'".$_POST["order_date"]."','".$_POST["req_due_date"]."','".$_POST["game_event_date"]."','".addslashes($_POST["customer_po"])."','".$_POST["project_name"]."','".$_POST["payment_opt"]."','".$_POST["sales_rep"]."','".addslashes($_POST["reorder_num"]).",'".$prod_id."'";
				$s_insert .= ",'".$user_id."','".addslashes($_POST["company_name"])."','".addslashes($_POST["contact"])."','".addslashes($_POST["address_info"])."','".addslashes($_POST["city"])."','".addslashes($_POST["country"])."'";
				$s_insert .= ",'".addslashes($_POST["zip_code"])."','".addslashes($_POST["tel"])."','".addslashes($_POST["email"])."','".addslashes($_POST["tax_id"])."','".addslashes($_POST["d_company_name"])."','".addslashes($_POST["d_contact"])."'";
				$s_insert .= ",'".addslashes($_POST["d_address_info"])."','".addslashes($_POST["d_city"])."','".addslashes($_POST["d_country"])."','".addslashes($_POST["d_zip_code"])."','".addslashes($_POST["d_tel"])."','".addslashes($_POST["d_email"])."'";
				if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){
					$s_insert .= ",'1','".$_POST["select_assign"][$form_id]."'";
				}
				$s_insert .= ",'".addslashes($_POST["d_tax_id"])."','".$add_date."','".$draft_id."','".addslashes($form_name)."','".addslashes($on_team_name)."','".addslashes($on_year)."','".addslashes($special_comment)."'); ";

				
				$conn->query($s_insert);
				$of_id = $conn->insert_id;

				$file_name = $_FILES['file_upload']['name'][$form_id];
				$tmp_file_name = explode(".", $file_name);
				$n_tmp_name = sizeof($tmp_file_name);
				$ext_file = $tmp_file_name[($n_tmp_name-1)];

				if(move_uploaded_file($_FILES['file_upload']['tmp_name'][$form_id],'../../src/upload/'.$of_id.".".$ext_file)){
					$sql_up_id = "UPDATE tbl_draft_of SET xls_name='".$of_id.".".$ext_file."' WHERE of_id='".$of_id."'; ";
					$conn->query($sql_up_id);
				}

			}else{
				if($of_id!="new" && $of_id!=""){

					$sales_rep_id = isset($_POST["sales_rep"]) ? $_POST["sales_rep"] : 0; 

					$s_update = "UPDATE tbl_draft_of SET order_date='".$_POST["order_date"]."',req_due_date='".$_POST["req_due_date"]."',game_event_date='".$_POST["game_event_date"]."',customer_po='".addslashes($_POST["customer_po"])."',project_name='".addslashes($_POST["project_name"])."',payment_opt='".$_POST["payment_opt"]."',reorder_num='".$_POST["reorder_num"]."',sales_rep_id='". $sales_rep_id ."'";
					$s_update .= ",user_id='".$user_id."',bill_comp_name='".addslashes($_POST["company_name"])."',bill_contact_name='".addslashes($_POST["contact"])."',bill_address='".addslashes($_POST["address_info"])."'";
					$s_update .= ",bill_city='".addslashes($_POST["city"])."',bill_country='".addslashes($_POST["country"])."',bill_zip_code='".addslashes($_POST["zip_code"])."',bill_tel='".addslashes($_POST["tel"])."'";
					$s_update .= ",bill_email='".addslashes($_POST["email"])."',bill_tax_id='".addslashes($_POST["tax_id"])."',deli_comp_name='".addslashes($_POST["d_company_name"])."',deli_contact_name='".addslashes($_POST["d_contact"])."'";
					$s_update .= ",deli_address='".addslashes($_POST["d_address_info"])."',deli_city='".addslashes($_POST["d_city"])."',deli_country='".addslashes($_POST["d_country"])."',deli_zip_code='".addslashes($_POST["d_zip_code"])."'";
					$s_update .= ",deli_tel='".addslashes($_POST["d_tel"])."',deli_email='".addslashes($_POST["d_email"])."',deli_tax_id='".addslashes($_POST["d_tax_id"])."',form_name='".addslashes($_POST["form_name_list"][$form_id])."',special_comment='".addslashes($special_comment)."'";
					if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){
						$s_update .= ",is_assigned='1',assign_user_id='".$_POST["select_assign"][$form_id]."'";
					}
					$s_update .= " WHERE of_id='".$of_id."'; ";
					
					$conn->query($s_update);

				}else if($of_id=="new"){

					$on_team_name = $_POST["on_team_name_list"][$form_id];
					$on_year = $_POST["on_year_list"][$form_id];

					$s_insert = "INSERT INTO tbl_draft_of (";
					$s_insert .= "order_date,req_due_date,game_event_date,customer_po,project_name,payment_opt,sales_rep_id,reorder_num,prod_id";
					$s_insert .= ",user_id,bill_comp_name,bill_contact_name,bill_address,bill_city,bill_country";
					$s_insert .= ",bill_zip_code,bill_tel,bill_email,bill_tax_id,deli_comp_name,deli_contact_name";
					$s_insert .= ",deli_address,deli_city,deli_country,deli_zip_code,deli_tel,deli_email";
					if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){
						$s_insert .= ",is_assigned,assign_user_id";
					}
					$s_insert .= ",deli_tax_id,date_add,draft_id,form_name,on_team_name,on_year,special_comment) VALUES (";
					$s_insert .= "'".$_POST["order_date"]."','".$_POST["req_due_date"]."','".$_POST["game_event_date"]."','".addslashes($_POST["customer_po"])."','".$_POST["project_name"]."','".$_POST["payment_opt"]."','".$_POST["sales_rep"]."','".addslashes($_POST["reorder_num"])."','".$prod_id."'";
					$s_insert .= ",'".$user_id."','".addslashes($_POST["company_name"])."','".addslashes($_POST["contact"])."','".addslashes($_POST["address_info"])."','".addslashes($_POST["city"])."','".addslashes($_POST["country"])."'";
					$s_insert .= ",'".addslashes($_POST["zip_code"])."','".addslashes($_POST["tel"])."','".addslashes($_POST["email"])."','".addslashes($_POST["tax_id"])."','".addslashes($_POST["d_company_name"])."','".addslashes($_POST["d_contact"])."'";
					$s_insert .= ",'".addslashes($_POST["d_address_info"])."','".addslashes($_POST["d_city"])."','".addslashes($_POST["d_country"])."','".addslashes($_POST["d_zip_code"])."','".addslashes($_POST["d_tel"])."','".addslashes($_POST["d_email"])."'";
					if( isset($_POST["select_assign"][$form_id]) && $_POST["select_assign"][$form_id]!="" ){
						$s_insert .= ",'1','".$_POST["select_assign"][$form_id]."'";
					}
					$s_insert .= ",'".addslashes($_POST["d_tax_id"])."','".$add_date."','".$draft_id."','".addslashes($form_name)."','".addslashes($on_team_name)."','".addslashes($on_year)."','".addslashes($special_comment)."'); ";
					$conn->query($s_insert);
					$of_id = $conn->insert_id;

				}

				$loop = 0;
				if(isset($_POST["a_oi_id"][$form_id])){
					$loop = sizeof($_POST["a_oi_id"][$form_id]);
				}

				if($loop>0){
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

						if($_POST["a_oi_id"][$form_id][$j]=="new"){

							$sql_insert2 = "INSERT INTO tbl_draft_oi (";
							$sql_insert2 .= "of_id,player_name,p_or_g,sex,product_size_id,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,name_for_packing,note) VALUES (";
							$sql_insert2 .= "'".$of_id."','".addslashes($player_name)."','".$p_or_g."','".$sex."','".$product_size."','".addslashes($jersey_number)."','".addslashes($color_top1)."','".$qty_top1."','".addslashes($color_top2)."'";
							$sql_insert2 .= ",'".$qty_top2."','".$bottom_size."','".addslashes($color_bottom1)."','".$qty_bottom1."','".addslashes($color_bottom2)."','".$qty_bottom2."','".$c_or_a."','".addslashes($name_for_packing)."','".addslashes($note)."');";
							//of_id,player_name,p_or_g,sex,product_size,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note
							$conn->query($sql_insert2);

						}else{

							$oi_id = $_POST["a_oi_id"][$form_id][$j];

							$sql_update2 = "UPDATE tbl_draft_oi SET player_name='".addslashes($player_name)."',p_or_g='".$p_or_g."',sex='".$sex."',product_size_id='".$product_size."',jersey_number='".addslashes($jersey_number)."'";
							$sql_update2 .= ",color_top1='".addslashes($color_top1)."',qty_top1='".$qty_top1."',color_top2='".addslashes($color_top2)."',qty_top2='".$qty_top2."',bottom_size='".$bottom_size."',color_bottom1='".addslashes($color_bottom1)."'";
							$sql_update2 .= ",qty_bottom1='".$qty_bottom1."',color_bottom2='".addslashes($color_bottom2)."',qty_bottom2='".$qty_bottom2."',c_or_a='".$c_or_a."',name_for_packing='".addslashes($name_for_packing)."',note='".addslashes($note)."'";
							$sql_update2 .= " WHERE oi_id='".$oi_id."'; ";
							$conn->query($sql_update2);

						}

					}
				}
			}

	}
}

if(isset($_POST['assigned_edit_of_id']) && !empty($_POST['assigned_edit_of_id'])){
	 $of_id = $_POST['assigned_edit_of_id'] ?? 0; 
	 $sql = "UPDATE tbl_draft_of SET req_due_date = ? , game_event_date =? , customer_po=? , project_name=? , payment_opt=?  , sales_rep_id=? , reorder_num=? ";
	 $sql .= " , bill_comp_name =? , bill_contact_name=?  , bill_address=? , bill_city=? , bill_country=? ,bill_zip_code=? , bill_tel=? , bill_email=? , bill_tax_id=? , deli_comp_name=?  , deli_contact_name=? , deli_address=? , deli_city=? , deli_country=?  , deli_zip_code=?  , deli_tel=? , deli_email=? , deli_tax_id=?  " ;
	 $sql .=  " Where of_id= ?" ;  
	 $stmt = $conn->prepare($sql) ; 
	 $stmt->bind_param("sssssisssssssssssssssssssi" , $_POST['req_due_date'] ,
	                          $_POST['game_event_date'] , 
                              $_POST['customer_po'] , 
							  $_POST['project_name'] , 
							  $_POST['payment_opt'] ,
							  $_POST['sales_rep'], 
                              $_POST['reorder_num'] , 
							  $_POST['company_name'] , 
							  $_POST['contact'] , 
							  $_POST['address_info'] , 
							  $_POST['city'] , 
							  $_POST['country'],
							  $_POST['zip_code'] , 
							  $_POST['tel'] , 
                              $_POST['email'] , 
							  $_POST['tax_id'] , 
							  $_POST['d_company_name'] , 
							  $_POST['d_contact'] , 
							  $_POST['d_address_info'] , 
							  $_POST['d_city'], 
							  $_POST['d_country'] ,
							  $_POST['d_zip_code'] , 
							  $_POST['d_tel'] , 
							  $_POST['d_email'], 
							  $_POST['d_tax_id'] ,  
							  $of_id                 
	 ) ; 
$stmt->execute(); 
 
}



$strDate = date('Y-m-d H:i:s');
$sales_rep_id = isset($_POST["sales_rep"]) ? $_POST['sales_rep'] : 0 ;
  
$sql_add_noti = 'INSERT INTO notification (
								order_id,
								noti_detail,
								noti_date,
								employee_id,
								noti_from_employee_id
								) VALUES (
								"0",
								"ORDER FROM OLS",
								"'.$strDate.'",
								"'.$sales_rep_id.'",
								"0"
								)';
				$query = $conn3->query($sql_add_noti);


// if($_POST["is_submit_order"]=="yes"){			
// 	$add_date = date("Y-m-d H:i:s");

// 	$sql_select = "SELECT of_id FROM tbl_draft_of WHERE draft_id='".$draft_id."'; ";
// 	$rs_select = $conn->query($sql_select);
// 	$a_of_id = array();
// 	while($row_of_id = $rs_select->fetch_assoc()){
// 		$a_of_id[] = $row_of_id["of_id"];

// 	}

   
// 	if(empty($draft_id)){
// 		 die("Draft id is empty") ; 
// 	}

// 	$sql_insert1 = "INSERT INTO tbl_order_form SELECT * , 0 as design_order_id  , 0 as is_submitted ,  NULL AS submitted_date ,   0 as is_reordered  FROM tbl_draft_of WHERE draft_id='".$draft_id."' ORDER BY of_id ASC ";
// 	$is_insert1 = 0;
// 	if($conn->query($sql_insert1)){
// 		$is_insert1 = 1;
// 	}else {
//        die("Insert1 Error: " . $conn->error);
//     }

// 	$s_of_id = implode(",", $a_of_id);

// 	$sql_insert2 = "INSERT INTO tbl_order_item (
// 			of_id, player_name, p_or_g, sex, product_size_id, jersey_number,
// 			color_top1, qty_top1, color_top2, qty_top2,
// 			bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
// 			c_or_a, name_for_packing, note
// 		)
// 		SELECT 
// 			of_id, player_name, p_or_g, sex, product_size_id, jersey_number,
// 			color_top1, qty_top1, color_top2, qty_top2,
// 			bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
// 			c_or_a, name_for_packing, note
// 		FROM tbl_draft_oi 
// 		WHERE of_id IN (".$s_of_id.") 
// 		ORDER BY oi_id ASC
// 		";

// 	$is_insert2 = 0;
// 	if($conn->query($sql_insert2)){
// 		$is_insert2 = 1;
// 	}else {
//        die("Insert2 Error: " . $conn->error);
//     }
// 	//echo $is_insert2;

// 	if($is_insert1==1){
// 		$sql_delete1 = "DELETE FROM tbl_draft_of WHERE draft_id='".$draft_id."'; ";
// 		$conn->query($sql_delete1);

// 		$sql_update1 = "UPDATE tbl_order_form SET date_add='".$add_date."' WHERE draft_id='".$draft_id."';";
// 		$conn->query($sql_update1);
// 	}

// 	if($is_insert2==1){
// 		$sql_delete2 = "DELETE FROM tbl_draft_oi WHERE of_id IN (".$s_of_id."); ";
// 		$conn->query($sql_delete2);
// 	}
// 	echo '<script type="text/javascript">';
// 		echo 'parent.window.location.href = "/?vp='.base64_encode('new_order').'";';
// 	echo '</script>';
// }


if($_POST["is_submit_order"]=="yes"){			
	$add_date = date("Y-m-d H:i:s");

	$sql_select = "SELECT of_id FROM tbl_draft_of WHERE draft_id='".$draft_id."'; ";
	$rs_select = $conn->query($sql_select);
	$a_of_id = array();
	while($row_of_id = $rs_select->fetch_assoc()){
		$a_of_id[] = $row_of_id["of_id"];
	}
	

   
			$sql_insert1 = "INSERT INTO tbl_order_form (
			of_id,
			draft_id ,
			form_name, 
			special_comment, 
			on_team_name, 
			on_year, 
			xls_name , 
			order_date, 
			order_status,
			order_code,
		    req_due_date,
			game_event_date, 
			customer_po, 
			project_name, 
			payment_opt, 
			sales_rep_id, 
			reorder_num, 
			prod_id,
			user_id,
			bill_comp_name,
			bill_contact_name,
			bill_address,
			bill_city,
			bill_country, 
			bill_zip_code , 
			bill_tel , 
			bill_email , 
			bill_tax_id , 
			deli_comp_name , 
			deli_contact_name , 
			deli_address , 
			deli_city , 
			deli_country, 
			deli_zip_code, 
			deli_tel, 
			deli_email , 
			deli_tax_id , 
			code_match , 
			lkr_order_main_id , 
			re_order_id , 
			ship_status , 
		    date_add ,
			is_assigned,
			assign_user_id
		)
		SELECT 
			of_id,
			draft_id ,
			form_name, 
			special_comment, 
			on_team_name, 
			on_year, 
			xls_name , 
			order_date, 
			order_status,
			order_code,
		    req_due_date,
			game_event_date, 
			customer_po, 
			project_name, 
			payment_opt, 
			sales_rep_id, 
			reorder_num, 
			prod_id,
			user_id,
			bill_comp_name,
			bill_contact_name,
			bill_address,
			bill_city,
			bill_country, 
			bill_zip_code , 
			bill_tel , 
			bill_email , 
			bill_tax_id , 
			deli_comp_name , 
			deli_contact_name , 
			deli_address , 
			deli_city , 
			deli_country, 
			deli_zip_code, 
			deli_tel, 
			deli_email , 
			deli_tax_id , 
			code_match , 
			lkr_order_main_id , 
			re_order_id , 
			ship_status , 
		    date_add ,
			is_assigned,
			assign_user_id
		FROM tbl_draft_of 
		WHERE draft_id='".$draft_id."'
		ORDER BY of_id ASC
		";



	$is_insert1 = 0;
	if($conn->query($sql_insert1)){
		$is_insert1 = 1;
	}else {
       die("Insert1 Error: " . $conn->error);
    }

	$s_of_id = implode(",", $a_of_id);

		$sql_insert2 = "INSERT INTO tbl_order_item (
			of_id, player_name, p_or_g, sex, product_size_id, jersey_number,
			color_top1, qty_top1, color_top2, qty_top2,
			bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
			c_or_a, name_for_packing, note
		)
		SELECT 
			of_id, player_name, p_or_g, sex, product_size_id, jersey_number,
			color_top1, qty_top1, color_top2, qty_top2,
			bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
			c_or_a, name_for_packing, note
		FROM tbl_draft_oi 
		WHERE of_id IN (".$s_of_id.") 
		ORDER BY oi_id ASC
		";
	$is_insert2 = 0;
	if($conn->query($sql_insert2)){
		$is_insert2 = 1;
	}else {
       die("Insert2 Error: " . $conn->error);
    }
	
	if($is_insert1==1){
		$sql_delete1 = "DELETE FROM tbl_draft_of WHERE draft_id='".$draft_id."'; ";
		$conn->query($sql_delete1);

		$sql_update1 = "UPDATE tbl_order_form SET date_add='".$add_date."' WHERE draft_id='".$draft_id."';";
		$conn->query($sql_update1);
	}

	if($is_insert2==1){
		$sql_delete2 = "DELETE FROM tbl_draft_oi WHERE of_id IN (".$s_of_id."); ";
		$conn->query($sql_delete2);
	}
	echo '<script type="text/javascript">';
		echo 'parent.window.location.href = "/?vp='.base64_encode('new_order').'";';
	echo '</script>';
}

echo '<script type="text/javascript">';

if($_POST["is_submit_order"]=="yes"){
	
}else{
	echo 'parent.window.location.href = "/?vp='.base64_encode('manage_order').'";';
}

echo '</script>';
?>
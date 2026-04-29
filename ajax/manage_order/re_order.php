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
$draft_id = "JOG".date("YmdHis").$user_id;

$add_date = date("Y-m-d H:i:s");
$of_id = $_POST["of_id"];





// Step 1: Insert into tbl_draft_of (exclude of_id)
$sql_insert1 = " INSERT INTO tbl_draft_of(
    draft_id , form_name , special_comment , on_team_name , on_year , 
    order_date , order_code, req_due_date , game_event_date , customer_po , 
    project_name , payment_opt , sales_rep_id , reorder_num , prod_id , 
    user_id , bill_comp_name , bill_contact_name, bill_address , bill_city , 
    bill_country , bill_zip_code , bill_tel , bill_email , bill_tax_id , 
    deli_comp_name , deli_contact_name , deli_address , deli_city , 
    deli_country , deli_zip_code , deli_tel , deli_email , deli_tax_id , 
    is_assigned , assign_user_id , date_add 
)
SELECT 
    '". $draft_id ."' , form_name , special_comment , on_team_name , on_year , 
    CURDATE() , order_code, req_due_date , game_event_date , customer_po , 
    project_name , payment_opt , sales_rep_id , reorder_num , prod_id , 
    user_id , bill_comp_name , bill_contact_name , bill_address , bill_city , 
    bill_country , bill_zip_code , bill_tel , bill_email , bill_tax_id , 
    deli_comp_name , deli_contact_name , deli_address , deli_city , 
    deli_country , deli_zip_code , deli_tel , deli_email , deli_tax_id , 
    is_assigned , assign_user_id , CURDATE() 
FROM tbl_order_form 
WHERE of_id = '".$of_id."'
";

if ($conn->query($sql_insert1)) {
     $new_of_id = $conn->insert_id;
     $sql = "UPDATE  tbl_order_form set  is_reordered= 1 Where of_id = ?" ; 
	 $stmt = $conn->prepare($sql); 
	 $stmt->bind_param("i" , $of_id) ; 
	 $stmt->execute() ; 
    // ✅ Get correct inserted ID



		// Debug check
		if (!$new_of_id) {
			die("Error: new_of_id not generated");
		}
  

    // Step 2: Insert items with NEW of_id
    $sql_insert2 = "INSERT INTO tbl_draft_oi 
    (of_id, player_name, p_or_g, sex, product_size_id, jersey_number, color_top1, qty_top1, color_top2, qty_top2, bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2, c_or_a, note , name_for_packing) 
    SELECT 
    '".$new_of_id."', 
    player_name, p_or_g, sex, product_size_id, jersey_number, color_top1, qty_top1, color_top2, qty_top2, bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2, c_or_a, note  , name_for_packing
    FROM tbl_order_item 
    WHERE of_id='".$of_id."' 
    ORDER BY oi_id ASC
    ";

    if (!$conn->query($sql_insert2)) {
        die("Insert2 Error: " . $conn->error);
    }

} else {
    die("Insert1 Error: " . $conn->error);
}

$a_result["result"] = "success";

echo json_encode($a_result);
?>
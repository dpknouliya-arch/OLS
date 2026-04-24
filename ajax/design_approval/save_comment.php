<?php
session_start();

if($_POST["input_comment_detail"]==""){

	echo '<script type="text/javascript">';
	echo 'alert("Please input data");';
	echo '</script>';
	exit();
}

include('../../db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;
$date_add = date("Y-m-d H:i:s");
$comment_detail = addslashes($_POST["input_comment_detail"]);
$dd_id = $_POST["dd_id"];
$is_first_comment = $_POST["is_first_comment"];
$comment_from = 'OLS';

// $sql_insert = "INSERT INTO tbl_design_comment (dd_id,comment_from,comment,user_id,date_add) VALUES (";
// $sql_insert .= "'".$dd_id."','OLS','".$comment_detail."','".$user_id."','".$date_add."'";
// $sql_insert .= "); ";


	$sql_insert = "INSERT INTO tbl_design_comment 
	(dd_id, comment_from, comment, user_id, date_add) 
	VALUES (?, ?, ?, ?, ?)";

	$stmt = $conn->prepare($sql_insert);
	$stmt->bind_param(
		"issss",
		$dd_id,
		$comment_from,
		$comment_detail,
		$user_id,
		$date_add
	);

if($stmt->execute()){

	    $sql_design_draft = "SELECT order_id FROM tbl_design_draft WHERE dd_id = ?";
		$stmt = $conn->prepare($sql_design_draft);
		$stmt->bind_param("i", $dd_id); // assuming dd_id is integer
		$stmt->execute();

		$result = $stmt->get_result();
		$row_design_draft = $result->fetch_assoc();
		$order_id = $row_design_draft['order_id'] ?? null;

	//---Connect to Lockerroom DB
	$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
	mysqli_set_charset($conn3, "utf8");

	$sql_order_head =$conn3->prepare("SELECT * FROM order_head WHERE order_id=?");
    $sql_order_head->bind_param("i" , $order_id) ; 
    $sql_order_head->execute() ; 
	$rs_order_head = $sql_order_head->get_result() ; 
	$row_order_head = $rs_order_head->fetch_assoc();
		
	$sale_id = $row_order_head["order_sale_id"];
	$sale_id2 = $row_order_head["order_sale_id2"];
	$order_design_id = $row_order_head["order_design_id"];
	$order_design_id2 = $row_order_head["order_design_id2"];

	if($sale_id!="" && $sale_id!="0"){
		$sql_add_noti = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id) VALUES ('".$order_id."','Comment on OLS','".$date_add."','".$sale_id."');";
		$conn3->query($sql_add_noti);
	}

	if($sale_id2!="" && $sale_id2!="0"){
		$sql_add_noti2 = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id) VALUES ('".$order_id."','Comment on OLS','".$date_add."','".$sale_id2."');";
		$conn3->query($sql_add_noti2);
	}
	
	if($order_design_id!="" && $order_design_id!="0"){
		$sql_add_noti3 = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id) VALUES ('".$order_id."','Comment on OLS','".$date_add."','".$order_design_id."');";
		$conn3->query($sql_add_noti3);
	}
	
	if($order_design_id2!="" && $order_design_id2!="0"){
		$sql_add_noti4 = "INSERT INTO notification (order_id,noti_detail,noti_date,employee_id) VALUES ('".$order_id."','Comment on OLS','".$date_add."','".$order_design_id2."');";
		$conn3->query($sql_add_noti4);
	}

	if($is_first_comment=="yes"){
		$inner_html .= '<hr><div id="d_comment_list" class="row bg_weather">';
	}

	$inner_html .= '<div class="col-4"></div>
					<div class="col-8">
						<div style="text-align:left;color:white;">'.$obj_user->full_name.'<span class="comment_time"> @ '.date("M d, Y H:i:s",strtotime($date_add)).'</span></div>
						<div class="comment_box_OLS">'.$_POST["input_comment_detail"].'</div>
					</div>
				';
	if($is_first_comment=="yes"){
		$inner_html .= '</div>';
	}

	echo '<script type="text/javascript">';
	echo "window.parent.saveSuccess('".base64_encode($inner_html)."','".$is_first_comment."');";
	echo '</script>';
	
}else{
	
	echo '<script type="text/javascript">';
	echo 'alert("Error: save data fail.");';
	echo '</script>';

}

?>
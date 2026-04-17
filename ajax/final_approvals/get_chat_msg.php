<?php
include('../../db.php');

$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
mysqli_set_charset($conn3, "utf8");

if( !isset($_POST["of_id"]) || ($_POST["of_id"]=="") ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter";
	echo json_encode($a_result);
	exit();
}

$of_id = $_POST["of_id"];



$sql_select = "SELECT * FROM tbl_chat_approvals WHERE order_id='".$of_id."' ORDER BY chat_id ASC; ";
$rs_msg = $conn->query($sql_select);

if($rs_msg->num_rows > 0){

	$a_msg = array();
	$a_employee_id = array();
	$a_customer_id = array();

	$max_chat_id = "";

	while($row_msg = $rs_msg->fetch_assoc()){

		$a_msg[] = $row_msg;

		if( $row_msg["chat_type"]=="A" ){
			if( !in_array($row_msg["user_email"], $a_employee_id) ){
				$a_employee_id[] = $row_msg["user_email"];
			}
			
		}else{

			if( !in_array($row_msg["user_email"], $a_customer_id) ){
				$a_customer_id[] = $row_msg["user_email"];
			}
		}

		$max_chat_id = $row_msg["chat_id"];
		
	}
	
	$a_result["max_chat_id"] = $max_chat_id;

	$a_customer = array();
	if(sizeof($a_customer_id)>0){

		$sql_customer = "SELECT *,user_id AS person_id,full_name AS show_name FROM tbl_user WHERE user_email IN ('".implode(",", $a_customer_id)."'); ";
		$rs_customer = $conn->query($sql_customer);//OLS DB
		while($row_customer = $rs_customer->fetch_assoc()){
			$a_customer[$row_customer["user_email"]] = $row_customer["show_name"];
		}

	}
	

	$a_employee = array();
	if(sizeof($a_employee_id)>0){

		$sql_employee = "SELECT employee_id AS person_id,employee_name AS show_name FROM employee WHERE employee_id IN (".implode(",", $a_employee_id)."); ";
		$rs_employee = $conn3->query($sql_employee);//LKR DB
		while($row_employee = $rs_employee->fetch_assoc()){
			$a_employee[intval($row_employee["person_id"])] = $row_employee["show_name"];
		}

	}
	
	
	$msg_box = "";
	$chk_msg_read = "";

	for($i=0; $i<sizeof($a_msg); $i++){

		$show_name = "";
		$msg_class = "";
		$show_read = "";

		if($a_msg[$i]["chat_type"]=="A"){
			$show_name = $a_employee[($a_msg[$i]["user_email"])];
			$msg_class = "msg_box_answer";
			$sql_update = "UPDATE tbl_chat_approvals SET is_read=1 WHERE chat_id='".$a_msg[$i]["chat_id"]."'; ";
			$conn->query($sql_update);//OLS DB
			
		}else{
			$show_name = $a_customer[($a_msg[$i]["user_email"])];
			$msg_class = "msg_box_question";
			if($a_msg[$i]["is_read"]=="1"){
				$show_read = '<div class="q_read" id="msg_chat'.$a_msg[$i]["chat_id"].'">Read</div>';
			}else{
				if($chk_msg_read!=""){
					$chk_msg_read .= ",";
				}
				$chk_msg_read .= $a_msg[$i]["chat_id"];
				$show_read = '<div class="q_read" id="msg_chat'.$a_msg[$i]["chat_id"].'"></div>';
			}
		}

		$msg_box .= '<div id="msg_box_no'.$a_msg[$i]["chat_id"].'" class="'.$msg_class.' col-12">';
		$msg_box .= '<div class="meta_info">'.$a_msg[$i]["add_time"].' <b>'.$show_name.'</b></div>';
		$msg_box .= '<br><div class="msg_info"><pre>'.$a_msg[$i]["msg"].'</pre></div>'.$show_read;
		$msg_box .= '</div>';

	}

	$a_result["chk_msg_read"] = $chk_msg_read;
	$a_result["msg_box"] = base64_encode($msg_box);
	$a_result["result"] = "success";

}else{

	$a_result["result"] = "fail";
	$a_result["msg"] = "Not found data.";

}

echo json_encode($a_result);
?>

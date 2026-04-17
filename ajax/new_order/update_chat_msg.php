<?php
include('../../db.php');

$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
mysqli_set_charset($conn3, "utf8");

if( !isset($_POST["of_id"]) || ($_POST["of_id"]=="") || !isset($_POST["max_chat_id"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter";
	echo json_encode($a_result);
	exit();
}

$unread_chat_id = "[".$_POST["chk_msg_read"]."]";
$read_chat_id = "";
if($_POST["chk_msg_read"]!=""){
	$sql_chk_read = "SELECT chat_id FROM tbl_chat WHERE chat_id IN (".$_POST["chk_msg_read"].") AND is_read=1; ";
	$rs_chk_read = $conn->query($sql_chk_read);
	if($rs_chk_read->num_rows > 0){
		while($row_chk_read = $rs_chk_read->fetch_assoc()){
			if($read_chat_id!=""){
				$read_chat_id .= ",";
			}
			$read_chat_id .= $row_chk_read["chat_id"];

			$unread_chat_id = str_replace(",".$row_chk_read["chat_id"].",", "", $unread_chat_id);
			$unread_chat_id = str_replace($row_chk_read["chat_id"].",", "", $unread_chat_id);
			$unread_chat_id = str_replace(",".$row_chk_read["chat_id"], "", $unread_chat_id);
			$unread_chat_id = str_replace("[".$row_chk_read["chat_id"]."]", "[]", $unread_chat_id);
		}
	}
}
$a_result["read_chat_id"] = $read_chat_id;

$unread_chat_id = str_replace("[", "", $unread_chat_id);
$unread_chat_id = str_replace("]", "", $unread_chat_id);
$a_result["unread_chat_id"] = $unread_chat_id;

$max_chat_id = $_POST["max_chat_id"];
$of_id = $_POST["of_id"];

$sql_select = "SELECT * FROM tbl_chat WHERE of_id='".$of_id."' AND chat_id >".$max_chat_id." ORDER BY chat_id ASC; ";
$rs_msg = $conn->query($sql_select);

$msg_box = "";



if($rs_msg->num_rows > 0){

	$a_msg = array();
	$a_employee_id = array();
	$a_customer_id = array();

	$max_chat_id = "";

	while($row_msg = $rs_msg->fetch_assoc()){

		$a_msg[] = $row_msg;

		if( $row_msg["chat_type"]=="A" ){
			if( !in_array($row_msg["person_id"], $a_employee_id) ){
				$a_employee_id[] = $row_msg["person_id"];
			}
			
		}else{

			if( !in_array($row_msg["person_id"], $a_customer_id) ){
				$a_customer_id[] = $row_msg["person_id"];
			}
		}

		$max_chat_id = $row_msg["chat_id"];
		
	}
	
	$a_result["max_chat_id"] = $max_chat_id;

	$a_customer = array();
	if(sizeof($a_customer_id)>0){

		$sql_customer = "SELECT user_id AS person_id,full_name AS show_name FROM tbl_user WHERE user_id IN (".implode(",", $a_customer_id)."); ";
		$rs_customer = $conn->query($sql_customer);//OLS DB
		while($row_customer = $rs_customer->fetch_assoc()){
			$a_customer[intval($row_customer["person_id"])] = $row_customer["show_name"];
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
	
	for($i=0; $i<sizeof($a_msg); $i++){

		$show_name = "";
		$msg_class = "";
		$show_read = "";

		if($a_msg[$i]["chat_type"]=="A"){
			$show_name = $a_employee[($a_msg[$i]["person_id"])];
			$msg_class = "msg_box_answer";
		}else{
			$show_name = $a_customer[($a_msg[$i]["person_id"])];
			$msg_class = "msg_box_question";
			if($a_msg[$i]["is_read"]=="1"){
				$show_read = '<div class="q_read" id="msg_chat'.$a_msg[$i]["chat_id"].'">Read</div>';
			}else{
				$show_read = '<div class="q_read" id="msg_chat'.$a_msg[$i]["chat_id"].'"></div>';
			}
			
		}

		$msg_box .= '<div id="msg_box_no'.$a_msg[$i]["chat_id"].'" class="'.$msg_class.' col-12">';
		$msg_box .= '<div class="meta_info">'.$a_msg[$i]["add_time"].' <b>'.$show_name.'</b></div>';
		$msg_box .= '<br><div class="msg_info"><pre>'.$a_msg[$i]["msg"].'</pre></div>'.$show_read;
		$msg_box .= '</div>';

	}

	$a_result["msg_box"] = base64_encode($msg_box);
	$a_result["result"] = "success";

}else{

	$a_result["result"] = "no update";

}

echo json_encode($a_result);
?>

<?php
include('../../db.php');

//$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
//mysqli_set_charset($conn3, "utf8");

if( !isset($_POST["of_id"]) || ($_POST["of_id"]=="") || !isset($_POST["msg_input"]) || !isset($_POST["person_id"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter";
	echo json_encode($a_result);
	exit();
}

//$max_chat_id = $_POST["max_chat_id"];
$of_id = $_POST["of_id"];
$person_id = $_POST["person_id"];

/*$msg_box = "";

$sql_select = "SELECT * FROM tbl_chat WHERE of_id='".$of_id."' AND chat_id>".$max_chat_id." ORDER BY chat_id ASC; ";
$rs_msg = $conn->query($sql_select);



if($rs_msg->num_rows > 0){

	$a_msg = array();
	$a_employee_id = array();
	$a_customer_id = array();

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

		if($a_msg[$i]["chat_type"]=="A"){
			$show_name = $a_employee[($a_msg[$i]["person_id"])];
			$msg_class = "msg_box_answer";
		}else{
			$show_name = $a_customer[($a_msg[$i]["person_id"])];
			$msg_class = "msg_box_question";
			
		}

		$msg_box .= '<div id="msg_box_no'.$a_msg[$i]["chat_id"].'" class="'.$msg_class.' col-12">';
		$msg_box .= '<div class="meta_info">'.$a_msg[$i]["add_time"].' <b>'.$show_name.'</b></div>';
		$msg_box .= '<br><div class="msg_info"><pre>'.$a_msg[$i]["msg"].'</pre></div>';
		$msg_box .= '</div>';

	}

}*/


$msg_input = base64_decode($_POST["msg_input"]);

$sql_insert = "INSERT INTO tbl_chat (of_id,person_id,chat_type,msg,add_time) VALUES ('".$of_id."','".$person_id."','Q','".addslashes(htmlspecialchars($msg_input))."','".date("Y-m-d H:i:s")."'); ";
if($conn->query($sql_insert)){

	$a_result["max_chat_id"] = $conn->insert_id;

	$sql_update = "UPDATE tbl_chat SET is_read=1 WHERE of_id='".$of_id."' AND chat_type='A' AND chat_id<'".$a_result["max_chat_id"]."'; ";
	$conn->query($sql_update);

	/*$msg_box .= '<div id="msg_box_no'.$a_result["max_chat_id"].'" class="msg_box_question col-12">';
	$msg_box .= '<div class="meta_info">'.date("Y-m-d H:i:s").' <b>'.base64_decode($_POST["person_name"]).'</b></div>';
	$msg_box .= '<br><div class="msg_info"><pre>'.$msg_input.'</pre></div>';
	$msg_box .= '</div>';

	$a_result["msg_box"] = base64_encode($msg_box);*/

	$a_result["result"] = "success";

}else{
	$a_result["result"] = "fail";
	$a_result["msg"] = "Database Error!";
}

echo json_encode($a_result);
?>

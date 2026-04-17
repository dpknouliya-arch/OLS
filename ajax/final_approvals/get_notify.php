<?php
include('../../db.php');

if( !isset($_POST["s_of_id"]) || ($_POST["s_of_id"]=="") || !isset($_POST["msg_type"])){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter";
	echo json_encode($a_result);
	exit();
}

$s_of_id = $_POST["s_of_id"];

$sql_select = "SELECT order_id,COUNT(chat_id) AS num_new FROM tbl_chat_approvals WHERE order_id IN (".$s_of_id.") AND is_read=0 AND chat_type='".$_POST["msg_type"]."' GROUP BY order_id; ";
$rs_num_new = $conn->query($sql_select);

$a_count_unread = array();
$tmp_of_id = explode(",", $s_of_id);
for($i=0;$i<sizeof($tmp_of_id);$i++){
	$a_count_unread[($tmp_of_id[$i])] = $tmp_of_id[$i]."=0";
}

$result_return = "";

while($row_num_new = $rs_num_new->fetch_assoc()){

	$a_count_unread[($row_num_new["order_id"])] = $row_num_new["order_id"]."=".$row_num_new["num_new"];
}

$result_return = implode(",", $a_count_unread);

$a_result["result"] = "success";
$a_result["s_notify"] = $result_return;

echo json_encode($a_result);

?>

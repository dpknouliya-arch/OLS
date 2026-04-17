<?php
if(!isset($_POST["chk_email"])){
	exit();
}

include('../../db.php');

$chk_email = addslashes(base64_decode($_POST["chk_email"]));

$sql_check = "SELECT user_email FROM tbl_user WHERE user_email='".$chk_email."' AND enable<>0; ";

$rs_chk = $conn->query($sql_check);

$num_row = $rs_chk->num_rows;

if($num_row>0){
	$a_result["result"] = "dup";
}else{
	$a_result["result"] = "not_dup";
}


echo json_encode($a_result);
?>
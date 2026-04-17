<?php
session_start();
include "../../db.php";
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;
$user_email = $obj_user->user_email;
$order_id = base64_decode($_POST['of_id']);
$add_date = date("Y-m-d H:i:s");
$msg_input = $_POST['msg_input'];
$sql = "INSERT INTO `tbl_chat_approvals`(`user_email`, `order_id`, `chat_type`, `msg`) VALUES ('$user_email','$order_id','Q','$msg_input')";
$query = mysqli_query($conn,$sql);
if($query){
    $nsql = "SELECT employee_id as emp_id FROM employee WHERE employee_position_id IN (1,3,4,5,6,99)";
    $nquery = mysqli_query($conn3,$nsql);
    while($row=mysqli_fetch_assoc($nquery)){
        $emp_id = ltrim($row['emp_id'],0);
        $ins = "INSERT INTO `notification`(`order_id`, `noti_detail`, `noti_date`, `employee_id`, `noti_from_employee_id`, `noti_status`) VALUES ('$order_id','Comment on Final Approval','$add_date','$emp_id','0','0')";
        $query = mysqli_query($conn3,$ins);
    }
    die(json_encode(array('status'=>'1',"result"=>"success")));
}

?>
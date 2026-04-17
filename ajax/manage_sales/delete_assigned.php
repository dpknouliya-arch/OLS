<?php 
include('../../db.php');

$id = $_POST['id'] ?? 0 ; 
if(!$id){
     echo json_encode(['status'=>503 , 'msg'=>'Something went wrong']); 
     exit; 
}

if(isset($_POST['is_enable']) && !empty($_POST['is_enable'])){
     $sql =  "UPDATE tbl_sales_assignments SET enable=1 Where id='$id'"; 
     $update = $conn->query($sql);
     echo json_encode(['status'=>200 , 'msg'=>'Enable successfully']); 
     exit ; 
}


$sql =  "UPDATE tbl_sales_assignments SET enable=0 Where id='$id'"; 
$update = $conn->query($sql); 

$user_id = $_POST['user_id'];

$sql = "SELECT COUNT(DISTINCT user_id)  AS count FROM  tbl_sales_assignments Where sales_user_id='$user_id' AND enable=1"; 
$count = $conn->query($sql)->fetch_assoc(); 

echo json_encode(['status'=>200 , 'msg'=>'Deleted successfully' ,'count'=>$count['count']]); 
exit ; 



?>
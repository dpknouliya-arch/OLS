<?php 
include('../../db.php');



if(isset($_SESSION['JOGOLS'])):
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

$new_password = $_POST['New_password']; 
$pre_password = base64_encode($_POST['Previous_password']); 
$string_pass = md5(base64_decode($pre_password)); 

$check_password_exists = "SELECT * FROM tbl_user Where user_id = '$user_id' AND user_password='$string_pass'"; 

$data = $conn->query($check_password_exists);

if($data->num_rows  > 0){
    $update =  "UPDATE tbl_user SET user_password='".md5($new_password)."' Where user_id='$user_id'"; 
    $user_update = $conn->query($update); 
    echo json_encode(['status'=>200 , 'msg'=>'Password updated successfully']); 
    exit ; 

}else{
     echo json_encode(['status'=>503 ,'msg'=>'Incorrect previous password']);
     exit();
}

elseif(isset($_SESSION['JOGOLSSUB'])): 
 $obj_user = json_decode(base64_decode($_SESSION["JOGOLSSUB"]));
 $user_id = $obj_user->sub_user_id; 
 
 $new_password = $_POST['New_password']; 
 $pre_password = $_POST['Previous_password']; 
 
 $check_password_exists = "SELECT * FROM tbl_sub_user Where sub_user_id = '$user_id' AND s_user_pwd='$pre_password'"; 
 $data = $conn->query($check_password_exists);

    if($data->num_rows  > 0){
        $update =  "UPDATE tbl_sub_user SET s_user_pwd='".$new_password."' Where sub_user_id='$user_id'"; 
        $user_update = $conn->query($update); 
        echo json_encode(['status'=>200 , 'msg'=>'Password updated successfully']); 
        exit ; 

    }else{
        echo json_encode(['status'=>503 ,'msg'=>'Incorrect previous password']);
        exit();
    }
 
elseif(isset($_SESSION['JOGOLSSALE'])):
  $obj_user = json_decode(base64_decode($_SESSION["JOGOLSSALE"]));
 $user_id = $obj_user->sales_user_id; 
 
 $new_password = $_POST['New_password']; 
 $pre_password = $_POST['Previous_password']; 


  $check_password_exists = "SELECT * FROM tbl_sales_user  Where sales_user_id = '$user_id' AND s_user_pwd='$pre_password'"; 
  $data = $conn->query($check_password_exists);

    if($data->num_rows  > 0){
        $update =  "UPDATE tbl_sales_user SET s_user_pwd='".$new_password."' Where sales_user_id='$user_id'"; 
        $user_update = $conn->query($update); 
        echo json_encode(['status'=>200 , 'msg'=>'Password updated successfully']); 
        exit ; 

    }else{
        echo json_encode(['status'=>503 ,'msg'=>'Incorrect previous password']);
        exit();
    }

endif ;
?>
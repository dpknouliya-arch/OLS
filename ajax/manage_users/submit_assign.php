<?php

include('../../db.php');

// echo "<pre>";

// print_r($_SESSION);

// die;

if( !isset($_SESSION["JOGOLS"])){



	$a_result["result"] = "fail";

	$a_result["msg"] = "Your login session expired. Please login again.";

	echo json_encode($a_result);

	exit();

}





$sales_user_id = base64_decode($_POST["sales_user_id"]);

$assgindata = base64_decode($_POST["assgindata"]);



if (!empty($sales_user_id) && !empty($assgindata)) {

    

    $sql_update = "INSERT INTO tbl_sales_assignments (sales_user_id, user_id) VALUES ($sales_user_id, $assgindata); ";
  


	if($conn->query($sql_update)){
     $sql = "SELECT COUNT(DISTINCT user_id)  AS count FROM  tbl_sales_assignments Where sales_user_id='$sales_user_id' AND enable=1"; 
     $count = $conn->query($sql)->fetch_assoc(); 
     
        $a_result["result"] = "success";
        $a_result['sales_user_id'] = $sales_user_id; 
        $a_result['count'] =  $count['count']; 

    }else{

        $a_result["result"] = "fail";

        $a_result["msg"] = "Fail to edit User";

    }   

} else {

    $a_result["result"] = "fail";

}

echo json_encode($a_result);
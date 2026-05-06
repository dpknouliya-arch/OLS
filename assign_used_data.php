<?php





if (!isset($_POST["sales_user_id"])) {



    $a_result["result"] = "fail";

    $a_result["msg"] = "Fail to send email.";

    echo json_encode($a_result);



    exit();

}







$sales_user_id = $_POST["sales_user_id"];



$sql_s_user = "SELECT * FROM tbl_sales_user WHERE sales_user_id='" . $sales_user_id . "'; ";

$rs_s_user = $conn->query($sql_s_user);



$sql_all_user = "SELECT * FROM `tbl_user` WHERE `user_level` = 'user'  ORDER BY `tbl_user`.`full_name` ASC; ";

$all_user = $conn->query($sql_all_user);


$sql = "SELECT COUNT(DISTINCT user_id)  AS count FROM  tbl_sales_assignments Where sales_user_id='$sales_user_id' AND enable=1"; 
$count = $conn->query($sql)->fetch_assoc(); 





if ($rs_s_user->num_rows == 0) {

    $a_result["result"] = "fail";

    $a_result["msg"] = "Not found data.";

} else {

    $row_s_user = $rs_s_user->fetch_assoc();





    $html = "";

    $html .= "<div class='ManageAssigned_user XSmall'>";

    $html .= "<div class='customGrid2'>";

    $html .= "<div class='left box'>";

    $html .= "<p class='mb-0'>

    <figure class='m-0'>

        <img src='images/vector/assignedUser.png' alt=''>

    </figure>  

    Assigned Users: <span class='userNumber'>" .$count['count'] . "</span>

</p>";



    $html .= '<input type="hidden" name="sales_user_id"  id="new_sales_user_id" value="' . $sales_user_id . '" placeholder="Name">';

    $html .= "</div>";

    $html .= "<div class='right box'>";

    $html .= '<label for="assgindata">Assign User</label>';

    $html .= '<select name="assgindata" id="assgindata">';

    while ($row_s_user = $all_user->fetch_assoc()) {

        $html .= '<option value="' . $row_s_user['user_id'] . '">' . $row_s_user['full_name'] . '</option>';

    }

    $html .= '</select>';

    $html .= "</div>";
    $html .= "</div>";

    $sales_data = "SELECT * FROM `tbl_sales_assignments` WHERE `sales_user_id` = $sales_user_id AND enable=1";
    $sales_data_assign = $conn->query($sales_data);


    $html .= "<div class='selectedUserBox '>";

     while ($row_assign = $sales_data_assign->fetch_assoc()) {
        $user_id = $row_assign['user_id'];
        $assiggn_name = "SELECT * FROM `tbl_user` WHERE `user_id` = $user_id ; ";
        $assiggn_name = $conn->query($assiggn_name);
        $row_s_assgin = $assiggn_name->fetch_assoc();
        $id = $row_assign['id']; 

         $html .= "<p class='selectUserName me-4 iconBTn cursor deletebtn' style='display:inline-block;' data-userid = '".$row_assign['sales_user_id']."'   data-id='".$id."' >" . $row_s_assgin["full_name"] . "    
                    <img src='images/vector/closeBlue.png' alt=''>
                 </p>";
     }

    $html .= "</div>";

    $html .= "<div class='allUser'>";


    $sales_data = "SELECT * FROM `tbl_sales_assignments` WHERE `sales_user_id` = $sales_user_id AND enable=1";
    $sales_data_assign = $conn->query($sales_data);
    while ($row_assign = $sales_data_assign->fetch_assoc()) {

        $user_id = $row_assign['user_id'];

        $assiggn_name = "SELECT * FROM `tbl_user` WHERE `user_id` = $user_id ; ";

        $assiggn_name = $conn->query($assiggn_name);

        $row_s_assgin = $assiggn_name->fetch_assoc();

        $id = $row_assign['id']; 

        $html .= "<p class='userName iconBTn cursor deletebtn' data-userid = '".$row_assign['sales_user_id']."' data-id ='".$id."' >" . $row_s_assgin["full_name"] . "  <img src='images/vector/delteBTn.png' alt=''>  </p>";

    }



    $html .= "</div>";

    $html .= "</div>";



    $a_result["result"] = "success";

    $a_result["data"] = $html;

    echo json_encode($a_result);

}


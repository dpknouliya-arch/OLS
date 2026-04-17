<?php

// Include your database connection

if( !isset($_POST["sales_user_id"]) ){



	$a_result["result"] = "fail";

	$a_result["msg"] = "Fail to send email.";

	echo json_encode($a_result);



	exit();

}



include('../../db.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sales_user_id = $_POST['sales_user_id'];



    // Initialize response array

    $response = [];



    if (!empty($sales_user_id)) {

        // Start a transaction

        $conn->begin_transaction();



        try {

            // Delete from tbl_sales_assignments

            $delete_assignments = $conn->prepare("DELETE FROM tbl_sales_assignments WHERE sales_user_id = ?");

            $delete_assignments->bind_param("i", $sales_user_id);

            $delete_assignments->execute();



            // Delete from tbl_sales_user

            $delete_user = $conn->prepare("DELETE FROM tbl_sales_user WHERE sales_user_id = ?");

            $delete_user->bind_param("i", $sales_user_id);

            $delete_user->execute();



            // Commit transaction

            $conn->commit();



            $response['result'] = 'success';

        } catch (Exception $e) {

            // Rollback transaction if something goes wrong

            $conn->rollback();

            $response['result'] = 'error';

            $response['message'] = $e->getMessage();

        }

    } else {

        $response['result'] = 'error';

        $response['message'] = 'Invalid sales_user_id.';

    }



    // Return JSON response

    echo json_encode($response);

}

?>


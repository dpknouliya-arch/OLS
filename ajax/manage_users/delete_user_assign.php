<?php
include('../../db.php');
// echo "<pre>";
// print_r($_SESSION);
// die;
if( !isset($_SESSION["JOGOLSSALE"])){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

$user_id = $_POST["user_id"];
$sales_user_id = $_POST["sales_user_id"];

$query = "DELETE FROM tbl_sales_assignments WHERE sales_user_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $sales_user_id, $user_id);

if ($stmt->execute()) {
    $a_result["result"] = "success";
    $a_result["msg"] = "User assignment deleted successfully.";
} else {
    $a_result["result"] = "fail";
    $a_result["msg"] = "Error deleting user assignment.";
}

$stmt->close();
$conn->close();

echo json_encode($a_result);
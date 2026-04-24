<?php
session_start();

include('../../db.php');
include('../../encryption_helper.php');

if( !isset($_SESSION["JOGOLS"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}



	$a_of_id = array();
	$draft_id = isset($_POST['draft_id']) ? customDecode($_POST['draft_id']) : 0;

	$a_of_id = [];

	// 👉 Start transaction
	$conn->begin_transaction();

try {

  

    // ✅ Step 1: Get of_id list safely
    $stmt = $conn->prepare("SELECT DISTINCT of_id FROM tbl_draft_of WHERE of_id = ?");
    $stmt->bind_param("i", $draft_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $a_of_id[] = $row["of_id"];
    }

    // ✅ Step 2: Delete from tbl_draft_oi (only if IDs exist)
    if (!empty($a_of_id)) {

        // create placeholders (?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($a_of_id), '?'));

        $types = str_repeat('i', count($a_of_id));

        $stmt = $conn->prepare("DELETE FROM tbl_draft_oi WHERE of_id IN ($placeholders)");
        $stmt->bind_param($types, ...$a_of_id);
        $stmt->execute();
    }

    // ✅ Step 3: Delete from tbl_draft_of
    $stmt = $conn->prepare("DELETE FROM tbl_draft_of WHERE of_id = ?");
    $stmt->bind_param("i", $draft_id);
    $stmt->execute();

    // ✅ Commit if all good
    $conn->commit();

    $a_result["result"] = "success";

} catch (Exception $e) {

    $conn->rollback();

    $a_result["result"] = "error";
    $a_result["message"] = $e->getMessage();
}




echo json_encode($a_result);
?>
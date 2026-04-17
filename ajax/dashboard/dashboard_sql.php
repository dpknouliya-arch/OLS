<?php


if (!isset($_SESSION['JOGOLS'])) {
    echo json_encode([
        'status' => 401,
        'message' => 'Session expired'
    ]);
    exit;
}


$user_details = json_decode(base64_decode($_SESSION['JOGOLS']));
$user_id = $user_details->user_id;
$customer_id = $user_details->customer_id;



// SECURE: Use prepared statement to get codes
$stmt = $conn3->prepare(
    "SELECT DISTINCT(order_main_code)
     FROM order_main
     WHERE customer_id = ?"
);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 // Get all order_main_code into a simple array
 $codes = array_column($result, 'order_main_code');

 // DEPRECATED: This variable is unsafe - use prepared statements instead
 // $ex_codes = "'".implode("','"  , $codes)."'";



 function GetYearArr(){
    global $codes, $conn5;

    if (empty($codes)) {
        return [];
    }

    // SECURE: Use prepared statements with placeholders
    $placeholders = implode(',', array_fill(0, count($codes), '?'));
    $sql = "SELECT DISTINCT YEAR(created_date) As year From quotation_data as qd where jog_code IN($placeholders) Order by created_date desc";

    $stmt = $conn5->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $types = str_repeat('s', count($codes));
    $stmt->bind_param($types, ...$codes);
    $stmt->execute();
    $yearArr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $yearArr;
    exit;
 }

 function GetInvoiceStatus(){
     return ['Paid' => 'Paid' , 'Outstanding' =>'Unpaid' , 'Pending' => 'Pending'];
     exit;
 }


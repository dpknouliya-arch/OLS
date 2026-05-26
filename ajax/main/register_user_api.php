<?php
include('../../db.php');
header("Content-Type: application/json");

$user_email = base64_decode($_POST['user_email'] ?? '');
$user_password = md5(base64_decode($_POST['user_password'] ?? ''));
$full_name = $_POST['full_name'] ?? '';
$customer_id = $_POST['customer_id'] ?? '';
$brand_id = $_POST['brand_id'] ?? 1; // Default to 1 if not provided

if (empty($user_email) || empty($user_password) || empty($full_name)) {
    echo json_encode(['result' => 'error', 'message' => 'Missing fields']);
    exit;
}

// Check existing — email is globally unique, check without brand_id
$stmt = $conn->prepare("SELECT user_id FROM tbl_user WHERE user_email = ? AND brand_id = ?");
$stmt->bind_param("ss", $user_email, $brand_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    echo json_encode(['result' => 'exists', 'message' => 'User already exists']);
    exit;
}
$stmt->close();

// Insert user
$stmt = $conn->prepare("
    INSERT INTO tbl_user (user_email, user_password, full_name, customer_id, enable, date_add, first_login, brand_id)
    VALUES (?, ?, ?, ?, 1, NOW(), 1, ?)
");

$stmt->bind_param("ssssi", $user_email, $user_password, $full_name, $customer_id, $brand_id);

if ($stmt->execute()) {
    echo json_encode(['result' => 'success', 'user_id' => $stmt->insert_id]);
} else {
    echo json_encode(['result' => 'fail', 'message' => 'DB insert failed: ' . $stmt->error]);
}
$stmt->close();
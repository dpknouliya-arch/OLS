<?php
include('../../db.php');
header("Content-Type: application/json");

$user_email = base64_decode($_POST['user_email'] ?? '');
$user_password = md5(base64_decode($_POST['user_password'] ?? ''));
$full_name = $_POST['full_name'] ?? '';
$customer_id = $_POST['customer_id'] ?? '';

if (empty($user_email) || empty($user_password) || empty($full_name)) {
    echo json_encode(['result' => 'error', 'message' => 'Missing fields']);
    exit;
}

// Check existing
$stmt = $conn->prepare("SELECT user_id FROM tbl_user WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    echo json_encode(['result' => 'exists', 'message' => 'User already exists']);
    exit;
}

// Insert user
$stmt = $conn->prepare("
    INSERT INTO tbl_user (user_email, user_password, full_name, customer_id, enable, date_add, first_login)
    VALUES (?, ?, ?, ?, 1, NOW(), 1)
");
$stmt->bind_param("ssss", $user_email, $user_password, $full_name, $customer_id);

if ($stmt->execute()) {
    echo json_encode(['result' => 'success', 'user_id' => $stmt->insert_id]);
} else {
    echo json_encode(['result' => 'fail', 'message' => 'DB insert failed']);
}
$stmt->close();
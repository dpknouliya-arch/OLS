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
    $new_user_id = $stmt->insert_id;
    $stmt->close();

    if ($brand_id == 2) {
        $tpl_file = __DIR__ . '/../../email-templates/bauer-welcome.html';
        if (file_exists($tpl_file)) {
            $cta_url = defined('BAUERURL') ? rtrim(BAUERURL, '/') : 'https://3d.jog-joinourgame.com/3dbauer';
            $html_welcome = file_get_contents($tpl_file);
            $html_welcome = str_replace(
                ['{{first_name}}', '{{cta_url}}', '{{support_email}}', '{{company_address}}', '{{preferences_url}}', '{{unsubscribe_url}}', '{{year}}'],
                [$full_name, $cta_url, 'support@jog-joinourgame.com', 'Bauer Hockey', $cta_url . '/profile.php', $cta_url . '/profile.php', date('Y')],
                $html_welcome
            );
            $w_headers  = "MIME-Version: 1.0\r\n";
            $w_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $w_headers .= "From: Bauer Hockey <no-reply@jog-joinourgame.com>\r\n";
            mail($user_email, 'Welcome to the Bauer 3D Customizer', $html_welcome, $w_headers);
        }
    }

    echo json_encode(['result' => 'success', 'user_id' => $new_user_id]);
} else {
    echo json_encode(['result' => 'fail', 'message' => 'DB insert failed: ' . $stmt->error]);
    $stmt->close();
}
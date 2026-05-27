<?php
session_start();
if (!isset($_SESSION["JOGOLS"])) {
    echo json_encode(['result' => 'fail', 'msg' => 'Session expired']);
    exit;
}
include('../../db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = $obj_user->user_id;
$brand_id = get_ols_brand_id();
$date_add = date('Y-m-d H:i:s');

header('Content-Type: application/json');

$TOKEN_KEY = 'jogsports_secure_key_' . session_id();

function decryptId($enc, $key) {
    $data  = base64_decode($enc);
    $parts = explode('::', $data);
    if (count($parts) !== 2) return false;
    return openssl_decrypt($parts[0], 'AES-128-CBC', $key, 0, base64_decode($parts[1]));
}

$mode         = $_POST['mode']         ?? 'add'; // add | edit
$addr_type    = $_POST['addr_type']    ?? 'billing'; // billing | delivery
$edit_addr_id = $_POST['edit_addr_id'] ?? '';

$addr_name    = trim($_POST['company_name'] ?? '');
$contact_name = trim($_POST['contact']      ?? '');
$address      = trim($_POST['address_info'] ?? '');
$city         = trim($_POST['city']         ?? '');
$country      = trim($_POST['country']      ?? '');
$zip_code     = trim($_POST['zipcode']      ?? '');
$tel          = trim($_POST['tel']          ?? '');
$email        = trim($_POST['email_info']   ?? '');
$tax_id       = trim($_POST['tax_no']       ?? '');

if (!$addr_name || !$contact_name || !$address || !$city || !$country || !$zip_code || !$tel || !$email) {
    echo json_encode(['result' => 'fail', 'msg' => 'Missing required fields']);
    exit;
}

if ($mode === 'edit' && $edit_addr_id) {
    $addr_id = decryptId($edit_addr_id, $TOKEN_KEY);
    if (!$addr_id || !is_numeric($addr_id)) {
        echo json_encode(['result' => 'fail', 'msg' => 'Invalid address ID']);
        exit;
    }
    $stmt = $conn->prepare(
        "UPDATE tbl_address SET addr_name=?,contact_name=?,address=?,city=?,country=?,
         zip_code=?,tel=?,email=?,tax_id=? WHERE addr_id=? AND user_id=?"
    );
    $stmt->bind_param('sssssssssii',
        $addr_name, $contact_name, $address, $city, $country,
        $zip_code, $tel, $email, $tax_id, $addr_id, $user_id
    );
    if (!$stmt->execute()) {
        echo json_encode(['result' => 'fail', 'msg' => 'Update failed']);
        exit;
    }
} else {
    // Insert new address
    $stmt = $conn->prepare(
        "INSERT INTO tbl_address (addr_name,contact_name,address,city,country,zip_code,tel,email,tax_id,user_id,brand_id,date_add)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->bind_param('sssssssssiis',
        $addr_name, $contact_name, $address, $city, $country,
        $zip_code, $tel, $email, $tax_id, $user_id, $brand_id, $date_add
    );
    if (!$stmt->execute()) {
        echo json_encode(['result' => 'fail', 'msg' => 'Insert failed']);
        exit;
    }
    $addr_id = $conn->insert_id;
}

// Set as default billing or delivery (scoped to brand so other brand defaults are not affected)
if ($addr_type === 'billing') {
    $s1 = $conn->prepare("UPDATE tbl_address SET is_billing_addr=0 WHERE user_id=? AND brand_id=?");
    $s1->bind_param("ii", $user_id, $brand_id); $s1->execute(); $s1->close();
    $s2 = $conn->prepare("UPDATE tbl_address SET is_billing_addr=1 WHERE addr_id=? AND user_id=? AND brand_id=?");
    $s2->bind_param("iii", $addr_id, $user_id, $brand_id); $s2->execute(); $s2->close();
} else {
    $s1 = $conn->prepare("UPDATE tbl_address SET is_deliver_addr=0 WHERE user_id=? AND brand_id=?");
    $s1->bind_param("ii", $user_id, $brand_id); $s1->execute(); $s1->close();
    $s2 = $conn->prepare("UPDATE tbl_address SET is_deliver_addr=1 WHERE addr_id=? AND user_id=? AND brand_id=?");
    $s2->bind_param("iii", $addr_id, $user_id, $brand_id); $s2->execute(); $s2->close();
}

echo json_encode(['result' => 'success']);

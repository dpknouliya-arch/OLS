<?php
// ------------------------------------------------------
// ALLOW CROSS-DOMAIN COOKIE from jogsports.com (iframe)
// ------------------------------------------------------
header("Access-Control-Allow-Origin: https://jogsports.com");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ------------------------------------------------------
// COOKIE SETTINGS REQUIRED FOR IFRAME LOGIN (Chrome rules)
// ------------------------------------------------------
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'ols-test.jog-joinourgame.com', 
    'secure' => true,
    'httponly' => false,
    'samesite' => 'None'
]);

// Start PHP session AFTER cookie rules
session_start();

// ------------------------------------------------------
// CLEAN ANY POSSIBLE OUTPUT (important)
// ------------------------------------------------------
if (!ob_get_level()) ob_start();
ob_clean();

// ------------------------------------------------------
// DATABASE
// ------------------------------------------------------
require_once __DIR__ . "/db.php";

// ------------------------------------------------------
// INPUT
// ------------------------------------------------------
$user = isset($_GET['u']) ? base64_decode($_GET['u']) : '';
$pass = isset($_GET['p']) ? md5(base64_decode($_GET['p'])) : '';

// Missing data?
if ($user === "" || $pass === "") {
    ob_clean();
    echo "fail";
    exit;
}

// ------------------------------------------------------
// QUERY USER
// ------------------------------------------------------
$sql = "SELECT * FROM tbl_user 
        WHERE user_email='$user' 
        AND user_password='$pass' 
        AND enable=1";

$rs = $conn->query($sql);

// ------------------------------------------------------
// SUCCESS LOGIN
// ------------------------------------------------------
if ($rs && $rs->num_rows === 1) {

    $row = $rs->fetch_assoc();

    $obj = [
        "user_id"     => $row['user_id'],
        "full_name"   => $row['full_name'],
        "customer_id" => $row['customer_id'],
        "user_email"  => $row['user_email'],
        "user_level"  => $row['user_level'],
    ];

    // SAVE SAME SESSION KEY USED BY index.php
    $_SESSION['JOGOLS'] = base64_encode(json_encode($obj));

    // IMPORTANT: SAVE SESSION TO DISK NOW
    session_write_close();

    ob_clean();
    echo "ok";
    exit;
}

// ------------------------------------------------------
// FAIL LOGIN
// ------------------------------------------------------
ob_clean();
echo "fail";
exit;

?>

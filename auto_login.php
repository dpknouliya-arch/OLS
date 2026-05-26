<?php
// ------------------------------------------------------
// ALLOW CROSS-DOMAIN COOKIE from jogsports.com (iframe)
// ------------------------------------------------------
$allowedOrigins = [
    'https://jogsports.com',
    'https://3d.jog-joinourgame.com',
    'http://localhost',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://jogsports.com");
}
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
$user     = isset($_GET['u']) ? base64_decode($_GET['u']) : '';
$pass     = isset($_GET['p']) ? md5(base64_decode($_GET['p'])) : '';
$brand_id = isset($_GET['b']) ? intval($_GET['b']) : 0;

// Missing data or no brand?
if ($user === "" || $pass === "" || $brand_id === 0) {
    ob_clean();
    echo "fail";
    exit;
}

// ------------------------------------------------------
// QUERY USER — enforce brand_id to prevent cross-brand login
// ------------------------------------------------------
$stmt = $conn->prepare(
    "SELECT * FROM tbl_user WHERE user_email = ? AND user_password = ? AND brand_id = ? AND enable = 1"
);
$stmt->bind_param("ssi", $user, $pass, $brand_id);
$stmt->execute();
$rs = $stmt->get_result();
$stmt->close();

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
        "brand_id"    => (int)$row['brand_id'],
    ];

    // SAVE SAME SESSION KEY USED BY index.php
    $_SESSION['JOGOLS'] = base64_encode(json_encode($obj));
    set_ols_brand_id($brand_id);

    // IMPORTANT: SAVE SESSION TO DISK NOW
    
    

    function apiLogin($user, $password) {

        $url = OLS_BASE_URL . "/api/login.php";

        $postData = json_encode([
            "email" => base64_decode($user), // ✅ decode email
            "password" => base64_decode($password)          // ✅ plain password
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);

        $res = curl_exec($ch);
    
        if (curl_errno($ch)) {
            print_r($url); // ✅ debug: show what was sent
            echo "Curl Error: " . curl_error($ch);
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        print_r($res); // ✅ debug: show API response
        $data = json_decode($res, true);

        return $data['token'] ?? null;
    }

    $_SESSION['API_TOKEN'] = apiLogin($_GET['u'], $_GET["p"]);

    session_write_close();

    ob_clean();
    echo "success";
    exit;
}

// ------------------------------------------------------
// FAIL LOGIN
// ------------------------------------------------------
ob_clean();
echo "fail";
exit;

?>

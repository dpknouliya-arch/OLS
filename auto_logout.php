<?php
// Allow iframe logout from any origin in the same app
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

session_start();

// Remove all session data
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params["path"], 
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// End session completely
session_destroy();

// Clean any output
if (!ob_get_level()) ob_start();
ob_clean();

// Respond cleanly
echo "ok";
exit;

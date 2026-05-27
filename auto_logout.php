<?php
header('Cache-Control: no-store, no-cache, must-revalidate, private');
header('Pragma: no-cache');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
         || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// Must match the SameSite=None params used by auto_login.php so the PHPSESSID
// is correctly read whether this runs as a top-level redirect or an iframe fallback.
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $is_https,
    'httponly' => true,
    'samesite' => $is_https ? 'None' : 'Lax',
]);

session_start();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

if (!ob_get_level()) ob_start();
ob_clean();

// Handle redirect chain from 3DBauer logout.php.
// Only redirect to whitelisted origins to prevent open-redirect abuse.
$next = urldecode($_GET['next'] ?? '');
if ($next !== '') {
    $allowed = [
        'https://3d.jog-joinourgame.com/3dbauer/',
        'http://localhost/3dbauer/',
    ];
    foreach ($allowed as $prefix) {
        if (strpos($next, $prefix) === 0) {
            header("Location: $next");
            exit;
        }
    }
}

echo "ok";
exit;

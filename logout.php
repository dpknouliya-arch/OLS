<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!function_exists('get_ols_brand_id')) {
    require_once __DIR__ . '/db.php';
}

// Capture brand BEFORE clearing the user session so we can restore it
$_logout_brand_id = get_ols_brand_id();

// Clear user session key only — brand key survives
$_SESSION['JOGOLS'] = '';
unset($_SESSION['JOGOLS']);

// Restore brand context so login page reads it immediately
$_SESSION['OLS_BRAND_ID'] = $_logout_brand_id;
set_ols_brand_id($_logout_brand_id);

if($_logout_brand_id == 1){
    header('Location: ' . OLS_BASE_URL . '/login.php');
}else{
    header('Location: ' . OLS_BASE_URL . '/bauer_login.php');
}
exit;
?>
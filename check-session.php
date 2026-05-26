<?php
if (isset($_SESSION['JOGOLS']) && $_SESSION['JOGOLS'] !== '') {
    // logged in as user — continue
} elseif (isset($_SESSION['JOGOLSSALE']) && $_SESSION['JOGOLSSALE'] !== '') {
    // logged in as sales — continue
} else {
    if (!defined('OLS_BASE_URL')) {
        require_once __DIR__ . '/db.php';
    }
    header('Location: ' . OLS_BASE_URL . '/login.php');
    exit;
}
?>
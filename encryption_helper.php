<?php
function customEncode($id) {
    $salt = "MySecretKey"; // make this unique
    $encoded = base64_encode(($id * 12345) . $salt);
    return strtr($encoded, '+/=', '-_,'); // make it URL-safe
}

function customDecode($encoded) {
    $salt = "MySecretKey";
    $encoded = strtr($encoded, '-_,', '+/=');
    $decoded = base64_decode($encoded);
    $idWithSalt = str_replace($salt, '', $decoded);
    return intval($idWithSalt / 12345);
}

?>

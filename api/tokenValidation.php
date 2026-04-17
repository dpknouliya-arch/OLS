<?php

include __DIR__ . '/../db.php';

header("Content-Type: application/json");

// Get token from header or POST
$headers = getallheaders();
$token = $headers['Authorization'] ?? NULL;

function sendResponse($code, $data) {
    http_response_code($code);
    header("HTTP/1.1 $code");
    echo json_encode($data);
    exit;
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['status' => 401, 'msg' => 'Authorization token missing']);
     exit ; 
}
  
$sql = "SELECT * FROM tbl_user WHERE auth_token = '$token'";
$result = $conn->query($sql); 
$user = $result->fetch_assoc(); 

// Token invalid
if (!$user) {
    http_response_code(401);
    echo json_encode(['status' => 401, 'msg' => 'Invalid token']);
    exit;
}

// Token expired
if (strtotime($user['token_expiry']) < time()) {
    http_response_code(401);

    echo json_encode(['status' => 401, 'msg' => 'Token expired']);
    exit;
}


// Token is valid — user authenticated
$GLOBALS['AUTH_USER'] = $user;  // For use in your API

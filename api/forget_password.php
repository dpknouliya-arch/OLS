<?php
require_once __DIR__ . '/Authentication/authFile.php';
$auth  = new authFile(); 
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST') {
    $user_signin =  $auth->ForgetPassword(); 
     http_response_code(200);
     echo json_encode($user_signin);  
     exit;

} 



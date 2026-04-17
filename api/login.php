<?php 
require_once __DIR__ . '/Authentication/authFile.php';

$auth  = new authFile(); 

 if($_SERVER['REQUEST_METHOD'] =="POST"){
    $user_login = $auth->UserLogin(); 
    echo json_encode($user_login);
    exit ; 
 }


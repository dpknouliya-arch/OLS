<?php
include('../db.php');
include('../check-session.php');

header('Content-Type: application/json');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = (int) $obj_user->user_id;

$res = callAPI("get_drafts.php?user_id=$user_id");

echo json_encode($res);

<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $collar  = $_POST['collar'];
    $style   = $_POST['style'];
    $stripes = $_POST['stripes'];

    // ✅ Call API instead of DB
    $res = callAPI("get_design_data.php?collar=$collar&style=$style&stripes=$stripes");

    if (!$res || empty($res['data'])) {
        echo json_encode([]);
        exit;
    }

    echo json_encode($res['data']);
}
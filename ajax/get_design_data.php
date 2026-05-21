<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collar  = intval($_POST['collar']);
    $style   = intval($_POST['style']);
    $stripes = intval($_POST['stripes']);

    $result = callAPI("get_design_data.php?collar=$collar&style=$style&stripes=$stripes");

    // API returns {"status": true/false, "data": {...}} — forward only data to keep
    // the same response shape the frontend expects (raw design object with colors)
    echo json_encode($result['data'] ?? null);
}

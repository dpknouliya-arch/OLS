<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $design_id = intval($_POST['design_id']);

    // ✅ Call API
    $res = callAPI("get_zone_data.php?design_id=" . $design_id);

    if (!$res || empty($res['data'])) {
        echo json_encode([]);
        exit;
    }

    echo json_encode($res['data']);
}
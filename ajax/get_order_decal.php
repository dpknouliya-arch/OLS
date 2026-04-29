<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $orderId = intval($_POST['orderId']);

    // ✅ Call API instead of DB
    $res = callAPI("get_order.php?order_id=" . $orderId);

    if (!$res || empty($res['data'])) {
        echo json_encode([]);
        exit;
    }

    $row = $res['data'];

    $decals = [];

    $decals['textdecals']  = $row['textdecals'] ?? '';
    $decals['imagedecals'] = $row['imagedecals'] ?? '';

    // ✅ Handle color safely (same as your logic)
    $color = $row['colorDecals'] ?? '{}';

    if ($color === null || $color === '' || $color === 'undefined' || $color === 'null') {
        $color = '{}';
    }

    $decals['colorDecals'] = $color;

    echo json_encode($decals);
}
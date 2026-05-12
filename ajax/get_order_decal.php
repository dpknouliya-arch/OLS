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
    $design_json = $row['design_json'] ?? [];


    $decals = [];

    

    // ✅ Handle color safely (same as your logic)
    $color = $row['colorDecals'] ?? '{}';

    if ($color === null || $color === '' || $color === 'undefined' || $color === 'null') {
        $color = '{}';
    }

    $color = $design_json['color_data']   ?? $design_json['colorDecals'] ?? null;
    $logos = $design_json['image_decals'] ?? $design_json['imagedecals'] ?? null;
    $texts = $design_json['text_decals']  ?? $design_json['textdecals']  ?? null;

    $decals['colorDecals'] = $color;
    $decals['textdecals']  = $texts;
    $decals['imagedecals'] = $logos;

    echo json_encode($decals);
}
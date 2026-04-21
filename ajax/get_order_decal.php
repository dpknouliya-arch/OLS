<?php

include('../db.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $orderId = intval($_POST['orderId']);

    $sql = "SELECT * FROM design_order WHERE order_id = ?";

    $stmt = $conn4->prepare($sql);

    $stmt->bind_param("i", $orderId);

    $stmt->execute();

    $result = $stmt->get_result();



    $decals = [];

    while ($row = $result->fetch_assoc()) {

        $decals['textdecals']  = $row['textdecals'];        

        $decals['imagedecals']  = $row['imagedecals'];        

        //$decals['colorDecals']  = $row['colorDecals'];    
        $color = $row['colorDecals'];
        if ($color === null || $color === '' || $color === 'undefined' || $color === 'null') {
            $color = '{}';
        }
        $decals['colorDecals'] = $color;

    }



    echo json_encode($decals);

}
<?php

include('../db.php');



$design_id = intval($_POST['design_id'] ?? 0);

$response = [];



if ($design_id) {

    $sql = "SELECT * FROM design_placement WHERE design_id = ?";

    $stmt = $conn4->prepare($sql);

    $stmt->bind_param("i", $design_id);

    $stmt->execute();

    $result = $stmt->get_result();



    while ($row = $result->fetch_assoc()) {

        $mesh = $row['mesh_name'];



        // build structure

        $response['placementMeshes'][$mesh][] = [

            "displayName" => $row['display_name'],

            "displayType" => $row['display_tyoe'],

            "img_scop" => $row['image_scope'],

            "img_rotation" => $row['image_rotation'],

            "text_font_size" => $row['text_font_size'],

            "text_font_family" => $row['text_font_family'],



            "text_color" => $row['text_color'],

            "text_outline" => $row['text_outline'],

            "text_outline_size" => $row['text_outline_size'],

            "text_rotation" => $row['text_rotation'],

            "text_curve" => $row['text_curve'],

            "text_bend" => $row['text_bend'],

            "text_spacing_v" => $row['text_spacing_v'],

            "text_spacing_h" => $row['text_spacing_h'],



            "uvCorrection" => [

                "x" => (float)$row['uv_x'],

                "y" => (float)$row['uv_y']

            ],

            "textPosition" => [

                "x" => (float)$row['text_x'],

                "y" => (float)$row['text_y']

            ],

            "imagePosition" => [

                "x" => (float)$row['image_x'],

                "y" => (float)$row['image_y']

            ]

        ];

        // for meshImageMap

        $response['meshImageMap'][$mesh][] = $row['image_path'];

        $response['displayNames'][$mesh][] = $row['display_name'];

    }

}

echo json_encode($response);


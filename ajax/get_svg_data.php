<?php

include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['design_id'])) {

    $design_id = intval($_POST['design_id']);



    $query = "SELECT svg_id, design_id, mesh_name, url_svg, added_date ,color_code

              FROM design_svg 

              WHERE design_id = ? AND status = 1";

    $stmt = $conn4->prepare($query);

    $stmt->bind_param("i", $design_id);

    $stmt->execute();

    $result = $stmt->get_result();



    $data = [];

    while ($row = $result->fetch_assoc()) {

        $data[] = $row;

    }



    echo json_encode($data);

    exit;

}



echo json_encode([]);

?>
<?php

include('../db.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $design_id = intval($_POST['design_id']);

    $sql = "SELECT * FROM design_zones WHERE design_id = ?  Order by added_date ASC";

    $stmt = $conn4->prepare($sql);

    $stmt->bind_param("i", $design_id);

    $stmt->execute();

    $result = $stmt->get_result();



    $zones = [];

    while ($row = $result->fetch_assoc()) {

        $zone       = $row['zone_name'];

        $subZone    = $row['sub_zone_name'];

        $childZone  = $row['child_zone'];

        $mesh       = $row['mesh_name'];

        $color      = $row['color_group'];

        $category   = $row['zone_category'];



        // Zone Meshes

        $zones['zoneMeshMap'][$zone][] = $mesh;



        // Zone Colors

        $zones['zoneColorGroupMap'][$zone][$mesh] = $color;



        // SubZone Colors (if exists)

        if ($subZone) {

            $zones['subZoneColorGroupMap'][$subZone][$mesh] = $color;

        }



        // Zone Categories

        $zones['zoneCategories'][$category][] = $zone;

        if (!empty($childZone)) {

            $zones['zoneChildren'][$subZone][] = $childZone;

        }

    }



    echo json_encode($zones);

}


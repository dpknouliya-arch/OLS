<?php

include('../db.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $collar = $_POST['collar'];

    $style = $_POST['style'];

    $stripes = $_POST['stripes'];



    // Fetch design data

    $sql = "SELECT * FROM `designs` WHERE `coller_id` = ? AND `style_id` = ? AND `stripes_id` = ?  AND `status`= ?";

    $stmt = $conn4->prepare($sql);

   $status = 1;

  $stmt->bind_param("iiii", $collar, $style, $stripes, $status);


    $stmt->execute();

    $result = $stmt->get_result(); 

    $row = $result->fetch_assoc();



    if ($row) {

        $designId = $row['id'];



        // Fetch related colors

        $colorSql = "

            SELECT c.id, c.name , c.panton_name

            FROM colors c

            INNER JOIN design_colors dc ON dc.color_id = c.id

            WHERE dc.design_id = ? 
          
            ORDER BY c.sort_no ASC
        ";

        $colorStmt = $conn4->prepare($colorSql);

        $colorStmt->bind_param("i", $designId);

        $colorStmt->execute();

        $colorResult = $colorStmt->get_result();



        $colors = [];

        while ($colorRow = $colorResult->fetch_assoc()) {

            $colors[] = $colorRow;

        }



        // Attach colors to design data

        $row['colors'] = $colors;

    }


    echo json_encode($row);

}


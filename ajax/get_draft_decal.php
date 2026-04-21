<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designId = intval($_POST['designId']);
    $userId = intval($_POST['userId']);
    $sql = "SELECT * FROM design_drafts WHERE user_id = ? AND design_id = ? AND is_used = 0";
    $stmt = $conn4->prepare($sql);
    $stmt->bind_param("ii", $userId, $designId);
    $stmt->execute();
    $result = $stmt->get_result();

    $decals = [];
    while ($row = $result->fetch_assoc()) {
        $decals['textdecals']  = $row['text_decals'];        
        $decals['imagedecals']  = $row['image_decals'];        
        $decals['colorDecals']  = $row['color_data'];        
    }

    echo json_encode($decals);
}
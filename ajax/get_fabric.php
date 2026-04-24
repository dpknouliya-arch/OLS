<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $design_id = intval($_POST['design_id']);

    $sql = "SELECT * FROM design_fabric WHERE design_id = ?";
    $stmt = $conn4->prepare($sql);
    $stmt->bind_param("i", $design_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $fabric = [];

    while ($row = $result->fetch_assoc()) {
        // Group by fabric_type
        $type = $row['fabric_type'];
        $name = $row['mesh_name'];

        // Initialize array if not set
        if (!isset($fabric[$type])) {
            $fabric[$type] = [];
        }

        // Append mesh_name to the correct type
        $fabric[$type][] = $name;
    }

    echo json_encode($fabric);
}
?>

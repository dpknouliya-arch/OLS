<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit();
}

include('../../db.php');
include('../../includes/order_helpers.php');

header('Content-Type: application/json');

$obj_user        = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id         = (int)$obj_user->user_id;
$input           = json_decode(file_get_contents('php://input'), true);
$design_order_id = (int)($input['design_order_id'] ?? 0);

if ($design_order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing design_order_id.']);
    exit();
}

$order_check = callAPI("get_order.php?order_id=$design_order_id");
if (empty($order_check['data']) || $order_check['status'] !== 200) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$of_id = createNewTeamDraft($conn, $design_order_id, $user_id);
if ($of_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Could not create team draft.']);
    exit();
}

echo json_encode(['success' => true, 'of_id' => $of_id]);

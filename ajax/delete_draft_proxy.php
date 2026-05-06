<?php
include('../db.php');
include('../check-session.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 501, 'msg' => 'POST request only']);
    exit;
}

$draft_id = isset($_POST['draft_id']) ? (int)$_POST['draft_id'] : 0;

if (!$draft_id) {
    echo json_encode(['status' => 400, 'msg' => 'draft_id is required']);
    exit;
}

$res = callAPI_POST('deleted_draft.php', ['draft_id' => $draft_id]);

echo json_encode($res);

<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/Authentication/authFile.php';

$auth = new authFile();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode($auth->Logout());
    exit;
}

http_response_code(405);
echo json_encode(['status' => 405, 'msg' => 'Method not allowed']);

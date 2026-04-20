<?php
session_start();
include('../../check-session.php');
include('../../db.php');
include '../../encryption_helper.php';

header('Content-Type: application/json');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = $obj_user->user_id;

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order_id']);
    exit;
}

$order_id = (int) $input['order_id'];

// Verify order belongs to this user
$stmt = $conn4->prepare("SELECT order_id FROM design_order WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get existing row IDs for this order (try both 'id' and 'team_id' as PK name)
$pk = 'id';
$res = $conn4->query("SELECT id FROM order_team WHERE order_id = $order_id LIMIT 1");
if (!$res) {
    // Try team_id as PK
    $res = $conn4->query("SELECT team_id FROM order_team WHERE order_id = $order_id LIMIT 1");
    if ($res) $pk = 'team_id';
}

$existing_ids = [];
$all = $conn4->query("SELECT $pk FROM order_team WHERE order_id = $order_id");
if ($all) {
    while ($r = $all->fetch_assoc()) {
        $existing_ids[] = (int)$r[$pk];
    }
}

$rows         = $input['rows'] ?? [];
$submitted_ids = [];
$inserted     = 0;
$updated      = 0;

foreach ($rows as $r) {
    $tid           = (int)($r['team_id'] ?? 0);
    $player_name   = $r['player_name']    ?? '';
    $pattern_cut   = $r['pattern_cut']    ?? '';
    $pg            = $r['player_or_goalie'] ?? '';
    $jersey_size   = $r['jersey_size']    ?? '';
    $jersey_no     = $r['jersey_no']      ?? '';
    $jersey_color  = $r['jersey_color']   ?? '';
    $jersey_qty    = $r['jersey_qty']     ?? '';
    $jersey_color2 = $r['jersey_color2']  ?? '';
    $jersey_qty2   = $r['jersey_qty2']    ?? '';
    $sock_size     = $r['sock_size']      ?? '';
    $sock_color    = $r['sock_color']     ?? '';
    $sock_qty      = $r['sock_qty']       ?? '';
    $sock_color2   = $r['sock_color2']    ?? '';
    $sock_qty2     = $r['sock_qty2']      ?? '';
    $cor_a         = $r['cor_a']          ?? '';
    $name_packing  = $r['name_for_packing'] ?? '';
    $notes         = $r['notes']          ?? '';

    if ($tid > 0 && in_array($tid, $existing_ids)) {
        // UPDATE existing row
        $upd = $conn4->prepare(
            "UPDATE order_team SET
                player_name=?, pattern_cut=?, player_or_goalie=?, jersey_size=?,
                jersey_no=?, jersey_color=?, jersey_qty=?, jersey_color2=?, jersey_qty2=?,
                sock_size=?, sock_color=?, sock_qty=?, sock_color2=?, sock_qty2=?,
                cor_a=?, name_for_packing=?, notes=?
             WHERE $pk=? AND order_id=?"
        );
        $upd->bind_param(
            "sssssssssssssssssii",
            $player_name, $pattern_cut, $pg, $jersey_size,
            $jersey_no, $jersey_color, $jersey_qty, $jersey_color2, $jersey_qty2,
            $sock_size, $sock_color, $sock_qty, $sock_color2, $sock_qty2,
            $cor_a, $name_packing, $notes,
            $tid, $order_id
        );
        $upd->execute();
        $submitted_ids[] = $tid;
        $updated++;
    } else {
        // INSERT new row
        $ins = $conn4->prepare(
            "INSERT INTO order_team
                (order_id, player_name, pattern_cut, player_or_goalie, jersey_size,
                 jersey_no, jersey_color, jersey_qty, jersey_color2, jersey_qty2,
                 sock_size, sock_color, sock_qty, sock_color2, sock_qty2,
                 cor_a, name_for_packing, notes)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $ins->bind_param(
            "isssssssssssssssss",
            $order_id,
            $player_name, $pattern_cut, $pg, $jersey_size,
            $jersey_no, $jersey_color, $jersey_qty, $jersey_color2, $jersey_qty2,
            $sock_size, $sock_color, $sock_qty, $sock_color2, $sock_qty2,
            $cor_a, $name_packing, $notes
        );
        $ins->execute();
        $new_id = (int)$conn4->insert_id;
        if ($new_id > 0) $submitted_ids[] = $new_id;
        $inserted++;
    }
}

// DELETE rows that were removed by the user
$to_delete = array_diff($existing_ids, $submitted_ids);
$deleted   = 0;
foreach ($to_delete as $del_id) {
    $del_id = (int)$del_id;
    $conn4->query("DELETE FROM order_team WHERE $pk = $del_id AND order_id = $order_id");
    $deleted++;
}

// Save team_name and year to design_order (columns may or may not exist; fail silently)
$team_name = $input['team_name'] ?? '';
$team_year = $input['team_year'] ?? '';
if ($team_name !== '' || $team_year !== '') {
    @$conn4->query(
        "UPDATE design_order SET on_team_name='" . $conn4->real_escape_string($team_name) .
        "', on_year='" . $conn4->real_escape_string($team_year) .
        "' WHERE order_id=" . $order_id
    );
}

echo json_encode([
    'success'  => true,
    'inserted' => $inserted,
    'updated'  => $updated,
    'deleted'  => $deleted,
]);

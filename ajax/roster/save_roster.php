<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit();
}

include('../../db.php');
include('../../includes/order_helpers.php');

header('Content-Type: application/json');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = (int)$obj_user->user_id;

$input = json_decode(file_get_contents('php://input'), true);

// Accept either design_order_id or order_id (legacy JS sends order_id)
$design_order_id = (int)($input['design_order_id'] ?? $input['order_id'] ?? 0);
$rows            = $input['rows'] ?? [];

if ($design_order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing design_order_id.']);
    exit();
}

// Verify design_order ownership (READ ONLY DB)
$stmt = $conn4->prepare(
    "SELECT order_id FROM design_order WHERE order_id=? AND user_id=?"
);
$stmt->bind_param("ii", $design_order_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}
$stmt->close();

// Get or create the draft order form
$of_id = getOrCreateDraftOrder($conn, $design_order_id, $user_id);
if ($of_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Could not create draft order. Check server error log for details.']);
    exit();
}

// Check order state
$stmt = $conn->prepare(
    "SELECT is_submitted FROM tbl_order_form WHERE of_id=? LIMIT 1"
);
$stmt->bind_param("i", $of_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order form not found for of_id=' . $of_id]);
    exit();
}
$row_of       = $res->fetch_assoc();
$is_submitted = (int)$row_of['is_submitted'];
$stmt->close();

// Determine target table
$target_table = ($is_submitted === 0) ? 'tbl_draft_oi' : 'tbl_order_item';

// Build size name → size_id lookup map once
$size_map = [];
$sz_res = $conn->query("SELECT size_id, size_name FROM tbl_size");
if ($sz_res) {
    while ($sz = $sz_res->fetch_assoc()) {
        $size_map[strtolower(trim($sz['size_name']))] = (int)$sz['size_id'];
    }
}

// Fetch existing oi_ids in the target table for this of_id
$existing_ids = [];
$r_exist = $conn->query("SELECT oi_id FROM " . $target_table . " WHERE of_id=" . (int)$of_id);
if ($r_exist) {
    while ($e = $r_exist->fetch_assoc()) {
        $existing_ids[] = (int)$e['oi_id'];
    }
}

$submitted_ids = [];
$inserted      = 0;
$updated       = 0;
$errors        = [];

foreach ($rows as $r) {
    $item_id = (int)($r['item_id'] ?? $r['team_id'] ?? 0);

    $player_name   = $conn->real_escape_string($r['player_name']     ?? '');
    $sex           = $conn->real_escape_string($r['pattern_cut']      ?? '');
    $p_or_g        = $conn->real_escape_string($r['player_or_goalie'] ?? '');
    $jersey_number = $conn->real_escape_string($r['jersey_no']        ?? '');
    $color_top1    = $conn->real_escape_string($r['jersey_color']     ?? '');
    $color_top2    = $conn->real_escape_string($r['jersey_color2']    ?? '');
    $bottom_size   = $conn->real_escape_string($r['sock_size']        ?? '');
    $color_bottom1 = $conn->real_escape_string($r['sock_color']       ?? '');
    $color_bottom2 = $conn->real_escape_string($r['sock_color2']      ?? '');
    $c_or_a        = $conn->real_escape_string($r['cor_a']            ?? '');
    $name_packing  = $conn->real_escape_string($r['name_for_packing'] ?? '');
    $note          = $conn->real_escape_string($r['notes']            ?? '');

    // product_size_id is INT NOT NULL — resolve size text to numeric ID
    $jersey_size_text = trim($r['jersey_size'] ?? '');
    $product_size_id  = $size_map[strtolower($jersey_size_text)] ?? 0;

    // qty columns are smallint — must be int, not empty string
    $qty_top1    = (int)($r['jersey_qty']  ?? 0);
    $qty_top2    = (int)($r['jersey_qty2'] ?? 0);
    $qty_bottom1 = (int)($r['sock_qty']    ?? 0);
    $qty_bottom2 = (int)($r['sock_qty2']   ?? 0);

    if ($item_id > 0 && in_array($item_id, $existing_ids)) {
        // UPDATE existing row
        if ($target_table === 'tbl_draft_oi') {
            $sql = "UPDATE tbl_draft_oi SET
                        design_order_id=$design_order_id,
                        player_name='$player_name', sex='$sex', p_or_g='$p_or_g',
                        product_size_id=$product_size_id, jersey_number='$jersey_number',
                        color_top1='$color_top1', qty_top1=$qty_top1,
                        color_top2='$color_top2', qty_top2=$qty_top2,
                        bottom_size='$bottom_size',
                        color_bottom1='$color_bottom1', qty_bottom1=$qty_bottom1,
                        color_bottom2='$color_bottom2', qty_bottom2=$qty_bottom2,
                        c_or_a='$c_or_a', name_for_packing='$name_packing', note='$note'
                    WHERE oi_id=$item_id AND of_id=$of_id";
        } else {
            $sql = "UPDATE tbl_order_item SET
                        player_name='$player_name', sex='$sex', p_or_g='$p_or_g',
                        product_size_id=$product_size_id, jersey_number='$jersey_number',
                        color_top1='$color_top1', qty_top1=$qty_top1,
                        color_top2='$color_top2', qty_top2=$qty_top2,
                        bottom_size='$bottom_size',
                        color_bottom1='$color_bottom1', qty_bottom1=$qty_bottom1,
                        color_bottom2='$color_bottom2', qty_bottom2=$qty_bottom2,
                        c_or_a='$c_or_a', name_for_packing='$name_packing', note='$note'
                    WHERE oi_id=$item_id AND of_id=$of_id";
        }
        if ($conn->query($sql)) {
            $submitted_ids[] = $item_id;
            $updated++;
        } else {
            $errors[] = 'UPDATE error: ' . $conn->error;
        }
    } else {
        // INSERT new row
        if ($target_table === 'tbl_draft_oi') {
            $sql = "INSERT INTO tbl_draft_oi
                        (of_id, design_order_id, player_name, sex, p_or_g,
                         product_size_id, jersey_number,
                         color_top1, qty_top1, color_top2, qty_top2,
                         bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
                         c_or_a, name_for_packing, note)
                    VALUES
                        ($of_id, $design_order_id, '$player_name', '$sex', '$p_or_g',
                         $product_size_id, '$jersey_number',
                         '$color_top1', $qty_top1, '$color_top2', $qty_top2,
                         '$bottom_size', '$color_bottom1', $qty_bottom1, '$color_bottom2', $qty_bottom2,
                         '$c_or_a', '$name_packing', '$note')";
        } else {
            $sql = "INSERT INTO tbl_order_item
                        (of_id, player_name, sex, p_or_g,
                         product_size_id, jersey_number,
                         color_top1, qty_top1, color_top2, qty_top2,
                         bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
                         c_or_a, name_for_packing, note)
                    VALUES
                        ($of_id, '$player_name', '$sex', '$p_or_g',
                         $product_size_id, '$jersey_number',
                         '$color_top1', $qty_top1, '$color_top2', $qty_top2,
                         '$bottom_size', '$color_bottom1', $qty_bottom1, '$color_bottom2', $qty_bottom2,
                         '$c_or_a', '$name_packing', '$note')";
        }
        if ($conn->query($sql)) {
            $submitted_ids[] = (int)$conn->insert_id;
            $inserted++;
        } else {
            $errors[] = 'INSERT error: ' . $conn->error . ' | SQL: ' . $sql;
        }
    }
}

// DELETE rows removed by user
$to_delete = array_diff($existing_ids, $submitted_ids);
$deleted   = 0;
foreach ($to_delete as $del_id) {
    $del_id = (int)$del_id;
    $conn->query("DELETE FROM " . $target_table . " WHERE oi_id=$del_id AND of_id=$of_id");
    $deleted++;
}

// Save team name and year to tbl_order_form
$team_name = $conn->real_escape_string($input['team_name'] ?? '');
$team_year = $conn->real_escape_string($input['team_year'] ?? '');
if ($team_name !== '' || $team_year !== '') {
    $conn->query("UPDATE tbl_order_form SET on_team_name='$team_name', on_year='$team_year' WHERE of_id=$of_id");
}

if (!empty($errors)) {
    echo json_encode([
        'success'  => false,
        'message'  => implode(' | ', $errors),
        'of_id'    => $of_id,
        'inserted' => $inserted,
        'updated'  => $updated,
    ]);
    exit();
}

echo json_encode([
    'success'  => true,
    'of_id'    => $of_id,
    'inserted' => $inserted,
    'updated'  => $updated,
    'deleted'  => $deleted,
]);

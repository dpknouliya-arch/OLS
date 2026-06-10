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

$design_order_id = (int)($input['design_order_id'] ?? $input['order_id'] ?? 0);

if ($design_order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing design_order_id.']);
    exit();
}

// Verify ownership via API
$order_check = callAPI("get_order.php?order_id=$design_order_id");
if (empty($order_check['data']) || $order_check['status'] !== 200) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

// Build size name → size_id lookup (first occurrence wins for duplicate names)
$size_map = [];
$sz_res   = $conn->query("SELECT size_id, size_name FROM tbl_size ORDER BY size_id ASC");
if ($sz_res) {
    while ($sz = $sz_res->fetch_assoc()) {
        $key = strtolower(trim($sz['size_name']));
        if (!isset($size_map[$key])) {
            $size_map[$key] = (int)$sz['size_id'];
        }
    }
}

// ── Helper: resolve or create of_id for one team ───────────────────────────
function resolveOfId($conn, $client_of_id, $design_order_id, $user_id) {
    $client_of_id    = (int)$client_of_id;
    $design_order_id = (int)$design_order_id;

    if ($client_of_id > 0) {
        $s = $conn->prepare(
            "SELECT of_id FROM tbl_order_form WHERE of_id=? AND design_order_id=? LIMIT 1"
        );
        $s->bind_param("ii", $client_of_id, $design_order_id);
        $s->execute();
        $r = $s->get_result();
        $found = ($r->num_rows > 0) ? $client_of_id : 0;
        $s->close();
        if ($found > 0) return $found;
    }

    // New team (of_id=0): always create a fresh record
    return createNewTeamDraft($conn, $design_order_id, $user_id);
}

// ── Helper: upsert rows for one order form ─────────────────────────────────
function saveTeamRows($conn, $of_id, $design_order_id, $rows, $size_map, &$inserted, &$updated, &$deleted, &$errors) {
    $of_id           = (int)$of_id;
    $design_order_id = (int)$design_order_id;

    // Determine target table
    $s = $conn->prepare("SELECT is_submitted FROM tbl_order_form WHERE of_id=? LIMIT 1");
    $s->bind_param("i", $of_id);
    $s->execute();
    $res = $s->get_result();
    if ($res->num_rows === 0) {
        $errors[] = "of_id=$of_id not found";
        $s->close();
        return;
    }
    $is_sub  = (int)$res->fetch_assoc()['is_submitted'];
    $s->close();
    $tbl = ($is_sub === 0) ? 'tbl_draft_oi' : 'tbl_order_item';

    // Existing ids in this table for this form
    $existing_ids = [];
    $er = $conn->query("SELECT oi_id FROM $tbl WHERE of_id=$of_id");
    if ($er) {
        while ($e = $er->fetch_assoc()) { $existing_ids[] = (int)$e['oi_id']; }
    }

    $submitted_ids = [];

    foreach ($rows as $r) {
        $item_id       = (int)($r['item_id'] ?? 0);
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

        $jersey_size_text = trim($r['jersey_size'] ?? '');
        $product_size_id  = $size_map[strtolower($jersey_size_text)] ?? 0;

        $qty_top1    = (int)($r['jersey_qty']  ?? 0);
        $qty_top2    = (int)($r['jersey_qty2'] ?? 0);
        $qty_bottom1 = (int)($r['sock_qty']    ?? 0);
        $qty_bottom2 = (int)($r['sock_qty2']   ?? 0);

        if ($item_id > 0 && in_array($item_id, $existing_ids)) {
            if ($tbl === 'tbl_draft_oi') {
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
            if ($tbl === 'tbl_draft_oi') {
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

    // DELETE rows removed by user for this team
    foreach (array_diff($existing_ids, $submitted_ids) as $del_id) {
        $del_id = (int)$del_id;
        $conn->query("DELETE FROM $tbl WHERE oi_id=$del_id AND of_id=$of_id");
        $deleted++;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Multi-team format  (teams: [{of_id, team_name, team_year, rows}])
// ═══════════════════════════════════════════════════════════════════════════
if (isset($input['teams'])) {
    $teams_input  = $input['teams'];
    $deleted_oids = $input['deleted_of_ids'] ?? [];

    $inserted    = 0;
    $updated     = 0;
    $deleted     = 0;
    $errors      = [];
    $team_of_ids = [];

    foreach ($teams_input as $team) {
        $client_of_id = (int)($team['of_id'] ?? 0);
        $team_name    = $conn->real_escape_string($team['team_name'] ?? '');
        $team_year    = $conn->real_escape_string($team['team_year'] ?? '');
        $rows         = $team['rows'] ?? [];

        $of_id = resolveOfId($conn, $client_of_id, $design_order_id, $user_id);
        if ($of_id <= 0) {
            $errors[] = "Could not resolve of_id for team (client_of_id=$client_of_id)";
            $team_of_ids[] = 0;
            continue;
        }

        // Update team name/year
        $conn->query("UPDATE tbl_order_form SET on_team_name='$team_name', on_year='$team_year' WHERE of_id=$of_id");

        saveTeamRows($conn, $of_id, $design_order_id, $rows, $size_map, $inserted, $updated, $deleted, $errors);

        $team_of_ids[] = $of_id;
    }

    // Delete teams removed by user (only drafts)
    foreach ($deleted_oids as $del_oid) {
        $del_oid = (int)$del_oid;
        if ($del_oid <= 0) continue;
        $s = $conn->prepare(
            "SELECT of_id, is_submitted FROM tbl_order_form WHERE of_id=? AND design_order_id=? LIMIT 1"
        );
        $s->bind_param("ii", $del_oid, $design_order_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows > 0) {
            $rf = $r->fetch_assoc();
            if ((int)$rf['is_submitted'] === 0) {
                $conn->query("DELETE FROM tbl_draft_oi   WHERE of_id=$del_oid");
                $conn->query("DELETE FROM tbl_order_form WHERE of_id=$del_oid AND design_order_id=$design_order_id");
            }
        }
        $s->close();
    }

    if (!empty($errors)) {
        echo json_encode([
            'success'     => false,
            'message'     => implode(' | ', $errors),
            'team_of_ids' => $team_of_ids,
            'inserted'    => $inserted,
            'updated'     => $updated,
            'deleted'     => $deleted,
        ]);
        exit();
    }

    echo json_encode([
        'success'     => true,
        'team_of_ids' => $team_of_ids,
        'inserted'    => $inserted,
        'updated'     => $updated,
        'deleted'     => $deleted,
    ]);
    exit();
}

// ═══════════════════════════════════════════════════════════════════════════
// Legacy single-team format  (rows: [...], of_id, team_name, team_year)
// ═══════════════════════════════════════════════════════════════════════════
$rows         = $input['rows'] ?? [];
$client_of_id = (int)($input['of_id'] ?? 0);

$of_id = 0;
if ($client_of_id > 0) {
    $stmt_chk = $conn->prepare(
        "SELECT of_id FROM tbl_order_form WHERE of_id=? AND design_order_id=? LIMIT 1"
    );
    $stmt_chk->bind_param("ii", $client_of_id, $design_order_id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    if ($res_chk->num_rows > 0) { $of_id = $client_of_id; }
    $stmt_chk->close();
}
if ($of_id <= 0) {
    $stmt_find = $conn->prepare(
        "SELECT of_id FROM tbl_order_form WHERE design_order_id=? ORDER BY is_submitted DESC LIMIT 1"
    );
    $stmt_find->bind_param("i", $design_order_id);
    $stmt_find->execute();
    $res_find = $stmt_find->get_result();
    if ($res_find->num_rows > 0) { $of_id = (int)$res_find->fetch_assoc()['of_id']; }
    $stmt_find->close();
}
if ($of_id <= 0) {
    $of_id = getOrCreateDraftOrder($conn, $design_order_id, $user_id);
}
if ($of_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Could not create draft order.']);
    exit();
}

$inserted = 0; $updated = 0; $deleted = 0; $errors = [];
saveTeamRows($conn, $of_id, $design_order_id, $rows, $size_map, $inserted, $updated, $deleted, $errors);

$team_name = $conn->real_escape_string($input['team_name'] ?? '');
$team_year = $conn->real_escape_string($input['team_year'] ?? '');
if ($team_name !== '' || $team_year !== '') {
    $conn->query("UPDATE tbl_order_form SET on_team_name='$team_name', on_year='$team_year' WHERE of_id=$of_id");
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' | ', $errors), 'of_id' => $of_id, 'inserted' => $inserted, 'updated' => $updated]);
    exit();
}

echo json_encode(['success' => true, 'of_id' => $of_id, 'inserted' => $inserted, 'updated' => $updated, 'deleted' => $deleted]);

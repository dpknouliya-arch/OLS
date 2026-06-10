<?php

function create3DOrderDraft($conn, $design_order_id, $user_id) {
    $design_order_id = (int)$design_order_id;
    $user_id         = (int)$user_id;

    // STEP 1: Return existing unsubmitted draft if one already exists
    $stmt = $conn->prepare(
        "SELECT of_id FROM tbl_order_form WHERE design_order_id = ? AND is_submitted = 0 LIMIT 1"
    );
    if (!$stmt) {
        error_log('create3DOrderDraft SELECT prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("i", $design_order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row   = $res->fetch_assoc();
        $of_id = (int)$row['of_id'];
        $stmt->close();

        // DEBUG GUARD: of_id must never be 0 on an existing draft row
        if ($of_id === 0) {
            error_log("create3DOrderDraft INTEGRITY ERROR: existing draft has of_id=0 for design_order_id=$design_order_id — data corruption detected");
            return 0;
        }

        return $of_id;
    }
    $stmt->close();

    // STEP 2: Insert into tbl_draft_of FIRST — authoritative source of of_id
    $draft_id = '3D' . date('YmdHis') . str_pad($design_order_id, 4, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare(
        "INSERT INTO tbl_draft_of
            (draft_id, form_name, special_comment,
             order_date, req_due_date, game_event_date,
             project_name, payment_opt, sales_rep_id, reorder_num, prod_id,
             user_id,
             bill_comp_name, bill_contact_name, bill_address, bill_city,
             bill_country, bill_zip_code, bill_tel, bill_email,
             deli_comp_name, deli_contact_name, deli_address, deli_city,
             deli_country, deli_zip_code, deli_tel, deli_email,
             is_3dorder, date_add)
         VALUES
            (?, '3D Jersey', '',
             CURDATE(), CURDATE(), '',
             '', '', 0, '', 1,
             ?,
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             1, NOW())"
    );
    if (!$stmt) {
        error_log('create3DOrderDraft tbl_draft_of INSERT prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("si", $draft_id, $user_id);
    if (!$stmt->execute()) {
        error_log('create3DOrderDraft tbl_draft_of INSERT execute failed: ' . $stmt->error);
        $stmt->close();
        return 0;
    }
    $of_id = (int)$conn->insert_id;
    $stmt->close();

    // DEBUG GUARD: tbl_draft_of must yield a real AUTO_INCREMENT value
    if ($of_id === 0) {
        error_log("create3DOrderDraft CRITICAL: tbl_draft_of INSERT returned insert_id=0 — tbl_order_form will NOT be inserted. user_id=$user_id design_order_id=$design_order_id");
        return 0;
    }

    // STEP 3: Insert into tbl_order_form using the SAME of_id from tbl_draft_of

    $stmt = $conn->prepare(
        "INSERT INTO tbl_order_form
            (of_id, draft_id, is_submitted, form_name, special_comment,
             order_date, order_status,
             req_due_date, game_event_date, project_name,
             payment_opt, sales_rep_id, reorder_num,
             prod_id, user_id, design_order_id,
             bill_comp_name, bill_contact_name, bill_address, bill_city,
             bill_country, bill_zip_code, bill_tel, bill_email,
             deli_comp_name, deli_contact_name, deli_address, deli_city,
             deli_country, deli_zip_code, deli_tel, deli_email,
             date_add)
         VALUES
            (?, ?, 0, '3D Jersey', '',
             CURDATE(), 'draft',
             CURDATE(), '', '',
             '', 0, '',
             1, ?, ?,
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             NOW())"
    );
    if (!$stmt) {
        error_log('create3DOrderDraft tbl_order_form INSERT prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("isii", $of_id, $draft_id, $user_id, $design_order_id);
    if (!$stmt->execute()) {
        error_log('create3DOrderDraft tbl_order_form INSERT execute failed: ' . $stmt->error);
        $stmt->close();
        return 0;
    }
    $stmt->close();

    // STEP 4: Return the of_id — same value used in both tbl_draft_of and tbl_order_form
    return $of_id;
}

function getOrCreateDraftOrder($conn, $design_order_id, $user_id) {
    // All 3D order draft creation must go through create3DOrderDraft so that
    // tbl_draft_of is always inserted FIRST and of_id always originates there.
    return create3DOrderDraft($conn, $design_order_id, $user_id);
}

/**
 * Always creates a NEW order form for a team (no dedup check).
 * Used when a user adds a second/third team in the multi-team roster UI.
 */
function createNewTeamDraft($conn, $design_order_id, $user_id) {
    $design_order_id = (int)$design_order_id;
    $user_id         = (int)$user_id;

    $draft_id = '3D' . date('YmdHis') . str_pad($design_order_id, 4, '0', STR_PAD_LEFT) . rand(10, 99);

    $stmt = $conn->prepare(
        "INSERT INTO tbl_draft_of
            (draft_id, form_name, special_comment,
             order_date, req_due_date, game_event_date,
             project_name, payment_opt, sales_rep_id, reorder_num, prod_id,
             user_id,
             bill_comp_name, bill_contact_name, bill_address, bill_city,
             bill_country, bill_zip_code, bill_tel, bill_email,
             deli_comp_name, deli_contact_name, deli_address, deli_city,
             deli_country, deli_zip_code, deli_tel, deli_email,
             is_3dorder, date_add)
         VALUES
            (?, '3D Jersey', '',
             CURDATE(), CURDATE(), '',
             '', '', 0, '', 1,
             ?,
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             1, NOW())"
    );
    if (!$stmt) {
        error_log('createNewTeamDraft tbl_draft_of prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("si", $draft_id, $user_id);
    if (!$stmt->execute()) {
        error_log('createNewTeamDraft tbl_draft_of execute failed: ' . $stmt->error);
        $stmt->close();
        return 0;
    }
    $of_id = (int)$conn->insert_id;
    $stmt->close();

    if ($of_id === 0) {
        error_log("createNewTeamDraft: tbl_draft_of returned insert_id=0 for design_order_id=$design_order_id");
        return 0;
    }

    $stmt = $conn->prepare(
        "INSERT INTO tbl_order_form
            (of_id, draft_id, is_submitted, form_name, special_comment,
             order_date, order_status,
             req_due_date, game_event_date, project_name,
             payment_opt, sales_rep_id, reorder_num,
             prod_id, user_id, design_order_id,
             bill_comp_name, bill_contact_name, bill_address, bill_city,
             bill_country, bill_zip_code, bill_tel, bill_email,
             deli_comp_name, deli_contact_name, deli_address, deli_city,
             deli_country, deli_zip_code, deli_tel, deli_email,
             date_add)
         VALUES
            (?, ?, 0, '3D Jersey', '',
             CURDATE(), 'draft',
             CURDATE(), '', '',
             '', 0, '',
             1, ?, ?,
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             '', '', '', '',
             NOW())"
    );
    if (!$stmt) {
        error_log('createNewTeamDraft tbl_order_form prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("isii", $of_id, $draft_id, $user_id, $design_order_id);
    if (!$stmt->execute()) {
        error_log('createNewTeamDraft tbl_order_form execute failed: ' . $stmt->error);
        $stmt->close();
        return 0;
    }
    $stmt->close();

    return $of_id;
}

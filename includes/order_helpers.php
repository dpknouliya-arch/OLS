<?php

function getOrCreateDraftOrder($conn, $design_order_id, $user_id) {
    $design_order_id = (int)$design_order_id;
    $user_id         = (int)$user_id;

    // Also fetch draft_id so we can backfill it if missing on an existing row
    $stmt = $conn->prepare(
        "SELECT of_id, draft_id FROM tbl_order_form WHERE design_order_id=? LIMIT 1"
    );
    if (!$stmt) {
        error_log('getOrCreateDraftOrder SELECT prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("i", $design_order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row            = $res->fetch_assoc();
        $existing_of_id = (int)$row['of_id'];
        $existing_draft = $row['draft_id'] ?? '';
        $stmt->close();

        // Backfill draft_id only when it is genuinely absent (NULL or empty string).
        // '0' means the order was already submitted — do not overwrite.
        if ($existing_draft === null || $existing_draft === '') {
            $backfill_draft_id = '3D' . date('YmdHis') . str_pad($design_order_id, 4, '0', STR_PAD_LEFT);
            $upd = $conn->prepare(
                "UPDATE tbl_order_form SET draft_id = ? WHERE of_id = ? AND (draft_id IS NULL OR draft_id = '')"
            );
            $upd->bind_param("si", $backfill_draft_id, $existing_of_id);
            $upd->execute();
            $upd->close();
        }

        return $existing_of_id;
    }
    $stmt->close();

    // Format: 3D + YYYYMMDDHHmmss + zero-padded design_order_id (4 digits)
    // The suffix makes the value unique even when two orders are created in the same second.
    $draft_id = '3D' . date('YmdHis') . str_pad($design_order_id, 4, '0', STR_PAD_LEFT);

    // of_id = 0 is a temporary placeholder; it is overwritten with the real `id`
    // immediately after INSERT so all existing WHERE of_id=? queries keep working.
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
            (0, ?, 0, '3D Jersey', '',
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
        error_log('getOrCreateDraftOrder INSERT prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("sii", $draft_id, $user_id, $design_order_id);
    if (!$stmt->execute()) {
        error_log('getOrCreateDraftOrder INSERT execute failed: ' . $stmt->error);
        $stmt->close();
        return 0;
    }
    $new_id = (int)$conn->insert_id; // this is the new `id` PK (AUTO_INCREMENT >= 100000)
    $stmt->close();

    // Align of_id with id so every downstream query using of_id continues to work
    $upd = $conn->prepare("UPDATE tbl_order_form SET of_id = ? WHERE id = ?");
    $upd->bind_param("ii", $new_id, $new_id);
    $upd->execute();
    $upd->close();

    return $new_id;
}

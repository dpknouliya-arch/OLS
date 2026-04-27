<?php

function getOrCreateDraftOrder($conn, $design_order_id, $user_id) {
    $design_order_id = (int)$design_order_id;
    $user_id         = (int)$user_id;

    $stmt = $conn->prepare(
        "SELECT of_id FROM tbl_order_form WHERE design_order_id=? LIMIT 1"
    );
    if (!$stmt) {
        error_log('getOrCreateDraftOrder SELECT prepare failed: ' . $conn->error);
        return 0;
    }
    $stmt->bind_param("i", $design_order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $stmt->close();
        return (int)$row['of_id'];
    }
    $stmt->close();

    $draft_id = '3D' . $design_order_id;

    // Include every NOT NULL column that has no DEFAULT value in tbl_order_form
    $stmt = $conn->prepare(
        "INSERT INTO tbl_order_form
            (draft_id, is_submitted, form_name, special_comment,
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
            (?, 0, '3D Jersey', '',
             CURDATE(), 'draft',
             CURDATE(), '', '',
             '', 0, '',
             0, ?, ?,
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
    $new_id = (int)$conn->insert_id;
    $stmt->close();
    return $new_id;
}

<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
    echo json_encode(['result' => 'fail', 'msg' => 'Session expired.']);
    exit();
}

include('../../db.php');

header('Content-Type: application/json');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = (int)$obj_user->user_id;

$design_order_id = (int)($_POST['design_order_id'] ?? 0);
$customer_po     = addslashes($_POST['customer_po']     ?? '');
$req_due_date    = addslashes($_POST['req_due_date']    ?? '');
$game_event_date = addslashes($_POST['game_event_date'] ?? '');
$project_name    = addslashes($_POST['project_name']    ?? '');
$payment_opt     = addslashes($_POST['payment_opt']     ?? '');
$sales_rep_id    = (int)($_POST['sales_rep_id'] ?? 0);
$reorder_num     = addslashes($_POST['reorder_num']     ?? '');

if ($design_order_id <= 0) {
    echo json_encode(['result' => 'fail', 'msg' => 'Missing design_order_id.']);
    exit();
}

// Verify design_order ownership via API (no direct DB access to jogdigital)
$order_check = callAPI("get_order.php?order_id=$design_order_id");
if (empty($order_check['data']) || $order_check['status'] !== 200) {
    echo json_encode(['result' => 'fail', 'msg' => 'Unauthorized.']);
    exit();
}

// Fetch existing order form (draft or already submitted — one row per design_order_id)
$stmt = $conn->prepare(
    "SELECT of_id FROM tbl_order_form WHERE design_order_id=? LIMIT 1"
);
$stmt->bind_param("i", $design_order_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['result' => 'fail', 'msg' => 'No order form found for this design order.']);
    exit();
}
$row_draft = $res->fetch_assoc();
$of_id     = (int)$row_draft['of_id'];
$stmt->close();

// Fetch billing address
$bill = [
    'comp_name'    => '', 'contact_name' => '', 'address'  => '',
    'city'         => '', 'country'      => '', 'zip_code' => '',
    'tel'          => '', 'email'        => '', 'tax_id'   => '',
];
$sql_bill = "SELECT * FROM tbl_address WHERE user_id='" . $user_id . "' AND is_billing_addr=1 AND enable=1 LIMIT 1";
$rs_bill  = $conn->query($sql_bill);
if ($rs_bill && $rs_bill->num_rows > 0) {
    $row_bill = $rs_bill->fetch_assoc();
    $bill['comp_name']    = addslashes($row_bill['addr_name']    ?? '');
    $bill['contact_name'] = addslashes($row_bill['contact_name'] ?? '');
    $bill['address']      = addslashes($row_bill['address']      ?? '');
    $bill['city']         = addslashes($row_bill['city']         ?? '');
    $bill['country']      = addslashes($row_bill['country']      ?? '');
    $bill['zip_code']     = addslashes($row_bill['zip_code']     ?? '');
    $bill['tel']          = addslashes($row_bill['tel']          ?? '');
    $bill['email']        = addslashes($row_bill['email']        ?? '');
    $bill['tax_id']       = addslashes($row_bill['tax_id']       ?? '');
}

// Fetch delivery address
$deli = [
    'comp_name'    => '', 'contact_name' => '', 'address'  => '',
    'city'         => '', 'country'      => '', 'zip_code' => '',
    'tel'          => '', 'email'        => '', 'tax_id'   => '',
];
$sql_deli = "SELECT * FROM tbl_address WHERE user_id='" . $user_id . "' AND is_deliver_addr=1 AND enable=1 LIMIT 1";
$rs_deli  = $conn->query($sql_deli);
if ($rs_deli && $rs_deli->num_rows > 0) {
    $row_deli = $rs_deli->fetch_assoc();
    $deli['comp_name']    = addslashes($row_deli['addr_name']    ?? '');
    $deli['contact_name'] = addslashes($row_deli['contact_name'] ?? '');
    $deli['address']      = addslashes($row_deli['address']      ?? '');
    $deli['city']         = addslashes($row_deli['city']         ?? '');
    $deli['country']      = addslashes($row_deli['country']      ?? '');
    $deli['zip_code']     = addslashes($row_deli['zip_code']     ?? '');
    $deli['tel']          = addslashes($row_deli['tel']          ?? '');
    $deli['email']        = addslashes($row_deli['email']        ?? '');
    $deli['tax_id']       = addslashes($row_deli['tax_id']       ?? '');
}

// UPDATE tbl_order_form — mark as submitted
$sql_update = "UPDATE tbl_order_form SET
    is_submitted    = 1,
    submitted_date  = NOW(),
    order_status    = 'new',
    customer_po     = '$customer_po',
    req_due_date    = '$req_due_date',
    game_event_date = '$game_event_date',
    project_name    = '$project_name',
    payment_opt     = '$payment_opt',
    sales_rep_id    = '$sales_rep_id',
    reorder_num     = '$reorder_num',
    prod_id       = 1, -- hardcoded for 3D Jersey
    bill_comp_name    = '{$bill['comp_name']}',
    bill_contact_name = '{$bill['contact_name']}',
    bill_address      = '{$bill['address']}',
    bill_city         = '{$bill['city']}',
    bill_country      = '{$bill['country']}',
    bill_zip_code     = '{$bill['zip_code']}',
    bill_tel          = '{$bill['tel']}',
    bill_email        = '{$bill['email']}',
    bill_tax_id       = '{$bill['tax_id']}',
    deli_comp_name    = '{$deli['comp_name']}',
    deli_contact_name = '{$deli['contact_name']}',
    deli_address      = '{$deli['address']}',
    deli_city         = '{$deli['city']}',
    deli_country      = '{$deli['country']}',
    deli_zip_code     = '{$deli['zip_code']}',
    deli_tel          = '{$deli['tel']}',
    deli_email        = '{$deli['email']}',
    deli_tax_id       = '{$deli['tax_id']}'
WHERE of_id='$of_id'";

$conn->query($sql_update);

// Migrate roster from tbl_draft_oi → tbl_order_item (only when draft items exist)
$draft_count_res = $conn->query("SELECT COUNT(*) AS cnt FROM tbl_draft_oi WHERE of_id='$of_id'");
$draft_count     = (int)($draft_count_res ? $draft_count_res->fetch_assoc()['cnt'] : 0);

if ($draft_count > 0) {
    $sql_migrate = "INSERT INTO tbl_order_item
        (of_id, player_name, sex, p_or_g,
         product_size_id, jersey_number,
         color_top1, qty_top1, color_top2, qty_top2,
         bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
         c_or_a, name_for_packing, note)
    SELECT
        of_id, player_name, sex, p_or_g,
        product_size_id, jersey_number,
        color_top1, qty_top1, color_top2, qty_top2,
        bottom_size, color_bottom1, qty_bottom1, color_bottom2, qty_bottom2,
        c_or_a, name_for_packing, note
    FROM tbl_draft_oi
    WHERE of_id='$of_id'";

    $migrate_ok = $conn->query($sql_migrate);
    if (!$migrate_ok) {
        echo json_encode(['result' => 'fail', 'msg' => 'Roster migration failed: ' . $conn->error]);
        exit();
    }
    $conn->query("DELETE FROM tbl_draft_oi WHERE of_id='$of_id'");
}
// If draft_count=0 the roster was already in tbl_order_item (re-submission) — nothing to migrate

// Notification
if ($sales_rep_id > 0) {
    $noti_detail = '3D ORDER FROM OLS';
    $sql_noti = "INSERT INTO notification (noti_detail, employee_id)
                 VALUES ('" . $noti_detail . "', '" . $sales_rep_id . "')";
    $conn3->query($sql_noti);
}

echo json_encode(['result' => 'success']);

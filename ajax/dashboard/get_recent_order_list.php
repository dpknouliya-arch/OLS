<?php
session_start();

$lockeroom_url = "https://locker-test.jog-joinourgame.com";

if (!isset($_SESSION["JOGOLS"])) {
    echo '<center>Please re-login again.</center>';
    exit();
}

include('../../db.php');

$user_details = json_decode(base64_decode($_SESSION['JOGOLS']));
$user_id = $user_details->user_id;
$customer_id = $user_details->customer_id;


// ============== SECURE: Use prepared statements for all queries ==============

// Get all the excodes from locker room (SECURE)
$sql = "SELECT DISTINCT order_main_code FROM order_main WHERE customer_id = ?";
$stmt = $conn3->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$codes = array_column($result, 'order_main_code');
$locker_result = [];

if (empty($result)):
    $response = [
        'html' => '<tr>
             <td class="text-center" colspan="16"> No record found</td>
           </tr>',
        'pagination' => '',
    ];
    echo json_encode($response);
    exit;
endif;

// Prepare placeholders for IN clause
$codes = array_values($codes);
$placeholders = implode(',', array_fill(0, count($codes), '?'));

$sql = "SELECT
    om.order_main_id,
    om.order_main_name AS order_name,
    om.order_main_code AS main_ex_code,
    om.match_ols,

    MAX(omf.order_main_file_id) AS order_main_file_id,
    MAX(omf.order_main_file_title) AS file_title,
    MAX(omf.order_main_file_type) AS file_type,

    MAX(
        CASE
            WHEN omf.order_main_file_type = 'Order Form'
            THEN omf.order_main_file_name
        END
    ) AS order_main_file_name

FROM order_main AS om
LEFT JOIN order_main_file AS omf
       ON om.order_main_id = omf.order_main_id
WHERE om.order_main_code IN ($placeholders)
GROUP BY om.order_main_id";

$stmt = $conn3->prepare($sql);

if (!$stmt) {
    die('Prepare failed: ' . $conn3->error);
}

$stmt->bind_param(str_repeat('s', count($codes)), ...$codes);
$stmt->execute();

$result = $stmt->get_result();
$locker_result = $result->fetch_all(MYSQLI_ASSOC);


// ============== SECURE: Parameterized filter inputs ==============
$orderDetails = isset($_POST['orderDetails']) ? (int)$_POST['orderDetails'] : 0;
$year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
$invoice = isset($_POST['inv_status']) ? $_POST['inv_status'] : NULL;
$search = isset($_POST['search']) ? $_POST['search'] : NULL;
$page = !empty($_POST['page']) ? (int)$_POST['page'] : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

$limit_sql = $orderDetails ? " LIMIT ?, ? " : " LIMIT 5";
$where_condition = "";
$bind_params = [];
$bind_types = "";

// Build where clause with prepared statement parameters
if ($orderDetails) {
    if (!empty($year)) {
        $where_condition .= " AND YEAR(qd.created_date) = ? ";
        $bind_params[] = $year;
        $bind_types .= 'i';
    }

    if (!empty($invoice)) {
        // Validate invoice status to prevent injection
        $allowed_statuses = ['Paid', 'Outstanding', 'Pending'];
        if (in_array($invoice, $allowed_statuses)) {
            if ($invoice === 'Paid') {
                $where_condition .= " AND calc.invoice_status = 'Paid' ";
            } elseif ($invoice === 'Outstanding') {
                $where_condition .= " AND calc.invoice_status = 'Outstanding' ";
            } elseif ($invoice === 'Pending') {
                $where_condition .= " AND (calc.invoice_status IS NULL OR calc.invoice_status NOT IN ('Paid','Outstanding')) ";
            }
        }
    }

    if (!empty($search)) {
        $search_param = "%{$search}%";
        $where_condition .= " AND (tqd.est_number LIKE ? OR
                                     tqd.cust_name LIKE ?  OR
                                     tqd.po_number LIKE ? OR
                                     tp.prod_name LIKE ? OR
                                     qd.conv_by LIKE ?  OR
                                     qd.jog_code LIKE ?) ";
        // Add search parameter 6 times for each LIKE
        for ($i = 0; $i < 6; $i++) {
            $bind_params[] = $search_param;
        }
        $bind_types .= 'ssssss';
    }

    // Add pagination params
    $bind_params[] = $offset;
    $bind_params[] = $per_page;
    $bind_types .= 'ii';
}

$sql = "SELECT qd.conv_id AS conv_id,
               qd.jog_code AS ex_code,
               qd.conv_by AS sales_person,
               qd.created_date AS order_date,
               qd.conv_status AS sales_status,
               qd.invoice_link AS invoice_link,

               tqd.qdoc_id AS qdoc_id,
               tqd.est_number AS estimate_number,
               tqd.cust_name AS customer_name,
               tqd.po_number AS po_number,

               COUNT(tqi.qdoci_id) AS total_items,
               SUM(tqi.qty) AS total_qty,
               tqi.pro_type AS product_type,

              CASE WHEN tqd.inv_no IS NULL THEN 'Not Created'
                   WHEN calc.invoice_status = 'Paid' THEN 'Paid'
                   WHEN calc.invoice_status = 'Outstanding' THEN 'Unpaid'
                   ELSE 'Pending'
             END AS invoice_status,

            tp.prod_name AS product_name
            FROM quotation_data AS qd
            LEFT JOIN tbl_quote_doc AS tqd ON tqd.qdoc_id = qd.qdoci_id
            LEFT JOIN tbl_quote_item AS tqi ON tqi.qdoc_id = qd.qdoci_id
            LEFT JOIN tbl_product AS tp ON tp.prod_type = tqi.pro_type
            LEFT JOIN calculator AS calc ON calc.invoice = tqd.inv_no
       WHERE qd.jog_code IN ($placeholders) $where_condition
       GROUP BY tqi.qdoc_id
       ORDER BY qd.created_date DESC $limit_sql";

// Prepare the statement with dynamic parameters
$stmt = $conn5->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn5->error);
}

// Build parameter array: first codes, then filter params
$params = array_merge($codes, $bind_params);
$types = str_repeat('s', count($codes)) . $bind_types;

$stmt->bind_param($types, ...$params);
$stmt->execute();
$sales_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$total_rows = COUNT($sales_result);
$total_pages = ceil($total_rows / $per_page);


// Index locker data by ex_code (fast lookup)
$lockerByCode = [];

foreach ($locker_result as $locker) {
    $lockerByCode[strtolower($locker['main_ex_code'])] = $locker;
}

// Merge while looping sales_result (SQL order stays intact)
$newArray = [];

foreach ($sales_result as $sale) {
    $code = strtolower($sale['ex_code']);

    if (isset($lockerByCode[$code])) {
        $newArray[] = array_merge($lockerByCode[$code], $sale);
    }
}



$html2 = "";
$start = ($page - 1) * $per_page + 1;
$end = min($page * $per_page, $total_rows);

if ($orderDetails) {
    $html2 .= '<span>Page ' . $start . ' of ' . $total_rows . '</span>
                        <div> ';
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i == $page ? 'active' : 'unactive';
        $html2 .= '<button class="' . $active . '   pagination_btn">' . $i . '</button>';
    }

    $html2 .= '</div>';
}

ob_start();

$dt = $newArray;
include './order_list.php';   // this file will use $newArray and generate HTML

$html = ob_get_clean();

$response = [
    'html' => $html,
    'pagination' => $html2,
];

echo json_encode($response);
exit;

<?php
/**
 * Order Count API - Secure Version
 * Returns order statistics for authenticated customers
 */

session_start();

header('Content-Type: application/json');

/**
 * Send error response and exit
 */
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'status' => $code,
        'error' => $message,
        'total_order' => 0,
        'total_items' => 0,
        'complete_orders' => 0,
        'total_spend' => 0,
        'total_invoice' => 0,
        'Paid' => 0,
        'Unpaid' => 0,
        'Pending' => 0
    ]);
    exit;
}

// Check authentication
if (!isset($_SESSION["JOGOLS"])) {
    sendError('Please re-login again.', 401);
}

include('../../db.php');

// Check if database connections exist
if (!isset($conn3) || !isset($conn5)) {
    sendError('Database connection not available');
}

try {
    // Decode and validate session data
    $user_details = json_decode(base64_decode($_SESSION['JOGOLS']));
    if (!$user_details || !isset($user_details->customer_id)) {
        sendError('Invalid session data', 401);
    }

    $user_id = (int)$user_details->user_id;
    $customer_id = (int)$user_details->customer_id;

    // Validate year parameter
    $Year = isset($_POST['year']) ? (int)$_POST['year'] : 0;

    // Default response (safe zero state)
    $response = [
        'status' => 200,
        'total_order' => 0,
        'total_items' => 0,
        'complete_orders' => 0,
        'total_spend' => 0,
        'total_invoice' => 0,
        'Paid' => 0,
        'Unpaid' => 0,
        'Pending' => 0
    ];

    /* =========================
       1. Get order_main_codes (SECURE)
    ========================= */
    $sql = "SELECT DISTINCT order_main_code FROM order_main WHERE customer_id = ?";
    $stmt = $conn3->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn3->error);
    }
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $codes = array_column($result, 'order_main_code');

    // No codes found - return safe zero response
    if (empty($codes)) {
        echo json_encode($response);
        exit;
    }

    // Build placeholders for IN clause (SECURE - no string concatenation)
    $placeholders = implode(',', array_fill(0, count($codes), '?'));
    $types = str_repeat('s', count($codes));

    $date_sql = '';
    $params = $codes;
    $paramTypes = $types;

    if (!empty($Year)) {
        $date_sql = " AND YEAR(created_date) = ? ";
        $paramTypes .= 'i';
        $params[] = $Year;
    }

    /* =========================
       2. Get qdoc IDs (SECURE)
    ========================= */
    $sql = "SELECT DISTINCT qdoci_id AS qdoc_id 
        FROM quotation_data
        WHERE jog_code IN ($placeholders)
        $date_sql
    ";

    $stmt = $conn5->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn5->error);
    }
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $qdoc_id_arr = array_column($result, 'qdoc_id');

    // No qdoc IDs found - return safe zero response
    if (empty($qdoc_id_arr)) {
        echo json_encode($response);
        exit;
    }

    // Prepare qdoc placeholders
    $qdocPlaceholders = implode(',', array_fill(0, count($qdoc_id_arr), '?'));
    $qdocTypes = str_repeat('i', count($qdoc_id_arr));

    /* =========================
       3. Total orders (SECURE)
    ========================= */
    $sql = "SELECT COUNT(DISTINCT conv_id) AS total_order
        FROM quotation_data
        WHERE qdoci_id IN ($qdocPlaceholders)
    ";

    $stmt = $conn5->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn5->error);
    }
    $stmt->bind_param($qdocTypes, ...$qdoc_id_arr);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['total_order'] = (int)$row['total_order'];


    // Get Excode that is in sales rep 
    $sql = "SELECT distinct jog_code  FROM quotation_data  Where qdoci_id IN ($qdocPlaceholders) " ;
    $stmt = $conn5->prepare($sql);
    $stmt->bind_param($qdocTypes, ...$qdoc_id_arr);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $codes = array_column($row, 'jog_code'); 
   

    /* =========================
       3b. Complete orders (SECURE - using placeholders)
    ========================= */
    $codePlaceholders = implode(',', array_fill(0, count($codes), '?'));
    $codeTypes = str_repeat('s', count($codes));

    $sql = "SELECT COUNT(DISTINCT order_main_id) AS complete_order
            FROM order_main
            WHERE order_main_code IN ($codePlaceholders) AND order_main_status = 3";

    $stmt = $conn3->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn3->error);
    }
    $stmt->bind_param($codeTypes, ...$codes);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['complete_orders'] = (int)$row['complete_order'];

    /* =========================
       4. Total items (SECURE)
    ========================= */
    $sql = "SELECT COUNT(DISTINCT qdoci_id) AS total_item
        FROM tbl_quote_item
        WHERE qdoc_id IN ($qdocPlaceholders)
    ";

    $stmt = $conn5->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn5->error);
    }
    $stmt->bind_param($qdocTypes, ...$qdoc_id_arr);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['total_items'] = (int)$row['total_item'];

    /* =========================
       5. Spend & invoices (SECURE)
    ========================= */
    $sql = "SELECT
            SUM(sub_total) AS total_spend,
            COUNT(DISTINCT inv_no) AS total_invoice
        FROM tbl_quote_doc
        WHERE qdoc_id IN ($qdocPlaceholders)
    ";

    $stmt = $conn5->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn5->error);
    }
    $stmt->bind_param($qdocTypes, ...$qdoc_id_arr);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $response['total_spend'] = (float)($row['total_spend'] ?? 0);
    $response['total_invoice'] = (int)($row['total_invoice'] ?? 0);

    /* =========================
       6. Invoice status (SECURE)
    ========================= */
    if ($response['total_invoice'] > 0) {
        $sql = "SELECT
                COUNT(DISTINCT CASE WHEN invoice_status = 'Paid' THEN invoice END) AS paid_count,
                COUNT(DISTINCT CASE WHEN invoice_status = 'Outstanding' THEN invoice END) AS unpaid_count
            FROM calculator
            WHERE invoice IN (
                SELECT DISTINCT inv_no FROM tbl_quote_doc WHERE qdoc_id IN ($qdocPlaceholders)
            )
        ";

        $stmt = $conn5->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn5->error);
        }
        $stmt->bind_param($qdocTypes, ...$qdoc_id_arr);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $paid = (int)$row['paid_count'];
        $unpaid = (int)$row['unpaid_count'];

        // Safe calculation with division by zero protection
        $totalInvoice = $response['total_invoice'];
        $pending = $totalInvoice - ($paid + $unpaid);

        $response['Paid'] = $totalInvoice > 0 ? round(($paid / $totalInvoice) * 100) : 0;
        $response['Unpaid'] = $totalInvoice > 0 ? round(($unpaid / $totalInvoice) * 100) : 0;
        $response['Pending'] = $totalInvoice > 0 ? round(($pending / $totalInvoice) * 100) : 0;
    }

    echo json_encode($response);
    exit;

} catch (Exception $e) {
    error_log("OrderCount API Error: " . $e->getMessage());
    sendError('An error occurred while processing your request');
}

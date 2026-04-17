<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
    echo '<center>Please re-login again.</center>';
    exit();
}

include('../../db.php');

$user_details = json_decode(base64_decode($_SESSION['JOGOLS']));
$user_id = $user_details->user_id;
$customer_id = $user_details->customer_id;


// get all the excodes from locker room  
$sql = "SELECT DISTINCT order_main_code FROM order_main WHERE customer_id = ?";
$stmt = $conn3->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$codes = array_column($result, 'order_main_code');

if (empty($codes) || !is_array($codes)) {
    echo json_encode([
        'status' => 404,
        'year' => [],
        'spends' => [],
        'max_value' => 0,
        'max_array' => [],
        'items_year' => [],
        'items' => [],
        'max_items' => 0,
        'maxItemsArr' => []
    ]);
    exit;
}

$decoded_string = base64_decode($_POST['type'], true);

// --- get total spend amount by the year 
if (isset($_POST['type']) && $decoded_string == 'Spend'):
    /* ------------------------------------Prepare IN() placeholders------------------------------------ */
    $placeholders = implode(',', array_fill(0, count($codes), '?'));
    $types = str_repeat('s', count($codes));

    /* ------------------------------------ Total spend per year------------------------------------ */
    $sqlSpend = "SELECT 
        YEAR(qd.created_date) AS year,
        SUM(tqd.sub_total) AS total_spend
    FROM tbl_quote_doc tqd
    LEFT JOIN quotation_data qd 
        ON qd.qdoci_id = tqd.qdoc_id
    WHERE qd.jog_code IN ($placeholders)
    GROUP BY YEAR(qd.created_date)
    ORDER BY YEAR(qd.created_date)
";

    $stmt = $conn5->prepare($sqlSpend);
    $stmt->bind_param($types, ...$codes);
    $stmt->execute();
    $spendResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $years = array_column($spendResult, 'year');
    $totalSpends = array_map('floatval', array_column($spendResult, 'total_spend'));
    $maximum_val = $totalSpends ? max($totalSpends) : 0;
    $maxArray = array_fill(0, count($totalSpends), $maximum_val);

    /* ------------------------------------ Total items per year------------------------------------ */
    $sqlItems = "SELECT 
        YEAR(qd.created_date) AS year,
        COUNT(tqi.qdoci_id) AS total_items
    FROM tbl_quote_item tqi
    LEFT JOIN quotation_data qd 
        ON qd.qdoci_id = tqi.qdoc_id
    WHERE qd.jog_code IN ($placeholders)
      AND YEAR(qd.created_date) <> 0
    GROUP BY YEAR(qd.created_date)
    ORDER BY YEAR(qd.created_date)
";

    $stmt = $conn5->prepare($sqlItems);
    $stmt->bind_param($types, ...$codes);
    $stmt->execute();
    $itemResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $items_year = array_column($itemResult, 'year');
    $items = array_map('intval', array_column($itemResult, 'total_items'));
    $max_items = $items ? max($items) : 0;
    $maxItemsArr = array_fill(0, count($items), $max_items);

    /* ------------------------------------Final response----------------------------------- */
    echo json_encode([
        'status' => 200,
        'year' => $years,
        'spends' => $totalSpends,
        'max_value' => $maximum_val,
        'max_array' => $maxArray,
        'items_year' => $items_year,
        'items' => $items,
        'max_items' => $max_items,
        'maxItemsArr' => $maxItemsArr ,
       
    ]);
    exit;
 
endif;

<?php

include('check-session.php');

include('../../db.php');
include('./dashboard_sql.php');

if (isset($_POST['action_notification']) && !empty($_POST['action_notification'])) {
    $type = $_POST['type'] ?? NULL;
    $checked_ids = $_POST['checked_val'] ?? [];

    try { 

        if (empty($checked_ids) || empty($type)) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Invalid request data'
            ]);
            exit;
        } 

        if ($type === 'read') {

            $sql = "INSERT INTO notification_user_status 
                (noti_id, employee_id, project_key, is_read)
            VALUES 
                (? , ? , 'OLS', 1)
            ON DUPLICATE KEY UPDATE 
                is_read = 1
        ";
        } elseif ($type === 'delete') {

            $sql = "
            INSERT INTO notification_user_status 
                (noti_id, employee_id, project_key, is_deleted)
            VALUES 
                (?, ?, 'OLS', 1)
            ON DUPLICATE KEY UPDATE 
                is_deleted = 1,
                is_read = 1
        ";
        } else {
            throw new Exception('Invalid action type');
        }

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        foreach ($checked_ids as $val) {
            $noti_id = base64_decode($val);
            $employee_id = (int)$user_id;

            $stmt->bind_param("si", $noti_id, $employee_id);
            $stmt->execute();
        }

        $stmt->close();


        echo json_encode([
            'status'  => 'success',
            'message' => 'Notification updated successfully'
        ]);
        exit;
    } catch (Throwable $e) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Something went wrong',
            'error' => $e->getMessage() // enable only for debugging
        ]);
        exit;
    }
}



if (isset($_POST['is_notification']) && !empty($_POST['is_notification'])) {
    // Step 1  Get only those excode which exsists in sale rep

    // SECURE: Use prepared statements with placeholders
    if (empty($codes)) {
        echo json_encode(['status'=>false ,'html'=>'' ,'unread_count'=>0]);
        exit;
    }

    $codePlaceholders = implode(',', array_fill(0, count($codes), '?'));
    $sql = "SELECT jog_code FROM quotation_data WHERE jog_code IN ($codePlaceholders)";
    $stmt = $conn5->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($codes)), ...$codes);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $codes = array_column($result, 'jog_code');


    if(count($result)):

      // Get the notification data from locker room notification table

        $codes = array_values($codes); // ensure numeric indexes

        $placeholders = implode(',', array_fill(0, count($codes), '?'));

        $sql = "SELECT 
            CONCAT('INT_', omc.order_main_comment_id) AS notification_id,
            omc.order_main_comment_id AS source_id,
            'internal' AS source_type,
            omc.order_main_id AS order_id,
            omc.employee_id AS employee_id,
            omc.order_main_comment_detail AS comment,
            omc.order_main_comment_date AS date,
            om.order_main_name AS order_name,
            emp.employee_name AS employee_name
        FROM $dbName3.order_main_comment AS omc
        LEFT JOIN $dbName3.order_main AS om 
            ON om.order_main_id = omc.order_main_id
        LEFT JOIN $dbName3.employee AS emp 
            ON emp.employee_id = omc.employee_id
        WHERE om.order_main_code IN ($placeholders)
        ORDER BY omc.order_main_comment_date DESC
        ";

    
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }

        $types = str_repeat('s', count($codes));
        $stmt->bind_param($types, ...$codes);

        if (!$stmt->execute()) {
            die('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $internalNotifications = $result->fetch_all(MYSQLI_ASSOC);

       


      // Get external chat message  
     // =================Step 1 : Get all the order id exsits for user =====================
   
      $sql = "SELECT 
            CONCAT('EXT_', tca.chat_id) AS notification_id,
            tca.chat_id AS source_id,
            'external' AS source_type,
            tca.order_id AS order_id,
            tca.user_email AS employee_id,
            tca.msg AS comment,
            tca.add_time AS date,
            om.order_main_name AS order_name,
            tca.user_email AS employee_name
            FROM $dbName.tbl_chat_approvals AS tca
            LEFT JOIN $dbName3.order_main AS om 
            ON om.order_main_id = tca.order_id
            WHERE om.order_main_code IN ($placeholders) GROUP BY tca.chat_id";

     

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($codes)), ...$codes);
        $stmt->execute();

        $externalNotifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();


        $allNotifications = array_merge($internalNotifications, $externalNotifications);
      


        usort($allNotifications, function ($a, $b) {
        return strtotime($b['date']) <=> strtotime($a['date']);
        });
     
        if(!empty($allNotifications)):
        $notificationIds = array_column($allNotifications, 'notification_id');
        $placeholders = implode(',', array_fill(0, count($notificationIds), '?'));
        
       


        $sql = "SELECT noti_id, is_read, is_deleted
                FROM notification_user_status
                WHERE employee_id = ?
                AND noti_id IN ($placeholders)
        ";

        $stmt = $conn->prepare($sql);
        $params = array_merge([$user_id], $notificationIds);
        $stmt->bind_param('s' . str_repeat('s', count($notificationIds)), ...$params);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // map actions
        $actionMap = [];
        foreach ($rows as $row) {
            $actionMap[$row['noti_id']] = $row;
        }
        endif; 


        $finalNotifications = [];
        $unreadCount = 0;

      foreach ($allNotifications as $noti) {

        $action = $actionMap[$noti['notification_id']] ?? null;

        if (!empty($action['is_deleted'])) {
            continue;
        }

        $noti['is_read'] = $action['is_read'] ?? 0;
        if (!$noti['is_read']) {
            $unreadCount++;
        }

        $finalNotifications[] = $noti;
     }
      




    header('Content-Type: application/json');
    ob_start();

    $unreadCount = 0;

    foreach ($finalNotifications as $value) {
        $comment_name = substr($value['employee_name'], 0, 1);
        $is_read = $value['is_read'] ? '#ffffff' : '#f0f0f0';

        if (!$value['is_read']) {
            $unreadCount++;
        }
?>

        <label class="dropdown-item notification-item" style="background-color: <?= $is_read ?>;">
            <div>
                <input type="checkbox"
                    class="notifyCheck"
                    value="<?= base64_encode($value['notification_id']) ?>">
                <h6 class="commentedBy my-auto"><?= htmlspecialchars($comment_name) ?></h6>
            </div>

            <div class="commentContent">
                <p class="commentDesc">
                    Comment  - <?= nl2br(strip_tags($value['comment'])) ?>

                    by <?= htmlspecialchars($value['employee_name']) ?>
                    on ‘<?= htmlspecialchars($value['order_name']) ?>’
                </p>
                <div class="small text-muted">
                    <?= ($value['date'] === "0000:00:00 00:00:00")
                        ? ''
                        : date('d, M Y H:i', strtotime($value['date'])) ?>
                </div>
            </div>

            <a href="#"
                data-notification_id="<?= $value['notification_id'] ?>"
                class="deleteIcon"
                onclick="event.stopPropagation()">
                <figure class="my-auto">
                    <img src="assets/images/icons/deleteIcon.png" alt="">
                </figure>
            </a>
        </label>
<?php
    }

    $html = ob_get_clean();

    echo json_encode([
        'status' => true,
        'html' => $html,
        'unread_count' => $unreadCount
    ]);
    exit;
    else:
         echo json_encode(['status'=>false ,'html'=>'' ,'unread_count'=>0]) ;
         exit;
    endif ;
}

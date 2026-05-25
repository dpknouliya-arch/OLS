<?php
//include('up-session.php');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
    $user_id = 0 ;
    $customer_id = 0 ;

// Cross-domain SSO: 3dbauer passes s_obj token in URL because
// third-party iframe cookies are blocked by Chrome in production.
if (!empty($_GET['sso_token'])) {
    $decoded = json_decode(base64_decode($_GET['sso_token']), true);
    if (isset($decoded['user_id'], $decoded['user_email'])) {
        $_SESSION['JOGOLS'] = $_GET['sso_token'];
        $vp_param = !empty($_GET['vp']) ? '?vp=' . urlencode($_GET['vp']) : '';
        header('Location: ' . $vp_param);
        exit;
    }
}

if ((isset($_SESSION['JOGOLS']) && ($_SESSION['JOGOLS'] != "")) || (isset($_SESSION['JOGOLSSUB']) && ($_SESSION['JOGOLSSUB'] != "")) || (isset($_SESSION['JOGOLSSALE']) && ($_SESSION['JOGOLSSALE'] != ""))) {
    if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {
        $pageURL = 'https';
    } else {
        $pageURL = 'http';
    }
    $pageURL .= '://';
    if ($_SERVER['SERVER_PORT'] != '80') {
        $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']; //.''.$_SERVER['REQUEST_URI'];
    } else {
        $pageURL .= $_SERVER['SERVER_NAME']; //.''.$_SERVER['REQUEST_URI'];
    }
    if (isset($_SESSION['JOGOLS'])) {
        $obj_user = json_decode(base64_decode($_SESSION['JOGOLS']));
        $user_id = $obj_user->user_id ;
        // $customer_id = $obj_user->customer_id; 
    } elseif (isset($_SESSION['JOGOLSSALE'])) {
        $obj_user = json_decode(base64_decode($_SESSION['JOGOLSSALE']));
    } else {
        $obj_user_sub = json_decode(base64_decode($_SESSION['JOGOLSSUB']));
    }
    $vp = "";


 
    $savedOrderCount = 0 ; 
    $designApprovalCount = 0 ; 
    $TotalOrderCount = 0 ; 
    $FinalApprovalCount = 0 ; 
    $totalOrderHistory = 0; 
    $ManageUserCount = 0 ; 
    $totalArchivedOrder = 0 ; 



    // if( isset($_GET['vp']) ){ $vp=base64_decode($_GET['vp']); }else{
    // 	if(isset($_SESSION['JOGOLS'])){
    // 		echo '<meta http-equiv="refresh" content="0;URL=login.php">';
    // 	}else{
    // 		echo '<meta http-equiv="refresh" content="0;URL=login_sub.php">';
    // 	}
    // 	exit();
    // }  



    
    if($user_id || $customer_id){
         $status = 'archived' ;
         require_once 'db.php';
        

        //------- Saved order count -----------------
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT of_id) AS order_count FROM tbl_draft_of WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $data = $result->fetch_assoc();
        $savedOrderCount = $data['order_count'] ?? 0  ; 


        // -------------- Design approval count -------------------
        $stmt = $conn3->prepare("SELECT COUNT(DISTINCT order_id) AS order_count FROM order_head WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $data = $result->fetch_assoc();
        $designApprovalCount = $data['order_count'] ?? 0  ; 


        //------------------ total order count ------------------
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT of_id) AS order_count FROM tbl_order_form  WHERE user_id = ? AND enable = 1 AND order_status != ?" );
        $stmt->bind_param("is", $user_id ,$status);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $data = $result->fetch_assoc();
        $TotalOrderCount = $data['order_count'] ?? 0  ; 


       //-------------- Final approval count ----------------------    
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT final_approval_id) AS order_count FROM tbl_final_approvals  WHERE customer_id = ? ");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $data = $result->fetch_assoc();
        $FinalApprovalCount = $data['order_count'] ?? 0  ; 


        //-------------------User count --------------------
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT sub_user_id) AS userCount FROM tbl_sub_user  WHERE parent_user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $data = $result->fetch_assoc();
        $ManageUserCount = $data['userCount'] ?? 0  ;


        //---------------------- Archived order ----------------
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT of_id) AS orderCount FROM tbl_order_form  WHERE user_id = ? AND enable= 1  AND order_status =?" );
       
        $stmt->bind_param("is", $user_id ,$status);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $data = $result->fetch_assoc();
        $totalArchivedOrder = $data['orderCount'] ?? 0  ;


        // Order history count 

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
            if(!empty($codes)){
                  $placeholders = implode(',', array_fill(0, count($codes), '?'));
                   $types = str_repeat('s', count($codes));

                   $sql = "SELECT COUNT(DISTINCT conv_id) AS total_order
                    FROM quotation_data
                    WHERE jog_code IN ($placeholders) AND is_deleted = 0 
                    ";

                    $stmt = $conn5->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param($types, ...$codes);
                        $stmt->execute();
                        $row = $stmt->get_result()->fetch_assoc();
                        $totalOrderHistory = $row['total_order'] ?? 0;
                        $stmt->close();
                    }
            }
        
            $stmt = $conn->prepare("SELECT brand_id FROM tbl_user WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                $brand_id = $data['brand_id'] ?? 1;
                $stmt->close();
            }


            
        


    }

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Join Our Game</title>
        <!-- Bootstrap 5.3.x CSS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Css  -->
        <link rel="stylesheet" href="Style/main.css?var=10.5">
        <link rel="stylesheet" href="Style/default.css?var=2.3">
        <link rel="stylesheet" href="Style/sidebar.css?var=1.9">
        <link rel="stylesheet" href="Style/resposive.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- Css  -->
        <!-- faviconIcon -->
        <link rel="icon" type="image/x-icon" href="images/logo//feviconIcon.png">
        <!-- faviconIcon -->
        <style>
            .sidebar-heading {
                height: 75px;
                display: flex;
                border-bottom: 1px solid #ffffff47;
                align-items: center;
                justify-content: center;
            }

            .notificationBadge {
                position: absolute;
                right: -12px;
                top: -6px;
                font-size: 10px;
                padding: 0;
                background: #0B74F0 !important;
                color: #FFF;
                border-radius: 50%;
                min-width: 22px;
                min-height: 22px;
                display: flex;
                align-items: center;
                justify-content: center;

            }

            .commentDesc {
                white-space: normal;
                margin-bottom: 5px;
                font-size: 13px;
                font-weight: 500;
            }

            .notification-menu {
                min-width: 480px;
                max-width: 480px;
            }

            .notification-menu .dropdown-item {
                display: grid;
                gap: 5px;
                grid-template-columns: 40px auto 20px;
                border-top: 1px solid #dddddde0;
            }

            .notification-menu .dropdown-item:hover {
                background: #e3e3e330 !important;
            }

            .notification-menu .dropdown-item:hover .commentDesc {
                color: #2F50A3;
            }

            .notification-menu .commentedBy {
                font-size: 20px;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #F87575;
                border-radius: 50%;
                width: 2vw;
                height: 2vw;
                color: #FFF;
                margin: auto;
            }

            .sidebar-separator {
                border-bottom: 1px solid rgba(255, 255, 255, 0.3);
                padding: 0 0 15px 22px;
            }

            .sidebar-separator h4 {
                font-size: 14px;
                text-align: left;
                margin: auto;
                text-transform: uppercase;
            }

            .dropdown-header,
            .dropdown-Footer {
                background: #EBEEF6;
                padding: 8px 20px;
                font-size: 14px;
                color: #111111;
            }

            .dropdown-Footer button {
                border: none;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 13px;
                font-weight: 500;
                background: none;
            }

            .header .dropdown-menu .dropdown-item {
                position: relative;
            }
            .header{
                min-height: 70px;
            }
            .notificationDropdown input[type="checkbox"] {
                display: block;
                position: relative;
                left: -5px;
                top: 0;
                width: 12px;
                height: 12px;
            }
            
            .notification_dropdown_list{
                     height: 180px;
                    overflow-y: scroll;
            }
        </style>
    </head>

    <body>
        <div class="container-fluid p-0 h-100 dashboardMain">
            <div class="sidebar">
                <a href="?vp=YWRkT3JkZXI=" class="sidebar-heading ">
                    <?php
                    if ($brand_id ==1) {
                    ?>
                        <figure class="text-center  my-auto"><img src="images/logo/jogLOGO.png" alt="" class="brandLogo">                       
                    <?php               
                    } else {
                    ?>
                        <figure class="text-center  my-auto"><img src="images/logo/bauerLogoWhite.webp" alt="" class="brandLogo">
                    <?php
                    }
                    ?>
                    </figure>
                    <figure class="text-center my-auto "><img src="images/vector/feviconIcon.png" alt="" class="brandLogoCollapsed">
                    </figure>
                </a>
                <?php
                if (isset($_SESSION['JOGOLS'])) {
                    if ($brand_id ==1) {
                     
                ?>
                    <ul class="nav menu">
                        <li class="nav-item  <?php if ($_GET['vp'] == base64_encode('dashboradMain')) {
                                                    echo 'active';
                                                } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('dashboradMain'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/dashboardWhite.png" alt=""></figure>
                                <figure class="blueIcon"><img src="images/vector/dashboardBlue.png" alt=""></figure>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item  <?php if ($_GET['vp'] == base64_encode('billingInfo')) {
                                                    echo 'active';
                                                } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('billingInfo'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/billingInfo.png" alt=""></figure>
                                <figure class="blueIcon"><img src="images/vector/billingInfoBlue.png" alt=""></figure>
                                <span class="menu-title">Billing Info</span>
                            </a>
                        </li>
                        <li class="nav-item  <?php if ($_GET['vp'] == base64_encode('addOrder')) {
                                                    echo 'active';
                                                } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('addOrder'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/addApproWhite.png" alt=""></figure>
                                <figure class="blueIcon"><img src="images/vector/addAppro.png" alt=""></figure>
                                <span class="menu-title">Add Order
                                </span>
                            </a>
                        </li>  
                        <li class="nav-item  <?php if ($_GET['vp'] == base64_encode('manage_order')  ||  strpos(base64_decode($_GET['vp']), 'edit_order') !== false) {
                                                    echo 'active';
                                                } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('manage_order'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/savew.png" alt=""></figure>
                                <figure class="blueIcon"><img src="images/vector/saveb.png" alt=""></figure>
                                <span class="menu-title">Saved Orders</span>
                                 <span class="badge bg-success"><?= $savedOrderCount ?></span>
                            </a>
                        </li>
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('design_approval')) {
                                                echo 'active';
                                            } ?>  ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('design_approval'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/addOrder.png" alt=""></figure>
                                <figure class="blueIcon"><img src="images/vector/addOrderBlue.png" alt=""></figure>
                                <span class="menu-title">Design Approval</span>
                                 <span class="badge bg-success"><?= $designApprovalCount ?></span>

                                
                            </a>
                        </li>

                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('new_order')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('new_order'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/orderStatus.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/orderStatusBlue.png" alt=""></figure>
                                <span class="menu-title">Order Status</span>
                                 <span class="badge bg-success"><?= $TotalOrderCount ?></span>

                            </a>
                        </li>

                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('final_approvals')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('final_approvals'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/finalApprovals.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/finalApprovalsBlue.png" alt=""></figure>
                                <span class="menu-title">Final Approvals</span>
                                 <span class="badge bg-success"><?= $FinalApprovalCount ?></span>

                            </a>
                        </li>
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('orderDetail')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('orderDetail'); ?>">
                                <figure class="whiteIcon"><img src="assets/images/icons/orderDetailWhite.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="assets/images/icons/orderDetailBlue.png" alt=""></figure>
                                <span class="menu-title">Order History</span>
                                 <!-- <span class="badge bg-success"><?= $totalOrderHistory ?></span> -->

                            </a>
                        </li>
                        <?php
                        $obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
                        $user_id = $obj_user->user_id;
                        if ($obj_user->user_level == "admin") {
                        ?>
                            <li class="nav-item <?php if ($_GET['vp'] == base64_encode('manage_sales')) {
                                                    echo 'active';
                                                } ?> ">
                                <a class="nav-link" href="?vp=<?php echo base64_encode('manage_sales'); ?>">
                                    <figure class="whiteIcon"><img src="images/vector/manageORder.png" alt="">
                                    </figure>
                                    <figure class="blueIcon"><img src="images/vector/manageORderBlue.png" alt=""></figure>
                                    <span class="menu-title">Manage Sales</span>
                                 <span class="badge bg-success"><?= $ManageUserCount ?></span>

                                </a>
                            </li>
                        <?php
                        }
                        ?>

                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('manage_user')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('manage_user'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/manageORder.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/manageORderBlue.png" alt=""></figure>
                                <span class="menu-title">Manage Users</span>
                            </a>
                        </li>


                        <li class="nav-item <?php if($_GET['vp'] == base64_encode('archived')){echo 'active' ;}?>">
                                <a class="nav-link" href="?vp=<?php echo base64_encode('archived');?>">
                                <i class="menu-icon fa fa-archive"></i>
                                <span class="menu-title">Archived Orders</span>
                                <span class="badge bg-success"><?= $totalArchivedOrder ?></span>

                                </a>
                        </li>


                        <!-- <li class="nav-item ">
                            <a class="nav-link" href="tutorial.html">
                                <figure class="whiteIcon"><img src="images/vector/titorila.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/titorilaBlue.png" alt=""></figure>
                                <span class="menu-title">Tutorial</span>
                            </a>
                        </li> -->

                        <?php
                        $obj_login = json_decode(base64_decode($_SESSION['JOGOLS']));
                        if ($obj_login->user_level == "admin") {
                        ?>
                            <li class="nav-item <?php if ($_GET['vp'] == base64_encode('setting')) {
                                                    echo 'active';
                                                } ?> ">
                                <a class="nav-link" href="?vp=<?php echo base64_encode('setting'); ?>">
                                    <figure class="whiteIcon"><img src="images/vector/setting.png" alt=""></figure>
                                    <figure class="blueIcon"><img src="images/vector/settingBlue.png" alt=""></figure>
                                    <span class="menu-title">Settings</span>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                        <li id="menu_change_password" class="nav-item" style="display:none;">
                            <a class="nav-link" href="#" data-toggle="modal" data-target="#changePasswordModal">
                                <i class="menu-icon fa fa-cog"></i>
                                <span class="menu-title">Change Password</span>
                            </a>
                        </li>
                        <li id="menu_logout" class="nav-item" style="display:none;">
                            <a class="nav-link" href="logout.php">
                                <i class="menu-icon fa fa-sign-out"></i>
                                <span class="menu-title">Sign Out</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php if ($_GET['vp'] == base64_encode('logout')) {
                                                        echo 'active';
                                                    } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('logout'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/finalApprovals.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/finalApprovalsBlue.png" alt=""></figure>
                                <span class="menu-title">Log Out</span>
                            </a>
                        </li> -->
                    </ul>
                    <div class="sidebar-separator">
                        <h4>3D Customiser</h4>
                    </div>
                    <ul class="nav menu" style="padding-top: 0px !important; ">
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('save_draft')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('save_draft'); ?>">
                                <figure class="whiteIcon"><img src="images/icons/Jersey,Shorts.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/icons/Jersey,Shorts.png" alt=""></figure>
                                <span class="menu-title">3D Draft</span>
                            </a>
                        </li>
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('3d_orders')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('3d_orders'); ?>">
                                <figure class="whiteIcon"><img src="images/icons/Jersey,Shorts.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/icons/Jersey,Shorts.png" alt=""></figure>
                                <span class="menu-title">3D Orders</span>
                            </a>
                        </li>
                    </ul>
                    <?php } elseif ($brand_id == 2) { ?>
                        <div class="sidebar-separator pt-3">
                            <h4>3D Customiser</h4>
                        </div>
                        <ul class="nav menu" style="padding-top: 0px !important; ">
                            <li class="nav-item <?php if ($_GET['vp'] == base64_encode('save_draft')) {
                                                    echo 'active';
                                                } ?> ">
                                <a class="nav-link" href="?vp=<?php echo base64_encode('save_draft'); ?>">
                                    <figure class="whiteIcon"><img src="images/icons/Jersey,Shorts.png" alt="">
                                    </figure>
                                    <figure class="blueIcon"><img src="images/icons/Jersey,Shorts.png" alt=""></figure>
                                    <span class="menu-title">3D Draft</span>
                                </a>
                            </li>
                            <li class="nav-item <?php if ($_GET['vp'] == base64_encode('3d_orders')) {
                                                    echo 'active';
                                                } ?> ">
                                <a class="nav-link" href="?vp=<?php echo base64_encode('3d_orders'); ?>">
                                    <figure class="whiteIcon"><img src="images/icons/Jersey,Shorts.png" alt="">
                                    </figure>
                                    <figure class="blueIcon"><img src="images/icons/Jersey,Shorts.png" alt=""></figure>
                                    <span class="menu-title">3D Orders</span>
                                </a>
                            </li>
                        </ul>
                    <?php
                    }
                    ?>
                <?php }


                if (isset($_SESSION['JOGOLSSUB'])) { ?>

                    <ul class="nav menu">

                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('order_form_sub')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('order_form_sub'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/orderStatus.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/addOrderBlue.png" alt=""></figure>
                                <span class="menu-title">Order Form</span>
                            </a>
                        </li>

                        <!-- <li class="nav-item <?php if ($vp == 'order_form_sub') {
                                                        echo 'active';
                                                    } ?>">
                        <a class="nav-link" href="?vp=<?php echo base64_encode('order_form_sub'); ?>">
                            <i class="menu-icon fa fa-pencil-square-o"></i>
                            <span class="menu-title">Order Form</span>
                        </a>
                    </li> -->
                        <!-- <li class="nav-item <?php if ($vp == 'manual_sub') {
                                                        echo 'active';
                                                    } ?>">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('manual_sub'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/titorila.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/addOrderBlue.png" alt=""></figure>
                                <span class="menu-title">Tutorial</span>
                            </a>
                        </li> -->
                        <li id="menu_logout" class="nav-item" style="display:none;">
                            <a class="nav-link" href="logout_sub.php">
                                <i class="menu-icon fa fa-sign-out"></i>
                                <span class="menu-title">Sign Out</span>
                            </a>
                        </li>
                    </ul>

                <?php }

                if (isset($_SESSION['JOGOLSSALE']) && !isset($_SESSION['JOGOLS'])) { ?>
                    <ul class="nav menu">
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('sales_dash')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('sales_dash'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/dashboradWhite.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/dashboard.png" alt=""></figure>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('customers_user')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('customers_user'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/customerWhite.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/customerBlue.png" alt=""></figure>
                                <span class="menu-title">Customers</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item <?php if ($vp == 'order_form_sub') {
                                                        echo 'active';
                                                    } ?>">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('order_form_sub'); ?>">
                                <i class="menu-icon fa fa-pencil-square-o"></i>
                                <span class="menu-title">Order Form</span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item <?php if ($vp == 'manual_sub') {
                                                        echo 'active';
                                                    } ?>">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('manual_sub'); ?>">
                                <i class="menu-icon fa fa-book"></i>
                                <span class="menu-title">Tutorial</span>
                            </a>
                        </li>
                        <li class="nav-item <?php if ($_GET['vp'] == base64_encode('logout_sales')) {
                                                echo 'active';
                                            } ?> ">
                            <a class="nav-link" href="?vp=<?php echo base64_encode('logout_sales'); ?>">
                                <figure class="whiteIcon"><img src="images/vector/finalApprovals.png" alt="">
                                </figure>
                                <figure class="blueIcon"><img src="images/vector/finalApprovalsBlue.png" alt=""></figure>
                                <span class="menu-title">Log Out</span>
                            </a>
                        </li> -->
                    </ul>

                <?php } ?>

            </div>

            <div class="content main-content-header">

                <div class="row header">

                    <div class="  col-md-12  ">

                        <div class="links d-flex align-items-center h-100 justify-content-between">

                            <div class="d-flex">

                                <?php if (isset($_SESSION['JOGOLS'])) { ?>

                                    <div class="header-links">

                                        <button class="toggle-btn" onclick="toggleSidebar()">⮜</button>

                                        <a href="?vp=<?php echo base64_encode('manage_order'); ?>" class="sm-size">Manage <span>Order</span></a>

                                    </div>

                                <?php } ?>

                                <?php

                                if (isset($_SESSION['JOGOLSSALE']) && !isset($_SESSION['JOGOLS'])) { ?>

                                    <div class="header-links">

                                        <button class="toggle-btn" onclick="toggleSidebar()">⮜ </button>

                                    </div>

                                <?php } ?>

                                <?php

                                if (isset($_SESSION['JOGOLSSUB'])) { ?>

                                    <div class="header-links">

                                        <button class="toggle-btn" onclick="toggleSidebar()">⮜ </button>

                                    </div>

                                <?php } ?>

                            </div>

                            <div>

                                <?php

                                if (isset($_SESSION['JOGOLSSALE']) && isset($_SESSION['JOGOLS'])) { ?>
                                    <div class="header-links  btn btn-dark    p-0 ">
                                        <a href="?vp=<?php echo base64_encode('logout_user_sales'); ?>" class="sm-size text-white iconBTn py-1">
                                            <figure class="m-0"><img src="images/vector/back.png" alt=""></figure> Back To Manage </span>
                                            <div class="circleBox">
                                            </div>
                                        </a>
                                    </div>
                                <?php } ?>

                            </div>

                            <div class="rightHederSide d-flex gap-2 align-items-center">
                                       <?php if(isset($_SESSION['JOGOLS']) ) {?>

                                            <div class="dropdown notificationDropdown">

                                                <!-- Bell Icon -->
                                                <button class="btn position-relative p-0  dropdown_notification_btn" type="button"
                                                    id="notificationDropdown"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false" style="border: none;">
                                                    <figure class="my-auto"><img src="assets/images/icons/notificationIcon.png" alt=""></figure>

                                                    <!-- Notification Count -->
                                                    <span class="notificationBadge">
                                                        
                                                    </span>
                                                </button>

                                                <!-- Dropdown Panel -->
                                        

                                                <ul class="dropdown-menu dropdown-menu-end notification-menu p-0"
                                                    aria-labelledby="notificationDropdown">

                                                    <div class="dropdown-header">Notifications</div>
                                                        <div class="notification_dropdown_list">
                                                            
                                                        </div>

                                                

                                                    <div class="dropdown-Footer d-flex gap-2 align-items-center">
                                                        <button class="notification_action btn "  type="button" data-type="read">
                                                            <figure class="my-auto"><img src="assets/images/icons/doubleCheck.png" alt=""></figure> Mark all as read
                                                        </button>
                                                        <button class="notification_action btn"  type="button"   data-type="delete">
                                                            <figure class="my-auto"><img src="assets/images/icons/doubleCheck.png" alt=""></figure> Delete all
                                                        </button>
                                                    </div>
                                                </ul>

                                            </div>
                                <?php   }   ?>
                                

                                <div class="rightAdminProfile  dropdown  ">

                                    <?php if (isset($_SESSION['JOGOLS'])) { ?>

                                        <button class="btn dropdown-toggle d-flex align-items-center gap-0" type="button"

                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">

                                            <p class="m-0">HELLO, <?php echo $obj_user->full_name; ?></p>

                                            <figure class="m-0"><img src="images/vector/adminProfile.jpeg" alt=""></figure>

                                        </button>

                                    <?php } else { ?>

                                        <?php if (isset($_SESSION['JOGOLSSALE'])) { ?>

                                            <button class="btn dropdown-toggle d-flex align-items-center gap-0" type="button"

                                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">

                                                <p class="m-0">HELLO, <?php print_r($obj_user->s_user_name); ?></p>

                                                <figure class="m-0"><img src="images/vector/adminProfile.jpeg" alt=""></figure>

                                            </button>

                                    <?php }
                                    } ?>



                                    <?php if (isset($_SESSION['JOGOLSSUB'])) { ?>

                                        <button class="btn dropdown-toggle d-flex align-items-center gap-0" type="button"

                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">

                                            <p class="m-0">HELLO, <?php print_r($obj_user_sub->s_user_name); ?></p>

                                            <figure class="m-0"><img src="images/vector/adminProfile.jpeg" alt=""></figure>

                                        </button>

                                    <?php } ?>
                                    <?php if (isset($_SESSION['JOGOLSSALE']) && isset($_SESSION['JOGOLS'])) { ?>
                                    <?php } else { ?>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
                                            <li>
                                                <?php if (isset($_SESSION['JOGOLS'])) { ?>
                                                    <a class="dropdown-item" href="?vp=<?php echo base64_encode('logout'); ?>">Logout</a>
                                                <?php } ?>
                                                <?php if (isset($_SESSION['JOGOLSSUB'])) { ?>
                                                    <a class="dropdown-item" href="?vp=<?php echo base64_encode('logout_user'); ?>">Logout</a>
                                                <?php } ?>
                                                <?php if (isset($_SESSION['JOGOLSSALE']) && !isset($_SESSION['JOGOLS'])) { ?>
                                                    <a class="dropdown-item" href="?vp=<?php echo base64_encode('logout_sales'); ?>">Logout</a>
                                                <?php } ?>
                                            </li>
                                        </ul>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>



                <div class="defaultHeader">

                    <?php

                    $vp = "";



                    if (isset($_GET['vp'])) {

                        $chk_public = 1;

                        $vp = base64_decode($_GET['vp']);

                        require_once($vp . '.php');
                    } else {

                        ///include('addOrder.php');

                    }

                    ?>

                </div>

                <div class="innerMainContent row">



                </div>



                <div class="main-content-footer">

                    <a href="">Copyright © 2020 JOGSPORTS. All rights reserved. </a>

                </div>



            </div>

        </div>
        <!--==================
        addNewTeam Member Modal 
        ==================-->

        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Change Password</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" action="" method="post" name="upload_excel" id="change_password">
                            <div class="boxes border-none">
                                <fieldset class="grid2 singleFrom">
                                    <div class="form-group  column2">
                                        <label for="" class="mb-2">Password </label>
                                        <input type="password" name="Previous_password" value="" placeholder="Previous password" required>
                                    </div>
                                    <div class="form-group column2 ">
                                        <label for="" class="mb-2">Confirm Password </label>
                                        <input type="password" name="New_password" value="" placeholder="New password" required>
                                    </div>


                                </fieldset>
                            </div>

                            <input type="submit" type="button" class="themeBtn text-center" value="Save and Continue" />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="   text-end  formBottom w-100">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--==================
        addNewTeam Member Modal 
        ==================-->

     

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        </script>
        <!-- ionIcons -->
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
       
        <script src="js/main.js"></script>
        <script src="js/Notification.js?var=15"></script>

        <!-- ionIcons -->

        <script>
            function toggleSidebar() {
                const sidebar = document.querySelector('.sidebar');
                const toggleBtn = document.querySelector('.toggle-btn');
                sidebar.classList.toggle('collapsed');
                // Update the toggle button arrow direction
                if (sidebar.classList.contains('collapsed')) {
                    toggleBtn.innerHTML = '⮞'; // Point right when closed
                } else {
                    toggleBtn.innerHTML = '⮜'; // Point left when open
                }
            }

            // change password 

            $(document).on('submit', '#change_password', function(event) {
                event.preventDefault();
                var form = $(this);
                let data = $(this).serialize();
                $.ajax({
                    url: 'ajax/main/update_password.php', // the server file that handles the request
                    type: 'POST', // or 'GET'
                    data: data,

                    success: function(response) {
                        let resp = JSON.parse(response);
                        console.log("response", resp);
                        alert(resp.msg);
                        if (resp.status != 200) {

                            return false;
                        }
                        $('#changePasswordModal').modal('hide');
                        form[0].reset();
                    },
                    error: function(xhr, status, error) {
                        // Runs when there’s an error
                        console.error('AJAX Error:', error);
                    }
                });



            });
        </script>

        <script>
            document.querySelectorAll('.notification-menu').forEach(menu => {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation(); // 🔑 keeps dropdown open
                });
            });
        </script>

    </body>

    </html>

<?php
} else {
    echo '<meta http-equiv="refresh" content="0;URL=login.php">';
}

?>
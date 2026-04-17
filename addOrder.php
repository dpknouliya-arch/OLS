<?php

include('check-session.php');

include('db.php');



?>

<?php

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;
$user_email = $obj_user->user_email;

// Use prepared statement to prevent SQL injection
$stmt_select = $conn->prepare("SELECT * FROM tbl_address WHERE user_id=? AND enable=1 AND (is_billing_addr=1 OR is_deliver_addr=1) AND contact_name<>'' AND address<>'' AND city<>'' AND contact_name<>'' AND country<>'' AND zip_code<>'' AND tel<>'' AND email<>'' ORDER BY is_billing_addr ASC");
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$rs_select = $stmt_select->get_result();

$num_row = $rs_select->num_rows;



$a_data = array();



if ($num_row == 1) {

    $a_data[0] = $rs_select->fetch_assoc();

    $a_data[1] = $a_data[0];
} else {

    while ($row_select = $rs_select->fetch_assoc()) {

        if ($row_select["is_billing_addr"] == "1") {

            $a_data[0] = $row_select;
        } else if ($row_select["is_deliver_addr"] == "1") {

            $a_data[1] = $row_select;
        }
    }
}



if ($num_row == 0) {



    echo '<h4 align="center" style="color:white;">Please fill all the address info in "Billing Info" menu.</h4>';

    exit();
}

?>
<style>
    #team .tab-pane {
        margin-top: 0 !important;
        padding: 0 !important;
    }

    #team .table-striped {
        margin-bottom: 0;
    }

    #form1 .required {
        border: none !important;
        color: red;
        height: 0px;
        width: 0px;
    }

    .form-horizontal .required{
         border: none !important;
        color: red;
        height: 0px;
        width: 0px; 
    }


     .addOrderPage table td select {
            min-width: 6vw !important;
            font-size: 13px !important;
            text-align: center;
        }
        td,
        th {
            text-align: center !important;
        }
</style>

<div class="addOrderPage">

    <div class="  pageHeader">

        <h2>Add Order</h2>

        <p>Create your order.</p>

    </div>

    <div class="innerMainContent ">

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="billing-tab" data-bs-toggle="tab" href="#billing" role="tab" aria-controls="billing" aria-selected="true">
                    <ion-icon name="checkmark-circle-outline" class="iconImg"></ion-icon>Billing & Delivery
                </a>

            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link   billing_info_tab_continue" id="order-tab" data-bs-toggle="tab" href="#order" role="tab"
                    aria-controls="order" aria-selected="false">
                    <ion-icon name="checkmark-circle-outline" class="iconImg"></ion-icon>
                    Order Information
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link  continuteToteamRoster" id="team-tab" data-bs-toggle="tab" href="#team" role="tab"
                    aria-controls="team" aria-selected="false">
                    <ion-icon name="checkmark-circle-outline" class="iconImg"></ion-icon>
                    Team & Roster Details
                </a>
            </li>
        </ul>

        <form name="form1" id="form1" method="post" enctype="multipart/form-data">

            <div class="tab-content" id="myTabContent">

                <!------------ billing 1 step ------------>

                <div class="tab-pane fade show active" id="billing" role="tabpanel" aria-labelledby="billing-tab">

                    <div class="billingAndDelivery">

                        <div class="grid2">
                            <div class="boxes">
                                <div class="formTitle d-flex align-items-center flex-row justify-content-between">
                                    <h6 class="subHeading m-0">Billing Information </h6>
                                    <a href="?vp=<?= base64_encode("billingInfo") ?>" class="sm-Btn">Edit</a>
                                </div>
                                <fieldset class="grid2 singleFrom">
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Company/Organization/School</label>
                                        <input type="text" name="company_name" id="bi_company_name" maxlength="150" value="<?php echo $a_data[0]["addr_name"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Contact <span class="required">*</span> </label>
                                        <input type="text" name="contact" id="bi_contact" maxlength="200" value="<?php echo $a_data[0]["contact_name"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Country <span class="required">*</span> </label>
                                        <input type="text" name="country" id="bi_country" maxlength="50" value="<?php echo $a_data[0]["country"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="w-100 text-start  c-label">City <span class="required">*</span> </label>
                                        <input type="text" name="city" id="bi_city" maxlength="80" value="<?php echo $a_data[0]["city"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="w-100 text-start  c-label">Zipcode <span class="required">*</span></label>
                                        <input type="text" name="zip_code" id="bi_zip_code" maxlength="20" value="<?php echo $a_data[0]["zip_code"]; ?>">
                                    </div>
                                    <div class="form-group  ">
                                        <label for="" class="w-100 text-start  c-label">Email <span class="required">*</span> </label>
                                        <input type="text" name="email" id="bi_email" maxlength="200" value="<?php echo $a_data[0]["email"]; ?>">
                                    </div>
                                    <div class="form-group  ">
                                        <label for="" class="w-100 text-start  c-label">Tel. <span class="required">*</span> </label>
                                        <input type="text" name="tel" id="bi_tel" maxlength="30" value="<?php echo $a_data[0]["tel"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">TAX ID <span class="required">*</span> </label>
                                        <input type="text" name="tax_id" id="bi_tax_id" maxlength="30" value="<?php echo $a_data[0]["tax_id"]; ?>">
                                        <input type="hidden" name="bill_addr_id" id="bi_addr_id" value="<?php echo $a_data[0]["addr_id"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Address <span class="required">*</span> </label>
                                        <input type="text" name="address_info" id="bi_address" value="<?php echo $a_data[0]["address"]; ?>">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="boxes">
                                <div class="formTitle d-flex align-items-center flex-row justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <h6 class="subHeading m-0">Delivery Information </h6>
                                        <a href="?vp=<?= base64_encode("billingInfo") ?>">Edit</a>
                                    </div>
                                    <div class="checkbox">
                                        <div>
                                            <input type="checkbox" id="check" name="check" checked value="" />
                                            <label for="check" class="XSmall">
                                                Same
                                                as Billing Info
                                                <span><!-- This span is needed to create the "checkbox" element --></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <fieldset class="grid2 singleFrom">
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Company/Organization/School</label>
                                        <input type="text" name="d_company_name" id="de_company_name" maxlength="150" value="<?php echo $a_data[1]["addr_name"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Contact <span class="required">*</span> </label>
                                        <input type="text" name="d_contact" id="de_contact" maxlength="200" value="<?php echo $a_data[1]["contact_name"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Country <span class="required">*</span> </label>
                                        <input type="text" name="d_country" id="de_country" maxlength="50" value="<?php echo $a_data[1]["country"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="w-100 text-start  c-label">City <span class="required">*</span> </label>
                                        <input type="text" name="d_city" id="de_city" maxlength="80" value="<?php echo $a_data[1]["city"]; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="w-100 text-start  c-label">Zipcode <span class="required">*</span> </label>
                                        <input type="text" name="d_zip_code" id="de_zip_code" maxlength="20" value="<?php echo $a_data[1]["zip_code"]; ?>">
                                    </div>
                                    <div class="form-group  ">
                                        <label for="" class="w-100 text-start  c-label">Email <span class="required">*</span> </label>
                                        <input type="text" name="d_email" id="de_email" maxlength="200" value="<?php echo $a_data[1]["email"]; ?>">
                                    </div>
                                    <div class="form-group  ">
                                        <label for="" class="w-100 text-start  c-label">Tel. <span class="required">*</span> </label>
                                        <input type="text" name="d_tel" id="de_tel" maxlength="30" value="<?php echo $a_data[1]["tel"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">TAX ID </label>
                                        <input type="text" name="d_tax_id" id="de_tax_id" maxlength="30" value="<?php echo $a_data[1]["tax_id"]; ?>">
                                        <input type="hidden" name="deli_addr_id" id="de_addr_id" value="<?php echo $a_data[1]["addr_id"]; ?>">
                                    </div>
                                    <div class="form-group column2">
                                        <label for="" class="w-100 text-start  c-label">Address <span class="required">*</span> </label>
                                        <input type="text" name="d_address_info" id="de_address" value="<?php echo $a_data[1]["address"]; ?>">
                                    </div>
                                </fieldset>
                            </div>

                            <div class="submitBUtton column2">
                                <!-- <a class="themeBtn switch-tab iconBTn  billing_info_tab_continue" data-target="#order"  href="#order" role="tab"
                                    aria-controls="order" aria-selected="false">
                                    Save and Continue <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>
                                </a> -->

                                <a class="themeBtn switch-tab iconBTn  billing_info_tab_continue" data-target="#order" href="javascript:void(0);">
                                    Save and Continue
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!------------ order 2 step ------------>

                <div class="tab-pane fade" id="order">
                    <div class="boxes">
                        <div class="formTitle d-flex align-items-center flex-row">
                            <div class="orderBadge">
                                Order Date : <?php echo date("m/d/Y"); ?><input type="hidden" name="order_date" id="order_date" value="<?php echo date("Y-m-d"); ?>">
                            </div>
                        </div>
                        <fieldset class="grid2 singleFrom">
                            <div class="form-group  ">
                                <label for="" class="w-100 text-start  c-label">Order Name</label>
                                <input type="text" name="project_name" id="project_name" value="" placeholder="Order Name">
                            </div>
                            <div class="form-group  ">
                                <label for="" class="w-100 text-start  c-label">Customer PO</label>
                                <input type="text" name="customer_po" id="customer_po" value="" placeholder="Customer PO">
                            </div>
                            <div class="form-group">
                                <label for="" class="w-100 text-start  c-label">Game / Event date <span class="required">*</span> </label>
                                <input type="date" name="game_event_date" id="game_event_date" value="" placeholder="Game / Event date">
                            </div>
                            <div class="form-group">
                                <label for="" class="w-100 text-start  c-label">Request due date <span class="required">*</span> </label>
                                <input type="date" name="req_due_date" id="req_due_date" value="" placeholder="Request due date ">
                            </div>
                            <div class="styled-select">
                                <label for="" class="w-100 text-start  c-label">Payment options</label>
                                <select id="payOption" name="payment_opt">
                                    <option value="Wire transfer">Wire transfer</option>
                                    <option value="ACH transfer">ACH transfer</option>
                                    <option value="Credit card">Credit card (Processing fee 3%)</option>
                                    <option value="Cheque">Check</option>
                                </select>
                            </div>
                            <div class="styled-select">
                                <label for="" class="w-100 text-start  c-label">Sales Rep</label>
                                <select style="width: 100%; font-size: 14px;" name="sales_rep" id="sales_rep" required="">
                                    <?php
                                    $sql_new = "SELECT * FROM employee WHERE employee_position_id='5'";
                                    $emps = $conn3->query($sql_new);
                                    $num_rows = $emps->num_rows;
                                    if ($num_rows > 0) {
                                        while ($row_selection = $emps->fetch_assoc()) {
                                    ?>
                                            <option value="<?= $row_selection['employee_id'] ?>"><?= $row_selection['employee_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="styled-select">
                                <label for="" class="w-100 text-start  c-label">Reorder? They the previous JOG order EX# here</label>
                                <select id="reorder_num" name="reorder_num" onchange="getReorder()">
                                    <option value="">Reorder? Type the EX# here</option>
                                    <?php
                                    //$sql_new = "SELECT * FROM tbl_order_form WHERE user_id='$user_id'";
                                    $sql_new = "SELECT tbl_order_form.*,COUNT(DISTINCT tbl_order_form.prod_id) AS prod_num,COUNT(tbl_order_item.oi_id) AS item_num,SUM(tbl_order_item.qty_top1+tbl_order_item.qty_top2+tbl_order_item.qty_bottom1+tbl_order_item.qty_bottom2) AS qty_sum,tbl_user.full_name,tbl_user.customer_id FROM tbl_order_form LEFT JOIN tbl_product ON tbl_order_form.prod_id=tbl_product.prod_id LEFT JOIN tbl_order_item ON tbl_order_form.of_id=tbl_order_item.of_id LEFT JOIN tbl_user ON tbl_order_form.user_id=tbl_user.user_id WHERE tbl_order_form.user_id='$user_id' AND tbl_order_form.enable=1 AND tbl_order_form.order_status<>'finished' AND tbl_order_form.lkr_order_main_id IS NOT NULL AND tbl_order_form.order_status NOT IN ('shipped','received','archived') GROUP BY tbl_order_form.draft_id ORDER BY tbl_order_form.order_date DESC;";
                                    $emps = $conn->query($sql_new);
                                    $num_rows = $emps->num_rows;
                                    if ($num_rows > 0) {
                                        while ($row_selection = $emps->fetch_assoc()) {
                                            if (!empty($row_selection['code_match'])) {
                                    ?>
                                                <option value="<?= $row_selection['code_match'] ?>"><?= $row_selection['code_match'] ?></option>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <a class="nav-link d-flex" id="PreviewPdfMdoal " target="_blank" style="cursor: pointer;">
                                    <ion-icon name="eye-outline"></ion-icon>
                                </a>
                            </div>

                        </fieldset>
                    </div>
                    <div class="d-flex justify-content-between my-4">
                        <div class="goBackBtn themeBtn2grey ">
                            <a href="#" class="goback switch-tab iconBTn" data-target="#billing">
                                <figure class="m-0"><img src="images/vector/previousBtn.png" alt=""></figure> Go Back
                            </a>
                        </div>
                        <div class="submitBUtton">
                            <a href="#" class="themeBtn switch-tab iconBTn continuteToteamRoster" data-target="#team">Save and Continue <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure></a>
                        </div>
                    </div>
                </div>

                <!------------ team 3 step ------------>

                <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
                    <div class="row">
                        <div class="col-md-8 leftSide">
                            <div class="boxes bg-none border-none">
                                <div class="formTitle d-flex align-items-center flex-row">
                                    <h6>Create Order Form</h6>
                                </div>
                                <fieldset class="grid2 singleFrom">
                                    <div class="form-group">
                                        <label for="" class="w-100 text-start  c-label">Team Name <span class="required">*</span> </label>
                                        <input type="text" name="input_on_team_name" id="input_on_team_name" value="" placeholder="Team Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="w-100 text-start  c-label">Year <span class="required">*</span> </label>
                                        <!-- <input type="text" name="input_on_year" id="input_on_year" value="" placeholder="Year"> -->

                                         <select name="input_on_year" id="input_on_year" class="form-select">
                                    <option value="">-- Select Year --</option>
                                    <?php
                                    $currentYear = date("Y");
                                    for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                        echo '<option value="' . $year . '">' . $year . '</option>';
                                    }
                                    ?>
                                </select>


                                    </div>
                                    <div class="styled-select column2 form-group">
                                        <label for="" class="w-100 text-start  c-label">Order Form <span class="required">*</span> </label>
                                        <select id="prod_id" name="prod_id" class="form-select">
                                            <option value="">--Select Order Form --</option>
                                            <?php
                                            $sql_product = "SELECT * FROM tbl_product ORDER BY prod_id ASC";
                                            $rs_product = $conn->query($sql_product);
                                            while ($row_product = $rs_product->fetch_assoc()) {
                                                echo "<option value=\"" . $row_product["prod_id"] . '">' . $row_product["prod_name"] ."</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" id="form_id_inc" name="form_id_inc" value="1">
                                        <input type="hidden" id="tmp_num_form" value="0">
                                    </div>

                                 
                                    <div class="d-flex justify-content-between column2 formBottom">
                                        <div class="goBackBtn themeBtn2grey">
                                            <a href="#" class="goback switch-tab iconBTn" data-target="#order">
                                                <figure class="m-0"><img src="images/vector/previousBtn.png" alt=""></figure> Go
                                                Back
                                            </a>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <span class="themeBtn iconBTn teamAndRosterDetails" id="showTeamTabsSection">Save and Continue <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure></span>
                                           
                                                <!-- <button class="btn themeBtn2 d-flex gap-3">
                                                <figure class="m-0"><img src="images/vector/upload.png"alt=""></figure>                                                 
                                                Upload Order Form
                                            </button>
                                            <input type="file" name="myFile" /> !-->
                                           


                                            <!-- <span class="btn themeBtn2 iconBTn" onclick="return chooseUploadProcess(this);">
                                                <figure class="m-0">
                                                    <img src="images/vector/upload.png" alt=""> &nbsp; Upload Order Form
                                            </span> -->

                                            
                                                        <span class="btn themeBtn2 iconBTn">
                                                              <input type="file"  class="form-control " accept=".xlsx" name="order_form_file" id="order_form_file">
                                                        </span>

                                                          <button class="orderFormUpload btn btn-sm" type="button">
                                                            <figure class="m-0">
                                                            <img src="images/vector/upload.png" alt="">
                                                            </figure>
                                                            &nbsp; Upload Order Form
                                                            </button>
                                                  

                                        </div>
                                    </div>


                                
                            </div>
                        </div>
                        <div class="col-md-4 rightSide d-flex">
                            <div class="card bg-none border-none d-flex justify-content-evenly text-start">
                                <div class="slideIcon" id="toggleButton">
                                    <figure class="m-0"><img src="images/vector/rightArrow.png" alt=""></figure>
                                </div>
                                <h5 class="subHeading">Another way to create order form</h5>
                                <ol>
                                    <li class="XSmall"> You can download a Blank Order Form below.</li>
                                    <li class="XSmall"> Fill it accordingly and upload it using Upload
                                        Order Form
                                        button.</li>
                                </ol>
                                <p class="XSmall grey">You can download a Blank Order Form here</p>
                                <a href="src/download/OLS.xls" download="Blank-Order-Form.xls" class="themeBtn2 d-flex gap-3 iconBTn">
                                    <figure class="m-0">
                                        <img src="images/vector/OLSNew.png" alt="">
                                    </figure> Download <figure class="m-0">
                                        <img src="images/vector/excel.png" alt="">
                                    </figure>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="teamTabsSection">
                        <div class="mt-4">
                            <ul class="nav nav-tabs whiteBg" id="teamTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex  open_add_new_modal" id="addNewTeam ">
                                        <figure class="m-0"><img src="images/vector/add.png" alt="" style="width: 16px;">
                                        </figure>
                                        Add New
                                    </a>
                                </li>
                            </ul>
                            <div class="bg-white tableLower">
                                <div class="RosterDetailsGuide">
                                    <div class="innerBox">
                                        <h6 id="toggleGuide" class="d-flex align-items justify-content-between ">
                                            Roster
                                            Details Guide <figure class="m-0"><img src="images/vector/arrowDown.png" alt=""></figure>
                                        </h6>
                                        <ol id="detailsList">
                                            <li>Names will be in all CAPS (ex: ROGERS or MacDONALD). If you
                                                want lower case, please submit in lower case.</li>
                                            <li>Please submit your order with player sizes smallest to largest followed by goalie sizes smallest to largest.
                                            </li>
                                            <li>Before you complete your order, please ensure to review each
                                                item for accuracy. Once your order is processed, it becomes
                                                difficult to change or revise and may cause a delay to your
                                                order.</li>
                                            <li>Please note that any errors in garment personalization (name or number printed/embroidered on apparel), sizing or quantities will not be refunded. Please verify all the order details before submitting.</li>
                                            <li>If you have any special instructions or comments for a player order, please include them in the Notes column. You can also include any requests or comments in the Special Comments section at the bottom of the order form.</li>
                                        </ol>
                                    </div>
                                </div>
                                <div id="table_showing" class="table-responsive">
                                </div>
                                <div class="tab-content" id="teamTabContent">
                                    <div class=" fade show active row" id="team1" role="tabpanel" aria-labelledby="team1-tab">
                                        <div class="appendTable">

                                        </div>


                                        <div class="col-md-3 col-sm-12 d-none">
                                            <a href="#" class="themeBtn2grey d-flex align-items-center gap-3" id="showTeamTabsSection_new">
                                                <figure class="m-0"><img src="images/vector/add.png" alt="">
                                                </figure>
                                                Add Order to This Team
                                            </a>

                                        </div>



                                        <div class="col-md-9 col-sm-12" style="text-align:right;">
                                            <button id="btn_save_and_go" type="button" class="btn btn-success" onclick="return checkBeforeSaveDraft();">Save for Later</button>
                                            <button type="button" class="btn themeBtn iconBTn " onclick="return submitOrder();" id="btn_submit_order">Submit Order <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure></button>
                                            <input type="hidden" name="is_submit_order" id="is_submit_order" value="no">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--==================

addNewTeam Member Modal 

==================-->


<div class="modal fade bd-example-modal-sm" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <h4>Women’s cuts available for full team orders/reorders. </h4>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="addNewTeam" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h1 class="modal-title fs-5" id="exampleModalLabel">Create Order Form</h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <form class="form-horizontal" action=" " method="post" name="upload_excel" enctype="multipart/form-data" id=" ">

                    <div class="boxes border-none">

                        <fieldset class="grid2 singleFrom">

                            <div class="form-group  ">

                                <label for="">Team Name  <span class="required">*</span></label>

                                <input type="text" name="input_on_team_name" id="input_on_team_name_new"  placeholder="Team Name">

                            </div>

                            <div class="form-group  ">

                                <label for="">Year  <span class="required">*</span> </label>

                                <!-- <input type="text"  name="input_on_year" id="input_on_year_new" value="" placeholder="Year">  -->


                                     <select name="input_on_year" id="input_on_year_new" class="form-select">
                                       <option value="">-- Select Year --</option>
                                            <?php
                                            $currentYear = date("Y");
                                            for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                                echo '<option value="' . $year . '">' . $year . '</option>';
                                            }
                                    ?>

                                     </select>
                               

                            </div>



                            <div class="styled-select column2 form-group">

                                <label for="" class="w-100 text-start  c-label">Order Form <span class="required">*</span></label>

                                <select id="prod_id_new">
                                    <option value="">--Select Order Form --</option>

                                    <?php

                                    $sql_product = "SELECT * FROM tbl_product ORDER BY prod_id ASC";

                                    $rs_product = $conn->query($sql_product);

                                    while ($row_product = $rs_product->fetch_assoc()) {

                                        echo "<option value=\"" . $row_product["prod_id"] . '">' . $row_product["prod_name"] . "</option>";
                                    }

                                    ?>

                                </select>

                                <input type="hidden" id="form_id_inc_modal" name="form_id_inc" value="1">

                                <input type="hidden" id="tmp_num_form" value="0">

                            </div>

                        </fieldset>

                    </div>

                </form>

            </div>

            <div class="modal-footer">

                <div class="grid2 column2 formBottom w-100">

                    <span class="themeBtn text-center" id="AddNewTeamModal">Save and Continue</span>

                    <div class="upload-btn-wrapper">

                        <button class="btn themeBtn2 d-flex gap-3" type="button" onclick="return chooseUploadProcess(this);">

                            <figure class="m-0"><img src="images/vector/upload.png" alt=""></figure> Upload Order

                            Form

                        </button>

                        <!-- <input type="file" name="myFile"> -->

                    </div>

                </div>

            </div>

        </div>



    </div>

</div>

<!--==================

addNewTeam Member Modal 

==================-->


<!--==================

addNewTeam Member Modal 

==================-->

<!-- <div class="modal fade" id="PreviewPdfMdoal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exCode"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>

</div> -->

<!--==================

addNewTeam Member Modal 

==================-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function getReorder() {
        const selectedValue = document.getElementById('reorder_num').value;
        const previewLink = document.getElementById('PreviewPdfMdoal ');
        if (selectedValue) {

            $.ajax({
                type: "POST",
                dataType: "html",
                url: "getOrder_code.php",
                data: {
                    "order_main_code": selectedValue
                },
                success: function(resp) {
                    previewLink.href = `https://locker.jog-joinourgame.com/view/?id=${encodeURIComponent(resp)}`;
                }
            });

            // Replace with your actual PDF preview URL pattern

        } else {
            previewLink.href = "";
        }
    }
</script>
<script>
    function PreviewPdfMdoal() {
        var reorder_num = $('#reorder_num').val();
        $('#exCode').html(reorder_num);
    }
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.nav-link'); // Select all tabs
        const tabContent = document.querySelectorAll('.tab-pane'); // Select all tab content sections

        // Function to activate a tab
        function activateTab(tabId) {
            tabs.forEach(tab => {
                tab.classList.toggle('active', tab.getAttribute('href') === `#${tabId}`);
            });

            tabContent.forEach(content => {
                content.classList.toggle('show', content.id === tabId);
                content.classList.toggle('active', content.id === tabId);
            });
        }

        // Handle tab click
        tabs.forEach(tab => {
            tab.addEventListener('click', (event) => {
                const href = tab.getAttribute('href');
                const tabId = href.substring(1); // Remove '#' from href
                history.pushState({
                    tabId
                }, '', href); // Update the browser's history
            });
        });

        // Handle back/forward navigation
        window.addEventListener('popstate', (event) => {
            const tabId = event.state?.tabId || 'billing'; // Default to 'billing' if no hash
            activateTab(tabId);
        });

        // Handle initial load
        const initialTabId = window.location.hash ? window.location.hash.substring(1) : 'billing';
        activateTab(initialTabId);

        // Ensure a default tab is always active
        if (!window.location.hash) {
            history.replaceState({
                tabId: 'billing'
            }, '', '#billing'); // Update history with the default tab
        }
    });

    function toggleDropdown() {
        $('.dropdown-menu').toggleClass('show');
    }
</script>

<script src="js/main.js"></script>

<!-- ionIcons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ionIcons -->



<!-- add newRow  -->



<!-- add newRow  -->



<!-- Toggles  -->

<script>
    function chooseUploadProcess(ele = false) {
        console.log(ele);
        var input = '';
        if (ele) {
            let form = $(ele).closest('form'); // or .closest('.modal')
            input = form.find('#input_on_team_name').val();
        } else {
            input = $('#input_on_team_name').val();
        }
        console.log(input)
        // if ($('#input_on_team_name').val() == "") {
        //     alert("Please fill the Team Name.");
        //     return false;
        // } 

        if (input == "") {
            alert("Please fill the Team Name.");
            return false;
        }
        $('.teamTabsSection').show(300);

        const teamId = $('#teamTab li').length;
        $('#teamTab .nav-link').removeClass('active');

        $('#table_showing .tab-pane').removeClass('active');



        const teamTab = `

                    <li class="nav-item" role="presentation">

                        <a class="nav-link  active" id="fill-tab-${teamId}" data-bs-toggle="tab" href="#fill-tabpanel-${teamId}" role="tab" aria-controls="fill-tabpanel-${teamId}" aria-selected="true"> Team ${teamId} </a>

                    </li>`;

        $('#teamTab').append(teamTab);

        var prod_id = $('#prod_id').val();
        var form_id = $('#form_id_inc').val();
        // var on_team_name = window.btoa($('#input_on_team_name').val());
        var on_team_name = window.btoa(input);

        var on_year = window.btoa($('#input_on_year').val());

        //var draft_id = $('#edit_draft_id').val();
        $.ajax({
            type: "POST",
            dataType: "html",
            url: "ajax/manage_order/new_card_upload.php",
            data: {
                "prod_id": prod_id,
                "form_id": form_id,
                "on_team_name": on_team_name,
                "on_year": on_year
            },
            success: function(resp) {
                $('#table_showing').append(resp);

                $('#input_on_team_name').val("");
                $('#input_on_year').val("");
                form_id = parseInt(form_id);
                form_id++;
                $('#form_id_inc').val(form_id);
                $('#form_id_inc').val(form_id);
                tmp_num_form = parseInt($('#tmp_num_form').val());
                tmp_num_form++;
                $('#tmp_num_form').val(tmp_num_form);

                $('#addNewTeam').modal('hide');
            }
        });

    }

    $(document).ready(function() {
        $('#toggleGuide').click(function() {
            $('#detailsList').fadeToggle(300); // Adjust duration as needed
        });



    });

    $(document).ready(function() {
        $("#teamTab a").click(function() {
            $(this).tab('show');
        });
        $('#teamTab a').on('shown.bs.tab', function(event) {
            var x = $(event.target).text(); // active tab
            var y = $(event.relatedTarget).text(); // previous tab
            $(".act span").text(x);
            $(".prev span").text(y);
        });
    });

    
    $(document).on('click', '.orderFormUpload', function () {
        let isValid = CheckOrderFormValidation(); 
        if(!isValid){
             console.log("validation failed"); 
             return false; 
        }


            var prod_id = $('#prod_id').val();

            var form_id = $('#form_id_inc').val();

            var on_team_name = base64EncodeUnicode($('#input_on_team_name').val());

            var on_year = window.btoa($('#input_on_year').val());
            const teamId = $('#teamTab li').length;
    
        
            let fileInput = $('#order_form_file')[0];
            let file = fileInput.files[0];

            if (!file) return;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('form_id' ,form_id) ; 
            formData.append('prod_id' ,prod_id) ; 
            formData.append('on_team_name' ,on_team_name) ; 
            formData.append('teamno' , teamId) ; 
            formData.append('on_year' , on_year) ; 






            // 👉 Optional: add extra data
            // formData.append('order_id', $('#order_id').val());

            $.ajax({
                url: 'ajax/add_order/upload_order_form.php', // 🔁 change to your PHP file
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType : 'json' , 
                success: function (response) {
                    if(response.upload == false){
                         alert("Blank or wrong order form . Please add correct order form"); 
                         return false ; 
                    }


                      $('.teamTabsSection').show(300); // Adjust duration as needed
                     $('#table_showing').append(response.html);


                },
                error: function (xhr, status, error) {
                    console.error("Upload failed:", error);
                    alert('Upload failed');
                }
            });

    });




    $(document).ready(function() {
        $('#AddNewTeamModal').click(function() {

        let isValid = CheckOrderFormValidation(true); 
        if(!isValid){
             console.log("validation failed"); 
             return false; 
        }

            $('.teamTabsSection').show(300); // Adjust duration as needed
            var prod_id = $('#prod_id_new').val();
            var form_id = $('#form_id_inc').val();
            var on_team_name = window.btoa($('#input_on_team_name_new').val());
            var on_year = window.btoa($('#input_on_year_new').val());

            $('.teamTabsSection').show(300); // Show the section
            //const teamId = `team${nextTeamNumber}`;

            // const teamTab = `

            //     <li class="nav-item" role="presentation">

            //         <a class="nav-link" id="${teamId}-tab" data-bs-toggle="tab" href="#${teamId}" role="tab" aria-controls="${teamId}" aria-selected="false">

            //             Team ${nextTeamNumber}

            //         </a>

            //     </li>`;

            const teamId = $('#teamTab li').length;
            $('#teamTab .nav-link').removeClass('active');

            $('#table_showing .tab-pane').removeClass('active');



            const teamTab = `

                    <li class="nav-item" role="presentation">

                        <a class="nav-link  active" id="fill-tab-${teamId}" data-bs-toggle="tab" href="#fill-tabpanel-${teamId}" role="tab" aria-controls="fill-tabpanel-${teamId}" aria-selected="true"> Team ${teamId} </a>

                    </li>`;



            $('#teamTab').append(teamTab);



            $.ajax({

                type: "POST",

                dataType: "html",

                url: "ajax/add_order/new_card.php",

                data: {

                    "prod_id": prod_id,

                    "form_id": form_id,

                    "on_team_name": on_team_name,

                    "on_year": on_year,

                    "teamno": teamId

                },

                success: function(resp) {



                    //if(resp=="success"){



                    $('#table_showing').append(resp);

                    // $('#input_on_team_name').val("");

                    // $('#input_on_year').val("");

                    form_id = parseInt(form_id);

                    form_id++;

                    $('#form_id_inc').val(form_id);

                    $('#form_id_inc_modal').val(form_id);

                    //$('#addNewTeam').modal("toggle");

                    tmp_num_form = parseInt($('#tmp_num_form').val());

                    tmp_num_form++;

                    $('#tmp_num_form').val(tmp_num_form);

                    // }else{

                    //     alert(resp.msg);

                    // }
                    $('#addNewTeam').modal('hide');

                }

            });

        });

    });


    function base64EncodeUnicode(str) {
        return btoa(
            new TextEncoder().encode(str)
                .reduce((data, byte) => data + String.fromCharCode(byte), '')
        );
    }


    $(document).ready(function() {

        $('#showTeamTabsSection').click(function() {

            isValid = CheckOrderFormValidation();

            if (!isValid) {
                console.log("Validation issue");
                return false;
            }


           

            var prod_id = $('#prod_id').val();

            var form_id = $('#form_id_inc').val();

            var on_team_name = base64EncodeUnicode($('#input_on_team_name').val());

            var on_year = window.btoa($('#input_on_year').val());



            $('.teamTabsSection').show(300); // Show the section



            
            $('#teamTab .nav-link').removeClass('active');
            
            $('#table_showing .tab-pane').removeClass('active');
            
            
            
            const teamId = $('#teamTab li').length;
            const teamTab = `

                    <li class="nav-item" role="presentation">

                        <a class="nav-link  active" id="fill-tab-${teamId}" data-bs-toggle="tab" href="#fill-tabpanel-${teamId}" role="tab" aria-controls="fill-tabpanel-${teamId}" aria-selected="true"> Team ${teamId} </a>

                    </li>`;



            $('#teamTab').append(teamTab);



            $.ajax({

                type: "POST",

                dataType: "html",

                url: "ajax/add_order/new_card.php",

                data: {

                    "prod_id": prod_id,

                    "form_id": form_id,

                    "on_team_name": on_team_name,

                    "on_year": on_year,

                    "teamno": teamId

                },

                success: function(resp) {



                    //if(resp=="success"){

                   $('.teamTabsSection').show(300); // Adjust duration as needed

                    $('#table_showing').append(resp);

                    // $('#input_on_team_name').val("");

                    // $('#input_on_year').val("");

                    form_id = parseInt(form_id);

                    form_id++;

                    $('#form_id_inc').val(form_id);

                    $('#form_id_inc_modal').val(form_id);

                    tmp_num_form = parseInt($('#tmp_num_form').val());

                    tmp_num_form++;

                    $('#tmp_num_form').val(tmp_num_form);

                    // }else{

                    //     alert(resp.msg);

                    // }

                }

            });

        });

    });



    $(document).ready(function() {

        $('#showTeamTabsSection_new').click(function() {



            if ($('#input_on_team_name').val() == "") {

                alert("Please fill the Team Name.");

                return false;

            }

            $('.teamTabsSection').show(300); // Adjust duration as needed

            var prod_id = $('#prod_id').val();

            var form_id = $('#form_id_inc').val();

            var tablecount = $('.tablecount').text();

            var intval = parseInt(tablecount);



            var on_team_name = window.btoa($('#input_on_team_name').val());

            var on_year = window.btoa($('#input_on_year').val());

            var teamId = $('#teamTab li').length - 1;

            $.ajax({

                type: "POST",

                dataType: "html",

                url: "ajax/add_order/new_card_same_team.php",

                data: {

                    "prod_id": prod_id,

                    "form_id": form_id,

                    "on_team_name": on_team_name,

                    "on_year": on_year,

                    "teamno": teamId

                },

                success: function(resp) {



                    //if(resp=="success"){



                    $('#sameteam' + teamId + ' #tab-content #fill-tabpanel-' + teamId + '').append(resp);

                    // $('#input_on_team_name').val("");

                    // $('#input_on_year').val("");

                    form_id = parseInt(form_id);

                    form_id++;

                    $('#form_id_inc').val(form_id);



                    tmp_num_form = parseInt($('#tmp_num_form').val());

                    tmp_num_form++;

                    $('#tmp_num_form').val(tmp_num_form);

                    // }else{

                    //     alert(resp.msg);

                    // }

                }

            });

        });

    });
</script>

<!-- Toggle  RosterDetailsGuide  -->


<script>
    // document.addEventListener('DOMContentLoaded', function() {

    // const tabLinks = document.querySelectorAll('.switch-tab');

    // tabLinks.forEach(link => {

    //     link.addEventListener('click', function(event) {

    //         event.preventDefault();

    //         const target = this.getAttribute('data-target');

    //         // Activate the new tab

    //         const newTab = document.querySelector(target);

    //         const currentTab = document.querySelector('.tab-pane.show');

    //         if (currentTab) {

    //             currentTab.classList.remove('show', 'active');

    //         }

    //         newTab.classList.add('show', 'active');



    //         // Update the active class for nav links

    //         const navLinks = document.querySelectorAll('.nav-link');

    //         navLinks.forEach(navLink => {

    //             navLink.classList.remove('active');

    //             if (navLink.getAttribute('href') === target) {

    //                 navLink.classList.add('active');

    //             }

    //         });

    //     });

    // });

    // });



    document.addEventListener('DOMContentLoaded', function() {

        const tabLinks = document.querySelectorAll('.switch-tab');

        tabLinks.forEach(link => {

            link.addEventListener('click', function(event) {

                // 👉 Run validation ONLY for billing step
                let isValid = true;

                if (this.classList.contains('billing_info_tab_continue')) {
                    isValid = CheckBillingFormValidation();
                } else if (this.classList.contains('continuteToteamRoster')) {
                    isValid = CheckOrderInformationValidation();
                }

                console.log("ISvalid", isValid);


                if (!isValid) {
                    event.preventDefault();
                    console.log("Blocked by validation");
                    return false;
                }

                // ✅ Only runs if valid
                event.preventDefault();

                const target = this.getAttribute('data-target');

                const newTab = document.querySelector(target);
                const currentTab = document.querySelector('.tab-pane.show');

                if (currentTab) {
                    currentTab.classList.remove('show', 'active');
                }

                newTab.classList.add('show', 'active');

                // Update nav links
                const navLinks = document.querySelectorAll('.nav-link');

                navLinks.forEach(navLink => {
                    navLink.classList.remove('active');

                    if (navLink.getAttribute('href') === target) {
                        navLink.classList.add('active');
                    }
                });

            });

        });

    });
</script>

<script>
    function removeTable(element) {

        // Find the closest table-responsive div and remove it

        // const tableDiv = element.closest('.table-responsive'); 
        const tableDiv = element.closest('.table')



        if (tableDiv) {

            tableDiv.remove();

        }

    }

    function addItemRow(form_id, prod_id) {

        var num_item = $('#num_item_' + form_id).val();

        $('#loading_' + form_id).show();

        var of_id = $('#edit_of_id' + form_id).val();

        $.ajax({
            type: "POST",
            dataType: "html",
            url: "ajax/manage_order/add_item_row.php",
            data: {
                "form_id": form_id,
                "prod_id": prod_id,
                "num_item": num_item,
                "of_id": of_id
            },
            success: function(resp) {
                num_item = parseInt(num_item) + 1;
                $('#num_item_' + form_id).val(num_item);

                $('#prod_item_' + form_id).append(resp);

                $('#loading_' + form_id).hide();
            }
        });
    }

    function deleteItemRow(form_id, oi_id, prod_id, row_id, split_no = 1) {

        if (confirm("Deleting row. Confirm?")) {

            if (oi_id != "new") {
                var oi_id_delete = $('#oi_id_delete').val();
                if (oi_id_delete != "") {
                    oi_id_delete = oi_id_delete + "," + oi_id;
                } else {
                    oi_id_delete = oi_id;
                }
                $('#oi_id_delete').val(oi_id_delete);
            }

            $('#prod_item_' + form_id + '_' + row_id).remove();

            if (prod_id == "1") {
                calculateQTY(1, 'jersey_qty_' + form_id);
                calculateQTY(1, 'jersey_qty2_' + form_id);
                calculateQTY(1, 'sock_qty_' + form_id);
                calculateQTY(1, 'sock_qty2_' + form_id);
            } else {
                if (split_no == 1) {
                    calculateQTY(prod_id, 'jersey_qty_' + form_id);
                } else {
                    calculateQTY(prod_id, 'jersey_qty_' + form_id);
                    calculateQTY(prod_id, 'sock_qty_' + form_id);
                }
            }

        }

    }

    
    
	function calculateQTY(prod_id, class_name) {

		var qty_total = 0;
		$('.' + class_name).each(function() {
			if ($(this).val() != "") {
				qty_total += parseInt($(this).val());
			}

		});

		$('#total_' + class_name).html(qty_total);
	}


    function checkBeforeSaveDraft() {

        if ($('#req_due_date').val() == "") {
            alert("Please input Request Due date.");
            return false;
        }

        if ($('#game_event_date').val() == "") {
            alert("Please input Game/Event date.");
            return false;
        }

        if ($('#tmp_num_form').val() == "0") {
            alert("There is no form to submit.");
            return false;
        }

        var bi_addr_id = $('#bi_addr_id').val();
        var de_addr_id = $('#de_addr_id').val();
        var bi_company_name = $('#bi_company_name').val();
        var bi_contact = $('#bi_contact').val();
        var bi_address = $('#bi_address').val();
        var bi_city = $('#bi_city').val();
        var bi_country = $('#bi_country').val();
        var bi_zip_code = $('#bi_zip_code').val();
        var bi_tel = $('#bi_tel').val();
        var bi_email = $('#bi_email').val();
        var bi_tax_id = $('#bi_tax_id').val();

        var de_company_name = $('#de_company_name').val();
        var de_contact = $('#de_contact').val();
        var de_address = $('#de_address').val();
        var de_city = $('#de_city').val();
        var de_country = $('#de_country').val();
        var de_zip_code = $('#de_zip_code').val();
        var de_tel = $('#de_tel').val();
        var de_email = $('#de_email').val();
        var de_tax_id = $('#de_tax_id').val();

        if (bi_contact == "" || bi_address == "" || bi_city == "" || bi_country == "" || bi_zip_code == "" || bi_tel == "" || bi_email == "" || de_contact == "" || de_address == "" || de_city == "" || de_country == "" || de_zip_code == "" || de_tel == "" || de_email == "") {
            alert("All data is required except Company and TAX ID.");
            return false;
        }

        // $('#btn_save_and_go').attr("disabled", true).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Checking...');

        var is_match = true;
        if (bi_company_name != de_company_name) {
            is_match = false;
        }
        if (bi_contact != de_contact) {
            is_match = false;
        }

        if (bi_address != de_address) {
            is_match = false;
        }

        if (bi_city != de_city) {
            is_match = false;
        }

        if (bi_country != de_country) {
            is_match = false;
        }

        if (bi_zip_code != de_zip_code) {
            is_match = false;
        }

        if (bi_tel != de_tel) {
            is_match = false;
        }

        if (bi_email != de_email) {
            is_match = false;
        }

        if (bi_tax_id != de_tax_id) {
            is_match = false;
        }

        if (bi_addr_id == de_addr_id) {
            if (is_match) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "ajax/add_order/check_update_address.php?condition_case=1",
                    data: $('#form1').serialize(),
                    success: function(resp) {
                        if (resp.result == "fail") {
                            alert(resp.msg);
                            $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                            return false;
                        } else {
                            if (resp.result == "no_update") {
                                saveDraft();
                            } else {
                                if (confirm("System found some address info changes. \nDo you want to update Billing and Delivery address?")) {
                                    $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: "ajax/add_order/update_default_address.php?condition_case=1",
                                        data: $('#form1').serialize(),
                                        success: function(resp2) {
                                            if (resp2.result == "success") {
                                                saveDraft();
                                            } else {
                                                alert(resp2.msg);
                                                $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                                                return false;
                                            }
                                        }
                                    });
                                } else {
                                    saveDraft();
                                }
                            }
                        }
                    }
                });
            } else {
                if (confirm("System found some address info changes. \nDo you want to update Billing and Delivery address?")) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "ajax/add_order/update_default_address.php?condition_case=2",
                        data: $('#form1').serialize(),
                        success: function(resp2) {
                            if (resp2.result == "success") {
                                saveDraft();
                            } else {
                                alert(resp2.msg);
                                $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                                return false;
                            }
                        }
                    });
                } else {
                    saveDraft();
                }
            }
        } else {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "ajax/add_order/check_update_address.php?condition_case=2",
                data: $('#form1').serialize(),
                success: function(resp) {
                    if (resp.result == "fail") {
                        alert(resp.msg);
                        $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                        return false;
                    } else {
                        if (resp.result == "no_update") {
                            saveDraft();
                        } else {
                            if (confirm("System found some address info changes. \nDo you want to update Billing and Delivery address?")) {
                                if (is_match) {
                                    if (resp.bi_update == "yes" && resp.de_update == "yes") {
                                        $.ajax({
                                            type: "POST",
                                            dataType: "json",
                                            url: "ajax/add_order/update_default_address.php?condition_case=4",
                                            data: $('#form1').serialize(),
                                            success: function(resp2) {
                                                if (resp2.result == "success") {
                                                    saveDraft();
                                                } else {
                                                    alert(resp2.msg);
                                                    $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                                                    return false;
                                                }
                                            }
                                        });
                                    } else if (resp.bi_update == "no" && resp.de_update == "yes") {
                                        $.ajax({
                                            type: "POST",
                                            dataType: "json",
                                            url: "ajax/add_order/update_default_address.php?condition_case=5",
                                            data: $('#form1').serialize(),
                                            success: function(resp2) {
                                                if (resp2.result == "success") {
                                                    saveDraft();
                                                } else {
                                                    alert(resp2.msg);
                                                    $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                                                    return false;
                                                }
                                            }
                                        });
                                    } else if (resp.bi_update == "yes" && resp.de_update == "no") {
                                        $.ajax({
                                            type: "POST",
                                            dataType: "json",
                                            url: "ajax/add_order/update_default_address.php?condition_case=6",
                                            data: $('#form1').serialize(),
                                            success: function(resp2) {
                                                if (resp2.result == "success") {
                                                    saveDraft();
                                                } else {
                                                    alert(resp2.msg);
                                                    $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                                                    return false;
                                                }
                                            }
                                        });
                                    } else {
                                        saveDraft();
                                    }
                                } else {
                                    $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: "ajax/add_order/update_default_address.php?condition_case=3",
                                        data: $('#form1').serialize(),
                                        success: function(resp2) {
                                            if (resp2.result == "success") {
                                                saveDraft();
                                            } else {
                                                alert(resp2.msg);
                                                $('#btn_save_and_go').attr("disabled", false).html('Save and go to Manage Order');
                                                return false;
                                            }
                                        }
                                    });
                                }
                            } else {
                                saveDraft();
                            }
                        }
                    }
                }
            });
        }
    }



    function saveDraft() {



        // 	$('#btn_save_and_go').attr("disabled",true).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Saving...');



        var check_file_blank = 0;

        var check_not_excel = 0;



        var file_ext_allow = ['xls', 'xlsx'];



        $('.file_field').each(function() {



            if ($(this).val() == "") {



                check_file_blank = 1;

                return false;

            }

            if ($.inArray($(this).val().split('.').pop().toLowerCase(), file_ext_allow) == -1) {



                check_not_excel = 1;

                return false;

            }



        });


        if (check_file_blank == 1) {
            alert("Please choose file");
            return false;
        }



        if (check_not_excel == 1) {

            alert("Allow only Excel file [xls or xlsx]");

            return false;

        }



        $('#btn_save_and_go').attr("disabled", true).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Saving...');



        $('#form1').attr("action", "ajax/add_order/submit_draft.php");

        $('#form1').submit();



        // 	$.ajax({  

        //         type: "POST",  

        //         dataType: "json", 

        //         url:"ajax/add_order/submit_draft.php" ,

        //         data: $('#form1').serialize() ,

        //         success: function(resp){



        //         	if(resp.result == "success"){

        //         		window.location.href = "?vp=<?php echo base64_encode('manage_order'); ?>";

        //         	}else{

        //         		alert(resp.msg);

        //         		$('#btn_save_and_go').attr("disabled",false).html('Save and go to Manage Order');

        //         	}


 
        //         }

        //     });

    }

    function submitOrder() {

        if (confirm("Are you sure you want to submit? Changes will not be allowed after clicking Submit.")) {

            if ($('#req_due_date').val() == "") {
                $('.req_errormsg').text('Please input Request Due date.');
                const orderTab = document.querySelector('#order-tab');
                const tab = new bootstrap.Tab(orderTab);
                tab.show();
                return false;
            }

            if ($('#game_event_date').val() == "") {
                $('.game_errormsg').text('Please input Game/Event date.');
                const orderTab = document.querySelector('#order-tab');
                const tab = new bootstrap.Tab(orderTab);
                tab.show();
                return false;
            }

            if ($('#tmp_num_form').val() == "0") {
                alert("There is no form to submit.");
                return false;
            }

            //var bi_company_name = $('#bi_company_name').val();
            var bi_contact = $('#bi_contact').val();
            var bi_address = $('#bi_address').val();
            var bi_city = $('#bi_city').val();
            var bi_country = $('#bi_country').val();
            var bi_zip_code = $('#bi_zip_code').val();
            var bi_tel = $('#bi_tel').val();
            var bi_email = $('#bi_email').val();
            //var bi_tax_id = $('#bi_tax_id').val();

            //var de_company_name = $('#de_company_name').val();
            var de_contact = $('#de_contact').val();
            var de_address = $('#de_address').val();
            var de_city = $('#de_city').val();
            var de_country = $('#de_country').val();
            var de_zip_code = $('#de_zip_code').val();
            var de_tel = $('#de_tel').val();
            var de_email = $('#de_email').val();
            //var de_tax_id = $('#de_tax_id').val();

            if (bi_contact == "" || bi_address == "" || bi_city == "" || bi_country == "" || bi_zip_code == "" || bi_tel == "" || bi_email == "" || de_contact == "" || de_address == "" || de_city == "" || de_country == "" || de_zip_code == "" || de_tel == "" || de_email == "") {
                alert("All data is required except Company and TAX ID.");
                return false;
            }

            $('#btn_submit_order').attr("disabled", true).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Submiting...');

            $('#is_submit_order').val("yes");
            $('#form1').attr("action", "ajax/manage_order/submit_draft.php");
            $('#form1').submit();
        }

    }
</script>

<script>
    // Function to delete a row

    function deleteRow(button) {

        const row = button.closest('tr'); // Get the row that contains the button

        row.parentNode.removeChild(row); // Remove the row from the table

    }
</script>



<script>
    document.getElementById("toggleButton").addEventListener("click", function() {

        var leftSide = document.querySelector(".leftSide");

        var rightSide = document.querySelector(".rightSide");



        if (rightSide.classList.contains("collapsed")) {

            // Expand the right side

            rightSide.classList.remove("collapsed");

            leftSide.classList.remove("expanded");

        } else {

            // Collapse the right side

            rightSide.classList.add("collapsed");

            leftSide.classList.add("expanded");

        }

    });



    // same as billing info 

    $(document).on('change', '#check', function() {
        const a_data = <?php echo json_encode($a_data); ?>;
        if ($(this).is(':checked')) {
            $('#bi_company_name').val(a_data[0]["addr_name"]);
            $('#bi_contact').val(a_data[0]["contact_name"]);
            $('#bi_country').val(a_data[0]["country"]);
            $('#bi_city').val(a_data[0]["city"]);
            $('#bi_zip_code').val(a_data[0]["zip_code"]);
            $('#bi_email').val(a_data[0]["email"]);
            $('#bi_tel').val(a_data[0]["tel"]);
            //  $('#bi_addr_id').val(a_data[0]["addr_id"]);
            $('#bi_address').val(a_data[0]["address"]);
            $('#bi_tax_id').val(a_data[0]["tax_id"]);

            $('#de_company_name').val(a_data[1]["addr_name"]);
            $('#de_contact').val(a_data[1]["contact_name"]);
            $('#de_country').val(a_data[1]["country"]);
            $('#de_city').val(a_data[1]["city"]);
            $('#de_zip_code').val(a_data[1]["zip_code"]);
            $('#de_email').val(a_data[1]["email"]);
            $('#de_tel').val(a_data[1]["tel"]);
            $('#de_tax_id').val(a_data[1]["tax_id"]);
            //  $('#de_addr_id').val(a_data[1]["addr_id"]); 
            $('#de_address').val(a_data[1]["address"]);

        } else {
            $('#bi_company_name ,#bi_contact ,#bi_country ,#bi_city ,#bi_zip_code ,#de_company_name ,#de_contact ,#de_country ,#de_city ,#de_zip_code ,#de_email ,#de_tel ,#de_tax_id  ,#de_address ,#bi_email ,#bi_tel  ,#bi_address ,#bi_tax_id').val('');
        }
    })

    $(document).on('click', '.open_add_new_modal', function() {
        $('#addNewTeam').modal('show');
    })


    $(document).on('show.bs.tab', '.billing_info_tab_continue', function(event) {
        let isValid = CheckBillingFormValidation();

        if (!isValid) {
            event.preventDefault(); // THIS actually blocks tab switching
            console.log("Validation failed - tab blocked");
        } else {
            console.log("Valid - allow tab switch");
        }
    });




    $(document).on('show.bs.tab', '.continuteToteamRoster', function(event) {
        let isValid = CheckOrderInformationValidation();

        if (!isValid) {
            event.preventDefault(); // THIS actually blocks tab switching
            console.log("Validation failed - tab blocked");
        } else {
            console.log("Valid - allow tab switch");
        }
    });




    // Validation patterns
    const VALIDATION_PATTERNS = {
        textNumber: /^(?=.*[a-zA-Z])[a-zA-Z0-9+\-\s]{3,30}$/,
        city: /^[a-zA-Z\s]{2,80}$/, // Letters and spaces only
        text: /^[a-zA-Z\s]{2,50}$/, // Letters and spaces only
        zipcode: /^[a-zA-Z0-9\-\s]{2,20}$/, // Letters, numbers, hyphens
        tel: /^[0-9+\-\s\(\)]{3,30}$/, // Numbers, +, -, spaces, parentheses
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ // Standard email 
    };

    // Check the first step 





    function showFieldError(fieldId, message) {
        let mainDiv = $('#' + fieldId).closest('.form-group');
        // Remove existing error first
        mainDiv.find('.errorMessage').remove();

        let errorMessage = '<p class="errorMessage" style="color:red;">' + message + '</p>';
        mainDiv.append(errorMessage);
    }

    function clearFieldError(fieldId) {
        let mainDiv = $('#' + fieldId).closest('.form-group');
        mainDiv.find('.errorMessage').remove();
    }


    function validateField(selector, pattern, message, canEmpty = false) {
        let val = $(selector).val().trim();
        let id = $(selector).attr('id');

        // 👉 Case 1: Field is empty
        if (!val) {
            if (canEmpty) {
                clearFieldError(id);
                return true; // empty is allowed
            } else {
                showFieldError(id, message);
                return false; // empty NOT allowed
            }
        }

        // 👉 Case 2: Field has value → must match pattern
        if (!pattern.test(val)) {
            showFieldError(id, message);
            return false;
        }

        // 👉 Valid case
        clearFieldError(id);
        return true;
    }



    function validateDateField(selector, message) {
        let val = $(selector).val();
        let id = $(selector).attr('id');

        if (!val) {
            showFieldError(id, message);
            return false;
        }

        // Check valid date
        let date = new Date(val);
        if (isNaN(date.getTime())) {
            showFieldError(id, "Invalid date");
            return false;
        }

        clearFieldError(id);
        return true;
    }

    function ValidateOnlyEmpty(selector, message) {
        let val = $(selector).val();
        let selectedVal = $(selector).find('option:selected').val(); 
        let id = $(selector).attr('id');


        if (!val || selectedVal=='') {
            showFieldError(id, message);
            return false;
        }

        clearFieldError(id);
        return true;
    }



    function CheckBillingFormValidation() {
        let isValid = true;

        $('.errorMessage').remove();

        // TEXT FIELDS
        isValid &= validateField('#bi_contact', VALIDATION_PATTERNS.text, 'Invalid contact name');
        isValid &= validateField('#de_contact', VALIDATION_PATTERNS.text, 'Invalid contact name');

        isValid &= validateField('#bi_country', VALIDATION_PATTERNS.text, 'Only letters and spaces allowed');
        isValid &= validateField('#de_country', VALIDATION_PATTERNS.text, 'Only letters and spaces allowed');

        isValid &= validateField('#bi_city', VALIDATION_PATTERNS.city, 'Only letters and spaces allowed');
        isValid &= validateField('#de_city', VALIDATION_PATTERNS.city, 'Only letters and spaces allowed');

        isValid &= validateField('#bi_address', VALIDATION_PATTERNS.text, 'Only letters and spaces allowed');
        isValid &= validateField('#de_address', VALIDATION_PATTERNS.text, 'Only letters and spaces allowed');

        isValid &= validateField('#bi_zip_code', VALIDATION_PATTERNS.zipcode, 'Invalid zipcode');
        isValid &= validateField('#de_zip_code', VALIDATION_PATTERNS.zipcode, 'Invalid zipcode');

        isValid &= validateField('#bi_tel', VALIDATION_PATTERNS.tel, 'Invalid telephone number');
        isValid &= validateField('#de_tel', VALIDATION_PATTERNS.tel, 'Invalid telephone number');

        isValid &= validateField('#bi_email', VALIDATION_PATTERNS.email, 'Invalid email address');
        isValid &= validateField('#de_email', VALIDATION_PATTERNS.email, 'Invalid email address');

        isValid &= validateField('#bi_company_name', VALIDATION_PATTERNS.textNumber, 'Invalid company name', true);
        isValid &= validateField('#de_company_name', VALIDATION_PATTERNS.textNumber, 'Invalid company name', true);


        return Boolean(isValid);
    }


    function CheckOrderInformationValidation() {
        let isValid = true;

        $('.errorMessage').remove();

        let eventDateValid = validateDateField('#game_event_date', 'Please select event date');
        let dueDateValid = validateDateField('#req_due_date', 'Please select due date');

        if (!eventDateValid || !dueDateValid) {
            isValid = false;
        }

        // 🔥 Optional: logical validation (important)
        let eventDate = $('#game_event_date').val();
        let dueDate = $('#req_due_date').val();

        if (eventDate && dueDate) {
            if (new Date(dueDate) < new Date(eventDate)) {
                showFieldError('req_due_date', 'Due date cannot be before event date');
                isValid = false;
            }
        }

        isValid &= validateField('#project_name', VALIDATION_PATTERNS.textNumber, 'Invalid order name', true);
        isValid &= validateField('#customer_po', VALIDATION_PATTERNS.textNumber, 'Invalid customer PO', true);



        return isValid;
    }

    function CheckOrderFormValidation(is_new=false) {
        let isValid = true;
        $('.errorMessage').remove();


       if(is_new){
                 isValid &= validateField('#input_on_team_name_new', VALIDATION_PATTERNS.textNumber, 'Invalid team name');
                isValid &= ValidateOnlyEmpty('#input_on_year_new', 'Select Year');
                isValid &= ValidateOnlyEmpty('#prod_id_new', 'Select Order Form');
       }else{
                isValid &= ValidateOnlyEmpty('#input_on_team_name',  'Invalid team name');
                isValid &= ValidateOnlyEmpty('#input_on_year', 'Select Year');
                isValid &= ValidateOnlyEmpty('#prod_id', 'Select Order Form');
        }

        return isValid;
    }
</script>
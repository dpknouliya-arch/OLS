<?php

include('check-session.php');

require_once 'db.php';
include( __DIR__ .'/ajax/dashboard/dashboard_sql.php');

$year_arr = GetYearArr();
$invoice_status = GetInvoiceStatus();  
$status = $_GET['status'] ?? NULL; 


?>

 
<style>
    :root {
        --primaryText: 28px;
        --secondaryText: 14px;
        --secondaryGrey: #777777;
    }


    /*-----------------------
     mainStatus 
     -----------------------*/
    .mainStatus .statusNumber {
        font-size: var(--primaryText);
        color: #111111;
        margin: auto 0;

    }

    .mainStatus img {
        width: 28px;
        height: 28px;
    }

    .mainStatus p {
        color: var(--secondaryTextGrey);
        font-size: var(--secondaryText);
        margin: auto 0;
        position: relative;
        top: -5px;
    }

    .statusItmes,
    .graphsStatus .statusItmes {
        box-shadow: 0px 0px 16px 0px #0000001A;
        border: 1px solid #EBEEF6;
        background: #FAFAFA;
        padding: 1vw;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        justify-content: space-between;
    }

    .graphsStatus .statusItmes {
        border-radius: 8px;
        box-shadow: none;
    }

    /*-----------------------
     mainStatus 
     -----------------------*/
    .innerMainContent {
        background: #FFFFFF;
        margin-top: 15px;
        padding: 20px;
    }



    .graphsStatus {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        padding: 20px 0;
    }

    .chart-wrapper {
        display: flex;
        align-items: end;
        gap: 10px;
        justify-content: space-between;
    }

    .chart-wrapper h6 {
        font-size: var(--secondaryText);
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* DONUT */
    .donut-chart {
        width: 8vw;
        height: 8vw;
        border-radius: 50%;
        background: conic-gradient(#8ee000 0% 80%, #f18b1a 80% 95%, #1e88e5 95% 100%);
        position: relative;
    }

    /* INNER HOLE */
    .donut-chart::after {
        content: "";
        position: absolute;
        inset: 25px;
        background: #fff;
        border-radius: 50%;
    }

    .statusItems a {
        display: flex;
        align-items: center;
        color: #555;
        justify-content: space-between;
        gap: 10px;
    }

    .statusColor {
        width: 12px;
        height: 6px;
        display: block;
    }

    .green {
        background: #8ee000;
    }

    .orange {
        background: #f18b1a;
    }

    .blue {
        background: #1e88e5;
    }


    /*  */

    .moneySpentGraph h4,
    .itemBoughtGraph h4 {
        margin-bottom: 20px;
        color: #666;
        font-weight: 500;
        font-size: 15px;
    }

    /* CHART */
    .chart {
        display: flex;
        position: relative;
        gap: 0.5vw;
        margin-bottom: 20px;
    }

    /* Y AXIS */
    .y-axis {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-right: 1.5vw;
        font-size: 12px;
        gap: 1vw;
        color: #888;
    }

    /* BARS */
    .bars {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5vw;
        width: 100%;
        position: relative;
        padding: 0 15px;
    }

    /* GRID LINES */
    .bars::before {
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(to top, transparent 4px 39px, #e6e6e6 40px);
        z-index: 0;
    }

    /* BAR CONTAINER */
    .bar {
        width: 32px;
        height: 100%;
        position: relative;
        text-align: center;
        z-index: 1;
    }

    /* GRAY MAX BAR */
    .bar-bg {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 100%;
        background: #eeeeee;
        border-radius: 10px;
    }

    /* BLUE VALUE BAR */
    .bar-fill {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: #0b74f0;
        border-radius: 10px;
    }

    /* YEAR LABEL */
    .bar span {
        position: absolute;
        bottom: -28px;
        width: 100%;
        font-size: 12px;
        color: #777;
        white-space: nowrap;
        left: 0;
    }

    /*  YEARS BARS  */



    /* TABLE RECENT ORDERS  */
    .table-wrapper {
        background: #fff;
        padding: 2px;
        border-radius: 4px;
        overflow: auto;
        border: none;
        scrollbar-width: thin;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .table-header h5 {
        font-size: var(--secondaryText);
    }

    .table-header a {
        font-size: 13px;
        background: #0B74F0;
        padding: 5px 15px;
        border-radius: 4px;
        color: #FFF;
    }

    .filters select,
    .filters input {
        padding: 6px 8px;
        margin-left: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .order-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .order-table th,
    .order-table td {
        border-bottom: 1px solid #bdbdbd;
        padding: 8px 12px;
        text-align: center;
        font-size: 13px;
    }

    .order-table th {
        background: #EBEEF6;
        font-weight: 600;
        color: #111111;
    }

    .source-direct {
        color: #0046A1;
        background: #CEE3FF;

    }

    .source-direct,
    .source-ols {
        font-weight: 500;
        text-transform: uppercase;
    }

    .source-ols {
        color: #874600;
        background: #FFE8CF;
    }

    .status {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
    }

    .status.pending {
        color: #343232cc;
        font-style: italic;
        font-weight: 500;
    }

    .status.completed {
        background: #33C481;
        color: #FFF;
    }

    .btn-view {
        padding: 4px 10px;
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-view:hover {
        background: #f1f5f9;
    }

    .icon {
        font-size: 14px;
        cursor: pointer;
    }

    .projectNameData {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .projectNameData img {
        width: 16px;
        height: 16px;
        object-fit: cover;
    }

    /* TABLE RECENT ORDERS  */


    .pageHeader {
    margin: 0 auto;
}


</style>




<div class="container-fluid  h-100 dashboradMain">
    <div class="row w-100 mx-auto">
        <div class="innerMainContent">
            <div class=" defaultHeader position-relative  d-flex align-items-center">
                <div class="pageHeader">
                    <h2>Dashboard</h2>
                    <p>All your orders will appear here</p>
                </div>

                <div class="d-flex align-items-center gap-2">
                     <span>Year:</span>  <select name="" id="filter_year" class="form-select">
                                           <?php 
                                               foreach($year_arr  as $key=>$value){
                                                     ?>  <option value="<?= $value['year'] ?>"><?=$value['year']?></option>  <?php 
                                               }

                                            ?>
                                         
                                           </select>
                    
                </div>
            </div>

            <div class="mainStatus defaultStatus grid4 gap-3  total_count_div">
                <div class="statusItmes">
                    <figure class="my-auto"><img src="images/vector/totalOrder.png" alt=""></figure>
                    <h5 class="statusNumber total_order" >
                        0
                    </h5>
                    <p class="statuDesc">Total Orders</p>
                </div>
                <div class="statusItmes">
                    <figure class="my-auto"><img src="images/vector/itemOrder.png" alt=""></figure>
                    <h5 class="statusNumber  total_items">
                        0
                    </h5>
                    <p class="statuDesc">Items Ordered</p>
                </div>
                <div class="statusItmes">
                    <figure class="my-auto"><img src="images/vector/completeOrder.png" alt=""></figure>
                    <h5 class="statusNumber complete_orders">
                     0                    
                    </h5>
                    <p class="statuDesc">Completed Orders</p>
                </div>
                <div class="statusItmes">
                    <figure class="my-auto"><img src="images/vector/totalamountSpend.png" alt=""></figure>
                    <h5 class="statusNumber  total_spend">
                        0
                    </h5>
                    <p class="statuDesc">Total Amount Spent</p>
                </div>
            </div>

            <div class="graphsStatus defaultStatus gap-3 ">
                <div class="statusItmes invoiceGraph">
                    <div class="upperArea d-flex justify-content-between">
                        <div>
                            <h4 class="StatusNumber total_invoice">0</h4>
                            <p class="descStatus">
                                Invoives
                            </p>
                        </div>
                        <div>
                            <figure><img src="images/vector/totalOrder.png" alt=""></figure>
                        </div>
                    </div>
                    <div class="bottomArea defaultStatus grid3">
                        <div class="chart-wrapper">
                            <div class="stautsNumbers">
                                <div class="statusItems">
                                    <a href="?vp=<?= base64_encode('orderDetail') ?>" class="go_to_details" data-status="Paid">
                                        <h6 class="my-auto"><span class="statusColor green "></span> <span class="Paid"> 0% Paid </span> </h6>
                                        <figure class="my-auto"><img src="images/vector/viewEyeLight.png" alt=""></figure>
                                    </a>
                                </div>
                                <div class="statusItems ">
                                    <a href="?vp=<?= base64_encode('orderDetail') ?>"  class="go_to_details" data-status="Outstanding">
                                        <h6 class="my-auto"> <span class="statusColor orange"></span>  <span class="Unpaid"> 0% Unpaid </span> </h6>
                                        <figure class="my-auto"><img src="images/vector/viewEyeLight.png" alt=""></figure>
                                    </a>
                                </div>
                                <div class="statusItems ">
                                    <a href="?vp=<?= base64_encode('orderDetail') ?>" class="go_to_details" data-status="Pending">
                                        <h6 class="my-auto"> <span class="statusColor blue  "></span><span class="Pending"> 0% Pending </span></h6>
                                        <figure class="my-auto"><img src="images/vector/viewEyeLight.png" alt=""></figure>
                                    </a>
                                </div>

                            </div>
                            <div class="donut-chart"></div>
                        </div>

                    </div>
                </div>
                <div class="moneySpent statusItmes moneySpentGraph">
                    <canvas id="myChart" style="width:70%;max-width:700px"></canvas>

                </div>
                <div class="itemBought statusItmes itemBoughtGraph">
                     <canvas id="myChart2" style="width:70%;max-width:700px"></canvas>
                </div>
            </div>

            <div class="recentOrders">
                <div class="table-header">
                    <h5>Recent Orders <span class="allRecentOrders">(5)</span></h5>
                    <a href="?vp=<?php echo base64_encode('orderDetail'); ?>"> Show All</a>
                </div>
                <div class="tablemainDiv">
                    <div class="table-wrapper">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Draft No.</th>
                                    <th>Order Source</th>
                                    <th>Product</th>
                                    <th>Project Name</th>
                                    <th>Customer PO</th>
                                    <th>Sales Rep</th>
                                    <th>Order Date</th>
                                    <th>JOG Code</th>
                                    <th>Items</th>
                                    <th>Total QTY</th>
                                    <th>Order Status</th>
                                    <th>Order Form</th>
                                    <th>Invoice</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content-footer">

            <a href="">Copyright © 2020 JOGSPORTS. All rights reserved. </a>

        </div>
    </div>

</div>  
<?php include ('./ajax/dashboard/order_form_model.php') ; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
<script src="js/dashboard.js"></script>
<script src="js/viewOrderFiles.js?var=06"></script>





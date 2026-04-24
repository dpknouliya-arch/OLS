<?php

include('check-session.php');
include('db.php');
include( __DIR__ .'/ajax/dashboard/dashboard_sql.php');

$year_arr = GetYearArr();
$invoice_status = GetInvoiceStatus();  
$status =  !empty($_GET['type']) ?  base64_decode($_GET['type'] ,true) : NULL; 
$year = !empty($_GET['year']) ? base64_decode($_GET['year'] ,true) : NULL
 ?>


<style>
    :root {
        --primaryText: 28px;
        --secondaryText: 14px;
        --secondaryGrey: #777777;
    }

    .innerMainContent {
        background: #FFFFFF;
        padding: 20px;
        margin-top: 15px;
    }

    /* TABLE RECENT ORDERS  */
    .table-wrapper {
        background: #fff;
        padding: 2px;
        border-radius: 4px;
        overflow: auto;
        border: none;
        max-height: 600px;
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
        padding: 6px 10px;
        border: 1px solid #DDDDDD;
        border-radius: 4px;
        font-size: 13px;
        color: #111111;
        font-weight: 500;
    }

    .filters button {
        background: #111111;
        border: none;
        padding: 0 12px;
    }

    .searchBar {
        border: 1px solid #DDDDDD;
        display: flex;
        border-radius: 4px;
    }

    .searchBar input {
        border: none;
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

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        font-size: 13px;
    }

    .pagination button {
        margin-left: 4px;
        padding: 4px 8px;
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    .pagination button.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    /* TABLE RECENT ORDERS  */
</style>




<div class="container-fluid  h-100 dashboradMain">
    <div class="row w-100 mx-auto">
        <div class="innerMainContent">
            <div class=" defaultHeader position-relative">
                <div class="pageHeader">
                    <h2>Order History</h2>
                    <p>All your orders will appear here</p>
                </div>
            </div>





            <div class="recentOrders">
               
                <div class="table-header">
                    <div class="filters d-flex align-items-center gap-2">
                        <select class="year_select">
                            <?php 
                               foreach($year_arr as $key=>$val){
                                   $selected = $val['year'] == $year 
                                   ?>
                                       <option value="<?= $val['year'] ?>" $selected><?= $val['year'] ?></option>
                                   <?php 
                               }
                            ?>
                        </select>

                        <select class="invoice_status">
                             <?php 
                               foreach($invoice_status as $key=>$val){
                                  $selected = $key==$status ? 'selected' : '' ; 
                                   ?>
                                       <option value="<?= $key ?>" <?= $selected ?>  ><?= $val ?></option>
                                   <?php
                               }
                            ?>
                        </select>

                        <div class="searchBar">
                            <input type="text" placeholder="Search Orders">
                            <button class="searchBarBtn">
                                <figure class="my-auto"><img src="assets/images/icons/searchWhite.png" alt=""></figure>
                            </button>
                        </div>
                    </div>
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
                    <div class="pagination">
                        
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

<script src="./js/viewOrderFiles.js"></script>
<script>
      $(document).ready(function(){
          GetRecentOrderList()
      })


    function GetRecentOrderList(page=0){
        let year = $('.year_select').val(); 
        let invoice_status = $('.invoice_status').val(); 
        let search = $('.searchBar input').val(); 
        $.ajax({
            url : "./ajax/dashboard/get_recent_order_list.php" , 
            method : "POST" , 
            dataType : "JSON" , 
            data:{
                orderDetails: true , 
                page : page , 
                year : year  , 
                inv_status : invoice_status ,
                search : search 
            },
            success : function(response){
                $('.order-table').find('tbody').html(response['html']);
                $('.pagination').html(response['pagination']);
            } , 
            error : function(xhr ,error , status){
                alert("Something went wrong with recent orders"); 
            }


        }) ;
    }

    $(document).on('click' ,'.pagination_btn' ,function(){
          GetRecentOrderList($(this).text());
    });

    $(document).on('change' ,'.year_select , .invoice_status ' ,function(){
          let page = $('.pagination_btn.active').text();
            GetRecentOrderList(page) ; 
    }); 

    $(document).on('click' , '.searchBarBtn' , function(){
         let page = $('.pagination_btn.active').text();
         GetRecentOrderList(page); 
    });



</script>



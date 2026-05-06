<?php

include('check-session.php');



$obj_user = json_decode(base64_decode($_SESSION["JOGOLSSALE"]));

$sales_user_id = $obj_user->sales_user_id;



// $sql = " SELECT 

//             tsu.*,

//             tu.*,

//             ta.*,

//             tsa.* , 
//             tsa.id AS assigned_id 
//         FROM 

//             tbl_sales_assignments tsa

//         JOIN 

//             tbl_sales_user tsu ON tsa.sales_user_id = tsu.sales_user_id

//         JOIN 

//             tbl_user tu ON tsa.user_id = tu.user_id

//         JOIN 

//             tbl_address ta ON ta.user_id = tu.user_id
//              AND ta.is_billing_addr = 1 
//         WHERE 

//             tsa.sales_user_id = $sales_user_id
//             GROUP BY tsa.id
//     ";

$sql =  "SELECT 
    tsa.id AS assigned_id,
    tsu.*,
    tu.*,
    ta.*, 
    tsa.user_id , 
    tsa.sales_user_id , 
    tsa.enable 
FROM tbl_sales_assignments tsa
JOIN tbl_sales_user tsu 
    ON tsa.sales_user_id = tsu.sales_user_id
JOIN tbl_user tu 
    ON tsa.user_id = tu.user_id
LEFT JOIN tbl_address ta 
    ON ta.user_id = tu.user_id 
    AND ta.is_billing_addr = 1
WHERE tsa.sales_user_id = '$sales_user_id'
GROUP BY tsa.id  ORDER BY tsa.enable ASC";



$sales_data_assign = $conn->query($sql);

?>



<style>

   

    select {

        border: 1px solid #DDDDDD;

        background: #FFFFFF;

        border-radius: 4px;

        color: #777777;

        padding: 10px 10px;

        font-size: 14px;

        width: 100%;

    }



    .modal-dialog {

        max-width: 25vw;

        height: 100%;

        margin: auto;

        justify-content: center;

        align-content: center;

    }



    table th,

    table td {

        white-space: normal !important;

        border: none;

        font-size: 13px;

        border-bottom: 1px solid #DDD !important;

        vertical-align: middle;

    }



    table td:nth-child(2) {

        width: 10vw;

    }



    table td:nth-child(3) {

        width: 15vw;

    }



    table td:nth-child(4) {

        width: 20vw;

    }

    table .disable td{
        background-color: #dedede;
    }
</style>



<div class="customerUserPage innerMainContent">

    <div class="card border-none">



        <div class="head d-flex justify-content-between pageHeader align-items-center  ">

            <h6 class="my-auto">Customer List</h6>

            <div class="d-flex gap-2">

                <!-- <form action="" class="position-relative">

                    <div class="form-group column2">

                        <input type="text" name="" id="" maxlength="10" value="" placeholder="Search Customer...">

                        <button class="iconBTn">

                            <figure class="m-0"><img src="images/vector/whiteSearch.png" alt=""></figure>

                        </button>

                    </div>

                </form> -->

                <button type="button" class="btn   iconBTn   " data-bs-toggle="modal" data-bs-target="#newUserAssign" onclick="return newUserAssign('<?php echo $sales_user_id; ?>');">

                    Assign User <figure class="m-0"> <img src="images/vector/addWhite.png" alt=""></figure>

                </button>

            </div>

        </div>

        <table class="table" id="myTable">

            <thead>

                <tr>

                    <th scope="col" class="text-center">No.</th>

                    <th scope="col">User</th>

                    <th scope="col">Company</th>

                    <th scope="col">Contact</th>

                    <th scope="col" class="text-center">Sales Representative</th>

                    <th scope="col" class="text-center">Login to Acc.</th>

                    <th scope="col" class="text-center">Action</th>

                </tr>

            </thead>

            <tbody>

                <?php

                $i = 1;

                while ($row_assign = $sales_data_assign->fetch_assoc()) {
                    $is_enable =  $row_assign['enable']; 
                    

                ?>

                    <tr class="<?=$is_enable==1 ? '' :'disable' ?>">

                        <td scope="row" class="text-center"><?php echo $i; ?>   </td>

                        <td><?php echo $row_assign['user_email'];

                            echo "</br>"; 

                            echo $row_assign['full_name']   ; ?> </td>

                        <td><?php echo $row_assign['addr_name'];  ?></td>

                        <td><?php echo $row_assign['contact_name'];  ?></td>

                        <td class="text-center">

                            <div class="salesAdminRow">

                                <?php

                                $sqluser = "SELECT * FROM `tbl_sales_assignments` WHERE `user_id` = " . $row_assign['user_id'] . ";";

                                $userdata = $conn->query($sqluser);



                                // Get the total number of rows

                                $totalRows = $userdata->num_rows;



                                while ($salesdata = $userdata->fetch_assoc()) {

                                    $sqlusername = "SELECT `s_user_name` FROM `tbl_sales_user` WHERE `sales_user_id` = " . $salesdata['sales_user_id'] . ";";

                                    $username = $conn->query($sqlusername);

                                    while ($fullname = $username->fetch_assoc()) {

                                        echo '<div class="delete-sales-user salesAdmin" id="assignSales' . $salesdata['sales_user_id'] . '" onclick="deleteSalesAssagin(' . $row_assign['user_id'] . ', ' . $salesdata['sales_user_id'] . ');">';

                                        print_r($fullname['s_user_name'] . " ");

                                        echo '</div>';

                                    }

                                }

                                ?>



                                <?php

                                if ($totalRows <= 1) {

                                ?>
                                  
                                    <button type="button" class="btn  salesAdminBtn" data-bs-toggle="modal" data-bs-target="#newAddressModal" onclick="return addNewSales('<?php echo $row_assign['user_id']; ?>');">

                                        <i class="fa fa-plus"></i>

                                    </button>

                                <?php

                                }

                                ?>

                            </div>

                        </td>

                        <td class="text-center">
                            <button class="btn loginBTn iconBTn <?=$is_enable==1 ? '' :'disabled' ?>" id=<?php echo $row_assign['user_id']; ?> onclick="return loginasUser('<?php echo $row_assign['user_email']; ?>','<?php echo $row_assign['user_password']; ?>');">Login 
                             <figure class="m-0"><img src="images/vector/loginBTn.png" alt=""></figure>
                          </button> 
                           <?php 
                           if($is_enable==0){
                               ?>
                                   <button class="btn btn-sm btn-success enable_user"  data-id = "<?=$row_assign['assigned_id']?>" > Enable </button>
                               <?
                           }
                            
                           ?>
                         </td>


                        <td class="text-center">

                            <div class="d-flex gap-2 justify-content-center">

                                <!-- <button class="btn editBTn " id=<?php echo $row_assign['user_id']; ?>><i class="fa fa-pencil" aria-hidden="true"></i></button> -->

                                <button class="btn  deleteBtn " onclick="return deleteUserAssagin(<?php echo $row_assign['user_id']; ?>, <?php echo $sales_user_id; ?> ,this);"><i class="fa fa-trash" aria-hidden="true"></i></button>

                            </div>

                        </td>

                    </tr>

                <?php

                    $i++;

                }

                ?>

            </tbody>

        </table>

    </div>

</div>







<div class="modal fade" id="newAddressModal" tabindex="-1" aria-labelledby="newAddressModal" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h1 class="modal-title fs-5" id="modal_form_title">Assign Sales Rep</h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="form-group   ">

                    <select name="Username" id="new_s_user_name" required="">

                        <?php

                        $sql_all_user = "SELECT * FROM `tbl_sales_user` WHERE  `sales_user_id` != $sales_user_id ; ";

                        $all_user = $conn->query($sql_all_user);

                        while ($row_s_user = $all_user->fetch_assoc()) {

                            echo '<option value="' . $row_s_user['sales_user_id'] . '">' . $row_s_user['s_user_name'] . '</option>';

                        }

                        ?>

                    </select>

                    <input type="hidden" name="user_id" id="new_user_id" value="">

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn  themeBtn2grey iconBTn" data-bs-dismiss="modal">

                    <figure class="m-0"> <img src="images/vector/cancel.png" alt=""></figure>Cancel

                </button>

                <button type="button" class="btn themeBtn2grey iconBTn" id="btn_submit_address" onclick="return saveNewSales();">

                    <figure class="m-0"> <img src="images/vector/save.png" alt=""></figure> Save

                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="newUserAssign" tabindex="-1" aria-labelledby="newUserAssign" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h1 class="modal-title fs-5" id="modal_form_title">Assign Sales Rep</h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="form-group   ">

                    <select name="assgindata" id="assgindata" required="">

                        <?php

                        $sql_all_user = "SELECT * FROM `tbl_user` WHERE  1; ";

                        $all_user = $conn->query($sql_all_user);

                        while ($row_s_user = $all_user->fetch_assoc()) {

                            echo '<option value="' . $row_s_user['user_id'] . '">' . $row_s_user['full_name'] . '</option>';

                        }

                        ?>

                    </select>

                    <input type="hidden" name="sales_user_id" id="new_sales_user_id" value="<?php echo $sales_user_id; ?>">

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn themeBtn2grey iconBTn" data-bs-dismiss="modal">

                    <figure class="m-0"> <img src="images/vector/cancel.png" alt=""></figure> Cancel

                </button>

                <button type="button" class="btn themeBtn2grey iconBTn" id="btn_submit_address" onclick="return assignData();">

                    <figure class="m-0"> <img src="images/vector/save.png" alt=""></figure> Save

                </button>

            </div>

        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css" />
  
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
<script>
    $(document).ready( function () {
        $('#myTable').DataTable();
    } );
    function deleteUserAssagin(user_id, sales_user_id ,ele=false) {

        let tr = ele.closest('tr'); 

        if (!confirm("Are you sure you want to delete this user assignment?")) {

            return false;

        }

        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/manage_users/delete_user_assign.php",

            data: {

                "user_id": user_id,

                "sales_user_id": sales_user_id

            },

            success: function(resp) {

                if (resp.result == "success") {

                    $('tr').filter(function() {

                        return $(this).find('button.btn-info').attr('id') == user_id;

                    }).remove();

                    tr.remove(); 
                } else {

                    alert("Failed to delete user assignment.");

                }

            }

        });

    }



    function deleteSalesAssagin(user_id, sales_user_id) {

        if (!confirm("Are you sure you want to delete this Sales assignment?")) {

            return false;

        }

        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/manage_users/delete_user_assign.php",

            data: {

                "user_id": user_id,

                "sales_user_id": sales_user_id

            },

            success: function(resp) {

                if (resp.result == "success") {

                    $('#assignSales' + sales_user_id).html('<button type="button" class="btn btn-primary" style="font-size: 12px; padding: 3px 6px;" data-bs-toggle="modal" data-bs-target="#newAddressModal" onclick="return addNewSales(' + user_id + ');"><i class="fa fa-plus"></i></button>');



                } else {

                    alert("Failed to delete user assignment.");

                }

            }

        });

    }



    function assignData() {



        var new_sales_user_id = $('#new_sales_user_id').val();

        var assgindata = $('#assgindata').val();



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/manage_users/submit_assign_sales.php",

            data: {

                "sales_user_id": window.btoa(new_sales_user_id),

                "assgindata": window.btoa(assgindata),

            },

            success: function(resp) {

                if (resp.result == "success") {

                    $('#newUserAssign').modal('hide');

                    location.reload();

                } else {

                    alert(resp.msg);

                }



            }

        });

    }



    function addNewSales(user_id) {

        $('#new_user_id').val(user_id);

    }



    function saveNewSales() {

        var user_id = $('#new_user_id').val();

        var sales_name = $('#new_s_user_name').val();

        //  alert(sales_name);

        //  alert(user_id);

        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/manage_users/submit_assign_sales.php",

            data: {

                "sales_user_id": window.btoa(sales_name),

                "assgindata": window.btoa(user_id),

            },

            success: function(resp) {

                if (resp.result == "success") {

                    $('#newAddressModal').modal('hide');

                    location.reload();

                } else {

                    alert("Fail to assign sales rep.");

                }

            }

        });

    }



    function loginasUser(user_email, user_password) {

        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/main/check_sales_login.php",

            data: {

                "user": window.btoa(user_email),

                "password": user_password

            },

            success: function(resp) {



                if (resp.result == "success") {

                    $('#err_msg').hide();

                    //alert("success");

                    if (resp.first_login != 0) {

                        window.location.href = "?vp=YWRkT3JkZXI=";

                    } else {

                        window.location.href = "?vp=YWRkT3JkZXI=";

                    }

                } else {

                    $('#err_msg').show();

                }





            }

        });

    }



    function newUserAssign() {}

    $(document).on('click' ,'.enable_user' ,function(){
         let id = $(this).data('id'); 
         let ele = $(this); 
         let tr = $(this).closest('tr');
          if(!confirm("Are you sure want to enable this user")){
              return false; 
          }


             $.ajax({
                type: "POST",
                dataType: "json",
                url: "/ajax/manage_sales/delete_assigned.php",
                data: {
                    id : id , 
                    is_enable : true                     
                 },

            success: function(resp) {
                tr.removeClass('disable'); 
                tr.find('.loginBTn').removeClass('disabled'); 
                ele.addClass('d-none');
            } , 
            error : function(resp , error){
                 console.log("error " , error); 
            }

        });
    })

</script>
<?php

include('check-session.php');

include('db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;

if ($obj_user->user_level == "admin") {

    $sql_select = "SELECT 

        su.sales_user_id,

        su.parent_user_id,

        su.sales_email,

        su.last_send,

        su.sent_count,

        su.s_user_name,

        su.nick_name,

        su.s_user_pwd,

        su.last_active,

        su.date_add,

        su.enable,

        COUNT(DISTINCT sa.user_id) AS unique_user_count

    FROM 

        tbl_sales_user su

    LEFT JOIN 

        tbl_sales_assignments sa 
    ON 
        su.sales_user_id = sa.sales_user_id
         AND sa.enable = 1

    GROUP BY 

        su.sales_user_id

    ORDER BY 

        su.sales_user_id;

    ";

} else {

    $sql_select = "SELECT 

                su.sales_user_id,

                su.parent_user_id,

                su.sales_email,

                su.last_send,

                su.sent_count,

                su.s_user_name,

                su.nick_name,

                su.s_user_pwd,

                su.last_active,

                su.date_add,

                su.enable,

                COUNT(DISTINCT sa.user_id) AS unique_user_count

            FROM 
                tbl_sales_user su

            WHERE 
                su.parent_user_id='" . $user_id . "'

            LEFT JOIN 

                tbl_sales_assignments sa 
                
            ON 

                su.sales_user_id = sa.sales_user_id
                AND sa.enable=1 

            GROUP BY 

                su.sales_user_id

            ORDER BY 

                su.sales_user_id;

            ";

}

$rs_select = $conn->query($sql_select);



$maximum_sub_user = 20;

?>







<div class=" manageUSerPage">



    <div class="innerMainContent">

        <div class="pageHeader">

            <h2>Manage Sales</h2>

            <p>This is where your Billing Info will go.</p>

        </div>



        <div class="row userBoxes">

            <div class="d-flex justify-content-between align-items-center px-0 pb-3">

                <h6>Sales Rep. List</h6>

                <button type="button" class="btn btn-dark iconBTn XSmall" data-bs-toggle="modal" data-bs-target="#addSalesUser">

                    <figure class="m-0"><img src="images/vector/addWhite.png" alt=""></figure> Add Sales Rep.

                </button>

            </div>

            <table class="table">

                <thead>

                    <tr>

                        <th scope="col" class="text-center">No.</th>

                        <th scope="col">User</th>

                        <th scope="col">Name</th>

                        <th scope="col">Password</th>

                        <th scope="col">Email</th>

                        <th scope="col">No. of users</th>

                        <th scope="col">Assign Users</th>

                        <th scope="col">Action</th>

                    </tr>

                </thead>

                <tbody id="manage_su_main_panel">

                    <?php

                    $i = 1;

                    while ($row_s_user = $rs_select->fetch_assoc()) {

                    ?>

                        <tr id="row_da<?php echo $row_s_user["sales_user_id"]; ?>"  data-user_id = "<?= $row_s_user["sales_user_id"] ?>">

                            <td class="text-center"><?php echo $i; ?></td>

                            <td id="td_nick_name<?php echo $row_s_user["sales_user_id"]; ?>"><?php echo $row_s_user["nick_name"]; ?></td>

                            <td id="td_s_user_name<?php echo $row_s_user["sales_user_id"]; ?>"><?php echo $row_s_user["s_user_name"]; ?></td>

                            <td id="td_s_user_pwd<?php echo $row_s_user["sales_user_id"]; ?>"><?php echo $row_s_user["s_user_pwd"]; ?></td>

                            <td id="td_sub_email<?php echo $row_s_user["sales_user_id"]; ?>"><?php echo $row_s_user["sales_email"]; ?></td>

                            <td class=" ">

                                <div class="customIconBtn cursor " data-bs-toggle="modal" data-bs-target="#newAddressModal" onclick="return getAssignUser(<?php echo $row_s_user['sales_user_id']; ?>, <?php echo $row_s_user['unique_user_count']; ?>);">

                                    <p class="my-auto  assigned-count" id="td_sub_email<?php echo $row_s_user["sales_user_id"]; ?>"><?php echo $row_s_user["unique_user_count"]; ?></p>

                                    <figure class="iconImg m-0">

                                        <img src="images/vector/viewEye.png" alt="">

                                    </figure>

                                </div>

                            </td>

                            <td class="Assign_user">

                                <div class="iconBTn cursor " data-bs-toggle="modal" onclick="return assignUser(<?php echo $row_s_user['sales_user_id']; ?>, <?php echo $row_s_user['unique_user_count']; ?>);" data-bs-target="#orderFormModal">

                                    Assign User

                                    <figure class="iconImg m-0">

                                        <img src="images/vector/loginBTn.png" alt="">

                                    </figure>



                                </div>  
                            </td>

                            <td>

                                <div class="iconBTn">

                                    <!-- <button class="btn purpleBtn " id=<?php echo $row_s_user['sales_user_id']; ?>>

                                        <figure class="m-0"><img src="images/vector/purpleEdit.png" alt=""></figure>

                                    </button> -->

                                    <button class="btn  redBtn" onclick="return deleteUserAssagin(<?php echo $row_s_user['sales_user_id']; ?>);">

                                        <figure class="m-0"><img src="images/vector/deleteIcon.png" alt=""></figure>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    <?php

                        $i++;

                    } ?>

                </tbody>

            </table>

        </div>



        <div class="main-content-footer">

            <a href="">Copyright © 2020 JOGSPORTS. All rights reserved. </a>

        </div>

    </div>





    <!-- Modal -->

    <div class="modal fade" id="newAddressModal" tabindex="-1" aria-labelledby="newAddressModal" aria-hidden="true">

        <div class="modal-dialog smallModal">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="modal_form_title">Assign User</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    <div id="alluserassign"></div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn iconBTn themeBtn2grey" data-bs-dismiss="modal">

                        <figure class="m-0"><img src="images/vector/cancel.png" alt=""></figure> Close

                    </button>

                    <button type="button" class="btn iconBTn themeBtn" id="btn_submit_address" onclick="return saveAddressInfo();">

                        Submit <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>

                    </button>

                </div>

            </div>

        </div>

    </div>



    <div class="modal fade" id="addSalesUser" tabindex="-1" aria-labelledby="addSalesUser" aria-hidden="true">

        <div class="modal-dialog  smallModal">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="modal_form_title">Add Sales</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <form action="">

                    <div class="modal-body">

                        <div class="grid2 gridForm">

                            <div class="form-group  column2 ">

                                <label for="">Sales Rep</label>



                                <select name="Username" id="new_s_user_name_add" required="" class="form-group">

                                    <?php

                                    $sql_new = "SELECT * FROM employee WHERE employee_position_id='5'";

                                    $emps = $conn3->query($sql_new);

                                    $num_rows = $emps->num_rows;

                                    if ($num_rows > 0) {

                                        while ($row_selection = $emps->fetch_assoc()) {

                                    ?>

                                            <option value="<?= $row_selection['employee_name'] ?>"><?= $row_selection['employee_name'] ?></option>

                                    <?php

                                        }

                                    }

                                    ?>

                                </select>

                                <!-- <input type="text" name="Username" id="new_s_user_name"  onkeypress="$('#sp_check_result').html('');" placeholder="Username"> -->

                                <span id="sp_check_result" style="font-size: 16px; padding-left: 5px;"></span>

                            </div>

                            <div class="form-group  column2">

                                <label for="">Name</label>

                                <input type="text" name="name" id="new_s_nick_name" placeholder="Name">

                            </div>



                            <div class="form-group ">

                                <label for="">Email</label>

                                <input type="email" name="Email" style="text-transform: none;" id="new_sub_email" placeholder=" ">

                            </div>

                            <div class="form-group ">

                                <label for="">Password</label>

                                <input type="password" name="Password" id="new_s_password" placeholder=" ">

                            </div>



                        </div>

                    </div>



                    <div class="modal-footer">

                        <button type="button" class="btn iconBTn themeBtn2grey" data-bs-dismiss="modal">

                            <figure class="m-0"><img src="images/vector/cancel.png" alt=""></figure> Close

                        </button>

                        <button type="button" class="btn iconBTn themeBtn" onclick="return newSubUser();">

                            Submit<figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <div class="modal fade" id="orderFormModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="orderFormModal" aria-hidden="true">

        <div class="modal-dialog smallModal">

            <div class="modal-content">

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modal_form_title">Assign User</h1>
                    <button type="button"  class="btn-close close_assigned_modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 0px 26px;">

                    <form id="assign_form">

                        <div id="order_form_content" class="table-responsive">

                        </div>

                    </form>

                </div>

                <div class="modal-footer">



                    <button type="button" class="btn iconBTn themeBtn2grey  close_assigned_modal" data-bs-dismiss="modal">

                        <figure class="m-0"><img src="images/vector/cancel.png" alt=""></figure> Close

                    </button>

                    <button type="button" class="btn iconBTn themeBtn" onclick="return assignData();">

                        Assign User <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>

                    </button>

                </div>

            </div>

        </div>

    </div>



    <script>

    $(document).on('click'  ,'.close_assigned_modal' ,function(){
         let modal  = $('#orderFormModal'); 
         let count = modal.find('.userNumber').text();
         let sales_id = modal.find('#new_sales_user_id').val();
         $('tr[data-user_id="' + sales_id + '"] .assigned-count').text(count);
    })


    $(document).on('change' , '.active_inactive_select' ,function(){
         window.location.reload();
    })

        function deleteUserAssagin(sales_user_id) {

            if (!confirm("Are you sure you want to delete this Sales Rep?")) {

                return false;

            }

            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/manage_sales/delete_sales.php",

                data: {

                    "sales_user_id": sales_user_id

                },

                success: function(resp) {

                    if (resp.result == "success") {

                        $('#row_da' + sales_user_id + '').remove();

                    } else {

                        alert("Failed to delete user assignment.");

                    }

                }

            });

        }



        function getAssignUser(sales_id, total_user) {

            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/manage_sales/get_username.php",

                data: {

                    "sales_user_id": sales_id,

                    "total_user": total_user,

                },

                success: function(resp) {

                    if (resp.result == "success") {



                        $('#alluserassign').html(resp.data);

                    } else {

                        alert(resp.msg);

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

                url: "ajax/manage_users/submit_assign.php",

                data: {

                    "sales_user_id": window.btoa(new_sales_user_id),

                    "assgindata": window.btoa(assgindata),

                },

                success: function(resp) {

                    if (resp.result == "success") {

                        $('#orderFormModal').modal('hide');
                        $('tr[data-user_id="' + resp.sales_user_id + '"] .assigned-count').text(resp.count);

                    } else {

                        alert(resp.msg);

                    }



                }

            });

        }



        function editSU(sales_user_id) {



            var edit_td_nick_name = '<input type="text" id="edit_s_nick_name' + sales_user_id + '" value="' + $('#td_nick_name' + sales_user_id).html() + '" style="width: 85%;" maxlength="80">';

            edit_td_nick_name += '<input type="hidden" id="old_s_nick_name' + sales_user_id + '" value="' + $('#td_nick_name' + sales_user_id).html() + '">';

            $('#td_nick_name' + sales_user_id).html(edit_td_nick_name);



            var edit_td_s_user_name = '<input type="text" id="edit_s_user_name' + sales_user_id + '" value="' + $('#td_s_user_name' + sales_user_id).html() + '" style="width: 85%;" maxlength="30" onkeypress="$(\'#sp_check_result' + sales_user_id + '\').html(\'\');">';

            edit_td_s_user_name += '<input type="hidden" id="old_s_user_name' + sales_user_id + '" value="' + $('#td_s_user_name' + sales_user_id).html() + '">';

            edit_td_s_user_name += '<span id="sp_check_result' + sales_user_id + '" style="font-size: 16px; padding-left: 5px;"></span>';

            $('#td_s_user_name' + sales_user_id).html(edit_td_s_user_name);



            var edit_td_s_user_pwd = '<input type="text" id="edit_s_user_pwd' + sales_user_id + '" value="' + $('#td_s_user_pwd' + sales_user_id).html() + '" style="width: 85%;" maxlength="80">';

            edit_td_s_user_pwd += '<input type="hidden" id="old_s_user_pwd' + sales_user_id + '" value="' + $('#td_s_user_pwd' + sales_user_id).html() + '">';

            $('#td_s_user_pwd' + sales_user_id).html(edit_td_s_user_pwd);



            var edit_td_sub_email = '<input type="text" id="edit_sub_email' + sales_user_id + '" value="' + $('#td_sub_email' + sales_user_id).html() + '" style="width: 85%;" maxlength="80">';

            edit_td_sub_email += '<input type="hidden" id="old_sub_email' + sales_user_id + '" value="' + $('#td_sub_email' + sales_user_id).html() + '">';

            $('#td_sub_email' + sales_user_id).html(edit_td_sub_email);



            var inner = '<button class="btn btn-primary btn_action" onclick="return saveEditSU(' + sales_user_id + ');">Save</button>';

            inner += '<button class="btn btn-secondary btn_action" onclick="return cancelEditSU(' + sales_user_id + ');">Cancel</button>';

            $('#th_btn_zone' + sales_user_id).html(inner);

        }



        function saveEditSU(sales_user_id) {



            var s_nick_name = $('#edit_s_nick_name' + sales_user_id).val();

            var s_user_name = $('#edit_s_user_name' + sales_user_id).val();

            var s_user_pwd = $('#edit_s_user_pwd' + sales_user_id).val();

            var sub_email = $('#edit_sub_email' + sales_user_id).val();



            if (s_nick_name == "" || s_user_name == "" || s_user_pwd == "" || sub_email == "") {



                alert("Please input all info.");

                return false;

            }



            if (!isEmail(sub_email)) {

                alert("Please input correct email.");

                return false;

            }



            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/manage_users/check_edit_su.php",

                data: {

                    "s_user_name": window.btoa(s_user_name),

                    "sales_user_id": sales_user_id

                },

                success: function(resp2) {

                    if (resp2.result == "success") {



                        $('#sp_check_result').html('');



                        $.ajax({

                            type: "POST",

                            dataType: "json",

                            url: "ajax/manage_users/submit_edit_su.php",

                            data: {

                                "s_nick_name": window.btoa(s_nick_name),

                                "s_user_name": window.btoa(s_user_name),

                                "s_password": window.btoa(s_user_pwd),

                                "sub_email": window.btoa(sub_email),

                                "sales_user_id": sales_user_id

                            },

                            success: function(resp) {

                                if (resp.result == "success") {



                                    $('#td_nick_name' + sales_user_id).html(s_nick_name);

                                    $('#td_s_user_name' + sales_user_id).html(s_user_name);

                                    $('#td_s_user_pwd' + sales_user_id).html(s_user_pwd);

                                    $('#td_sub_email' + sales_user_id).html(sub_email);



                                    var inner = '<div class="d-flex gap-3 justify-content-end">';

                                    inner += '<div class="goBackBtn">';

                                    inner += '<button class="btn btn-primary btn_action" onclick="return editSU(' + sales_user_id + ');">Edit</button>';

                                    inner += '</div>';

                                    inner += '<div class="goBackBtn">';

                                    inner += ' <button class="btn btn-primary btn_action  " onclick="return assignUser(' + sales_user_id + ');" title="Send user info to Email.">';

                                    inner += 'Mail';

                                    inner += '</button>';

                                    inner += '</div>';

                                    $('#th_btn_zone' + sales_user_id).html(inner);



                                } else {

                                    alert(resp.msg);

                                }



                            }

                        });



                    } else {

                        alert("Duplicate user");

                        $('#sp_check_result' + sales_user_id).html('<font color=red><i class="fa fa-times"></i></font>');

                    }



                }

            });



        }



        function assignUser(sales_user_id, total) {



            $.ajax({

                type: "POST",

                dataType: "json",

                url: "assign_used_data.php",

                data: {

                    "sales_user_id": sales_user_id,

                    "total": total

                },

                success: function(resp) {

                    if (resp.result == "success") {



                        $('#order_form_content').html(resp.data);

                    } else {

                        alert(resp.msg);

                    }



                }

            });

        }



        function isEmail(email) {

            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

            return regex.test(email);

        }



        function newUserLink() {

            $('#newUserLink').addClass('d-none'); // Hide New User link

            $('#newUserForm').removeClass('d-none');

        }



        function cancelBtn() {

            $('#newUserForm').addClass('d-none'); // Hide the form

            $('#newUserLink').removeClass('d-none');

        }



        function newSubUser() {



            var s_nick_name = $('#new_s_nick_name').val();

            var s_user_name = $('#new_s_user_name_add').val();

            var s_password = $('#new_s_password').val();

            var sub_email = $('#new_sub_email').val();



            if (s_nick_name == "" || s_user_name == "" || s_password == "" || sub_email == "") {



                alert("Please input all info.");

                return false;

            }



            if (!isEmail(sub_email)) {

                alert("Please input correct email.");

                return false;

            }



            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/manage_users/check_salesu.php",

                data: {

                    "s_user_name": window.btoa(s_user_name),

                },

                success: function(resp2) {

                    if (resp2.result == "success") {

                        $('#sp_check_result').html('');

                        $.ajax({

                            type: "POST",

                            dataType: "json",

                            url: "ajax/manage_users/submit_new_sales.php",

                            data: {

                                "s_nick_name": window.btoa(s_nick_name),

                                "s_user_name": window.btoa(s_user_name),

                                "s_password": window.btoa(s_password),

                                "sub_email": window.btoa(sub_email)

                            },

                            success: function(resp) {

                                if (resp.result == "success") {

                                    $('#new_s_nick_name').val("");

                                    $('#new_s_user_name').val("");

                                    $('#new_s_password').val("");

                                    $('#new_sub_email').val("");

                                    if (resp.num_row_su >= <?php echo $maximum_sub_user; ?>) {

                                        $('#new_s_user').remove();

                                    }

                                    $('#manage_su_main_panel').append(resp.inner_new_card);

                                    $('#addSalesUser').modal('hide');

                                }

                            }

                        });



                    } else {

                        alert("Duplicate user");

                        $('#sp_check_result').html('<font color=red><i class="fa fa-times"></i></font>');

                    }



                }

            });



        }



        function cancelEditSU(sales_user_id) {



            var old_s_nick_name = $('#old_s_nick_name' + sales_user_id).val();

            var old_s_user_name = $('#old_s_user_name' + sales_user_id).val();

            var old_s_user_pwd = $('#old_s_user_pwd' + sales_user_id).val();

            var old_sub_email = $('#old_sub_email' + sales_user_id).val();

            $('#td_nick_name' + sales_user_id).html(old_s_nick_name);

            $('#td_s_user_name' + sales_user_id).html(old_s_user_name);

            $('#td_s_user_pwd' + sales_user_id).html(old_s_user_pwd);

            $('#td_sub_email' + sales_user_id).html(old_sub_email);



            var inner = '<div class="d-flex gap-3 justify-content-end">';

            inner += '<div class="goBackBtn">';

            inner += '<button class="btn btn-primary btn_action" onclick="return editSU(' + sales_user_id + ');">Edit</button>';

            inner += '</div>';

            inner += '<div class="goBackBtn">';

            inner += ' <button class="btn btn-primary btn_action" onclick="return assignUser(' + sales_user_id + ');" title="Send user info to Email.">';

            inner += 'Mail';

            inner += '</button>';

            $('#th_btn_zone' + sales_user_id).html(inner);

        }

    

        // Delete assign user 

        $(document).on('click' ,'.deletebtn' ,function(){
           let id = $(this).data('id');
           let user_id = $(this).data('userid');
           let ele = $(this);
            $.ajax({
            url: 'ajax/manage_sales/delete_assigned.php',       // Target PHP file or API endpoint
            type: 'POST',     
            dataType : 'json',
            data: { is_delete : true , id:id ,user_id :user_id}, // Data to send
            success: function(response) {
               assignUser(user_id ,response.count) ;
            },
            error: function(xhr, status, error) {
                   console.error('Error:', error);
            }
            });



        });


        // $(document).ready(function () {

        //     // When New User link is clicked

        //     $('#newUserLink').click(function (e) {

        //         e.preventDefault(); // Prevent default link behavior

        //         $('#newUserLink').addClass('d-none'); // Hide New User link

        //         $('#newUserForm').removeClass('d-none'); // Show the form

        //     });



        //     // When Cancel button is clicked

        //     $('#cancelBtn').click(function (e) {

        //         e.preventDefault(); // Prevent default link behavior

        //         $('#newUserForm').addClass('d-none'); // Hide the form

        //         $('#newUserLink').removeClass('d-none'); // Show New User link

        //     });

        // }); 


        //------------- mail function-----------------
        
    function sendEmailSU(sales_id) {
        $.ajax({

            type: "POST",

            dataType: "json",

            url: "email_sales_person.php",

            data: {
                "sales_id": sales_id
            },

            success: function(resp) {

                if (resp.result == "success") {
                    alert("User info has been sent.");
                } else {

                    alert(resp.msg);

                }



            }

        });



    }
    </script>
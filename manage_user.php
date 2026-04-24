<?php

include('check-session.php');

include('db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;


$sql_select = "SELECT * FROM tbl_sub_user 
               WHERE parent_user_id = ? 
               ORDER BY date_add ASC";

$stmt = $conn->prepare($sql_select);
$stmt->bind_param("i", $user_id); // assuming user_id is integer
$stmt->execute();

$rs_select = $stmt->get_result();


$maximum_sub_user = 20;

?>


<style>
    .manageUSerPage .singleUserBox td,
    .manageUSerPage .singleUserBox th,
    .manageUSerPage .singleUserBox .tableBody {
        padding: 5px 4px !important;
        font-size: 13px;
    }

    .manageUSerPage .userdata {
        padding: 4px;
    }

    .afterEditInput input[type="text"],
    .afterEditInput input[type="email"] {
        width: 100%;
        box-sizing: border-box;
        padding: 6px 8px;
        margin: 0;
    }
</style>


<div class="manageUSerPage">

    <div class="innerMainContent">

        <div class="pageHeader text-center">

            <h2>Manage Users</h2>

            <p>This is where your Users Info will go.</p>

        </div>



        <div class="row userBoxes">

            <div class="note">

                <h6 class="XSmall">Remark</h6>

                <ul>

                    <li>Limit user for 20 accounts.</li>

                    <li> The user is unique for whole system.</li>

                    <li> Limit to use sending email function 3 times a day per user.</li>

                </ul>







            </div>



            <div class=" row p-0" id="manage_su_main_panel">
                <?php

                while ($row_s_user = $rs_select->fetch_assoc()) {

                    $use_class = "";

                    $use_word = "";

                    $use_card_class = "";

                    switch ($row_s_user["enable"]) {

                        case '0':

                            $use_class = "su_status_inactive";

                            $use_word = "Inactive";

                            $use_card_class = "s_user_card_inactive";

                            break;

                        case '1':

                            $use_class = "su_status_active";

                            $use_word = "Active";

                            $use_card_class = "s_user_card";

                            break;
                    }

                ?>
                    <div class="col-md-3" style=" padding: 5px;">
                        <div class="singleUserBox ">

                            <div class="userProfile grid2">

                                <figure class="text-start"><img src="images/vector/userProfile1.png" alt=""></figure>

                                <div class="inactive select">

                                    <select name="format" id=" " class="status-selection" onchange="inactiveSU(<?php echo $row_s_user["sub_user_id"]; ?> ,this)">

                                        <option value="1" <?php if ($row_s_user["enable"] == "1") {
                                                                echo "selected";
                                                            } ?>>Active</option>

                                        <option value="0" <?php if ($row_s_user["enable"] == "0") {
                                                                echo "selected";
                                                            } ?>>Inactive</option>

                                    </select>

                                </div>

                            </div>

                            <div class="userdata">

                                <table class="table border-none overflow-hidden m-0 ">

                                    <tbody id="tableBody  ">

                                        <tr>

                                            <th>Name :</th>

                                            <td class="afterEditInput" id="td_nick_name<?php echo $row_s_user["sub_user_id"]; ?>"><?php echo $row_s_user["nick_name"]; ?></td>

                                        </tr>

                                        <tr>

                                            <th>Username :</th>

                                            <td class="afterEditInput" id="td_s_user_name<?php echo $row_s_user["sub_user_id"]; ?>"><?php echo $row_s_user["s_user_name"]; ?></td>

                                        <tr>

                                            <th>Password :</th>

                                            <td class="afterEditInput" id="td_s_user_pwd<?php echo $row_s_user["sub_user_id"]; ?>"><?php echo $row_s_user["s_user_pwd"]; ?></td>

                                        </tr>

                                        <tr>

                                            <th>Email :</th>

                                            <td class="afterEditInput" id="td_sub_email<?php echo $row_s_user["sub_user_id"]; ?>"><?php echo $row_s_user["sub_email"]; ?></td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            <div class="userContact" id="th_btn_zone<?php echo $row_s_user["sub_user_id"]; ?>">

                                <div class="d-flex gap-3 justify-content-end">

                                    <div class="">



                                        <button type="button" class="btn themeBtn2grey iconBTn XSmall" onclick="return editSU(<?php echo $row_s_user["sub_user_id"]; ?>);">

                                            <figure class="m-0"><img src="images/vector/editSmall.png" alt=""></figure> Edit

                                        </button>

                                    </div>

                                    <div class="    ">

                                        <!-- <a href="mailto:mailm@ail.com"

                                            class="goback d-flex  gap-3 justify-content-between">

                                            <figure class="m-0"><img src="images/vector/mail.png" alt=""></figure> Mail

                                        </a> -->

                                        <button type="button" class="btn themeBtn2grey iconBTn XSmall" onclick="return sendEmailSU(<?php echo $row_s_user["sub_user_id"]; ?>);" title="Send user info to Email.">

                                            <figure class="m-0"><img src="images/vector/email.png" alt=""></figure> Mail

                                        </button>



                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                <?php

                }

                ?>

                <div class="col-md-3">
                    <div class="singleUserBox d-flex align-items-center justify-content-center flex-row addNewUser">



                        <a href="#" class="d-flex   iconBTn gap-3" id="newUserLink" onclick="newUserLink()">

                            <figure class="m-0"><img src="images/vector/addBlue.png" alt=""></figure> New User

                        </a>

                        <div class="newUserForm d-none w-100 " id="newUserForm">

                            <form action="">

                                <div class="formTitle d-flex align-items-center justify-content-between mb-4 ">

                                    <h6 class="Small m-0">Details : </h6>

                                    <h6 class="Small m-0 border">New User</h6>

                                </div>

                                <fieldset class="grid2 singleFrom">

                                    <div class="form-group  ">

                                        <!-- <label for="">Name</label> -->

                                        <input type="text" name="name" id="new_s_nick_name" placeholder="Name">

                                    </div>

                                    <div class="form-group">

                                        <input type="text" name="Username" id="new_s_user_name" onkeypress="$('#sp_check_result').html('');" placeholder="Username">

                                        <span id="sp_check_result" style="font-size: 16px; padding-left: 5px;"></span>

                                    </div>

                                    <div class="form-group column2">

                                        <input type="email" name="Email" id="new_sub_email" placeholder="Email">

                                    </div>

                                    <div class="form-group column2">

                                        <input type="password" name="Password" id="new_s_password" placeholder="Password">

                                    </div>

                                </fieldset>

                                <div class="userContact">

                                    <div class="d-flex gap-3 justify-content-end">

                                        <!-- Cancel Button -->

                                        <div class="goBackBtn">

                                            <a href="#" class="goback iconBTn d-flex gap-3 justify-content-between"

                                                id="cancelBtn" onclick="cancelBtn()">

                                                <figure class="m-0"><img src="images/vector/cancel.png" alt="">

                                                </figure> Cancel

                                            </a>

                                        </div>

                                        <!-- Save Button -->

                                        <div class="goBackBtn">

                                            <a href="#" class="goback iconBTn d-flex gap-3 justify-content-between" onclick="return newSubUser(this);">

                                                Save <figure class="m-0"><img src="images/vector/saveGreen.png" alt="">

                                                </figure>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>





<script>
    function editSU(sub_user_id) {



        var edit_td_nick_name = '<input type="text" id="edit_s_nick_name' + sub_user_id + '" value="' + $('#td_nick_name' + sub_user_id).html() + '" style="width: 85%;" maxlength="80">';

        edit_td_nick_name += '<input type="hidden" id="old_s_nick_name' + sub_user_id + '" value="' + $('#td_nick_name' + sub_user_id).html() + '">';

        $('#td_nick_name' + sub_user_id).html(edit_td_nick_name);



        var edit_td_s_user_name = '<input type="text" id="edit_s_user_name' + sub_user_id + '" value="' + $('#td_s_user_name' + sub_user_id).html() + '" style="width: 85%;" maxlength="30" onkeypress="$(\'#sp_check_result' + sub_user_id + '\').html(\'\');">';

        edit_td_s_user_name += '<input type="hidden" id="old_s_user_name' + sub_user_id + '" value="' + $('#td_s_user_name' + sub_user_id).html() + '">';

        edit_td_s_user_name += '<span id="sp_check_result' + sub_user_id + '" style="font-size: 16px; padding-left: 5px; display:block; "></span>';

        $('#td_s_user_name' + sub_user_id).html(edit_td_s_user_name);



        var edit_td_s_user_pwd = '<input type="text" id="edit_s_user_pwd' + sub_user_id + '" value="' + $('#td_s_user_pwd' + sub_user_id).html() + '" style="width: 85%;" maxlength="80">';

        edit_td_s_user_pwd += '<input type="hidden" id="old_s_user_pwd' + sub_user_id + '" value="' + $('#td_s_user_pwd' + sub_user_id).html() + '">';

        $('#td_s_user_pwd' + sub_user_id).html(edit_td_s_user_pwd);



        var edit_td_sub_email = '<input type="text" id="edit_sub_email' + sub_user_id + '" value="' + $('#td_sub_email' + sub_user_id).html() + '" style="width: 85%;" maxlength="80">';

        edit_td_sub_email += '<input type="hidden" id="old_sub_email' + sub_user_id + '" value="' + $('#td_sub_email' + sub_user_id).html() + '">';

        $('#td_sub_email' + sub_user_id).html(edit_td_sub_email);



        var inner = '<button class="btn themeBtn2grey mx-3 iconBTn btn_action" onclick="return saveEditSU(' + sub_user_id + ');">  <figure class="m-0"><img src="images/vector/saveGreen.png" alt=""></figure> Save</button>';

        inner += '<button class="btn  themeBtn2grey iconBTn  btn_action" onclick="return cancelEditSU(' + sub_user_id + ');"><figure class="m-0"><img src="images/vector/cancel.png" alt=""></figure> Cancel</button>';

        $('#th_btn_zone' + sub_user_id).html(inner);

    }



    function saveEditSU(sub_user_id) {

      let isValid = CheckFormValidation(true , sub_user_id); 

      if(!isValid){
          return false ; 
      }

      $('.duplicate_span').remove(); 


        var s_nick_name = $('#edit_s_nick_name' + sub_user_id).val();

        var s_user_name = $('#edit_s_user_name' + sub_user_id).val();

        var s_user_pwd = $('#edit_s_user_pwd' + sub_user_id).val();

        var sub_email = $('#edit_sub_email' + sub_user_id).val();

        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/manage_users/check_edit_su.php",

            data: {

                "s_user_name": window.btoa(s_user_name),

                "sub_user_id": sub_user_id , 
                email : sub_email , 

            },

            success: function(resp2) {

                if (resp2.result == "success") {



                    $('#sp_check_result').html('');
                    $('#sp_check_result' + sub_user_id ).html('');



                    $.ajax({

                        type: "POST",

                        dataType: "json",

                        url: "ajax/manage_users/submit_edit_su.php",

                        data: {

                            "s_nick_name": window.btoa(s_nick_name),

                            "s_user_name": window.btoa(s_user_name),

                            "s_password": window.btoa(s_user_pwd),

                            "sub_email": window.btoa(sub_email),

                            "sub_user_id": sub_user_id

                        },

                        success: function(resp) {

                            if (resp.result == "success") {



                                $('#td_nick_name' + sub_user_id).html(s_nick_name);

                                $('#td_s_user_name' + sub_user_id).html(s_user_name);

                                $('#td_s_user_pwd' + sub_user_id).html(s_user_pwd);

                                $('#td_sub_email' + sub_user_id).html(sub_email);



                                var inner = '<div class="d-flex gap-3 justify-content-end">';

                                inner += '<div class=" ">';

                                inner += '<button class="btn themeBtn2grey iconBTn XSmall btn_action" onclick="return editSU(' + sub_user_id + ');"><figure class="m-0"><img src="images/vector/editSmall.png" alt=""></figure> Edit</button>';

                                inner += '</div>';

                                inner += '<div class=" ">';

                                inner += ' <button class="btn themeBtn2grey iconBTn XSmall btn_action" onclick="return sendEmailSU(' + sub_user_id + ');" title="Send user info to Email.">';

                                inner += '<figure class="m-0"><img src="images/vector/email.png" alt=""></figure> Mail';

                                inner += '</button>';

                                inner += '</div>';

                                $('#th_btn_zone' + sub_user_id).html(inner);



                            } else {

                                alert(resp.msg);

                            }



                        }

                    });



                } else {

                    alert("Duplicate user");

                
                        if(resp2.username =='Fail'){
                         $('#sp_check_result' + sub_user_id).html('<font color=red><i class="fa fa-times"></i></font>');
                    }

                    if(resp2.email =='Fail'){
            
                        $('#edit_sub_email' + sub_user_id).after('<p class="duplicate_span"><font color=red><i class="fa fa-times"></i></font></p>');
                    }

                }



            }

        });



    }



    function sendEmailSU(sub_user_id) {



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "send_sub_user_info.php",

            data: {

                "sub_user_id": sub_user_id

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


        function base64EncodeUnicode(str) {
                return btoa(
                    new TextEncoder().encode(str)
                    .reduce((data, byte) => data + String.fromCharCode(byte), '')
                );
        }




    function newSubUser(element) {

       let isValid = CheckFormValidation(); 

        var s_nick_name = $('#new_s_nick_name').val();

        var s_user_name = $('#new_s_user_name').val();

        var s_password = $('#new_s_password').val();

        var sub_email = $('#new_sub_email').val();

        
   
       
        if(!isValid){
             return false ; 
        }
 
        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/manage_users/check_su.php",

            data: {

                "s_user_name":base64EncodeUnicode(s_user_name),
                email :sub_email , 

            },

            success: function(resp2) {
               $('.duplicate_span').remove(); 
                if (resp2.result == "success") {
                    $('#sp_check_result').html('');

                    $.ajax({

                        type: "POST",

                        dataType: "json",

                        url: "ajax/manage_users/submit_new_su.php",

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




                                //---Remove add panel when reach to maximum limit of sub user

                                if (resp.num_row_su >= <?php echo $maximum_sub_user; ?>) {

                                    $('#new_s_user').remove();

                                }


                               



                               $('#manage_su_main_panel').append(resp.inner_new_card);



                            }



                        }

                    });



                } else {

                    alert("Duplicate user");
                  
                    if(resp2.username =='Fail'){
                        $('#sp_check_result').html('<font color=red><i class="fa fa-times"></i></font>');
                    }

                    if(resp2.email =='Fail'){
               
                        $('#new_sub_email').after('<span class="duplicate_span"><font color=red><i class="fa fa-times"></i></font></span>');
                    }


                }



            }

        });



    }

    

    
        const VALIDATION_PATTERNS = {
        text: /^[a-zA-Z\s]{2,80}$/, // Letters and spaces only
        tel: /^[0-9+\-\s\(\)]{3,30}$/, // Numbers, +, -, spaces, parentheses
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ // Standard email format
    };


   function clearValidationErrors() {
         $('.error_msg').remove(); 

    }

      function showFieldError(fieldId, message) {
        let errorMsg = `<p class="error_msg" style="color:red; margin:5px 0 0;"> ${message} </p>` ; 
        fieldId.after(errorMsg) ; 

      }


            function validateField(fieldId, pattern, msg) {
                        let value = fieldId.val().trim();

                        if (value === "" || value === null || value === undefined) {
                            showFieldError(fieldId, msg);
                            return false;
                        }

                        if (pattern && !pattern.test(value)) {
                            showFieldError(fieldId, msg);
                            return false;
                        }

                        return true; // ✅ IMPORTANT FIX
            }

            function CheckFormValidation(edit = false , sub_user_id = 0 ) {
                clearValidationErrors();

                let isValid = true;

                let name = $('#new_s_nick_name');
                let username = $('#new_s_user_name');
                let email = $('#new_sub_email');
                let password = $('#new_s_password');

                if(edit==true){
                    name  = $('#edit_s_nick_name' + sub_user_id);
                    s_user_name = $('#edit_s_user_name' + sub_user_id) ; 
                    password = $('#edit_s_user_pwd' + sub_user_id);
                    email = $('#edit_sub_email' + sub_user_id);
                }

                isValid = validateField(name, VALIDATION_PATTERNS.text, 'Invalid Name') && isValid;

                isValid = validateField(username, null , 'Invalid Username') && isValid;

                isValid = validateField(email, VALIDATION_PATTERNS.email, 'Invalid Email') && isValid;

                // skip password validation in edit mode if empty
                if (!edit || $.trim(password.val()) !== '') {
                    isValid = validateField(password, null ,  'Invalid Password') && isValid;
                }

                return isValid;
            }
    


    function cancelEditSU(sub_user_id) {



        var old_s_nick_name = $('#old_s_nick_name' + sub_user_id).val();

        var old_s_user_name = $('#old_s_user_name' + sub_user_id).val();

        var old_s_user_pwd = $('#old_s_user_pwd' + sub_user_id).val();

        var old_sub_email = $('#old_sub_email' + sub_user_id).val();

        $('#td_nick_name' + sub_user_id).html(old_s_nick_name);

        $('#td_s_user_name' + sub_user_id).html(old_s_user_name);

        $('#td_s_user_pwd' + sub_user_id).html(old_s_user_pwd);

        $('#td_sub_email' + sub_user_id).html(old_sub_email);



        var inner = '<div class="d-flex gap-3 justify-content-end">';

        inner += '<div class=" ">';

        inner += '<button class="btn iconBTn themeBtn2grey btn_action" onclick="return editSU(' + sub_user_id + ');"><figure class="m-0"><img src="images/vector/editSmall.png" alt=""></figure> Edit</button>';

        inner += '</div>';

        inner += '<div class=" ">';

        inner += ' <button class="btn iconBTn themeBtn2grey  btn_action" onclick="return sendEmailSU(' + sub_user_id + ');" title="Send user info to Email.">';

        inner += '<figure class="m-0"><img src="images/vector/email.png" alt=""></figure> Mail';

        inner += '</button>';

        $('#th_btn_zone' + sub_user_id).html(inner);

    }

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
    function inactiveSU(sub_user_id, ele) {
        let status = ele.value;
        console.log(status);

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "ajax/manage_users/inactive_su.php",
            data: {
                "sub_user_id": sub_user_id,
                status: status
            },
            success: function(resp) {
                if (resp.result == "success") {

                }

            }
        });

    }
</script>
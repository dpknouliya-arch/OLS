<?php
session_start();
require_once __DIR__ . '/db.php';

$brand = [
    'name'       => 'Bauer Hockey',
    'logo'       => 'images/logo/bauerLogoWhite.webp',
    'login_logo' => 'assets/images/auth/bauerLogoBlack.webp',
    'favicon'    => 'images/logo/bauerFeviconIcon.png',
    'page_title' => 'Bauer Online Services',
    'copyright'  => 'Copyright &copy; ' . date('Y') . ' Bauer Hockey. All rights reserved.',    
];

?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($brand['page_title']) ?> — Login</title>
    <!-- Bootstrap 5.3.x CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Css  -->
    <link rel="stylesheet" href="Style/main.css">
    <link rel="stylesheet" href="Style/default.css">
    <link rel="stylesheet" href="Style/resposive.css">
    <!-- Css  -->
    <!-- faviconIcon -->
    <link rel="icon" type="image/x-icon" href="<?= $brand['favicon'] ?>">
    <!-- faviconIcon -->
    <style>
        body {
            overflow: hidden;
        }

        .brandLogo {
            width: 8vw;
            margin-bottom: 0;
            padding: 1vw;
            border-radius: 4px;
        }

        .mainLogin .userLogin,
        .SalesLogin.grid2 {
            grid-template-columns: 60vw auto;
        }

        .themeBtn2grey,
        .input-group-text {
            text-align: center;
        }

        @media screen and (max-width:1250px) {

            .brandLogo {
                width: 20vw;
            }

        }
    </style>
</head>

<body>
    <section class="mainLogin p-0">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="userLogin grid2" id="userLogin">
                    <div class="leftSide ">
                        <video autoplay muted loop class="backgroundVideo">
                            <source src="video/02.mp4" type="video/mp4">
                            <source src="video/02.ogg" type="video/ogg">
                            Your browser does not support the video tag.
                        </video>
                        <!-- Your content here -->
                    </div>

                    <div class="rightSide  justify-content-center   d-flex align-items-center">

                        <div class="card bg-none d-flex border-none  h-100  align-items-center  justify-content-center">

                            <form id="loginForm">

                                <div class="text-center w-100 ">
                                    <figure class="text-center mb-0"><img src="<?= $brand['login_logo'] ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="brandLogo"></figure>
                                    <h6 class="formTitle">LOGIN AS <?= strtoupper(htmlspecialchars($brand['name'])) ?> USER</h6>
                                </div>
                                <div class="form-group" style="padding-bottom:4px;">
                                    <div class="input-group" style="margin: 0 0 8px 0px;">
                                        <label for="">Email</label>
                                        <div class="input-group-prepend d-flex position-relative">
                                            <span class="input-group-text">
                                                <ion-icon name="mail-outline"></ion-icon>
                                            </span>
                                            <input type="text" name="user_email" id="user_email" class="form-control" placeholder="Email" value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="padding-bottom:4px;">
                                    <div class="input-group" style="margin: 0 0 8px 0px;">
                                        <label for="">Password</label>
                                        <div class="input-group-prepend d-flex position-relative">
                                            <span class="input-group-text">
                                                <ion-icon name="lock-closed-outline"></ion-icon>
                                            </span>
                                            <input type="password" name="user_password" id="user_password" class="form-control" placeholder="Password" value="">
                                        </div>
                                    </div>
                                    <p id="err_msg" style="display:none;" class="footer-text text-danger"><br>Username Or Password Incorrect</p>
                                </div>
                                <hr>
                                <br>
                                <div class="form-group">
                                    <div class="grid2">
                                        <button type="submit" class="themeBtn border-none  text-center  submit-btn">Login</button>
                                        <span class="link_cls themeBtn2grey userForgotPasswordBtn" id="userForgotPasswordBtn">Forgot password?</span>
                                        <div class="forGetPassNote column2" style="display: none;">
                                            <!-- Initially hidden -->
                                            <div class="form-group" style="padding-bottom: 4px;">
                                                <div class="input-group" style="margin: 0 0 15px 0px;">
                                                    <label for="" class="XSmall">You will get an email with link inside.
                                                        And you can change password by following that link</label>
                                                    <div class="input-group-prepend d-flex position-relative">
                                                        <span class="input-group-text" onclick="return requestResetPassword();">
                                                            <ion-icon name="send-outline"></ion-icon>
                                                        </span>
                                                        <input type="text" id="forgot_email" class="form-control" placeholder="Email" value="" onkeyup="checkForgotEmail();">
                                                        <span id="email_format_result"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-none">
                                            <p class="text-center column2 mb-0">or</p>
                                            <a class="btn  teamMemberLoginBtn team_m_login column2" href="#teamMemberLogin">Team Members - Login Here</a>
                                            <a href="#SalesLogin" class="btn salesLoginOls team_m_login column2" id="salesloginform">Login as Sales</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper mt-5 text-gray">
                                    <p class="footer-text XSmall"><?= $brand['copyright'] ?></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row teamMemberLogin p-0 grid2" id="teamMemberLogin">
                    <div class="leftSide mobileShow ">
                        <video autoplay muted loop class="backgroundVideo">
                            <source src="video/01.mp4" type="video/mp4">
                            <source src="video/01.ogg" type="video/ogg">
                            Your browser does not support the video tag.
                        </video>
                        <!-- Your content here -->
                    </div>
                    <div class="rightSide  justify-content-center  d-flex align-items-center">
                        <div class="card bg-none d-flex border-none  h-100  align-items-center  justify-content-center">
                            <form id="loginFormSub">
                                <div class="text-center w-100 ">
                                    <figure class="text-center mb-0"><img src="<?= $brand['login_logo'] ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="brandLogo"></figure>
                                    <h6 class="formTitle">LOGIN AS TEAM MEMBER</h6>
                                </div>
                                <div class="form-group" style="padding-bottom:4px;">
                                    <div class="input-group" style="margin: 0 0 15px 0px;">
                                        <label for="">User Name </label>
                                        <div class="input-group-prepend d-flex position-relative">
                                            <span class="input-group-text">
                                                <ion-icon name="mail-outline"></ion-icon>
                                            </span>
                                            <input type="text" name="user_name" id="user_name_sub" class="form-control" placeholder="Username">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="padding-bottom:4px;">
                                    <div class="input-group" style="margin: 0 0 15px 0px;">
                                        <label for="">Password</label>
                                        <div class="input-group-prepend d-flex position-relative">
                                            <span class="input-group-text">
                                                <ion-icon name="lock-closed-outline"></ion-icon>
                                            </span>
                                            <input type="password" name="user_password" id="user_password_sub" class="form-control" placeholder="Password">
                                        </div>
                                    </div>
                                    <p id="err_msg" style="display:none;" class="footer-text text-danger"> Username Or Password Incorrect</p>
                                </div>
                                <hr>
                                <br>
                                <div class="form-group">
                                    <div class="grid2">
                                        <button type="submit" class="themeBtn border-none submit-btn  column2" >Login</button>
                                        <p class="text-center column2 mb-0">or</p>
                                        <a class="btn teamMemberLoginBtn team_m_login column2" href="#userLogin"> User - Login Here</a>
                                        <a href="#SalesLogin" class="btn salesLoginOls team_m_login column2" id="salesloginform">Login as Sales</a>
                                    </div>
                                </div>
                                <div class="wrapper mt-5 text-gray">
                                    <p class="footer-text XSmall"><?= $brand['copyright'] ?></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="leftSide ">
                        <video autoplay muted loop class="backgroundVideo">
                            <source src="video/01.mp4" type="video/mp4">
                            <source src="video/01.ogg" type="video/ogg">
                            Your browser does not support the video tag.
                        </video>
                        <!-- Your content here -->
                    </div>
                </div>

                <div class="SalesLogin grid2" id="SalesLogin">
                    <div class="leftSide ">
                        <video autoplay muted loop class="backgroundVideo">
                            <source src="video/03.mp4" type="video/mp4">
                            <source src="video/03.ogg" type="video/ogg">
                            Your browser does not support the video tag.
                        </video>
                        <!-- Your content here -->
                    </div>
                    <div class="rightSide  justify-content-center   d-flex align-items-center">
                        <div class="card bg-none d-flex border-none  h-100  align-items-center  justify-content-center">
                            <form id="loginFormSeles">
                                <div class="text-center w-100 ">
                                    <figure class="text-center mb-0"><img src="<?= $brand['login_logo'] ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="brandLogo"></figure>
                                    <h6 class="formTitle">LOGIN AS SALES</h6>
                                </div>
                                <div class="form-group" style="padding-bottom:4px;">
                                    <div class="input-group" style="margin: 0 0 15px 0px;">
                                        <label for="">Username</label>
                                        <div class="input-group-prepend d-flex position-relative">
                                            <span class="input-group-text">
                                                <ion-icon name="mail-outline"></ion-icon>
                                            </span>
                                            <input type="text" name="user_name_sales" id="user_name_sales" class="form-control" placeholder="Username" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="padding-bottom:4px;">
                                    <div class="input-group" style="margin: 0 0 15px 0px;">
                                        <label for="">Password</label>
                                        <div class="input-group-prepend d-flex position-relative">
                                            <span class="input-group-text">
                                                <ion-icon name="lock-closed-outline"></ion-icon>
                                            </span>
                                            <input type="password" name="user_password_sales" id="user_password_sales" class="form-control" placeholder="Password" value="">
                                        </div>
                                    </div>
                                    <p id="err_msg" style="display:none;" class="footer-text text-danger"> Username Or Password Incorrect</p>
                                </div>
                                <hr>
                                <br>
                                <div class="form-group">
                                    <div class="grid2">
                                        <button type="submit" class="themeBtn border-none  text-center  submit-btn column2">Login</button>
                                        <!-- <span class="link_cls themeBtn2grey userForgotPasswordBtn" id="userForgotPasswordBtn">Forgot password?</span>
                                        <div class="forGetPassNote column2" style="display: none;">                                         
                                            <div class="form-group" style="padding-bottom: 4px;">
                                                <div class="input-group" style="margin: 0 0 15px 0px;">
                                                    <label for="" class="XSmall">You will get an email with link inside. And you can change password by following that link</label>
                                                    <div class="input-group-prepend d-flex position-relative">
                                                        <button class="input-group-text">
                                                            <ion-icon name="send-outline"></ion-icon>
                                                        </button>
                                                        <input type="text" name="user_email" id="user_email" class="form-control" placeholder="Email" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <p class="text-center column2 mb-0">or</p>
                                        <a class="btn teamMemberLoginBtn  team_m_login column2" href="#teamMemberLogin">Team Members - Login Here</a>
                                        <a class="btn   salesLoginOls  team_m_login column2" href="#userLogin"> User - Login Here</a>
                                    </div>
                                </div>
                                <div class="wrapper mt-5 text-gray">
                                    <p class="footer-text XSmall"><?= $brand['copyright'] ?></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalForgotPassword" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 15px 16px; background-color: #FFF;">
                    <h5 class="modal-title h5-modal-input-title">Forgot Password</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 10px 16px 15px 16px;">

                    <div class="row register_form" id="main_panel">
                        <div class="col-12 text-center">You will get an email with link inside.<br>And you can change password by following that link.</div>
                        <div class="col-4 text-right">Email:</div>
                        <div class="col-8"><input type="text" id="forgot_email"> <span id="email_format_result"></span></div>
                    </div>
                </div>
                <div class="modal-footer" style=" background-color: #FFF;">
                    <button id="btn_submit_request" type="button" class="btn btn-info" onclick="return requestResetPassword();">Request Reset Password</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add new  Team modal  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <!-- ionIcons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- ionIcons -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        $(document).ready(function() {
            $('.userForgotPasswordBtn').click(function() {
                $('.forGetPassNote').fadeToggle(300); // Adjust duration as needed
            })
        });
        $(document).ready(function() {
            $('.MemberForgotPasswordBtn').click(function() {
                $('.forGetPassNote').fadeToggle(300); // Adjust duration as needed
            });
        });
    </script>



    <script type="text/javascript">
        $(document).on('keypress', function(e) {
            if (e.which == 13) {
                signIn();
            }
        });



        function isEmail(email) {

            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

            return regex.test(email);

        }

        function checkForgotEmail() {
            var email = $('#forgot_email').val();
            if (!isEmail(email)) {
                $('#email_format_result').html('<i class="fa fa-times-circle" style="color:#F55;" title="Invalid format."></i>');
            } else {

                $('#email_format_result').html('<i class="fa fa-check-circle" style="color:#5F5;"></i>');

            }
        }



        function requestResetPassword() {
            var email = $('#forgot_email').val();
            if (!isEmail(email)) {
                alert("Please input email with correct format.");
                return false;
            }
            $('#btn_submit_request').html("Sending email...");
            $('#btn_submit_request').prop("disabled", true);
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "request_reset_password.php",
                data: {
                    "email": window.btoa(email),
                    "brand_id": 2
                },
                success: function(resp) {
                    if (resp.result == "success") {
                        $('#btn_submit_request').html("Email has been sent.");
                        alert("Email has been sent.");
                    } else {
                        alert(resp.msg);
                        $('#btn_submit_request').html("Request Reset Password");
                        $('#btn_submit_request').prop("disabled", false);
                    }
                }
            });
        }



        function checkRegisterEmail() {
            var email = $('#regis_email').val();
            if (!isEmail(email)) {
                //alert("Please input email with correct format.");
                $('#dup_result').html('<i class="fa fa-times-circle" style="color:#F55;" title="Invalid format."></i>');
            } else {
                $('#dup_result').html('<i class="fa fa-check-circle" style="color:#5F5;"></i>');
            }
        }
        <?php

        /*

	function submitRegister(){

		

		var email = $('#regis_email').val();



		if(!isEmail(email)){

			alert("Please input email with correct format.");

			return false;

		}



		$.ajax({  

            type: "POST",  

            dataType: "json", 

            url:"ajax/main/check_email_duplicate.php" ,

            data:{

                "chk_email": window.btoa(email)

            },

            success: function(resp){ 



                if(resp.result=="dup"){

                    alert("Not allow this Email");

					return false;

                }else{

                    var fullname = $('#fullname').val();



                    $('#btn_submit_register').html("Loading...");

                    $('#btn_submit_register').prop("disabled",true);



					$.ajax({  

			            type: "POST",  

			            dataType: "json", 

			            url:"save_register.php" ,

			            data:{

			                "email": window.btoa(email),

			                "full_name": window.btoa(fullname)

			            },

			            success: function(resp2){

			                if(resp2.result=="success"){

			                    $('#main_panel').html('<center style="width:100%;"><b>Please check your mail box for confirm your Email.<br>(Try to see in junk if not see in mail box)</b></center>');

			                    $('#btn_submit_register').html("Saved!!");

			                }else{

			                    alert(resp2.msg);

			                    $('#btn_submit_register').html("Submit");

			                    $('#btn_submit_register').prop("disabled",false);



			                }

			            }  

			        });

                }



            }  

        });



	}

	*/

        ?>

        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                signIn();
            });
        });

        $(document).ready(function() {
            $('#loginFormSeles').submit(function(e) {
                e.preventDefault();
                signInSales();
            });
        });

        $(document).ready(function() {
            $('#loginFormSub').submit(function(e) {
                e.preventDefault();
                signInsub();
            });
        });





        function signInsub() {
            var user_name = $('#user_name_sub').val();
            var user_password = $('#user_password_sub').val();

            if (user_name == '' || user_password == '') {
                alert('Please input User name and Password.');
                return false;
            }

            let errMsg= $('#loginFormSub').find('#err_msg'); 
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "ajax/main/check_login_sub.php",
                data: {
                    "user": window.btoa(user_name),
                    "password": window.btoa(user_password)
                },
                success: function(resp) {
                    //alert(resp.result);
                    if (resp.result == "success") {
                        errMsg.hide();
                        //alert("success");
                        window.location.href = "./?vp=<?php echo base64_encode("order_form_sub"); ?>";
                    } else {
                        errMsg.show();
                    }
                }

            });

        }



        function signIn() {
            var user_email = $('#user_email').val();
            var user_password = $('#user_password').val();
            if (user_email == '' || user_password == '') {
                alert('Please input Email.');
                return false;
            }
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "ajax/main/check_login.php",
                data: {
                    "user": window.btoa(user_email),
                    "password": window.btoa(user_password),
                    "brand_id": 2
                },
                success: function(resp) {
                    if (resp.result == "success") {
                        $('#err_msg').hide();
                       
                        //alert("success");
                        // apiLoginJS(user_email, user_password)
                        //     .then(apiResp => {

                        //         console.log(apiResp);

                        //         if (apiResp.token) {
                        //             localStorage.setItem("API_TOKEN", apiResp.token); // store in browser
                        //         }

                        //         window.location.href = "./?vp=ZGFzaGJvcmFkTWFpbg==";
                        //     });



                        if (resp.first_login != 0) {
                            window.location.href = "./?vp=ZGFzaGJvcmFkTWFpbg==";
                        } else {
                            window.location.href = "./?vp=ZGFzaGJvcmFkTWFpbg==";
                        }
                    } else {
                        $('#err_msg').show();
                    }
                }

            });

        }

        // function apiLoginJS(email, password) {
        //     return fetch("api/login.php", {
        //         method: "POST",
        //         headers: {
        //             "Content-Type": "application/json"
        //         },
        //         body: JSON.stringify({
        //             email: email,
        //             password: password
        //         })
        //     })
        //     .then(res => res.json());
        // }



        function signInSales() {
            var user_name_sales = $('#user_name_sales').val();
            var user_password_sales = $('#user_password_sales').val();
            if (user_name_sales == '' || user_password_sales == '') {
                alert('Please input Username.');
                return false;
            }

            let errorMsg = $('#loginFormSeles').find('#err_msg');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "ajax/main/sales_login.php",
                data: {
                    "username": window.btoa(user_name_sales),
                    "password": window.btoa(user_password_sales)
                },
                success: function(resp) {
                    if (resp.result == "success") {
                        errorMsg.hide();
                        //alert("success");
                        if (resp.first_login != 0) {
                            window.location.href = "./?vp=<?php echo base64_encode("sales_dash"); ?>";
                        } else {
                            window.location.href = "./?vp=<?php echo base64_encode("sales_dash"); ?>";
                        }
                    } else {
                        errorMsg.show();
                    }
                }
            });
        }
    </script>



</body>



</html>
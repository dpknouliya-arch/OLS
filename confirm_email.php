<?php
if (!isset($_GET["key"]) || ($_GET["key"] == "")) {
	echo "Invalid access page";
	exit();
}

include('db.php');

$sql_select = "SELECT user_id,user_email FROM tbl_user WHERE activate_key='" . $_GET["key"] . "' ; ";

$rs_user = $conn->query($sql_select);
$num_row = $rs_user->num_rows;

if ($num_row == 0) {
	echo "Invalid access page";
	exit();
}

$row_user = $rs_user->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home</title>
	<!-- Bootstrap 5.3.x CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Css  -->
	<link rel="stylesheet" href="Style/main.css">
	<link rel="stylesheet" href="Style/default.css">
	<link rel="stylesheet" href="Style/sidebar.css">
	<link rel="stylesheet" href="Style/login.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style type="text/css">
		.link_cls {
			text-decoration: underline;
			cursor: pointer;
			padding-left: 15px;
			font-size: 12px;
			color: #00F;
		}

		.register_form div {
			padding: 10px;
		}

		.leftSide {
			background: url('../assets/images/auth/mailForgetBanner.jpg');
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
			height: 100vh;
		}

		.input-group {
			flex-direction: column;
			gap: 12px;
		}

		.input-group input,
		.input-group input::placeholder {
			width: 100% !important;
			font-size: 12px;
			background: rgba(255, 255, 255, 0.2) !important;
			box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1) !important;
			backdrop-filter: blur(5px) !important;
			-webkit-backdrop-filter: blur(5px) !important;
			border: 1px solid rgba(255, 255, 255, 0.3) !important;
		}

		.input-group input {
			padding: 10px 55px !important
		}


		.input-group-text {
			position: absolute;
			left: -2px;
			top: 1px;
			border-radius: 0 3px 3px 0;
			height: 40px;
			z-index: 100;
		}



		label {
			font-size: 14px;
		}

		.form-group {
			margin-bottom: 20px;
		}

		.grid2 {
			grid-template-columns: 55vw auto;
		}



		.formTitle {
			font-size: 20px;
			text-align: center;
			text-transform: uppercase;
		}

		.item1 .input-group-text {
			top: 33px;
		}

		.lockIcn {
			background: var(--black-bg);
			padding: 1.5vw;
			color: #FFF;
			font-size: 30px;
			border-radius: 50%;
			display: flex;
			margin: 10px auto;
		}

		.brandLogo {
			width: 10vw;
			margin-bottom: 10px;
			padding: 1.5vw;
			border-radius: 4px;

		}

		.themeBtn {
			background: #000000;
		}

		#form_change_password {
			width: 30vw;
		}

		@media screen and (max-width:1250px) {
			.grid2 {
				grid-template-columns: 60vw auto;

			}

		}

		@media screen and (max-width:1250px) {
			.grid2 {
				grid-template-columns: 1fr;

			}

			.brandLogo {
				width: 25vw;
				margin-bottom: 24px;
				margin-top: -70px;
				background: #FFF;
				padding: 4vw;
			}

			.leftSide {
				background-position: top;
				height: 40vh;
			}

			#form_change_password {
			    width: 90vw;
			}

			.formTitle {
                 font-size: 16px; 
			}
		}
	</style>
</head>

<body>
	<div class="forgetPssLogin grid2" id="">
		<div class="leftSide ">

		</div>

		<div class="rightSide  justify-content-center   d-flex align-items-center">
			<div class="card bg-none d-flex border-none  h-100  align-items-center  justify-content-center">
				<form id="form_change_password">
					<figure class="text-center mb-0"><img src="assets/images/auth/Joglogosymbol.png" alt="" class="brandLogo"></figure>

					<!-- <ion-icon name="lock-closed-outline" class="lockIcn md hydrated" role="img"></ion-icon> -->
					<h5 class="mr-auto formTitle">JOG Online services.</h5>
					<p class="mb-3 mr-auto text-center ">Please determine your own password.</p>
					<div class="form-group">
						<div class="input-group  position-relative">
							<label for="">Enter New Password</label>

							<div class="input-group-prepend item1">
								<span class="input-group-text ">
									<i class="fa fa-key" aria-hidden="true"></i>
								</span>
								<input type="password" name="new_password" id="new_password" class="form-control" placeholder="Password">
								<input type="hidden" name="user_id" value="<?php echo $row_user["user_id"]; ?>">
								<input type="hidden" name="confirm_key" value="<?php echo $_GET["key"]; ?>">

								<input type="hidden" name="is_ios"  id="is_ios"  value="0">
							</div>

						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="">Confirm New Password </label>
							<div class="input-group-prepend position-relative">
								<span class="input-group-text">
									<i class="fa fa-check-circle-o" aria-hidden="true"></i>
								</span>
								<input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password">

							</div>
						</div>
					</div>
					<div class="form-group">
						<button type="button" class="themeBtn border-none  text-center  submit-btn w-100 " onclick="return submitSignIn();">
							Submit & Sign In 
						      <span class="spinner-border d-none  loader_span"></span> 
						</button>
					</div>
					<div class="wrapper mt-5 text-gray">
						<p class="footer-text">
							Copyright © 2020 JOGSPORTS. All rights reserved.
						</p>
					</div>
				</form>
			</div>
		</div>
	</div>


	<!-- plugins:js -->
	<script type="text/javascript" src="ajax/assets/bootstrap-4.4.1/js/bootstrap.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

	<!-- ionIcons -->
	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<script src="js/main.js"></script>
	<!-- endinject -->
	<!-- Custom js for this page-->
	<!-- End custom js for this page-->
	<script type="text/javascript" src="https://code.jquery.com/ui/jquery-ui-git.js"></script>
	<script type="text/javascript">
		function submitSignIn() {
          let is_ios = isIOS();
			// 	if(is_ios){
			// 		 let obj = {
			// 			  status :"success" ,
			// 			  authToken : '111' ,
			// 		 }
        
			// 		 window.webkit.messageHandlers.iosListener.postMessage(JSON.stringify(obj));
			// 	}else{
			// 		alert("hello");
			// 	}
			//   return 

		  $('.loader_span').removeClass('d-none');
			if (($('#new_password').val() == "") || ($('#new_password').val() != $('#confirm_password').val())) {
				  alert("Confirm password not match!!");
				  $('.loader_span').addClass('d-none'); 
				return false;
			}

			if(is_ios){
				 $('#is_ios').val('true');
			}

			$.ajax({
				type: "POST",
				dataType: "html",
				url: "ajax/main/change_password.php",
				data: $('#form_change_password').serialize(),
				success: function(resp) {
						if (resp == "success" || isJSON(resp)) {
							//alert(resp.url);
							if(is_ios){
			                    // window.webkit.messageHandlers.iosListener.postMessage("success");
								let response = JSON.stringify(JSON.parse(resp)); 
								window.webkit.messageHandlers.iosListener.postMessage(response);
							}else{
						     	window.location.href = "../?vp=ZGVzaWduX2FwcHJvdmFs";
							}
						} else {
							if(is_ios){
			                    window.webkit.messageHandlers.iosListener.postMessage("fail");
                              
							}else{

								alert(resp);
							}
						}
					

		            $('.loader_span').addClass('d-none'); 

				}
			});
		}

		
		function isIOS() {
		return /iPad|iPhone|iPod/.test(navigator.userAgent) 
		|| (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
		}

		function isJSON(str) {
		try {
		    JSON.parse(str);
	     	return true;
		} catch (e) {
		   return false;
		}
		}

	</script>
</body>

</html>
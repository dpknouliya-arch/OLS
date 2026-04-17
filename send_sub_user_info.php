<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

if( !isset($_POST["sub_user_id"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Fail to send email.";
	echo json_encode($a_result);

	exit();
}

include('db.php');

$sub_user_id = $_POST["sub_user_id"];
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';


$sql_s_user = "SELECT * FROM tbl_sub_user WHERE sub_user_id='".$sub_user_id."'; ";
$rs_s_user = $conn->query($sql_s_user);

if( $rs_s_user->num_rows == 0 ){
	$a_result["result"] = "fail";
	$a_result["msg"] = "Not found data.";
	
}else{

	$row_s_user = $rs_s_user->fetch_assoc();
	$parent_user_id = $row_s_user["parent_user_id"];
	$sub_user_id = $row_s_user["sub_user_id"];
	$sub_email = $row_s_user["sub_email"];

	if( $sub_email=="" ){

		$a_result["result"] = "fail";
		$a_result["msg"] = "Email is empty.";
		echo json_encode($a_result);

		exit();
	}

	$today_mark = strtotime(date("Y-m-d")." 00:00:00");
	$last_send = 0;
	if($row_s_user["last_send"]!=""){
		$last_send = strtotime($row_s_user["last_send"]);
	}
	
	$sent_count = intval($row_s_user["sent_count"]);
	if($last_send>$today_mark){
		$sent_count++;
	}else{
		$sent_count = 1;
	}

	if($sent_count<=3){

		/*$sql_user = "SELECT user_email FROM tbl_user WHERE user_id='".$parent_user_id."'; ";
		$rs_user = $conn->query($sql_user);
		$row_user = $rs_user->fetch_assoc();
		$admin_email = $row_user["user_email"];*/
		$admin_email = 'administration@jogsportswear.com';

		$sql_sender = "SELECT * FROM tbl_email_setting WHERE use_for='gmail_SMTP' AND enable=1 LIMIT 0,1; ";//sendgrid_SMTP
		$rs_sender = $conn->query($sql_sender);

		if($rs_sender->num_rows > 0){

			$row_sender = $rs_sender->fetch_assoc();
			$email_name = $row_sender["email_name"];
			$email_password = $row_sender["email_password"];
			$host_name = $row_sender["server_name"];
			$port_num = $row_sender["port_name"];
			
			$smtp_secure = $row_sender["smtp_secure"];

			$mail = new PHPMailer;
			//$mail->isSMTP();
			//$mail->SMTPDebug = 2;
			$mail->Host = $host_name;
			$mail->Port = $port_num;//465
			$mail->SMTPSecure = $smtp_secure;
			$mail->SMTPAuth = true;
			$mail->Username = $email_name;
			$mail->Password = $email_password;

			$mail->setFrom( $admin_email, 'JOG Online Services(Admin)');
			$mail->addAddress($sub_email);
			$mail->Subject = 'JOG Online Services: User info for '.$row_s_user["nick_name"];

			$html_content = '<center><img src="https://jogsports.com/online-services/assets/images/logo.png" alt="JOG LOGO"></center>
					<h4>User info</h4>
					<table style="margin:0px 5px 10px 5px; width: 50%;" >
						<tr>
							<th style="width: 72px; text-align:left;">User:</th>
							<td style="width: 100%;">'.$row_s_user["s_user_name"].'</td>
						</tr>
						<tr>
							<th style="text-align:left;">Password:</th>
							<td >'.$row_s_user["s_user_pwd"].'</td>
						</tr>
						<tr>
							<th style="text-align:left;">Login:</th>
							<td ><a href="'.$base_url.'login.php">Click here</a></td>
						</tr>
					</table>';

			$mail->msgHTML($html_content);

			

			if (!$mail->send()) {

			    $a_result["result"] = "fail";
				$a_result["msg"] = "Mailer Error: ".$mail->ErrorInfo;

			} else {

			    $a_result["result"] = "success";

			    $sql_update = "UPDATE tbl_sub_user SET last_send='".date("Y-m-d H:i:s")."',sent_count='".$sent_count."' WHERE sub_user_id='".$sub_user_id."'";
			    $conn->query($sql_update);
			}

		}else{

			$a_result["result"] = "fail";
			$a_result["msg"] = "Error: Sender email account not set.";
		}

	}else{
		$a_result["result"] = "fail";
		$a_result["msg"] = "Mailer Error: Sending mail exceed limit.\nTo prevent being categorized as spam mail.\nYou can send user info to the same email limit 3 times a day.";
	}

}

echo json_encode($a_result);
?>
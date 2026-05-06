<?php 
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php'; 


$sales_id = $_POST['sales_id'] ?? 0 ; 
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';

if(!$sales_id){
     echo json_encode(['error'=>503 ,'msg'=>'invalid credentials']); 
    exit ; 
}


$sql_s_user = "SELECT * FROM tbl_sales_user WHERE sales_user_id='".$sales_id."' AND sales_email!='' ";
$result = $conn->query($sql_s_user);

if($result->num_rows==0){
    echo json_encode(['error'=>404 , 'msg'=>'No data found']); 
    exit ; 
}
$data = $result->fetch_assoc();
$email = $data['sales_email']; 
$send_count = $data['sent_count']; 
$s_user_name = $data['s_user_name']; 
$s_user_pwd = $data['s_user_pwd']; 
$nick_name = $data['nick_name']; 

$admin_email = 'administration@jogsportswear.com';
$sql_sender = "SELECT * FROM tbl_email_setting WHERE use_for='gmail_SMTP' AND enable=1 LIMIT 0,1; "; 
$rs_sender = $conn->query($sql_sender);


if ($rs_sender->num_rows > 0) {

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
    $mail->Port = $port_num; //465
    $mail->SMTPSecure = $smtp_secure;
    $mail->SMTPAuth = true;
    $mail->Username = $email_name;
    $mail->Password = $email_password;

    $mail->setFrom($admin_email, 'JOG Online Services(Admin)');
    $mail->addAddress($email);
    $mail->Subject = 'JOG Online Services: Sales Person information  ' . $nick_name;

    $html_content = '<center><img src="https://jogsports.com/online-services/assets/images/logo.png" alt="JOG LOGO"></center>
					<h4>Sales info</h4>
					<table style="margin:0px 5px 10px 5px; width: 50%;" >
						<tr>
							<th style="width: 72px; text-align:left;">User:</th>
							<td style="width: 100%;">' . $s_user_name . '</td>
						</tr>
						<tr>
							<th style="text-align:left;">Password:</th>
							<td >' . $s_user_pwd . '</td>
						</tr>
						<tr>
							<th style="text-align:left;">Login:</th>
							<td ><a href="'.$base_url.'login.php">Click here</a></td>
						</tr>
					</table>';

    $mail->msgHTML($html_content);



    if (!$mail->send()) {

        $a_result["result"] = "fail";
        $a_result["msg"] = "Mailer Error: " . $mail->ErrorInfo;
    } 
    else {

        $a_result["result"] = "success";

    //     $sql_update = "UPDATE tbl_sub_user SET last_send='" . date("Y-m-d H:i:s") . "',sent_count='" . $sent_count . "' WHERE sub_user_id='" . $sub_user_id . "'";
    //     $conn->query($sql_update);
    }
} else {

    $a_result["result"] = "fail";
    $a_result["msg"] = "Error: Sender email account not set.";
}

echo json_encode($a_result); 
exit(); 

?>
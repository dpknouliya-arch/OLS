<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';

if (!isset($_POST["email"])) {

	$a_result["result"] = "fail";
	$a_result["msg"] = "Fail to register.";
	echo json_encode($a_result);

	exit();
}



$email = base64_decode($_POST["email"]);
$url_src = $_POST["url_src"] ?? '';

  
$sql_chk = "SELECT user_id,user_email,full_name FROM tbl_user WHERE user_email='" . $email . "' ; ";
$rs_chk = $conn->query($sql_chk);
if ($rs_chk->num_rows == 0) {
	$a_result["result"] = "fail";
	$a_result["msg"] = "Make sure you input correctly email.";
} else {

	$row_chk = $rs_chk->fetch_assoc();
	$user_id = $row_chk["user_id"];
	$user_email = $row_chk["user_email"];
	$full_name = $row_chk["full_name"];

	$s_tmp = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$activate_key = "";
	for ($i = 0; $i < 32; $i++) {
		$ran_num = rand(0, 61);
		$activate_key .= substr($s_tmp, $ran_num, 1);
	}

	$sql_update = "UPDATE tbl_user SET activate_key='" . $activate_key . "' WHERE user_id='" . $user_id . "'; ";

	if ($conn->query($sql_update)) {

		$sql_sender = "SELECT * FROM tbl_email_setting WHERE use_for='gmail_SMTP' AND enable=1 LIMIT 0,1; "; //sendgrid_SMTP
		$rs_sender = $conn->query($sql_sender);

		if ($rs_sender->num_rows > 0) {

			$row_sender = $rs_sender->fetch_assoc();
			$email_name = $row_sender["email_name"];
			$email_password = $row_sender["email_password"];
			$host_name = $row_sender["server_name"];
			$port_num = $row_sender["port_name"];

			$smtp_secure = $row_sender["smtp_secure"];

			$html='<!DOCTYPE html>
					<html lang="en">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<title>Reset Your Password</title>
					</head>
					<body style="margin:0;padding:0;background-color:#f0f2f5;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif;">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f0f2f5;">
						<tr>
						<td align="center" style="padding:40px 16px;">

							<!-- Email Card -->
							<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px;">

							<!-- Header -->
							<tr>
								<td align="center" style="background-color:#111111;border-radius:12px 12px 0 0;padding:32px 40px;">
								<img src="https://online.jog-joinourgame.com/assets/images/logo.png"
									width="110" height="88"
									alt="JOG Sports"
									style="display:block;border:0;object-fit:contain;" />
								</td>
							</tr>

							<!-- Lock Icon Banner -->
							<tr>
								<td align="center" style="background-color:#1a1a1a;padding:28px 40px 24px;">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
									<td align="center" style="width:64px;height:64px;background-color:#333333;border-radius:50%;font-size:28px;line-height:64px;text-align:center;">
										&#128274;
									</td>
									</tr>
									<tr>
									<td align="center" style="padding-top:16px;">
										<h1 style="margin:0;font-size:24px;font-weight:700;color:#ffffff;letter-spacing:-0.3px;">Password Reset Request</h1>
										<p style="margin:8px 0 0;font-size:14px;color:#999999;letter-spacing:0.2px;">Secure your JOG account</p>
									</td>
									</tr>
								</table>
								</td>
							</tr>

							<!-- Body -->
							<tr>
								<td style="background-color:#ffffff;padding:40px 48px;">

								<!-- Greeting -->
								<p style="margin:0 0 24px;font-size:16px;color:#1a1a1a;font-weight:600;">Hi '.$full_name.',</p>

								<!-- Message -->
								<p style="margin:0 0 8px;font-size:15px;color:#444444;line-height:1.7;">
									We received a request to reset the password for your JOG account associated with this email address.
								</p>
								<p style="margin:0 0 32px;font-size:15px;color:#444444;line-height:1.7;">
									Click the button below to choose a new password. This link is valid for a limited time.
								</p>

								<!-- CTA Button -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
									<td align="center" style="padding:8px 0 36px;">
										<a href="https://ols-test.jog-joinourgame.com/OLS/confirm_email.php?key=' . $activate_key . '&url_src=' . urlencode($url_src) . '"
										style="display:inline-block;background-color:#111111;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;letter-spacing:0.4px;padding:15px 44px;border-radius:8px;border:2px solid #111111;">
										Reset My Password
										</a>
									</td>
									</tr>
								</table>

								<!-- Divider -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
									<td style="border-top:1px solid #eeeeee;padding-top:28px;">

										<!-- Security Notice -->
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td style="background-color:#fff8f0;border:1px solid #ffe0b2;border-radius:8px;padding:14px 18px;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
												<td style="width:20px;font-size:16px;vertical-align:top;padding-top:1px;">&#9888;&#65039;</td>
												<td style="padding-left:10px;font-size:13px;color:#7a4f00;line-height:1.6;">
													<strong>Didn&apos;t request this?</strong> If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.
												</td>
												</tr>
											</table>
											</td>
										</tr>
										</table>

										<!-- Sign-off -->
										<p style="margin:28px 0 0;font-size:15px;color:#444444;line-height:1.7;">
										Warm regards,<br>
										<strong style="color:#111111;">The JOG Team</strong>
										</p>

									</td>
									</tr>
								</table>

								</td>
							</tr>

							<!-- Footer -->
							<tr>
								<td style="background-color:#111111;border-radius:0 0 12px 12px;padding:28px 48px;">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
									<td align="center">
										<p style="margin:0 0 8px;font-size:14px;color:#cccccc;font-weight:600;">Need help?</p>
										<p style="margin:0;font-size:13px;color:#888888;line-height:1.6;">
										Visit us at
										<a href="https://ols-test.jog-joinourgame.com" target="_blank" rel="noreferrer"
											style="color:#aaaaaa;text-decoration:underline;">jog-joinourgame.com</a>
										&nbsp;&mdash;&nbsp; we&apos;re here to help.
										</p>
										<p style="margin:16px 0 0;font-size:11px;color:#555555;">
										&copy; '.date('Y').' JOG Sports. All rights reserved.
										</p>
									</td>
									</tr>
								</table>
								</td>
							</tr>

							</table>
							<!-- /Email Card -->

						</td>
						</tr>
					</table>
					</body>
					</html>';

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
			$headers .= "From: JOG Sports <no-reply@jog-joinourgame.com>\r\n";
			$headers .= "Bcc: ravish@jogsportswear.com\r\n";

			if (mail($email, 'Reset your password (JOG Online Services)', $html, $headers)) {
				$a_result["result"] = "success";
			} else {
				$a_result["result"] = "fail";
				$a_result["msg"] = "Failed to send email.";
			}
		} else {
			$a_result["result"] = "fail";
			$a_result["msg"] = "Error: Sender email account not set.";
		}
	} else {
		$a_result["result"] = "fail";
		$a_result["msg"] = "Fail to connect DB.";
	}
}

echo json_encode($a_result);
